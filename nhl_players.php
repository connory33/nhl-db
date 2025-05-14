<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="NHL Players Database - Search and browse current and former NHL players">
    <meta name="author" content="Connor Young">
    <link rel="icon" href="../../../../favicon.ico">

    <title>NHL Players Database</title>

    <link href="/resources/css/default_v3.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    
</head>
<body>

  <?php include 'header.php'; ?>

  <div class="page-container">
    <div class="page-header text-center">
      <h1 class="page-title">NHL Players Database</h1>
      <p class="text-gray-300">Search and browse current and former NHL players</p><br>
    </div>
    
    <div class="search-container w-[85%] mx-auto">
      <?php
      include('db_connection.php');
      ini_set('display_errors', 1); error_reporting(E_ALL);

      if (!empty($_GET['search_column']) && !empty($_GET['search_term'])) {
          $searchColumn = mysqli_real_escape_string($conn, $_GET['search_column']);
          $searchTerm = mysqli_real_escape_string($conn, $_GET['search_term']);
          $originalSearchTerm = $searchTerm;

          $sql = "SELECT nhl_players.*, nhl_teams.triCode as currentTeamAbbrev
          FROM
              nhl_players
          LEFT JOIN nhl_teams on nhl_players.currentTeamID = nhl_teams.id
          WHERE 
              firstName LIKE '%$searchTerm%' 
              OR lastName LIKE '%$searchTerm%'
              OR CONCAT(firstName, ' ', lastName) LIKE '%$searchTerm%'
          ORDER BY nhl_players.playerID DESC";
      } else {
          $sql = "SELECT nhl_players.*, nhl_teams.triCode as currentTeamAbbrev
          FROM
              nhl_players
          LEFT JOIN nhl_teams on nhl_players.currentTeamID = nhl_teams.id
          ORDER BY nhl_players.playerID DESC";
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
      $result = mysqli_query($conn, $sql);

      if (!$result) {
          die("Query failed: " . mysqli_error($conn));
      }
      ?>
      
      <form id="nhl-search" method="GET" action="nhl_games.php" class="search-form">
        <select name="search_column" id="nhl-search-column" class="w-full sm:w-48">
            <option value="season">Season</option>
            <option value="gameDate">Game Date</option>
            <option value="easternStartTime">Start Time</option>
            <option value="gameType">Game Type</option>
            <option value="team">Team</option>
            <option value="homeTeamId">Home Team</option>
            <option value="awayTeamId">Away Team</option>
            <option value="player" selected>Player Name</option>
        </select>
        <input type="text" name="search_term" id="search-term" placeholder="Enter player name" required class="w-full sm:flex-1">
        <button type="submit" class="search-button">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          Search
        </button>
      </form>
    </div>

    <div class="results-container">
      <div class="results-header">
        <h2 class="results-title">Results</h2>
        <?php if ($total_rows > 0): ?>
          <p class="results-count">Showing results <?= $start ?>–<?= $end ?> of <?= $total_rows ?> (Page <?= $page ?> of <?= $total_pages ?>)</p>
        <?php endif; ?>
      </div>
      
      <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="text-center py-8">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-xl mt-4">No players found matching your search criteria.</p>
          <p class="text-gray-400 mt-2">Try adjusting your search terms or browse all players.</p>
        </div>
      <?php else: ?>
        <div class="table-container">
          <table class="players-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Height</th>
                <th>Weight</th>
                <th>Birthdate</th>
                <th>Country</th>
                <th>Shoots/Catches</th>
                <th>Active</th>
                <th>Number</th>
                <th>Team</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td>
                    <a href="player_details.php?player_id=<?= $row['playerId'] ?>" class="player-link">
                      <?= $row['playerId'] ?>
                    </a>
                  </td>
                  <td>
                    <a href="player_details.php?player_id=<?= $row['playerId'] ?>" class="player-link">
                      <?= $row['firstName'] . ' ' . $row['lastName'] ?>
                    </a>
                  </td>
                  <td><?= $row['heightInInches'] ?> in / <?= $row['heightInCentimeters'] ?> cm</td>
                  <td><?= $row['weightInPounds'] ?> lbs / <?= $row['weightInKilograms'] ?> kg</td>
                  <td><?= date('m/d/Y', strtotime($row['birthDate'])) ?></td>
                  <td><?= $row['birthCountry'] ?></td>
                  <td><?= $row['shootsCatches'] ?></td>
                  <td><?= $row['isActive'] ?></td>
                  <td><?= $row['sweaterNumber'] ?: '-' ?></td>
                  <td><?= $row['currentTeamAbbrev'] ?: '-' ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
        
        <div class="pagination">
          <?php if ($total_pages > 1): ?>
            <!-- Previous page button -->
            <?php if ($page > 1): ?>
              <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                 class="page-button">
                  &lt;
              </a>
            <?php else: ?>
              <span class="page-button disabled">&lt;</span>
            <?php endif; ?>

            <!-- First page -->
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" 
               class="page-button <?= $page == 1 ? 'active' : '' ?>">
                1
            </a>

            <!-- Ellipsis if needed -->
            <?php if ($page > 3): ?>
              <span class="page-ellipsis">...</span>
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
                 class="page-button <?= $page == $i ? 'active' : '' ?>">
                  <?= $i ?>
              </a>
            <?php endfor; ?>

            <!-- Ellipsis if needed -->
            <?php if ($end < $total_pages - 1): ?>
              <span class="page-ellipsis">...</span>
            <?php endif; ?>

            <!-- Last page (if more than 1 page) -->
            <?php if ($total_pages > 1 && $total_pages != $page): ?>
              <a href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" 
                 class="page-button <?= $page == $total_pages ? 'active' : '' ?>">
                  <?= $total_pages ?>
              </a>
            <?php endif; ?>

            <!-- Next page button -->
            <?php if ($page < $total_pages): ?>
              <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
                 class="page-button">
                  &gt;
              </a>
            <?php else: ?>
              <span class="page-button disabled">&gt;</span>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php $conn->close(); ?>
    </div>
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