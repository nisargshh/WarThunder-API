<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use Irazasyed\LaravelGAMP\Facades\GAMP;

class TotalStatisticsController extends Controller
{
  public function getStatistics($user){
    $client = new Client();

    $crawler = $client->request('GET', 'https://warthunder.com/en/community/userinfo/?nick=' . $user);
    $headers[] = $this->headers($crawler);
    $name = $this->name($crawler);
    $abs[] = $this->abs($crawler);
    $title[] = $this->absTitle($crawler);
    $rbs[] = $this->rbs($crawler);
    $title[] = $this->rbsTitle($crawler);
    $sbs[] = $this->sbs($crawler);
    $title[] = $this->sbsTitle($crawler);

    if(empty($name)){
      return response()->json([
        'error' => 'User does not exist'
      ], 400);
    }

    $name = $name[0];
    $name = trim(preg_replace('/\s\s+/', ' ', $name));
    $status = array('status' => 'success', 'name' => $name);
    $merge = array_merge($status, $this->mergeArray($headers[0], $abs[0], $title[0]));
    $merge = array_merge($merge, $this->mergeArray($headers[0], $rbs[0], $title[1]));
    $merge = array_merge($merge, $this->mergeArray($headers[0], $sbs[0], $title[2]));

    $gamp = GAMP::setClientId( '172375005' );
    $gamp->setDocumentPath( '/api/statistics/' . $name . '/total' );
    $gamp->sendPageview();

    return response($merge);
  }

  /**
  * Merge array.
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
  * Get headers from website
  */
  public function headers($crawler){
    return $crawler->filter('.profile-stat__list-row > .profile-stat__list:nth-child(1) > li:not(:first-child)')->each(function ($node) {
      return $node->text();
    });
  }

  /**
  * Get arcade battle statistics
  */
  public function abs($crawler){
    return $abs[] = $crawler->filter('.profile-stat__list-row > .profile-stat__list-ab:nth-child(2) > li:not(:first-child)')->each(function ($node) {
      return $node->text();
    });
  }

  /**
  * Get arcade battle title
  */
  public function absTitle($crawler){
    return $crawler->filter('.profile-stat__list-row > .profile-stat__list-ab:nth-child(2) > li:nth-child(1)')->each(function ($node) {
      return $node->text();
    });
  }

  /**
  * Get realistic battle statistics
  */
  public function rbs($crawler){
    return $crawler->filter('.profile-stat__list-row > .profile-stat__list-rb:nth-child(3) > li:not(:first-child)')->each(function ($node) {
      return $node->text();
    });
  }

  /**
  * Get player name
  */
  public function name($crawler){
    return $crawler->filter('.user-profile__data-nick')->each(function ($node) {
      return $node->text();
    });
  }

  /**
  * Get realistic battle stats title
  */
  public function rbsTitle($crawler){
    return $crawler->filter('.profile-stat__list-row > .profile-stat__list-rb:nth-child(3) > li:nth-child(1)')->each(function ($node) {
      return $node->text();
    });
  }

  /**
  * Get Simulator battle stats data
  */
  public function sbs($crawler){
    return $crawler->filter('.profile-stat__list-row > .profile-stat__list-sb:nth-child(4) > li:not(:first-child)')->each(function ($node) {
      return $node->text();
    });
  }

  /**
  * Get Simulator title
  */
  public function sbsTitle($crawler){
    return $crawler->filter('.profile-stat__list-row > .profile-stat__list-sb:nth-child(4) > li:nth-child(1)')->each(function ($node) {
      return $node->text();
    });
  }
}
