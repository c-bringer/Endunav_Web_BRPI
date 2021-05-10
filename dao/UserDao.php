<?php
require_once("Dao.php");
require_once("User.php");

class UserDao extends Dao {
    public function __construct()
    {
        parent::__construct();
    }

    public function loginUser($email, $password)
    {
        $selectAccount = "SELECT * FROM `users` WHERE `email` = :email";

        try {
            $selectAccountStmt = $this->pdo->prepare($selectAccount);
            $selectAccountStmt->bindValue(':email', $email, PDO::PARAM_STR);
            $selectAccountStmt->execute();

            $row = $selectAccountStmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception($e->getMessage());
        }

        $isPasswordCorrect = password_verify($password, $row['password']);

        if(!$row) {
            echo "Le compte n'existe pas.";
        } else {
            if($isPasswordCorrect) {
                if($row['admin'] == 1) {
                    session_start();
                    $user = new User($row['id'], $row['name'], $row['email'], $row['status'], $row['admin']);
                    $_SESSION['user'] = $user;
                    header('Location: dashboard.php');
                } else {
                    echo "Vous devez être admnistrateur pour vous connecter.";
                }
            } else {
                echo "Mauvais mot de passe.";
            }
        }
    }

    public function disableAccount($email)
    {
        try {
            $checkStatus = "UPDATE `users` SET `status` = 0 WHERE `email` = :email";
            $checkStatusStmt = $this->pdo->prepare($checkStatus);
            $checkStatusStmt->bindValue(':email', $email, PDO::PARAM_STR);
            $checkStatusStmt->execute();
    
            echo "Compte désactivé.";
            time(2);
            header('Location: dashboard.php');
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function activateAccount($email)
    {
        try {
            $checkStatus = "UPDATE `users` SET `status` = 1 WHERE `email` = :email";
            $checkStatusStmt = $this->pdo->prepare($checkStatus);
            $checkStatusStmt->bindValue(':email', $email, PDO::PARAM_STR);
            $checkStatusStmt->execute();
    
            echo "Compte activé.";
            time(2);
            header('Location: dashboard.php');
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
}