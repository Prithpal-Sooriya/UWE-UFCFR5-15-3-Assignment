<?php
/*
This php script will convert the xml files into something that can be used with
the client (for google charts)
- this will be a json table!
*/

// Get Query String (used for deciding which table to show)
// $q = $_REQUEST['q'];


//current version will only support wells_rd_no2.xml
$rows = array();
$table = array();
$table["cols"] = array(
  //labels for chart
  /*
  This is an accosiative array

  types supported by google charts:
    string', 'number', 'boolean', 'date', 'datetime', and 'timeofday'.

  new Date(Year, Month, Day, Hours, Minutes, Seconds, Milliseconds)
  ISO 8601 — 'YYYY-MM-DD'
  RFC 2822 — 'MMM DD, YYYY' or 'DD MMM, YYYY'
  json formatted dates:
    Date(year, month, day[,hour, minute, second[, millisecond]])
    where everything after day is optional, and months are zero-based.
  */
  array("label" => "date/time", "type" => "date"),
  array("label" => "NO2", "type" => "number")

);

#get the result (from csv)
/*
lets use xmlreader

issues of using xpath
- issue 1 = memory allocation (alot of mem used...)
- issue 2 = data quite ordered
*/
$inputFileName = "../backend/wells_rd_no2.xml";
$reader = new XMLReader();
if(!$reader->open($inputFileName)) {
  die("failed to open '$inputFileName'");
}

//get to 'reading' nodes
while($reader->read() && $reader->name !== "reading");

$dateFormat = "d/m/Y H:i:s";
$domDoc = new DOMDocument;
// NOTE: need to allow user selection of time interval in PAST YEAR
while($reader->name === "reading") {
  $domNode = simplexml_import_dom($domDoc->importNode($reader->expand(), true));
  $date = DateTime::createFromFormat($dateFormat, ($domNode->attributes()->date ." ". $domNode->attributes()->time));
  $val = $domNode->attributes()->val;
  $temp = array(); //reset temp array (do not want to keep alloc data)
  // echo $date->format("U");
  // exit(0);
  //json formatted date: Date(year, month, day[,hour, minute, second[, millisecond]])
  //using date function to get part of date
  $googleChartsJSONDate = "Date(";
  $googleChartsJSONDate .= date("Y", $date->format("U")).", ";
  $googleChartsJSONDate .= date("m", $date->format("U")).", ";
  $googleChartsJSONDate .= date("d", $date->format("U")).", ";
  $googleChartsJSONDate .= date("H", $date->format("U")).", ";
  $googleChartsJSONDate .= date("i", $date->format("U")).", ";
  $googleChartsJSONDate .= date("s", $date->format("U")).")";

  // echo $googleChartsJSONDate."<br/>";
  // echo "hello, fuckin work please!!<br/>";
  // exit(0);

  $temp[] = array("v" => $googleChartsJSONDate); //add val
  $temp[] = array("v" => (int) $val); //add val
  $rows[] = array("c" => $temp); //add row to new column
  $reader->next("reading");
}

$table["rows"] = $rows;

//convert table into JSON format :DDD pls work...
//there is a json encode in php
$tableJSON = json_encode($table);

//echo this shit out --> since we want AJAX my man!!
echo $tableJSON;
?>
