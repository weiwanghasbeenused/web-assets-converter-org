<?php

function m_pad($id){
    $output = strval($id);
    while(strlen($output) < 5)
        $output = '0' . $output;
    return $output;
}