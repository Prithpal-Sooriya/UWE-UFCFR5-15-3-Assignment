/* 
 * This is a script that is used with the PieChart.html
 * @author Prithpal Sooriya
 */

/*
 * starting the js script.
 * when the document is ready, run some code.
 * 2 versions so as to support IE
 */
const loading = () => {
  google.charts.load('current', {'packages': ['corechart']});
  google.charts.setOnLoadCallback(drawChart);
  updateSlider();
  updateChart();
};
$(document).ready(() => {
  loading();
});
//$(window).on("load", () => {
//  loading();
//});

/*
 * @function updateChartAndSlider()
 * function used to update the slider value and also update the chart
 */
const updateSlider = () => {
  let timeSlider = document.getElementById("timeSlider");
  let timeOutput = document.getElementById("timeOutput");
  if (timeSlider === "undefined")
    return;

  //fix minutes
  let minuteString = timeSlider.value - Math.floor(timeSlider.value); //get decimals only
  minuteString *= 60; //convert decimal to minutes (e.g. 0.25 * 60 = 15 minutes)
  minuteString = minuteString === 0 ? "00" : "" + minuteString;

  //fix hours
  let hourString = (timeSlider.value < 10 ? "0" : "") + Math.floor(timeSlider.value);

  //update html
  timeOutput.innerHTML = `${hourString}:${minuteString}:00`;

  //update google charts
//  updateChart();
};


/*
 * @function updateChart()
 * function used to update the chart
 * 
 * uses jquery async ajax
 */
const updateChart = () => {

  /*
   * @function getDateString()
   * function used to convert js date (where month starts at 0)
   * to uk date.
   * @param {Date} date
   * @returns {string} date
   */
  const getDateString = (date) => {
    let day = date.getDate().toString();
    day = (day.length === 2 ? "" : "0") + day;

    let month = (date.getMonth() + 1).toString(); //month starts at 0, so needs to be +1
    month = (month.length === 2 ? "" : "0") + month;

    let year = date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  //get details from user input
  let time = document.getElementById("timeOutput").innerHTML;
  let dateDOM = document.getElementById("date").value;
  let date = new Date(dateDOM);
  let datestr = getDateString(date);

  //perform ajax
  let url = "../scripts/JSONCreatorPieChart.php";
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

  //return if the doc is not ready (to preven DataTable from not being known)
//  if (document.readyState !== "complete")
//    return;
//  var data = new google.visualization.DataTable(json);
//  if (data.og.length == 0) {
//    document.getElementById("chart_div").innerHTML = "no data found!";
//    return;
//  }
//  var options = {
//    title: "NO2 comparison at: " + datestr + " " + time,
//    width: window.innerWidth,
//    height: window.innerHeight / 4
//  };
//  var chart = new google.visualization.PieChart(document.getElementById("chart_div"));
//  chart.draw(data, options);
};

const drawChart = (json, time, datestr) => {
  if (document.readyState !== "complete")
    return;
  var data = new google.visualization.DataTable(json);
  if (data.og.length === 0) {
    document.getElementById("chart_title").innerHTML = "";
    document.getElementById("chart_div").innerHTML = "no data found!";
    return;
  }
  console.log(json);
  document.getElementById("chart_title").innerHTML = "NO2 comparison at: " + datestr + " " + time;
  var options = {
    titlePosition: 'none',
    pieSliceText: 'value',
    width: window.innerWidth,
    height: window.innerHeight / 4
  };
  var chart = new google.visualization.PieChart(document.getElementById("chart_div"));
  chart.draw(data, options);
};



