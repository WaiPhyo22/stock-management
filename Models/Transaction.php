<?php
// models/Transaction.php

require_once __DIR__ . '/../core/Database.php';

class Transaction {
    private $conn;
    private $table = 'transactions';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getPaginated($limit = 10, $offset = 0) {
        $stmt = $this->conn->prepare("
            SELECT t.*, u.name AS user_name, p.name AS product_name
            FROM {$this->table} t
            JOIN users u ON t.user_id = u.id
            JOIN products p ON t.product_id = p.id
            ORDER BY t.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM {$this->table}");
        return $stmt->fetchColumn();
    }

    // Create transaction with stock check & update
    public function create($product_id, $quantity, $user_id) {
        // Start transaction to ensure atomicity
        $this->conn->beginTransaction();

        try {
            // 1. Check product stock
            $stmt = $this->conn->prepare("SELECT quantity_available FROM products WHERE id = ? FOR UPDATE");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception("Product not found.");
            }

            if ($product['quantity_available'] < $quantity) {
                throw new Exception("Insufficient stock available.");
            }

            // 2. Insert transaction record
            $stmt = $this->conn->prepare("INSERT INTO {$this->table} (product_id, quantity, user_id, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$product_id, $quantity, $user_id]);

            // 3. Update product quantity
            $newQty = $product['quantity_available'] - $quantity;
            $stmt = $this->conn->prepare("UPDATE products SET quantity_available = ? WHERE id = ?");
            $stmt->execute([$newQty, $product_id]);

            // Commit transaction
            $this->conn->commit();

            return true;

        } catch (Exception $e) {
            // Rollback on error
            $this->conn->rollBack();
            return $e->getMessage();
        }
    }

    public function findById($id) {
        $stmt = $this->conn->prepare("
            SELECT t.*, u.name AS user_name, p.name AS product_name
            FROM {$this->table} t
            JOIN users u ON t.user_id = u.id
            JOIN products p ON t.product_id = p.id
            WHERE t.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}