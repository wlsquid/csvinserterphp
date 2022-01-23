<?php 
$file = fopen("users.csv","r");

while (($data = fgetcsv($file)) !== FALSE)
{
    echo $data[0] . $data[1] . $data[2];
}