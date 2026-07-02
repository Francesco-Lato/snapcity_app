<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\UsuarioController;
use App\Controllers\FotoController;
use App\Controllers\GincanaController;

// Leer qué acción quiere hacer el usuario 
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

switch ($action) {
    case 'home':
        $controller = new \App\Controllers\HomeController();
        $controller->index();
        break;

    //ruta perfil
    case 'perfil':
        $controller = new UsuarioController();
        $controller->miPerfil();
        break;

    case 'cambiar_avatar':
        $controller = new \App\Controllers\UsuarioController();
        $controller->actualizarAvatar();
        break;

    //ruta registro
    case 'registro':
        $controller = new AuthController();
        $controller->mostrarRegistro();
        break;
    case 'procesar_registro':
        $controller = new AuthController();
        $controller->procesarRegistro();
        break;

    //ruta a login
    case 'login':
        $controller = new AuthController();
        $controller->mostrarLogin();
        break;
    case 'procesar_login':
        $controller = new AuthController();
        $controller->procesarLogin();
        break;
        
    //ruta logout
    case 'logout':
        session_destroy(); // Rompemos la pulsera VIP
        header("Location: index.php");
        exit();
        break;

    case 'subir_foto':
        $controller = new FotoController();
        $controller->mostrarSubida();
        break;
    case 'procesar_subida':
        $controller = new FotoController();
        $controller->procesarSubida();
        break;

    case 'crear_gincana':
        $controller = new GincanaController();
        $controller->mostrarCrear();
        break;
    case 'procesar_gincana':
        $controller = new GincanaController();
        $controller->procesarCrear();
        break;

    case 'gincanas':
        $controller = new GincanaController();
        $controller->listarActivas();
        break;

    case 'ver_gincana':
        $controller = new GincanaController();
        $controller->verGaleria();
        break;
    case 'votar_foto':
        $controller = new GincanaController();
        $controller->votarFoto();
        break;

    case 'perfil_publico':
        $controller = new \App\Controllers\UsuarioController();
        $controller->perfilPublico();
        break;

    case 'buscar':
        $controller = new \App\Controllers\BuscadorController();
        $controller->buscar();
        break;

    case 'eliminar_foto':
        $controller = new \App\Controllers\FotoController();
        $controller->eliminarFoto();
        break;

    case 'eliminar_cuenta':
        $controller = new \App\Controllers\UsuarioController();
        $controller->eliminarCuenta();
        break;

    default:
        echo "Error 404: Página no encontrada";
        break;
}