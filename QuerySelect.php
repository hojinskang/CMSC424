<?php
if (session_status() == PHP_SESSION_NONE) {
session_start();
}

require_once("support2.php");
require_once("simple_html_dom.php");

$title = "FIFAHistory Query Select";

$host = "localhost";
$user = "cmsc424";
$password = "ilovedb";
$database = "WorldCup";
$db = connectToDB($host, $user, $password, $database);

$body = "";

header('Content-Type: text/html; charset=ISO-8859-1');

$scriptName = $_SERVER["PHP_SELF"];

class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('test.db');
      }
   }
$dbLite = new MyDB();

$body .= <<<EOBODY
    <div align="center">
    <h1>FIFAHistory</h1>
    <img src="http://www.slate.com/content/dam/slate/articles/sports/sports_nut/2014/03/140306_SNUT_Brazuca-Promo.jpg.CROP.original-original.jpg" width="300"/>
    <br/>
    <h3><i>Select one of the queries below</i></h3>
    <br/>

    <ul class="tab">
      <li><a href="#" class="tablinks" onclick="openCity(event, 'Cup')">Cups</a></li>
      <li><a href="#" class="tablinks" onclick="openCity(event, 'Player')">Players</a></li>
      <li><a href="#" class="tablinks" onclick="openCity(event, 'Super')">Super Stars</a></li>
      <li><a href="#" class="tablinks" onclick="openCity(event, 'Team')">Teams</a></li>
      <li><a href="#" class="tablinks" onclick="openCity(event, 'Country')">Countrys Players</a></li>
      <li><a href="#" class="tablinks" onclick="openCity(event, 'Misc')">Misc</a></li>
    </ul>

    <form action=$scriptName method="post">

    <div id="Cup" class="tabcontent" >
      <h3>Cup Query</h3>
      <p>
      Get the country where the world cup was held in a specified year and participating countries with details of their performance.
      <br/><br/>
      Enter year: <input type="text" name="cupyear" maxlength="4" style="width: 55px;"/>
      </p>
      <input type="submit" name="cupRes" value="Submit"/>
      <input type="submit" name="reset" value="Reset"/>
    </div>

    <div id="Player" class="tabcontent">
      <h3>Player Query</h3>
      <p>
      Get all the players with specified (partial) name and the details of their performance for each world cup they participated in.
      <br/><br/>
      Enter name: <input type="text" name="playername" style="width: 200px;"/>
      </p>
      <input type="submit" name="playerRes" value="Submit"/>
      <input type="submit" name="reset" value="Reset"/>
    </div>

    <div id="Super" class="tabcontent">
      <h3>Super Stars Query</h3>
      <p>Want to see who the super stars (players having participated in more than one world cup) are and the number of goals they scored in each cup?</p>
      <input type="submit" name="superRes" value="Yes"/>
      <input type="submit" name="reset" value="No"/>
    </div>

    <div id="Team" class="tabcontent">
      <h3>Team Historical Query</h3>
      <p>
      Get the details of the performance of the specified country for the specificed world cup year.
      <br/><br/>
      Enter country: <input type="text" name="teamname" style="width: 200px;"/>
      <br/><br/>
      Enter cup year: <input type="text" name="teamyear" style="width: 55px;"/>
      </p>
      <input type="submit" name="teamRes" value="Submit"/>
      <input type="submit" name="reset" value="Reset"/>
    </div>

    <div id="Country" class="tabcontent">
      <h3>Countrys Players Query</h3>
      <p>
      Get the details of all the players that have played for specified country.
      <br/><br/>
      Enter country: <input type="text" name="countryname" style="width: 200px;"/>
      </p>
      <input type="submit" name="countryRes" value="Submit"/>
      <input type="submit" name="reset" value="Reset"/>
    </div>

    <div id="Misc" class="tabcontent">
      <h3>Misc Query</h3>
      <p><em>Yellow/Red Cards</em>
      <br/>
      <br/>
      Get the details of the players yellow/red card history.
      <br/><br/>
      Enter name: <input type="text" name="misc1" style="width: 200px;"/>
      </p>
      <input type="submit" name="miscRes1" value="Submit"/>
      <input type="submit" name="reset" value="Reset"/>
      <br/>
      <br/>
      <hr>
      <p><em>Own Goal</em>
      <br/><br/>
      Get the players of specified country that scored their own goal.
      <br/><br/>
      Enter country: <input type="text" name="misc2" style="width: 200px;"/>
      </p>
      <input type="submit" name="miscRes2" value="Submit"/>
      <input type="submit" name="reset" value="Reset"/>
      <br/>
      <br/>
      <hr>
      <p><em>Overtime Savior</em>
      <br/><br/>
      Get the players of specified country that scored during overtime and won the game.
      <br/><br/>
      Enter country: <input type="text" name="misc3" style="width: 200px;"/>
      </p>
      <input type="submit" name="miscRes3" value="Submit"/>
      <input type="submit" name="reset" value="Reset"/>
      <br/>
      <br/>
      <hr>
      <p><em>Consistent Scorers</em>
      <br/><br/>
      Get the players who score consistently.
      </p>
      <input type="submit" name="miscRes4" value="Show"/>
      <input type="submit" name="reset" value="Reset"/>
    </div>

