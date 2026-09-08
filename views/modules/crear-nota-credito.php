<?php
require_once "controllers/NotaCreditoController.php";

$emitirNC = new NotaCreditoController();
$emitirNC->emitirNotaCreditoController();
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <h4 class="mb-4">Emitir Nota de Crédito / Débito</h4>

                <!-- 1. BÚSQUEDA DE FACTURA ORIGEN -->
                <form method="GET" action="index.php" class="row g-3 mb-4">
                    <input type="hidden" name="action" value="crear-nota-credito">
                    <div class="col-md-4">
                        <label for="buscar_factura" class="form-label">Número de Factura Origen</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="buscar_factura" name="factura" placeholder="Ej: FC-0001-00000123" required>
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </div>
                    </div>
                </form>

                <hr class="my-4">

                <!-- 2. FORMULARIO DE EMISIÓN DE NC -->
                <form method="POST" id="formNotaCredito">

                    <!-- ID Oculto de la Factura Encontrada -->
                    <input type="hidden" name="id_factura_origen" value="1"> <!-- Reemplazar con ID dinámico dinámicamente -->

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">N° Nota de Crédito</label>
                            <input type="text" class="form-control" name="numero_nc" value="NC-0001-00000001" readonly>
                        </div>

                        <div class="col-md-4">
                            <label for="tipo_ajuste" class="form-label">Tipo de Ajuste</label>
                            <select class="form-select" id="tipo_ajuste" name="tipo_ajuste" onchange="cambiarTipoAjuste(this.value)">
                                <option value="devolucion_total">Devolución Total (Anulación con reingreso de stock)</option>
                                <option value="devolucion_parcial">Devolución Parcial (Ajuste de unidades físicas)</option>
                                <option value="diferencia_precio">Diferencia de Precio (Ajuste solo financiero)</option>
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label for="motivo" class="form-label">Motivo de Emisión</label>
                            <input type="text" class="form-control" id="motivo" name="motivo" placeholder="Ej: Producto defectuoso / Error de facturación" required>
                        </div>
                    </div>

                    <!-- TABLA DE PRODUCTOS A AJUSTAR -->
                    <div class="table-responsive mb-4" id="seccionProductos">
                        <table class="table align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-white">
                                    <th>Producto</th>
                                    <th>Cant. Original</th>
                                    <th>Cant. a Devolver</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal Devolución</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Ejemplo de ítem recuperado de la factura -->
                                <tr>
                                    <td>
                                        Teclado Mecánico RGB
                                        <input type="hidden" name="id_producto[]" value="10">
                                    </td>
                                    <td>5</td>
                                    <td style="width: 150px;">
                                        <input type="number" class="form-control input-cant" name="cantidad_devolver[]" value="5" min="0" max="5" onchange="calcularTotales()">
                                    </td>
                                    <td>$ <span class="precio-unitario">15000.00</span></td>
                                    <td>$ <span class="subtotal-item">75000.00</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- TOTALES -->
                    <div class="row justify-content-end mb-4">
                        <div class="col-md-4">
                            <div class="bg-dark p-3 rounded text-end">
                                <h5>Total Nota de Crédito:</h5>
                                <h3 class="text-primary">$ <span id="montoTotalNC">75000.00</span></h3>
                                <input type="hidden" name="total_nc" id="inputTotalNC" value="75000.00">
                                <input type="hidden" name="listaProductosNC" id="listaProductosNC" value="[]">
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="index.php?action=ventas" class="btn btn-outline-light me-2">Cancelar</a>
                        <button type="submit" class="btn btn-success"><i class="fa fa-check me-2"></i>Emitir Comprobante</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT DE COMPORTAMIENTO INTERACTIVO -->
<script>
function cambiarTipoAjuste(tipo) {
    const inputsCant = document.querySelectorAll('.input-cant');
    
    if (tipo === 'devolucion_total') {
        inputsCant.forEach(input => {
            input.value = input.getAttribute('max');
            input.readOnly = true;
        });
    } else if (tipo === 'devolucion_parcial') {
        inputsCant.forEach(input => {
            input.readOnly = false;
        });
    } else if (tipo === 'diferencia_precio') {
        inputsCant.forEach(input => {
            input.value = 0;
            input.readOnly = true;
        });
    }
    calcularTotales();
}

function calcularTotales() {
    let filas = document.querySelectorAll('tbody tr');
    let totalGeneral = 0;
    let productosDevolver = [];

    filas.forEach(fila => {
        let cantInput = fila.querySelector('.input-cant');
        let cant = parseFloat(cantInput.value) || 0;
        let precio = parseFloat(fila.querySelector('.precio-unitario').textContent) || 0;
        let idProd = fila.querySelector('input[name="id_producto[]"]').value;

        let subtotal = cant * precio;
        fila.querySelector('.subtotal-item').textContent = subtotal.toFixed(2);
        totalGeneral += subtotal;

        if (cant > 0) {
            productosDevolver.push({
                id_producto: idProd,
                cantidad: cant
            });
        }
    });

    // Si es diferencia de precio pura, permitir edición manual del total
    let tipo = document.getElementById('tipo_ajuste').value;
    if (tipo !== 'diferencia_precio') {
        document.getElementById('montoTotalNC').textContent = totalGeneral.toFixed(2);
        document.getElementById('inputTotalNC').value = totalGeneral.toFixed(2);
    }

    document.getElementById('listaProductosNC').value = JSON.stringify(productosDevolver);
}

// Inicialización al cargar la vista
document.addEventListener('DOMContentLoaded', () => {
    cambiarTipoAjuste(document.getElementById('tipo_ajuste').value);
});
</script>