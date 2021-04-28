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

//Including database and making object
require __DIR__.'/config/Database.php';

$database = new Database();
$db = $database->getConnection();

//Get data
$data = json_decode(file_get_contents("php://input"));
$returnData = [];

//If request method is not post
if($_SERVER["REQUEST_METHOD"] != "POST") {
    $returnData = msg(0, 404, 'Page introuvable');
}

//Checking empty fields
else if(!isset($data->email) 
    || !isset($data->oldEmail)
    || !isset($data->password)
    || empty(trim($data->email))
    || empty(trim($data->oldEmail))
    || empty(trim($data->password))
    ) {
        $fields = ['fields' => ['email', 'oldEmail', 'password']];
        $returnData = msg(0, 422, 'ALL_INPUT_INCOMPLETE', $fields);
    }

//If there are no empty fields then
else {
    $email = trim($data->email);
    $oldEmail = trim($data->oldEmail);
    $password = trim($data->password);

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $returnData = msg(0, 422, 'INVALID_EMAIL_ADDRESS');
    } else if(strlen($password) < 8) {
        $returnData = msg(0, 422, 'INVALID_PASSWORD');
    } else {
        try {
            if($password == "EMPTY_PASSWORD") {
                $updateUserInfos = "UPDATE `users` SET `email` = :email WHERE `email` = :oldEmail";
            } else {
                $updateUserInfos = "UPDATE `users` SET `email` = :email, `password` = :password WHERE `email` = :oldEmail";
            }

            $updateUserInfosStmt = $db->prepare($updateUserInfos);

            //Data binding
            $updateUserInfosStmt->bindValue(':email', $email, PDO::PARAM_STR);
            $updateUserInfosStmt->bindValue(':oldEmail', $oldEmail, PDO::PARAM_STR);

            if($password != "EMPTY_PASSWORD") {
                $updateUserInfosStmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);    
            }

            $updateUserInfosStmt->execute();

            $returnData = msg(1, 201, 'UPDATE_USER_INFOS_SUCCESS');
        } catch(PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    }
}   

echo json_encode($returnData);