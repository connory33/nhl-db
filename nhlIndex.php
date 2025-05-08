<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Connor Young</title>

    <!-- Bootstrap core CSS -->
    <link href="../resources/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <!-- <link href="../resources/css/album.css" rel="stylesheet"> -->

    <link rel="stylesheet" type="text/css" href="../resources/css/default_v3.css">

    <script src="https://cdn.tailwindcss.com"></script>
  </head><!--  -->
  <body>
<!-- Header -->
<?php include 'header.php'; ?>

<div class="nhlindex-bg-img flex items-center justify-center py-5 h-screen">  <!-- Set h-screen to take full viewport height -->
    <div id="nhlindex-content" class="text-center max-w-6xl mx-auto flex-grow flex flex-col mb-20">  <!-- flex-grow will stretch it vertically -->
        <h2 class="text-4xl font-bold text-white">NHL Historical Database</h2><br>
        <p>
            This site serves as a repository for historical data from the National Hockey League (NHL).<br><br>
            You can search past games by season, date, game type, start time, and teams. You can also search for players by name.
            <br><br>
            Search by player or game, view player detail pages with bio info and stats, team detail pages with historical rosters and stats, and game detail pages with rosters, play-by-play, and shift charts.
            Pages also exist to view historical playoff results and draft results.<br><br>
        </p>

        <div class="flex justify-center items-start">
            <form id="nhl-search" method="GET" action="nhl_games.php"
                class="px-4 sm:px-6 py-4 rounded-lg flex flex-col sm:flex-row gap-4 sm:items-center w-full max-w-4xl">
    
            <!-- Dropdown -->
            <select name="search_column" id='nhl-search-column' required
                class="w-full sm:w-auto flex-1 bg-white text-black text-sm rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
        <br>
        <div class='w-3/5 text-white text-center mx-auto'>
          <p class='font-semibold text-white'>Select any team below to view details:</p>
          <p>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=24'>ANA</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=53'>ARI</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=6'>BOS</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=7'>BUF</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=20'>CGY</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=12'>CAR</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=16'>CHI</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=21'>COL</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=29'>CBJ</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=25'>DAL</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=17'>DET</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=22'>EDM</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=13'>FLA</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=26'>LAK</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=30'>MIN</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=8'>MTL</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=1'>NJD</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=18'>NSH</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=2'>NYI</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=3'>NYR</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=9'>OTT</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=4'>PHI</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=5'>PIT</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=28'>SJS</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=55'>SEA</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=19'>STL</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=14'>TBL</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=10'>TOR</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=59'>UTA</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=23'>VAN</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=54'>VGK</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=52'>WPG</a><span class='text-slate-800'> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=15'>WSH</a></p><br>
        <a class='font-semibold text-white hover:text-blue-700' href='https://connoryoung.com/playoff_results.php?season_id=20232024'>View historical playoff info</a><br><br>
        <a class='font-semibold text-white hover:text-blue-700' href='https://connoryoung.com/draft_history.php?draft_id=63'>View historical draft info</a>
        </div>
        <br>
        <p>This database is a work in progress. For any bugs or feature requests, please reach out
          at connor@connoryoung.com.
        </p>
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

  </body>
</html>