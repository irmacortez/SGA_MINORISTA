<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario - Kiosco SGA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🏪 Gestión de Inventario</h2>
            <button class="btn btn-primary">+ Nuevo Artículo</button>
        </div>

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
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($p['categoria']) ?></span></td>
                                    <td><small class="text-muted"><?= htmlspecialchars($p['proveedor'] ?? 'Sin asignar') ?></small></td>
                                    <td><strong>$<?= number_format($p['precio_venta'], 2, ',', '.') ?></strong></td>
                                    <td>
                                        <?php if ($p['stock_actual'] <= $p['stock_minimo']): ?>
                                            <span class="badge bg-danger"><?= $p['stock_actual'] ?> u. (¡Bajo!)</span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?= $p['stock_actual'] ?> u.</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning">Editar</button>
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
</body>
</html>