<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

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
$_SESSION['last_activity'] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMU QR | Bulk Print System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="icon" type="icon" href="IMG_4043.jpg">
    <script src="jspdf.umd.min.js"></script>
    
    <style>
        :root {
            --primary: #FB8500;
            --dark: #1a1a1a;
            --bg: #f8f9fa;
            --white: #ffffff;
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


        /* Dashboard & Print Layout */
        .dashboard {
            padding: 40px 5%;
            max-width: 1600px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        .upload-panel {
            background: var(--white);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 600px;
            text-align: center;
            border: 2px dashed #ccc;
        }

        .print-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            width: 100%;
        }

        .print-grid img {
            width: 300px; /* Base size for preview */
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
        }

        .btn-print {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 800;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            transition: 0.3s;
        }

        .btn-print:hover { background: #e67a00; transform: scale(1.05); }

        /* PRINT LOGIC: This triggers when you click Print */
        @media print {
            header, .upload-panel, .btn-print, .no-print {
                display: none !important;
            }
            body { background: white; }
            .dashboard { padding: 0; margin: 0; width: 100%; }
            .print-grid { gap: 0; display: block; } /* Block display helps printer pagination */
            .print-grid img {
                width: 100%; /* Scales to the paper width chosen (A4, A3, etc) */
                page-break-after: always; /* Each ticket on a new page */
                box-shadow: none;
                border: none;
            }
        }


        /* --- MODERN UPLOAD PANEL DESIGN --- */
.upload-panel {
    background: var(--white);
    padding: 50px 30px;
    border-radius: 24px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.05);
    width: 100%;
    max-width: 700px;
    text-align: center;
    border: 2px dashed #d1d5db;
    transition: all 0.3s ease;
    position: relative;
    cursor: pointer;
}

.upload-panel:hover {
    border-color: var(--primary);
    background: #fffaf5;
}

.upload-icon {
    font-size: 50px;
    margin-bottom: 15px;
    display: block;
}

.upload-panel h2 {
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 10px;
}

.upload-panel p {
    color: #6b7280;
    font-size: 15px;
    margin-bottom: 25px;
}

/* Hidden real input */
#bulkUpload {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.counter-badge {
    background: var(--dark);
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    display: none; /* Shown via JS */
    margin-bottom: 20px;
}

.action-area {
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Print Button Upgrade */
.btn-print {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--primary);
    color: white;
    border: none;
    padding: 16px 40px;
    border-radius: 14px;
    font-weight: 800;
    cursor: pointer;
    font-size: 16px;
    box-shadow: 0 10px 20px rgba(251, 133, 0, 0.3);
    transition: 0.3s;
}

.btn-print:hover {
    background: var(--dark);
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}


/* Update the upload-panel to handle layering */
.upload-panel {
    position: relative;
    z-index: 1; /* Base layer */
}

/* Ensure the file input doesn't cover the buttons */
#bulkUpload {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
    z-index: 2; /* Sits above the text */
}

/* Push the buttons to the very top layer */
.action-area {
    position: relative;
    z-index: 10; /* Sits above the file input */
}

.btn-print {
    position: relative;
    z-index: 20; /* Highest layer - guaranteed clickable */
}

/* --- ORIGINAL SIZE PREVIEW & PRINT --- */
.print-grid {
    display: flex;
    flex-direction: column; /* Stack them to maintain original width */
    align-items: center;
    gap: 20px;
    width: 100%;
}

.print-grid img {
    /* Use 'max-width: 100%' only to prevent the image from physically 
       breaking the browser window, but it will print at original scale. */
    max-width: 100%; 
    height: auto;
    display: block;
    margin-bottom: 20px;
}

