<?php

class Groups {
   var $remoteGroups;
   var $groupsInConfig;
   var $groupArray = [];
   
   var $showUnusedGroups = false;
   
   function getGroupsFromConfig() {
      $this->groupsInConfig = file(__DIR__ . "/config.conf");
      $this->groupsInConfig = array_map("mb_strtolower", $this->groupsInConfig);
      $this->groupsInConfig = array_map("trim", $this->groupsInConfig);
      
      $i = 1;
      Log::d("Список групп в config.conf:" . "\n");
      foreach ($this->groupsInConfig as $key => $groupName) {
         Log::d($i . ". " . $groupName . "\n");
         $i++;
      }
      Log::d("\n");
   }
   
   function getGroupsFromServer($baseUrl, $context) {
      $groupsUrl = $baseUrl . "/entity/productFolder?" . "offset=0&limit=100";
      $this->remoteGroups = json_decode(file_get_contents($groupsUrl, false, $context));
   }
   
   function getGroupArray() {
      foreach ($this->remoteGroups->rows as $group) {
         if (in_array(mb_strtolower($group->name), $this->groupsInConfig)) {
   
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
         }
      }
   }
}