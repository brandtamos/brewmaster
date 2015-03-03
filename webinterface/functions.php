<?php
function ConnectToDB(){
	$link = mysql_connect('localhost', 'brewmaster', 'brewmasterpassword');
	$mysqliconn = mysqli_connect('localhost', 'brewmaster', 'brewmasterpassword', 'brewmaster');
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db('brewmaster');
	return $link;
}

?>