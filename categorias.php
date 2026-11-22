<?php
require_once "includes/sesion.php";
requerir_login();
require_once "config/conexion.php";

$errores = [];
$nombre = "";
$descripcion = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = dato($_POST, 'accion');

    if (!csrf_validar($_POST['csrf'] ?? '')) {
        $_SESSION['error'] = "El formulario expiró. Inténtalo nuevamente.";
        header("Location: categorias.php");
        exit;
    }

    if ($accion === 'crear') {
        $nombre      = dato($_POST, 'nombre');
        $descripcion = dato($_POST, 'descripcion');

        if ($nombre === '') {
            $errores[] = "El nombre es obligatorio.";
        } elseif (mb_strlen($nombre) > 100) {
            $errores[] = "El nombre no puede superar los 100 caracteres.";
        }
        if (mb_strlen($descripcion) > 500) {
            $errores[] = "La descripción no puede superar los 500 caracteres.";
        }

        if (count($errores) === 0) {
            $stmt = $conexion->prepare("SELECT COUNT(*) FROM categorias WHERE nombre = :nombre");
            $stmt->execute([':nombre' => $nombre]);
            if ($stmt->fetchColumn() > 0) {
                $errores[] = "Ya existe una categoría con ese nombre.";
            }
        }

        if (count($errores) === 0) {
            try {
                $stmt = $conexion->prepare(
                    "INSERT INTO categorias (nombre, descripcion, estado) VALUES (:nombre, :descripcion, 1)"
                );
                $stmt->execute([
                    ':nombre'      => $nombre,
                    ':descripcion' => $descripcion !== '' ? $descripcion : null,
                ]);
                $_SESSION['mensaje'] = "Categoría '" . $nombre . "' creada correctamente.";
                header("Location: categorias.php");
                exit;
            } catch (PDOException $ex) {
                if (isset($ex->errorInfo[1]) && $ex->errorInfo[1] == 1062) {
                    $errores[] = "Ya existe una categoría con ese nombre.";
                } else {
                    $errores[] = "No se pudo crear la categoría.";
                }
            }
        }
    }

    if ($accion === 'estado') {
        $id     = dato($_POST, 'id');
        $nuevo  = dato($_POST, 'nuevo_estado');
        if (preg_match('/^\d+$/', (string)$id) && ($nuevo === '0' || $nuevo === '1')) {
            try {
                $stmt = $conexion->prepare("UPDATE categorias SET estado = :estado WHERE id_categoria = :id");
                $stmt->execute([':estado' => (int)$nuevo, ':id' => (int)$id]);
                $_SESSION['mensaje'] = $nuevo === '1' ? "Categoría activada." : "Categoría desactivada.";
            } catch (PDOException $ex) {
                $_SESSION['error'] = "No se pudo cambiar el estado de la categoría.";
            }
        } else {
            $_SESSION['error'] = "Solicitud no válida.";
        }
        header("Location: categorias.php");
        exit;
    }
}

$titulo = "Categorías";
require_once "includes/header.php";

$mensaje = $_SESSION['mensaje'] ?? "";
$error   = $_SESSION['error'] ?? "";
unset($_SESSION['mensaje'], $_SESSION['error']);

$categorias = $conexion->query(
    "SELECT c.id_categoria, c.nombre, c.descripcion, c.estado, COUNT(p.id_producto) AS total
     FROM categorias c
     LEFT JOIN productos p ON p.id_categoria = c.id_categoria
     GROUP BY c.id_categoria, c.nombre, c.descripcion, c.estado
     ORDER BY c.nombre"
)->fetchAll();
?>

<h1 class="h3 mb-3">Categorías</h1>

<?php if ($mensaje !== ""): ?>
    <div class="alert alert-success"><?php echo e($mensaje); ?></div>
<?php endif; ?>
<?php if ($error !== ""): ?>
    <div class="alert alert-danger"><?php echo e($error); ?></div>
<?php endif; ?>
<?php if (count($errores) > 0): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errores as $err): ?><li><?php echo e($err); ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header fw-bold">Nueva categoría</div>
            <div class="card-body">
                <form method="POST" action="categorias.php" class="needs-validation" novalidate>
                    <?php echo csrf_campo(); ?>
                    <input type="hidden" name="accion" value="crear">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100" required
                               value="<?php echo e($nombre); ?>">
                        <div class="invalid-feedback">El nombre es obligatorio.</div>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="2"
                                  maxlength="500"><?php echo e($descripcion); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Crear categoría</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <?php if (count($categorias) === 0): ?>
                    <p class="text-muted mb-0">Aún no hay categorías.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th class="text-end">Productos</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorias as $c): ?>
                                    <tr class="<?php echo $c['estado'] == 0 ? 'table-secondary' : ''; ?>">
                                        <td><?php echo e($c['nombre']); ?></td>
                                        <td><?php echo e($c['descripcion']); ?></td>
                                        <td class="text-end"><?php echo (int)$c['total']; ?></td>
                                        <td>
                                            <span class="badge <?php echo $c['estado'] == 1 ? 'text-bg-success' : 'text-bg-secondary'; ?>">
                                                <?php echo $c['estado'] == 1 ? 'Activa' : 'Inactiva'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" action="categorias.php" class="d-inline"
                                                  data-confirmar="¿<?php echo $c['estado'] == 1 ? 'Desactivar' : 'Activar'; ?> la categoría '<?php echo e($c['nombre']); ?>'?">
                                                <?php echo csrf_campo(); ?>
                                                <input type="hidden" name="accion" value="estado">
                                                <input type="hidden" name="id" value="<?php echo (int)$c['id_categoria']; ?>">
                                                <input type="hidden" name="nuevo_estado" value="<?php echo $c['estado'] == 1 ? '0' : '1'; ?>">
                                                <button type="submit"
                                                        class="btn btn-sm <?php echo $c['estado'] == 1 ? 'btn-outline-danger' : 'btn-outline-secondary'; ?>">
                                                    <?php echo $c['estado'] == 1 ? 'Desactivar' : 'Activar'; ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <p class="text-muted small mt-2">Una categoría inactiva ya no aparece al registrar productos nuevos, pero los productos existentes la conservan.</p>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