EOBODY;

$body2 = "None.";

if (isset($_POST["cupRes"])) {
  ?>
  <style>
  #cup {
     display: inline-block;
  }
  #player, #super, #team, #country, #misc {
    display: none;
  }
  </style>
  <?php
  $cupyear = $_POST["cupyear"];

  $sqlQuery = sprintf("select year, country from cup where year=%s", $cupyear);
  $result = mysqli_query($db, $sqlQuery);
  $numberOfRows = 0;
  if ($result) {
    $numberOfRows = mysqli_num_rows($result);
    if ($numberOfRows == 0) {
      $body2 = "No entry exists for the specified year";
    } else {
      while ($recordArray = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $year = $recordArray['year'];
        $country = $recordArray['country'];

        $search_keyword = "world+cup+". $year;
        //var_dump($search_keyword);
        $newhtml = file_get_html("https://www.google.com/search?q=".$search_keyword."&tbm=isch");
        $result_image_source = $newhtml->find('img', 0)->src;

        $body2 = "<img src='$result_image_source' height='100'/><br/><br/><strong>Year:</strong> $year
        <br/>
        <strong>Country:</strong> $country<br/>";

        $_SESSION['videoname'] = "world cup ".$year;

        include 'video.php';
      }
    }
  }

  $participants = array();

  if ($numberOfRows > 0) {
  $body2 .= "<br/>
  <br/>
  Click each country to get details of their performance
  <table border ='1' style=\"width:600px;\">";
} else {
  $body2 = "No entry exists for the specified year";
}

  $sqlQuery = sprintf("
  SELECT
    country.NAME as name,
    country.image as image,
    country_cup.standing as standing,
    country_cup.stage as stage,
    country_cup.points as points,
    country_cup.win as win,
    country_cup.draw as draw,
    country_cup.loss as loss,
    country_cup.goals_scored as scored,
    country_cup.goals_against as against
  FROM
    country,
    country_cup
  WHERE
    country.id = country_cup.cid AND country_cup.cup_year = %s
  ORDER BY
    standing;", $cupyear);
  $result2 = mysqli_query($db, $sqlQuery);
  if ($result2) {
    $numberOfRows2 = mysqli_num_rows($result2);
    if ($numberOfRows2 == 0) {
      $body2 = "No entry exists for the specified year";
    } else {
      while ($recordArray = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
        $participant = $recordArray['name'];
        $image = $recordArray['image'];
        $standing = $recordArray['standing'];
        $stage = $recordArray['stage'];
        $points = $recordArray['points'];
        $win = $recordArray['win'];
        $draw = $recordArray['draw'];
        $loss = $recordArray['loss'];
        $scored = $recordArray['scored'];
        $against = $recordArray['against'];

        $participants[intval($standing)] = $participant;

        $body2 .= "<tr>
        <td><a href=\"javascript:toggle('$participant')\">$participant</a></td>
        </tr>
        <tr>
        <td id=\"$participant\" style=\"display:none\">
          <img src='$image'/>
          <br/><br/>
          <strong>Standing:</strong> $standing
          <br/>
          <strong>Stage:</strong> $stage
          <br/>
          <strong>Points:</strong> $points
          <br/>
          <strong>Win:</strong> $win
          <br/>
          <strong>Draw:</strong> $draw
          <br/>
          <strong>Loss:</strong> $loss
          <br/>
          <strong>Goals Scored:</strong> $scored
          <br/>
          <strong>Goals Against:</strong> $against
        </td>
        </tr>";
      }
      $body2 .= "</table>";
    }
  }

  unsetAll();
} elseif (isset($_POST["playerRes"])) {
  ?>
  <style>
  #player {
     display: inline-block;
  }
  #cup, #super, #team, #country, #misc {
    display: none;
  }
  </style>
  <?php
  $name = $_POST["playername"];

  $body2 = "Click each player to get details of their performance
  <table border='1' width='600'>";

  $sqlQuery = "
  SELECT
    name,
    id,
    dob_m,
    dob_d,
    dob_y,
    height_ft,
    height_in,
    position_1,
    position_2
  FROM
    player
  WHERE NAME LIKE '%$name%';";
  $result = mysqli_query($db, $sqlQuery);
  if ($result) {
    $numberOfRows = mysqli_num_rows($result);
    if ($numberOfRows == 0) {
      $body2 = "No entry exists for the specified player";
    } else {
      while ($recordArray = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $name = $recordArray['name'];
        $pid = $recordArray['id'];
        $dobM = $recordArray['dob_m'];
        $dobD = $recordArray['dob_d'];
        $dobY = $recordArray['dob_y'];
        $ft = $recordArray['height_ft'];
        $in = $recordArray['height_in'];
        $pos1 = $recordArray['position_1'];
        $pos2 = $recordArray['position_2'];

        $search_keyword = str_replace(' ','+',$name);
        $search_keyword .= "+world+cup";
        $newhtml = file_get_html("https://www.google.com/search?q=".$search_keyword."&tbm=isch");
        $result_image_source = $newhtml->find('img', 0)->src;
        //var_dump($result_image_source);
        //echo '<img src="'.$result_image_source.'">';

        $body2 .= "<tr>
        <td><a href=\"javascript:toggle('$name')\">$name</a></td>
        </tr>
        <tr>
        <td id=\"$name\" style=\"display:none\">
        <img src=\"$result_image_source\">
        <br/><br/>
        <strong>Date of Birth:</strong>";
        if ($dobY != 0 || $dobY < 2016) {
          $age = 2016 - $dobY;
          if ($dobM > 4 || ($dobM == 4 && $dobD > 27)) {
            $age = $age - 1;
          }
          $body2 .= " $dobM/$dobD/$dobY
          <br/>
          <strong>Age:</strong> $age";
        } else {
          $body2 .= " Unknown";
        }
        $body2 .= "<br/>
        <strong>Height:</strong>";
        if ($ft != 0) {
          $body2 .= " $ft' $in\"";
        } else {
          $body2 .= " Unknown";
        }
        $body2 .= "
        <br/>
        <strong>Position:</strong> $pos1";

        if ($pos2) {
          $body2 .= " and $pos2";
        }

        $penaltyArry = array();

        $sqlQuery2 = sprintf("
        SELECT
          player_cup.cup_year AS cupyear,
          SUM(game_stats.penalty) AS penalty
        FROM
          player_cup,
          player,
          game_stats
        WHERE
          player.NAME = '%s' AND player.id = player_cup.pid AND player_cup.cup_year = game_stats.cup_year AND player.id = game_stats.pid
        GROUP BY
          player_cup.cup_year;", $name);
        $result2 = mysqli_query($db, $sqlQuery2);
        if ($result2) {
          $numberOfRows2 = mysqli_num_rows($result2);
          if ($numberOfRows2 > 0) {
            while ($recordArray2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
              $cupyear = intval($recordArray2['cupyear']);
              $penalty = $recordArray2['penalty'];
              $penaltyArry[$cupyear] = $penalty;
            }
          }
        }

        $body2 .= "
        <br/><br/>
        <table border='1' width='100%'>
        <tr>
        <td><strong>Cup Year</strong></td>
        <td><strong>Country</strong></td>
        <td><strong>Flag</strong></td>
        <td><strong>Jersey #</strong></td>
        <td><strong>Started</strong></td>
        <td><strong>Captain</strong></td>
        <td><strong>Goal</strong></td>
        <td><strong>Penalty</strong></td>
        </tr>
        ";

        $sqlQuery3 = sprintf("
        SELECT
          country.image AS image,
          country.name AS countryname,
          player_cup.cup_year AS cupyear,
          player_cup.jersey AS jersey,
          player_cup.started AS started,
          player_cup.captain AS captain,
          player_cup.goals AS goals
        FROM
          player_cup,
          country,
          player,
          game_stats
        WHERE
          player.NAME = '%s' AND player.id = player_cup.pid AND player_cup.cid = country.id
        GROUP BY
          player_cup.cup_year;", $name);
        $result3 = mysqli_query($db, $sqlQuery3);
        if ($result3) {
          $numberOfRows3 = mysqli_num_rows($result3);
          if ($numberOfRows3 > 0) {
            while ($recordArray3 = mysqli_fetch_array($result3, MYSQLI_ASSOC)) {
              $image = $recordArray3['image'];
              $countryname = $recordArray3['countryname'];
              $cupyear = intval($recordArray3['cupyear']);
              $jersey = $recordArray3['jersey'];
              $started = $recordArray3['started'];
              $captain = $recordArray3['captain'];
              $goals = $recordArray3['goals'];

              if (array_key_exists($cupyear,$penaltyArry)) {
                $penalty = $penaltyArry[$cupyear];
              } else {
                $penalty = 0;
              }

              $body2 .= "
              <tr>
              <td>$cupyear</td>
              <td>$countryname</td>
              <td><img src='$image'/></td>
              <td>$jersey</td>
              <td>$started</td>
              <td>$captain</td>
              <td>$goals</td>
              <td>$penalty</td>
              </tr>
              ";
            }
          } else {
            $body2 .= "<tr><td colspan = '8'>No information available</td></tr>";
          }
        }

        $_SESSION['videoname'] = $name." ".$countryname;

        $body2 .= "</table><br/><br/>Click
        <a href='https://en.wikipedia.org/wiki/$name'>here</a> to visit his Wikipedia page";

        $body2 .= "</td></tr>";
      }
    }
    $body2 .= "</table>";
  }
  unsetAll();
} elseif (isset($_POST["superRes"])) {
  ?>
  <style>
  #super {
     display: inline-block;
  }
  #cup, #player, #team, #country, #misc {
    display: none;
  }
  </style>
  <?php
  $sql = "
  SELECT p_name,
       cups_played,
       Sum(goals_scored)  as goalSum
