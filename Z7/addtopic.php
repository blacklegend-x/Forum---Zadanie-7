<?php declare(strict_types=1); // włączenie typowania zmiennych
	session_start();
	$link = mysqli_connect('', '', '', ''); //polaczenie z baza danych
	if(!$link) { echo "Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } //obsługa błędu połączenia z BD
	$link->query("SET NAMES 'utf8'");

	//aktualnie zalogowany user
	$username =  $_SESSION['username'];
	$name = $_POST['name'];
	$datetime = date('Y-m-d H:i:s');

	$result = mysqli_query($link, "SELECT * FROM topics WHERE name='$name'"); // wiersza, w którym login=login z formularza
	$rekord = mysqli_fetch_array($result); // wiersza z BD, struktura zmiennej jak w BD

	if(!$rekord){
		$addtopic = mysqli_query($link, "INSERT INTO topics (name, author, datetime) VALUES ('$name', '$username', '$datetime')"); //dodanie do bazy
		echo "Dodano temat!<br><a href='mainPage.php'>Strona główna</a>";
	}else{
		echo "Podany temat już istnieje!<br><a href='mainPage.php'>Strona główna</a>";
	}

	//automatyczne blokowanie
	$find = stripos(strval($name), 'cholera');
	if($find !== false){
		$query = "UPDATE user SET isblock = 1 WHERE login='$username'";
		mysqli_query($link, $query);
	}
?>