@media print {
    /* Hide all UI elements */
    header, .upload-panel, .btn-print, .no-print, .counter-badge {
        display: none !important;
    }

    body, .dashboard {
        margin: 0;
        padding: 0;
        background: white;
    }

    .print-grid {
        display: block; /* Standard block flow for printing */
        gap: 0;
    }

    .print-grid img {
        /* FORCE PRINT AT ORIGINAL SIZE */
        width: auto !important;
        height: auto !important;
        max-width: none !important; /* Allow image to be larger than screen if necessary */
        page-break-after: always; /* Each ticket gets its own page */
        margin: 0;
    }
    
    /* Remove browser-added margins */
    @page {
        margin: 0;
    }
}






/* --- HORIZONTAL GRID DESIGN --- */
.print-grid {
    display: flex;
    flex-wrap: wrap;    /* This allows images to go side-by-side */
    justify-content: flex-start; 
    gap: 10px;          /* Space between images */
    width: 100%;
    padding: 20px 0;
}

.print-grid img {
    /* Keeps original size but ensures they don't 
       overflow the physical paper width */
    max-width: 100%; 
    height: auto;
    object-fit: contain;
    display: inline-block;
}

@media print {
    /* Hide UI */
    header, .upload-panel, .btn-print, .counter-badge {
        display: none !important;
    }

    body, .dashboard {
        margin: 0;
        padding: 0;
        background: white;
    }

    .print-grid {
        display: flex !important;   /* Force horizontal layout on paper */
        flex-direction: row !important;
        flex-wrap: wrap !important;
        gap: 5px; /* Tighten gap for printing to save paper */
    }

    .print-grid img {
        /* This is the magic part: it keeps original size 
           unless the image is wider than the paper itself */
        width: auto !important; 
        max-width: 100% !important;
        page-break-inside: avoid; /* Prevents an image from being cut in half across two pages */
        margin-bottom: 5px;
    }

    @page {
        margin: 0.5cm; /* Small margin so the printer doesn't cut off edges */
    }
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





:root {
    --grid-gap: 10px; /* Default spacing */
}

.print-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start; 
    gap: var(--grid-gap); /* Controlled by the slider */
    width: 100%;
    padding: 20px 0;
}

@media print {
    /* ... your other print styles ... */
    .print-grid {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: var(--grid-gap) !important; /* Keeps your chosen spacing on the paper! */
    }
}




/* --- Tiled Flow Logic --- */
:root {
    --grid-gap: 10px; 
}

.print-grid {
    display: flex;
    flex-wrap: wrap;       /* This allows images to wrap to the next row */
    justify-content: flex-start; 
    gap: var(--grid-gap);
    width: 100%;
}

.print-grid img {
    /* Natural size preservation */
    width: auto;
    height: auto;
    max-width: 100%;       /* Prevents single image from being wider than paper */
    display: block;
    object-fit: contain;
}

@media print {
    /* 1. Hide the Dashboard UI */
    header, .upload-panel, .btn-print, #controls, .counter-badge {
        display: none !important;
    }

    /* 2. Reset layout for Paper */
    body, .dashboard {
        margin: 0;
        padding: 0;
        background: white;
    }

    .print-grid {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important; /* Forces wrap to next line on paper */
        gap: var(--grid-gap) !important;
        padding: 0;
    }

    .print-grid img {
        /* This prevents an image from being cut horizontally between page 1 and page 2 */
        page-break-inside: avoid; 
        break-inside: avoid;
        margin-bottom: var(--grid-gap);
    }

    /* 3. Page Setup */
    @page {
        margin: 1cm; /* Adjust based on your printer's safe area */
    }
}







.print-grid {
    display: flex !important;
    flex-wrap: wrap !important;
    flex-direction: row !important;
    gap: var(--grid-gap);
    width: 210mm; /* Fixed width of A4 to ensure PDF scaling is 1:1 */
    margin: 0 auto;
    background: white;
    padding: 10px;
}

.print-grid img {
    /* We use inline-block to help the PDF engine understand layout */
    display: inline-block; 
    height: auto;
    /* This ensures that if the image is too big, it stays within the PDF wall */
    max-width: calc(100% - 10px); 
}

