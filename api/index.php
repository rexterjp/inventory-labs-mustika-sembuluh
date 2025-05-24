<?php
// CORS configuration
$allowed_origins = array(
    'https://www.mustikasembuluhlabs.my.id',
    'https://mustikasembuluhlabs.my.id',
    'http://localhost:3000'
);

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $origin);
}

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// Start session
session_start();

require_once '../controllers/JenisBarangController.php';
require_once '../controllers/SatuanController.php';
require_once '../controllers/BarangController.php';
require_once '../controllers/BarangMasukController.php';
require_once '../controllers/BarangKeluarController.php';
require_once '../controllers/UserController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Get the endpoint
$endpoint = $uri[count($uri)-2];

// Get the action
$action = $uri[count($uri)-1];

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Get the request body
$data = json_decode(file_get_contents("php://input"));

// Route the request
switch($endpoint) {
    case 'dashboard':
        $barangMasukController = new BarangMasukController();
        $barangKeluarController = new BarangKeluarController();
        $barangController = new BarangController();
        
        switch($action) {
            case 'statistik':
                $totalMasuk = $barangMasukController->getTotalMasuk();
                $totalMasukHariIni = $barangMasukController->getTotalMasukHariIni();
                $totalKeluar = $barangKeluarController->getTotalKeluar();
                $totalKeluarHariIni = $barangKeluarController->getTotalKeluarHariIni();
                
                $statistik = array(
                    "total_masuk" => $totalMasuk,
                    "total_masuk_hari_ini" => $totalMasukHariIni,
                    "total_keluar" => $totalKeluar,
                    "total_keluar_hari_ini" => $totalKeluarHariIni
                );
                
                http_response_code(200);
                echo json_encode($statistik);
                break;
                
            case 'low-stock':
                $barangController->getLowStock();
                break;
                
            case 'transaksi-terbaru':
                $transaksi = array();
                
                // Get 5 transaksi barang masuk terbaru
                $barangMasuk = $barangMasukController->getAll();
                $barangMasuk = array_slice($barangMasuk, 0, 5);
                foreach($barangMasuk as $bm) {
                    $transaksi[] = array(
                        "id" => $bm["id"],
                        "tanggal" => $bm["tanggal"],
                        "jenis" => "masuk",
                        "barang_nama" => $bm["barang_nama"],
                        "jumlah" => $bm["jumlah"],
                        "keterangan" => $bm["keterangan"]
                    );
                }
                
                // Get 5 transaksi barang keluar terbaru
                $barangKeluar = $barangKeluarController->getAll();
                $barangKeluar = array_slice($barangKeluar, 0, 5);
                foreach($barangKeluar as $bk) {
                    $transaksi[] = array(
                        "id" => $bk["id"],
                        "tanggal" => $bk["tanggal"],
                        "jenis" => "keluar",
                        "barang_nama" => $bk["barang_nama"],
                        "jumlah" => $bk["jumlah"],
                        "keterangan" => $bk["keterangan"]
                    );
                }
                
                // Sort by tanggal descending
                usort($transaksi, function($a, $b) {
                    return strtotime($b["tanggal"]) - strtotime($a["tanggal"]);
                });
                
                // Get only 5 transaksi terbaru
                $transaksi = array_slice($transaksi, 0, 5);
                
                http_response_code(200);
                echo json_encode($transaksi);
                break;
        }
        break;

    case 'jenis-barang':
        $controller = new JenisBarangController();
        switch($method) {
            case 'GET':
                if($action) {
                    $controller->getOne($action);
                } else {
                    $controller->getAll();
                }
                break;
            case 'POST':
                $controller->create($data);
                break;
            case 'PUT':
                $controller->update($data);
                break;
            case 'DELETE':
                $controller->delete($action);
                break;
        }
        break;

    case 'satuan':
        $controller = new SatuanController();
        switch($method) {
            case 'GET':
                if($action) {
                    $controller->getOne($action);
                } else {
                    $controller->getAll();
                }
                break;
            case 'POST':
                $controller->create($data);
                break;
            case 'PUT':
                $controller->update($data);
                break;
            case 'DELETE':
                $controller->delete($action);
                break;
        }
        break;

    case 'barang':
        $controller = new BarangController();
        switch($method) {
            case 'GET':
                if($action == 'low-stock') {
                    $controller->getLowStock();
                } else if($action) {
                    $controller->getOne($action);
                } else {
                    $controller->getAll();
                }
                break;
            case 'POST':
                $controller->create($data);
                break;
            case 'PUT':
                $controller->update($data);
                break;
            case 'DELETE':
                $controller->delete($action);
                break;
        }
        break;

    case 'barang-masuk':
        $controller = new BarangMasukController();
        switch($method) {
            case 'GET':
                if($action == 'date-range') {
                    $start_date = $_GET['start_date'] ?? date('Y-m-d');
                    $end_date = $_GET['end_date'] ?? date('Y-m-d');
                    $controller->getByDateRange($start_date, $end_date);
                } else if($action == 'total-date-range') {
                    $start_date = $_GET['start_date'] ?? date('Y-m-d');
                    $end_date = $_GET['end_date'] ?? date('Y-m-d');
                    $controller->getTotalMasukByDateRange($start_date, $end_date);
                } else if($action) {
                    $controller->getOne($action);
                } else {
                    $controller->getAll();
                }
                break;
            case 'POST':
                $controller->create($data);
                break;
            case 'PUT':
                $controller->update($data);
                break;
            case 'DELETE':
                $controller->delete($action);
                break;
        }
        break;

    case 'barang-keluar':
        $controller = new BarangKeluarController();
        switch($method) {
            case 'GET':
                if($action == 'date-range') {
                    $start_date = $_GET['start_date'] ?? date('Y-m-d');
                    $end_date = $_GET['end_date'] ?? date('Y-m-d');
                    $controller->getByDateRange($start_date, $end_date);
                } else if($action == 'total-date-range') {
                    $start_date = $_GET['start_date'] ?? date('Y-m-d');
                    $end_date = $_GET['end_date'] ?? date('Y-m-d');
                    $controller->getTotalKeluarByDateRange($start_date, $end_date);
                } else if($action) {
                    $controller->getOne($action);
                } else {
                    $controller->getAll();
                }
                break;
            case 'POST':
                $controller->create($data);
                break;
            case 'PUT':
                $controller->update($data);
                break;
            case 'DELETE':
                $controller->delete($action);
                break;
        }
        break;

    case 'user':
        $controller = new UserController();
        switch($method) {
            case 'GET':
                if($action == 'me') {
                    $controller->getCurrentUser();
                } else if($action) {
                    $controller->getOne($action);
                } else {
                    $controller->getAll();
                }
                break;
            case 'POST':
                if($action == 'login') {
                    $controller->login($data);
                } else {
                    $controller->create($data);
                }
                break;
            case 'PUT':
                $controller->update($data);
                break;
            case 'DELETE':
                $controller->delete($action);
                break;
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(array("message" => "Endpoint tidak ditemukan."));
        break;
}
?> 