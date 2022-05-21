<?php
// required headers
header("Access-Control-Allow-Origin: http://localhost/api-noob/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// database connection will be here
// files needed to connect to database
include_once '../core/database.php';
// include_once 'core/jwt.php';
include_once '../object/user.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$user = new User($db);

// check username existence here
// get posted data
$data = json_decode(file_get_contents("php://input"));

// set product property values
$user->username = $data->username;
$username_exists = $user->usernameExists();

// check if username exists and if password is correct
if ($username_exists && password_verify($data->password, $user->password)) {
    http_response_code(200);
    echo json_encode(
        array(
            "message" => "Successful login.",
            "data" => (object)["id" => $user->id]
        )
    );
}

// login failed will be here
// login failed
else {

    // set response code
    http_response_code(401);

    // tell the user login failed
    echo json_encode(array("message" => "Login failed."));
}

// // validasi token.
// if ($jwt::is_jwt_valid($access_token, "key", "APInoob", "its a secrect")) {
//     echo json_encode(array(
//         "message" => "Successful Validate.",
//         // "jwt" => $jwt::getPayload($access_token),
//         "data" => (object)["id" => $user->id, "username" => $user->username, "password" => $user->password]
//     ));
// } else {
//     "invalid";
// }
