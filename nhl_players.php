<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Connor Young</title>

    <link href="/resources/css/default_v3.css" rel="stylesheet" type="text/css" />

    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body>

  <?php include 'header.php'; ?>


        <?php
        include('db_connection.php');

        ini_set('display_errors', 1); error_reporting(E_ALL);


        if (!empty($_GET['search_column']) && !empty($_GET['search_term'])) {
            // $searchTerm = $_POST['search_term'];
            $searchColumn = mysqli_real_escape_string($conn, $_GET['search_column']);
            $searchTerm = mysqli_real_escape_string($conn, $_GET['search_term']);
            $originalSearchTerm = $searchTerm;

            $sql = "SELECT nhl_players.*, nhl_teams.triCode as currentTeamAbbrev
                FROM
                    nhl_players
                LEFT JOIN nhl_teams on nhl_players.currentTeamID = nhl_teams.id
                WHERE 
                    firstName LIKE '%$searchTerm%' 
                    OR lastName LIKE '%$searchTerm%'";

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

            $sql .= " LIMIT $limit OFFSET $offset";

            // Execute the query, check if successful and if results were found
            $result = mysqli_query($conn, $sql);

            if (!$result) {
                die("Query failed: " . mysqli_error($conn));
            }
                        
            if (mysqli_num_rows($result) == 0) {
                print("No results found.<br><br>");
            }

            ?>

      <div id="nhl-games-players-summary-content-container" style='background-color: #343a40'>
            <br>
            <p class="text-lg text-center">Search again:</p>

            <div class="flex justify-center">
              <form id="nhl-search" method="GET" action="nhl_games.php" class="backdrop-blur-sm px-4 sm:px-6 py-4 rounded-lg flex flex-col sm:flex-row gap-4
                items-stretch sm:items-center w-full max-w-4xl">
                      <select name="search_column" id="nhl-search-column" class='w-full sm:w-auto flex-1 bg-white text-black text-sm rounded-md border border-gray-300
                      px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500'>
                          <option value="season">Season</option>
                          <option value="gameDate">Game Date</option>
                          <option value="easternStartTime">Start Time</option>
                          <option value="gameType">Game Type</option>
                          <option value="team">Team</option>
                          <option value="homeTeamId">Home Team</option>
                          <option value="awayTeamId">Away Team</option>
                          <option value="player">Player Name</option>
                      </select>
                      <input  type="text" name="search_term" id="search-term" placeholder="Enter search term" required class="w-full sm:flex-2 text-black px-3 py-2
                      rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                      <input type="submit" value="Search"
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-md transition-colors duration-200 cursor-pointer">
              </form>
            </div>

            <br><hr class='border border-white mx-auto w-4/5'><br>

            <h4 class='text-4xl text-center text-white'>Player Results</h4>
            <br>
            <?php echo "<h5 class='text-center>" . $total_rows . " results found where " . $searchColumn . " = '" . $originalSearchTerm . "'</h5>"; ?>
            <p>Click any player ID or name to view additional details.</p>
            <br>
            <!-- Display results in a table format -->
            <div class="table-container default-zebra-table max-w-[80%] mx-auto">
                <table id='games-players-summary-table' class='default-zebra-table mx-auto'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Height</th>
                            <th>Weight</th>
                            <th>Birthdate</th>
                            <th>Country</th>
                            <th>Shoots / Catches </th>
                            <th>Number</th>
                            <th>Team</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        while ($row = $result->fetch_assoc()){
                            echo "<tr>";
                                echo "<td><a href='player_details.php?player_id=" . $row['playerId'] . "'" . "</a>" . $row['playerId'] . "</td>";
                                echo "<td><a href='player_details.php?player_id=" . $row['playerId'] . "'" . "</a>" . $row['firstName'] . ' ' . $row['lastName'] . "</td>";
                                echo "<td>" . $row['heightInInches'] . " in/" . $row['heightInCentimeters'] . " cm</td>";
                                echo "<td>" . $row['weightInPounds'] . " lbs/" . $row['weightInKilograms'] . " kg</td>";
                                echo "<td>" . date('m/d/Y', strtotime($row['birthDate'])) . "</td>";
                                echo "<td>" . $row['birthCountry'] . "</td>";
                                echo "<td>" . $row['shootsCatches'] . "</td>";
                                
                                if ($row['sweaterNumber'] == '') {
                                    echo "<td>-</td>";
                                } else {
                                    echo "<td>" . $row['sweaterNumber'] . "</td>";
                                }
                                if ($row['currentTeamAbbrev'] == '') {
                                    echo "<td>-</td>";
                                } else {
                                    echo "<td>" . $row['currentTeamAbbrev'] . "</td>";
                                }
                        echo "</tr>";
                        }
                    echo "</tbody>";
                echo "</table>";
            echo "</div>";

            $total_pages = ceil($total_rows / $limit);

                ?>

                <?php if ($total_rows > 0): ?>
                    <br><div class='mt-4'>
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
                    echo "<div class='text-center mt-6'>
                        <a class='btn btn-secondary' href='?" . $prev_page . "' class='mr-4'>Previous</a>";
                    echo "<a class='btn btn-secondary' href='?" . $advance_page . "'>Next</a>
                        </div>";
                } else {
                    $prev_page = $page - 1;
                    echo "<div class='text-center mt-6'>
                        <a class='btn btn-secondary' href='?" . $prev_page . "'>Previous</a></div>";
                }      

            $conn->close();
        }
        ?>
      <br>
      <br>
      </div>

      <?php include 'footer.php'; ?>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="../js/vendor/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/vendor/holder.min.js"></script>
    <!-- Placed at the end of the document so the pages load faster -->

          <!-- JS for search form, allowing player to access nhl_players.php and others to nhl_games.php -->
          <script>
            document.getElementById('nhl-search').addEventListener('submit', function (e) {
                const column = document.getElementById('nhl-search-column').value;
                console.log("Search column selected:", column); // Debugging
                if (column === 'player') {
                    this.action = 'nhl_players.php';
                    console.log("Form action set to nhl_players.php"); // Debugging
                } else {
                    this.action = 'nhl_games.php';
                    console.log("Form action set to nhl_games.php"); // Debugging
                }
            });
    </script>

  </body>
</html>