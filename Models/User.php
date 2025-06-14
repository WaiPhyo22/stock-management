<?php
require_once __DIR__ . '/../core/Database.php';

class User {
    private $conn;
    private $table = "users";

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getPaginated($limit, $offset) {
        $stmt = $this->conn->prepare("SELECT * FROM users ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function countAll() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM users");
        return $stmt->fetchColumn();
    }

    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($name, $email, $password, $role = 'user') {
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        return $stmt->execute([$name, $email, $hashed, $role]);
    }

    public function update($id, $name, $email,$password, $role) {
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?");
        return $stmt->execute([$name, $email, $password, $role, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT id, name, email, role FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>