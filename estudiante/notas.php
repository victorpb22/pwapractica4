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

// Obtener notas del estudiante
$notas = $db->executeQuery(
    "SELECT n.id, a.nombre AS asignatura, l.nombre AS lugar, 
            n.parcial, n.teoria, n.practica, 
            (n.teoria + n.practica) / 2 AS promedio,
            n.observaciones, n.creado_en
     FROM notas n
     JOIN asignaturas a ON n.asignatura_id = a.id
     JOIN lugares l ON n.lugar_id = l.id
     WHERE n.estudiante_id = ?
     ORDER BY n.creado_en DESC", 
    [$estudiante_id]
)->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Calificaciones</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Mis Calificaciones</h1>
        
        <?php if (empty($notas)): ?>
            <p>No tienes calificaciones registradas aún.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Asignatura</th>
                        <th>Lugar</th>
                        <th>Parcial</th>
                        <th>Teoría</th>
                        <th>Práctica</th>
                        <th>Promedio</th>
                        <th>Fecha</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notas as $nota): ?>
                    <tr>
                        <td><?= htmlspecialchars($nota['asignatura']) ?></td>
                        <td><?= htmlspecialchars($nota['lugar']) ?></td>
                        <td>
                            <?php 
                                switch($nota['parcial']) {
                                    case '1': echo '1er'; break;
                                    case '2': echo '2do'; break;
                                    case '3': echo 'Mejor.'; break;
                                }
                            ?>
                        </td>
                        <td><?= number_format($nota['teoria'], 2) ?></td>
                        <td><?= number_format($nota['practica'], 2) ?></td>
                        <td><?= number_format($nota['promedio'], 2) ?></td>
                        <td><?= formatDate($nota['creado_en']) ?></td>
                        <td><?= htmlspecialchars($nota['observaciones']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
</body>
</html>