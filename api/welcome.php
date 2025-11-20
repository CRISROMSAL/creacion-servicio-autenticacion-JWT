<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS'); //Acepta metodos get y post
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') { //Verifica que la peticion sea get
    http_response_code(405); //si no fuese get nos da error 405
    echo json_encode(['error' => 'Método no permitido']); //devuelve un json con error de metodo no permitido
    exit(); //Termina la ejecucion
}

$headers = getallheaders(); // Obtiene todas las cabeceras HTTP de la petición y las devuelve en formato array asociativo
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? ''; //Busca la cabecera authorization en el array con 3 intentos por si viene en diferentes formatos; Authorization, autorizathion, si no existen ninguno de estos dos cadena vacía. COMO PHP ES SENSIBLE A MAYUSCULAS Y MINUSCULAS POR ESO BUSCA AMBAS

if (empty($authHeader) || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    http_response_code(403);
    echo json_encode(['error' => 'Token no proporcionado']);
    exit();
}

$token = $matches[1];

//Intenta decodificar y validar el token, primero decodifica de base64 a json y luego convierte el json a array php
try {
    $payload = json_decode(base64_decode($token), true);
    
    if (!$payload || !isset($payload['exp']) || $payload['exp'] < time()) { //Si el payload es null, no tiene la clave de la expiración, o ha expirado
        http_response_code(403); //nos da error
        echo json_encode(['error' => 'Token expirado o inválido']); //nos devuelve un error con token expirado o invalido
        exit();
    }
    
    //obtiene el nombre de usuario del payload
    $nombreUsuario = $payload['nombre-usuario'];
    
    //obtiene el mensaje personalizado del usuario
    $mensajes = [
        "admin" => "Bienvenido administrador, aquí tienes acceso al sistema.",
        "usuario" => "Bienvenido usuario, aquí puedes encontrar la información que necesites.",
        "DAW2" => "Bienvenido usuario de segundo de desarrollo de aplicaciones web, aquí puedes encontrar la información que necesites.",
        "CristinaRoman" => "Bienvenida Cristina Román, aquí puedes encontrar la información que necesites.",
        "CarlosBasulto" => "Bienvenido Carlos Basulto, aquí puedes encontrar la información que necesites."
    ];
    
    http_response_code(200); //reponde con exito
    //devuelve un json con los datos del usuario
    echo json_encode([
        'success' => true,
        'usuario' => $username,
        'fecha' => date('d/m/Y'),
        'hora' => date('H:i:s'),
        'mensaje' => $mensaje_bienvenida
    ]);
    
//si hay cualquier error al decodificar el token    
} catch (Exception $e) {
    http_response_code(403);
    echo json_encode(['error' => 'Token inválido']);
}
?>