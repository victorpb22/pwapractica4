<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();
if (!isEstudiante()) {
    header("Location: ../index.php");
    exit();
}

$estudiante_id = $_SESSION['user_id'];

// Obtener información del estudiante
$estudiante = $db->executeQuery(
    "SELECT nombre, apellidos, email FROM usuarios WHERE id = ?", 
    [$estudiante_id]
)->get_result()->fetch_assoc();

// Obtener asignaturas del estudiante
$asignaturas = $db->executeQuery(
    "SELECT a.nombre, a.id 
     FROM estudiante_asignatura ea
     JOIN asignaturas a ON ea.asignatura_id = a.id
     WHERE ea.estudiante_id = ?", 
    [$estudiante_id]
)->get_result()->fetch_all(MYSQLI_ASSOC);

// Obtener últimas notas
$ultimas_notas = $db->executeQuery(
    "SELECT a.nombre as asignatura, n.teoria, n.practica, (n.teoria + n.practica)/2 as promedio
     FROM notas n
     JOIN asignaturas a ON n.asignatura_id = a.id
     WHERE n.estudiante_id = ?
     ORDER BY n.creado_en DESC LIMIT 5", 
    [$estudiante_id]
)->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Estudiante</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Bienvenido, <?= htmlspecialchars($estudiante['nombre'] . ' ' . $estudiante['apellidos']) ?></h1>
        
        <div class="stats-container">
            <div class="stat-card">
                <h3>Asignaturas</h3>
                <div class="stat-value"><?= count($asignaturas) ?></div>
            </div>
        </div>
        
        <div class="card">
            <h2>Mis Últimas Calificaciones</h2>
            <?php if (empty($ultimas_notas)): ?>
                <p>No tienes calificaciones registradas aún.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Asignatura</th>
                            <th>Teoría</th>
                            <th>Práctica</th>
                            <th>Promedio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimas_notas as $nota): ?>
                        <tr>
                            <td><?= htmlspecialchars($nota['asignatura']) ?></td>
                            <td><?= number_format($nota['teoria'], 2) ?></td>
                            <td><?= number_format($nota['practica'], 2) ?></td>
                            <td><?= number_format($nota['promedio'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>Mis Asignaturas</h2>
            <?php if (empty($asignaturas)): ?>
                <p>No estás inscrito en ninguna asignatura.</p>
            <?php else: ?>
                <ul class="asignaturas-list">
                    <?php foreach ($asignaturas as $asig): ?>
                    <li><?= htmlspecialchars($asig['nombre']) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>