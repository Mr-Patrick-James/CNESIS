<?php
/**
 * Delete Program API with Archive Support
 * Archives program records before deletion for recovery
 */

require_once __DIR__ . '/../auth/auth_guard.php';
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../includes/ArchiveManager.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Get program ID and delete reason
$programId = null;
$deleteReason = 'Manual deletion by administrator';
$deletedBy = 'Administrator';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $programId = isset($_GET['id']) ? $_GET['id'] : null;
    $deleteReason = isset($_GET['reason']) ? $_GET['reason'] : $deleteReason;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $programId = isset($data->id) ? $data->id : null;
    $deleteReason = isset($data->reason) ? $data->reason : $deleteReason;
    $deletedBy = isset($data->deleted_by) ? $data->deleted_by : $deletedBy;
}

if (!$programId) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Program ID is required"
    ]);
    exit;
}

// Validate program ID
if (!is_numeric($programId)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid program ID"
    ]);
    exit;
}

try {
    // Check if program exists
    $checkQuery = "SELECT id, program_code, program_title FROM programs WHERE id = :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':id', $programId);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Program not found"
        ]);
        exit;
    }
    
    $program = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    // Start transaction
    $db->beginTransaction();
    
    // Insert into archive table
    $archiveQuery = "INSERT INTO archive_programs (
        original_id, program_code, program_title, description, department,
        duration_years, tuition_fee, status, created_at, updated_at, deleted_at, deleted_by, delete_reason
    ) SELECT 
        id, program_code, program_title, description, department,
        duration_years, tuition_fee, status, created_at, updated_at, NOW(), :deleted_by, :delete_reason
    FROM programs WHERE id = :id";
    
    $archiveStmt = $db->prepare($archiveQuery);
    $archiveStmt->bindParam(':id', $programId);
    $archiveStmt->bindParam(':deleted_by', $deletedBy);
    $archiveStmt->bindParam(':delete_reason', $deleteReason);
    
    if ($archiveStmt->execute()) {
        // Delete the original program
        $deleteQuery = "DELETE FROM programs WHERE id = :id";
        $deleteStmt = $db->prepare($deleteQuery);
        $deleteStmt->bindParam(':id', $programId);
        
        if ($deleteStmt->execute()) {
            $db->commit();
            
            echo json_encode([
                "success" => true,
                "message" => "Program archived successfully",
                "archived" => true,
                "data" => [
                    "archived_id" => $programId,
                    "program_code" => $program['program_code'],
                    "program_title" => $program['program_title'],
                    "reason" => $deleteReason,
                    "deleted_by" => $deletedBy
                ]
            ]);
        } else {
            $db->rollBack();
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to delete original program"
            ]);
        }
    } else {
        $db->rollBack();
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to archive program"
        ]);
    }
    
} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log("Error archiving program: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error archiving program: " . $e->getMessage()
    ]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log("General error archiving program: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error archiving program: " . $e->getMessage()
    ]);
}
?>
