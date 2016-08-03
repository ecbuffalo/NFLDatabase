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
            <li class="active"><a href="coach.php">Coaches</a></li>
            <li><a href="team.php">Teams</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <form method="post" action="addcoach.php">
                    <legend>Add New Coach</legend>
                    <p>First Name: <input type="text" name="FirstName"/></p>
                    <p>Last Name: <input type="text" name="LastName"/></p>
                    <p>Division Championships Won: <input type="number" name="Division" min="0"/></p>
                    <p>Conference Championships Won: <input type="number" name="Conference" min="0"/></p>
                    <p>League Championships Won: <input type="number" name="League" min="0"/></p>
                    <p><input type="submit" /></p>
                </form>
            </div>
        </div>
        <div class="col-sm-4">
            <h2>Coaches</h2>
            <table>
                <tr>
                    <td>Name</td>
                    <td>Division Championships</td>
                    <td>Conference Championships</td>
                    <td>League Championships</td>
                    <td>Delete</td>
                </tr>
                <?php

                if(!($stmt = $mysqli->prepare("SELECT id,first_name,last_name,division_titles,conference_titles,championships FROM coach"))){
                    echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                }

                if(!$stmt->execute()){
                    echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                if(!$stmt->bind_result($coach_id,$first_name,$last_name,$division,$conference,$league)){
                    echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                while($stmt->fetch()){
                    $name = "<td>" . $first_name . " " . $last_name . "</td>";
                    $div = "<td>" . $division . "</td>";
                    $conf = "<td>" . $conference . "</td>";
                    $leag = "<td>" . $league . "</td>";
                    $hiddenFieldId = "<input type=\"hidden\" name=\"CoachID\" value=\"" . $coach_id . "\">";
                    $hiddenFieldFName = "<input type=\"hidden\" name=\"FirstName\" value=\"" . $first_name . "\">";
                    $hiddenFieldLName = "<input type=\"hidden\" name=\"LastName\" value=\"" . $last_name . "\">";
                    $hiddenField = $hiddenFieldId . $hiddenFieldFName . $hiddenFieldLName;
                    $deleteForm = "<form action=\"deleteCoach.php\" method=\"post\"><input type=\"submit\" value=\"Delete\">" . $hiddenField . "</form>";
                    $delete = "<td>" . $deleteForm . "</td>";
                    echo "<tr>" . $name . $div . $conf . $leag . $delete . "</tr>";
                }
                $stmt->close();
                ?>

            </table>
        </div>
    </div>
</div>