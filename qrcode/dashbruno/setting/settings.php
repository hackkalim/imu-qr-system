<?php
session_start();


if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../../login.php");
    exit();
}


// 30 minutes in seconds
$timeout_duration = 1800;

if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    if ($elapsed_time > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: ../../login.php?timeout=1");
        exit();
    }
}

// Update last activity time stamp
$_SESSION['last_activity'] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMU QR | Professional Batch System</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="../qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="icon" type="icon" href="../IMG_4043.jpg">
    
    <style>
        :root {
            --primary: #FB8500;
            --primary-hover: #e67a00;
            --dark: #1a1a1a;
            --bg: #f8f9fa;
            --white: #ffffff;
            --success: #2d6a4f;
            --pending: #ffb703;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--dark); }

        /* Navigation Bar */
        header {
            background: var(--dark); color: var(--white);
            padding: 15px 5%; display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 1000;
        }
        .logo { font-size: 22px; font-weight: 800; }
        .logo span { color: var(--primary); }

        nav ul { display: flex; list-style: none; gap: 20px; }
        nav ul li { 
            cursor: pointer; padding: 8px 16px; border-radius: 8px; 
            font-weight: 600; transition: 0.3s; font-size: 14px;
        }
        nav ul li:hover, nav ul li.active { background: var(--primary); color: white; }

        /* Dashboard Layout */
        .dashboard {
            display: grid; grid-template-columns: 350px 1fr;
            gap: 30px; padding: 40px 5%; max-width: 1600px; margin: 0 auto;

            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .dashboard { grid-template-columns: 1fr; }
        }



        :root {
            --primary-orange: #ff9800;
            --primary-hover: #e68900;
            --dark-bg: #1a1a1a;
            --panel-bg: #ffffff;
            --text-main: #333333;
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

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Class to trigger when loading */
    .loading .spinner { display: block; }
    .loading .btn-text { display: none; }



    

/* --- RESPONSIVE ENGINE --- */

/* For Tablets and smaller laptops */
@media (max-width: 1100px) {
    .dashboard {
        grid-template-columns: 1fr; /* Stack Sidebar on top of Table */
    }
    
    header {
        flex-direction: column;
        gap: 15px;
        padding: 15px 2%;
    }
}

/* For Mobile Phones */
@media (max-width: 768px) {
    .container {
        padding: 15px 3%;
    }

    /* Stack the 3 Live Monitor Tables vertically */
    #home .panel div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important; 
        gap: 40px !important;
    }

    /* Make tables scrollable on tiny screens so they don't break the layout */
    .panel {
        padding: 15px;
        overflow-x: auto;
        border-radius: 15px;
    }

    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    /* Adjust Navigation for touch */
    nav ul {
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
    }

    nav ul li {
        font-size: 12px;
        padding: 6px 10px;
    }

    .logo {
        font-size: 18px;
    }

    /* Adjust the Summary Table headers for readability */
    .summary-table th {
        font-size: 10px;
        padding: 8px;
    }
    
    .summary-table td {
        font-size: 12px;
        padding: 8px;
    }
}

/* Touch targets optimization */
button, .nav-link {
    min-height: 44px; /* Standard for mobile fingers */
    display: flex;
    align-items: center;
    justify-content: center;
}
    </style>
</head>
<body>

<header>
    <div class="logo">IMU<span> QR</span></div>
    <nav>
        <ul>
            <a href="../index.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link" onclick="openTab('home')">Home</li></a>
            <a href="../index.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">Simple</li></a>
            <a href="../vip/index.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">VIP</li></a>
            <a href="../vvip/index.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">VVIP</li></a>
            <a href="../print.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">Print</li></a>

            <a href="settings.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link active">Settings</li></a>
            <a href="../logout.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">Logout</li></a>
        </ul>
    </nav>
</header>

<div class="dashboard">
    <div class="login-card">
        <div class="header">
            <h1>Admin <span>System</span></h1>
            <p style="color: #888; font-size: 14px; margin-top: 8px;">Secure Management Admin Portal</p>
        </div>

        <form method="POST">
            <?php if(isset($_GET['error'])): ?>
                <div class="error-box">
                    <strong>Access Denied:</strong> Invalid username or password.
                </div>
            <?php endif; ?>

            <div class="field">
                <label>New Username</label>
                <input type="text" id="new_username" name="username" placeholder="Enter new username" required autocomplete="off">
            </div>

            <div class="field" style="position: relative;">
                <label>New Password</label>
                <div style="position: relative; display: flex; align-items: center;">
                <input type="password" id="new_password" name="password" placeholder="Enter new password" required>
                <button type="button" onclick="togglePass()" style="position: absolute; right: 10px; background: none; border: none; color: #ff9800; font-weight: bold; cursor: pointer;">SHOW</button>
            </div>
            </div>

<button type="submit" class="login-btn" id="loginBtn" onclick="updateAccount()">
    <div class="spinner"></div>
    <span class="btn-text">Update Account</span>
    <span class="loading-text" style="display:none;">Verifying...</span>
</button>
        </form>

</div>




        <div class="login-card">
        <div class="header">
            <h1>User <span>Simple</span></h1>
            <p style="color: #888; font-size: 14px; margin-top: 8px;">Secure Management Admin Portal</p>
        </div>

        <form method="POST">
            <?php if(isset($_GET['error'])): ?>
                <div class="error-box">
                    <strong>Access Denied:</strong> Invalid username or password.
                </div>
            <?php endif; ?>

            <div class="field">
                <label>New Username</label>
                <input type="text" id="new_username_simple" name="username" placeholder="Enter new username" required autocomplete="off">
            </div>

            <div class="field" style="position: relative;">
                <label>New Password</label>
                <div style="position: relative; display: flex; align-items: center;">
                <input type="password" id="new_password_simple" name="password" placeholder="Enter new password" required>
                <button type="button" onclick="togglePassSimple()" style="position: absolute; right: 10px; background: none; border: none; color: #ff9800; font-weight: bold; cursor: pointer;">SHOW</button>
            </div>
            </div>

<button type="submit" class="login-btn" id="loginBtn" onclick="updateAccountSimpleUsers()">
    <div class="spinner"></div>
    <span class="btn-text">Update Account</span>
    <span class="loading-text" style="display:none;">Verifying...</span>
</button>
        </form>

</div>




        <div class="login-card">
        <div class="header">
            <h1>User <span>Vip</span></h1>
            <p style="color: #888; font-size: 14px; margin-top: 8px;">Secure Management Admin Portal</p>
        </div>

        <form method="POST">
            <?php if(isset($_GET['error'])): ?>
                <div class="error-box">
                    <strong>Access Denied:</strong> Invalid username or password.
                </div>
            <?php endif; ?>

            <div class="field">
                <label>New Username</label>
                <input type="text" id="new_username_vip" name="username" placeholder="Enter new username" required autocomplete="off">
            </div>

            <div class="field" style="position: relative;">
                <label>New Password</label>
                <div style="position: relative; display: flex; align-items: center;">
                <input type="password" id="new_password_vip" name="password" placeholder="Enter new password" required>
                <button type="button" onclick="togglePassVip()" style="position: absolute; right: 10px; background: none; border: none; color: #ff9800; font-weight: bold; cursor: pointer;">SHOW</button>
            </div>
            </div>

<button type="submit" class="login-btn" id="loginBtn" onclick="updateAccountVipUsers()">
    <div class="spinner"></div>
    <span class="btn-text">Update Account</span>
    <span class="loading-text" style="display:none;">Verifying...</span>
</button>
        </form>

</div>


        <div class="login-card">
        <div class="header">
            <h1>User <span>Vvip</span></h1>
            <p style="color: #888; font-size: 14px; margin-top: 8px;">Secure Management Admin Portal</p>
        </div>

        <form method="POST">
            <?php if(isset($_GET['error'])): ?>
                <div class="error-box">
                    <strong>Access Denied:</strong> Invalid username or password.
                </div>
            <?php endif; ?>

            <div class="field">
                <label>New Username</label>
                <input type="text" id="new_username_vvip" name="username" placeholder="Enter new username" required autocomplete="off">
            </div>

            <div class="field" style="position: relative;">
                <label>New Password</label>
                <div style="position: relative; display: flex; align-items: center;">
                <input type="password" id="new_password_vvip" name="password" placeholder="Enter new password" required>
                <button type="button" onclick="togglePassVvip()" style="position: absolute; right: 10px; background: none; border: none; color: #ff9800; font-weight: bold; cursor: pointer;">SHOW</button>
            </div>
            </div>

<button type="submit" class="login-btn" id="loginBtn" onclick="updateAccountVvipUsers()">
    <div class="spinner"></div>
    <span class="btn-text">Update Account</span>
    <span class="loading-text" style="display:none;">Verifying...</span>
</button>
        </form>
    </div>

</div>

    <script>
        function togglePass() {
    const p = document.getElementById('new_password');
    const btn = event.target;
    if (p.type === "password") {
        p.type = "text";
        btn.innerText = "HIDE";
    } else {
        p.type = "password";
        btn.innerText = "SHOW";
    }
}

function togglePassSimple() {
    const p = document.getElementById('new_password_simple');
    const btn = event.target;
    if (p.type === "password") {
        p.type = "text";
        btn.innerText = "HIDE";
    } else {
        p.type = "password";
        btn.innerText = "SHOW";
    }
}

function togglePassVip() {
    const p = document.getElementById('new_password_vip');
    const btn = event.target;
    if (p.type === "password") {
        p.type = "text";
        btn.innerText = "HIDE";
    } else {
        p.type = "password";
        btn.innerText = "SHOW";
    }
}

function togglePassVvip() {
    const p = document.getElementById('new_password_vvip');
    const btn = event.target;
    if (p.type === "password") {
        p.type = "text";
        btn.innerText = "HIDE";
    } else {
        p.type = "password";
        btn.innerText = "SHOW";
    }
}


    document.querySelector('form').onsubmit = function() {
        const btn = document.getElementById('loginBtn');
        const btnText = btn.querySelector('.btn-text');
        const loadingText = btn.querySelector('.loading-text');
        
        btn.classList.add('loading');
        btn.disabled = true; // Prevent double submit
        btnText.style.display = 'none';
        loadingText.style.display = 'inline';
    };


    function updateAccount() {
    const user = document.getElementById('new_username').value;
    const pass = document.getElementById('new_password').value;

    if(!user || !pass) return alert("Fields cannot be empty");

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_admin.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.responseText.trim() === "success") {
            alert("Credentials updated successfully!");
        }
    };
    xhr.send("new_user=" + encodeURIComponent(user) + "&new_pass=" + encodeURIComponent(pass));
}

