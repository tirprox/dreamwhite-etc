<?php
namespace Dreamwhite\Assortment;

require_once "vendor/autoload.php";

require_once "AssortmentManager.php";
require_once "ImportHandler.php";
require_once "MongoTagAdapter.php";

require_once "generators/XMLReportGenerator.php";
require_once "generators/XMLTaxonomyListGenerator.php";

require_once "generators/JSONShortReportGenerator.php";
require_once "generators/JSONWriter.php";

require_once "tags/TagFactory.php";
require_once "tags/TagMap.php";
require_once "tags/InvertableAttribute.php";
require_once "tags/Tag.php";
require_once "tags/CsvTagParser.php";

require_once "Product.php";
require_once "ProductVariant.php";
require_once "Group.php";
require_once "Groups.php";

require_once "tools/Log.php";
require_once "tools/Tools.php";
require_once "tools/Timers.php";

require_once "config/Auth.php";
require_once "config/Settings.php";
require_once "config/Config.php";

require_once "Connector.php";


