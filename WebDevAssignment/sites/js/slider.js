/*
 * script that contains showing the value of the slider.
 * This script was created because it is used in most/all of the html charts.
 * So this will prevent redundant code.
 * 
 */

var Slider = {
  updateSlider: (timeSlider, timeOutput) => {
    if (timeSlider === "undefined")
      return;

    //minutes
    let minuteString = timeSlider.value - Math.floor(timeSlider.value); //get decimals only
    minuteString *= 60; //convert decimal to minutes (e.g. 0.25 * 60 = 15 minutes)
    minuteString = minuteString === 0 ? "00" : "" + minuteString;

    //hours
    let hourString = (timeSlider.value < 10 ? "0" : "") + Math.floor(timeSlider.value);

    //update html of timer
    timeOutput.innerHTML = `${hourString}:${minuteString}:00`;
  }
}

