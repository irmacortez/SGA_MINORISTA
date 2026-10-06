<?php
require_once "controllers/VentaController.php";
require_once "controllers/NotaCreditoController.php";
require_once "models/Venta.php";
require_once "models/NotaCredito.php";

// Instanciar el controlador para procesar la emisión si hay un envío POST
$crearNC = new NotaCreditoController();$crearNC->ctrCrearNotaCredito();

// Consultar ventas activas directamente desde la base de datos
$db = Conexion::conectar();$sqlVentasActivas = "SELECT id_venta, numero_comprobante, total, fecha_venta 
                     FROM ventas 
                     WHERE estado = '1' 
                     ORDER BY id_venta DESC";
$stmtVentas =$db->prepare($sqlVentasActivas);$stmtVentas->execute();
$ventasActivas =$stmtVentas->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Emitir Nota de Crédito</h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <form role="form" method="post" id="formNC">
                <div class="box-body">

                    <!-- Selector de Facturas -->
                    <div class="form-group">
                        <label for="idVentaAnular">Seleccionar Factura a Anular / Devolver:</label>
                        <select class="form-control select2" id="idVentaAnular" name="idVentaAnular" required style="width: 100%;">
                            <option value="">-- Seleccionar Comprobante --</option>
                            <?php foreach ($ventasActivas as$venta): ?>
                                <option value="<?php echo $venta['id_venta']; ?>">
                                    Comprobante: <?php echo $venta['numero_comprobante']; ?> \vert{} Fecha: <?php echo$venta['fecha_venta']; ?> | Total: $<?php echo number_format($venta['total'], 2); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Motivo -->
                    <div class="form-group">
                        <label for="motivoNC">Motivo de la Nota de Crédito:</label>
                        <input type="text" class="form-control" name="motivoNC" placeholder="Ej: Anulación completa por error de carga / Devolución" required>
                    </div>

                    <!-- Detalle de productos de la venta -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tablaProductosNC">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad Vendida</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="bodyProductosNC">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Seleccioná una factura para cargar los ítems.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Total y campos ocultos -->
                    <div class="row">
                        <div class="col-xs-6 pull-right text-right">
                            <h3>Total Nota de Crédito: $ <span id="lblTotalNC">0.00</span></h3>
                            <input type="hidden" name="totalNC" id="totalNC" value="0">
                            <input type="hidden" name="listaProductosNC" id="listaProductosNC">
                        </div>
                    </div>

                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-danger pull-right" id="btnEmitirNC" disabled>Emitir Nota de Crédito</button>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const selectVenta = document.getElementById("idVentaAnular");
    const bodyTabla   = document.getElementById("bodyProductosNC");
    const lblTotal    = document.getElementById("lblTotalNC");
    const inputTotal  = document.getElementById("totalNC");
    const inputLista  = document.getElementById("listaProductosNC");
    const btnEmitir   = document.getElementById("btnEmitirNC");

    selectVenta.addEventListener("change", function () {
        const idVenta = this.value;

        if (!idVenta) {
            bodyTabla.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Seleccioná una factura para cargar los ítems.</td></tr>';
            lblTotal.textContent = "0.00";
            inputTotal.value = "0";
            inputLista.value = "";
            btnEmitir.disabled = true;
            return;
        }

        const formData = new FormData();
        formData.append("idVenta", idVenta);

        fetch("ajax/ventas.ajax.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.productos && data.productos.length > 0) {
                let html = "";
                let total = 0;
                let listaProductos = [];

                data.productos.forEach(prod => {
                    const subtotal = parseFloat(prod.cantidad) * parseFloat(prod.precio_unitario);
                    total += subtotal;

                    html += `
                        <tr>
                            <td>${prod.descripcion}</td>
                            <td>${prod.cantidad}</td>
                            <td>$ ${parseFloat(prod.precio_unitario).toFixed(2)}</td>
                            <td>$ ${subtotal.toFixed(2)}</td>
                        </tr>
                    `;

                    listaProductos.push({
                        id_producto: prod.id_producto,
                        cantidad: prod.cantidad,
                        precio_unitario: prod.precio_unitario
                    });
                });

                bodyTabla.innerHTML = html;
                lblTotal.textContent = total.toFixed(2);
                inputTotal.value = total.toFixed(2);
                inputLista.value = JSON.stringify(listaProductos);
                btnEmitir.disabled = false;
            } else {
                bodyTabla.innerHTML = '<tr><td colspan="4" class="text-center text-warning">No se encontraron productos registrados en esta factura.</td></tr>';
                btnEmitir.disabled = true;
            }
        })
        .catch(error => {
            console.error("Error al obtener el detalle de la venta:", error);
            alert("Ocurrió un error al cargar la factura seleccionada.");
        });
    });
});
</script>