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

define('SECRET_KEY', 'mi_clave_secreta_re_cordis_2025');

$headers = getallheaders(); // Obtiene todas las cabeceras HTTP de la petición y las devuelve en formato array asociativo
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? ''; //Busca la cabecera authorization en el array con 3 intentos por si viene en diferentes formatos; Authorization, autorizathion, si no existen ninguno de estos dos cadena vacía. COMO PHP ES SENSIBLE A MAYUSCULAS Y MINUSCULAS POR ESO BUSCA AMBAS

if (empty($authHeader) || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    http_response_code(403);
    echo json_encode(['error' => 'Token no proporcionado']);
    exit();
}

$token = $matches[1];


try {
    // Separar las 3 partes del JWT
    $tokenParts = explode('.', $token);
    
    if (count($tokenParts) !== 3) {
        throw new Exception('Token inválido');
    }
    
    list($headerEncoded, $payloadEncoded, $signatureEncoded) = $tokenParts;
    
    // Verificar la firma
    $signature = base64_decode(strtr($signatureEncoded, '-_', '+/'));
    $expectedSignature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, SECRET_KEY, true);
    
    if (!hash_equals($signature, $expectedSignature)) {
        throw new Exception('Firma inválida');
    }
    
    // Decodificar el payload
    $payload = json_decode(base64_decode(strtr($payloadEncoded, '-_', '+/')), true);
    
    // Verificar expiración
    if (!isset($payload['exp']) || $payload['exp'] < time()) {
        http_response_code(403);
        echo json_encode(['error' => 'Token expirado']);
        exit();
    }
    
    $nombreUsuario = $payload['nombre-usuario'];
    
    $mensajes = [
        "admin" => "Bienvenido administrador, aquí tienes acceso al sistema.",
        "usuario" => "Bienvenido usuario, aquí puedes encontrar la información que necesites.",
        "DAW2" => "Bienvenido usuario de segundo de desarrollo de aplicaciones web.",
        "CristinaRoman" => "Bienvenida Cristina Román, aquí puedes encontrar la información que necesites.",
        "CarlosBasulto" => "Bienvenido Carlos Basulto, aquí puedes encontrar la información que necesites."
    ];
    
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'usuario' => $username,
        'fecha' => date('d/m/Y'),
        'hora' => date('H:i:s'),
        'mensaje' => $mensaje_bienvenida
    ]);
    
} catch (Exception $e) {
    http_response_code(403);
    echo json_encode(['error' => 'Token inválido']);
}
?>    