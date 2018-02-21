<?php
/*
this php script will read in the xml files for each location (brislington, etc)
and will create a new xml file for a format given.

NOTE: this is supposed to be an improvement compared to the original
      as the original kept timing out.
*/

/*
Plan of Action:
use XML reader to read in the xml line by line
  - a lot faster to use

user XML writer to write the new XML file
  - will need to look up if this is relatively easy to do.
  - compare this to using DomDocument
    - DomDocument is more structured and (personally) easier to us
    - DomDocument was quite slow compared, and is probably the reason for the
      timeouts.

This code (currently) will only take 1 (or all 6) file/s and will convert them
into the smaller files.
  - Later, I will want the user to do HTTP-GET of which file to create.
    So will create the file the user will want only.
  - Later, will need to add checks if file is already created and update file
    if needed.

If the code still doesnt work, lets try some threads/concurrency.

NOTE: we are supposed to learn xpath!! maybe use XPATH may be easier to query data
- using simplexml dom
  - $collection = $var.xpath("xpath code here"); //will return all of the elements that match
- https://stackoverflow.com/questions/721928/xpath-to-select-multiple-tags
- http://sandbox.onlinephpfunctions.com/code/61dfcd2965313c7cb562cdc558b6b1650ae0e71b
  - show how to use xpath with or gates.
*/

//file names
$inputFileName = "wells_rd.xml";
$outputFileName = str_replace(".xml", "", $inputFileName);
$outputFileName = $outputFileName."_no2.xml"; //xml writer requires uri
echo $outputFileName;
// exit(0);

//check if file already exists..
//in future add check to see if file needs updating
if(file_exists($inputFileName)) {
  echo "file already exists!";
  exit(0);
}

//xml reader
$reader = new XMLReader();
if(!$reader->open($inputFileName)) {
  die("failed to open '$inputFileName'");
}

//xml writer
$writer = new XMLWriter();
$writer->openUri($outputFileName);

/*
data format:
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

//start writing the document
$writer->startDocument("1.0");
$writer->setIndent("2");
$writer->startElement("data");
$writer->writeAttribute("type", "nitrogen dioxide");

//xml reader move to first "row"
while($reader->read() && $reader->name !== "row");


$notAddLocation = true;
$domDoc = new DOMDocument;

//now we will loop through each "row"
while($reader->name === "row") {
  //create small DOM of current node/"row"
  $domNode = simplexml_import_dom($domDoc->importNode($reader->expand(), true));
  if($notAddLocation) {
    $writer->startElement("location");
    $writer->writeAttribute("id", $domNode->monitor_description);
    $writer->writeAttribute("lat", $domNode->lat);
    $writer->writeAttribute("long", $domNode->long);
    $notAddLocation = false;
  }

  //add rest of data
  $writer->startElement("reading");
  $writer->writeAttribute("date", $domNode->date);
  $writer->writeAttribute("time", $domNode->time);
  $writer->writeAttribute("val", $domNode->no2);
  $writer->endElement();

  //this will go to the next "row" in xml
  $reader->next("row");
}

$writer->endElement(); //</location>
$writer->endElement(); //</data>
$writer->endDocument();
$writer->flush();

//close reader and writer
$reader->close();

?>
