<?php include('db_connection.php'); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connor Young</title>
    <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<?php include 'header.php'; ?>
<body>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if 'game_id' is passed in URL
if (isset($_GET['game_id'])) {
    $game_id = $_GET['game_id'];

    # Header Info
    $headerSQL = "SELECT nhl_games.id,
    nhl_games.gameDate,
    nhl_games.venue,
    nhl_games.venueLocation,
    nhl_games.easternStartTime,
    nhl_games.gameStateId,
    nhl_games.homeScore,
    nhl_games.awayScore,
    home_teams.teamLogo AS homeLogo,
    away_teams.teamLogo AS awayLogo,
    nhl_games.gameType,
    nhl_games.gameNumber,
    nhl_games.season,
    home_teams.fullName AS home_team_name,
    away_teams.fullName AS away_team_name,
    nhl_games.gameOutcome
    FROM 
    nhl_games
    LEFT JOIN nhl_teams AS home_teams
    ON nhl_games.homeTeamId = home_teams.id
    LEFT JOIN nhl_teams AS away_teams
    ON nhl_games.awayTeamId = away_teams.id
    WHERE
    nhl_games.id = '$game_id'";

    // echo($headerSQL);

    try {
        $header = mysqli_query($conn, $headerSQL);
        // echo "<p>Successful</p>";
        } catch (mysqli_sql_exception $e) {
        echo "MySQL Error: " . $e->getMessage();
        exit;
        }

    // echo '<pre>'; print_r($header); echo '</pre>';

    $row = mysqli_fetch_assoc($header);
    if (!$row) {
        echo "<p>No data returned from header query.</p>";
    } else {
        $venue = $row['venue'];
        $venueLocation = $row['venueLocation'];
        $game_date = $row['gameDate'];
        $homeScore = $row['homeScore'];
        $awayScore = $row['awayScore'];
        $homeLogo = $row['homeLogo'];
        $awayLogo = $row['awayLogo'];
        $homeTeamName = $row['home_team_name'];
        $awayTeamName = $row['away_team_name'];
        $formatted_startTime = substr($row['easternStartTime'], 11, -3);
        $gameType_num = $row['gameType'];
        $gameNum = $row['gameNumber'];
        if ($gameType_num == 1) {
            $gameType_text = "Preseason";
        } elseif ($gameType_num == 2) {
            $gameType_text = "Reg. Season";
        } elseif ($gameType_num == 3) {
            $gameType_text = "Playoffs";
        } else {
            $gameType_text = "Unknown";
        }
        $season = $row['season'];
        $formatted_season = substr($season, 0, 4) . '-' . substr($season, 4);
        $game_outcome = $row['gameOutcome']; #### this isn't working
        if ($game_outcome == 'REG') {
            $formatted_outcome = '';
        }
        else if ($game_outcome == 'OT') {
            $formatted_outcome = "(OT)";
        }

        $gameDatetime = new DateTime($game_date);
        $formatted_gameDate = $gameDatetime->format('m/d/Y');

        
        echo "<div class='text-white text-center' style='background-color: #343a40'>"; // whole page background color
        ?>
        <div class="relative">
            <div class="absolute top-0 left-0" style='margin-left: 20px; margin-top: 20px;'>
                <a href="https://connoryoung.com/game_details.php?game_id=<?php echo htmlspecialchars($game_id); ?>" class="hover:underline" style='color:rgb(57, 121, 199)'>Back to Game Details</a>
            </div>
        </div>
        <?php
        echo "<br><br>";
        echo "<div class='max-w-[80%] mx-auto bg-slate-800 text-white py-6 px-4 rounded-lg shadow-lg mb-8 border-2 border-slate-600'>";
        echo "<div class='flex flex-col items-center space-y-4'>"; // Removed flex-grow on the outer container

        // Team logos and names
        echo "<div class='flex items-center justify-center space-x-6'>"; // Keep the spacing but remove flex-grow
        echo "<img src='" . htmlspecialchars($homeLogo) . "' alt='homeLogo' class='h-20 max-w-xs'>"; // Added max-width for logos
        echo "<h3 class='text-3xl font-bold text-center whitespace-nowrap'>" . htmlspecialchars($homeTeamName) . " (H) <span class='mx-2'>vs.</span> " . htmlspecialchars($awayTeamName) . " (A)</h3>";
        echo "<img src='" . htmlspecialchars($awayLogo) . "' alt='awayLogo' class='h-20 max-w-xs'>"; // Added max-width for logos
        echo "</div>";
        
        // Score line
        echo "<h3 class='text-4xl font-semibold'>" . htmlspecialchars($homeScore) . " - " . htmlspecialchars($awayScore) . " <span class='text-lg font-normal ml-2'>" . $formatted_outcome . "</span></h3>";
        
        // Venue and time
        echo "<p class='text-lg'>" . htmlspecialchars($venue) . ", " . htmlspecialchars($venueLocation) . "<br>" . htmlspecialchars($formatted_gameDate) . " " . htmlspecialchars($formatted_startTime) . " EST</p>";
        
        // Season and game info
        echo "<p class='text-base italic'>" . $formatted_season . " " . $gameType_text . " - Game Number " . $gameNum . "</p>";
        
        echo "</div>"; // flex-col container
        echo "</div>"; // banner wrapper
        
        
        echo "<hr style='width:80%; background-color:white' class='mx-auto'>";


    // Fetch all results for the given game_id
    $sql = "SELECT nhl_shifts.*, nhl_players.firstName, nhl_players.lastName, nhl_teams.triCode
            FROM nhl_shifts
            LEFT JOIN nhl_players ON nhl_shifts.playerID = nhl_players.playerId
            LEFT JOIN nhl_teams ON nhl_shifts.teamId = nhl_teams.ID
            WHERE nhl_shifts.gameID = '$game_id'
            ORDER BY nhl_shifts.period, nhl_shifts.startTime ASC";

    $result = mysqli_query($conn, $sql);

    // Store all rows in a PHP array
    $all_shifts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $all_shifts[] = [
            'gameID' => $row['gameID'],
            'shiftID' => $row['shiftID'],
            'playerID' => htmlspecialchars($row['playerID']),
            'playerName' => htmlspecialchars($row['firstName'] . ' ' . $row['lastName']),
            'shiftNumber' => $row['shiftNumber'],
            'period' => $row['period'],
            'startTime' => $row['startTime'],
            'endTime' => $row['endTime'],
            'duration' => $row['duration'],
            'teamTricode' => htmlspecialchars($row['triCode']),
            'eventDescription' => htmlspecialchars($row['eventDescription']),
        ];
    }

    // Pass data to JavaScript as JSON
    echo "<script>const allShifts = " . json_encode($all_shifts) . ";</script>";
}
$conn->close();
} else {
    echo "<p>No game ID provided.</p>";
}
?>
    <br><br>
