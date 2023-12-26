<?php
//-------------------------------------------------------------------------------------------
require 'config.php';

$db;
	$sql1 = "SELECT * FROM stat WHERE id = 0";
	$result1 = $db->query($sql1);
	if (!$result1) {
	  { echo "Error: " . $sql1 . "<br>" . $db->error; }
	}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
        $(document).ready(function() {
            setInterval(function() {
                $.ajax({
                    url: 'refresh.php',
                    success: function(refresh) {
                        $('#tablebody').html(refresh);
                    }
                });
            }, 100); // Refresh every 5 seconds
        });
    </script>
<style>
.chart {
  width: 100%; 
  min-height: 450px;
}
.row {
  margin:0 !important;
}
html {
          font-family: Arial;
          display: inline-block;
          margin: 0px auto;
          text-align: center;
      }
      
      h1 { font-size: 2.0rem; color:#2980b9;}

      .buttonON {
        display: inline-block;
        padding: 15px 25px;
        margin-top: 20px;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        outline: none;
        color: #fff;
        background-color: #4CAF50;
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px #999;
      }
      .buttonON:hover {background-color: #3e8e41}
      .buttonON:active {
        background-color: #3e8e41;
        box-shadow: 0 1px #666;
        transform: translateY(4px);
      }
</style>
 
</head>
<body>
  
<div class="container">
  
	<div class="row">
  		<div class="col-md-12 text-center">
    		<h1>Real Time Weather Station</h1>
    		<p>Device : <a href="#">Eco-Bator</a></p>
  		</div>
  		<div class="clearfix"></div>
  
		<div class="col-md-6">
    		<div id="chart_temperature" class="chart"></div>
  		</div>
  
		<div class="col-md-6">
    		<div id="chart_humidity" class="chart"></div>
  		</div>
	</div>
</div>

<div class="col-md-12 text-center">
	<form action="updatedstatus.php" method="post" id="InFanOn" onsubmit="myFunction()">
		<label>Override control
			 <select name="man">
           		 <option value="0">OFF</option>
            	 <option value="1">ON</option>
        	</select>
   		</label>
    <br>
    	<label>Inlet Fan :
			 <select name="infan">
           		 <option value="1">ON</option>
            	 <option value="0">OFF</option>
        	</select>
   		</label>
		<label>Exhaust Fan :
			<select name="exfan">
            	<option value="1">ON</option>
            	<option value="0">OFF</option>
        	</select>
    	</label> 
    	<label>Lamp :
    		<select name="lamp">
            	<option value="1">ON</option>
            	<option value="0">OFF</option>
        	</select>
    	</label> 
    	<label>Humidifier :
    		<select name="humd">
            	<option value="1">ON</option>
            	<option value="0">OFF</option>
        	</select>
    	</label>           
    </form>
    <button class="buttonON" name= "subject" type="submit" form="InFanOn" value="SubmitLEDON" >Submit</button>
</div>
<br><br>

<div class="container">
	<div class="row">
	<div class="col-md-12">
		<table class="table">
			<thead>
				<tr>
					<th scope="col">Control Type</th>	
					<th scope="col">Inlet Fan</th>
        			<th scope="col">Exhaust Fan</th>
        			<th scope="col">Lamp</th>
        			<th scope="col">Humidifier</th>
      			</tr>
    		</thead>
   			<tbody>
			   <?PHP $i = 0; while ($row = mysqli_fetch_assoc($result1)) {?>
      				<tr>						
						<td><?PHP if ($row["manual"] == 0) {
            					echo "Automatic Control";
        						} else {
            					echo "Manual Control";}?>
						</td>
						<td><?PHP if ($row["infanstatus"] == 0) {
            					echo "Fan is OFF";
        						} else {
            					echo "Fan is ON";}?>
						</td>
        				<td><?PHP if ($row["exfanstatus"] == 0) {
            					echo "Fan is OFF";
        						} else {
            					echo "Fan is ON";}?>
							</td>
        				<td><?PHP if ($row["lampstatus"] == 0) {
            					echo "Lamp is OFF";
        						} else {
            					echo "Lamp is ON";}?>
							</td>
        				<td><?PHP if ($row["humidifierstatus"] == 0) {
            					echo "Humidifier is OFF";
        						} else {
            					echo "Humidifier is ON";}?>
							</td>
      				</tr>
     			<?PHP } ?>	
    		</tbody>
		</table>
	</div>
	</div>

	<br><br>
	<div class="row">
	<div class="col-md-12">
		<table class="table">
			<thead>
				<tr>
        			<th scope="col">ID</th>
        			<th scope="col">Temperature</th>
        			<th scope="col">Humidity</th>
        			<th scope="col">date time</th>
      			</tr>
    		</thead>
   			<tbody id="tablebody">
     			
    		</tbody>
		</table>
	</div>
	</div>
</div>
<!-- ---------------------------------------------------------------------------------------- -->
 
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
//$(document).ready(function(){
//-------------------------------------------------------------------------------------------------
google.charts.load('current', {'packages':['gauge']});
google.charts.setOnLoadCallback(drawTemperatureChart);
//-------------------------------------------------------------------------------------------------
function drawTemperatureChart() {
	//guage starting values
	var data = google.visualization.arrayToDataTable([
		['Label', 'Value'],
		['Temperature', 0],
	]);
	//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
	var options = {
		width: 		1600, 
		height: 	480,
		redFrom: 	70, 
		redTo:		100,
		yellowFrom:	40, 
		yellowTo: 	70,
		greenFrom:	10, 
		greenTo: 	40,
		minorTicks: 5
	};
	//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
	var chart = new google.visualization.Gauge(document.getElementById('chart_temperature'));
	chart.draw(data, options);
	//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN



	//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
	function refreshData () {
		$.ajax({
			url: 'getdata.php',
			// use value from select element
			data: 'q=' + $("#users").val(),
			dataType: 'json',
			success: function (responseText) {
				//______________________________________________________________
				//console.log(responseText);
				var var_temp = parseFloat(responseText.temp).toFixed(2)
				//console.log(var_temperature);
				// use response from php for data table
				//______________________________________________________________
				//guage starting values
				var data = google.visualization.arrayToDataTable([
					['Label', 'Value'],
					['Temperature', eval(var_temp)],
				]);
				//______________________________________________________________
				//var chart = new google.visualization.Gauge(document.getElementById('chart_temperature'));
				chart.draw(data, options);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown + ': ' + textStatus);
			}
		});
    }
	//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
	//refreshData();
	
	setInterval(refreshData, 1000);
}
//-------------------------------------------------------------------------------------------------



//-------------------------------------------------------------------------------------------------
google.charts.load('current', {'packages':['gauge']});
google.charts.setOnLoadCallback(drawHumidityChart);
//-------------------------------------------------------------------------------------------------
function drawHumidityChart() {
	//guage starting values
	var data = google.visualization.arrayToDataTable([
		['Label', 'Value'],
		['Humidity', 0],
	]);
	//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
	var options = {
		width: 		1600, 
		height: 	480,
		redFrom: 	70, 
		redTo:		100,
		yellowFrom:	40, 
		yellowTo: 	70,
		greenFrom:	10, 
		greenTo: 	40,
		minorTicks: 5
	};
	//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
	var chart = new google.visualization.Gauge(document.getElementById('chart_humidity'));
	chart.draw(data, options);
	//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN



	//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
	function refreshData () {
		$.ajax({
			url: 'getdata.php',
			// use value from select element
			data: 'q=' + $("#users").val(),
			dataType: 'json',
			success: function (responseText) {
				//______________________________________________________________
				//console.log(responseText);
				var var_humd = parseFloat(responseText.humd).toFixed(2)
				//console.log(var_temperature);
				// use response from php for data table
				//______________________________________________________________
				//guage starting values
				var data = google.visualization.arrayToDataTable([
					['Label', 'Value'],
					['Humidity', eval(var_humd)],
				]);
				//______________________________________________________________
				//var chart = new google.visualization.Gauge(document.getElementById('chart_temperature'));
				chart.draw(data, options);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown + ': ' + textStatus);
			}
		});
    }
	//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
	//refreshData();
	
	setInterval(refreshData, 1000);
}
//-------------------------------------------------------------------------------------------------

//});




$(window).resize(function(){
  drawTemperatureChart();
  drawHumidityChart();
});





</script>
<!-- --------------------------------------------------------------------- -->
</body>
</html>
