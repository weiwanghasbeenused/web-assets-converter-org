<?php
$submissionURLString = getenv("MYSQL_R_DATABASE_URL");
$submissionURLString = getenv("MYSQL_RW_DATABASE_URL");
$submissionURLString = getenv("MYSQL_FULL_DATABASE_URL");
function db_connect($remote_user) {
    global $submissionURLString;
    if($remote_user === 'guest')
        $url_string_name = "MYSQL_R_DATABASE_URL";
    else if($remote_user === 'main')
        $url_string_name = "MYSQL_RW_DATABASE_URL";
    else if($remote_user === 'admin')
        $url_string_name = "MYSQL_FULL_DATABASE_URL";
    else return null;

    $url_string_raw = getenv($url_string_name);

    if(!$url_string_raw || !$url_string = parse_url($url_string_raw)) return null;

    $host = $url_string["host"];
    $dbse = substr($url_string["path"], 1);
    $user = $url_string['user'];
    $pass = $url_string['pass'];

	$db = new mysqli($host, $user, $pass, $dbse);
	if($db->connect_errno)
		echo "Failed to connect to MySQL: " . $db->connect_error;
	return $db;
}