# Plan de pruebas

Cada prueba se ejecuta en http://localhost/TecnoStock/ con el usuario demo. Completar las dos últimas columnas y guardar las capturas en `docs/evidencias/` con el nombre `prueba-NN.png`.

| N.º | Caso | Datos de entrada | Resultado esperado | Resultado obtenido | Evidencia |
|---|---|---|---|---|---|
| 1 | Login correcto | demo@tecnostock.cl / Demo1234! | Entra al dashboard y muestra el nombre |  |  |
| 2 | Login con contraseña incorrecta | demo@tecnostock.cl / abc | Mensaje genérico de credenciales incorrectas |  |  |
| 3 | Login con campos vacíos | Enviar sin escribir | Mensaje de campos obligatorios |  |  |
| 4 | Página protegida sin sesión | Abrir /dashboard.php sin iniciar sesión | Redirige al login |  |  |
| 5 | Cerrar sesión y presionar atrás | Cerrar sesión y volver atrás en el navegador | No muestra contenido; pide iniciar sesión |  |  |
| 6 | Producto con código duplicado | Código de un producto que ya existe | Error de código existente |  |  |
| 7 | Producto con precio negativo | Precio -100 | Rechazado con mensaje |  |  |
| 8 | Producto con stock negativo o decimal | Stock actual -5 y luego 2.5 | Rechazado con mensaje |  |  |
| 9 | Producto válido | Datos completos y código nuevo | Aparece en el listado con mensaje de éxito |  |  |
| 10 | Búsqueda por nombre, código y categoría | Un nombre, un código y una categoría existentes | Muestra solo coincidencias |  |  |
| 11 | Búsqueda sin resultados | zzzz | Mensaje de que no se encontraron productos |  |  |
| 12 | Intento de inyección SQL en el buscador | ' OR '1'='1 | Sin resultados ni errores |  |  |
| 13 | Editar precio, descripción y categoría | Cambiar cada campo | Cambios guardados y visibles en el listado |  |  |
| 14 | Desactivar un producto | Botón Desactivar y confirmar | Pasa a inactivo y no aparece en Movimientos |  |  |
| 15 | Reactivar un producto | Botón Reactivar | Vuelve a estado activo |  |  |
| 16 | Entrada de 10 unidades | Producto con stock conocido, tipo Entrada, cantidad 10 | Stock sube 10 y queda en el historial con usuario y fecha |  |  |
| 17 | Salida válida | Tipo Salida, cantidad menor al stock | Stock baja y queda en el historial |  |  |
| 18 | Salida mayor al stock | Cantidad 9999 | Rechazada y el stock no cambia |  |  |
| 19 | Cantidad cero o negativa | 0 y luego -3 | Rechazada |  |  |
| 20 | Salida que deja stock bajo el mínimo | Salida que cruce el stock mínimo | Aviso de stock bajo; aparece en el dashboard |  |  |
| 21 | Filtrar el historial | Por producto y por tipo | Muestra solo lo filtrado |  |  |
| 22 | Borrar en MySQL un producto con movimientos | DELETE FROM productos WHERE id_producto = ... | MySQL lo impide (ON DELETE RESTRICT) |  |  |
