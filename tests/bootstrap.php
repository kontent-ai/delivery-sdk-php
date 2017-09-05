<?php

require_once __DIR__ . '/../vendor/autoload.php';

foreach (glob(__DIR__ . '/E2E/*Model.php') as $filename)
{
    require_once $filename;
}