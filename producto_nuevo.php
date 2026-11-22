<?php
require_once "includes/sesion.php";
requerir_login();
require_once "config/conexion.php";
require_once "includes/validar_producto.php";

$errores = [];
$datos = [
    'codigo' => '', 'nombre' => '', 'descripcion' => '', 'id_categoria' => '',
    'precio' => '', 'stock_actual' => '0', 'stock_minimo' => '0',
];

$categorias = $conexion->query(
    "SELECT id_categoria, nombre, estado FROM categorias WHERE estado = 1 ORDER BY nombre"
)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // mismas reglas que valida el JavaScript
    list($errores, $datos) = validar_producto($_POST, $conexion, true);

    if (!csrf_validar($_POST['csrf'] ?? '')) {
        array_unshift($errores, "El formulario expiró. Recarga la página e inténtalo de nuevo.");
    }

    if (count($errores) === 0) {
        try {
            $stmt = $conexion->prepare(
                "INSERT INTO productos
                    (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, estado, id_categoria)
                 VALUES
                    (:codigo, :nombre, :descripcion, :precio, :stock_actual, :stock_minimo, 1, :id_categoria)"
            );
            $stmt->execute([
                ':codigo'       => $datos['codigo'],
                ':nombre'       => $datos['nombre'],
                ':descripcion'  => $datos['descripcion'] !== '' ? $datos['descripcion'] : null,
                ':precio'       => $datos['precio'],
                ':stock_actual' => (int)$datos['stock_actual'],
                ':stock_minimo' => (int)$datos['stock_minimo'],
                ':id_categoria' => (int)$datos['id_categoria'],
            ]);

            $_SESSION['mensaje'] = "Producto '" . $datos['nombre'] . "' registrado correctamente.";
            header("Location: productos.php");
            exit;
        } catch (PDOException $ex) {
            // 1062 = código duplicado detectado por MySQL
            if (isset($ex->errorInfo[1]) && $ex->errorInfo[1] == 1062) {
                $errores[] = "Ya existe un producto con ese código.";
            } else {
                $errores[] = "No se pudo registrar el producto. Inténtalo nuevamente.";
            }
        }
    }
}

// header.php imprime HTML, por eso lo anterior va antes
$titulo = "Nuevo producto";
$modo = 'nuevo';
$id_producto = 0;
require_once "includes/header.php";
?>

<h1 class="h3 mb-3">Nuevo producto</h1>

<?php if (count($categorias) === 0): ?>
    <div class="alert alert-warning">
        No hay categorías activas. <a href="categorias.php">Crea una categoría</a> antes de registrar productos.
    </div>
<?php endif; ?>

<?php require_once "includes/form_producto.php"; ?>
<?php require_once "includes/footer.php"; ?>
