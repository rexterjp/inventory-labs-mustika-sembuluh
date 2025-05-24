<?php
class JenisBarang {
    private $conn;
    private $table_name = "jenis_barang";

    public $id;
    public $nama;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nama";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nama = :nama";
        $stmt = $this->conn->prepare($query);

        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $stmt->bindParam(":nama", $this->nama);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nama = :nama WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?> 