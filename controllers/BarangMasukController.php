<?php
require_once '../config/database.php';
require_once '../models/BarangMasuk.php';
require_once '../models/Barang.php';

class BarangMasukController {
    private $db;
    private $barangMasuk;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->barangMasuk = new BarangMasuk($this->db);
    }

    public function getAll() {
        $stmt = $this->barangMasuk->read();
        $barangMasuk_arr = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $barangMasuk_item = array(
                "id" => $id,
                "barang_id" => $barang_id,
                "barang_kode" => $barang_kode,
                "barang_nama" => $barang_nama,
                "jenis_nama" => $jenis_nama,
                "satuan_nama" => $satuan_nama,
                "jumlah" => $jumlah,
                "tanggal" => $tanggal,
                "keterangan" => $keterangan,
                "created_at" => $created_at,
                "updated_at" => $updated_at
            );
            array_push($barangMasuk_arr, $barangMasuk_item);
        }
        
        http_response_code(200);
        echo json_encode($barangMasuk_arr);
    }

    public function create($data) {
        if(empty($data->barang_id) || empty($data->jumlah) || empty($data->tanggal)) {
            http_response_code(400);
            echo json_encode(array("message" => "Data tidak lengkap."));
            return;
        }

        // Cek stok barang
        $barang = new Barang($this->db);
        $barang->id = $data->barang_id;
        if(!$barang->getById()) {
            http_response_code(404);
            echo json_encode(array("message" => "Barang tidak ditemukan."));
            return;
        }

        $this->barangMasuk->barang_id = $data->barang_id;
        $this->barangMasuk->jumlah = $data->jumlah;
        $this->barangMasuk->tanggal = $data->tanggal;
        $this->barangMasuk->keterangan = $data->keterangan ?? '';
        
        if($this->barangMasuk->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Barang masuk berhasil dibuat."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat membuat barang masuk."));
        }
    }

    public function getTotalMasuk() {
        $total = $this->barangMasuk->getTotalMasuk();
        http_response_code(200);
        echo json_encode(array("total" => $total));
    }

    public function getTotalMasukHariIni() {
        $total = $this->barangMasuk->getTotalMasukHariIni();
        http_response_code(200);
        echo json_encode(array("total" => $total));
    }

    public function getByDateRange($start_date, $end_date) {
        $stmt = $this->barangMasuk->readByDateRange($start_date, $end_date);
        $barangMasuk_arr = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $barangMasuk_item = array(
                "id" => $id,
                "barang_id" => $barang_id,
                "barang_kode" => $barang_kode,
                "barang_nama" => $barang_nama,
                "jenis_nama" => $jenis_nama,
                "satuan_nama" => $satuan_nama,
                "jumlah" => $jumlah,
                "tanggal" => $tanggal,
                "keterangan" => $keterangan,
                "created_at" => $created_at,
                "updated_at" => $updated_at
            );
            array_push($barangMasuk_arr, $barangMasuk_item);
        }
        
        http_response_code(200);
        echo json_encode($barangMasuk_arr);
    }

    public function getTotalMasukByDateRange($start_date, $end_date) {
        $total = $this->barangMasuk->getTotalMasukByDateRange($start_date, $end_date);
        http_response_code(200);
        echo json_encode(array("total" => $total));
    }
}
?> 