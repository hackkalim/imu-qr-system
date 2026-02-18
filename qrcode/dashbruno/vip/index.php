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
    <script src="qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="icon" type="icon" href="IMG_4043.jpg">
    
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
        }

        /* Panels */
        .panel {
            background: var(--white); border-radius: 24px; padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.05);
        }

        .panel-title { font-size: 18px; font-weight: 800; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        .panel-title::before { content: ''; width: 4px; height: 20px; background: var(--primary); border-radius: 10px; }

        /* Inputs & Buttons */
        .field { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; opacity: 0.7; }
        input {
            width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e0e0e0;
            font-family: inherit; font-size: 14px; transition: 0.3s;
        }
        input:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 4px rgba(251, 133, 0, 0.1); }

        .btn-main {
            width: 100%; background: var(--dark); color: white; border: none; padding: 15px;
            border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;
        }
        .btn-main:hover { background: var(--primary); transform: translateY(-2px); }

        /* Table Design */
        .search-container { position: relative; margin-bottom: 25px; }
        .search-box { padding-left: 45px; background: #f1f1f1; border: none; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: #888; border-bottom: 2px solid #f4f4f4; }
        td { padding: 18px 15px; font-size: 14px; border-bottom: 1px solid #f4f4f4; }

        /* Status & Actions */
        .badge {
            padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; text-transform: uppercase;
        }
        .badge.pending { background: #fff8e1; color: #b7791f; }
        .badge.verified { background: #e8f5e9; color: #2e7d32; }

        .btn-check {
            background: var(--primary); color: white; border: none; padding: 6px 14px;
            border-radius: 8px; cursor: pointer; font-size: 12px; font-weight: 700; transition: 0.2s;
        }
        .btn-check:hover { background: var(--dark); }

        #status-msg { margin-top: 15px; font-size: 12px; font-weight: 600; color: var(--primary); }

        /* Responsive */
        @media (max-width: 900px) {
            .dashboard { grid-template-columns: 1fr; }
        }

        /* Status Badges */
.badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    display: inline-block;
}

.badge.pending {
    background: #FFF4E5;
    color: #FF8800; /* Orange for Pending */
}

.badge.verified {
    background: #E8F5E9;
    color: #2D6A4F; /* Green for Verified */
}

/* The Checkout Button */
.btn-check {
    background: var(--dark);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.2s;
}

.btn-check:hover {
    background: var(--primary); /* Turns Orange on hover */
    transform: scale(1.05);
}


.btn-check {
    background: #FB8500; /* Akejo Orange */
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
}

.btn-check:hover {
    background: #2b1d14; /* Dark brown on hover */
    transform: translateY(-2px);
}

.badge.pending { background: #fff3e0; color: #ef6c00; padding: 5px 10px; border-radius: 5px; }
.badge.verified { background: #e8f5e9; color: #2e7d32; padding: 5px 10px; border-radius: 5px; }

.btn-tool {
    padding: 10px 18px;
    border-radius: 10px;
    border: none;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
}
.btn-csv { background: #e3f2fd; color: #1976d2; }
.btn-csv:hover { background: #1976d2; color: white; }

.btn-pdf { background: #f3e5f5; color: #7b1fa2; }
.btn-pdf:hover { background: #7b1fa2; color: white; }

.btn-danger { background: #ffebee; color: #d32f2f; margin-left: auto; }
.btn-danger:hover { background: #d32f2f; color: white; }

/* Hide generator panel when printing PDF */
@media print {
    header, aside, .admin-actions, .search-container, .btn-check { display: none !important; }
    .panel { box-shadow: none; border: none; }
    main { width: 100% !important; margin: 0; padding: 0; }
}





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
            <a href="../simple/index.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">Simple</li></a>
            <a href="index.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link active">VIP</li></a>
            <a href="../vvip/index.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">VVIP</li></a>
            <a href="../print.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">Print</li></a>
            <a href="../setting/settings.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">Settings</li></a>
            <a href="../logout.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">Logout</li></a>
        </ul>
    </nav>
</header>

<div class="dashboard">
    <aside class="panel">
    <div class="panel-title">Batch Generator ~ Vip</div>
    
    <div class="field">
        <label>1. Select Base Photo</label>
        <input type="file" id="imageInput" accept="image/*">
    </div>

    <div class="field">
        <label>2. Position QR Code (Click on image)</label>
        <div id="preview-container" style="width: 100%; border-radius: 12px; overflow: hidden; background: #eee; cursor: crosshair; position: relative;">
            <canvas id="previewCanvas" style="width: 100%; display: block;"></canvas>
            <div id="qr-guide" style="position: absolute; border: 2px dashed var(--primary); background: rgba(251, 133, 0, 0.2); pointer-events: none; display: none;"></div>
        </div>
        <p style="font-size: 10px; margin-top: 5px; color: #888;">*The square shows where the QR will be placed.</p>
    </div>

    <div class="field">
        <label>3. Quantity</label>
        <input type="number" id="duplicateCount" placeholder="Amount...">
    </div>

    <div class="field">
    <label>4. Ticket Price ($)</label>
    <input type="number" id="ticketPrice" placeholder="Enter amount (e.g. 50)" value="">
</div>

    <button class="btn-main" onclick="startGeneration()">Generate & Download</button>
    <div id="status-msg">System Idle</div>
</aside>

    <main class="panel">
        <div class="panel-title">QR Records Management</div>

        <div class="admin-actions" style="margin-bottom: 20px; display: flex; gap: 10px;">
    <button class="btn-tool btn-csv" onclick="exportCSV()">📁 Export CSV</button>
    <button class="btn-tool btn-pdf" onclick="window.print()">📄 Print / PDF</button>
    <button class="btn-tool btn-danger" onclick="clearAll()">🗑️ Clear All Records</button>
</div>
        
        <div class="search-container">
            <input type="text" id="searchInput" class="search-box" placeholder="Search QR code..." onkeyup="loadData()">
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>QR Code</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                </tbody>
        </table>
    </main>
</div>

<div id="qrcode-hidden" style="display:none;"></div>
<canvas id="canvas" style="display:none;"></canvas>

<script>
    let currentCount = 0;
    let totalNeeded = 0;

    // --- GENERATION LOGIC ---
    async function startGeneration() {
        const file = document.getElementById('imageInput').files[0];
        totalNeeded = parseInt(document.getElementById('duplicateCount').value);
        
        if (!file || !totalNeeded) {
            alert("Please select an image and quantity.");
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => processLoop(img);
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function processLoop(baseImg) {
    if (currentCount >= totalNeeded) {
        document.getElementById('status-msg').innerText = "✅ Batch Complete!";
        loadData(); // Refresh table
        return;
    }

    currentCount++;
    
    // 1. Generate the random number
    const randomNumber = Math.floor(Math.random() * 9000000) + 1000000;
    
    // 2. Add the "v" prefix to create the final QR Content
    const uniqueID = "v" + randomNumber; 
    
    document.getElementById('status-msg').innerText = `Progress: ${currentCount}/${totalNeeded}`;

    // Clear and generate the visual QR code with the "v" included
    document.getElementById('qrcode-hidden').innerHTML = "";
    new QRCode(document.getElementById("qrcode-hidden"), { 
        text: uniqueID, // This now contains "v1234567"
        width: 200, 
        height: 200 
    });

    setTimeout(() => {
        const qrImg = document.querySelector('#qrcode-hidden img');
        
        // Draw the image with the "v" ID and download
        drawAndDownload(baseImg, qrImg, uniqueID);
        
        // Save the "v" version to your database
        saveToDatabase(uniqueID);
        
        setTimeout(() => processLoop(baseImg), 1000);
    }, 400);
}

    function drawAndDownload(baseImg, qrImg, id) {
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = baseImg.width; canvas.height = baseImg.height;
        ctx.drawImage(baseImg, 0, 0);

        const qrSize = canvas.width * 0.18;
        ctx.fillStyle = "white";
        ctx.fillRect(canvas.width - qrSize - 30, canvas.height - qrSize - 30, qrSize, qrSize);
        ctx.drawImage(qrImg, canvas.width - qrSize - 20, canvas.height - qrSize - 20, qrSize - 20, qrSize - 20);

        const link = document.createElement('a');
        link.download = `QR_${id}.png`;
        link.href = canvas.toDataURL();
        link.click();
    }

// --- UPDATED AJAX MANAGEMENT LOGIC ---
function saveToDatabase(qrValue) {
    // 1. Get the price from your input field
    const priceValue = document.getElementById('ticketPrice').value;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "save_qr.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    // 2. Send BOTH qrcode and price
    xhr.send("qrcode=" + qrValue + "&price=" + encodeURIComponent(priceValue));
}

    function loadData() {
        const query = document.getElementById('searchInput').value;
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "fetch_data.php?search=" + query, true);
        xhr.onload = function() {
            if (this.status == 200) document.getElementById('tableBody').innerHTML = this.responseText;
        }
        xhr.send();
    }

        // AUTO-REFRESH: Runs every 1 seconds when on the Home Tab
setInterval(() => {
        loadData();
}, 1000); 

    function checkout(id) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "update_status.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (this.responseText.trim() == "success") loadData();
        }
        xhr.send("id=" + id);
    }

    function checkout(id) {
    // We use AJAX so the page doesn't refresh
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    xhr.onload = function() {
        if (this.status == 200 && this.responseText.trim() === "success") {
            // Success! Now reload the table data to show the change
            loadData(); 
        } else {
            alert("Error updating status.");
        }
    };
    
    xhr.send("id=" + id);
}

function checkout(id) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    xhr.onload = function() {
        if (this.responseText.trim() === "success") {
            // This reloads the table so the button disappears and 'Verified' appears
            loadData(); 
        } else {
            console.error("Server error: " + this.responseText);
        }
    };
    
    xhr.send("id=" + id);
}


// Function to export table to CSV
function exportCSV() {
    let csv = "ID,QR Code,Status\n";
    const rows = document.querySelectorAll("table tr");
    
    for (let i = 1; i < rows.length; i++) {
        const cols = rows[i].querySelectorAll("td");
        if (cols.length > 1) {
            csv += `${cols[0].innerText},${cols[1].innerText},${cols[2].innerText}\n`;
        }
    }

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', 'akejo_qr_report.csv');
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// Function to delete all records via AJAX

/*
function clearAll() {
    if (confirm("Are you sure? This will delete EVERY record in the database forever!")) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "clear_data.php", true);
        xhr.onload = function() {
            if (this.responseText.trim() === "success") {
                loadData(); // Refresh empty table
                alert("Database cleared successfully.");
            }
        };
        xhr.send();
    }
}
*/


// --- GLOBAL RESET (Clears all 3 tables) ---
function clearAll() {
    if (confirm("⚠️ WARNING: Are you sure? This will delete EVERY record in the database forever!")) {
        const password = prompt("Please enter the Admin Password to confirm:");
        if (password === "hacker") { // You can change this password
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "clear_data.php", true);
            xhr.onload = function() {
                if (this.responseText.trim() === "success") {
                    alert("Database cleared successfully.");
                loadData(); // Refresh empty table
                }
            };
            xhr.send();
        } else {
            alert("Incorrect password.");
        }
    }
}

    window.onload = loadData;





    let qrX = 0;
let qrY = 0;
let qrSizePercent = 0.15; // 15% of image width
let baseImg = null;

const pCanvas = document.getElementById('previewCanvas');
const pCtx = pCanvas.getContext('2d');
const qrGuide = document.getElementById('qr-guide');

// Handle Image Preview
document.getElementById('imageInput').onchange = function(e) {
    const reader = new FileReader();
    reader.onload = function(event) {
        baseImg = new Image();
        baseImg.onload = function() {
            // Set canvas dimensions to match image
            pCanvas.width = baseImg.width;
            pCanvas.height = baseImg.height;
            pCtx.drawImage(baseImg, 0, 0);
            document.getElementById('status-msg').innerText = "Image Loaded. Click to position QR.";
        };
        baseImg.src = event.target.result;
    };
    reader.readAsDataURL(e.target.files[0]);
};

// Handle Click to Position
pCanvas.addEventListener('click', function(e) {
    if(!baseImg) return;

    const rect = pCanvas.getBoundingClientRect();
    const scaleX = pCanvas.width / rect.width;
    const scaleY = pCanvas.height / rect.height;

    // Calculate real coordinates on the original image
    qrX = (e.clientX - rect.left) * scaleX;
    qrY = (e.clientY - rect.top) * scaleY;

    // Calculate size for the guide (visual only)
    const displaySize = rect.width * qrSizePercent;
    qrGuide.style.width = displaySize + "px";
    qrGuide.style.height = displaySize + "px";
    qrGuide.style.left = (e.clientX - rect.left - displaySize/2) + "px";
    qrGuide.style.top = (e.clientY - rect.top - displaySize/2) + "px";
    qrGuide.style.display = "block";

    // Adjust qrX/Y to be the top-left corner for the final drawing
    qrX = qrX - (pCanvas.width * qrSizePercent / 2);
    qrY = qrY - (pCanvas.width * qrSizePercent / 2);
});

// Update your existing drawAndDownload function to use the new qrX and qrY
function drawAndDownload(imgObj, qrImg, id) {
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = imgObj.width;
    canvas.height = imgObj.height;
    
    ctx.drawImage(imgObj, 0, 0);

    const finalSize = canvas.width * qrSizePercent;
    
    // Draw White Background Box
    ctx.fillStyle = "white";
    ctx.fillRect(qrX, qrY, finalSize, finalSize);
    
    // Draw QR Code
    ctx.drawImage(qrImg, qrX + 5, qrY + 5, finalSize - 10, finalSize - 10);

    const link = document.createElement('a');
    link.download = `QR_${id}.png`;
    link.href = canvas.toDataURL();
    link.click();
}
</script>

</body>
</html>