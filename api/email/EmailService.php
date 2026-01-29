<?php
/**
 * Professional Email Service Class
 * Handles all email operations with proper error handling and logging
 */

class EmailService {
    private $db;
    private $config;
    private $logger;
    
    public function __construct($database) {
        $this->db = $database;
        $this->config = $this->loadEmailConfig();
        $this->logger = new EmailLogger();
    }
    
    /**
     * Send professional email with attachments
     */
    public function sendEmail($data) {
        try {
            // Validate input
            $this->validateInput($data);
            
            // Prepare email
            $email = $this->prepareEmail($data);
            
            // Send using PHPMailer
            $result = $this->sendWithPHPMailer($email);
            
            // Log the attempt
            $this->logger->logEmailAttempt($email, $result);
            
            return $result;
            
        } catch (Exception $e) {
            $this->logger->logError($e->getMessage());
            throw new EmailServiceException("Email sending failed: " . $e->getMessage());
        }
    }
    
    /**
     * Load email configuration
     */
    private function loadEmailConfig() {
        // Hardcoded config for reliability
        return [
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_username' => 'belugaw6@gmail.com',
            'smtp_password' => 'klotmfurniohmmjo',
            'encryption' => 'tls',
            'from_email' => 'belugaw6@gmail.com',
            'from_name' => 'Colegio De Naujan',
            'reply_to' => 'belugaw6@gmail.com'
        ];
    }
    
    /**
     * Validate input data
     */
    private function validateInput($data) {
        $required = ['recipient_email'];
        foreach ($required as $field) {
            if (!isset($data->$field) || empty($data->$field)) {
                throw new EmailServiceException("Missing required field: $field");
            }
        }
        
        // Validate email format
        if (!filter_var($data->recipient_email, FILTER_VALIDATE_EMAIL)) {
            throw new EmailServiceException("Invalid email format");
        }
    }
    
    /**
     * Prepare email object
     */
    private function prepareEmail($data) {
        $email = new stdClass();
        $email->to = $data->recipient_email;
        $email->subject = $data->custom_subject ?? 'Message from Colegio De Naujan';
        $email->message = $data->custom_message ?? 'This is a message from Colegio De Naujan.';
        $email->type = $data->email_type ?? 'custom';
        $email->admission_id = $data->admission_id ?? null;
        
        // Process attachments
        $email->attachments = [];
        if (isset($data->attachments) && is_array($data->attachments)) {
            foreach ($data->attachments as $attachment) {
                $email->attachments[] = $this->processAttachment($attachment);
            }
        }
        
        // Create HTML email
        $email->html = $this->createHTMLEmail($email);
        
        return $email;
    }
    
    /**
     * Process attachment
     */
    private function processAttachment($attachment) {
        $filePath = $attachment['path'];
        
        // Resolve path
        if (strpos($filePath, 'assets/') === 0) {
            $fullPath = '../' . $filePath;
        } else {
            $fullPath = $filePath;
        }
        
        // Validate file
        if (!file_exists($fullPath)) {
            throw new EmailServiceException("Attachment file not found: $fullPath");
        }
        
        if (!is_readable($fullPath)) {
            throw new EmailServiceException("Attachment file not readable: $fullPath");
        }
        
        return [
            'path' => $fullPath,
            'name' => $attachment['name'] ?? basename($fullPath),
            'size' => filesize($fullPath),
            'type' => mime_content_type($fullPath)
        ];
    }
    
