<?php
namespace Dreamwhite\Import;
class Groups
{
    var $remoteGroups;
    var $groupsInConfig;
    var $groupArray = [];
    const base = "https://online.moysklad.ru/api/remap/1.1/entity/productfolder";

    var $showUnusedGroups = false;
    var $stores = [
        "Флигель" => "baedb9ed-de2a-11e6-7a34-5acf00087a3f",
        "В белом" => "4488e436-07e7-11e6-7a69-971100273f23",
        "Склад" => "4488e436-07e7-11e6-7a69-971100273f23",

    ];

    /*var $config = [
        "Свадебные пальто" => [
            "store" => "В белом",
            "pathName" => "Аксессуары для свадьбы/Свадебные пальто",
            "id" => "cca342fd-07e7-11e6-7a69-93a700454ad1",
        ],
        "Женские пальто" => [
            "store" => "Флигель",
            "pathName" => "Женские пальто",
            "id" => "cc91a970-07e7-11e6-7a69-93a700454ab8",
        ],
        "Женские плащи" => [
            "store" => "Флигель",
            "pathName" => "Женские плащи",
            "id" => "2e7b7745-246a-11e7-7a31-d0fd00184b78",
        ],
        "Мужские пальто" => [
            "store" => "Флигель",
            "pathName" => "Мужские пальто",
            "id" => "3c1129d6-925a-11e7-7a69-971100078524",
        ],
        "Женские куртки" => [
            "store" => "Флигель",
            "pathName" => "Женские куртки",
            "id" => "c3e048e5-d358-11e7-7a6c-d2a900192646",
        ],
        "Женские парки" => [
            "store" => "Флигель",
            "pathName" => "Женские куртки/Женские парки",
            "id" => "ccba5668-d359-11e7-7a31-d0fd00191b3a",
        ],

        "SS18" => [
            "store" => "Флигель",
            "pathName" => "Женские пальто/SS18",
            "id" => "1ada01b0-30f5-11e8-9107-5048001661e8",
        ],
    ];*/

    function getGroupsFromConfig()
    {

        /*foreach ($this->config as $name => $group) {
           $group['store'] = $this->stores[$group['store']];
           $group['href'] = self::base . $group['id'];
        }*/

        $config = [
            "Свадебные пальто" => [
                "store" => $this->stores['В белом'],
                "pathName" => "Аксессуары для свадьбы/Свадебные пальто",
                "category" => "Свадебные пальто",
                "id" => "cca342fd-07e7-11e6-7a69-93a700454ad1",
                "href" => "https://online.moysklad.ru/api/remap/1.1/entity/productfolder/cca342fd-07e7-11e6-7a69-93a700454ad1"
            ],
            "Женские пальто" => [
                "store" => $this->stores['Флигель'],
                "pathName" => "Женские пальто",
                "category" => "Женские пальто",
                "id" => "cc91a970-07e7-11e6-7a69-93a700454ab8",
                "href" => "https://online.moysklad.ru/api/remap/1.1/entity/productfolder/cc91a970-07e7-11e6-7a69-93a700454ab8"
            ],
            "Женские плащи" => [
                "store" => $this->stores['Флигель'],
                "pathName" => "Женские плащи",
                "category" => "Женские плащи",
                "id" => "2e7b7745-246a-11e7-7a31-d0fd00184b78",
                "href" => "https://online.moysklad.ru/api/remap/1.1/entity/productfolder/2e7b7745-246a-11e7-7a31-d0fd00184b78"
            ],
            "Мужские пальто" => [
                "store" => $this->stores['Флигель'],
                "pathName" => "Мужские пальто",
                "category" => "Мужские пальто",
                "id" => "3c1129d6-925a-11e7-7a69-971100078524",
                "href" => "https://online.moysklad.ru/api/remap/1.1/entity/productfolder/3c1129d6-925a-11e7-7a69-971100078524"
            ],
            "Женские куртки" => [
                "store" => $this->stores['Флигель'],
                "pathName" => "Женские куртки",
                "category" => "Женские куртки",
                "id" => "c3e048e5-d358-11e7-7a6c-d2a900192646",
                "href" => "https://online.moysklad.ru/api/remap/1.1/entity/productfolder/c3e048e5-d358-11e7-7a6c-d2a900192646"
            ],
            "Женские парки" => [
                "store" => $this->stores['Флигель'],
                "pathName" => "Женские куртки/Женские парки",
                "category" => "Женские парки",
                "id" => "ccba5668-d359-11e7-7a31-d0fd00191b3a",
                "href" => "https://online.moysklad.ru/api/remap/1.1/entity/productfolder/ccba5668-d359-11e7-7a31-d0fd00191b3a"
            ],

            "SS18" => [
                "store" => $this->stores['Флигель'],
                "pathName" => "Женские пальто/SS18",
                "category" => "SS18",
                "id" => "1ada01b0-30f5-11e8-9107-5048001661e8",
                "href" => "https://online.moysklad.ru/api/remap/1.1/entity/productfolder/1ada01b0-30f5-11e8-9107-5048001661e8"
            ],


            "Жилеты" => [
                "store" => $this->stores['Флигель'],
                "pathName" => "Женские пальто/Жилеты",
                "category" => "Жилеты",
                "id" => "90e5d7fc-3819-11e8-9107-504800189a1b",
                "href" => "https://online.moysklad.ru/api/remap/1.1/entity/productfolder/90e5d7fc-3819-11e8-9107-504800189a1b"
            ],

        ];


        $this->groupsInConfig = $config;

    }

    function createGroups()
    {
        foreach ($this->groupsInConfig as $groupName => $options) {
            Log::d($groupName, "groups");
            Log::d("URL: " . $options['href'], "groups");
            Log::d("ID: " . $options['id'], "groups");
            Log::d("StoreID: " . $options['store'], "groups");

            $this->groupArray[] = new Group($options['href'], $groupName, $options['id'], $options['store'], $options['pathName'], $options['category']);
        }

    }

    function getGroupsFromServer($baseUrl, $context)
    {
        $groupsUrl = $baseUrl . "/entity/productFolder?" . "offset=0&limit=100";
        $this->remoteGroups = json_decode(file_get_contents($groupsUrl, false, $context));
    }

    function getGroupArray()
    {
        foreach ($this->remoteGroups->rows as $group) {
            foreach ($this->groupsInConfig as $configGroup => $options) {
                if ($group->name === $configGroup) {
                    Log::d($group->name, "groups");
                    Log::d("URL: " . $group->meta->href, "groups");
                    Log::d("ID: " . $group->id, "groups");
                    Log::d("StoreID: " . $options['store'], "groups");
                    Log::d();

                    $this->groupArray[] = new Group($group->meta->href, $group->name, $group->id, $options['store'], $options['pathName']);
                    continue;
                }
            }
        }
    }
}