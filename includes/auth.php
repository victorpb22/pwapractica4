<?php
require_once 'db.php';
require_once 'functions.php';

function login($email, $password) {
    global $db;
    
    $sql = "SELECT id, nombre, apellidos, email, rol, contrasena FROM usuarios WHERE email = ? AND activo = 1";
    $stmt = $db->executeQuery($sql, [$email]);
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['contrasena'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['rol'];
            
            return true;
        }
    }
    
    return false;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isDocente() {
    return isLoggedIn() && $_SESSION['user_role'] == ROL_DOCENTE;
}

function isEstudiante() {
    return isLoggedIn() && $_SESSION['user_role'] == ROL_ESTUDIANTE;
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: " . SITE_URL . "/login.php");
        exit();
    }
}

function redirectBasedOnRole() {
    if (isLoggedIn()) {
        if (isDocente()) {
            header("Location: " . SITE_URL . "/docente/dashboard.php");
        } else {
            header("Location: " . SITE_URL . "/estudiante/dashboard.php");
        }
        exit();
    }
}
?>