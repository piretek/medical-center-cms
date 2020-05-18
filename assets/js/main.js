function clock() {
  var date = new Date();
  var hour = date.getHours();
  var minute = date.getMinutes();
  var second = date.getSeconds();
  var day = date.getDate();
  var dayN = date.getDay();
  var month = date.getMonth();
  var year = date.getFullYear();
    
  if (minute < 10) minute = "0" + minute;
  if (second < 10) second = "0" + second;
    
  var days = new Array("Niedziela", "Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota");
  var months = new Array("stycznia", "lutego", "marca", "kwietnia", "maja", "czerwca", "lipca", "sierpnia", "września", "października", "listopada", "grudnia");
    
  var showDate = days[dayN] + ', ' + day + ' ' + months[month] + ' ' + year + "</br><b>" + hour + ':' + minute + ':' + second + "</b>";
  document.getElementById('date').innerHTML = showDate;           
}    

window.addEventListener('load', function() {
  clock();
  // Perform after page load

  var interval = setInterval(clock, 1000);
})
