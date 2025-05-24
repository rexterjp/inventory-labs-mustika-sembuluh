<?php
require_once '../config/database.php';
require_once '../models/Satuan.php';

class SatuanController {
    private $db;
    private $satuan;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->satuan = new Satuan($this->db);
    }

    public function getAll() {
        $stmt = $this->satuan->read();
        $satuan_arr = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $satuan_item = array(
                "id" => $id,
                "nama" => $nama
            );
            array_push($satuan_arr, $satuan_item);
        }
        
        http_response_code(200);
        echo json_encode($satuan_arr);
    }

    public function create($data) {
        $this->satuan->nama = $data->nama;
        
        if($this->satuan->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Satuan berhasil dibuat."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat membuat satuan."));
        }
    }

    public function update($data) {
        $this->satuan->id = $data->id;
        $this->satuan->nama = $data->nama;
        
        if($this->satuan->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Satuan berhasil diperbarui."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat memperbarui satuan."));
        }
    }

    public function delete($id) {
        $this->satuan->id = $id;
        
        if($this->satuan->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Satuan berhasil dihapus."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat menghapus satuan."));
        }
    }
}
?> 