<?php

session_start();

include 'includes/web_header.php';
include 'includes/web_menu.php';

$carrito = $_SESSION['cotizacion'] ?? [];

$productosCotizacion = [];

if(!empty($carrito))
{
    $ids = implode(',', array_keys($carrito));

    $productosCotizacion = $pdo->query(
        "SELECT *
         FROM productos
         WHERE id IN ($ids)"
    )->fetchAll(PDO::FETCH_ASSOC);
}

if(isset($_POST['enviar']) && !empty($carrito))
{
    $stmt = $pdo->prepare(
    "INSERT INTO cotizaciones
    (
        nombre,
        empresa,
        telefono,
        correo,
        comentarios
    )
    VALUES
    (
        ?,?,?,?,?
    )");

    $stmt->execute([
        $_POST['nombre'],
        $_POST['empresa'],
        $_POST['telefono'],
        $_POST['correo'],
        $_POST['comentarios']
    ]);

    $cotizacion_id = $pdo->lastInsertId();

    foreach($carrito as $producto_id => $cantidad)
    {
        $stmt = $pdo->prepare(
        "INSERT INTO cotizacion_detalle
        (
            cotizacion_id,
            producto_id,
            cantidad
        )
        VALUES
        (
            ?,?,?
        )");

        $stmt->execute([
            $cotizacion_id,
            $producto_id,
            $cantidad
        ]);
    }

    unset($_SESSION['cotizacion']);

    $enviado = true;
}

?>

<div class="container py-5">

    <div class="bg-success text-white p-4 rounded mb-4">

        <h1 class="mb-0">
            Solicitud de Cotización
        </h1>

        <small>
            Complete los datos para recibir una propuesta personalizada.
        </small>

    </div>

    <?php if(isset($enviado)): ?>

        <div class="alert alert-success">

            Su solicitud fue enviada correctamente.

        </div>

    <?php endif; ?>

    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card border-success">

                <div class="card-body text-center">

                    <h3><?= count($productosCotizacion) ?></h3>

                    <strong>
                        Productos Seleccionados
                    </strong>

                </div>

            </div>

        </div>

    </div>

    <div class="card shadow mb-4">

        <div class="card-header bg-success text-white">

            <h4 class="mb-0">
                Productos Seleccionados
            </h4>

        </div>

        <div class="card-body">

            <?php if(empty($productosCotizacion)): ?>

                <div class="alert alert-warning">

                    No hay productos agregados.

                </div>

            <?php else: ?>

                <table class="table table-hover align-middle">

                    <thead>

                    <tr>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th width="120">Acción</th>
                    </tr>

                    </thead>

                    <tbody>

                    <?php foreach($productosCotizacion as $producto): ?>

                        <tr>

                            <td width="120">

                                <?php if($producto['imagen']): ?>

                                    <img
                                    src="uploads/productos/<?= $producto['imagen'] ?>"
                                    class="img-fluid rounded">

                                <?php endif; ?>

                            </td>

                            <td>

                                <strong>
                                    <?= htmlspecialchars($producto['nombre']) ?>
                                </strong>

                                <br>

                                <?= htmlspecialchars($producto['descripcion_corta']) ?>

                            </td>

                            <td>

                                <?= $carrito[$producto['id']] ?>

                            </td>

                            <td>

                                <a
                                href="cotizacion_eliminar.php?id=<?= $producto['id'] ?>"
                                class="btn btn-danger btn-sm">

                                    Eliminar

                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            <?php endif; ?>

        </div>

    </div>

    <div class="card shadow">

        <div class="card-header bg-success text-white">

            <h4 class="mb-0">
                Datos del Cliente
            </h4>

        </div>

        <div class="card-body">

            <form method="post">

                <div class="row">

                    <div class="col-md-6">

                        <input
                        type="text"
                        name="nombre"
                        class="form-control mb-3"
                        placeholder="Nombre"
                        required>

                    </div>

                    <div class="col-md-6">

                        <input
                        type="text"
                        name="empresa"
                        class="form-control mb-3"
                        placeholder="Empresa">

                    </div>

                </div>

                <input
                type="text"
                name="telefono"
                class="form-control mb-3"
                placeholder="Teléfono">

                <input
                type="email"
                name="correo"
                class="form-control mb-3"
                placeholder="Correo">

                <textarea
                name="comentarios"
                class="form-control mb-3"
                rows="5"
                placeholder="Comentarios"></textarea>

                <button
                name="enviar"
                class="btn btn-success"
                <?= empty($productosCotizacion) ? 'disabled' : '' ?>>

                    Enviar Solicitud

                </button>

            </form>

        </div>

    </div>

</div>

<?php include 'includes/web_footer.php'; ?>
