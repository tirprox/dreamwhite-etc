<?php
namespace Dreamwhite\Assortment;

require_once "vendor/autoload.php";

require_once "AssortmentManager.php";

require_once "generators/CSVReportGenerator.php";
require_once "generators/XMLReportGenerator.php";
require_once "generators/XMLShortReportGenerator.php";
require_once "generators/JSONShortReportGenerator.php";
require_once "generators/JSONStockGenerator.php";

require_once "tags/TagFactory.php";
require_once "tags/InvertableAttribute.php";
require_once "tags/Tag.php";

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


