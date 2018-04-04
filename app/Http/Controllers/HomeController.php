<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use SEOMeta;
use OpenGraph;
use Twitter;
## or
use SEO;

class HomeController extends Controller
{
    use SEOToolsTrait;
    /**
    *
    */
    public function index(){
      SEOMeta::setTitle('Home');
      SEOMeta::setDescription('WarThunder Unofficial API');
      SEOMeta::setCanonical('https://warthunderapi.com');

      OpenGraph::setDescription('WarThunder Unofficial API');
      OpenGraph::setTitle('Home');
      OpenGraph::setUrl('https://warthunderapi.com');
      OpenGraph::addProperty('warthunder', 'api', 'REST', 'game');

      Twitter::setTitle('Home');
      Twitter::setSite('@5oClutch');

      ## Or

      SEO::setTitle('Home');
      SEO::setDescription('WarThunder Unofficial API');
      SEO::opengraph()->setUrl('https://warthunderapi.com');
      SEO::setCanonical('https://warthunderapi.com');
      SEO::opengraph()->addProperty('warthunder', 'api', 'REST', 'game');
      SEO::twitter()->setSite('@5oClutch');
      redirect()->away('https://warthunderapi.com/api/documentation');
      return redirect()->away('https://warthunderapi.com/api/documentation');
    }
}
