<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../config/database.php';

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if (empty($data->email) || empty($data->password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    // Find the latest verified verification record for this email
    $stmt = $db->prepare("SELECT * FROM email_verifications 
                          WHERE email = ? AND status = 'verified' 
                          ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$data->email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && isset($user['password_hash']) && $user['password_hash']) {
        if (password_verify($data->password, $user['password_hash'])) {
            // Login successful
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['verified_email'] = $user['email'];
            $_SESSION['student_type'] = 'freshman';
            $_SESSION['portal_token'] = $user['portal_token'];

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'portal_token' => $user['portal_token']
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
        }
    } else {
        // User not found or no password set (magic link user)
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or account not set up for password login']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
