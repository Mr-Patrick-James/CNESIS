<?php
/**
 * Delete Program Head API with Archive Support
 * Archives program head records before deletion for recovery
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../includes/ArchiveManager.php';

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

// Get program head ID and delete reason
$programHeadId = null;
$deleteReason = 'Manual deletion by administrator';
$deletedBy = 'Administrator';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $programHeadId = isset($_GET['id']) ? $_GET['id'] : null;
    $deleteReason = isset($_GET['reason']) ? $_GET['reason'] : $deleteReason;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $programHeadId = isset($data->id) ? $data->id : null;
    $deleteReason = isset($data->reason) ? $data->reason : $deleteReason;
    $deletedBy = isset($data->deleted_by) ? $data->deleted_by : $deletedBy;
}

if (!$programHeadId) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Program head ID is required"
    ]);
    exit;
}

// Validate program head ID
if (!is_numeric($programHeadId)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid program head ID"
    ]);
    exit;
}

try {
    // Check if program head exists
    $checkQuery = "SELECT id, employee_id, first_name, last_name FROM program_heads WHERE id = :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':id', $programHeadId);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Program head not found"
        ]);
        exit;
    }
    
    $programHead = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    // Start transaction
    $db->beginTransaction();
    
    // Insert into archive table
    $archiveQuery = "INSERT INTO archive_program_heads (
        original_id, employee_id, first_name, middle_name, last_name, email, phone,
        department, specialization, hire_date, status, created_at, updated_at, deleted_at, deleted_by, delete_reason
    ) SELECT 
        id, employee_id, first_name, middle_name, last_name, email, phone,
        department, specialization, hire_date, status, created_at, updated_at, NOW(), :deleted_by, :delete_reason
    FROM program_heads WHERE id = :id";
    
    $archiveStmt = $db->prepare($archiveQuery);
    $archiveStmt->bindParam(':id', $programHeadId);
    $archiveStmt->bindParam(':deleted_by', $deletedBy);
    $archiveStmt->bindParam(':delete_reason', $deleteReason);
    
    if ($archiveStmt->execute()) {
        // Delete the original program head
        $deleteQuery = "DELETE FROM program_heads WHERE id = :id";
        $deleteStmt = $db->prepare($deleteQuery);
        $deleteStmt->bindParam(':id', $programHeadId);
        
        if ($deleteStmt->execute()) {
            $db->commit();
            
            echo json_encode([
                "success" => true,
                "message" => "Program head archived successfully",
                "archived" => true,
                "data" => [
                    "archived_id" => $programHeadId,
                    "employee_id" => $programHead['employee_id'],
                    "name" => $programHead['first_name'] . ' ' . $programHead['last_name'],
                    "reason" => $deleteReason,
                    "deleted_by" => $deletedBy
                ]
            ]);
        } else {
            $db->rollBack();
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to delete original program head"
            ]);
        }
    } else {
        $db->rollBack();
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to archive program head"
        ]);
    }
    
} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log("Error archiving program head: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error archiving program head: " . $e->getMessage()
    ]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log("General error archiving program head: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error archiving program head: " . $e->getMessage()
    ]);
}
?>