<div class='shift-table-container max-w-[80%] mx-auto'>

    <!-- Search Filter Fields -->
    <div class="flex justify-between items-center mb-4">
        <input type="text" id="searchByPlayer" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Search by Player">
        <h2 class="text-4xl font-bold text-white">Player Shifts</h2>
        <input type="text" id="searchByTeam" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Search by Team">
    </div>

    <!-- Table -->
    <table class='shift-table default-zebra-table' id="shiftTable">
        <thead>
            <tr>
                <!-- <th>Game ID</th>
                <th>Shift ID</th> -->
                <th>Player Name</th>
                <th>Shift Number</th>
                <th>Period</th>
                <th>Shift Start</th>
                <th>Shift End</th>
                <th>Shift Duration</th>
                <th>Team ID</th>
                <th>Shift Event(s)</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be dynamically generated by JavaScript -->
        </tbody>
    </table>

    <!-- Pagination Controls -->
    <div id="pagination" class="flex justify-center space-x-4 mt-6 text-white">
        <!-- Pagination buttons will be dynamically generated -->
    </div>
    <br>
</div>

<script>
    // JavaScript to dynamically filter and paginate table rows
    document.addEventListener("DOMContentLoaded", function () {
        const tableBody = document.querySelector("#shiftTable tbody");
        const searchByPlayer = document.getElementById("searchByPlayer");
        const searchByTeam = document.getElementById("searchByTeam");
        const pagination = document.getElementById("pagination");

        let currentPage = 1;
        const pageSize = 50; // Number of rows per page

        // Function to render rows dynamically
        function renderTable(data) {
            tableBody.innerHTML = ""; // Clear the table first
            const start = (currentPage - 1) * pageSize;
            const end = start + pageSize;
            const paginatedData = data.slice(start, end);

            paginatedData.forEach(row => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td><a href="player_details.php?team_id=${row.playerID}">${row.playerName}</a></td>
                    <td>${row.shiftNumber}</td>
                    <td>${row.period}</td>
                    <td>${row.startTime}</td>
                    <td>${row.endTime}</td>
                    <td>${row.duration}</td>
                    <td>${row.teamTricode}</td>
                    <td>${row.eventDescription}</td>
                `;
                tableBody.appendChild(tr);
            });
        }

        // Function to render pagination controls
        function renderPagination(data) {
            pagination.innerHTML = ""; // Clear existing pagination controls
            const totalPages = Math.ceil(data.length / pageSize);

            // Previous button
            if (currentPage > 1) {
                const prevButton = document.createElement("button");
                prevButton.textContent = "Previous";
                prevButton.className = "btn btn-secondary";
                prevButton.addEventListener("click", () => {
                    currentPage--;
                    updateTableAndPagination(data);
                });
                pagination.appendChild(prevButton);
            }

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                const pageButton = document.createElement("button");
                pageButton.textContent = i;
                pageButton.className = `btn ${i === currentPage ? "btn-primary" : "btn-secondary"}`;
                pageButton.addEventListener("click", () => {
                    currentPage = i;
                    updateTableAndPagination(data);
                });
                pagination.appendChild(pageButton);
            }

            // Next button
            if (currentPage < totalPages) {
                const nextButton = document.createElement("button");
                nextButton.textContent = "Next";
                nextButton.className = "btn btn-secondary";
                nextButton.addEventListener("click", () => {
                    currentPage++;
                    updateTableAndPagination(data);
                });
                pagination.appendChild(nextButton);
            }
        }

        // Function to filter rows based on input
        function filterTable() {
            const playerFilter = searchByPlayer.value.toLowerCase();
            const teamFilter = searchByTeam.value.toLowerCase();

            return allShifts.filter(row => {
                const matchesPlayer = row.playerName.toLowerCase().includes(playerFilter);
                const matchesTeam = row.teamTricode.toLowerCase().includes(teamFilter);
                return matchesPlayer && matchesTeam;
            });
        }

        // Function to update table and pagination
        function updateTableAndPagination(data) {
            renderTable(data);
            renderPagination(data);
        }

        // Attach event listeners for filtering
        searchByPlayer.addEventListener("keyup", () => {
            currentPage = 1; // Reset to first page on filter change
            const filteredData = filterTable();
            updateTableAndPagination(filteredData);
        });

        searchByTeam.addEventListener("keyup", () => {
            currentPage = 1; // Reset to first page on filter change
            const filteredData = filterTable();
            updateTableAndPagination(filteredData);
        });

        // Initially render all rows and pagination
        updateTableAndPagination(allShifts);
    });

</script>

</body>
</html>