// Verificar si ya está logueado
if (localStorage.getItem('token')) {
    window.location.href = 'bienvenida.html';
}

// Manejar envío del formulario
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault(); //evita que la pagina se recargue
    
    const nombreUsuario = document.getElementById('nombre-usuario').value;
    const contraseña = document.getElementById('contraseña').value;
    const errorMessage = document.getElementById('errorMessage');
    
    errorMessage.style.display = 'none';
    
    try {
        const response = await fetch('http://localhost/creacion-servicio-autenticacion-JWT/api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                'nombre-usuario': nombreUsuario, 
                'contraseña': contraseña 
            })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
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
    } catch (error) {
        errorMessage.textContent = 'Error de conexión con el servidor';
        errorMessage.style.display = 'block';
    }
});