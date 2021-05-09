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

$returnData = [];
$numbers = [];

// Total compte
try {
    $checkAccount = "SELECT COUNT(*) FROM `users`";
    $checkAccountStmt = $db->prepare($checkAccount);
    $checkAccountStmt->execute();

    if($checkAccountStmt->rowCount()) {
        $row = $checkAccountStmt->fetch(PDO::FETCH_ASSOC);
        array_push($numbers, $row['COUNT(*)']);
    }    
} catch(PDOException $e) {
    $returnData = msg(0, 500, $e->getMessage());
}

//Compte active
try {
    $checkAccount = "SELECT COUNT(*) FROM `users` WHERE `status` = 1";
    $checkAccountStmt = $db->prepare($checkAccount);
    $checkAccountStmt->execute();

    if($checkAccountStmt->rowCount()) {
        $row = $checkAccountStmt->fetch(PDO::FETCH_ASSOC);
        array_push($numbers, $row['COUNT(*)']);
    }    
} catch(PDOException $e) {
    $returnData = msg(0, 500, $e->getMessage());
}

// Compte desactive
try {
    $checkAccount = "SELECT COUNT(*) FROM `users` WHERE `status` = 0";
    $checkAccountStmt = $db->prepare($checkAccount);
    $checkAccountStmt->execute();

    if($checkAccountStmt->rowCount()) {
        $row = $checkAccountStmt->fetch(PDO::FETCH_ASSOC);
        array_push($numbers, $row['COUNT(*)']);
    }    
} catch(PDOException $e) {
    $returnData = msg(0, 500, $e->getMessage());
}

$returnData = msg(1, 201, "SUCCESS", array($numbers));

return $returnData;