<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function requerir_login() {
    // sin caché: el botón "atrás" no debe mostrar páginas tras cerrar sesión
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");

    if (!isset($_SESSION['id_usuario'])) {
        header("Location: login.php");
        exit;
    }
}

// token anti-CSRF, uno por sesión
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_campo() {
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_validar($token) {
    return isset($_SESSION['csrf']) && is_string($token) && hash_equals($_SESSION['csrf'], $token);
}

function dato($origen, $clave, $recortar = true) {
    $valor = $origen[$clave] ?? '';
    if (!is_string($valor)) {
        return '';
    }
    return $recortar ? trim($valor) : $valor;
}
