<?php
require_once('passwordGenerator.class.php');
$pwgen = new passwordGenerator($_GET['wordsCount'],$_GET['digitsCount'],(bool)$_GET['upperCaseLetter'],$_GET['charactersCount'],$_GET['passwordsCount']);
$pwgen->generate();
$variant = 1;
    switch ($variant) {
      case 1:
        $pwgen->highlight("<span style=\"color: red\">","</span>");
        $pwgen->escape();
        $pwgen->printPreHTML();
        break;
      case 2:
        $pwgen->highlight("_","_");
        $pwgen->escape();
        $pwgen->printPure();
        break;
      case 3:
        $pwgen->highlight("*","*");
        $pwgen->printJSON();
        break;
      default:
        $pwgen->printPure();
    }
exit();
?>
