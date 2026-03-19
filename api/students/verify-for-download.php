<?php
/**
 * Verify Student for Download API
 * Checks if a student's email exists in the students table
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

try {
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    if (empty($data->email)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Email is required"]);
        exit;
    }
    
    // Check if email exists in students table
    $query = "SELECT id, first_name, last_name FROM students WHERE email = :email AND status = 'active' LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $data->email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Start session to remember verification for this session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['verified_student_email'] = $data->email;
        $_SESSION['verified_student_name'] = $student['first_name'] . ' ' . $student['last_name'];

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Verification successful",
            "student_name" => $_SESSION['verified_student_name']
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false, 
            "message" => "Only registered students can download this file. Please ensure you are using your registered email or contact the registrar."
        ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}

$database->closeConnection();
?>
