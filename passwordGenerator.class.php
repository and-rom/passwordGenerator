<?php
mb_internal_encoding("UTF-8");

mysql_connect("localhost", "root", "hjvfyjd") or die("Ошибка соединения: " . mysql_error());
mysql_set_charset("utf8");
mysql_select_db("pwgen");

class passwordGenerator {

  public $wordsCount;
  public $digitsCount;
  public $upperCaseLetter;
  public $charactersCount;
  public $passwordsCount;

  private $passwords = array();
  private $transliterated = False;

  function __construct($wordsCount,$digitsCount,$upperCaseLetter,$charactersCount,$passwordsCount,$transliterate = False) {
    $this->wordsCount = $wordsCount;
    $this->digitsCount = $digitsCount;
    $this->upperCaseLetter = $upperCaseLetter;
    $this->charactersCount = $charactersCount;
    $this->passwordsCount = $passwordsCount;
    $this->transliterated = $transliterate;
  }

  function __destruct() {
  }

  function printJSON() {
    print json_encode($this->passwords,JSON_UNESCAPED_UNICODE);
  }

  function printPure() {
    foreach ($this->passwords as $pair){
      print $pair['password'] . " " . $pair['sentence'] . "\n";
    }
  }
  function printPreHTML() {
    $i=1;
    print  "<pre style=\"white-space: pre-wrap;\">\n";
    foreach ($this->passwords as $pair){
      print "<strong>" . ($i<10 ? "0" . $i: $i) . "</strong> " . $pair['password'] . " " . $pair['sentence'] . "\n";
      $i++;
    }
    print "</pre>\n";
  }

  function printVarDump() {
    print  "<pre style=\"white-space: pre-wrap;\">\n";
    var_dump($this->passwords);
    print "</pre>\n";
    print  "<pre style=\"white-space: pre-wrap;\">\n";
    print_r ($this->passwords);
    print "</pre>\n";
  }

  function generateSentences() {
    for ($i = 0; $i < $this->passwordsCount; $i++) {
      $number = $this->generateNumber();
      $ps_type = $this->getPluralType($number);

      $subject = $this->getSubject($ps_type);
      $words[1] = $subject['word'];

      $predicate = $this->getPredicate($subject['ps'],($subject['ps'] == "p" ? "'-'" : $subject['g']));
      $words[2] = $predicate['word'];

      $object = $this->getObject();
      $words[4] = $object['word'];

      if ($this->wordsCount == 4 or $this->wordsCount == 5) {
        $attribute1 = $this->getAttribute1($ps_type,($ps_type == 2 ? "'-'" : $subject['g']));
        $words[0] = $attribute1['word'];
      }
      if ($this->wordsCount == 5) {
        $attribute2 = $this->getAttribute2($object['ps'],$object['g'],$object['alt_case']);
        $words[3] = $attribute2['word'];
      }

      ksort($words);
/*
      if ($number > 1) {
        $sentence = $number." ";
        $password = $number;
      } else {
        $sentence = "";
        $password = "";
      }

      foreach ($words as $word) {
        if ($this->upperCaseLetter) {$word = mb_convert_case($word,MB_CASE_TITLE);}
         $sentence .= $word." ";
         $password .= $this->invertLayout(mb_substr($word,0,$this->charactersCount));
      }
*/
      $sentence = array();
//      $password = array();
      if ($number > 1) {
        $sentence[] = $number;
//        $password[] = $number;
      }

      foreach ($words as $word) {
        if ($this->upperCaseLetter) {$word = mb_convert_case($word,MB_CASE_TITLE);}
         $sentence[] = $word;
//         $password[] = $this->invertLayout(mb_substr($word,0,$this->charactersCount));
      }

/*
      $pair['password'] = implode($password);
      $pair['sentence'] = implode(" ", $sentence);
      $this->passwords[] = $pair;
*/
      $this->passwords[]['sentence'] = implode(" ", $sentence);;

    }
  }

