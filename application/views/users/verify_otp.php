<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Use min-height to allow content to expand */
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .otp-container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
            margin: auto;
        }
        .otp-container h2 {
            margin-bottom: 25px;
            color: #333;
            font-size: 24px;
            font-weight: 600;
        }
        .otp-container p {
            margin-bottom: 25px;
            color: #555;
            font-size: 15px;
            line-height: 1.6;
        }
        .otp-container input[type="text"] {
            width: calc(100% - 20px); /* Adjust for padding */
            padding: 12px 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            text-align: center;
            letter-spacing: 2px;
        }
        .otp-container button {
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            width: 100%;
            margin-bottom: 15px;
        }
        .otp-container button:hover:not(:disabled) {
            background-color: #0056b3;
        }
        .otp-container button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .resend-section {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
        #resendOtpButton {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            padding: 0;
            font-size: 14px;
            text-decoration: underline;
            margin-left: 5px;
            display: inline; /* To ensure it stays in line with text */
            width: auto; /* Override 100% width from general button style */
        }
        #resendOtpButton:hover:not(:disabled) {
            color: #0056b3;
        }
        #resendOtpButton:disabled {
            color: #cccccc;
            cursor: not-allowed;
            text-decoration: none;
        }
        .message {
            margin-top: 15px;
            font-size: 14px;
            color: #333;
            min-height: 20px; /* Reserve space for messages */
        }
        .error-message {
            color: #dc3545; /* Red color for errors */
            font-weight: 500;
            margin-bottom: 15px;
        }
        .success-message {
            color: #28a745; /* Green color for success */
            font-weight: 500;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="otp-container">
    <h2>Two-Factor Authentication</h2>

       <p>A One-Time Password (OTP) has been sent to your registered email/phone number. Please enter it below to verify your login.</p>

    <form id="otpForm" action="<?= base_url('users/verify_otp') ?>" method="post">
        <input type="text" id="otp_input" name="otp" placeholder="Enter OTP" maxlength="6" inputmode="numeric" pattern="[0-9]*" required autocomplete="off">
        <button type="submit" id="verifyOtpButton" disabled>Verify OTP</button>
    </form>

    <div class="resend-section">
        <?php if (isset($error_message) && $error_message): ?>
            <p id="errorMessage" class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php else: ?>
            <p id="errorMessage" class="error-message" style="display: none;"></p>
        <?php endif; ?>
        <span id="dynamicMessage" class="message"></span><br>
        Didn't receive the OTP?
        <button id="resendOtpButton" disabled>Resend OTP</button>
        <span id="countdownDisplay"></span>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const otpInput = document.getElementById('otp_input');
        const verifyOtpButton = document.getElementById('verifyOtpButton');
        const resendOtpButton = document.getElementById('resendOtpButton');
        const countdownDisplay = document.getElementById('countdownDisplay');
        const otpForm = document.getElementById('otpForm');
        const dynamicMessage = document.getElementById('dynamicMessage');
        const errorMessage = document.getElementById('errorMessage');

        // time in seconds
        let otpExpiryTimestamp = <?php echo $otp_expiry_timestamp??0 ?>; // Default to 0 if not set

        let countdownInterval;
        let resendCooldown = 60; // Cooldown period for resend button in seconds

        function updateCountdown() {
            const now = Math.floor(Date.now() / 1000); // Current time in seconds
            const timeLeft = otpExpiryTimestamp - now;

            if (timeLeft > 0) {
                countdownDisplay.textContent = ` (resend in ${timeLeft}s)`;
                resendOtpButton.disabled = true;
            } else {
                clearInterval(countdownInterval);
                countdownDisplay.textContent = '';
                resendOtpButton.disabled = false;
                dynamicMessage.textContent = 'OTP has expired. Please resend.';
                dynamicMessage.className = 'message error-message';
            }
        }

        function startCountdown() {
            // Only start if a valid expiry timestamp is provided
            if (otpExpiryTimestamp > Math.floor(Date.now() / 1000)) {
                updateCountdown(); // Initial update
                countdownInterval = setInterval(updateCountdown, 1000);
            } else {
                resendOtpButton.disabled = false; // Enable resend if OTP already expired or not set
                countdownDisplay.textContent = '';
                dynamicMessage.textContent = 'OTP has expired or not available. Please resend.';
                dynamicMessage.className = 'message error-message';
            }
        }

        function toggleVerifyButton() {
            verifyOtpButton.disabled = otpInput.value.trim().length === 0;
        }

        // Handle OTP form submission via AJAX
        otpForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            // Disable buttons and show loading message
            verifyOtpButton.disabled = true;
            resendOtpButton.disabled = true;
            dynamicMessage.textContent = 'Verifying OTP...';
            dynamicMessage.className = 'message';
            errorMessage.style.display = 'none'; // Hide previous error

            const otp = otpInput.value.trim();

            // Perform AJAX request
            fetch('<?= base_url('users/verify_otp') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest' // Identify as AJAX request for CodeIgniter's is_ajax_request()
                },
                body: `otp=${encodeURIComponent(otp)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        dynamicMessage.textContent = data.message;
                        dynamicMessage.className = 'message success-message';
                        // Redirect on success
                        setTimeout(() => {
                            window.location.href = data.redirect || '<?= base_url('dashboard') ?>';
                        }, 1000);
                    } else {
                        errorMessage.textContent = data.message;
                        errorMessage.style.display = 'block'; // Show error message
                        dynamicMessage.textContent = ''; // Clear dynamic message
                        dynamicMessage.className = 'message'; // Reset class
                        verifyOtpButton.disabled = false; // Re-enable verify button
                        resendOtpButton.disabled = (otpExpiryTimestamp > Math.floor(Date.now() / 1000)); // Only re-enable resend if countdown finished
                    }
                })
                .catch(error => {
                    console.error('Error during OTP verification:', error);
                    errorMessage.textContent = 'An unexpected error occurred. Please try again.';
                    errorMessage.style.display = 'block';
                    dynamicMessage.textContent = '';
                    dynamicMessage.className = 'message';
                    verifyOtpButton.disabled = false;
                    resendOtpButton.disabled = (otpExpiryTimestamp > Math.floor(Date.now() / 1000));
                });
        });

        // Handle Resend OTP button click
        resendOtpButton.addEventListener('click', function() {
            resendOtpButton.disabled = true; // Disable immediately
            verifyOtpButton.disabled = true;
            dynamicMessage.textContent = 'Resending OTP...';
            dynamicMessage.className = 'message';
            errorMessage.style.display = 'none'; // Hide previous error

            fetch('<?= base_url('users/resend_otp') ?>', { // New endpoint for resending OTP
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', // Using JSON for resend for simplicity
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        dynamicMessage.textContent = data.message || 'New OTP sent!';
                        dynamicMessage.className = 'message success-message';
                        // Update the expiry timestamp for the new OTP
                        otpExpiryTimestamp = data.new_expiry_timestamp; // iko shida hapa
                        clearInterval(countdownInterval); // Clear old interval
                        startCountdown(); // Start new countdown
                        otpInput.value = ''; // Clear OTP input
                        toggleVerifyButton(); // Update verify button state
                    } else {
                        errorMessage.textContent = data.message || 'Failed to resend OTP. Please try again.';
                        errorMessage.style.display = 'block';
                        dynamicMessage.textContent = '';
                        dynamicMessage.className = 'message';
                        // Re-enable resend button based on cooldown or original expiry if resend failed
                        resendOtpButton.disabled = (otpExpiryTimestamp > Math.floor(Date.now() / 1000));
                    }
                    verifyOtpButton.disabled = (otpInput.value.trim().length === 0); // Re-evaluate verify button state
                })
                .catch(error => {
                    console.error('Error during OTP resend:', error);
                    errorMessage.textContent = 'An unexpected error occurred during resend. Please try again.';
                    errorMessage.style.display = 'block';
                    dynamicMessage.textContent = '';
                    dynamicMessage.className = 'message';
                    resendOtpButton.disabled = (otpExpiryTimestamp > Math.floor(Date.now() / 1000));
                    verifyOtpButton.disabled = (otpInput.value.trim().length === 0);
                });
        });

        // Initial setup
        otpInput.addEventListener('input', toggleVerifyButton);
        toggleVerifyButton(); // Set initial state of verify button
        startCountdown(); // Start the countdown on page load
    });
</script>
</body>
</html>