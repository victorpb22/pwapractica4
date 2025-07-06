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

// Procesar creación de asignatura
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_asignatura'])) {
    $nombre = sanitizeInput($_POST['nombre']);
    $codigo = sanitizeInput($_POST['codigo']);
    $descripcion = sanitizeInput($_POST['descripcion']);

    if (empty($nombre)) {
        $error = "El nombre es requerido";
    } else {
        $data = [
            'nombre' => $nombre,
            'codigo' => $codigo,
            'descripcion' => $descripcion
        ];
        $data = addAuditFields($data, 'creacion');

        $sql = "INSERT INTO asignaturas (".implode(",", array_keys($data)).") VALUES (".str_repeat("?,", count($data)-1)."?)";
        $stmt = $db->executeQuery($sql, array_values($data));

        if ($stmt->affected_rows > 0) {
            $mensaje = "Asignatura creada correctamente";
        } else {
            $error = "Error al crear asignatura";
        }
    }
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $db->executeQuery("DELETE FROM asignaturas WHERE id = ?", [$id]);
    if ($stmt->affected_rows > 0) {
        $mensaje = "Asignatura eliminada";
    } else {
        $error = "Error al eliminar";
    }
}

// Obtener asignaturas
$asignaturas = $db->executeQuery("SELECT * FROM asignaturas ORDER BY nombre")->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Asignaturas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Gestión de Asignaturas</h1>
        
        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?= $mensaje ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Nueva Asignatura</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Nombre*</label>
                    <input type="text" name="nombre" required>
                </div>
                <div class="form-group">
                    <label>Código</label>
                    <input type="text" name="codigo">
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion"></textarea>
                </div>
                <button type="submit" name="crear_asignatura" class="btn btn-primary">Guardar</button>
            </form>
        </div>
        
        <div class="card">
            <h2>Listado de Asignaturas</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Código</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asignaturas as $asig): ?>
                    <tr>
                        <td><?= $asig['id'] ?></td>
                        <td><?= htmlspecialchars($asig['nombre']) ?></td>
                        <td><?= htmlspecialchars($asig['codigo']) ?></td>
                        <td>
                            <a href="editar_asignatura.php?id=<?= $asig['id'] ?>" class="btn btn-edit">Editar</a>
                            <a href="asignaturas.php?eliminar=<?= $asig['id'] ?>" class="btn btn-delete" onclick="return confirm('¿Eliminar asignatura?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
</body>
</html>