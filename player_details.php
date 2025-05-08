<!-- TEMPLATE FOR GAME DETAIL PAGES - GETS ID SELECTED ON GAMES PAGE AND USES IT TO QUERY DATABASE FOR ADDITIONAL GAME DETAILS -->
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

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />

  </head>
  <!-- Header -->
  <?php include 'header.php'; ?>
  <body>
    <div class="text-white" style='align-items: flex-start; background-color: #343a40'>
      <div style='margin-left: 10px; margin-right: 10px'>
        <?php

          ini_set('display_errors', 1);
          ini_set('display_startup_errors', 1);
          error_reporting(E_ALL);
    ########## GET ALL PLAYER DATA TO USE FOR THE PAGE ##########
          // Check if the 'game_id' is passed in the URL
          if (isset($_GET['player_id'])) {
              $player_id = $_GET['player_id'];

              $sql = "SELECT nhl_player_details.*, nhl_teams.teamLogo, nhl_teams.fullName AS fullTeamName,
                      nhl_contracts.*
                      FROM nhl_player_details
                      LEFT JOIN nhl_teams ON nhl_player_details.currentTeamId = nhl_teams.id
                      LEFT JOIN nhl_contracts ON nhl_player_details.playerID = nhl_contracts.playerId
                      WHERE nhl_player_details.playerID=$player_id";
              $playerInfo = mysqli_query($conn, $sql);

            ##### Iterate through all player results and assign data to variables #####
            while ($row = mysqli_fetch_assoc($playerInfo)) {
              ### Basic Info ###
                $name = $row['firstName'] . ' ' . $row['lastName'];
                $sweaterNumber = $row['sweaterNumber'];
                $position = $row['position'];
                $headshot = $row['headshot'];
                $isActiveRaw = strtolower(trim($row['isActive']));
                if ($isActiveRaw === 'false') {
                    $active = "No";
                } else {
                    $active = "Yes";
                }

                if ($active == "Yes") {
                  $teamName = $row['fullTeamName'];
                  $teamLogo = $row['teamLogo'];
                  $teamID = $row['currentTeamId'];
                } else {
                  $teamName = 'N/A';
                  $teamLogo = 'N/A';
                  $teamID = 'N/A';
                } 
                
                $badgesLogos = $row['badgesLogos'];
                $badgesNames = $row['badgesNames'];
                $heroImage = $row['heroImage'];

                if ($row['heightInInches']) {
                  $heightIn = $row['heightInInches'];
                  $heightFt = floor($heightIn/12);
                  $heightInches = ($heightIn % 12);
                  $heightIn = $heightFt . "' " . $heightInches . '"';
                } else {
                  $heightIn = '?';
                }
                if ($row['heightInCentimeters']) {
                  $heightCm = $row['heightInCentimeters'];
                } else {
                  $heightCm = intval($heightIn*2.54);
                }
                if ($row['weightInPounds']) {
                  $weightLb = $row['weightInPounds'];
                } else {
                  $weightLb = '?';
                }
                if ($row['weightInKilograms']) {
                  $weightKg = $row['weightInKilograms'];
                } else {
                  $weightKg = '?';
                }
                
                $birthDate = date('F j, Y',strtotime($row['birthDate']));
                $birthCity = $row['birthCity'];
                $birthStateProvince = $row['birthStateProvince'];
                $birthCountry = $row['birthCountry'];
                $shootsCatches = $row['shootsCatches'];
                if ($row['draftYear'] == '') {
                  $draftYear = 'N/A';
                  $draftTeam = 'N/A';
                  $draftRound = 'N/A';
                  $draftPickInRound = 'N/A';
                  $draftOverall = 'N/A';
                } else {
                  $draftYear = $row['draftYear'];
                  $draftTeam = $row['draftTeam'];
                  $draftRound = $row['draftRound'];
                  $draftPickInRound = $row['draftPickInRound'];
                  $draftOverall = $row['draftOverall'];
                }
                // if ($row['inHHOF']) {
                //   $inHHOF = '<b>In HOF:</b> Yes';
                // } else {
                //   $inHHOF = '<b>In HOF:</b> No';
                // }

                $contractSignedDate = $row['Signed Date'];
                $contractStartSeason = $row['Start Season'];
                $contractEndSeason = $row['End Season'];
                $contractLength = $row['Years'];
                $contractValue = $row['Total Value'];
                $capHit = $row['Cap Hit'];
                $signingBonus = $row['Signing Bonus'];
                $baseSalary = $row['Base Salary'];
                $performanceBonus = $row['Performance Bonus'];
                $contractTerms = $row['Terms'];


                # checks if a value (i.e. # of assists) is an empty string and changes to 0 if so
                if (!function_exists('fillEmptyStats')) {
                  function fillEmptyStats($value) {
                      return ($value === null || $value === '') ? '-' : $value;
                  }
              }

              ### Featured Season Stats ###
                $featuredSeason = $row['featuredSeason'];
                $featuredSeasonGP = fillEmptyStats($row['featuredSeasonGP']);
                $featuredSeasonAssists = fillEmptyStats($row['featuredSeasonAssists']);
                $featuredSeasonGWG = fillEmptyStats($row['featuredSeasonGWG']);
                $featuredSeasonGoals = fillEmptyStats($row['featuredSeasonGoals']);
                $featuredSeasonOTGoals = fillEmptyStats($row['featuredSeasonOTGoals']);
                $featuredSeasonPIM = fillEmptyStats($row['featuredSeasonPIM']);
                $featuredSeasonPlusMinus = fillEmptyStats($row['featuredSeasonPlusMinus']);
                $featuredSeasonPts = fillEmptyStats($row['featuredSeasonPts']);
                $featuredSeasonPPG = fillEmptyStats($row['featuredSeasonPPG']);
                $featuredSeasonPPPoints = fillEmptyStats($row['featuredSeasonPPPoints']);
                $featuredSeasonShootingPct = fillEmptyStats($row['featuredSeasonShootingPct']);
                $featuredSeasonSHG = fillEmptyStats($row['featuredSeasonSHG']);
                $featuredSeasonSHPts = fillEmptyStats($row['featuredSeasonSHPts']);
                $featuredSeasonShots = fillEmptyStats($row['featuredSeasonShots']);
                  # Goalie
                $featuredSeasonGAA = fillEmptyStats($row['featuredSeasonGAA']);
                $featuredSeasonLosses = fillEmptyStats($row['featuredSeasonLosses']);
                $featuredSeasonSO = fillEmptyStats($row['featuredSeasonSO']);
                $featuredSeasonTies = fillEmptyStats($row['featuredSeasonTies']);
                $featuredSeasonWins = fillEmptyStats($row['featuredSeasonWins']);
                $featuredSeasonGS = fillEmptyStats($row['featuredSeasonGS']);
                $featuredSeasonGA = fillEmptyStats($row['featuredSeasonGA']);
                $featuredSeasonSavePct = fillEmptyStats($row['featuredSeasonSavePct']);
                $featuredSeasonOTLosses = fillEmptyStats($row['featuredSeasonOTLosses']);
                $featuredSeasonShotsAgainst = fillEmptyStats($row['featuredSeasonShotsAgainst']);
                
              ### Regular Season Stats ###
                $regSeasonCareerGP = fillEmptyStats($row['regSeasonCareerGP']);
                $regSeasonCareerAssists = fillEmptyStats($row['regSeasonCareerAssists']);
                $regSeasonCareerGWG = fillEmptyStats($row['regSeasonCareerGWG']);
                $regSeasonCareerGoals = fillEmptyStats($row['regSeasonCareerGoals']);
                $regSeasonCareerOTGoals = fillEmptyStats($row['regSeasonCareerOTGoals']);
                $regSeasonCareerPIM = fillEmptyStats($row['regSeasonCareerPIM']);
                $regSeasonCareerPlusMinus = fillEmptyStats($row['regSeasonCareerPlusMinus']);
                $regSeasonCareerPts = fillEmptyStats($row['regSeasonCareerPts']);
                $regSeasonCareerPPG = fillEmptyStats($row['regSeasonCareerPPG']);
                $regSeasonCareerPPPoints = fillEmptyStats($row['regSeasonCareerPPPoints']);
                $regSeasonCareerShootingPct = fillEmptyStats($row['regSeasonCareerShootingPct']);
                $regSeasonCareerSHG = fillEmptyStats($row['regSeasonCareerSHG']);
                $regSeasonCareerSHPts = fillEmptyStats($row['regSeasonCareerSHPts']);
                $regSeasonCareerShots = fillEmptyStats($row['regSeasonCareerShots']);
                  # Goalie
                $regSeasonCareerGAA = fillEmptyStats($row['regSeasonCareerGAA']);
                $regSeasonCareerLosses = fillEmptyStats($row['regSeasonCareerLosses']);
                $regSeasonCareerSO = fillEmptyStats($row['regSeasonCareerSO']);
                $regSeasonCareerTies = fillEmptyStats($row['regSeasonCareerTies']);
                $regSeasonCareerWins = fillEmptyStats($row['regSeasonCareerWins']);
                $regSeasonCareerGS = fillEmptyStats($row['regSeasonCareerGS']);
                $regSeasonCareerGA = fillEmptyStats($row['regSeasonCareerGA']);
                $regSeasonCareerSavePct = fillEmptyStats($row['regSeasonCareerSavePct']);
                $regSeasonCareerOTLosses = fillEmptyStats($row['regSeasonCareerOTLosses']);
                $regSeasonCareerShotsAgainst = fillEmptyStats($row['regSeasonCareerShotsAgainst']);

              ### Playoff Stats ###
                $playoffsCareerAssists = fillEmptyStats($row['playoffsCareerAssists']);
                $playoffsCareerGP = fillEmptyStats($row['playoffsCareerGP']);
                $playoffsCareerAssists = fillEmptyStats($row['playoffsCareerAssists']);
                $playoffsCareerGWG = fillEmptyStats($row['playoffsCareerGWG']);
                $playoffsCareerGoals = fillEmptyStats($row['playoffsCareerGoals']);
                $playoffsCareerOTGoals = fillEmptyStats($row['playoffsCareerOTGoals']);
                $playoffsCareerPIM = fillEmptyStats($row['playoffsCareerPIM']);
                $playoffsCareerPlusMinus = fillEmptyStats($row['playoffsCareerPlusMinus']);
                $playoffsCareerPts = fillEmptyStats($row['playoffsCareerPts']);
                $playoffsCareerPPG = fillEmptyStats($row['playoffsCareerPPG']);
                $playoffsCareerPPPoints = fillEmptyStats($row['playoffsCareerPPPoints']);
                $playoffsCareerShootingPct = fillEmptyStats($row['playoffsCareerShootingPct']);
                $playoffsCareerSHG = fillEmptyStats($row['playoffsCareerSHG']);
                $playoffsCareerSHPts = fillEmptyStats($row['playoffsCareerSHPts']);
                $playoffsCareerShots = fillEmptyStats($row['playoffsCareerShots']);
                  # Goalie
                $playoffsCareerGAA = fillEmptyStats($row['playoffsCareerGAA']);
                $playoffsCareerLosses = fillEmptyStats($row['playoffsCareerLosses']);
                $playoffsCareerSO = fillEmptyStats($row['playoffsCareerSO']);
                $playoffsCareerTies = fillEmptyStats($row['playoffsCareerTies']);
                $playoffsCareerWins = fillEmptyStats($row['playoffsCareerWins']);
                $playoffsCareerGS = fillEmptyStats($row['playoffsCareerGS']);
                $playoffsCareerGA = fillEmptyStats($row['playoffsCareerGA']);
                $playoffsCareerSavePct = fillEmptyStats($row['playoffsCareerSavePct']);
                $playoffsCareerOTLosses = fillEmptyStats($row['playoffsCareerOTLosses']);
                $playoffsCareerShotsAgainst = fillEmptyStats($row['playoffsCareerShotsAgainst']);
                

                // $last5Games = $row['last5Games'];
                // $seasonTotals = $row['seasonTotals'];
                $awardNames = $row['awardNames'];
                $awardSeasons = $row['awardSeasons'];
                $currentTeamRoster = $row['currentTeamRoster'];
          }

            $last5GameSQL = "SELECT 
                  player_last_5_games.*,
                  skaters_gamebygame_stats.goals AS skater_goals,
                  skaters_gamebygame_stats.assists AS skater_assists,
                  skaters_gamebygame_stats.points AS skater_points,
                  skaters_gamebygame_stats.plusMinus AS skater_plusMinus,
                  skaters_gamebygame_stats.pim AS skater_pim,
                  skaters_gamebygame_stats.hits AS skater_hits,
                  skaters_gamebygame_stats.sog AS skater_sog,
                  skaters_gamebygame_stats.powerPlayGoals AS skater_ppg,
                  skaters_gamebygame_stats.faceoffWinningPctg AS skater_faceoffWinningPctg,
                  skaters_gamebygame_stats.toi AS skater_toi,
                  skaters_gamebygame_stats.blockedShots AS skater_blockedShots,
                  skaters_gamebygame_stats.shifts AS skater_shifts,
                  skaters_gamebygame_stats.giveaways AS skater_giveaways,
                  skaters_gamebygame_stats.takeaways AS skater_takeaways,
                  goalies_gamebygame_stats.pim AS goalie_pim,
                  goalies_gamebygame_stats.evenStrengthShotsAgainst AS evenStrengthSA,
                  goalies_gamebygame_stats.powerPlayShotsAgainst AS powerPlaySA,
                  goalies_gamebygame_stats.shorthandedShotsAgainst AS shorthandedSA,
                  goalies_gamebygame_stats.saveShotsAgainst AS saveSA,
                  goalies_gamebygame_stats.savePctg AS savePctg,
                  goalies_gamebygame_stats.evenStrengthGoalsAgainst AS evenStrengthGA,
                  goalies_gamebygame_stats.powerPlayGoalsAgainst AS powerPlayGA,
                  goalies_gamebygame_stats.shorthandedGoalsAgainst AS shorthandedGA,
                  goalies_gamebygame_stats.goalsAgainst AS goalsAgainst,
                  goalies_gamebygame_stats.starter AS starter,
                  goalies_gamebygame_stats.shotsAgainst AS shotsAgainst,
                  goalies_gamebygame_stats.saves AS saves

              FROM player_last_5_games
              LEFT JOIN skaters_gamebygame_stats 
                  ON player_last_5_games.playerId = skaters_gamebygame_stats.playerId
                  AND player_last_5_games.game_id = skaters_gamebygame_stats.gameID
              LEFT JOIN goalies_gamebygame_stats 
                  ON player_last_5_games.playerId = goalies_gamebygame_stats.playerId
              WHERE player_last_5_games.playerId = '$player_id' AND game_id != 0";

            $last5GameInfo = mysqli_query($conn, $last5GameSQL);

            
            // echo $last5GameSQL;
          echo "<br>";

    ########## DISPLAY ALL RESULTS ##########

        ### Flexbox for player name/number/active/id and headshot/team logo - aligns them side-by-side ###
          echo "<div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;'>";
            // Left side: Name and status
            echo "<div>";
              echo "<h1 class='text-4xl' style='margin-left: 20px; margin-top: 20px'>" . $name . " #" . $sweaterNumber . "</h1>";
              if ($active == 'Yes') {
                echo "<p style='color: #4CAF50; margin-left: 20px; margin-top: 20px' class='font-medium text-2xl'>Active - " . $player_id . "</p>";
              } else {
                echo "<p style='color: #e63946 ; margin-left: 20px; margin-top: 20px' class='font-medium text-2xl'>Not Active - " . $player_id . "</p>";
              }
            echo "</div>";
            // Right side: Headshot and logo
            echo "<div style='display: flex; align-items: center; gap: 5px;'>";
              if ($badgesLogos != 'false' and $badgesLogos != '') {
                echo "<img src='" . htmlspecialchars($badgesLogos) . "' alt='badge logo' style='height: 110px; margin-right: 15px'>";
              } else {
                echo "<p></p>";
              }
              if ($headshot != 'false' and $headshot != '' and $headshot != 'N/A') {
                echo "<img src='" . htmlspecialchars($headshot) . "' alt='headshot' style='height: 120px; margin-right: 15px; border-radius: 8px'>";
              } else {
                echo "<p></p>";
              }

              if ($teamLogo != 'false' and $teamLogo != '' and $teamLogo != 'N/A') {
                echo "<a href='https://connoryoung.com/team_details.php?team_id=" . htmlspecialchars($teamID) . "'>" 
                . "<img src='" . htmlspecialchars($teamLogo) . "' alt='team logo' style='height: 120px; margin-right: 15px'>"
                . "</a>";
                          } else {
                echo "<p></p>";
              }
            echo "</div>";
          echo "</div>";

              // echo "<p>Current Team: " . $teamName . " <img src='" . htmlspecialchars($teamLogo) . "' alt='N/A' style='height: 65px;'></p>";
          

        ### Flexbox for hero image and player bio box ###
          echo "<div class='hero-bio-container gap-20'>";
            
              // Left side: Hero image container
            echo "<div lg:basis-[60%] justify-center>";
            echo "<img style = 'border: 1px solid #bcd6e7' src='" . htmlspecialchars($heroImage) . "' alt='heroImage' class='hero-bio-image'>";
            echo "</div>";

            // Right side: Bio box container
            echo "<div class='bio-box'>";
            echo "<h4 class='bio-header'>Player Bio</h4>";
          
            echo "<div class='bio-body mr-4'>";
          
              echo "<p><b>Height:</b> " . $heightIn . " / " . $heightCm . " cm</p>";
              echo "<p><b>Weight:</b> " . $weightLb . " lbs / " . $weightKg . " kg</p>";
              echo "<p><b>Birthdate:</b> " . $birthDate . "</p>";
          
              if ($birthStateProvince == '') {
                echo "<p><b>Birthplace:</b> " . $birthCity . " (" . $birthCountry . ")</p>";
              } else {
                echo "<p><b>Birthplace:</b> " . $birthCity . ", " . $birthStateProvince . " (" . $birthCountry . ")</p>";
              }
          
              echo "<p><b>Shoots/catches:</b> " . $shootsCatches . "</p>";
              echo "<p><b>Position:</b> " . $position . "</p>";
          
              if ($draftYear == 'N/A') {
                echo "<p><b>Draft Info:</b> Undrafted</p>";
              } else {
                echo "<p><b>Draft Info:</b> " . $draftYear . " Rd. " . $draftRound . " Pick " . $draftPickInRound .
                " (#" . $draftOverall . " Ovr.) (" . $draftTeam . ")</p>";
              }
          
              // echo "<p>" . $inHHOF . "</p>";
          
              if (!empty($awardNames)) {
                $awardNamesArray = json_decode(str_replace("'", '"', $awardNames), true);
                $awardSeasonsArray = json_decode(str_replace("'", '"', $awardSeasons), true);
          
                if (is_array($awardNamesArray) && is_array($awardSeasonsArray)) {
                  echo "<div class='awards mt-6'><b>Awards:</b>";
                    for ($i = 0; $i < count($awardNamesArray); $i++) {
                        $award = $awardNamesArray[$i];
                        $seasonsRaw = $awardSeasonsArray[$i];
          
                        $formattedSeasons = array_map(function($s) {
                            return substr($s, 0, 4) . "–" . substr($s, 4);
                        }, $seasonsRaw);
          
                        $seasonString = implode(", ", $formattedSeasons);
                        echo "<p mt-2>" . $award . " (" . $seasonString . ")</p>";
                    }
                  echo "</div>";
                } else {
                    echo "<p><b>Awards:</b> None</p>";
                }
              } else {
                  echo "<p><b>Awards:</b> None</p>";
              }
          
              if ($contractSignedDate != '') {
                echo "<p><b>Contract Info: </b>Signed " . $contractSignedDate . " for " . $contractLength . 
                " years @ cap hit of " . $capHit . " - (" . $contractValue . " total)</p>";

                // $contractSignedDate = $row['Signed Date'];
                // $contractStartSeason = $row['Start Season'];
                // $contractEndSeason = $row['End Season'];
                // $contractLength = $row['Years'];
                // $contractValue = $row['Total Value'];
                // $capHit = $row['Cap Hit'];
                // $signingBonus = $row['Signing Bonus'];
                // $baseSalary = $row['Base Salary'];
                // $performanceBonus = $row['Performance Bonus'];
                // $contractTerms = $row['Terms'];






              } else {
                echo "<p>No active contract</p>";
              }
            echo "</div>"; // close bio-body
          echo "</div>"; // close bio-box
          


          echo "</div><br><br>";

      ########################################################################## Stat Tables ##################################################################

        // Start OUTER WRAPPER
        echo "<div class='flex flex-col lg:flex-row gap-10'>";

        // LEFT COLUMN (top 3 tables stacked)
        echo "<div class='flex flex-col lg:basis-[45%]'>";

        ### Last 5 Games ###  
        echo "<h3 class='text-center text-2xl text-white'>Last 5 Games Statistics</h3>";
          # Goalies
        if (strtolower($position) == 'g') {
          if (mysqli_num_rows($last5GameInfo) == 0) {
            echo "<table class='goalie-stats-table default-zebra-table text-center'>";
            echo "<colgroup>";
            echo "<col class='last-5-games-id'>";
            echo "<col class='last-5-games-team'>";
            echo "<col class='last-5-games-opponent'>";
            echo "<col class='last-5-games-home-road'>";
            echo "<col class='last-5-games-shots-against'>";
            echo "<col class='last-5-games-goals-against'>";
            echo "<col class='last-5-games-saves'>";
            echo "<col class='last-5-games-save-percentage'>";
            echo "<col class='last-5-games-starter'>";
            echo "</colgroup>";
            echo "<thead>";
            echo "<tr>";
                echo "<th class='border border-slate-600 px-2 py-1'>ID</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>Team</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>Opp.</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>H/A</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>SA</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>GA</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>Saves</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>Save %</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>Starter</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            echo "<tr>";
            echo "<td colspan='9' class='text-center border border-slate-600 px-2 py-1'>No data available.</td>";
            echo "</tr>";
            echo "</tbody>";
            echo "</table><br><br>";
          } else {
          echo "<table class='player-stats-table default-zebra-table text-center'>";
          echo "<colgroup>";
          echo "<col class='last-5-games-id'>";
          echo "<col class='last-5-games-team'>";
          echo "<col class='last-5-games-opponent'>";
          echo "<col class='last-5-games-home-road'>";
          echo "<col class='last-5-games-goals'>";
          echo "<col class='last-5-games-assists'>";
          echo "<col class='last-5-games-points'>";
          echo "<col class='last-5-games-plus-minus'>";
          echo "<col class='last-5-games-pim'>";
          echo "<col class='last-5-games-hits'>";
          echo "<col class='last-5-games-ppg'>";
          echo "<col class='last-5-games-sog'>";
          echo "<col class='last-5-games-faceoff-winning-pctg'>";
          echo "<col class='last-5-games-toi'>";
          echo "</colgroup>";
          echo "<thead>";
          echo "<tr>";
                echo "<th class='border border-slate-600 px-2 py-1'>ID</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>Team</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>Opp.</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>H / A</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>G</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>A</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>P</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>+/-</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>PIM</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>Hits</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>PPG</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>SOG</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>FO %</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>TOI</th>";
            echo "</tr>";
          echo "</thead>";

          while ($row = mysqli_fetch_assoc($last5GameInfo)) {
            $last5_games_id = isset($row['game_id']) ? $row['game_id'] : null;
            $last5_games_date = isset($row['game_date']) ? $row['game_date'] : null;
            $last5_games_team = isset($row['team']) ? $row['team'] : null;
            $last5_games_opponent = isset($row['opponent']) ? $row['opponent'] : null;
            $last5_games_homeRoad = isset($row['homeRoad']) ? $row['homeRoad'] : null;  
            $last5_games_goals = isset($row['skater_goals']) ? $row['skater_goals'] : null;
            $last5_games_shotsAgainst = isset($row['goalie_shotsAgainst']) ? $row['goalie_shotsAgainst'] : null;
            $last5_games_goalsAgainst = isset($row['goalie_goalsAgainst']) ? $row['goalie_goalsAgainst'] : null;
            $last5_games_saves = isset($row['goalie_saves']) ? $row['goalie_saves'] : null;
            $last5_games_savePctg = isset($row['goalie_savePctg']) ? $row['goalie_savePctg'] : null;
            $last5_games_starter = isset($row['goalie_starter']) ? $row['goalie_starter'] : null;
            echo "<tr>";
            echo "<td class='border border-slate-600 px-2 py-1'><a href='https://connoryoung.com/game_details.php?game_id=" . $last5_games_id . "'>$last5_games_id</a></td>";
            echo "<td class='border border-slate-600 px-2 py-1'>" . $last5_games_team . "</td>";
            echo "<td class='border border-slate-600 px-2 py-1'>" . $last5_games_opponent . "</td>";
            if ($last5_games_homeRoad =='H') {
              $last5_games_homeRoad = 'Home';
            } else if ($last5_games_homeRoad == 'A') {
              $last5_games_homeRoad = 'Away';
            } else {
              $last5_games_homeRoad = 'N/A';
            }
            echo "<td class='border border-slate-600 px-2 py-1'>" . $last5_games_homeRoad . "</td>";


            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_shotsAgainst)) . "</td>";
            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_goalsAgainst)) . "</td>";
            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(number_format($last5_games_saves, 2)) . "</td>";
            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(number_format($last5_games_savePctg, 3)) . "</td>";
            if ($last5_games_starter == 1) {
              echo "<td class='border border-slate-600 px-2 py-1'>Yes</td>";
            } else {
              echo "<td class='border border-slate-600 px-2 py-1'>No</td>";
            }


            // if (strtolower($position) == 'g') {
            //   echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_shotsAgainst)) . "</td>";
            //   echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_goalsAgainst)) . "</td>";
            //   echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(number_format($last5_games_saves, 2)) . "</td>";
            //   echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(number_format($last5_games_savePctg, 3)) . "</td>";
            //   if ($last5_games_starter == 1) {
            //     echo "<td class='border border-slate-600 px-2 py-1'>Yes</td>";
            //   } else {
            //     echo "<td class='border border-slate-600 px-2 py-1'>No</td>";
            //   }
            // } else {
            //   echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_goals)) . "</td>";
            //   echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_assists)) . "</td>";
            //   echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_points)) . "</td>";
            //   echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_plusMinus)) . "</td>";
            //   echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_pim)) . "</td>";
            //   echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_hits)) . "</td>";
            //   echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_ppg)) . "</td>";
            //   echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_sog)) . "</td>";
            //   echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_faceoffWinningPctg)) . "</td>";
            //   echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_toi)) . "</td>";
            echo "</tr>";
            }
          }
        }
        } else {
          if (mysqli_num_rows($last5GameInfo) == 0) {
            # Skaters
            echo "<table class='player-stats-table default-zebra-table text-center mt-10'>";
            echo "<colgroup>";
            echo "<col class='last-5-games-id'>";
            echo "<col class='last-5-games-team'>";
            echo "<col class='last-5-games-opponent'>";
            echo "<col class='last-5-games-home-road'>";
            echo "</colgroup>";
            echo "<thead>";
            echo "<tr>";
              echo "<th class='border border-slate-600 px-2 py-1'>ID</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>Team</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>Opp.</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>H/A<th>";
              echo "<th class='border border-slate-600 px-2 py-1'>G</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>A</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>P</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>+/-</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>PIM</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>Hits</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>PPG</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>SOG</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>FO %</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>TOI</th>";
            echo "</tr>";
            echo "</thead>";
              echo "<tr>";
              echo "<td colspan='15' class='text-center border border-slate-600 px-2 py-1'>No data available.</td>";
              echo "</tr>";
          } else {
            echo "<table class='goalie-stats-table default-zebra-table text-center mt-10'>";
            echo "<colgroup>";
            echo "<col class='last-5-games-id'>";
            echo "<col class='last-5-games-team'>";
            echo "<col class='last-5-games-opponent'>";
            echo "<col class='last-5-games-home-road'>";
            echo "</colgroup>";
            echo "<thead>";
            echo "<tr>";
              echo "<th class='border border-slate-600 px-2 py-1'>ID</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>Team</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>Opp.</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>H/A</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>G</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>A</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>P</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>+/-</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>PIM</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>Hits</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>PPG</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>SOG</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>FO %</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>TOI</th>";
            echo "</tr>";
            echo "</thead>";
            while ($row = mysqli_fetch_assoc($last5GameInfo)) {
              $last5_games_id = isset($row['game_id']) ? $row['game_id'] : null;
              $last5_games_date = isset($row['game_date']) ? $row['game_date'] : null;
              $last5_games_team = isset($row['team']) ? $row['team'] : null;
              $last5_games_opponent = isset($row['opponent']) ? $row['opponent'] : null;
              $last5_games_homeRoad = isset($row['homeRoad']) ? $row['homeRoad'] : null;  
              $last5_games_goals = isset($row['skater_goals']) ? $row['skater_goals'] : null;
              $last5_games_assists = isset($row['assists']) ? $row['assists'] : null;
              $last5_games_points = isset($row['points']) ? $row['points'] : null;
              $last5_games_plusMinus = isset($row['plusMinus']) ? $row['plusMinus'] : null;
              $last5_games_pim = isset($row['pim']) ? $row['pim'] : null;
              $last5_games_hits = isset($row['hits']) ? $row['hits'] : null;
              $last5_games_ppg = isset($row['powerPlayGoals']) ? $row['powerPlayGoals'] : null;
              $last5_games_sog = isset($row['sog']) ? $row['sog'] : null;
              $last5_games_faceoffWinningPctg = isset($row['faceoffWinningPctg']) ? $row['faceoffWinningPctg'] : null;
              $last5_games_toi = isset($row['toi']) ? $row['toi'] : null;
              echo "<tr>";
              echo "<td class='border border-slate-600 px-2 py-1'><a href='https://connoryoung.com/game_details.php?game_id=" . $last5_games_id . "'>$last5_games_id</a></td>";
              echo "<td class='border border-slate-600 px-2 py-1'>" . $last5_games_team . "</td>";
              echo "<td class='border border-slate-600 px-2 py-1'>" . $last5_games_opponent . "</td>";
              if ($last5_games_homeRoad =='H') {
                $last5_games_homeRoad = 'Home';
              } else if ($last5_games_homeRoad == 'A') {
                $last5_games_homeRoad = 'Away';
              } else {
                $last5_games_homeRoad = 'N/A';
              }
              echo "<td class='border border-slate-600 px-2 py-1'>" . $last5_games_homeRoad . "</td>";
              echo "<td class='border border-slate-600 px-2 py-1'>" . $last5_games_goals . "</td>";
              echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_assists)) . "</td>";
              echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_points)) . "</td>";
              echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_plusMinus)) . "</td>";
              echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_pim)) . "</td>";
              echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_hits)) . "</td>";
              echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_ppg)) . "</td>";
              echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_sog)) . "</td>";
              echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_faceoffWinningPctg)) . "</td>";
              echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($last5_games_toi)) . "</td>";
              
              echo "</tr>";
            }
          }
        }
      echo "</tbody>";
      echo "</table><br>";
        
      


          ### Featured Season Stats ###
          $formatted_featuredSeason_1 = substr($featuredSeason, 0, 4);
          $formatted_featuredSeason_2 = substr($featuredSeason, 4);
        echo "<h3 class='text-center text-2xl text-white'>Featured Season Statistics (" .
         $formatted_featuredSeason_1 . "-" . $formatted_featuredSeason_2 . ")</h3>";
        
        if (strtolower($position) == 'g') {
            // GOALIE STATS BLOCK
            echo "<table class='goalie-stats-table default-zebra-table'>";
            echo "<colgroup>";
            echo "<col class='goalie-player-details-gp>";
            echo "<col class='goalie-player-details-w'>";
            echo "<col class='goalie-player-details-l'>";
            echo "<col class='goalie-player-details-gaa'>";
            echo "<col class='goalie-player-details-savepct'>";
            echo "<col class='goalie-player-details-so'>";
            echo "<col class='goalie-player-details-t'>";
            echo "<col class='goalie-player-details-gs'>";
            echo "<col class='goalie-player-details-ga'>";
            echo "<col class='goalie-player-details-otl'>";
            echo "<col class='goalie-player-details-sa'>";
            echo "</colgroup>";
            echo "<thead>";
            echo "<tr>";
                echo "<th class='border border-slate-600 px-2 py-1'>GP</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>W</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>L</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>GAA</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>Save %</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>SO</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>T</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>GS</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>GA</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>OT L</th>";
                echo "<th class='border border-slate-600 px-2 py-1'>SA</th>";
            echo "</tr>";
            echo "</thead>";
        
            echo "<tbody>";
            echo "<tr>";
                echo "<td class='border border-slate-600 px-2 py-1'>$featuredSeasonGP</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>$featuredSeasonWins</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>$featuredSeasonLosses</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>" . number_format($featuredSeasonGAA,2) . "</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>" . number_format($featuredSeasonSavePct,3) . "</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>$featuredSeasonSO</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>$featuredSeasonTies</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>$featuredSeasonGS</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>$featuredSeasonGA</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>$featuredSeasonOTLosses</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>$featuredSeasonShotsAgainst</td>";
            echo "</tr>";
            echo "</tbody>";
            echo "</table><br><br>";
        

            } else {
              // SKATER STATS BLOCK
              echo "<table class='player-stats-table default-zebra-table'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>GP</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 6%'>G</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 6%'>A</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>Pts</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>+/-</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>PIM</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 9%'>Shots</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 8%'>Shot %</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>PPG</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>PP Pts</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>SHG</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 8%'>SH Pts</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>GWG</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>OTG</th>"; 
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $featuredSeasonGP . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $featuredSeasonGoals . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $featuredSeasonAssists . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $featuredSeasonPts . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $featuredSeasonPlusMinus . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $featuredSeasonPIM . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $featuredSeasonShots . "</td>";
                  // echo "<td>" . $featuredSeasonShootingPct . "</td>";
                  $formatted_featuredSeasonShootingPct = $featuredSeasonShootingPct * 100;
                  echo "<td class='border border-slate-600 px-2 py-1'>" . round(number_format($formatted_featuredSeasonShootingPct, 2), 1) . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $featuredSeasonPPG . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $featuredSeasonPPPoints . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $featuredSeasonSHG . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $featuredSeasonSHPts . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $featuredSeasonGWG . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $featuredSeasonOTGoals . "</td>";
                echo "</tr>";
                echo "</tbody>";
              echo "</table><br><br>";
             }

        ### Career Regular Season Stats ###
            echo "<h3 class='text-center text-2xl text-white'>Career Regular Season Statistics</h3>";
            if (strtolower($position) == 'g') {
              // GOALIE STATS BLOCK
              echo "<table class='goalie-stats-table default-zebra-table'>";
                echo "<thead>";
                echo "<tr>";
                  echo "<th class='border border-slate-600 px-2 py-1'>GP</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>W</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>L</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>GAA</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>Save %</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>SO</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>T</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>GS</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>GA</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>OT L</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>SA</td>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$regSeasonCareerGP</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$regSeasonCareerWins</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$regSeasonCareerLosses</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . number_format($regSeasonCareerGAA,2) . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . number_format($regSeasonCareerSavePct,3) . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$regSeasonCareerSO</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$regSeasonCareerTies</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$regSeasonCareerGS</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$regSeasonCareerGA</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$regSeasonCareerOTLosses</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$regSeasonCareerShotsAgainst</td>";
                echo "</tr>";
                echo "</tbody>";
              echo "</table><br><br>";
            } else {
              // SKATER STATS BLOCK
              echo "<table class='player-stats-table default-zebra-table'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>GP</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 6%'>G</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 6%'>A</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>Pts</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>+/-</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>PIM</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 9%'>Shots</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 8%'>Shot %</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>PPG</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>PP Pts</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>SHG</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 8%'>SH Pts</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>GWG</th>";
                echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>OTG</th>"; 
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $regSeasonCareerGP . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $regSeasonCareerGoals . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $regSeasonCareerAssists . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $regSeasonCareerPts . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $regSeasonCareerPlusMinus . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $regSeasonCareerPIM . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $regSeasonCareerShots . "</td>";
                  $formatted_regSeasonCareerShootingPct = round((float)$regSeasonCareerShootingPct * 100, 1);
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $formatted_regSeasonCareerShootingPct . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $regSeasonCareerPPG . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $regSeasonCareerPPPoints . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $regSeasonCareerSHG . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $regSeasonCareerSHPts . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $regSeasonCareerGWG . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $regSeasonCareerOTGoals . "</td>";
                echo "</tr>";
                echo "</tbody>";
              echo "</table><br><br>";
            }
        ### Career Playoff Stats ###
            echo "<h3 class='text-center text-2xl text-white'>Career Playoff Statistics</h3>";
            if (strtolower($position) == 'g') {
              // GOALIE STATS BLOCK
              echo "<table class='goalie-stats-table default-zebra-table'>";
              echo "<thead>";
                echo "<tr>";
                  echo "<th class='border border-slate-600 px-2 py-1'>GP</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>W</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>L</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>GAA</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>Save %</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>SO</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>T</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>GS</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>GA</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>OT L</td>";
                  echo "<th class='border border-slate-600 px-2 py-1'>SA</td>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$playoffsCareerGP</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$playoffsCareerWins</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$playoffsCareerLosses</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . number_format((float)$playoffsCareerGAA,2) . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . number_format((float)$playoffsCareerSavePct,3) . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$playoffsCareerSO</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$playoffsCareerTies</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$playoffsCareerGS</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$playoffsCareerGA</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$playoffsCareerOTLosses</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>$playoffsCareerShotsAgainst</td>";
                echo "</tr>";
                echo "</tbody>";
              echo "</table><br><br>";
            } else {
              // SKATER STATS BLOCK
              echo "<table class='player-stats-table default-zebra-table'>";
                echo "<thead>";
                echo "<tr>";
                  echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>GP</th>";
                  echo "<th class='border border-slate-600 px-2 py-1' style='width: 6%'>G</th>";
                  echo "<th class='border border-slate-600 px-2 py-1' style='width: 6%'>A</th>";
                  echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>Pts</th>";
                  echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>+/-</th>";
                  echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>PIM</th>";
                  echo "<th class='border border-slate-600 px-2 py-1' style='width: 9%'>Shots</th>";
                  echo "<th class='border border-slate-600 px-2 py-1' style='width: 8%'>Shot %</th>";
                  echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>PPG</th>";
                  echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>PP Pts</th>";
                  echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>SHG</th>";
                  echo "<th class='border border-slate-600 px-2 py-1' style='width: 8%'>SH Pts</th>";
                  echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>GWG</th>";
                  echo "<th class='border border-slate-600 px-2 py-1' style='width: 7%'>OTG</th>"; 
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $playoffsCareerGP . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $playoffsCareerGoals . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $playoffsCareerAssists . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $playoffsCareerPts . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $playoffsCareerPlusMinus . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $playoffsCareerPIM . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $playoffsCareerShots . "</td>";
                  $formatted_playoffsCareerShootingPct = round((float)$playoffsCareerShootingPct * 100, 1);
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $formatted_playoffsCareerShootingPct . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $playoffsCareerPPG . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $playoffsCareerPPPoints . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $playoffsCareerSHG . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $playoffsCareerSHPts . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $playoffsCareerGWG . "</td>";
                  echo "<td class='border border-slate-600 px-2 py-1'>" . $playoffsCareerOTGoals . "</td>";
                echo "</tr>";
                echo "</tbody>";
              echo "</table><br><br>";
            }

            echo "</div>"; // END LEFT COLUMN




