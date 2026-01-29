<?php
// Disable all error output and enable output buffering
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

header("Content-Type: application/json");

try {
    // Get data
    $input = file_get_contents('php://input');
    $data = json_decode($input);
    
    if (!$data || !isset($data->recipient_email)) {
        ob_end_clean();
        echo json_encode(["success" => false, "message" => "No email provided"]);
        exit;
    }
    
    $to = $data->recipient_email;
    $subject = $data->custom_subject ?? 'Test from Colegio De Naujan';
    $message = $data->custom_message ?? 'This is a test message.';
    
    // Try basic mail() function first
    $headers = "From: Colegio De Naujan <belugaw6@gmail.com>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $htmlMessage = "<html><body><h2>Colegio De Naujan</h2><p>$message</p><hr><small>Sent: " . date('Y-m-d H:i:s') . "</small></body></html>";
    
    $sent = mail($to, $subject, $htmlMessage, $headers);
    
    ob_end_clean();
    
    if ($sent) {
        echo json_encode([
            "success" => true,
            "message" => "Email sent successfully!",
            "method" => "mail() function",
            "debug" => [
                "to" => $to,
                "subject" => $subject,
                "message_length" => strlen($message),
                "timestamp" => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "mail() function failed",
            "debug" => [
                "to" => $to,
                "subject" => $subject,
                "headers" => $headers,
                "message_length" => strlen($message),
                "mail_return" => error_get_last()['message'] ?? 'Unknown error',
                "timestamp" => date('Y-m-d H:i:s')
            ]
        ]);
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        "success" => false,
        "message" => "Exception: " . $e->getMessage(),
        "debug" => [
            "error_type" => get_class($e),
            "error_message" => $e->getMessage(),
            "timestamp" => date('Y-m-d H:i:s')
        ]
    ]);
}
?>
