<?php
require_once "includes/sesion.php";
require_once "config/conexion.php";

if (isset($_SESSION['id_usuario'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
$correo = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = dato($_POST, 'correo');
    $clave  = dato($_POST, 'clave', false);

    if ($correo === '' || $clave === '') {
        $error = "Debes ingresar correo y contraseña.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo no tiene un formato válido.";
    } else {
        // consulta preparada
        $stmt = $conexion->prepare(
            "SELECT id_usuario, nombre, clave, estado
             FROM usuarios
             WHERE correo = :correo
             LIMIT 1"
        );
        $stmt->execute([':correo' => $correo]);
        $usuario = $stmt->fetch();

        if ($usuario && $usuario['estado'] == 1 && password_verify($clave, $usuario['clave'])) {
            // nuevo id de sesión al ingresar
            session_regenerate_id(true);

            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre']     = $usuario['nombre'];

            header("Location: dashboard.php");
            exit;
        } else {
            // mensaje genérico, no dice cuál dato falló
            $error = "Correo o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="h3 text-center mb-1">TecnoStock</h1>
                        <p class="text-center text-muted mb-4">Sistema de inventario</p>

                        <?php if ($error !== ""): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="login.php" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo</label>
                                <input type="email" class="form-control" id="correo" name="correo" maxlength="150"
                                       value="<?php echo htmlspecialchars($correo); ?>" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="clave" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="clave" name="clave" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="assets/js/validaciones.js"></script>
</body>
</html>