// RIGHT COLUMN (bottom season-by-season table)
            echo "<div class='flex flex-col gap-8 lg:basis-[55%]'>";

            // echo "<p>" . $seasonTotals . "</p>";
        
      ### Season-by-Season Stats ###
          $seasonStatsSQL = "SELECT *
                             FROM player_season_stats 
                             WHERE playerID=$player_id 
                             ORDER BY seasonSeason ASC";
          $seasonStats = mysqli_query($conn, $seasonStatsSQL);

          echo "<h3 class='text-center text-2xl'>Season-by-Season Statistics</h3>";
          echo "<table class='player-season-by-season-stats-table default-zebra-table w-full'>";
          echo "<colgroup>";
            echo "<col class='season-by-season-season'>";
            echo "<col class='season-by-season-league'>";
            echo "<col class='season-by-season-team'>";
            echo "<col class='season-by-season-gametype'>";
            echo "<col class='season-by-season-gp'>";
            if (strtolower($position) == 'g') {
              echo "<col class='season-by-season-wins'>";
              echo "<col class='season-by-season-losses'>";
              echo "<col class='season-by-season-gaa'>";
              echo "<col class='season-by-season-savepct'>";
            } else {
              echo "<col class='season-by-season-goals'>";
              echo "<col class='season-by-season-assists'>";
              echo "<col class='season-by-season-pts'>";
              echo "<col class='season-by-season-pim'>";
            }
          echo "<thead>";
            echo "<tr>";
            if (strtolower($position) == 'g') {
              echo "<th class='border border-slate-600 px-2 py-1'>Season</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>League</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>Team Name</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>Season Type</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>GP</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>W</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>L</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>GAA</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>Sv %</th>";
            } else {
              echo "<th class='border border-slate-600 px-2 py-1'>Season</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>League</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>Team Name</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>Season Type</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>GP</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>G</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>A</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>Pts</th>";
              echo "<th class='border border-slate-600 px-2 py-1'>PIM</th>";
            }
            echo "</tr>";
          echo "</thead>";
          echo "<tbody>";

          # initializing variables to store career totals
          $totalGP = $totalG = $totalA = $totalPts = $totalPIM = $totalW = $totalL = $totalGAA = $totalSavePct = $count = 0;

          while ($row = mysqli_fetch_assoc($seasonStats)) {
              $count+=1;
              echo "<tr>";
              $formatted_season_1 = substr($row['seasonSeason'], 0, 4);
              $formatted_season_2 = substr($row['seasonSeason'], 4);
              echo "<td class='border border-slate-600 px-2 py-1'>".htmlspecialchars($formatted_season_1)."-".htmlspecialchars($formatted_season_2)."</td>";
              echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($row['seasonLeagueAbbrev']) . "</td>";
              echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($row['seasonTeamName']) . "</td>";
              $gameType_num = $row['seasonGameTypeId'];
                  if ($gameType_num == 1) {
                      $gameType_text = "Pre.";
                  } elseif ($gameType_num == 2) {
                      $gameType_text = "Reg.";
                  } elseif ($gameType_num == 3) {
                      $gameType_text = "Post.";
                  } else {
                      $gameType_text = "Unknown";
                  }
                  echo "<td class='border border-slate-600 px-2 py-1'>".$gameType_text."</td>";

              echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($row['seasonGamesPlayed'])) . "</td>";

              if (strtolower($position) == 'g') {
                echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($row['seasonWins'])) . "</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($row['seasonLosses'])) . "</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(number_format($row['seasonGAA'], 2)) . "</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(number_format($row['seasonSavePct'], 3)) . "</td>";
        
                // Tally up goalie stats
                $totalGP       += (int) $row['seasonGamesPlayed'];
                $totalW        += (int) $row['seasonWins'];
                $totalL        += (int) $row['seasonLosses'];
                $totalGAA      += (float) $row['seasonGAA'];
                $totalSavePct  += (float) $row['seasonSavePct'];
                $count++;
            } else {
                echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($row['seasonGoals'])) . "</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($row['seasonAssists'])) . "</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($row['seasonPoints'])) . "</td>";
                echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars(fillEmptyStats($row['seasonPIM'])) . "</td>";
        
                // Tally up skater stats
                $totalGP   += (int) $row['seasonGamesPlayed'];
                $totalG    += (int) $row['seasonGoals'];
                $totalA    += (int) $row['seasonAssists'];
                $totalPts  += (int) $row['seasonPoints'];
                $totalPIM  += (int) $row['seasonPIM'];
            }
        
            echo "</tr>";
          }
              
          if (strtolower($position) == 'g') {
            $avgGAA = $count > 0 ? $totalGAA / $count : 0;
            $avgSavePct = $count > 0 ? $totalSavePct / $count : 0;
        
            echo "<tr>";
              echo "<td colspan='4' rowspan='2' class='career-header border-2 border-slate-600 px-2 py-1'>Career Totals</td>";
              echo "<td class='career-subheader border-2 border-slate-600 px-2 py-1'>GP</td>";
              echo "<td class='career-subheader border-2 border-slate-600 px-2 py-1'>W</td>";
              echo "<td class='career-subheader border-2 border-slate-600 px-2 py-1'>L</td>";
              echo "<td class='career-subheader border-2 border-slate-600 px-2 py-1'>GAA</td>";
              echo "<td class='career-subheader border-2 border-slate-600 px-2 py-1'>Sv %</td>";
            echo "</tr>";
        
            echo "<tr>";
              echo "<td class='career-data border-2 border-slate-600 px-2 py-1'>$totalGP</td>";
              echo "<td class='career-data border-2 border-slate-600 px-2 py-1'>$totalW</td>";
              echo "<td class='career-data border-2 border-slate-600 px-2 py-1'>$totalL</td>";
              echo "<td class='career-data border-2 border-slate-600 px-2 py-1'>" . number_format($avgGAA, 2) . "</td>";
              echo "<td class='career-data border-2 border-slate-600 px-2 py-1'>" . number_format($avgSavePct, 3) . "</td>";
            echo "</tr>";
        } else {
            echo "<tr>";
              echo "<td colspan='4' rowspan='2' class='career-header border-2 border-slate-600 px-2 py-1'>Career Totals</td>";
              echo "<td class='career-subheader border-2 border-slate-600 px-2 py-1'>GP</td>";
              echo "<td class='career-subheader border-2 border-slate-600 px-2 py-1'>G</td>";
              echo "<td class='career-subheader border-2 border-slate-600 px-2 py-1'>A</td>";
              echo "<td class='career-subheader border-2 border-slate-600 px-2 py-1'>Pts</td>";
              echo "<td class='career-subheader border-2 border-slate-600 px-2 py-1'>PIM</td>";
            echo "</tr>";
        
            echo "<tr>";
              echo "<td class='career-data border-2 border-slate-600 px-2 py-1'>$totalGP</td>";
              echo "<td class='career-data border-2 border-slate-600 px-2 py-1'>$totalG</td>";
              echo "<td class='career-data border-2 border-slate-600 px-2 py-1'>$totalA</td>";
              echo "<td class='career-data border-2 border-slate-600 px-2 py-1'>$totalPts</td>";
              echo "<td class='career-data border-2 border-slate-600 px-2 py-1'>$totalPIM</td>";
            echo "</tr>";
        }

              
          echo "</tbody>";
          echo "</table><br><br>";

          echo "</div>"; // END RIGHT COLUMN
          echo "</div>"; // END OUTER WRAPPER


        ?>
        <br><br><br><br><br>

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
  </body>
</html>