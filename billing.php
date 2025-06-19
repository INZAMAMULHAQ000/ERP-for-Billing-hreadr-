<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION['loggedin'])) {
    header("location: index.php");
    exit;
}

// Fetch materials for dropdown
$materials_query = "SELECT * FROM materials ORDER BY name";
$materials_result = mysqli_query($conn, $materials_query);

// Fetch transports for dropdown
$transports_query = "SELECT * FROM transports ORDER BY name";
$transports_result = mysqli_query($conn, $transports_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Bill</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background: #000;
            color: #fff;
            min-height: 100vh;
        }
        .container {
            padding: 2rem;
        }
        .billing-form {
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
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #0ff;
            color: #fff;
        }
        .form-control:focus, .form-select:focus {
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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand neon-text" href="#">Billing System</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="materials.php">Manage Materials</a>
                <a class="nav-link" href="transport.php">Manage Transport</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="billing-form">
            <h2 class="text-center mb-4 neon-text">Generate Bill</h2>
            <form id="billingForm" action="generate_pdf.php" method="post" target="_blank">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Invoice Number</label>
                        <input type="text" name="invoice_number" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>GSTIN ID</label>
                        <input type="text" name="gstin" class="form-control">
                    </div>
                </div>

                <!-- New fields for address and phone -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Address</label>
                        <textarea name="customer_address" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Phone No</label>
                        <input type="text" name="customer_phone" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Material</label>
                        <select name="material" id="material" class="form-select" required>
                            <option value="">Select Material</option>
                            <?php while($row = mysqli_fetch_assoc($materials_result)): ?>
                                <option value="<?php echo $row['id']; ?>" data-price="<?php echo $row['price']; ?>">
                                    <?php echo $row['name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required min="1">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Price</label>
                        <input type="number" name="price" id="price" class="form-control" required readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>GST Type</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gst_type" value="sgst_cgst" checked>
                            <label class="form-check-label">SGST + CGST</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gst_type" value="igst">
                            <label class="form-check-label">IGST</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>State</label>
                        <input type="text" name="state" class="form-control" value="Karnataka" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>GST Rate (%)</label>
                        <input type="number" name="gst_rate" class="form-control" value="18" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Mode of Transport</label>
                        <select name="transport" id="transport" class="form-select" required>
                            <option value="">Select Mode of Transport</option>
                            <?php while($trow = mysqli_fetch_assoc($transports_result)): ?>
                                <option value="<?php echo $trow['id']; ?>"><?php echo $trow['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-neon btn-lg">Generate Bill</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#material').select2();

            $('#material').change(function() {
                const price = $(this).find(':selected').data('price');
                $('#price').val(price);
            });

            $('#quantity').change(function() {
                const price = $('#material').find(':selected').data('price');
                const quantity = $(this).val();
                $('#price').val(price * quantity);
            });
        });
    </script>
</body>
</html> 