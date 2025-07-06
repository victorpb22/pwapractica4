<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

// Verificar si ya está instalado
try {
    $db->executeQuery("SELECT 1 FROM usuarios LIMIT 1");
    header("Location: index.php");
    exit();
} catch (Exception $e) {
    // Continuar con la instalación
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Crear tablas
    $sql = file_get_contents('database.sql');
    $db->getConnection()->multi_query($sql);
    
    // Crear usuario admin
    $nombre = sanitizeInput($_POST['nombre']);
    $email = sanitizeInput($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO usuarios (nombre, email, contrasena, rol, activo) VALUES (?, ?, ?, 'docente', 1)";
    $stmt = $db->executeQuery($sql, [$nombre, $email, $password]);
    
    if ($stmt->affected_rows > 0) {
        $success = true;
    } else {
        $error = "Error al crear usuario administrador";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Instalación del Sistema</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="install-container">
        <h1>Instalación del Sistema</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                Instalación completada correctamente. <a href="login.php">Iniciar sesión</a>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <h2>Crear Usuario Administrador</h2>
                
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Instalar Sistema</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>