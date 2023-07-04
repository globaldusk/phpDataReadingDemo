<?php
    function readFromJSON($filename) { //turns file into json
        $jsonString = file_get_contents($filename);
        $data = json_decode($jsonString, true);
        return $data;
    }


    function getPlayerGames($playerID) { //returns formatted player games
        $games = readFromJSON('./snippet_data.json')['games'];

        $formattedGames = "";

        foreach ($games as $game){

            if ($game['winnerId'] == $playerID || $game['loserId'] == $playerID){
        
                $winner = getRank($game['winnerId']) ." ". findPlayerData($game['winnerId'], 'firstName') ." ". findPlayerData($game['winnerId'], 'lastName');
                $loser = getRank($game['loserId']) ." ". findPlayerData($game['loserId'], 'firstName') ." ". findPlayerData($game['loserId'], 'lastName');

                $formattedGame = "Tournament Match <br>";
                $formattedGame .= "Score: " . $game['winnerScore'] . " - " . $game['loserScore'] . "<br>";
                $formattedGame .= "Winner: " . $winner . "<br>";
                $formattedGame .= "Loser: " . $loser . "<br><br>";

                $formattedGames .= $formattedGame;
        
            } 
        
        }

        //return the players games data
        echo $formattedGames;
    }

    function findPlayerData($playerID, $data) { //get the data of one section from the player
        $players = readFromJSON('./snippet_data.json')['players'];

        foreach ($players as $player){
            if ($player['id'] == $playerID){
                return $player[$data];
            }
            
        }
    }

    function getRank($playerID) { //calculates the rank of the player and returns it. lets tied players have same rank, without changing the rank of lower players
        $players = readFromJSON('./snippet_data.json')['players'];

        $scores = array();

        foreach ($players as $player){//creates sorted list of avg score
            array_push($scores, ($player['wins'] - $player['losses']));
            sort($scores);
        }
        
        foreach ($players as $player){
            if ($player['id'] == $playerID){
                $score = $player['wins'] - $player['losses'];
                return "#" . (sizeof($scores) - array_search($score, $scores));//returns the rank reversed order from array cause its sorted smallest to largest
            }
        }

    }

    function buttonPressed(){
        //get the player id from the POST data
        $playerID = $_POST['playerID'];
        //call the getPlayerGames function with the player id
        getPlayerGames($playerID);
    }
?>

 
<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="container">
        <div class="heading-wrapper">
            <h1>Table Tennis Terminal</h1>
        </div>

        <div class="input">
            <form method="post">
                <input type="text" name="playerID" placeholder="Player ID">
                <button type="submit" name="getGames">Get Games</button>
            </form>
        </div>

        <div class="output">
            <div class="echo-chamber">
                <?php
                    if (isset($_POST['getGames'])) {
                        buttonPressed();
                    }
                ?>
            </div>
        </div>      

    </div>

  </body>
</html>
 