<?php
error_reporting(1);
$salt = 'NB!@#12ZKWd';
$rand = $_POST['r'];

// MySQL connection - UPDATE THESE
$db = new mysqli("localhost", "apfkgyeksbf_svg4our2bossman", "uHy64gVeb(*eg3g3GEJHV", "apfkgyeksbf_svg4our2");
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$res = $db->query('SELECT * FROM dns'); 
$rows = array();
while ($row = $res->fetch_assoc()) {
	$rows[] = $row['url'];
}
$dns = rtrim(implode(',', $rows), ',');
$arry = str_replace("\\", "", "$dns");

$resV = $db->query('SELECT * FROM vpn'); 
$vpn_arr = array(); 
while ($rowV = $resV->fetch_assoc()) {
	$row_array['out_name'] = $rowV['vpn_file_name']; 
	$row_array['username'] = $rowV['username']; 
	$row_array['password'] = $rowV['password']; 
	array_push($vpn_arr,$row_array);  
}

$final_vpn = base64_encode('{"VPNs":'.json_encode($vpn_arr).'}');
$final = '{"status":true,"su":"'.$dns.'","sc":"'.md5($arry."*".$salt."*".$rand).'","ndd":"","vpn":"'.$final_vpn.'"}';

$db->close();
echo $final;
?>