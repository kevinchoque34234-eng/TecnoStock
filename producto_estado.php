<?php
require_once "includes/sesion.php";
requerir_login();
require_once "config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: productos.php");
    exit;
}

if (!csrf_validar($_POST['csrf'] ?? '')) {
    $_SESSION['error'] = "El formulario expiró. Inténtalo nuevamente.";
    header("Location: productos.php");
    exit;
}

$id     = dato($_POST, 'id');
$accion = dato($_POST, 'accion');

if (!preg_match('/^\d+$/', (string)$id) || !in_array($accion, ['desactivar', 'reactivar'], true)) {
    $_SESSION['error'] = "Solicitud no válida.";
    header("Location: productos.php");
    exit;
}

try {
    $stmt = $conexion->prepare("SELECT nombre FROM productos WHERE id_producto = :id");
    $stmt->execute([':id' => (int)$id]);
    $producto = $stmt->fetch();

    if (!$producto) {
        $_SESSION['error'] = "El producto no existe.";
    } else {
        $nuevo_estado = ($accion === 'desactivar') ? 0 : 1;
        // solo cambia el estado, el producto no se borra
        $stmt = $conexion->prepare("UPDATE productos SET estado = :estado WHERE id_producto = :id");
        $stmt->execute([':estado' => $nuevo_estado, ':id' => (int)$id]);

        $_SESSION['mensaje'] = "Producto '" . $producto['nombre'] . "' "
            . ($nuevo_estado === 0 ? "desactivado." : "reactivado.");
    }
} catch (PDOException $ex) {
    $_SESSION['error'] = "No se pudo cambiar el estado del producto.";
}

header("Location: productos.php");
exit;
