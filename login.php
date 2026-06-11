<?php

session_start();

require_once 'config/database.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM usuarios
            WHERE usuario = :usuario
            AND activo = 1
            LIMIT 1";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':usuario',$usuario);

    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user){

        if(password_verify($password,$user['password'])){

            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol'];

            header("Location: admin/dashboard.php");
            exit;

        }else{
            $error = "Contraseña incorrecta";
        }

    }else{
        $error = "Usuario no encontrado";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Acceso al Sistema</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container">

<div class="row justify-content-center mt-5">

<div class="col-md-4">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h4 class="mb-0">Iniciar Sesión</h4>

</div>

<div class="card-body">

<?php if($error): ?>

<div class="alert alert-danger">
    <?= htmlspecialchars($error) ?>
</div>

<?php endif; ?>

<form method="POST">

<div class="mb-3">

<label>Usuario</label>

<input
type="text"
name="usuario"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Contraseña</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<button
type="submit"
class="btn btn-success w-100">

Entrar

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>