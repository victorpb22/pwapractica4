<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();
if (!isDocente()) {
    header("Location: ../index.php");
    exit();
}

$mensaje = '';
$error = '';

// Procesar creación de estudiante
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_estudiante'])) {
    $nombre = sanitizeInput($_POST['nombre']);
    $apellidos = sanitizeInput($_POST['apellidos']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($nombre) || empty($apellidos) || empty($email) || empty($password)) {
        $error = "Todos los campos son requeridos";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $data = [
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'email' => $email,
            'contrasena' => $hashed_password,
            'rol' => ROL_ESTUDIANTE,
            'activo' => 1
        ];
        $data = addAuditFields($data, 'creacion');

        $sql = "INSERT INTO usuarios (".implode(",", array_keys($data)).") VALUES (".str_repeat("?,", count($data)-1)."?)";
        $stmt = $db->executeQuery($sql, array_values($data));

        if ($stmt->affected_rows > 0) {
            $mensaje = "Estudiante creado correctamente";
        } else {
            $error = "Error al crear estudiante";
        }
    }
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $db->executeQuery("UPDATE usuarios SET activo = 0 WHERE id = ?", [$id]);
    if ($stmt->affected_rows > 0) {
        $mensaje = "Estudiante desactivado";
    } else {
        $error = "Error al desactivar";
    }
}

// Obtener estudiantes activos
$estudiantes = $db->executeQuery("SELECT id, nombre, apellidos, email FROM usuarios WHERE rol = ? AND activo = 1", [ROL_ESTUDIANTE])->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Estudiantes</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Gestión de Estudiantes</h1>
        
        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?= $mensaje ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Nuevo Estudiante</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Nombre*</label>
                    <input type="text" name="nombre" required>
                </div>
                <div class="form-group">
                    <label>Apellidos*</label>
                    <input type="text" name="apellidos" required>
                </div>
                <div class="form-group">
                    <label>Email*</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Contraseña*</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" name="crear_estudiante" class="btn btn-primary">Guardar</button>
            </form>
        </div>
        
        <div class="card">
            <h2>Listado de Estudiantes</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Email</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estudiantes as $est): ?>
                    <tr>
                        <td><?= $est['id'] ?></td>
                        <td><?= htmlspecialchars($est['nombre']) ?></td>
                        <td><?= htmlspecialchars($est['apellidos']) ?></td>
                        <td><?= htmlspecialchars($est['email']) ?></td>
                        <td>
                            <a href="editar_estudiante.php?id=<?= $est['id'] ?>" class="btn btn-edit">Editar</a>
                            <a href="estudiantes.php?eliminar=<?= $est['id'] ?>" class="btn btn-delete" onclick="return confirm('¿Desactivar estudiante?')">Desactivar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
</body>
</html>