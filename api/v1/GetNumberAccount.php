<?php
//Including database and making object
require __DIR__.'/config/Database.php';

$database = new Database();
$db = $database->getConnection();

$returnData = "";

try {
    $checkAccount = "SELECT COUNT(*) FROM `users`";
    $checkAccountStmt = $db->prepare($checkAccount);
    $checkAccountStmt->execute();

    if($checkAccountStmt->rowCount()) {
        $returnData = $checkAccountStmt->fetch(PDO::FETCH_ASSOC);
    }    
} catch(PDOException $e) {
    $returnData = msg(0, 500, $e->getMessage());
}

echo $returnData;