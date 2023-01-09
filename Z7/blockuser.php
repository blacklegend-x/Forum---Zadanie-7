<?php declare(strict_types=1); // włączenie typowania zmiennych
	session_start();
	$link = mysqli_connect('', '', '', ''); //polaczenie z baza danych
	if(!$link) { echo "Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } //obsługa błędu połączenia z BD
	$link->query("SET NAMES 'utf8'");

	//aktualnie zalogowany user
	$username =  $_GET['username'];
	$blockstatus = $_GET['blockstatus'];
	$datetime = date('Y-m-d H:i:s');

	if($blockstatus == 0){
		$query = "UPDATE user SET isblock = 1 WHERE login='$username'";
	}else{
		$query = "UPDATE user SET isblock = 0 WHERE login='$username'";
	}
	mysqli_query($link, $query);
	mysqli_close($link);
	header('Location: mainPage.php');
?>