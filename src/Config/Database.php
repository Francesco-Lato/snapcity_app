<?php
namespace App\Config;

class Database {
    // Credenciales de mi base de datos
    private $host = "localhost";
    private $db_name = "urban_photo_app";
    private $username = "root";
    private $password = ""; 
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Conexión PDO
            $this->conn = new \PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            
            
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
        } catch(\PDOException $exception) {
            
            echo "Error de conexión a la Base de Datos: " . $exception->getMessage();
        }

        return $this->conn;
    }
}