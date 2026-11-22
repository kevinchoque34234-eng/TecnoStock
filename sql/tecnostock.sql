-- TecnoStock - base de datos (MySQL 8)

CREATE DATABASE IF NOT EXISTS tecnostock
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tecnostock;

CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    correo     VARCHAR(150) NOT NULL UNIQUE,
    clave      VARCHAR(255) NOT NULL,
    estado     TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(100) NOT NULL UNIQUE,
    descripcion  TEXT,
    estado       TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS productos (
    id_producto  INT AUTO_INCREMENT PRIMARY KEY,
    codigo       VARCHAR(50)  NOT NULL UNIQUE,
    nombre       VARCHAR(150) NOT NULL,
    descripcion  TEXT,
    precio       DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock_actual INT NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 0,
    estado       TINYINT(1) NOT NULL DEFAULT 1,
    id_categoria INT NOT NULL,
    CONSTRAINT chk_precio_no_negativo       CHECK (precio >= 0),
    CONSTRAINT chk_stock_actual_no_negativo CHECK (stock_actual >= 0),
    CONSTRAINT chk_stock_minimo_no_negativo CHECK (stock_minimo >= 0),
    CONSTRAINT fk_productos_categoria FOREIGN KEY (id_categoria)
        REFERENCES categorias (id_categoria)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS movimientos (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    fecha_hora    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tipo          ENUM('ENTRADA','SALIDA') NOT NULL,
    cantidad      INT NOT NULL,
    observacion   TEXT,
    id_producto   INT NOT NULL,
    id_usuario    INT NOT NULL,
    CONSTRAINT chk_cantidad_positiva CHECK (cantidad > 0),
    CONSTRAINT fk_mov_producto FOREIGN KEY (id_producto)
        REFERENCES productos (id_producto)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_mov_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- usuario demo: demo@tecnostock.cl / Demo1234!
INSERT IGNORE INTO usuarios (nombre, correo, clave, estado) VALUES
('Usuario Demo', 'demo@tecnostock.cl', '$2y$12$753oEgSGtmJGIq3jgrbpyejJ6evd2dvlXGHkbRdyBhH.i1YOfQEv.', 1);

INSERT IGNORE INTO categorias (nombre, descripcion) VALUES
    ('Periféricos',    'Mouse, teclados y otros accesorios de entrada'),
    ('Audio',          'Audífonos y parlantes'),
    ('Cables',         'Cables y adaptadores'),
    ('Almacenamiento', 'Memorias y discos');

INSERT IGNORE INTO productos (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, id_categoria) VALUES
    ('MOU-001', 'Mouse USB',          'Mouse óptico con cable USB',          5990, 25, 10, (SELECT id_categoria FROM categorias WHERE nombre = 'Periféricos')),
    ('TEC-001', 'Teclado USB',        'Teclado estándar en español',        12990, 15,  5, (SELECT id_categoria FROM categorias WHERE nombre = 'Periféricos')),
    ('AUD-001', 'Audífonos',          'Audífonos con micrófono',            14990,  8, 10, (SELECT id_categoria FROM categorias WHERE nombre = 'Audio')),
    ('CAB-001', 'Cable HDMI',         'Cable HDMI 1,8 metros',               4990, 30, 10, (SELECT id_categoria FROM categorias WHERE nombre = 'Cables')),
    ('MEM-001', 'Memoria USB 32GB',   'Pendrive USB 3.0 de 32 GB',           6990, 40, 15, (SELECT id_categoria FROM categorias WHERE nombre = 'Almacenamiento'));

-- opcional: la app hace lo mismo con una transacción PDO
DROP PROCEDURE IF EXISTS registrar_movimiento;

DELIMITER $$
CREATE PROCEDURE registrar_movimiento(
    IN p_id_producto INT,
    IN p_id_usuario  INT,
    IN p_tipo        VARCHAR(10),
    IN p_cantidad    INT,
    IN p_observacion TEXT
)
BEGIN
    DECLARE v_stock  INT;
    DECLARE v_estado TINYINT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF p_cantidad IS NULL OR p_cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La cantidad debe ser mayor que cero.';
    END IF;

    IF p_tipo NOT IN ('ENTRADA', 'SALIDA') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El tipo debe ser ENTRADA o SALIDA.';
    END IF;

    START TRANSACTION;

    SELECT stock_actual, estado INTO v_stock, v_estado
    FROM productos WHERE id_producto = p_id_producto FOR UPDATE;

    IF v_stock IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El producto no existe.';
    END IF;

    IF v_estado <> 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El producto está inactivo.';
    END IF;

    IF p_tipo = 'SALIDA' AND p_cantidad > v_stock THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock insuficiente para la salida.';
    END IF;

    IF p_tipo = 'ENTRADA' THEN
        UPDATE productos SET stock_actual = stock_actual + p_cantidad WHERE id_producto = p_id_producto;
    ELSE
        UPDATE productos SET stock_actual = stock_actual - p_cantidad WHERE id_producto = p_id_producto;
    END IF;

    INSERT INTO movimientos (tipo, cantidad, observacion, id_producto, id_usuario)
    VALUES (p_tipo, p_cantidad, p_observacion, p_id_producto, p_id_usuario);

    COMMIT;
END$$
DELIMITER ;
