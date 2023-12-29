<?php
//SQLite3 Database Connection - Version 1.0

function DBconnect($database){return new SQLite3($database, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);}
function DBclose($link){$link->close();}
function DBquery($link,$query){return $link->query($query);}
function DBnum_rows($result){$n=0; while($result->fetchArray()){$n++;} return $n;}
function DBresult($result,$i=0,$field=0){
    $n=0;
    $value = false;
    while ($row = $result->fetchArray()) {
        if($n==$i){if(isset($row[$field])){$value=$row[$field];}}
        $n++;
    }
    return $value;
}
?>