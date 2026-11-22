
(function () {
    'use strict';

    // reglas HTML + clases de Bootstrap
    document.querySelectorAll('form.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (evento) {
            if (!form.checkValidity()) {
                evento.preventDefault();
                evento.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // confirmación antes de desactivar o reactivar
    document.querySelectorAll('form[data-confirmar]').forEach(function (form) {
        form.addEventListener('submit', function (evento) {
            if (!window.confirm(form.dataset.confirmar)) {
                evento.preventDefault();
            }
        });
    });

    var selProducto = document.getElementById('id_producto');
    var selTipo     = document.getElementById('tipo');
    var inCantidad  = document.getElementById('cantidad');

    // movimientos: muestra el stock y bloquea salidas mayores al stock
    if (selProducto && selTipo && inCantidad) {
        var info = document.getElementById('info-stock');
        var feedback = document.getElementById('feedback-cantidad');
        var mensajeBase = 'Ingresa un número entero mayor que cero.';

        var actualizar = function () {
            var opcion = selProducto.options[selProducto.selectedIndex];
            var stock = (opcion && opcion.dataset.stock !== undefined) ? parseInt(opcion.dataset.stock, 10) : null;
            var minimo = (opcion && opcion.dataset.minimo !== undefined) ? parseInt(opcion.dataset.minimo, 10) : null;

            if (stock === null || isNaN(stock)) {
                info.textContent = '';
            } else {
                info.textContent = 'Stock actual: ' + stock + ' (mínimo: ' + minimo + ')';
            }

            inCantidad.setCustomValidity('');
            feedback.textContent = mensajeBase;

            var cantidad = parseInt(inCantidad.value, 10);
            if (selTipo.value === 'SALIDA' && stock !== null && !isNaN(stock) && !isNaN(cantidad) && cantidad > stock) {
                var mensaje = 'Stock insuficiente: solo hay ' + stock + ' unidad(es) disponibles.';
                inCantidad.setCustomValidity(mensaje);
                feedback.textContent = mensaje;
            }
        };

        selProducto.addEventListener('change', actualizar);
        selTipo.addEventListener('change', actualizar);
        inCantidad.addEventListener('input', actualizar);
        actualizar();
    }
})();