  function generatePasswords() {
    for ($i = 0; $i < count($this->passwords); $i++) {
      $words = explode (" ",$this->passwords[$i]['sentence']);
      $password = array();
      foreach ($words as $word) {
        if (preg_match('/^[0-9]+$/', $word)) {
          $password[] = $word;
        } else {
          $part_to_password = mb_substr($word,0,$this->charactersCount);
          $password[] = ($this->transliterated ? $part_to_password : $this->invertLayout($part_to_password));
        }
      }
      $this->passwords[$i]['password'] = implode($password);
    }
  }

  function generate() {
    $this->generateSentences();
    if ($this->transliterated) $this->transliterate();
    $this->generatePasswords();
  }

  private function generateNumber() {
    switch ($this->digitsCount) {
      case 0: $number = 1; break;
      case 1: $number = rand(2, 9); break;
      case 2:
      case 3:
      case 4: $number = rand(pow(10, $this->digitsCount-1), pow(10, $this->digitsCount)-1); break;
      default: $number = 1;
    }
    return $number;
  }

  public function transliterate() {
    for ($i = 0; $i < count($this->passwords); $i++) {
      $this->passwords[$i]['sentence'] = $this->get_in_translate_to_en($this->passwords[$i]['sentence'],True);
    }
  }

  private function getSubject($ps_type) {
    $query = "SELECT `word`,`g`,`ps` FROM `subject` WHERE `ps_type`=$ps_type ORDER BY RAND() LIMIT 0,1";
    return $this->dbQuery($query);
  }

  private function getPredicate($ps,$g) {
    $query = "SELECT `word` FROM `predicate` WHERE `ps`='$ps' AND `g`='$g' ORDER BY RAND() LIMIT 0,1";
    return $this->dbQuery($query);
  }

  private function getObject() {
    $query = "SELECT `word`,`g`,`ps`,`alt_case` FROM `object` ORDER BY RAND() LIMIT 0,1";
    return $this->dbQuery($query);
  }

  private function getAttribute1($ps_type,$g) {
    $query = "SELECT `word` FROM `attrib` WHERE `ps_type`=$ps_type AND `g`='$g' AND `case`='nom' ORDER BY RAND() LIMIT 0,1";
    return $this->dbQuery($query);
  }

  private function getAttribute2($ps,$g,$ac) {
    $query = "SELECT `word` FROM `attrib` WHERE `ps`='$ps' AND `g`='$g' AND `case`='acc' AND `alt_case`='$ac' ORDER BY RAND() LIMIT 0,1";
    return $this->dbQuery($query);
  }

  private function dbQuery($query) {
    $result = mysql_query($query) or die("Query failed");
    $row = mysql_fetch_array($result,MYSQL_ASSOC);
    return $row;
  }

  private function getPluralType($number) {
    $number = $number % 100;
    if ($number>=11 && $number<=19) {
        $ending=2;
    } else {
      $i = $number % 10;
      switch ($i) {
        case (1): $ending = 0; break;
        case (2):
        case (3):
        case (4): $ending = 1; break;
        default: $ending=2;
      }
    }
    return $ending;
  }

  private function invertLayout ($string) {
    $search = array(
      "Й","Ц","У","К","Е","Н","Г","Ш","Щ","З","Х","Ъ",
      "Ф","Ы","В","А","П","Р","О","Л","Д","Ж","Э",
      "Я","Ч","С","М","И","Т","Ь","Б","Ю",
      "й","ц","у","к","е","н","г","ш","щ","з","х","ъ",
      "ф","ы","в","а","п","р","о","л","д","ж","э",
      "я","ч","с","м","и","т","ь","б","ю"
      );
    $replace = array(
      "Q","W","E","R","T","Y","U","I","O","P","{","}",
      "A","S","D","F","G","H","J","K","L",":","\"",
      "Z","X","C","V","B","N","M","<",">",
      "q","w","e","r","t","y","u","i","o","p","[","]",
      "a","s","d","f","g","h","j","k","l",";","'",
      "z","x","c","v","b","n","m",",","."
      );
    return str_replace($search, $replace, $string);
  }
  function escape () {
    for($i = 0; $i < count($this->passwords);$i++) {
     $this->passwords[$i]['password'] = htmlspecialchars($this->passwords[$i]['password']);
    }
  }
  function highlight ($highlighter_s = "_",$highlighter_f = "_") {
    for ($i = 0; $i < count($this->passwords); $i++) {
     $sentence = $this->passwords[$i]['sentence'];
     $words = explode(" ", $sentence);
     for ($j = 0; $j < count($words); $j++) {
       if (preg_match('/^[0-9]+$/', $words[$j])) {
         $words[$j] = $highlighter_s . $words[$j] . $highlighter_f;
       } else {
         $words[$j] = mb_substr($words[$j], 0, $this->charactersCount).$highlighter_f.mb_substr($words[$j], $this->charactersCount);
         $words[$j] = $highlighter_s . $words[$j];
       }
     }
     $this->passwords[$i]['sentence'] = implode(" ", $words);
    }
  }

