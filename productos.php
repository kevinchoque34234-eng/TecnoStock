<?php
$titulo = "Productos";
require_once "includes/header.php";
require_once "config/conexion.php";

$mensaje = $_SESSION['mensaje'] ?? "";
$error   = $_SESSION['error'] ?? "";
unset($_SESSION['mensaje'], $_SESSION['error']);

$buscar = dato($_GET, 'buscar');
$estado = $_GET['estado'] ?? '1';

$sql = "SELECT p.id_producto, p.codigo, p.nombre, c.nombre AS categoria,
               p.precio, p.stock_actual, p.stock_minimo, p.estado
        FROM productos p
        INNER JOIN categorias c ON p.id_categoria = c.id_categoria
        WHERE 1 = 1";
$parametros = [];

// búsqueda por nombre, código o categoría
if ($buscar !== '') {
    $sql .= " AND (p.nombre LIKE :q1 OR p.codigo LIKE :q2 OR c.nombre LIKE :q3)";
    $parametros[':q1'] = "%" . $buscar . "%";
    $parametros[':q2'] = "%" . $buscar . "%";
    $parametros[':q3'] = "%" . $buscar . "%";
}

if ($estado === '1' || $estado === '0') {
    $sql .= " AND p.estado = :estado";
    $parametros[':estado'] = (int)$estado;
} else {
    $estado = 'todos';
}

$sql .= " ORDER BY p.nombre ASC";

// los valores van como parámetros, no dentro del SQL
$stmt = $conexion->prepare($sql);
$stmt->execute($parametros);
$productos = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Productos</h1>
    <a href="producto_nuevo.php" class="btn btn-primary">+ Nuevo producto</a>
</div>

<?php if ($mensaje !== ""): ?>
    <div class="alert alert-success"><?php echo e($mensaje); ?></div>
<?php endif; ?>
<?php if ($error !== ""): ?>
    <div class="alert alert-danger"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="GET" action="productos.php" class="row g-2 mb-3">
    <div class="col-md-6">
        <input type="text" name="buscar" class="form-control"
               placeholder="Buscar por nombre, código o categoría"
               value="<?php echo e($buscar); ?>">
    </div>
    <div class="col-md-3">
        <select name="estado" class="form-select">
            <option value="1"     <?php echo $estado === '1' ? 'selected' : ''; ?>>Solo activos</option>
            <option value="0"     <?php echo $estado === '0' ? 'selected' : ''; ?>>Solo inactivos</option>
            <option value="todos" <?php echo $estado === 'todos' ? 'selected' : ''; ?>>Todos</option>
        </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-dark">Buscar</button>
        <a href="productos.php" class="btn btn-outline-secondary">Limpiar</a>
    </div>
</form>

<div class="card">
    <div class="card-body">
        <?php if (count($productos) === 0): ?>
            <p class="text-muted mb-0">No se encontraron productos.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th class="text-end">Precio</th>
                            <th class="text-end">Stock</th>
                            <th class="text-end">Mínimo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $p): ?>
                            <?php $stock_bajo = ($p['stock_actual'] < $p['stock_minimo']); ?>
                            <tr class="<?php echo $p['estado'] == 0 ? 'table-secondary' : ''; ?>">
                                <td><?php echo e($p['codigo']); ?></td>
                                <td><?php echo e($p['nombre']); ?></td>
                                <td><?php echo e($p['categoria']); ?></td>
                                <td class="text-end">$<?php echo number_format($p['precio'], fmod((float)$p['precio'], 1) == 0 ? 0 : 2, ',', '.'); ?></td>
                                <td class="text-end <?php echo ($stock_bajo && $p['estado'] == 1) ? 'text-danger fw-bold' : ''; ?>">
                                    <?php echo (int)$p['stock_actual']; ?>
                                    <?php if ($stock_bajo && $p['estado'] == 1): ?>
                                        <span class="badge text-bg-danger">Stock bajo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end"><?php echo (int)$p['stock_minimo']; ?></td>
                                <td>
                                    <?php if ($p['estado'] == 1): ?>
                                        <span class="badge text-bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap">
                                    <?php if ($p['estado'] == 1): ?>
                                        <a href="movimientos.php?producto=<?php echo (int)$p['id_producto']; ?>"
                                           class="btn btn-sm btn-outline-success">Mover stock</a>
                                        <a href="producto_editar.php?id=<?php echo (int)$p['id_producto']; ?>"
                                           class="btn btn-sm btn-outline-primary">Editar</a>
                                        <form method="POST" action="producto_estado.php" class="d-inline"
                                              data-confirmar="¿Desactivar el producto '<?php echo e($p['nombre']); ?>'? Dejará de aparecer en los movimientos, pero su historial se conserva.">
                                            <?php echo csrf_campo(); ?>
                                            <input type="hidden" name="id" value="<?php echo (int)$p['id_producto']; ?>">
                                            <input type="hidden" name="accion" value="desactivar">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Desactivar</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="producto_estado.php" class="d-inline"
                                              data-confirmar="¿Reactivar el producto '<?php echo e($p['nombre']); ?>'?">
                                            <?php echo csrf_campo(); ?>
                                            <input type="hidden" name="id" value="<?php echo (int)$p['id_producto']; ?>">
                                            <input type="hidden" name="accion" value="reactivar">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Reactivar</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<p class="text-muted small mt-2">
    <?php echo count($productos); ?> producto(s) encontrado(s).
</p>

<?php require_once "includes/footer.php"; ?>
