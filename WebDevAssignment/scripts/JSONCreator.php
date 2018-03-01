<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//just a basic test to see if this works
$location = $_REQUEST["location"];
$time = $_REQUEST["time"];
$date = $_REQUEST["date"];
//echo "Hello world!!";
echo createJSONUserSelectionSorted($location, $time, $date);



/**
 * Creates a JSON from a given file.
 * This does not consider user input.
 * 
 * @param string $inputFilePath relative path of xml file to use.
 * @return string JSON created in form of string returned.
 * 
 * @todo add check to see if file is xml
 * @todo return output of nothing, if no data found (so Higher level calls can handle it)
 * @todo test all use cases (and handle potential errors)
 */
function createJSON($inputFilePath) {
  //default will use time = 08:00:00, and date = 2016
  createJSONUserSelection($inputFilePath, "08:00:00", "2016");
}

/**
 * This will take user input of time and date and return equivalent JSON
 * 
 * @param string $inputFilePath relative path of xml file to use
 *
 * @param string $selectedTime users selected time, format = HH:MM:SS (e.g. 08:00:00)
 * @todo support time range?
 *
 * @param strigng $selectedDate user selected date, format = YYYY (e.g. 2016)
 * @todo support date range?
 * 
 * @todo add check to see if xml
 * @todo return nothing if no data found (higher level calls will handle it)
 * @todo error handling and testing
 */
function createJSONUserSelection($inputFilePath, $selectedTime, $selectedDate) {
  $xml = simplexml_load_file($inputFilePath);


  $resultArr = $xml->xpath("//reading[@time='$selectedTime' and contains(@date,'$selectedDate')]");

  $rows = array();
  $table = array();
  $table["cols"] = array(
      array("label" => "date/time", "type" => "date"),
      array("label" => "NO2", "type" => "number")
  );

  $dateFormat = "d/m/Y H:i:s";
  foreach ($resultArr as $single) {
    $reading = simplexml_load_string($single->asXML());
    $date = DateTime::createFromFormat($dateFormat, ($reading->attributes()->date . " " . $reading->attributes()->time));
    $val = $reading->attributes()->val;

    # create json string (for date)
    $temp = array();
    $googleChartsJSONDate = "Date(";
    $googleChartsJSONDate .= date("Y", $date->format("U")) . ", ";
    $googleChartsJSONDate .= (date("m", $date->format("U")) - 1) . ", ";
    $googleChartsJSONDate .= date("d", $date->format("U")) . ", ";
    $googleChartsJSONDate .= date("H", $date->format("U")) . ", ";
    $googleChartsJSONDate .= date("i", $date->format("U")) . ", ";
    $googleChartsJSONDate .= date("s", $date->format("U")) . ")";

    $temp[] = array("v" => $googleChartsJSONDate); //add val
    $temp[] = array("v" => (int) $val); //add val
    $rows[] = array("c" => $temp); //add row to new column
  }

  $table["rows"] = $rows;
  $tableJSON = json_encode($table);
  return $tableJSON; //return the string
}

/**
 * This will take user input of time and date and return equivalent JSON
 * This function will sort the data before placing into the array.
 * 
 * @param string $inputFilePath relative path of xml file to use
 *
 * @param string $selectedTime users selected time, format = HH:MM:SS (e.g. 08:00:00)
 * @todo support time range?
 *
 * @param strigng $selectedDate user selected date, format = YYYY (e.g. 2016)
 * @todo support date range?
 * 
 * @todo add check to see if xml
 * @todo return nothing if no data found (higher level calls will handle it)
 * @todo error handling and testing
 */
function createJSONUserSelectionSorted($inputFilePath, $selectedTime, $selectedDate) {

  $xml = simplexml_load_file($inputFilePath);
  $resultArr = $xml->xpath("//reading[@time='$selectedTime' and contains(@date,'$selectedDate')]");

  //need to sort array
  usort($resultArr, 'sortSimpleXMLElementByDateTime'); //fingers crossed this sorting works!!

  $rows = array();
  $table = array();
  $table["cols"] = array(
      array("label" => "date/time", "type" => "date"),
      array("label" => "NO2", "type" => "number")
  );

  $dateFormat = "d/m/Y H:i:s";
  foreach ($resultArr as $single) {
    $reading = simplexml_load_string($single->asXML());
    $date = DateTime::createFromFormat($dateFormat, ($reading->attributes()->date . " " . $reading->attributes()->time));
    $val = $reading->attributes()->val;

    # create json string (for date)
    $temp = array();
    $googleChartsJSONDate = "Date(";
    $googleChartsJSONDate .= date("Y", $date->format("U")) . ", ";
    $googleChartsJSONDate .= (date("m", $date->format("U")) - 1) . ", ";
    $googleChartsJSONDate .= date("d", $date->format("U")) . ", ";
    $googleChartsJSONDate .= date("H", $date->format("U")) . ", ";
    $googleChartsJSONDate .= date("i", $date->format("U")) . ", ";
    $googleChartsJSONDate .= date("s", $date->format("U")) . ")";

    $temp[] = array("v" => $googleChartsJSONDate); //add val
    $temp[] = array("v" => (int) $val); //add val
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
