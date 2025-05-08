
<?php include('db_connection.php'); ?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../../../favicon.ico">

        <title>Connor Young</title>

        <!-- Bootstrap core CSS -->
        <!-- <link href="../resources/css/bootstrap.min.css" rel="stylesheet"> -->


        <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />

        <script src="https://cdn.tailwindcss.com"></script>

    </head>
    <body>
<!-- Header -->
<?php include 'header.php'; ?>

        <?php

          ini_set('display_errors', 1);
          ini_set('display_startup_errors', 1);
          error_reporting(E_ALL);

    ########## GET ALL TEAM DATA TO USE FOR THE PAGE ##########

        // $teamColors = [
        //   "ANA" => ['#F47A38', '#B9975B', '#C1C6C8', '#000']
        // ]

          // Check if the 'team_id' is passed in the URL
          if (isset($_GET['team_id'])) {
            $team_id = $_GET['team_id'];

            // Query for getting overall season stats for the team
            $overallSQL = "SELECT * FROM team_overall_stats_by_season WHERE teamId = $team_id ORDER BY season_id DESC";
            $overallStatsResult = mysqli_query($conn, $overallSQL);

            // Combined query for skaters (forwards and defensemen)
            // Step 1: Create the temp_forwards table
            $sql1 = "
            CREATE TEMPORARY TABLE temp_forwards AS
            SELECT 
                team_season_rosters.team_id,
                nhl_teams.triCode,
                team_season_rosters.season,
                nhl_players.position,
                exploded_forwards.player_id,
                nhl_players.firstName,
                nhl_players.lastName
            FROM team_season_rosters
            JOIN JSON_TABLE(team_season_rosters.forwards, '$[*]' COLUMNS(player_id INT PATH '$')) AS exploded_forwards
                ON 1=1
            JOIN nhl_players ON nhl_players.playerID = exploded_forwards.player_id
            JOIN nhl_teams ON nhl_teams.id = team_season_rosters.team_id
            WHERE team_season_rosters.team_id = $team_id;
            ";
            mysqli_query($conn, $sql1);

            // Step 2: Create the temp_defensemen table
            $sql2 = "
            CREATE TEMPORARY TABLE temp_defensemen AS
            SELECT 
                team_season_rosters.team_id,
                nhl_teams.triCode,
                team_season_rosters.season,
                nhl_players.position,
                exploded_defensemen.player_id,
                nhl_players.firstName,
                nhl_players.lastName
            FROM team_season_rosters
            JOIN JSON_TABLE(team_season_rosters.defensemen, '$[*]' COLUMNS(player_id INT PATH '$')) AS exploded_defensemen
                ON 1=1
            JOIN nhl_players ON nhl_players.playerID = exploded_defensemen.player_id
            JOIN nhl_teams ON nhl_teams.id = team_season_rosters.team_id
            WHERE team_season_rosters.team_id = $team_id;
            ";
            mysqli_query($conn, $sql2);

            // Step 3: Create the temp_roster table by combining temp_forwards and temp_defensemen
            $sql3 = "
            CREATE TEMPORARY TABLE temp_roster AS
            SELECT * FROM temp_forwards
            UNION ALL
            SELECT * FROM temp_defensemen;
            ";
            mysqli_query($conn, $sql3);

            // Step 4: Run the main query to fetch the results
            $sql4 = "
            SELECT 
                temp_roster.team_id,
                teams.triCode,
                temp_roster.position,
                temp_roster.player_id,
                temp_roster.firstName,
                temp_roster.lastName,
                temp_roster.season,
                CONCAT(temp_roster.season, '-2') as seasonWithType,
                teams.id,
                teams.fullName,
                teams.teamLogo,
                teams.teamColor1,
                teams.teamColor2,
                teams.teamColor3,
                teams.teamColor4,
                teams.teamColor5,
                stats.seasonGamesPlayed,
                stats.seasonGoals,
                stats.seasonAssists,
                stats.seasonPoints, 
                stats.seasonPlusMinus,
                stats.seasonShots,
                stats.seasonShootingPct,
                stats.seasonAvgTOI,
                stats.seasonAvgShifts,
                stats.seasonFOWinPct
            FROM temp_roster AS temp_roster
            LEFT JOIN nhl_teams AS teams ON teams.id = temp_roster.team_id
            LEFT JOIN team_season_stats AS stats 
                ON stats.teamID = temp_roster.team_id 
                AND stats.playerID = temp_roster.player_id 
                AND CONCAT(temp_roster.season, '-2') = stats.seasonID
            ORDER BY temp_roster.season DESC, temp_roster.lastName
            ";
            $result_skaters_combined = mysqli_query($conn, $sql4);

            // Step 6: Drop temporary tables after use
            mysqli_query($conn, "DROP TEMPORARY TABLE IF EXISTS temp_forwards");
            mysqli_query($conn, "DROP TEMPORARY TABLE IF EXISTS temp_defensemen");
            mysqli_query($conn, "DROP TEMPORARY TABLE IF EXISTS temp_roster");

  
            // Step 1: Create the temp_goalies table
            $sql1 = "
            CREATE TEMPORARY TABLE temp_goalies AS
            SELECT 
                team_season_rosters.team_id,
                nhl_teams.triCode,
                team_season_rosters.season,
                CAST('goalie' AS VARCHAR(10)) AS position,
                exploded_goalies.player_id,
                nhl_players.firstName,
                nhl_players.lastName
            FROM team_season_rosters
            JOIN JSON_TABLE(team_season_rosters.goalies, '$[*]' COLUMNS(player_id INT PATH '$')) AS exploded_goalies
                ON 1=1
            JOIN nhl_players ON nhl_players.playerID = exploded_goalies.player_id
            JOIN nhl_teams ON nhl_teams.id = team_season_rosters.team_id
            WHERE team_season_rosters.team_id = $team_id
            ";

            mysqli_query($conn, $sql1);
  
  
            // Step 4: Run the main query to fetch the results
            $sql4 = "
            SELECT 
            teams.triCode,
            temp_goalies.position,
            temp_goalies.player_id,
            temp_goalies.firstName,
            temp_goalies.lastName,
            temp_goalies.season,
            CONCAT(temp_goalies.season, '-2') as seasonWithType,
            teams.id,
            teams.fullName,
            teams.teamLogo,
            teams.teamColor1,
            teams.teamColor2,
            teams.teamColor3,
            teams.teamColor4,
            teams.teamColor5,
            stats.seasonGamesPlayed,
            stats.seasonGS,
            stats.seasonWins,
            stats.seasonLosses,
            stats.seasonTies,
            stats.seasonOTLosses,
            stats.seasonGAA,
            stats.seasonSavePct,
            stats.seasonSA,
            stats.seasonSaves,
            stats.seasonGA,
            stats.seasonSO,
            stats.seasonTOI
            FROM temp_goalies
            LEFT JOIN nhl_teams AS teams ON teams.id = temp_goalies.team_id
            LEFT JOIN team_season_stats AS stats 
                ON stats.teamID = temp_goalies.team_id 
                AND stats.playerID = temp_goalies.player_id 
                AND CONCAT(temp_goalies.season, '-2') = stats.seasonID
            ORDER BY temp_goalies.season DESC, temp_goalies.lastName
            ";
            $result_goalies_combined = mysqli_query($conn, $sql4);
  
            // Step 6: Drop temporary tables after use
            mysqli_query($conn, "DROP TEMPORARY TABLE IF EXISTS temp_goalies");
              
            // $result_skaters_combined = mysqli_query($conn, $skaters_combined_sql);
            // $result_goalies_combined = mysqli_query($conn, $goalies_combined_sql);

              
            // $result_skaters = mysqli_query($conn, $skaters_sql);
            // $result_goalies = mysqli_query($conn, $goalies_sql);
            // $result_rosters = mysqli_query($conn, $rosters_sql);

            if (!$result_skaters_combined) {
              die("Query failed: " . mysqli_error($conn));
          } elseif (mysqli_num_rows($result_skaters_combined) == 0) {
              echo "No players found for this team.";
          } else {
              // Fetch the row to get the team logo and build header
              $team = mysqli_fetch_assoc($result_skaters_combined);

              $teamColor1 = $team['teamColor1'];
              $teamColor2 = $team['teamColor2'];
              $teamColor3 = $team['teamColor3'];
              $teamColor4 = $team['teamColor4'];
              $teamColor5 = $team['teamColor5'];
              

              function getTextColorForBackground($bgColorHex) {
                  // Remove the hash if present
                  $bgColorHex = ltrim($bgColorHex, '#');
                  
                  // Split into R, G, B
                  $r = hexdec(substr($bgColorHex, 0, 2));
                  $g = hexdec(substr($bgColorHex, 2, 2));
                  $b = hexdec(substr($bgColorHex, 4, 2));
                  
                  // Calculate luminance (brightness)
                  $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
                  
                  // Return black or white depending on brightness
                  return ($brightness > 128) ? '#000000' : '#FFFFFF';
              }
              
              $teamColor1Contrast = getTextColorForBackground($teamColor1);  // Contrast color for teamColor1
              $teamColor2Contrast = getTextColorForBackground($teamColor2);  // Contrast color for teamColor2
              ?>
              
              <div class="full-page-content-container" style='background-color:#343a40'>
                  <?php
                  echo "<div style='padding-left: 10px; padding-right: 10px; padding-top: 25px'>";
                  
                  // Flexbox for player name/number/active/id and headshot/team logo - aligns them side-by-side
                  echo "<div class='team-header' style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; 
                        border: 2px solid $teamColor1; padding-left: 15px; padding-right: 15px; padding-top: 10px; padding-bottom: 10px; 
                        background-color: $teamColor2; border-radius: 10px; width: 70%; margin: auto;'>";
              
                  // Left side: Team Name
                  echo "<div>";
                  
                  // Set the text color dynamically based on background contrast
                  echo "<h1 class='text-2xl' style='margin-top: 3px; color: $teamColor2Contrast;'>Team Details: </h1>";
                  echo "<h1 class='text-4xl' style='color: $teamColor2Contrast'>" . $team['fullName'] . "</h1>";
                  echo "</div>";
              
                  // Right side: Team Logo
                  $teamLogo = $team['teamLogo'];
                  echo "<div style='display: flex; align-items: center; gap: 5px'>";
                  if ($teamLogo != 'false' && $teamLogo != '' && $teamLogo != 'N/A') {
                      echo "<img src='" . htmlspecialchars($teamLogo) . "' alt='team logo' style='height: 120px'>";
                  } else {
                      echo "<p>No Logo</p>"; // Add fallback message if no logo
                  }
                  echo "</div>";
              
                  echo "</div>"; // Close team-header div
                  echo "</div>"; // Close padding div
                
              
              mysqli_data_seek($result_skaters_combined, 0); // Reset the result pointer to the first row
                }

              // Step 1: Get all unique seasons for the dropdown
              $seasons = [];
              // Get seasons from skaters
              mysqli_data_seek($result_skaters_combined, 0);
              while ($row = mysqli_fetch_assoc($result_skaters_combined)) {
                  $seasonID = $row['season'];
                  $seasonWithType = $row['seasonWithType']; // Format: 20242025-2
                  if (!in_array($seasonWithType, $seasons)) {
                      $seasons[] = $seasonWithType;
                  }
              }
              
              // Get seasons from goalies
              mysqli_data_seek($result_goalies_combined, 0);
              while ($row = mysqli_fetch_assoc($result_goalies_combined)) {
                  $seasonWithType = $row['seasonWithType']; // Format: 20242025-2
                  if (!in_array($seasonWithType, $seasons)) {
                      $seasons[] = $seasonWithType;
                  }
              }
              
              rsort($seasons); // Sort seasons in descending order to show the latest season first
              ?>
              <br><br>

              <!-- Step 2: Add Dropdown for Season Selection -->
              <div class="mx-auto w-fit px-6 py-4 border rounded-md text-black flex items-center space-x-4"
                  style="border: 2px solid <?php echo $teamColor2; ?>; background-color: <?php echo $teamColor1; ?>;">

                  <label for="seasonDropdown" class="text-base flex" style='color: <?php echo $teamColor1Contrast; ?>; margin-right: 10px;'>
                  Filter by Season: 
                  </label>

              <select id="seasonDropdown" class="border rounded px-3 py-2 text-base"
                      style="border-color: <?php echo $teamColor2; ?>;" onchange="updateSeason()">
                  <?php foreach ($seasons as $seasonID): ?>
                      <?php 
                          $seasonYear1 = substr($seasonID, 0, 4);
                          $seasonYear2 = substr($seasonID, 4, 4);
                      ?>
                      <option value="<?php echo $seasonID; ?>">
                          <?php echo $seasonYear1 . "-" . $seasonYear2; ?>
                      </option>
                  <?php endforeach; ?>
              </select>

</div>
<br><br>

              <div class="max-w-[90%] mx-auto overflow-x-auto">
              <!-- OVERALL TEAM STATS BY SEASON -->
              <div>
                <h2 class="text-2xl text-center text-white">Overall Team Stats</h2>
                <table class='default-zebra-table overall-team-stats-table text-center min-w-[900px]'>
                  <colgroup>
                    <col class='overall-team-stats-season'>
                    <col class='overall-team-stats-gp'>
                    <col class='overall-team-stats-w'>
                    <col class='overall-team-stats-l'>
                    <col class='overall-team-stats-otl'>
                    <col class='overall-team-stats-pts'>
                    <col class='overall-team-stats-t'>
                    <col class='overall-team-stats-reg-wins'>
                    <col class='overall-team-stats-ot-wins'>
                    <col class='overall-team-stats-so-wins'>
                    <col class='overall-team-stats-fo-win-pct'>
                    <col class='overall-team-stats-sa-gp'>
                    <col class='overall-team-stats-sf-gp'>
                    <col class='overall-team-stats-gf'>
                    <col class='overall-team-stats-gf-gp'>
                    <col class='overall-team-stats-ga'>
                    <col class='overall-team-stats-ga-gp'>
                    <col class='overall-team-stats-pk-pct'>
                    <col class='overall-team-stats-pt-pct'>
                    <col class='overall-team-stats-pp-net-pct'>
                    <col class='overall-team-stats-pp-pct'>
                  </colgroup>
                  <thead style='background-color: <?php echo $teamColor1; ?>; color: <?php echo $teamColor1Contrast; ?>'>
                    <tr>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Season</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GP</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>W</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>L</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>OTL</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Pts</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>T</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Reg W</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>OT W</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>SO W</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>FOW %</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>SA / G</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>SF / G</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GF</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GF / G</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GA</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GA / G</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>PK %</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Pt %</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>PP Net %</th>
                      <th class='border' style='border-color: <?php echo $teamColor2; ?>'>PP %</th>
                  </tr>
                  </thead>
                  <tbody id='overallStatsTable'>

              <?php
              while ($row = mysqli_fetch_assoc($overallStatsResult)) {
                
                $overallSeason = $row['season_id'];
                $overallGP = $row['gamesPlayed'];
                $overallW = $row['wins'];
                $overallL = $row['losses'];
                $overallOTL = $row['otLosses'];
                $overallPts = $row['points'];
                if ($row['ties'] == null) {
                    $overallTies = 0;
                } else {
                    $overallTies = $row['ties'];
                }
                $overallRegWins = $row['winsInRegulation'];
                $overallRegOTWins = $row['regulationAndOtWins'];
                $overallSOWins = $row['winsInShootout'];
                $overallFOWinPct = $row['faceoffWinPct'];
                $overallShotsAgainstPerGame = $row['shotsAgainstPerGame'];
                $overallShotsForPer = $row['shotsForPerGame'];
                $overallGF = $row['goalsFor'];
                $overallGFper = $row['goalsForPerGame'];
                $overallGA = $row['goalsAgainst'];
                $overallGAper = $row['goalsAgainstPerGame'];
                $overallPKPct = $row['penaltyKillPct'];
                $overallPtPct = $row['pointPct'];
                $overallPPNetPct = $row['powerPlayNetPct'];
                $overallPPPct = $row['powerPlayPct'];

                # derived variables
                $overallOTWins = $overallRegOTWins - $overallRegWins; // Overtime Wins = Reg OT Wins - Reg Wins
                $overall_totalWins = $overallW + $overallRegWins + $overallSOWins;
                # Corsi For = (Corsi For) / (Corsi For + Corsi Against) * 100 = Total Shot Attempts (for) / Total Shot Attempts (for + against) * 100
                # = 
                // $corsi_for
                

                echo "<tr data-season='$overallSeason'>";
                echo "<td class='border' style='border-color: $teamColor2'>" . substr($overallSeason, 0, 4) . "-" . substr($overallSeason, 4, 4) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallGP . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallW . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallL . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallOTL . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallPts . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallTies . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallRegWins . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallOTWins . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallSOWins . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format($overallFOWinPct,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format($overallShotsAgainstPerGame,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format($overallShotsForPer,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallGF . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format($overallGFper,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallGA . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format($overallGAper,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format($overallPKPct,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format($overallPtPct,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format($overallPPNetPct,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format($overallPPPct,2) . "</td>";
                echo "</tr>";

            }

            ?>
                </tbody>
              </table>
              </div>
                <!-- SKATERS COMBINED TABLE -->
              
                  <div>
                  <br>
                    <h2 class="text-2xl text-center text-white">Individual Player Stats</h2>
                    <table class='skaters-combined-table default-zebra-table min-w-[900px]' style="border: 2px solid <?php echo $teamColor2; ?>;">
                    <colgroup>
                    <col class='skaters-combined-season'>
                    <col class='skaters-combined-name'>
                    <col class='skaters-combined-position'>
                    <col class='skaters-combined-gp'>
                    <col class='skaters-combined-g'>
                    <col class='skaters-combined-a'>
                    <col class='skaters-combined-p'>
                    <col class='skaters-combined-plus-minus'>
                    <col class='skaters-combined-shots'>
                    <col class='skaters-combined-shot-pct'>
                    <col class='skaters-combined-avg-toi'>
                    <col class='skaters-combined-avg-shifts'>
                    <col class='skaters-combined-fo-pct'>
                      </colgroup>
                    <thead style='background-color: <?php echo $teamColor1; ?> !important; color: <?php echo $teamColor1Contrast; ?>'>
                            <tr data-season='$seasonWithType'>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Season</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Name</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Pos.</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GP</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>G</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>A</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>P</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>+/-</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Shots</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Shot %</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Avg TOI</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Avg Shifts</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>FO %</th>
                            </tr>
                        </thead>
                        <tbody id='skaterStatsTable'>
                            <?php
                            mysqli_data_seek($result_skaters_combined, 0);
                            while ($row = mysqli_fetch_assoc($result_skaters_combined)) {
                                $seasonID = $row['season'];
                                $seasonWithType = $row['seasonWithType']; // Format: 20242025-2
                                $playerID = $row['player_id'];
                                $firstName = $row['firstName'];
                                $lastName = $row['lastName'];
                                
                                // Format position display
                                $position = $row['position'];
                                if ($position == 'R') {
                                    $positionDisplay = 'RW';
                                } else if ($position == 'L') {
                                    $positionDisplay = 'LW';
                                } else if ($position == 'C') {
                                    $positionDisplay = 'C';
                                } else if ($position == 'D') {
                                    $positionDisplay = 'D';
                                } else {
                                    $positionDisplay = $position; // Keep original value if not a forward or defenseman
                                }
                                
                                // Extract season years for display
                                $seasonYear1 = substr($seasonID, 0, 4);
                                $seasonYear2 = substr($seasonID, 4, 4);
                                
                                echo "<tr data-season='$seasonWithType'>"; // For filtering by season with type
                                echo "<td class='border' style='border-color: $teamColor2'>" . $seasonYear1 . "-" . $seasonYear2 . "</td>";  // Season display
                                echo "<td class='border' style='border-color: $teamColor2'><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $playerID . "'>" . $firstName . " " . $lastName . "</a></td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . $positionDisplay . "</td>";
                                
                                // Display stats if available, otherwise show dash
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonGamesPlayed'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonGoals'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonAssists'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonPoints'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonPlusMinus'] !== null && $row['seasonPlusMinus'] !== '' ? $row['seasonPlusMinus'] : "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonShots'] ?? "-") . "</td>";
                                
                                // Handle percentages and formatting
                                if (isset($row['seasonShootingPct'])) {
                                    echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float) $row['seasonShootingPct']*100, 1) . "</td>";
                                } else {
                                    echo "<td class='border' style='border-color: $teamColor2'>-</td>";
                                }
                                
                                // Format time on ice if available
                                if (isset($row['seasonAvgTOI'])) {
                                    echo "<td class='border' style='border-color: $teamColor2'>" . gmdate("i:s", (int) $row['seasonAvgTOI']) . "</td>";
                                } else {
                                    echo "<td class='border' style='border-color: $teamColor2'>-</td>";
                                }
                                
                                // Format shifts
                                if (isset($row['seasonAvgShifts'])) {
                                    echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float) $row['seasonAvgShifts'], 1) . "</td>";
                                } else {
                                    echo "<td class='border' style='border-color: $teamColor2'>-</td>";
                                }
                                
                                // Format faceoff percentage
                                if (isset($row['seasonFOWinPct'])) {
                                    echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float) $row['seasonFOWinPct']*100, 1) . "</td>";
                                } else {
                                    echo "<td class='border' style='border-color: $teamColor2'>-</td>";
                                }
                                
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                          </div>
                <!-- GOALIES COMBINED TABLE -->

                <div>
                <div class="shadow-md rounded-lg">
                    <table class='goalies-combined-table default-zebra-table text-center min-w-[900px]' style='color: black; border: 2px solid <?php echo $teamColor2; ?>;'>
                    <colgroup>
                    <col class='goalies-combined-season'>
                    <col class='goalies-combined-name'>
                    <col class='goalies-combined-gp'>
                    <col class='goalies-combined-gs'>
                    <col class='goalies-combined-w'>
                    <col class='goalies-combined-l'>
                    <col class='goalies-combined-t'>
                    <col class='goalies-combined-otl'>
                    <col class='goalies-combined-gaa'>
                    <col class='goalies-combined-sv'>
                    <col class='goalies-combined-sa'>
                    <col class='goalies-combined-saves'>
                    <col class='goalies-combined-ga'>
                    <col class='goalies-combined-so'>
                    <col class='goalies-combined-toi'>
                    </colgroup>
                    <thead style='color: <?php echo $teamColor1Contrast; ?>'>
                            <tr style='background-color: <?php echo $teamColor1; ?>; border: 2px solid <?php echo $teamColor2; ?>; color: <?php echo $teamColor2; ?>'>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Season</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Name</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GP</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GS</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>W</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>L</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>T</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>OTL</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GAA</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Sv. %</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>SA</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Saves</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GA</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>SO</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>TOI (min)</th>
                            </tr>
                        </thead>
                        <tbody id='goalieStatsTable'>
                            <?php
                            mysqli_data_seek($result_goalies_combined, 0);
                            while ($row = mysqli_fetch_assoc($result_goalies_combined)) {
                                $seasonID = $row['season'];
                                $seasonWithType = $row['seasonWithType']; // Format: 20242025-2
                                $playerID = $row['player_id'];
                                $firstName = $row['firstName'];
                                $lastName = $row['lastName'];
                                
                                // Extract season years for display
                                $seasonYear1 = substr($seasonID, 0, 4);
                                $seasonYear2 = substr($seasonID, 4, 4);
                                
                                echo "<tr data-season='$seasonWithType'>"; // For filtering by season with type
                                echo "<td class='border' style='border-color: $teamColor2'>" . $seasonYear1 . "-" . $seasonYear2 . "</td>";  // Season
                                echo "<td class='border' style='border-color: $teamColor2'><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $playerID . "'>" . $firstName . " " . $lastName . "</a></td>";
                                
                                // Display stats if available, otherwise show dash
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonGamesPlayed'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonGS'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonWins'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonLosses'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonTies'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonOTLosses'] ?? "-") . "</td>";
                                
                                // Format GAA
                                if (isset($row['seasonGAA'])) {
                                    echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float) $row['seasonGAA'], 2) . "</td>";
                                } else {
                                    echo "<td class='border' style='border-color: $teamColor2'>-</td>";
                                }
                                
                                // Format save percentage
                                if (isset($row['seasonSavePct'])) {
                                    echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float) $row['seasonSavePct'], 3) . "</td>";
                                } else {
                                    echo "<td class='border' style='border-color: $teamColor2'>-</td>";
                                }
                                
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonSA'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonSaves'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonGA'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonSO'] ?? "-") . "</td>";
                                
                                // Format TOI
                                if (isset($row['seasonTOI'])) {
                                    echo "<td class='border' style='border-color: $teamColor2'>" . gmdate("i:s", (int) $row['seasonTOI']) . "</td>";
                                } else {
                                    echo "<td class='border' style='border-color: $teamColor2'>-</td>";
                                }
                                
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                          </div>
                </div>

            
            

            <?php
            echo "<br><br>";
            echo "<div>";
                $draftSQL = "SELECT * FROM draft_history WHERE teamID = $team_id";

                $draftResult = mysqli_query($conn, $draftSQL);
                echo "<h3 class='text-2xl text-center text-white'>Draft Picks</h3><br>";
                echo "<div class='team-draft-history-table-container'>";
                echo "<table class='team-draft-history-table default-zebra-table text-center mx-auto' style='border: 2px solid $teamColor2; width: 90%;'>";
                echo "<colgroup>";
                    echo "<col class='draft-year'>";
                    echo "<col class='draft-round'>";
                    echo "<col class='draft-pick-in-round'>";
                    echo "<col class='draft-overall-pick'>";
                    echo "<col class='draft-player-name'>";
                    echo "<col class='draft-player-position'>";
                    echo "<col class='draft-player-country'>";
                    echo "<col class='draft-player-id'>";
                echo "</colgroup>";
                echo "<thead style='background-color: $teamColor1; color: $teamColor1Contrast;'>";
                echo "<tr>";
                    echo "<th class='border' style='border-color: $teamColor2'>Year</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Round</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Pick In Round</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Overall Pick</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Player Name</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Position</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Country</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>ID</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody id='draftHistoryTable'>";

                while ($row = mysqli_fetch_assoc($draftResult)) {
                    // print_r($row);
                    $draftPlayerID = $row['playerId'];
                    echo $draftPlayerID;
                    $draftYear = $row['draftYear'];
                    $draftRound = $row['round'];
                    $draftPickInRound = $row['pickInRound'];
                    $draftPickOvr = $row['overallPick'];
                    $draftPlayerFirstName = $row['firstName'];
                    $draftPlayerLastName = $row['lastName'];
                    $draftPlayerName = $draftPlayerFirstName . " " . $draftPlayerLastName;
                    $draftPlayerPosition = $row['position'];
                    $draftPlayerCountry = $row['country'];

                    echo "<tr data-season='$draftYear'>";
                        echo "<td class='border' style='border-color: $teamColor2'>" . $draftYear . "</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>" . $draftRound . "</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>" . $draftPickInRound . "</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>" . $draftPickOvr . "</td>";
                        echo "<td class='border' style='border-color: $teamColor2'> ". $draftPlayerName . "</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>" . $draftPlayerPosition . "</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>" . $draftPlayerCountry . "</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>" . $draftPlayerID . "</td>";
                    
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
            echo "</div>";





            ### PROSPECTS ###
            echo "<br><br>";
            echo "<div>";
                $prospectSQL = "SELECT team_prospects.*, nhl_players.sweaterNumber, nhl_players.firstName, nhl_players.lastName, nhl_players.position FROM team_prospects LEFT JOIN nhl_players ON team_prospects.prospect_id=nhl_players.playerId WHERE team_id = $team_id";
                $prospectResult = mysqli_query($conn, $prospectSQL);
                echo "<h3 class='text-2xl text-center text-white'>Current Prospects</h3><br>";
                echo "<div class='team-prospects-table-container'>";
                echo "<table class='team-prospects-table default-zebra-table text-center mx-auto' style='border: 2px solid $teamColor2; width: 90%;'>";
                echo "<colgroup>";
                    echo "<col class='prospect-id'>";
                    echo "<col class='prospect-name'>";
                    // echo "<col class='prospect-position'>";
                    // echo "<col class='prospect-age'>";
                    // echo "<col class='prospect-height'>";
                    // echo "<col class='prospect-weight'>";
                    // echo "<col class='prospect-country'>";
                echo "</colgroup>";
                echo "<thead style='background-color: $teamColor1; color: $teamColor1Contrast;'>";
                echo "<tr>";
                    echo "<th class='border' style='border-color: $teamColor2'>Prospect ID</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Name</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Number</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Position</th>";
                    // echo "<th class='border' style='border-color: $teamColor2'>Age</th>";
                    // echo "<th class='border' style='border-color: $teamColor2'>Height</th>";
                    // echo "<th class='border' style='border-color: $teamColor2'>Weight</th>";
                    // echo "<th class='border' style='border-color: $teamColor2'>Country</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody id='prospectTable'>";
                while ($row = mysqli_fetch_assoc($prospectResult)) {
                    // print_r($row);
                    $prospectID = $row['prospect_id'];
                    $firstName = $row['firstName'];
                    // echo 'first' . $firstName;
                    $lastName = $row['lastName'];
                    // echo 'last'.  $lastName;
                    $number = $row['sweaterNumber'];
                    $position = $row['position'];
                    // $prospectAge = $row['age'];
                    // $prospectHeight = $row['height'];
                    // $prospectWeight = $row['weight'];
                    // $prospectCountry = $row['country'];

                    echo "<tr>";
                        echo "<td class='border' style='border-color: $teamColor2'>$prospectID</td>";
                        echo "<td class='border' style='border-color: $teamColor2'><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $prospectID . "'>" . $firstName . " " . $lastName . "</a></td>";
                        echo "<td class='border' style='border-color: $teamColor2'>$number</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>$position</td>";
                        // echo "<td class='border' style='border-color: $teamColor2'>$prospectAge</td>";
                        // echo "<td class='border' style='border-color: $teamColor2'>$prospectHeight</td>";
                        // echo "<td class='border' style='border-color: $teamColor2'>$prospectWeight</td>";
                        // echo "<td class='border' style='border-color: $teamColor2'>$prospectCountry</td>";

                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
            echo "</div>";




        } else {
            echo "<div class='container'><div class='alert alert-warning'>No team ID provided. Please select a team.</div></div>";
        }
        // Close database connection
        mysqli_close($conn);
          ?>
          <div>
          <br>
          <hr style='border-color: <?php echo $teamColor1 ?>; width: 70%; margin: auto; border-width: 2px;'>
          <br>
          
          
          <p class='text-white text-center font-semibold '>Select any team below to view details:</p>
              <div class="w-2/5 mx-auto text-center footer-teams text-center">
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=24'>ANA</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=53'>ARI</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=6'>BOS</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=7'>BUF</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=20'>CGY</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=12'>CAR</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=16'>CHI</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=21'>COL</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=29'>CBJ</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=25'>DAL</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=17'>DET</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=22'>EDM</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=13'>FLA</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=26'>LAK</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=30'>MIN</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=8'>MTL</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=1'>NJD</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=18'>NSH</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=2'>NYI</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=3'>NYR</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=9'>OTT</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=4'>PHI</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=5'>PIT</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=28'>SJS</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=55'>SEA</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=19'>STL</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=14'>TBL</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=10'>TOR</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=59'>UTA</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=23'>VAN</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=54'>VGK</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=52'>WPG</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a class='text-white' href='https://connoryoung.com/team_details.php?team_id=15'>WSH</a>
              </div>
    

          <br>            
          <hr style='border-color: <?php echo $teamColor1 ?>; width: 70%; margin: auto; border-width: 2px;'>
          <br>
          </div>
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
            <script>
                // Make sure the DOM is fully loaded before running the script
                document.addEventListener("DOMContentLoaded", function() {
                    function updateSeason() {
                        // Get the selected season from the dropdown
                        var selectedSeason = document.getElementById("seasonDropdown").value;

                        // Extract the season years from the selected value
                        var seasonYear1 = selectedSeason.substr(0, 4);
                        var seasonYear2 = selectedSeason.substr(4, 4);

                        // Update the <h3> element with the selected season
                        document.getElementById("seasonTitle").textContent = "Skaters " + seasonYear1 + "-" + seasonYear2;
                    }

                    // Trigger the updateSeason function on page load to match the initial dropdown value
                    updateSeason();

                    // Add event listener for the dropdown change
                    document.getElementById("seasonDropdown").addEventListener("change", updateSeason);
                });
            </script>
            <script>
                  document.addEventListener('DOMContentLoaded', function () {
                  const dropdown = document.getElementById('seasonDropdown');
                  const skaterRows = document.querySelectorAll('#skaterStatsTable tr');
                  const goalieRows = document.querySelectorAll('#goalieStatsTable tr');
                  const rosterRows = document.querySelectorAll('#seasonRosterTable tr');
                  const overallRows = document.querySelectorAll('#overallStatsTable tr');
                  const draftRows = document.querySelectorAll('#draftHistoryTable tr');


                  // // Debug information
                  // console.log('Season dropdown:', dropdown ? dropdown.value : 'Not found');
                  // console.log('Skater rows found:', skaterRows.length);
                  // console.log('Goalie rows found:', goalieRows.length);
                  // console.log('Roster rows found:', rosterRows.length);

                  // // Debug data attributes
                  // console.log('Skater seasons:', Array.from(skaterRows).map(row => row.dataset.season).filter(Boolean));
                  // console.log('Goalie seasons:', Array.from(goalieRows).map(row => row.dataset.season).filter(Boolean));
                  // console.log('Roster seasons:', Array.from(rosterRows).map(row => row.dataset.season).filter(Boolean));

                  // Function to filter rows by season
                  function filterTableBySeason(seasonID) {
                      console.log("Filtering by season:", seasonID);

                      const baseSeasonID = seasonID.split('-')[0]; // "20242025-2" becomes "20242025" - needed for roster table filtering

                      // Filter skater rows
                      skaterRows.forEach(row => {
                          if (row.dataset.season === seasonID) {
                              row.style.display = ''; // Show row
                          } else {
                              row.style.display = 'none'; // Hide row
                          }
                      });
                      
                      // Filter goalie rows
                      goalieRows.forEach(row => {
                          if (row.dataset.season === seasonID || row.classList.contains('no-data-row')) {
                              row.style.display = ''; // Show row
                          } else {
                              row.style.display = 'none'; // Hide row
                          }
                      });

                      // Filter roster rows
                      rosterRows.forEach(row => {
                          if (row.dataset.season === seasonID || row.dataset.season === baseSeasonID) {
                              row.style.display = ''; // Show row
                              alert(row.dataset.season);
                          } else {
                              row.style.display = 'none'; // Hide row
                              
                          }
                      });

                      // Filter overall rows
                      overallRows.forEach(row => {
                          if (row.dataset.season === seasonID || row.dataset.season === baseSeasonID) {
                            row.style.display = ''; // Show row
                          } else {
                              row.style.display = 'none'; // Hide row
                            }
                      });

                      // Filter draft rows
                      draftRows.forEach(row => {
                            const draftYear = row.dataset.season; // Use draftYear directly
                            const selectedYear = seasonID.substring(0, 4); // Extract the first year of the season
                            if (draftYear === selectedYear) {
                                row.style.display = ''; // Show row
                            } else {
                                row.style.display = 'none'; // Hide row
                            }
                        });
                  }

                  // Set default season to the first option in the dropdown
                  if (dropdown) {
                      const defaultSeason = dropdown.value;
                      console.log("Setting default season:", defaultSeason);
                      filterTableBySeason(defaultSeason);

                      // Add event listener to dropdown
                      dropdown.addEventListener('change', function () {
                          console.log("Dropdown changed to:", this.value);
                          filterTableBySeason(this.value);
                      });
                  } else {
                      console.error("Season dropdown not found!");
                  }

                      // Handle season selection change
    document.getElementById('seasonDropdown').addEventListener('change', function () {
        const selectedSeason = this.value;
        const seasonYear1 = selectedSeason.substring(0, 4); // Extract first 4 digits
        const seasonYear2 = selectedSeason.substring(4, 8); // Extract last 4 digits

        // Update the Skaters table header
        const skatersHeader = document.getElementById('skatersHeader');
        skatersHeader.textContent = `Skaters ${seasonYear1}-${seasonYear2}`;

        // Optionally, you can add logic here to load data dynamically or filter table rows
        // based on the selected season.
    });
              });
                          </script>
    </body>
</html>