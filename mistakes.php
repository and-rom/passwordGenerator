<?php
$file = dirname(__FILE__).'/mistakes.txt';
$current = file_get_contents($file);
$current .= $_POST['mistake'] . "\n";
file_put_contents($file, $current);
header("Location: ./");
?>
