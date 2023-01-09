<?php declare(strict_types=1); // włączenie typowania zmiennych
	session_start();
	$link = mysqli_connect('', '', '', ''); //polaczenie z baza danych
	if(!$link) { echo "Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } //obsługa błędu połączenia z BD
	$link->query("SET NAMES 'utf8'");

	//aktualnie zalogowany user
	$username =  $_SESSION['username'];
	$datetime = date('Y-m-d H:i:s');
	$message = $_POST['message'];
	$recipient = $_POST['recipient'];

	$target_dir = 'pliki/';
	$file_name = $_FILES["file"]["name"];

	//przetwarzanie plikow
	if($file_name != ""){
		$file_extension = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION); //rozszerzenie dodawanego pliku
		$target_location = $target_dir . $file_name; //lokacja docelowa nowego pliku
		move_uploaded_file($_FILES["file"]["tmp_name"], $target_location); //przeniesienie nowego pliku
	}else{
		$file_extension = "";
		$target_location = ""; //jezeli nie podano nowego pliku, to pusta lokalizacja
	}

	$addpost = mysqli_query($link, "INSERT INTO messages (datetime, message, user, file, ext, recipient) 
	VALUES ('$datetime', '$message', '$username', '$file_name', '$file_extension', '$recipient')"); //dodanie do bazy

	//automatyczne blokowanie
	$find = stripos(strval($message), 'cholera');
	if($find !== false){
		$query = "UPDATE user SET isblock = 1 WHERE login='$username'";
		mysqli_query($link, $query);
	}

	mysqli_close($link);
	header('Location: privatemessages.php'); //strona glowna
?>