<?php
require_once '../config/database.php';
require_once '../models/BarangKeluar.php';
require_once '../models/Barang.php';

class BarangKeluarController {
    private $db;
    private $barangKeluar;
    private $barang;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->barangKeluar = new BarangKeluar($this->db);
        $this->barang = new Barang($this->db);
    }

    public function getAll() {
        $stmt = $this->barangKeluar->read();
        $barang_keluar_arr = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $barang_keluar_item = array(
                "id" => $id,
                "barang_id" => $barang_id,
                "barang_kode" => $barang_kode,
                "nama_barang" => $nama_barang,
                "jenis_nama" => $jenis_nama,
                "satuan_nama" => $satuan_nama,
                "jumlah" => $jumlah,
                "tanggal" => $tanggal,
                "matriks_analisa" => $matriks_analisa,
                "preparasi_analisa" => $preparasi_analisa,
                "nama_pengambil" => $nama_pengambil,
                "keterangan" => $keterangan,
                "created_at" => $created_at,
                "updated_at" => $updated_at
            );
            array_push($barang_keluar_arr, $barang_keluar_item);
        }
        
        http_response_code(200);
        echo json_encode($barang_keluar_arr);
    }

    public function create($data) {
        if(empty($data->barang_id) || empty($data->jumlah) || empty($data->tanggal) || 
           empty($data->matriks_analisa) || empty($data->preparasi_analisa) || empty($data->nama_pengambil)) {
            http_response_code(400);
            echo json_encode(array("message" => "Data tidak lengkap."));
            return;
        }

        // Cek stok barang
        $this->barang->id = $data->barang_id;
        if(!$this->barang->getById()) {
            http_response_code(404);
            echo json_encode(array("message" => "Barang tidak ditemukan."));
            return;
        }

        if($this->barang->stok < $data->jumlah) {
            http_response_code(400);
            echo json_encode(array("message" => "Stok tidak mencukupi."));
            return;
        }

        $this->barangKeluar->barang_id = $data->barang_id;
        $this->barangKeluar->jumlah = $data->jumlah;
        $this->barangKeluar->tanggal = $data->tanggal;
        $this->barangKeluar->matriks_analisa = $data->matriks_analisa;
        $this->barangKeluar->preparasi_analisa = $data->preparasi_analisa;
        $this->barangKeluar->nama_pengambil = $data->nama_pengambil;
        $this->barangKeluar->keterangan = $data->keterangan ?? '';
        
        if($this->barangKeluar->create()) {
            // Update stok barang
            $this->barang->stok -= $data->jumlah;
            $this->barang->updateStok();
            
            http_response_code(201);
            echo json_encode(array("message" => "Barang keluar berhasil dicatat."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat mencatat barang keluar."));
        }
    }

    public function update($data) {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(array("message" => "Akses ditolak. Hanya admin yang diizinkan."));
            return;
        }

        if(empty($data->id) || empty($data->barang_id) || empty($data->jumlah) || empty($data->tanggal) ||
           empty($data->matriks_analisa) || empty($data->preparasi_analisa) || empty($data->nama_pengambil)) {
            http_response_code(400);
            echo json_encode(array("message" => "Data tidak lengkap."));
            return;
        }

        $this->barangKeluar->id = $data->id;
        $this->barangKeluar->barang_id = $data->barang_id;
        $this->barangKeluar->jumlah = $data->jumlah;
        $this->barangKeluar->tanggal = $data->tanggal;
        $this->barangKeluar->matriks_analisa = $data->matriks_analisa;
        $this->barangKeluar->preparasi_analisa = $data->preparasi_analisa;
        $this->barangKeluar->nama_pengambil = $data->nama_pengambil;
        $this->barangKeluar->keterangan = $data->keterangan ?? '';
        
        if($this->barangKeluar->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Barang keluar berhasil diperbarui."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat memperbarui barang keluar."));
        }
    }

    public function delete($id) {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(array("message" => "Akses ditolak. Hanya admin yang diizinkan."));
            return;
        }

        if(empty($id)) {
            http_response_code(400);
            echo json_encode(array("message" => "ID tidak valid."));
            return;
        }

        $this->barangKeluar->id = $id;
        
        if($this->barangKeluar->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Barang keluar berhasil dihapus."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat menghapus barang keluar."));
        }
    }

    public function getTotalKeluar() {
        $total = $this->barangKeluar->getTotalKeluar();
        http_response_code(200);
        echo json_encode(array("total" => $total));
    }

    public function getTotalKeluarHariIni() {
        $total = $this->barangKeluar->getTotalKeluarHariIni();
        http_response_code(200);
        echo json_encode(array("total" => $total));
    }

    public function getByDateRange($start_date, $end_date) {
        $stmt = $this->barangKeluar->readByDateRange($start_date, $end_date);
        $barang_keluar_arr = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $barang_keluar_item = array(
                "id" => $id,
                "barang_id" => $barang_id,
                "barang_kode" => $barang_kode,
                "nama_barang" => $nama_barang,
                "jenis_nama" => $jenis_nama,
                "satuan_nama" => $satuan_nama,
                "jumlah" => $jumlah,
                "tanggal" => $tanggal,
                "matriks_analisa" => $matriks_analisa,
                "preparasi_analisa" => $preparasi_analisa,
                "nama_pengambil" => $nama_pengambil,
                "keterangan" => $keterangan,
                "created_at" => $created_at,
                "updated_at" => $updated_at
            );
            array_push($barang_keluar_arr, $barang_keluar_item);
        }
        
        http_response_code(200);
        echo json_encode($barang_keluar_arr);
    }

    public function getTotalKeluarByDateRange($start_date, $end_date) {
        $total = $this->barangKeluar->getTotalKeluarByDateRange($start_date, $end_date);
        http_response_code(200);
        echo json_encode(array("total" => $total));
    }
}
?> 