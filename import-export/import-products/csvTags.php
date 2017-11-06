<?php
/**
 *
 * Debug file for testing tags
 *
 */
include "CSVTagFactory.php";

$tagFactory = new CSVTagFactory();

$tagFactory->loadTagsFromFile();