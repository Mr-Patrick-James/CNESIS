<?php
/**
 * Update Program Head API
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT, PATCH");
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
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $data = json_decode(file_get_contents("php://input"));
    
    if (!$id) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Program head ID is required"
        ]);
        exit;
    }
    
    // Check if program head exists
    $checkQuery = "SELECT id FROM program_heads WHERE id = :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':id', $id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() == 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Program head not found"
        ]);
        exit;
    }
    
    // Check if employee ID already exists for another record
    if (!empty($data->employee_id)) {
        $checkEmpQuery = "SELECT id FROM program_heads WHERE employee_id = :employee_id AND id != :id";
        $checkEmpStmt = $db->prepare($checkEmpQuery);
        $checkEmpStmt->bindParam(':employee_id', $data->employee_id);
        $checkEmpStmt->bindParam(':id', $id);
        $checkEmpStmt->execute();
        
        if ($checkEmpStmt->rowCount() > 0) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Employee ID already exists"
            ]);
            exit;
        }
    }
    
    // Check if email already exists for another record
    if (!empty($data->email)) {
        $checkEmailQuery = "SELECT id FROM program_heads WHERE email = :email AND id != :id";
        $checkEmailStmt = $db->prepare($checkEmailQuery);
        $checkEmailStmt->bindParam(':email', $data->email);
        $checkEmailStmt->bindParam(':id', $id);
        $checkEmailStmt->execute();
        
        if ($checkEmailStmt->rowCount() > 0) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Email already exists"
            ]);
            exit;
        }
    }
    
    // Build update query dynamically
    $updates = [];
    $params = [];
    
    if (isset($data->employee_id)) {
        $updates[] = "employee_id = :employee_id";
        $params[':employee_id'] = $data->employee_id;
    }
    if (isset($data->first_name)) {
        $updates[] = "first_name = :first_name";
        $params[':first_name'] = $data->first_name;
    }
    if (isset($data->middle_name)) {
        $updates[] = "middle_name = :middle_name";
        $params[':middle_name'] = $data->middle_name ?: null;
    }
    if (isset($data->last_name)) {
        $updates[] = "last_name = :last_name";
        $params[':last_name'] = $data->last_name;
    }
    if (isset($data->email)) {
        $updates[] = "email = :email";
        $params[':email'] = $data->email;
    }
    if (isset($data->phone)) {
        $updates[] = "phone = :phone";
        $params[':phone'] = $data->phone;
    }
    if (isset($data->department)) {
        $updates[] = "department = :department";
        $params[':department'] = $data->department;
    }
    if (isset($data->specialization)) {
        $updates[] = "specialization = :specialization";
        $params[':specialization'] = $data->specialization ?: null;
    }
    if (isset($data->hire_date)) {
        $updates[] = "hire_date = :hire_date";
        $params[':hire_date'] = $data->hire_date;
    }
    if (isset($data->status)) {
        $updates[] = "status = :status";
        $params[':status'] = $data->status;
    }
    
    // Always update updated_at
    $updates[] = "updated_at = CURRENT_TIMESTAMP";
    
    if (empty($updates)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "No fields to update"
        ]);
        exit;
    }
    
    $query = "UPDATE program_heads SET " . implode(", ", $updates) . " WHERE id = :id";
    $params[':id'] = $id;
    
    $stmt = $db->prepare($query);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Program head updated successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to update program head"
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