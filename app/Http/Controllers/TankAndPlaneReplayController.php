<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;

class TankAndPlaneReplayController extends Controller
{
    /**
    * Main function called
    */
    public function getMatch($name){
      $client = new Client();
      $crawler = $client->request('GET', 'https://warthunder.com/en/tournament/replay/');
      $link = $this->getURL($crawler);

      if($link == 'https://login.gaijin.net/en/sso/login/'){
        $crawler = $this->login($client, $crawler);
      }else {
        return response()->json(['status' => 'error', 'code' => '401', 'message' => 'Not in login page']);
      }

      $matches = array();
      $crawler = $client->request('GET', 'https://warthunder.com/en/tournament/replay/type/replays?Filter%5Bstatistic_group%5D=mixed&Filter%5Bkeyword%5D=&Filter%5Bnick%5D='. $name .'&action=search');
      $matchid = $this->matchID($crawler, $client, $name);
      if(count($matchid) == 0){
        return response()->json(['status' => 'error', 'code' => '402', 'message' => 'User does not have any data']);
      }
      $matchname = $this->matchName($crawler, $client, $name);
      $gametype = $this->gameType($crawler, $client, $name);
      $gamemode = $this->gameMode($crawler, $client, $name);
      $vehicle = $this->gameVehicles($crawler, $client, $name);
      $time = $this->gameTime($crawler, $client, $name);

      $status = array('status' => 'success', 'name' => $name);
      $merge = $this->mergeArray($matchid, $matchname, 'Match Name');
      $merge = $status + $merge;
      //$merge = array_merge_recursive($status, $this->mergeArray($matchid, $matchname, 'Match Name'));
      $merge = $this->array_merge_recursive_distinct($merge, $this->mergeArray($matchid, $gametype, 'Game Type'));

      $merge = $this->array_merge_recursive_distinct ($merge, $this->mergeArray($matchid, $gamemode, 'Game Mode'));
      $merge = $this->array_merge_recursive_distinct ($merge, $this->mergeArray($matchid, $vehicle, 'Vehicle'));
      $merge = $this->array_merge_recursive_distinct ($merge, $this->mergeArray($matchid, $time, 'Time'));
      return $merge;
    }

    /**
    * Get Currnet URL
    */
    public function getURL($crawler){
      $char = $crawler->getUri();
      $link = '';
      for($i = 0; $i < 38; $i++){
        $link = $link . $char[$i];
      }
      return $link;
    }

    /**
    * Login to warthunder website
    */
    public function login($client, $crawler){
      $form = $crawler->selectButton('Authorization')->form();
      $crawler = $client->submit($form, array('login' => 'emailtonisarg@gmail.com', 'password' => 'Basketball123'));
      return $crawler;
    }

    /**
    * Get all matchids
    */
    public function matchID($crawler, $client, $name){
      $matchids = array();
      $matchids[] = $crawler->filter('.replay__item')->extract(array('data-replay'));
      $i = 2;
      while($crawler->filter('.next')->each(function ($node) {return $node->text();})){
        $crawler = $client->request('GET', 'https://warthunder.com/en/tournament/replay/page/'. $i .'?Filter%5Bstatistic_group%5D=mixed&Filter%5Bkeyword%5D=&Filter%5Bnick%5D='. $name .'&action=search');
        $matchids[] = $crawler->filter('.replay__item')->extract(array('data-replay'));
        $i++;
      }
      $matchid = $this->combineArray($matchids);
      return $matchid;

    }
    /**
    * Match names
    */
    public function matchName($crawler, $client, $name){
      $matchnames = array();
      $matchnames[] = $crawler->filter('.replay__title')->each(function($node){
        return $node->text();
      });

      $i = 2;
      while($crawler->filter('.next')->each(function ($node) {return $node->text();})){
        $crawler = $client->request('GET', 'https://warthunder.com/en/tournament/replay/page/'. $i .'?Filter%5Bstatistic_group%5D=mixed&Filter%5Bkeyword%5D=&Filter%5Bnick%5D='. $name .'&action=search');
        $matchnames[] = $crawler->filter('.replay__title')->each(function($node){
          return $node->text();
        });
        $i++;
      }
      $matchname = $this->combineArray($matchnames);
      return $matchname;
    }

