<?php
require_once '../config/database.php';
require_once '../models/Barang.php';

class BarangController {
    private $db;
    private $barang;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->barang = new Barang($this->db);
    }

    private function isAdmin() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(array("message" => "Akses ditolak. Hanya admin yang diizinkan."));
            return false;
        }
        return true;
    }

    public function getAll() {
        $stmt = $this->barang->read();
        $barang_arr = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $barang_item = array(
                "id" => $id,
                "kode" => $kode,
                "nama" => $nama,
                "jenis_id" => $jenis_id,
                "jenis_nama" => $jenis_nama,
                "satuan_id" => $satuan_id,
                "satuan_nama" => $satuan_nama,
                "lot_number" => $lot_number,
                "katalog_number" => $katalog_number,
                "kadaluarsa" => $kadaluarsa,
                "stok" => $stok,
                "minimal_stok" => $minimal_stok,
                "created_at" => $created_at,
                "updated_at" => $updated_at
            );
            array_push($barang_arr, $barang_item);
        }
        
        http_response_code(200);
        echo json_encode($barang_arr);
    }

    public function getOne($id) {
        $this->barang->id = $id;
        
        if($this->barang->readOne()) {
            $barang_item = array(
                "id" => $this->barang->id,
                "kode" => $this->barang->kode,
                "nama" => $this->barang->nama,
                "jenis_id" => $this->barang->jenis_id,
                "satuan_id" => $this->barang->satuan_id,
                "stok" => $this->barang->stok,
                "minimal_stok" => $this->barang->minimal_stok,
                "created_at" => $this->barang->created_at,
                "updated_at" => $this->barang->updated_at
            );
            
            http_response_code(200);
            echo json_encode($barang_item);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Barang tidak ditemukan."));
        }
    }

    public function create($data) {
        if (!$this->isAdmin()) return;

        if(empty($data->kode) || empty($data->nama) || empty($data->jenis_id) || empty($data->satuan_id)) {
            http_response_code(400);
            echo json_encode(array("message" => "Data tidak lengkap."));
            return;
        }

        $this->barang->kode = $data->kode;
        $this->barang->nama = $data->nama;
        $this->barang->jenis_id = $data->jenis_id;
        $this->barang->satuan_id = $data->satuan_id;
        $this->barang->lot_number = $data->lot_number ?? '';
        $this->barang->katalog_number = $data->katalog_number ?? '';
        $this->barang->kadaluarsa = $data->kadaluarsa ?? null;
        $this->barang->stok = $data->stok ?? 0;
        $this->barang->minimal_stok = $data->minimal_stok ?? 0;
        
        if($this->barang->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Barang berhasil dibuat."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat membuat barang."));
        }
    }

    public function update($data) {
        if (!$this->isAdmin()) return;

        if(empty($data->id) || empty($data->kode) || empty($data->nama) || empty($data->jenis_id) || empty($data->satuan_id)) {
            http_response_code(400);
            echo json_encode(array("message" => "Data tidak lengkap."));
            return;
        }

        $this->barang->id = $data->id;
        $this->barang->kode = $data->kode;
        $this->barang->nama = $data->nama;
        $this->barang->jenis_id = $data->jenis_id;
        $this->barang->satuan_id = $data->satuan_id;
        $this->barang->lot_number = $data->lot_number ?? '';
        $this->barang->katalog_number = $data->katalog_number ?? '';
        $this->barang->kadaluarsa = $data->kadaluarsa ?? null;
        $this->barang->stok = $data->stok ?? 0;
        $this->barang->minimal_stok = $data->minimal_stok ?? 0;
        
        if($this->barang->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Barang berhasil diperbarui."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat memperbarui barang."));
        }
    }

    public function delete($id) {
        if (!$this->isAdmin()) return;

        if(empty($id)) {
            http_response_code(400);
            echo json_encode(array("message" => "ID tidak valid."));
            return;
        }

        $this->barang->id = $id;
        
        if($this->barang->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Barang berhasil dihapus."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat menghapus barang."));
        }
    }

    public function getLowStock() {
        $stmt = $this->barang->getLowStock();
        $barang_arr = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $barang_item = array(
                "id" => $id,
                "kode" => $kode,
                "nama" => $nama,
                "jenis_nama" => $jenis_nama,
                "satuan_nama" => $satuan_nama,
                "stok" => $stok,
                "minimal_stok" => $minimal_stok
            );
            array_push($barang_arr, $barang_item);
        }
        
        http_response_code(200);
        echo json_encode($barang_arr);
    }
}
?> 