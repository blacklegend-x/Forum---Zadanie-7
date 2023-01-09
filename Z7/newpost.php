<?php declare(strict_types=1); // włączenie typowania zmiennych
session_start();
if (!isset($_SESSION['loggedin']))
{
	$username =  'gosc';
}else{
	//aktualnie zalogowany user
	$username =  $_SESSION['username'];
}

$link = mysqli_connect('', '', '', ''); //polaczenie z baza danych
if(!$link) { echo "Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } //obsługa błędu połączenia z BD
$link->query("SET NAMES 'utf8'");

//ustawianie aktualnego tematu 
if(!isset($_SESSION['topic'])){
	$_SESSION['topic'] = '';
}

if($username != 'gosc'){
	$block = mysqli_query($link, "SELECT * FROM user WHERE login='$username'");
	foreach($block as $row){
		$blocked = $row['isblock']; //czy user zablokowany
	}
}else{
	$blocked = 0;
}
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="fonts/fontawesome/css/all.css">
<link rel="stylesheet" href="maincss.css">
<style>

</style>
</head>
<body>
	<div class="head">
	<p style="font-size: 30px; color: black;">Forum PHP</p>
	<br> Zalogowano w aplikacji jako użytkownik: <?php echo $username ?>
	<br>

</div>
	<div class="menu">
		<a href="logout.php"><button class="button">Wyloguj</button></a>
		<?php if($username != 'gosc'){ ?>
		<a href='privatemessages.php'><button class="button">Wiadomości</button></a>
		<a href='userdetails.php?username=<?=$username?>'><button class="button">Moje stworzone wątki</button></a>
		<?php } ?>
		<?php if($blocked == 0 && $username != 'gosc'){ ?>
		<a href='newtopic.php'><button class="button">Stwórz wątek</button></a>
		<?php } ?>
		<?php if($username == 'admin'){ ?>
		<a href='adminpanel.php'><button class="button">Panel administratora</button></a>
		<?php } ?>
	</div>

	<?php
		$topic = $_SESSION['topic'];
	?>

	Dodanie nowego tematu
	<form method="post" action="addpost.php" form enctype="multipart/form-data">
	Treść:<input type="text" name="message" maxlength="200" size="20"><br>
	Plik:<input type="file" name="file" id="file"><br>
	<input type="submit" value="Send"/>

</body>
</html>