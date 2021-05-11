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
$data = $_POST;
$returnData = [];

//If request method is not post
if($_SERVER["REQUEST_METHOD"] != "POST") {
    $returnData = msg(0, 404, 'Page introuvable');
}

//Checking empty fields
else if(!isset($data['email']) 
    || empty(trim($data['email']))
    ) {
        $fields = ['fields' => ['email']];
        $returnData = msg(0, 422, 'ALL_INPUT_INCOMPLETE', $fields);
    }

//If there are no empty fields then
else {
    $email = trim($data['email']);

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $returnData = msg(0, 422, 'INVALID_EMAIL_ADDRESS');
    } else {
        try {
            $deleteAccount = "DELETE FROM `users` WHERE `email` = :email";
            $deleteAccountStmt = $db->prepare($deleteAccount);
            $deleteAccountStmt->bindValue(':email', $email, PDO::PARAM_STR);
            $deleteAccountStmt->execute();

            $returnData = msg(1, 201, 'DELETE_ACCOUNT_SUCCESS');
        } catch(PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    }
}   

echo json_encode($returnData);