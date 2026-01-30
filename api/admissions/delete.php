<?php
/**
 * Delete Admission API with Archive Support
 * Archives admission records before deletion for recovery
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

// Get admission ID and delete reason
$admissionId = null;
$deleteReason = 'Manual deletion by administrator';
$deletedBy = 'Administrator';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $admissionId = isset($_GET['id']) ? $_GET['id'] : null;
    $deleteReason = isset($_GET['reason']) ? $_GET['reason'] : $deleteReason;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $admissionId = isset($data->id) ? $data->id : null;
    $deleteReason = isset($data->reason) ? $data->reason : $deleteReason;
    $deletedBy = isset($data->deleted_by) ? $data->deleted_by : $deletedBy;
}

if (!$admissionId) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Admission ID is required"
    ]);
    exit;
}

// Validate admission ID
if (!is_numeric($admissionId)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid admission ID"
    ]);
    exit;
}

try {
    // Initialize Archive Manager
    $archiveManager = new ArchiveManager($db, $deletedBy);
    
    // Archive the admission (this also deletes it from the main table)
    $result = $archiveManager->archiveAdmission($admissionId, $deleteReason);
    
    if ($result['success']) {
        echo json_encode([
            "success" => true,
            "message" => "Admission archived successfully",
            "archived" => true,
            "data" => [
                "archived_id" => $admissionId,
                "reason" => $deleteReason,
                "deleted_by" => $deletedBy
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => $result['message']
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error archiving admission: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error archiving admission: " . $e->getMessage()
    ]);
}
?>
