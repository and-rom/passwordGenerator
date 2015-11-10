<?php
error_reporting(0);
?>
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
    if (!file_exists("config_db.php") and !$config) {
      $link = mysqli_connect($dbhost, $dbuser, $dbpass) or die("Ошибка соединения: " . mysqli_error($link));
      mysqli_set_charset($link,"utf8") or die("Ошибка: " . mysqli_error($link));
      mysqli_select_db($link,$dbname) or die("Ошибка: " . mysqli_error($link));
      echo "Соединение установлено с " . mysqli_get_host_info ($link) . ".<br />";
      echo "Открываем SQL файл...<br />";
      $sql = file_get_contents("./pwgen.sql");
      if (!$sql) {
        die ("Ошибка открытия SQL файла.");
      }
      echo "Обрабатываем SQL файл...<br />";
      mysqli_multi_query($link,$sql) or die("Ошибка: " . mysqli_error($link));
      mysqli_close($link);
      echo "Готово.<br />";
      $config = True;
    }
    if ($config) {
      echo "Открываем файл настроек...<br />";
      $file = fopen("config_db.php","w") or die("Ошибка открытия файла настроек.");
      $config = "<?php\n";
      $config .= '  define("DBHOST", "' . $dbhost . '");' . "\n";
      $config .= '  define("DBUSER", "' . $dbuser . '");' . "\n";
      $config .= '  define("DBPASS", "' . $dbpass . '");' . "\n";
      $config .= '  define("DBNAME", "' . $dbname . '");' . "\n";
      $config .= "?>";
      echo "Записываем файл настроек...<br />";
      fwrite($file,$config) or die("Ошибка записи файла настроек.");
      fclose($file);
      echo "Файл настроек записан.<br />";
      echo "<a href=./>На главную</a>";
      $ok = "ok";
    } else {
      die("Установка была проведена ранее.");
    }
  } else {
    die("Не все поля заполнены.");
  }
}

if (empty($_POST) | !isset($ok)) {
?>
  <form id="form-setup" method="post" action="<?=$_SERVER['SCRIPT_NAME']?>">
  <fieldset>
    <legend>Настройка базы данных</legend>
	<fieldset>
	  <legend>Сервер</legend>
	  <label for="dbhost">Адрес: </label><input id="dbhost" type="text" name="dbhost"/>
	</fieldset>
	<fieldset>
	  <legend>Пользователь</legend>
	  <label for="dbuser">Имя: </label><input id="dbuser" type="text" name="dbuser"/><br />
	  <label for="dbpass">Пароль: </label><input id="dbpass" type="password" name="dbpass"/>
	</fieldset>
	<fieldset>
	  <legend>База данных</legend>
	  <label for="dbname">Имя: </label><input id="dbname" type="text" name="dbname"/>
	</fieldset>
        <input id="only_config" type="checkbox" name="only_config" checked/><label for="only_config">Создать только файл настроек</label><br />
	<input type="submit" name="submit" value="OK"/>
  </fieldset>
  </form>
<?
}
?>
</body>
</html>
