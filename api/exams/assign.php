<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';
include_once '../config/email_config.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->exam_schedule_id) &&
    !empty($data->student_ids) &&
    is_array($data->student_ids)
) {
    try {
        $db->beginTransaction();
        
        // 1. Get Exam Schedule Details
        $query = "SELECT * FROM exam_schedules WHERE id = :id AND status = 'active' FOR UPDATE";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $data->exam_schedule_id);
        $stmt->execute();
        $schedule = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$schedule) {
            throw new Exception("Exam schedule not found or inactive");
        }
        
        // Check slots (optional, but good practice)
        // We will just proceed and update the count later or let it overflow if admin insists?
        // Let's enforce the limit.
        $currentCountQuery = "SELECT COUNT(*) FROM admissions WHERE exam_schedule_id = :id";
        $countStmt = $db->prepare($currentCountQuery);
        $countStmt->bindParam(":id", $data->exam_schedule_id);
        $countStmt->execute();
        $currentCount = $countStmt->fetchColumn();
        
        $newCount = count($data->student_ids);
        if (($currentCount + $newCount) > $schedule['max_slots']) {
            throw new Exception("Not enough slots available in this batch. Available: " . ($schedule['max_slots'] - $currentCount));
        }
        
        // 2. Update Students
        $updateQuery = "UPDATE admissions SET 
                        exam_schedule_id = :schedule_id, 
                        status = 'scheduled' 
                        WHERE id = :student_id";
        $updateStmt = $db->prepare($updateQuery);
        
        $successCount = 0;
        $emailErrors = [];
        
        // Prepare Emailer if needed
        $mailer = null;
        if (!empty($data->send_email)) {
            $emailConfig = new EmailConfig($db);
            try {
                $mailer = $emailConfig->getMailer();
            } catch (Exception $e) {
                // If mailer fails, we continue assignment but note the error
                $emailErrors[] = "Failed to initialize mailer: " . $e->getMessage();
            }
        }
        
        foreach ($data->student_ids as $studentId) {
            $updateStmt->bindParam(":schedule_id", $data->exam_schedule_id);
            $updateStmt->bindParam(":student_id", $studentId);
            
            if ($updateStmt->execute()) {
                $successCount++;
                
                // Send Email
                if ($mailer) {
                    // Get student email
                    $studentQuery = "SELECT email, first_name, last_name FROM admissions WHERE id = :id";
                    $studentStmt = $db->prepare($studentQuery);
                    $studentStmt->bindParam(":id", $studentId);
                    $studentStmt->execute();
                    $student = $studentStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($student) {
                        try {
                            // Reset recipients
                            $mailer->clearAddresses();
                            $mailer->addAddress($student['email'], $student['first_name'] . ' ' . $student['last_name']);
                            
                            $mailer->isHTML(true);
                            $mailer->Subject = "Entrance Exam Schedule - Colegio De Naujan";
                            
                            $body = "
                                <h2>Entrance Exam Schedule</h2>
                                <p>Dear {$student['first_name']} {$student['last_name']},</p>
                                <p>Your application has been approved and you are scheduled for the entrance exam.</p>
                                <p><strong>Batch:</strong> {$schedule['batch_name']}</p>
                                <p><strong>Date:</strong> " . date('F d, Y', strtotime($schedule['exam_date'])) . "</p>
                                <p><strong>Time:</strong> " . date('h:i A', strtotime($schedule['start_time'])) . " - " . date('h:i A', strtotime($schedule['end_time'])) . "</p>
                                <p><strong>Venue:</strong> {$schedule['venue']}</p>
                                <p>Please bring your valid ID and examination permit (if applicable).</p>
                                <p>Good luck!</p>
                                <br>
                                <p>Best regards,<br>Admission Office<br>Colegio De Naujan</p>
                            ";
                            
                            $mailer->Body = $body;
                            $mailer->AltBody = strip_tags($body);
                            
                            $mailer->send();
                        } catch (Exception $e) {
                            $emailErrors[] = "Failed to send email to {$student['email']}: " . $mailer->ErrorInfo;
                        }
                    }
                }
            }
        }
        
        // Update current slots in schedule table by recounting
        // This ensures accuracy even if multiple requests come in or manual updates happened
        $updateSlotsSql = "UPDATE exam_schedules SET current_slots = (
                            SELECT COUNT(*) FROM admissions WHERE exam_schedule_id = :id_sub
                          ) WHERE id = :id_main";
        $updateSlotsStmt = $db->prepare($updateSlotsSql);
        $updateSlotsStmt->bindParam(":id_sub", $data->exam_schedule_id);
        $updateSlotsStmt->bindParam(":id_main", $data->exam_schedule_id);
        $updateSlotsStmt->execute();
        
        $db->commit();
        
        http_response_code(200);
        echo json_encode([
            "success" => true, 
            "message" => "Successfully assigned $successCount students.",
            "email_errors" => $emailErrors
        ]);
        
    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid data"]);
}
?>
