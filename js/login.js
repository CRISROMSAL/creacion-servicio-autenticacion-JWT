// Verificar si ya está logueado
if (localStorage.getItem('token')) { // Se comprueba si existe un token JWT almacenado previamente en localStorage
    window.location.href = 'bienvenida.html'; //redirige a la pagina de bienvenida sin mostrar el login
}

// Manejar envío del formulario
document.getElementById('loginForm').addEventListener('submit', async (e) => { // Se asocia una función asíncrona al evento 'submit' del formulario de login.
    e.preventDefault(); //evita que la pagina se recargue
    
    const nombreUsuario = document.getElementById('nombre-usuario').value; // Se obtiene el valore introducido por el usuario en el campo de nombre usuario
    const contraseña = document.getElementById('contraseña').value;  // Se obtiene el valore introducido por el usuario en el campo de contraseña
    const errorMessage = document.getElementById('errorMessage'); // Se obtiene el elemento donde se mostrarán los mensajes de error
    
    errorMessage.style.display = 'none'; // Oculta el mensaje de error inicialmente
    
 //comunicacion con la API    
    try {
        // Se realiza una petición HTTP asíncrona mediante la Fetch API.
        const response = await fetch('http://localhost/creacion-servicio-autenticacion-JWT/api/login.php', {
            method: 'POST', // Método HTTP utilizado para el envío seguro de credenciales.
            headers: {
                'Content-Type': 'application/json' // Definición de la cabecera Content-Type para indicar payload JSON.
            },
            // Cuerpo de la petición con las credenciales del usuario en formato JSON.
            body: JSON.stringify({ 
                'nombre-usuario': nombreUsuario, 
                'contraseña': contraseña 
            })
        });
               
        const data = await response.json(); // Se espera la respuesta del servidor y se parsea a JSON.
        
        if (response.ok && data.success) { // Si la respuesta es exitosa y el login es correcto
            // Guardar token en localStorage del navegador
            localStorage.setItem('token', data.token);
            localStorage.setItem('nombre-usuario', data['nombre-usuario']);            
            // Redirigir a bienvenida
            window.location.href = 'bienvenida.html';
        } else {
            // Mostrar error
            errorMessage.textContent = data.error || 'Error al iniciar sesión';
            errorMessage.style.display = 'block';
        }
    } catch (error) { // Manejo de errores de red o de la petición
        errorMessage.textContent = 'Error de conexión con el servidor';
        errorMessage.style.display = 'block';
    }
});