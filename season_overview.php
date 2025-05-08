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

  <?php include 'header.php'; ?>

  <body>

    <div class="text-white" style='align-items: flex-start; background-color: #343a40'>

    <?php
      include('db_connection.php');

      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);

      if (isset($_GET['season_id'])) {
          $season_id = $_GET['season_id'];
?>
          <h1 class="text-3xl font-bold text-center mt-4">Season Overview: <?php echo $season_id ?></h1>

          <?php 
          $goals_sql = 'SELECT * FROM skater_past_season_leaders WHERE seasonID = $season_id AND statCategory = "Goals" ORDER BY statValue';
          $result = mysqli_query($conn, $goals_sql);
          if ($result) {
              if (mysqli_num_rows($result) > 0) {
                  echo "<table class='table-auto w-full text-left border-collapse border border-gray-800'>";
                  echo "<thead>";
                  echo "<tr>";
                  echo "<th class='border border-gray-800 px-4 py-2'>Season</th>";
                  echo "<th class='border border-gray-800 px-4 py-2'>Player</th>";
                  echo "<th class='border border-gray-800 px-4 py-2'>Season Type</th>";
                  echo "<th class='border border-gray-800 px-4 py-2'>Category</th>";
                  echo "<th class='border border-gray-800 px-4 py-2'>Value</th>";
                  echo "</tr>";
                  echo "</thead>";
                  echo "<tbody>";
      
                  while ($row = mysqli_fetch_assoc($result)) {
                      echo "<tr>";
                      echo "<td class='border border-gray-800 px-4 py-2'>" . $row['seasonID'] . "</td>";
                      echo "<td class='border border-gray-800 px-4 py-2'>" . $row['playerID'] . "</td>";
                      echo "<td class='border border-gray-800 px-4 py-2'>" . $row['seasonType'] . "</td>";
                      echo "<td class='border border-gray-800 px-4 py-2'>" . $row['statCategory'] . "</td>";
                      echo "<td class='border border-gray-800 px-4 py-2'>" . $row['statValue'] . "</td>";
                      echo "</tr>";
                  }
                  echo "</tbody>";
                  echo "</table>";
              } else {
                  echo "<p class='text-center text-white'>No data found for the selected season.</p>";
              }
          } else {
              echo "<p class='text-center text-white'>Error executing query: " . mysqli_error($conn) . "</p>";
          }
          mysqli_close($conn);
          






      } else {
            die("No season ID provided.");
    }

    ?>

       

    </div>

    <?php include 'footer.php'; ?>
  </body>


