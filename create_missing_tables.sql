-- Create missing tables for email system

-- TABLE: email_documents
CREATE TABLE IF NOT EXISTS email_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_name VARCHAR(255) NOT NULL COMMENT 'Original file name',
    file_path VARCHAR(500) NOT NULL COMMENT 'Server file path',
    file_size INT NOT NULL COMMENT 'File size in bytes',
    file_type VARCHAR(100) NOT NULL COMMENT 'MIME type',
    category ENUM('application-form', 'requirements', 'policies', 'general', 'templates', 'other') DEFAULT 'general' COMMENT 'Document category',
    description TEXT DEFAULT NULL COMMENT 'Document description',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Whether document is available',
    uploaded_by INT NOT NULL COMMENT 'Admin user ID who uploaded',
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When file was uploaded',
    download_count INT DEFAULT 0 COMMENT 'How many times downloaded',
    last_used TIMESTAMP NULL COMMENT 'Last used in email',
    INDEX idx_category (category),
    INDEX idx_is_active (is_active),
    INDEX idx_upload_date (upload_date),
    INDEX idx_uploaded_by (uploaded_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: admission_status_log
CREATE TABLE IF NOT EXISTS admission_status_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admission_id INT NOT NULL COMMENT 'Related admission ID',
    old_status VARCHAR(20) DEFAULT NULL COMMENT 'Previous status',
    new_status VARCHAR(20) NOT NULL COMMENT 'New status',
    action_by INT NOT NULL COMMENT 'Admin user ID who made the change',
    notes TEXT DEFAULT NULL COMMENT 'Notes about the status change',
    email_sent BOOLEAN DEFAULT FALSE COMMENT 'Whether email was sent',
    email_error TEXT DEFAULT NULL COMMENT 'Email error if sending failed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When the change was made',
    INDEX idx_admission_id (admission_id),
    INDEX idx_action_by (action_by),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (admission_id) REFERENCES admissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: email_logs
CREATE TABLE IF NOT EXISTS email_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_email VARCHAR(255) NOT NULL COMMENT 'Email address of recipient',
    subject VARCHAR(255) NOT NULL COMMENT 'Email subject',
    sent_successfully BOOLEAN NOT NULL COMMENT 'Whether email was sent successfully',
    error_message TEXT DEFAULT NULL COMMENT 'Error message if sending failed',
    email_type VARCHAR(50) DEFAULT NULL COMMENT 'Type of email (admission, acceptance, etc.)',
    admission_id INT DEFAULT NULL COMMENT 'Related admission ID if applicable',
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When email was sent',
    INDEX idx_recipient_email (recipient_email),
    INDEX idx_sent_successfully (sent_successfully),
    INDEX idx_sent_at (sent_at),
    INDEX idx_admission_id (admission_id),
    FOREIGN KEY (admission_id) REFERENCES admissions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: email_templates
CREATE TABLE IF NOT EXISTS email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_name VARCHAR(100) NOT NULL UNIQUE COMMENT 'Template identifier',
    template_type ENUM('admission', 'acceptance', 'rejection', 'reminder', 'payment', 'general') NOT NULL,
    subject VARCHAR(255) NOT NULL COMMENT 'Email subject template',
    html_body TEXT NOT NULL COMMENT 'HTML email body template',
    text_body TEXT DEFAULT NULL COMMENT 'Plain text email body template',
    variables TEXT DEFAULT NULL COMMENT 'JSON string of available variables',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Whether template is active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_template_name (template_name),
    INDEX idx_template_type (template_type),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: email_configs
CREATE TABLE IF NOT EXISTS email_configs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_name VARCHAR(100) NOT NULL DEFAULT 'default' COMMENT 'Configuration name',
    smtp_host VARCHAR(255) NOT NULL COMMENT 'SMTP server hostname',
    smtp_port INT NOT NULL DEFAULT 587 COMMENT 'SMTP port',
    smtp_username VARCHAR(255) NOT NULL COMMENT 'SMTP username',
    smtp_password VARCHAR(255) NOT NULL COMMENT 'SMTP password (encrypted)',
    encryption_type ENUM('none', 'tls', 'ssl') DEFAULT 'tls' COMMENT 'Encryption type',
    from_email VARCHAR(255) NOT NULL COMMENT 'From email address',
    from_name VARCHAR(255) NOT NULL COMMENT 'From display name',
    reply_to_email VARCHAR(255) DEFAULT NULL COMMENT 'Reply-to email address',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Whether this config is active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_config_name (config_name),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default data if not exists
INSERT IGNORE INTO email_configs (
    config_name,
    smtp_host,
    smtp_port,
    smtp_username,
    smtp_password,
    encryption_type,
    from_email,
    from_name,
    reply_to_email,
    is_active
) VALUES (
    'default',
    'smtp.gmail.com',
    587,
    'belugaw6@gmail.com',
    'klotmfurniohmmjo',
    'tls',
    'belugaw6@gmail.com',
    'Colegio De Naujan',
    'belugaw6@gmail.com',
    TRUE
);

INSERT IGNORE INTO email_templates (template_name, template_type, subject, html_body, text_body, variables) VALUES
('application_received', 'admission', 
 'Application Received - Colegio De Naujan',
 '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Application Received</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1a365d; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { background: #2c5282; color: white; padding: 15px; text-align: center; font-size: 12px; }
        .btn { display: inline-block; padding: 10px 20px; background: #d4af37; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Colegio De Naujan</h1>
            <p>Application Confirmation</p>
        </div>
        <div class="content">
            <h2>Dear {{first_name}} {{last_name}},</h2>
            <p>Thank you for your interest in <strong>{{program_title}}</strong> at Colegio De Naujan.</p>
            <p>We have received your application and it is currently under review. Your application ID is: <strong>{{application_id}}</strong></p>
        </div>
        <div class="footer">
            <p>&copy; 2026 Colegio De Naujan. All rights reserved.</p>
        </div>
    </div>
</body>
</html>',
 'Dear {{first_name}} {{last_name}},

Thank you for your interest in {{program_title}} at Colegio De Naujan.

We have received your application and it is currently under review. Your application ID is: {{application_id}}

Colegio De Naujan',
 '{"first_name": "Student first name", "last_name": "Student last name", "program_title": "Applied program", "application_id": "Application ID"}');
