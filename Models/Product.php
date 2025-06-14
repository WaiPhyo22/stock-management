<?php
// models/Product.php

require_once __DIR__ . '/../core/Database.php';

class Product {
    private $conn;
    private $table = 'products';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll($limit = 10, $offset = 0, $sort = 'id', $order = 'asc') {
        $allowedSorts = ['id', 'name', 'price', 'quantity_available'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'id';
        $order = strtolower($order) === 'desc' ? 'DESC' : 'ASC';
        
        $sql = "SELECT * FROM {$this->table} ORDER BY $sort $order LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM {$this->table}");
        return $stmt->fetchColumn();
    }

    public function create($name, $price, $quantity) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (name, price, quantity_available) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $price, $quantity]);
    }

    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $name, $price, $quantity) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET name=?, price=?, quantity_available=? WHERE id=?");
        return $stmt->execute([$name, $price, $quantity, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        return $stmt->execute([$id]);
    }
}
