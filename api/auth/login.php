<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../config/database.php';

// Get posted data
$data = json_decode(file_get_contents("php://input"));

$identifier = $data->email ?? $data->username ?? '';
$password = $data->password ?? '';

if (empty($identifier) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username/Email and password are required']);
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
    // 1. Check USERS table (Admin, Staff, Faculty)
    $stmt = $db->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) AND status = 'active' LIMIT 1");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Login successful
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['must_change_password'] = isset($user['must_change_password']) ? $user['must_change_password'] : 0;

            // Determine redirect URL — detect base path for local vs production
            $basePath = str_replace('/api/auth/login.php', '', $_SERVER['SCRIPT_NAME']);
            $redirect = $basePath . '/index.php';
            if ($user['role'] === 'admin') {
                $redirect = $basePath . '/views/admin/features/dashboard.php';
            } elseif ($user['role'] === 'faculty') {
                $redirect = $basePath . '/index.php'; // Placeholder
            } elseif ($user['role'] === 'student') {
                $redirect = $basePath . '/views/student/dashboard.php';
            }

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'role' => $user['role'],
                'must_change_password' => $_SESSION['must_change_password'],
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ],
                'redirect' => $redirect
            ]);
            exit;
        }
    }

    // If we reach here, authentication failed
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