function updateAccountSimpleUsers() {
    const user = document.getElementById('new_username_simple').value;
    const pass = document.getElementById('new_password_simple').value;

    if(!user || !pass) return alert("Fields cannot be empty");

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_admin_simple_users.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.responseText.trim() === "success") {
            alert("Credentials updated successfully!");
        }
    };
    xhr.send("new_user=" + encodeURIComponent(user) + "&new_pass=" + encodeURIComponent(pass));
}



    function updateAccountVipUsers() {
    const user = document.getElementById('new_username_vip').value;
    const pass = document.getElementById('new_password_vip').value;

    if(!user || !pass) return alert("Fields cannot be empty");

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_admin_vip_users.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.responseText.trim() === "success") {
            alert("Credentials updated successfully!");
        }
    };
    xhr.send("new_user=" + encodeURIComponent(user) + "&new_pass=" + encodeURIComponent(pass));
}




    function updateAccountVvipUsers() {
    const user = document.getElementById('new_username_vvip').value;
    const pass = document.getElementById('new_password_vvip').value;

    if(!user || !pass) return alert("Fields cannot be empty");

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_admin_vvip_users.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.responseText.trim() === "success") {
            alert("Credentials updated successfully!");
        }
    };
    xhr.send("new_user=" + encodeURIComponent(user) + "&new_pass=" + encodeURIComponent(pass));
}
</script>

</body>
</html>