    /**
     * Create HTML email
     */
    private function createHTMLEmail($email) {
        $personalization = '';
        if ($email->admission_id) {
            $personalization = $this->getPersonalization($email->admission_id);
        }
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($email->subject) . '</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
        .header { background: #1a365d; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .header h1 { margin: 0; font-size: 28px; }
        .header p { margin: 10px 0 0 0; opacity: 0.9; }
        .content { padding: 30px; background: #f9f9f9; border-left: 1px solid #ddd; border-right: 1px solid #ddd; }
        .content h2 { color: #1a365d; margin-top: 0; }
        .content p { margin-bottom: 20px; }
        .footer { background: #2c5282; color: white; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
        .footer p { margin: 5px 0; }
        .btn { display: inline-block; padding: 12px 24px; background: #d4af37; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .attachment-info { background: #e2e8f0; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Colegio De Naujan</h1>
        <p>Official Communication</p>
    </div>
    <div class="content">
        ' . $personalization . '
        <h2>' . htmlspecialchars($email->subject) . '</h2>
        <div style="white-space: pre-wrap;">' . nl2br(htmlspecialchars($email->message)) . '</div>';
        
        // Add attachment info
        if (!empty($email->attachments)) {
            $html .= '<div class="attachment-info">
                <h3>Attachments:</h3>';
            foreach ($email->attachments as $attachment) {
                $html .= '<p>📎 ' . htmlspecialchars($attachment['name']) . ' (' . $this->formatFileSize($attachment['size']) . ')</p>';
            }
            $html .= '</div>';
        }
        
        $html .= '
    </div>
    <div class="footer">
        <p>&copy; 2026 Colegio De Naujan. All rights reserved.</p>
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>If you have questions, please contact the admissions office directly.</p>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Get personalization data
     */
    private function getPersonalization($admissionId) {
        try {
            $query = "SELECT a.*, p.title as program_title 
                     FROM admissions a 
                     LEFT JOIN programs p ON a.program_id = p.id 
                     WHERE a.id = ? OR a.application_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$admissionId, $admissionId]);
            $admission = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admission) {
                return '<p>Dear <strong>' . htmlspecialchars($admission['first_name']) . ' ' . htmlspecialchars($admission['last_name']) . '</strong>,</p>
                        <p>Your application for <strong>' . htmlspecialchars($admission['program_title']) . '</strong> has been processed.</p>
                        <p><strong>Application ID:</strong> ' . htmlspecialchars($admission['application_id']) . '</p>
                        <hr>';
            }
        } catch (Exception $e) {
            $this->logger->logError("Personalization error: " . $e->getMessage());
        }
        
        return '';
    }
    
    /**
     * Send email using PHPMailer
     */
    private function sendWithPHPMailer($email) {
        if (!file_exists('../../vendor/autoload.php')) {
            throw new EmailServiceException("PHPMailer library not found");
        }
        
        require_once '../../vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configure SMTP
        $mail->isSMTP();
        $mail->Host = $this->config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $this->config['smtp_username'];
        $mail->Password = $this->config['smtp_password'];
        $mail->SMTPSecure = $this->config['encryption'];
        $mail->Port = $this->config['smtp_port'];
        $mail->CharSet = 'UTF-8';
        
        // Set recipients
        $mail->setFrom($this->config['from_email'], $this->config['from_name']);
        $mail->addAddress($email->to);
        
        if ($this->config['reply_to']) {
            $mail->addReplyTo($this->config['reply_to'], $this->config['from_name']);
        }
        
        // Set content
        $mail->isHTML(true);
        $mail->Subject = $email->subject;
        $mail->Body = $email->html;
        $mail->AltBody = strip_tags($email->html);
        
        // Add attachments
        foreach ($email->attachments as $attachment) {
            $mail->addAttachment($attachment['path'], $attachment['name']);
        }
        
        // Send email
        $result = $mail->send();
        
        if (!$result) {
            throw new EmailServiceException("SMTP Error: " . $mail->ErrorInfo);
        }
        
        return [
            'success' => true,
            'message_id' => $mail->getLastMessageID(),
            'attachments_sent' => count($email->attachments)
        ];
    }
    
    /**
     * Format file size
     */
    private function formatFileSize($bytes) {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}

/**
 * Email Service Exception
 */
class EmailServiceException extends Exception {
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * Email Logger
 */
class EmailLogger {
    private $logFile;
    
    public function __construct() {
        $this->logFile = '../logs/email_' . date('Y-m-d') . '.log';
        $this->ensureLogDirectory();
    }
    
    public function logEmailAttempt($email, $result) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'to' => $email->to,
            'subject' => $email->subject,
            'type' => $email->type,
            'admission_id' => $email->admission_id,
            'attachments' => count($email->attachments),
            'success' => $result['success'] ?? false,
            'message_id' => $result['message_id'] ?? null
        ];
        
        $this->writeLog($logEntry);
    }
    
    public function logError($error) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'error',
            'message' => $error
        ];
        
        $this->writeLog($logEntry);
    }
    
    private function writeLog($entry) {
        $logLine = json_encode($entry) . "\n";
        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    private function ensureLogDirectory() {
        $logDir = dirname($this->logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
}
?>
