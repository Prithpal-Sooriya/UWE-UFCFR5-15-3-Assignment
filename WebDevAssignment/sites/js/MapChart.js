/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
const loading = () => {
  google.charts.load('current', {'packages': ['map'], 'mapsApiKey': config.MAP_KEY});
  google.charts.setOnLoadCallback(drawChart);
  updateSlider();
  updateChart();
};

$(document).ready(() => {
  loading();
});

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
};

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


  let url = "../scripts/JSONCreatorMap.php";
  $.ajax({
    url: url,
    dataType: "json",
    data: {
      time: time,
      date: datestr
    },
    type: "POST",
    success: (data) => drawChart(data)
  });
};

const drawChart = (json) => {
  if (document.readyState !== "complete")
    return;
  var data = new google.visualization.DataTable(json);
//  if (data.og.length === 0) {
////    document.getElementById("chart_title").innerHTML = "";
//    document.getElementById("chart_div").innerHTML = "no data found!";
//    return;
//  }
  console.log(json);
  var options = {
//    sizeAxis: { minValue: 0, maxValue: 100 },
    showTooltip: true,
    showInfoWindow: true,
    useMapTypeControl: true,
    zoomLevel: 12,
    width: window.innerWidth,
    height: window.innerHeight / 4
  };

  var chart = new google.visualization.Map(document.getElementById("chart_div"));
  chart.draw(data, options);


};