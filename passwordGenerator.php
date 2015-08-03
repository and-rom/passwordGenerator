<?php
require_once('passwordGenerator.class.php');

if (!empty($_GET)){

  $format = (isset($_GET['format']) ? $_GET['format'] : "");
  $highlight = (isset($_GET['hl']) ? (bool)$_GET['hl'] : True);
  $passwordsCount = (isset($_GET['passwordsCount']) ?$_GET['passwordsCount']  : 5);

  if (isset($_GET['args']) and !empty(isset($_GET['args']))) {
    $args = str_split($_GET['args']);

    $wordsCount = (isset($args[0]) ? $args[0] : 3);
    $digitsCount = (isset($args[1]) ? $args[1] : 0);
    $charactersCount = (isset($args[2]) ? $args[2] : 3);
    $upperCaseLetter = (isset($args[3]) ? $args[3] : False);
    $transliterate = (isset($args[4]) ? (bool)$args[4] : False);
  } else {
    $wordsCount = (isset($_GET['wordsCount']) ? $_GET['wordsCount'] : 3);
    $digitsCount = (isset($_GET['digitsCount']) ? $_GET['digitsCount'] : 0);
    $charactersCount = (isset($_GET['charactersCount']) ? $_GET['charactersCount'] : 3);
    $upperCaseLetter = (isset($_GET['upperCaseLetter']) ? $_GET['upperCaseLetter'] : False);
    $transliterate = (isset($_GET['transliterate']) ? (bool)$_GET['transliterate'] : False);
  }

  $wordsCount = ($wordsCount>=3 && $wordsCount<=5 ? $wordsCount : 3 );
  $digitsCount = ($digitsCount>=0 && $digitsCount<=4 ? $digitsCount : 0 );
  $charactersCount = ($charactersCount>=3 && $charactersCount<=4 ? $charactersCount : 3 );

  $pwgen = new passwordGenerator($wordsCount,$digitsCount,$upperCaseLetter,$charactersCount,$passwordsCount,$transliterate);


  switch ($format) {
    case "html":
      $pwgen->generate();
      if ($highlight) $pwgen->highlight("<span style=\"color: red\">","</span>");
      $pwgen->escape();
      $pwgen->printPreHTML();
      break;
    case "pure":
      $pwgen->generate();
      if ($highlight) $pwgen->highlight("_","_");
      $pwgen->escape();
      $pwgen->printPure();
      break;
    case "json":
      $pwgen->generate();
      if ($highlight) $pwgen->highlight("*","_");
      $pwgen->escape();
      $pwgen->printJSON();
      break;
    case "sentences":
      $pwgen->generateSentences();
      $pwgen->printPure();
      break;
    default:
      $pwgen->generate();
      $pwgen->escape();
      $pwgen->printPreHTML();
    }
} else {
  $pwgen = new passwordGenerator(3,0,False,0,5,False);
  $pwgen->generate();
  $pwgen->escape();
  $pwgen->printPure();
}
exit();
?>
