<?php
@session_start();

// MySQL connection - UPDATE THESE
$db = new mysqli("localhost", "apfkgyeksbf_svg4our2bossman", "uHy64gVeb(*eg3g3GEJHV", "apfkgyeksbf_svg4our2");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Initialize database tables
$db->query("CREATE TABLE IF NOT EXISTS users(id INT PRIMARY KEY AUTO_INCREMENT, username TEXT, password TEXT)");

$log_check = $db->query("SELECT * FROM users WHERE id='1'");
$roe = $log_check->fetch_assoc();
$loggedinuser = @$roe['username'];

if (isset($_SESSION['name']) == $loggedinuser) {
    header("location:"."dns.php");
}

$rows = $db->query("SELECT COUNT(*) as count FROM users");
$row = $rows->fetch_assoc();
$numRows = $row['count'];
if ($numRows == 0){
    $db->query("INSERT INTO users(id, username, password) VALUES('1', 'admin', 'admin')");
    $db->close();
}

if (isset($_POST["login"])){
    if(!$db){
        echo $db->error;
    } else {
    }
    $username = $_POST["username"];
    $password = $_POST["password"];
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $ret = $stmt->get_result();
    
    while($row = $ret->fetch_assoc()){
        $id=$row['id'];
        $username=$row['username'];
        $password_db=$row['password'];
    }
    if ($id!=""){
        if ($password_db==$password){
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            if ($_POST['username'] == 'admin'){
                header('Location: user.php');
            }else{
                header('Location: dns.php');
            }
        }else{
        header('Location: index.php?error=1');
        }
    }else{
        header('Location: index.php?error=1');
    }
    $db->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="FTG">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/css.css">
    <title>FTG Panel</title>
</head>
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
</style>
<div id="js-particles"></div>
<br><br>
<div class="container">
    <div class="row">
        <div class="col-lg-4 mx-md-auto">
            <div class="text-center">
                <img class="w-75 p-3" src="./img/logo.png" alt="">
            </div>
            <br>
            <form method="post">
                <div class="form-group">
                    <input type="text" class="form-control form-control-lg"
                           placeholder="Username" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control form-control-lg"
                           placeholder="Password" name="password" required>
                </div>
                <input type="submit" class="btn btn-warning btn-lg btn-block" value="Log In" name="login">
            </form>
            <br>
            <center><a class="list-grup-item" href="https://t.me/FireTVGuru" target="_blank">&nbsp&nbsp&nbsp&nbsp&#169; <?=date("Y")?> * FTG Panels * </a></center>
        </div>
    </div>
</div>
<br><br>

<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script src="js/particles.js"></script>