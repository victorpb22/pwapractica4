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

// Estadísticas
$total_estudiantes = $db->executeQuery("SELECT COUNT(*) as total FROM usuarios WHERE rol = ?", [ROL_ESTUDIANTE])->get_result()->fetch_assoc()['total'];
$total_asignaturas = $db->executeQuery("SELECT COUNT(*) as total FROM asignaturas")->get_result()->fetch_assoc()['total'];
$total_lugares = $db->executeQuery("SELECT COUNT(*) as total FROM lugares")->get_result()->fetch_assoc()['total'];
$notas_recientes = $db->executeQuery(
    "SELECT n.id, u.nombre as estudiante, a.nombre as asignatura, n.teoria, n.practica 
     FROM notas n
     JOIN usuarios u ON n.estudiante_id = u.id
     JOIN asignaturas a ON n.asignatura_id = a.id
     ORDER BY n.creado_en DESC LIMIT 5"
)->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Docente</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
        
        <div class="stats-container">
            <div class="stat-card">
                <h3>Estudiantes</h3>
                <div class="stat-value"><?= $total_estudiantes ?></div>
            </div>
            <div class="stat-card">
                <h3>Asignaturas</h3>
                <div class="stat-value"><?= $total_asignaturas ?></div>
            </div>
            <div class="stat-card">
                <h3>Lugares</h3>
                <div class="stat-value"><?= $total_lugares ?></div>
            </div>
        </div>
        
        <div class="card">
            <h2>Últimas Calificaciones</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Asignatura</th>
                        <th>Teoría</th>
                        <th>Práctica</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notas_recientes as $nota): ?>
                    <tr>
                        <td><?= htmlspecialchars($nota['estudiante']) ?></td>
                        <td><?= htmlspecialchars($nota['asignatura']) ?></td>
                        <td><?= $nota['teoria'] ?></td>
                        <td><?= $nota['practica'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>