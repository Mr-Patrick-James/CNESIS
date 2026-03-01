<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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
    !empty($data->id) &&
    !empty($data->batch_name) &&
    !empty($data->exam_date) &&
    !empty($data->start_time) &&
    !empty($data->end_time) &&
    !empty($data->venue) &&
    !empty($data->max_slots) &&
    !empty($data->status)
) {
    try {
        $db->beginTransaction();

        // 1. Get current batch info to check for changes
        $checkQuery = "SELECT * FROM exam_schedules WHERE id = :id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(":id", $data->id);
        $checkStmt->execute();
        $oldBatch = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$oldBatch) {
            throw new Exception("Batch not found");
        }

        // 2. Update the batch
        $query = "UPDATE exam_schedules SET 
                    batch_name = :batch_name, 
                    exam_date = :exam_date, 
                    start_time = :start_time, 
                    end_time = :end_time, 
                    venue = :venue, 
                    max_slots = :max_slots, 
                    status = :status 
                  WHERE id = :id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":batch_name", $data->batch_name);
        $stmt->bindParam(":exam_date", $data->exam_date);
        $stmt->bindParam(":start_time", $data->start_time);
        $stmt->bindParam(":end_time", $data->end_time);
        $stmt->bindParam(":venue", $data->venue);
        $stmt->bindParam(":max_slots", $data->max_slots);
        $stmt->bindParam(":status", $data->status);
        $stmt->bindParam(":id", $data->id);
        
        if (!$stmt->execute()) {
            throw new Exception("Unable to update exam schedule");
        }

        // 3. Check if we need to notify students
        // Notify if date/time changed OR if batch was cancelled
        $dateChanged = $oldBatch['exam_date'] != $data->exam_date || 
                       $oldBatch['start_time'] != $data->start_time || 
                       $oldBatch['end_time'] != $data->end_time ||
                       $oldBatch['venue'] != $data->venue;
        
        $cancelled = $oldBatch['status'] != 'cancelled' && $data->status == 'cancelled';
        
        $notificationCount = 0;
        $emailErrors = [];

        if (($dateChanged || $cancelled) && !empty($data->notify)) {
            // Get all students assigned to this batch
            $studentQuery = "SELECT email, first_name, last_name FROM admissions WHERE exam_schedule_id = :id";
            $studentStmt = $db->prepare($studentQuery);
            $studentStmt->bindParam(":id", $data->id);
            $studentStmt->execute();
            $students = $studentStmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($students)) {
                $emailConfig = new EmailConfig($db);
                $mailer = $emailConfig->getMailer();

                foreach ($students as $student) {
                    try {
                        $mailer->clearAddresses();
                        $mailer->addAddress($student['email'], $student['first_name'] . ' ' . $student['last_name']);
                        $mailer->isHTML(true);
                        
                        if ($cancelled) {
                            $mailer->Subject = "URGENT: Exam Batch Cancelled - Colegio De Naujan";
                            $mailer->Body = "
                                <h3>Exam Cancellation Notice</h3>
                                <p>Dear {$student['first_name']} {$student['last_name']},</p>
                                <p>We regret to inform you that your scheduled exam batch <strong>{$data->batch_name}</strong> has been <strong>CANCELLED</strong>.</p>
                                <p>Please log in to your admission portal or contact the admissions office for rescheduling information.</p>
                                <p>We apologize for the inconvenience.</p>";
                        } else {
                            $mailer->Subject = "IMPORTANT: Exam Schedule Updated - Colegio De Naujan";
                            $mailer->Body = "
                                <h3>Exam Schedule Update Notice</h3>
                                <p>Dear {$student['first_name']} {$student['last_name']},</p>
                                <p>Please be informed that the schedule for your exam batch <strong>{$data->batch_name}</strong> has been updated.</p>
                                <p><strong>New Schedule:</strong></p>
                                <ul>
                                    <li><strong>Date:</strong> " . date('F d, Y', strtotime($data->exam_date)) . "</li>
                                    <li><strong>Time:</strong> " . date('h:i A', strtotime($data->start_time)) . " - " . date('h:i A', strtotime($data->end_time)) . "</li>
                                    <li><strong>Venue:</strong> {$data->venue}</li>
                                </ul>
                                <p>Please make sure to arrive at least 30 minutes before your scheduled time.</p>";
                        }
                        
                        if ($mailer->send()) {
                            $notificationCount++;
                        }
                    } catch (Exception $e) {
                        $emailErrors[] = "Failed to send to {$student['email']}: " . $e->getMessage();
                    }
                }
            }
        }

        $db->commit();
        http_response_code(200);
        echo json_encode([
            "success" => true, 
            "message" => "Exam schedule updated successfully",
            "notifications_sent" => $notificationCount,
            "email_errors" => $emailErrors
        ]);

    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Incomplete data"]);
}
?>
