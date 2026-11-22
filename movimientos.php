<?php
require_once "includes/sesion.php";
requerir_login();
require_once "config/conexion.php";

$cantidad_max = 1000000;
$errores = [];

$f_producto = dato($_GET, 'producto');
$f_tipo     = '';
$f_cantidad = '';
$f_obs      = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $f_producto = dato($_POST, 'id_producto');
    $f_tipo     = dato($_POST, 'tipo');
    $f_cantidad = dato($_POST, 'cantidad');
    $f_obs      = dato($_POST, 'observacion');

    if (!csrf_validar($_POST['csrf'] ?? '')) {
        $errores[] = "El formulario expiró. Recarga la página e inténtalo de nuevo.";
    }
    if (!preg_match('/^\d+$/', $f_producto) || (int)$f_producto <= 0) {
        $errores[] = "Selecciona un producto.";
    }
    if (!in_array($f_tipo, ['ENTRADA', 'SALIDA'], true)) {
        $errores[] = "Selecciona el tipo de movimiento (entrada o salida).";
    }
    if (!preg_match('/^\d{1,7}$/', $f_cantidad) || (int)$f_cantidad <= 0 || (int)$f_cantidad > $cantidad_max) {
        $errores[] = "La cantidad debe ser un número entero mayor que cero (máximo 1.000.000).";
    }
    if (mb_strlen($f_obs) > 500) {
        $errores[] = "La observación no puede superar los 500 caracteres.";
    }

    if (count($errores) === 0) {
        $id_producto = (int)$f_producto;
        $cantidad    = (int)$f_cantidad;
        // responsable: el usuario con sesión iniciada
        $id_usuario  = (int)$_SESSION['id_usuario'];

        try {
            // transacción: el producto queda bloqueado (FOR UPDATE) hasta el commit
            $conexion->beginTransaction();

            $stmt = $conexion->prepare(
                "SELECT nombre, stock_actual, stock_minimo, estado
                 FROM productos WHERE id_producto = :id FOR UPDATE"
            );
            $stmt->execute([':id' => $id_producto]);
            $p = $stmt->fetch();

            if (!$p) {
                throw new RuntimeException("El producto no existe.");
            }
            if ($p['estado'] != 1) {
                throw new RuntimeException("El producto está inactivo y no admite movimientos.");
            }

            // una salida no puede dejar el stock bajo cero
            if ($f_tipo === 'SALIDA' && $cantidad > (int)$p['stock_actual']) {
                throw new RuntimeException(
                    "Stock insuficiente: '" . $p['nombre'] . "' tiene " . (int)$p['stock_actual']
                    . " unidad(es) y se intentó sacar " . $cantidad . "."
                );
            }

            $nuevo_stock = ($f_tipo === 'ENTRADA')
                ? (int)$p['stock_actual'] + $cantidad
                : (int)$p['stock_actual'] - $cantidad;

            if ($nuevo_stock > 2147483647) {
                throw new RuntimeException("El stock resultante supera el máximo permitido.");
            }

            // el stock se actualiza junto con el movimiento
            $stmt = $conexion->prepare("UPDATE productos SET stock_actual = :stock WHERE id_producto = :id");
            $stmt->execute([':stock' => $nuevo_stock, ':id' => $id_producto]);

            $stmt = $conexion->prepare(
                "INSERT INTO movimientos (tipo, cantidad, observacion, id_producto, id_usuario)
                 VALUES (:tipo, :cantidad, :obs, :producto, :usuario)"
            );
            $stmt->execute([
                ':tipo'     => $f_tipo,
                ':cantidad' => $cantidad,
                ':obs'      => $f_obs !== '' ? $f_obs : null,
                ':producto' => $id_producto,
                ':usuario'  => $id_usuario,
            ]);

            $conexion->commit();

            $_SESSION['mensaje'] = ($f_tipo === 'ENTRADA' ? "Entrada" : "Salida") . " registrada: '"
                . $p['nombre'] . "' (" . ($f_tipo === 'ENTRADA' ? "+" : "-") . $cantidad
                . "). Stock actual: " . $nuevo_stock . ".";

            // alerta de stock bajo
            if ($nuevo_stock < (int)$p['stock_minimo']) {
                $_SESSION['advertencia'] = "Alerta de stock bajo: '" . $p['nombre'] . "' quedó con "
                    . $nuevo_stock . " unidad(es) y su mínimo es " . (int)$p['stock_minimo'] . ".";
            }

            header("Location: movimientos.php");
            exit;
        } catch (RuntimeException $ex) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            $errores[] = $ex->getMessage();
        } catch (PDOException $ex) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            $errores[] = "No se pudo registrar el movimiento. Inténtalo nuevamente.";
        }
    }
}

$titulo = "Movimientos";
require_once "includes/header.php";

$mensaje     = $_SESSION['mensaje'] ?? "";
$advertencia = $_SESSION['advertencia'] ?? "";
unset($_SESSION['mensaje'], $_SESSION['advertencia']);

$productos_activos = $conexion->query(
    "SELECT id_producto, codigo, nombre, stock_actual, stock_minimo
     FROM productos WHERE estado = 1 ORDER BY nombre"
)->fetchAll();

$productos_todos = $conexion->query(
    "SELECT id_producto, codigo, nombre FROM productos ORDER BY nombre"
)->fetchAll();

$h_tipo     = dato($_GET, 'h_tipo');
$h_producto = dato($_GET, 'h_producto');

$sql = "SELECT m.id_movimiento, m.fecha_hora, m.tipo, m.cantidad, m.observacion,
               p.codigo, p.nombre AS producto, u.nombre AS usuario
        FROM movimientos m
        INNER JOIN productos p ON m.id_producto = p.id_producto
        INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
        WHERE 1 = 1";
