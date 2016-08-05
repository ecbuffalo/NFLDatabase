<?php
ini_set('display_errors', 'On');
//Connects to the database
$mysqli = new mysqli("oniddb.cws.oregonstate.edu","fjerstam-db","C9VOP9KTCMKBpICU","fjerstam-db");
if($mysqli->connect_errno){
    echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <title>NFL Database</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>


<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="index.php">NFL Database</a>
        </div>
        <ul class="nav navbar-nav">
            <li class="active"><a href="player.php">Players</a></li>
            <li><a href="coach.php">Coaches</a></li>
            <li><a href="team.php">Teams</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <form method="post" action="addPlayer.php">
                    <legend>Add New Player</legend>
                    <p>First Name: <input type="text" name="FirstName"/></p>
                    <p>Last Name: <input type="text" name="LastName"/></p>
                    <p>Birth Date Year: <input type="number" name="BirthDateYear" min="1900" max="2000" value="1990"/></p>
                    <p>Birth Date Month: <input type="number" name="BirthDateMonth" min="1" max="12" value="1"/></p>
                    <p>Birth Date Day: <input type="number" name="BirthDateDay" min="1" max="31" value="1"/></p>
                    <p>Alma Mater: <input type="text" name="AlmaMater"/></p>
                    <p><input type="submit" /></p>
                </form>
            </div>
        </div>
        <div class="col-sm-4">
            <h2>Players</h2>
            <table class="table table-hover" border="1">
                <tr>
                    <th>Name</th>
                    <th>Birthdate</th>
                    <th>Alma Mater</th>
                    <th>Delete</th>
                </tr>
                <?php

                if(!($stmt = $mysqli->prepare("SELECT id,first_name,last_name,birthdate,alma_mater FROM player"))){
                    echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                }

                if(!$stmt->execute()){
                    echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                if(!$stmt->bind_result($player_id,$first_name,$last_name,$birthdate,$alma_mater)){
                    echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                while($stmt->fetch()){
                    $name = "<td>" . $first_name . " " . $last_name . "</td>";
                    $bdate = new DateTime($birthdate);
                    $now = new DateTime();
                    $interval = $now->diff($bdate);
                    $birth =  "<td>" . $interval->y . "</td>";
                    $alma = "<td>" . $alma_mater . "</td>";
                    $hiddenFieldId = "<input type=\"hidden\" name=\"PlayerID\" value=\"" . $player_id . "\">";
                    $hiddenFieldFName = "<input type=\"hidden\" name=\"FirstName\" value=\"" . $first_name . "\">";
                    $hiddenFieldLName = "<input type=\"hidden\" name=\"LastName\" value=\"" . $last_name . "\">";
                    $hiddenField = $hiddenFieldId . $hiddenFieldFName . $hiddenFieldLName;
                    $deleteForm = "<form action=\"deletePlayer.php\" method=\"post\"><input type=\"submit\" value=\"Delete\">" . $hiddenField . "</form>";
                    $delete = "<td>" . $deleteForm . "</td>";
                    echo "<tr>" . $name . $birth . $alma . $delete . "</tr>";
                }
                $stmt->close();
                ?>

            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            if(isset($_GET['PlayerID'])){
                $coachID = $_GET['PlayerID'];

                // Player Team Information
                $playerHasEntries = false;
                $playerName = "";
                $playerHistory = array();
                $sql = "SELECT p.first_name,p.last_name,t.name,t.region_name,pf.start_date,pf.end_date,pf.position FROM player p
                                            INNER JOIN played_for pf on p.id=pf.player_id
                                            INNER JOIN team t on pf.team_id=t.id
                                            WHERE p.id = ?";
                if(!($stmt = $mysqli->prepare($sql))){
                    echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                }
                if(!($stmt->bind_param("i",$coachID))){
                    echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
                }

                if(!$stmt->execute()){
                    echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                if(!$stmt->bind_result($p_first_name,$p_last_name,$team_name,$team_region,$start,$end,$position)){
                    echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                while($stmt->fetch()){
                    $playerHasEntries = true;
                    $playerName = $p_first_name . " " . $p_last_name;
                    $rowHTML = "<tr>";
                    $rowHTML .= "<td>" . $team_region . " " . $team_name . "</td>";
                    $rowHTML .= "<td>" . $start . "</td>";
                    $rowHTML .= "<td>" . $end . "</td>";
                    $rowHTML .= "<td>" . $position . "</td>";
                    $rowHTML .= "</tr>";
                    array_push($playerHistory, $rowHTML);
                }
                $stmt->close();

                if($playerName == ""){
                    $playerName = "No Player Name";
                }
                $tableHTML = "<h2>" . $playerName . "</h2>
            <table class=\"table table-hover\" border=\"1\">
                <tr>
                    <th>Team Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Position</th>
                </tr>";
                echo $tableHTML;
                if($playerHasEntries){
                    $historyLength = count($playerHistory);
                    for($x = 0 ; $x < $historyLength; $x++){
                        echo $playerHistory[$x];
                    }
                }else{
                    echo "<td>No Player History</td>";
                }
                echo "</table>";


                // Player Game Statistics
                $playerHasEntries = false;
                $playerGameStat = array();
                $sql = "SELECT p.first_name,p.last_name,gs.game_date,gs.passing_yards,gs.passing_tds,
                                gs.rushing_yards,gs.rushing_tds,gs.receiving_yards,gs.receiving_tds,
                                t.name,t.region_name FROM player p
                                            INNER JOIN game_statistics gs on p.id=gs.player_id
                                            INNER JOIN team t on gs.team_id=t.id
                                            WHERE p.id = ?";
                if(!($stmt = $mysqli->prepare($sql))){
                    echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                }
                if(!($stmt->bind_param("i",$coachID))){
                    echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
                }

                if(!$stmt->execute()){
                    echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                if(!$stmt->bind_result($p_first_name,$p_last_name,$game_date,$passing_yards,$passing_tds,
                    $rushing_yards,$rushing_tds,$receiving_yards,$receiving_tds,$team_name,$team_region)){
                    echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                while($stmt->fetch()){
                    $playerHasEntries = true;
                    $playerName = $p_first_name . " " . $p_last_name;
                    $rowHTML = "<tr>";
                    $rowHTML .= "<td>" . $team_region . " " . $team_name . "</td>";
                    $rowHTML .= "<td>" . $game_date . "</td>";
                    $rowHTML .= "<td>" . $passing_yards . "</td>";
                    $rowHTML .= "<td>" . $passing_tds . "</td>";
                    $rowHTML .= "<td>" . $rushing_yards . "</td>";
                    $rowHTML .= "<td>" . $rushing_tds . "</td>";
                    $rowHTML .= "<td>" . $receiving_yards . "</td>";
                    $rowHTML .= "<td>" . $receiving_tds . "</td>";
                    $rowHTML .= "</tr>";
                    array_push($playerGameStat, $rowHTML);
                }
                $stmt->close();

                if($playerName == ""){
                    $playerName = "No Player Name";
                }
                $tableHTML = "<h2>" . $playerName . "</h2>
            <table class=\"table table-hover\" border=\"1\">
                <tr>
                    <th>Team Name</th>
                    <th>Game Date</th>
                    <th>Passing Yards</th>
                    <th>Passing TDs</th>
                    <th>Rushing Yards</th>
                    <th>Rushing TDs</th>
                    <th>Receiving Yards</th>
                    <th>Receiving TDs</th>
                </tr>";
                echo $tableHTML;
                if($playerHasEntries){
                    $gameStatLength = count($playerGameStat);
                    for($x = 0 ; $x < $gameStatLength; $x++){
                        echo $playerGameStat[$x];
                    }
                }else{
                    echo "<td>No Player Game Statistics</td>";
                }
                echo "</table>";
            }
            ?>
        </div>
    </div>
</div>