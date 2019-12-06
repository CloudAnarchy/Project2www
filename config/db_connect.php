<?php

// connect to database
$connect = mysqli_connect('localhost', 'nikos', 'nick9185GR', 'project2www');

// check connection
if(!$connect) $connect_info = 'Coneection error: ' . mysqli_connect_error();
else $connect_info = 'Connected!';
?>