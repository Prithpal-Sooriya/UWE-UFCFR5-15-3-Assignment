<?php

/**
 * This file takes ../files/air_quality.csv and creates multiple .xml files from
 * the csv contents.
 * This file will mainly be used for testing various ways for converting .csv into .xml
 * 
 * @author Prithpal Sooriya
 * @version 0.0.1
 * @package files
 * 
 * Conclusions:
 * - the original version is much faster compared to my version
 *   - there was only 1 second difference for this 1 file conversion, but larger
 *     files and multiple conversions would mean that this difference will increase
 * 
 * @todo TALK TO PRAKESH ABOUT USING DATABASE INSTEAD - result, stick with csv file.
 */


/**
 * start time of script execution
 * @var float
 */
$startTime = microtime(true);

/**
 * path for .CSV
 * @var string
 */
$csvInputPath = "../files/air_quality.csv";

if (!file_exists($csvInputPath)) {
  echo "file $csvInputPath does not exist <br/>";
  die();
}

//3.28 seconds
//memory: 15091936 --> wut!!! thought Prakeshes would use less mem
//version2($csvInputPath);

//3.13 seconds (yayyy mine is a little faster XD)
//memory: 441392
originalCSVReader($csvInputPath);



//echo "reading file: $csvInputPath<br/>";
//checks to see if files have already been created
//echo "beginning to create .xml files<br/>";

/**
 * end time of script execution
 * @var float
 */
$endTime = microtime(true);
echo "Time taken to execute:<br/>" . ($endTime-$startTime) . " seconds <br/>";
echo "<br/><br/>";
echo "Memory used:<br/>";
echo memory_get_peak_usage(false) . "<br/>";



#-------------------------------------------------------------------------------------------------------


function originalCSVReader($csvInputPath) {
  if (($handle = fopen($csvInputPath, "r")) !== FALSE) {

    # define the tags - last col value in csv file is derived so ignore
    $header = array('id', 'desc', 'date', 'time', 'nox', 'no', 'no2', 'lat', 'long');

    # throw away the first line - field names
    fgetcsv($handle, 200, ",");

    # count the number of items in the $header array so we can loop using it
    $cols = count($header);

    #set record count to 1
    $count = 1;
    # set row count to 2 - this is the row in the original csv file
    $row = 2;

    # start ##################################################
    $out = '<records>';

    while (($data = fgetcsv($handle, 200, ",")) !== FALSE) {

      if ($data[0] == 10) {
        $rec = '<row count="' . $count . '" id="' . $row . '">';

        for ($c = 0; $c < $cols; $c++) {
          $rec .= '<' . trim($header[$c]) . ' val="' . trim($data[$c]) . '"/>';
        }
        $rec .= '</row>';
        $count++;
        $out .= $rec;
      }
      $row++;
    }

    $out .= '</records>';
    # finish ##################################################
    # write out file
    file_put_contents('../files/wells_rd.xml', $out);

    fclose($handle);
  }
}

function ownCSVReaderTest($csvInputPath) {
  
  $inputFile = fopen($csvInputPath, "rt");
  
  $doc = new DOMDocument();
  $doc->formatOutput = true;
  $root = $doc->createElement("records");
  $doc->appendChild($root);
  $count = 1; //number of times wells_road appeared
  $rowNum = 2; //row of csv... why do we need this!!
  
  $outputFileName = "wells_rd.xml";
  
  //remove the headers of the csv file. (1st line of file)
  $headers = fgetcsv($inputFile);
  
  while(($row = fgetcsv($inputFile)) !== FALSE) {
    
    $monitor_id = trim($row[0]);
    
    if($monitor_id == 10) {
      
      $records = $doc->getElementsByTagName("records")->item(0);
      
      $rowXML = $doc->createElement("row");
      $rowXML->setAttribute("count", $count);
      $rowXML->setAttribute("id", $rowNum);
      
      $row_id = $doc->createElement("id");
      $row_id->setAttribute("val", $monitor_id);
      $rowXML->appendChild($row_id);
      
      $row_desc = $doc->createElement("desc");
      $row_desc->setAttribute("val", trim($row[1]));
      $rowXML->appendChild($row_desc);
      
      $row_date = $doc->createElement("date");
      $row_date->setAttribute("val", trim($row[2]));
      $rowXML->appendChild($row_date);
      
      $row_time = $doc->createElement("time");
      $row_time->setAttribute("val", trim($row[3]));
      $rowXML->appendChild($row_time);
      
      $row_nox = $doc->createElement("nox");
      $row_nox->setAttribute("val", trim($row[4]));
      $rowXML->appendChild($row_nox);
      
      $row_no = $doc->createElement("no");
      $row_no->setAttribute("val", trim($row[5]));
      $rowXML->appendChild($row_no);
      
      $row_no2 = $doc->createElement("no2");
      $row_no2->setAttribute("val", trim($row[6]));
      $rowXML->appendChild($row_no2);
      
      $row_lat = $doc->createElement("lat");
      $row_lat->setAttribute("val", trim($row[7]));
      $rowXML->appendChild($row_lat);
      
      $row_long = $doc->createElement("long");
      $row_long->setAttribute("val", trim($row[8]));
      $rowXML->appendChild($row_long);
      
      $records->appendChild($rowXML);
      
      $count++;
    }
    $rowNum++;
  }
  
  $strxml =  $doc->saveXML();
  $outputFile = fopen("../files/".$outputFileName, "w");
  fwrite($outputFile, $strxml);
  fclose($outputFile);
}

function createOutputFileName($unformmatedOutput) {
  return str_replace(" ", "_", $unformmatedOutput).".xml";
}

?>
