<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Destruir la sesión
$_SESSION = array();
session_destroy();

// Redirigir al login
header("Location: login.php");
exit();
?>