<?php
error_reporting(0);

//Choose a datbase MYSQL or SQLITE (Only use one or the other).
require_once('includes/DB_SQLITE.php');
//require_once('includes/DB_MYSQL.php');

const version = 'v0.5-RC1';

// If EDIT_PIN is blank just add '&edit' to the url.
// Otherwize use '&edit=x' ('x' must match your EDIT_PIN).
const EDIT_PIN = ''; 

//Database Config
function database($key=''){
    //For SQLITE only a path to the db file is needed.
    //MYSQL requires server credentials.
    $database=[
        'database' => 'content/data/content.db',
        'host' => '',
        'user' => '',
        'pass' => ''
    ];

    return isset($database[$key]) ? $database[$key] : null;
}

//Table Config
function table($key='', $link=''){
    $table=[
        'settings'=>'settings',
        'page_content'=>'page_content',
        'tags'=>'tags'
    ];

    return isset($table[$key]) ? $table[$key] : null;
}
?>
