<?php
require_once('php/functions.php');
require_once('php/mysql.php');
require_once('php/global.php');
//session_start();
?>
<!doctype html>
<html>
<!-- InstanceBegin template="/Templates/template.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
    <!--<meta charset="UTF-8">
    <!-- InstanceBeginEditable name="doctitle" -->
    <title>Reelist</title>
    <!-- InstanceEndEditable -->
    <meta name="viewport" content="width=device-width">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <!-- InstanceBeginEditable name="head" -->
    <meta name="description" content="Movie Scheduling with a Social Twist!">
    <!-- InstanceEndEditable -->
</head>
<body>

  <div class="fixed filled">
    <div class="box-header">
      <div class="row">
        <div class="navbar">
          <div class="navbar-inner">
            <ul class="nav">
              <?php
			  	$selection = $_GET['Selection'];
				echo navbar($selection);
              ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- InstanceBeginEditable name="Content" -->

  <div class="cartbuffer">
    <div class="box-main">
      <div class="row">
        <?php
		// Depending on what 'Selection' parameter was passed in the URL, the displayGrid() displays the requested grid with the correct formatting based on what button in the navbar was pressed
		if (isset($_GET['Selection'])) {
		  // displayGrid() function call with the 'Selection' parameter passed as the argument
		  echo displayGrid($_GET['Selection']);
		}

		// If the parameter passed within the URL isn't 'Selection' but instead, 'MovieID' then the correct movie page is loaded up based on the movie selected by the user
		else if (isset($_GET['MovieID'])) {
		  // movieData() function call with the 'MovieID' parameter passed as the argument
		  echo movieData($_GET['MovieID']);
		}

		// If no parameter was passed in the URL, for example when a user enters in the base URL 'apps.facebook.com/reelist', this displays the standard welcome page which is similar if the 'Selection = 1' parameter was passed
		else
		  // displayGrid() function call with no parameter
		  echo displayGrid();
		?>
      </div>
    </div>
  </div>

  <!-- InstanceEndEditable -->

</body>
<!-- InstanceEnd -->
</html>
