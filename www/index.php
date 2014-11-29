<?php

namespace PPP\Wikipedia;

use PPP\Module\ModuleEntryPoint;

require_once(__DIR__ . '/../vendor/autoload.php');

$entryPoint = new ModuleEntryPoint(new WikipediaRequestHandler());
$entryPoint->exec();
