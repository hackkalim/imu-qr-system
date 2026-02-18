<?php
include 'db_config.php';

$tiers = [
    'simple' => ['table' => 'qrcodegenerate'],
    'vip'    => ['table' => 'qrcodevip'],
    'vvip'   => ['table' => 'qrcodevvip']
];

foreach ($tiers as $name => $info) {
    $tbl = $info['table'];
    
    // 1. Total Generated
    $totalRow = $conn->query("SELECT COUNT(*) as cnt FROM $tbl")->fetch_assoc();
    $totalCount = $totalRow['cnt'] ?? 0;

    // 2. Verified Stats (Count and Sum of Price)
    $vData = $conn->query("SELECT COUNT(*) as cnt, SUM(price) as amt FROM $tbl WHERE verified='Verified'")->fetch_assoc();
    $vQty = $vData['cnt'] ?? 0;
    $vCash = $vData['amt'] ?? 0;

    // 3. Pending Stats (Count and Sum of Price)
    $pData = $conn->query("SELECT COUNT(*) as cnt, SUM(price) as amt FROM $tbl WHERE verified='Pending'")->fetch_assoc();
    $pQty = $pData['cnt'] ?? 0;
    $pCash = $pData['amt'] ?? 0;

    // 4. Get current unit price (most recent)
    $lastPriceRow = $conn->query("SELECT price FROM $tbl ORDER BY id DESC LIMIT 1")->fetch_assoc();
    $unitPrice = $lastPriceRow['price'] ?? 0;

    echo "<tr>
            <td><b>" . strtoupper($name) . "</b></td>
            <td>$totalCount</td>
            <td>$unitPrice Fbu</td>
            <td>$vQty</td>
            <td><span style='color:green; font-weight:bold;'>$vCash Fbu</span></td>
            <td>$pQty</td>
            <td><span style='color:orange; font-weight:bold;'>$pCash Fbu</span></td>
          </tr>";
}
?>