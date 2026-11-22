<?php
require_once "includes/sesion.php";

if (isset($_SESSION['id_usuario'])) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit;
