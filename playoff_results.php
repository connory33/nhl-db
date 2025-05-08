<!doctype html>
<html lang="en" class="min-h-screen">
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

  <body>

    <?php include 'header.php'; ?>

    <div class='text-white text-center w-full overflow-x-auto' style='background-color: #343a40'> <!-- Open full page div -->
      <br>

      <?php
      include('db_connection.php');

      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);

      if (isset($_GET['season_id'])) {
          $season_id = $_GET['season_id'];
          $currentSeason = $_GET['season_id'] ?? '20232024';
          $seasonYear1 = substr($currentSeason, 0, 4);
          $seasonYear2 = substr($currentSeason, 4, 4);

          $seasons = ['19171918', '19181919', '19201921', '19211922', '19221923', '19231924', '19241925', '19251926', '19261927', '19271928',
              '19281929', '19291930', '19301931', '19311932', '19321933', '19331934', '19341935', '19351936', '19361937', '19371938',
              '19381939', '19391940', '19401941', '19411942', '19421943', '19431944', '19441945', '19451946', '19461947', '19471948',
              '19481949', '19491950', '19501951', '19511952', '19521953', '19531954', '19541955', '19551956', '19561957', '19571958',
              '19581959', '19591960', '19601961', '19611962', '19621963', '19631964', '19641965', '19651966', '19661967', '19671968',
              '19681969', '19691970', '19701971', '19711972', '19721973', '19731974', '19741975', '19751976', '19761977', '19771978',
              '19781979', '19791980', '19801981', '19811982', '19821983', '19831984', '19841985', '19851986', '19861987', '19871988',
              '19881989', '19891990', '19901991', '19911992', '19921993', '19931994', '19941995', '19951996', '19961997', '19971998',
              '19981999', '19992000', '20002001', '20012002', '20022003', '20032004', '20042005', '20052006', '20062007', '20072008',
              '20082009', '20092010', '20102011', '20112012', '20122013', '20132014', '20142015', '20152016', '20162017', '20172018',
              '20182019', '20192020', '20202021', '20212022', '20222023', '20232024', '20242025'];

          $seasons = array_reverse($seasons);
        ?>

        <!-- Dropdown -->
        <div class="mx-auto w-fit px-6 py-4 rounded-md text-black flex items-center space-x-4 border border-slate-600 bg-slate-800">
          <label for="seasonSelect" class="mr-2 text-white font-semibold">Select Season:</label>
          <select id="seasonSelect" class="px-3 py-1 rounded text-black" onchange="changeSeason(this.value)">
            <option value="">Season</option>
            <?php foreach ($seasons as $seasonID): 
                $seasonYear1 = substr($seasonID, 0, 4);
                $seasonYear2 = substr($seasonID, 4, 4);
                $selected = ($seasonID === $currentSeason) ? 'selected' : '';
            ?>
              <option value="<?php echo $seasonID; ?>" <?php echo $selected; ?>>
                <?php echo $seasonYear1 . "-" . $seasonYear2; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      <br>
      <hr class='w-4/5 border-white align-center mx-auto'>

      <!-- Bracket Header -->
      <h2 class="text-2xl font-bold mb-4 text-white text-center">
        Playoff Bracket (<?php echo $seasonYear1 . '-' . $seasonYear2; ?>)
      </h2>

      <script>
        function changeSeason(seasonId) {
          if (seasonId) {
            const url = new URL(window.location.href);
            url.searchParams.set('season_id', seasonId);
            window.location.href = url.toString();
          }
        }
      </script>

      <?php
      $sql = "SELECT playoff_results.*, 
                     bottomSeedTeam.id AS bottomSeedTeamID,
                     bottomSeedTeam.fullName AS bottomSeedTeamName,
                     bottomSeedTeam.triCode AS bottomSeedTeamTriCode,
                     bottomSeedTeam.teamLogo AS bottomSeedTeamLogo,
                     bottomSeedTeam.teamColor1 AS bottomSeedTeamColor1,
                     bottomSeedTeam.teamColor2 AS bottomSeedTeamColor2,
                     bottomSeedTeam.division AS bottomSeedTeamDivision,
                     topSeedTeam.id AS topSeedTeamID,
                     topSeedTeam.fullName AS topSeedTeamName,
                     topSeedTeam.triCode AS topSeedTeamTriCode,
                     topSeedTeam.teamLogo AS topSeedTeamLogo,
                     topSeedTeam.teamColor1 AS topSeedTeamColor1,
                     topSeedTeam.teamColor2 AS topSeedTeamColor2,
                     topSeedTeam.division AS topSeedTeamDivision
              FROM playoff_results
              LEFT JOIN nhl_teams AS bottomSeedTeam ON playoff_results.bottomSeedIDs = bottomSeedTeam.id
              LEFT JOIN nhl_teams AS topSeedTeam ON playoff_results.topSeedIDs = topSeedTeam.id
              WHERE playoff_results.seasonID = '$season_id'
              GROUP BY playoff_results.roundNums, playoff_results.seriesLetters";

      $result = mysqli_query($conn, $sql);
      if (!$result) {
          die("Query failed: " . mysqli_error($conn));
      }

      $rounds = [];
      while ($row = mysqli_fetch_assoc($result)) {
          $rounds[$row['roundNums']][] = $row;
      }



  
// Define an array of series names (A-O)
$seriesNames = range('A', 'O'); // Creates an array ['A', 'B', 'C', ..., 'O']

// Define an array for grid classes that correspond to each series
$gridAreas = [
    'A' => 'seriesA', 'B' => 'seriesB', 'C' => 'seriesC', 'D' => 'seriesD',
    'E' => 'seriesE', 'F' => 'seriesF', 'G' => 'seriesG', 'H' => 'seriesH',
    'I' => 'seriesI', 'J' => 'seriesJ', 'K' => 'seriesK', 'L' => 'seriesL',
    'M' => 'seriesM', 'N' => 'seriesN', 'O' => 'seriesO'
];

// Organize round headers for the grid
echo "<div class='playoff-grid-container'>";

// Add round headers
echo "<div class='round-header r1-west'>Round 1</div>";
echo "<div class='round-header r2-west'>Round 2</div>";
echo "<div class='round-header r3-west'>Conference Final</div>";
echo "<div class='round-header final'>Stanley Cup Final</div>";
echo "<div class='round-header r3-east'>Conference Final</div>";
echo "<div class='round-header r2-east'>Round 2</div>";
echo "<div class='round-header r1-east'>Round 1</div>";

// Process matchups by region and round
$position = 0;
$winners = [];

// Process first round West matchups
foreach ($rounds as $round => $matchups) {
    if ($round != 1) continue; // Only process first round for West first
    
    foreach ($matchups as $match) {
        // Check if the series belongs to West region
        $topDiv = $match['topSeedTeamDivision'] ?? '';
        $botDiv = $match['bottomSeedTeamDivision'] ?? '';
        $westDivs = ['Pacific', 'Central', 'Northwest', 'Western'];
        
        if (!in_array($topDiv, $westDivs) && !in_array($botDiv, $westDivs)) continue;
        
        // Series info
        $bottomWins = (int)$match['bottomSeedWins'];
        $topWins = (int)$match['topSeedWins'];
        $bottomBold = $bottomWins > $topWins ? 'font-bold text-green-500' : '';
        $topBold = $topWins > $bottomWins ? 'font-bold text-green-500' : '';
        $seriesId = $match['seasonID'] . $match['seriesLetters'];
        
        // Determine the winner
        $winner = $bottomWins > $topWins ? $match['bottomSeedTeamTriCode'] : $match['topSeedTeamTriCode'];
        $winners['West'][$round][] = [
            'triCode' => $winner,
            'wins' => max($bottomWins, $topWins),
            'logo' => $bottomWins > $topWins ? $match['bottomSeedTeamLogo'] : $match['topSeedTeamLogo']
        ];
        
        // Assign grid class
        $seriesLetter = $seriesNames[$position];
        $gridAreaClass = $gridAreas[$seriesLetter];
        
        // Output the series box
        echo "<a href='series_details.php?series_id={$seriesId}' class='no-underline $gridAreaClass'>";
        echo "<div class='series-box bg-slate-800 border border-slate-600 p-3 rounded-lg shadow text-center w-60 hover:bg-slate-700 flex flex-col justify-between h-[150px]'>";
        
        // Top team
        echo "<div class='team'>";
        echo "<img class='team-logo' src='" . $match['bottomSeedTeamLogo'] . "' alt='" . $match['bottomSeedTeamTriCode'] . "'>";
        echo "<div class='team-info $bottomBold'>" . $match['bottomSeedTeamTriCode'] . " (" . $match['bottomSeedRankAbbrevs'] . ")</div>";
        echo "<div class='series-score'><span class='$bottomBold'>{$bottomWins}</span></div>";
        echo "</div>";
        
        // Center line
        echo "<hr class='border-white w-[90%] mx-auto my-2'>";
        
        // Bottom team
        echo "<div class='team'>";
        echo "<img class='team-logo' src='" . $match['topSeedTeamLogo'] . "' alt='" . $match['topSeedTeamTriCode'] . "'>";
        echo "<div class='team-info $topBold'>" . $match['topSeedTeamTriCode'] . " (" . $match['topSeedRankAbbrevs'] . ")</div>";
        echo "<div class='series-score'><span class='$topBold'>{$topWins}</span></div>";
        echo "</div>";
        
        echo "</div>"; // Close series box
        echo "</a>";
        
        $position++;
    }
}

// Process first round East matchups
foreach ($rounds as $round => $matchups) {
    if ($round != 1) continue; // Only process first round for East
    
    foreach ($matchups as $match) {
        // Check if the series belongs to East region
        $topDiv = $match['topSeedTeamDivision'] ?? '';
        $botDiv = $match['bottomSeedTeamDivision'] ?? '';
        $eastDivs = ['Atlantic', 'Metropolitan', 'Northeast', 'Southeast'];
        
        if (!in_array($topDiv, $eastDivs) && !in_array($botDiv, $eastDivs)) continue;
        
        // Series info
        $bottomWins = (int)$match['bottomSeedWins'];
        $topWins = (int)$match['topSeedWins'];
        $bottomBold = $bottomWins > $topWins ? 'font-bold text-green-500' : '';
        $topBold = $topWins > $bottomWins ? 'font-bold text-green-500' : '';
        $seriesId = $match['seasonID'] . $match['seriesLetters'];
        
        // Determine the winner
        $winner = $bottomWins > $topWins ? $match['bottomSeedTeamTriCode'] : $match['topSeedTeamTriCode'];
        $winners['East'][$round][] = [
            'triCode' => $winner,
            'wins' => max($bottomWins, $topWins),
            'logo' => $bottomWins > $topWins ? $match['bottomSeedTeamLogo'] : $match['topSeedTeamLogo']
        ];
        
        // Assign grid class
        $seriesLetter = $seriesNames[$position];
        $gridAreaClass = $gridAreas[$seriesLetter];
        
        // Output the series box
        echo "<a href='series_details.php?series_id={$seriesId}' class='no-underline $gridAreaClass'>";
        echo "<div class='series-box bg-slate-800 border border-slate-600 p-3 rounded-lg shadow text-center w-60 hover:bg-slate-700 flex flex-col justify-between h-[150px]'>";
        
        // Top team
        echo "<div class='team'>";
        echo "<img class='team-logo' src='" . $match['bottomSeedTeamLogo'] . "' alt='" . $match['bottomSeedTeamTriCode'] . "'>";
        echo "<div class='team-info $bottomBold'>" . $match['bottomSeedTeamTriCode'] . " (" . $match['bottomSeedRankAbbrevs'] . ")</div>";
        echo "<div class='series-score'><span class='$bottomBold'>{$bottomWins}</span></div>";
        echo "</div>";
        
        // Center line
        echo "<hr class='border-white w-[90%] mx-auto my-2'>";
        
        // Bottom team
        echo "<div class='team'>";
        echo "<img class='team-logo' src='" . $match['topSeedTeamLogo'] . "' alt='" . $match['topSeedTeamTriCode'] . "'>";
        echo "<div class='team-info $topBold'>" . $match['topSeedTeamTriCode'] . " (" . $match['topSeedRankAbbrevs'] . ")</div>";
        echo "<div class='series-score'><span class='$topBold'>{$topWins}</span></div>";
        echo "</div>";
        
        echo "</div>"; // Close series box
        echo "</a>";
        
        $position++;
    }
}

// Process second and third rounds for West
foreach ([2, 3] as $currentRound) {
    foreach ($rounds as $round => $matchups) {
        if ($round != $currentRound) continue; 
        
        foreach ($matchups as $match) {
            // Check if the series belongs to West region
            $topDiv = $match['topSeedTeamDivision'] ?? '';
            $botDiv = $match['bottomSeedTeamDivision'] ?? '';
            $westDivs = ['Pacific', 'Central', 'Northwest', 'Western'];
            
            if (!in_array($topDiv, $westDivs) && !in_array($botDiv, $westDivs)) continue;
            
            // Series info
            $bottomWins = (int)$match['bottomSeedWins'];
            $topWins = (int)$match['topSeedWins'];
            $bottomBold = $bottomWins > $topWins ? 'font-bold text-green-500' : '';
            $topBold = $topWins > $bottomWins ? 'font-bold text-green-500' : '';
            $seriesId = $match['seasonID'] . $match['seriesLetters'];
            
            // Determine the winner
            $winner = $bottomWins > $topWins ? $match['bottomSeedTeamTriCode'] : $match['topSeedTeamTriCode'];
            $winners['West'][$round][] = [
                'triCode' => $winner,
                'wins' => max($bottomWins, $topWins),
                'logo' => $bottomWins > $topWins ? $match['bottomSeedTeamLogo'] : $match['topSeedTeamLogo']
            ];
            
            // Assign grid class - for second round use I or J, for third round use M
            if ($currentRound == 2) {
                $seriesLetter = ($position <= 8) ? 'I' : 'J'; 
            } else {
                $seriesLetter = 'M';
            }
            $gridAreaClass = $gridAreas[$seriesLetter];
            
            // Output the series box
            echo "<a href='series_details.php?series_id={$seriesId}' class='no-underline $gridAreaClass'>";
            echo "<div class='series-box bg-slate-800 border border-slate-600 p-3 rounded-lg shadow text-center w-60 hover:bg-slate-700 flex flex-col justify-between h-[150px]'>";
            
            // Top team
            echo "<div class='team'>";
            echo "<img class='team-logo' src='" . $match['bottomSeedTeamLogo'] . "' alt='" . $match['bottomSeedTeamTriCode'] . "'>";
            echo "<div class='team-info $bottomBold'>" . $match['bottomSeedTeamTriCode'] . " (" . $match['bottomSeedRankAbbrevs'] . ")</div>";
            echo "<div class='series-score'><span class='$bottomBold'>{$bottomWins}</span></div>";
            echo "</div>";
            
            // Center line
            echo "<hr class='border-white w-[90%] mx-auto my-2'>";
            
            // Bottom team
            echo "<div class='team'>";
            echo "<img class='team-logo' src='" . $match['topSeedTeamLogo'] . "' alt='" . $match['topSeedTeamTriCode'] . "'>";
            echo "<div class='team-info $topBold'>" . $match['topSeedTeamTriCode'] . " (" . $match['topSeedRankAbbrevs'] . ")</div>";
            echo "<div class='series-score'><span class='$topBold'>{$topWins}</span></div>";
            echo "</div>";
            
            echo "</div>"; // Close series box
            echo "</a>";
            
            // Don't increment position for these rounds as we're manually assigning
        }
    }
}

// Process second and third rounds for East
foreach ([2, 3] as $currentRound) {
    foreach ($rounds as $round => $matchups) {
        if ($round != $currentRound) continue; 
        
        foreach ($matchups as $match) {
            // Check if the series belongs to East region
            $topDiv = $match['topSeedTeamDivision'] ?? '';
            $botDiv = $match['bottomSeedTeamDivision'] ?? '';
            $eastDivs = ['Atlantic', 'Metropolitan', 'Northeast', 'Southeast'];
            
            if (!in_array($topDiv, $eastDivs) && !in_array($botDiv, $eastDivs)) continue;
            
            // Series info
            $bottomWins = (int)$match['bottomSeedWins'];
            $topWins = (int)$match['topSeedWins'];
            $bottomBold = $bottomWins > $topWins ? 'font-bold text-green-500' : '';
            $topBold = $topWins > $bottomWins ? 'font-bold text-green-500' : '';
            $seriesId = $match['seasonID'] . $match['seriesLetters'];
            
            // Determine the winner
            $winner = $bottomWins > $topWins ? $match['bottomSeedTeamTriCode'] : $match['topSeedTeamTriCode'];
            $winners['East'][$round][] = [
                'triCode' => $winner,
                'wins' => max($bottomWins, $topWins),
                'logo' => $bottomWins > $topWins ? $match['bottomSeedTeamLogo'] : $match['topSeedTeamLogo']
            ];
            
            // Assign grid class - for second round use K or L, for third round use N
            if ($currentRound == 2) {
                $seriesLetter = ($position <= 10) ? 'K' : 'L'; 
            } else {
                $seriesLetter = 'N';
            }
            $gridAreaClass = $gridAreas[$seriesLetter];
            
            // Output the series box
            echo "<a href='series_details.php?series_id={$seriesId}' class='no-underline $gridAreaClass'>";
            echo "<div class='series-box bg-slate-800 border border-slate-600 p-3 rounded-lg shadow text-center w-60 hover:bg-slate-700 flex flex-col justify-between h-[150px]'>";
            
            // Top team
            echo "<div class='team'>";
            echo "<img class='team-logo' src='" . $match['bottomSeedTeamLogo'] . "' alt='" . $match['bottomSeedTeamTriCode'] . "'>";
            echo "<div class='team-info $bottomBold'>" . $match['bottomSeedTeamTriCode'] . " (" . $match['bottomSeedRankAbbrevs'] . ")</div>";
            echo "<div class='series-score'><span class='$bottomBold'>{$bottomWins}</span></div>";
            echo "</div>";
            
            // Center line
            echo "<hr class='border-white w-[90%] mx-auto my-2'>";
            
            // Bottom team
            echo "<div class='team'>";
            echo "<img class='team-logo' src='" . $match['topSeedTeamLogo'] . "' alt='" . $match['topSeedTeamTriCode'] . "'>";
            echo "<div class='team-info $topBold'>" . $match['topSeedTeamTriCode'] . " (" . $match['topSeedRankAbbrevs'] . ")</div>";
            echo "<div class='series-score'><span class='$topBold'>{$topWins}</span></div>";
            echo "</div>";
            
            echo "</div>"; // Close series box
            echo "</a>";
            
            // Don't increment position for these rounds as we're manually assigning
        }
    }
}

// Process Stanley Cup Final (round 4)
foreach ($rounds as $round => $matchups) {
    if ($round != 4) continue; 
    
    foreach ($matchups as $match) {
        // Series info
        $bottomWins = (int)$match['bottomSeedWins'];
        $topWins = (int)$match['topSeedWins'];
        $bottomBold = $bottomWins > $topWins ? 'font-bold text-green-500' : '';
        $topBold = $topWins > $bottomWins ? 'font-bold text-green-500' : '';
        $seriesId = $match['seasonID'] . $match['seriesLetters'];
        
        // Determine the winner
        $winner = $bottomWins > $topWins ? $match['bottomSeedTeamTriCode'] : $match['topSeedTeamTriCode'];
        $champion = [
            'triCode' => $winner,
            'wins' => max($bottomWins, $topWins),
            'logo' => $bottomWins > $topWins ? $match['bottomSeedTeamLogo'] : $match['topSeedTeamLogo']
        ];
        
        // For the final, always use series O
        $gridAreaClass = 'seriesO';
        
        echo "<div class='final-header'>Stanley Cup Final</div>";
        
        // Output the series box
        echo "<a href='series_details.php?series_id={$seriesId}' class='no-underline $gridAreaClass'>";
        echo "<div class='series-box bg-slate-800 border border-slate-600 p-3 rounded-lg shadow text-center w-60 hover:bg-slate-700 flex flex-col justify-between h-[150px]'>";
        
        // Top team
        echo "<div class='team'>";
        echo "<img class='team-logo' src='" . $match['bottomSeedTeamLogo'] . "' alt='" . $match['bottomSeedTeamTriCode'] . "'>";
        echo "<div class='team-info $bottomBold'>" . $match['bottomSeedTeamTriCode'] . " (" . $match['bottomSeedRankAbbrevs'] . ")</div>";
        echo "<div class='series-score'><span class='$bottomBold'>{$bottomWins}</span></div>";
        echo "</div>";
        
        // Center line
        echo "<hr class='border-white w-[90%] mx-auto my-2'>";
        
        // Bottom team
        echo "<div class='team'>";
        echo "<img class='team-logo' src='" . $match['topSeedTeamLogo'] . "' alt='" . $match['topSeedTeamTriCode'] . "'>";
        echo "<div class='team-info $topBold'>" . $match['topSeedTeamTriCode'] . " (" . $match['topSeedRankAbbrevs'] . ")</div>";
        echo "<div class='series-score'><span class='$topBold'>{$topWins}</span></div>";
        echo "</div>";
        
        echo "</div>"; // Close series box
        echo "</a>";
        
        // Add champion trophy and annotation if series is complete
        if ($bottomWins == 4 || $topWins == 4) {
            echo "<div class='text-center mt-4'>";
            echo "<img src='../resources/images/stanley-cup.png' alt='Stanley Cup' class='champion-trophy'>";
            echo "<div class='text-xl font-bold text-yellow-400'>Champion: " . $champion['triCode'] . "</div>";
            echo "</div>";
        }
    }
}

echo "</div>"; // End playoff grid container

      } // end if season_id
      ?>
      </div> <!-- Close full page div -->

        <?php include 'footer.php'; ?>
  </body>
</html>
