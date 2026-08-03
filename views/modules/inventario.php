<?php
// Ejecución de controladores para procesar las peticiones POST/GET
ProductoController::guardarProductoController();
ProductoController::actualizarProductoController();
ProductoController::eliminarProductoController();

// Carga de datos para poblar la tabla y los desplegables de los modales
$productos   = ProductoController::listarProductosController();
$categorias  = CategoriaController::listarCategoriasController();
$proveedores = ProveedorController::listarProveedoresController();
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Gestión de Inventario (Productos)</h1>
  </section>

  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProducto">
          <i class="fa fa-plus"></i> Agregar Producto
        </button>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive" width="100%">
          <thead>
            <tr>
              <th style="width:10px">#</th>
              <th>Producto</th>
              <th>Categoría</th>
              <th>Proveedor</th>
              <th>Precio Venta</th>
              <th>Stock Actual</th>
              <th>Stock Mínimo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($productos as $key => $value): ?>
              <tr>
                <td><?php echo ($key + 1); ?></td>
                <td><?php echo htmlspecialchars($value["nombre_producto"]); ?></td>
                <td><?php echo htmlspecialchars($value["nombre_categoria"] ?? "Sin categoría"); ?></td>
                <td><?php echo htmlspecialchars($value["nombre_proveedor"] ?? "Sin proveedor"); ?></td>
                <td>$ <?php echo number_format($value["precio_venta"], 2); ?></td>
                <td>
                  <?php if ($value["stock_actual"] <= $value["stock_minimo"]): ?>
                    <span class="label label-danger"><?php echo $value["stock_actual"]; ?></span>
                  <?php else: ?>
                    <span class="label label-success"><?php echo $value["stock_actual"]; ?></span>
                  <?php endif; ?>
                </td>
                <td><?php echo $value["stock_minimo"]; ?></td>
                <td>
                  <button class="btn btn-warning btn-sm btnEditarProducto" 
                          idProducto="<?php echo $value["id_producto"]; ?>" 
                          nombreProducto="<?php echo htmlspecialchars($value["nombre_producto"]); ?>" 
                          precioVenta="<?php echo $value["precio_venta"]; ?>" 
                          stockActual="<?php echo $value["stock_actual"]; ?>" 
                          stockMinimo="<?php echo $value["stock_minimo"]; ?>" 
                          idCategoria="<?php echo $value["id_categoria"]; ?>" 
                          idProveedor="<?php echo $value["id_proveedor"]; ?>" 
                          data-toggle="modal" data-target="#modalEditarProducto">
                    <i class="fa fa-pencil"></i>
                  </button>
                  <button class="btn btn-danger btn-sm btnEliminarProducto" idProducto="<?php echo $value["id_producto"]; ?>">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- MODAL AGREGAR PRODUCTO -->
<div id="modalAgregarProducto" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header" style="background:#3c8dbc; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Producto</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nombre del Producto</label>
            <input type="text" class="form-control" name="nuevoNombreProducto" required>
          </div>
          <div class="form-group">
            <label>Categoría</label>
            <select class="form-control" name="nuevaCategoria" required>
              <option value="">Seleccionar Categoría</option>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?php echo $cat["id_categoria"]; ?>"><?php echo htmlspecialchars($cat["nombre_categoria"]); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Proveedor</label>
            <select class="form-control" name="nuevoProveedor" required>
              <option value="">Seleccionar Proveedor</option>
              <?php foreach ($proveedores as $prov): ?>
                <option value="<?php echo $prov["id_proveedor"]; ?>"><?php echo htmlspecialchars($prov["nombre_proveedor"]); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Precio de Venta</label>
            <input type="number" step="0.01" class="form-control" name="nuevoPrecioVenta" required>
          </div>
          <div class="form-group">
            <label>Stock Actual</label>
            <input type="number" class="form-control" name="nuevoStockActual" required>
          </div>
          <div class="form-group">
            <label>Stock Mínimo</label>
            <input type="number" class="form-control" name="nuevoStockMinimo" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar Producto</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL EDITAR PRODUCTO -->
<div id="modalEditarProducto" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header" style="background:#f39c12; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Producto</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editarIdProducto" name="editarIdProducto">
          
          <div class="form-group">
            <label>Nombre del Producto</label>
            <input type="text" class="form-control" id="editarNombreProducto" name="editarNombreProducto" required>
          </div>
          <div class="form-group">
            <label>Categoría</label>
            <select class="form-control" id="editarCategoria" name="editarCategoria" required>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?php echo $cat["id_categoria"]; ?>"><?php echo htmlspecialchars($cat["nombre_categoria"]); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Proveedor</label>
            <select class="form-control" id="editarProveedor" name="editarProveedor" required>
              <?php foreach ($proveedores as $prov): ?>
                <option value="<?php echo $prov["id_proveedor"]; ?>"><?php echo htmlspecialchars($prov["nombre_proveedor"]); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Precio de Venta</label>
            <input type="number" step="0.01" class="form-control" id="editarPrecioVenta" name="editarPrecioVenta" required>
          </div>
          <div class="form-group">
            <label>Stock Actual</label>
            <input type="number" class="form-control" id="editarStockActual" name="editarStockActual" required>
          </div>
          <div class="form-group">
            <label>Stock Mínimo</label>
            <input type="number" class="form-control" id="editarStockMinimo" name="editarStockMinimo" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).on("click", ".btnEditarProducto", function(){
    var idProducto = $(this).attr("idProducto");
    var nombreProducto = $(this).attr("nombreProducto");
    var precioVenta = $(this).attr("precioVenta");
    var stockActual = $(this).attr("stockActual");
    var stockMinimo = $(this).attr("stockMinimo");
    var idCategoria = $(this).attr("idCategoria");
    var idProveedor = $(this).attr("idProveedor");

    $("#editarIdProducto").val(idProducto);
    $("#editarNombreProducto").val(nombreProducto);
    $("#editarPrecioVenta").val(precioVenta);
    $("#editarStockActual").val(stockActual);
    $("#editarStockMinimo").val(stockMinimo);
    $("#editarCategoria").val(idCategoria);
    $("#editarProveedor").val(idProveedor);
});

$(document).on("click", ".btnEliminarProducto", function(e){
    e.preventDefault();
    var idProducto = $(this).attr("idProducto");

    Swal.fire({
        title: '¿Está seguro de eliminar este producto?',
        icon: 'warning',
        showCancelButton: true,
        confirmColor: '#d33',
        cancelColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = "index.php?action=inventario&idEliminarProducto=" + idProducto;
        }
    });
});
</script>