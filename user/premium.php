<?php
session_start();
require 'db_conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?redirect=user/premium.php');
    exit();
}

// Handle premium upgrade
if (isset($_POST['upgrade_plan'])) {
    $user_id = $_SESSION['user_id'];
    
    // Simple update query for isPremium only
    $update_query = "UPDATE users SET isPremium = 1 WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['isPremium'] = 1;
        $_SESSION['show_success_modal'] = true;
        header('Location: premium.php');
        exit();
    } else {
        $_SESSION['error_message'] = "Error upgrading to premium. Please try again.";
        header('Location: premium.php');
        exit();
    }
}

// Get user details
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .premium-header {
            background-color: #365486;
            color: white;
            text-align: center;
            padding: 40px 20px;
            margin-bottom: 40px;
        }

        .premium-header h1 {
            margin: 0;
            font-size: 2.5em;
        }

        .premium-header p {
            margin: 10px 0 0;
            font-size: 1.2em;
            opacity: 0.9;
        }

        .upgrade-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .benefits-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .benefit-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .benefit-card i {
            font-size: 40px;
            color: #365486;
            margin-bottom: 20px;
        }

        .benefit-card h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .plan-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .plan-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            text-align: center;
            position: relative;
            transition: transform 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-5px);
        }

        .plan-card.recommended {
            border: 3px solid #365486;
        }

        .recommended-badge {
            position: absolute;
            top: -12px;
            right: -12px;
            background: #365486;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        .plan-price {
            font-size: 36px;
            font-weight: bold;
            color: #365486;
            margin: 20px 0;
        }

        .plan-price span {
            font-size: 16px;
            color: #666;
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 25px 0;
        }

        .plan-features li {
            margin: 15px 0;
            color: #555;
            font-size: 16px;
        }

        .plan-features i {
            color: #28a745;
            margin-right: 10px;
        }

        .select-plan-btn {
            background: #365486;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 18px;
            transition: background 0.3s;
        }

        .select-plan-btn:hover {
            background: #2a4170;
        }

        .payment-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 40px 0;
            text-align: center;
        }

        .payment-icons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin: 20px 0;
        }

        .payment-icons i {
            font-size: 40px;
            color: #365486;
        }

        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #28a745;
            font-size: 16px;
            margin-top: 20px;
        }

        .faq-section {
            margin: 40px 0;
        }

        .faq-item {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .faq-question {
            font-weight: bold;
            color: #365486;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .premium-header {
                padding: 30px 15px;
            }

            .premium-header h1 {
                font-size: 2em;
            }

            .plan-options {
                grid-template-columns: 1fr;
            }
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .modal.show {
            opacity: 1;
        }

        .modal-content {
            position: relative;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.7);
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }

        .modal.show .modal-content {
            transform: translate(-50%, -50%) scale(1);
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
            ?>
        </div>
    <?php endif; ?>

    <div class="premium-header">
        <h1>Upgrade to Premium</h1>
        <p>Unlock the full potential of your learning experience</p>
    </div>

    <div class="upgrade-container">
        <div class="benefits-section">
            <div class="benefit-card">
                <i class="fas fa-palette"></i>
                <h3>Enhanced Design Tools</h3>
                <p>Access premium templates and customization options</p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-chart-line"></i>
                <h3>Advanced Analytics</h3>
                <p>Gain deeper insights into your learning progress</p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-crown"></i>
                <h3>Exclusive Content</h3>
                <p>Access premium learning materials and resources</p>
            </div>
        </div>

        <div class="plan-options">
            <div class="plan-card">
                <h2>Monthly</h2>
                <div class="plan-price">₱450<span>/month</span></div>
                <ul class="plan-features">
                    <li><i class="fas fa-check"></i> All Premium Features</li>
                    <li><i class="fas fa-check"></i> Priority Support</li>
                    <li><i class="fas fa-check"></i> Monthly Analytics</li>
                </ul>
                <form method="POST">
                    <input type="hidden" name="plan_type" value="monthly">
                    <button type="submit" name="upgrade_plan" class="select-plan-btn">
                        Get Premium Access
                    </button>
                </form>
            </div>

            <div class="plan-card recommended">
                <span class="recommended-badge">BEST VALUE</span>
                <h2>Annual</h2>
                <div class="plan-price">₱4,500<span>/year</span></div>
                <p class="savings">Save 17%</p>
                <ul class="plan-features">
                    <li><i class="fas fa-check"></i> All Premium Features</li>
                    <li><i class="fas fa-check"></i> Priority Support</li>
                    <li><i class="fas fa-check"></i> Advanced Analytics</li>
                    <li><i class="fas fa-check"></i> Exclusive Workshops</li>
                </ul>
                <form method="POST">
                    <input type="hidden" name="plan_type" value="annual">
                    <button type="submit" name="upgrade_plan" class="select-plan-btn">
                        Get Premium Access
                    </button>
                </form>
            </div>
        </div>

        <div class="payment-section">
            <h2>Secure Payment Methods</h2>
            <div class="payment-icons">
                <i class="fab fa-cc-visa"></i>
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-cc-paypal"></i>
                <i class="fab fa-cc-stripe"></i>
            </div>
            <div class="secure-badge">
                <i class="fas fa-lock"></i>
                <span>Your payment information is secure</span>
            </div>
        </div>

    </div>

    <!-- Success Modal -->
    <div class="modal" id="successModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 30px; border-radius: 10px; max-width: 400px; text-align: center;">
            <i class="fas fa-check-circle" style="font-size: 50px; color: #28a745; margin-bottom: 20px;"></i>
            <h2>Congratulations!</h2>
            <p>You have successfully upgraded to Premium!</p>
            <button onclick="window.location.href='explore.php'" class="select-plan-btn" style="margin-top: 20px;">
                Continue to Explore
            </button>
        </div>
    </div>

    <script>
    <?php if (isset($_SESSION['show_success_modal'])): ?>
        const modal = document.getElementById('successModal');
        modal.style.display = 'block';
        // Trigger reflow
        modal.offsetHeight;
        modal.classList.add('show');
        <?php unset($_SESSION['show_success_modal']); ?>
    <?php endif; ?>
    </script>
</body>
</html>