<?php declare(strict_types=1); // włączenie typowania zmiennych
session_start();
if (!isset($_SESSION['loggedin']))
{
	header('Location: login.php');
	exit();
}

$link = mysqli_connect('', '', '', ''); //polaczenie z baza danych
if(!$link) { echo "Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } //obsługa błędu połączenia z BD
$link->query("SET NAMES 'utf8'");
//aktualnie zalogowany user
$username =  $_SESSION['username'];

//ustawianie aktualnego tematu 
if(!isset($_SESSION['topic'])){
	$_SESSION['topic'] = '';
}

$block = mysqli_query($link, "SELECT * FROM user WHERE login='$username'");
foreach($block as $row){
	$blocked = $row['isblock']; //czy user zablokowany
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
		<a href='privatemessages.php'><button class="button">Wiadomości</button></a>
		<a href='userdetails.php?username=<?=$username?>'><button class="button">Moje stworzone wątki</button></a>
		<?php if($blocked == 0){ ?>
		<a href='newtopic.php'><button class="button">Stwórz wątek</button></a>
		<?php } ?>
		<?php if($username == 'admin'){ ?>
		<a href='adminpanel.php'><button class="button">Panel administratora</button></a>
		<?php } ?>


		<form method="post" action="topicswitch.php" form enctype="multipart/form-data">
			<select name="topic" id="topic">
				<?php $topics= mysqli_query($link, "SELECT * FROM topics");
					foreach($topics as $row){
						$topicname = $row['name'];
						echo "<option value=$topicname>$topicname</option>";
					}
				?>
			</select>
		<input type="submit" value="Zmień temat"/>

	</div>
	<?php
		echo "PANEL ADMINISTRATORA";
		echo "<br><hr style='width:100%'>";
		$users = mysqli_query($link, "SELECT * FROM user");
		foreach($users as $row){
			$login = $row['login'];
			echo "<br><a href='userdetails.php?username=$login'>" . $login . "</a>";
		}
	?>
</body>
</html>