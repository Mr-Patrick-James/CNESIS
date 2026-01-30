<?php
/**
 * Archive API - View and Manage Archived Items
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
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

$method = $_SERVER['REQUEST_METHOD'];

try {
    $archiveManager = new ArchiveManager($db);
    
    switch ($method) {
        case 'GET':
            // Get archived items with filters
            $filters = [
                'item_type' => $_GET['item_type'] ?? null,
                'status' => $_GET['status'] ?? null,
                'date_from' => $_GET['date_from'] ?? null,
                'date_to' => $_GET['date_to'] ?? null,
                'search' => $_GET['search'] ?? null
            ];
            
            // Remove null filters
            $filters = array_filter($filters, function($value) {
                return $value !== null && $value !== '';
            });
            
            $result = $archiveManager->getAllArchivedItems($filters);
            
            if ($result['success']) {
                echo json_encode([
                    "success" => true,
                    "message" => "Archived items retrieved successfully",
                    "data" => $result['data'],
                    "count" => count($result['data'])
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "message" => $result['message']
                ]);
            }
            break;
            
        case 'POST':
            // Restore archived item
            $data = json_decode(file_get_contents("php://input"));
            
            if (!isset($data->archive_id) || !isset($data->table)) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "Archive ID and table are required"
                ]);
                exit;
            }
            
            $result = [];
            switch ($data->table) {
                case 'archive_admissions':
                    $result = $archiveManager->restoreAdmission($data->archive_id);
                    break;
                case 'archive_programs':
                    $result = $archiveManager->restoreProgram($data->archive_id);
                    break;
                case 'archive_students':
                    $result = $archiveManager->restoreStudent($data->archive_id);
                    break;
                case 'archive_program_heads':
                    $result = $archiveManager->restoreProgramHead($data->archive_id);
                    break;
                default:
                    http_response_code(400);
                    echo json_encode([
                        "success" => false,
                        "message" => "Invalid table specified"
                    ]);
                    exit;
            }
            
            if ($result['success']) {
                echo json_encode([
                    "success" => true,
                    "message" => $result['message'],
                    "restored_id" => $data->archive_id
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "message" => $result['message']
                ]);
            }
            break;
            
        case 'DELETE':
            // Permanently delete archived item
            $archiveId = $_GET['id'] ?? null;
            $table = $_GET['table'] ?? null;
            
            if (!$archiveId || !$table) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "Archive ID and table are required"
                ]);
                exit;
            }
            
            $result = $archiveManager->permanentDelete($archiveId, $table);
            
            if ($result['success']) {
                echo json_encode([
                    "success" => true,
                    "message" => $result['message'],
                    "permanently_deleted" => true
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "message" => $result['message']
                ]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode([
                "success" => false,
                "message" => "Method not allowed"
            ]);
            break;
    }
    
} catch (Exception $e) {
    error_log("Archive API Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Server error: " . $e->getMessage()
    ]);
}
?>
