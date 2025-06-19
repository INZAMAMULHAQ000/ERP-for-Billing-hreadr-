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
            --neon-color: #007bff; /* A blue neon for light theme */
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
        .billing-form {
            background: var(--form-bg);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px var(--neon-color),
                        inset 0 0 20px rgba(0, 255, 255, 0.5); /* Keep a bit of the original neon feel for the form */
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }
        .neon-text {
            color: var(--text-color);
            text-shadow: 0 0 5px var(--text-color),
                         0 0 10px var(--neon-color),
                         0 0 20px var(--neon-color);
            transition: color 0.3s ease, text-shadow 0.3s ease;
        }
        .form-control, .form-select {
            background: var(--form-bg);
            border: 1px solid var(--form-border);
            color: var(--text-color);
            transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
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
<body class="dark-theme"> <!-- Default to dark theme -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand neon-text" href="#">Billing System</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="materials.php">Manage Materials</a>
                <a class="nav-link" href="transport.php">Manage Transport</a>
                <button id="themeToggle" class="btn btn-secondary ms-2">Toggle Theme</button>
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
                        <select name="material[]" id="material" class="form-select" multiple="multiple">
                            <?php while($row = mysqli_fetch_assoc($materials_result)): ?>
                                <option value="<?php echo $row['id']; ?>" data-price="<?php echo $row['price']; ?>" data-hsn="<?php echo $row['hsn_code']; ?>">
                                    <?php echo $row['name']; ?> (HSN: <?php echo $row['hsn_code']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Total Price</label>
                        <input type="number" name="price" id="price" class="form-control" required readonly value="0">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label>Selected Materials</label>
                        <div id="selectedMaterialsTableContainer" style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-bordered table-sm" id="selectedMaterialsTable">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>HSN</th>
                                        <th>Price/Unit</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Selected materials will be added here -->
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="selected_materials_data" id="selectedMaterialsData">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>CGST (%)</label>
                        <input type="number" name="cgst_rate" class="form-control" value="0" required>
                        </div>
                    <div class="col-md-3 mb-3">
                        <label>SGST (%)</label>
                        <input type="number" name="sgst_rate" class="form-control" value="0" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>IGST (%)</label>
                        <input type="number" name="igst_rate" class="form-control" value="0" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>GST (%)</label>
                        <input type="number" name="gst_rate" class="form-control" value="0" required>
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

            function calculateGrandTotal() {
                let grandTotal = 0;
                $('#selectedMaterialsTable tbody tr').each(function() {
                    const subtotal = parseFloat($(this).find('.item-subtotal').text());
                    if (!isNaN(subtotal)) {
                        grandTotal += subtotal;
                    }
                });
                $('#price').val(grandTotal.toFixed(2));
            }

            $('#material').on('change', function() {
                const selectedMaterialIds = $(this).val() || [];
                const materialsInTable = {};
                $('#selectedMaterialsTable tbody tr').each(function() {
                    const id = $(this).data('id');
                    materialsInTable[id] = $(this);
                });

                // Remove deselected materials from table
                for (const id in materialsInTable) {
                    if (!selectedMaterialIds.includes(id)) {
                        materialsInTable[id].remove();
                    }
                }

                // Add newly selected materials to table
                selectedMaterialIds.forEach(function(id) {
                    if (!materialsInTable[id]) {
                        const option = $('#material option[value="' + id + '"]');
                        const name = option.text().split('(HSN:')[0].trim();
                        const hsn = option.data('hsn');
                        const pricePerUnit = option.data('price');

                        const newRow = `<tr data-id="${id}" data-price-per-unit="${pricePerUnit}">
                                            <td>${name}</td>
                                            <td>${hsn}</td>
                                            <td>${pricePerUnit}</td>
                                            <td><input type="number" class="form-control form-control-sm item-quantity" value="1" min="1" style="width: 80px;"></td>
                                            <td class="item-subtotal">${(pricePerUnit * 1).toFixed(2)}</td>
                                            <td><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></td>
                                        </tr>`;
                        $('#selectedMaterialsTable tbody').append(newRow);
                    }
                });
                updateHiddenDataAndTotal();
            });

            $('#selectedMaterialsTable').on('input', '.item-quantity', function() {
                const row = $(this).closest('tr');
                const quantity = parseInt($(this).val()) || 0;
                const pricePerUnit = parseFloat(row.data('price-per-unit'));
                const subtotal = quantity * pricePerUnit;
                row.find('.item-subtotal').text(subtotal.toFixed(2));
                updateHiddenDataAndTotal();
            });

            $('#selectedMaterialsTable').on('click', '.remove-item', function() {
                const row = $(this).closest('tr');
                const materialIdToRemove = row.data('id').toString();
                
                // Deselect the item in the select2 dropdown
                const currentSelected = $('#material').val();
                const newSelected = currentSelected.filter(id => id !== materialIdToRemove);
                $('#material').val(newSelected).trigger('change.select2');

                row.remove(); // Remove the row from the table
                updateHiddenDataAndTotal();
            });

            function updateHiddenDataAndTotal() {
                const selectedMaterials = [];
                $('#selectedMaterialsTable tbody tr').each(function() {
                    const id = $(this).data('id');
                    const name = $(this).find('td').eq(0).text().trim();
                    const hsn_code = $(this).find('td').eq(1).text().trim();
                    const price_per_unit = parseFloat($(this).data('price-per-unit'));
                    const quantity = parseInt($(this).find('.item-quantity').val()) || 0;
                    selectedMaterials.push({ id: id, name: name, hsn_code: hsn_code, price_per_unit: price_per_unit, quantity: quantity });
                });
                $('#selectedMaterialsData').val(JSON.stringify(selectedMaterials));
                calculateGrandTotal();
            }

            // Initial call to populate table if there are pre-selected items (e.g., on form reload, though not implemented here)
            updateHiddenDataAndTotal();

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