<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION['loggedin'])) {
    header("location: index.php");
    exit;
}

if(isset($_POST['add_material'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    
    $sql = "INSERT INTO materials (name, price) VALUES ('$name', '$price')";
    mysqli_query($conn, $sql);
}

$materials_query = "SELECT * FROM materials ORDER BY name";
$materials_result = mysqli_query($conn, $materials_query);
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
        .materials-container {
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
        .table thead th {
            border-color: #0ff;
        }
        .table td {
            border-color: rgba(0, 255, 255, 0.2);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand neon-text" href="#">Billing System</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="billing.php">Generate Bill</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="materials-container">
            <h2 class="text-center mb-4 neon-text">Manage Materials</h2>
            
            <form method="post" class="mb-4">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" name="name" class="form-control" placeholder="Material Name" required>
                    </div>
                    <div class="col-md-5">
                        <input type="number" name="price" class="form-control" placeholder="Price" step="0.01" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="add_material" class="btn btn-neon w-100">Add</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Material Name</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($materials_result)): ?>
                            <tr>
                                <td><?php echo $row['name']; ?></td>
                                <td>₹<?php echo number_format($row['price'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 