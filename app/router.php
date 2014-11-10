<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

// run not in cli-server
if (PHP_SAPI != 'cli-server') {
    exit('This script can be run from the CLI-server only.');
}

// immediately return the update log or dev
if ($_SERVER['SCRIPT_NAME'] == '/update.log' || $_SERVER['SCRIPT_NAME'] == '/app_dev.php') {
    return false;
} else {
    include __DIR__.'/../web/app.php';
}
