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

$input = json_decode(file_get_contents('php://input'), true); //variable que contiene todos los datos json que el cliente envió en el cuerpo de la peticion http, convertidos en un array asociativo de PHP. El parámetro true es importante porque sin él devolvería un objeto en lugar de un array
$nombreUsuario = $input['nombre-usuario'] ?? ''; //extrae el nombre del usuario, si no existe o es nulo asigna cadena vacia por defecto
$contraseña = $input['contraseña'] ?? ''; //extrae la contraseña, si no existe o es nula asigna cadena vacía por defecto

if (empty($username) || empty($password)) { //si el nombreUsuario o la contraseña están vacias
    http_response_code(400); //se establece el codigo de estado HTTP a 404, indicanso que la petición está mal formada
    echo json_encode(['error' => 'Faltan credenciales']); //repuesta json a cliente diciendo que faltan credenciales
    exit(); //Termina la ejecución
}

// Validar credenciales con el array
if (isset($usuarios[$username]) && $usuarios[$username] === $password) { //verifica que exista la clave en el array y compara que la contraseña coincida exactamente
    // Crear payload (datos) que queremos guardar dentro del token
    //Sirve para cuando el ciente envíe este token en futuras peticiones, el servidor sabrá quien es el usuario sin necesidad de consultas la BD cada vez.
    $payload = [
        'nombre-usuario' => $nombreUsuario, //guarda el nombre de usuario que acaba de iniciar sesion
        'iat' => time(), //permite saber cuando se creó el token
        'exp' => time() + 3600 //expiración del token,por seguridad, si alguien lo roba, solo podra usarlo durante 1 hora, después de este tiempo el token no será valido
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