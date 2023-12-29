<?php
/*
TO DO
=====
-?topic pages
-?add comments

Finalize
========
-turn off error_reporting in config
-change version
*/

require_once('config.php');
require_once('includes/functions.php');

//Connect to the database
$link = DBconnect(database('database'),database('host'),database('user'),database('pass'));
create_tables($link); config();

//Main Document
require_once('includes/handler.php');
require_once('content/template.php');

//Close the database
DBclose($link);
?>
