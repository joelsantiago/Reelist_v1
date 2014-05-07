<?php
// Session start to enable session variables to exists
session_start();

// welcome() function that displays a message to the user as well as displaying a sitewide like button and the 'suggest app' button, allowing the user to inform their friends about the app 
function welcome() {
  echo '<div class="box-short">';
  echo '<h1 class="center-text h1-margin">Welcome to Reelist!</h1>';
  echo '<p align="justify">This site is primarily focused around enhancing the way you plan movie nights with your friends and family. Reelist gives you the ability to create unique lists of movies that you have either seen or plan on seeing.  Reelist also allows you to directly message your facebook friends, giving you the chance to quickly and efficiently schedule movie nights with the people you want to go with.</p>';
  echo '<form method="post" action="">';
  echo '</form>';	
  echo '</div>';
}

// displayGrid() function with '$selection' parameter received from the URL, this determines what grid and sorting method is to be displayed
function displayGrid($selection=1) {
	global $db;
	$movie = array();
	$date = date('m/d/Y');
	
	// Grid selection process
	
	// Standard randomized grid along with welcome() function to display the welcome message
	if ($selection == 1) {
	  echo welcome();
	  $sql = mysql_query('SELECT MovieID FROM Movies ORDER BY RAND()');
	}
	
	// Upcoming movie grid sorted in ascending fashion
	else if ($selection == 2)
	  $sql = mysql_query('SELECT MovieID FROM Movies WHERE ReleaseDate > "'.$date.'" ORDER BY ReleaseDate ASC');
	  
	// Now Playing movie grid sorted in descending fashion
	else if ($selection == 3) 
	  $sql = mysql_query('SELECT MovieID FROM Movies WHERE ReleaseDate <= "'.$date.'" ORDER BY ReleaseDate DESC');
	
	// I wanna see movie grid 
	else if ($selection == 4) 
		$sql = mysql_query('SELECT MovieID FROM Lists WHERE Flag = 1 AND UserID = '.$_SESSION['UID'].'');
	else if ($selection == 5)
	  $sql = mysql_query('SELECT MovieID FROM Lists WHERE Flag = 2 AND UserID = '.$_SESSION['UID'].' ORDER BY Date DESC');
	else {
	  echo welcome();	
	  $sql = mysql_query('SELECT MovieID FROM Movies ORDER BY RAND()');	  
	}
	
	while ($row = mysql_fetch_assoc($sql)) {
		$movie[] = $row['MovieID'];
	}
	
	foreach ($movie as $value) {
	  $sql = 'SELECT * FROM Movies WHERE MovieID = '.$value;
	  $result = $db->query($sql);
	  $row = $result->fetch();
	  extract($row);
	  $output[] = '<div class="large-2 columns bottom">';
	  $output[] = '<a href="http://joelsantiago.co/reelist/?MovieID='.$MovieID.'"><img src="'.$Thumbnail.'" class="img-box"></a>';
	  $output[] = '<form method="get" action="">';
	  
	  $output[] = '<button type="submit" class="btn btn-small btn-success half" name="wanna" value="'.$MovieID.'">Wanna!</button>';
	  $output[] = '<button type="submit" class="btn btn-small btn-primary half" name="seen" value="'.$MovieID.'">Seen!</button>';
	  /*
	  $Flag = mysql_result(mysql_query('SELECT Flag FROM Lists WHERE UserID = '.$_SESSION['UID'].' AND MovieID = '.$value),0);
	  if ($Flag == 0) {
		if ($ReleaseDate <= $date) {
		  $output[] = '<button type="submit" class="btn btn-small btn-success half" name="wanna" value="'.$MovieID.'">Wanna!</button>';
		  $output[] = '<button type="submit" class="btn btn-small btn-primary half" name="seen" value="'.$MovieID.'">Seen!</button>';
		} else if ($ReleaseDate > $date) {
			$output[] = '<button type="submit" class="btn btn-small btn-block btn-success" name="wanna" value="'.$MovieID.'">Wanna!</button>';
		}
	  }
	  else if (($Flag == 1) || ($Flag == 2)) {
		$output[] = '<button type="submit" class="btn btn-small btn-block btn-danger" name="delete" value="'.$MovieID.'">Delete it!</button>';
	  }
	  */
	  $output[] = '</form>';
	  $output[] = '</div>';
	}
	return join('',$output);
}

