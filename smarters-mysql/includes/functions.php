<?php
// MySQL connection - UPDATE THESE
$db = new mysqli("localhost", "apfkgyeksbf_svg4our2bossman", "uHy64gVeb(*eg3g3GEJHV", "apfkgyeksbf_svg4our2");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

function initializeDatabase($db) {
	$tables = [
    "users" => "CREATE TABLE IF NOT EXISTS users(id INT PRIMARY KEY AUTO_INCREMENT, username TEXT, password TEXT)",
    "dns" => "CREATE TABLE IF NOT EXISTS dns(id INT PRIMARY KEY AUTO_INCREMENT NOT NULL, title TEXT, url TEXT)",
    "maintenance" => "CREATE TABLE IF NOT EXISTS maintenance (id INT PRIMARY KEY AUTO_INCREMENT, title TEXT, body TEXT, mode TEXT)",
    "devices" => "CREATE TABLE IF NOT EXISTS devices (id INT PRIMARY KEY AUTO_INCREMENT, deviceid TEXT, deviceusername TEXT, added_on TEXT)",
    "reports" => "CREATE TABLE IF NOT EXISTS reports (id INT PRIMARY KEY AUTO_INCREMENT, username TEXT, macaddress TEXT, section TEXT, section_category TEXT, report_title TEXT, report_sub_title TEXT, report_cases TEXT, report_custom_message TEXT, stream_name TEXT, stream_id INT)",
    "feedback" => "CREATE TABLE IF NOT EXISTS feedback (id INT PRIMARY KEY AUTO_INCREMENT, username TEXT, macaddress TEXT, feedback_content TEXT)",
    "announcements" => "CREATE TABLE IF NOT EXISTS announcements (id INT PRIMARY KEY AUTO_INCREMENT, title TEXT NOT NULL, message TEXT NOT NULL, created_on TEXT NOT NULL)",
    "announcement_views" => "CREATE TABLE IF NOT EXISTS announcement_views (announcement_id INT NOT NULL, deviceid TEXT NOT NULL, FOREIGN KEY (announcement_id) REFERENCES announcements(id))",
    "vpn" => "CREATE TABLE IF NOT EXISTS vpn(id INT PRIMARY KEY AUTO_INCREMENT NOT NULL, vpn_country TEXT, vpn_file_name TEXT, username TEXT, password TEXT, embed TEXT)",
    "advertisement" => "CREATE TABLE IF NOT EXISTS advertisement (id INT PRIMARY KEY AUTO_INCREMENT, title TEXT, text TEXT)",
    "ads" => "CREATE TABLE IF NOT EXISTS ads (id INT PRIMARY KEY AUTO_INCREMENT, title TEXT, text TEXT)",
    "ads2" => "CREATE TABLE IF NOT EXISTS ads2 (id INT PRIMARY KEY AUTO_INCREMENT, text TEXT)",
    "ads2_images" => "CREATE TABLE IF NOT EXISTS ads2_images (id INT PRIMARY KEY AUTO_INCREMENT, ads2_id INT, url TEXT, FOREIGN KEY (ads2_id) REFERENCES ads2(id))",
    "settings" => "CREATE TABLE IF NOT EXISTS settings (id INT PRIMARY KEY AUTO_INCREMENT, tmdb_api_enabled INT)"
];

	$insert = [
		"advertisement" => "INSERT IGNORE INTO advertisement (title, text) VALUES('','')",
		"maintenance" => "INSERT IGNORE INTO maintenance (title, body, mode) VALUES('','','no')"
		];

	foreach ($tables as $tableName => $createStmt) {
		$db->query($createStmt);
	}
	
	foreach ($insert as $tableName => $createStmt) {
		$rows = $db->query("SELECT COUNT(*) as count FROM ".$tableName);
		$row = $rows->fetch_assoc();
		$numRows = $row['count'];
		if ($numRows == 0){
			$db->query($createStmt);
		}
	}
}

function sanitize($data) {
	$data = trim($data);
	$data = htmlspecialchars($data, ENT_QUOTES );
	$data = $db->real_escape_string($data);
	return $data;
}