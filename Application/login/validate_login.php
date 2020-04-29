﻿<?php

use DB\MySql;
use DB\DBInterface;
use DB\EntityGateway;
use Login\UserEntity;

define("LOGIN", "TRUE");


$documentRoot = ".././";

require_once $documentRoot.'Models/userEntity.php';
require_once $documentRoot.'Interfaces/DBInterface.php';
require_once $documentRoot.'DB/DB.php';
require_once $documentRoot.'DB/entityGateway.php';

require_once $documentRoot."special_characters_handler.php";
require_once $documentRoot."functions.php";


$logfile = $documentRoot."Log/thesis.log";


$user = new UserEntity( EntityGateway::getDB() );


/**
 * lname = loginName, lpass = loginPassword
 */

if( $user->Login( $_GET["lName"], $_GET["lPass"] ) )
{
	echo $user->result;
}
