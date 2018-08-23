<?php

namespace Dreamwhite\Assortment;

class Config
{
    const CITY = 'msk';

    const CITIES = ['spb', 'msk'];

    const DBUPDATEURLS = [
        'https://dreamwhite.ru/stock/update.php',
        'https://msk.dreamwhite.ru/stock/update.php',
    ];

    const SITES = [
        'https://dreamwhite.ru/',
        'https://msk.dreamwhite.ru/'
    ];

    const WPALLIMPORT_PROCESSING_URL = 'wp-cron.php?import_key=b504KBpDIN&import_id=1&action=processing';
    const WPALLIMPORT_TRIGGER_URL = 'wp-cron.php?import_key=b504KBpDIN&import_id=1&action=trigger';
}