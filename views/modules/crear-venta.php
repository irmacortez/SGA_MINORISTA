<?php
// Cargar productos para el panel de selección
$productos = ProductoController::listarProductosController();

// Procesar el guardado si se presiona "Confirmar Venta"
VentaController::guardarVentaController();
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Nueva Venta</h1>
    </section>

    <section class="content">
        <div class="row">
            <!-- COLUMNA IZQUIERDA: SELECCIÓN DE PRODUCTOS -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Seleccionar Productos</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Stock</th>
                                    <th>Precio</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $prod): ?>
                                    <tr>
                                        <td><?php echo $prod["nombre_producto"]; ?></td>
                                        <td>
                                            <?php if ($prod["stock_actual"] > 0): ?>
                                                <span class="badge bg-green"><?php echo $prod["stock_actual"]; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-red">Sin Stock</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>$<?php echo number_format($prod["precio_venta"], 2); ?></td>
                                        <td>
                                            <?php if ($prod["stock_actual"] > 0): ?>
                                                <button type="button" class="btn btn-primary btn-sm btnAgregarProducto" 
                                                        data-id="<?php echo $prod["id_producto"]; ?>"
                                                        data-nombre="<?php echo htmlspecialchars($prod["nombre_producto"]); ?>"
                                                        data-stock="<?php echo $prod["stock_actual"]; ?>"
                                                        data-precio="<?php echo $prod["precio_venta"]; ?>">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: CARRITO Y CONFIRMACIÓN -->
            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Detalle de la Venta</h3>
                    </div>
                    <form role="form" method="post">
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th style="width: 80px;">Cantidad</th>
                                        <th>P. Unitario</th>
                                        <th>Subtotal</th>
                                        <th style="width: 30px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyCarrito">
                                    <!-- Se llena dinámicamente -->
                                </tbody>
                            </table>

                            <div class="row text-right" style="padding-right: 15px; margin-top: 15px;">
                                <h3>Total: $<span id="totalVentaTexto">0.00</span></h3>
                            </div>

                            <!-- Inputs ocultos enviadores a PHP -->
                            <input type="hidden" name="totalVenta" id="totalVentaInput" value="0">
                            <input type="hidden" name="productosCarrito" id="productosCarritoInput" value="[]">
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-success pull-right">
                                <i class="fa fa-check"></i> Confirmar Venta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- LÓGICA DE CARRITO Y ACCIONES (ELIMINAR / MODIFICAR) -->
<script>
let carrito = [];

// 1. AGREGAR AL CARRITO (Botón +)
$(document).on("click", ".btnAgregarProducto", function() {
    let id = $(this).data("id");
    let nombre = $(this).data("nombre");
    let stock = parseInt($(this).data("stock"));
    let precio = parseFloat($(this).data("precio"));

    let existe = carrito.find(p => p.id_producto == id);

    if (existe) {
        if (existe.cantidad < stock) {
            existe.cantidad++;
            existe.subtotal = (existe.cantidad * existe.preciounitario).toFixed(2);
        } else {
            Swal.fire({ icon: "warning", title: "Stock insuficiente", text: "Llegaste al límite del stock disponible." });
            return;
        }
    } else {
        carrito.push({
            id_producto: id,
            nombre_producto: nombre,
            cantidad: 1,
            preciounitario: precio,
            subtotal: precio.toFixed(2),
            stockMax: stock
        });
    }

    actualizarCarritoUI();
});

// 2. ELIMINAR UN PRODUCTO DEL CARRITO (Botón X)
$(document).on("click", ".btnEliminarProducto", function() {
    let id = $(this).data("id");
    carrito = carrito.filter(p => p.id_producto != id);
    actualizarCarritoUI();
});

// 3. CAMBIAR CANTIDAD DIRECTAMENTE EN EL INPUT
$(document).on("change", ".inputCantidad", function() {
    let id = $(this).data("id");
    let nuevaCant = parseInt($(this).val());
    let prod = carrito.find(p => p.id_producto == id);

    if (prod) {
        if (nuevaCant <= 0 || isNaN(nuevaCant)) nuevaCant = 1;
        if (nuevaCant > prod.stockMax) {
            nuevaCant = prod.stockMax;
            Swal.fire({ icon: "warning", title: "Stock máximo", text: "No podés vender más del stock actual." });
        }
        prod.cantidad = nuevaCant;
        prod.subtotal = (nuevaCant * prod.preciounitario).toFixed(2);
    }

    actualizarCarritoUI();
});

// RENDERIZAR TABLA Y ACTUALIZAR TOTALES Y JSON
function actualizarCarritoUI() {
    $("#tbodyCarrito").empty();
    let total = 0;

    carrito.forEach(p => {
        total += parseFloat(p.subtotal);
        $("#tbodyCarrito").append(`
            <tr>
                <td>${p.nombre_producto}</td>
                <td>
                    <input type="number" class="form-control input-sm inputCantidad" 
                           data-id="${p.id_producto}" value="${p.cantidad}" min="1" max="${p.stockMax}" style="width: 60px;">
                </td>
                <td>$${p.preciounitario.toFixed(2)}</td>
                <td>$${p.subtotal}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-xs btnEliminarProducto" data-id="${p.id_producto}">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
        `);
    });

    $("#totalVentaTexto").text(total.toFixed(2));
    $("#totalVentaInput").val(total.toFixed(2));
    $("#productosCarritoInput").val(JSON.stringify(carrito));
}
</script>