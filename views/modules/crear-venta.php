<?php
/*=============================================
 OBTENER LISTA DE PRODUCTOS DE LA BASE DE DATOS
=============================================*/
$productos = array();

// Invocar el controlador según la clase declarada
if (class_exists('ProductoController') && method_exists('ProductoController', 'listarProductosController')) {
    $productos = ProductoController::listarProductosController();
} elseif (class_exists('ProductoControllers') && method_exists('ProductoControllers', 'listarProductosController')) {
    $productos = ProductoControllers::listarProductosController();
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Registrar Nueva Venta <small>Facturación de Productos</small></h1>
    </section>

    <section class="content">
        <form role="form" method="post" action="index.php?action=crear-venta" id="formVenta" autocomplete="off">
            <div class="row">
                
                <!-- COLUMNA IZQUIERDA: SELECCIÓN DE PRODUCTOS -->
                <div class="col-md-5">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">1. Seleccionar Producto</h3>
                        </div>
                        <div class="box-body">
                            
                            <!-- Desplegable de Productos -->
                            <div class="form-group">
                                <label for="selectProducto">Producto:</label>
                                <select class="form-control" id="selectProducto" name="selectProducto">
                                    <option value="">-- Seleccionar Producto --</option>
                                    <?php if (!empty($productos) && (is_array($productos) || is_object($productos))): ?>
                                        <?php foreach ($productos as $p): ?>
                                            <?php 
                                                // Mapeo flexible de nombres de columnas
                                                $id     = $p['id_producto']   ?? $p['id']           ?? '';
                                                $nombre = $p['nombre_producto'] ?? $p['nombre']       ?? $p['descripcion'] ?? 'Producto';
                                                $precio = floatval($p['precio_venta'] ?? $p['precio_unitario'] ?? $p['precio'] ?? 0);
                                                $stock  = intval($p['stock_actual']  ?? $p['stock']  ?? 0);
                                            ?>
                                            <option value="<?php echo $id; ?>" 
                                                    data-nombre="<?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-precio="<?php echo $precio; ?>"
                                                    data-stock="<?php echo $stock; ?>">
                                                <?php echo $nombre; ?> | Stock: <?php echo $stock; ?> | $<?php echo number_format($precio, 2); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Entrada de Cantidad -->
                            <div class="form-group">
                                <label for="inputCantidadAgregar">Cantidad:</label>
                                <input type="number" class="form-control" id="inputCantidadAgregar" name="inputCantidadAgregar" value="1" min="1">
                            </div>

                            <!-- Botón para Agregar al Carrito -->
                            <div class="form-group">
                                <button type="button" class="btn btn-success btn-block" id="btnAgregarProducto" onclick="insertarProductoAlCarrito()">
                                    <i class="fa fa-plus"></i> Agregar al Carrito
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- COLUMNA DERECHA: TABLA Y DETALLE DE LA FACTURA -->
                <div class="col-md-7">
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title">2. Detalle de la Factura</h3>
                        </div>
                        <div class="box-body">
                            
                            <!-- Número de Factura / Ticket -->
                            <div class="form-group">
                                <label for="codigoFactura">Número de Factura / Ticket:</label>
                                <input type="text" class="form-control" name="codigoFactura" id="codigoFactura" value="<?php echo class_exists('VentaControllers') && method_exists('VentaControllers', 'ctrObtenerUltimoCodigoFactura') ? VentaControllers::ctrObtenerUltimoCodigoFactura() : '1'; ?>" readonly>
                            </div>

                            <!-- Tabla del Carrito de Compras -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="tablaVentasDetalle">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th style="width: 80px;" class="text-center">Cant.</th>
                                            <th style="width: 100px;" class="text-right">P. Unit</th>
                                            <th style="width: 110px;" class="text-right">Subtotal</th>
                                            <th style="width: 40px;" class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyCarrito">
                                        <tr id="filaSinProductos">
                                            <td colspan="5" class="text-center text-muted">No hay productos agregados a la venta.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <hr>

                            <!-- Acciones de Vaciar Carrito y Mostrar Total -->
                            <div class="row">
                                <div class="col-xs-6">
                                    <button type="button" class="btn btn-default btn-sm" id="btnVaciarCarrito" onclick="vaciarCarritoCompleto()">
                                        <i class="fa fa-trash"></i> Vaciar Carrito
                                    </button>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <h3>Total: $<span id="lblTotal">0.00</span></h3>
                                </div>
                            </div>

                            <!-- Campos ocultos para procesar la venta en POST -->
                            <input type="hidden" name="totalVenta" id="inputTotalVenta" value="0">
                            <input type="hidden" name="productosCarrito" id="inputProductosCarrito" value="[]">

                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary pull-right btn-lg">
                                <i class="fa fa-check"></i> Confirmar y Facturar
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>

        <?php
        // Procesar el guardado de la venta si se envió el formulario
        if (class_exists('VentaControllers')) {
            $guardarVenta = new VentaControllers();
            if (method_exists($guardarVenta, 'guardarVentaController')) {
                $guardarVenta->guardarVentaController();
            }
        }
        ?>
    </section>
</div>

<!-- LÓGICA DE JAVASCRIPT DEL CARRITO -->
<script>
var carritoCompras = [];

function insertarProductoAlCarrito() {
    var select = document.getElementById("selectProducto");
    if (!select || !select.value) {
        alert("Por favor, seleccioná un producto de la lista desplegable.");
        return;
    }

    var idProducto = select.value;
    var option = select.options[select.selectedIndex];
    var nombre = option.getAttribute("data-nombre") || option.text.split('|')[0].trim();
    var precio = parseFloat(option.getAttribute("data-precio")) || 0;
    var stock = parseInt(option.getAttribute("data-stock"), 10) || 0;

    var inputCant = document.getElementById("inputCantidadAgregar");
    var cantidad = inputCant ? parseInt(inputCant.value, 10) : 1;
    if (isNaN(cantidad) || cantidad <= 0) cantidad = 1;

    if (stock <= 0) {
        alert("El producto seleccionado no tiene stock disponible.");
        return;
    }

    var existe = carritoCompras.find(function(item) { return item.id_producto == idProducto; });

    if (existe) {
        if ((existe.cantidad + cantidad) > stock) {
            alert("No se puede superar el stock disponible (" + stock + " unidades).");
            return;
        }
        existe.cantidad += cantidad;
        existe.subtotal = (existe.cantidad * existe.preciounitario).toFixed(2);
    } else {
        if (cantidad > stock) {
            alert("La cantidad elegida supera el stock disponible (" + stock + " unidades).");
            return;
        }
        carritoCompras.push({
            id_producto: idProducto,
            nombre_producto: nombre,
            cantidad: cantidad,
            preciounitario: precio,
            subtotal: (precio * cantidad).toFixed(2)
        });
    }

    select.value = "";
    if (inputCant) inputCant.value = "1";

    dibujarTablaCarrito();
}

function dibujarTablaCarrito() {
    var tbody = document.getElementById("tbodyCarrito");
    if (!tbody) return;

    tbody.innerHTML = "";

    if (carritoCompras.length === 0) {
        tbody.innerHTML = '<tr id="filaSinProductos"><td colspan="5" class="text-center text-muted">No hay productos agregados a la venta.</td></tr>';
        document.getElementById("lblTotal").innerText = "0.00";
        document.getElementById("inputTotalVenta").value = "0";
        document.getElementById("inputProductosCarrito").value = "[]";
        return;
    }

    var totalAcumulado = 0;
    carritoCompras.forEach(function(item, index) {
        var sub = parseFloat(item.subtotal);
        totalAcumulado += sub;

        var tr = document.createElement("tr");
        tr.innerHTML = '<td>' + item.nombre_producto + '</td>' +
                       '<td class="text-center">' + item.cantidad + '</td>' +
                       '<td class="text-right">$' + item.preciounitario.toFixed(2) + '</td>' +
                       '<td class="text-right">$' + sub.toFixed(2) + '</td>' +
                       '<td class="text-center"><button type="button" class="btn btn-danger btn-xs" onclick="quitarItemCarrito(' + index + ')"><i class="fa fa-times"></i></button></td>';
        tbody.appendChild(tr);
    });

    document.getElementById("lblTotal").innerText = totalAcumulado.toFixed(2);
    document.getElementById("inputTotalVenta").value = totalAcumulado.toFixed(2);
    document.getElementById("inputProductosCarrito").value = JSON.stringify(carritoCompras);
}

function quitarItemCarrito(index) {
    carritoCompras.splice(index, 1);
    dibujarTablaCarrito();
}

function vaciarCarritoCompleto() {
    carritoCompras = [];
    dibujarTablaCarrito();
}
</script>