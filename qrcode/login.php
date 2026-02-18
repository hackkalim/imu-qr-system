<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) { header("Location: dashbruno/index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMU Admin Access | Login</title>
    <style>
        :root {
            --primary-orange: #ff9800;
            --primary-hover: #e68900;
            --dark-bg: #1a1a1a;
            --panel-bg: #ffffff;
            --text-main: #333333;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            /* Subtle background pattern to match high-end dashboards */
            background-image: radial-gradient(#ff9800 0.5px, transparent 0.5px);
            background-size: 30px 30px;
            background-opacity: 0.05;
        }

        .login-card {
            background: var(--panel-bg);
            width: 100%;
            max-width: 380px;
            padding: 45px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-top: 5px solid var(--primary-orange);
        }

        .header {
            margin-bottom: 35px;
            text-align: center;
        }

        .header h1 {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header span {
            color: var(--primary-orange);
        }

        .field {
            margin-bottom: 25px;
        }

        .field label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #666;
            text-transform: uppercase;
        }

        .field input {
            width: 100%;
            padding: 14px;
            border: 2px solid #eee;
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 15px;
            background: #fafafa;
            transition: all 0.3s ease;
        }

        .field input:focus {
            outline: none;
            border-color: var(--primary-orange);
            background: #fff;
            box-shadow: 0 0 8px rgba(255, 152, 0, 0.2);
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: var(--primary-orange);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
        }

        .login-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(255, 152, 0, 0.4);
        }

        .error-box {
            background: #fff5f5;
            color: #d9534f;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            border-left: 4px solid #d9534f;
            text-align: left;
        }


    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Class to trigger when loading */
    .loading .spinner { display: block; }
    .loading .btn-text { display: none; }



    .login-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .spinner {
        display: none; /* Hidden by default */
        width: 18px;
        height: 18px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 0.8s linear infinite;
    }

    .spinner {
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 0.8s linear infinite;
        display: none; /* Hidden by default */
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .btn-loading {
        background: #e68900 !important;
        cursor: not-allowed;
    }
    </style>
</head>
<body>

    <div class="login-card">

        <?php if(isset($_GET['timeout'])): ?>
    <div style="background: #fff3e0; color: #e65100; padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; border: 1px solid #ffe0b2;">
        <strong>Session Expired:</strong> You have been logged out for security.
    </div>
<?php endif; ?>


        <div class="header">
            <h1>IMU <span>QR</span></h1>
            <p style="color: #888; font-size: 14px; margin-top: 8px;">Secure Admin Portal</p>
        </div>

        <form action="process_login.php" method="POST">
            <?php if(isset($_GET['error'])): ?>
                <div class="error-box">
                    <strong>Access Denied:</strong> Invalid username or password.
                </div>
            <?php endif; ?>

            <div class="field">
                <label>Admin Username</label>
                <input type="text" name="username" placeholder="Enter username" required autocomplete="off">
            </div>

            <div class="field" style="position: relative;">
                <label>System Password</label>
                <div style="position: relative; display: flex; align-items: center;">
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <button type="button" onclick="togglePass()" style="position: absolute; right: 10px; background: none; border: none; color: #ff9800; font-weight: bold; cursor: pointer;">SHOW</button>
            </div>
            </div>

<button type="submit" class="login-btn" id="loginBtn">
    <div class="spinner" id="btnSpinner"></div>
    <span id="btnText">AUTHENTICATE</span>
</button>
        </form>
    </div>


    <script>
    document.querySelector('form').addEventListener('submit', function() {
        const btn = document.getElementById('loginBtn');
        const text = document.getElementById('btnText');
        const spinner = document.getElementById('btnSpinner');

        btn.classList.add('btn-loading');
        text.innerText = "VERIFYING...";
        spinner.style.display = "block";
    });

    function togglePass() {
    const p = document.getElementById('password');
    const btn = event.target;
    if (p.type === "password") {
        p.type = "text";
        btn.innerText = "HIDE";
    } else {
        p.type = "password";
        btn.innerText = "SHOW";
    }
}
</script>

</body>
</html>