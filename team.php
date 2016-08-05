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
                    $deleteForm = "<form action=\"deleteTeam.php\" method=\"post\"><input type=\"submit\" value=\"Delete\">" . $hiddenField . "</form>";
                    $delete = "<td>" . $deleteForm . "</td>";
                    echo "<tr>" . $name . $c . $s . $found . $delete . "</tr>";
                }
                $stmt->close();
                ?>

            </table>
        </div>
    </div>
</div>