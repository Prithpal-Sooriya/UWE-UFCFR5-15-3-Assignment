<?php
/**
 * PHP file used for the ScatterChart.html ajax calls.
 * It will return json data from given parameters (that google charts can use)
 * 
 * @author Prithpal Sooriya
 */
header("Access-Control-Allow-Origin: *");
#user requests
$location = $_REQUEST["location"];
$time = $_REQUEST["time"];
$date = $_REQUEST["date"];

#test values
//$location = "../files/fishponds_no2.xml";
//$time = "10:00:00";
//$date = "2015";
echo createScatterJSON($location, $time, $date);

/**
 * This will take user input of time and date and return equivalent JSON
 * This function will sort the data before placing into the array.
 * 
 * @param string $inputFilePath relative path of xml file to use
 *
 * @param string $selectedTime users selected time, format = HH:MM:SS (e.g. 08:00:00)
 *
 * @param strigng $selectedDate user selected date, format = YYYY (e.g. 2016)
 * 
 * @return string json string of the values from corresponding xml file
 */
function createScatterJSON($inputFilePath, $selectedTime, $selectedDate) {
  $xml = simplexml_load_file($inputFilePath);
  $resultArr = $xml->xpath("//reading[@time='$selectedTime' and contains(@date,'$selectedDate')]");

  $rows = array();
  $table = array();
  $table["cols"] = array(
   array("label" => "date/time", "type" => "date"),
   array("label" => "NO2", "type" => "number"),
   array("role" => "style", "type" => "string")
  );

  $dateFormat = "d/m/Y H:i:s";
  foreach ($resultArr as $single) {
    $reading = simplexml_load_string($single->asXML());
    $date = DateTime::createFromFormat($dateFormat, ($reading->attributes()->date . " " . $reading->attributes()->time));
    $val = $reading->attributes()->val; //no absolute, we will add talk about bad values in the report.
    # create json string (for date)
    $temp = array();
    $googleChartsJSONDate = "Date(";
    $googleChartsJSONDate .= date("Y, ", $date->format("U"));
    $googleChartsJSONDate .= date("m", $date->format("U")) - 1 . ", "; //google charts wants months 0-11, cuz of goddamn java and javascript!
    $googleChartsJSONDate .= date("d, H, i, s", $date->format("U")) . ")";

    $temp[] = array("v" => $googleChartsJSONDate); //add date
    $temp[] = array("v" => (int) $val); //add val
    $temp[] = array("v" => NO2Color($val)); //add colour
    $rows[] = array("c" => $temp); //add row to new column
  }

  $table["rows"] = $rows;
  $tableJSON = json_encode($table);
  return $tableJSON; //return the string
}

/**
 * function used to for retrieving the NO2 colour.
 * @param integer $val value of NO2
 * @return string corresponding hash colour
 */
function NO2Color($val) {

  /**
   * a short function used to tell if an integer is in range
   * @param int $val value to check against
   * @param int $min minimum value boundary
   * @param int $max maximum value boundary
   * @return boolean true if the value was in range, false if not
   */
  $inRange = function ($val, $min, $max) { return $min <= $val && $val <= $max;};

  //wish there was a shorthand if statement...
  if($inRange($val, 0, 67)):    return "#9FFF8E"; endif;
  if($inRange($val, 68, 134)):  return "#55FF00"; endif;
  if($inRange($val, 135, 200)): return "#48C900"; endif;
  if($inRange($val, 201, 267)): return "#FEFE00"; endif;
  if($inRange($val, 268, 334)): return "#FAC900"; endif;
  if($inRange($val, 335, 400)): return "#F45958"; endif;
  if($inRange($val, 401, 467)): return "#F30000"; endif;
  if($inRange($val, 468, 534)): return "#890000"; endif;
  if($inRange($val, 535, 600)): return "#C12EFF"; endif;
  if($val >= 601):             return "#55FF00"; endif;
}
