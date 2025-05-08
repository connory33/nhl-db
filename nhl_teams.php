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
  <header>
      <div class="collapse bg-dark" id="navbarHeader">
        <div class="container">
          <div class="row">
            <div class="col-sm-8 col-md-7 py-4">
              <h4 class="text-white">About Me</h4>
              <p class="text-white">
                  My name is Connor Young and I'm from Palo Alto, CA. I have a BS in Mechanical Engineering and a minor in Business from Cornell University
                  and a Master's in Operations Research and Information Engineering from Cornell Tech. 
                  <br /><br />
                  My career goal is to bring innovative technologies to the world.
                  I thrive at the intersection of technical and non-technical thinking and enjoy using my analytical mindset to break down and solve problems
                  that are both quantitatively and qualitatively complex.
                  <br /><br />
                  I seek opportunities to understand and balance customer needs with engineering constraints to deliver
                  innovative and high-value products. I greatly enjoy fast-paced work environments with high levels of
                  collaboration, accountability, and room for impact and growth.
              </p>
            </div>
            <div class="col-sm-4 offset-md-1 py-4">
              <h4 class="text-white">Contact</h4>
              <ul class="list-unstyled">
                <li><a style="color: dodgerblue" class="footerContent" href="https://www.linkedin.com/in/connoryoung33/">LinkedIn</a></li>
                <li><a style="color: #b55850" class="footerContent" href="https://www.github.com/connory33">GitHub</a></li>
                <li><a href="#" class="text-white">connor@connoryoung.com</a></li>
              </ul>
              <h4 class="text-white">Site Navigation</h4>
              <ul class="list-unstyled">
                <li><a style="color: dodgerblue" class="footerContent" href="index_v3.html">Home</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="nhlIndex.html">NHL Database</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="nhlLinesProject.html">NHL Lines Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="nbaFantasyProjections.html">NBA Fantasy Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="maddenOptimizer.html">NFL Roster Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="seniorDesign.html">Sr. Design Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="autonomousRobot.html">Robot Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="thermistorCleaner.html">Thermistor Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="waterPump.html">Water Pump Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="planterBoxes.html">Planter Box Project</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="navbar navbar-dark bg-dark box-shadow">
        <div class="container d-flex justify-content-between">
          <a href="#" class="navbar-brand d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
            <!-- <a href="../resources/images/Wheel1.jpg" width="20" height="20" class="mr-2"></a> -->
            <strong>CY</strong>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </div>
  </header>
  <body>
    
    <div class="bg-dark text-white text-center">
        <p>Search again:</p>

        <div class='search-container'>
        <form id="nhl-search" method="GET" action="nhl_games.php">
                <select name="search_column" id="nhl-search-column">
                    <option value="season">Season</option>
                    <option value="gameDate">Game Date</option>
                    <option value="easternStartTime">Start Time</option>
                    <option value="gameType">Game Type</option>
                    <option value="team">Team</option>
                    <option value="homeTeamId">Home Team</option>
                    <option value="awayTeamId">Away Team</option>
                    <option value="player">Player Name</option>
                </select>
                <input  type="text" name="search_term" id="search-term" placeholder="Enter search term" required>
                <input  type="submit" value="Search" class="btn btn-primary">
        </form>
    </div>
        <br>

        <?php
        include('db_connection.php');

        ini_set('display_errors', 1); error_reporting(E_ALL);

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            # Set default values for search column and term
            $searchColumn = '';
            $searchTerm = '';
            $originalSearchTerm = '';

            # Set default values for sorting and ordering
            $sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'gameDate';  // Default to 'gameDate'
            $sortOrder = isset($_GET['order']) ? $_GET['order'] : 'desc';      // Default to 'desc'

            # Validate allowed sort columns
            $allowedSortColumns = [
                'gameDate' => 'gameDate',
                'home_team_name' => 'home_teams.fullName',
                'away_team_name' => 'away_teams.fullName'
            ];

            if (array_key_exists($sortColumn, $allowedSortColumns)) {
                $sortColumn = $allowedSortColumns[$sortColumn];
            } else {
                $sortColumn = 'gameDate'; // Default to 'gameDate' if the sort column is invalid
            }

            # SQL
            # base query
            $sql = "SELECT
                        nhl_games.*,
                        home_teams.fullName AS home_team_name,
                        home_teams.id AS home_team_id,
                        away_teams.fullName AS away_team_name,
                        away_teams.id AS away_team_id
                    FROM
                        nhl_games
                    JOIN nhl_teams AS home_teams
                        ON nhl_games.homeTeamId = home_teams.id
                    JOIN nhl_teams AS away_teams
                        ON nhl_games.awayTeamId = away_teams.id";
            # add where clause if search values are set (not empty)
            if (!empty($_GET['search_column']) && !empty($_GET['search_term'])) {

                $searchColumn = mysqli_real_escape_string($conn, $_GET['search_column']);
                $searchTerm = mysqli_real_escape_string($conn, $_GET['search_term']);
                $originalSearchTerm = $searchTerm;

                // Pagination setup
                $limit = 25; // Results per page
                $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                $offset = ($page - 1) * $limit;

                // Get total count (for Load More logic)
                $count_sql = "SELECT COUNT(*) as total FROM (" . preg_replace("/SELECT.+?FROM/", "SELECT 1 FROM", $sql, 1) . ") as count_table";
                $count_result = mysqli_query($conn, $count_sql);
                $total_rows = mysqli_fetch_assoc($count_result)['total'];

                $start = $offset + 1;
                $end = min($offset + $limit, $total_rows);
                $total_pages = ceil($total_rows / $limit);

                echo "<h5>Games found where " . $searchColumn . ' = ' . $searchTerm . "</h5>";

                # get lowercase version of search term to use in mapping numeric values
                $lowerTerm = strtolower($searchTerm);
                # map and assign new value
                $gameType_duration_map = [
                        'preseason' => 1, 'pre' => 1,
                        'regular season' => 2, 'reg' => 2,
                        'playoffs' => 3, 'postseason' => 3, 'post' => 3,
                        'reg' => 3, 'ot' => 4, 'so' => 5
                    ];
                    if (isset($termMap[$lowerTerm])) {
                        $searchTerm = $termMap[$termLower];
                    }

                // Convert date search term to DB format (YYYY-MM-DD)s
                if ($searchColumn == 'gameDate') { # assuming MM/DD/YY input - BUILD OUT TO MAKE ROBUST TO OTHER INPUTS
                    $year = substr($searchTerm, 6);
                    $month = substr($searchTerm, 0, 2);
                    $day = substr($searchTerm, 3, 2);
                    $searchTerm = $year."-".$month."-".$day;
                }


                // Convert search term to numeric ID values for different teams
                $teamMap = [
                    'anaheim' => 24, 'ducks' => 24, 'anaheim ducks' => 24, 'ana' => 24,
                    'arizona' => 53, 'coyotes' => 53, 'arizona coyotes' => 53, 'ari' => 53,
                    'boston' => 6, 'bruins' => 6, 'boston bruins' => 6, 'bos' => 6,
                    'buffalo' => 7, 'sabres' => 7, 'buffalo sabres' => 7, 'buf' => 7,
                    'calgary' => 20, 'flames' => 20, 'calgary flames' => 20, 'cgy' => 20,
                    'carolina' => 12, 'hurricanes' => 12, 'carolina hurricanes' => 12, 'car' => 12,
                    'chicago' => 16, 'blackhawks' => 16, 'chicago blackhawks' => 16, 'chi' => 16,
                    'colorado' => 21, 'avalanche' => 21, 'colorado avalanche' => 21, 'col' => 21,
                    'columbus' => 29, 'blue jackets' => 29, 'columbus blue jackets' => 29, 'cbj' => 29,
                    'dallas' => 25, 'stars' => 25, 'dallas stars' => 25, 'dal' => 25,
                    'detroit' => 17, 'red wings' => 17, 'detroit red wings' => 17, 'det' => 17,
                    'edmonton' => 22, 'oilers' => 22, 'edmonton oilers' => 22, 'edm' => 22,
                    'florida' => 13, 'panthers' => 13, 'florida panthers' => 13, 'fla' => 13,
                    'los angeles' => 26, 'kings' => 26, 'los angeles kings' => 26, 'lak' => 26,
                    'minnesota' => 30, 'wild' => 30, 'minnesota wild' => 30, 'min' => 30,
                    'montreal' => 8, 'canadiens' => 8, 'montreal canadiens' => 8, 'mon' => 8,
                    'nashville' => 18, 'predators' => 18, 'nashville predators' => 18, 'nas' => 18,
                    'new jersey' => 1, 'devils' => 1, 'new jersey devils' => 1, 'njd' => 1,
                    'islanders' => 2, 'new york islanders' => 2, 'nyi' => 2,
                    'rangers' => 2, 'new york rangers' => 3, 'nyr' => 3,
                    'ottawa' => 9, 'senators' => 9, 'ottawa senators' => 9, 'ott' => 9,
                    'philadelphia' => 4, 'flyers' => 4, 'philadelphia flyers' => 4, 'phi' => 4,
                    'pittsburgh' => 5, 'penguins' => 5, 'pittsburgh penguins' => 5, 'pit' => 5,
                    'san jose' => 28, 'sharks' => 28, 'san jose sharks' => 28, 'sjs' => 28,
                    'seattle' => 55, 'kraken' => 55, 'seattle kraken' => 55, 'sea' => 55,
                    'st. louis' => 19, 'blues' => 19, 'st. louis blues' => 19, 'stl' => 19,
                    'tampa bay' => 14, 'lightning' => 14, 'tampa bay lightning' => 14, 'tbl' => 14,
                    'toronto' => 10, 'maple leafs' => 10, 'toronto maple leafs' => 10, 'tor' => 10,
                    'vancouver' => 23, 'canucks' => 23, 'vancouver canucks' => 23, 'van' => 23,
                    'las vegas' => 5, 'vegas' => 5, 'golden knights' => 5, 'vegas golden knights' => 5, 'vgk' => 5,
                    'washington' => 15, 'capitals' => 15, 'washington capitals' => 15, 'wsh' => 15,
                    'winnipeg' => 52, 'jets' => 52, 'winnipeg jets' => 52, 'wpg' => 52
                ];
                $lowerTerm = strtolower($searchTerm);
                if (isset($teamMap[$lowerTerm])) {
                    $searchTerm = $teamMap[$lowerTerm];
                }

                // Add SQL WHERE clause based on search column and term
                // Team search (home or away)
                if ($searchColumn === "team") {
                    $sql .= " WHERE home_teams.id = '$searchTerm' OR away_teams.id = '$searchTerm'";
                } else {
                    $sql .= " WHERE $searchColumn LIKE '%$searchTerm%'";
                }
            }

                // Date range filter
                if (!empty($_GET['startDate']) && !empty($_GET['endDate'])) {
                    $startDate = $_GET['startDate'];
                    $endDate = $_GET['endDate'];
                    $sql .= (strpos($sql, 'WHERE') !== false ? " AND" : " WHERE") . " gameDate BETWEEN '$startDate' AND '$endDate'";
                }
                
                // Add order and limit clauses
                $sql .= " ORDER BY $sortColumn $sortOrder";
                $sql .= " LIMIT $limit OFFSET $offset";

                // Execute and check query
                $result = mysqli_query($conn, $sql) or die("Query failed: " . mysqli_error($conn));


                // Execute the query, check if successful and if results were found
                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    die("Query failed: " . mysqli_error($conn));
                }
                
                
                // if (mysqli_num_rows($result) > 0) {
                //     echo "<h6>" . mysqli_num_rows($result) . " results<br><br></h6>";
                // } else {
                //     print("No results found.<br><br>");
                // }

                // echo "<style>.test-table { border: 5px solid red; }</style>";
                ?>

                <!-- Display results in a table format -->
                <!-- <table style="width: 70%; margin: 0px auto; border: 1px solid #bcd6e7"> -->
                <div class="table-container">
                    <table id='games-players-summary-table'>
                        <thead>
                            <tr>
                                <th>Season</th>
                                <th>Game<br>#</th>
                                <!--
                                // echo "<th>Date<br><a style='color: white' href='?search_column=" . urlencode($searchColumn) . "&search_term=" . urlencode($searchTerm)
                                // . "&sort_by=gameDate&sort_order=asc'>△</a><a style='color: white' href='?search_column=" . urlencode($searchColumn)
                                // . "&search_term=" . urlencode($searchTerm)
                                // . "&sort_by=gameDate&sort_order=desc'>▽</a></th>";
                                //-->
                                <th>Date<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=gameDate&sort_order=asc'>△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=gameDate&sort_order=desc'>▽</a>
                                </span>
                                </th>
                                <th>Start Time<br>(EST)</th>
                                <th>Game<br>Type</th>
                                <?php
                                echo "<th>Home Team<br><a style='color: white' href='?search_column=" . urlencode($searchColumn) . "&search_term=" . urlencode($searchTerm)
                                . "&sort_by=home_team_name&sort_order=asc'>△</a><a style='color: white' href='?search_column=" . urlencode($searchColumn)
                                . "&search_term=" . urlencode($searchTerm)
                                . "&sort_by=home_team_name&sort_order=desc'>▽</a></th>";
                                ?>
                                <th>Home<br>Score</th>
                                <th>Away<br>Team</th>
                                <th>Away<br>Score</th>
                                <th>Game<br>ID</th>
                            </tr>
                        </thead>
                        <tbody>


                    <?php
                    while ($row = $result->fetch_assoc()){
                        echo "<tr>";
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

                        // # Period
                        // $period_num = $row['regPeriods'];
                        // if ($period_num == 3) {
                        //     $period_text = "Regulation";
                        // } elseif ($period_num == 4) {
                        //     $period_text = "OT";
                        // } elseif ($period_num == 5) {
                        //     $period_text = "SO";
                        // } else {
                        //     $period_text = $period_num;
                        // }
                        // echo "<td>".$period_text."</td>";

                        # Home Team
                        if ($row['homeScore']>$row['awayScore']) {
                            echo "<td style='font-weight: bold'><a href='team_details.php?team_id={$row['home_team_id']}'>" . htmlspecialchars($row['home_team_name']) . "</a></td>";
                            // echo "<td style='font-weight: bold'>".$row['home_team_name']."</td>";
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
                        echo "<td><a href='game_details.php?game_id=" . $row['id'] . "'>" . $row['id'] . "</a></td>";

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
                    
                        echo "</tbody>";
                    echo "</table>";
                echo "</div>";

                
                $total_pages = ceil($total_rows / $limit);

                ?>

                <?php if ($total_rows > 0): ?>
                    <br><div style="margin-bottom: 10px;">
                        Showing results <?= $start ?>–<?= $end ?> of <?= $total_rows ?> (Page <?= $page ?> of <?= $total_pages ?>)
                    </div>
                <?php endif; ?>

                <?php

                if ($page==1) {
                    $next_page = $page + 1;
                    $advance_page = http_build_query(array_merge($_GET, ['page' => $next_page]));
                    echo "<div><a class='btn btn-secondary' href='?" . $advance_page . "'>Next</a>
                        </div>";
                } else if ($page>1 and $page<$total_pages) {
                    $prev_page = $page - 1;
                    $next_page = $page + 1;
                    $prev_page = http_build_query(array_merge($_GET, ['page' => $prev_page]));
                    $advance_page = http_build_query(array_merge($_GET, ['page' => $next_page]));
                    echo "<div style='text-align:center; margin-top: 20px;'>
                        <a class='btn btn-secondary' href='?" . $prev_page . "' style='margin-right: 10px'>Previous</a>";
                    echo "<a class='btn btn-secondary' href='?" . $advance_page . "'>Next</a>
                        </div>";
                } else {
                    $prev_page = $page - 1;
                    echo "<div style='text-align:center; margin-top: 20px;'>
                        <a class='btn btn-secondary' href='?" . $prev_page . "'>Previous</a></div>";
                }      
                
                
        



            $conn->close();
        }
        ?>

    <br>
    <br>
    </div>

    <footer class="bg-body-tertiary">
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

    <!-- JS for search form, allowing player to access nhl_players.php and others to nhl_games.php -->
    <script>
        document.getElementById('nhl-search').addEventListener('submit', function (e) {
            const column = document.getElementById('nhl-search-column').value;
            if (column === 'player') {
            this.action = 'nhl_players.php';
            } else {
            this.action = 'nhl_games.php';
            }
        });
    </script>

  </body>
</html>