// List Update Methods
if (isset($_GET['wanna'])) {
	$sub_date = date('Y-m-d H:i:s');
	$sql = mysql_query("INSERT INTO Lists (Date,UserID,Flag,MovieID) VALUES ('".$sub_date."',".$_SESSION['UID'].",1,".$_GET['wanna'].")");
}
else if (isset($_GET['seen'])) {
	$sub_date = date('Y-m-d H:i:s');
	$sql = mysql_query("INSERT INTO Lists (Date,UserID,Flag,MovieID) VALUES ('".$sub_date."',".$_SESSION['UID'].",2,".$_GET['seen'].")");
}
else if (isset($_GET['delete'])) {
	$sql = mysql_query("DELETE FROM Lists WHERE UserID = ".$_SESSION['UID']." AND MovieID = ".$_GET['delete']);
}
else if (isset($_GET['justsawit'])) {
	$sql = mysql_query("UPDATE Lists SET Flag = 2 WHERE UserID = ".$_SESSION['UID']." AND MovieID = ".$_GET['justsawit']);
}

// Movie Information Page function
function movieData($mid) {
	$date = date('m/d/Y');
	$sql = mysql_query('SELECT * FROM Movies WHERE MovieID = '.$mid);
	$row = mysql_fetch_array($sql);
	extract(row);
	$output[] = '<div class="box">';
	$output[] = '<table width="95%">';
	$output[] = '<tr>';
	$output[] = '<td rowspan="15" width="500px" height="auto" valign="top" halign="center" colspan="3"><img src="'.$row['Poster'].'" class="img-box"></td>';
	$output[] = '<td rowspan="15" width="15"></td>';
	$output[] = '<td colspan="2"></td>';
	$output[] = '</tr>';
	$output[] = '<tr><td colspan="2"><h1>'.$row['Title'].'</h1></td></tr>';
	$output[] = '<tr><td colspan="2"><hr></td></tr>';
	$output[] = '<tr><td colspan="2">&nbsp;</td></tr>';
	$output[] = '<tr><td colspan="2"><strong>Rating: </strong>'.$row['Rating'].'</td></tr>';
	$output[] = '<tr><td colspan="2"><strong>Release Date: </strong>'.$row['ReleaseDate'].'</td></tr>';
	$output[] = '<tr><td colspan="2"><strong>Runtime: </strong>'.$row['Runtime'].'</td></tr>';	
	$output[] = '<tr><td colspan="2"><strong>Genre: </strong>'.$row['Genre'].'</td></tr>';
	$output[] = '<tr><td colspan="2">&nbsp;</td></tr>';
	$output[] = '<tr><td colspan="2"><strong>Current Rating: </strong>'.$row['UserRating'].'</td></tr>';
	$output[] = '<tr><td colspan="2">&nbsp;</td></tr>';
	$output[] = '<form method="get" action="">';
	$Flag = mysql_result(mysql_query('SELECT Flag FROM Lists WHERE UserID = '.$_SESSION['UID'].' AND MovieID = '.$mid),0);
	if ($Flag == 0) {
	  if ($row['ReleaseDate'] <= $date) {
		$output[] = '<tr><td colspan="2" align="center"><button type="submit" class="btn btn-small btn-success btn-block" name="wanna" value="'.$mid.'">Wanna!</button></td></tr>';
		$output[] = '<tr><td colspan="2" align="center"><button type="submit" class="btn btn-small btn-primary btn-block" name="seen" value="'.$mid.'">Seen it!</button></td></tr>';
		$output[] = '<tr><td colspan="2">&nbsp;</td></tr>';
	  } else if ($row['ReleaseDate'] > $date) {
		  $output[] = '<tr><td colspan="2" align="center"><button type="submit" class="btn btn-small btn-block btn-success" name="wanna" value="'.$mid.'">Wanna see it!</button></td></tr>';
		  $output[] = '<tr><td colspan="2">&nbsp;</td></tr>';
	  }
	}
	else if (($Flag == 1) || ($Flag == 2)) {
	  if ($Flag == 1) {
		$output[] = '<tr><td colspan="2" align="center"><button type="submit" class="btn btn-small btn-block btn-danger" name="delete" value="'.$mid.'">Remove It!</button></td></tr>';
		$output[] = '<tr><td colspan="2" align="center"><button type="submit" class="btn btn-small btn-block btn-primary" name="justsawit" value="'.$mid.'">Just Saw It!</button></td></tr>';
		$output[] = '<tr><td colspan="2">&nbsp;</td></tr>';
	  }
	  else if ($Flag == 2) {
		$output[] = '<tr><td colspan="2" align="center"><button type="submit" class="btn btn-small btn-block btn-danger" name="delete" value="'.$mid.'">Remove It!</button></td></tr>';
		$output[] = '<tr><td colspan="2">&nbsp;</td></tr>';
	  }  
	}
	$output[] = '</form>';
	$output[] = '<tr><td colspan="2">&nbsp;</td></tr>';
	$output[] = '<tr><td colspan="6">&nbsp;</td></tr>';
	$output[] = '<tr><td colspan="6">&nbsp;</td></tr>';
	$output[] = '<tr><td colspan="3"><strong>Director: </strong>'.$row['Director'].'</td>';
	$output[] = '<td colspan="3"><strong>Production Company: </strong>'.$row['ProductionCo'].'</td></tr>';
	$output[] = '<tr><td colspan="6">&nbsp;</td></tr>';	
	$output[] = '<tr><td colspan="6"><strong><u>Summary</u></strong></td></tr>';
	$output[] = '<tr><td colspan="6"><p>'.$row['Summary'].'</p></td></tr>';
	$output[] = '<tr><td colspan="6">&nbsp;</td></tr>';
	$output[] = '<tr><td colspan="6"><strong><u>Cast</u></strong></td></tr>';
	$output[] = '<tr><td colspan="3"><ul><p>';
	$i = 1;
	$cast = explode(',',$row['Cast']);
	$c = count($cast);
	foreach ($cast as $actor) {
		$output[] = '<li>'.$actor.'</li>';
		$output[] = '&nbsp;';
		if ($i == round($c/2)) {
			$output[] = '</p></ul></td>';
			$output[] = '<td colspan="3"><ul><p>';
		}
		$i += 1;
	}
	$output[] = '</p></ul></td></tr>';
	$output[] = '<tr><td colspan="6">&nbsp;</td></tr>';
	$output[] = '<tr><td colspan="6"><strong><u>Trailer</u></strong></td></tr>';
	$output[] = '<tr><td colspan="6" align="center">';
	if ($row['Trailer'] == '')
	  $output[] = 'There is currently no trailer for this movie';
	else
	  $output[] = '<video src="'.$row['Trailer'].'" type="video/mp4" controls></video>';
	$output[] = '</td></tr>';
	$output[] = '</table>';
	$output[] = '</div>';
	return join('', $output);
}

