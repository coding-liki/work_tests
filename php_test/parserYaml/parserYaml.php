<?
require_once('./parserYaml/sfYamlParser.php');
class YamlToArray extends sfYamlParser{
  public function __construct(){ 
  
  }  
  
  public function fileParseToArray($yaml){     
   return parent::parse($yaml);
  }  
 
  public static function printYamlAsArray($yaml){ 
   $data = parent::parse($yaml);
   echo "<pre>";
   echo htmlspecialchars(iconv('utf-8', 'windows-1251', print_r($data, true)));
   echo "</pre>";
  }   
}
