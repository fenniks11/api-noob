<?php
// required headers
header("Access-Control-Allow-Origin: http://localhost/api-noob/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// files needed to connect to database
include_once '../core/database.php';
include_once '../object/user.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate product object
$user = new User($db);

// submitted data will be heres

// get posted data
$data = json_decode(file_get_contents("php://input"));

// set product property values
$user->username = $data->username;
$user->password = $data->password;

// use the create() method here

// create the user
if (
    !empty($user->username) &&
    !empty($user->password) &&
    $user->create()
) {

    // set response code
    http_response_code(200);

    // display message: user was created
    echo json_encode(array("message" => "User was created."));
}

// message if unable to create user
else {

    // set response code
    http_response_code(400);

    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create user."));
}