/* This helps the PDF engine know not to cut an image in half */
.print-grid img {
    page-break-inside: avoid;
    break-inside: avoid;
}




<style>
    /* This container is what will be turned into a PDF */
    #previewGrid {
        display: flex !important;
        flex-wrap: wrap !important;
        flex-direction: row !important;
        gap: var(--grid-gap);
        width: 190mm; /* Slightly less than A4 width to account for margins */
        background: white;
        padding: 10px;
        margin: 0 auto;
    }

    #previewGrid img {
        display: inline-block;
        height: auto;
        /* This ensures the image stays at its natural size unless it's wider than the page */
        max-width: 100%; 
        page-break-inside: avoid; /* Essential: No splitting images between pages */
    }

    /* Hide the system print if the user accidentally hits Ctrl+P */
    @media print {
        body { display: none !important; }
    }



    #previewGrid {
    display: flex !important;
    flex-wrap: wrap !important;
    flex-direction: row !important;
    gap: var(--grid-gap);
    width: 190mm; /* Strict A4 width minus margins */
    margin: 0 auto;
    background: white;
    padding: 10px;
}

#previewGrid img {
    /* Keep original image quality and prevent stretching */
    display: inline-block;
    height: auto;
    max-width: 100%; 
    /* Force the engine to never cut a QR code between pages */
    page-break-inside: avoid !important;
    break-inside: avoid !important;
}

/* Hide everything else if the browser print DOES accidentally open */
@media print {
    header, .upload-panel, #controls, .counter-badge { display: none !important; }
}
</style>
    </style>
</head>
<body>

<header>
    <div class="logo">IMU<span> QR</span></div>
    <nav>
        <ul>
            <a href="index.php" style="text-decoration: none;color: white;"><li>Home</li></a>
            <a href="simple/index.php" style="text-decoration: none;color: white;"><li>Simple</li></a>
            <a href="vip/index.php" style="text-decoration: none;color: white;"><li>VIP</li></a>
            <a href="vvip/index.php" style="text-decoration: none;color: white;"><li>VVIP</li></a>
            <a href="print.php" style="text-decoration: none;color: white;"><li class="active">Print</li></a>
            <a href="setting/settings.php" style="text-decoration: none;color: white;"><li>Settings</li></a>
            <a href="logout.php" style="text-decoration: none;color: white;"><li>Logout</li></a>
        </ul>
    </nav>
</header>

<div class="dashboard">
    <div class="upload-panel" id="drop-zone">
        <span class="upload-icon">📂</span>
        <h2>Bulk Ticket Printer</h2>
        <p>Drag and drop your generated QR images here <br> or <strong>click to browse files</strong></p>
        
        <input type="file" id="bulkUpload" accept="image/*" multiple>
        
        <div class="action-area">
    <div id="file-count" class="counter-badge">0 Tickets Selected</div>
    
    <div id="controls" style="display: none; margin-bottom: 20px; text-align: center;">
        <label style="font-size: 12px; font-weight: 800; color: var(--dark); display: block; margin-bottom: 5px;">
            ADJUST SPACING: <span id="gap-val">10</span>px
        </label>
        <input type="range" id="spacing-slider" min="0" max="100" value="10" style="width: 200px; cursor: pointer;">
    </div>

    <div id="progress-container" style="display: none; width: 100%; max-width: 300px; margin-bottom: 15px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span style="font-size: 10px; font-weight: 800; color: var(--primary);">GENERATING PDF...</span>
            <span id="progress-percent" style="font-size: 10px; font-weight: 800; color: var(--primary);">0%</span>
        </div>
        <div style="width: 100%; background: #eee; border-radius: 10px; height: 10px; overflow: hidden;">
            <div id="progress-bar" style="width: 0%; height: 100%; background: var(--primary); transition: width 0.1s linear;"></div>
        </div>
    </div>

    <button type="button" class="btn-print" id="printBtn" style="display: none;">
        <span id="btn-icon">📄</span> <span id="btn-text">Generate PDF / Print</span>
    </button>
