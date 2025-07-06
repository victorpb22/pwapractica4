<?php
require_once 'config.php';
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Calificaciones</title>
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <h1>Sistema de Calificaciones</h1>
            </div>
            <nav class="main-nav">
                <?php if (isLoggedIn()): ?>
                    <span class="user-info">
                        <?= htmlspecialchars($_SESSION['user_name']) ?>
                        (<?= isDocente() ? 'Docente' : 'Estudiante' ?>)
                    </span>
                    <ul>
                        <?php if (isDocente()): ?>
                            <li><a href="<?= SITE_URL ?>/docente/dashboard.php">Inicio</a></li>
                            <li><a href="<?= SITE_URL ?>/docente/estudiantes.php">Estudiantes</a></li>
                            <li><a href="<?= SITE_URL ?>/docente/asignaturas.php">Asignaturas</a></li>
                            <li><a href="<?= SITE_URL ?>/docente/lugares.php">Lugares</a></li>
                            <li><a href="<?= SITE_URL ?>/docente/notas.php">Notas</a></li>
                        <?php else: ?>
                            <li><a href="<?= SITE_URL ?>/estudiante/dashboard.php">Inicio</a></li>
                            <li><a href="<?= SITE_URL ?>/estudiante/notas.php">Mis Notas</a></li>
                        <?php endif; ?>
                        <li><a href="<?= SITE_URL ?>/logout.php">Cerrar Sesión</a></li>
                    </ul>
                <?php else: ?>
                    <ul>
                        <li><a href="<?= SITE_URL ?>/login.php">Iniciar Sesión</a></li>
                        <li><a href="<?= SITE_URL ?>/register.php">Registrarse</a></li>
                    </ul>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">