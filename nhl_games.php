<?php include('db_connection.php'); ?>
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
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

  </head>
  <body>
<!-- Header -->
<?php include 'header.php'; ?>



        <?php
        ini_set('display_errors', 1); error_reporting(E_ALL);

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            # Set default values for search column and term
            $searchColumn = '';
            $searchTerm = '';
            $originalSearchTerm = '';

            $sortColumnMap = [
                'gameDate' => 'nhl_games.gameDate',
                'home_team_name' => 'home_teams.fullName',
                'away_team_name' => 'away_teams.fullName',
                'home_score' => 'nhl_games.homeScore',
                'away_score' => 'nhl_games.awayScore',
                'game_id' => 'nhl_games.id'
            ];
            
            $requestedSortColumn = $_GET['sort_by'] ?? 'gameDate';
            $sortColumn = isset($sortColumnMap[$requestedSortColumn]) ? $sortColumnMap[$requestedSortColumn] : 'nhl_games.gameDate';
            $sortOrder = (isset($_GET['sort_order']) && strtolower($_GET['sort_order']) === 'asc') ? 'ASC' : 'DESC';
        
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


            if (!empty($_GET['search_column']) && !empty($_GET['search_term'])) {

                $searchColumn = mysqli_real_escape_string($conn, $_GET['search_column']);
                $searchTerm = mysqli_real_escape_string($conn, $_GET['search_term']);
                $originalSearchTerm = $searchTerm;


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
                if ($searchColumn === "team") {
                    $sql .= " WHERE home_teams.id = '$searchTerm' OR away_teams.id = '$searchTerm'";
                } else {
                    $sql .= " WHERE $searchColumn LIKE '%$searchTerm%'";
                }
                

                // Date range filter
                if (!empty($_GET['startDate']) && !empty($_GET['endDate'])) {
                    $startDate = $_GET['startDate'];
                    $endDate = $_GET['endDate'];
                    $sql .= (strpos($sql, 'WHERE') !== false ? " AND" : " WHERE") . " gameDate BETWEEN '$startDate' AND '$endDate'";
                }
                

                // Add "counting" query to get total number of result rows independent of pagination limit
                // Do this BEFORE adding ORDER BY and LIMIT clauses to the main query
                $count_query = "SELECT COUNT(*) as total
                FROM nhl_games
                JOIN nhl_teams AS home_teams ON nhl_games.homeTeamId = home_teams.id
                JOIN nhl_teams AS away_teams ON nhl_games.awayTeamId = away_teams.id";
                // Apply same WHERE clause
                $where_clauses = [];
                if ($searchColumn === "team") {
                    $where_clauses[] = "(home_teams.id = '$searchTerm' OR away_teams.id = '$searchTerm')";
                } else {
                    $where_clauses[] = "$searchColumn LIKE '%$searchTerm%'";
                }
                if (!empty($_GET['startDate']) && !empty($_GET['endDate'])) {
                    $startDate = $_GET['startDate'];
                    $endDate = $_GET['endDate'];
                    $where_clauses[] = "gameDate BETWEEN '$startDate' AND '$endDate'";
                }

                if (!empty($where_clauses)) {
                    $count_query .= " WHERE " . implode(" AND ", $where_clauses);
                }
                $count_result = mysqli_query($conn, $count_query) or die("Count query failed: " . mysqli_error($conn));
                $total_rows = mysqli_fetch_assoc($count_result)['total'] ?? 0;

                // Add order and limit clauses
                $sql .= " ORDER BY $sortColumn $sortOrder";
                // $sql .= " LIMIT $limit OFFSET $offset";

                // Execute and check query
                $result = mysqli_query($conn, $sql) or die("Query failed: " . mysqli_error($conn));

                if (!$result) {
                    die("Query failed: " . mysqli_error($conn));
                }
                
                ?>

            <div id="nhl-games-players-summary-content-container" style='background-color: #343a40'>
                <br>
                    <p class="text-lg text-center">Search again:</p>
                    <div class="flex justify-center">
                        <form id='nhl-search' method="GET" action="nhl_games.php"
                            class="backdrop-blur-sm px-4 sm:px-6 py-4 rounded-lg flex flex-col sm:flex-row gap-4 items-stretch sm:items-center w-full max-w-4xl nhl-search-column">
                
                        <!-- Dropdown -->
                        <select name="search_column" id='nhl-search-column' class="w-full sm:w-auto flex-1 bg-white text-black text-sm rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="season">Season</option>
                            <option value="gameDate">Game Date</option>
                            <option value="easternStartTime">Start Time</option>
                            <option value="gameType">Game Type</option>
                            <option value="team">Team</option>
                            <option value="homeTeamId">Home Team</option>
                            <option value="awayTeamId">Away Team</option>
                            <option value="player">Player Name</option>
                        </select>

                        <!-- Text input -->
                        <input type="text" name="search_term" id="search-term" placeholder="Enter search term" required
                            class="w-full sm:flex-2 text-black px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">

                        <!-- Submit button -->
                        <input type="submit" value="Search"
                            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-md transition-colors duration-200 cursor-pointer">
                        </form>
                
                    </div>


                    <?php
                    while ($row = $result->fetch_assoc()){

                        # Season
                        $formatted_season_1 = substr($row['season'], 0, 4);
                        $formatted_season_2 = substr($row['season'], 4);
                        $formatted_season = $formatted_season_1 . "-" . $formatted_season_2;

                        # Date
                        $gameDate = $row['gameDate'];
                        $gameDatetime = new DateTime($gameDate);
                        $formatted_gameDate = $gameDatetime->format('m/d/Y');

                        # Time
                        $formatted_startTime = substr($row['easternStartTime'], 11, -3);

                        # Game Type (i.e. Preseason, Regular Season, etc.)
                        $gameType_num = $row['gameType'];
                        if ($gameType_num == 1) {
                            $gameType_text = "Pre.";
                        } elseif ($gameType_num == 2) {
                            $gameType_text = "Reg.";
                        } elseif ($gameType_num == 3) {
                            $gameType_text = "Post.";
                        } else {
                            $gameType_text = "Unknown";
                        }


                        $all_games[] = [
                            'season' => $formatted_season,
                            'gameNumber' => $row['gameNumber'],
                            'gameDate' => $formatted_gameDate,
                            'easternStartTime' => $formatted_startTime,
                            'gameType' => $gameType_text,
                            'home_team_id' => $row['home_team_id'],
                            'home_team_name' => $row['home_team_name'],
                            'homeScore' => $row['homeScore'],
                            'away_team_id' => $row['away_team_id'],
                            'away_team_name' => $row['away_team_name'],
                            'awayScore' => $row['awayScore'],
                            'id' => $row['id']
                        ];

                    } 
                        // Pass data to JavaScript as JSON
                        echo "<script>const allGames = " . json_encode($all_games) . ";</script>";

                    } else {
                        echo "<tr><td colspan='10' class='text-center'>No results found.</td></tr>";
                    }
                    
                
        }
        
                
        ?>
        <br><hr class='border-white border mx-auto w-4/5'><br>
                        <!-- Display results in a table format -->
                <h2 class="text-4xl font-bold text-white text-center">Game Results</h2><br>
                <?php echo "<h5 style='text-align: center'>" . $total_rows . " results found where " . $searchColumn . " = '" . $originalSearchTerm . "'</h5>"; ?>
                <p>Click on any team name or game ID to view additional details about the team/game, or filter games below.</p><br>

                        <!-- Search Filter Fields -->
                    <div class="mb-4">
                    <div class='flex justify-between flex-wrap gap-4 max-w-[85%] mx-auto mt-5'>
                    <input type="text" id="searchBySeason" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Season">
                    <input type="text" id="searchByDate" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Date">
                    <input type="text" id="searchByStartTime" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Start Time (EST)">
                    <input type="text" id="searchByGameType" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Game Type">
                    <input type="text" id="searchByHomeTeam" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Home Team">
                    <input type="text" id="searchByAwayTeam" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Away Team">
                    </div>
                    <br>
                    <div class="table-container shadow-md rounded-lg overflow-x-auto mx-auto w-[90%]">
                    <!-- Table -->
                    <table id='games-players-summary-table' class="min-w-max table-auto default-zebra-table">
                        <colgroup>
                        <col class="games-players-summary-col-season">
                        <col class="games-players-summary-col-gameNumber">
                        <col class="games-players-summary-col-date">
                        <col class="games-players-summary-col-startTime">
                        <col class="games-players-summary-col-gameType">
                        <col class="games-players-summary-col-homeTeam">
                        <col class="games-players-summary-col-homeScore">
                        <col class="games-players-summary-col-awayTeam">
                        <col class="games-players-summary-col-awayScore">
                        <col class="games-players-summary-col-id">
                        </colgroup>
                        <thead class='default-zebra-table'>
                            <tr class='default-zebra-table'>
                                <th>Season<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=season&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=season&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                                <th>Game #<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=gameNumber&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=gameNumber&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                                <th>Date<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=gameDate&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=gameDate&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                                <th>Start (EST)<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=easternStartTime&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=easternStartTime&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                                <th>Game Type<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=gameType&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=gameType&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                                <th>Home Team<br>
                                    <span class='sort-arrows'>
                                        <a href='?search_column=" . urlencode($searchColumn) . "&search_term=" . urlencode($searchTerm)
                                        . "&sort_by=home_team_name&sort_order=asc'>△</a><a href='?search_column=" . urlencode($searchColumn)
                                        . "&search_term=" . urlencode($searchTerm)
                                        . "&sort_by=home_team_name&sort_order=desc'>▽</a>
                                    </span>
                                </th>
                                <th>Home Score<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=homeScore&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=homeScore&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                                <th>Away Team<br>
                                    <span class='sort-arrows'>
                                        <a href='?search_column=" . urlencode($searchColumn) . "&search_term=" . urlencode($searchTerm)
                                        . "&sort_by=away_team_name&sort_order=asc' class="text-xs">△</a><a href='?search_column=" . urlencode($searchColumn)
                                        . "&search_term=" . urlencode($searchTerm)
                                        . "&sort_by=away_team_name&sort_order=desc' class="text-xs">▽</a>
                                    </span>
                                </th>
                                <th>Away Score<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=awayScore&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=awayScore&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                                <th>Game ID<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=id&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=id&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                                        <!-- Rows will be dynamically generated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                
    <br>
        <!-- Pagination Controls -->
        <div id="pagination" class="flex justify-center flex-wrap gap-2 mt-6 text-white w-3/5 mx-auto">
        <!-- Pagination buttons will be dynamically generated -->
    </div>
    <br>
    

    <?php include 'footer.php'; ?>


    <!-- JS for pagination -->
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.querySelector("#games-players-summary-table tbody");
    const searchBySeason = document.getElementById("searchBySeason");
    const searchByDate = document.getElementById("searchByDate");
    const searchByStartTime = document.getElementById("searchByStartTime");
    const searchByGameType = document.getElementById("searchByGameType");
    const searchByHomeTeam = document.getElementById("searchByHomeTeam");
    const searchByAwayTeam = document.getElementById("searchByAwayTeam");
    const pagination = document.getElementById("pagination");

    let currentPage = 1;
    const pageSize = 50;
    
    // Function to render rows dynamically
    function renderTable(data) {
            tableBody.innerHTML = ""; // Clear the table first
            const start = (currentPage - 1) * pageSize;
            const end = start + pageSize;
            const paginatedData = data.slice(start, end);

            paginatedData.forEach(row => {
                const tr = document.createElement("tr");

                // Build the static part of the row (values that don't depend on conditions)
                tr.innerHTML = `
                    <td>${row.season}</td>
                    <td>${row.gameNumber}</td>
                    <td>${row.gameDate}</td>
                    <td>${row.easternStartTime}</td>
                    <td>${row.gameType}</td>
                `;

                let homeScoreCell, awayScoreCell, homeTeamCell, awayTeamCell;

                // Conditional logic to populate the score and team cells
                if (row.homeScore > row.awayScore) {
                    homeTeamCell = `<td class='font-bold'><a href='team_details.php?team_id=${row.home_team_id}'>${row.home_team_name}</a></td>`;
                    homeScoreCell = `<td class='font-bold'>${row.homeScore}</td>`;
                    awayTeamCell = `<td><a href='team_details.php?team_id=${row.away_team_id}'>${row.away_team_name}</a></td>`;
                    awayScoreCell = `<td>${row.awayScore}</td>`;
                } else if (row.homeScore < row.awayScore) {
                    homeTeamCell = `<td><a href='team_details.php?team_id=${row.home_team_id}'>${row.home_team_name}</a></td>`;
                    homeScoreCell = `<td>${row.homeScore}</td>`;
                    awayTeamCell = `<td class='font-bold'><a href='team_details.php?team_id=${row.away_team_id}'>${row.away_team_name}</a></td>`;
                    awayScoreCell = `<td class='font-bold'>${row.awayScore}</td>`;
                } else {
                    homeTeamCell = `<td><a href='team_details.php?team_id=${row.home_team_id}'>${row.home_team_name}</a></td>`;
                    homeScoreCell = `<td>${row.homeScore}</td>`;
                    awayTeamCell = `<td><a href='team_details.php?team_id=${row.away_team_id}'>${row.away_team_name}</a></td>`;
                    awayScoreCell = `<td>${row.awayScore}</td>`;
                }

                // Add the team and score cells to the row
                tr.innerHTML += homeTeamCell + homeScoreCell + awayTeamCell + awayScoreCell;

                // Add the last column for the game ID link
                tr.innerHTML += `<td><a href='game_details.php?game_id=${row.id}'>${row.id}</a></td>`;

                tableBody.appendChild(tr);

            });
        }

        // Function to render pagination controls
        function renderPagination(data) {
            const pagination = document.getElementById("pagination");
            pagination.innerHTML = "";

            const totalPages = Math.ceil(data.length / pageSize);
            const maxVisiblePages = 5; // how many pages to show around the current one

            const createButton = (text, page = null) => {
                const btn = document.createElement("button");
                btn.textContent = text;
                btn.className = "px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-500 transition-all";
                if (page !== null) {
                    btn.addEventListener("click", () => {
                        currentPage = page;
                        updateTableAndPagination(data);
                    });
                } else {
                    btn.disabled = true;
                    btn.classList.add("disabled");
                }
                return btn;
            };

            const addPageButton = (i) => {
                const btn = createButton(i, i);
                if (i === currentPage) btn.classList.add("active");
                pagination.appendChild(btn);
            };

            // Always show first page
            addPageButton(1);

            let start = Math.max(2, currentPage - 1);
            let end = Math.min(totalPages - 1, currentPage + 1);

            if (currentPage <= 3) {
                start = 2;
                end = Math.min(4, totalPages - 1);
            } else if (currentPage >= totalPages - 2) {
                start = Math.max(totalPages - 3, 2);
                end = totalPages - 1;
            }

            if (start > 2) {
                pagination.appendChild(createButton("..."));
            }

            for (let i = start; i <= end; i++) {
                addPageButton(i);
            }

            if (end < totalPages - 1) {
                pagination.appendChild(createButton("..."));
            }

            // Always show last page
            if (totalPages > 1) {
                addPageButton(totalPages);
            }
        }



        function filterTable() {
            const seasonFilter = searchBySeason.value.toLowerCase();
            const startTimeFilter = searchByStartTime.value.toLowerCase();
            const dateFilter = searchByDate.value.toLowerCase();
            const gameTypeFilter = searchByGameType.value.toLowerCase();
            const homeTeamFilter = searchByHomeTeam.value.toLowerCase();
            const awayTeamFilter = searchByAwayTeam.value.toLowerCase();

            return allGames.filter(row => {
                const matchesSeason = row.season.toLowerCase().includes(seasonFilter);
                const matchesDate = row.gameDate.toLowerCase().includes(dateFilter);
                const matchesStartTime = row.easternStartTime.toLowerCase().includes(startTimeFilter);
                const matchesGameType = row.gameType.toLowerCase().includes(gameTypeFilter);
                const matchesHomeTeam = row.home_team_name.toLowerCase().includes(homeTeamFilter);
                const matchesAwayTeam = row.away_team_name.toLowerCase().includes(awayTeamFilter);
                return matchesSeason && matchesDate && matchesStartTime && matchesGameType && matchesHomeTeam && matchesAwayTeam;
            });
}


        // Function to update table and pagination
        function updateTableAndPagination(data) {
            renderTable(data);
            renderPagination(data);
        }

        // Attach event listeners for filtering

        searchBySeason.addEventListener("keyup", () => {
            currentPage = 1; // Reset to first page on filter change
            const filteredData = filterTable();
            updateTableAndPagination(filteredData);
        });

        searchByDate.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });

        searchByStartTime.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });

        searchByGameType.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });

        searchByHomeTeam.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });

        searchByAwayTeam.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });


        // Initially render all rows and pagination
        updateTableAndPagination(allGames);
    });
</script>
<script>
        //   <!-- JS for search form, allowing player to access nhl_players.php and others to nhl_games.php -->
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