</div>
    </div>

    <div class="print-grid" id="previewGrid"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('bulkUpload');
    const previewGrid = document.getElementById('previewGrid');
    const printBtn = document.getElementById('printBtn');
    const controls = document.getElementById('controls');
    const slider = document.getElementById('spacing-slider');
    const gapValLabel = document.getElementById('gap-val');
    const progressBar = document.getElementById('progress-bar');
    const progressContainer = document.getElementById('progress-container');
    const btnText = document.getElementById('btn-text');

    // 1. Handle File Selection & Preview
    fileInput.addEventListener('change', function() {
        previewGrid.innerHTML = ''; 
        const files = this.files;
        if (files.length > 0) {
            printBtn.style.display = 'flex';
            controls.style.display = 'block';
            document.getElementById('file-count').style.display = 'inline-block';
            document.getElementById('file-count').innerText = `${files.length} Tickets Selected`;
            
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    previewGrid.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        }
    });

    // 2. Handle Spacing Slider
    slider.addEventListener('input', function() {
        const newVal = this.value + 'px';
        gapValLabel.innerText = this.value;
        document.documentElement.style.setProperty('--grid-gap', newVal);
    });

    // 3. THE "NO-CRASH" PDF GENERATOR (Direct Injection)
    printBtn.addEventListener('click', async function(e) {
    e.preventDefault();
    const { jsPDF } = window.jspdf;
    const images = previewGrid.querySelectorAll('img');
    
    if (images.length === 0) return;

    // 1. UI Setup
    btnText.innerText = "Generating Original Quality PDF...";
    printBtn.disabled = true;
    printBtn.style.opacity = "0.5";
    progressContainer.style.display = "block";
    
    const pdf = new jsPDF('p', 'mm', 'a4');
    const pageWidth = 210; 
    const pageHeight = 297; 
    const margin = 10;
    const gap = parseInt(slider.value) || 10;
    
    let curX = margin;
    let curY = margin;
    let maxRowHeight = 0;

    // 2. Processing Loop
    for (let i = 0; i < images.length; i++) {
        const img = images[i];
        
        // Calculate dimensions
        let imgW = img.naturalWidth / 3.78;
        let imgH = img.naturalHeight / 3.78;

        if (imgW > (pageWidth - margin * 2)) {
            const ratio = (pageWidth - margin * 2) / imgW;
            imgW *= ratio;
            imgH *= ratio;
        }

        if (curX + imgW > pageWidth - margin) {
            curX = margin;
            curY += maxRowHeight + (gap / 3.78);
            maxRowHeight = 0;
        }

        if (curY + imgH > pageHeight - margin) {
            pdf.addPage();
            curX = margin;
            curY = margin;
            maxRowHeight = 0;
        }

        // Inject Image
        pdf.addImage(img.src, 'JPEG', curX, curY, imgW, imgH, undefined, 'FAST');
        
        curX += imgW + (gap / 3.78);
        if (imgH > maxRowHeight) maxRowHeight = imgH;

        // 3. Update Progress Bar & Percentage
        const percentage = Math.round(((i + 1) / images.length) * 100);
        progressBar.style.width = percentage + "%";
        document.getElementById('progress-percent').innerText = percentage + "%";
        
        // Brief pause to allow the browser to update the UI (so it doesn't freeze)
        if (i % 5 === 0) {
            await new Promise(resolve => setTimeout(resolve, 1));
        }
    }

    // 4. Finalize
    btnText.innerText = "Downloading...";
    pdf.save("IMU_QR_Original_Quality.pdf");

    setTimeout(() => {
        alert("Success! PDF downloaded with 100% original quality.");
        progressContainer.style.display = "none";
        progressBar.style.width = "0%";
        document.getElementById('progress-percent').innerText = "0%";
        btnText.innerText = "Generate PDF / Print";
        printBtn.disabled = false;
        printBtn.style.opacity = "1";
    }, 800);
});
});
</script>
</body>

</body>
</html>