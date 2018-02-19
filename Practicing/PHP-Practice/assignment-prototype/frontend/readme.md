# Assignment - frontend
The assignment task (front end related) is to create a simple web page that will display information in the form of charts/graphs of the data wanted.

## plan of action
- plan 1
  - create a php front end (index.php)
    - so php front end will run on a php server
    - so run php as a script for a client.
    - once data is processed, pass to Google Charts to display data
  - benefits
    - super easy to set up!
  - drawbacks
    - prefer thin clients and performance.

- plan 2
  - create a php server side script
    - it will handle the data parsing back to the client
  - client html will call HTTP-GET to get the information from the server
    - client will handle Google Charts and display contents.
  - Benefits
    - server will compile data as requested (less work on client)
  - drawbacks
    - the client will still need to process google charts
After some searching, I found out that this is known as AJAX...

- plan 3
  - same as plan 2, but php server will pass back google charts.
  - benefits
    - server will do all the computation
  - drawbacks
    - no clue how to do this!!

## currently doing
- play around with html and php
  - play around with AJAX and how to html to communicate with php server
