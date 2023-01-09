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


		<form method="post" action="topicswitch.php" form enctype="multipart/form-data">
			<select name="topic" id="topic">
				<?php $topics= mysqli_query($link, "SELECT * FROM topics");
					foreach($topics as $row){
						$topicname = $row['name'];
						echo "<option value='" . $topicname . "'>" . $topicname . "</option>";
					}
				?>
			</select>
		<input type="submit" value="Zmień temat"/>

	</div>
	<?php
		echo "Aktualny temat: " . $_SESSION['topic'] . "<br>";
		if($_SESSION['topic']!=''){
			$topic = $_SESSION['topic'];
			if($blocked == 0 && $username != 'gosc'){ 
				echo "<a href='newpost.php'>Nowy post</a>";
			}
		}

		$topic = $_SESSION['topic'];
		$posts = mysqli_query($link, "SELECT * FROM posts WHERE topic='$topic'");
		foreach($posts as $row){
			echo "<br><hr style='width:100%'>";
			$user = $row['user'];
			echo "Post uzytkownika: <a href='userdetails.php?username=$user'>" . $row['user'] . "</a>" . "<br>" . $row['datetime'] . "<br>" . $row['message'] . "<br>";

			$file = $row['file_name'];
			$ext = $row['file_ext'];
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

			$idp = $row['idp'];
			if($row['user'] == $username || $username == 'admin' && $username != 'gosc'){ //jesli zalogowano jako admin lub post jest aktualnego usera
				echo "<br><a href='removepost.php?idp=$idp'>usuń post</a>";
			}
		}
	?>
</body>
</html>