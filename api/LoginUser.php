<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

function msg($success, $status, $message, $extra = [])
{
    return array_merge([
        'success' => $success,
        'status' => $status,
        'code' => $message
    ], $extra);
}

require __DIR__.'/config/Database.php';
require __DIR__.'/config/JwtHandler.php';

$database = new Database();
$db = $database->getConnection();

//Get data
$data = json_decode(file_get_contents("php://input"));
$returnData = [];

//If request method is not post
if($_SERVER["REQUEST_METHOD"] != "POST") {
    $returnData = msg(0, 404, 'Page introuvable');
}

// Checking empty fields
elseif(!isset($data->email) 
    || !isset($data->password)
    || empty(trim($data->email))
    || empty(trim($data->password))
    ) {
        $fields = ['fields' => ['email', 'password']];
        $returnData = msg(0, 422, 'ALL_INPUT_INCOMPLETE', $fields);
    }

//If there are no empty fields then
else {
    $email = trim($data->email);
    $password = trim($data->password);

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $returnData = msg(0, 422, 'INVALID_EMAIL_ADDRESS');
    } else if(strlen($password) < 8) {
        $returnData = msg(0, 422, 'INVALID_PASSWORD');
    }

    //The user is able to perform the login action
    else {
        try {
            $fetchUserByEmail = "SELECT * FROM `users` WHERE `email` = :email";
            $queryStmt = $db->prepare($fetchUserByEmail);
            $queryStmt->bindValue(':email', $email, PDO::PARAM_STR);
            $queryStmt->execute();

            //If the user is founded by email
            if($queryStmt->rowCount()) {
                $row = $queryStmt->fetch(PDO::FETCH_ASSOC);
                $checkPassword = password_verify($password, $row['password']);

                //Verifying the password, if is correct the send the login token
                if($checkPassword) {
                    $jwt = new JwtHandler();
                    $token = $jwt->_jwt_encode_data(
                        'auth0',
                        array("user_id" => $row['id'])
                    );
                    
                    $returnData = [
                        'success' => 1,
                        'code' => 'LOGIN_SUCCESS',
                        'token' => $token
                    ];
                } else {
                    $returnData = msg(0, 422, 'WRONG_PASSWORD');
                }
            } else {
                $returnData = msg(0, 422, 'NO_ACCOUNT');
            }
        } catch(PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    }
}

echo json_encode($returnData);