<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
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
require __DIR__.'/../config/Database.php';

$database = new Database();
$db = $database->getConnection();

$data = $_POST;
$returnData = [];

//If request method is not post
if($_SERVER["REQUEST_METHOD"] != "POST") {
    $returnData = msg(0, 404, 'Page introuvable');
}

//Checking empty fields
else if(!isset($data['action']) || empty(trim($data['action']))) {
        $fields = ['fields' => ['action']];
        $returnData = msg(0, 422, 'ALL_INPUT_INCOMPLETE', $fields);
    }

//If there are no empty fields then
else {
    $action = trim($data['action']);
    $email = $_GET['email'];

    switch($action) {
        case 'Activer':
            $checkStatus = "UPDATE `users` SET `status` = 1 WHERE `email` = :email";
            break;
        case 'Désactiver': 
            $checkStatus = "UPDATE `users` SET `status` = 0 WHERE `email` = :email";
            break;
    }

    try {
        $checkStatusStmt = $db->prepare($checkStatus);
        $checkStatusStmt->bindValue(':email', $email, PDO::PARAM_STR);
        $checkStatusStmt->execute();

        $returnData = msg(1, 201, 'STATUS_ACCOUNT_UPDATED');
    } catch(PDOException $e) {
        $returnData = msg(0, 500, $e->getMessage());
    }
}

if($returnData['success'] == 1) {
    header('Location: ../../../dashboard.php');
} else {
    return $returnData;
}