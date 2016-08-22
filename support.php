<?php

$salt = '$5$rounds=5000$usesomesillystringforsalt$';

function generatePage($body, $title="Application System") {
    $page = <<<EOPAGE
<!doctype html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>$title</title>
        <link rel="stylesheet" type="text/css" href="stylesheet.css">
        <script>
        function openCity(evt, cityName) {
          // Declare all variables
          var i, tabcontent, tablinks;

          // Get all elements with class="tabcontent" and hide them
          tabcontent = document.getElementsByClassName("tabcontent");
          for (i = 0; i < tabcontent.length; i++) {
              tabcontent[i].style.display = "none";
          }

          // Get all elements with class="tablinks" and remove the class "active"
          tablinks = document.getElementsByClassName("tablinks");
          for (i = 0; i < tabcontent.length; i++) {
              tablinks[i].className = tablinks[i].className.replace(" active", "");
          }

          // Show the current tab, and add an "active" class to the link that opened the tab
          document.getElementById(cityName).style.display = "block";
          evt.currentTarget.className += " active";
        }
        </script>
    </head>

    <body>
      <div>
            $body
      </div>
    </body>
</html>
EOPAGE;

    return $page;
}

function connectToDB($host, $user, $password, $database) {
  $db = mysqli_connect($host, $user, $password, $database);
  if (mysqli_connect_errno()) {
    echo "<h2>Connect failed.</h2>\n".mysqli_connect_error();
    exit();
  }
  return $db;
}

?>
