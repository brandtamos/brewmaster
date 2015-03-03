
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no, minimal-ui">
<meta charset=utf-8>
<?php
include_once 'functions.php';
include_once 'classes.php';

//$link = ConnectToDB();

$dataObject = BuildTemperatureDataObject();
$eventCollection = BuildEventDataObject();

$mysqli = new mysqli("localhost", "brewmaster", "brewmasterpassword", "brewmaster");
$sql = "CALL GetFridgeDuty()";

$result = $mysqli->query($sql);

if($result){
	$dutyDataObject = "data: [";
	while($row = $result->fetch_object()){
		$dutyDataObject = $dutyDataObject . "{label: '" . $row->Action . "', value: " . $row->total_seconds . "},";
	}
	$dutyDataObject = rtrim($dutyDataObject, ",");
	$dutyDataObject = $dutyDataObject . "]";
}
?>
<script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="http://cdn.oesmith.co.uk/morris-0.4.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/moment.min.js"></script>
<script src="js/bootstrap-datetimepicker.js"></script>
<link href="css/bootstrap.min.css" rel="stylesheet">
<script>
$(document).ready(function(){
	Morris.Line({
	  element: 'temperature-graph',
	  <?php echo $dataObject; ?>,
	  xkey: 'y',
	  ykeys: ['a', 'b'],
	  labels: ['Temperature', 'GoalTemp'],
	  ymin: 'auto',
	  pointSize: 1,
	  continuousLine: true,
	  lineColors: ["blue", "green"],
	  pointStrokeColors: ["blue", "green"],
	  <?php echo $eventCollection->EventData; ?>,
	  <?php echo $eventCollection->EventLineColors; ?>
	});
	
	Morris.Donut({
		element: 'fridge-duty',
		<?php echo $dutyDataObject; ?>,
		colors: ["black", "red"]
	
	});
});
</script>
<style type="text/css">
body{
	padding-top: 35px;
}
</style>
<title>Brewmaster</title>
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Brewmaster</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="http://brandtrpi.duckdns.org:9001">Supervisor</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
</nav>
<div class="container">
  <h3>Set Temperature</h3>
  <p>Set a new temperature for now or some point in the future.</p>
  <form class="form-inline" role="form">
    <div class="form-group">
      <label for="temperature">Temperature:</label>
      <input type="text" class="form-control" id="temperature" placeholder="Enter temperature">
    </div>
    <div class="form-group">
		<div class='input-group date' id='datetimepicker1'>
			<input type='text' class="form-control" />
				<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
			</span>
		</div>
    </div>
	<script type="text/javascript">
		$(function () {
			$('#datetimepicker1').datetimepicker();
		});
	</script>
    <button type="submit" class="btn btn-default">Submit</button>
  </form>
</div>
  <div id="temperature-graph"></div>
  <div id="fridge-duty"></div>
</body>
</html>