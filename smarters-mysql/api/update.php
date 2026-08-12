<?php
error_reporting(0);
// MySQL connection - UPDATE THESE
$db = new mysqli("localhost", "apfkgyeksbf_svg4our2bossman", "uHy64gVeb(*eg3g3GEJHV", "apfkgyeksbf_svg4our2");
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$res = $db->query('SELECT * FROM update_apk'); 
$row = $res->fetch_assoc();

if ($_GET['id'] == $row['package']){
	header("Location: {$row['url']}");
	exit();
}else{
	echo '
<!DOCTYPE html>
<html>
<head>
<style>
.table {
  display: table;         
  width: auto;         
  background-color: #eee;         
  border: 1px solid #666666;         
  border-spacing: 5px;
}
.hAyfc {
  display: table-row;
  width: auto;
  clear: both;
}
.BgcNfc {
  float: left;
  display: table-column;         
  width: 200px;         
  background-color: #ccc;  
}
</style>
</head>
<body>
<div class="table">
<div class="hAyfc"><div class="BgcNfc">App Name</div><span class="htlgb"><div class="IQ1z0d"><span class="htlgb">' . $row['app_name'] . '</span></div></span></div>
<div class="hAyfc"><div class="BgcNfc">Updated</div><span class="htlgb"><div class="IQ1z0d"><span class="htlgb">' . $row['u_date'] . '</span></div></span></div>
<div class="hAyfc"><div class="BgcNfc">Package Name</div><span class="htlgb"><div class="IQ1z0d"><span class="htlgb">' . $row['package'] . '</span></div></span></div>
<div class="hAyfc"><div class="BgcNfc">Current Version</div><span class="htlgb"><div class="IQ1z0d"><span class="htlgb">' . $row['version'] . '</span></div></span>
<div class="hAyfc"><div class="BgcNfc">Created By</div><span class="htlgb"><div class="IQ1z0d"><span class="htlgb"><a href="https://t.me/FireTVGuru">FTG Panels</a></span></div></span>
</div>
</body>
</html>
';
}
$db->close();
?>