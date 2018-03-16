<?php

//input and output documents
$inputarray = array("../files/brislington.xml");
$outputarray = array("../files/brislington_no2.xml");

//loop through input producing required output.
for ($i = 0; $i < sizeof($inputarray); $i++) {

  //empty out output file
  file_put_contents($outputarray[$i], "");
  
  //initialize readers and writers and choose documents to read and write.
  $reader = new XMLReader();
  $writer = new XMLWriter();
  $reader->open($inputarray[$i]);
  //$writer->openURI('php://output');
  $writer->openMemory();


  //start writing required XML
  $writer->startDocument('1.0', 'UTF-8');
  $writer->setIndent(4);
  $writer->startElement('data');
  $writer->writeAttribute('type', 'nitrogen dioxide');

  //Will represent the XML document; serves as the root of the document tree.
  $doc = new DOMDocument;

  //variables for getting correct info
  $locationcheck = 0;

  $bufferLimit = 0;

  //start reading the document.
  while ($reader->read()) {
    // move to the first <row /> node
    // NOTE: NEED TO CHECK IF 'row' IS NOT END ELEMENT, BUT START ELEMENT (aka: want <row>, not </row>)
    if ($reader->name == 'row' && $reader->nodeType==XMLReader::ELEMENT) {

      //Get a SimpleXMLElement object from a DOM node - Represents an element in an XML document.
      $node = simplexml_import_dom($doc->importNode($reader->expand(), true));

      if ($locationcheck == 0) {

        $locationcheck = 1;

        $writer->startElement('location');
        $writer->writeAttribute('id', (string) $node->desc->attributes()->val);
        $writer->writeAttribute('lat', (string) $node->lat->attributes()->val);
        $writer->writeAttribute('long', (string) $node->long->attributes()->val);
      }

//      if ($node->date) {
      $writer->startElement('reading');
      $writer->writeAttribute('date', (string) $node->date->attributes()->val);
      $writer->writeAttribute('time', (string) $node->time->attributes()->val);
      $writer->writeAttribute('val', (string) $node->no2->attributes()->val);
      $writer->endElement();
//      $writer->flush();
//      }
      //flush writer frequently so to not go over memory (can use a larger number than 100)
      if (($bufferLimit %= 1000) == 999) {
        //flush/empty onto file
        file_put_contents($outputarray[$i], $writer->flush(true), FILE_APPEND);
      }
      $bufferLimit++;
    }
  }
  $writer->endElement(); //end location tag
  $writer->endElement(); //end data tag
  $writer->endDocument(); //end whole document
  file_put_contents($outputarray[$i], $writer->flush(true), FILE_APPEND);
//  $reader->close();
  echo $bufferLimit;
}
?>
