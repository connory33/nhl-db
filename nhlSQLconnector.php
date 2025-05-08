<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Connor Young</title>

    <!-- Bootstrap core CSS -->
    <link href="../resources/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../resources/css/album.css" rel="stylesheet">

    <link href="/resources/css/default_v3.css" rel="stylesheet" type="text/css" />

  </head>
  <body>
    <div class="bg-dark text-white text-center">
        <p>Search again:</p>

        <form method="GET" action="nhlSQLconnector.php">
            <select name="search_column">
                <option value="season">Season</option>
                <option value="gameDate">Game Date</option>
                <option value="easternStartTime">Start Time</option>
                <option value="gameType">Game Type</option>
                <option value="team">Team</option>
                <option value="homeTeamId">Home Team</option>
                <option value="awayTeamId">Away Team</option>
            </select>
            <input  type="text" name="search_term" placeholder="Enter search term" required>
            <input  type="submit" value="Search">
        </form>

        <?php
        include('db_connection.php');

        ini_set('display_errors', 1); error_reporting(E_ALL);

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            // $searchTerm = $_POST['search_term'];
            $searchColumn = mysqli_real_escape_string($conn, $_GET['search_column']);
            $searchTerm = mysqli_real_escape_string($conn, $_GET['search_term']);
            $originalSearchTerm = $searchTerm;

            // Convert search term to numeric value if for game type (pre = 1, reg = 2, play = 3)
            if (strtolower($searchTerm) == 'preseason' OR strtolower($searchTerm) == 'pre') {
                $searchTerm = 1;
            } elseif (strtolower($searchTerm) == 'regular season' or strtolower($searchTerm) == 'reg') {
                $searchTerm = 2;
            } elseif (strtolower($searchTerm) == 'playoffs' or strtolower($searchTerm) == 'postseason'
                or strtolower($searchTerm) == 'post') {
                $searchTerm = 3;
            }

            // Convert search term to numeric value if for period (reg = 3, ot = 4, so = 5)
            if (strtolower($searchTerm) == 'reg') {
                $searchTerm = 3;
            } elseif (strtolower($searchTerm) == 'ot') {
                $searchTerm = 4;
            } elseif (strtolower($searchTerm) == 'so') {
                $searchTerm = 5;
            }

            // Convert date search term to DB format (YYYY-MM-DD)s
            if ($searchColumn == 'gameDate') { # assuming MM/DD/YY input - BUILD OUT TO MAKE ROBUST TO OTHER INPUTS
                $year = substr($searchTerm, 6);
                $month = substr($searchTerm, 0, 2);
                $day = substr($searchTerm, 3, 2);
                $searchTerm = $year."-".$month."-".$day;
            }

            // Convert search term to numeric ID values for different teams
            if (strtolower($searchTerm) == 'anaheim ducks' or strtolower($searchTerm) == 'anaheim'
                or strtolower($searchTerm) == 'ducks' or strtolower($searchTerm) == 'ana') {
                $searchTerm = 24;
            } elseif (strtolower($searchTerm) == 'arizona coyotes' or strtolower($searchTerm) == 'arizona'
                or strtolower($searchTerm) == 'coyotes' or strtolower($searchTerm) == 'ari') {
                $searchTerm = 53;
            } elseif (strtolower($searchTerm) == 'boston bruins' or strtolower($searchTerm) == 'boston'
                or strtolower($searchTerm) == 'bruins' or strtolower($searchTerm) == 'bos') {
                $searchTerm = 6;
            } elseif (strtolower($searchTerm) == 'buffalo sabres' or strtolower($searchTerm) == 'buffalo'
                or strtolower($searchTerm) == 'sabres' or strtolower($searchTerm) == 'buf') {
                $searchTerm = 7;
            } elseif (strtolower($searchTerm) == 'calgary flames' or strtolower($searchTerm) == 'calgary'
                or strtolower($searchTerm) == 'flames' or strtolower($searchTerm) == 'cgy') {
                $searchTerm = 20;
            } elseif (strtolower($searchTerm) == 'carolina hurricanes' or strtolower($searchTerm) == 'carolina'
                or strtolower($searchTerm) == 'hurricanes' or strtolower($searchTerm) == 'car') {
                $searchTerm = 12;
            } elseif (strtolower($searchTerm) == 'chicago blackhawks' or strtolower($searchTerm) == 'chicago'
                or strtolower($searchTerm) == 'blackhawks' or strtolower($searchTerm) == 'chi') {
                $searchTerm = 16;
            } elseif (strtolower($searchTerm) == 'colorado avalanche' or strtolower($searchTerm) == 'colorado'
                or strtolower($searchTerm) == 'avalanche' or strtolower($searchTerm) == 'col') {
                $searchTerm = 21;
            } elseif (strtolower($searchTerm) == 'columbus blue jackets' or strtolower($searchTerm) == 'columbus'
                or strtolower($searchTerm) == 'blue jackets' or strtolower($searchTerm) == 'cbj') {
                $searchTerm = 29;
            } elseif (strtolower($searchTerm) == 'dallas stars' or strtolower($searchTerm) == 'dallas'
                or strtolower($searchTerm) == 'stars' or strtolower($searchTerm) == 'dal') {
                $searchTerm = 25;  
            } elseif (strtolower($searchTerm) == 'detroit red wings' or strtolower($searchTerm) == 'detroit'
                or strtolower($searchTerm) == 'red wings' or strtolower($searchTerm) == 'det') {
                $searchTerm = 17;
            } elseif (strtolower($searchTerm) == 'edmonton oilers' or strtolower($searchTerm) == 'edmonton'
                or strtolower($searchTerm) == 'oilers' or strtolower($searchTerm) == 'edm') {
                $searchTerm = 22;
            } elseif (strtolower($searchTerm) == 'florida panthers' or strtolower($searchTerm) == 'florida'
                or strtolower($searchTerm) == 'panthers' or strtolower($searchTerm) == 'fla') {
                $searchTerm = 13;
            } elseif (strtolower($searchTerm) == 'los angeles kings' or strtolower($searchTerm) == 'los angeles'
                or strtolower($searchTerm) == 'kings' or strtolower($searchTerm) == 'lak') {
                $searchTerm = 26;
            } elseif (strtolower($searchTerm) == 'minnesota wild' or strtolower($searchTerm) == 'minnesota'
                or strtolower($searchTerm) == 'wild' or strtolower($searchTerm) == 'min') {
                $searchTerm = 30;
            } elseif (strtolower($searchTerm) == 'montreal canadiens' or strtolower($searchTerm) == 'montreal'
                or strtolower($searchTerm) == 'canadiens' or strtolower($searchTerm) == 'mon') {
                $searchTerm = 8;
            } elseif (strtolower($searchTerm) == 'nashville predators' or strtolower($searchTerm) == 'nashville'
                or strtolower($searchTerm) == 'predators' or strtolower($searchTerm) == 'nas') {
                $searchTerm = 18;
            } elseif (strtolower($searchTerm) == 'new jersey devils' or strtolower($searchTerm) == 'new jersey'
                or strtolower($searchTerm) == 'devils' or strtolower($searchTerm) == 'njd') {
                $searchTerm = 1;
            } elseif (strtolower($searchTerm) == 'new york islanders' or strtolower($searchTerm) == 'new york'
                or strtolower($searchTerm) == 'islanders' or strtolower($searchTerm) == 'nyi') {
                $searchTerm = 2;
            } elseif (strtolower($searchTerm) == 'new york rangers' or strtolower($searchTerm) == 'new york'
                or strtolower($searchTerm) == 'rangers' or strtolower($searchTerm) == 'nyr') {
                $searchTerm = 3;
            } elseif (strtolower($searchTerm) == 'ottawa senators' or strtolower($searchTerm) == 'ottawa'
                or strtolower($searchTerm) == 'senators' or strtolower($searchTerm) == 'ott') {
                $searchTerm = 9;
            } elseif (strtolower($searchTerm) == 'philadelphia flyers' or strtolower($searchTerm) == 'philadelphia'
                or strtolower($searchTerm) == 'flyers' or strtolower($searchTerm) == 'phi') {
                $searchTerm = 4;
            } elseif (strtolower($searchTerm) == 'pittsburgh penguins' or strtolower($searchTerm) == 'pittsburgh'
                or strtolower($searchTerm) == 'penguins' or strtolower($searchTerm) == 'pit') {
                $searchTerm = 5;
            } elseif (strtolower($searchTerm) == 'san jose sharks' or strtolower($searchTerm) == 'san jose'
                or strtolower($searchTerm) == 'sharks' or strtolower($searchTerm) == 'sjs') {
                $searchTerm = 28;
            } elseif (strtolower($searchTerm) == 'seattle kraken' or strtolower($searchTerm) == 'seattle'
                or strtolower($searchTerm) == 'kraken' or strtolower($searchTerm) == 'sea') {
                $searchTerm = 55;
            } elseif (strtolower($searchTerm) == 'st. louis blues' or strtolower($searchTerm) == 'st. louis'
                or strtolower($searchTerm) == 'blues' or strtolower($searchTerm) == 'stl') {
                $searchTerm = 19;
            } elseif (strtolower($searchTerm) == 'tampa bay lightning' or strtolower($searchTerm) == 'tampa bay'
                or strtolower($searchTerm) == 'lightning' or strtolower($searchTerm) == 'tbl') {
                $searchTerm = 14;
            } elseif (strtolower($searchTerm) == 'toronto maple leafs' or strtolower($searchTerm) == 'toronto'
                or strtolower($searchTerm) == 'maple leafs' or strtolower($searchTerm) == 'tor') {
                $searchTerm = 10;
            } elseif (strtolower($searchTerm) == 'vancouver canucks' or strtolower($searchTerm) == 'vancouver'
                or strtolower($searchTerm) == 'canucks' or strtolower($searchTerm) == 'van') {
                $searchTerm = 23;
            } elseif (strtolower($searchTerm) == 'vegas golden knights' or strtolower($searchTerm) == 'vegas'
                or strtolower($searchTerm) == 'golden knights' or strtolower($searchTerm) == 'vgk') {
                $searchTerm = 5;
            } elseif (strtolower($searchTerm) == 'washington capitals' or strtolower($searchTerm) == 'washington'
                or strtolower($searchTerm) == 'capitals' or strtolower($searchTerm) == 'wsh') {
                $searchTerm = 15;
            } elseif (strtolower($searchTerm) == 'winnipeg jets' or strtolower($searchTerm) == 'winnipeg'
                or strtolower($searchTerm) == 'jets' or strtolower($searchTerm) == 'wpg') {
                $searchTerm = 52;
            }

            ///////////////////////////////// SQL Queries //////////////////////////////////

            // Query for searching by team name and getting all game results, both home and away
            if ($searchColumn == "team") {
                $sql = "SELECT
                        nhl_games.*,
                        home_teams.fullName AS home_team_name,
                        away_teams.fullName AS away_team_name
                    FROM
                        nhl_games
                    JOIN nhl_teams AS home_teams
                        ON nhl_games.homeTeamId = home_teams.id
                    JOIN nhl_teams AS away_teams
                        ON nhl_games.awayTeamId = away_teams.id
                    WHERE 
                        home_teams.id = '$searchTerm' 
                        OR away_teams.id = '$searchTerm'";
            
            // Basic query for all cases besides searching for both home/away by team name
            } else {
            $sql = "SELECT
                        nhl_games.*,
                        home_teams.fullName AS home_team_name,
                        away_teams.fullName AS away_team_name
                    FROM
                        nhl_games
                    JOIN nhl_teams AS home_teams
                        ON nhl_games.homeTeamId = home_teams.id
                    JOIN nhl_teams AS away_teams
                        ON nhl_games.awayTeamId = away_teams.id
                    WHERE $searchColumn LIKE '%$searchTerm%'";
            }

            // TROUBLESHOOTING
            // echo($sql);
            echo "<br>";

            // Execute the query, check if successful and if results were found
            $result = mysqli_query($conn, $sql);

            if (!$result) {
                die("Query failed: " . mysqli_error($conn));
            }
            
            echo "<td>Previous search: </td>" . $originalSearchTerm . "<br>";

            if (mysqli_num_rows($result) > 0) {
                print("<br> Results found: " . mysqli_num_rows($result) . "<br><br>");
            } else {
                print("No results found.<br><br>");
            }

            ?>

            <!-- Display results in a table format -->
            <table style="width: 70%; margin: 0px auto; border: 1px solid #bcd6e7">
            <tr style="color: white; font-weight: bold; background-color: #2e5b78; border: 1px solid #bcd6e7">
                <td>Season</td>
                <td>Game #</td>
                <td>Date</td>
                <td>Start Time (EST)</td>
                <td>Game Type</td>
                <td>Duration</td>
                <td>Home Name</td>
                <td>Home Score</td>
                <td>Visitor Name</td>
                <td>Visitor Score</td>
                <td>Game ID</td>
            </tr>

            <?php
            while ($row = $result->fetch_assoc()){
                echo "<tr style='border: 1px solid rgba(188, 214, 231, 0.3)'>";
                // echo "<td>".$row['id']."</td>";
                
                # Season
                $formatted_season_1 = substr($row['season'], 0, 4);
                $formatted_season_2 = substr($row['season'], 4);
                echo "<td>".htmlspecialchars($formatted_season_1)."-".htmlspecialchars($formatted_season_2)."</td>";
                
                # Game Number
                echo "<td>".$row['gameNumber']."</td>";

                # Date
                $gameDate = $row['gameDate'];
                $gameDatetime = new DateTime($gameDate);
                $formatted_gameDate = $gameDatetime->format('m/d/Y');
                echo "<td>".htmlspecialchars($formatted_gameDate)."</td>";

                # Time
                $formatted_startTime = substr($row['easternStartTime'], 11, -3);
                echo "<td>".htmlspecialchars($formatted_startTime)."</td>";

                # Game Type (i.e. Preseason, Regular Season, etc.)
                $gameType_num = $row['gameType'];
                if ($gameType_num == 1) {
                    $gameType_text = "Preseason";
                } elseif ($gameType_num == 2) {
                    $gameType_text = "Reg. Season";
                } elseif ($gameType_num == 3) {
                    $gameType_text = "Playoffs";
                } else {
                    $gameType_text = "Unknown";
                }
                echo "<td>".$gameType_text."</td>";

                # Period
                $period_num = $row['regPeriods'];
                if ($period_num == 3) {
                    $period_text = "Regulation";
                } elseif ($period_num == 4) {
                    $period_text = "OT";
                } elseif ($period_num == 5) {
                    $period_text = "SO";
                } else {
                    $period_text = $period_num;
                }
                echo "<td>".$period_text."</td>";

                # Home Team
                if ($row['homeScore']>$row['awayScore']) {
                    echo "<td style='font-weight: bold'>".$row['home_team_name']."</td>";
                    echo "<td style='font-weight: bold'>".$row['homeScore']."</td>";
                } else {
                    echo "<td>".$row['home_team_name']."</td>";
                    echo "<td>".$row['homeScore']."</td>";
                }
                
                # Away Team
                if ($row['homeScore']<$row['awayScore']) {
                    echo "<td style='font-weight: bold'>".$row['away_team_name']."</td>";
                    echo "<td style='font-weight: bold'>".$row['awayScore']."</td>";
                } else {
                    echo "<td>".$row['away_team_name']."</td>";
                    echo "<td>".$row['awayScore']."</td>";
                }

                # Game ID
                echo "<td><a href='game_details.php?game_id=" . $row['id'] . "'>View details</a></td>";

                echo "</tr>";
            }
                //// Extraneous ////
                // echo "<td style='font-weight: bold'>Game ID</td>";
                // echo "<td style='font-weight: bold'>gameScheduleStateId</td>";
                // echo "<td style='font-weight: bold'>gameStateId</td>";
                // echo "<td style='font-weight: bold'>Home Team Tricode</td>";
                // echo "<td style='font-weight: bold'>Visiting Team Tricode</td>";    
                // echo "<td>".$row['gameScheduleStateId']."</td>";

                # Game State (i.e. Final, In Progress, etc.)
                // echo "<td>".$row['gameStateId']."</td>";

            echo "</table>";


            $conn->close();
        }
        ?>

    <br>
    <br>
    </div>

    <footer class="text-muted">
      <div class="container">
        <p class="float-right">
          <a href="#">Back to top</a>
        </p>
        <p>Copyright &copy; 2025 Connor Young</p>
      </div>
    </footer>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="../js/vendor/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/vendor/holder.min.js"></script>
  </body>
</html>