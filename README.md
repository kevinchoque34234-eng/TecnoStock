# TecnoStock - Sistema de inventario

Aplicación web para controlar el inventario de TecnoStock, un emprendimiento de accesorios tecnológicos que hoy usa planillas. Proyecto individual del Taller de Sistemas de Información (Caso 1).

Funciones: inicio de sesión, dashboard con alertas de stock bajo, CRUD de productos con búsqueda, categorías, y entradas y salidas de inventario con historial.

Tecnologías: HTML5, Bootstrap 5 (CDN), JavaScript, PHP 8 con PDO, MySQL 8, XAMPP.

## Instalación

1. Instalar XAMPP e iniciar Apache y MySQL.
2. Copiar la carpeta `TecnoStock` en `C:\xampp\htdocs\`.
3. En MySQL Workbench o phpMyAdmin, ejecutar completo `sql/tecnostock.sql`. Crea la base, las tablas, 5 productos de ejemplo y un usuario demo.
4. Opcional: ejecutar `sql/inserts.sql` para cargar 100 productos de prueba.
5. Revisar `config/conexion.php` (por defecto `root` sin contraseña; si MySQL usa otro puerto, agregar `;port=XXXX` al DSN).
6. Abrir http://localhost/TecnoStock/

## Usuario de prueba

| Correo | Contraseña |
|---|---|
| demo@tecnostock.cl | Demo1234! |

Es solo para evaluar el proyecto. Para crear otro usuario hay que generar el hash de la contraseña:

```
C:\xampp\php\php.exe -r "echo password_hash('contraseña', PASSWORD_DEFAULT);"
```

y guardarlo en la columna `clave` de la tabla `usuarios`.

## Estructura

```
index.php, login.php, logout.php, dashboard.php
productos.php, producto_nuevo.php, producto_editar.php, producto_estado.php
categorias.php, movimientos.php
config/conexion.php
includes/       sesión, header, footer, validación y formulario de producto
assets/js/      validaciones del lado del cliente
sql/            tecnostock.sql, inserts.sql, mejoras_integridad.sql
docs/           análisis de requerimientos y plan de pruebas
```

`sql/mejoras_integridad.sql` solo se usa si la base ya existía sin restricciones CHECK.

## Reglas de negocio

- El código de producto es único.
- Precio, stocks y cantidades no pueden ser negativos; la cantidad de un movimiento debe ser mayor que cero.
- Una salida no puede dejar el stock bajo cero.
- Los productos se desactivan, no se eliminan.
- Cada movimiento guarda fecha, tipo, cantidad, producto y usuario.
- El stock se actualiza en la misma transacción que registra el movimiento.

Más detalle en `docs/ANALISIS.md` y `docs/PRUEBAS.md`.
