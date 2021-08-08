<?php
//Queries
$upload_q='INSERT INTO videos(mpd_path, thumbnail_path, upload_date, video_name, video_duration, processed, view_count, video_description) VALUES (?,?,?,?,?,?,?,?)';
$process_q='UPDATE videos SET processed=? WHERE video_id=?';
$random_q='SELECT video_id,thumbnail_path, upload_date, video_name, video_duration, view_count FROM videos WHERE processed != 0 ORDER BY rand() LIMIT 12';
$random_lim_q='SELECT video_id,thumbnail_path, upload_date, video_name, video_duration, view_count FROM videos WHERE video_id != ? AND processed !=0 ORDER BY rand() LIMIT 12';
$id_q='SELECT mpd_path, thumbnail_path, upload_date, video_name, processed, view_count, video_description FROM videos WHERE video_id=?';
$search_q="SELECT video_id,thumbnail_path, upload_date, video_name, video_duration, processed, view_count from videos WHERE MATCH(video_name) AGAINST( ? IN NATURAL LANGUAGE MODE)";
$update_views="UPDATE videos SET view_count = view_count + 1 WHERE video_id=?";
?>
