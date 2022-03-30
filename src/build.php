<?php namespace Lockshire\Minecraft\Tools;

require_once('Book.php');
require_once('BookBuilder.php');
require_once('BookCommandBuilder.php');

$mode = php_sapi_name();

$builder = new BookBuilder($mode);
$builder->build();
$builder->output($builder->getCommand());
