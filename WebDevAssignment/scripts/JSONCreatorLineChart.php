<?php
/**
 * PHP file used for the LineChart.html ajax calls.
 * It will return json data from given parameters (that google charts can use)
 * 
 * @author Prithpal Sooriya
 */



#user requests
$location = $_REQUEST["location"];
$time = $_REQUEST["time"];
$date = $_REQUEST["date"];

#test values
//$location = "../files/brislington_no2.xml";
//$time = "08:00:00";
//$date = "01/06/2016";
echo createJSONUserSelectionSorted($location, $time, $date);

/**
 * Creates JSON string that outputs all no2 values from a selected 24 hour time range
 * and selected date (dd mm yyyy)
 * 
 * @param string $location location of sensor to get data from
 *        (must match the correct xml path!)
 *        e.g. "../files/brislington_no2.xml"
 * 
 * @param string $time start and end of 24 hour time interval to use
 *        e.g. 08:00:00
 * 
 * @param string $date selected date to examine values from.
 *        e.g. 13/07/2016
 * 
 * @return string json string
 * 
 * @NOTE - UK date time is very awkward to use,
 *         especially with the 'magical' date and strtotime functions!!
 *         (months and days flipped)
 * @NOTE - Using google charts developer site (https://developers.google.com/chart/interactive/docs/dev/implementing_data_source?hl=pt-BR#jsondatatable): for working out the google charts dates.
 * @NOTE - Used stack overflow (https://stackoverflow.com/questions/16016005/display-a-google-chart-with-json-php) and a lot of testing to create the correct json format!
 */
function createJSONUserSelectionSorted($location, $time, $date) {
  list($day, $month, $year) = sscanf($date, "%d/%d/%d");
  $USDate = "$month/$day/$year";

  //now use the 'magical' functions, with string format of UK/NORMAL date format
  $nextDate = date("d/m/Y", strtotime("+1 day", strtotime($USDate)));

  $xml = simplexml_load_file($location);

  //translate function = replace parts of a string
  //when a string only contains numbers, xpath will treat it as a number
  $resultArr = $xml->xpath("//reading["
          . "(@date='$date' and translate(@time, ':', '') >= translate('$time', ':', '')) or"
          . "(@date='$nextDate' and translate(@time, ':', '') <= translate('$time', ':', ''))]");

  //need to sort array
  usort($resultArr, 'sortSimpleXMLElementByDateTime');

  #json creation
  $rows = array();
  $table = array();
  $table["cols"] = array(
      array("label" => "date/time", "type" => "date"),
      array("label" => "NO2", "type" => "number"),
      array("type" => "string", "role" => "tooltip", "p" => array('html' => 'true'))
  );
  
  foreach ($resultArr as $single) {
    $reading = simplexml_load_string($single->asXML());
    $date = createDateTime($reading->attributes()->date, $reading->attributes()->time);
    $val = ($reading->attributes()->val);
    $temp = array(); //new array per loop to avoid array overflow.
    $googleChartsJSONDate = "Date(";
    $googleChartsJSONDate .= date("Y, ", $date->format("U"));
    $googleChartsJSONDate .= date("m", $date->format("U")) - 1 . ", ";
    $googleChartsJSONDate .= date("d, H, i, s", $date->format("U")) . ")";

    $tooltip = "<span style=\"font-size: 18pt; color: #000000; font-family: arial\">"
            . "Time = " . $date->format("H:i A") . "<br>"
            . "<b style=\"color: ".NO2Color($val)."\">NO2 = " . $val . "</b>"
            . "</span>";

    
    $temp[] = array("v" => $googleChartsJSONDate); //add date
    $temp[] = array("v" => (int) $val); //add val
    $temp[] = array("v" => $tooltip); //add tooltip
    $rows[] = array("c" => $temp); //add row to new column
  }

  $table["rows"] = $rows;
  $tableJSON = json_encode($table);
  return $tableJSON; //return the string
}

/**
 * Sorts 2 simplexmlelements by their dates!!
 * Very specific function... but oh well :P
 * 
 * @param SimpleXMLElement $a 1st object to compare against
 * @param SimpleXMLElement $b 2nd object to compare against
 */
function sortSimpleXMLElementByDateTime($a, $b) {
  $reading1 = simplexml_load_string($a->asXML());
  $reading2 = simplexml_load_string($b->asXML());

  //get DATE and TIME formed together as DATETIME
  $date1 = createDateTime($reading1->attributes()->date, $reading1->attributes()->time);
  $date2 = createDateTime($reading2->attributes()->date, $reading2->attributes()->time);

  //return comparison
  return $date1->format("U") - $date2->format("U");
}

/**
 * create a datetime object from a given string date and string time
 * function created because datetimes are used in multiple locations of the code
 * datetime returned will be in form of "d/m/Y H:i:s"
 * @param string $date date string
 * @param string $time time string
 * @return DateTime DateTime object
 */
function createDateTime($date, $time) {
  $dateFormat = "d/m/Y H:i:s";
  $dateTime = DateTime::createFromFormat($dateFormat, "$date $time");
  return $dateTime;
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

?>
