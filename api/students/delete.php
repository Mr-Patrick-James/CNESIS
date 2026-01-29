<?php
/**
 * Delete Student API
 * Handles deleting existing students
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

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

try {
    // Get student ID from URL or POST data
    $studentId = isset($_GET['id']) ? $_GET['id'] : null;
    
    if (!$studentId) {
        $rawInput = file_get_contents("php://input");
        $data = json_decode($rawInput);
        $studentId = isset($data->id) ? $data->id : null;
    }
    
    if (empty($studentId)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Student ID is required"
        ]);
        exit;
    }
    
    // Check if student exists
    $checkQuery = "SELECT id, student_id FROM students WHERE id = :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':id', $studentId);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Student not found"
        ]);
        exit;
    }
    
    $student = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    // Delete student
    $query = "DELETE FROM students WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $studentId);
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Student deleted successfully",
            "deleted_student_id" => $student['student_id']
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to delete student"
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
