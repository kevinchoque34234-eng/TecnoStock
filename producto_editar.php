<?php
require_once "includes/sesion.php";
requerir_login();
require_once "config/conexion.php";
require_once "includes/validar_producto.php";

$id = dato($_GET, 'id');
if (!preg_match('/^\d+$/', (string)$id) || (int)$id <= 0) {
    $_SESSION['error'] = "Producto no válido.";
    header("Location: productos.php");
    exit;
}
$id = (int)$id;

$stmt = $conexion->prepare("SELECT * FROM productos WHERE id_producto = :id");
$stmt->execute([':id' => $id]);
$producto = $stmt->fetch();

if (!$producto) {
    $_SESSION['error'] = "El producto no existe.";
    header("Location: productos.php");
    exit;
}
if ($producto['estado'] == 0) {
    $_SESSION['error'] = "El producto está inactivo. Reactívalo para poder editarlo.";
    header("Location: productos.php");
    exit;
}

$stmt = $conexion->prepare(
    "SELECT id_categoria, nombre, estado FROM categorias
     WHERE estado = 1 OR id_categoria = :actual
     ORDER BY nombre"
);
$stmt->execute([':actual' => (int)$producto['id_categoria']]);
$categorias = $stmt->fetchAll();

$errores = [];

$datos = [
    'codigo'       => $producto['codigo'],
    'nombre'       => $producto['nombre'],
    'descripcion'  => $producto['descripcion'] ?? '',
    'id_categoria' => $producto['id_categoria'],
    'precio'       => $producto['precio'],
    'stock_actual' => $producto['stock_actual'],
    'stock_minimo' => $producto['stock_minimo'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    list($errores, $datos) = validar_producto($_POST, $conexion, false, (int)$producto['id_categoria']);
    // el código no se modifica
    $datos['codigo'] = $producto['codigo'];

    if (!csrf_validar($_POST['csrf'] ?? '')) {
        array_unshift($errores, "El formulario expiró. Recarga la página e inténtalo de nuevo.");
    }

    if (count($errores) === 0) {
        try {
            $stmt = $conexion->prepare(
                "UPDATE productos
                 SET nombre = :nombre, descripcion = :descripcion, id_categoria = :id_categoria,
                     precio = :precio, stock_actual = :stock_actual, stock_minimo = :stock_minimo
                 WHERE id_producto = :id"
            );
            $stmt->execute([
                ':nombre'       => $datos['nombre'],
                ':descripcion'  => $datos['descripcion'] !== '' ? $datos['descripcion'] : null,
                ':id_categoria' => (int)$datos['id_categoria'],
                ':precio'       => $datos['precio'],
                ':stock_actual' => (int)$datos['stock_actual'],
                ':stock_minimo' => (int)$datos['stock_minimo'],
                ':id'           => $id,
            ]);

            $_SESSION['mensaje'] = "Producto '" . $datos['nombre'] . "' actualizado correctamente.";
            header("Location: productos.php");
            exit;
        } catch (PDOException $ex) {
            $errores[] = "No se pudo actualizar el producto. Inténtalo nuevamente.";
        }
    }
}

$titulo = "Editar producto";
$modo = 'editar';
$id_producto = $id;
require_once "includes/header.php";
?>

<h1 class="h3 mb-3">Editar producto</h1>

<?php require_once "includes/form_producto.php"; ?>
<?php require_once "includes/footer.php"; ?>
