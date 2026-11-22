<?php
$accion_url = ($modo === 'nuevo') ? 'producto_nuevo.php' : 'producto_editar.php?id=' . (int)$id_producto;
?>
<?php if (count($errores) > 0): ?>
    <div class="alert alert-danger">
        <strong>Revisa los siguientes puntos:</strong>
        <ul class="mb-0">
            <?php foreach ($errores as $err): ?>
                <li><?php echo e($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo e($accion_url); ?>" class="needs-validation" novalidate>
            <?php echo csrf_campo(); ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="codigo" class="form-label">Código *</label>
                    <?php if ($modo === 'nuevo'): ?>
                        <input type="text" class="form-control" id="codigo" name="codigo" maxlength="50"
                               pattern="[A-Za-z0-9_\-]+" required
                               value="<?php echo e($datos['codigo']); ?>">
                        <div class="invalid-feedback">Obligatorio. Solo letras, números, guion y guion bajo (sin espacios).</div>
                    <?php else: ?>
                        <input type="text" class="form-control" id="codigo" value="<?php echo e($datos['codigo']); ?>" disabled>
                        <div class="form-text">El código no se puede modificar.</div>
                    <?php endif; ?>
                </div>

                <div class="col-md-8">
                    <label for="nombre" class="form-label">Nombre *</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" maxlength="150" required
                           value="<?php echo e($datos['nombre']); ?>">
                    <div class="invalid-feedback">El nombre es obligatorio (máximo 150 caracteres).</div>
                </div>

                <div class="col-md-6">
                    <label for="id_categoria" class="form-label">Categoría *</label>
                    <select class="form-select" id="id_categoria" name="id_categoria" required>
                        <option value="">-- Selecciona --</option>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?php echo (int)$c['id_categoria']; ?>"
                                <?php echo ((string)$datos['id_categoria'] === (string)$c['id_categoria']) ? 'selected' : ''; ?>>
                                <?php echo e($c['nombre']); ?><?php echo $c['estado'] == 0 ? ' (inactiva)' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Selecciona una categoría.</div>
                </div>

                <div class="col-md-6">
                    <label for="precio" class="form-label">Precio *</label>
                    <input type="number" class="form-control" id="precio" name="precio" min="0" step="0.01" required
                           value="<?php echo e($datos['precio']); ?>">
                    <div class="invalid-feedback">Ingresa un precio igual o mayor que 0.</div>
                </div>

                <div class="col-12">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                              maxlength="1000"><?php echo e($datos['descripcion']); ?></textarea>
                </div>

                <div class="col-md-6">
                    <label for="stock_actual" class="form-label">Stock actual *</label>
                    <input type="number" class="form-control" id="stock_actual" name="stock_actual"
                           min="0" max="1000000" step="1" required
                           value="<?php echo e($datos['stock_actual']); ?>">
                    <div class="invalid-feedback">Ingresa un número entero igual o mayor que 0.</div>
                    <?php if ($modo === 'editar'): ?>
                        <div class="form-text">Cambiar el stock aquí no deja registro. Para entradas y salidas usa <a href="movimientos.php?producto=<?php echo (int)$id_producto; ?>">Movimientos</a>.</div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="stock_minimo" class="form-label">Stock mínimo *</label>
                    <input type="number" class="form-control" id="stock_minimo" name="stock_minimo"
                           min="0" max="1000000" step="1" required
                           value="<?php echo e($datos['stock_minimo']); ?>">
                    <div class="invalid-feedback">Ingresa un número entero igual o mayor que 0.</div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <?php echo $modo === 'nuevo' ? 'Registrar producto' : 'Guardar cambios'; ?>
                </button>
                <a href="productos.php" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
