-- solo si la base ya existía sin restricciones CHECK
USE tecnostock;

ALTER TABLE productos
    ADD CONSTRAINT chk_precio_no_negativo       CHECK (precio >= 0),
    ADD CONSTRAINT chk_stock_actual_no_negativo CHECK (stock_actual >= 0),
    ADD CONSTRAINT chk_stock_minimo_no_negativo CHECK (stock_minimo >= 0);
