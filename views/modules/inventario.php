<div class="content-wrapper">

  <section class="content-header">
    <h1>Gestión de Inventario</h1>
  </section>

  <section class="content">

    <!-- 1. ALERTAS DE ESTADO TRAS ACCIONES (POST / GET) -->
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'success'): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> ¡Éxito!</h4>
                El producto fue registrado correctamente.
            </div>
        <?php elseif ($_GET['status'] === 'updated'): ?>
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-info"></i> ¡Actualizado!</h4>
                El producto fue modificado correctamente.
            </div>
        <?php elseif ($_GET['status'] === 'deleted'): ?>
            <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-trash"></i> ¡Eliminado!</h4>
                El producto fue eliminado del inventario.
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="box">
      
      <!-- BOTÓN PARA ABRIR MODAL DE AGREGAR -->
      <div class="box-header with-border">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProducto">
          <i class="fa fa-plus"></i> Agregar Producto
        </button>
      </div>

      <div class="box-body">

        <!-- 2. FILTROS RÁPIDOS (Categoría y Proveedor) -->
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-md-4">
                <label for="filtroCategoria">Filtrar por Categoría:</label>
                <select id="filtroCategoria" class="form-control" onchange="filtrarTabla()">
                    <option value="">Todas las Categorías</option>
                    <?php if(!empty($categorias)): foreach ($categorias as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['nombre_categoria']); ?>">
                            <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="filtroProveedor">Filtrar por Proveedor:</label>
                <select id="filtroProveedor" class="form-control" onchange="filtrarTabla()">
                    <option value="">Todos los Proveedores</option>
                    <?php if(!empty($proveedores)): foreach ($proveedores as $prov): ?>
                        <option value="<?php echo htmlspecialchars($prov['nombre_proveedor']); ?>">
                            <?php echo htmlspecialchars($prov['nombre_proveedor']); ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
        </div>

        <!-- 3. TABLA DE INVENTARIO CON RESALTADO DE STOCK -->
        <table class="table table-bordered table-striped dt-responsive" id="tablaInventario" width="100%">
          <thead>
            <tr>
              <th style="width:10px">#</th>
              <th>Producto</th>
              <th>Categoría</th>
              <th>Proveedor</th>
              <th>Precio Venta</th>
              <th>Stock Actual</th>
              <th>Stock Mínimo</th>
              <th>Estado Stock</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($productos)): foreach ($productos as $key => $value): ?>
                <?php 
                    // Evalúa si el stock actual está en nivel crítico
                    $esCritico = $value["stock_actual"] <= $value["stock_minimo"];
                ?>
                <tr class="<?php echo $esCritico ? 'danger' : ''; ?>">
                    <td><?php echo ($key + 1); ?></td>
                    <td><b><?php echo htmlspecialchars($value["nombre_producto"]); ?></b></td>
                    <td><?php echo htmlspecialchars($value["nombre_categoria"] ?? 'Sin asignar'); ?></td>
                    <td><?php echo htmlspecialchars($value["nombre_proveedor"] ?? 'Sin asignar'); ?></td>
                    <td>$<?php echo number_format($value["precio_venta"], 2); ?></td>
                    
                    <td>
                        <span class="badge <?php echo $esCritico ? 'bg-red' : 'bg-green'; ?>">
                            <?php echo $value["stock_actual"]; ?>
                        </span>
                    </td>
                    <td><?php echo $value["stock_minimo"]; ?></td>
                    
                    <!-- Estado según el stock -->
                    <td>
                        <?php if ($esCritico): ?>
                            <span class="label label-danger"><i class="fa fa-warning"></i> Reponer</span>
                        <?php else: ?>
                            <span class="label label-success"><i class="fa fa-check"></i> Normal</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <button class="btn btn-warning btn-sm btnEditarProducto" idProducto="<?php echo $value["id_producto"]; ?>" data-toggle="modal" data-target="#modalEditarProducto">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btnEliminarProducto" idProducto="<?php echo $value["id_producto"]; ?>">
                            <i class="fa fa-times"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>

      </div>
    </div>

  </section>
</div>

