# UWE-UFCFR5-15-3 Assignment
PHP and Google charts integration with a Bristol Air Quality dataset.

Code can be found from [my Github Repository](https://github.com/Prithpal-Sooriya/UWE-UFCFR5-15-3-Assignment)
- the data files can be found in the repository's [WebDevAssignment/files](https://github.com/Prithpal-Sooriya/UWE-UFCFR5-15-3-Assignment/tree/master/WebDevAssignment/files) directory.

-----
# Stream Parsers
This is a short discussion of what Stream parsers are and why/when they are used for document processing.

## What are Stream parsers

## Why use Stream parsers
- processing large documents can utilise alot of memory and can also be very time consuming.
- Since Stream parsers will read a document sequentially (one line at a time), they do not use alot of memory when processing large documents.

SHOW EXAMPLE USE OF Stream PARSER VS DOM PARSER WITH A TABLE/image

| DOM parser time | Other example time    |
| :------------- | :------------- |
| Item One       | Item Two       |

## issues with Stream parsers
- However, depending on the document to process, because Stream parsers read and write files sequentially, they may need to take multiple passes through the document to create a new document required.
  - most of these issues can be mitigated by the use of careful design and planning of using Stream parsers compared to DOM parsers.
- Stream parsers can also be harder to read compared to DOM objects.
  - Best practice with Stream parsers is to wrap the code into a function, thus abstracting away the complexity by just calling the function. This will work for same datasets, but not for different datasets...
  - Documentation is important for other developers to understand the purpose of the DOM parser.

-----
# Visualisation Extension

## Scatter Chart
- The scatter chart has included colouring for each individual point (based on the colour encodings from the [DEFRA](https://uk-air.defra.gov.uk/air-pollution/daqi) site).
- noted that some points from the dataset are negative (bad) values, I have chose to leave them in and give a "blue" colour to them. This was because (after advice from our Tutor Prakash) no information was given to either disclude these results or to use the `abs` function on them (to make the result absolute).
- Added user input of:
  - Year (e.g. 2015) via html select/dropdown;
  - Location (e.g. Fishponds) via html select/dropdown;
  - And time (e.g. 10:00:00) via html input range/slider;

## Line Chart
- added a customised tooltip, which also included colour encodings from the [DEFRA](https://uk-air.defra.gov.uk/air-pollution/daqi) site for the corresponding NO2 values.
- Added user input for:
  - Date (e.g. 12/06/2016 in the DD/MM/YYYY format) using HTML date;
  - Location (e.g. Fishponds) via HTML select/dropdown;
  - Time Interval (e.g. 08:00:00) va HTML range/slider.
- Used majority ES6 Javascript Coding style (to utilise some benefits of modern Javascript).
- This example used asynchronous Promises (by using the ES6 `fetch` command in JavaScript).
  - By utilising asynchronous calls, I could chain multiple calls to the php server (thus allowing for a smooth transition of chart updates when using the slider).
  - Although this was a new paradigm I was using, I did face some issues, that are still somewhat visible. The main example is by continuously moving the time interval slider for a prolonged period of time, it would cause a massive chain of Promises to complete - thus causing some "lag" till the chart is updated with the latest value.
    - A solution to this was to store all the `Promise`'s in an array (via `array.push() function`), then call the `Promise.all()` function to call only the latest promise. This Limited the number of promises to execute, and also only update the graph with the latest promise.
    - I did also try adding a timeout to the fetch promise, however that did not solve the problem (and ended up stopping new fetch requests).

## Pi Chart
- added a pi chart that displays the percentage and quantity of NO2 in different locations
- user selection provided for the date (DD/MM/YYYY) and time (HH:MM:SS).
- JQuery Ajax asynchronous calls for this (to show a different method of calling the php server).
```Javascript
let json = $.ajax({
  url: url,
  dataType: "json",
  data: {
    time: time,
    date: datestr
  },
  type: "POST",
  success: (data) => drawChart(data, time, datestr)
//    async: false
}).responseText;
```
  - JQuery async Ajax was quite easy to work with compared to Promises, however lacks flexability compared to Promises (with customised commands to do next with the `.then` command, and also easy catching of errors with `.catch` command)
