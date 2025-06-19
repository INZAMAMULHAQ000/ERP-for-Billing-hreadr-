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
        $sql = "INSERT INTO transports (name) VALUES ('$name')";
        $response['success'] = mysqli_query($conn, $sql);
        $response['id'] = mysqli_insert_id($conn);
    } elseif ($_POST['action'] === 'update') {
        $id = intval($_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $sql = "UPDATE transports SET name='$name' WHERE id=$id";
        $response['success'] = mysqli_query($conn, $sql);
    } elseif ($_POST['action'] === 'delete') {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM transports WHERE id=$id";
        $response['success'] = mysqli_query($conn, $sql);
    } elseif ($_POST['action'] === 'fetch') {
        $transports = [];
        $result = mysqli_query($conn, "SELECT * FROM transports ORDER BY name");
        while ($row = mysqli_fetch_assoc($result)) {
            $transports[] = $row;
        }
        $response['success'] = true;
        $response['transports'] = $transports;
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
    <title>Manage Transport</title>
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
        .transports-form, .transports-table {
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
        <div class="transports-form mb-4">
            <h2 class="text-center mb-4 neon-text">Manage Transport</h2>
            <form id="addTransportForm" class="row g-3">
                <div class="col-md-10">
                    <input type="text" class="form-control" id="transportName" placeholder="Mode of Transport" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-neon w-100">Add</button>
                </div>
            </form>
        </div>
        <div class="transports-table">
            <h4 class="mb-3 neon-text">Transport Modes List</h4>
            <table class="table table-hover" id="transportsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Transports will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function fetchTransports() {
            $.post('transport.php', {action: 'fetch'}, function(data) {
                if(data.success) {
                    let rows = '';
                    data.transports.forEach(function(tr) {
                        rows += `<tr data-id="${tr.id}">
                            <td><span class="tr-name">${tr.name}</span></td>
                            <td>
                                <button class="btn btn-sm btn-neon edit-btn">Edit</button>
                                <button class="btn btn-sm btn-danger delete-btn">Delete</button>
                            </td>
                        </tr>`;
                    });
                    $('#transportsTable tbody').html(rows);
                }
            }, 'json');
        }

        $(document).ready(function() {
            fetchTransports();

            $('#addTransportForm').submit(function(e) {
                e.preventDefault();
                const name = $('#transportName').val().trim();
                if(name) {
                    $.post('transport.php', {action: 'add', name}, function(data) {
                        if(data.success) {
                            fetchTransports();
                            $('#addTransportForm')[0].reset();
                        }
                    }, 'json');
                }
            });

            $('#transportsTable').on('click', '.delete-btn', function() {
                if(confirm('Delete this mode of transport?')) {
                    const id = $(this).closest('tr').data('id');
                    $.post('transport.php', {action: 'delete', id}, function(data) {
                        if(data.success) fetchTransports();
                    }, 'json');
                }
            });

            $('#transportsTable').on('click', '.edit-btn', function() {
                const tr = $(this).closest('tr');
                const id = tr.data('id');
                const name = tr.find('.tr-name').text();
                tr.html(`<td><input type='text' class='form-control form-control-sm edit-name' value='${name}'></td>
                         <td>
                            <button class='btn btn-sm btn-success save-btn'>Save</button>
                            <button class='btn btn-sm btn-secondary cancel-btn'>Cancel</button>
                         </td>`);
            });

            $('#transportsTable').on('click', '.cancel-btn', function() {
                fetchTransports();
            });

            $('#transportsTable').on('click', '.save-btn', function() {
                const tr = $(this).closest('tr');
                const id = tr.data('id');
                const name = tr.find('.edit-name').val().trim();
                if(name) {
                    $.post('transport.php', {action: 'update', id, name}, function(data) {
                        if(data.success) fetchTransports();
                    }, 'json');
                }
            });
        });
    </script>
</body>
</html> 