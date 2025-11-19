php<?php
header('Content-Type: application/json'); //respuesta en formato json
header('Access-Control-Allow-Origin: *'); //permite que cualquier origen pueda hacer peticiones, sin esto el navegador bloquearía las peticiones desde index.html
header('Access-Control-Allow-Methods: POST, OPTIONS'); //permitir solo el método POST y OPTIONS
header('Access-Control-Allow-Headers: Content-Type'); //define las cabeceras que puede enviar el cliente


//Da permido al navegador para que envie la peticion post, sin esto no tendría permisos y me bloquearía la petición POST
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { //detecta peticion de permiso
    http_response_code(200); //tiene permiso
    exit(); //no sigue ejecutando codigo porque la peticion options solo pide permido, no procesa el login
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { //Verifica que la peticion sea post
    http_response_code(405); //si no fuese post nos da error 405
    echo json_encode(['error' => 'Método no permitido']); //devuelve un json con error de metodo no permitido
    exit(); //Termina la ejecucion
}

// Array de usuarios 
$usuarios = [
    "admin" => "1234",
    "usuario" => "abcd",
    "DAW2" => "DAW2",
    "CristinaRoman" => "CristinaRoman",
    "CarlosBasulto" => "CarlosBasulto"
];

$input = json_decode(file_get_contents('php://input'), true); //
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan credenciales']);
    exit();
}

// Validar credenciales
if (isset($usuarios[$username]) && $usuarios[$username] === $password) {
    // Crear payload del token
    $payload = [
        'username' => $username,
        'iat' => time(),
        'exp' => time() + 3600
    ];
    
    // Generar token con base64_encode
    $token = base64_encode(json_encode($payload));
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'token' => $token,
        'username' => $username
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Usuario o contraseña incorrectos'
    ]);
}
?>