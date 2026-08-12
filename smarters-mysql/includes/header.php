<?php

ini_set('display_errors', 0);
if(session_status() === PHP_SESSION_NONE) session_start();

include('includes/functions.php');

// MySQL connection - UPDATE THESE
$db = new mysqli("localhost", "apfkgyeksbf_svg4our2bossman", "uHy64gVeb(*eg3g3GEJHV", "apfkgyeksbf_svg4our2");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

initializeDatabase($db);

//user check
$log_check = $db->query("SELECT * FROM users WHERE id='1'");
$roe = $log_check->fetch_assoc();
$loggedinuser = @$roe['username'];

//login check
if (!isset($_SESSION['name']) == $loggedinuser) {
    echo "<script>window.location.href='index.php'</script>";
    exit();
}

//current file var
$base_file = basename($_SERVER["SCRIPT_NAME"]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="FTG">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="css/themes/darkly/bootstrap.css" rel="stylesheet" title="main">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.3/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="css/simple-sidebar.css" rel="stylesheet">
</head>
<body>
<style>
body{
  background-color: #181828;
  background-image: url("./img/binding_dark.webp");
  color #fff;
}
#particles-js{
  background-size: cover;
  background-position: 50% 50%;
  background-repeat: no-repeat;
  background: #8000FF;
  display: flex;
  justify-content: center;
  align-items: center;
}
.particles-js-canvas-el{
  position: fixed;
}
#pageMessages {
  left: 50%;
  transform: translateX(-50%);
  position:fixed; 
  text-align: center;
  top: 5px;
  width: 60%;
  z-index:9999; 
  border-radius:0px
}
.alert {
  position: relative;
}
.alert .close {
  position: absolute;
  top: 5px;
  right: 5px;
  font-size: 1em;
}
.alert .fa {
  margin-right:.3em;
}
</style>
<div id="js-particles"></div>
<body> 

  <div class="d-flex" id="wrapper">
    <!-- Sidebar-->
    <div class="" id="sidebar-wrapper">
      <div class="sidebar-heading">Bet3 Smarters V4 </div>
      <span><a class="list-grup-item" href="https://t.me/FireTVGuru" target="_blank">&nbsp&nbsp&nbsp&nbsp&#169; <?=date("Y")?> * FTG Panels * </a> </span>
      <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action " href="dns.php">
        <i class="fa fa-cogs"></i>&nbsp;&nbsp;    DNS Settings </a>
        <a class="list-group-item list-group-item-action " href="ads.php">
        <i class="fa fa-image"></i>&nbsp;&nbsp;    Adverts Settings </a>

        <a class="list-group-item list-group-item-action " href="note.php">
        <i class="fa fa-commenting" ></i>&nbsp;&nbsp;    In-app Messages </a>
        
        <a class="list-group-item list-group-item-action " href="reports.php">
        <i class="fa fa-commenting" ></i>&nbsp;&nbsp;    In-app Reports </a>
        <a class="list-group-item list-group-item-action " href="feedback.php">
        <i class="fa fa-commenting" ></i>&nbsp;&nbsp;    In-app Feedback </a>
        <a class="list-group-item list-group-item-action " href="vpn.php">
        <i class="fa fa-shield" ></i>&nbsp;&nbsp;    OVPN Settings </a>
        <a class="list-group-item list-group-item-action " href="maint.php">
        <i class="fa fa-wrench" >&nbsp;&nbsp;</i>  Maintenance Mode </a>
        <a class="list-group-item list-group-item-action " href="user.php">
        <i class="fa fa-user" ></i>&nbsp;&nbsp;    Update credentials </a>
      </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">

      <nav class="navbar navbar-expand-lg navbar-dark ">

        <button class="btn btn-primary" id="menu-toggle"><img src="img/logo.png" width="25" height="25" class="d-flex justify-content-center text-allign centre" alt=""></button>
        
      &nbsp;&nbsp;
        <div class="center" id="pageMessages"></div>
        <a href="logout.php" class="btn btn-danger ml-auto mr-1">Logout</a>
      </nav>

      <div class="container-fluid"><br>