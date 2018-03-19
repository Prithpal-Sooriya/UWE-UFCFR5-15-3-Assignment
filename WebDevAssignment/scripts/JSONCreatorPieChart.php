<?php

/**
 * @author Prithpal Sooriya
 * This script will return the json for a pie chart to the user.
 */
/**
 * Global variables used
 * @var array all the files to be used
 * @NOTE: try to potentially find a better way to gather all files
 * 
 * @var string relative string path to find files
 */
$no2Files = array("brislington_no2.xml", "fishponds_no2.xml", "parson_st_no2.xml",
    "rupert_st_no2.xml", "wells_rd_no2.xml", "newfoundland_way_no2.xml");
$path = "../files/";


//user requests
$time = $_REQUEST["time"]; //e.g. 08:00:00
$date = $_REQUEST["date"]; //e.g. 27/04/2016 in format DD/MM/YYYY
//test data
//$time = "08:00:00";
//$date = "27/04/2016";
echo createJSONString($time, $date);

/* -------------------------------- */
/* functions                        */
/* -------------------------------- */

/**
 * Function that will create the json string using all the no2 files
 * @param string $time time in format HH:MM:SS (e.g. 08:00:00)
 * @param string $date date used in format DD/MM/YYYY (e.g. 27/04/2016)
 * @return string json string to be used by google charts
 * 
 */
function createJSONString($time, $date) {
  #json creation
  $rows = array();
  $table = array();
  $table["cols"] = array(
      array("label" => "location", "type" => "string"),
      array("label" => "NO2", "type" => "number")
  );

  foreach ($GLOBALS["no2Files"] as $file) {
    $temp = createJSONStringSingleFile($file, $time, $date);
    if ($temp != NULL) {
      $rows = array_merge($rows, $temp);
//      $rows[] = $temp;
    }
  }

  $table["rows"] = $rows;
  $tableJSON = json_encode($table);
  return $tableJSON; //return the string
}

/**
 * function used to get the json string from a single file
 * @param string $file file name to look through
 * @param string $time time in format HH:MM:SS (e.g. 08:00:00)
 * @param string $date date used in format DD/MM/YYYY (e.g. 27/04/2016)
 * @return array json array to be then used to create json string
 */
function createJSONStringSingleFile($file, $time, $date) {
  $path = $GLOBALS["path"];

  if (!file_exists($path . $file)) {
//    echo "file does not exist: $path$file";
    return NULL;
  }

  $xml = simplexml_load_file($path . $file);

  #get location values (just location id)
  $locationArr = $xml->xpath("//location");
  $locationID = simplexml_load_string($locationArr[0]->asXML())
                  ->attributes()->id;

//  print_r($locationID["0"]);
//  die();
  #get result values (just val)
  $resultArr = $xml->xpath("//reading[@date='$date' and @time='$time']");
//  echo(count($resultArr));
//  die();
  if (count($resultArr) == 0) {
//    echo "result array does not contain any values<br>";
    return NULL;
  }
  //result arr should only be 1 val, but in worst case there is more than 1 val just return end of index
  $single = $resultArr[count($resultArr) - 1];
  //extract data from single
  $reading = simplexml_load_string($single->asXML());
  $val = $reading->attributes()->val;

  #add to array
  $rows = array();
  $temp = array();
  $temp[] = array("v" => (string) $locationID); //add location name/id
  $temp[] = array("v" => (int) $val); //add val
  $rows[] = array("c" => $temp);

  #return array
  return $rows;
}

?>
