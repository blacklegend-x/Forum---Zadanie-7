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
	<br> Zalogowano w aplikacji jako użytkownik: <?php echo $username; ?>
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
	</form>

	</div>
	<?php
		echo "Prywatne wiadomości użytkownika: " . $username;

			echo "<br>NOWA WIADOMOŚĆ:";
			$result = mysqli_query($link, "SELECT * FROM user");
	?>

		<?php if($blocked == 0){ ?>
		<form method="post" action="addwiadomosc.php" enctype="multipart/form-data"><br>
			<label for="recipient">Recipient:</label>
			<select id="recipient" name="recipient">
			  <?php 
				  foreach($result as $row)
				  {
				  $user = $row['login']; //nazwy uzytkownikow
				   echo "<option value=" . $user . " >" . $user . "</option>";
				  }
			  ?>
			</select><br>

			Wiadomość:<input type="text" name="message" maxlength="200" size="90"><br>
			File to send:<input type="file" name="file" id="file">

			<input type="submit" value="Wyślij wiadomość"/>
		</form>
		<?php } ?>

		<?php
		echo "<br><br>Wiadomości: ";
		$messages = mysqli_query($link, "SELECT * FROM messages WHERE user='$username' OR recipient='$username'");
		foreach($messages as $row){
			echo "<br><hr style='width:100%'>";
			echo "Nadawca: " . $row['user'] . "<br>Odbiorca: " . $row['recipient'] . "<br>" . $row['datetime'] . "<br>" . $row['message'];

			$file = $row['file'];
			$ext = $row['ext'];
			$path = 'pliki/' . $file;
			if($file != "") //jesli plik istnieje
			{ 
				if($ext == "mp4"){
					echo "<video controls autoplay muted width='320px' height='240px'><source src='$path' type='video/mp4'></video><br>";
				}
				if($ext == "mp3"){
					echo "<audio controls><source src='$path' type='audio/mpeg'></audio><br>";
				}
				if($ext == "jpg" || $ext == "png" || $ext == "jpeg" || $ext == "gif"){
					echo "<img src='$path'><br>";
				}
			}

			$idk = $row['idk'];
			if($row['user'] == $username){ //jesli wiadomosc wyslana przez aktualnego usera
				echo "<br><a href='removemessage.php?idk=$idk'><button>usuń wiadomość</button></a>";
			}
		}
		mysqli_close($link);
	?>
</body>
</html>