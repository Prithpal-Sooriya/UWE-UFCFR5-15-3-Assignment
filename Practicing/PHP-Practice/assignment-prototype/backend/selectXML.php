<?php
/*
NOTE: plans for future
- read in http-get request
  - format document requested (if updates are found)
    - this would mean there would need to be alot of XML files stored on server for all the different possibilities... not good
  - http-put response to give xml formatted document back to user

- concurrency to support multiple users... need to learn this!!

- wish this requesting could all be done via xslt... but maybe not best practice
*/

/*
plan of action
NOTE: need to install simplexml onto server/machine!
- open up 1 xml file only (I want to support HTTP-GET of which file in the future)
- use php's SimpleXML functions
  - $xml = simplexml_load_file("foo.xml"); //read in the file
  - can then use DOMDocument functions!!
    - modify XML how we want, for now lets hardcode 1 option.
      - maybe again allow user input/HTTP-GET to choose what parts to get.
        - allow multi HTTP-get user requests = string of args sent down.
  - file_put_contents("bar.xml", $xml->asXML()); //will place info into a new file.
    - I may need to add checks in the future if this is needed
      - check if file exists and if it is out of date (to update file)
      - maybe better to send xml to client and not save (but then not good to HTTP-PUT massive files)
*/

//error reporting
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('auto_detect_line_endings', true);

//input file name (in future allow user to choose)
$inputFileName = "wells_rd.xml";

//ouput file name (in future allow user to choose)
$outputFileName = "wells_rd_no2.xml";

/*
data needs to be in this format:
<?xml version="1.0" encoding="UTF-8"?>
<data type="nitrogen dioxide">
    <location id="wells road" lat="51.427" long="-2.568">
        <reading date="13/02/2016" time="03:15:00" val="11"/>
        <reading date="13/02/2016" time="03:30:00" val="11"/>
        <reading date="13/02/2016" time="03:45:00" val="11"/>
        <!-- thouands of other rows -->
        <reading date="13/02/2017" time="16:15:00" val="35"/>
    </location>
</data>
*/

echo "reading $inputFileName<br/>";
$domDoc = new DomDocument();
$domDoc->formatOutput = true;

//add root node <data>
$rootElement = $domDoc->createElement("data");
$rootAttribute = $domDoc->createAttribute("type");
$rootAttribute->value = "nitrogen dioxide";
$rootElement->appendChild($rootAttribute);
$domDoc->appendChild($rootElement);

//simplexml stuff
$rows = simplexml_load_file($inputFileName);

//add location element to root element
$locationElement = $domDoc->createElement("location");
$locationAttributeID = $domDoc->createAttribute("id");
$locationAttributeID->value = $rows->row[0]->monitor_description;
$locationAttributeLat = $domDoc->createAttribute("lat");
$locationAttributeLat->value = $rows->row[0]->lat;
$locationAttributeLong = $domDoc->createAttribute("long");
$locationAttributeLong->value = $rows->row[0]->long;
$locationElement->appendChild($locationAttributeID);
$locationElement->appendChild($locationAttributeLat);
$locationElement->appendChild($locationAttributeLong);
$rootElement->appendChild($locationElement);

//now to add the rest of the rows contents wanted
$rows_length = $rows->count();
echo "$rows_length<br/>";
//for testing lets just do first 10
for($i = 0; $i < $rows_length; $i++) {
  $quickAccess= $rows->row[$i];
  $readingElement = $domDoc->createElement("reading");
  $newnode = $locationElement->appendChild($readingElement);
  $newnode->setAttribute("date", $quickAccess->date);
  $newnode->setAttribute("time", $quickAccess->time);
  $newnode->setAttribute("val", $quickAccess->val);
  // $readingAttributeDate = $domDoc->createAttribute("date");
  // $readingAttributeDate->value = $quickAccess->date;
  // $readingAttributeTime = $domDoc->createAttribute("time");
  // $readingAttributeTime->value = $quickAccess->time;
  // $readingAttributeVal = $domDoc->createAttribute("val");
  // $readingAttributeVal->value = $quickAccess->no2;
  // $readingElement->appendChild($readingAttributeDate);
  // $readingElement->appendChild($readingAttributeTime);
  // $readingElement->appendChild($readingAttributeVal);
  // $locationElement->appendChild($readingElement);

}



//output to file
$outputString = $domDoc->saveXML();
$handle = fopen($outputFileName, "w");
fwrite($handle, $outputString);
fclose($handle);

echo "Writing to $outputFileName complete<br/>";

?>
