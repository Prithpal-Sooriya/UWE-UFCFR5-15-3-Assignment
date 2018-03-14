<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

#test values
//$location = "../files/brislington_no2.xml";
//$time = "08:00:00";
//$date = "01/06/2016";

$location = $_REQUEST["location"];
$time = $_REQUEST["time"];
$date = $_REQUEST["date"];
//echo "Location = $location, Time = $time, Date = $date";
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
 */
function createJSONUserSelectionSorted($location, $time, $date) {

  list($day, $month, $year) = sscanf($date, "%d/%d/%d");
  $USDate = "$month/$day/$year";

  //now use the 'magical' functions, with string format of BEST WAY TO SET DATES
  $nextDate = date("d/m/Y", strtotime("+1 day", strtotime($USDate)));

  //get xml

  $xml = simplexml_load_file($location);
//  $xml->registerXPathNamespace("op", "http://www.w3.org/2001/XMLSchema");
//  $xml->registerXPathNamespace("xs:time", $time);
//  $timesCurrent = $xml->xpath("//reading[@date='$date' and xs:time(@time) => xs:time('08:00:00')]");
//  $timesCurrent = $xml->xpath("//reading[@date='$date' and substring-after(@time, ':') > substring-after('$time', ':')]");
  //this fuckin works!! replaces ':' with '' and treats it as a number!
  $resultArr = $xml->xpath("//reading["
          . "(@date='$date' and translate(@time, ':', '') >= translate('$time', ':', '')) or"
          . "(@date='$nextDate' and translate(@time, ':', '') <= translate('$time', ':', ''))]");

//  $resultArr = $xml->xpath("//reading["
//          . "(@date='$date' and dateTime('$date',@time) >= dateTime('$date', '$time')) or"
//          . "(@date='$nextDate' and dateTime('$nextDate', @time) <= dateTime('$nextDate', '$time'))]");
  
  //need to sort array
  usort($resultArr, 'sortSimpleXMLElementByDateTime');


  //json creation
  $rows = array();
  $table = array();
  $table["cols"] = array(
      array("label" => "date/time", "type" => "date"),
      array("label" => "NO2", "type" => "number"),
      array("type" => "string", "role" => "tooltip", "p" => array('html' => 'true'))
  );
  $dateFormat = "d/m/Y H:i:s";
  foreach ($resultArr as $single) {
    $reading = simplexml_load_string($single->asXML());
    $date = DateTime::createFromFormat($dateFormat, ($reading->attributes()->date . " " . $reading->attributes()->time));
    $val = ($reading->attributes()->val); //TOLD TO NOTE IN REPORT ROUGE VALUES.

    # create json string (for date)
    $temp = array();
    $googleChartsJSONDate = "Date(";
    $googleChartsJSONDate .= date("Y", $date->format("U")) . ", ";
    $googleChartsJSONDate .= (date("m", $date->format("U")) - 1) . ", ";
    $googleChartsJSONDate .= date("d", $date->format("U")) . ", ";
    $googleChartsJSONDate .= date("H", $date->format("U")) . ", ";
    $googleChartsJSONDate .= date("i", $date->format("U")) . ", ";
    $googleChartsJSONDate .= date("s", $date->format("U")) . ")";
    
    $tooltip = "<span style=\"font-size: 18pt; color: #ff0000; font-family: arial\">";
    $tooltip .= "Time = " . $date->format("H:i A")."<br>";
    $tooltip .= "<b>val = " . $val . "<b>";
    $tooltip .= "</span>";
    
    $temp[] = array("v" => $googleChartsJSONDate); //add val
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
  $dateFormat = "d/m/Y H:i:s";
  $date1 = DateTime::createFromFormat($dateFormat, ($reading1->attributes()->date . " " . $reading1->attributes()->time));
  $date2 = DateTime::createFromFormat($dateFormat, ($reading2->attributes()->date . " " . $reading2->attributes()->time));

  //return comparison
  /* `Note: As of PHP 5.2.2, DateTime objects can be compared using comparison operators.` */
  //fuck yeah ternary operators XD
  return $date1 == $date2 ? 0 :
          $date1 < $date2 ? -1 : 1;
}

?>
