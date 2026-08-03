<?php
CategoriaController::guardarCategoriaController();
CategoriaController::actualizarCategoriaController();
CategoriaController::eliminarCategoriaController();

$categorias = CategoriaController::listarCategoriasController();
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Gestión de Categorías</h1>
  </section>

  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarCategoria">
          <i class="fa fa-plus"></i> Agregar Categoría
        </button>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive" width="100%">
          <thead>
            <tr>
              <th style="width:10px">#</th>
              <th>Nombre Categoría</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categorias as $key => $value): ?>
              <tr>
                <td><?php echo ($key + 1); ?></td>
                <td><?php echo htmlspecialchars($value["nombre_categoria"]); ?></td>
                <td>
                  <button class="btn btn-warning btn-sm btnEditarCategoria" 
                          idCategoria="<?php echo $value["id_categoria"]; ?>" 
                          nombreCategoria="<?php echo htmlspecialchars($value["nombre_categoria"]); ?>" 
                          data-toggle="modal" data-target="#modalEditarCategoria">
                    <i class="fa fa-pencil"></i>
                  </button>
                  <button class="btn btn-danger btn-sm btnEliminarCategoria" idCategoria="<?php echo $value["id_categoria"]; ?>">
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

<!-- MODAL AGREGAR CATEGORÍA -->
<div id="modalAgregarCategoria" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header" style="background:#3c8dbc; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Categoría</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nombre de la Categoría</label>
            <input type="text" class="form-control" name="nuevoNombreCategoria" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar Categoría</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL EDITAR CATEGORÍA -->
<div id="modalEditarCategoria" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header" style="background:#f39c12; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Categoría</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editarIdCategoria" name="editarIdCategoria">
          <div class="form-group">
            <label>Nombre de la Categoría</label>
            <input type="text" class="form-control" id="editarNombreCategoria" name="editarNombreCategoria" required>
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
$(document).on("click", ".btnEditarCategoria", function(){
    var idCategoria = $(this).attr("idCategoria");
    var nombreCategoria = $(this).attr("nombreCategoria");

    $("#editarIdCategoria").val(idCategoria);
    $("#editarNombreCategoria").val(nombreCategoria);
});

$(document).on("click", ".btnEliminarCategoria", function(e){
    e.preventDefault();
    var idCategoria = $(this).attr("idCategoria");

    Swal.fire({
        title: '¿Está seguro de eliminar esta categoría?',
        icon: 'warning',
        showCancelButton: true,
        confirmColor: '#d33',
        cancelColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = "index.php?action=categorias&idEliminarCategoria=" + idCategoria;
        }
    });
});
</script>