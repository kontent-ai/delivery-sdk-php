<?php

require_once __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);

foreach (glob(__DIR__ . '/E2E/*Model.php') as $filename)
{
    require_once $filename;
}

require_once __DIR__ . '/E2E/Utils/TestMapper.php';
require_once __DIR__ . '/E2E/Utils/CustomContentLinkUrlResolver.php';
