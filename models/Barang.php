<?php
class Barang {
    private $conn;
    private $table_name = "barang";

    public $id;
    public $kode;
    public $nama;
    public $jenis_id;
    public $satuan_id;
    public $lot_number;
    public $katalog_number;
    public $kadaluarsa;
    public $stok;
    public $minimal_stok;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT b.*, j.nama as jenis_nama, s.nama as satuan_nama 
                 FROM " . $this->table_name . " b
                 LEFT JOIN jenis_barang j ON b.jenis_id = j.id
                 LEFT JOIN satuan s ON b.satuan_id = s.id
                 ORDER BY b.nama";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT b.*, j.nama as jenis_nama, s.nama as satuan_nama 
                 FROM " . $this->table_name . " b
                 LEFT JOIN jenis_barang j ON b.jenis_id = j.id
                 LEFT JOIN satuan s ON b.satuan_id = s.id
                 WHERE b.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->kode = $row['kode'];
            $this->nama = $row['nama'];
            $this->jenis_id = $row['jenis_id'];
            $this->satuan_id = $row['satuan_id'];
            $this->lot_number = $row['lot_number'];
            $this->katalog_number = $row['katalog_number'];
            $this->kadaluarsa = $row['kadaluarsa'];
            $this->stok = $row['stok'];
            $this->minimal_stok = $row['minimal_stok'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                 SET kode = :kode,
                     nama = :nama,
                     jenis_id = :jenis_id,
                     satuan_id = :satuan_id,
                     lot_number = :lot_number,
                     katalog_number = :katalog_number,
                     kadaluarsa = :kadaluarsa,
                     stok = :stok,
                     minimal_stok = :minimal_stok,
                     created_at = NOW(),
                     updated_at = NOW()";

        $stmt = $this->conn->prepare($query);

        $this->kode = htmlspecialchars(strip_tags($this->kode));
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->jenis_id = htmlspecialchars(strip_tags($this->jenis_id));
        $this->satuan_id = htmlspecialchars(strip_tags($this->satuan_id));
        $this->lot_number = htmlspecialchars(strip_tags($this->lot_number));
        $this->katalog_number = htmlspecialchars(strip_tags($this->katalog_number));
        $this->kadaluarsa = htmlspecialchars(strip_tags($this->kadaluarsa));
        $this->stok = htmlspecialchars(strip_tags($this->stok));
        $this->minimal_stok = htmlspecialchars(strip_tags($this->minimal_stok));

        $stmt->bindParam(":kode", $this->kode);
        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":jenis_id", $this->jenis_id);
        $stmt->bindParam(":satuan_id", $this->satuan_id);
        $stmt->bindParam(":lot_number", $this->lot_number);
        $stmt->bindParam(":katalog_number", $this->katalog_number);
        $stmt->bindParam(":kadaluarsa", $this->kadaluarsa);
        $stmt->bindParam(":stok", $this->stok);
        $stmt->bindParam(":minimal_stok", $this->minimal_stok);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                 SET kode = :kode,
                     nama = :nama,
                     jenis_id = :jenis_id,
                     satuan_id = :satuan_id,
                     lot_number = :lot_number,
                     katalog_number = :katalog_number,
                     kadaluarsa = :kadaluarsa,
                     stok = :stok,
                     minimal_stok = :minimal_stok,
                     updated_at = NOW()
                 WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->kode = htmlspecialchars(strip_tags($this->kode));
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->jenis_id = htmlspecialchars(strip_tags($this->jenis_id));
        $this->satuan_id = htmlspecialchars(strip_tags($this->satuan_id));
        $this->lot_number = htmlspecialchars(strip_tags($this->lot_number));
        $this->katalog_number = htmlspecialchars(strip_tags($this->katalog_number));
        $this->kadaluarsa = htmlspecialchars(strip_tags($this->kadaluarsa));
        $this->stok = htmlspecialchars(strip_tags($this->stok));
        $this->minimal_stok = htmlspecialchars(strip_tags($this->minimal_stok));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":kode", $this->kode);
        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":jenis_id", $this->jenis_id);
        $stmt->bindParam(":satuan_id", $this->satuan_id);
        $stmt->bindParam(":lot_number", $this->lot_number);
        $stmt->bindParam(":katalog_number", $this->katalog_number);
        $stmt->bindParam(":kadaluarsa", $this->kadaluarsa);
        $stmt->bindParam(":stok", $this->stok);
        $stmt->bindParam(":minimal_stok", $this->minimal_stok);
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

    public function getLowStock() {
        $query = "SELECT b.*, j.nama as jenis_nama, s.nama as satuan_nama 
                 FROM " . $this->table_name . " b
                 LEFT JOIN jenis_barang j ON b.jenis_id = j.id
                 LEFT JOIN satuan s ON b.satuan_id = s.id
                 WHERE b.stok <= b.minimal_stok
                 ORDER BY b.stok ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById() {
        $query = "SELECT b.*, j.nama as jenis_nama, s.nama as satuan_nama 
                 FROM " . $this->table_name . " b
                 LEFT JOIN jenis_barang j ON b.jenis_id = j.id
                 LEFT JOIN satuan s ON b.satuan_id = s.id
                 WHERE b.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->kode = $row['kode'];
            $this->nama = $row['nama'];
            $this->jenis_id = $row['jenis_id'];
            $this->satuan_id = $row['satuan_id'];
            $this->lot_number = $row['lot_number'];
            $this->katalog_number = $row['katalog_number'];
            $this->kadaluarsa = $row['kadaluarsa'];
            $this->stok = $row['stok'];
            $this->minimal_stok = $row['minimal_stok'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function updateStok($jumlah = null) {
        if ($jumlah !== null) {
            $query = "UPDATE " . $this->table_name . "
                     SET stok = stok + :jumlah,
                         updated_at = NOW()
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":jumlah", $jumlah);
        } else {
            $query = "UPDATE " . $this->table_name . " 
                     SET stok = :stok, 
                         updated_at = NOW() 
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":stok", $this->stok);
        }
        
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }
}
?> 