<?php

// devuelve [errores, datos limpios]
function validar_producto(array $in, PDO $conexion, bool $es_nuevo, int $id_cat_actual = 0): array
{
    $errores = [];

    $d = [
        'codigo'       => dato($in, 'codigo'),
        'nombre'       => dato($in, 'nombre'),
        'descripcion'  => dato($in, 'descripcion'),
        'id_categoria' => dato($in, 'id_categoria'),
        'precio'       => str_replace(',', '.', dato($in, 'precio')),
        'stock_actual' => dato($in, 'stock_actual'),
        'stock_minimo' => dato($in, 'stock_minimo'),
    ];

    if ($es_nuevo) {
        if ($d['codigo'] === '') {
            $errores[] = "El código es obligatorio.";
        } elseif (mb_strlen($d['codigo']) > 50) {
            $errores[] = "El código no puede superar los 50 caracteres.";
        } elseif (!preg_match('/^[A-Za-z0-9_-]+$/', $d['codigo'])) {
            $errores[] = "El código solo puede tener letras, números, guion (-) y guion bajo (_), sin espacios.";
        } else {
            // código único
            $stmt = $conexion->prepare("SELECT COUNT(*) FROM productos WHERE codigo = :codigo");
            $stmt->execute([':codigo' => $d['codigo']]);
            if ($stmt->fetchColumn() > 0) {
                $errores[] = "Ya existe un producto con el código «" . $d['codigo'] . "».";
            }
        }
    }

    if ($d['nombre'] === '') {
        $errores[] = "El nombre es obligatorio.";
    } elseif (mb_strlen($d['nombre']) > 150) {
        $errores[] = "El nombre no puede superar los 150 caracteres.";
    }

    if (mb_strlen($d['descripcion']) > 1000) {
        $errores[] = "La descripción no puede superar los 1000 caracteres.";
    }

    if (!preg_match('/^\d+$/', $d['id_categoria']) || (int)$d['id_categoria'] <= 0) {
        $errores[] = "Debes seleccionar una categoría.";
    } else {
        $stmt = $conexion->prepare(
            "SELECT COUNT(*) FROM categorias
             WHERE id_categoria = :id AND (estado = 1 OR id_categoria = :actual)"
        );
        $stmt->execute([':id' => (int)$d['id_categoria'], ':actual' => $id_cat_actual]);
        if ($stmt->fetchColumn() == 0) {
            $errores[] = "La categoría seleccionada no es válida.";
        }
    }

    if ($d['precio'] === '') {
        $errores[] = "El precio es obligatorio.";
    } elseif (!preg_match('/^\d+(\.\d{1,2})?$/', $d['precio'])) {
        $errores[] = "El precio debe ser un número positivo (máximo 2 decimales), sin signo negativo.";
    } elseif ((float)$d['precio'] > 99999999.99) {
        $errores[] = "El precio es demasiado alto.";
    }

    foreach (['stock_actual' => 'El stock actual', 'stock_minimo' => 'El stock mínimo'] as $campo => $etiqueta) {
        if ($d[$campo] === '') {
            $errores[] = "$etiqueta es obligatorio.";
        } elseif (!preg_match('/^\d{1,7}$/', $d[$campo]) || (int)$d[$campo] > 1000000) {
            $errores[] = "$etiqueta debe ser un número entero entre 0 y 1.000.000 (no se permiten negativos ni decimales).";
        }
    }

    return [$errores, $d];
}
