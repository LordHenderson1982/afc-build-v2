<?php 
include ('includes/header.php');

$table_name = 'update_apk';

$db->query("CREATE TABLE IF NOT EXISTS {$table_name}(id INT PRIMARY KEY AUTO_INCREMENT,app_name TEXT,version TEXT ,apk TEXT,package TEXT,u_date TEXT)");

$rows = $db->query("SELECT COUNT(*) as count FROM {$table_name}");
$row = $rows->fetch_assoc();
$numRows = $row['count'];
if ($numRows == 0){
	$db->query("INSERT INTO {$table_name}(app_name,version,apk,package,u_date) VALUES('','','','','')");
}

$res = $db->query("SELECT * FROM {$table_name} WHERE id='1'");
$rowU=$res->fetch_assoc();

if(isset($_POST['submit'])){
	$u_date = date("Y-m-d");
	$db->query("UPDATE {$table_name} SET	app_name='".sanitize($_POST['app_name'])."', version='".sanitize($_POST['version'])."', apk='".sanitize($_POST['apk'])."', package='".sanitize($_POST['package'])."', u_date='{$u_date}'WHERE id='1' ");
	echo "<script>window.location.href='". basename($_SERVER["SCRIPT_NAME"])."?status=1'</script>";
}

?>

        <div class="col-md-6 mx-auto">
            <div class="card-body">
                <div class="card bg-primary text-white">
                    <div class="card-header card-header-warning">
                        <center>
                            <h2><i class="icon icon-bullhorn"></i> Update</h2>
                        </center>
                    </div>
                    
                    <div class="card-body">
                        <div class="col-12">
                            <h3>Push Update</h3>
                        </div>
                            <form method="post">
                                <div class="form-group">
                                    <label class="form-label " for="version">App Name</label>
                                        <input class="form-control" placeholder="My App" name="app_name" value="<?=$rowU['app_name'] ?>" type="text"/>
                                </div>
                                <div class="form-group">
                                    <label class="form-label " for="version">Version Number</label>
                                        <input class="form-control" placeholder="3.0" name="version" value="<?=$rowU['version'] ?>" type="text"/>
                                </div>
                                <div class="form-group">
                                    <label class="form-label " for="package">Package name</label>
                                        <input class="form-control" placeholder="com.package.name" name="package" value="<?=$rowU['package'] ?>" type="text"/>
                                </div>
                                <div class="form-group">
                                    <label class="form-label " for="apk">APK URL</label>
                                        <input class="form-control" placeholder="http://apkurl.com/yourapp.apk" name="apk" value="<?=$rowU['apk'] ?>" type="text"/>
                                </div>
                                <div class="form-group">
                                    <center>
                                        <button class="btn btn-info " name="submit" type="submit">
                                            <i class="icon icon-check"></i> Submit
                                        </button>
                                    </center>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>

<?php include ('includes/footer.php');?>