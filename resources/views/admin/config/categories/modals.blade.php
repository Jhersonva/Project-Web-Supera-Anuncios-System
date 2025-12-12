<!-- views/admin/categories/admin.blade.php -->
<!-- MODALES DE CATEGORÍAS -->
<div class="modal fade" id="modalAddCategory" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('admin.config.categories.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Nueva Categoría</h5>
            </div>
            <div class="modal-body">

                <label class="form-label">Nombre</label>
                <input type="text" name="name" class="form-control" required>

                <label class="form-label mt-3">Icono (FontAwesome)</label>
                <input type="text" name="icon" class="form-control" required>
                <small class="text-muted">Ejemplo: fa-car, fa-house</small>
                <div class="form-check mt-3">
                    <input type="checkbox" name="is_property" value="1" class="form-check-input">
                    <label class="form-check-label">¿Es un inmueble?</label>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-danger">Guardar</button>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="modalEditCategory" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" id="formEditCategory">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Editar Categoría</h5>
            </div>
            <div class="modal-body">

                <input type="hidden" name="id" id="editCategoryId">

                <label>Nombre</label>
                <input type="text" class="form-control" id="editCategoryName" name="name">

                <label class="mt-3">Icono</label>
                <input type="text" class="form-control" id="editCategoryIcon" name="icon">
            </div>

            <div class="modal-footer">
                <button class="btn btn-danger">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalDeleteCategory">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" id="formDeleteCategory">
            @csrf @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title">Eliminar Categoría</h5>
            </div>
            <div class="modal-body">
                ¿Eliminar esta categoría y todos sus datos?
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>

<!-- SUBCATEGORÍAS -->
<div class="modal fade" id="modalAddSubcategory">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('admin.config.subcategories.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Nueva Subcategoría</h5>
            </div>
            <div class="modal-body">

                <input type="hidden" name="ad_categories_id" id="addSubcategoryCategoryId">

                <label>Nombre</label>
                <input type="text" name="name" class="form-control">

                <label class="mt-3">Precio (S/.)</label>
                <input type="number" name="price" step="0.01" class="form-control">

            </div>
            <div class="modal-footer">
                <button class="btn btn-danger">Guardar</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditSubcategory">
    <div class="modal-dialog">
        <form class="modal-content" id="formEditSubcategory" method="POST">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Editar Subcategoría</h5>
            </div>
            <div class="modal-body">

                <input type="hidden" name="id" id="editSubId">

                <label>Nombre</label>
                <input type="text" id="editSubName" name="name" class="form-control">

                <label class="mt-3">Precio</label>
                <input type="number" id="editSubPrice" name="price" step="0.01" class="form-control">

            </div>
            <div class="modal-footer">
                <button class="btn btn-danger">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalDeleteSubcategory">
    <div class="modal-dialog">
        <form class="modal-content" id="formDeleteSubcategory" method="POST">
            @csrf @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title">Eliminar Subcategoría</h5>
            </div>
            <div class="modal-body">
                ¿Eliminar esta subcategoría y sus campos?
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>

<!-- CAMPOS -->
<div class="modal fade" id="modalAddField">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('admin.config.fields.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Campo</h5>
            </div>
            <div class="modal-body">

                <input type="hidden" name="ad_subcategories_id" id="addFieldSubId">

                <label>Nombre del campo</label>
                <input type="text" name="name" class="form-control">

            </div>
            <div class="modal-footer">
                <button class="btn btn-danger">Guardar</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditField">
    <div class="modal-dialog">
        <form class="modal-content" id="formEditField" method="POST">
            @csrf @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title">Editar Campo</h5>
            </div>

            <div class="modal-body">

                <input type="hidden" id="editFieldId" name="id">

                <label>Nombre</label>
                <input type="text" id="editFieldName" name="name" class="form-control">

            </div>

            <div class="modal-footer">
                <button class="btn btn-danger">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalDeleteField">
    <div class="modal-dialog">
        <form class="modal-content" id="formDeleteField" method="POST">
            @csrf @method('DELETE')

            <div class="modal-header">
                <h5 class="modal-title">Eliminar Campo</h5>
            </div>

            <div class="modal-body">
                ¿Eliminar este campo?
            </div>

            <div class="modal-footer">
                <button class="btn btn-danger">Eliminar</button>
            </div>

        </form>
    </div>
</div>
