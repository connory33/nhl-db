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
if (isset($_GET['draft_id'])) {
    $draft_id = $_GET['draft_id'];

    
    $sql = "SELECT draft_history.*, nhl_teams.id as team_id, nhl_teams.triCode as triCode, nhl_teams.teamLogo as logo, league_pages.* from 
            draft_history 
            LEFT JOIN nhl_teams ON draft_history.teamID = nhl_teams.id
            LEFT JOIN league_pages on draft_history.amateurLeague = league_pages.leagueName
            WHERE draftID = '$draft_id'
            ORDER BY round, pickInRound";

    $result = mysqli_query($conn, $sql);

    // Store all rows in a PHP array
    $all_picks = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $all_picks[] = [
            'draftYear' => $row['draftYear'],
            'round' => $row['round'],
            'pickInRound' => $row['pickInRound'],
            'overallPick' => $row['overallPick'],
            'teamID' => $row['teamID'],
            'pickHistory' => $row['teamPickHistory'],
            'firstName' => $row['firstName'],
            'lastName' => $row['lastName'],
            'position' => $row['position'],
            'country' => $row['country'],
            'height' => $row['height'],
            'weight' => $row['weight'],
            'amateurLeague' => $row['amateurLeague'],
            'amateurClubName' => $row['amateurClubName'],
            'triCode' => $row['triCode'],
            'logo' => $row['logo'],
            'team_id' => $row['team_id'],
            'playerID' => $row['playerId'],
            'amateurLeagueName' => $row['leagueName'],
            'amateurLeagueURL' => $row['homepageURL'],
            'selectableRounds' => $row['selectableRounds']
        ];
    }

    // Pass data to JavaScript as JSON
    echo "<script>const allPicks = " . json_encode($all_picks) . ";</script>";

} else {
    echo "<p>No game ID provided.</p>";
}
?>
<div style='background-color: #343a40'>
    <br><br>
<div class='max-w-[90%] mx-auto'>
    <div class='flex flex-wrap justify-center items-center gap-2 mb-2 text-white text-sm'>
        <a href="https://connoryoung.com/draft_history.php?draft_id=52">1979</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=16">1980</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=39">1981</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=7">1982</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=30">1983</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=54">1984</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=20">1985</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=43">1986</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=12">1987</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=34">1988</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=1">1989</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=25">1990</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=50">1991</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=15">1992</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=37">1993</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=6">1994</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=28">1995</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=53">1996</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=18">1997</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=42">1998</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=10">1999</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=33">2000</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=56">2001</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=22">2002</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=46">2003</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=13">2004</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=36">2005</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=5">2006</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=38">2007</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=3">2008</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=23">2009</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=47">2010</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=14">2011</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=41">2012</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=4">2013</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=31">2014</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=57">2015</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=19">2016</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=44">2017</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=9">2018</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=32">2019</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=58">2020</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=59">2021</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=60">2022</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=62">2023</a> |
        <a href="https://connoryoung.com/draft_history.php?draft_id=63">2024</a>
    </div><br>
<h2 class="text-4xl font-bold text-white text-center">
    Draft Picks <?php if (!empty($all_picks)) echo htmlspecialchars($all_picks[0]['draftYear']); ?>
</h2><br>
<p class='text-center text-white'>Sort by:</p><br>
    <!-- Search Filter Fields -->
    <div class="flex flex-wrap justify-center items-center gap-4 mb-4 max-w-[75%] mx-auto">
        <input type="text" id="searchByRound" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Round">
        <input type="text" id="searchByTeam" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Team (tricode, e.g., 'NYR')">
        <input type="text" id="searchByPlayer" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Player">
        <input type="text" id="searchByPosition" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Position">
        <input type="text" id="searchByCountry" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Country">
        <input type="text" id="searchByLeague" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Amateur League">
        <input type="text" id="searchByClub" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Amateur Team">
    </div>
