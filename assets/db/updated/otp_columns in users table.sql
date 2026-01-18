ALTER TABLE users ADD otp_code NVARCHAR(10) NULL;
-- This column will store the timestamp when the generated OTP expires.
ALTER TABLE users ADD otp_expiry DATETIME NULL;
-- This column will store the timestamp of the last successful OTP verification for a user.
ALTER TABLE users ADD last_otp_verified_at DATETIME NULL;
-- This column will store a unique hash representing the device/browser used for the last login.
-- Length 255 is standard for storing MD5 or SHA256 hashes.
ALTER TABLE users
ADD last_login_device_fingerprint NVARCHAR(255) NULL;