<?php
// servidor/UsuariosDB.php

/**
 * Clase UsuariosDB
 * Maneja la capa de acceso a datos (Persistencia) para el módulo de usuarios.
 */
class UsuariosDB {
    // Parámetros de configuración de la topología de red local
    private $host = "localhost";
    private $db_name = "sgii_db";
    private $username = "root"; // Usuario por defecto de su instancia MySQL local
    private $password = "ingbrandon105"; // contraseña 
    public $conn;

    /**
     * Establece y retorna la instancia de conexión PDO.
     */
    public function getConnection() {
        $this->conn = null;
        try {
            // Instanciación del túnel PDO con prevención de inyección SQL inherente
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Configuración de codificación de caracteres y modo de errores
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Emisión de log de error en caso de fallo de handshake
            echo json_encode(["status" => "error", "message" => "Fallo crítico de infraestructura: " . $exception->getMessage()]);
            exit;
        }
        return $this->conn;
    }
}
?>