<?php
require_once __DIR__ . "/sesion.php";
requerir_login();

$titulo = $titulo ?? "TecnoStock";

// para marcar el enlace activo del menú
$pagina_actual = basename($_SERVER['PHP_SELF']);

$paginas_productos = ['productos.php', 'producto_nuevo.php', 'producto_editar.php'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($titulo); ?> - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">TecnoStock</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $pagina_actual === 'dashboard.php' ? 'active' : ''; ?>"
                       href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($pagina_actual, $paginas_productos) ? 'active' : ''; ?>"
                       href="productos.php">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $pagina_actual === 'categorias.php' ? 'active' : ''; ?>"
                       href="categorias.php">Categorías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $pagina_actual === 'movimientos.php' ? 'active' : ''; ?>"
                       href="movimientos.php">Movimientos</a>
                </li>
            </ul>
            <span class="navbar-text me-3">
                <?php echo e($_SESSION['nombre']); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
