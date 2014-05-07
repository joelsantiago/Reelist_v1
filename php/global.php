<?php
// Database information
$host = 'jsmysql.joelsantiago.co';
$user = 'jsantiago';
$pass = 'toymbl1';
$name = 'reelist_db';

// variable $db that is assigned with the database information to be used globally throughout the site when querying the database
$db = new MySQL($host,$user,$pass,$name);
?>