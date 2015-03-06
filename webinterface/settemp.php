<?php
include_once 'functions.php';
$temp = "";
$date = "";
if(isset($_POST['temp'])){
	$temp = $_POST['temp'];
}
if(isset($_POST['date'])){
	$date = $_POST['date'];
}


if($date != "" && $temp != ""){
	WriteNewTempToDB($temp, $date);
	echo "{\"result\" : \"SUCCESS\"}";
}
else{
	echo "{\"result\" : \"MISSING_PARAMETERS\"}";
}
?>