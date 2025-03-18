<?php
/**
 * Servicio Web para Registro e Inicio de Sesión
 *
 * Este script PHP proporciona dos endpoints:
 * - /registro: Para registrar nuevos usuarios.
 * - /login:   Para autenticar usuarios existentes.
 */

// Habilitar el manejo de errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar la sesión (útil para mantener información del usuario después del login)
session_start();

// **Simulación de Base de Datos**
// En una aplicación real, usarías una base de datos (MySQL, PostgreSQL, etc.)
// para almacenar los usuarios de forma persistente.
$usuarios =;

/**
 * Función para cargar usuarios desde un archivo (simulación de persistencia)
 *
 * En un sistema real, esto se haría con una base de datos.
 */
function cargarUsuarios() {
    global $usuarios;
    $archivo = 'usuarios.json';
    if (file_exists($archivo)) {
        $usuarios = json_decode(file_get_contents($archivo), true);
    }
}

/**
 * Función para guardar usuarios en un archivo (simulación de persistencia)
 *
 * En un sistema real, esto se haría con una base de datos.
 */
function guardarUsuarios() {
    global $usuarios;
    $archivo = 'usuarios.json';
    file_put_contents($archivo, json_encode($usuarios));
}

// Cargar usuarios al inicio del script
cargarUsuarios();

// Obtener el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

// Leer el cuerpo de la solicitud (datos JSON)
$input = json_decode(file_get_contents('php://input'), true);

/**
 * **Endpoint: /registro**
 *
 * Permite registrar un nuevo usuario.
 */
if ($method == 'POST' && strpos($_SERVER['REQUEST_URI'], '/registro') !== false) {
    // Verificar que los datos de entrada existen
    if (!isset($input['usuario']) || !isset($input['contrasena'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['mensaje' => 'Datos incompletos para el registro']);
        exit;
    }

    $usuario = $input['usuario'];
    $contrasena = $input['contrasena'];

    // Verificar si el usuario ya existe
    foreach ($usuarios as $u) {
        if ($u['usuario'] == $usuario) {
            http_response_code(409); // Conflict
            echo json_encode(['mensaje' => 'El usuario ya existe']);
            exit;
        }
    }

    // Hashear la contraseña de forma segura
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Crear el nuevo usuario
    $nuevo_usuario = [
        'usuario' => $usuario,
        'contrasena' => $contrasena_hash,
    ];

    // Agregar el usuario a la lista
    $usuarios= $nuevo_usuario;

    // Guardar los usuarios (simulación de base de datos)
    guardarUsuarios();

    // Devolver una respuesta de éxito
    http_response_code(201); // Created
    echo json_encode(['mensaje' => 'Registro exitoso']);
    exit;
}

/**
 * **Endpoint: /login**
 *
 * Permite autenticar a un usuario existente.
 */
if ($method == 'POST' && strpos($_SERVER['REQUEST_URI'], '/login') !== false) {
    // Verificar que los datos de entrada existen
    if (!isset($input['usuario']) || !isset($input['contrasena'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['mensaje' => 'Datos incompletos para el inicio de sesión']);
        exit;
    }

    $usuario = $input['usuario'];
    $contrasena = $input['contrasena'];

    // Verificar si el usuario existe y si la contraseña es correcta
    foreach ($usuarios as $u) {
        if ($u['usuario'] == $usuario && password_verify($contrasena, $u['contrasena'])) {
            // Autenticación exitosa
            // Puedes almacenar información del usuario en la sesión si es necesario
            $_SESSION['usuario'] = $usuario;

            echo json_encode(['mensaje' => 'Autenticación satisfactoria']);
            exit;
        }
    }

    // Devuelve un mensaje de error si la autenticación falla
    http_response_code(401); // Unauthorized
    echo json_encode(['mensaje' => 'Error en la autenticación']);
    exit;
}

// Si la ruta no coincide con ninguna de las anteriores, devolver un error 404
http_response_code(404); // Not Found
echo json_encode(['mensaje' => 'Ruta no encontrada']);
?>