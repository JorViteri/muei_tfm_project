<?php
include "/etc/server/config.php";
include "/etc/server/sql_cmd.php";

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

$str = str_replace(', ',' ',trim($_GET['name']));
$stmt = $mysqli->prepare($search_q);
$stmt -> bind_param('s',$str);

$query_result = $stmt->execute();
if (!$query_result) {
    $result_msg = "Querry Error!";
    $myObj->result_msg = $result_msg;
    die(json_encode($myObj));
}

$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);
$json_response= json_encode($data);

echo($json_response);

$stmt->close();
$mysqli->close();



?>