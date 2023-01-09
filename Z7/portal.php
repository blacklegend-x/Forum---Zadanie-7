<?php declare(strict_types=1); // włączenie typowania zmiennych
session_start();
if (!isset($_SESSION['loggedin']))
{
	header('Location: login.php');
	exit();
}
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="fonts/fontawesome/css/all.css">
<style>
</style>
</head>
<body>
	Zalogowano w aplikacji jako użytkownik: 
	
<?php
$link = mysqli_connect('', '', '', ''); //polaczenie z baza danych
if(!$link) { echo "Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } //obsługa błędu połączenia z BD
$link->query("SET NAMES 'utf8'");

//aktualnie zalogowany user
$username =  $_SESSION['username'];
echo $username;
echo "<br><a href ='logout.php'>Wyloguj</a><br/>";

//MYSPOTIFY

//id zalogowanego usera
$idu_sql = mysqli_query($link, "SELECT idu FROM user WHERE login='$username'");
$idu_sql_array = mysqli_fetch_array($idu_sql);
$idu = $idu_sql_array[0];
$_SESSION['idu'] = $idu;

//dostepne gatunki
$type_sql = mysqli_query($link, "SELECT * FROM filmtype");

//dostepne piosenki
$song_sql = mysqli_query($link, "SELECT * FROM film");

//dostepne nazwy playlist_name
$playlist_name_sql = mysqli_query($link, "SELECT * FROM playlistname");

//domyslne ustawienia playlisty
if(!isset($_POST['playlist'])){
	$_POST['playlist'] = "default";
}
?>

<div id="fileform" display="inline-block">
	<br><form action="newfile.php" enctype="multipart/form-data" method="post">
	Prześlij plik: <input type="file" name="file" size="20"/>
	Tytuł: <input type="text" name="title"  size="20"/>
	&ensp;Wykonawca: <input type="text" name="director"  size="20"/>
	&ensp;Tekst: <input type="text" name="subtitles"  size="20"/>
	&ensp;<label for="type">Gatunek:</label>
	<select id="type" name="type">
	<?php
		while($row = mysqli_fetch_array($type_sql)){
			?>
			<option value=<?=$row[0]?>><?=$row[1]?></option>
			<?php
		}?>
	</select>
	<input type="submit" value="Prześlij"/>
	</form>
</div>

<br><br>PLAYER:<br>

<a href="newplaylist.php">Stwórz nową playlistę</a>

<form action="portal.php" method="post">
<label for="playlist">Wybierz playlistę:</label>
<select id="playlist" name = "playlist">
<option value="default" hidden="hidden"></option>
<option value="default">default</option>
<?php
	while($row = mysqli_fetch_array($playlist_name_sql)){
		if($row[1] == $idu || $row[3] == 1){?>
			<option value=<?=$row[2]?>><?=$row[2]?></option>
		<?php	
		}
	}
?>
</select>
<input type="submit" value="Wybierz"/>
</form>

<?php
//wyswietlanie utworow
if(isset($_POST['playlist']) && $_POST['playlist'] == 'default'){
	foreach($song_sql as $row){
		$idf = $row['idf'];
		$title = $row['title'];
		$idft = $row['idft'];
		$filename = $row['filename'];
		$filename = 'films/' . $filename;
		
		//gatunek danej piosenki
		$type_sql = mysqli_query($link, "SELECT * FROM filmtype WHERE idft='$idft'");
		while($row = mysqli_fetch_array($type_sql)){$mt = $row[1];}
		echo $title . " (" . $mt . ")&ensp;<video controls style='height:240px;width:320px'><source src='$filename' type='video/mp4'></video><br>";
		//dodanie do playlisty:
		?>
		<form action="videotoplaylist.php" method="post">
		<input type="hidden" name="idf" value=<?=$idf?> />
		<label for="addto">Dodaj do:</label>
		<select id="addto" name = "addto">
		<?php
			$playlist_name_sql = mysqli_query($link, "SELECT * FROM playlistname");
			while($row = mysqli_fetch_array($playlist_name_sql)){
				if($row[1] == $idu){?>
					<option value=<?=$row[0]?>><?=$row[2]?></option>
				<?php	
				}
			}
		?>
		</select>
		<input type="submit" value="Dodaj"/>
		</form>
		
		<?php
		
		echo "<br><br>";
	}
}
else if(isset($_POST['playlist'])){
	$current_playlist = $_POST['playlist'];
	echo "<br>" . $current_playlist . "<br><br>";
	
	//szukanie id current_playlist
	$idpl_sql = mysqli_query($link, "SELECT * FROM playlistname WHERE name='$current_playlist'");
	$idpl_sql_array = mysqli_fetch_array($idpl_sql);
	$idpl = $idpl_sql_array[0];
	
	//rekordy piosenek dla current_playlist
	$pldb_sql = mysqli_query($link, "SELECT * FROM playlistdatabase WHERE idpl='$idpl'");
	while($row = mysqli_fetch_array($pldb_sql)){
		$idf = $row[2];	
		$song_in_playlist_sql = mysqli_query($link, "SELECT * FROM film WHERE idf='$idf'");
		foreach($song_in_playlist_sql as $row){
			$idf = $row['idf'];
			$title = $row['title'];
			$idft = $row['idft'];
			$filename = $row['filename'];
			$filename = 'films/' . $filename;
			
			//gatunek danej piosenki
			$type_sql = mysqli_query($link, "SELECT * FROM filmtype WHERE idft='$idft'");
			while($row = mysqli_fetch_array($type_sql)){$mt = $row[1];}
			echo $title . " (" . $mt . ")&ensp;<video controls style='height:240px;width:320px'><source src='$filename' type='video/mp4'></video><br>";
			//dodanie do playlisty:
			?>
			<form action="videotoplaylist.php" method="post">
			<input type="hidden" name="idf" value=<?=$idf?> />
			<label for="addto">Dodaj do:</label>
			<select id="addto" name = "addto">
			<?php
				$playlist_name_sql = mysqli_query($link, "SELECT * FROM playlistname");
				while($row = mysqli_fetch_array($playlist_name_sql)){
					if($row[1] == $idu){?>
						<option value=<?=$row[0]?>><?=$row[2]?></option>
					<?php	
					}
				}
			?>
			</select>
			<input type="submit" value="Dodaj"/>
			</form>
			<?php
			
			echo "<br><br>";
		}
	}
}


mysqli_close($link);
?>

</body>
</html>