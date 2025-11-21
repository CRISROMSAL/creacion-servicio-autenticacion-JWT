Pagina de login.php:

Es un endpoint de autenticación que:
Recibe credenciales (usuario y contraseña) desde el frontend
Verifica si son correctas
Genera un token si son válidas
Devuelve ese token al cliente para que lo use en futuras peticiones


Pagina de welcome.php:
Es un endpoint protegido de la API que:
Recibe el token (JWT) enviado por el cliente en la cabecera de la petición.
Valida la autenticidad del token (comprueba la firma y que no haya expirado) para asegurar que no ha sido falsificado.
Extrae la identidad del usuario directamente desde el token (el payload).
Devuelve los datos personalizados (mensaje de bienvenida, hora, usuario) si el acceso es autorizado.



En resumen:
login.php crea el pase (el token).
welcome.php verifica el pase y te deja entrar.



Página de login.html
Es la interfaz gráfica (Frontend) de entrada que:
Proporciona el formulario visual para que el usuario introduzca sus datos.
Contiene los campos de entrada (inputs) y el contenedor para mostrar errores.
Carga y ejecuta el archivo js/login.js para dar funcionalidad al formulario.


Archivo js/login.js
Es el controlador lógico del inicio de sesión en el cliente que:
Intercepta el envío del formulario para evitar la recarga de la página.
Captura los datos del usuario y los envía a login.php mediante una petición POST.
Gestiona la respuesta: si es exitosa, guarda el token en el almacenamiento local (localStorage); si falla, muestra el error.
Redirige automáticamente al usuario a la página de bienvenida si la autenticación es correcta.



Página de bienvenida.html
Es la interfaz visual protegida que:
Define la estructura donde se mostrará la información del usuario.
Contiene elementos vacíos (con ids) esperando ser rellenados con datos dinámicos.
Incluye el botón para cerrar sesión.
Carga el script js/welcome.js para gestionar la seguridad y los datos.


Archivo js/welcome.js
Es el gestor de sesión y datos del cliente que:
Verifica inmediatamente si existe un token guardado; si no, expulsa al usuario.
Solicita los datos protegidos a welcome.php enviando el token en la cabecera Authorization.
Maneja la seguridad: Si recibe un error 403 (token falso/expirado), cierra la sesión y redirige.
Renderiza la vista: Si todo está bien, inyecta los datos (nombre, hora, mensaje) en el HTML.
Gestiona la funcionalidad de "Cerrar Sesión" (borrar token y redirigir).


Página de permisos.html
Es una página informativa de error que:
Informa al usuario de que ha intentado acceder a una zona restringida sin autorización.
Actúa como "sala de espera" o destino de redirección cuando falla la seguridad.
Proporciona un enlace para volver a la pantalla de login e intentarlo de nuevo.
