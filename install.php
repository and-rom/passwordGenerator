<!DOCTYPE html>
<html>
<head>
  <title>Настройка БД для <?=basename(__DIR__)?> на <?=$_SERVER['SERVER_NAME']?></title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="utf-8">
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="">
</head>
<body>
<?php
$config = isset($_POST['only_config']);
if (!empty($_POST)) {
  if (!empty($_POST['dbhost']) & !empty($_POST['dbuser']) & !empty($_POST['dbpass']) & !empty($_POST['dbname'])) {
      $dbhost = $_POST['dbhost'];
      $dbname = $_POST['dbname'];
      $dbuser = $_POST['dbuser'];
      $dbpass = $_POST['dbpass'];
    if (filesize("config_db.php") <= 0 and !$config) {
      $link = mysqli_connect($dbhost, $dbuser, $dbpass) or die("Connection error: " . mysqli_error($link));
      mysqli_set_charset($link,"utf8") or die("Error: " . mysqli_error($link));
      mysqli_select_db($link,$dbname) or die("Error: " . mysqli_error($link));
      echo "Connection established with " . mysqli_get_host_info ($link) . ".<br />";
      echo "Opening SQL file...<br />";
      $sql = file_get_contents("./pwgen.sql");
      if (!$sql) {die ("Error opening SQL file");}
      echo "Processing SQL file.<br />";
      mysqli_multi_query($link,$sql) or die("Error: " . mysqli_error($link));
      mysqli_close($link);
      echo "Done.<br />";
      $config = True;
    } 
    if ($config) {
      echo "Opening config file...<br />";
      $file = fopen("config_db.php","w");
      $config = "<?php\n";
      $config .= '  define("DBHOST", "' . $dbhost . '");' . "\n";
      $config .= '  define("DBUSER", "' . $dbuser . '");' . "\n";
      $config .= '  define("DBPASS", "' . $dbpass . '");' . "\n";
      $config .= '  define("DBNAME", "' . $dbname . '");' . "\n";
      $config .= "?>";
      echo "Writing config file...<br />";
      fwrite($file,$config);
      fclose($file);
      echo "Config file done.<br />";
      echo "<a href=./>Go home</a>";
      $ok = "ok";
    } else {
      die("Installation has already been completed.");
    }
  } else {
    die("Not all fields are filled in.");
  }
}

if (empty($_POST) | !isset($ok)) {
?>
  <form id="form-setup" method="post" action="<?=$_SERVER['SCRIPT_NAME']?>">
  <fieldset>
    <legend>Setting up the database</legend>
	<fieldset>
	  <legend>DB Server</legend>
	  <label for="dbhost">User: </label><input id="dbhost" type="text" name="dbhost"/>
	</fieldset>
	<fieldset>
	  <legend>DB User</legend>
	  <label for="dbuser">User: </label><input id="dbuser" type="text" name="dbuser"/><br />
	  <label for="dbpass">Pass: </label><input id="dbpass" type="password" name="dbpass"/>
	</fieldset>
	<fieldset>
	  <legend>DB</legend>
	  <label for="dbname">DB Name: </label><input id="dbname" type="text" name="dbname"/>
	</fieldset>
        <input id="only_config" type="checkbox" name="only_config" checked/><label for="only_config">Only create config file</label><br />
	<input type="submit" name="submit" value="OK"/>
  </fieldset>
  </form>
<?
}
?>
</body>
</html>
