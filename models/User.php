<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;
    public $nama;
    public $role;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT id, username, nama, role, created_at, updated_at 
                 FROM " . $this->table_name . " 
                 ORDER BY nama";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                 SET username = :username,
                     password = :password,
                     nama = :nama,
                     role = :role,
                     created_at = NOW(),
                     updated_at = NOW()";

        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->role = htmlspecialchars(strip_tags($this->role));

        // Hash password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":role", $this->role);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        // Jika password diupdate
        if(!empty($this->password)) {
            $query = "UPDATE " . $this->table_name . "
                     SET username = :username,
                         password = :password,
                         nama = :nama,
                         role = :role,
                         updated_at = NOW()
                     WHERE id = :id";
        } else {
            $query = "UPDATE " . $this->table_name . "
                     SET username = :username,
                         nama = :nama,
                         role = :role,
                         updated_at = NOW()
                     WHERE id = :id";
        }

        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":id", $this->id);

        if(!empty($this->password)) {
            // Hash password baru
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $password_hash);
        }

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

    public function login() {
        $query = "SELECT id, username, password, nama, role 
                 FROM " . $this->table_name . " 
                 WHERE username = ?";
        
        $stmt = $this->conn->prepare($query);
        $this->username = htmlspecialchars(strip_tags($this->username));
        $stmt->bindParam(1, $this->username);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->nama = $row['nama'];
                $this->role = $row['role'];
                return true;
            }
        }
        return false;
    }

    public function getById() {
        $query = "SELECT id, username, nama, role
                FROM " . $this->table_name . "
                WHERE id = ?
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->username = $row['username'];
            $this->nama = $row['nama'];
            $this->role = $row['role'];
            return true;
        }
        return false;
    }
}
?> 