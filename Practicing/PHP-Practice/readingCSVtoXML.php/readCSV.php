<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>reading csv</title>
  </head>
  <body>
    <!-- using html just to output, probs will not need this for the server side... -->
    <?php
      /*
      using this as guidance:
      https://stackoverflow.com/a/4853122

      using fgetcsv to read a csv
      - rt flags = "read" and "translation" (for windows \n to \r\n) flags

      DomDocument to create xml document
      - ->createElement() = creates tag of that value
      - appendChild() = indent?, so becomes a child o a node.
      - createTextNode = contains only text.... not sure too much about this..
      */

      //error reporting
      error_reporting(E_ALL | E_STRICT);
      ini_set('display_errors', true);
      ini_set('auto_detect_line_endings', true);

      $inputFileName = "air_quality.csv";
      $outputFileNames = array("brislington.xml", "fishponds.xml", "parson_st.xml", "rupert_st.xml", "wells_rd.xml", "newfoundland_way.xml");

      /*
      Dirty solution to check if output files have been created.

      A better solution would be to check if files were created,
      update files if there was a change
        - or even update files hourly/daily instead.
      */
      echo "reading files<br>";
      if(file_exists($outputFileNames[0])) {
        echo "files are already created!<br>";
        echo "reading complete<br>";
        exit(0);
      }

      //open csv to read
      $inputFile = fopen($inputFileName, "rt");

      //get headers of file
      $headers = fgetcsv($inputFile);

      //need to find out how to do arrays of DOM Documents...
      $docs = array(new DomDocument(), new DomDocument(), new DomDocument(), new DomDocument(), new DomDocument(), new DomDocument());
      for($i = 0; $i < sizeof($docs); $i++){
        $docs[$i]->formatOutput = true;
        //add root node (rows)
        $root = $docs[$i]->createElement("rows");
        $docs[$i]->appendChild($root);
      }

      //loop through csv, may need to do divide and conqure approach
      while(($row = fgetcsv($inputFile)) !== FALSE) {
        $monitor_id = trim($row[0]);
        // exit(0);
        switch($monitor_id) {
          case 3:
            //brislington
            $rows = $docs[0]->getElementsByTagName("rows")->item(0);
            $rowxml = $docs[0]->createElement("row");
            $row_monitor_id = $docs[0]->createElement("monitor_id", $monitor_id);
            $row_monitor_description = $docs[0]->createElement("monitor_description", trim($row[1]));
            $row_date =  $docs[0]->createElement("date", trim($row[2]));
            $row_time =  $docs[0]->createElement("time", trim($row[3]));
            $row_nox =  $docs[0]->createElement("nox", trim($row[4]));
            $row_no =  $docs[0]->createElement("no", trim($row[5]));
            $row_no2 =  $docs[0]->createElement("no2", trim($row[6]));
            $row_lat =  $docs[0]->createElement("lat", trim($row[7]));
            $row_long =  $docs[0]->createElement("long", trim($row[8]));

            $rowxml->appendChild($row_monitor_id);
            $rowxml->appendChild($row_monitor_description);
            $rowxml->appendChild($row_date);
            $rowxml->appendChild($row_time);
            $rowxml->appendChild($row_nox);
            $rowxml->appendChild($row_no);
            $rowxml->appendChild($row_no2);
            $rowxml->appendChild($row_lat);
            $rowxml->appendChild($row_long);

            $rows->appendChild($rowxml);
            break;
          case 6:
            //fishponds
            $rows = $docs[1]->getElementsByTagName("rows")->item(0);
            $rowxml = $docs[1]->createElement("row");
            $row_monitor_id = $docs[1]->createElement("monitor_id", $monitor_id);
            $row_monitor_description = $docs[1]->createElement("monitor_description", trim($row[1]));
            $row_date =  $docs[1]->createElement("date", trim($row[2]));
            $row_time =  $docs[1]->createElement("time", trim($row[3]));
            $row_nox =  $docs[1]->createElement("nox", trim($row[4]));
            $row_no =  $docs[1]->createElement("no", trim($row[5]));
            $row_no2 =  $docs[1]->createElement("no2", trim($row[6]));
            $row_lat =  $docs[1]->createElement("lat", trim($row[7]));
            $row_long =  $docs[1]->createElement("long", trim($row[8]));

            $rowxml->appendChild($row_monitor_id);
            $rowxml->appendChild($row_monitor_description);
            $rowxml->appendChild($row_date);
            $rowxml->appendChild($row_time);
            $rowxml->appendChild($row_nox);
            $rowxml->appendChild($row_no);
            $rowxml->appendChild($row_no2);
            $rowxml->appendChild($row_lat);
            $rowxml->appendChild($row_long);

            $rows->appendChild($rowxml);
            break;
          case 8:
            //parson_st
            $rows = $docs[2]->getElementsByTagName("rows")->item(0);
            $rowxml = $docs[2]->createElement("row");
            $row_monitor_id = $docs[2]->createElement("monitor_id", $monitor_id);
            $row_monitor_description = $docs[2]->createElement("monitor_description", trim($row[1]));
            $row_date =  $docs[2]->createElement("date", trim($row[2]));
            $row_time =  $docs[2]->createElement("time", trim($row[3]));
            $row_nox =  $docs[2]->createElement("nox", trim($row[4]));
            $row_no =  $docs[2]->createElement("no", trim($row[5]));
            $row_no2 =  $docs[2]->createElement("no2", trim($row[6]));
            $row_lat =  $docs[2]->createElement("lat", trim($row[7]));
            $row_long =  $docs[2]->createElement("long", trim($row[8]));

            $rowxml->appendChild($row_monitor_id);
            $rowxml->appendChild($row_monitor_description);
            $rowxml->appendChild($row_date);
            $rowxml->appendChild($row_time);
            $rowxml->appendChild($row_nox);
            $rowxml->appendChild($row_no);
            $rowxml->appendChild($row_no2);
            $rowxml->appendChild($row_lat);
            $rowxml->appendChild($row_long);

            $rows->appendChild($rowxml);
            break;
          case 9:
            //rupert_st
            $rows = $docs[3]->getElementsByTagName("rows")->item(0);
            $rowxml = $docs[3]->createElement("row");
            $row_monitor_id = $docs[3]->createElement("monitor_id", $monitor_id);
            $row_monitor_description = $docs[3]->createElement("monitor_description", trim($row[1]));
            $row_date =  $docs[3]->createElement("date", trim($row[2]));
            $row_time =  $docs[3]->createElement("time", trim($row[3]));
            $row_nox =  $docs[3]->createElement("nox", trim($row[4]));
            $row_no =  $docs[3]->createElement("no", trim($row[5]));
            $row_no2 =  $docs[3]->createElement("no2", trim($row[6]));
            $row_lat =  $docs[3]->createElement("lat", trim($row[7]));
            $row_long =  $docs[3]->createElement("long", trim($row[8]));

            $rowxml->appendChild($row_monitor_id);
            $rowxml->appendChild($row_monitor_description);
            $rowxml->appendChild($row_date);
            $rowxml->appendChild($row_time);
            $rowxml->appendChild($row_nox);
            $rowxml->appendChild($row_no);
            $rowxml->appendChild($row_no2);
            $rowxml->appendChild($row_lat);
            $rowxml->appendChild($row_long);

            $rows->appendChild($rowxml);
            break;
          case 10:
            //wells_road
            $rows = $docs[4]->getElementsByTagName("rows")->item(0);
            $rowxml = $docs[4]->createElement("row");
            $row_monitor_id = $docs[4]->createElement("monitor_id", $monitor_id);
            $row_monitor_description = $docs[4]->createElement("monitor_description", trim($row[1]));
            $row_date =  $docs[4]->createElement("date", trim($row[2]));
            $row_time =  $docs[4]->createElement("time", trim($row[3]));
            $row_nox =  $docs[4]->createElement("nox", trim($row[4]));
            $row_no =  $docs[4]->createElement("no", trim($row[5]));
            $row_no2 =  $docs[4]->createElement("no2", trim($row[6]));
            $row_lat =  $docs[4]->createElement("lat", trim($row[7]));
            $row_long =  $docs[4]->createElement("long", trim($row[8]));

            $rowxml->appendChild($row_monitor_id);
            $rowxml->appendChild($row_monitor_description);
            $rowxml->appendChild($row_date);
            $rowxml->appendChild($row_time);
            $rowxml->appendChild($row_nox);
            $rowxml->appendChild($row_no);
            $rowxml->appendChild($row_no2);
            $rowxml->appendChild($row_lat);
            $rowxml->appendChild($row_long);

            $rows->appendChild($rowxml);
            break;
          case 11:
            //newfoundland_way
            $rows = $docs[5]->getElementsByTagName("rows")->item(0);
            $rowxml = $docs[5]->createElement("row");
            $row_monitor_id = $docs[5]->createElement("monitor_id", $monitor_id);
            $row_monitor_description = $docs[5]->createElement("monitor_description", trim($row[1]));
            $row_date =  $docs[5]->createElement("date", trim($row[2]));
            $row_time =  $docs[5]->createElement("time", trim($row[3]));
            $row_nox =  $docs[5]->createElement("nox", trim($row[4]));
            $row_no =  $docs[5]->createElement("no", trim($row[5]));
            $row_no2 =  $docs[5]->createElement("no2", trim($row[6]));
            $row_lat =  $docs[5]->createElement("lat", trim($row[7]));
            $row_long =  $docs[5]->createElement("long", trim($row[8]));

            $rowxml->appendChild($row_monitor_id);
            $rowxml->appendChild($row_monitor_description);
            $rowxml->appendChild($row_date);
            $rowxml->appendChild($row_time);
            $rowxml->appendChild($row_nox);
            $rowxml->appendChild($row_no);
            $rowxml->appendChild($row_no2);
            $rowxml->appendChild($row_lat);
            $rowxml->appendChild($row_long);

            $rows->appendChild($rowxml);
            break;
          default:
            echo "not defined";
        }
      }

      //now output to files!!!
      for ($i=0; $i < sizeof($docs); $i++) {
        $strxml = $docs[$i]->saveXML();
        $handle = fopen($outputFileNames[$i], "w");
        fwrite($handle, $strxml);
        fclose($handle);
      }

    ?>
    <!-- plugin for live reloading, pretty shit, only works half of the time!! -->
    <script type="text/javascript" src="http://localhost:35729/livereload.js"></script>
  </body>
</html>
