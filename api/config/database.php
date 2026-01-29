<?php
/**
 * Database Connection Configuration
 * PDO connection for CNESIS Database
 */

class Database {
    // Database credentials
    private $host = "localhost";
    private $db_name = "cnesis_db";
    private $username = "root";  // Default WAMP username
    private $password = "";      // Default WAMP password (empty)
    private $charset = "utf8mb4";
    
    public $conn;
    
    /**
     * Get database connection
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $exception) {
            // Try with default charset if utf8mb4 fails
            try {
                $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name;
                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            } catch(PDOException $exception2) {
                error_log("Connection error: " . $exception2->getMessage());
                return null;
            }
        }
        
        return $this->conn;
    }
    
    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->conn = null;
    }
}
?>
