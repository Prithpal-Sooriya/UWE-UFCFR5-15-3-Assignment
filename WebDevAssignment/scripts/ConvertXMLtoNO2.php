<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$inputfileNames = array("brislington.xml", "fishponds.xml", "parson_st.xml", "rupert_st.xml", "wells_rd.xml", "newfoundland_way.xml");
$ouputFileNames = array();
$path = "../files/";

//create output filenames
foreach ($inputfileNames as $value) {
  $temp = str_replace(".xml", "", $value);
  $temp .= "_no2.xml";
  $outputFileNames[] = $temp;
}

print_r($outputFileNames);

if(file_exists($ouputFileNames[0])) {
  echo "output file already exists!";
  exit(0);
}

for ($index = 0; $index < count($ouputFileNames); $index++) {
  writeNO2File($path.$inputName, $path.$outputName);
}


function writeNO2File($inputName, $outputName) {
  //xml reader
  $reader = new XMLReader();
  if(!$reader->open($inputFileName)) {
    die("failed to open '$inputFileName'");
  }
  
  //xml writer
  $writer = new XMLWriter();
  $writer->openUri($outputFileName);
  
  //start writing the document
  $writer->startDocument("1.0");
  $writer->setIndent("2");
  $writer->startElement("data");
  $writer->writeAttribute("type", "nitrogen dioxide");
  
  //xml reader move to first "row"
  while($reader->read() && $reader->name !== "row");
  
  
}

?>
