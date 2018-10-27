<?php

function formatSimpleDate($time = 0)
{
    $time = $time == 0 ? time() : $time;
    return date('Y-m-d', $time);
}

function formatFullDate($time = 0)
{
    $time = $time == 0 ? time() : $time;
    return date('Y-m-d H:i:s', $time);
}