<!-- =============================================
MODAL AGREGAR PRODUCTO
============================================= -->
<div id="modalAgregarProducto" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" action="index.php?action=inventario">
        
        <div class="modal-header" style="background:#3c8dbc; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Producto</h4>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label>Nombre del Producto:</label>
            <input type="text" class="form-control" name="nuevoNombre" required>
          </div>

          <div class="form-group">
            <label>Categoría:</label>
            <select class="form-control" name="nuevoIdCategoria" required>
              <option value="">Seleccionar Categoría</option>
              <?php if(!empty($categorias)): foreach ($categorias as $cat): ?>
                <option value="<?php echo $cat['id_categoria']; ?>"><?php echo $cat['nombre_categoria']; ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Proveedor:</label>
            <select class="form-control" name="nuevoIdProveedor" required>
              <option value="">Seleccionar Proveedor</option>
              <?php if(!empty($proveedores)): foreach ($proveedores as $prov): ?>
                <option value="<?php echo $prov['id_proveedor']; ?>"><?php echo $prov['nombre_proveedor']; ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Precio Venta:</label>
            <input type="number" step="0.01" class="form-control" name="nuevoPrecioVenta" required>
          </div>

          <div class="form-group">
            <label>Stock Actual:</label>
            <input type="number" class="form-control" name="nuevoStockActual" required>
          </div>

          <div class="form-group">
            <label>Stock Mínimo:</label>
            <input type="number" class="form-control" name="nuevoStockMinimo" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar Producto</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- =============================================
MODAL EDITAR PRODUCTO
============================================= -->
<div id="modalEditarProducto" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" action="index.php?action=inventario">
        
        <div class="modal-header" style="background:#f39c12; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Producto</h4>
        </div>

        <div class="modal-body">
          <input type="hidden" id="editarIdProducto" name="editarIdProducto">

          <div class="form-group">
            <label>Nombre del Producto:</label>
            <input type="text" class="form-control" id="editarNombre" name="editarNombre" required>
          </div>

          <div class="form-group">
            <label>Categoría:</label>
            <select class="form-control" id="editarIdCategoria" name="editarIdCategoria" required>
              <option value="">Seleccionar Categoría</option>
              <?php if(!empty($categorias)): foreach ($categorias as $cat): ?>
                <option value="<?php echo $cat['id_categoria']; ?>"><?php echo $cat['nombre_categoria']; ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Proveedor:</label>
            <select class="form-control" id="editarIdProveedor" name="editarIdProveedor" required>
              <option value="">Seleccionar Proveedor</option>
              <?php if(!empty($proveedores)): foreach ($proveedores as $prov): ?>
                <option value="<?php echo $prov['id_proveedor']; ?>"><?php echo $prov['nombre_proveedor']; ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Precio Venta:</label>
            <input type="number" step="0.01" class="form-control" id="editarPrecioVenta" name="editarPrecioVenta" required>
          </div>

          <div class="form-group">
            <label>Stock Actual:</label>
            <input type="number" class="form-control" id="editarStockActual" name="editarStockActual" required>
          </div>

          <div class="form-group">
            <label>Stock Mínimo:</label>
            <input type="number" class="form-control" id="editarStockMinimo" name="editarStockMinimo" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning">Guardar Cambios</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- =============================================
SCRIPTS DEL MÓDULO INVENTARIO
============================================= -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// 1. FILTRADO RÁPIDO DE TABLA POR CATEGORÍA Y PROVEEDOR
function filtrarTabla() {
    var catSeleccionada = document.getElementById("filtroCategoria").value.toLowerCase();
    var provSeleccionado = document.getElementById("filtroProveedor").value.toLowerCase();
    var filas = document.querySelectorAll("#tablaInventario tbody tr");

    filas.forEach(function(fila) {
        var textoCategoria = fila.children[2].innerText.toLowerCase();
        var textoProveedor = fila.children[3].innerText.toLowerCase();

        var coincideCat = (catSeleccionada === "" || textoCategoria === catSeleccionada);
        var coincideProv = (provSeleccionado === "" || textoProveedor === provSeleccionado);

        if (coincideCat && coincideProv) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }
    });
}

// 2. RELLENAR MODAL DE EDICIÓN VÍA AJAX
$(document).on("click", ".btnEditarProducto", function(){

    var idProducto = $(this).attr("idProducto");

    var datos = new FormData();
    datos.append("idProducto", idProducto);

    $.ajax({
        url: "ajax/productos.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(respuesta){

            $("#editarIdProducto").val(respuesta["id_producto"]);
            $("#editarNombre").val(respuesta["nombre_producto"]);
            $("#editarIdCategoria").val(respuesta["id_categoria"]);
            $("#editarIdProveedor").val(respuesta["id_proveedor"]);
            $("#editarPrecioVenta").val(respuesta["precio_venta"]);
            $("#editarStockActual").val(respuesta["stock_actual"]);
            $("#editarStockMinimo").val(respuesta["stock_minimo"]);

        }
    });

});

// 3. CONFIRMACIÓN DE ELIMINACIÓN CON SWEETALERT2
$(document).on("click", ".btnEliminarProducto", function(e){
    e.preventDefault();
    var idProducto = $(this).attr("idProducto");

    Swal.fire({
        title: '¿Está seguro de eliminar este producto?',
        text: "Esta acción no se podrá deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmColor: '#d33',
        cancelColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = "index.php?action=inventario&idEliminar=" + idProducto;
        }
    });
});
</script>