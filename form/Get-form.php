<?php
// Get-form.php — lives in the same folder as form-process.php
require_once '../db_conn/db_conn.php';

header('Content-Type: application/json');
ini_set('display_errors', '0');
ob_start();

try {
    $srNum = trim($_GET['sr'] ?? '');
    if (!$srNum) throw new Exception('Missing sr');

    $srEsc = $mysqli->real_escape_string($srNum);

    $row = $mysqli->query("SELECT * FROM applicant WHERE srNum='$srEsc' LIMIT 1")->fetch_assoc();
    if (!$row) throw new Exception('Record not found');

    // Pest types
    $pests = [];
    $res = $mysqli->query("SELECT pestType FROM applicant_pest WHERE srNum='$srEsc'");
    while ($r = $res->fetch_assoc()) $pests[] = $r;

    // Pesticides
    $pesticides = [];
    $res = $mysqli->query("SELECT * FROM pesticide WHERE srNum='$srEsc' ORDER BY pesticideNo");
    while ($r = $res->fetch_assoc()) $pesticides[] = $r;

    // Pest status
    $pestStatus = [];
    $res = $mysqli->query("SELECT * FROM pest_status WHERE srNum='$srEsc' ORDER BY pestStatNo");
    while ($r = $res->fetch_assoc()) $pestStatus[] = $r;

    // Recommendations
    $recommendations = [];
    $res = $mysqli->query("SELECT * FROM recomend WHERE srNum='$srEsc' ORDER BY recomNo");
    while ($r = $res->fetch_assoc()) $recommendations[] = $r;

    // Applicator
    $applicator = $mysqli->query("SELECT * FROM applicator WHERE srNum='$srEsc' LIMIT 1")->fetch_assoc();

    // Termite
    $termite = $mysqli->query("SELECT * FROM termite_post WHERE srNum='$srEsc' LIMIT 1")->fetch_assoc();

    ob_end_clean();
    echo json_encode([
        'success'   => true,
        'sr_number' => $srNum,
        'form'      => array_merge($row, [
            'pests'           => $pests,
            'pesticides'      => $pesticides,
            'pestStatus'      => $pestStatus,
            'recommendations' => $recommendations,
            'applicator'      => $applicator,
            'termite'         => $termite,
        ]),
    ]);

} catch (Throwable $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}