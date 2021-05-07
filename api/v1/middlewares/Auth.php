<?php
require __DIR__.'/../config/JwtHandler.php';

class Auth extends JwtHandler{

    protected $db;
    protected $headers;
    protected $token;

    public function __construct($db, $headers) 
    {
        parent::__construct();
        $this->db = $db;
        $this->headers = $headers;
    }

    public function isAuth()
    {
        if(array_key_exists('Authorization', $this->headers) && !empty(trim($this->headers['Authorization']))) {
            $this->token = explode(" ", trim($this->headers['Authorization']));
            if(isset($this->token[1]) && !empty(trim($this->token[1]))) {
                $data = $this->_jwt_decode_data($this->token[1]);
                if(isset($data['auth']) && isset($data['data']->user_id) && $data['auth']) {
                    $user = $this->fetchUser($data['data']->user_id);
                    return $user;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return  null;
        }
    }

    protected function fetchUser($userId)
    {
        try {
            $fetchUserById = "SELECT `name`, `email` FROM `users` WHERE `id` = :id";
            $queryStmt = $this->db->prepare($fetchUserById);
            $queryStmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $queryStmt->execute();

            if($queryStmt->rowCount()) {
                $row = $queryStmt->fetch(PDO::FETCH_ASSOC);

                return [
                    'success' => 1,
                    'status' => 200,
                    'user' => $row
                ];
            } else {
                return null;
            }
        } catch(PDOException $e) {
            return null;
        }
    }
} 