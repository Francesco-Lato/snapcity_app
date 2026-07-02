<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;

class FotoController {
    
    /**
     * Plantilla maestra para mostrar mensajes de error o límites sin romper el diseño.
     * Genera una tarjeta Bento oscura e independiente.
     */
    private function mostrarMensajeLimite($titulo, $mensaje, $tipo = 'warning', $btnTexto = "Volver a mi perfil", $btnUrl = "index.php?action=perfil") {
        $colorBorde = ($tipo === 'error') ? '#f44336' : '#ffc107'; 
        $claseBtn = ($tipo === 'error') ? 'btn-sec' : 'btn-prim';

        echo "<!DOCTYPE html>\n<html lang='es'>\n<head>";
        echo "<meta charset='UTF-8'>\n<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>$titulo - SnapCity</title>";
        echo "<link rel='stylesheet' href='css/global.css'>";
        echo "<style>
            .feedback-body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-image: radial-gradient(circle at top, #1a1a1a 0%, #0a0a0a 100%); margin:0; }
            .feedback-card { background: #1a1a1a; padding: 40px; border-radius: 20px; border: 1px solid #2a2a2a; border-top: 5px solid $colorBorde; text-align: center; max-width: 500px; width: 90%; box-shadow: 0 15px 35px rgba(0,0,0,0.6); }
            .feedback-title { color: $colorBorde; font-size: 1.8em; margin-bottom: 15px; margin-top: 0; font-weight: 700; }
            .feedback-text { color: #bbb; font-size: 1.1em; line-height: 1.6; margin-bottom: 30px; }
            .btn-fback { display: inline-block; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: bold; transition: 0.3s; }
            .btn-fback.btn-sec { background: #333; color: #fff; border: 1px solid #444; }
            .btn-fback.btn-sec:hover { background: #444; }
            .btn-fback.btn-prim { background: #ffc107; color: #000; }
            .btn-fback.btn-prim:hover { filter: brightness(1.1); }
        </style>";
        echo "</head>\n<body class='feedback-body'>";
        echo "<div class='feedback-card'>";
        echo "<h2 class='feedback-title'>$titulo</h2>";
        echo "<p class='feedback-text'>$mensaje</p>";
        echo "<a href='$btnUrl' class='btn-fback $claseBtn'>$btnTexto</a>";
        echo "</div>\n</body>\n</html>";
    }

    public function mostrarSubida() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        $id_usuario = $_SESSION['usuario_id'];
        $db = new Database();
        $conexion = $db->getConnection();
        
        // Comprobar límite de 30 fotos personales
        $sqlGlobal = "SELECT COUNT(*) FROM fotos WHERE id_usuario = :id_usuario AND (etiqueta IS NULL OR etiqueta = '' OR etiqueta NOT IN (SELECT etiqueta FROM gincanas))";
        $stmtGlobal = $conexion->prepare($sqlGlobal);
        $stmtGlobal->execute([':id_usuario' => $id_usuario]);
        $total_fotos = $stmtGlobal->fetchColumn();

        if ($total_fotos >= 30) {
            $this->mostrarMensajeLimite("¡Límite de perfil!", "Has alcanzado el límite máximo de 30 fotos en tu galería personal. Elimina alguna para subir nuevas.");
            exit();
        }

        require_once __DIR__ . '/../../views/galeria/subir_foto.php';
    }

    public function procesarSubida() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["foto"])) {
            $id_usuario = $_SESSION['usuario_id'];
            $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
            $ubicacion = isset($_POST['ubicacion']) ? trim($_POST['ubicacion']) : '';
            $etiqueta = isset($_POST['etiqueta']) ? trim($_POST['etiqueta']) : '';
            $latitud = !empty($_POST['latitud']) ? $_POST['latitud'] : null;
            $longitud = !empty($_POST['longitud']) ? $_POST['longitud'] : null;

            $db = new Database();
            $conexion = $db->getConnection();

            // 1. COMPROBACIÓN DE LÍMITES
            $stmtGlobal = $conexion->prepare("SELECT COUNT(*) FROM fotos WHERE id_usuario = :id_usuario AND (etiqueta IS NULL OR etiqueta = '' OR etiqueta NOT IN (SELECT etiqueta FROM gincanas))");
            $stmtGlobal->execute([':id_usuario' => $id_usuario]);
            if ($stmtGlobal->fetchColumn() >= 30) {
                $this->mostrarMensajeLimite("Límite Alcanzado", "Límite de 30 fotos personales superado.", "warning");
                exit();
            }

            if (!empty($etiqueta)) {
                $stmtG = $conexion->prepare("SELECT * FROM gincanas WHERE etiqueta = :etiqueta");
                $stmtG->execute([':etiqueta' => $etiqueta]);
                $gincana = $stmtG->fetch(PDO::FETCH_ASSOC);

                if ($gincana) {
                    if (strtotime($gincana['fecha_fin']) < time()) {
                        $this->mostrarMensajeLimite("Reto finalizado", "El plazo ha caducado.", "error", "Volver", "index.php");
                        exit();
                    }
                    $stmtCountG = $conexion->prepare("SELECT COUNT(*) FROM fotos WHERE id_usuario = :id_usuario AND etiqueta = :etiqueta");
                    $stmtCountG->execute([':id_usuario' => $id_usuario, ':etiqueta' => $etiqueta]);
                    if ($stmtCountG->fetchColumn() >= 3) {
                        $this->mostrarMensajeLimite("Límite Gincana", "Ya has subido 3 fotos a este reto.", "warning");
                        exit();
                    }
                }
            }

            // 2. PROCESADO DE IMAGEN
            $foto = $_FILES['foto'];
            $ruta_temporal = $foto['tmp_name'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_real = finfo_file($finfo, $ruta_temporal);
            finfo_close($finfo);

            // EXIF
            $camara = "Desconocida"; $distancia_focal = "Desconocida"; $fecha_hora_toma = null;
            $exif = @exif_read_data($ruta_temporal);
            if ($exif !== false) {
                if (isset($exif['Model'])) $camara = $exif['Model'];
                if (isset($exif['FocalLength'])) $distancia_focal = $exif['FocalLength'] . " mm";
                if (isset($exif['DateTimeOriginal'])) $fecha_hora_toma = date('Y-m-d H:i:s', strtotime($exif['DateTimeOriginal']));
            }

            // Redimensión y Compresión
            $max_res = 1920;
            list($w_orig, $h_orig) = getimagesize($ruta_temporal);
            $ratio = $w_orig / $h_orig;
            $w_new = ($w_orig > $h_orig) ? $max_res : round($max_res * $ratio);
            $h_new = ($w_orig > $h_orig) ? round($max_res / $ratio) : $max_res;

            $img_nueva = imagecreatetruecolor($w_new, $h_new);
            switch ($mime_real) {
                case 'image/jpeg': $img_orig = imagecreatefromjpeg($ruta_temporal); break;
                case 'image/png':  $img_orig = imagecreatefrompng($ruta_temporal); break;
                case 'image/webp': $img_orig = imagecreatefromwebp($ruta_temporal); break;
                default: die("Formato no válido");
            }
            imagecopyresampled($img_nueva, $img_orig, 0,0,0,0, $w_new, $h_new, $w_orig, $h_orig);

            $nombre_unico = time() . "_" . uniqid() . ".jpg";
            $ruta_destino = __DIR__ . '/../../public/uploads/' . $nombre_unico;
            
            if (imagejpeg($img_nueva, $ruta_destino, 80)) {
                $sql = "INSERT INTO fotos (id_usuario, ruta_archivo, titulo, camara, distancia_focal, ubicacion, fecha_hora_toma, etiqueta, latitud, longitud) 
                        VALUES (:uid, :ruta, :tit, :cam, :foc, :ubi, :fecha, :eti, :lat, :lng)";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([
                    ':uid' => $id_usuario, ':ruta' => 'uploads/'.$nombre_unico, ':tit' => $titulo, 
                    ':cam' => $camara, ':foc' => $distancia_focal, ':ubi' => $ubicacion, 
                    ':fecha' => $fecha_hora_toma, ':eti' => $etiqueta, ':lat' => $latitud, ':lng' => $longitud
                ]);

                // Redirección inteligente
                if (!empty($etiqueta) && isset($gincana)) {
                    header("Location: index.php?action=ver_gincana&etiqueta=" . urlencode($etiqueta));
                } else {
                    header("Location: index.php?action=perfil");
                }
                exit();
            }
        }
    }

    public function eliminarFoto() {
        if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
            header("Location: index.php?action=perfil");
            exit();
        }

        $id_foto = $_GET['id'];
        $id_usuario = $_SESSION['usuario_id'];
        $db = new Database();
        $conexion = $db->getConnection();

        $stmt = $conexion->prepare("SELECT * FROM fotos WHERE id = :id AND id_usuario = :uid");
        $stmt->execute([':id' => $id_foto, ':uid' => $id_usuario]);
        $foto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($foto) {
            // Protección: No borrar si es gincana activa
            if (!empty($foto['etiqueta'])) {
                $stmtG = $conexion->prepare("SELECT COUNT(*) FROM gincanas WHERE etiqueta = :eti");
                $stmtG->execute([':eti' => $foto['etiqueta']]);
                if ($stmtG->fetchColumn() > 0) {
                    $this->mostrarMensajeLimite("Denegado", "No puedes borrar fotos de gincanas oficiales.", "error");
                    exit();
                }
            }

            // Borrado físico y BD
            $ruta = __DIR__ . '/../../public/' . $foto['ruta_archivo'];
            if (file_exists($ruta)) unlink($ruta);
            
            $conexion->prepare("DELETE FROM votos WHERE id_foto = ?")->execute([$id_foto]);
            $conexion->prepare("DELETE FROM fotos WHERE id = ?")->execute([$id_foto]);
        }
        header("Location: index.php?action=perfil");
        exit();
    }
}