<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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

function normalizeEmailPart($value) {
    $s = is_null($value) ? '' : (string)$value;
    $s = trim($s);
    $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
    $s = strtolower($s);
    $s = preg_replace('/[^a-z0-9]+/', '.', $s);
    $s = preg_replace('/\.+/', '.', $s);
    $s = preg_replace('/^\./', '', $s);
    $s = preg_replace('/\.$/', '', $s);
    return $s;
}

function buildStudentEmailFromName($firstName, $middleName, $lastName, &$usedEmails) {
    $domain = 'colegiodenaujan.edu.ph';
    $first = normalizeEmailPart($firstName);
    $middle = normalizeEmailPart($middleName);
    $last = normalizeEmailPart($lastName);

    $base = implode('.', array_values(array_filter([$first, $last], function($v) { return $v !== ''; })));
    if ($base === '') {
        $base = 'student';
    }

    $local = $base;
    if (isset($usedEmails[$local]) && $middle !== '') {
        $alt = implode('.', array_values(array_filter([$first, substr($middle, 0, 1), $last], function($v) { return $v !== ''; })));
        if ($alt !== '' && !isset($usedEmails[$alt])) {
            $local = $alt;
        }
    }

    if (!isset($usedEmails[$local])) {
        $usedEmails[$local] = 1;
        return $local . '@' . $domain;
    }

    $usedEmails[$local] += 1;
    return $local . $usedEmails[$local] . '@' . $domain;
}

try {
    $rawInput = file_get_contents("php://input");
    $data = json_decode($rawInput, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid JSON"]);
        exit;
    }

    $confirm = isset($data['confirm']) ? $data['confirm'] : null;
    if ($confirm !== 'REBUILD_EMAILS') {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Missing confirmation"]);
        exit;
    }

    $stmt = $db->prepare("SELECT id, first_name, middle_name, last_name FROM students ORDER BY last_name, first_name, id");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $usedEmails = [];
    $update = $db->prepare("UPDATE students SET email = :email WHERE id = :id");

    $updated = 0;
    foreach ($rows as $row) {
        $email = buildStudentEmailFromName($row['first_name'], $row['middle_name'], $row['last_name'], $usedEmails);
        $update->execute([
            ':email' => $email,
            ':id' => $row['id']
        ]);
        $updated += 1;
    }

    echo json_encode([
        "success" => true,
        "message" => "Student emails rebuilt",
        "updated" => $updated
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>