$parametros = [];

if ($h_tipo === 'ENTRADA' || $h_tipo === 'SALIDA') {
    $sql .= " AND m.tipo = :tipo";
    $parametros[':tipo'] = $h_tipo;
} else {
    $h_tipo = '';
}
if (preg_match('/^\d+$/', (string)$h_producto) && (int)$h_producto > 0) {
    $sql .= " AND m.id_producto = :producto";
    $parametros[':producto'] = (int)$h_producto;
} else {
    $h_producto = '';
}

$sql .= " ORDER BY m.fecha_hora DESC, m.id_movimiento DESC LIMIT 100";
$stmt = $conexion->prepare($sql);
$stmt->execute($parametros);
$historial = $stmt->fetchAll();
?>

<h1 class="h3 mb-3">Movimientos de inventario</h1>

<?php if ($mensaje !== ""): ?>
    <div class="alert alert-success"><?php echo e($mensaje); ?></div>
<?php endif; ?>
<?php if ($advertencia !== ""): ?>
    <div class="alert alert-warning"><strong>&#9888;</strong> <?php echo e($advertencia); ?></div>
<?php endif; ?>
<?php if (count($errores) > 0): ?>
    <div class="alert alert-danger">
        <strong>No se registró el movimiento:</strong>
        <ul class="mb-0">
            <?php foreach ($errores as $err): ?><li><?php echo e($err); ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-3">

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header fw-bold">Registrar movimiento</div>
            <div class="card-body">
                <?php if (count($productos_activos) === 0): ?>
                    <p class="text-muted mb-0">No hay productos activos. <a href="producto_nuevo.php">Registra uno</a> primero.</p>
                <?php else: ?>
                <form method="POST" action="movimientos.php" class="needs-validation" novalidate>
                    <?php echo csrf_campo(); ?>

                    <div class="mb-3">
                        <label for="id_producto" class="form-label">Producto *</label>
                        <select class="form-select" id="id_producto" name="id_producto" required>
                            <option value="">-- Selecciona --</option>
                            <?php foreach ($productos_activos as $p): ?>
                                <option value="<?php echo (int)$p['id_producto']; ?>"
                                        data-stock="<?php echo (int)$p['stock_actual']; ?>"
                                        data-minimo="<?php echo (int)$p['stock_minimo']; ?>"
                                        <?php echo ((string)$f_producto === (string)$p['id_producto']) ? 'selected' : ''; ?>>
                                    <?php echo e($p['codigo'] . ' - ' . $p['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Selecciona un producto.</div>
                        <div class="form-text" id="info-stock"></div>
                    </div>

                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo *</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="">-- Selecciona --</option>
                            <option value="ENTRADA" <?php echo $f_tipo === 'ENTRADA' ? 'selected' : ''; ?>>Entrada (suma stock)</option>
                            <option value="SALIDA"  <?php echo $f_tipo === 'SALIDA' ? 'selected' : ''; ?>>Salida (resta stock)</option>
                        </select>
                        <div class="invalid-feedback">Selecciona el tipo de movimiento.</div>
                    </div>

                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad *</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad"
                               min="1" max="1000000" step="1" required
                               value="<?php echo e($f_cantidad); ?>">
                        <div class="invalid-feedback" id="feedback-cantidad">Ingresa un número entero mayor que cero.</div>
                    </div>

                    <div class="mb-3">
                        <label for="observacion" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacion" name="observacion" rows="2"
                                  maxlength="500" placeholder="Ej: Reposición proveedor, venta mostrador"><?php echo e($f_obs); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Registrar movimiento</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header fw-bold">Historial de movimientos</div>
            <div class="card-body">
                <form method="GET" action="movimientos.php" class="row g-2 mb-3">
                    <div class="col-md-5">
                        <select name="h_producto" class="form-select form-select-sm">
                            <option value="">Todos los productos</option>
                            <?php foreach ($productos_todos as $p): ?>
                                <option value="<?php echo (int)$p['id_producto']; ?>"
                                    <?php echo ((string)$h_producto === (string)$p['id_producto']) ? 'selected' : ''; ?>>
                                    <?php echo e($p['codigo'] . ' - ' . $p['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="h_tipo" class="form-select form-select-sm">
                            <option value="">Entradas y salidas</option>
                            <option value="ENTRADA" <?php echo $h_tipo === 'ENTRADA' ? 'selected' : ''; ?>>Solo entradas</option>
                            <option value="SALIDA"  <?php echo $h_tipo === 'SALIDA' ? 'selected' : ''; ?>>Solo salidas</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-dark btn-sm">Filtrar</button>
                        <a href="movimientos.php" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                    </div>
                </form>

                <?php if (count($historial) === 0): ?>
                    <p class="text-muted mb-0">No hay movimientos para mostrar.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Producto</th>
                                    <th class="text-end">Cantidad</th>
                                    <th>Usuario</th>
                                    <th>Observación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial as $m): ?>
                                    <tr>
                                        <td class="text-nowrap"><?php echo e(date('d/m/Y H:i', strtotime($m['fecha_hora']))); ?></td>
                                        <td>
                                            <span class="badge <?php echo $m['tipo'] === 'ENTRADA' ? 'text-bg-success' : 'text-bg-warning'; ?>">
                                                <?php echo e($m['tipo']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo e($m['codigo'] . ' - ' . $m['producto']); ?></td>
                                        <td class="text-end"><?php echo ($m['tipo'] === 'ENTRADA' ? '+' : '-') . (int)$m['cantidad']; ?></td>
                                        <td><?php echo e($m['usuario']); ?></td>
                                        <td><?php echo e($m['observacion']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted small mt-2 mb-0">
                        Se muestran los últimos <?php echo count($historial); ?> movimiento(s) (máximo 100).
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
