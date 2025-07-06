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

// Procesar formulario de notas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $estudiante_id = intval($_POST['estudiante_id']);
    $asignatura_id = intval($_POST['asignatura_id']);
    $lugar_id = intval($_POST['lugar_id']);
    $parcial = sanitizeInput($_POST['parcial']);
    $teoria = floatval($_POST['teoria']);
    $practica = floatval($_POST['practica']);
    $observaciones = sanitizeInput($_POST['observaciones']);

    $data = [
        'estudiante_id' => $estudiante_id,
        'asignatura_id' => $asignatura_id,
        'docente_id' => $_SESSION['user_id'],
        'lugar_id' => $lugar_id,
        'parcial' => $parcial,
        'teoria' => $teoria,
        'practica' => $practica,
        'observaciones' => $observaciones
    ];

    $data = addAuditFields($data, 'creacion');

    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), '?'));
    $values = array_values($data);

    $sql = "INSERT INTO notas ($columns) VALUES ($placeholders)";
    $stmt = $db->executeQuery($sql, $values);

    if ($stmt->affected_rows > 0) {
        $mensaje = "Notas registradas correctamente";
    } else {
        $error = "Error al registrar las notas";
    }
}

// Obtener datos para los selects
$estudiantes = $db->executeQuery("SELECT id, nombre, apellidos FROM usuarios WHERE rol = ?", [ROL_ESTUDIANTE])->get_result()->fetch_all(MYSQLI_ASSOC);
$asignaturas = $db->executeQuery("SELECT id, nombre FROM asignaturas")->get_result()->fetch_all(MYSQLI_ASSOC);
$lugares = $db->executeQuery("SELECT id, nombre FROM lugares")->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Notas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Registrar Notas</h1>
        
        <form method="POST">
            <div class="form-group">
                <label>Estudiante</label>
                <select name="estudiante_id" required>
                    <?php foreach ($estudiantes as $est): ?>
                        <option value="<?= $est['id'] ?>"><?= htmlspecialchars($est['nombre'] . ' ' . $est['apellidos']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Asignatura</label>
                <select name="asignatura_id" required>
                    <?php foreach ($asignaturas as $asig): ?>
                        <option value="<?= $asig['id'] ?>"><?= htmlspecialchars($asig['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Lugar Educativo</label>
                <select name="lugar_id" required>
                    <?php foreach ($lugares as $lug): ?>
                        <option value="<?= $lug['id'] ?>"><?= htmlspecialchars($lug['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Parcial</label>
                <select name="parcial" required>
                    <option value="1">Primer Parcial</option>
                    <option value="2">Segundo Parcial</option>
                    <option value="3">Mejoramiento</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Nota Teoría</label>
                <input type="number" name="teoria" step="0.01" min="0" max="10" required>
            </div>
            
            <div class="form-group">
                <label>Nota Práctica</label>
                <input type="number" name="practica" step="0.01" min="0" max="10" required>
            </div>
            
            <div class="form-group">
                <label>Observaciones</label>
                <textarea name="observaciones"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Guardar Notas</button>
        </form>
    </div>
    
</body>
</html>