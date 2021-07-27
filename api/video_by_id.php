<?php
include "/etc/server/config.php";


$mysqli = mysqli_init();
if (!$mysqli) {
    $result_msg = "Can't start MySQLI";
    $myObj->result_msg = $result_msg;
    die(json_encode($myObj));
}

if (!$mysqli->real_connect($host, $database_user, $database_pwd, $database)) {
    $result_msg = "Can't connect to DB";
    $myObj->result_msg = $result_msg;
    die(json_encode($myObj));
}
$mysqli->select_db($database);

$id = intval($_GET['id']);


$stmt = $mysqli->prepare($update_views);
$stmt -> bind_param('i',$id);
$query_result = $stmt->execute();
if (!$query_result) {
    $result_msg = "Querry Error!";
    $myObj->result_msg = $result_msg;
    die(json_encode($myObj));
}

$stmt = $mysqli->prepare($id_q);
$stmt -> bind_param('i',$id);

$query_result = $stmt->execute();
if (!$query_result) {
    die("Query Error!");
}

$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);
$json_response= json_encode($data);

echo($json_response);

$stmt->close();
$mysqli->close();

?>