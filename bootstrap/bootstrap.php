<?php
/**
 * Bootstrap.
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/constants.php';

global $hrhub;

$hrhub = require __DIR__ . '/../config/container.php';

return $hrhub;
