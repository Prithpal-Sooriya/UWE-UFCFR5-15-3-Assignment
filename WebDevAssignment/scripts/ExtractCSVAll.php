<?php

/**
 * This file will convert the air_quality.csv file into MULTIPLE .xml files
 * 
 * @author Prithpal
 * @version 0.0.1
 * @package files
 * 
 * 
 * 
 * @todo Future plans 1:
 *  - checks if output files have already been created
 *    - will need to add checks if .csv has been updated (so update .xml files)
 *  - divide and conquer approach when reading and writing file...
 * 
 * @todo Future plans 2:
 *  - refactor this code!!
 *    - use associative array of outputfilenames!!
 *      - make it dynamic do creates array when reading files
 *        - removes need of switch!!
 *    - place code into functions
 *    - maybe place, outputfilenames, outputfileStrings and
 *        association key of into a class...
 *    - finally think about using threads :/
 */

$csvInputPath = "../files/air_quality.csv";
$outputFileNames = array("brislington.xml", "fishponds.xml", "parson_st.xml", "rupert_st.xml", "wells_rd.xml", "newfoundland_way.xml");

//if input file does not exist
if(!file_exists($csvInputPath)) {
  echo "input file does not exist!";
  exit(0); //NOTE: exit and die are the same
}

//if output file already exists
//if(file_exists("../files/".$outputFileNames[0])) {
//  echo "output files already exists!";
//  exit(0);
//}

$inputFile = fopen($csvInputPath, "rt");

/*
WHY DO WE NEED THIS!!
$count = 1;
$rowNum = 2;
*/

//remove the headers of the csv file (1st line of file)
$count = count(fgetcsv($inputFile)) - 1; //-1 because we do not want location

//array of tags/elements to create
$tags = array("id", "desc", "date", "time", "nox", "no", "no2", "lat", "long");
echo $count;
# START CREATING FILES

//array of strings representing each file
//root DOM
$outputFileStrings = array(
    "<records>", "<records>", "<records>", 
    "<records>", "<records>", "<records>");

//contents
while(($row = fgetcsv($inputFile)) !== FALSE) {
  
  $monitor_id = trim($row[0]);
  switch($monitor_id) {
    case 3: //Brislington
      $outputFileStrings[0] .= addRow($row, $tags, $count);
      break;
    case 6: //Fishponds
      $outputFileStrings[1] .= addRow($row, $tags, $count);
      break;
    case 8: //Parson Street
      $outputFileStrings[2] .= addRow($row, $tags, $count);
      break;
    case 9: //Rupert Street
      $outputFileStrings[3] .= addRow($row, $tags, $count);
      break;
    case 10: //Wells Road
      $outputFileStrings[4] .= addRow($row, $tags, $count);
      break;
    case 11: //Newfoundland way
      $outputFileStrings[5] .= addRow($row, $tags, $count);
      break;
  } 
}

//end root DOM
for ($index = 0; $index < count($outputFileStrings); $index++) {
  $outputFileStrings[$index] .= "</records>";
}

# END CREATING FILES

# WRITE FILES
for ($index = 0; $index < count($outputFileNames); $index++) {
  file_put_contents(
          "../files/" . $outputFileNames[$index],
          $outputFileStrings[$index]);
}
fclose($inputFile); //good practice to close file.

/**
 * This creates a <row> element and returns it as a string
 * 
 * @param string[] $csvArr The input tags from the .csv.
 *    It will take these values and use them as attributes for each
 *    inner <row> element.
 * @param string[] $tagsArr The names for the inner element tags for the
 *    <row> element.
 * @param integer $count The number of inner elements to add to <row>.
 *    It is to be used to query both arrays above.
 * 
 * @return string string of the xml <row> created.
 * 
 * @todo Need to add checks/validation for the $count param.
 * 
 * 
 */
function addRow($csvArr, $tagsArr, $count) {
  $ret = '<row>';
  for ($index = 0; $index < $count; $index++) {
    $ret .= '<' . $tagsArr[$index] . ' val="' . trim($csvArr[$index]) . '"/>';
  }
  $ret .= '</row>';
  return $ret;
}
?>

