<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;
use PDOException;

class AuthController {
    
    /**
     * Plantilla maestra para mostrar mensajes de feedback (errores, éxitos)
     * Mantiene el diseño oscuro, la fuente Lexend Deca y la estructura Bento de SnapCity.
     */
    private function mostrarFeedback($titulo, $mensaje, $tipo = 'error', $btnTexto = "Volver", $btnUrl = "index.php") {
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
        exit();
    }

    public function mostrarRegistro() {
        require_once __DIR__ . '/../../views/auth/registro.php';
    }

    public function mostrarLogin() {
        require_once __DIR__ . '/../../views/auth/login.php';
    }

    public function procesarRegistro() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            
            // Datos formulario de registro ATENCIÓN (quiero sanearlo en un segundo momento)
            $nombre = trim($_POST['nombre']);
            $nickname = trim($_POST['nickname']);
            $email = trim($_POST['email']);
            $zona = trim($_POST['zona']);
            $bio = trim($_POST['bio']);
            $fecha_nac = $_POST['fecha_nac'];
            
            // Recogemos la contraseña tal cual la escribe el usuario para poder validarla
            $password_raw = $_POST['password']; 
            
            // --- VALIDACIÓN DE CONTRASEÑA FUERTE ---
            if (!preg_match('/^(?=.*[A-Z])(?=.*[\W_]).{8,}$/', $password_raw)) {
                $this->mostrarFeedback(
                    "Error de seguridad 🛡️", 
                    "La contraseña debe tener al menos 8 caracteres, incluir una letra mayúscula y un carácter especial.", 
                    "error", 
                    "Volver al formulario", 
                    "index.php?action=registro"
                );
            }
            // ---------------------------------------

            // Si pasa la validación, procedemos a encriptarla
            $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);
            
            $estilos = isset($_POST['estilos']) ? implode(", ", $_POST['estilos']) : "";

            // Conexión base de datos
            $db = new Database();
            $conexion = $db->getConnection();

            if ($conexion) {
                try {
                    // Consulta SQL
                    $sql = "INSERT INTO usuarios (nombre, nickname, email, password, fecha_nac, zona, bio, estilos) 
                            VALUES (:nombre, :nickname, :email, :password, :fecha_nac, :zona, :bio, :estilos)";
                    
                    $stmt = $conexion->prepare($sql);
                    
                    // 4. Ejecutamos la consulta
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

                    $this->mostrarFeedback(
                        "¡Registro completado!", 
                        "El fotógrafo <strong>" . htmlspecialchars($nickname) . "</strong> ya forma parte de la comunidad.", 
                        "success", 
                        "Ir a iniciar sesión", 
                        "index.php?action=login"
                    );

                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $this->mostrarFeedback(
                            "Datos en uso", 
                            "El Nickname o el Correo Electrónico ya están registrados. Por favor, elige otros.", 
                            "error", 
                            "Volver al formulario", 
                            "index.php?action=registro"
                        );
                    } else {
                        $this->mostrarFeedback(
                            "Error técnico", 
                            "Ocurrió un problema: " . htmlspecialchars($e->getMessage()), 
                            "error", 
                            "Volver al formulario", 
                            "index.php?action=registro"
                        );
                    }
                }
            } else {
                $this->mostrarFeedback(
                    "Error de conexión", 
                    "No se pudo conectar a la base de datos.", 
                    "error", 
                    "Reintentar", 
                    "index.php?action=registro"
                );
            }
        }
    }

    public function procesarLogin(){
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $identificador = trim($_POST['identificador']);
            $password = $_POST['password'];

            $db = new Database();
            $conexion = $db->getConnection();

            if($conexion){
                $sql = "SELECT * FROM usuarios WHERE email = :id OR nickname = :id";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([':id' => $identificador]);

                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                if($usuario && password_verify($password, $usuario['password'])){
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nickname'] = $usuario['nickname'];
                    $_SESSION['usuario_rol'] = $usuario['rol'];

                    header("Location: index.php");
                    exit();
                } else {
                    $this->mostrarFeedback(
                        "Credenciales incorrectas", 
                        "El usuario no existe o la contraseña es errónea. Por favor, comprueba tus datos.", 
                        "error", 
                        "Volver a intentar", 
                        "index.php?action=login"
                    );
                }
            } else {
                $this->mostrarFeedback(
                    "Error de conexión", 
                    "No se pudo conectar a la base de datos para verificar tus credenciales.", 
                    "error", 
                    "Reintentar", 
                    "index.php?action=login"
                );
            }
        }
    }
}