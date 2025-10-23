<?php

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct($database = null) {
        $this->db = $database ?? $this->getDefaultDatabase();
    }
    
    // Get database connection (you'll implement this)
    private function getDefaultDatabase() {
        // Return your database connection
        // This will depend on your Database-conn.php setup
    }
    
    // Find a single record by ID
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Find all records
    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Find records with conditions
    public function where($conditions, $values = []) {
        $whereClause = implode(' AND ', array_map(fn($col) => "$col = ?", array_keys($conditions)));
        $sql = "SELECT * FROM {$this->table} WHERE $whereClause";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($conditions));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Find first record with conditions
    public function first($conditions, $values = []) {
        $whereClause = implode(' AND ', array_map(fn($col) => "$col = ?", array_keys($conditions)));
        $sql = "SELECT * FROM {$this->table} WHERE $whereClause LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($conditions));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Insert a new record
    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }
    
    // Update a record
    public function update($id, $data) {
        $setClause = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
        $sql = "UPDATE {$this->table} SET $setClause WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $values = array_values($data);
        $values[] = $id;
        $stmt->execute($values);
        return $stmt->rowCount();
    }
    
    // Delete a record
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
    
    // Count records
    public function count($conditions = []) {
        if (empty($conditions)) {
            $sql = "SELECT COUNT(*) as count FROM {$this->table}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        } else {
            $whereClause = implode(' AND ', array_map(fn($col) => "$col = ?", array_keys($conditions)));
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE $whereClause";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_values($conditions));
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    // Execute raw SQL (for complex queries)
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get the last inserted ID
    public function lastInsertId() {
        return $this->db->lastInsertId();
    }
}