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
				"pathName" => "Аксессуары для свадьбы/Свадебные пальто"
			],
			"Женские пальто"   => [
				"store"    => $this->stores[ 'Флигель' ],
				"pathName" => "Женские пальто"
			],
			"Женские плащи"    => [
				"store"    => $this->stores[ 'Флигель' ],
				"pathName" => "Женские плащи"
			],
			"Мужские пальто"   => [
				"store"    => $this->stores[ 'Флигель' ],
				"pathName" => "Мужские пальто"
			],
			"Женские куртки"   => [
				"store"    => $this->stores[ 'Флигель' ],
				"pathName" => "Женские куртки"
			],
		];
		
		/*$this->groupsInConfig = file(__DIR__ . "/config.conf");
		$this->groupsInConfig = array_map("mb_strtolower", $this->groupsInConfig);
		$this->groupsInConfig = array_map("trim", $this->groupsInConfig);*/
		$this->groupsInConfig = $config;
		
		$i = 1;
		Log::d( "Список групп в config.conf:" . "\n" );
		foreach ( $this->groupsInConfig as $groupName => $options ) {
			Log::d( $i . ". Name: " . $groupName . " ID: " . $options['store'] . "\n" );
			$i ++;
		}
		Log::d( "\n" );
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
			
			/*if (in_array(mb_strtolower($group->name), $this->groupsInConfig)) {
	  
			   Log::d("Добавляем группу: " . $group->name . "\n");
			   Log::d("url: " . $group->meta->href . "\n");
			   Log::d("id: " . $group->id . "\n");
			   Log::d("\n");
			   
			   $this->groupArray[] = new Group($group->meta->href, $group->name, $group->id);
			}
			else {
			   if ($this->showUnusedGroups) {
				  Log::d("Группа " . $group->name . " не используется\n");
			   }
			}*/
		}
	}
}