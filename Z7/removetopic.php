<?php declare(strict_types=1); // włączenie typowania zmiennych
	session_start();
	$link = mysqli_connect('', '', '', ''); //polaczenie z baza danych
	if(!$link) { echo "Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } //obsługa błędu połączenia z BD
	$link->query("SET NAMES 'utf8'");

	$name = $_GET['name'];

	$removepost = mysqli_query($link, "DELETE FROM topics WHERE name='$name'"); //dodanie do bazy
	header('Location: mainPage.php'); //strona glowna
?>