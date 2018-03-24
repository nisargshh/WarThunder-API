<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;

class AviationStatisticsController extends Controller
{
    /**
    * Aviation Statistic for player
    */
    public function getStatistics($name){
      $client = new Client();
      $crawler = $client->request('GET', 'https://warthunder.com/en/community/userinfo/?nick=' . $name);

      $name = $this->name($crawler);
      $header = $this->header($crawler);
      $abtitle = $this->abTitle($crawler);
      $abdata = $this->abStatistics($crawler);
      $rbtitle = $this->rbTitle($crawler);
      $rbdata = $this->rbStatistics($crawler);
      $sbtitle = $this->sbTitle($crawler);
      $sbdata = $this->sbStatistics($crawler);

      if(empty($name)){
        return response()->json([
          'error' => 'User does not exist'
        ]);
      }

      $name = $name[0];
      $name = trim(preg_replace('/\s\s+/', ' ', $name));
      $status = array('status' => 'success', 'name' => $name);
      $merge = array_merge($status, $this->mergeArray($header, $abdata, $abtitle));
      $merge = array_merge($merge, $this->mergeArray($header, $rbdata, $rbtitle));
      $merge = array_merge($merge, $this->mergeArray($header, $sbdata, $sbtitle));

      return $merge;
    }

    /**
    * Merge the arrays
    */
    public function mergeArray($arrs1, $arrs2, $titles){
      $size = count($arrs1);
      foreach ($titles as $title) {
        $i = 0;
        foreach ($arrs1 as $arr1) {
          $merge[$title][$arr1][] = $arrs2[$i];
          $i++;
        }
      }
      return $merge;
    }

    /**
    * Player name
    */
    public function name($crawler){
      return $crawler->filter('.user-profile__data-nick')->each(function ($node) {
        return $node->text();
      });
    }

    /**
    * Table headers
    */
    public function header($crawler){
      return $crawler->filter('.profile-stat__list-row:nth-child(1) > .profile-stat__list:nth-child(2) > li:not(:first-child)')->each(function ($node) {
        return $node->text();
      });
    }

    /**
    * Ab Title
    */
    public function abTitle($crawler){
      return $crawler->filter('.profile-stat__list-row:nth-child(1) > .profile-stat__list-ab:nth-child(3) > li:nth-child(1)')->each(function ($node) {
        return $node->text();
      });
    }

    /**
    * Arcade battle statistics for player
    */
    public function abStatistics($crawler){
      return $crawler->filter('.profile-stat__list-row:nth-child(1) > .profile-stat__list-ab:nth-child(3) > li:not(:first-child)')->each(function ($node) {
        return $node->text();
      });
    }

    /**
    * Rb Title
    */
    public function rbTitle($crawler){
      return $crawler->filter('.profile-stat__list-row:nth-child(1) > .profile-stat__list-rb:nth-child(4) > li:nth-child(1)')->each(function ($node) {
        return $node->text();
      });
    }

    /**
    * Realistic battle statistics for player
    */
    public function rbStatistics($crawler){
      return $crawler->filter('.profile-stat__list-row:nth-child(1) > .profile-stat__list-rb:nth-child(4) > li:not(:first-child)')->each(function ($node) {
        return $node->text();
      });
    }

    /**
    * Sb Title
    */
    public function sbTitle($crawler){
      return $crawler->filter('.profile-stat__list-row:nth-child(1) > .profile-stat__list-sb:nth-child(5) > li:nth-child(1)')->each(function ($node) {
        return $node->text();
      });
    }

    /**
    * Simulator battle statistics for player
    */
    public function sbStatistics($crawler){
      return $crawler->filter('.profile-stat__list-row:nth-child(1) > .profile-stat__list-sb:nth-child(5) > li:not(:first-child)')->each(function ($node) {
        return $node->text();
      });
    }
}
