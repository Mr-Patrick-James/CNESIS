<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../config/database.php';
require_once '../email/EmailService.php';

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if (empty($data->email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

$email = $data->email;

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
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
    // Check if email is already verified and has an active portal token
    $stmt = $db->prepare("SELECT portal_token, token_expires_at FROM email_verifications 
                          WHERE email = ? AND status = 'verified' 
                          AND token_expires_at > NOW() 
                          ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$email]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Start session and store verified email
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['verified_email'] = $email;
        $_SESSION['student_type'] = 'freshman';
        $_SESSION['portal_token'] = $existing['portal_token'];

        echo json_encode([
            'success' => true, 
            'already_verified' => true, 
            'message' => 'Email already verified. Redirecting to your portal...',
            'portal_link' => 'views/user/admission-portal.php?token=' . $existing['portal_token']
        ]);
        exit;
    }

    // Generate OTP
    $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    // Invalidate previous OTPs for this email
    $stmt = $db->prepare("UPDATE email_verifications SET status = 'expired' WHERE email = ? AND status = 'pending'");
    $stmt->execute([$email]);

    // Save OTP to database
    $stmt = $db->prepare("INSERT INTO email_verifications (email, otp_code, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$email, $otp, $expires_at]);

    // Send email
    $emailService = new EmailService($db);
    $emailData = new stdClass();
    $emailData->recipient_email = $email;
    $emailData->custom_subject = 'Email Verification - Colegio De Naujan';
    $emailData->custom_message = "Your verification code is: $otp\n\nThis code will expire in 15 minutes.";
    $emailData->email_type = 'verification';

    $result = $emailService->sendEmail($emailData);

    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Verification code sent to your email']);
    } else {
        throw new Exception("Failed to send email");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>