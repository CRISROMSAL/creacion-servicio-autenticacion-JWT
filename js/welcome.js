// Verificar token
const token = localStorage.getItem('token'); // Obtener el token JWT almacenado en localStorage
if (!token) { // Si no hay token, redirigir a permisos
    window.location.href = 'permisos.html';
} else {
    cargarDatosUsuario(); // Si hay token, cargar los datos del usuario
}

// Cargar datos del usuario desde la API
async function cargarDatosUsuario() {
    try {
        //peticion http get asincrona al endpoint de la API para obtener datos de bienvenida
        const response = await fetch('http://localhost/creacion-servicio-autenticacion-JWT/api/welcome.php', {
            method: 'GET',
            headers: { // Incluir el token JWT en la cabecera Authorization
                'Authorization': `Bearer ${token}`
            }
        });
        
        if (response.status === 403) { //si la respuesta es 403
            // Token inválido o expirado
            // Eliminar token y redirigir a permisos
            localStorage.removeItem('token');
            localStorage.removeItem('nombre-usuario');
            window.location.href = 'permisos.html';
            return;
        }
        
        const data = await response.json(); //decodificacion del payload JSON de la respuesta
        
        if (data.success) { // Si la respuesta es exitosa, mostrar los datos en la página
            document.getElementById('usuario').textContent = data.usuario;
            document.getElementById('fecha').textContent = data.fecha;
            document.getElementById('hora').textContent = data.hora;
            document.getElementById('mensaje').textContent = data.mensaje;
        }
    } catch (error) { // Manejo de errores de red o de la petición
        console.error('Error:', error);
        window.location.href = 'permisos.html';
    }
}

// Cerrar sesión
//se borra el token y el nombre de usuario del almacenamiento local y se redirige al usuario a la página de inicio de sesión.
document.getElementById('btnLogout').addEventListener('click', () => {
    localStorage.removeItem('token');
    localStorage.removeItem('nombre-usuario');
    window.location.href = 'login.html';
});

