<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION['loggedin'])) {
    header("location: index.php");
    exit;
}

// Handle AJAX requests for CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = ['success' => false];
    if ($_POST['action'] === 'add') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = floatval($_POST['price']);
        $hsn_code = mysqli_real_escape_string($conn, $_POST['hsn_code']);
        // Check uniqueness
        $check = mysqli_query($conn, "SELECT id FROM materials WHERE hsn_code='$hsn_code'");
        if (mysqli_num_rows($check) > 0) {
            $response['success'] = false;
            $response['error'] = 'HSN code must be unique.';
        } else {
            $sql = "INSERT INTO materials (name, price, hsn_code) VALUES ('$name', $price, '$hsn_code')";
            $response['success'] = mysqli_query($conn, $sql);
            $response['id'] = mysqli_insert_id($conn);
        }
    } elseif ($_POST['action'] === 'update') {
        $id = intval($_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = floatval($_POST['price']);
        $hsn_code = mysqli_real_escape_string($conn, $_POST['hsn_code']);
        // Check uniqueness for update
        $check = mysqli_query($conn, "SELECT id FROM materials WHERE hsn_code='$hsn_code' AND id!=$id");
        if (mysqli_num_rows($check) > 0) {
            $response['success'] = false;
            $response['error'] = 'HSN code must be unique.';
        } else {
            $sql = "UPDATE materials SET name='$name', price=$price, hsn_code='$hsn_code' WHERE id=$id";
            $response['success'] = mysqli_query($conn, $sql);
        }
    } elseif ($_POST['action'] === 'delete') {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM materials WHERE id=$id";
        $response['success'] = mysqli_query($conn, $sql);
    } elseif ($_POST['action'] === 'fetch') {
        $materials = [];
        $result = mysqli_query($conn, "SELECT * FROM materials ORDER BY name");
        while ($row = mysqli_fetch_assoc($result)) {
            $materials[] = $row;
        }
        $response['success'] = true;
        $response['materials'] = $materials;
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Materials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #000;
            color: #fff;
            min-height: 100vh;
        }
        .container {
            padding: 2rem;
        }
        .materials-form, .materials-table {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px #0ff,
                        inset 0 0 20px rgba(0, 255, 255, 0.5);
        }
        .neon-text {
            color: #fff;
            text-shadow: 0 0 5px #fff,
                         0 0 10px #0ff,
                         0 0 20px #0ff;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #0ff;
            color: #fff;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #0ff;
            box-shadow: 0 0 10px #0ff;
            color: #fff;
        }
        .btn-neon {
            background: transparent;
            border: 2px solid #0ff;
            color: #fff;
            text-shadow: 0 0 5px #0ff;
            box-shadow: 0 0 10px #0ff;
            transition: all 0.3s ease;
        }
        .btn-neon:hover {
            background: #0ff;
            color: #000;
            box-shadow: 0 0 20px #0ff;
        }
        .nav-link {
            color: #0ff;
            text-shadow: 0 0 5px #0ff;
        }
        .nav-link:hover {
            color: #fff;
        }
        .table {
            color: #fff;
        }
        .table th, .table td {
            background: rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand neon-text" href="billing.php">Billing System</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="billing.php">Back to Billing</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="materials-form mb-4">
            <h2 class="text-center mb-4 neon-text">Manage Materials</h2>
            <form id="addMaterialForm" class="row g-3">
                <div class="col-md-5">
                    <input type="text" class="form-control" id="materialName" placeholder="Material Name" required>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" id="materialPrice" placeholder="Price" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" id="materialHSN" placeholder="HSN Code" maxlength="20" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-neon w-100">Add</button>
                </div>
            </form>
        </div>
        <div class="materials-table">
            <h4 class="mb-3 neon-text">Materials List</h4>
            <table class="table table-hover" id="materialsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>HSN Code</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Materials will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function fetchMaterials() {
            $.post('materials.php', {action: 'fetch'}, function(data) {
                if(data.success) {
                    let rows = '';
                    data.materials.forEach(function(mat) {
                        rows += `<tr data-id="${mat.id}">
                            <td><span class="mat-name">${mat.name}</span></td>
                            <td><span class="mat-price">${parseFloat(mat.price).toFixed(2)}</span></td>
                            <td><span class="mat-hsn">${mat.hsn_code}</span></td>
                            <td>
                                <button class="btn btn-sm btn-neon edit-btn">Edit</button>
                                <button class="btn btn-sm btn-danger delete-btn">Delete</button>
                            </td>
                        </tr>`;
                    });
                    $('#materialsTable tbody').html(rows);
                }
            }, 'json');
        }

        $(document).ready(function() {
            fetchMaterials();

            $('#addMaterialForm').submit(function(e) {
                e.preventDefault();
                const name = $('#materialName').val().trim();
                const price = $('#materialPrice').val();
                const hsn_code = $('#materialHSN').val().trim();
                if(name && price && hsn_code) {
                    $.post('materials.php', {action: 'add', name, price, hsn_code}, function(data) {
                        if(data.success) {
                            fetchMaterials();
                            $('#addMaterialForm')[0].reset();
                        } else if(data.error) {
                            alert(data.error);
                        }
                    }, 'json');
                }
            });

            $('#materialsTable').on('click', '.delete-btn', function() {
                if(confirm('Delete this material?')) {
                    const id = $(this).closest('tr').data('id');
                    $.post('materials.php', {action: 'delete', id}, function(data) {
                        if(data.success) fetchMaterials();
                    }, 'json');
                }
            });

            $('#materialsTable').on('click', '.edit-btn', function() {
                const tr = $(this).closest('tr');
                const id = tr.data('id');
                const name = tr.find('.mat-name').text();
                const price = tr.find('.mat-price').text();
                const hsn = tr.find('.mat-hsn').text();
                tr.html(`<td><input type='text' class='form-control form-control-sm edit-name' value='${name}'></td>
                         <td><input type='number' class='form-control form-control-sm edit-price' value='${price}' min='0' step='0.01'></td>
                         <td><input type='text' class='form-control form-control-sm edit-hsn' value='${hsn}' maxlength='20'></td>
                         <td>
                            <button class='btn btn-sm btn-success save-btn'>Save</button>
                            <button class='btn btn-sm btn-secondary cancel-btn'>Cancel</button>
                         </td>`);
            });

            $('#materialsTable').on('click', '.cancel-btn', function() {
                fetchMaterials();
            });

            $('#materialsTable').on('click', '.save-btn', function() {
                const tr = $(this).closest('tr');
                const id = tr.data('id');
                const name = tr.find('.edit-name').val().trim();
                const price = tr.find('.edit-price').val();
                const hsn_code = tr.find('.edit-hsn').val().trim();
                if(name && price && hsn_code) {
                    $.post('materials.php', {action: 'update', id, name, price, hsn_code}, function(data) {
                        if(data.success) fetchMaterials();
                        else if(data.error) alert(data.error);
                    }, 'json');
                }
            });
        });
    </script>
</body>
</html> 