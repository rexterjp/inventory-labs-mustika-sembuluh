<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
require_once '../../models/Barang.php';

$database = new Database();
$db = $database->getConnection();
$barang = new Barang($db);

$stmt = $barang->read();
$num = $stmt->rowCount();

if($num > 0) {
    $barang_arr = array();
    $barang_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $barang_item = array(
            "id" => $id,
            "kode" => $kode,
            "nama" => $nama,
            "kategori" => $kategori,
            "stok" => $stok,
            "satuan" => $satuan,
            "harga" => $harga
        );

        array_push($barang_arr["records"], $barang_item);
    }

    http_response_code(200);
    echo json_encode($barang_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Tidak ada data barang."));
}
?> 