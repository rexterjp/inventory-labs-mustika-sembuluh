<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
require_once '../../models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->username) && !empty($data->password)) {
    $user->username = $data->username;
    $user->password = $data->password;

    if($user->login()) {
        $token = bin2hex(random_bytes(32));
        
        http_response_code(200);
        echo json_encode(array(
            "message" => "Login berhasil.",
            "token" => $token,
            "user" => array(
                "id" => $user->id,
                "username" => $user->username,
                "role" => $user->role
            )
        ));
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Username atau password salah."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Data tidak lengkap."));
}
?> 