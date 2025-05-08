<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Connor Young</title>


    <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />

       <script src="https://cdn.tailwindcss.com"></script>

  </head>
  <body style='background-color: #343a40'>
  <?php include 'header.php'; ?>
    <div class="text-white text-center">
        <br><br>

        <?php
        include('db_connection.php');

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Check if the 'game_id' is passed in the URL
        if (isset($_GET['series_id'])) {
          $series_id = $_GET['series_id'];
          // print($series_id);
          $season = substr($series_id, 0, 8);
          $letter = substr($series_id, 8, 1);

          $headerSQL = "SELECT playoff_results.*, away_teams.fullName AS awayTeamName, home_teams.fullName AS homeTeamName,
                        home_teams.teamLogo AS homeLogo, away_teams.teamLogo AS awayLogo, away_teams.id AS awayTeamId, home_teams.id AS homeTeamId,
                        winning_teams.fullName AS winningTeamName, losing_teams.fullName AS losingTeamName
                        FROM playoff_results
                        LEFT JOIN nhl_teams AS home_teams ON playoff_results.winningTeamIds = home_teams.id
                        LEFT JOIN nhl_teams AS away_teams ON playoff_results.losingTeamIds = away_teams.id
                        LEFT JOIN nhl_teams AS winning_teams ON playoff_results.winningTeamIds = winning_teams.id
                        LEFT JOIN nhl_teams AS losing_teams ON playoff_results.losingTeamIds = losing_teams.id
                        WHERE playoff_results.seasonID = '$season' AND playoff_results.seriesLetters = '$letter'";


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
            $playoffYear = substr($season, 4, 8);
            echo "<div class='relative max-w-[95%] mx-auto bg-slate-800 text-white py-6 px-4 rounded-lg shadow-lg mb-8 border-2 border-slate-600'>";

            // Logos positioned to the left and right
            echo "<div class='absolute top-1/2 left-4 transform -translate-y-1/2'>";
            echo "<a href='https://connoryoung.com/team_details.php?team_id=" . htmlspecialchars($row['homeTeamId']) . "'>";
            echo "<img src='" . htmlspecialchars($row['homeLogo']) . "' alt='Home Logo' class='h-[130px]'>";
            echo "</a>";
            echo "</div>";

            echo "<div class='absolute top-1/2 right-4 transform -translate-y-1/2'>";
            echo "<a href='https://connoryoung.com/team_details.php?team_id=" . htmlspecialchars($row['awayTeamId']) . "'>";
            echo "<img src='" . htmlspecialchars($row['awayLogo']) . "' alt='Away Logo' class='h-[130px]'>";
            echo "</a>";
            echo "</div>";

            // Main content centered
            echo "<div class='flex flex-col items-center space-y-3'>";

            echo "<h2 class='text-2xl font-bold text-white text-center'>Playoff Series Details: " . $playoffYear . " Round " . $row['roundNums'] . "</h2>";

            // Team names centered under the title
            echo "<div class='flex items-center space-x-4 text-4xl font-semibold mt-2'>";
            echo "<a href='https://connoryoung.com/team_details.php?team_id=" . htmlspecialchars($row['homeTeamId']) . "'>";
            echo htmlspecialchars($row['homeTeamName']) . " (" . htmlspecialchars($row['topSeedRankAbbrevs']) . ")";
            echo "</a>";
            echo "<span class='mx-2'>vs.</span>";
            echo "<a href='https://connoryoung.com/team_details.php?team_id=" . htmlspecialchars($row['awayTeamId']) . "'>";
            echo htmlspecialchars($row['awayTeamName']) . " (" . htmlspecialchars($row['bottomSeedRankAbbrevs']) . ")";
            echo "</a>";
            echo "</div>";

            // Additional info
            // echo "<p>Best of " . htmlspecialchars($row['length']) . "</p>";
            if ($row['topSeedWins'] > $row['bottomSeedWins']) {
              echo "<p class='text-2xl mt-10'>" . htmlspecialchars($row['winningTeamName']) . " win " . $row['topSeedWins'] . " - " . $row['bottomSeedWins'] . "</p>";
            } else {
              echo "<p class='text-2xl mt-10'>" . htmlspecialchars($row['winningTeamName']) . " win " . $row['bottomSeedWins'] . " - " . $row['topSeedWins'] . "</p>";
            }
            echo "<a href='https://nhl.com" . htmlspecialchars($row['seriesLinks']) . "' class='text-blue-500 hover:underline'>NHL.com Series URL</a>";
            echo "</div>"; // end content container
            echo "</div>"; // end outer wrapper

            
            echo "<p class='text-2xl font-bold'>Game Results</p>";
            echo "<p class='text-sm'>Click on the game number to view the game details.</p><br>";
            echo "<hr style='width:80%; background-color:white' class='mx-auto'>";
          }

          $sql = "SELECT playoff_results.*, nhl_games.gameDate, top_seeds.fullName AS top_name, top_seeds.triCode AS top_tricode, top_seeds.teamLogo AS top_logo, 
          bottom_seeds.fullName AS bottom_name, bottom_seeds.triCode AS bottom_tricode, bottom_seeds.teamLogo AS bottom_logo FROM playoff_results 
          JOIN nhl_games ON playoff_results.gameId = nhl_games.id
          JOIN nhl_teams AS top_seeds ON playoff_results.topSeedIDs = top_seeds.id
          JOIN nhl_teams AS bottom_seeds ON playoff_results.bottomSeedIDs = bottom_seeds.id
          WHERE seasonID = '$season' AND seriesLetters = '$letter'";
          $result = mysqli_query($conn, $sql);

          $topSeedSchedule = ['Home', 'Home', 'Away', 'Away', 'Home', 'Away', 'Home'];
          ?>
          <br>
          <div>
            
            <table class='w-4/5 mx-auto border-separate border-spacing-y-10'>
              <colgroup>
                <col class='gameNumDate-playoff-series-table'>
                <col class='homeTricode-playoff-series-table'>
                <col class='homeLogo-playoff-series-table'>
                <col class='homeScore-playoff-series-table'>
                <col class='gameStatus-playoff-series-table'>
                <col class='awayScore-playoff-series-table'>
                <col class='awayLogo-playoff-series-table'>
                <col class='awayTricode-playoff-series-table'>
                <col class='seriesStatus-playoff-series-table'>
                <col class='gameCenter-playoff-series-table'>
              </colgroup>
              <tbody>

          <?php
          $totalRows = mysqli_num_rows($result);
          $count=1;
          while ($row = mysqli_fetch_assoc($result)) {
            $bracketLogos = $row['bracketLogos'];
            // $topSeedRanks = $row['topSeedRanks'];
            // $topSeedRankAbbrev = $row['topSeedRankAbbrevs'];
            // $bottomSeedRanks = $row['bottomSeedRanks'];
            // $bottomSeedRankAbbrev = $row['bottomSeedRankAbbrevs'];
            $winningTeamID = $row['winningTeamIds'];
            $losingTeamID = $row['losingTeamIds'];
            $gameID = $row['gameId'];
            $gameDate = $row['gameDate'];
            $formattedGameDate = date('F j, Y', strtotime($gameDate));
            $awayTeamScore = $row['awayTeamScore'];
            $homeTeamScore = $row['homeTeamScore'];
            $gameCenterLink = $row['gameCenterLink'];
            $gameCenterLink = "https://nhl.com" . $gameCenterLink;
            $seriesStatusTopSeedWins = $row['seriesStatusTopSeedWins'];
            $seriesStatusBottomSeedWins = $row['seriesStatusBottomSeedWins'];
            $fullCoverageURL = $row['fullCoverageURL'];
            $lastPeriodType = $row['lastPeriodType'];
            $topSeedID = $row['topSeedIDs'];
            $topSeedName = $row['top_name'];
            $topSeedTriCode = $row['top_tricode'];
            $topSeedLogo = $row['top_logo'];
            $bottomSeedID = $row['bottomSeedIDs'];
            $bottomSeedName = $row['bottom_name'];
            $bottomSeedTriCode = $row['bottom_tricode'];
            $bottomSeedLogo = $row['bottom_logo'];

            if ($topSeedSchedule[$count - 1] == 'Home') {
              $homeTeamID = $topSeedID;
              $homeTeamName = $row['top_name'];
              $homeTeamTriCode = $row['top_tricode'];
              $homeTeamLogo = $row['top_logo'];
              $awayTeamID = $bottomSeedID;
              $awayTeamName = $row['bottom_name'];
              $awayTeamTriCode = $row['bottom_tricode'];
              $awayTeamLogo = $row['bottom_logo'];
            } else {
              $homeTeamID = $bottomSeedID;
              $homeTeamName = $row['bottom_name'];
              $awayTeamID = $topSeedID;
              $awayTeamName = $row['top_name'];
              $homeTeamTriCode = $row['bottom_tricode'];
              $homeTeamLogo = $row['bottom_logo'];
              $awayTeamTriCode = $row['top_tricode'];
              $awayTeamLogo = $row['top_logo'];
            }

            echo "<tr>";
            echo "<td><a href='https://connoryoung.com/game_details.php?game_id={$gameID}'>" . "Game " . $count . "<br>" . $formattedGameDate . "</a></td>";
            
            echo "<td><a href='https://connoryoung.com/team_details.php?team_id=" . htmlspecialchars($homeTeamID) . "'>" . $homeTeamTriCode . " (H)</a></td>";
            echo "<td><a href='https://connoryoung.com/team_details.php?team_id=" . htmlspecialchars($homeTeamID) . "'>";
            echo "<img src='" . htmlspecialchars($homeTeamLogo) . "' alt='Home Logo' class='h-[50px]'>";
            echo "</a></td>";
            echo "<td>$homeTeamScore</td>";
            

            if ($lastPeriodType == 'REG') {
              echo "<td>Final</td>";
            } else if ($lastPeriodType == 'OT') {
              echo "<td>Final/OT</td>";
            } else {
              echo "<td>Error</td>";
            }

            echo "<td>$awayTeamScore</td>";
            echo "<td><a href='https://connoryoung.com/team_details.php?team_id=" . htmlspecialchars($awayTeamID) . "'>";
            echo "<img src='" . htmlspecialchars($awayTeamLogo) . "' alt='Home Logo' class='h-[50px]'>";
            echo "</a></td>";
            echo "<td><a href='https://connoryoung.com/team_details.php?team_id=" . htmlspecialchars($awayTeamID) . "'>" . $awayTeamTriCode . " (A)</a></td>";
            

            if ($seriesStatusTopSeedWins > $seriesStatusBottomSeedWins) {
              if ($seriesStatusTopSeedWins == 4) {
                echo "<td>" . $topSeedTriCode . " wins the series " . $seriesStatusTopSeedWins . " - " . $seriesStatusBottomSeedWins . "</td>";
              } else {
                echo "<td>" . $topSeedTriCode . " leads " . $seriesStatusTopSeedWins . " - " . $seriesStatusBottomSeedWins . "</td>";
              }
            } else if ($seriesStatusTopSeedWins < $seriesStatusBottomSeedWins) {
              if ($seriesStatusBottomSeedWins == 4) {
                echo "<td>" . $bottomSeedTriCode . " wins series " . $seriesStatusBottomSeedWins . " - " . $seriesStatusTopSeedWins . "</td>";
              } else {
                echo "<td>" . $bottomSeedTriCode . " leads " . $seriesStatusBottomSeedWins . " - " . $seriesStatusTopSeedWins . "</td>";
              }
            } else {
              echo "<td>Series tied " . $seriesStatusTopSeedWins . " - " . $seriesStatusBottomSeedWins . "</td>";
            }

            echo "<td><a class='text-blue-500 hover:underline' href=" . $gameCenterLink . ">GameCenter</a></td>";
            
        
            echo "</tr>";

            if ($count < $totalRows) {
              echo "<tr><td colspan='10'><div class='border-t border-gray-400 my-4'></div></td></tr>";
          }

            $count+=1;
          }

          echo "</tbody>";
          echo "</table>";
          echo "<hr style='width:80%; background-color:white' class='mx-auto'>";
          echo "<br><br>";


          
        }
?>

    <?php include 'footer.php'; ?>
    </div>
    </div>
    </body>
</html>