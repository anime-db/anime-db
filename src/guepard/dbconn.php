<?php

//$db_type  = 'mysql';
//$db_host  = 'localhost';
//$db_name  = 'anime';
$db_user  = 'anime-db';
$db_pass  = 'rE53g1e5kN';
$db_debug = false;
//$db_dsn   = 'mysql:host=localhost;port=3306;dbname=anime';
$db_dsn	  = 'sqlite:'.dirname(ROOT).'/catalog.db';


@set_time_limit(60);