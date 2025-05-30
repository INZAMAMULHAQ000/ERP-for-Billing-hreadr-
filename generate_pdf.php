<?php
session_start();
require_once "config/database.php";
require_once 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

if(!isset($_SESSION['loggedin'])) {
    header("location: index.php");
    exit;
}

// Fetch material details
$material_id = $_POST['material'];
$material_query = "SELECT name FROM materials WHERE id = '$material_id'";
$material_result = mysqli_query($conn, $material_query);
$material = mysqli_fetch_assoc($material_result);

$quantity = $_POST['quantity'];
$price = $_POST['price'];
$gst_rate = $_POST['gst_rate'];
$gst_amount = ($price * $gst_rate) / 100;
$total = $price + $gst_amount;

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
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
    </div>

    <div class="company-details">
        <h3>Your Company Name</h3>
        <p>Address Line 1<br>
        City, State - PIN<br>
        Phone: +91-XXXXXXXXXX<br>
        Email: company@example.com</p>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <td><strong>Invoice No:</strong> '.$_POST['invoice_number'].'</td>
                <td><strong>Date:</strong> '.$_POST['date'].'</td>
            </tr>
            <tr>
                <td><strong>Customer Name:</strong> '.$_POST['customer_name'].'</td>
                <td><strong>GSTIN:</strong> '.$_POST['gstin'].'</td>
            </tr>
            <tr>
                <td colspan="2"><strong>State:</strong> '.$_POST['state'].'</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Material</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>GST</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>'.$material['name'].'</td>
                <td>'.$quantity.'</td>
                <td>₹'.number_format($price, 2).'</td>
                <td>₹'.number_format($gst_amount, 2).'</td>
                <td>₹'.number_format($total, 2).'</td>
            </tr>
        </tbody>
    </table>

    <div class="gst-details">';

if($_POST['gst_type'] == 'sgst_cgst') {
    $sgst = $gst_amount / 2;
    $html .= '
        <p style="text-align: right;">
            <strong>SGST ('.($gst_rate/2).'%):</strong> ₹'.number_format($sgst, 2).'<br>
            <strong>CGST ('.($gst_rate/2).'%):</strong> ₹'.number_format($sgst, 2).'
        </p>';
} else {
    $html .= '
        <p style="text-align: right;">
            <strong>IGST ('.$gst_rate.'%):</strong> ₹'.number_format($gst_amount, 2).'
        </p>';
}

$html .= '
    </div>

    <div class="total">
        <p>Grand Total: ₹'.number_format($total, 2).'</p>
    </div>

    <div class="terms">
        <h4>Terms & Conditions:</h4>
        <ol>
            <li>Goods once sold will not be taken back.</li>
            <li>Interest will be charged @24% p.a. if the payment is not made within the stipulated time.</li>
            <li>Subject to local jurisdiction.</li>
            <li>E. & O.E.</li>
        </ol>
    </div>

    <div class="signature">
        <p>Authorized Signature</p>
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