
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt Demo with QR Code</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 20px auto;
            border: 1px solid #ccc;
            padding: 15px;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 15px;
        }
        .receipt-items {
            margin-bottom: 15px;
        }
        .receipt-items table {
            width: 100%;
            border-collapse: collapse;
        }
        .receipt-items th, .receipt-items td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: left;
        }
        .totals {
            margin-top: 10px;
            text-align: right;
        }
        #qrCode {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="receipt-header">
        <h2>Sample Pawn Receipt</h2>
        <p>Branch: Main Branch</p>
        <p>Date: 2025-09-03</p>
    </div>

    <div class="receipt-items">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Gold Ring</td>
                    <td>₱5,000.00</td>
                </tr>
                <tr>
                    <td>Necklace</td>
                    <td>₱3,500.00</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="totals">
        <p>Total Pawned: ₱8,500.00</p>
        <p>Interest: ₱500.00</p>
        <p>Penalty: ₱50.00</p>
        <p>Total Paid: ₱9,050.00</p>
    </div>

    <!-- QR Code container -->
    <div id="qrCode"></div>

    <script>
        // URL of Facebook page
        let fbURL = "https://www.facebook.com/YourPageName";

        // Generate QR code
        new QRCode(document.getElementById("qrCode"), {
            text: fbURL,
            width: 100,
            height: 100,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>

