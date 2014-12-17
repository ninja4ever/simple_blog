<?php
include 'tool_functions.php';
__autoload('dbconfig.php');
__autoload('core/user.php');

$user = new user;

$logout = $user->logout();

?>