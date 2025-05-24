<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
require_once '../../models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->username) &&
    !empty($data->password)
) {
    $user->username = $data->username;
    $user->password = $data->password;
    $user->role = isset($data->role) ? $data->role : 'user';

    if($user->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "User berhasil dibuat."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Tidak dapat membuat user."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Data tidak lengkap."));
}
?> 