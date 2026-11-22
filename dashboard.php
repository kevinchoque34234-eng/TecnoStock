<?php
$titulo = "Dashboard";
require_once "includes/header.php";
require_once "config/conexion.php";

$total_productos = $conexion->query("SELECT COUNT(*) FROM productos")->fetchColumn();

$total_activos = $conexion->query("SELECT COUNT(*) FROM productos WHERE estado = 1")->fetchColumn();

// regla: stock_actual < stock_minimo, solo productos activos
$total_stock_bajo = $conexion->query(
    "SELECT COUNT(*) FROM productos WHERE stock_actual < stock_minimo AND estado = 1"
)->fetchColumn();

$stock_bajo = $conexion->query(
    "SELECT p.codigo, p.nombre, c.nombre AS categoria, p.stock_actual, p.stock_minimo
     FROM productos p
     INNER JOIN categorias c ON p.id_categoria = c.id_categoria
     WHERE p.stock_actual < p.stock_minimo AND p.estado = 1
     ORDER BY p.stock_actual ASC"
)->fetchAll();

$ultimos_movimientos = $conexion->query(
    "SELECT m.fecha_hora, m.tipo, m.cantidad, p.nombre AS producto, u.nombre AS usuario
     FROM movimientos m
     INNER JOIN productos p ON m.id_producto = p.id_producto
     INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
     ORDER BY m.fecha_hora DESC, m.id_movimiento DESC
     LIMIT 5"
)->fetchAll();
?>

<h1 class="h3 mb-4">Resumen del inventario</h1>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <div class="fs-1 fw-bold"><?php echo (int)$total_productos; ?></div>
                <div>Productos registrados</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-success h-100">
            <div class="card-body">
                <div class="fs-1 fw-bold"><?php echo (int)$total_activos; ?></div>
                <div>Productos activos</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card <?php echo $total_stock_bajo > 0 ? 'text-bg-danger' : 'text-bg-secondary'; ?> h-100">
            <div class="card-body">
                <div class="fs-1 fw-bold"><?php echo (int)$total_stock_bajo; ?></div>
                <div>Productos con stock bajo</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-bold">Alertas de stock bajo</div>
    <div class="card-body">
        <?php if (count($stock_bajo) === 0): ?>
            <p class="text-muted mb-0">No hay productos con stock bajo.</p>
        <?php else: ?>
            <div class="alert alert-danger">
                Hay <?php echo count($stock_bajo); ?> producto(s) con stock actual menor al mínimo.
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-end">Stock actual</th>
                            <th class="text-end">Stock mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stock_bajo as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['codigo']); ?></td>
                                <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($p['categoria']); ?></td>
                                <td class="text-end text-danger fw-bold"><?php echo (int)$p['stock_actual']; ?></td>
                                <td class="text-end"><?php echo (int)$p['stock_minimo']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-bold">Últimos movimientos</div>
    <div class="card-body">
        <?php if (count($ultimos_movimientos) === 0): ?>
            <p class="text-muted mb-0">Aún no hay movimientos registrados.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Producto</th>
                            <th class="text-end">Cantidad</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimos_movimientos as $m): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m['fecha_hora']); ?></td>
                                <td>
                                    <span class="badge <?php echo $m['tipo'] === 'ENTRADA' ? 'text-bg-success' : 'text-bg-warning'; ?>">
                                        <?php echo htmlspecialchars($m['tipo']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($m['producto']); ?></td>
                                <td class="text-end"><?php echo (int)$m['cantidad']; ?></td>
                                <td><?php echo htmlspecialchars($m['usuario']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
