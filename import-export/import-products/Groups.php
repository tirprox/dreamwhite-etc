<?php

class Groups {
	var $remoteGroups;
	var $groupsInConfig;
	var $groupArray = [];
	
	var $showUnusedGroups = false;
	var $stores = [
		"Флигель" => "baedb9ed-de2a-11e6-7a34-5acf00087a3f",
		"В белом" => "4488e436-07e7-11e6-7a69-971100273f23"
	];
	
	function getGroupsFromConfig() {
		
		$config = [
			"Свадебные пальто" => [
				"store"    => $this->stores[ 'В белом' ],
				"pathName" => "Аксессуары для свадьбы/Свадебные пальто",
            "id" => "cca342fd-07e7-11e6-7a69-93a700454ad1",
            "href" => "https://online.moysklad.ru/api/remap/1.1/entity/productfolder/cca342fd-07e7-11e6-7a69-93a700454ad1"
			],
			"Женские пальто"   => [
				"store"    => $this->stores[ 'Флигель' ],
				"pathName" => "Женские пальто",
            "id" => "cc91a970-07e7-11e6-7a69-93a700454ab8",
            "href" => "https://online.moysklad.ru/api/remap/1.1/entity/productfolder/cc91a970-07e7-11e6-7a69-93a700454ab8"
			],
			"Женские плащи"    => [
				"store"    => $this->stores[ 'Флигель' ],
				"pathName" => "Женские плащи",
            "id" => "2e7b7745-246a-11e7-7a31-d0fd00184b78",
            "href" => "https://online.moysklad.ru/api/remap/1.1/entity/productfolder/2e7b7745-246a-11e7-7a31-d0fd00184b78"
			],
			"Мужские пальто"   => [
				"store"    => $this->stores[ 'Флигель' ],
				"pathName" => "Мужские пальто",
            "id" => "3c1129d6-925a-11e7-7a69-971100078524",
            "href" => "https://online.moysklad.ru/api/remap/1.1/entity/productfolder/3c1129d6-925a-11e7-7a69-971100078524"
			],
			"Женские куртки"   => [
				"store"    => $this->stores[ 'Флигель' ],
				"pathName" => "Женские куртки",
            "id" => "c3e048e5-d358-11e7-7a6c-d2a900192646",
            "href" => "https://online.moysklad.ru/api/remap/1.1/entity/productfolder/c3e048e5-d358-11e7-7a6c-d2a900192646"
			],
		];

		$this->groupsInConfig = $config;
		
		$i = 1;
		Log::d( "Список групп в config.conf:" . "\n" );
		foreach ( $this->groupsInConfig as $groupName => $options ) {
			Log::d( $i . ". Name: " . $groupName . " ID: " . $options['store'] . "\n" );
			$i ++;
		}
		Log::d( "\n" );
	}
	
	
	function createGroups() {
      foreach ( $this->groupsInConfig as $groupName => $options ) {
            Log::d( "Добавляем группу: " . $groupName . "\n" );
            Log::d( "URL: " . $options['href'] . "\n" );
            Log::d( "ID: " . $options['id'] . "\n" );
            Log::d( "StoreID: " . $options['store'] . "\n" );
            Log::d( "\n" );
            
            $this->groupArray[] = new Group( $options['href'], $groupName, $options['id'], $options['store'], $options['pathName']);
      }
   }
	
	function getGroupsFromServer( $baseUrl, $context ) {
		$groupsUrl          = $baseUrl . "/entity/productFolder?" . "offset=0&limit=100";
		$this->remoteGroups = json_decode( file_get_contents( $groupsUrl, false, $context ) );
	}
	
	function getGroupArray() {
		foreach ( $this->remoteGroups->rows as $group ) {
			foreach ( $this->groupsInConfig as $configGroup => $options ) {
				if ( $group->name === $configGroup ) {
					Log::d( "Добавляем группу: " . $group->name . "\n" );
					Log::d( "URL: " . $group->meta->href . "\n" );
					Log::d( "ID: " . $group->id . "\n" );
					Log::d( "StoreID: " . $options['store'] . "\n" );
					Log::d( "\n" );
					
					$this->groupArray[] = new Group( $group->meta->href, $group->name, $group->id, $options['store'], $options['pathName']);
					continue;
				}
			}
		}
	}
}