<?php
class Database
{
    //Specify your own database credentials
    private $host = "localhost";
    private $dbName = "endunav";
    private $username = "root";
    private $password = "root";
    public $pdo;

    //Get the database connection
    public function getConnection()
    {
        $this->pdo = null;

        try {
            $this->pdo = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbName, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->pdo;
    }
}
