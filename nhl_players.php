<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>NHL Players</title>

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
            } else {
                $sql = "SELECT nhl_players.*, nhl_teams.triCode as currentTeamAbbrev
                FROM
                    nhl_players
                LEFT JOIN nhl_teams on nhl_players.currentTeamID = nhl_teams.id";
            }

            

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
                            <th>Active</th>
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
                                echo "<td>" . $row['isActive'] . "</td>";

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

                

            $conn->close();
        
        ?>

<!-- Replace your empty pagination div with this PHP-based pagination -->
<div id="pagination" class="flex justify-center items-center mt-4 gap-2">
    <?php if ($total_pages > 1): ?>
        <!-- Previous page button -->
        <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
               class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-500 transition-all">
                &lt;
            </a>
        <?php else: ?>
            <span class="px-4 py-2 bg-gray-400 text-white rounded opacity-50 cursor-not-allowed">&lt;</span>
        <?php endif; ?>

        <!-- First page -->
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" 
           class="px-4 py-2 <?= $page == 1 ? 'bg-blue-600' : 'bg-gray-600' ?> text-white rounded hover:bg-gray-500 transition-all">
            1
        </a>

        <!-- Ellipsis if needed -->
        <?php if ($page > 3): ?>
            <span class="px-4 py-2">...</span>
        <?php endif; ?>

        <!-- Page numbers around current page -->
        <?php 
        $start = max(2, $page - 1);
        $end = min($total_pages - 1, $page + 1);
        
        if ($page <= 3) {
            $start = 2;
            $end = min(4, $total_pages - 1);
        } elseif ($page >= $total_pages - 2) {
            $start = max($total_pages - 3, 2);
            $end = $total_pages - 1;
        }
        
        for ($i = $start; $i <= $end; $i++): 
        ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
               class="px-4 py-2 <?= $page == $i ? 'bg-blue-600' : 'bg-gray-600' ?> text-white rounded hover:bg-gray-500 transition-all">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <!-- Ellipsis if needed -->
        <?php if ($end < $total_pages - 1): ?>
            <span class="px-4 py-2">...</span>
        <?php endif; ?>

        <!-- Last page (if more than 1 page) -->
        <?php if ($total_pages > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" 
               class="px-4 py-2 <?= $page == $total_pages ? 'bg-blue-600' : 'bg-gray-600' ?> text-white rounded hover:bg-gray-500 transition-all">
                <?= $total_pages ?>
            </a>
        <?php endif; ?>

        <!-- Next page button -->
        <?php if ($page < $total_pages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
               class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-500 transition-all">
                &gt;
            </a>
        <?php else: ?>
            <span class="px-4 py-2 bg-gray-400 text-white rounded opacity-50 cursor-not-allowed">&gt;</span>
        <?php endif; ?>
    <?php endif; ?>
</div>
        
      <br>
      <br>
      </div>


      <?php include 'footer.php'; ?>


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