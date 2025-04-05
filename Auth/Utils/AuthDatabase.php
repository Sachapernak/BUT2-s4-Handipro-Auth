<?php

namespace Auth\Utils;
use PDO;
use PDOException;

require_once __DIR__ . '/../../config/Secrets.php';

class AuthDatabase {
    private static ?AuthDatabase $instance = null;
    private PDO $conn;
    function __construct(){
        $host = DBurl;
        $dbname = DB;
        $user = DBuser;
        $password = DBpassword;
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};port=3306;dbname={$dbname};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->conn = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }

    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new AuthDatabase();
        }
        return self::$instance;
    }

    public function getConnection(){
        return $this->conn;
    }




}
?>