<?php

/**
 * This file will convert the the .xml files into no2.xml files
 * 
 * @author Prithpal
 * @package scripts
 */

$inputfileNames = array("brislington.xml", "fishponds.xml", "parson_st.xml", "rupert_st.xml", "wells_rd.xml", "newfoundland_way.xml");
$outputFileNames = array();
$path = "../files/";

//create output filenames
foreach ($inputfileNames as $value) {
  $temp = str_replace(".xml", "", $value);
  $temp .= "_no2.xml";
  $outputFileNames[] = $temp;
}


//if (file_exists($outputFileNames[0])) {
//  echo "output file already exists!";
//  exit(0);
//}



for ($index = 0; $index < count($outputFileNames); $index++) {
  echo "creating $outputFileNames[$index]<br>";
  writeNO2File($path . $inputfileNames[$index], $path . $outputFileNames[$index]);
  echo "created $outputFileNames[$index]<br><br><br>";
}

/**
 * Function used to write a single location.xml file
 * @param string $inputFilePath input file path of xml to use
 * @param sting $outputFilePath output file
 */
function writeNO2File($inputFilePath, $outputFilePath) {

  //xml reader
  $reader = new XMLReader();
  if (!$reader->open($inputFilePath)) {
    die("failed to open '$inputFilePath'");
  }

  //xml writer
  $writer = new XMLWriter();
  $writer->openMemory();

  /*
   * data format wanted:
   * <?xml version="1.0" encoding="UTF-8"?>
   * <data type="nitrogen dioxide">
   *   <location id="wells road" lat="51.427" long="-2.568">
   *     <reading date="13/02/2016" time="03:15:00" val="11"/>
   *     <reading date="13/02/2016" time="03:30:00" val="11"/>
   *     <reading date="13/02/2016" time="03:45:00" val="11"/>
   *     <!-- thouands of other rows -->
   *     <reading date="13/02/2017" time="16:15:00" val="35"/>
   *   </location>
   * </data>
   */

  /*
   * current data format:
   * <records>
   *   <row>
   *     <id val="3"/>
   *     <desc val="Brislington"/>
   *     <date val="31/03/2016"/>
   *     <time val="22:00:00"/>
   *     <nox val="22"/>
   *     <no2 val="6"/>
   *     <lat val="15"/>
   *     <long val="51.4417"/>
   *   </row>
   * </records>
   */

  /*
   * for now just erase output file before use
   * TODO: check if file has been updated and only append.... hardish to do!!
   */
  file_put_contents($outputFilePath, "");

  //start writing the document
  $writer->startDocument("1.0", "UTF-8");
  $writer->setIndent(true);
  $writer->startElement("data");
  $writer->writeAttribute("type", "nitrogen dioxide");

  //xml reader move to first "row"
  while ($reader->read() && $reader->name !== "row");

  $i = 0;

  $notAddLocation = true;
  $domDoc = new DOMDocument;

  //now we will loop through each "row"
  while ($reader->name === "row") {
    //create small DOM of current node/"row"
    //this allow the use of speed of streams and easy readability of DOM
    $xmlElement = simplexml_import_dom($domDoc->importNode($reader->expand(), true));

    if ($notAddLocation) {
      $writer->startElement("location");
      $writer->writeAttribute("id", $xmlElement->desc->attributes()->val);
      $writer->writeAttribute("lat", $xmlElement->lat->attributes()->val);
      $writer->writeAttribute("long", $xmlElement->long->attributes()->val);
      $notAddLocation = false;
    }

    //add 'reading' children
    $writer->startElement("reading");
    $writer->writeAttribute("date", $xmlElement->date->attributes()->val);
    $writer->writeAttribute("time", $xmlElement->time->attributes()->val);
    $writer->writeAttribute("val", $xmlElement->no2->attributes()->val);
    $writer->endElement();

    $reader->next("row");

    //every 1000 iterations (let say) flush the output stream (to use less memory
    if($i === 1000) {
      $i = 0;
      file_put_contents($outputFilePath, $writer->flush(true), FILE_APPEND);
    }
  }

  $writer->endElement(); //</location>
  $writer->endElement(); //</data>
  $writer->endDocument();
  file_put_contents($outputFilePath, $writer->flush(true), FILE_APPEND);

  $reader->close();

}
?>
