<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
require_once '../../models/Barang.php';

$database = new Database();
$db = $database->getConnection();
$barang = new Barang($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->kode) &&
    !empty($data->nama) &&
    !empty($data->kategori) &&
    !empty($data->stok) &&
    !empty($data->satuan) &&
    !empty($data->harga)
) {
    $barang->kode = $data->kode;
    $barang->nama = $data->nama;
    $barang->kategori = $data->kategori;
    $barang->stok = $data->stok;
    $barang->satuan = $data->satuan;
    $barang->harga = $data->harga;

    if($barang->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Barang berhasil dibuat."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Tidak dapat membuat barang."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Data tidak lengkap."));
}
?> 