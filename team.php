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
            <li><a href="player.php">Players</a></li>
            <li><a href="coach.php">Coaches</a></li>
            <li class="active"><a href="team.php">Teams</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <form method="post" action="addTeam.php">
                    <legend>Add New Team</legend>
                    <p>Team Name: <input type="text" name="Name"/></p>
                    <p>City Name: <input type="text" name="City"/></p>
                    <p>State Name: <input type="text" name="State"/></p>
                    <p>Region Name: <input type="text" name="Region"/></p>
                    <p>Year Founded: <input type="number" name="FoundedYear" min="1900" max="2016"/></p>
                    <p>Month Founded: <input type="number" name="FoundedMonth" min="1" max="12"/></p>
                    <p>Day Founded: <input type="number" name="FoundedDay" min="1" max="31"/></p>
                    <p><input type="submit" /></p>
                </form>
            </div>
        </div>
        <div class="col-sm-4">
            <h2>Teams</h2>
            <table class="table table-hover" border="1">
                <tr>
                    <th>Name</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Date Founded</th>
                    <th>View Team Info</th>
                    <th>Add Player History</th>
                    <th>Add Game Stats</th>
                    <th>Delete</th>
                </tr>
                <?php

                if(!($stmt = $mysqli->prepare("SELECT id,name,city,state,region_name,date_founded FROM team"))){
                    echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                }

                if(!$stmt->execute()){
                    echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                if(!$stmt->bind_result($team_id,$name,$city,$state,$region,$date_founded)){
                    echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                while($stmt->fetch()){
                    $name = "<td>" . $region . " " . $name . "</td>";
                    $c = "<td>" . $city . "</td>";
                    $s = "<td>" . $state . "</td>";
                    $found = "<td>" . $date_founded . "</td>";
                    $hiddenFieldId = "<input type=\"hidden\" name=\"TeamID\" value=\"" . $team_id . "\">";
                    $hiddenFieldFName = "<input type=\"hidden\" name=\"RegionName\" value=\"" . $region . "\">";
                    $hiddenFieldLName = "<input type=\"hidden\" name=\"TeamName\" value=\"" . $name . "\">";
                    $hiddenField = $hiddenFieldId . $hiddenFieldFName . $hiddenFieldLName;
                    $viewForm = "<form action=\"team.php\" method=\"get\"><input type=\"submit\" value=\"View\">" . $hiddenField . "</form>";
                    $view = "<td>" . $viewForm . "</td>";
                    $addForm =  "<form action=\"addPlayerInfo.php\" method=\"get\"><input type=\"submit\" value=\"Add Player\">" . $hiddenField . "</form>";
                    $add = "<td>" . $addForm . "</td>";
                    $addStatForm =  "<form action=\"addPlayerGameStat.php\" method=\"get\"><input type=\"submit\" value=\"Add Game Stat\">" . $hiddenField . "</form>";
                    $addStat = "<td>" . $addStatForm . "</td>";
                    $deleteForm = "<form action=\"deleteTeam.php\" method=\"post\"><input type=\"submit\" value=\"Delete\">" . $hiddenField . "</form>";
                    $delete = "<td>" . $deleteForm . "</td>";
                    echo "<tr>" . $name . $c . $s . $found . $view . $add . $addStat . $delete . "</tr>";
                }
                $stmt->close();
                ?>

            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            if(isset($_GET['TeamID'])){
                $teamID = $_GET['TeamID'];

                // Get Team Player Information
                $teamHasEntries = false;
                $teamName = "";
                $teamHistory = array();
                $sql = "SELECT p.first_name,p.last_name,t.name,t.region_name,pf.start_date,pf.end_date,pf.position FROM player p
                                            INNER JOIN played_for pf on p.id=pf.player_id
                                            INNER JOIN team t on pf.team_id=t.id
                                            WHERE t.id = ?";
                if(!($stmt = $mysqli->prepare($sql))){
                    echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                }
                if(!($stmt->bind_param("i",$teamID))){
                    echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
                }

                if(!$stmt->execute()){
                    echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                if(!$stmt->bind_result($p_first_name,$p_last_name,$team_name,$team_region,$start,$end,$position)){
                    echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                while($stmt->fetch()){
                    $teamHasEntries = true;
                    $teamName = $team_region . " " . $team_name;
                    $rowHTML = "<tr>";
                    $rowHTML .= "<td>" . $p_first_name . " " . $p_last_name . "</td>";
                    $rowHTML .= "<td>" . $start . "</td>";
                    $rowHTML .= "<td>" . $end . "</td>";
                    $rowHTML .= "<td>" . $position . "</td>";
                    $rowHTML .= "</tr>";
                    array_push($teamHistory, $rowHTML);
                }
                $stmt->close();

                if($teamName == ""){
                    $teamName = "No Team Name";
                }
                echo "<h2>" . $teamName . "</h2>";

                // Echo Player History
                $tableHTML = "<h3>Player History</h3>
            <table class=\"table table-hover\" border=\"1\">
                <tr>
                    <th>Player Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Position</th>
                </tr>";
                echo $tableHTML;
                if($teamHasEntries){
                    $historyLength = count($teamHistory);
                    for($x = 0 ; $x < $historyLength; $x++){
                        echo $teamHistory[$x];
                    }
                }else{
                    echo "<td>No Player History</td>";
                }
                echo "</table>";


                // Get Team Player Game Statistics
                $teamHasEntries = false;
                $teamGameStat = array();
                $sql = "SELECT p.first_name,p.last_name,gs.game_date,gs.passing_yards,gs.passing_tds,
                                gs.rushing_yards,gs.rushing_tds,gs.receiving_yards,gs.receiving_tds,
                                t.name,t.region_name FROM player p
                                            INNER JOIN game_statistics gs on p.id=gs.player_id
                                            INNER JOIN team t on gs.team_id=t.id
                                            WHERE t.id = ?";
                if(!($stmt = $mysqli->prepare($sql))){
                    echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                }
                if(!($stmt->bind_param("i",$teamID))){
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
                    $teamHasEntries = true;
                    $teamName = $team_region . " " . $team_name;
                    $rowHTML = "<tr>";
                    $rowHTML .= "<td>" . $p_first_name . " " . $p_last_name . "</td>";
                    $rowHTML .= "<td>" . $game_date . "</td>";
                    $rowHTML .= "<td>" . $passing_yards . "</td>";
                    $rowHTML .= "<td>" . $passing_tds . "</td>";
                    $rowHTML .= "<td>" . $rushing_yards . "</td>";
                    $rowHTML .= "<td>" . $rushing_tds . "</td>";
                    $rowHTML .= "<td>" . $receiving_yards . "</td>";
                    $rowHTML .= "<td>" . $receiving_tds . "</td>";
                    $rowHTML .= "</tr>";
                    array_push($teamGameStat, $rowHTML);
                }
                $stmt->close();

                if($teamName == ""){
                    $teamName = "No Team Name";
                }
                // Echo Player Game Stats
                $tableHTML = "<h3>Game Statistics</h3>
            <table class=\"table table-hover\" border=\"1\">
                <tr>
                    <th>Player Name</th>
                    <th>Game Date</th>
                    <th>Passing Yards</th>
                    <th>Passing TDs</th>
                    <th>Rushing Yards</th>
                    <th>Rushing TDs</th>
                    <th>Receiving Yards</th>
                    <th>Receiving TDs</th>
                </tr>";
                echo $tableHTML;
                if($teamHasEntries){
                    $gameStatLength = count($teamGameStat);
                    for($x = 0 ; $x < $gameStatLength; $x++){
                        echo $teamGameStat[$x];
                    }
                }else{
                    echo "<td>No Team Game Statistics</td>";
                }
                echo "</table>";
            }

            $teamHasEntries = false;
            $teamHistory = array();
            if(isset($_GET['TeamID'])){
                $teamID = $_GET['TeamID'];
                $sql = "SELECT c.first_name,c.last_name,t.name,t.region_name,cf.start_date,cf.end_date,cf.job_title FROM coach c
                                            INNER JOIN coached_for cf on c.id=cf.coach_id
                                            INNER JOIN team t on cf.team_id=t.id
                                            WHERE t.id = ?";
                if(!($stmt = $mysqli->prepare($sql))){
                    echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                }
                if(!($stmt->bind_param("i",$teamID))){
                    echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
                }

                if(!$stmt->execute()){
                    echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                if(!$stmt->bind_result($c_first_name,$c_last_name,$team_name,$team_region,$start,$end,$job)){
                    echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                while($stmt->fetch()){
                    $teamHasEntries = true;
                    $rowHTML = "<tr>";
                    $rowHTML .= "<td>" . $c_first_name . " " . $c_last_name . "</td>";
                    $rowHTML .= "<td>" . $start . "</td>";
                    $rowHTML .= "<td>" . $end . "</td>";
                    $rowHTML .= "<td>" . $job . "</td>";
                    $rowHTML .= "</tr>";
                    array_push($teamHistory, $rowHTML);
                }
                $stmt->close();

                if($teamName == ""){
                    $teamName = "No Coach Name";
                }

                $tableHTML = "<h3>Coach History</h3>
            <table class=\"table table-hover\" border=\"1\">
                <tr>
                    <th>Coach Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Job Title</th>
                </tr>";
                echo $tableHTML;
                if($teamHasEntries){
                    $historyLength = count($teamHistory);
                    for($x = 0 ; $x < $historyLength; $x++){
                        echo $teamHistory[$x];
                    }
                }else{
                    echo "<td>No Coaching History</td>";
                }
                echo "</table>";
            }
            ?>
        </div>
    </div>
</div>