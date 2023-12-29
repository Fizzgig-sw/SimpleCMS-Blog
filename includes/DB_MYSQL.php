<?php
//MySQL Database Connection - Version 1.0

function DBconnect($database,$host,$user,$pass){return mysqli_connect($host,$user,$pass,$database);}
function DBclose($link){mysqli_close($link);}
function DBquery($link,$query){return mysqli_query($link,$query);}
function DBnum_rows($result){return mysqli_num_rows($result);}
function DBresult($result,$i=0,$field=0){ 
    $rows = mysqli_num_rows($result); 
    if ($rows && $i <= ($rows-1) && $i >=0){
        mysqli_data_seek($result,$i);
        $data = (is_numeric($field)) ? mysqli_fetch_row($result) : mysqli_fetch_assoc($result);
        if (isset($data[$field])){ return $data[$field]; }
    }
    return false;
}
?>