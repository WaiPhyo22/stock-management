<?php
class Database {
    private $host = "127.0.0.1";
    private $db_name = "stock_management";
    private $port = "3307";
    private $username = "root";
    private $password = "";
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            // Set error mode
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection Error: " . $e->getMessage());
        }
        return $this->conn;
    }
}
?>