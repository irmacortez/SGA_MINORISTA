<?php
$controller = new ProductoController();
// Escucha si hay un envío de formulario POST
$controller->guardarProductoController();
// Carga los productos para la tabla
$productos = $controller->listarProductosController();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario - Kiosco SGA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

    <div class="container my-5">
        
        <!-- Cartel verde de éxito (solo se muestra DESPUÉS de guardar) -->
        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>¡Excelente!</strong> El artículo fue guardado correctamente en el inventario.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- ENCABEZADO CON TÍTULO Y BOTÓN DE ACCIÓN -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🏪 Gestión de Inventario</h2>
            <!-- Este botón abre la ventana modal para ingresar datos -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoArticulo">
                + Nuevo Artículo
            </button>
        </div>

        <!-- TABLA DE ARTÍCULOS -->
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th># ID</th>
                            <th>Artículo</th>
                            <th>Categoría</th>
                            <th>Proveedor</th>
                            <th>P. Venta</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
<?php if (!empty($productos)): ?>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><code>#<?= $p['id_producto'] ?></code></td>
                <td><strong><?= htmlspecialchars($p['nombre_producto']) ?></strong></td>
                <td>
                    <span class="badge bg-secondary">
                        Cat. #<?= $p['id_categoria'] ?>
                    </span>
                </td>
                <td>
                    <small class="text-muted">
                        Prov. #<?= $p['id_proveedor'] ?? 'Sin asignar' ?>
                    </small>
                </td>
                <td><strong>$<?= number_format($p['precio_venta'], 2, ',', '.') ?></strong></td>
                <td>
                    <?php if ($p['stock_actual'] <= ($p['stock_minimo'] ?? 0)): ?>
                        <span class="badge bg-danger"><?= $p['stock_actual'] ?> u. (¡Bajo!)</span>
                    <?php else: ?>
                        <span class="badge bg-success"><?= $p['stock_actual'] ?> u.</span>
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-warning">Editar</button>
                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" class="text-center text-muted">No hay artículos cargados en el kiosco.</td>
        </tr>
    <?php endif; ?>
</tbody>

                        
                </table>
            </div>
        </div>
    </div>

    <!-- VENTANA EMERGENTE (MODAL) QUE ABRE EL BOTÓN -->
    <div class="modal fade" id="modalNuevoArticulo" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="index.php?action=inventario" method="POST">
            <div class="modal-header">
              <h5 class="modal-title" id="modalLabel">Agregar Nuevo Artículo</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Nombre del Artículo</label>
                <input type="text" class="form-control" name="nuevoNombre" required placeholder="Ej: Alfajor Jorgito">
              </div>
              
              <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">ID Categoría</label>
                    <input type="text" class="form-control" name="nuevaCategoria" required placeholder="Ej: Golosinas">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">ID Proveedor</label>
                    <input type="text" class="form-control" name="nuevoProveedor" placeholder="Ej: Arcor">
                  </div>
              </div>

              <div class="row">
                  <div class="col-md-4 mb-3">
                    <label class="form-label">P. Venta ($)</label>
                    <input type="number" step="0.01" class="form-control" name="nuevoPrecioVenta" required placeholder="0.00">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Stock Actual</label>
                    <input type="number" class="form-control" name="nuevoStockActual" required placeholder="0">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Stock Mínimo</label>
                    <input type="number" class="form-control" name="nuevoStockMinimo" value="5">
                  </div>
              </div>
            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Guardar Artículo</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- JS de Bootstrap imprescindible para abrir el modal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>