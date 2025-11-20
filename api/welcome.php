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

define('SECRET_KEY', 'mi_clave_secreta_re_cordis_2025'); // Definimos la misma clave secreta usada en el login para validar la firma.



//Extracción del token
$headers = getallheaders(); // Obtiene todas las cabeceras HTTP de la petición y las devuelve en formato array asociativo
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? ''; //Busca la cabecera authorization en el array con 3 intentos por si viene en diferentes formatos; Authorization, autorizathion, si no existen ninguno de estos dos cadena vacía. COMO PHP ES SENSIBLE A MAYUSCULAS Y MINUSCULAS POR ESO BUSCA AMBAS

$partes = explode(' ', $authHeader); // Separa "Bearer TOKEN" en un array

// Verifica que haya al menos 2 partes y que la primera parte sea la palabra estándar "Bearer".
if (count($partes) < 2 || $partes[0] !== 'Bearer') {
    http_response_code(403); //si no lo cumple error
    echo json_encode(['error' => 'Token no proporcionado']);
    exit();
}

$token = $partes[1]; //guardamos la segunda parte que es el token real


//validación y decoddificacion de JWT
try {
    // Separar las 3 partes del JWT por puntos, Header.Payload.Signature
    $tokenParts = explode('.', $token); //Rompe el string por los puntos
    
    if (count($tokenParts) !== 3) { //si no tiene 3 partes, no es un jwt valido
        throw new Exception('Token inválido'); //se lanza error para ir al bloque catch
    }
    
    // Asignamos cada parte a una variable: Header, Payload y Firma (codificadas).
    list($headerEncoded, $payloadEncoded, $signatureEncoded) = $tokenParts;
    
    // Verificar la firma (firma y firma esperada)
    $signature = base64_decode(strtr($signatureEncoded, '-_', '+/')); // Decodificamos la firma que venía en el token., strtr cambia los caracteres URL - y _ por los estándar Base64 + y /
    $expectedSignature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, SECRET_KEY, true); //Calculamos la firma que DEBERÍA tener el token usando nuestra SECRET_KEY y los datos recibidos.
    
    if (!hash_equals($signature, $expectedSignature)) { // Comparamos la firma que llegó con la que calculamos nosotros (la firma con la firma esperada).
        throw new Exception('Firma inválida'); // Si no coinciden, el token fue modificado o es falso.
    }
    
    // Decodificar el payload
    $payload = json_decode(base64_decode(strtr($payloadEncoded, '-_', '+/')), true);
    
    // Verificar expiración
    if (!isset($payload['exp']) || $payload['exp'] < time()) {
        http_response_code(403);
        echo json_encode(['error' => 'Token expirado']);
        exit();
    }
    
    $nombreUsuario = $payload['nombre-usuario']; //Extraemos el nombre del usuario guardado dentro del token.
    
    $mensajes = [
        "admin" => "Bienvenido administrador, aquí tienes acceso al sistema.",
        "usuario" => "Bienvenido usuario, aquí puedes encontrar la información que necesites.",
        "DAW2" => "Bienvenido usuario de segundo de desarrollo de aplicaciones web.",
        "CristinaRoman" => "Bienvenida Cristina Román, aquí puedes encontrar la información que necesites.",
        "CarlosBasulto" => "Bienvenido Carlos Basulto, aquí puedes encontrar la información que necesites."
    ];
    
    $mensaje_bienvenida = $mensajes[$nombreUsuario]; // Seleccionamos el mensaje específico para este usuario.

    http_response_code(200);
    echo json_encode([ // Convierte el array de respuesta a JSON para enviarlo al navegador.
        'success' => true,
        'usuario' => $nombreUsuario,
        'fecha' => date('d/m/Y'),
        'hora' => date('H:i:s'),
        'mensaje' => $mensaje_bienvenida
    ]);
    
} catch (Exception $e) { // Si algo falló dentro del 'try' (firma mal, formato mal, etc.)
    http_response_code(403); //error
    echo json_encode(['error' => 'Token inválido']); // Mensaje genérico de error de seguridad.
}
?>    