<?php
require_once('passwordGenerator.class.php');

if (!empty($_GET)){

  $format         = (isset($_GET['format']) ? $_GET['format'] : "");
  $highlight      = (isset($_GET['hl']) ? ((bool)$_GET['hl'] ? $_GET['hl'] : False) : False);
  $passwordsCount = (isset($_GET['pc']) ? $_GET['pc'] : 5);

  if (isset($_GET['args']) and !empty(isset($_GET['args']))) {
    $args = str_split($_GET['args']);

    $wordsCount = (isset($args[0]) ? $args[0] : 3);
    $digitsCount = (isset($args[1]) ? $args[1] : 0);
    $charactersCount = (isset($args[2]) ? $args[2] : 3);
    $upperCaseLetter = (isset($args[3]) ? (bool)$args[3] : False);
    $transliterate = (isset($args[4]) ? (bool)$args[4] : False);
  } else {
    $wordsCount = (isset($_GET['wc']) ? $_GET['wc'] : 3);
    $digitsCount = (isset($_GET['dc']) ? $_GET['dc'] : 0);
    $charactersCount = (isset($_GET['cc']) ? $_GET['cc'] : 3);
    $upperCaseLetter = (isset($_GET['ul']) ? (bool)$_GET['ul'] : False);
    $transliterate = (isset($_GET['tl']) ? (bool)$_GET['tl'] : False);
  }

  $passwordsCount = ($passwordsCount>=1 && $passwordsCount<=50 ? $passwordsCount : 5 );

  $wordsCount = ($wordsCount>=3 && $wordsCount<=5 ? $wordsCount : 3 );
  $digitsCount = ($digitsCount>=0 && $digitsCount<=4 ? $digitsCount : 0 );
  $charactersCount = ($charactersCount>=2 && $charactersCount<=4 ? $charactersCount : 3 );

  $pwgen = new passwordGenerator($passwordsCount,$wordsCount,$digitsCount,$charactersCount,$upperCaseLetter,$transliterate);

  switch ($format) {
    case "html":
      header("Content-Type: text/html; charset=utf-8");
      $pwgen->generate();
      if ($highlight) {
        $before = ($highlight == 1 ? "<span style=\"color: red\">" : (isset(str_split($highlight)[1]) ? str_split($highlight)[0] : ""));
        $after = ($highlight == 1 ? "</span>" : (isset(str_split($highlight)[1]) ? str_split($highlight)[1] : str_split($highlight)[0]));
        $pwgen->highlight($before,$after);
      }
      $pwgen->escape();
      $pwgen->printPreHTML();
      break;
    case "pure":
      header("Content-Type: text/plain; charset=utf-8");
      $pwgen->generate();
      if ($highlight) {
        $before = ($highlight == 1 ? "_" : (isset(str_split($highlight)[1]) ? str_split($highlight)[0] : ""));
        $after = ($highlight == 1 ? "_" : (isset(str_split($highlight)[1]) ? str_split($highlight)[1] : str_split($highlight)[0]));
        $pwgen->highlight($before,$after);
      }
      $pwgen->escape();
      $pwgen->printPure();
      break;
    case "json":
      header("Content-Type: application/json; charset=utf-8");
      $pwgen->generate();
      if ($highlight) {
        $before = ($highlight == 1 ? "*" : (isset(str_split($highlight)[1]) ? str_split($highlight)[0] : ""));
        $after = ($highlight == 1 ? "_" : (isset(str_split($highlight)[1]) ? str_split($highlight)[1] : str_split($highlight)[0]));
        $pwgen->highlight($before,$after);
      }
      $pwgen->escape();
      $pwgen->printJSON();
      break;
    case "sentences":
      header("Content-Type: text/plain; charset=utf-8");
      $pwgen->generateSentences();
      $pwgen->printPure();
      break;
    default:
      header("Content-Type: text/html; charset=utf-8");
      $pwgen->generate();
      if ($highlight) {
        $before = ($highlight == 1 ? "_" : (isset(str_split($highlight)[1]) ? str_split($highlight)[0] : ""));
        $after = ($highlight == 1 ? "_" : (isset(str_split($highlight)[1]) ? str_split($highlight)[1] : str_split($highlight)[0]));
        $pwgen->highlight($before,$after);
      }
      $pwgen->escape();
      $pwgen->printPreHTML();
  }
} else {
  header("Content-Type: text/plain; charset=utf-8");
  $pwgen = new passwordGenerator(5,3,0,3,False,False);
  $pwgen->generate();
  $pwgen->escape();
  $pwgen->printPure();
}
exit();
?>
