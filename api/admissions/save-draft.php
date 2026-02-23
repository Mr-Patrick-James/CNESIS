<?php
// Start output buffering immediately to catch any early output
ob_start();

// Suppress warnings and errors from being output
error_reporting(0);
ini_set('display_errors', 0);
// error_reporting(E_ALL);

/**
 * Save Draft Admission API
 * Handles saving/updating admission application drafts
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
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

try {
    // Get raw input
    $rawInput = file_get_contents("php://input");
    $data = json_decode($rawInput);
    
    // Validate JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid JSON: " . json_last_error_msg()
        ]);
        exit;
    }
    
    // Email is the unique identifier for the user session
    if (empty($data->email)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Email is required to save draft"
        ]);
        exit;
    }

    // Generate temp application_id if missing for draft
    $application_id = $data->application_id ?? 'DRAFT-' . time();

    // Check if application already exists
    $checkQuery = "SELECT id, status FROM admissions WHERE email = :email LIMIT 1";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':email', $data->email);
    $checkStmt->execute();
    
    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Prevent overwriting submitted applications
        // If the application is already submitted (pending, approved, enrolled, rejected), do not allow draft updates
        if (in_array($existing['status'], ['pending', 'approved', 'rejected', 'enrolled'])) {
             http_response_code(409); 
             echo json_encode([
                 "success" => false,
                 "message" => "Cannot update draft: Application already submitted with status " . $existing['status']
             ]);
             exit;
        }

        // UPDATE existing record
        // Only update if status is NOT 'pending', 'approved', 'rejected' (don't overwrite submitted apps unless allowed)
        // For now, we allow updating 'new' or 'draft' status.
        // If it's already submitted, we might want to prevent overwrite or create a new version (but schema doesn't support versions easily yet).
        // Let's assume we can update 'new' status.
        
        $query = "UPDATE admissions SET 
                    application_id = :application_id,
                    student_id = :student_id,
                    program_id = :program_id,
                    first_name = :first_name,
                    middle_name = :middle_name,
                    last_name = :last_name,
                    phone = :phone,
                    birthdate = :birthdate,
                    gender = :gender,
                    address = :address,
                    high_school = :high_school,
                    last_school = :last_school,
                    year_graduated = :year_graduated,
                    gwa = :gwa,
                    entrance_exam_score = :entrance_exam_score,
                    admission_type = :admission_type,
                    previous_program = :previous_program,
                    status = :status,
                    notes = :notes,
                    attachments = :attachments,
                    form_data = :form_data
                  WHERE email = :email";
        
        // Status logic: if existing is 'new' or 'draft', keep it as 'draft' or whatever is passed.
        // If passed status is empty, default to 'draft'.
        $status = !empty($data->status) ? $data->status : ($existing['status'] == 'new' ? 'draft' : $existing['status']);
        
    } else {
        // INSERT new record
        $query = "INSERT INTO admissions (
                    application_id, student_id, program_id, first_name, middle_name, last_name, email,
                    phone, birthdate, gender, address, high_school, last_school, year_graduated,
                    gwa, entrance_exam_score, admission_type, previous_program, status, notes,
                    attachments, form_data
                  ) VALUES (
                    :application_id, :student_id, :program_id, :first_name, :middle_name, :last_name, :email,
                    :phone, :birthdate, :gender, :address, :high_school, :last_school, :year_graduated,
                    :gwa, :entrance_exam_score, :admission_type, :previous_program, :status, :notes,
                    :attachments, :form_data
                  )";
        $status = !empty($data->status) ? $data->status : 'draft';
    }

    $stmt = $db->prepare($query);
    
    // Bind parameters (use null if empty for non-required fields in draft)
    $stmt->bindValue(':application_id', !empty($data->application_id) ? $data->application_id : $application_id);
    $stmt->bindValue(':student_id', $data->student_id ?? '');
    $stmt->bindValue(':program_id', !empty($data->program_id) ? $data->program_id : null);
    $stmt->bindValue(':first_name', $data->first_name ?? '');
    $stmt->bindValue(':middle_name', $data->middle_name ?? '');
    $stmt->bindValue(':last_name', $data->last_name ?? '');
    $stmt->bindValue(':email', $data->email);
    $stmt->bindValue(':phone', $data->phone ?? '');
    $stmt->bindValue(':birthdate', !empty($data->birthdate) ? $data->birthdate : null);
    $stmt->bindValue(':gender', $data->gender ?? '');
    $stmt->bindValue(':address', $data->address ?? '');
    $stmt->bindValue(':high_school', $data->high_school ?? '');
    $stmt->bindValue(':last_school', $data->last_school ?? '');
    $stmt->bindValue(':year_graduated', !empty($data->year_graduated) ? $data->year_graduated : null);
    $stmt->bindValue(':gwa', !empty($data->gwa) ? $data->gwa : null);
    $stmt->bindValue(':entrance_exam_score', !empty($data->entrance_exam_score) ? $data->entrance_exam_score : null);
    $stmt->bindValue(':admission_type', $data->admission_type ?? '');
    $stmt->bindValue(':previous_program', $data->previous_program ?? '');
    $stmt->bindValue(':status', $status);
    $stmt->bindValue(':notes', $data->notes ?? '');
    $stmt->bindValue(':attachments', !empty($data->attachments) ? json_encode($data->attachments) : null);
    $stmt->bindValue(':form_data', !empty($data->form_data) ? json_encode($data->form_data) : null);

    if ($stmt->execute()) {
        $id = $existing ? $existing['id'] : $db->lastInsertId();
        
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Draft saved successfully",
            "id" => $id,
            "status" => $status
        ]);
    } else {
        throw new Exception("Execute failed");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error saving draft: " . $e->getMessage()
    ]);
}
?>