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

  private $passwords=array();

  function __construct($wordsCount,$digitsCount,$upperCaseLetter,$charactersCount,$passwordsCount) {
    $this->wordsCount = $wordsCount;
    $this->digitsCount = $digitsCount;
    $this->upperCaseLetter = $upperCaseLetter;
    $this->charactersCount = $charactersCount;
    $this->passwordsCount = $passwordsCount;
  }

  function __destruct() {
  }

  function printJSON() {
    /*echo $this->wordsCount . "\n";
    echo $this->digitsCount . "\n";
    echo $this->upperCaseLetter . "\n";
    echo $this->charactersCount . "\n";
    echo $this->passwordsCount . "\n";*/
    print json_encode($this->passwords,JSON_UNESCAPED_UNICODE);
  }

  function printPure() {
    /*echo $this->wordsCount . "\n";
    echo $this->digitsCount . "\n";
    echo $this->upperCaseLetter . "\n";
    echo $this->charactersCount . "\n";
    echo $this->passwordsCount . "\n";*/
    $i=1;
    foreach ($this->passwords as $pair){
      print ($i<10 ? "0" . $i: $i) . " " . $pair['password'] . " " . $pair['sentence'] . "\n";
      $i++;
    }
  }


  function generate() {
    for ($i = 0; $i <= $this->passwordsCount-1; $i++) {
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
      $pair['password'] = $password;
      $pair['sentence'] = $sentence;
      $this->passwords[] = $pair;
    }
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
}
?>
