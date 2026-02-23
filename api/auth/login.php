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

            // Determine redirect URL
            $redirect = '/CNESIS/index.php';
            if ($user['role'] === 'admin') {
                $redirect = '/CNESIS/views/admin/features/dashboard.php';
            } elseif ($user['role'] === 'faculty') {
                // $redirect = '/CNESIS/views/faculty/dashboard.php';
                $redirect = '/CNESIS/index.php'; // Placeholder
            }

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'role' => $user['role'],
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

    // 2. Check EMAIL_VERIFICATIONS table (Students/Applicants)
    $stmt = $db->prepare("SELECT * FROM email_verifications 
                          WHERE email = ? AND status = 'verified' 
                          ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$identifier]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student && isset($student['password_hash']) && $student['password_hash']) {
        if (password_verify($password, $student['password_hash'])) {
            // Login successful
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['verified_email'] = $student['email'];
            $_SESSION['student_type'] = 'freshman';
            $_SESSION['portal_token'] = $student['portal_token'];
            $_SESSION['role'] = 'student';

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'role' => 'student',
                'portal_token' => $student['portal_token'],
                'redirect' => '/CNESIS/views/user/admission-portal.php?token=' . $student['portal_token']
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
