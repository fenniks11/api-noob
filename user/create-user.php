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

// cek_validation data
$conn = mysqli_connect('localhost', 'root', '');
mysqli_select_db($conn, 'api-noob');
$cek = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE username='$user->username'"));
if ($cek > 0) {
    // set response code
    http_response_code(409);

    // display message: unable to create user
    echo json_encode(array("message" => "The username you entered already exists."));
} elseif (
    $cek == false && !empty($user->username) &&
    !empty($user->password) && $user->create()
) {
    // set response code
    http_response_code(200);

    // display message: user was created
    echo json_encode(array("message" => "User was created."));
} else {
    // set response code
    http_response_code(400);

    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create user."));
}

// // create the user
// if (
//     !empty($user->username) &&
//     !empty($user->password) &&
//     $user->create()
// ) {

   
// }

// // message if unable to create user
// else {

  
// }
