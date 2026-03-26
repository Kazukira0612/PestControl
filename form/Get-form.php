<?php
require_once '../db_conn/db_conn.php';
header('Content-Type: application/json');

try {

    // ── STEP 1: Read the JSON JavaScript sent ──────────────────
    $d = json_decode(file_get_contents('php://input'), true);
    if (!$d) throw new Exception('No data received');

    // ── STEP 2: Grab the SR number ─────────────────────────────
    // If JS sent one, use it. If not, make a new one.
    $srNum = $mysqli->real_escape_string($d['sr_number'] ?? '');
    if (!$srNum) {
        // Auto-generate: SR-20260326-001 style
        $srNum = 'SR-' . date('Ymd') . '-' . rand(100, 999);
    }

    // ── STEP 3: Save the main applicant row ────────────────────
    // Delete old record first (so we can re-save without duplicate errors)
    $mysqli->query("DELETE FROM applicant WHERE srNum='$srNum'");

    $comId    = (int)$d['com_id'];
    $purpose  = $mysqli->real_escape_string($d['purpose_of_treatment'] ?? '');
    $planDate = $mysqli->real_escape_string($d['planned_date'] ?? '');
    $premises = $mysqli->real_escape_string($d['type_of_premises'] ?? '');
    $treatTime= $mysqli->real_escape_string($d['standard_treatment_time'] ?? '');
    $address  = $mysqli->real_escape_string($d['service_address'] ?? '');
    $specInst = $mysqli->real_escape_string($d['special_instruction'] ?? '');
    $remarks  = $mysqli->real_escape_string($d['remarks'] ?? '');
    $feedback = $mysqli->real_escape_string($d['customer_feedback'] ?? '');

    $mysqli->query("INSERT INTO applicant 
        (srNum, purposeTreat, planDate, premiseType, treatTime, servAdd, specIstruct, remarks, custFeedback, comId)
        VALUES 
        ('$srNum','$purpose','$planDate','$premises','$treatTime','$address','$specInst','$remarks','$feedback',$comId)");

    // ── STEP 4: Save which pest types were checked ──────────────
    // $d['pest_common_ants'] is 1 if checked, 0 if not (from your JS)
    $pestMap = [
        'A' => $d['pest_common_ants']  ?? 0,
        'B' => $d['pest_cockroaches']  ?? 0,
        'C' => $d['pest_rats']         ?? 0,
        'D' => $d['pest_mosquitoes']   ?? 0,
        'E' => $d['pest_flies']        ?? 0,
        'F' => $d['pest_termites']     ?? 0,
    ];
    foreach ($pestMap as $type => $checked) {
        if ($checked) {
            $mysqli->query("INSERT INTO applicant_pest (srNum, pestType) VALUES ('$srNum','$type')");
        }
    }

    // ── STEP 5: Save each pesticide row ────────────────────────
    // $d['pesticides_applied'] is an array of rows your JS collected
    foreach ($d['pesticides_applied'] ?? [] as $p) {
        $active  = $mysqli->real_escape_string($p['active_ingredients'] ?? '');
        $trade   = $mysqli->real_escape_string($p['trade_name']         ?? '');
        $class   = $mysqli->real_escape_string($p['class']              ?? '');
        $dilute  = (float)($p['dilution'] ?? 0);
        $method  = $mysqli->real_escape_string($p['method']             ?? '');
        $area    = (float)($p['area']    ?? 0);
        $qty     = (float)($p['qty']     ?? 0);

        $mysqli->query("INSERT INTO pesticide 
            (activeItem, tradeName, class, diluteSolution, methodApply, totalArea, totalQtyDilute, srNum)
            VALUES 
            ('$active','$trade','$class',$dilute,'$method',$area,$qty,'$srNum')");
    }

    // ── STEP 6: Save pest status rows ──────────────────────────
    foreach ($d['pest_status'] ?? [] as $ps) {
        $name     = $mysqli->real_escape_string($ps['pest']     ?? '');
        $level    = $mysqli->real_escape_string($ps['level']    ?? '');
        $location = $mysqli->real_escape_string($ps['location'] ?? '');
        $remark   = $mysqli->real_escape_string($ps['remarks']  ?? '');

        $mysqli->query("INSERT INTO pest_status 
            (pestStatName, statLvl, statLocation, statRemark, srNum)
            VALUES 
            ('$name','$level','$location','$remark','$srNum')");
    }

    // ── STEP 7: Save recommendations ───────────────────────────
    foreach ($d['recommendations'] ?? [] as $r) {
        $type = $mysqli->real_escape_string($r['type']           ?? '');
        $rec  = $mysqli->real_escape_string($r['recommendation'] ?? '');

        $mysqli->query("INSERT INTO recomend 
            (recomType, recommendation, srNum)
            VALUES 
            ('$type','$rec','$srNum')");
    }

    // ── STEP 8: Save applicator info ───────────────────────────
    $leaderId  = $mysqli->real_escape_string($d['leader_id']      ?? '');
    $treatDate = $mysqli->real_escape_string($d['treatment_date'] ?? '');
    $vehicleNo = $mysqli->real_escape_string($d['vehicle_no']     ?? '');
    $timeIn    = $mysqli->real_escape_string($d['time_in']        ?? '');
    $timeOut   = $mysqli->real_escape_string($d['time_out']       ?? '');
    $custName  = $mysqli->real_escape_string($d['customer_name']  ?? '');
    $custDate  = $mysqli->real_escape_string($d['customer_date']  ?? '');
    $leaderSig = $mysqli->real_escape_string($d['applicator_signature'] ?? '');
    $custSig   = $mysqli->real_escape_string($d['customer_signature']   ?? '');

    $mysqli->query("INSERT INTO applicator 
        (srNum, leaderId, treatDate, vehicleNo, timeIn, timeOut, leaderSign, custName, custSign, custSignDate)
        VALUES 
        ('$srNum','$leaderId','$treatDate','$vehicleNo','$timeIn','$timeOut','$leaderSig','$custName','$custSig','$custDate')");

    // ── STEP 9: Save termite info (only if filled in) ──────────
    if (!empty($d['no_of_holes'])) {
        $holes    = (int)$d['no_of_holes'];
        $distance = (int)$d['distance_between_holes'];
        $termQty  = (int)$d['qty_termicide_per_hole'];

        $mysqli->query("INSERT INTO termite_post 
            (holeQty, holeDistance, termicideQty, srNum)
            VALUES 
            ($holes,$distance,$termQty,'$srNum')");
    }

    // ── STEP 10: Save team members ─────────────────────────────
    foreach ($d['other_applicators'] ?? [] as $empId) {
        $empId = $mysqli->real_escape_string($empId);
        $mysqli->query("INSERT INTO team_member (srNum, empId) VALUES ('$srNum','$empId')");
    }

    // ── ALL DONE: tell JavaScript it worked ────────────────────
    echo json_encode(['success' => true, 'sr_number' => $srNum]);

} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}