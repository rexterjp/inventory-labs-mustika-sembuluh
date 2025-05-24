<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
require_once '../../models/BarangKeluar.php';
require_once '../../models/Barang.php';

$database = new Database();
$db = $database->getConnection();
$barangKeluar = new BarangKeluar($db);
$barang = new Barang($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->barang_id) &&
    !empty($data->jumlah) &&
    !empty($data->user_id)
) {
    // Cek stok barang
    $barang->id = $data->barang_id;
    if($barang->readOne()) {
        if($barang->stok < $data->jumlah) {
            http_response_code(400);
            echo json_encode(array("message" => "Stok tidak mencukupi."));
            exit();
        }

        $barangKeluar->tanggal = date('Y-m-d H:i:s');
        $barangKeluar->jumlah = $data->jumlah;
        $barangKeluar->keterangan = isset($data->keterangan) ? $data->keterangan : "";
        $barangKeluar->barang_id = $data->barang_id;
        $barangKeluar->user_id = $data->user_id;

        if($barangKeluar->create()) {
            // Update stok barang
            $barang->updateStok(-$data->jumlah);
            
            http_response_code(201);
            echo json_encode(array("message" => "Data barang keluar berhasil dibuat."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Tidak dapat membuat data barang keluar."));
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Barang tidak ditemukan."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Data tidak lengkap."));
}
?> 