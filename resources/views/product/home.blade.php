<!DOCTYPE html>
<html lang="en">
 
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel 11 CRUD (Create, Read, Update and Delete) Ajax with Upload Image</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
 
<body>
    <div class="container">
        <h1>Laravel 11 CRUD (Create, Read, Update and Delete) Ajax with Upload Image</h1>
        <a href="javascript:void(0)" class="btn btn-info ml-3" id="create-new-product">Add New</a>
        <br><br>
        <table class="table table-bordered table-striped" id="laravel_11_datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>S. No</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="modal fade" id="ajax-product-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="productCrudModal"></h4>
                </div>
                <div class="modal-body">
                    <form id="productForm" name="productForm" class="form-horizontal" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" id="product_id">
                        <div class="form-group">
                            <label for="title" class="col-sm-2 control-label">Title</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="title" name="title" placeholder="Enter Title" maxlength="50" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="category" class="col-sm-2 control-label">Category</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="category" name="category" placeholder="Enter Category" maxlength="50" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="price" class="col-sm-2 control-label">Price</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="price" name="price" placeholder="Enter Price" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="image" class="col-sm-2 control-label">Image</label>
                            <div class="col-sm-12">
                                <input id="image" type="file" name="image" accept="image/*" onchange="readURL(this);">
                                <input type="hidden" name="hidden_image" id="hidden_image">
                            </div>
                        </div>
                        <img id="modal-preview" src="https://via.placeholder.com/150" alt="Preview" class="form-group hidden" width="100" height="100">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" id="btn-save" value="create">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
 
    <script>
        
        // Link
        var SITEURL = '{{ url('/') }}/';

        // Token security
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $('#laravel_11_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: SITEURL + "products",
                    type: 'GET',
                },
                columns: [
                    { data: 'id', name: 'id', 'visible': false },
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'title', name: 'title' },
                    { data: 'category', name: 'category' },
                    { data: 'price', name: 'price' },
                    { data: 'image', name: 'image', orderable: false },
                    { data: 'action', name: 'action', orderable: false }
                ],
                order: [[0, 'desc']]
            });

            $('#create-new-product').click(function() {
                $('#btn-save').val("create-product");
                $('#product_id').val('');
                $('#productForm').trigger("reset");
                $('#productCrudModal').html("Add New Product");
                $('#ajax-product-modal').modal('show');
                $('#modal-preview').attr('src', 'https://via.placeholder.com/150');
            });

            $('body').on('click', '.edit-product', function() {
                var product_id = $(this).data('id');
                showProductModal(product_id);
            });

            $('body').on('click', '#delete-product', function() {
                var product_id = $(this).data("id");
                deleteProduct(product_id);
            });
        });

        $('#productForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $('#btn-save').html('Sending..');
            $.ajax({
                type: 'POST',
                url: SITEURL + "products/Store",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: (data) => {
                    $('#btn-save').html('Save Changes');
                    $('#productForm').trigger("reset");
                    $('#ajax-product-modal').modal('hide');
                    $('#laravel_11_datatable').DataTable().ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Product added successfully!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                },
                error: function(data) {
                    $('#btn-save').html('Save Changes');
                    console.log('Error:', data);
                }
            });
        });

        function showProductModal(id) {
            $.ajax({
                type: "GET",
                url: SITEURL + "products/Edit/" + id,
                dataType: 'json',
                success: function(data) {
                    $('#productCrudModal').html("Edit Product");
                    $('#btn-save').val("edit-product");
                    $('#ajax-product-modal').modal('show');
                    $('#product_id').val(data.id);
                    $('#title').val(data.title);
                    $('#category').val(data.category);
                    $('#price').val(data.price);
                    if (data.image) {
                        $('#modal-preview').attr('src', SITEURL + 'public/product/' + data.image);
                        $('#hidden_image').val(data.image);
                    } else {
                        $('#modal-preview').attr('src', 'https://via.placeholder.com/150');
                        $('#hidden_image').attr('src', 'https://via.placeholder.com/150');
                    }
                }
            });
        }

        function deleteProduct(id) {
            if (confirm("Are you sure you want to delete this product?")) {
                $.ajax({
                    type: "DELETE",
                    url: SITEURL + "products/Delete/" + id,
                    success: function(data) {
                        $('#laravel_11_datatable').DataTable().ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Product deleted successfully!',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            }
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#modal-preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
 
</html>