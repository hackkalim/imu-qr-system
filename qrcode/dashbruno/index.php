<?php
session_start();


if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}


// 30 minutes in seconds
$timeout_duration = 1800;

if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    if ($elapsed_time > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: ../login.php?timeout=1");
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
        .container { padding: 40px 5%; max-width: 1600px; margin: 0 auto; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .dashboard { display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
        .panel { background: var(--white); border-radius: 24px; padding: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.05); margin-bottom: 20px;}
        .panel-title { font-size: 18px; font-weight: 800; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        .panel-title::before { content: ''; width: 4px; height: 20px; background: var(--primary); border-radius: 10px; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { text-align: left; padding: 15px; font-size: 12px; text-transform: uppercase; color: #888; border-bottom: 2px solid #f4f4f4; }
        td { padding: 15px; font-size: 14px; border-bottom: 1px solid #f4f4f4; }
        
        .summary-table th { background: #fdfdfd; }

        /* Inputs & UI */
        .field { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; opacity: 0.7; }
        input { width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #e0e0e0; }
        .btn-main { width: 100%; background: var(--dark); color: white; border: none; padding: 15px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-main:hover { background: var(--primary); }
        
        .badge { padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        .pending { background: #FFF4E5; color: #FF8800; }
        .verified { background: #E8F5E9; color: #2D6A4F; }

        #qr-guide { position: absolute; border: 2px dashed var(--primary); background: rgba(251, 133, 0, 0.2); pointer-events: none; display: none; }


        .btn-tool {
    padding: 10px 18px;
    border-radius: 10px;
    border: none;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
}
         /* Update these in your index.php <style> section */
.nav-link.active {
    background: #ff9800 !important; /* Your brand orange */
    color: white !important;
}

.panel-title {
    border-left: 5px solid #ff9800; /* Orange accent on panels */
    padding-left: 15px;
    font-weight: 800;
}

.btn-check {
    background: #ff9800;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.btn-check:hover {
    background: #e68900;
}


        @media print {
    /* Hide everything except the main panels */
    header, nav, aside, .btn-main, .btn-tool, #status-msg, .field, #qr-guide {
        display: none !important;
    }
    
    body { background: white; }
    .container { width: 100%; padding: 0; margin: 0; }
    .panel { 
        box-shadow: none !important; 
        border: 1px solid #eee !important; 
        width: 100% !important;
        margin-bottom: 30px;
    }
    .tab-content { display: block !important; } /* Show home data on print */
    
    /* Ensure the Home tables show up nicely on paper */
    .dashboard { display: block !important; }
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
            <li class="nav-link active" onclick="openTab('home')">Home</li>
            <a href="simple/index.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">Simple</li></a>
            <a href="vip/index.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">VIP</li></a>
            <a href="vvip/index.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">VVIP</li></a>
            <a href="print.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">Print</li></a>

            <a href="setting/settings.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">Settings</li></a>
            <a href="logout.php" class="nav-link" style="text-decoration: none;color: white;"><li class="nav-link">Logout</li></a>
        </ul>
    </nav>
</header>

<div class="container">
    
    <div id="home" class="tab-content active">
        <div class="panel">
            <div class="panel-title">Sales & Verification Summary</div>
<div class="admin-actions" style="margin-bottom: 20px; display: flex; gap: 10px;">
    <button class="btn-tool" style="background: #e3f2fd; color: #1976d2;" onclick="printReport()">📄 Print Report / PDF</button>
    <button class="btn-tool" style="background: #ffebee; color: #d32f2f; margin-left: auto;" onclick="resetAllData()">🗑️ Reset All Data</button>
</div>
            <table class="summary-table">
    <thead>
        <tr>
            <th>Category</th>          <th>Total Generated</th>    <th>Price/Ticket</th>       <th>Qty Verified</th>       <th>Total Verified ($)</th> <th>Qty Pending</th>        <th>Total Pending ($)</th>  </tr>
    </thead>
    <tbody id="summaryBody">
        </tbody>
</table>
        </div>

        <div class="panel">
    <div class="panel-title">Global Live Monitor (Real-time)</div>
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
        <div>
            <h4>SIMPLE</h4>
            <table>
                <thead><tr><th>ID</th><th>QR</th><th>Price</th><th>Status</th></tr></thead>
                <tbody id="bodySimple"></tbody>
            </table>
        </div>
        <div>
            <h4>VIP</h4>
            <table>
                <thead><tr><th>ID</th><th>QR</th><th>Price</th><th>Status</th></tr></thead>
                <tbody id="bodyVip"></tbody>
            </table>
        </div>
        <div>
            <h4>VVIP</h4>
            <table>
                <thead><tr><th>ID</th><th>QR</th><th>Price</th><th>Status</th></tr></thead>
                <tbody id="bodyVvip"></tbody>
            </table>
        </div>
    </div>
</div>
    </div>

    <div id="tier-panel" class="tab-content">
        <div class="dashboard">
            <aside class="panel">
                <div class="panel-title" id="tier-title">Generator</div>
                <div class="field">
                    <label>Base Photo</label>
                    <input type="file" id="imageInput" accept="image/*">
                </div>
                <div id="preview-container" style="position: relative; background: #eee; cursor: crosshair;">
                    <canvas id="previewCanvas" style="width: 100%; display: block;"></canvas>
                    <div id="qr-guide"></div>
                </div>
                <div class="field" style="margin-top:15px">
                    <label>Quantity</label>
                    <input type="number" id="duplicateCount" placeholder="Amount...">
                </div>
                <div class="field">
    <label>4. Ticket Price ($)</label>
    <input type="number" id="ticketPrice" placeholder="Enter amount (e.g. 50)" value="">
</div>
                <button class="btn-main" onclick="startGeneration()">Generate & Download</button>
                <div id="status-msg" style="margin-top:10px; font-weight:700; color:var(--primary)">System Ready</div>
            </aside>

            <main class="panel">
                <div class="panel-title">Manage Records</div>
                <table id="mainTable">
                    <thead>
                        <tr><th>ID</th><th>QR Code</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </main>
        </div>
    </div>
</div>

<div id="qrcode-hidden" style="display:none;"></div>
<canvas id="canvas" style="display:none;"></canvas>

<script>
    let currentTier = 'simple';
    let baseImg = null;
    let qrX = 0, qrY = 0;
    const qrSizePercent = 0.15;

    function openTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        
        if(tabName === 'home') {
            document.getElementById('home').classList.add('active');
            loadHomeData();
        } else {
            currentTier = tabName;
            document.getElementById('tier-panel').classList.add('active');
            document.getElementById('tier-title').innerText = tabName.toUpperCase() + " Generator";
            loadTierData();
        }
        event.currentTarget.classList.add('active');
    }

    // --- POSITIONING LOGIC ---
    const pCanvas = document.getElementById('previewCanvas');
    const pCtx = pCanvas.getContext('2d');
    const qrGuide = document.getElementById('qr-guide');

    document.getElementById('imageInput').onchange = function(e) {
        const reader = new FileReader();
        reader.onload = function(event) {
            baseImg = new Image();
            baseImg.onload = function() {
                pCanvas.width = baseImg.width;
                pCanvas.height = baseImg.height;
                pCtx.drawImage(baseImg, 0, 0);
            };
            baseImg.src = event.target.result;
        };
        reader.readAsDataURL(e.target.files[0]);
    };

    pCanvas.addEventListener('click', function(e) {
        if(!baseImg) return;
        const rect = pCanvas.getBoundingClientRect();
        const scaleX = pCanvas.width / rect.width;
        const scaleY = pCanvas.height / rect.height;
        qrX = (e.clientX - rect.left) * scaleX;
        qrY = (e.clientY - rect.top) * scaleY;

        const displaySize = rect.width * qrSizePercent;
        qrGuide.style.width = displaySize + "px";
        qrGuide.style.height = displaySize + "px";
        qrGuide.style.left = (e.clientX - rect.left - displaySize/2) + "px";
        qrGuide.style.top = (e.clientY - rect.top - displaySize/2) + "px";
        qrGuide.style.display = "block";
        
        qrX -= (pCanvas.width * qrSizePercent / 2);
        qrY -= (pCanvas.width * qrSizePercent / 2);
    });

    // --- GENERATION ---
    async function startGeneration() {
        const qty = parseInt(document.getElementById('duplicateCount').value);
        if(!baseImg || !qty) return alert("Select image and quantity");

        for(let i=0; i<qty; i++) {
            let prefix = currentTier === 'vip' ? 'V' : (currentTier === 'vvip' ? 'Vv' : '');
            let uniqueID = prefix + (Math.floor(Math.random() * 9000000) + 1000000);
            
            document.getElementById('qrcode-hidden').innerHTML = "";
            new QRCode(document.getElementById("qrcode-hidden"), { text: uniqueID, width: 200, height: 200 });

            await new Promise(r => setTimeout(r, 400));
            const qrImg = document.querySelector('#qrcode-hidden img');
            
            drawAndDownload(uniqueID, qrImg);
            saveToDB(uniqueID);
            document.getElementById('status-msg').innerText = `Generated ${i+1}/${qty}`;
            await new Promise(r => setTimeout(r, 600));
        }
        loadTierData();
    }

    function drawAndDownload(id, qrImg) {
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = baseImg.width; canvas.height = baseImg.height;
        ctx.drawImage(baseImg, 0, 0);
        const size = canvas.width * qrSizePercent;
        ctx.fillStyle = "white";
        ctx.fillRect(qrX, qrY, size, size);
        ctx.drawImage(qrImg, qrX+5, qrY+5, size-10, size-10);
        
        const link = document.createElement('a');
        link.download = `${currentTier}_${id}.png`;
        link.href = canvas.toDataURL();
        link.click();
    }

    // --- DATA HANDLING ---
    function saveToDB(val) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "save_qr.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send(`qrcode=${val}&tier=${currentTier}`);
    }

    function loadTierData() {
        fetch(`fetch_data.php?tier=${currentTier}`)
            .then(res => res.text())
            .then(data => document.getElementById('tableBody').innerHTML = data);
    }

    // Function to fetch the 6-column Summary Table and the 3-part Monitor
function loadHomeData() {
    // 1. Fetch the Financial Summary (Table 1)
    fetch('get_summary.php')
        .then(res => res.text())
        .then(data => {
            document.getElementById('summaryBody').innerHTML = data;
        });

    // 2. Fetch the Live Monitor Records (Table 2 - Side by Side)
    const tiers = ['simple', 'vip', 'vvip'];
    tiers.forEach(t => {
        fetch(`fetch_data.php?tier=${t}&mode=minimal`)
            .then(res => res.text())
            .then(data => {
                const bodyId = `body${t.charAt(0).toUpperCase() + t.slice(1)}`;
                document.getElementById(bodyId).innerHTML = data;
            });
    });
}

// AUTO-REFRESH: Runs every 1 seconds when on the Home Tab
setInterval(() => {
    const homeTab = document.getElementById('home');
    if (homeTab.classList.contains('active')) {
        loadHomeData();
    }
}, 1000); 

// Initial load
window.onload = loadHomeData;


function checkout(id, tier) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (this.responseText.trim() === "success") {
            loadTierData(); // Refresh the current view
        }
    };
    xhr.send("id=" + id + "&tier=" + tier);
}



//let currentTier = 'simple'; // Default tier

// Function to switch menus
function openTab(tabName) {
    // UI switching
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    
    currentTier = tabName; // Update global tier variable

    if(tabName === 'home') {
        document.getElementById('home').classList.add('active');
        loadHomeData(); // Refresh home stats
    } else {
        document.getElementById('tier-panel').classList.add('active');
        document.getElementById('tier-title').innerText = tabName.toUpperCase() + " Panel";
        loadTierData(); // AJAX load the specific table (Simple, Vip, or Vvip)
    }
    event.currentTarget.classList.add('active');
}

// --- AUTOMATIC TIERED GENERATION ---
async function startGeneration() {
    const qty = parseInt(document.getElementById('duplicateCount').value);
    if(!baseImg || !qty) return alert("Select image and quantity");

    document.getElementById('status-msg').innerText = "Processing...";

    for(let i=0; i<qty; i++) {
        // Step A: Logic for Prefixes
        let prefix = "";
        if(currentTier === 'vip') prefix = "V";
        if(currentTier === 'vvip') prefix = "Vv";
        
        let uniqueID = prefix + (Math.floor(Math.random() * 9000000) + 1000000);
        
        // Step B: Generate QR
        document.getElementById('qrcode-hidden').innerHTML = "";
        new QRCode(document.getElementById("qrcode-hidden"), { text: uniqueID, width: 200, height: 200 });

        await new Promise(r => setTimeout(r, 400));
        const qrImg = document.querySelector('#qrcode-hidden img');
        
        // Step C: Draw/Download & Save to specific table via AJAX
        drawAndDownload(uniqueID, qrImg);
        saveToDB(uniqueID); // This uses currentTier to pick the table

        document.getElementById('status-msg').innerText = `Generated ${i+1}/${qty} for ${currentTier}`;
        await new Promise(r => setTimeout(r, 600));
    }
    
    loadTierData(); // Refresh current table without reloading page
}


// --- UPDATED GENERATION WITH PRICE ---
async function startGeneration() {
    const qty = parseInt(document.getElementById('duplicateCount').value);
    const price = document.getElementById('ticketPrice').value;
    
    if(!baseImg || !qty || !price) {
        alert("Please select image, quantity, and set a price.");
        return;
    }

    document.getElementById('status-msg').innerText = "Processing...";

    for(let i=0; i<qty; i++) {
        let prefix = "";
        if(currentTier === 'vip') prefix = "V";
        if(currentTier === 'vvip') prefix = "Vv";
        
        let uniqueID = prefix + (Math.floor(Math.random() * 9000000) + 1000000);
        
        document.getElementById('qrcode-hidden').innerHTML = "";
        new QRCode(document.getElementById("qrcode-hidden"), { text: uniqueID, width: 200, height: 200 });

        await new Promise(r => setTimeout(r, 400));
        const qrImg = document.querySelector('#qrcode-hidden img');
        
        drawAndDownload(uniqueID, qrImg);
        
        // Pass both ID and PRICE to the database
        saveToDB(uniqueID, price); 

        document.getElementById('status-msg').innerText = `Generated ${i+1}/${qty}`;
        await new Promise(r => setTimeout(r, 600));
    }
    loadTierData();
}

function saveToDB(val, price) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "save_qr.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    // Sending tier, code, and price
    xhr.send(`qrcode=${val}&tier=${currentTier}&price=${price}`);
}



// --- AJAX DATABASE SAVE ---
function saveToDB(val) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "save_qr.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    // We send 'tier' so the PHP knows which table to use
    xhr.send(`qrcode=${val}&tier=${currentTier}`);
}



// --- PRINT TO PDF ---
function printReport() {
    // Add a temporary title for the printout
    const header = document.querySelector('header').innerHTML;
    window.print();
}

// --- GLOBAL RESET (Clears all 3 tables) ---
function resetAllData() {
    if (confirm("⚠️ WARNING: This will permanently delete EVERY record in Simple, VIP, and VVIP tables. This cannot be undone!")) {
        const password = prompt("Please enter the Admin Password to confirm:");
        if (password === "hacker") { // You can change this password
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "reset_all.php", true);
            xhr.onload = function() {
                if (this.responseText.trim() === "success") {
                    alert("All tables have been cleared.");
                    loadHomeData(); // Refresh the empty dashboard
                }
            };
            xhr.send();
        } else {
            alert("Incorrect password.");
        }
    }
}





// Function to refresh the Home Dashboard every second
function loadHomeData() {
    // 1. Update the Financial Summary Table (Table 1)
    const xhrSummary = new XMLHttpRequest();
    xhrSummary.open("GET", "get_summary.php", true);
    xhrSummary.onload = function() {
        if(this.status == 200) {
            document.getElementById('summaryBody').innerHTML = this.responseText;
        }
    };
    xhrSummary.send();

    // 2. Update the Live Monitor Lists (Table 2 - Simple, Vip, Vvip)
    const tiers = ['simple', 'vip', 'vvip'];
    tiers.forEach(t => {
        const xhr = new XMLHttpRequest();
        // Mode 'minimal' fetches data with the Price column and without the Action button
        xhr.open("GET", `fetch_data.php?tier=${t}&mode=minimal`, true);
        xhr.onload = function() {
            if(this.status == 200) {
                const bodyId = `body${t.charAt(0).toUpperCase() + t.slice(1)}`;
                document.getElementById(bodyId).innerHTML = this.responseText;
            }
        };
        xhr.send();
    });
}

// THE HEARTBEAT: Refresh every 1 second (1000ms)
setInterval(() => {
    if (currentTier === 'home') {
        loadHomeData();
    }
}, 1000);

// --- AUTO REFRESH EVERY 3 SECONDS ---
setInterval(() => {
    // Only refresh if the user is looking at the Home tab
    if (currentTier === 'home') {
        loadHomeData();
    }
}, 3000);

    window.onload = loadHomeData;
</script>
</body>
</html>