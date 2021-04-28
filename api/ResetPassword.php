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
require __DIR__.'/libs/Utils.php';

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
else if(!isset($data->email)) {
        $fields = ['fields' => ['email']];
        $returnData = msg(0, 422, 'ALL_INPUT_INCOMPLETE', $fields);
}

//If there are no empty fields then
else {
    $email = trim($data->email);

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $returnData = msg(0, 422, 'INVALID_EMAIL_ADDRESS');
    } else {
        try {
            $checkEmail = "SELECT `email` FROM `users` WHERE `email` = :email";
            $checkEmailStmt = $db->prepare($checkEmail);
            $checkEmailStmt->bindValue(':email', $email, PDO::PARAM_STR);
            $checkEmailStmt->execute();

            if(!$checkEmailStmt->rowCount()) {
                $returnData = msg(0, 422, 'NO_ACCOUNT');
            } else {
                $newPassword = Utils::generatePassword();

                $to = 'contact@bikelock.fr';
                $subject = 'Nouveau mot de passe';

                $headers = "From: no-reply@bikelock.fr\r\n";
                $headers .= "Reply-To: no-reply@bikelock.fr\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                $message = '<html><body>';
                $message .= '<h1>Nouveau mot de passe</h1>';
                $message .= '<p>Voici votre nouveau mot de passe : ' . $newPassword . '</p>';
                $message .= '</body></html>';

                mail($to, $subject, $message, $headers);

                $returnData = msg(1, 201, 'NEW_PASSWORD_SEND');
            }
        } catch(PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    }
}   

echo json_encode($returnData);