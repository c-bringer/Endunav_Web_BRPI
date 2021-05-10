<?php
require_once("Dao.php");
require_once("User.php");

class StatsDao extends Dao {
    public function __construct()
    {
        parent::__construct();
    }

    public function getTotalAccounts()
    {
        try {
            $checkAccount = "SELECT COUNT(*) FROM `users`";
            $checkAccountStmt = $this->pdo->prepare($checkAccount);
            $checkAccountStmt->execute();

            if($checkAccountStmt->rowCount()) {
                $row = $checkAccountStmt->fetch(PDO::FETCH_ASSOC);
                $total = $row['COUNT(*)'];
            }    
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        return $total;
    }

    public function getAccountsActivated()
    {
        try {
            $checkAccount = "SELECT COUNT(*) FROM `users` WHERE `status` = 1";
            $checkAccountStmt = $this->pdo->prepare($checkAccount);
            $checkAccountStmt->execute();

            if($checkAccountStmt->rowCount()) {
                $row = $checkAccountStmt->fetch(PDO::FETCH_ASSOC);
                $total = $row['COUNT(*)'];
            }    
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        return $total;
    }

    public function getAccountsDisabled()
    {
        try {
            $checkAccount = "SELECT COUNT(*) FROM `users` WHERE `status` = 0";
            $checkAccountStmt = $this->pdo->prepare($checkAccount);
            $checkAccountStmt->execute();

            if($checkAccountStmt->rowCount()) {
                $row = $checkAccountStmt->fetch(PDO::FETCH_ASSOC);
                $total = $row['COUNT(*)'];
            }    
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        return $total;
    }

    public function getAllAccountsDetails()
    {
        try {
            $selectAccounts = "SELECT `id`, `name`, `email`, `status`, `admin` FROM `users`";
            $selectAccountsStmt = $this->pdo->prepare($selectAccounts);
            $selectAccountsStmt->execute();
            $rows = $selectAccountsStmt->fetchAll(PDO::FETCH_ASSOC);
        
            $accounts = array();
        
            foreach($rows as $row) {
                array_push($accounts, $row);
            }
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        return $accounts;
    }
}