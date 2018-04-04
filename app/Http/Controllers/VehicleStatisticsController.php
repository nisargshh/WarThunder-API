<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;

class VehicleStatisticsController extends Controller
{
    /**
    * Statisitcs for vehicles and awards
    */
    public function getStatistics($name){
      $client = new Client();
      $crawler = $client->request('GET', 'https://warthunder.com/en/community/userinfo/?nick=' . $name);

      $name = $this->name($crawler);
      $header = $this->header($crawler);
      $plane = $this->plane($crawler);
      $planetitle = array('Planes');
      $eliteplane = $this->elitePlane($crawler);
      $eliteplanetitle = array('Elite-Planes');
      $awards = $this->awards($crawler);
      $awardstitle = array('Awards');

      if(empty($name)){
        return response()->json([
          'error' => 'User does not exist'
        ]);
      }

      $name = $name[0];
      $name = trim(preg_replace('/\s\s+/', ' ', $name));
      $status = array('status' => 'success', 'name' => $name);
      $merge = array_merge($status, $this->mergeArray($header, $plane, $planetitle));
      $merge = array_merge($merge, $this->mergeArray($header, $eliteplane, $eliteplanetitle));
      $merge = array_merge($merge, $this->mergeArray($header, $awards, $awardstitle));

      $gamp = GAMP::setClientId( '172375005' );
      $gamp->setDocumentPath( '/api/statistics/' . $name . '/vehicle' );
      $gamp->sendPageview();

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
          $merge[$title][$arr1] = $arrs2[$i];
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
    * Header for the ul
    */
    public function header($crawler){
      return $crawler->filter('.profile-score__list-title:nth-child(1) > .profile-score__list-item:not(:first-child) > p')->each(function ($node) {
        return $node->text();
      });
    }

    /**
    * planes
    */
    public function plane($crawler){
      return $crawler->filter('.profile-score__list-col:nth-child(2) > .profile-score__list-item:not(:first-child)')->each(function ($node) {
        return $node->text();
      });
    }

    /**
    * Elite-planes
    */
    public function elitePlane($crawler){
      return $crawler->filter('.profile-score__list-col:nth-child(3) > .profile-score__list-item:not(:first-child)')->each(function ($node) {
        return $node->text();
      });
    }

    /**
    * Awards
    */
    public function awards($crawler){
      return $crawler->filter('.profile-score__list-col:nth-child(4) > .profile-score__list-item:not(:first-child)')->each(function ($node) {
        return $node->text();
      });
    }
}
