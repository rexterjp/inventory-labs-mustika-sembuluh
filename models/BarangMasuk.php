<?php
class BarangMasuk {
    private $conn;
    private $table_name = "barang_masuk";

    public $id;
    public $barang_id;
    public $jumlah;
    public $tanggal;
    public $keterangan;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT bm.*, b.kode as barang_kode, b.nama as barang_nama,
                        j.nama as jenis_nama, s.nama as satuan_nama
                 FROM " . $this->table_name . " bm
                 LEFT JOIN barang b ON bm.barang_id = b.id
                 LEFT JOIN jenis_barang j ON b.jenis_id = j.id
                 LEFT JOIN satuan s ON b.satuan_id = s.id
                 ORDER BY bm.tanggal DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                 SET barang_id = :barang_id,
                     jumlah = :jumlah,
                     tanggal = :tanggal,
                     keterangan = :keterangan,
                     created_at = NOW(),
                     updated_at = NOW()";

        $stmt = $this->conn->prepare($query);

        $this->barang_id = htmlspecialchars(strip_tags($this->barang_id));
        $this->jumlah = htmlspecialchars(strip_tags($this->jumlah));
        $this->tanggal = htmlspecialchars(strip_tags($this->tanggal));
        $this->keterangan = htmlspecialchars(strip_tags($this->keterangan));

        $stmt->bindParam(":barang_id", $this->barang_id);
        $stmt->bindParam(":jumlah", $this->jumlah);
        $stmt->bindParam(":tanggal", $this->tanggal);
        $stmt->bindParam(":keterangan", $this->keterangan);

        if($stmt->execute()) {
            // Update stok barang
            $barang = new Barang($this->conn);
            $barang->id = $this->barang_id;
            $barang->updateStok($this->jumlah);
            return true;
        }
        return false;
    }

    public function getTotalMasuk() {
        $query = "SELECT SUM(jumlah) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function getTotalMasukHariIni() {
        $query = "SELECT SUM(jumlah) as total FROM " . $this->table_name . 
                 " WHERE DATE(tanggal) = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function readByDateRange($start_date, $end_date) {
        $query = "SELECT bm.*, b.kode as barang_kode, b.nama as barang_nama,
                        j.nama as jenis_nama, s.nama as satuan_nama
                 FROM " . $this->table_name . " bm
                 LEFT JOIN barang b ON bm.barang_id = b.id
                 LEFT JOIN jenis_barang j ON b.jenis_id = j.id
                 LEFT JOIN satuan s ON b.satuan_id = s.id
                 WHERE bm.tanggal BETWEEN :start_date AND :end_date
                 ORDER BY bm.tanggal DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->execute();
        return $stmt;
    }

    public function getTotalMasukByDateRange($start_date, $end_date) {
        $query = "SELECT SUM(jumlah) as total FROM " . $this->table_name . 
                 " WHERE tanggal BETWEEN :start_date AND :end_date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }
}
?> 