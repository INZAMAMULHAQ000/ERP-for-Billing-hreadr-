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
        :root {
            --background-color: #000;
            --text-color: #fff;
            --neon-color: #0ff;
            --form-bg: rgba(255, 255, 255, 0.1);
            --form-border: #0ff;
            --form-focus-bg: rgba(255, 255, 255, 0.2);
            --form-focus-shadow: 0 0 10px var(--neon-color);
            --btn-text-shadow: 0 0 5px var(--neon-color);
            --btn-hover-bg: var(--neon-color);
            --btn-hover-color: #000;
            --btn-hover-shadow: 0 0 20px var(--neon-color);
            --table-bg: rgba(0,0,0,0.5);
            --table-border: #ddd;
        }

        body.light-theme {
            --background-color: #f0f2f5;
            --text-color: #333;
            --neon-color: #007bff;
            --form-bg: rgba(255, 255, 255, 0.8);
            --form-border: #007bff;
            --form-focus-bg: rgba(255, 255, 255, 0.9);
            --form-focus-shadow: 0 0 10px var(--neon-color);
            --btn-text-shadow: none;
            --btn-hover-bg: var(--neon-color);
            --btn-hover-color: #fff;
            --btn-hover-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
            --table-bg: rgba(255,255,255,0.9);
            --table-border: #ccc;
        }

        body {
            background: var(--background-color);
            color: var(--text-color);
            min-height: 100vh;
            transition: background 0.3s ease, color 0.3s ease;
        }
        .container {
            padding: 2rem;
        }
        .transports-form, .transports-table {
            background: var(--form-bg);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px var(--neon-color),
                        inset 0 0 20px rgba(0, 255, 255, 0.5);
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }
        .neon-text {
            color: var(--text-color);
            text-shadow: 0 0 5px var(--text-color),
                         0 0 10px var(--neon-color),
                         0 0 20px var(--neon-color);
            transition: color 0.3s ease, text-shadow 0.3s ease;
        }
        .form-control {
            background: var(--form-bg);
            border: 1px solid var(--form-border);
            color: var(--text-color);
            transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
        .form-control:focus {
            background: var(--form-focus-bg);
            border-color: var(--form-border);
            box-shadow: var(--form-focus-shadow);
            color: var(--text-color);
        }
        .btn-neon {
            background: transparent;
            border: 2px solid var(--neon-color);
            color: var(--text-color);
            text-shadow: var(--btn-text-shadow);
            box-shadow: 0 0 10px var(--neon-color);
            transition: all 0.3s ease;
        }
        .btn-neon:hover {
            background: var(--btn-hover-bg);
            color: var(--btn-hover-color);
            box-shadow: var(--btn-hover-shadow);
        }
        .nav-link {
            color: var(--neon-color);
            text-shadow: 0 0 5px var(--neon-color);
            transition: color 0.3s ease, text-shadow 0.3s ease;
        }
        .nav-link:hover {
            color: var(--text-color);
        }
        .table {
            color: var(--text-color);
        }
        .table th, .table td {
            background: var(--table-bg);
            border-color: var(--table-border);
            transition: background 0.3s ease, border-color 0.3s ease;
        }
    </style>
</head>
<body class="dark-theme">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand neon-text" href="billing.php">Billing System</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="billing.php">Back to Billing</a>
                <button id="themeToggle" class="btn btn-secondary ms-2">Toggle Theme</button>
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

            // Theme Toggle Logic
            $('#themeToggle').on('click', function() {
                $('body').toggleClass('light-theme dark-theme');
                // Save preference to localStorage
                if ($('body').hasClass('light-theme')) {
                    localStorage.setItem('theme', 'light');
                } else {
                    localStorage.setItem('theme', 'dark');
                }
            });

            // Load theme preference on page load
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                $('body').removeClass('light-theme dark-theme').addClass(savedTheme + '-theme');
            } else {
                // Default to dark if no preference saved
                $('body').addClass('dark-theme');
            }
        });
    </script>
</body>
</html> 