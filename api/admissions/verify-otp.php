<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../config/database.php';
require_once '../email/EmailService.php';

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if (empty($data->email) || empty($data->otp)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and OTP are required']);
    exit;
}

$email = $data->email;
$otp = $data->otp;

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    // Check if OTP exists and is valid
    $stmt = $db->prepare("SELECT id, expires_at FROM email_verifications 
                          WHERE email = ? AND otp_code = ? AND status = 'pending' 
                          ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$email, $otp]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $expires_at = strtotime($row['expires_at']);
        $current_time = time();

        if ($current_time <= $expires_at) {
            // Generate portal token
            $portal_token = bin2hex(random_bytes(32));
            $token_expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

            // Update OTP status to verified and set portal token
            $stmt = $db->prepare("UPDATE email_verifications SET status = 'verified', portal_token = ?, token_expires_at = ? WHERE id = ?");
            $stmt->execute([$portal_token, $token_expires_at, $row['id']]);

            // Start session and store verified email
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $student_type = isset($data->student_type) ? $data->student_type : 'freshman';
            $_SESSION['verified_email'] = $email;
            $_SESSION['student_type'] = $student_type;
            $_SESSION['portal_token'] = $portal_token;

            // Send portal link email
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            
            // Extract the base path dynamically (e.g., /CNESIS)
            $scriptName = $_SERVER['SCRIPT_NAME']; // e.g. /CNESIS/api/admissions/verify-otp.php
            $basePath = str_replace('/api/admissions/verify-otp.php', '', $scriptName);
            
            $baseUrl = $protocol . "://" . $host . $basePath;
            $portalPage = ($student_type === 'transferee') ? 'transferee-portal.php' : 'admission-portal.php';
            $portalLink = $baseUrl . "/views/user/" . $portalPage . "?token=" . $portal_token;

            $emailService = new EmailService($db);
            $emailData = new stdClass();
            $emailData->recipient_email = $email;
            $emailData->custom_subject = 'Your Admission Portal Access - Colegio De Naujan';
            $emailData->custom_message = "Your email has been successfully verified!\n\nYou can now access your personalized admission portal to track your application progress step-by-step.\n\nAccess your portal here: $portalLink\n\nThis link will expire in 24 hours.";
            $emailData->email_type = 'portal_link';

            $emailService->sendEmail($emailData);

            echo json_encode([
                'success' => true, 
                'message' => 'Email verified successfully! A portal access link has been sent to your email.',
                'portal_link' => $portalLink
            ]);
        } else {
            // Mark as expired
            $stmt = $db->prepare("UPDATE email_verifications SET status = 'expired' WHERE id = ?");
            $stmt->execute([$row['id']]);
            
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Verification code has expired']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid verification code']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>