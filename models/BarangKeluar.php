<?php
class BarangKeluar {
    private $conn;
    private $table_name = "barang_keluar";

    public $id;
    public $barang_id;
    public $jumlah;
    public $tanggal;
    public $matriks_analisa;
    public $preparasi_analisa;
    public $nama_pengambil;
    public $keterangan;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT bk.*, b.nama as nama_barang, b.kode as barang_kode,
                        j.nama as jenis_nama, s.nama as satuan_nama
                 FROM " . $this->table_name . " bk
                 LEFT JOIN barang b ON bk.barang_id = b.id
                 LEFT JOIN jenis_barang j ON b.jenis_id = j.id
                 LEFT JOIN satuan s ON b.satuan_id = s.id
                 ORDER BY bk.tanggal DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT bk.*, b.nama as nama_barang, b.kode as barang_kode,
                        j.nama as jenis_nama, s.nama as satuan_nama
                FROM " . $this->table_name . " bk
                LEFT JOIN barang b ON bk.barang_id = b.id
                LEFT JOIN jenis_barang j ON b.jenis_id = j.id
                LEFT JOIN satuan s ON b.satuan_id = s.id
                WHERE bk.id = ?
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->tanggal = $row['tanggal'];
            $this->jumlah = $row['jumlah'];
            $this->keterangan = $row['keterangan'];
            $this->barang_id = $row['barang_id'];
            $this->matriks_analisa = $row['matriks_analisa'];
            $this->preparasi_analisa = $row['preparasi_analisa'];
            $this->nama_pengambil = $row['nama_pengambil'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                 SET barang_id = :barang_id,
                     jumlah = :jumlah,
                     tanggal = :tanggal,
                     matriks_analisa = :matriks_analisa,
                     preparasi_analisa = :preparasi_analisa,
                     nama_pengambil = :nama_pengambil,
                     keterangan = :keterangan,
                     created_at = NOW(),
                     updated_at = NOW()";

        $stmt = $this->conn->prepare($query);

        $this->barang_id = htmlspecialchars(strip_tags($this->barang_id));
        $this->jumlah = htmlspecialchars(strip_tags($this->jumlah));
        $this->tanggal = htmlspecialchars(strip_tags($this->tanggal));
        $this->matriks_analisa = htmlspecialchars(strip_tags($this->matriks_analisa));
        $this->preparasi_analisa = htmlspecialchars(strip_tags($this->preparasi_analisa));
        $this->nama_pengambil = htmlspecialchars(strip_tags($this->nama_pengambil));
        $this->keterangan = htmlspecialchars(strip_tags($this->keterangan));

        $stmt->bindParam(":barang_id", $this->barang_id);
        $stmt->bindParam(":jumlah", $this->jumlah);
        $stmt->bindParam(":tanggal", $this->tanggal);
        $stmt->bindParam(":matriks_analisa", $this->matriks_analisa);
        $stmt->bindParam(":preparasi_analisa", $this->preparasi_analisa);
        $stmt->bindParam(":nama_pengambil", $this->nama_pengambil);
        $stmt->bindParam(":keterangan", $this->keterangan);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                 SET barang_id = :barang_id,
                     jumlah = :jumlah,
                     tanggal = :tanggal,
                     matriks_analisa = :matriks_analisa,
                     preparasi_analisa = :preparasi_analisa,
                     nama_pengambil = :nama_pengambil,
                     keterangan = :keterangan,
                     updated_at = NOW()
                 WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->barang_id = htmlspecialchars(strip_tags($this->barang_id));
        $this->jumlah = htmlspecialchars(strip_tags($this->jumlah));
        $this->tanggal = htmlspecialchars(strip_tags($this->tanggal));
        $this->matriks_analisa = htmlspecialchars(strip_tags($this->matriks_analisa));
        $this->preparasi_analisa = htmlspecialchars(strip_tags($this->preparasi_analisa));
        $this->nama_pengambil = htmlspecialchars(strip_tags($this->nama_pengambil));
        $this->keterangan = htmlspecialchars(strip_tags($this->keterangan));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":barang_id", $this->barang_id);
        $stmt->bindParam(":jumlah", $this->jumlah);
        $stmt->bindParam(":tanggal", $this->tanggal);
        $stmt->bindParam(":matriks_analisa", $this->matriks_analisa);
        $stmt->bindParam(":preparasi_analisa", $this->preparasi_analisa);
        $stmt->bindParam(":nama_pengambil", $this->nama_pengambil);
        $stmt->bindParam(":keterangan", $this->keterangan);

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

    public function getTotalKeluar() {
        $query = "SELECT SUM(jumlah) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function getTotalKeluarHariIni() {
        $query = "SELECT SUM(jumlah) as total FROM " . $this->table_name . " WHERE DATE(tanggal) = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function readByDateRange($start_date, $end_date) {
        $query = "SELECT bk.*, b.kode as barang_kode, b.nama as nama_barang,
                        j.nama as jenis_nama, s.nama as satuan_nama
                 FROM " . $this->table_name . " bk
                 LEFT JOIN barang b ON bk.barang_id = b.id
                 LEFT JOIN jenis_barang j ON b.jenis_id = j.id
                 LEFT JOIN satuan s ON b.satuan_id = s.id
                 WHERE bk.tanggal BETWEEN :start_date AND :end_date
                 ORDER BY bk.tanggal DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->execute();
        return $stmt;
    }

    public function getTotalKeluarByDateRange($start_date, $end_date) {
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