<!-- TEMPLATE FOR GAME DETAIL PAGES - GETS ID SELECTED ON GAMES PAGE AND USES IT TO QUERY DATABASE FOR ADDITIONAL GAME DETAILS -->

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
 <!-- Header -->
 <?php include 'header.php'; ?>
  <body>
    <div class="text-white text-center" style='background-color: #343a40'>
        <br><br>

        <?php
        include('db_connection.php');

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Check if the 'game_id' is passed in the URL
        if (isset($_GET['game_id'])) {
                $game_id = $_GET['game_id'];

                ###################### DEFINING ALL SQL QUERIES ################################
                # Fetch all roster data to allow PHP lookup instead of SQL joins for speed
                $roster_lookup = [];
                $rosterSQL = "SELECT nhl_rosters.playerID, nhl_players.firstName, nhl_players.lastName 
                              FROM nhl_rosters
                              JOIN nhl_players ON nhl_rosters.playerID = nhl_players.playerId
                              WHERE nhl_rosters.gameID='$game_id'";
                $players = mysqli_query($conn, $rosterSQL);

                while ($row = mysqli_fetch_assoc($players)) {
                    $roster_lookup[$row['playerID']] = $row['firstName'] . ' ' . $row['lastName'];
                }
                $roster_lookup[0] = '-';

                # Fetch all team data to allow PHP lookup instead of SQL joins for speed
                $team_name_lookup = [];
                $team_tricode_lookup = [];
                $teamSQL = "SELECT id, fullName, triCode FROM nhl_teams";
                $teams = mysqli_query($conn, $teamSQL);

                while ($row = mysqli_fetch_assoc($teams)) {
                    $team_name_lookup[$row['id']] = $row['fullName'];
                    $team_tricode_lookup[$row['id']] = $row['triCode'];
                }

                # Roster Tables
                $rostertableSQL = "SELECT nhl_rosters.gameID,
                            nhl_rosters.teamID,
                            nhl_rosters.playerID,
                            nhl_players.firstName,
                            nhl_players.lastName,
                            nhl_players.headshot,
                            nhl_teams.id,
                            nhl_teams.fullName,
                            nhl_teams.triCode,
                            -- SUM(skaters_gamebygame_stats.sog) AS total_shots,
                            -- SUM(skaters_gamebygame_stats.blockedShots) AS total_blocked_shots,
                            -- SUM(skaters_gamebygame_stats.missedShots) AS total_missed_shots,
                            nhl_games.homeTeamID as homeTeamID, nhl_games.awayTeamID as awayTeamID,
                            skaters_gamebygame_stats.playerID AS skater_playerID,
                            skaters_gamebygame_stats.sweaterNumber AS skater_sweaterNumber,
                            skaters_gamebygame_stats.position AS skater_position,
                            skaters_gamebygame_stats.goals AS skater_goals,
                            skaters_gamebygame_stats.assists AS skater_assists,
                            skaters_gamebygame_stats.points AS skater_points,
                            skaters_gamebygame_stats.plusMinus AS skater_plusMinus,
                            skaters_gamebygame_stats.pim AS skater_pim,
                            skaters_gamebygame_stats.hits AS skater_hits,
                            skaters_gamebygame_stats.powerPlayGoals AS skater_powerPlayGoals,
                            skaters_gamebygame_stats.sog AS skater_sog,
                            skaters_gamebygame_stats.faceoffWinningPctg AS skater_faceoffWinningPctg,
                            skaters_gamebygame_stats.toi AS skater_toi,
                            skaters_gamebygame_stats.blockedShots AS skater_blockedShots,
                            skaters_gamebygame_stats.shifts AS skater_shifts,
                            skaters_gamebygame_stats.giveaways AS skater_giveaways,
                            skaters_gamebygame_stats.takeaways AS skater_takeaways,
                            goalies_gamebygame_stats.sweaterNumber AS goalie_sweaterNumber,
                            goalies_gamebygame_stats.position AS goalie_position,
                            goalies_gamebygame_stats.pim AS goalie_pim,
                            goalies_gamebygame_stats.toi AS goalie_toi,
                            goalies_gamebygame_stats.evenStrengthShotsAgainst AS goalie_evenStrengthShotsAgainst,
                            goalies_gamebygame_stats.powerPlayShotsAgainst AS goalie_powerPlayShotsAgainst,
                            goalies_gamebygame_stats.shorthandedShotsAgainst AS goalie_shorthandedShotsAgainst,
                            goalies_gamebygame_stats.saveShotsAgainst AS goalie_saveShotsAgainst,
                            goalies_gamebygame_stats.savePctg AS goalie_savePctg,
                            goalies_gamebygame_stats.evenStrengthGoalsAgainst AS goalie_evenStrengthGoalsAgainst,
                            goalies_gamebygame_stats.powerPlayGoalsAgainst AS goalie_powerPlayGoalsAgainst,
                            goalies_gamebygame_stats.shorthandedGoalsAgainst AS goalie_shorthandedGoalsAgainst,
                            goalies_gamebygame_stats.goalsAgainst AS goalie_goalsAgainst,
                            goalies_gamebygame_stats.starter AS goalie_starter,
                            goalies_gamebygame_stats.shotsAgainst AS goalie_shotsAgainst,
                            goalies_gamebygame_stats.saves AS goalie_saves
                                FROM nhl_rosters 
                                JOIN nhl_teams ON nhl_rosters.teamID = nhl_teams.id
                                JOIN nhl_games ON nhl_rosters.gameID = nhl_games.id
                                LEFT JOIN nhl_players 
                                    ON nhl_rosters.playerID = nhl_players.playerId
                                LEFT JOIN skaters_gamebygame_stats 
                                    ON nhl_rosters.playerID = skaters_gamebygame_stats.playerID 
                                    AND nhl_rosters.gameID = skaters_gamebygame_stats.gameID
                                    AND skaters_gamebygame_stats.position IS NOT NULL

                                LEFT JOIN goalies_gamebygame_stats 
                                    ON nhl_rosters.playerID = goalies_gamebygame_stats.playerID 
                                    AND nhl_rosters.gameID = goalies_gamebygame_stats.gameID
                                    AND goalies_gamebygame_stats.position IS NOT NULL

                                WHERE nhl_rosters.gameID='$game_id'
                                ORDER BY nhl_players.lastName";

                try {
                    $rosters_result = mysqli_query($conn, $rostertableSQL);
                } catch (mysqli_sql_exception $e) {
                    echo "MySQL Error: " . $e->getMessage();
                    exit;
                }

                # Play-by-Play Table
                $playsSQL = "SELECT 
                nhl_plays.gameID,
                nhl_plays.period,
                nhl_plays.timeRemaining,
                nhl_plays.typeDescKey,
                nhl_plays.xCoord,
                nhl_plays.yCoord,
                nhl_plays.eventOwnerTeamId,
                nhl_plays.zoneCode,
                nhl_plays.faceoffLoserId,
                nhl_plays.faceoffWinnerId,
                nhl_plays.hittingPlayerId,
                nhl_plays.hitteePlayerId,
                nhl_plays.shotType,
                nhl_plays.shootingPlayerId,
                nhl_plays.goalieInNetId,
                nhl_plays.awaySOG,
                nhl_plays.homeSOG,
                nhl_plays.reason,
                nhl_plays.takeawayGiveawayPlayerId,
                nhl_plays.blockingPlayerId,
                nhl_plays.scoringPlayerId,
                nhl_plays.assist1PlayerId,
                nhl_plays.awayScore,
                nhl_plays.homeScore,
                nhl_plays.penaltySeverity,
                nhl_plays.penaltyType,
                nhl_plays.committerId,
                nhl_plays.drawerId,
                nhl_games.homeTeamId as home_team_id,
                nhl_games.awayTeamId as away_team_id,
                home_teams.fullName as home_team_name,
                -- home_teams.color1 as home_team_color1,
                away_teams.fullName as away_team_name,
                -- away_teams.color1 as away_team_color1,
                event_team.triCode as event_team_tricode,
                nhl_games.gameDate,
                nhl_games.venue,
                nhl_games.venueLocation,
                nhl_games.easternStartTime,
                nhl_games.gameStateId
                FROM 
                nhl_plays

                LEFT JOIN nhl_games
                ON nhl_plays.gameID = nhl_games.id

                LEFT JOIN nhl_teams AS home_teams
                ON nhl_games.homeTeamId = home_teams.id

                LEFT JOIN nhl_teams AS away_teams
                ON nhl_games.awayTeamId = away_teams.id

                LEFT JOIN nhl_teams AS event_team
                ON nhl_plays.eventOwnerTeamId = event_team.id

                WHERE
                nhl_plays.gameID = '$game_id'

                ORDER BY 
                nhl_plays.period, nhl_plays.timeInPeriod ASC";

                try {
                $plays = mysqli_query($conn, $playsSQL);
                } catch (mysqli_sql_exception $e) {
                echo "MySQL Error: " . $e->getMessage();
                exit;
                }

                // if (mysqli_num_rows($result) > 0) {
                // print("<br> Results found: " . mysqli_num_rows($result) . "<br><br>");
                // } else {
                // print("No results found.<br>");
                // }

                $row = mysqli_fetch_assoc($plays);
                // $home_team_name = $row['home_team_name'];
                // $away_team_name = $row['away_team_name'];

                if ($row !== null && isset($row['gameDate'])) {
                    $game_date = $row['gameDate'];
                } else {
                    $game_data = '';
                }
                

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
                home_teams.id AS homeTeamId,
                home_teams.fullName AS home_team_name,
                away_teams.fullName AS away_team_name,
                away_teams.id AS awayTeamId,
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
                    $homeTeamID = $row['homeTeamId'];
                    $awayTeamID = $row['awayTeamId'];
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
                
                    echo "<div class='w-fit max-w-[95%] mx-auto bg-slate-800 text-white py-6 px-4 rounded-lg shadow-lg mb-8 border-2 border-slate-600'>";
                    echo "<div class='flex flex-col items-center space-y-4 flex-grow'>"; // Removed flex-grow on the outer container

                    // Team logos and names
                    echo "<div class='flex items-center justify-center space-x-6'>"; // Keep the spacing but remove flex-grow
                    echo "<a href='https://connoryoung.com/team_details.php?team_id=" . htmlspecialchars($homeTeamID) . "'>" . "<img src='" . htmlspecialchars($homeLogo) . "' alt='homeLogo' class='h-20 max-w-xs'>" . "</a>"; // Added max-width for logos
                    echo "<a class='text-4xl font-bold text-center whitespace-nowrap' href='https://connoryoung.com/team_details.php?team_id=>" . htmlspecialchars($homeTeamID) . "'>" . htmlspecialchars($homeTeamName) . " (H)</a> <span class='mx-2 text-4xl'>vs.</span>" . "<a class='text-3xl font-bold text-center whitespace-nowrap' href='https://connoryoung.com/team_details.php?team_id=>" . htmlspecialchars($awayTeamID) . "'>" . htmlspecialchars($awayTeamName) . " (A)</a></h3>";
                    echo "<a href='https://connoryoung.com/team_details.php?team_id=" . htmlspecialchars($awayTeamID) . "'>" . "<img src='" . htmlspecialchars($awayLogo) . "' alt='homeLogo' class='h-20 max-w-xs'>" . "</a>"; // Added max-width for logos
                    echo "</div>";
                    
                    // Score line
                    echo "<h3 class='text-3xl font-semibold'>" . htmlspecialchars($homeScore) . " - " . htmlspecialchars($awayScore) . " <span class='text-3xl font-semibold ml-2'>" . $formatted_outcome . "</span></h3>";
                    
                    // Venue and time
                    echo "<p class='text-lg'>" . htmlspecialchars($venue) . ", " . htmlspecialchars($venueLocation) . "<br>" . htmlspecialchars($formatted_gameDate) . " " . htmlspecialchars($formatted_startTime) . " EST</p>";
                    
                    // Season and game info
                    echo "<p class='text-base italic'>" . $formatted_season . " " . $gameType_text . " - Game Number " . $gameNum . "</p>";
                    
                    echo "</div>"; // flex-col container
                    echo "</div>"; // banner wrapper
                    
                    
                    echo "<hr style='width:80%; background-color:white' class='mx-auto'>";

                
                }

                    // echo "<p>Test</p> " . print_r($rosters_result);



                    ###################################### ROSTER TABLES #####################################
                    
                    $home_players = [];
                    $away_players = [];

                    while ($row = mysqli_fetch_assoc($rosters_result)) {
                        // print_r($row);
                        if ($row['teamID'] == $row['homeTeamID']) {
                            $home_players[] = $row;
                            $home_team_name = $row['fullName'];
                        } elseif ($row['teamID'] == $row['awayTeamID']) {
                            $away_players[] = $row;
                            $away_team_name = $row['fullName'];
                        }
                    }


                    $home_skaters = array_filter($home_players, fn($p) => $p['skater_position'] !== null && $p['skater_position'] !== '');
                    $home_goalies = array_filter($home_players, fn($p) => $p['goalie_position'] !== null && $p['goalie_position'] !== '');

                    $away_skaters = array_filter($away_players, fn($p) => $p['skater_position'] !== null && $p['skater_position'] !== '');
                    $away_goalies = array_filter($away_players, fn($p) => $p['goalie_position'] !== null && $p['goalie_position'] !== '');


                    function render_skater_table($players, $team_label, $roster_lookup) {
                        echo "<h4 class='roster-title text-2xl'>$team_label</h4>";
                        echo "<div class='roster-table-wrapper'>";
                        echo "<table class='roster-table default-zebra-table'>";
                        echo "<colgroup>";
                        echo "<col class='game_details_skater_stats_name'>";
                        echo "<col class='game_details_skater_stats_number'>";
                        echo "<col class='game_details_skater_stats_position'>";
                        echo "<col class='game_details_skater_stats_goals'>";
                        echo "<col class='game_details_skater_stats_assists'>";
                        echo "<col class='game_details_skater_stats_points'>";
                        echo "<col class='game_details_skater_stats_plusminus'>";
                        echo "<col class='game_details_skater_stats_pim'>";
                        echo "<col class='game_details_skater_stats_hits'>";
                        echo "<col class='game_details_skater_stats_powerplaygoals'>";
                        echo "<col class='game_details_skater_stats_sog'>";
                        echo "<col class='game_details_skater_stats_faceoffpctg'>";
                        echo "<col class='game_details_skater_stats_toi'>";
                        echo "<col class='game_details_skater_stats_blockedshots'>";
                        echo "<col class='game_details_skater_stats_shifts'>";
                        echo "<col class='game_details_skater_stats_giveaways'>";
                        echo "<col class='game_details_skater_stats_takeaways'>";
                        echo "<thead class='default-zebra-table'>";
                        echo "<tr style='color: white; font-weight: bold; background-color: #1F2833' class='default-zebra-table'>";
                        echo "<th class='border border-slate-600 px-2 py-1'>Name</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>#</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>Pos</th>";
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
                        echo "<th class='border border-slate-600 px-2 py-1'>Blocks</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>Shifts</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>Give</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>Take</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        foreach ($players as $player) {
                            $player_id = $player['playerID'];
                            $player_name = $roster_lookup[$player_id] ?? 'Unknown';
                            $position = $player['skater_position'];
                            $positionDisplay = ($position == 'L') ? 'LW' : (($position == 'R') ? 'RW' : $position);
                            $formatted_FOPctg = number_format($player['skater_faceoffWinningPctg'] * 100, 1);
                            if ($formatted_FOPctg == 0) {
                                $formatted_FOPctg = '-';
                            }
                            // $totalShots = $row['total_shots'] ?? 0;
                            // $totalBlockedShots = $row['total_blocked_shots'] ?? 0;
                            // $totalMissedShots = $row['total_missed_shots'] ?? 0;
                            
                            echo "<tr class='default-zebra-table'>";
                            echo "<td class='border border-slate-600 px-2 py-1'><a style='color:navy' href='player_details.php?player_id=" . htmlspecialchars($player_id) ."'>$player_name</a></td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['skater_sweaterNumber'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $position . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_goals']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_assists']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_points']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_plusMinus']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_pim']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_hits']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_powerPlayGoals']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_sog']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($formatted_FOPctg) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_toi']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_blockedShots']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_shifts']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_giveaways']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_takeaways']) . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";
                        echo "</table></div>";
                    }
                    
                    function render_goalie_table($players, $team_label, $roster_lookup) {
                        echo "<div class='roster-table-wrapper'>";
                        echo "<table class='roster-table default-zebra-table border-2 border-slate-600'>";
                            echo "<colgroup>";
                                echo "<col class='game_details_goalie_stats_name'>";
                                echo "<col class='game_details_goalie_stats_number'>";
                                echo "<col class='game_details_goalie_stats_pim'>";
                                echo "<col class='game_details_goalie_stats_toi'>";
                                echo "<col class='game_details_goalie_stats_evenStrengthShotsAgainst'>";
                                echo "<col class='game_details_goalie_stats_powerPlayShotsAgainst'>";
                                echo "<col class='game_details_goalie_stats_shorthandedShotsAgainst'>";
                                echo "<col class='game_details_goalie_stats_saveShotsAgainst'>";
                                echo "<col class='game_details_goalie_stats_savePctg'>";
                                echo "<col class='game_details_goalie_stats_evenStrengthGoalsAgainst'>";
                                echo "<col class='game_details_goalie_stats_powerPlayGoalsAgainst'>";
                                echo "<col class='game_details_goalie_stats_shorthandedGoalsAgainst'>";
                                echo "<col class='game_details_goalie_stats_goalsAgainst'>";
                                echo "<col class='game_details_goalie_stats_starter'>";
                                echo "<col class='game_details_goalie_stats_shotsAgainst'>";
                                echo "<col class='game_details_goalie_stats_saves'>";
                            echo "</colgroup>";
                            echo "<thead>";
                                echo "<tr style='color: white; font-weight: bold;' class='default-zebra-table bg-slate-800'>"; // Added missing opening <tr> tag
                                    echo "<th class='border border-slate-600 px-2 py-1'>Name</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>Number</t class='border border-slate-600 px-2 py-1'h>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>PIM</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>TOI</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>Even SA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>PP SA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>SH SA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>Sv SA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>Sv %</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>Even GA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>PP GA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>SH GA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>GA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>Starter</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>SA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>Saves</th>";
                                echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                    
                        foreach ($players as $player) {
                            $player_id = $player['playerID'];
                            $player_name = $roster_lookup[$player_id] ?? 'Unknown';
                            $goalie_savePctg = $player['goalie_savePctg'];
                            if ($goalie_savePctg == '') {
                                $goalie_savePctg = '-';
                            }
                            $starter = $player['goalie_starter'];
                            if ($starter == 'True') {
                                $player_name = $player_name . " (S)";
                            }
                                                
                            echo "<tr class='default-zebra-table'>";
                            echo "<td class='border border-slate-600 px-2 py-1'><a style='color:navy' href='player_details.php?player_id=" . htmlspecialchars($player_id) ."'>$player_name</a></td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_sweaterNumber'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_pim'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_toi'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_evenStrengthShotsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_powerPlayShotsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_shorthandedShotsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_saveShotsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $goalie_savePctg . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_evenStrengthGoalsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_powerPlayGoalsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_shorthandedGoalsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_goalsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_starter'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_shotsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_saves'] . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table></div>";
                    }
                    

        }
        if (empty($home_players)) {
            echo "<p>No roster data available.</p>";
        } else {
            echo "<br><br>";
            echo "<h4 class='text-center text-4xl'>Game Rosters & Statistics</h4>";
            echo "<div class='roster-container'>";
            echo "<div class='team-column'>";
            render_skater_table($home_skaters, $home_team_name, $roster_lookup);
            render_goalie_table($home_goalies, $home_team_name, $roster_lookup);
            echo "</div>";

            echo "<div class='team-column'>";
            render_skater_table($away_skaters, $away_team_name, $roster_lookup);
            render_goalie_table($away_goalies, $away_team_name, $roster_lookup);
            echo "</div>";

            echo "</div>"; // end .roster-container
            echo "<p class='text-center text-sm'>(S) indicates the starting goalie</p>";
            echo "<br>";

        }
        
            echo "<hr style='width:80%; background-color:white' class='mx-auto'>";
            echo "<br><a class='font-bold' href='https://connoryoung.com/shift_charts.php?game_id=" . $game_id . "'>Click here to view shift charts for this game.</a><br><br>";
            echo "<hr style='width:80%; background-color:white' class='mx-auto'>";


        ?>

        <br><br>


