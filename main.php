<?php namespace Lockshire\Minecraft\Tools;

require_once('Book.php');
require_once('BookBuilder.php');
require_once('CommandBuilder.php');

$mode = php_sapi_name();

$builder = new BookBuilder($mode);
$builder->exec();
$builder->output($builder->getCommand());
