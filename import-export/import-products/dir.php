<?php
/**
 * Created by PhpStorm.
 * User: DreamWhite
 * Date: 06.11.2017
 * Time: 11:49
 */

echo json_encode(scandir(__DIR__), JSON_UNESCAPED_UNICODE);