FROM   (SELECT player.name                AS p_name,
               game_stats.cup_year        AS cups_played,
               game_stats.stage           AS which_stage,
               Ifnull(game_stats.goal, 0) AS goals_scored
        FROM   (SELECT *
                FROM   (SELECT player.name                AS player_name,
                               game_stats.cup_year        AS year_of_cup,
                               Ifnull(game_stats.goal, 0) AS goals_scored,
                               Count(DISTINCT cup_year)   AS cups_played
                        FROM   player,
                               game_stats
                        WHERE  game_stats.cup_year
                               AND game_stats.pid = player.id
                        GROUP  BY player.name) AS T
                WHERE  cups_played > 1) AS T,
               player,
               game_stats
        WHERE  player_name = player.name
               AND player.id = game_stats.pid)
GROUP  BY p_name,
          cups_played;
  ";
  $ret = $dbLite->query($sql);

    $body2 = "<table border='1'>
    <tr>
    <td><strong>Player</strong></td>
    <td><strong>Cup Year</strong></td>
    <td><strong>Goals Scored</strong></td>
    </tr>";
    $flag = 0;
    $name = 1;
    $repeat = 1;
    $player = "default";
    while ($rows = $ret->fetchArray(SQLITE3_ASSOC)){
      $flag = 1;
      //var_dump($rows);
      if (strcmp($player, $rows["p_name"]) != 0) {
        $repeat = $name;
        $name = 1;
      } else {
        $name = $name + 1;
      }
      $player = $rows["p_name"];
      $cupyear = $rows["cups_played"];
      $scored = $rows["goalSum"];

      $body2 .= "
      <tr>";

      if ($name == 1) {
      $body2 .= "<td>$player</td>";
    } else {
      $body2 .= "<td style='border: 0px;'></td>";
    }

      $body2 .= "
      <td>$cupyear</td>
      <td>$scored</td>
      </tr>";
    }
  if($flag = 0) {
   $body2 = "No super stars";
  }
  unsetAll();
} elseif (isset($_POST["teamRes"])) {
  ?>
  <style>
  #team {
     display: inline-block;
  }
  #cup, #player, #super, #country, #misc {
    display: none;
  }
  </style>
  <?php
  $country_name = $_POST["teamname"];
  $cup_year = $_POST["teamyear"];

  $sqlQuery = "
  SELECT
    cc.standing AS standing,
    cc.stage AS stage,
    cc.win AS win,
    cc.loss AS loss,
    cc.draw AS draw,
    cc.goals_scored AS total_goals_scored,
    cc.goals_against AS total_goals_received,
    cc.points as points,
    c.image AS image,
    k.country as location
  FROM
    country_cup cc,
    country c,
    cup k
  WHERE
    c.name = '$country_name' AND c.id = cc.cid AND cc.cup_year = $cup_year AND k.year = cc.cup_year;";
  $result = mysqli_query($db, $sqlQuery);
  if ($result) {
    $numberOfRows = mysqli_num_rows($result);
    if ($numberOfRows == 0) {
      $body2 = "No entry exists for the specified country and cup year";
    } else {
      while ($recordArray = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $location = $recordArray['location'];

        $standing = $recordArray['standing'];
        $stage = $recordArray['stage'];

        $win = $recordArray['win'];
        $loss = $recordArray['loss'];
        $draw = $recordArray['draw'];
        $participation = $win + $loss + $draw;

        $points = $recordArray['points'];

        $scored = $recordArray['total_goals_scored'];
        $received = $recordArray['total_goals_received'];
        $img = $recordArray['image'];

        $search_keyword = str_replace(' ','+',$country_name);
        $search_keyword = $search_keyword ."+world+cup+". $cup_year;
        //var_dump($search_keyword);
        $newhtml = file_get_html("https://www.google.com/search?q=".$search_keyword."&tbm=isch");
        $result_image_source = $newhtml->find('img', 0)->src;

        $body2 = "<img src=\"$result_image_source\">
        <br/>
        <br/>
        <strong>Country:</strong> $country_name
        <br/>
        <strong>Cup Year:</strong> $cup_year (in $location)
        <br/>
        <br/>
        <strong>Standing:</strong> $standing
        <br/>
        <strong>Stage:</strong> $stage
        <br/>
        <strong>Points:</strong> $points
        <br/>
        <br/>
        <strong>Total Participation:</strong> $participation
        <br/>
        <em>Win:</em> $win
        <br/>
        <em>Loss:</em> $loss
        <br/>
        <em>Draw:</em> $draw
        <br/>
        <br/>
        <strong>Total Goals Scored:</strong> $scored
        <br/>
        <strong>Total Goals Received:</strong> $received
        <br/>
        <br/>
        <img src=\"$img\">";
      }
    }
  }
  unsetAll();
} elseif (isset($_POST["countryRes"])) {
  ?>
  <style>
  #country {
     display: inline-block;
  }
  #cup, #player, #super, #team, #misc {
    display: none;
  }
  </style>
  <?php
  $country = $_POST["countryname"];
  //var_dump($name);

  $body2 = "<strong>Country:</strong> $country<br/><br/>";

  $sql = "
  SELECT name, img,
       ( CASE
           WHEN num_goals_scored <= 0 THEN num_goals_scored
           ELSE num_goals_scored / 3
         END ) AS num_goals_scored,
       num_penalties_scored,
       primary_position,
       secondary_position
