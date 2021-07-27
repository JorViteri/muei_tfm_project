<?php
include "/etc/server/config.php";
include "/etc/server/cmd_conf.php";
$result_msg = "There has been an error";

$arr_file_types = ['video/webm','video/mp4'];
$file_type = $_FILES['file']['type'];

if (!(in_array($file_type, $arr_file_types))) {
    $result_msg = "File type not valid.";
    $myObj->result_msg = $result_msg;
    die(json_encode($myObj));
}

$file_ext = str_replace('video/', '.', $file_type);
$upload_time = time();
$file_name = $upload_time . '_' . $_POST['name'];
if(isset($_POST['opt_name'])){
    $simple_file_name = $_POST['opt_name'];
} else {
    $simple_file_name = str_replace($file_ext, '', $_POST['name']);
}

$video_description = $_POST['description'];

$out_name = str_replace($file_ext, '.mpd', $file_name);
$dir_name =$dir_p.str_replace($file_ext, '', $file_name);

if (!file_exists($dir_name)) {
    mkdir($dir_name, 0777);
}

$input_path = $dir_name.'/'.$file_name;
$output_path = $dir_name.'/'.$out_name;


move_uploaded_file($_FILES['file']['tmp_name'], $input_path);

$ffmpeg_get_res = sprintf($get_res,$input_path);
exec($ffmpeg_get_res, $out_res);

$width = intval($out_res[0]);

if ($width >= 1080) {
    $video_cmd = $video_1080;
    $audio_cmd = $audio_1080;
} elseif ($width>=720) {
    $video_cmd = $video_720;
    $audio_cmd = $audio_720;
} elseif ($width>=480) {
    $video_cmd = $video_480;
    $audio_cmd = $audio_480;
} elseif ($width>=360) {
    $video_cmd = $video_360;
    $audio_cmd = $audio_360;
} elseif ($width>=240) {
    $video_cmd = $video_240;
    $audio_cmd = $audio_240;
} elseif ($width>=144) {
    $video_cmd = $video_144;
    $audio_cmd = $audio_144;
} else {
    $result_msg = 'Resolution not supported';
    $myObj->result_msg = $result_msg;
    die(json_encode($myObj));
    return;
}

//Obtenemos el thumbnail
$thumbnail_name = str_replace($file_ext, '.png', $file_name);
$thumbnail_path = $dir_name.'/'.$thumbnail_name;
$thumbnail_cmd = sprintf($get_thumbnail,$input_path,$thumbnail_path);
exec($thumbnail_cmd);

//Obtenemos la duracion del video
$dur_cmd = sprintf($get_duration, $input_path);
exec($dur_cmd, $exec_out);
$rest = substr($exec_out[0], 6);
$seconds = round($rest);
$video_duration = str_replace($rest, $seconds, $exec_out[0]);


/***************SQL INSERT****************/

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
$processed = 0;
$views = 0;
$form_date = date('Y-m-d H:i:s', $upload_time);

$final_output_path = $dir_a.$output_path;
$final_thumbnail_path = $dir_a.$thumbnail_path;

$mysqli->select_db($database);

$stmt = $mysqli->prepare($upload_q);
$stmt -> bind_param('sssssiis',$final_output_path, $final_thumbnail_path, $form_date, $simple_file_name, $video_duration, $processed, $views, $video_description);


$query_result = $stmt->execute();
if (!$query_result) {
    if ($stmt->errno == $name_exists){
        $result_msg = "There is already a video with that name.";
    } if ($stmt->errno == $empty_name) {
        $result_msg = "The name of the file is not valid.";
    }

    exec(sprintf($remove_all, $dir_name));
    $myObj->result_msg = $result_msg;
    die(json_encode($myObj));
} else {

    $last_id =$mysqli->insert_id;
    $mysqli->close();

    /*****************LA SECCION DEL RABBITMQ ******************/
    require_once(__DIR__ . '/vendor/autoload.php');
    define("RABBITMQ_HOST", $host);
    define("RABBITMQ_PORT", $rabbitmq_port);
    define("RABBITMQ_USERNAME", $rabbitmq_user);
    define("RABBITMQ_PASSWORD", $rabbitmq_pwd);
    define("RABBITMQ_QUEUE_NAME", $rabbitmq_queue_name);

    $connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(
        RABBITMQ_HOST,
        RABBITMQ_PORT,
        RABBITMQ_USERNAME,
        RABBITMQ_PASSWORD
    );

    $channel = $connection->channel();

    # Create the queue if it doesn't already exist.
    $channel->queue_declare(
        $queue = RABBITMQ_QUEUE_NAME,
        $passive = false,
        $durable = true,
        $exclusive = false,
        $auto_delete = false,
        $nowait = false,
        $arguments = null,
        $ticket = null
    );


    $jobArray = array(
        'video_id' => $last_id,
        'input_path' => $input_path,
        'output_path' => $output_path,
        'video_cmd' => $video_cmd,
        'audio_cmd' => $audio_cmd,
        'video_duration' => $video_duration

    );

    $msg = new \PhpAmqpLib\Message\AMQPMessage(
        json_encode($jobArray, JSON_UNESCAPED_SLASHES),
        array('delivery_mode' => 2) # make message persistent
    );

    $channel->basic_publish($msg, '', RABBITMQ_QUEUE_NAME);

    $myObj->result_msg = "Video uploaded!! You will be able to see It when It is fully processed.";
    die(json_encode($myObj));
    sleep(1);
}



?>