/* 
 * This is a script that is used with the LineChart.html
 * @author Prithpal Sooriya
 * 
 * @NOTE: I will try to use (more) ES6 for this version
 *       compared to ScatterChart.html
 */

/*
 * starting the js script.
 * when the document is ready, it will run some code
 * (show slider details and show google charts)
 * 
 * Using jquery
 * 
 * 2 versions so as to support IE
 */
const loading = () => {
  //load google chart libraries and information (that is used by google charts)
  //running it here because it only needs to be ran once
  google.charts.load('current', {'packages': ['corechart']});
  google.charts.setOnLoadCallback(showGraph);

  //update slider info and google charts
  updateSlider();
  updateChart();
};
$(document).ready(() => {
  loading();
});

$(window).on("load", () => {
  loading();
});

var promisesArr = [];

/*
 * @function updateChartAndSlider()
 * function used for setting up the slider and updating the chart
 * 
 * It will take the slider.value and convert it into a time (and outputs it on the document)
 */
const updateSlider = () => {
  let timeSlider = document.getElementById("timeSlider");
  let timeOutput = document.getElementById("timeOutput");
  Slider.updateSlider(timeSlider, timeOutput);
};

/*
 * @function updateChart()
 * function used to update google charts
 */
const updateChart = () => {

  /*
   * @function getDateString()
   * function used to convert js Date to a string of dd/mm/yyyy
   * this function is only used in this function (so made this a local function)
   * 
   * @param {Date} date, js Date to be converted
   * @returns {string} string date, with format dd/mm/yyyy 
   */
  const getDateString = (date) => {
    let day = date.getDate().toString();
    day = (day.length === 2 ? "" : "0") + day;

    let month = (date.getMonth() + 1).toString(); //month starts at 0, so needs to be +1
    month = (month.length === 2 ? "" : "0") + month;

    let year = date.getFullYear();
    return `${day}/${month}/${year}`;
  };


  //get details from inputs
  //I cant really use form.serialise, thus chose not to use a form.
  //get time input
  let time = document.getElementById("timeOutput").innerHTML;
  //get date input
  let dateDOM = document.getElementById("date").value;
  let date = new Date(dateDOM);
  let datestr1 = getDateString(date);
  date.setDate(date.getDate() + 1);
  let datestr2 = getDateString(date);
  //get file input
  let location = $("#location").val();
  let locationStr = $("#location :selected").text();

  //ajax call using fetch (lets see if I can get it working!!
  //from fetch spec: https://fetch.spec.whatwg.org/#fetch-api 
  let url = "../scripts/JSONCreatorLineChart.php";
  let params = {location: location, time: time, date: datestr1};
  let esc = encodeURIComponent;
  let query = Object.keys(params)
          .map(k => esc(k) + "=" + esc(params[k]))
          .join("&");

//use fetch with await (will make then we will not need to keep using promises!)
//it will also allow waiting for promise to finish.
//info retrieved from here: https://dev.to/johnpaulada/synchronous-fetch-with-asyncawait
  const request = async (url, query) => {
    //wait for fetch to finish (fetch will return promise so response is promise)
//    var p = Promise.race([
//      fetch(url + "?" + query),
//      new Promise((_, reject) =>
//        setTimeout(() => reject(new Error("Timeout")), 7000)
//      )
//    ]);
    const response = await fetch(url + "?" + query);
    //wait for response promise to complete, it will return json
    const json = await response.json();
    return json;
  };

  //request() returns a promise
  var json;
  var promise = request(url, query);
//  promise.then(json => {
//    console.log(json);
//    let title = "NO2 over Time in " + locationStr + " from:\n" + datestr1 + " - " + time + " TO " + datestr2 + " - " + time;
//    showGraph(json, title);
//  });

  //store all resolved promises in this array
  //max size of 3 for a buffer (allow smooth google charts transitions
  if (promisesArr.length < 3)
    promisesArr.push(promise);
  else
    promisesArr[2] = promise;
  Promise.all(promisesArr).then((values) => {
    json = values[values.length - 1];
    let title = "NO2 over Time in " + locationStr + " from:\n" + datestr1 + " - " + time + " TO " + datestr2 + " - " + time;
    showGraph(json, title);
  });
  promisesArr = []; //clear array, to allow new promises in
};

/*
 * @function showGraph(json, title)
 * function that will process and display the google chart graph
 * @param {string} json - The json value retrieved from promise
 * @param {string} title - The title of the graph 
 *  (so as to not pass lots of params to this function).
 * 
 */
const showGraph = (json, title) => {
  if (json === "undefined")
    return;
  if (document.readyState !== "complete")
    return;
  var data = new google.visualization.DataTable(json);
  if (data.og.length === 0) {
    document.getElementById("chart_div").innerHTML = "no chart found!";
    return;
  }

  var options = {
    title: title,
    tooltip: {
      isHtml: true
    },
    hAxis: {
      gridlines: {
        count: -1,
        units: {
          days: {format: ["dd MMM"]},
          hours: {format: ["HH:mm", 'ha']}
        }
      },
      minorGridlines: {
        units: {
          hours: {format: ["hh:mm:ss a", "ha"]},
          minutes: {format: ["HH:mm a Z", ":mm"]}
        }
      }
    },
    vAxis: {
      format: "0"
    },
    width: window.innerWidth,
    height: window.innerHeight / 3
  };
  if (document.readyState !== "complete")
    return;
  var chart = new google.visualization.LineChart(document.getElementById("chart_div"));
  chart.draw(data, options);
  jsonGlobal = null;
};