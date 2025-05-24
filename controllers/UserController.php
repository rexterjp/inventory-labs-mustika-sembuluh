<?php
require_once '../config/database.php';
require_once '../models/User.php';

class UserController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    // Fungsi untuk mengecek apakah user adalah admin
    private function isAdmin() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(array("message" => "Akses ditolak. Hanya admin yang diizinkan."));
            return false;
        }
        return true;
    }

    public function getAll() {
        $stmt = $this->user->read();
        $user_arr = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $user_item = array(
                "id" => $id,
                "username" => $username,
                "nama" => $nama,
                "role" => $role,
                "created_at" => $created_at,
                "updated_at" => $updated_at
            );
            array_push($user_arr, $user_item);
        }
        
        http_response_code(200);
        echo json_encode($user_arr);
    }

    public function create($data) {
        if (!$this->isAdmin()) return;

        if(empty($data->username) || empty($data->password) || empty($data->nama) || empty($data->role)) {
            http_response_code(400);
            echo json_encode(array("message" => "Data tidak lengkap."));
            return;
        }

        $this->user->username = $data->username;
        $this->user->password = $data->password;
        $this->user->nama = $data->nama;
        $this->user->role = $data->role;
        
        if($this->user->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "User berhasil dibuat."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat membuat user."));
        }
    }

    public function update($data) {
        if (!$this->isAdmin()) return;

        if(empty($data->id) || empty($data->username) || empty($data->nama) || empty($data->role)) {
            http_response_code(400);
            echo json_encode(array("message" => "Data tidak lengkap."));
            return;
        }

        $this->user->id = $data->id;
        $this->user->username = $data->username;
        $this->user->nama = $data->nama;
        $this->user->role = $data->role;
        
        if(!empty($data->password)) {
            $this->user->password = $data->password;
        }
        
        if($this->user->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "User berhasil diperbarui."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat memperbarui user."));
        }
    }

    public function delete($id) {
        if (!$this->isAdmin()) return;

        if(empty($id)) {
            http_response_code(400);
            echo json_encode(array("message" => "ID tidak valid."));
            return;
        }

        $this->user->id = $id;
        
        if($this->user->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "User berhasil dihapus."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat menghapus user."));
        }
    }

    public function login($data) {
        if(empty($data->username) || empty($data->password)) {
            http_response_code(400);
            echo json_encode(array("message" => "Username dan password harus diisi."));
            return;
        }

        $this->user->username = $data->username;
        $this->user->password = $data->password;
        
        if($this->user->login()) {
            session_start();
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['username'] = $this->user->username;
            $_SESSION['user_role'] = $this->user->role;
            
            $user_item = array(
                "id" => $this->user->id,
                "username" => $this->user->username,
                "nama" => $this->user->nama,
                "role" => $this->user->role
            );
            
            http_response_code(200);
            echo json_encode($user_item);
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Login gagal. Username atau password salah."));
        }
    }

    public function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(array("message" => "User tidak terautentikasi."));
            return;
        }

        $this->user->id = $_SESSION['user_id'];
        
        if($this->user->getById()) {
            $user_item = array(
                "id" => $this->user->id,
                "username" => $this->user->username,
                "nama" => $this->user->nama,
                "role" => $this->user->role
            );
            
            http_response_code(200);
            echo json_encode($user_item);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "User tidak ditemukan."));
        }
    }
}
?> 