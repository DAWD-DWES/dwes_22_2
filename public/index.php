<?php

/**
 *  --- Lógica del script --- 
 * 
 * Establece conexión a la base de datos PDO
 * Si el usuario ya está validado
 *   Si se solicita el formulario de modificación de perfil
 *     Invoco la vista del formulario de modificación de perfil
 *    Sino si se solicita procesar la modificación de perfil
 *     Leo los datos
 *     Estalezco flags de error
 *     Si hay errores
 *        Invoco la vista de formulario de registro  con información sobre los errores
 *     Sino persisto la información de perfil de usuario en la base de datos
 *          Invoco la vista del juego
 *    Sino si se solicita cerrar la sesión
 *     Destruyo la sesión
 *     Invoco la vista del formulario de login
 *    Sino redirección a juego para jugar una partida
 *  Sino 
 *   Si se pide procesar los datos del formulario
 *       Lee los valores del formulario
 *       Si los credenciales son correctos
 *       Redirijo al cliente al script de juego con una nueva partida
 *        Sino Invoco la vista del formulario de login con el flag de error
 *   Sino (En cualquier otro caso)
 *      Invoco la vista del formulario de login
 */
require "../vendor/autoload.php";

use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;
use App\BD\BD;
use App\Modelo\Usuario;
use App\DAO\UsuarioDao;

session_start();

// Inicializa el acceso a las variables de entorno

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

// Inicializa el acceso a las variables de entorno

$views = __DIR__ . '/../vistas';
$cache = __DIR__ . '/../cache';
$blade = new BladeOne($views, $cache, BladeOne::MODE_DEBUG);

// Funciones de validación de datos del formulario de registro
// Validación del nombre con expresión regular
function esNombreValido(string $nombre): bool {
    return preg_match("/^\w{3,15}$/", $nombre);
}

// 
function esPasswordValido(string $clave): bool {
    $alMenos1Digito = false;
    for ($i = 0; $i < strlen($clave); $i++) {
        if (is_numeric($clave[$i])) {
            $alMenos1Digito = true;
            break;
        }
    }
    return $alMenos1Digito && strpos($clave, " ") === false && (strlen($clave) >= 6);
}

function esEmailValido(string $email): bool {
    return empty($email) || (filter_var($email, FILTER_VALIDATE_EMAIL) && substr($email, -3) === ".es");
}

// Establece conexión a la base de datos PDO
try {
    $host = $_ENV['DB_HOST'];
    $port = $_ENV['DB_PORT'];
    $database = $_ENV['DB_DATABASE'];
    $usuario = $_ENV['DB_USUARIO'];
    $password = $_ENV['DB_PASSWORD'];
    $bd = BD::getConexion($host, $port, $database, $usuario, $password);
} catch (PDOException $error) {
    echo $blade->run("cnxbderror", compact('error'));
    die;
}

$usuarioDao = new UsuarioDao($bd);
// Si el usuario ya está validado
if (isset($_SESSION['usuario'])) {
    // Si se solicita cerrar la sesión
    if (isset($_REQUEST['botonlogout'])) {
        // Destruyo la sesión
        session_unset();
        session_destroy();
        setcookie(session_name(), '', 0, '/');
        // Invoco la vista del formulario de login
        echo $blade->run("formlogin");
        die;
    } elseif (isset($_REQUEST['botonperfil'])) {
        $usuario = $_SESSION['usuario'];
        $nombre = $usuario->getNombre();
        $clave = $usuario->getClave();
        $email = $usuario->getEmail();
        echo $blade->run("formperfil", compact('nombre', 'clave', 'email'));
        die;
    } elseif (isset($_REQUEST['botonprocperfil'])) {
        $usuario = $_SESSION['usuario'];
        $nombre = trim(filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING));
        $clave = trim(filter_input(INPUT_POST, 'clave', FILTER_SANITIZE_STRING));
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING));
        $errorNombre = empty($nombre) || !esNombreValido($nombre);
        $errorPassword = empty($clave) || !esPasswordValido($clave);
        $errorEmail = empty($email) || !esEmailValido($email);
        if ($errorNombre || $errorPassword || $errorEmail) {
            echo $blade->run("formperfil", compact('nombre', 'clave', 'email', 'errorNombre', 'errorPassword', 'errorEmail'));
            die;
        } else {
            $usuario->setNombre($nombre);
            $usuario->setClave($clave);
            $usuario->setEmail($email);
            try {
                $usuarioDao->modifica($usuario);
            } catch (PDOException $e) {
                echo $blade->run("formperfil", ['errorBD' => true]);
                die();
            }
            $partida = $_SESSION['partida'];
            echo $blade->run("juego", compact('usuario', 'partida'));
            die();
        }
    } else {
        // Redirijo al cliente al script de gestión del juego
        header("Location:juego.php?botonnuevapartida");
        die;
    }

    // Sino 
} else {
    if (isset($_REQUEST['botonproclogin'])) {
        // Lee los valores del formulario
        $nombre = trim(filter_input(INPUT_POST, 'nombre', FILTER_UNSAFE_RAW));
        $clave = trim(filter_input(INPUT_POST, 'clave', FILTER_UNSAFE_RAW));
        $usuario = $usuarioDao->recuperaPorCredencial($nombre, $clave);
        // Si los credenciales son correctos
        if ($usuario) {
            $_SESSION['usuario'] = $usuario;
            // Redirijo al cliente al script de juego con una nueva partida
            header("Location:juego.php?botonnuevapartida");
            die;
        }
        // Si los credenciales son incorrectos
        else {
            // Invoco la vista del formulario de login con el flag de error activado
            echo $blade->run("formlogin", ['error' => true]);
            die;
        }
        // En cualquier otro caso
    } else {
        // Invoco la vista del formulario de login
        echo $blade->run("formlogin");
        die;
    }
}