<!-- -------------------------------------------- PLAY-BY-PLAY --------------------------------------- -->
        <?php
        ### Pagination logic ###
        $limit = 25; // Results per page
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;


        // Get total count (for Load More logic)
        $count_sql = "SELECT COUNT(*) as total FROM nhl_plays WHERE nhl_plays.gameID = $game_id";
        $count_result = mysqli_query($conn, $count_sql);
        $total_rows = mysqli_fetch_assoc($count_result)['total'];

        $start = $offset + 1;
        $end = min($offset + $limit, $total_rows);
        $total_pages = ceil($total_rows / $limit);

        

        $plays_sql = "SELECT * FROM nhl_plays WHERE nhl_plays.gameID = $game_id ORDER BY nhl_plays.period, nhl_plays.timeInPeriod ASC LIMIT $offset, $limit";
        $plays = mysqli_query($conn, $plays_sql);
        echo "<h4 class='text-center text-4xl'>Play-by-Play</h4><br>";

        if (mysqli_num_rows($plays) > 0) {
        ?>



<!-- <h4 class="text-2xl font-semibold mb-4 text-white text-center">Play-by-Play Key</h4> -->

<div class="bg-gray-800 rounded-lg p-6 text-sm text-white shadow-lg border-2 border-slate-600 max-w-4xl mx-auto">
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-2 gap-x-8">
    <p><strong>FO</strong> – Faceoff</p>
    <p><strong>SOG</strong> – Shot on Goal</p>
    <p><strong>Pen.</strong> – Penalty</p>
    <p><strong>Block</strong> – Blocked Shot</p>
    <p><strong>Miss</strong> – Missed Shot</p>
    <p><strong>Stop</strong> – Stoppage</p>
    <p><strong>Give</strong> – Giveaway</p>
    <p><strong>Take</strong> – Takeaway</p>
    <p><strong>D. Pen.</strong> – Delayed Penalty</p>
    <p><strong>Back</strong> – Backhand</p>
    <p><strong>Tip</strong> – Tip-in</p>
  </div>
</div>

<br>




<div class='max-w-full mx-auto text-center'>
  <h4 class='text-2xl font-semibold mb-4 text-white'>Rink Diagram / Coordinates</h4>

  <div class="relative inline-block w-1/2 max-w-full" id="rink-container">
    <img src="../resources/images/hockey-rink2.jpg" id="rink-image" class="w-full h-auto mx-auto" />
    <canvas id="heatmap-canvas" class="absolute top-0 left-0 w-full h-full"></canvas>
    <!-- <div id="marker" style="
      position: absolute;
      width: 8px;
      height: 8px;
      background: red;
      border-radius: 50%;
      display: none;
      pointer-events: none;
    "></div> -->
  </div>
</div>

<div id="filters" class="text-center mt-4 flex justify-between max-w-[50%] mx-auto">
    <label><input type="checkbox" class="shot-filter" value="goal" checked> Goals</label>
    <label><input type="checkbox" class="shot-filter" value="missed-shot" checked> Missed Shots</label>
    <label><input type="checkbox" class="shot-filter" value="shot-on-goal" checked> Shots on Net</label>
    <label><input type="checkbox" class="shot-filter" value="blocked-shot" checked> Blocked Shots</label>
    <label><input type="checkbox" class="shot-filter" value="hit" unchecked> Hits</label>
</div>


<br>




<!-- <script>

function drawMarker(x, y) {
    const marker = document.getElementById('marker');
    if (!marker) return;

    const { x: xPx, y: yPx } = transformCoords(x, y);

    marker.style.left = `${xPx - 4}px`;  // center the 8x8 marker
    marker.style.top = `${yPx - 4}px`;
    marker.style.display = 'block';
}


document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('.play-row');

    rows.forEach(row => {
        row.addEventListener('click', function () {
            // Remove highlight from all rows
            rows.forEach(r => r.classList.remove('default-selected-row'));

            // Add highlight to the clicked row
            this.classList.add('default-selected-row');

            // Get the x and y coordinates from the data attributes
            const x = parseFloat(this.dataset.x);
            const y = parseFloat(this.dataset.y);
            
            // Log the coordinates for debugging
            console.log(`Marker coordinates: (${x}, ${y})`);

            // Move the marker
            drawMarker(x, y);
        });
    });
});

