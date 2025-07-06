<?php
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

function addAuditFields($data, $action = 'creacion') {
    $currentUserId = $_SESSION['user_id'] ?? null;
    $currentDateTime = date('Y-m-d H:i:s');
    
    if ($action === 'creacion') {
        $data['creado_por'] = $currentUserId;
        $data['creado_en'] = $currentDateTime;
    } elseif ($action === 'actualizacion') {
        $data['actualizado_por'] = $currentUserId;
        $data['actualizado_en'] = $currentDateTime;
    }
    
    return $data;
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function jsonResponse($data, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit();
}
?>