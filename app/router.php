<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
if (PHP_SAPI != 'cli-server') { // run not in cli-server
    echo 'This script can be run from the CLI-server only.';

    return true;
} elseif ($_SERVER['SCRIPT_NAME'] == '/update.log' || $_SERVER['SCRIPT_NAME'] == '/app_dev.php') {
    return false; // immediately return the update log or dev
} else {
    include __DIR__.'/../web/app.php';

    return true;
}
