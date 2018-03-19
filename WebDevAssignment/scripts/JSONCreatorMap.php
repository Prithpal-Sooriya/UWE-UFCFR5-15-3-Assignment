<?php

/**
 * @author Prithpal Sooriya
 * This script will return the json for a pie chart to the user.
 */
/**
 * Global variables to use
 * @var array array of all the files to be used
 * @NOTE: need to try and potentially find a better way to gather all the files
 * 
 * @var string relative string path to find the no2.xml files
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
//output to user
echo createJSONString($time, $date);

/* -------------------------------- */
/* functions                        */
/* -------------------------------- */

/**
 * Function that will create the json string using all the no2 files
 * @param string $time time in format HH:MM:SS (e.g. 08:00:00)
 * @param string $date date used in format DD/MM/YYYY (e.g. 27/04/2016)
 * @return string json string to be used by google charts
 */
function createJSONString($time, $date) {
  #json creation
  $rows = array();
  $table = array();
  $table["cols"] = array(
      array("label" => "Lat", "type" => "number"),
      array("label" => "Long", "type" => "number"),
      array("label" => "Location", "type" => "string"),
//      array("label" => "NO2", "type" => "number")
  );

  foreach ($GLOBALS["no2Files"] as $file) {
    $temp = createJSONArraySingleFile($file, $time, $date);
    if ($temp != NULL) {
      $rows = array_merge($rows, $temp);
    }
  }

  $table["rows"] = $rows;
  $tableJSON = json_encode($table);
  return $tableJSON;
}

/**
 * function used to create array for adding to json
 * @param string $file filename
 * @param string $time time from user input (format: HH:MM:SS)
 * @param string $date date from user input (format: DD/MM/YYYY)
 * @return array array containing row for json
 */
function createJSONArraySingleFile($file, $time, $date) {
  $path = $GLOBALS["path"];
  if (!file_exists($path . $file)) {
//    echo "file does not exist: $path$file";
    return NULL;
  }

  $xml = simplexml_load_file($path . $file);

  #get location values (lat, long, id/name)
  $locationArr = $xml->xpath("//location");
  $locationArr = simplexml_load_string($locationArr[0]->asXML());
  $locationID = $locationArr->attributes()->id;
  $locationLat = $locationArr->attributes()->lat;
  $locationLong = $locationArr->attributes()->long;

  #get record value (just no2 val)
  $resultArr = $xml->xpath("//reading[@date='$date' and @time='$time']");
  if (count($resultArr) == 0) {
//    echo "result array for $locationID does not have any values in $date $time <br/>";
    return NULL;
  }
  //if there were >1 val, then just get last (NOTE: maybe add mean val)
  $reading = $resultArr[count($resultArr) - 1];
  $val = simplexml_load_string($reading->asXML())->attributes()->val;

  #add to array
  $rows = array();
  $temp = array();
  $temp[] = array("v" => (float) $locationLat); //add location lat
  $temp[] = array("v" => (float) $locationLong); //add location long
//  $temp[] = array("v" => (string) $locationID);
  $colour = NO2Color($val);
  $tooltip = ""
          . "<div style=\""
          . "color:$colour;"
          . "font-size: 20pt;"
          . "text-shadow:"
          . "-1px -1px 0 #000,"
          . "1px -1px 0 #000,"
          . "-1px 1px 0 #000,"
          . "1px 1px 0 #000;"
          . "\""
          . ">$locationID<br/>NO2: $val</div>";
  $temp[] = array("v" => (string) $tooltip); //add location name
//  $temp[] = array("v" => (string) $val); //add no2
  $rows[] = array("c" => $temp);

  #return array
  return $rows;
}

function NO2Color($val) {

  /**
   * a short function used to tell if an integer is in range
   * @param int $val value to check against
   * @param int $min minimum value boundary
   * @param int $max maximum value boundary
   * @return boolean true if the value was in range, false if not
   */
  $inRange = function ($val, $min, $max) {
    return $min <= $val && $val <= $max;
  };

  //wish there was a shorthand if statement...
  if ($inRange($val, 0, 67)): return "#9FFF8E";
  endif;
  if ($inRange($val, 68, 134)): return "#55FF00";
  endif;
  if ($inRange($val, 135, 200)): return "#48C900";
  endif;
  if ($inRange($val, 201, 267)): return "#FEFE00";
  endif;
  if ($inRange($val, 268, 334)): return "#FAC900";
  endif;
  if ($inRange($val, 335, 400)): return "#F45958";
  endif;
  if ($inRange($val, 401, 467)): return "#F30000";
  endif;
  if ($inRange($val, 468, 534)): return "#890000";
  endif;
  if ($inRange($val, 535, 600)): return "#C12EFF";
  endif;
  if ($val >= 601): return "#55FF00";
  endif;
}
?>