<div class="overflow-x-auto">
    <!-- Table -->
     <p class='text-center text-white'> Click any team logo or player name to view details.</p><br>
    <table class='shift-table default-zebra-table text-center' id="draftTable">
        <thead>
            <tr>
                <!-- <th>Draft Year</th> -->
                <th class='border border-slate-600 px-2 py-1'>Round</th>
                <!-- <th>Pick</th> -->
                <th class='border border-slate-600 px-2 py-1'>Overall</th>
                <th class='border border-slate-600 px-2 py-1'>Team</th>
                <!-- <th>Pick History</th> -->
                <th class='border border-slate-600 px-2 py-1'>Name</th>
                <th class='border border-slate-600 px-2 py-1'>Position</th>
                <th class='border border-slate-600 px-2 py-1'>Country</th>
                <th class='border border-slate-600 px-2 py-1'>Height (in.)</th>
                <th class='border border-slate-600 px-2 py-1'>Weight (lbs)</th>
                <th class='border border-slate-600 px-2 py-1'>Amateur League</th>
                <th class='border border-slate-600 px-2 py-1'>Amateur Club Name</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be dynamically generated by JavaScript -->
             
        </tbody>
    </table>
</div>

    <!-- Pagination Controls -->
    <div id="pagination" class="flex justify-center space-x-4 mt-6 text-white">
        <!-- Pagination buttons will be dynamically generated -->
    </div>
    <br>
</div>

<script>
    // JavaScript to dynamically filter and paginate table rows
    document.addEventListener("DOMContentLoaded", function () {
        const tableBody = document.querySelector("#draftTable tbody");
        const searchByPlayer = document.getElementById("searchByPlayer");
        const searchByTeam = document.getElementById("searchByTeam");
        const searchByRound = document.getElementById("searchByRound");
        const searchByClub = document.getElementById("searchByClub");
        const searchByLeague = document.getElementById("searchByLeague");
        const searchByPosition = document.getElementById("searchByPosition");
        const searchByCountry = document.getElementById("searchByCountry");
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
                    <td class='border border-slate-600 px-2 py-1'>${row.round}</td>
                    <td class='border border-slate-600 px-2 py-1'>${row.overallPick}</td>
                    <td class='border border-slate-600 px-2 py-1'><a href='team_details.php?team_id=${row.team_id}'><img src="${row.logo}" style='width: 45px' class='mx-auto'></a></td>
                    <td class='border border-slate-600 px-2 py-1'><a href="player_details.php?player_id=${row.playerID}">${row.firstName} ${row.lastName}</a></td>
                    <td class='border border-slate-600 px-2 py-1'>${row.position}</td>
                    <td class='border border-slate-600 px-2 py-1'>${row.country}</td>
                    <td class='border border-slate-600 px-2 py-1'>${row.height}</td>
                    <td class='border border-slate-600 px-2 py-1'>${row.weight}</td>
                    <td class='border border-slate-600 px-2 py-1'><a href='${row.amateurLeagueURL}'>${row.amateurLeague}</a></td>
                    <td class='border border-slate-600 px-2 py-1'>${row.amateurClubName}</td>
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

        function filterTable() {
            const playerFilter = searchByPlayer.value.toLowerCase();
            const teamFilter = searchByTeam.value.toLowerCase();
            const leagueFilter = searchByLeague.value.toLowerCase();
            const roundFilter = searchByRound.value.toLowerCase();
            const clubFilter = searchByClub.value.toLowerCase();
            const positionFilter = searchByPosition.value.toLowerCase();
            const countryFilter = searchByCountry.value.toLowerCase();

            return allPicks.filter(row => {
                const fullName = `${row.firstName} ${row.lastName}`.toLowerCase();
                const matchesPlayer = fullName.includes(playerFilter);
                const matchesTeam = row.triCode?.toLowerCase().includes(teamFilter);
                const matchesLeague = row.amateurLeague?.toLowerCase().includes(leagueFilter);
                const matchesRound = row.round?.toString().toLowerCase().includes(roundFilter);
                const matchesClub = row.amateurClubName?.toLowerCase().includes(clubFilter);
                const matchesPosition = row.position?.toLowerCase().includes(positionFilter);
                const matchesCountry = row.country?.toLowerCase().includes(countryFilter);

                return matchesPlayer && matchesTeam && matchesLeague && matchesRound && matchesClub && matchesPosition && matchesCountry;
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

        searchByLeague.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });

        searchByClub.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });

        searchByRound.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });

        searchByPosition.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });

        searchByCountry.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });


        // Initially render all rows and pagination
        updateTableAndPagination(allPicks);
    });

</script>
</div>
<?php include 'footer.php'; ?>
</body>
</html>