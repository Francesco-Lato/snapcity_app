<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;
use PDOException;

class GincanaController {
    
    // Método para verificar si el usuario tiene rol de administrador
    private function verificarAdmin() {
        if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
            // Si no eres admin, te redirigimos a la portada
            header("Location: index.php");
            exit();
        }
    }

    public function mostrarCrear() {
        $this->verificarAdmin(); 
        require_once __DIR__ . '/../../views/admin/crear_gincana.php';
    }

    public function procesarCrear() {
        $this->verificarAdmin(); 

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            
            $zona = trim($_POST['zona']);
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'];
            
            $latitud = !empty($_POST['latitud']) ? $_POST['latitud'] : null;
            $longitud = !empty($_POST['longitud']) ? $_POST['longitud'] : null;
            $radio = !empty($_POST['radio']) ? $_POST['radio'] : 500;
            
            $etiqueta = trim($_POST['etiqueta']);
            // Aseguramos que la etiqueta empiece por #
            if (strpos($etiqueta, '#') !== 0) {
                $etiqueta = '#' . $etiqueta;
            }
            $etiqueta = str_replace(' ', '', $etiqueta); 

            $db = new Database();
            $conexion = $db->getConnection();

            if ($conexion) {
                // Estilo para los mensajes de respuesta
                $head = "<html><head><link rel='stylesheet' href='css/global.css'><link rel='stylesheet' href='css/admin-gincana.css'></head><body class='auth-body'>";
                $footer = "</body></html>";

                try {
                    // Formateamos las fechas para SQL
                    $fecha_inicio_formato = date('Y-m-d H:i:s', strtotime($fecha_inicio));
                    $fecha_fin_formato = date('Y-m-d H:i:s', strtotime($fecha_fin));

                    $sql = "INSERT INTO gincanas (zona, fecha_inicio, fecha_fin, etiqueta, latitud, longitud, radio) 
                            VALUES (:zona, :fecha_inicio, :fecha_fin, :etiqueta, :latitud, :longitud, :radio)";
                    
                    $stmt = $conexion->prepare($sql);
                    $stmt->execute([
                        ':zona' => $zona,
                        ':fecha_inicio' => $fecha_inicio_formato,
                        ':fecha_fin' => $fecha_fin_formato,
                        ':etiqueta' => $etiqueta,
                        ':latitud' => $latitud,
                        ':longitud' => $longitud,
                        ':radio' => $radio
                    ]);

                    // MENSAJE DE ÉXITO
                    echo $head;
                    echo "<div class='auth-container admin-card' style='max-width:500px; text-align:center;'>";
                    echo "<h2>¡Gincana <span>Creada!</span></h2>";
                    echo "<div style='margin: 20px 0; color: #888;'>";
                    echo "<p>Zona: <strong>$zona</strong></p>";
                    echo "<p>Etiqueta oficial: <strong style='color:#ff9800;'>$etiqueta</strong></p>";
                    echo "</div>";
                    echo "<a href='index.php' class='btn-admin' style='text-decoration:none; display:block;'>Volver al inicio</a>";
                    echo "</div>";
                    echo $footer;

                } catch (PDOException $e) {
                    echo $head;
                    echo "<div class='auth-container admin-card' style='max-width:500px; text-align:center; border-top-color: #f44336;'>";
                    if ($e->getCode() == 23000) {
                        echo "<h2 style='color:#f44336;'>Error de Etiqueta</h2>";
                        echo "<p style='color:#888; margin: 20px 0;'>La etiqueta <strong style='color:#fff;'>$etiqueta</strong> ya está en uso. Por favor, elige una diferente.</p>";
                    } else {
                        echo "<h2 style='color:#f44336;'>Error Técnico</h2>";
                        echo "<p style='color:#888; margin: 20px 0;'>" . $e->getMessage() . "</p>";
                    }
                    echo "<a href='javascript:history.back()' class='btn-secundario' style='text-decoration:none; display:block;'>Intentar de nuevo</a>";
                    echo "</div>";
                    echo $footer;
                }
            }
        }
    }

    public function listarActivas() {
        $db = new Database();
        $conexion = $db->getConnection();

        if ($conexion) {
            $sql = "SELECT * FROM gincanas ORDER BY fecha_inicio DESC";
            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            $gincanas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            require_once __DIR__ . '/../../views/galeria/gincanas.php';
        }
    }

    public function verGaleria() {
        if (!isset($_GET['etiqueta'])) {
            header("Location: index.php?action=gincanas");
            exit();
        }

        $etiqueta = $_GET['etiqueta'];
        $id_usuario = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

        $db = new \App\Config\Database();
        $conexion = $db->getConnection();

        if ($conexion) {
            // 1. Obtener los datos de la gincana
            $sqlGincana = "SELECT * FROM gincanas WHERE etiqueta = :etiqueta";
            $stmtG = $conexion->prepare($sqlGincana);
            $stmtG->execute([':etiqueta' => $etiqueta]);
            $datos_gincana = $stmtG->fetch(\PDO::FETCH_ASSOC);

            if (!$datos_gincana) {
                header("Location: index.php?action=gincanas");
                exit();
            }

            // 2. Obtener las fotos y sus votos
            $sqlFotos = "SELECT f.*, u.nickname, COUNT(v.id) as total_votos 
                         FROM fotos f 
                         JOIN usuarios u ON f.id_usuario = u.id 
                         LEFT JOIN votos v ON f.id = v.id_foto 
                         WHERE f.etiqueta = :etiqueta 
                         GROUP BY f.id 
                         ORDER BY total_votos DESC, f.subido_en DESC";
            
            $stmtF = $conexion->prepare($sqlFotos);
            $stmtF->execute([':etiqueta' => $etiqueta]);
            $fotos_gincana = $stmtF->fetchAll(\PDO::FETCH_ASSOC);

            // 3. Datos de votos del usuario logueado
            $mis_votos = [];
            $votos_usados = 0;
            if ($id_usuario) {
                $sqlVotos = "SELECT v.id_foto FROM votos v 
                             JOIN fotos f ON v.id_foto = f.id 
                             WHERE v.id_usuario = :id_usuario AND f.etiqueta = :etiqueta";
                $stmtV = $conexion->prepare($sqlVotos);
                $stmtV->execute([':id_usuario' => $id_usuario, ':etiqueta' => $etiqueta]);
                $mis_votos = $stmtV->fetchAll(\PDO::FETCH_COLUMN);
                $votos_usados = count($mis_votos);
            }

            require_once __DIR__ . '/../../views/galeria/gincana_fotos.php';
        }
    }

    public function votarFoto() {
        if (!isset($_SESSION['usuario_id']) || !isset($_GET['id_foto']) || !isset($_GET['etiqueta'])) {
            header("Location: index.php?action=login");
            exit();
        }

        $id_usuario = $_SESSION['usuario_id'];
        $id_foto = $_GET['id_foto'];
        $etiqueta = $_GET['etiqueta'];

        $db = new \App\Config\Database();
        $conexion = $db->getConnection();

        if ($conexion) {
            // Verificar si el usuario intenta votar su propia foto
            $sqlPropietario = "SELECT id_usuario FROM fotos WHERE id = :id_foto";
            $stmtProp = $conexion->prepare($sqlPropietario);
            $stmtProp->execute([':id_foto' => $id_foto]);
            $propietario_foto = $stmtProp->fetchColumn();

            if ($propietario_foto == $id_usuario) {
                header("Location: index.php?action=ver_gincana&etiqueta=" . urlencode($etiqueta) . "&error=voto_propio");
                exit();
            }

            // Comprobar si ya ha votado esa foto para quitar o poner el voto
            $sqlCheck = "SELECT COUNT(*) FROM votos WHERE id_usuario = :id_usuario AND id_foto = :id_foto";
            $stmtCheck = $conexion->prepare($sqlCheck);
            $stmtCheck->execute([':id_usuario' => $id_usuario, ':id_foto' => $id_foto]);
            $ya_voto = $stmtCheck->fetchColumn() > 0;

            if ($ya_voto) {
                // Si ya votó, quitamos el voto
                $sqlDelete = "DELETE FROM votos WHERE id_usuario = :id_usuario AND id_foto = :id_foto";
                $stmtDelete = $conexion->prepare($sqlDelete);
                $stmtDelete->execute([':id_usuario' => $id_usuario, ':id_foto' => $id_foto]);
            } else {
                // Si no ha votado, comprobamos el límite de 3 votos por gincana
                $sqlCount = "SELECT COUNT(*) FROM votos v 
                             JOIN fotos f ON v.id_foto = f.id 
                             WHERE v.id_usuario = :id_usuario AND f.etiqueta = :etiqueta";
                $stmtCount = $conexion->prepare($sqlCount);
                $stmtCount->execute([':id_usuario' => $id_usuario, ':etiqueta' => $etiqueta]);
                $votos_gastados = $stmtCount->fetchColumn();

                if ($votos_gastados < 3) {
                    $sqlInsert = "INSERT INTO votos (id_usuario, id_foto) VALUES (:id_usuario, :id_foto)";
                    $stmtInsert = $conexion->prepare($sqlInsert);
                    $stmtInsert->execute([':id_usuario' => $id_usuario, ':id_foto' => $id_foto]);
                } else {
                    // Límite de 3 alcanzado
                    header("Location: index.php?action=ver_gincana&etiqueta=" . urlencode($etiqueta) . "&error=limite_votos");
                    exit();
                }
            }

            header("Location: index.php?action=ver_gincana&etiqueta=" . urlencode($etiqueta));
            exit();
        }
    }
}