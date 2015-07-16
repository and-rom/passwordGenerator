<?php
require_once('passwordGenerator.class.php');
if (!empty($_GET)){
$pwgen = new passwordGenerator($_GET['wordsCount'],$_GET['digitsCount'],(bool)$_GET['upperCaseLetter'],$_GET['charactersCount'],$_GET['passwordsCount'],(isset($_GET['transliterate']) ? (bool)$_GET['transliterate'] : False));
$pwgen->generate();
$variant = 4;
    switch ($variant) {
      case 1:
        $pwgen->highlight("<span style=\"color: red\">","</span>");
        $pwgen->escape();
        $pwgen->printPreHTML();
        break;
      case 2:
        $pwgen->highlight("_","_");
        $pwgen->escape();
        $pwgen->printPreHTML();
        break;
      case 3:
        $pwgen->highlight("_","_");
        $pwgen->escape();
        $pwgen->printPure();
        break;
      case 4:
        $pwgen->highlight("*","_");
        $pwgen->escape();
        $pwgen->printJSON();
        break;
      default:
        $pwgen->printPure();
    }
} else {
  $pwgen = new passwordGenerator(5,2,True,2,3,True);
  $pwgen->generate();
  $pwgen->escape();
  $pwgen->printVarDump();
}
exit();
?>