FROM  (SELECT player.NAME                        AS NAME,
              Sum(Ifnull (game_stats.goal, 0))   AS num_goals_scored,
              Sum(Ifnull(game_stats.penalty, 0)) AS num_penalties_scored,
              player.position_1                  AS primary_position,
              Ifnull(player.position_2, 'none')  AS secondary_position,
              country.image as img
       FROM   player,
              game_stats,
              country,
              player_cup
       WHERE  country.NAME LIKE '$country'
              AND player_cup.pid = player.id
              AND country.id = player_cup.cid
              AND player_cup.pid = player.id
              AND player.id = game_stats.pid
              AND ( game_stats.cid1 = country.id
                     OR game_stats.cid2 = country.id )
              AND ( game_stats.goal <> 0
                     OR game_stats.penalty <> 0 )
       GROUP  BY player.NAME) AS T;
  ";
    $ret = $dbLite->query($sql);
    $flag = 0;

      $body2 .= "<table border='1'>
      <tr>
      <td><strong>Name</strong></td>
      <td><strong>Image</strong></td>
      <td><strong>Goals Scored</strong></td>
      <td><strong>Penalties Scored</strong></td>
      <td><strong>Primary Position</strong></td>
      <td><strong>Secondary Position</strong></td>
      </tr>";

      while ($recordArray = $ret->fetchArray(SQLITE3_ASSOC)) {
        $flag = 1;
        $name = $recordArray['name'];
        $goals = $recordArray['num_goals_scored'];
        $penalties = $recordArray['num_penalties_scored'];
        $pos1 = $recordArray['primary_position'];
        $pos2 = $recordArray['secondary_position'];
        $img = $recordArray['img'];

        $search_keyword = str_replace(' ','+',$name);
        $search_keyword .= "+world+cup";
        //$search_keyword = $search_keyword ."+world+cup+". $cup_year;
        //var_dump($search_keyword);
        $newhtml = file_get_html("https://www.google.com/search?q=".$search_keyword."&tbm=isch");
        $result_image_source = $newhtml->find('img', 0)->src;

        if ($pos2 == "none") {
          $pos2 = "";
        }

        $body2 .= "<tr>
        <td>$name</td>
        <td><img src='$result_image_source'/></td>
        <td>$goals</td>
        <td>$penalties</td>
        <td>$pos1</td>
        <td>$pos2</td>
        </tr>";
      }
      $body2 .= "</table><br/>
      ** Negative number under \"Goals Scored\" represents <em>own goal **</em><br/><br/><img src='$img'/>";
  if ($flag == 0) {
    $body2 = "No entry exists for the specified country";
  }
  unsetAll();
} elseif (isset($_POST["miscRes1"])) {
  ?>
  <style>
  #misc {
     display: inline-block;
  }
  #cup, #player, #super, #country, #team {
    display: none;
  }
  </style>
  <?php
  $name = $_POST["misc1"];
  //var_dump($name);

  $body2 = "Click each player to get details of their performance
  <table border='1' width='600'>";

  $sqlQuery = "
  SELECT
    name as player
  FROM
    player_cup,
    player
  WHERE
    player.name like '%$name%' AND player.id = player_cup.pid AND (yellow > 0 OR red > 0)
  GROUP BY
    player.name;
  ";
  $result = mysqli_query($db, $sqlQuery);
  if ($result) {
    $numberOfRows = mysqli_num_rows($result);
    if ($numberOfRows == 0) {
      $body2 = "No entry exists for the specified player<br/>OR <em>$name</em> received no red/yellow cards";
    } else {
      while ($recordArray = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $name = $recordArray['player'];

        $search_keyword = str_replace(' ','+',$name);
        $search_keyword .= "+red+yellow+card";
        //var_dump($search_keyword);
        $newhtml = file_get_html("https://www.google.com/search?q=".$search_keyword."&tbm=isch");
        $result_image_source = $newhtml->find('img', 0)->src;

        $body2 .= "<tr>
        <td><a href=\"javascript:toggle('$name')\">$name</a></td>
        </tr>
        <tr>
        <td id=\"$name\" style=\"display:none\"><br/>
        <img src='$result_image_source'/>
        <br/><br/>";

        $sqlQuery2 = "
        SELECT
          name as player,
          player_cup.cup_year AS cupyear,
          yellow,
          red
        FROM
          player_cup,
          player
        WHERE
          player.name = '$name' AND player.id = player_cup.pid AND (yellow > 0 OR red > 0);
        ";
        $result2 = mysqli_query($db, $sqlQuery2);
        if ($result2) {
          $numberOfRows2 = mysqli_num_rows($result2);
          if ($numberOfRows2 > 0) {
            while ($recordArray2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
              $cupyear = $recordArray2['cupyear'];
              $yellow = $recordArray2['yellow'];
              $red = $recordArray2['red'];

              $body2 .= "
              <strong>Cup Year:</strong> $cupyear<br/>
              <strong>Yellow:</strong> $yellow<br/>
              <strong>Red:</strong> $red
              <br/><br/>";
            }
          }
        }

        $body2 .= "</td></tr>";
      }
      $body2 .= "</table>";
    }
  }
  unsetAll();
} elseif (isset($_POST["miscRes2"])) {
  ?>
  <style>
  #misc {
     display: inline-block;
  }
  #cup, #player, #super, #country, #team {
    display: none;
  }
  </style>
  <?php
  $countryname = $_POST["misc2"];
  //var_dump($name);

  $body2 = "<strong>Country:</strong> $countryname<br/><br/>";

  $sqlQuery = sprintf("
  SELECT player.NAME as name,
         country.image as image
  FROM   player,
         game_stats,
         country
  WHERE  player.id = game_stats.pid
         AND game_stats.goal < 0
         AND country.NAME LIKE '%s'
         AND country.id = game_stats.cid1;
  ",$countryname);
  $result = mysqli_query($db, $sqlQuery);
  if ($result) {
    $numberOfRows = mysqli_num_rows($result);
    if ($numberOfRows == 0) {
      $body2 = "No entry exists for the specified country";
    } else {
      $body2 .= "<strong>Players who scored an own goal</strong>
      <br/><br/>
      <table border='1'>";
      while ($recordArray = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $name = $recordArray['name'];
        $img = $recordArray['image'];

        $search_keyword = str_replace(' ','+',$name);
        $search_keyword .= "+world+cup+own+goal";
        //var_dump($search_keyword);
        $newhtml = file_get_html("https://www.google.com/search?q=".$search_keyword."&tbm=isch");
        $result_image_source = $newhtml->find('img', 0)->src;

        $body2 .= "<tr><td><img src='$result_image_source'/><br/><br/>$name</td></tr>";
      }
      $body2 .= "</table><br/><br/><img src='$img'/>";
    }
  }
  unsetAll();
} elseif (isset($_POST["miscRes3"])) {
  ?>
  <style>
  #misc {
     display: inline-block;
  }
  #cup, #player, #super, #country, #team {
    display: none;
  }
  </style>
  <?php
  $countryname = $_POST["misc3"];
  //var_dump($name);

  $sql = "
  WITH
  temp AS(
  SELECT DISTINCT
    a.NAME AS self,
    a.image as selfImg,
    b.NAME AS AGAINST,
    b.image as againstImg,
    player.NAME,
    game_stats.cup_year AS cupyear,
    game_stats.stage as stage,
    game_stats.time,
    SUM(game_stats.goal) AS goal_self
  FROM
    country a,
    player,
    game_stats,
    country_cup,
    player_cup,
    country b
  WHERE
    a.NAME LIKE '$countryname' AND a.id = country_cup.cid AND country_cup.win > 0 AND a.id = game_stats.cid1 AND player_cup.pid = player.id AND game_stats.pid = player.id AND game_stats.time LIKE '%+%' AND b.id = game_stats.cid2 AND player.id = player_cup.pid AND player_cup.cid = a.id
  GROUP BY AGAINST
  ),
  temp2 AS(
  SELECT
    game_stats.goal AS goal_other,
    a.NAME AS oppo,
    b.NAME AS home
  FROM
    temp,
    game_stats,
    country b,
    country a
  WHERE
    a.NAME = temp.against AND b.NAME = temp.self
  GROUP BY
    oppo
  )
  SELECT
    cupyear,
    stage,
    temp.NAME AS player,
    temp.time AS time,
    self AS country,
    AGAINST AS opponent,
    selfImg,
    againstImg
  FROM
    temp,
    temp2
  WHERE
    temp.goal_self > temp2.goal_other
  GROUP BY
    player, opponent
  ORDER BY
    player;
  ";
  $ret = $dbLite->query($sql);



    $body2 = "<strong>Country:</strong> $countryname<br/><br/><table border='1'>
    <tr>
    <td><strong>Player</strong></td>
    <td><strong>Winning Shot</strong></td>
    <td><strong>Cup Year</strong></td>
    <td><strong>Stage</strong></td>
    <td><strong>Opponent</strong></td>
    <td><strong>Time</strong></td>
    </tr>";
    $flag = 0;
    while ($rows = $ret->fetchArray(SQLITE3_ASSOC)){
      $flag = 1;
      //var_dump($rows);
      $cupyear = $rows["cupyear"];
      $stage = $rows["stage"];
      $opponent = $rows["opponent"];
      $player = $rows["player"];
      $time = $rows["time"];
      $self = $rows["selfImg"];
      $oppo = $rows["againstImg"];

      $search_keyword = str_replace(' ','+',$player);
      $search_keyword .= "+world+cup+overtime+win";
      //var_dump($search_keyword);
      $newhtml = file_get_html("https://www.google.com/search?q=".$search_keyword."&tbm=isch");
      $result_image_source = $newhtml->find('img', 0)->src;

      $body2 .= "
      <tr>
      <td>$player</td>
      <td><img src='$result_image_source'/></td>
      <td>$cupyear</td>
      <td>$stage</td>
      <td>$opponent<br/><br/><img src='$oppo'/></td>
      <td>$time</td>
      </tr>";
    }
    if ($flag != 0) {
    $body2 .= "</table><br/><img src='$self'/>";
  }
  if($flag == 0) {
   $body2 = "No entry exists for the specified country";
  }
  unsetAll();
} elseif (isset($_POST["miscRes4"])) {
  ?>
  <style>
  #misc {
     display: inline-block;
  }
  #cup, #player, #super, #country, #team {
    display: none;
  }
  </style>
  <?php
  //$year = $_POST["misc4"];
  //var_dump($name);

  $sql = "
  SELECT
    player.NAME AS player,
    country.NAME AS country,
    country.image as image,
    avg(player_cup.goal_avg) AS goalAvg
  FROM   country,
         player_cup,
         player
  WHERE  player_cup.cid = country.id
         AND player.id = player_cup.pid
  	   group by player.name
  ORDER  BY goalAvg DESC;
  ";
  $ret = $dbLite->query($sql);

    $body2 = "<table border='1'>
    <tr>
    <td><strong>Player</strong></td>
    <td><strong>Country</strong></td>
    <td><strong>Flag</strong></td>
    <td><strong>Goal Average</strong></td>
    </tr>";
    while ($rows = $ret->fetchArray(SQLITE3_ASSOC)){
      //var_dump($rows);
      $player = $rows["player"];
      $country = $rows["country"];
      $flag = $rows["image"];
      $avg = $rows["goalAvg"];

      if ($avg > 0) {

      $body2 .= "
      <tr>
      <td>$player</td>
      <td>$country</td>
      <td><img src='$flag'/></td>
      <td>$avg</td>
      </tr>";
      }
    }
    $body2 .= "</table>";
  unsetAll();
} elseif (isset($_POST["reset"])) {
  ?>
  <style>
  #cup, #player, #super, #team, #country, #misc {
    display: none;
  }
  </style>
  <?php
  $body2 = "None.";
  unsetAll();
}

function unsetAll() {
  unset($_POST['cupRes']);
  unset($_POST['playerRes']);
  unset($_POST['superRes']);
  unset($_POST['reset']);
}

//mysqli_close($db);

echo generatePage($body, $body2, $title);

?>
