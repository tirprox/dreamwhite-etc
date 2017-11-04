<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 05.11.2017
 * Time: 1:32
 */
include "CSVTagFactory.php";

$tagFactory = new CSVTagFactory();

$tagFactory->loadTagsFromFile();