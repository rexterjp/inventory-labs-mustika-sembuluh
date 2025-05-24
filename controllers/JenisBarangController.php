<?php
require_once '../config/database.php';
require_once '../models/JenisBarang.php';

class JenisBarangController {
    private $db;
    private $jenisBarang;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->jenisBarang = new JenisBarang($this->db);
    }

    public function getAll() {
        $stmt = $this->jenisBarang->read();
        $jenisBarang_arr = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $jenisBarang_item = array(
                "id" => $id,
                "nama" => $nama
            );
            array_push($jenisBarang_arr, $jenisBarang_item);
        }
        
        http_response_code(200);
        echo json_encode($jenisBarang_arr);
    }

    public function create($data) {
        $this->jenisBarang->nama = $data->nama;
        
        if($this->jenisBarang->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Jenis barang berhasil dibuat."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat membuat jenis barang."));
        }
    }

    public function update($data) {
        $this->jenisBarang->id = $data->id;
        $this->jenisBarang->nama = $data->nama;
        
        if($this->jenisBarang->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Jenis barang berhasil diperbarui."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat memperbarui jenis barang."));
        }
    }

    public function delete($id) {
        $this->jenisBarang->id = $id;
        
        if($this->jenisBarang->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Jenis barang berhasil dihapus."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat menghapus jenis barang."));
        }
    }
}
?> 