</script> -->



<!-- <h4 class='text-2xl font-semibold mb-4 text-white'>Play-by-Play Events</h4> -->
<div class="overflow-x-auto">
    <table id="play-by-play-table" class="min-w-max table-auto border-2 border-slate-600 border-collapse default-zebra-table">
        <thead class='default-zebra-table'>
            <tr class='default-zebra-table'>
                <th class='pbp-col-time-left border-2 border-slate-600'>Per. Time Left</th>
                <th class='pbp-col-type border-2 border-slate-600'>Type</th>
                <th class='pbp-col-coords border-2 border-slate-600'>Coords.</th>
                <th class='pbp-col-team border-2 border-slate-600'>Team</th>
                <th class='pbp-col-fo-winner border-2 border-slate-600'>F/O Winner</th>
                <th class='pbp-col-fo-loser border-2 border-slate-600'>F/O Loser</th>
                <th class='pbp-col-hitter border-2 border-slate-600'>Hitter</th>
                <th class='pbp-col-hittee border-2 border-slate-600'>Hittee</th>
                <th class='pbp-col-shot-type border-2 border-slate-600'>Shot Type</th>
                <th class='pbp-col-shooter border-2 border-slate-600'>Shooter</th>
                <th class='pbp-col-goalie border-2 border-slate-600'>Goalie</th>
                <th class='pbp-col-reason border-2 border-slate-600'>Reason</th>
                <th class='pbp-col-take-give border-2 border-slate-600'>Taker / Giver</th>
                <th class='pbp-col-blocker border-2 border-slate-600'>Blocker</th>
                <th class='pbp-col-scorer border-2 border-slate-600'>Scorer</th>
                <th class='pbp-col-primary-assister border-2 border-slate-600'>1st Assist</th>
                <th class='pbp-col-penalty border-2 border-slate-600'>Penalty</th>
                <th class='pbp-col-committer border-2 border-slate-600'>Committer</th>
                <th class='pbp-col-drawer border-2 border-slate-600'>Drawer</th>
            </tr>
        </thead>
        <tbody class='default-zebra-table'>
                    <?php
                        $all_plays = [];
                        while ($row = $plays->fetch_assoc()){
                            # Coordinates
                            $formatted_coordinates = $row['xCoord'] . '/' . $row['yCoord'];
                            // echo "<td>".$row['xCoord']."</td>";
                            // echo "<td>".$row['yCoord']."</td>";

                            # saving values for use in onclick event
                            $xCoord = $row['xCoord'];
                            $yCoord = $row['yCoord'];
                            $type = $row['typeDescKey'];

                            echo "<tr class='play-row' 
                            data-x='{$xCoord}' 
                            data-y='{$yCoord}' 
                            data-typedesckey='{$type}'
                            style='color: white; border: 1px solid #bcd6e7'>";
                            


                            $all_plays[] = [
                                'x' => $xCoord,
                                'y' => $yCoord,
                                'typedesckey' => $type
                            ];
    




                            # Period/Time Remaining
                            $formatted_time = $row['period'] . ' - ' . substr($row['timeRemaining'],0,5);
                            echo "<td>".$formatted_time."</td>";

                            // echo "<td>".$row['situationCode']."</td>";
                            // echo "<td>".$row['typeCode']."</td>";
                            $eventType = $row['typeDescKey'];
                            if ($eventType == 'goal') {
                                $rowClass = 'goal-row';
                            }

                            # Play Type
                            // echo "<td>".$row['typeDescKey']."</td>";
                            $playType = $row['typeDescKey'];
                            if ($playType == 'period-start') {
                                $formatted_playType = 'Per. Start';
                            } else if ($playType == 'faceoff') {
                                $formatted_playType = 'FO';
                            } else if ($playType == 'hit') {
                                $formatted_playType = 'Hit';
                            } else if ($playType == 'shot-on-goal') {
                                $formatted_playType = 'SOG';
                            } else if ($playType == 'goal') {
                                $formatted_playType = 'Goal';
                            } else if ($playType == 'stoppage') {
                                $formatted_playType = 'Stop';
                            } else if ($playType == 'giveaway') {
                                $formatted_playType = 'Give';
                            } else if ($playType == 'takeaway') {
                                $formatted_playType = 'Takea';
                            } else if ($playType == 'blocked-shot') {
                                $formatted_playType = 'Block';
                            } else if ($playType == 'missed-shot') {
                                $formatted_playType = 'Miss';
                            } else if ($playType == 'penalty') {
                                $formatted_playType = 'Pen.';
                            } else if ($playType == 'delayed-penalty') {
                                $formatted_playType = 'D. Pen.';
                            } else if ($playType == 'period-end') {
                                $formatted_playType = 'Per. End';
                            } else {
                                $formatted_playType = $playType;
                            }
                            echo "<td>".htmlspecialchars($formatted_playType)."</td>";

                            echo "<td>" . $formatted_coordinates . "</td>";

                            # Event Team
                            echo "<td>".($row['event_team_tricode'] ?? 'N/A')."</td>";

                            # Faceoffs
                            $faceoff_winner_id = $row['faceoffWinnerId'];
                            $faceoff_winner_name = isset($roster_lookup[$faceoff_winner_id]) ? $roster_lookup[$faceoff_winner_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($faceoff_winner_name)."</td>";
                            
                            $faceoff_loser_id = $row['faceoffLoserId'];
                            $faceoff_loser_name = isset($roster_lookup[$faceoff_loser_id]) ? $roster_lookup[$faceoff_loser_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($faceoff_loser_name)."</td>";

                            # Hits
                            $hitter_id = $row['hittingPlayerId'];
                            $hitter_name = isset($roster_lookup[$hitter_id]) ? $roster_lookup[$hitter_id] : 'Unknown';
                            $hittee_id = $row['hitteePlayerId'];
                            $hittee_name = isset($roster_lookup[$hittee_id]) ? $roster_lookup[$hittee_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($hitter_name)."</td>";
                            echo "<td>".htmlspecialchars($hittee_name)."</td>";
                            
                            # Shot Type
                            $formatted_shotType = ucfirst($row['shotType']);
                            if ($formatted_shotType == 'Backhand') {
                                $formatted_shotType = 'Back';
                            } else if ($formatted_shotType == 'Tip-in') {
                                $formatted_shotType = 'Tip';
                            } else if ($formatted_shotType == ''){
                                $formatted_shotType = '-';
                            } else {
                                $formatted_shotType = $formatted_shotType;
                            }
                            
                            echo "<td>".htmlspecialchars($formatted_shotType)."</td>";

                            # Shooting Player
                            $shooter_id = $row['shootingPlayerId'];
                            $shooter_name = isset($roster_lookup[$shooter_id]) ? $roster_lookup[$shooter_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($shooter_name)."</td>";

                            # Goalie
                            $goalie_id = $row['goalieInNetId'];
                            $goalie_name = isset($roster_lookup[$goalie_id]) ? $roster_lookup[$goalie_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($goalie_name)."</td>";

                            // echo "<td>".$row['awaySOG']."</td>";
                            // echo "<td>".$row['homeSOG']."</td>";

                            # Reason
                            $reason = $row['reason'];
                            if ($reason == 'wide-right') {
                                $formatted_reason = 'Wide right';
                            } else if ($reason == 'high-and-wide-right') {
                                $formatted_reason = 'High / wide right';
                            } else if ($reason == 'wide-left') {
                                $formatted_reason = 'Wide left';
                            } else if ($reason == 'high-and-wide-left') {
                                $formatted_reason = 'High / wide left';
                            } else if ($reason == 'puck-frozen') {
                                $formatted_reason = 'Puck frozen';
                            } else if ($reason == 'goalie-stopped-after-sog') {
                                $formatted_reason = 'Goalie freeze';
                            } else if ($reason == 'tv-timeout') {
                                $formatted_reason = 'TV';
                            } else if ($reason == 'hit-crossbar') {
                                $formatted_reason = 'Crossbar';
                            } else if ($reason == 'above-crossbar') {
                                $formatted_reason = 'Over net';
                            } else if ($reason == 'hit-right-post') {
                                $formatted_reason = 'Right post';
                            } else if ($reason == 'hit-left-post') {
                                $formatted_reason = 'Left post';
                            } else if ($reason == 'puck-in-netting' || $reason == 'puck-in-benches' || $reason == 'puck-in-penalty-benches') {
                                $formatted_reason = 'Out of play';
                            } else if ($reason == 'player-injury') {
                                $formatted_reason = 'Injury';
                            } else if ($reason == 'offside') {
                                $formatted_reason = 'Offside';
                            } else if ($reason == 'icing') {
                                $formatted_reason = 'Icing';
                            } else if ($reason == 'hand-pass') {
                                $formatted_reason = 'Hand pass';
                            } else {
                                $formatted_reason = '-';
                            }
                            echo "<td>".$formatted_reason."</td>";

                            # Takeaway/Giveaway Player
                            $take_give_id = $row['takeawayGiveawayPlayerId'];
                            $take_give_name = isset($roster_lookup[$take_give_id]) ? $roster_lookup[$take_give_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($take_give_name)."</td>";

                            # Shot Blocker
                            $blocker_id = $row['blockingPlayerId'];
                            $blocker_name = isset($roster_lookup[$blocker_id]) ? $roster_lookup[$blocker_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($blocker_name)."</td>";

                            # Scorer
                            $scorer_id = $row['scoringPlayerId'];
                            $scorer_name = isset($roster_lookup[$scorer_id]) ? $roster_lookup[$scorer_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($scorer_name)."</td>";

                            # Assister
                            $assister_id = $row['assist1PlayerId'];
                            $assister_name = isset($roster_lookup[$assister_id]) ? $roster_lookup[$assister_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($assister_name)."</td>";

                            # Score
                            // echo "<td>".$row['awayScore']."</td>";
                            // echo "<td>".$row['homeScore']."</td>";

                            # Penalty
                            if ($row['penaltySeverity'] == 'MIN') {
                                $formatted_severity = '(2)';
                            } else if ($row['penaltySeverity'] == 'MAJ') {
                                $formatted_severity = '(4)';
                            } else if ($row['penaltySeverity'] == ''){
                                $formatted_severity = '-';
                            } else {
                                $formatted_severity = 'ERROR';
                            }
                            echo "<td>" . $row['penaltyType'] . ' ' . $formatted_severity . "</td>";
                            $committer_id = $row['committerId'];
                            $committer_name = isset($roster_lookup[$committer_id]) ? $roster_lookup[$committer_id] : 'Unknown';
                            $drawer_id = $row['drawerId'];
                            $drawer_name = isset($roster_lookup[$drawer_id]) ? $roster_lookup[$drawer_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($committer_name)."</td>";
                            echo "<td>".htmlspecialchars($drawer_name)."</td>";
                            

                            echo "</tr>";
                        }


                    
                echo "</tbody>";

                echo "</table>";


                $total_pages = ceil($total_rows / $limit);


                // Fetch all plays (no LIMIT)
                $all_plays_query = "SELECT * FROM nhl_plays WHERE gameID = $game_id";
                $plays_result = mysqli_query($conn, $all_plays_query);
                $all_plays = [];
                while ($row = mysqli_fetch_assoc($plays_result)) {
                    $all_plays[] = $row;
                }  
                // print_r($all_plays);     
                ?>
                </div>
                <br><br>
                
                <script>const allPlays = <?= json_encode($all_plays) ?>;</script>
                <script src="https://cdn.jsdelivr.net/npm/simpleheat/simpleheat.js"></script>
                <script>
    const rinkXMin = -100, rinkXMax = 100;
    const rinkYMin = -42.5, rinkYMax = 42.5;
    const rink = document.getElementById("rink-image");
    const heatmapCanvas = document.getElementById("heatmap-canvas");
    const heat = simpleheat(heatmapCanvas);

    const validTypes = ['shot-on-goal', 'missed-shot', 'goal', 'blocked-shot'];
    let shotPoints = []; // Store the filtered shot points

    function updateCanvasSize() {
        heatmapCanvas.width = rink.clientWidth;
        heatmapCanvas.height = rink.clientHeight;
        heat.resize(); // Resize heatmap to match updated canvas dimensions
    }

    function transformCoords(x, y) {
        const width = rink.clientWidth;
        const height = rink.clientHeight;

        const paddingX = 0.1;
        const paddingY = 0.1;

        const usableWidth = width * (1 - 2 * paddingX);
        const usableHeight = height * (1 - 2 * paddingY);

        const xPx = (x - rinkXMin) / (rinkXMax - rinkXMin) * usableWidth + width * paddingX;
        const yPx = height - ((y - rinkYMin) / (rinkYMax - rinkYMin) * usableHeight + height * paddingY);

        const clampedX = Math.max(0, Math.min(width, xPx));
        const clampedY = Math.max(0, Math.min(height, yPx));

        return [clampedX, clampedY];
    }

    function filterShotData(selectedTypes) {
        const pointMap = new Map();

        // Filter all plays by the selected shot types
        const filteredPlays = allPlays.filter(play => selectedTypes.includes(play.typeDescKey));

        // Transform and aggregate shot points
        filteredPlays.forEach(play => {
            const x = parseFloat(play.xCoord);
            const y = parseFloat(play.yCoord);

            if (!isNaN(x) && !isNaN(y)) {
                const [xPx, yPx] = transformCoords(x, y);
                const key = `${Math.round(xPx)},${Math.round(yPx)}`;
                if (!pointMap.has(key)) {
                    pointMap.set(key, [xPx, yPx, 1]); // Initial intensity of 1
                } else {
                    pointMap.get(key)[2] += 1; // Increment intensity for repeated points
                }
            }
        });

        // Convert pointMap values to an array and apply intensity scaling
        return Array.from(pointMap.values()).map(point => {
            point[2] = Math.log(point[2] + 1); // Intensity scaling
            return point;
        });
    }

    function updateHeatmap() {
        // Get selected shot types from the checkboxes
        const selectedTypes = Array.from(document.querySelectorAll('.shot-filter:checked')).map(cb => cb.value);

        // Filter shot points and redraw the heatmap
        shotPoints = filterShotData(selectedTypes);
        drawHeatmap(shotPoints);
    }

    function drawHeatmap(points) {
        heat.clear(); // Clears any previous data
        heat.data(points); // Set the data to the points with intensity

        const maxIntensity = Math.max(...points.map(point => point[2]), 1); // Avoid max = 0
        heat.max(maxIntensity); // Set max intensity

        heat.radius(30, 15); // Adjust radius if necessary
        heat.gradient({
            0.3: 'rgba(0, 0, 255, 1)',     // blue
            0.5: 'rgba(0, 255, 0, 1)',     // lime
            0.8: 'rgba(255, 165, 0, 1)',   // orange
            1.0: 'rgba(255, 0, 0, 1)'      // red
        });

        heat.draw(); // Draw the heatmap
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateCanvasSize();

        // Initial rendering with all shot types
        shotPoints = filterShotData(validTypes);
        drawHeatmap(shotPoints);

        // Add event listeners to filters
        const filters = document.querySelectorAll('.shot-filter');
        filters.forEach(filter => filter.addEventListener('change', updateHeatmap));
    });

    // Resize listener to adjust canvas size and redraw the heatmap
    window.addEventListener('resize', () => {
        updateCanvasSize();
        drawHeatmap(shotPoints);
    });
</script>


                <script>

                document.addEventListener('DOMContentLoaded', () => {
                updateCanvasSize();

                const shotPoints = [];

                const validTypes = ['shot-on-goal', 'missed-shot', 'goal', 'blocked-shot'];

                const pointMap = new Map();

                allPlays.forEach(play => {
                const x = parseFloat(play.xCoord);
                const y = parseFloat(play.yCoord);
                const type = play.typeDescKey;

                if (!isNaN(x) && !isNaN(y) && validTypes.includes(type)) {
                    const [xPx, yPx] = transformCoords(x, y);
                    const key = `${Math.round(xPx)},${Math.round(yPx)}`;
                    if (!pointMap.has(key)) {
                        pointMap.set(key, [xPx, yPx, 1]);
                    } else {
                        pointMap.get(key)[2] += 1;
                    }
                    }
                });
                const shotPoints = Array.from(pointMap.values());

                drawHeatmap(shotPoints);
                });

                window.addEventListener('resize', () => {
                updateCanvasSize();
                drawHeatmap(shotPoints); // re-render after resize
                });

                </script>



                <div style = 'text-align:center'>
                <?php if ($total_rows > 0): ?>
                    <br><div style="margin-bottom: 10px;">
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
                    echo "<div style='text-align:center; margin-top: 20px;'>
                        <a class='btn btn-secondary' href='?" . $prev_page . "' style='margin-right: 10px'>Previous</a>";
                    echo "<a class='btn btn-secondary' href='?" . $advance_page . "'>Next</a>
                        </div>";
                } else {
                    $prev_page = $page - 1;
                    echo "<div style='text-align:center; margin-top: 20px;'>
                        <a class='btn btn-secondary' href='?" . $prev_page . "'>Previous</a></div>";
                }
        

            mysqli_close($conn);
            
        } else {
                echo "<br><p style='text-align: center'>No play-by-play data available for this game.</p>";
        }
        ?>
            <br>
        </div>

        <?php include 'footer.php'; ?>

    </div>

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