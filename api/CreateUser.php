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
else if(!isset($data->name) 
    || !isset($data->email) 
    || !isset($data->password)
    || empty(trim($data->name))
    || empty(trim($data->email))
    || empty(trim($data->password))
    ) {
        $fields = ['fields' => ['name', 'email', 'password']];
        $returnData = msg(0, 422, 'ALL_INPUT_INCOMPLETE', $fields);
    }

//If there are no empty fields then
else {
    $name = trim($data->name);
    $email = trim($data->email);
    $password = trim($data->password);

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $returnData = msg(0, 422, 'INVALID_EMAIL_ADDRESS');
    } else if(strlen($password) < 8) {
        $returnData = msg(0, 422, 'INVALID_PASSWORD');
    } else if(strlen($name) < 3) {
        $returnData = msg(0, 422, 'INVALID_NAME');
    } else {
        try {
            $checkEmail = "SELECT `email` FROM `users` WHERE `email` = :email";
            $checkEmailStmt = $db->prepare($checkEmail);
            $checkEmailStmt->bindValue(':email', $email, PDO::PARAM_STR);
            $checkEmailStmt->execute();

            if($checkEmailStmt->rowCount()) {
                $returnData = msg(0, 422, 'EMAIL_ALREADY_USED');
            } else {
                $insertQuery = "INSERT INTO `users`(`name`, `email`, `password`) VALUES(:name, :email, :password)";
                $insertStmt = $db->prepare($insertQuery);

                //Data binding
                $insertStmt->bindValue(':name', htmlspecialchars(strip_tags($name)), PDO::PARAM_STR);
                $insertStmt->bindValue(':email', $email, PDO::PARAM_STR);
                $insertStmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);

                $insertStmt->execute();

                $returnData = msg(1, 201, 'CREATE_ACCOUNT_SUCCESS');
            }
        } catch(PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    }
}   

echo json_encode($returnData);