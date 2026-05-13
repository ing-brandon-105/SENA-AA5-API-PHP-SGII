<?php
// servidor/UsuariosAPI.php

// 1. Configuración de Cabeceras HTTP (CORS y tipo de contenido)
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 2. Inyección de dependencias
require_once 'UsuariosDB.php';

// 3. Inicialización de la conexión
$database = new UsuariosDB();
$db = $database->getConnection();

// 4. Captura del flujo de datos asíncrono (Payload JSON entrante)
$data = json_decode(file_get_contents("php://input"));

// 5. Enrutador de lógica de negocio
if(isset($data->action)) {
    
    // ==========================================
    // MÓDULO DE AUTENTICACIÓN (LOGIN)
    // ==========================================
    if($data->action === "login") {
        if(!empty($data->correo) && !empty($data->password)) {
            
            // Preparación de la consulta (Prepared Statement) para mitigar SQL Injection
            $query = "SELECT id, password, nombre, rol_id FROM usuarios WHERE correo = :correo LIMIT 1";
            $stmt = $db->prepare($query);
            
            // Sanitización y enlace de parámetros
            $correo_limpio = htmlspecialchars(strip_tags($data->correo));
            $stmt->bindParam(":correo", $correo_limpio);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($data->password === $row['password']) {
                    
                    echo json_encode([
                        "status" => "success", 
                        "message" => "Autenticación satisfactoria",
                        "usuario" => $row['nombre']
                    ]);
                } else {
                    
                    echo json_encode(["status" => "error", "message" => "Error en la autenticación. Contraseña incorrecta."]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Error en la autenticación. El correo no existe en el sistema."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Payload incompleto. Se requiere correo y password."]);
        }
    }
    
    // ==========================================
    // MÓDULO DE REGISTRO 
    // ==========================================
    elseif($data->action === "register") {
        if(!empty($data->correo) && !empty($data->password) && !empty($data->nombre)) {
            
            // Por defecto, asignaremos rol_id = 2 (Operador) según el esquema de mi proyecto SGII
            $query = "INSERT INTO usuarios (nombre, correo, password, rol_id) VALUES (:nombre, :correo, :password, 2)";
            $stmt = $db->prepare($query);
            
            // Sanitización
            $nombre_limpio = htmlspecialchars(strip_tags($data->nombre));
            $correo_limpio = htmlspecialchars(strip_tags($data->correo));
            // En producción: $password_hash = password_hash($data->password, PASSWORD_BCRYPT);
            $password_limpia = htmlspecialchars(strip_tags($data->password)); 
            
            $stmt->bindParam(":nombre", $nombre_limpio);
            $stmt->bindParam(":correo", $correo_limpio);
            $stmt->bindParam(":password", $password_limpia);
            
            try {
                if($stmt->execute()) {
                    echo json_encode(["status" => "success", "message" => "Usuario registrado correctamente en la infraestructura SGII."]);
                }
            } catch (PDOException $e) {
                echo json_encode(["status" => "error", "message" => "Error de integridad de datos. Posible correo duplicado."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Payload incompleto para el registro."]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Acción de enrutamiento no especificada en el endpoint."]);
}
?>