function navbar($selection) {
	
  if ($selection == 1) {	  
	$output[] = '<li class="active"><a href="/reelist/?Selection=1"><strong>Reelist</strong></a></li>';
	$output[] = '<li><a href="/reelist/?Selection=2">Upcoming</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=3">Now Playing</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=4">I Wanna See...</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=5">I\'ve Seen...</a></li>';
  }
  else if ($selection == 2) {
	$output[] = '<li><a href="/reelist/?Selection=1"><strong>Reelist</strong></a></li>';
	$output[] = '<li class="active"><a href="/reelist/?Selection=2">Upcoming</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=3">Now Playing</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=4">I Wanna See...</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=5">I\'ve Seen...</a></li>';
  }
  else if ($selection == 3) {
	$output[] = '<li><a href="/reelist/?Selection=1"><strong>Reelist</strong></a></li>';
	$output[] = '<li><a href="/reelist/?Selection=2">Upcoming</a></li>';
	$output[] = '<li class="active"><a href="/reelist/?Selection=3">Now Playing</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=4">I Wanna See...</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=5">I\'ve Seen...</a></li>';
  }
  else if ($selection == 4) {
	$output[] = '<li><a href="/reelist/?Selection=1"><strong>Reelist</strong></a></li>';
	$output[] = '<li><a href="/reelist/?Selection=2">Upcoming</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=3">Now Playing</a></li>';
	$output[] = '<li class="active"><a href="/reelist/?Selection=4">I Wanna See...</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=5">I\'ve Seen...</a></li>';
  }
  else if ($selection == 5) {
	$output[] = '<li><a href="/reelist/?Selection=1"><strong>Reelist</strong></a></li>';
	$output[] = '<li><a href="/reelist/?Selection=2">Upcoming</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=3">Now Playing</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=4">I Wanna See...</a></li>';
	$output[] = '<li class="active"><a href="/reelist/?Selection=5">I\'ve Seen...</a></li>';
  }
  else {
	$output[] = '<li class="active"><a href="/reelist/?Selection=1"><strong>Reelist</strong></a></li>';
	$output[] = '<li><a href="/reelist/?Selection=2">Upcoming</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=3">Now Playing</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=4">I Wanna See...</a></li>';
	$output[] = '<li><a href="/reelist/?Selection=5">I\'ve Seen...</a></li>';
  }
  return join('',$output);
}

?>
