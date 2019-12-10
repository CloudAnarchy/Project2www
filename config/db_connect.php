<?php

// connect to database
// mysql://bcb3aac8f0de15:081e1143@eu-cdbr-west-02.cleardb.net/heroku_8e47c5bea080f34?reconnect=true
$connect = mysqli_connect('eu-cdbr-west-02.cleardb.net', 'bcb3aac8f0de15', '081e1143', 'heroku_8e47c5bea080f34', 3306);
// $connect = mysqli_connect('localhost', 'nikos', 'nick9185GR', 'project2www');

// check connection
if(!$connect) $connect_info = 'Coneection error: ' . mysqli_connect_error();
else $connect_info = 'Connected!';
?>