    /**
    * Game Type
    */
    public function gameType($crawler, $client, $name){
      $gametypes = array();
      $gametypes[] = $crawler->filter('span.stat__label:contains("Game type:") + .stat__value')->each(function($node){
        return $node->text();
      });

      $i = 2;
      while($crawler->filter('.next')->each(function ($node) {return $node->text();})){
        $crawler = $client->request('GET', 'https://warthunder.com/en/tournament/replay/page/'. $i .'?Filter%5Bstatistic_group%5D=mixed&Filter%5Bkeyword%5D=&Filter%5Bnick%5D='. $name .'&action=search');
        $gametypes[] = $crawler->filter('span.stat__label:contains("Game type:") + .stat__value')->each(function($node){
          return $node->text();
        });
        $i++;
      }
      $gametype = $this->combineArray($gametypes);

      return $gametype;
    }

    /**
    * Game Mode
    */
    public function gameMode($crawler, $client, $name){
      $gamemodes = array();
      $gamemodes[] = $crawler->filter('span.stat__label:contains("Game mode:") + .stat__value')->each(function($node){
        return $node->text();
      });

      $i = 2;
      while($crawler->filter('.next')->each(function ($node) {return $node->text();})){
        $crawler = $client->request('GET', 'https://warthunder.com/en/tournament/replay/page/'. $i .'?Filter%5Bstatistic_group%5D=tank&Filter%5Bkeyword%5D=&Filter%5Bnick%5D='. $name .'&action=search');
        $gamemodes[] = $crawler->filter('span.stat__label:contains("Game mode:") + .stat__value')->each(function($node){
          return $node->text();
        });
        $i++;
      }
      $gamemode = $this->combineArray($gamemodes);

      return $gamemode;
    }

    /**
    * Game vehicles
    */
    public function gameVehicles($crawler, $client, $name){
      $vehicles = array();
      $vehicles[] = $crawler->filter('span.stat__label:contains("Vehicles:") + .stat__value')->each(function($node){
        return $node->text();
      });

      $i = 2;
      while($crawler->filter('.next')->each(function ($node) {return $node->text();})){
        $crawler = $client->request('GET', 'https://warthunder.com/en/tournament/replay/page/'. $i .'?Filter%5Bstatistic_group%5D=tank&Filter%5Bkeyword%5D=&Filter%5Bnick%5D='. $name .'&action=search');
        $vehicles[] = $crawler->filter('span.stat__label:contains("Vehicles:") + .stat__value')->each(function($node){
          return $node->text();
        });
        $i++;
      }
      $vehicle = $this->combineArray($vehicles);

      return $vehicle;
    }

    /**
    * Game time
    */
    public function gameTime($crawler, $client, $name){
      $times = array();
      $times[] = $crawler->filter('.icon.icon--time + .inlined.text-left')->each(function($node){
        return $node->text();
      });

      $i = 2;
      while($crawler->filter('.next')->each(function ($node) {return $node->text();})){
        $crawler = $client->request('GET', 'https://warthunder.com/en/tournament/replay/page/'. $i .'?Filter%5Bstatistic_group%5D=tank&Filter%5Bkeyword%5D=&Filter%5Bnick%5D='. $name .'&action=search');
        $times[] = $crawler->filter('.icon.icon--time + .inlined.text-left')->each(function($node){
          return $node->text();
        });
        $i++;
      }
      $time = $this->combineArray($times);

      return $time;
    }

    /**
    * Combine 2 arrays
    */
    public function combineArray($arr1){
      $combarr = array();
      for($i = 0; $i < count($arr1); $i++){
        for($j = 0; $j < count($arr1[$i]); $j++){
          $combarr[] = $arr1[$i][$j];
        }
      }
      return $combarr;
    }

    /**
    * Merge Arrays
    */
    public function mergeArray($arrs1, $arrs2, $titles){
      $size = count($arrs1);
      $i = 0;
      foreach ($arrs1 as $arr1) {
        $merge[$arr1][$titles] = $arrs2[$i];
        $i++;
      }
      return $merge;
    }
    public function array_merge_recursive_distinct () {
      $arrays = func_get_args();
      $base = array_shift($arrays);
      if(!is_array($base)) $base = empty($base) ? array() : array($base);
      foreach($arrays as $append) {
        if(!is_array($append)) $append = array($append);
        foreach($append as $key => $value) {
          if(!array_key_exists($key, $base) and !is_numeric($key)) {
            $base[$key] = $append[$key];
            continue;
          }
          if(is_array($value) or is_array($base[$key])) {
            $base[$key] = $this->array_merge_recursive_distinct($base[$key], $append[$key]);
          } else if(is_numeric($key)) {
            if(!in_array($value, $base)) $base[] = $value;
          } else {
            $base[$key] = $value;
          }
        }
      }
      return $base;
    }

}
