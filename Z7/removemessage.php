<?php declare(strict_types=1); // włączenie typowania zmiennych
	session_start();
	$link = mysqli_connect('', '', '', ''); //polaczenie z baza danych
	if(!$link) { echo "Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } //obsługa błędu połączenia z BD
	$link->query("SET NAMES 'utf8'");

	$idk = $_GET['idk'];

	$removepost = mysqli_query($link, "DELETE FROM messages WHERE idk='$idk'"); //dodanie do bazy
	header('Location: mainPage.php'); //strona glowna
?>