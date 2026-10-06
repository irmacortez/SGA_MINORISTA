<?php
require_once "controllers/VentaController.php";
require_once "controllers/ProductoController.php";
require_once "models/Producto.php";
require_once "models/Venta.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Registrar Nueva Venta
            <small>Facturación de Productos</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="index.php?action=inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">Crear Venta</li>
        </ol>
    </section>

    <section class="content">
        <!-- PROCESAR VENTA AL ENVIAR EL FORMULARIO POR POST DIRECTO -->
        <?php
            $crearVenta = new VentaController();
            $crearVenta->ctrCrearVenta();
        ?>

        <form id="formFactura" method="post" class="row">
            
            <!-- COLUMNA IZQUIERDA: SELECCIÓN DE PRODUCTOS -->
            <div class="col-md-5 col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">1. Seleccionar Producto</h3>
                    </div>
                    <div class="box-body">
                        
                        <!-- SELECCIÓN DE PRODUCTO -->
                        <div class="form-group">
                            <label for="selectProducto">Producto:</label>
                            <select class="form-control" id="selectProducto" style="width: 100%;">
                                <option value="">-- Seleccionar Producto --</option>
                                <?php
                                try {
                                    $productos = ProductoController::listarProductosController();

                                    if (!empty($productos) && is_array($productos)) {
                                        foreach ($productos as $key => $value) {
                                            $idProd = $value["id_producto"] ?? $value["id"] ?? 0;
                                            $desc   = htmlspecialchars($value["nombre_producto"] ?? $value["nombre"] ?? $value["descripcion"] ?? "Producto");
                                            $precio = $value["precio_venta"] ?? $value["precio_unitario"] ?? $value["precio"] ?? 0;
                                            
                                            // Compatibilidad amplia para recuperar stock_actual fresco desde la BD
                                            $stock  = $value["stock_actual"] ?? $value["stock"] ?? $value["cant_stock"] ?? 0;

                                            echo '<option value="'.$idProd.'" data-precio="'.$precio.'" data-stock="'.$stock.'" data-descripcion="'.$desc.'">'.$desc.' (Stock: '.$stock.') - $'.number_format($precio, 2, ',', '.').'</option>';
                                        }
                                    }
                                } catch (Exception $e) {
                                    echo '<option value="">Error al cargar productos</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- CANTIDAD -->
                        <div class="form-group">
                            <label for="cantProducto">Cantidad:</label>
                            <input type="number" class="form-control" id="cantProducto" min="1" value="1">
                        </div>

                        <button type="button" class="btn btn-primary btn-block" id="btnAgregarCarrito">
                            <i class="fa fa-cart-plus"></i> Agregar al Carrito
                        </button>

                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: DETALLE Y TOTALES DE LA FACTURA -->
            <div class="col-md-7 col-xs-12">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">2. Detalle de la Factura</h3>
                    </div>
                    <div class="box-body">
                        
                        <!-- TABLA DEL CARRITO -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tablaCarrito">
                                <thead>
                                    <tr>
                                        <th style="width: 10%;">Cant.</th>
                                        <th style="width: 45%;">Producto</th>
                                        <th style="width: 20%;">Precio U.</th>
                                        <th style="width: 20%;">Subtotal</th>
                                        <th style="width: 5%;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="filaVacia">
                                        <td colspan="5" class="text-center">No hay productos en la cesta</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <!-- MUESTRA DEL TOTAL -->
                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <h2 style="margin:0; font-weight: bold;">Total: $<span id="lblTotal">0.00</span></h2>
                                <input type="hidden" name="totalVenta" id="totalVenta" value="0">
                                <input type="hidden" name="listaProductos" id="listaProductos" value="[]">
                            </div>
                        </div>

                    </div>
                    
                    <div class="box-footer text-right">
                        <!-- 1. Vaciar Carrito -->
                        <button type="button" class="btn btn-default pull-left" id="btnVaciarCarrito">
                            <i class="fa fa-trash"></i> Vaciar Carrito
                        </button>
                        
                        <!-- 2. Previsualización limpia de ticket borrador -->
                        <button type="button" class="btn btn-info" id="btnImprimirBorrador">
                            <i class="fa fa-print"></i> Imprimir / Guardar PDF
                        </button>

                        <!-- 3. Confirmar y Registrar en BD -->
                        <button type="submit" class="btn btn-success btn-lg" id="btnConfirmarVenta">
                            <i class="fa fa-check-circle"></i> Confirmar y Facturar
                        </button>
                    </div>

                </div>
            </div>

        </form>
    </section>
</div>

<!-- JAVASCRIPT NATIVO -->
<script>
window.addEventListener('DOMContentLoaded', function() {

    var carrito = [];

    var btnAgregar  = document.getElementById("btnAgregarCarrito");
    var selectProd  = document.getElementById("selectProducto");
    var inputCant   = document.getElementById("cantProducto");
    var btnVaciar   = document.getElementById("btnVaciarCarrito");
    var btnBorrador = document.getElementById("btnImprimirBorrador");
    var form        = document.getElementById("formFactura");

    // 1. Agregar Producto
    btnAgregar.addEventListener("click", function(e) {
        e.preventDefault();

        var selectedOption = selectProd.options[selectProd.selectedIndex];
        var id = selectProd.value;

        if (!id || id === "") {
            alert("Por favor, selecciona un producto.");
            return;
        }

        var descripcion = selectedOption.getAttribute("data-descripcion") || selectedOption.text;
        var precio      = parseFloat(selectedOption.getAttribute("data-precio") || 0);
        var stock       = parseInt(selectedOption.getAttribute("data-stock") || 0);
        var cantidad    = parseInt(inputCant.value || 1);

        if (isNaN(cantidad) || cantidad <= 0) {
            alert("Ingresa una cantidad válida.");
            return;
        }

        if (cantidad > stock) {
            alert("La cantidad supera el stock disponible (" + stock + ").");
            return;
        }

        var encontrado = false;
        for (var i = 0; i < carrito.length; i++) {
            if (carrito[i].id == id) {
                if ((carrito[i].cantidad + cantidad) > stock) {
                    alert("Supera el stock disponible.");
                    return;
                }
                carrito[i].cantidad += cantidad;
                carrito[i].total = carrito[i].cantidad * carrito[i].precio;
                encontrado = true;
                break;
            }
        }

        if (!encontrado) {
            carrito.push({
                id: id,
                descripcion: descripcion,
                precio: precio,
                cantidad: cantidad,
                total: cantidad * precio
            });
        }

        renderizarTabla();
        inputCant.value = 1;
    });

    // 2. Vaciar Carrito
    btnVaciar.addEventListener("click", function() {
        carrito = [];
        renderizarTabla();
    });

    // 3. Imprimir Presupuesto/Borrador
    btnBorrador.addEventListener("click", function() {
        if (carrito.length === 0) {
            alert("Agrega al menos un producto para previsualizar.");
            return;
        }
        window.print();
    });

    // 4. Renderizar Tabla en HTML
    function renderizarTabla() {
        var tbody      = document.querySelector("#tablaCarrito tbody");
        var lblTotal   = document.getElementById("lblTotal");
        var inputTotal = document.getElementById("totalVenta");
        var inputLista = document.getElementById("listaProductos");

        tbody.innerHTML = "";
        var totalGeneral = 0;

        if (carrito.length === 0) {
            tbody.innerHTML = '<tr id="filaVacia"><td colspan="5" class="text-center">No hay productos en la cesta</td></tr>';
        } else {
            for (var i = 0; i < carrito.length; i++) {
                var item = carrito[i];
                totalGeneral += item.total;

                var tr = document.createElement("tr");
                tr.innerHTML = '<td>' + item.cantidad + '</td>' +
                    '<td>' + item.descripcion + '</td>' +
                    '<td>$' + item.precio.toFixed(2) + '</td>' +
                    '<td>$' + item.total.toFixed(2) + '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-danger btn-xs btnEliminar" data-id="' + item.id + '">' +
                            '<i class="fa fa-times"></i>' +
                        '</button>' +
                    '</td>';
                tbody.appendChild(tr);
            }
        }

        lblTotal.innerText = totalGeneral.toFixed(2);
        inputTotal.value = totalGeneral;
        inputLista.value = JSON.stringify(carrito);

        var btnsEliminar = document.querySelectorAll(".btnEliminar");
        btnsEliminar.forEach(function(btn) {
            btn.addEventListener("click", function() {
                var idEliminar = this.getAttribute("data-id");
                carrito = carrito.filter(function(p) { return p.id != idEliminar; });
                renderizarTabla();
            });
        });
    }

    // 5. Validar Submit
    form.addEventListener("submit", function(e) {
        if (carrito.length === 0) {
            e.preventDefault();
            alert("Debes agregar al menos un producto para facturar.");
            return false;
        }
    });

});
</script>