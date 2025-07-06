<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirectBasedOnRole();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = sanitizeInput($_POST['nombre']);
    $apellidos = sanitizeInput($_POST['apellidos']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validaciones
    if (empty($nombre)) $errors['nombre'] = "Nombre requerido";
    if (empty($apellidos)) $errors['apellidos'] = "Apellidos requeridos";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email inválido";
    } else {
        $stmt = $db->executeQuery("SELECT id FROM usuarios WHERE email = ?", [$email]);
        if ($stmt->get_result()->num_rows > 0) {
            $errors['email'] = "Email ya registrado";
        }
    }
    
    if (strlen($password) < 8) {
        $errors['password'] = "Mínimo 8 caracteres";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'] = "Debe contener mayúscula y número";
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Las contraseñas no coinciden";
    }
    
    // Registrar si no hay errores
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $data = [
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'email' => $email,
            'contrasena' => $hashed_password,
            'rol' => ROL_ESTUDIANTE,
            'activo' => 1
        ];
        
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO usuarios ($columns) VALUES ($placeholders)";
        $stmt = $db->executeQuery($sql, array_values($data));
        
        if ($stmt->affected_rows > 0) {
            $success = true;
        } else {
            $errors['general'] = "Error al registrar. Intente nuevamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="register-container">
        <h1>Registro de Usuario</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                Registro exitoso. <a href="login.php">Iniciar sesión</a>
            </div>
        <?php else: ?>
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger"><?= $errors['general'] ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Nombre*</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
                    <?php if (isset($errors['nombre'])): ?>
                        <span class="error"><?= $errors['nombre'] ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label>Apellidos*</label>
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($_POST['apellidos'] ?? '') ?>" required>
                    <?php if (isset($errors['apellidos'])): ?>
                        <span class="error"><?= $errors['apellidos'] ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label>Email*</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error"><?= $errors['email'] ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label>Contraseña*</label>
                    <input type="password" name="password" required>
                    <small>Mínimo 8 caracteres, una mayúscula y un número</small>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error"><?= $errors['password'] ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label>Confirmar Contraseña*</label>
                    <input type="password" name="confirm_password" required>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <span class="error"><?= $errors['confirm_password'] ?></span>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary">Registrarse</button>
            </form>
            
            <div class="login-link">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>