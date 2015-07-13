<?php
require_once('passwordGenerator.class.php');
$pwgen = new passwordGenerator($_GET['wordsCount'],$_GET['digitsCount'],(bool)$_GET['upperCaseLetter'],$_GET['charactersCount'],$_GET['passwordsCount']);
$pwgen->generate();
$pwgen->escape();
echo "<pre style=\"white-space: pre-wrap;\">";
$pwgen->printPure();
echo "</pre>";
exit();
?>
