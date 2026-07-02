<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;

class BuscadorController {
    
    public function buscar() {
        //controlo si el usuario ha escrito algo en la barra de búsqueda
        $termino = isset($_GET['q']) ? trim($_GET['q']) : '';
        
        $usuarios_encontrados = [];
        $fotos_encontradas = [];

        // Solo buscamos en la base de datos si ha escrito algo
        if (!empty($termino)) {
            $db = new Database();
            $conexion = $db->getConnection();

            if ($conexion) {
                // El comodín % le dice a MySQL que busque ese texto en cualquier parte de la palabra
                $busqueda_sql = '%' . $termino . '%';

                //Buscamos usuario
                $sqlUsuarios = "SELECT id, nickname, nombre, zona FROM usuarios 
                                WHERE nickname LIKE :termino OR nombre LIKE :termino 
                                LIMIT 10";
                $stmtUsu = $conexion->prepare($sqlUsuarios);
                $stmtUsu->execute([':termino' => $busqueda_sql]);
                $usuarios_encontrados = $stmtUsu->fetchAll(PDO::FETCH_ASSOC);

                //Buscamos Foto
                $sqlFotos = "SELECT f.*, u.nickname FROM fotos f 
                             JOIN usuarios u ON f.id_usuario = u.id 
                             WHERE f.titulo LIKE :termino 
                                OR f.etiqueta LIKE :termino 
                                OR f.ubicacion LIKE :termino 
                             ORDER BY f.subido_en DESC 
                             LIMIT 20";
                $stmtFot = $conexion->prepare($sqlFotos);
                $stmtFot->execute([':termino' => $busqueda_sql]);
                $fotos_encontradas = $stmtFot->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        //cargamos visat con resultadoss
        require_once __DIR__ . '/../../views/buscador/buscar.php';
    }
}
