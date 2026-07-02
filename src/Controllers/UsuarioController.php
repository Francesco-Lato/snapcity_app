<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;

class UsuarioController {

    private function mostrarFeedback($titulo, $mensaje, $tipo = 'error', $btnTexto = "Volver", $btnUrl = "index.php") {
        // Colores según el tipo de feedback
        if ($tipo === 'error') {
            $colorBorde = '#f44336'; // Rojo
            $claseBtn = 'btn-sec';
        } elseif ($tipo === 'success') {
            $colorBorde = '#4CAF50'; // Verde
            $claseBtn = 'btn-prim';
        } else {
            $colorBorde = '#ffc107'; // Amarillo
            $claseBtn = 'btn-prim';
        }

        echo "<!DOCTYPE html>\n<html lang='es'>\n<head>";
        echo "<meta charset='UTF-8'>\n<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>$titulo - SnapCity</title>";
        echo "<link rel='stylesheet' href='css/global.css'>";
        echo "<style>
            .feedback-body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-image: radial-gradient(circle at top, #1a1a1a 0%, #0a0a0a 100%); margin:0; }
            .feedback-card { background: #1a1a1a; padding: 40px; border-radius: 20px; border: 1px solid #2a2a2a; border-top: 5px solid $colorBorde; text-align: center; max-width: 500px; width: 90%; box-shadow: 0 15px 35px rgba(0,0,0,0.6); }
            .feedback-title { color: $colorBorde; font-size: 1.8em; margin-bottom: 15px; margin-top: 0; font-weight: 700; }
            .feedback-text { color: #bbb; font-size: 1.1em; line-height: 1.6; margin-bottom: 30px; }
            .feedback-text ul { list-style: none; padding: 0; margin-top: 15px; text-align: left; background: #0a0a0a; padding: 15px; border-radius: 10px; }
            .feedback-text li { margin-bottom: 8px; color: #ff5252; font-size: 0.95em; }
            .btn-fback { display: inline-block; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: bold; transition: 0.3s; }
            .btn-fback.btn-sec { background: #333; color: #fff; border: 1px solid #444; }
            .btn-fback.btn-sec:hover { background: #444; }
            .btn-fback.btn-prim { background: #ffc107; color: #000; }
            .btn-fback.btn-prim:hover { filter: brightness(1.1); }
        </style>";
        echo "</head>\n<body class='feedback-body'>";
        echo "<div class='feedback-card'>";
        echo "<h2 class='feedback-title'>$titulo</h2>";
        echo "<div class='feedback-text'>$mensaje</div>";
        echo "<a href='$btnUrl' class='btn-fback $claseBtn'>$btnTexto</a>";
        echo "</div>\n</body>\n</html>";
        exit(); // Detenemos la ejecución aquí
    }

    public function actualizarAvatar() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['avatar'])) {
            $id_usuario = $_SESSION['usuario_id'];
            $foto = $_FILES['avatar'];

            // 1. Validaciones básicas de seguridad
            if ($foto['error'] !== UPLOAD_ERR_OK) {
                $this->mostrarFeedback("Error de subida", "Ha ocurrido un error al intentar subir el archivo al servidor.", "error", "Volver al perfil", "index.php?action=perfil");
            }

            if ($foto['size'] > 5 * 1024 * 1024) { // Límite de 5MB para avatares
                $this->mostrarFeedback("Archivo muy pesado", "La foto del avatar supera el límite máximo de 5MB.", "error", "Volver al perfil", "index.php?action=perfil");
            }

            // val tipo MIME
            $ruta_temporal = $foto['tmp_name'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_real = finfo_file($finfo, $ruta_temporal);
            finfo_close($finfo);

            $mimes_permitidos = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($mime_real, $mimes_permitidos)) {
                $this->mostrarFeedback("Formato no válido", "Solo se permiten imágenes en formato JPG, PNG o WebP.", "error", "Volver al perfil", "index.php?action=perfil");
            }

            //Compresión y redimensión 400x400
            $max_resolucion = 400; 
            list($ancho_orig, $alto_orig) = getimagesize($ruta_temporal);
            $ratio = $ancho_orig / $alto_orig;

            if ($ancho_orig > $max_resolucion || $alto_orig > $max_resolucion) {
                if ($ancho_orig > $alto_orig) {
                    $ancho_nuevo = $max_resolucion;
                    $alto_nuevo = round($max_resolucion / $ratio);
                } else {
                    $alto_nuevo = $max_resolucion;
                    $ancho_nuevo = round($max_resolucion * $ratio);
                }
            } else {
                $ancho_nuevo = $ancho_orig;
                $alto_nuevo = $alto_orig;
            }

            $imagen_nueva = imagecreatetruecolor($ancho_nuevo, $alto_nuevo);
            
            // Mantener transparencias, no es fundamental pero está
            if ($mime_real === 'image/png' || $mime_real === 'image/webp') {
                imagealphablending($imagen_nueva, false);
                imagesavealpha($imagen_nueva, true);
                $color_transparente = imagecolorallocatealpha($imagen_nueva, 255, 255, 255, 127);
                imagefill($imagen_nueva, 0, 0, $color_transparente);
            }

            switch ($mime_real) {
                case 'image/jpeg': $imagen_original = imagecreatefromjpeg($ruta_temporal); break;
                case 'image/png':  $imagen_original = imagecreatefrompng($ruta_temporal); break;
                case 'image/webp': $imagen_original = imagecreatefromwebp($ruta_temporal); break;
            }

            imagecopyresampled($imagen_nueva, $imagen_original, 0, 0, 0, 0, $ancho_nuevo, $alto_nuevo, $ancho_orig, $alto_orig);

            // Generar nombre único y guardar
            $nombre_unico = 'avatar_' . $id_usuario . '_' . time() . '.jpg';
            $ruta_destino = __DIR__ . '/../../public/uploads/' . $nombre_unico;
            $ruta_db = 'uploads/' . $nombre_unico;

            $guardado_exito = imagejpeg($imagen_nueva, $ruta_destino, 85);

            // 4. Actualizar la base de datos
            if ($guardado_exito) {
                $db = new \App\Config\Database();
                $conexion = $db->getConnection();

                if ($conexion) {
                    // Primero, podemos buscar si ya tenía un avatar y borrar el archivo viejo para ahorrar espacio
                    $stmtViejo = $conexion->prepare("SELECT avatar FROM usuarios WHERE id = :id");
                    $stmtViejo->execute([':id' => $id_usuario]);
                    $viejo = $stmtViejo->fetch(\PDO::FETCH_ASSOC);
                    
                    if (!empty($viejo['avatar']) && file_exists(__DIR__ . '/../../public/' . $viejo['avatar'])) {
                        unlink(__DIR__ . '/../../public/' . $viejo['avatar']);
                    }

                    // Ahora guardamos el nuevo
                    $sql = "UPDATE usuarios SET avatar = :avatar WHERE id = :id";
                    $stmt = $conexion->prepare($sql);
                    $stmt->execute([':avatar' => $ruta_db, ':id' => $id_usuario]);
                }
                
                header("Location: index.php?action=perfil");
                exit();
            }
        }
    }

    public function procesarRegistro() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            
            // recogemos los datos y saneamos
            $nombre = strip_tags(trim($_POST['nombre']));
            $nickname = strip_tags(trim($_POST['nickname']));
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $zona = strip_tags(trim($_POST['zona']));
            $bio = strip_tags(trim($_POST['bio']));
            $fecha_nac = trim($_POST['fecha_nac']);
            $password_raw = $_POST['password']; 
            $estilos = isset($_POST['estilos']) ? implode(", ", $_POST['estilos']) : "";

            // validamos campos form
            $errores = [];

            if (empty($nombre) || empty($nickname) || empty($email) || empty($password_raw)) {
                $errores[] = "Por favor, rellena todos los campos obligatorios.";
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = "El formato del correo electrónico no es válido.";
            }

            // Validación de contraseña fuerte
            if (!preg_match('/^(?=.*[A-Z])(?=.*[\W_]).{8,}$/', $password_raw)) {
                $errores[] = "La contraseña debe tener al menos 8 caracteres, una mayúscula y un carácter especial.";
            }

            // Si hay errores, detenemos el proceso y los mostramos
            if (!empty($errores)) {
                $listaErrores = "<ul>";
                foreach ($errores as $error) {
                    $listaErrores .= "<li>" . htmlspecialchars($error) . "</li>";
                }
                $listaErrores .= "</ul>";

                $this->mostrarFeedback("Errores de Validación", "Por favor, corrige lo siguiente: " . $listaErrores, "error", "Volver al formulario", "index.php?action=registro");
            }

            // pass
            $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

            // Conexión base de datos
            $db = new Database();
            $conexion = $db->getConnection();

            if ($conexion) {
                try {
                    $sql = "INSERT INTO usuarios (nombre, nickname, email, password, fecha_nac, zona, bio, estilos) 
                            VALUES (:nombre, :nickname, :email, :password, :fecha_nac, :zona, :bio, :estilos)";
                    
                    $stmt = $conexion->prepare($sql);
                    $stmt->execute([
                        ':nombre' => $nombre,
                        ':nickname' => $nickname,
                        ':email' => $email,
                        ':password' => $password_hash,
                        ':fecha_nac' => $fecha_nac,
                        ':zona' => $zona,
                        ':bio' => $bio,
                        ':estilos' => $estilos
                    ]);

                    //mensaje todo ha ido ok
                    $this->mostrarFeedback("¡Registro Completado! 🎉", "El fotógrafo <strong>" . htmlspecialchars($nickname) . "</strong> ya forma parte de SnapCity.", "success", "Ir al Login", "index.php?action=login");

                } catch (\PDOException $e) {
                    // Si el email o nickname son únicos y fallan, capturarlo aquí sería ideal.
                    $this->mostrarFeedback("Error de Base de Datos", "Hubo un problema al intentar registrar el usuario. El correo o apodo podrían estar en uso.", "error", "Volver", "index.php?action=registro");
                }
            }
        }
    }

    public function miPerfil() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        if ($_SESSION['usuario_rol'] === 'admin') {
            header("Location: index.php");
            exit();
        }

        $db = new Database();
        $conexion = $db->getConnection();

        if ($conexion) {
            // Datos del usuario
            $sql = "SELECT * FROM usuarios WHERE id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':id' => $_SESSION['usuario_id']]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Fotos usuario 
            // Fotos usuario (Filtramos las que son de gincanas oficiales)
            $sqlFotos = "SELECT * FROM fotos WHERE id_usuario = :id_usuario AND (etiqueta IS NULL OR etiqueta = '' OR etiqueta NOT IN (SELECT etiqueta FROM gincanas)) ORDER BY subido_en DESC";
            $stmtFotos = $conexion->prepare($sqlFotos);
            $stmtFotos->execute([':id_usuario' => $_SESSION['usuario_id']]);
            $fotos = $stmtFotos->fetchAll(PDO::FETCH_ASSOC);

            require_once __DIR__ . '/../../views/usuario/perfil.php';
        }
    }

    // Método para ver el perfil público de otro usuario
    // Método para ver el perfil público de otro usuario
    public function perfilPublico() {
        // Si no nos pasan un ID por la URL, lo mandamos al inicio
        if (!isset($_GET['id'])) {
            header("Location: index.php");
            exit();
        }

        $id_usuario_publico = $_GET['id'];
        
        // Si el usuario hace clic en su propio nombre, lo mandamos a su perfil
        if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id_usuario_publico) {
            header("Location: index.php?action=perfil");
            exit();
        }

        $db = new Database();
        $conexion = $db->getConnection();

        if ($conexion) {
            // --- ⚠️ AQUÍ ESTABA EL ERROR: Faltaba esta línea definiendo $sqlUsuario ---
            $sqlUsuario = "SELECT * FROM usuarios WHERE id = :id";
            $stmtUsuario = $conexion->prepare($sqlUsuario);
            $stmtUsuario->execute([':id' => $id_usuario_publico]);
            $usuario_publico = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

            //Si el usuario no existe, mostramos error
            if (!$usuario_publico) {
                die("<h2 style='text-align:center; margin-top:50px;'>Error 404: Usuario no encontrado.</h2>");
            }

            // Buscamos las fotos de este usuario (Con el filtro inteligente de gincanas aplicado)
            $sqlFotos = "SELECT * FROM fotos WHERE id_usuario = :id AND (etiqueta IS NULL OR etiqueta = '' OR etiqueta NOT IN (SELECT etiqueta FROM gincanas)) ORDER BY subido_en DESC";
            $stmtFotos = $conexion->prepare($sqlFotos);
            $stmtFotos->execute([':id' => $id_usuario_publico]);
            $fotos_publicas = $stmtFotos->fetchAll(PDO::FETCH_ASSOC);

            // IMPORTANTE: Cargamos la vista del perfil público
            require_once __DIR__ . '/../../views/usuario/perfil_publico.php';
        }
    }

    public function eliminarCuenta() {
        // 1. Verificamos que esté logueado
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_usuario = $_SESSION['usuario_id'];
            
            $db = new \App\Config\Database();
            $conexion = $db->getConnection();

            if ($conexion) {
                try {
                    // borrar avatar
                    $stmtAvatar = $conexion->prepare("SELECT avatar FROM usuarios WHERE id = :id");
                    $stmtAvatar->execute([':id' => $id_usuario]);
                    $usuario = $stmtAvatar->fetch(\PDO::FETCH_ASSOC);
                    
                    if (!empty($usuario['avatar'])) {
                        $ruta_avatar = __DIR__ . '/../../public/' . $usuario['avatar'];
                        if (file_exists($ruta_avatar)) {
                            unlink($ruta_avatar); // unlink() borra el archivo físico
                        }
                    }

                    // Borrar las fotos de la galería del servidor 
                    $stmtFotos = $conexion->prepare("SELECT ruta_archivo FROM fotos WHERE id_usuario = :id");
                    $stmtFotos->execute([':id' => $id_usuario]);
                    $fotos = $stmtFotos->fetchAll(\PDO::FETCH_ASSOC);
                    
                    foreach ($fotos as $foto) {
                        $ruta_foto = __DIR__ . '/../../public/' . $foto['ruta_archivo'];
                        if (file_exists($ruta_foto)) {
                            unlink($ruta_foto);
                        }
                    }

                    // Borrar los datos de las tablas en orden 
                    // Borramos tambiémn los votos que haya hecho este usuario
                    $stmtVotos = $conexion->prepare("DELETE FROM votos WHERE id_usuario = :id");
                    $stmtVotos->execute([':id' => $id_usuario]);

                    // Borramos los registros de sus fotos en la base de datos
                    $stmtDeleteFotos = $conexion->prepare("DELETE FROM fotos WHERE id_usuario = :id");
                    $stmtDeleteFotos->execute([':id' => $id_usuario]);

                    // borramos al usuario
                    $stmtDeleteUser = $conexion->prepare("DELETE FROM usuarios WHERE id = :id");
                    $stmtDeleteUser->execute([':id' => $id_usuario]);

                    // Cerrar la sesión y  a la pantalla de inicio ---
                    session_destroy();
                    
                    // mensaje de éxito
                    $this->mostrarFeedback("Cuenta Eliminada", "Lamentamos verte partir. Todos tus datos y fotos han sido borrados de nuestros servidores.", "success", "Volver al Inicio", "index.php");

                } catch (\PDOException $e) {
                    $this->mostrarFeedback("Error Interno", "No se ha podido eliminar la cuenta debido a un error del servidor.", "error", "Volver al Perfil", "index.php?action=perfil");
                }
            }
        } else {
            // Si intentan acceder a index.php?action=eliminar_cuenta escribiéndolo en la URL (GET), los devolvemos al perfil
            header("Location: index.php?action=perfil");
            exit();
        }
    }
}
