<?php
/**
 * Update Examinees Status API (Bulk)
 * Handles updating status for multiple examinees in a batch
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->student_ids) || !is_array($data->student_ids) || !isset($data->status)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Student IDs (array) and status are required"
    ]);
    exit;
}

$studentIds = $data->student_ids;
$newStatus = $data->status;

if (empty($studentIds)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "No students selected"
    ]);
    exit;
}

try {
    $db->beginTransaction();

    // First, let's ensure the status column can handle the new values if they are not in the enum
    // We'll check the current enum values
    $stmt = $db->query("SHOW COLUMNS FROM admissions LIKE 'status'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $type = $row['Type']; // e.g. enum('pending','approved','rejected','enrolled')
    
    // Check if new status is in the enum
    if (strpos($type, "'" . $newStatus . "'") === false) {
        // We need to update the enum definition
        // Extract existing values
        preg_match_all("/'([^']+)'/", $type, $matches);
        $existingValues = $matches[1];
        
        // Add new required statuses if they don't exist
        $requiredStatuses = ['examed', 'did not attend', 'reschedule'];
        $updatedValues = array_unique(array_merge($existingValues, $requiredStatuses));
        
        $enumStr = "ENUM('" . implode("','", $updatedValues) . "')";
        $db->exec("ALTER TABLE admissions MODIFY COLUMN status $enumStr DEFAULT 'pending'");
    }

    $placeholders = implode(',', array_fill(0, count($studentIds), '?'));

    // If status is 'reschedule', we should also remove them from the current batch so they can be rescheduled
    if ($newStatus === 'reschedule') {
        // 1. Get the schedule IDs before clearing them
        $getScheduleIdsQuery = "SELECT DISTINCT exam_schedule_id FROM admissions WHERE id IN ($placeholders) AND exam_schedule_id IS NOT NULL";
        $getScheduleIdsStmt = $db->prepare($getScheduleIdsQuery);
        $getScheduleIdsStmt->execute($studentIds);
        $scheduleIds = $getScheduleIdsStmt->fetchAll(PDO::FETCH_COLUMN);

        // 2. Clear the schedule IDs for these students
        $clearBatchQuery = "UPDATE admissions SET exam_schedule_id = NULL WHERE id IN ($placeholders)";
        $clearBatchStmt = $db->prepare($clearBatchQuery);
        $clearBatchStmt->execute($studentIds);

        // 3. Update slot counts for each affected schedule
        foreach ($scheduleIds as $scheduleId) {
            $updateSlotsQuery = "UPDATE exam_schedules SET current_slots = (SELECT COUNT(*) FROM admissions WHERE exam_schedule_id = ?) WHERE id = ?";
            $updateSlotsStmt = $db->prepare($updateSlotsQuery);
            $updateSlotsStmt->execute([$scheduleId, $scheduleId]);
        }
    }

    // Update students status
    $query = "UPDATE admissions SET status = ?, reviewed_at = NOW(), reviewed_by = 1 WHERE id IN ($placeholders)";
    $stmt = $db->prepare($query);
    
    $params = array_merge([$newStatus], $studentIds);
    $stmt->execute($params);

    $db->commit();

    echo json_encode([
        "success" => true,
        "message" => "Updated " . count($studentIds) . " students to status: " . $newStatus
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error updating statuses: " . $e->getMessage()
    ]);
}
