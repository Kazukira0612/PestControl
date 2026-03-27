<?php
include ('db_conn.php');

//applicant table
$srnum = mysqli_real_escape_string($conn, $_POST['srnum']);
$purposetreat = mysqli_real_escape_string($conn, $_POST['purposetreat']);
$plandate = mysqli_real_escape_string($conn, $_POST['plandate']);
$premisetype = mysqli_real_escape_string($conn, $_POST['premisetype']);
$treattime = mysqli_real_escape_string($conn, $_POST['treattime']);
$servadd = mysqli_real_escape_string($conn, $_POST['servadd']);
$specinstruct = mysqli_real_escape_string($conn, $_POST['specinstruct']);
$remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
$custfeedback = mysqli_real_escape_string($conn, $_POST['custfeedback']);
$comid = mysqli_real_escape_string($conn, $_POST['comid']);
$factorno = mysqli_real_escape_string($conn, $_POST['factorno']);

$sql = "INSERT INTO applicant (srNum, purposeTreat, planDate, premiseType, 
        treatTime, servAdd, specInstruct, remarks, custFeedback, comId, factoryNo) 
        VALUES ('$srnum', '$purposetreat', '$plandate', '$premisetype', '$treattime',
        '$servadd', '$specinstruct', '$remarks', '$custfeedback', '$comid', '$factorno');";
    
//applicant_pest table
if (isset($_POST['pest']) && is_array($_POST['pest'])) {
    foreach ($_POST['pest'] as $pesttype) {
        $sql .= "INSERT INTO applicant_pest (srNum, pestType) VALUES ('$srnum', '$pesttype');";
    }
}

//pesticide table
if (isset($_POST['activeitem']) && is_array($_POST['activeitem'])) {
    for($i = 0; $i < count($_POST['activeitem']); $i++) {

        if (empty($_POST['activeitem'][$i]) || empty($_POST['tradename'][$i]) || empty($_POST['class'][$i]) || 
            empty($_POST['dilutesolution'][$i]) || empty($_POST['methodapply'][$i]) || 
            empty($_POST['totarea'][$i]) || empty($_POST['totqtydilute'][$i])) {
            continue;
        }
        $activeitem = mysqli_real_escape_string($conn, $_POST['activeitem'][$i]);
        $tradename = mysqli_real_escape_string($conn, $_POST['tradename'][$i]);
        $class = mysqli_real_escape_string($conn, $_POST['class'][$i]);
        $dilutesolution = mysqli_real_escape_string($conn, $_POST['dilutesolution'][$i]);
        $methodapply = mysqli_real_escape_string($conn, $_POST['methodapply'][$i]);
        $totalarea = mysqli_real_escape_string($conn, $_POST['totarea'][$i]);
        $totalqtydilute = mysqli_real_escape_string($conn, $_POST['totqtydilute'][$i]);

        $sql .= "INSERT INTO pesticide (activeItem, tradeName, class, diluteSolution,
                methodApply, totalArea, totalQtyDilute, srNum) 
                VALUES ('$activeitem', '$tradename', '$class', '$dilutesolution', 
                '$methodapply', '$totalarea', '$totalqtydilute', '$srnum');";
    }
}

//termite_post table
$termiteid = mysqli_real_escape_string($conn, $_POST['termiteid']);
$holeqty = mysqli_real_escape_string($conn, $_POST['holeqty']);
$holedistance = mysqli_real_escape_string($conn, $_POST['holedistance']);
$termicideqty = mysqli_real_escape_string($conn, $_POST['termicideqty']);

$sql .= "INSERT INTO termite_post (termiteId, holeQty, holeDistance, termicideQty, srNum) 
        VALUES ('$termiteid', '$holeqty', '$holedistance', '$termicideqty', '$srnum');";
        
//pest_status table
if (isset($_POST['$peststatname']) && is_array($_POST['$peststatname'])) {
    for($i = 0; $i < count($_POST['$peststatname']); $i++) {

        if (empty($_POST['$peststatname'][$i]) || empty($_POST['statlvl'][$i]) || 
            empty($_POST['statlocation'][$i]) || empty($_POST['statremark'][$i])) {
            continue;
        }
        $peststatname = mysqli_real_escape_string($conn, $_POST['$peststatname'][$i]);
        $statlvl = mysqli_real_escape_string($conn, $_POST['statlvl'][$i]);
        $statlocation = mysqli_real_escape_string($conn, $_POST['statlocation'][$i]);
        $statremark = mysqli_real_escape_string($conn, $_POST['statremark'][$i]);

        $sql .= "INSERT INTO pest_status (pestStatName, statLvl, statLocation, statRemark, srNum) 
                VALUES ('$peststatname', '$statlvl', '$statlocation', '$statremark', '$srnum');";
    }
}

//recommendation table
if (isset($_POST['recomtype']) && is_array($_POST['recomtype'])) {
    for($i = 0; $i < count($_POST['recomtype']); $i++) {

        if (empty($_POST['recomtype'][$i]) || empty($_POST['recommendation'][$i])) {
            continue;
        }
        $recomtype = mysqli_real_escape_string($conn, $_POST['recomtype'][$i]);
        $recommendation = mysqli_real_escape_string($conn, $_POST['recommendation'][$i]);

        $sql .= "INSERT INTO recommendation (recomType, recommendation, srNum) 
                VALUES ('$recomtype', '$recommendation', '$srnum');";
    }
}

//applicator table
$leaderid = mysqli_real_escape_string($conn, $_POST['leaderid']);
$treatdate = mysqli_real_escape_string($conn, $_POST['treatdate']);
$vehicleno = mysqli_real_escape_string($conn, $_POST['vehicleno']);
$timeins = mysqli_real_escape_string($conn, $_POST['timeins']);
$timeouts = mysqli_real_escape_string($conn, $_POST['timeouts']);
$leadersign = mysqli_real_escape_string($conn, $_POST['leadersign']);
$custname = mysqli_real_escape_string($conn, $_POST['custname']);
$custsign = mysqli_real_escape_string($conn, $_POST['custsign']);
$custsigndate = mysqli_real_escape_string($conn, $_POST['custsigndate']);

$sql .= "INSERT INTO applicator (srNum, leaderId, treatDate, vehicleNo, timeIns, 
        timeOuts, leaderSign, custName, custSign, custSignDate) 
        VALUES ('$srnum', '$leaderid', '$treatdate', '$vehicleno', '$timeins', '$timeouts', 
        '$leadersign', '$custname', '$custsign', '$custsigndate');";

//team_member table
if (isset($_POST['empid']) && is_array($_POST['empid'])) {
    for($i = 0; $i < count($_POST['empid']); $i++) {

        if (empty($_POST['empid'][$i])) {
            continue;
        }
        $membername = mysqli_real_escape_string($conn, $_POST['membername'][$i]);
        
        $sql .= "INSERT INTO team_member (srNum, empId) 
                VALUES ('$srnum', '$memberid');";
    }
}

if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>