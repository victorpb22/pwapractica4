<<?php
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

// Procesar creación de lugar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_lugar'])) {
    $nombre = sanitizeInput($_POST['nombre']);
    $direccion = sanitizeInput($_POST['direccion']);
    $descripcion = sanitizeInput($_POST['descripcion']);

    if (empty($nombre)) {
        $error = "El nombre es requerido";
    } else {
        $data = [
            'nombre' => $nombre,
            'direccion' => $direccion,
            'descripcion' => $descripcion
        ];
        $data = addAuditFields($data, 'creacion');

        $sql = "INSERT INTO lugares (".implode(",", array_keys($data)).") VALUES (".str_repeat("?,", count($data)-1)."?)";
        $stmt = $db->executeQuery($sql, array_values($data));

        if ($stmt->affected_rows > 0) {
            $mensaje = "Lugar creado correctamente";
        } else {
            $error = "Error al crear lugar";
        }
    }
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $db->executeQuery("DELETE FROM lugares WHERE id = ?", [$id]);
    if ($stmt->affected_rows > 0) {
        $mensaje = "Lugar eliminado";
    } else {
        $error = "Error al eliminar";
    }
}

// Obtener lugares
$lugares = $db->executeQuery("SELECT * FROM lugares ORDER BY nombre")->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Lugares Educativos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Gestión de Lugares Educativos</h1>
        
        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?= $mensaje ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Nuevo Lugar Educativo</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Nombre*</label>
                    <input type="text" name="nombre" required>
                </div>
                <div class="form-group">
                    <label>Dirección</label>
                    <input type="text" name="direccion">
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion"></textarea>
                </div>
                <button type="submit" name="crear_lugar" class="btn btn-primary">Guardar</button>
            </form>
        </div>
        
        <div class="card">
            <h2>Listado de Lugares</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lugares as $lug): ?>
                    <tr>
                        <td><?= $lug['id'] ?></td>
                        <td><?= htmlspecialchars($lug['nombre']) ?></td>
                        <td><?= htmlspecialchars($lug['direccion']) ?></td>
                        <td>
                            <a href="editar_lugar.php?id=<?= $lug['id'] ?>" class="btn btn-edit">Editar</a>
                            <a href="lugares.php?eliminar=<?= $lug['id'] ?>" class="btn btn-delete" onclick="return confirm('¿Eliminar lugar?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
</body>
</html>