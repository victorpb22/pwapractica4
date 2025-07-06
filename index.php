<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    redirectBasedOnRole();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestión de Calificaciones</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="hero">
        <div class="container">
            <h1>Bienvenido al Sistema de Gestión de Calificaciones</h1>
            <p>Una plataforma integral para docentes y estudiantes</p>
            
            <div class="cta-buttons">
                <a href="login.php" class="btn btn-primary">Iniciar Sesión</a>
                <a href="register.php" class="btn btn-secondary">Registrarse</a>
            </div>
        </div>
    </div>
    
    <div class="features">
        <div class="container">
            <div class="feature">
                <h3>Para Docentes</h3>
                <p>Gestiona estudiantes, asignaturas y calificaciones de manera eficiente</p>
            </div>
            <div class="feature">
                <h3>Para Estudiantes</h3>
                <p>Consulta tus calificaciones y progreso académico</p>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>