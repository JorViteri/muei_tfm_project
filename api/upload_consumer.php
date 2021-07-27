<?php

/*
 * This is a worker process that will run forever and execute tasks as they appear in the queue.
 */

include "/etc/server/config.php";
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

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


echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function ($msg) {
    include "/etc/server/config.php";
    include "/etc/server/cmd_conf.php";
    echo " [x] Received ", $msg->body, "\n";
    $job = json_decode($msg->body);
    $video_id = $job->video_id;
    $input_path = $job->input_path;
    $video_cmd = $job->video_cmd;
    $audio_cmd = $job->audio_cmd;
    $output_path = $job->output_path;

    $ffmpeg_gen_mpd=sprintf($gen_mpd,$input_path,$video_cmd,$audio_cmd,$output_path);
    exec($ffmpeg_gen_mpd);
    exec(sprintf($remove, $input_path));

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

    $processed = 1;
    $mysqli->select_db($database);

    $stmt = $mysqli->prepare($process_q);
    $stmt -> bind_param('ii', $processed, $video_id);

    $query_result = $stmt->execute();
    if (!$query_result) {
        $myObj->result_msg = $stmt->error;
        $myObj->errno=$stmt->errno;
        die(json_encode($myObj));
    }

    $mysqli->close();


    /************************MEJOR PONER TODO ESTE SQL EN UNA FUNCION A PARTE */

    echo " [x] Done", "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_qos(null, 1, null);

$channel->basic_consume(
    $queue = RABBITMQ_QUEUE_NAME,
    $consumer_tag = '',
    $no_local = false,
    $no_ack = false,
    $exclusive = false,
    $nowait = false,
    $callback
);

// This is the best I could find for a non-blocking wait. Unfortunately one has to have
// a timeout (for now), and simply setting nonBlocking=true on its own appears do to nothing.
// An exception is thrown when the timout is reached, breaking the loop, and you should catch it
// to exit gracefully.
try {
    while (count($channel->callbacks)) {
        print "running non blocking wait." . PHP_EOL;
        $channel->wait();
        sleep(3);
    }
} catch (Exception $e) {
    print "There are no more tasks in the queue." . PHP_EOL;
}


$channel->close();
$connection->close();
