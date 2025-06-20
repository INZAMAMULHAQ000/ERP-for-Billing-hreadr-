<?php
error_reporting(E_ALL); // Display all errors for debugging
ini_set('display_errors', 1); // Make sure errors are displayed

session_start();
require_once "config/database.php";
require_once 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Read QR code image and convert to base64
$qr_code_file = __DIR__ . '/QR.jpeg';
$qr_code_data = '';
if (file_exists($qr_code_file)) {
    $qr_code_type = pathinfo($qr_code_file, PATHINFO_EXTENSION);
    $qr_code_data = 'data:image/' . $qr_code_type . ';base64,' . base64_encode(file_get_contents($qr_code_file));
} else {
    // Optionally, handle the case where the file does not exist (e.g., log error, use a placeholder)
    error_log("QR code image not found at: " . $qr_code_file);
}

if(!isset($_SESSION['loggedin'])) {
    header("location: index.php");
    exit;
}

// Validate required POST data
if (!isset($_POST['transport']) || empty($_POST['transport'])) {
    die("Error: Mode of Transport is required.");
}

if (!isset($_POST['selected_materials_data']) || empty($_POST['selected_materials_data'])) {
    die("Error: No materials selected.");
}

$selected_materials_data = json_decode($_POST['selected_materials_data'], true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error: Invalid material data provided.");
}

if (empty($selected_materials_data)) {
    die("Error: No materials selected for billing.");
}

// Fetch transport details
$transport_id = $_POST['transport'];
$transport_query = "SELECT name FROM transports WHERE id = '$transport_id'";
$transport_result = mysqli_query($conn, $transport_query);
$transport = mysqli_fetch_assoc($transport_result);

$total_price_before_gst = 0;
$html_material_rows = '';
$item_count = 1;

foreach ($selected_materials_data as $item) {
    $item_id = $item['id'];
    $item_name = htmlspecialchars($item['name']);
    $item_hsn_code = htmlspecialchars($item['hsn_code']);
    $item_price_per_unit = floatval($item['price_per_unit']);
    $item_quantity = intval($item['quantity']);
    $item_subtotal = $item_price_per_unit * $item_quantity;
    $total_price_before_gst += $item_subtotal;

    $html_material_rows .= '
            <tr>
                <td>' . $item_count++ . '</td>
                <td>' . $item_name . '</td>
                <td>' . $item_hsn_code . '</td>
                <td>' . $item_quantity . '</td>
                <td>₹' . number_format($item_price_per_unit, 2) . '</td>
                <td>₹' . number_format($item_subtotal, 2) . '</td>
            </tr>';
}

$price = $total_price_before_gst; // Rename for clarity, this is the total material price before any GST

$cgst_rate = isset($_POST['cgst_rate']) ? floatval($_POST['cgst_rate']) : 0;
$sgst_rate = isset($_POST['sgst_rate']) ? floatval($_POST['sgst_rate']) : 0;
$igst_rate = isset($_POST['igst_rate']) ? floatval($_POST['igst_rate']) : 0;
$gst_rate = isset($_POST['gst_rate']) ? floatval($_POST['gst_rate']) : 0;

$cgst_amount = ($price * $cgst_rate) / 100;
$sgst_amount = ($price * $sgst_rate) / 100;
$igst_amount = ($price * $igst_rate) / 100;
$gst_amount = ($price * $gst_rate) / 100;
$total = $price + $cgst_amount + $sgst_amount + $igst_amount + $gst_amount;

// New: Read customer address and phone number
$customer_name = $_POST['customer_name'];
$customer_address = $_POST['customer_address'];
$customer_phone = $_POST['customer_phone'];

// Initialize Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf = new Dompdf($options);

// Generate HTML content
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            color: #0066cc;
            margin-bottom: 30px;
        }
        .company-details {
            margin-bottom: 30px;
            position: relative; /* Added for QR code positioning */
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .customer-details {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #0066cc;
            color: white;
        }
        .gst-details {
            margin-bottom: 30px;
        }
        .total {
            text-align: right;
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .terms {
            margin-top: 50px;
            font-size: 0.9em;
        }
        .signature {
            text-align: right;
            margin-top: 50px;
            border-top: 1px solid #000;
            width: 200px;
            float: right;
        }
        .qr-code {
            position: absolute;
            top: 0;
            right: 0;
            margin-right: 20px; /* Adjust as needed */
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SS ENTERPRISES</h1>
    </div>

    <div class="company-details">
        <h3>INVOICE</h3>
        <p>No : 206, Byraveshwara Badavane, Laggere, 1st Main, 4th Cross,<br>
        Near Sharada School, Bangalore - 560 058<br>
        Mob : 9900868607<br>
        State : Karnataka</p>
        <div class="qr-code">
            <img src="' . $qr_code_data . '" alt="QR Code" style="width: 100px; height: 100px;">
        </div>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <td><strong>Invoice No:</strong> '.$_POST['invoice_number'].'</td>
                <td><strong>Date:</strong> '.$_POST['date'].'</td>
            </tr>
            <tr>
                <td><strong>PARTY\'S GSTIN:</strong> '.$_POST['gstin'].'</td>
                <td><strong>State:</strong> Karnataka</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Mode of Transport:</strong> '.($transport ? $transport['name'] : '').'</td>
            </tr>
        </table>
    </div>

    <div class="customer-details">
        <strong>Customer Name:</strong> '.htmlspecialchars($customer_name).'<br>
        <strong>Address:</strong> '.nl2br(htmlspecialchars($customer_address)).'<br>
        <strong>Phone No:</strong> '.htmlspecialchars($customer_phone).'
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Material</th>
                <th>HSN Code</th>
                <th>Quantity</th>
                <th>Price/Unit</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            ' . $html_material_rows . '
        </tbody>
    </table>

    <div class="gst-details">
        <p style="text-align: right;">
            <strong>CGST ('.$cgst_rate.'%):</strong> ₹'.number_format($cgst_amount, 2).'<br>
            <strong>SGST ('.$sgst_rate.'%):</strong> ₹'.number_format($sgst_amount, 2).'<br>
            <strong>IGST ('.$igst_rate.'%):</strong> ₹'.number_format($igst_amount, 2).'<br>
            <strong>GST ('.$gst_rate.'%):</strong> ₹'.number_format($gst_amount, 2).'
        </p>
    </div>

    <div class="total">
        <p>Grand Total: ₹'.number_format($total, 2).'</p>
    </div>

    <div class="terms" style="float: left; width: 50%;">
        <h4>Terms &amp; Conditions:</h4>
        <ol>
            <li>Goods once sold cannot be take back or exchanged.</li>
            <li>Our responsibility ceases immediately the goods is delivery or handed over to the carrier.</li>
            <li>Subject to Bangalore Jurisdiction.</li>
        </ol>
    </div>

    <div class="signature" style="float: right; width: 45%;">
        <p>Receiver\'s Signature with Seal</p>
    </div>
</body>
</html>';

// Load HTML content
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output PDF
$dompdf->stream('Invoice_'.$_POST['invoice_number'].'.pdf', array('Attachment' => false)); 