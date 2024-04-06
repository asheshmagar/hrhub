<?php
/**
 * Bootstrap.
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../configs/constants.php';

global $hrhub;

$hrhub = require __DIR__ . '/../configs/container.php';

return $hrhub;
