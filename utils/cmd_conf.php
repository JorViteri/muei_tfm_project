<?php
//1080 Video
$video_1080 = "-map 0 -map 0 -map 0 -map 0 -map 0 -map 0 -c:a aac -c:v libx264 -movflags faststart -preset fast -b:v:0 6250k -maxrate:v:0 6250k -bufsize:v:0 12500k -b:v:1 4750k -maxrate:v:1 4750k -bufsize:v:1 9500k -b:v:2 1250k -maxrate:v:2 1250k -bufsize:v:2 2500k -b:v:3 700k -maxrate:v:3 700k -bufsize:v:3 1400k -b:v:4 500k -maxrate:v:4 500k -bufsize:v:4 1000k -b:v:5 300k -maxrate:v:5 300k -bufsize:v:5 600k -s:v:0 1920x1080 -s:v:1 1280x720 -s:v:2 854x480 -s:v:3 640x360 -s:v:4 426x240 -s:v:5 256x144";
$audio_1080 = "-ar:a:2 22050 -ar:a:3 22050 -ar:a:4 22050 -ar:a:5 22050";
//720 Video
$video_720 = "-map 0 -map 0 -map 0 -map 0 -map 0 -c:a aac -c:v libx264 -movflags faststart -preset fast -b:v:0 4750k -maxrate:v:0 4750k -bufsize:v:0 9500k -b:v:1 1250k -maxrate:v:1 1250k -bufsize:v:1 2500k -b:v:2 700k -maxrate:v:2 700k -bufsize:v:2 1400k -b:v:3 500k -maxrate:v:3 500k -bufsize:v:3 1000k -b:v:4 300k -maxrate:v:4 300k -bufsize:v:4 600k -s:v:0 1280x720 -s:v:1 854x480 -s:v:2 640x360 -s:v:3 426x240 -s:v:4 256x144";
$audio_720 = "-ar:a:1 22050 -ar:a:2 22050 -ar:a:3 22050 -ar:a:4 22050";
//480 Video
$video_480 = "-map 0 -map 0 -map 0 -map 0 -c:a aac -c:v libx264 -movflags faststart -preset fast -b:v:0 1250k -maxrate:v:0 1250k -bufsize:v:0 2500k -b:v:1 700k -maxrate:v:1 700k -bufsize:v:1 1400k -b:v:2 500k -maxrate:v:2 500k -bufsize:v:2 1000k -b:v:3 300k -maxrate:v:3 300k -bufsize:v:3 600k -s:v:0 854x480 -s:v:1 640x360 -s:v:2 426x240 -s:v:3 256x144";
$audio_480 = "-ar:a:0 22050 -ar:a:1 22050 -ar:a:2 22050 -ar:a:3 22050";
//360 Video
$video_360 = "-map 0 -map 0 -map 0 -c:a aac -c:v libx264 -movflags faststart -preset fast -b:v:0 700k -maxrate:v:0 700k -bufsize:v:0 1400k -b:v:1 500k -maxrate:v:1 500k -bufsize:v:1 1000k -b:v:2 300k -maxrate:v:2 300k -bufsize:v:2 600k -s:v:0 640x360 -s:v:1 426x240 -s:v:2 256x144";
$audio_360 = "-ar:a:0 22050 -ar:a:1 22050 -ar:a:2 22050";
//240 Video
$video_240 = "-map 0 -map 0 -c:a aac -c:v libx264 -movflags faststart -preset fast -b:v:0 500k -maxrate:v:0 500k -bufsize:v:0 1000k -b:v:1 300k -maxrate:v:1 300k -bufsize:v:1 600k -s:v:0 426x240 -s:v:1 256x144";
$audio_240 = "-ar:a:0 22050 -ar:a:1 22050";
//144 Video
$video_144 = "-map 0 -c:a aac -c:v libx264 -movflags faststart -preset fast -b:v:0 300k -maxrate:v:0 300k -bufsize:v:0 600k -s:v:0 256x144";
$audio_144 = "-ar:a:0 22050";

//Get resolution
$get_res = 'ffprobe -v error -select_streams v:0 -show_entries stream=height -of csv=s=x:p=0 "%s"';
$get_thumbnail = 'ffmpeg -i "%s" -ss 00:00:01.000 -vframes 1 "%s"';
$get_duration = 'ffmpeg -i "%s" 2>&1 | grep "Duration"| cut -d " " -f 4 | sed s/,//';
$remove = 'rm "%s"';
$remove_all='rm -r "%s"';
$gen_mpd='ffmpeg -re -i "%s" %s -metadata:s:a language=zxx %s -use_timeline 1 -use_template 1 -adaptation_sets "id=0,streams=v id=1,streams=a" -f dash "%s"';
?>
