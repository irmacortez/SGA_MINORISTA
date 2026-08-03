<?php
// Procesamos las acciones si se envía un formulario o se solicita una eliminación
ProveedorController::guardarProveedorController();
ProveedorController::actualizarProveedorController();
ProveedorController::eliminarProveedorController();

// Obtenemos el listado de todos los proveedores
$proveedores = ProveedorController::listarProveedoresController();
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Gestión de Proveedores</h1>
  </section>

  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProveedor">
          <i class="fa fa-plus"></i> Agregar Proveedor
        </button>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive" width="100%">
          <thead>
            <tr>
              <th style="width:10px">#</th>
              <th>Nombre / Empresa</th>
              <th>Teléfono</th>
              <th>Email</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($proveedores as $key => $value): ?>
              <tr>
                <td><?php echo ($key + 1); ?></td>
                <td><?php echo htmlspecialchars($value["nombre_proveedor"]); ?></td>
                <td><?php echo htmlspecialchars($value["telefono"]); ?></td>
                <td><?php echo htmlspecialchars($value["email"]); ?></td>
                <td>
                  <button class="btn btn-warning btn-sm btnEditarProveedor" 
                          idProveedor="<?php echo $value["id_proveedor"]; ?>" 
                          nombreProveedor="<?php echo htmlspecialchars($value["nombre_proveedor"]); ?>" 
                          telefono="<?php echo htmlspecialchars($value["telefono"]); ?>" 
                          email="<?php echo htmlspecialchars($value["email"]); ?>" 
                          data-toggle="modal" data-target="#modalEditarProveedor">
                    <i class="fa fa-pencil"></i>
                  </button>
                  <button class="btn btn-danger btn-sm btnEliminarProveedor" idProveedor="<?php echo $value["id_proveedor"]; ?>">
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

<!-- =============================================
MODAL AGREGAR PROVEEDOR
============================================= -->
<div id="modalAgregarProveedor" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header" style="background:#3c8dbc; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Proveedor</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nombre del Proveedor / Empresa</label>
            <input type="text" class="form-control" name="nuevoNombreProveedor" placeholder="Ej: Distribuidora Central" required>
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input type="text" class="form-control" name="nuevoTelefono" placeholder="Ej: 1122334455">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="nuevoEmail" placeholder="Ej: contacto@proveedor.com">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar Proveedor</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- =============================================
MODAL EDITAR PROVEEDOR
============================================= -->
<div id="modalEditarProveedor" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header" style="background:#f39c12; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Proveedor</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editarIdProveedor" name="editarIdProveedor">
          <div class="form-group">
            <label>Nombre del Proveedor / Empresa</label>
            <input type="text" class="form-control" id="editarNombreProveedor" name="editarNombreProveedor" required>
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input type="text" class="form-control" id="editarTelefono" name="editarTelefono">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" id="editarEmail" name="editarEmail">
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

<!-- =============================================
SCRIPTS DEL MÓDULO PROVEEDORES
============================================= -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Cargar datos en el modal de edición al hacer clic en el botón amarillo
$(document).on("click", ".btnEditarProveedor", function(){
    var idProveedor = $(this).attr("idProveedor");
    var nombreProveedor = $(this).attr("nombreProveedor");
    var telefono = $(this).attr("telefono");
    var email = $(this).attr("email");

    $("#editarIdProveedor").val(idProveedor);
    $("#editarNombreProveedor").val(nombreProveedor);
    $("#editarTelefono").val(telefono);
    $("#editarEmail").val(email);
});

// Confirmación de eliminación con SweetAlert2
$(document).on("click", ".btnEliminarProveedor", function(e){
    e.preventDefault();
    var idProveedor = $(this).attr("idProveedor");

    Swal.fire({
        title: '¿Está seguro de eliminar este proveedor?',
        text: "Esta acción no se podrá deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmColor: '#d33',
        cancelColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = "index.php?action=proveedores&idEliminarProveedor=" + idProveedor;
        }
    });
});
</script>