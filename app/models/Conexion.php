<?php
namespace Api\Models;
use PDO;
use PDOException;

class Conexion {
    private $con_bd;

    public function __construct() {
        $host = $_ENV['DB_HOST'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
        $dbname = $_ENV['DB_NAME'];

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $this->con_bd = new PDO($dsn, $user, $pass);
            $this->con_bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die(json_encode(["error" => "Error de conexión: " . $e->getMessage()]));
        }
    }

    public function getConnection() {
        return $this->con_bd;
    }
}