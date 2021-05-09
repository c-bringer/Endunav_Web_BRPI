<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// function msg($success, $status, $message, $extra = [])
// {
//     return array_merge([
//         'success' => $success,
//         'status' => $status,
//         'code' => $message
//     ], $extra);
// }

//Including database and making object
// require __DIR__.'/../config/Database.php';

// $database = new Database();
$db = $database->getConnection();

$returnData = [];

try {
    $selectAccounts = "SELECT `id`, `name`, `email`, `status` FROM `users`";
    $selectAccountsStmt = $db->prepare($selectAccounts);
    $selectAccountsStmt->execute();
    $rows = $selectAccountsStmt->fetchAll(PDO::FETCH_ASSOC);

    $accounts = array();

    foreach($rows as $row) {
        array_push($accounts, $row);
    }

    $returnData = msg(1, 200, "SUCCESS", array($accounts));
} catch(PDOException $e) {
    $returnData = msg(0, 500, $e->getMessage());
}

return $returnData;