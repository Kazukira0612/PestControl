<?php
include('../db_conn/db_conn.php');

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';

switch($action) {
    case 'dashboardStats':
        echo json_encode([
            'success' => true,
            'total' => 10,
            'signed' => 5,
            'draft' => 5,
            'today' => 2,
            'recent' => [
                ['srNum'=>'SR-001','comName'=>'Acme','servAdd'=>'123 Street','planDate'=>'2026-03-27','status'=>'signed']
            ]
        ]);
        break;
   
    default:
        echo json_encode(['success'=>false,'message'=>'Unknown action']);
}

/* ======================================================
   ADD CUSTOMER
====================================================== */
if ($action === 'addCustomer') {

    $name  = $data['comName'] ?? '';
    $phone = $data['comPhone'] ?? '';

    $sql = "INSERT INTO customer (comName, comPhone)
            VALUES (?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $name, $phone);
    $ok = mysqli_stmt_execute($stmt);

    echo json_encode(["success"=>$ok]);
    exit;
}

/* ======================================================
   UPDATE CUSTOMER
====================================================== */
if ($action === 'updateCustomer') {

    $id    = $data['comId'] ?? 0;
    $name  = $data['comName'] ?? '';
    $phone = $data['comPhone'] ?? '';

    $sql = "UPDATE customer
            SET comName=?, comPhone=?
            WHERE comId=?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi",
        $name, $phone, $id);

    $ok = mysqli_stmt_execute($stmt);

    echo json_encode(["success"=>$ok]);
    exit;
}

/* ======================================================
   DASHBOARD STATS
====================================================== */
if ($action === 'dashboardStats') {

  $total  = mysqli_fetch_row(
    mysqli_query($conn,"SELECT COUNT(*) FROM report")
  )[0];

  $signed = mysqli_fetch_row(
    mysqli_query($conn,"SELECT COUNT(*) FROM report WHERE status='signed'")
  )[0];

  $draft  = mysqli_fetch_row(
    mysqli_query($conn,"SELECT COUNT(*) FROM report WHERE status='draft'")
  )[0];

  $today  = mysqli_fetch_row(
    mysqli_query($conn,"
      SELECT COUNT(*) FROM report
      WHERE DATE(created_at)=CURDATE()
    ")
  )[0];

  $recent = [];

  $res = mysqli_query($conn,"
      SELECT srNum, comName, servAdd, planDate, status
      FROM report
      ORDER BY id DESC
      LIMIT 5
  ");

  while($row = mysqli_fetch_assoc($res)){
      $recent[] = $row;
  }

  echo json_encode([
    "success"=>true,
    "total"=>$total,
    "signed"=>$signed,
    "draft"=>$draft,
    "today"=>$today,
    "recent"=>$recent
  ]);
  exit;
}
?>