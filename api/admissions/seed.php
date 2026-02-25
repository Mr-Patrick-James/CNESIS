<?php
/**
 * Seed Freshman Admissions
 * Visit this page to insert 50 dummy freshman students into the admissions table.
 */

header("Content-Type: application/json; charset=UTF-8");

include_once __DIR__ . '/../config/database.php';

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
    // Fetch all programs to assign to students
    $progQuery = "SELECT id FROM programs";
    $progStmt = $db->prepare($progQuery);
    $progStmt->execute();
    $programs = $progStmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($programs)) {
        echo json_encode(["success" => false, "message" => "No programs found to associate with admissions."]);
        exit;
    }

    $first_names = ["Juan", "Maria", "Jose", "Ana", "Pedro", "Liza", "Antonio", "Carmen", "Roberto", "Rosa", "Francisco", "Teresa", "Ricardo", "Elena", "Mario", "Patricia", "Luis", "Isabel", "Miguel", "Sofia", "Carlos", "Adriana", "Fernando", "Beatriz", "Jorge", "Raquel", "Enrique", "Silvia", "Diego", "Monica", "Sergio", "Laura", "Alberto", "Cristina", "Ramon", "Victoria", "Gabriel", "Paula", "Andres", "Gloria", "Javier", "Esther", "Oscar", "Margarita", "Victor", "Marta", "Eduardo", "Sara", "Angel", "Lucia"];
    $last_names = ["Dela Cruz", "Santos", "Reyes", "Garcia", "Bautista", "Mendoza", "Pascual", "Villanueva", "Lopez", "Castillo", "Rivera", "Gonzales", "Hernandez", "Martinez", "Rodriguez", "Cruz", "Santiago", "Soriano", "De Leon", "Perez", "Tolentino", "Velasco", "Navarro", "Bernardo", "Aguilar", "Corpuz", "Valdez", "Ramos", "Espiritu", "Salvador", "Manalo", "Dimaculangan", "Abad", "Aquino", "Beltran", "Cabrera", "Dizon", "Estrella", "Flores", "Guevarra", "Ignacio", "Jimenez", "Laxamana", "Miranda", "Nicolas", "Ocampo", "Padilla", "Quizon", "Rosales", "Torres"];
    
    $statuses = ["pending"];
    $genders = ["male", "female"];

    $inserted_count = 0;
    $errors = [];

    for ($i = 0; $i < 50; $i++) {
        $first_name = $first_names[array_rand($first_names)];
        $last_name = $last_names[array_rand($last_names)];
        $email = strtolower($first_name . "." . str_replace(' ', '', $last_name) . rand(100, 999) . "@example.com");
        $application_id = "APP-" . date("Y") . "-" . str_pad(rand(1, 99999), 5, "0", STR_PAD_LEFT);
        $program_id = $programs[array_rand($programs)];
        $gender = $genders[array_rand($genders)];
        $status = $statuses[array_rand($statuses)];
        $gwa = number_format(rand(8500, 9800) / 100, 2); // Random GWA between 85.00 and 98.00
        $phone = "09" . rand(100000000, 999999999);
        $birthdate = date("Y-m-d", strtotime("-" . rand(17, 20) . " years"));
        
        $query = "INSERT INTO admissions (
                    application_id,
                    program_id,
                    first_name,
                    last_name,
                    email,
                    phone,
                    birthdate,
                    gender,
                    address,
                    high_school,
                    last_school,
                    year_graduated,
                    gwa,
                    admission_type,
                    status,
                    submitted_at,
                    form_data
                  ) VALUES (
                    :application_id,
                    :program_id,
                    :first_name,
                    :last_name,
                    :email,
                    :phone,
                    :birthdate,
                    :gender,
                    'Sample Address, City, Province',
                    'Sample High School',
                    'Sample High School',
                    '2025',
                    :gwa,
                    'freshman',
                    :status,
                    NOW(),
                    '{}'
                  )";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':application_id', $application_id);
        $stmt->bindParam(':program_id', $program_id);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':birthdate', $birthdate);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':gwa', $gwa);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            $inserted_count++;
        } else {
            $errors[] = "Failed to insert record $i: " . implode(", ", $stmt->errorInfo());
        }
    }

    echo json_encode([
        "success" => true,
        "message" => "$inserted_count records inserted successfully.",
        "errors" => $errors
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "An error occurred: " . $e->getMessage()
    ]);
}
