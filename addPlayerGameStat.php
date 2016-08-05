<?php
//Turn on error reporting
ini_set('display_errors', 'On');
//Connects to the database
$mysqli = new mysqli("oniddb.cws.oregonstate.edu","fjerstam-db","C9VOP9KTCMKBpICU","fjerstam-db");
if(!$mysqli || $mysqli->connect_errno){
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
            <li><a href="team.php">Teams</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <form method="post" action="addPlayerGameStat.php">
                    <legend>Player Game Stats</legend>
                    <p>Player:
                        <select name="PlayerID" >
                            <?php
                            $sql = "SELECT id,first_name,last_name FROM player";
                            if(!($stmt = $mysqli->prepare($sql))){
                                echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                            }
                            if(!$stmt->execute()){
                                echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
                            }
                            if(!$stmt->bind_result($player_id,$first_name,$last_name)){
                                echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                            }

                            $hasPlayerOptions = false;
                            while($stmt->fetch()){
                                $hasPlayerOptions = true;
                                if(isset($_GET['PlayerID'])){
                                    if($_GET['PlayerID'] == $player_id){
                                        echo '<option selected="selected" value=" '. $player_id . ' "> ' . $first_name . " " .$last_name . '</option>\n';
                                    }else{
                                        echo '<option value=" '. $player_id . ' "> ' . $first_name . " " .$last_name . '</option>\n';
                                    }
                                }else{
                                    echo '<option value=" '. $player_id . ' "> ' . $first_name . " " .$last_name . '</option>\n';
                                }
                            }
                            $stmt->close();

                            if ($hasTeamOptions){
                                echo "</select><h1>No Players in Database! Add at least one first...</h1>";
                                return;
                            }
                            ?>
                        </select>
                    </p>
                    <p>Team:
                        <select name="TeamID">
                            <?php
                            $teams = array();
                            $sql = "SELECT id,name,region_name FROM team";
                            if(!($stmt = $mysqli->prepare($sql))){
                                echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                            }
                            if(!$stmt->execute()){
                                echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
                            }
                            if(!$stmt->bind_result($team_id,$team_name,$team_region)){
                                echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                            }

                            $hasTeamOptions = false;
                            while($stmt->fetch()){
                                $hasTeamOptions = true;
                                if(isset($_GET['TeamID'])){
                                    if($_GET['TeamID'] == $team_id){
                                        echo '<option selected="selected" value=" '. $team_id . ' "> ' . $team_region . " " .$team_name . '</option>\n';
                                    }else{
                                        echo '<option value=" '. $team_id . ' "> ' . $team_region . " " .$team_name . '</option>\n';
                                    }
                                }else{
                                    echo '<option value=" '. $team_id . ' "> ' . $team_region . " " .$team_name . '</option>\n';
                                }
                            }
                            $stmt->close();

                            if ($hasTeamOptions){
                                echo "</select><h1>No Teams in Database! Add at least one first...</h1>";
                                return;
                            }
                            ?>
                        </select>
                    </p>
                    <p>Game Date Year:<input type="number" name="GameYear" min="1900" value="2016"/></p>
                    <p>Game Date Month:<input type="number" name="GameMonth" min="1" max="12" value="1"/></p>
                    <p>Game Date Day:<input type="number" name="GameDay" min="1" max="31" value="1"/></p>
                    <p>Passing Yards:<input type="number" name="PassYard"/></p>
                    <p>Passing Touchdowns:<input type="number" name="PassTD"/></p>
                    <p>Rushing Yards:<input type="number" name="RushYard"/></p>
                    <p>Rushing Touchdowns:<input type="number" name="RushTD"/></p>
                    <p>Receiving Yards:<input type="number" name="RecYard"/></p>
                    <p>Receiving Touchdowns:<input type="number" name="RecTD"/></p>
                    <p><input type="submit"/></p>

                </form>
            </div>
        </div>
    </div>
    <?php
    if(isset($_POST['PlayerID']) && isset($_POST['TeamID'])){
        $sql="INSERT INTO game_statistics(player_id,team_id,game_date,passing_tds,passing_yards,rushing_tds,rushing_yards,receiving_tds,receiving_yards) VALUES (?,?,?,?,?,?,?,?,?)";
        if(!($stmt = $mysqli->prepare($sql))){
            echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
        }
        $GameDate = "";
        if($_POST['GameYear'] != ""){
            $GameDate = $_POST['GameYear'] . "-" . $_POST['GameMonth'] . "-" . $_POST['GameDay'] . " 00:00:00";
        }
        if(!($stmt->bind_param("iisssssss",$_POST['PlayerID'],$_POST['TeamID'],$GameDate,$_POST['PassTD'],$_POST['PassYard'],$_POST['RushTD'],$_POST['RushYard'],$_POST['RecTD'],$_POST['RecYard']))){
            echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
        }

        if(!$stmt->execute()){
            echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
        } else {
            echo "Successfully added to Player's Game Statistics table.<br/>";
        }
        $stmt->close();
    }

    ?>



</div>
</body>
</html>