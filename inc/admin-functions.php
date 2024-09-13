<?php
function make_array_shallow($array)
{
    $shallowArray = array();

    foreach ($array as $value) {
        if (is_array($value)) {
            $shallowArray = array_merge($shallowArray, make_array_shallow($value));
        } else {
            $shallowArray[] = $value;
        }
    }

    return $shallowArray;
}

function dd($val)
{
    echo '<pre>';
    print_r($val);
    echo '</pre>';
}