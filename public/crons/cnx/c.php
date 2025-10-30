<?php
$mysqli = new mysqli("localhost", "jok", "Arn@u1b3rt@", "patinscat");

// Check connection
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli->connect_error;
  exit();
}

?>
