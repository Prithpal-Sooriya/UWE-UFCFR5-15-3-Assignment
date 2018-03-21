/*
 * This is a script that is used with the ScatterChart.html
 * @author Prithal Sooriya
 */

/*
 * starting js script.
 * when the document is ready, it will run a function to show slider details
 * and run the google charts function (to show the graph)
 * 
 * This uses jQuery.
 */
$(document).ready(() => {
  //need to load google to use google charts
  google.charts.load('current', {'packages': ['corechart']});
  google.charts.setOnLoadCallback(updateChart);

  updateSlider();
  updateChart();
});

function updateSlider() {
  let timeSlider = document.getElementById("timeSlider");
  let timeOutput = document.getElementById("timeOutput");
  Slider.updateSlider(timeSlider, timeOutput);
}


/*
 * @function updateChart()
 * function used to update google charts
 * 
 * @note chose to use jquery ajax instead of promises because:
 * - ajax was simpler to use (this example does not use async, the line chart will)
 * - although promises are super nice, there is no way to cancel a promise.
 */
function updateChart() {

  //get json information from php
  let time = document.getElementById("timeOutput").innerHTML;
  let dateElement = document.getElementById("dates");
  let date = dateElement.options[dateElement.selectedIndex].value;
  let locationElement = document.getElementById("location");
  let locationPath = locationElement.options[locationElement.selectedIndex].value;
  let locationName = locationElement.options[locationElement.selectedIndex].text;
  let url = "../scripts/JSONCreatorScatterChart.php";
  let json = $.ajax({
    url: url,
    dataType: "json",
    data: {
      location: locationPath,
      date: date,
      time: time
    },
    type: "POST",
    async: false
  }).responseText;

  //this code was obtained via the google charts website
  //with some of my own customisation
  //https://developers.google.com/chart/interactive/docs/php_example
  //return if the doc is not ready (to preven DataTable from not being known)
  if (document.readyState !== "complete")
    return;
  var data = new google.visualization.DataTable(json);

  //checking if one of the values inside data is 0.
  if (data.og.length == 0) {
    document.getElementById("chart_div").innerHTML = "no chart found!";
    return;
  }

  var options = {
    chartArea: {
      backgroudColor: {
        fill: "#747d8c",
        opacity: 0.8
      }
    },
//    backgroudColor: "#747d8c",
    title: "NO2 over time in " + locationName,
    hAxis: {
      gridlines: {
        count: 12
      },
      title: 'Date'
    },
    vAxis: {
      title: 'NO2',
//      minValue: 0,
//      viewWindow: {
//        min: 0
//      }
    },
    legend: "NO2 from " + date + " time range",
//    width: window.innerWidth - 20,
    height: window.innerHseight / 4
//    explorer: {
//       actions: ['dragToZoom', 'rightClickToReset'],
//       keepInBounds:true
//    }

  };
  var chart = new google.visualization.ScatterChart(document.getElementById("chart_div"));
  chart.draw(data, options);
}