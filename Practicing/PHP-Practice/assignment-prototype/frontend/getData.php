<?php

// This is just an example of reading server side data and sending it to the client.
// It reads a json formatted text file and outputs it.




// Instead you can query your database and parse into JSON etc etc
$xml = simplexml_load_file("../backend/fishponds_no2.xml");

//print_r($xml);

$tester = $xml->type;

//echo $tester;

$result = $xml->xpath("//reading[@time='08:00:00' and contains(@date,'2016')]");

$rows = array();
$table = array();
$table["cols"] = array(
  array("label" => "date/time", "type" => "date"),
  array("label" => "NO2", "type" => "number")
);

$dateFormat = "d/m/Y H:i:s";
foreach ($result as $single){
	$reading = simplexml_load_string($single->asXML());
  $date = DateTime::createFromFormat($dateFormat, ($reading->attributes()->date ." ". $reading->attributes()->time));
  $val = $reading->attributes()->val;


  # create json string (for date)
  $temp = array();
  $googleChartsJSONDate = "Date(";
  $googleChartsJSONDate .= date("Y", $date->format("U")).", ";
  // if(date("Y", $date->format("U")) == "2017"){
  //   echo "2017<br/>";
  // }
  $googleChartsJSONDate .= (date("m", $date->format("U"))-1).", ";
  $googleChartsJSONDate .= date("d", $date->format("U")).", ";
  $googleChartsJSONDate .= date("H", $date->format("U")).", ";
  $googleChartsJSONDate .= date("i", $date->format("U")).", ";
  $googleChartsJSONDate .= date("s", $date->format("U")).")";

  $temp[] = array("v" => $googleChartsJSONDate); //add val
  $temp[] = array("v" => (int) $val); //add val
  $rows[] = array("c" => $temp); //add row to new column
}

$table["rows"] = $rows;
$tableJSON = json_encode($table);
echo $tableJSON;

#$masterarray = array('date', 'time', 'val');
# foreach ($masterarray as $mastervalue){
#   $object = ($result->$mastervalue); //get object
#  $array = (array) $object;  //turn object into an array

#   foreach($array as $key=>$value) {
#    foreach($value as $key=>$answer){
#      echo $answer;
#    }
#  }
#}


//$value = (string) $result->@attributes[0]->val;

//echo $result->attributes[0]->time->__toString();
//echo json_encode($result);
//print_r($result);

//$json = json_encode($xml); // convert the XML string to JSON

//$array = json_decode($json,TRUE); // convert the JSON-encoded string to a PHP variable

//echo xml_attribute($result, 'time');

//function xml_attribute($object, $attribute)
////    if(isset($object[$attribute]))
//        return (string) $object[$attribute];
//}



?>
