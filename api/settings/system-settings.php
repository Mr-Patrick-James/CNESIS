<?php
/**
 * System Settings API
 * Handles getting and updating system settings
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

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

// Create system_settings table if it doesn't exist
function createSystemSettingsTable($db) {
    $createTableQuery = "CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_type ENUM('text', 'email', 'phone', 'textarea', 'file', 'video', 'number', 'select') DEFAULT 'text',
        setting_label VARCHAR(200) NOT NULL,
        setting_group VARCHAR(50) DEFAULT 'general',
        description TEXT DEFAULT NULL,
        is_required BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_setting_key (setting_key),
        INDEX idx_setting_group (setting_group)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($createTableQuery);
    
    // Insert default settings if they don't exist
    $defaultSettings = [
        ['institution_name', 'Colegio De Naujan', 'text', 'Institution Name', 'general', 'The official name of the institution'],
        ['contact_email', 'info@colegiodenaujan.edu.ph', 'email', 'Contact Email', 'general', 'Main contact email address'],
        ['contact_phone', '(043) 123-4567', 'phone', 'Contact Phone', 'general', 'Main contact phone number'],
        ['address', 'Brgy. Sta. Cruz, Naujan, Oriental Mindoro', 'textarea', 'Address', 'general', 'Physical address of the institution'],
        ['academic_year', '2025-2026', 'select', 'Academic Year', 'general', 'Current academic year'],
        ['home_video', 'assets/videos/landingvid.mp4', 'video', 'Home Page Video', 'media', 'Background video for the home page hero section'],
        ['admin_username', 'admin_demo', 'text', 'Admin Username', 'account', 'Administrator username'],
        ['admin_email', 'admin_demo@colegio.edu', 'email', 'Admin Email', 'account', 'Administrator email address']
    ];
    
    $insertQuery = "INSERT IGNORE INTO system_settings (setting_key, setting_value, setting_type, setting_label, setting_group, description) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($insertQuery);
    
    foreach ($defaultSettings as $setting) {
        $stmt->execute($setting);
    }
}

try {
    // Create table if needed
    createSystemSettingsTable($db);
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        // Get all settings or specific group
        $group = isset($_GET['group']) ? $_GET['group'] : null;
        
        if ($group) {
            $query = "SELECT setting_key, setting_value, setting_type, setting_label, setting_group, description, is_required 
                     FROM system_settings WHERE setting_group = :group ORDER BY setting_group, setting_label";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':group', $group);
        } else {
            $query = "SELECT setting_key, setting_value, setting_type, setting_label, setting_group, description, is_required 
                     FROM system_settings ORDER BY setting_group, setting_label";
            $stmt = $db->prepare($query);
        }
        
        $stmt->execute();
        $settings = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[] = [
                'key' => $row['setting_key'],
                'value' => $row['setting_value'],
                'type' => $row['setting_type'],
                'label' => $row['setting_label'],
                'group' => $row['setting_group'],
                'description' => $row['description'],
                'required' => (bool)$row['is_required']
            ];
        }
        
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "settings" => $settings
        ]);
        
    } elseif ($method === 'POST' || $method === 'PUT') {
        // Update settings
        $rawInput = file_get_contents("php://input");
        $data = json_decode($rawInput);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Invalid JSON: " . json_last_error_msg()
            ]);
            exit;
        }
        
        if (empty($data->settings) || !is_array($data->settings)) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "No settings provided"
            ]);
            exit;
        }
        
        $updateQuery = "UPDATE system_settings SET setting_value = ?, updated_at = CURRENT_TIMESTAMP WHERE setting_key = ?";
        $updateStmt = $db->prepare($updateQuery);
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($data->settings as $setting) {
            if (!isset($setting->key) || !isset($setting->value)) {
                $errorCount++;
                $errors[] = "Invalid setting format";
                continue;
            }
            
            try {
                $updateStmt->execute([$setting->value, $setting->key]);
                $successCount++;
            } catch (PDOException $e) {
                $errorCount++;
                $errors[] = "Error updating {$setting->key}: " . $e->getMessage();
            }
        }
        
        if ($successCount > 0 && $errorCount === 0) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "Settings updated successfully",
                "updated" => $successCount
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Some settings could not be updated",
                "updated" => $successCount,
                "errors" => $errors
            ]);
        }
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