  private function get_in_translate_to_en($string, $gost=false){
    if($gost) {
      $replace = array("А"=>"A","а"=>"a","Б"=>"B","б"=>"b","В"=>"V","в"=>"v","Г"=>"G","г"=>"g","Д"=>"D","д"=>"d",
                       "Е"=>"E","е"=>"e","Ё"=>"E","ё"=>"e","Ж"=>"Zh","ж"=>"zh","З"=>"Z","з"=>"z","И"=>"I","и"=>"i",
                       "Й"=>"I","й"=>"i","К"=>"K","к"=>"k","Л"=>"L","л"=>"l","М"=>"M","м"=>"m","Н"=>"N","н"=>"n","О"=>"O","о"=>"o",
                       "П"=>"P","п"=>"p","Р"=>"R","р"=>"r","С"=>"S","с"=>"s","Т"=>"T","т"=>"t","У"=>"U","у"=>"u","Ф"=>"F","ф"=>"f",
                       "Х"=>"Kh","х"=>"kh","Ц"=>"Tc","ц"=>"tc","Ч"=>"Ch","ч"=>"ch","Ш"=>"Sh","ш"=>"sh","Щ"=>"Shch","щ"=>"shch",
                       "Ы"=>"Y","ы"=>"y","Э"=>"E","э"=>"e","Ю"=>"Iu","ю"=>"iu","Я"=>"Ia","я"=>"ia","ъ"=>"","ь"=>"");
    } else {
      $arStrES = array("ае","уе","ое","ые","ие","эе","яе","юе","ёе","ее","ье","ъе","ый","ий");
      $arStrOS = array("аё","уё","оё","ыё","иё","эё","яё","юё","ёё","её","ьё","ъё","ый","ий");        
      $arStrRS = array("а$","у$","о$","ы$","и$","э$","я$","ю$","ё$","е$","ь$","ъ$","@","@");
                    
      $replace = array("А"=>"A","а"=>"a","Б"=>"B","б"=>"b","В"=>"V","в"=>"v","Г"=>"G","г"=>"g","Д"=>"D","д"=>"d",
                      "Е"=>"Ye","е"=>"e","Ё"=>"Ye","ё"=>"e","Ж"=>"Zh","ж"=>"zh","З"=>"Z","з"=>"z","И"=>"I","и"=>"i",
                      "Й"=>"Y","й"=>"y","К"=>"K","к"=>"k","Л"=>"L","л"=>"l","М"=>"M","м"=>"m","Н"=>"N","н"=>"n",
                      "О"=>"O","о"=>"o","П"=>"P","п"=>"p","Р"=>"R","р"=>"r","С"=>"S","с"=>"s","Т"=>"T","т"=>"t",
                      "У"=>"U","у"=>"u","Ф"=>"F","ф"=>"f","Х"=>"Kh","х"=>"kh","Ц"=>"Ts","ц"=>"ts","Ч"=>"Ch","ч"=>"ch",
                      "Ш"=>"Sh","ш"=>"sh","Щ"=>"Shch","щ"=>"shch","Ъ"=>"","ъ"=>"","Ы"=>"Y","ы"=>"y","Ь"=>"","ь"=>"",
                      "Э"=>"E","э"=>"e","Ю"=>"Yu","ю"=>"yu","Я"=>"Ya","я"=>"ya","@"=>"y","$"=>"ye");
                
      $string = str_replace($arStrES, $arStrRS, $string);
      $string = str_replace($arStrOS, $arStrRS, $string);
    }
        
    return iconv("UTF-8","UTF-8//IGNORE",strtr($string,$replace));
  }
}
?>
