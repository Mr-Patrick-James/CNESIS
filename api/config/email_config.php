<?php
/**
 * Email Configuration Class
 * Manages SMTP settings and email sending functionality
 */

class EmailConfig {
    private $db;
    private $config;
    
    public function __construct($database) {
        $this->db = $database;
        $this->loadConfig();
    }
    
    /**
     * Load email configuration from database with fallback
     */
    private function loadConfig() {
        try {
            $query = "SELECT * FROM email_configs WHERE is_active = TRUE LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $this->config = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$this->config) {
                throw new Exception("No active email configuration found");
            }
        } catch (Exception $e) {
            error_log("Database email config failed: " . $e->getMessage());
            // Use fallback configuration
            $this->config = $this->getFallbackConfig();
        }
    }
    
    /**
     * Fallback email configuration
     */
    private function getFallbackConfig() {
        return [
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_username' => 'belugaw6@gmail.com',
            'smtp_password' => 'klotmfurniohmmjo',
            'encryption_type' => 'tls',
            'from_email' => 'belugaw6@gmail.com',
            'from_name' => 'Colegio De Naujan',
            'reply_to_email' => 'belugaw6@gmail.com'
        ];
    }
    
    /**
     * Get PHPMailer instance with configuration
     */
    public function getMailer() {
        if (!$this->config) {
            throw new Exception("Email configuration not loaded");
        }
        
        try {
            require_once '../vendor/autoload.php';
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtp_username'];
            $mail->Password = $this->config['smtp_password'];
            $mail->SMTPSecure = $this->config['encryption_type'];
            $mail->Port = $this->config['smtp_port'];
            
            // Recipients
            $mail->setFrom(
                $this->config['from_email'], 
                $this->config['from_name']
            );
            
            if ($this->config['reply_to_email']) {
                $mail->addReplyTo($this->config['reply_to_email']);
            }
            
            // Set default encoding
            $mail->CharSet = 'UTF-8';
            
            return $mail;
            
        } catch (Exception $e) {
            error_log("Error creating PHPMailer instance: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Send email with template
     */
    public function sendEmail($to, $subject, $body, $attachments = [], $cc = [], $bcc = []) {
        try {
            $mail = $this->getMailer();
            
            // Add recipients
            $mail->addAddress($to);
            
            foreach ($cc as $email) {
                $mail->addCC($email);
            }
            
            foreach ($bcc as $email) {
                $mail->addBCC($email);
            }
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            // Add plain text version
            $mail->AltBody = strip_tags($body);
            
            // Add attachments
            foreach ($attachments as $attachment) {
                $filePath = $attachment['path'];
                
                // Resolve relative paths
                if (strpos($filePath, 'assets/') === 0) {
                    $filePath = '../' . $filePath;
                }
                
                if (file_exists($filePath)) {
                    try {
                        $mail->addAttachment(
                            $filePath, 
                            $attachment['name'] ?? basename($filePath)
                        );
                        error_log("Attachment added: " . $filePath);
                    } catch (Exception $attachError) {
                        error_log("Failed to add attachment $filePath: " . $attachError->getMessage());
                    }
                } else {
                    error_log("Attachment file not found: " . $filePath);
                }
            }
            
            // Send email
            $result = $mail->send();
            
            // Log email
            $this->logEmail($to, $subject, $result, $mail->ErrorInfo);
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error sending email: " . $e->getMessage());
            $this->logEmail($to, $subject, false, $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Log email sending attempts
     */
    private function logEmail($to, $subject, $success, $error = null) {
        try {
            $query = "INSERT INTO email_logs (recipient_email, subject, sent_successfully, error_message, sent_at) 
                     VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$to, $subject, $success, $error]);
        } catch (Exception $e) {
            error_log("Error logging email: " . $e->getMessage());
        }
    }
    
    /**
     * Get current configuration
     */
    public function getConfig() {
        return $this->config;
    }
    
    /**
     * Update email configuration
     */
    public function updateConfig($data) {
        try {
            $query = "UPDATE email_configs SET 
                     smtp_host = ?, 
                     smtp_port = ?, 
                     smtp_username = ?, 
                     smtp_password = ?, 
                     encryption_type = ?, 
                     from_email = ?, 
                     from_name = ?, 
                     reply_to_email = ?,
                     updated_at = NOW()
                     WHERE id = ?";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                $data['smtp_host'],
                $data['smtp_port'],
                $data['smtp_username'],
                $data['smtp_password'],
                $data['encryption_type'],
                $data['from_email'],
                $data['from_name'],
                $data['reply_to_email'],
                $this->config['id']
            ]);
            
            if ($result) {
                $this->loadConfig(); // Reload configuration
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error updating email config: " . $e->getMessage());
            throw $e;
        }
    }
}
?>
