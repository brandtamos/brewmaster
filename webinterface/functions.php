<?php
include_once 'classes.php';
function ConnectToDB(){
	$link = mysql_connect('localhost', 'brewmaster', 'brewmasterpassword');
	$mysqliconn = mysqli_connect('localhost', 'brewmaster', 'brewmasterpassword', 'brewmaster');
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db('brewmaster');
	return $link;
}

function GetTemperatureData(){
	//get temperature data
	$link = ConnectToDB();
	$sql = "SELECT ts.ReadingTime, ts.Temperature, (select Temperature from TemperatureSchedule where KeyDate <= ts.ReadingTime order by KeyDate desc limit 1) as GoalTemp FROM TemperatureStatistics ts\n"
    . " where ts.ReadingTime > DATE_ADD(NOW(), INTERVAL -6 HOUR)";
	$result = mysql_query($sql);
	mysql_close($link);
	return $result;
}

function BuildTemperatureDataObject(){
	$tempdata = GetTemperatureData();
	$dataObject = "";
	if($tempdata){
		$dataObject = "data: [";
		while($row = mysql_fetch_array($tempdata)){
			$dataObject = $dataObject . "{ y: '" . $row['ReadingTime'] . "', a: " . $row['Temperature'] . ", b: " . $row['GoalTemp'] . "},";
		}
		$dataObject = rtrim($dataObject, ",");
		$dataObject = $dataObject . "]";
	}
	return $dataObject;
}

function GetPowerActivityData(){
	$link = ConnectToDB();
	//get power activity data
	$sql = "SELECT Date, Action FROM ActivityLog where Date > DATE_ADD(NOW(), INTERVAL -6 HOUR)";
	$result = mysql_query($sql);
	mysql_close($link);
	return $result;
}

function BuildEventDataObject(){
	$eventLineColors = "eventLineColors: [\"red\"]"; //this is just here for safety
	$dataObject = new EventCollection();
	//build event object for graph
	$result = GetPowerActivityData();
	if($result){
		$eventObject = "events: [";
		$firstRecord = true;
		while($row = mysql_fetch_array($result)){
			$eventObject = $eventObject . "'" . $row['Date'] . "',";
			//the first event record determines the color patter for the event lines
			if($firstRecord == true){
				if($row['Action'] == "Power on"){
					$eventLineColors = "eventLineColors: [\"red\", \"black\"]";
				}
				else{
					$eventLineColors = "eventLineColors: [\"black\", \"red\"]";
				}
			}
			$firstRecord = false;
		}
		$eventObject = rtrim($eventObject, ",");
		$eventObject = $eventObject . "]";
		$dataObject->EventData = $eventObject;
		$dataObject->EventLineColors = $eventLineColors;
	}
	
	return $dataObject;
}

function BuildDutyObject(){
	$mysqli = new mysqli("localhost", "brewmaster", "brewmasterpassword", "brewmaster");
	$sql = "CALL GetFridgeDuty()";
	$result = $mysqli->query($sql);
	$dutyDataObject = "";
	if($result){
		$dutyDataObject = "data: [";
		while($row = $result->fetch_object()){
			$dutyDataObject = $dutyDataObject . "{label: '" . $row->Action . "', value: " . $row->total_seconds . "},";
		}
		$dutyDataObject = rtrim($dutyDataObject, ",");
		$dutyDataObject = $dutyDataObject . "]";
	}
	
	return $dutyDataObject;
}

function WriteNewTempToDB($temp, $date){
	$link = ConnectToDB();
	$sql = "INSERT INTO TemperatureSchedule (KeyDate, Temperature) VALUES ('$date', $temp)";
	$result = mysql_query($sql);
	mysql_close($link);
}

?>