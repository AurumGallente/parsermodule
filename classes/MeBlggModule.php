<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolModule');

class MeBlggModule extends BxDolModule {
 
//    function __construct(){
//            
//
//
//    }

    function MeBlggModule(&$aModule) {        
        parent::BxDolModule($aModule);
            ini_set('display_errors',1);
            ini_set('display_startup_errors',1);
            error_reporting(1);
            error_reporting(E_ALL);
            header('Content-Type: application/json');
            require 'ImageClass.php';
            $imageParser = new Parser_Provider_Image();
    }

    function actionHome () {
        $this->_oTemplate->pageStart();
        $aVars = array ();
        echo $this->_oTemplate->parseHtmlByName('main', $aVars);
        $this->_oTemplate->pageCode(_t('_me_blgg'), true);
    }
    function actionJson() {
        //$this->bbc_uk_top_parse();
        //$this->get_bbc_uk_top();
        //$this->daily_uk_top_parse();
        //$this->mcn_us_parse();
        $this->sky_tech_parse();
    }
    public function sky_tech_parse(){
        //http://news.sky.com/feeds/rss/technology.xml
         $channel = new Zend_Feed_Rss('http://news.sky.com/feeds/rss/technology.xml');
         $imageParser = new Parser_Provider_Image();
         $i = 0;         
         foreach ($channel as $item) {  
                $height = 10;
                $width = 10;
                $news['title'] = $item->title();
                $news['description'] = $item->description();
                $news['description'] = $news['description'][1]->nodeValue;
                $news['link'] = $item->link();
                $news['date'] = $item->pubDate();
                $news['author'] = 'sky.com';
                $client = new Zend_Http_Client();
                $client->setUri(trim($item->link()));
                $response = $client->request(); 
                $html = $response->getBody();
                $r1 = preg_match_all('/figure(.*?)\/figure/s', $html, $matches);
                foreach($matches[1] as $m){                    
                    $r2 = preg_match_all('/src=\"(.*?)\"/', $m, $match);
                    $image = $match[1][0];
                    $dimentions = $imageParser->getImageSize($image);
                    if(isset($dimentions[0])){
                     if($dimentions[0]*$dimentions[1]> $height*$width){                        
                          $news['img'] = $image;
                          $width = $dimentions[0];
                          $height = $dimentions[1];                     
                     }
                    }                    
                }
                $texts = preg_match('/<div\sid=\"articleText\">(.*?)<aside>/s', $html, $matches);
                preg_match_all('/<p>(.*?)<\/p>/s', $matches[1], $t);
                $text = '';
                foreach($t[1] as $texts){
                    $text .= $texts;
                }
                $news['text'] = $text;
                $this->_oDb->Insert($news, 4);
                if($i >= 5)
                    break;   
         }
    }
    public function mcn_us_parse(){
        $channel = new Zend_Feed_Rss('http://entertainment.ca.msn.com/celebs/rss-celeb-news.aspx');
        $i = 0;
        foreach ($channel as $item) {
                $i++;                               
                $height = 10;
                $width = 10;
                $news = array();
                $news['title'] = $item->title();
                $news['description'] = $item->description();
                $news['link'] = $item->link();
                $news['date'] = $item->pubDate();
                $news['author'] = 'msn.com';
                $client = new Zend_Http_Client();
                $client->setUri(trim($item->link()));
                $response = $client->request(); 
                $html = $response->getBody();                
                //var_dump($news);
                //var_dump($html);
                $r1 = preg_match('/<div\sclass=\"img\">(.*?)<\/div>/s', $html, $res);
                //var_dump($res[1]);
                $im = preg_match('/src=\"(.*?)\"/', $res[1], $img);
                //var_dump($img[1]);
                $news['image'] = $img[1];
                $news['img'] = $img[1];
                $texts = preg_match('/svrichtxt\scf\">(.*?)<strong>/', $html, $res);
                //var_dump($res);
                $texts = preg_match_all('/<p>(.*?)<\/p>/', $res[1], $result);
                //var_dump($result[1]);
                $text = '';
                foreach($result[1] as $r){
                    $text .= strip_tags($r, '<p>');
                }
                //var_dump($text);
                $news['text'] = $text;
                $this->_oDb->Insert($news, 3);
                if($i >= 10)
                    break;  
        }
    }
    public function daily_uk_top_parse(){
        $imageParser = new Parser_Provider_Image();        
        $channel = new Zend_Feed_Rss('http://www.dailymail.co.uk/news/index.rss');
        $i = 0;
        $result = array();
        foreach($channel as $item){
                $i++;
                $height = 10;
                $width = 10;
                $news = array();
                $news['title'] = $item->title();
                //$news['description'] = $item->description();
                $news['link'] = trim($item->link());
                $news['date'] = $item->pubDate();
                $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
                $options = array(

                    CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
                    CURLOPT_POST           =>false,        //set to GET
                    CURLOPT_USERAGENT      => $user_agent, //set user agent
                    CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
                    CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
                    CURLOPT_RETURNTRANSFER => true,     // return web page
                    CURLOPT_HEADER         => false,    // don't return headers
                    CURLOPT_FOLLOWLOCATION => true,     // follow redirects
                    CURLOPT_ENCODING       => "",       // handle all encodings
                    CURLOPT_AUTOREFERER    => true,     // set referer on redirect
                    CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
                    CURLOPT_TIMEOUT        => 120,      // timeout on response
                    CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
                );

                $ch      = curl_init(trim($item->link));
                curl_setopt_array( $ch, $options );
                $content = curl_exec( $ch );
                $err     = curl_errno( $ch );
                $errmsg  = curl_error( $ch );
                $header  = curl_getinfo( $ch );
                curl_close( $ch );
                $header['errno']   = $err;
                $header['errmsg']  = $errmsg;
                $header['content'] = $content;                
                $description = preg_match('/<h1>(.*?)<\/h1>/', $content, $matches);
                $news['description'] = $matches[1];
                //var_dump($matches[1]);
                $v = preg_match_all('/src=\"(.*?)\"\s/', $content, $n);
                $images = array();
                foreach($n[1] as $image){
                   unset($dimentions); 
                   $dimentions = $imageParser->getImageSize($image);
                   //var_dump($dimentions);
                   if(isset($dimentions[0])){
                    if($dimentions[0]*$dimentions[1]> $height*$width){                        
                         $news['img'] = $image;
                         $width = $dimentions[0];
                         $height = $dimentions[1];                     
                    }
                   }
                }
                $text = preg_match_all('/<p\sclass=\"mol\-para\-with\-font\">(.*?)<\/p>/', $content, $texts);
                //var_dump($texts[1]);
                $text = '';
                foreach($texts[1] as $t){
                    $text .= $t;
                }
                $news['text'] = $text;
                $news['author'] = 'dailymail';
                $this->_oDb->Insert($news, 2);
                if($i >= 5)
                    break;       
        }
    }
    public function bbc_uk_top_parse(){
   
            $channel = new Zend_Feed_Rss('http://feeds.bbci.co.uk/news/rss.xml?edition=uk');
            //echo $channel->title();
            $i = 0;
            $result = array();
            foreach ($channel as $item) {
                $height = 10;
                $width = 10;
                $news = array();
                $news['title'] = $item->title();
                $news['description'] = $item->description();
                $news['link'] = $item->link();
                $i++;
                $client = new Zend_Http_Client();
                $client->setUri($item->link());
                $response = $client->request(); 
                $html = $response->getBody();
                $value=preg_match_all('/width\">(.*?)<\/div>/s',$html,$m);

                            $date = preg_match('/<span class=\"date\">(.*?)<\/span>/s', $html, $matches);
                            //var_dump($matches[1]);
                            $date1 = $matches[1];
                            //<span class="date"></span>;
                            $date = preg_match('/<span class=\"time\">(.*?)<\/span>/s', $html, $matches);
                            //var_dump($matches[1]);
                            $date2 = $matches[1];
                            $date = $date1.' '.$date2;
                            //<span class="time">10:06 GMT</span>
                            $test = preg_match('/class=\"story\-body\">(.*?)<!\-\-\s\/\sstory\-body\s\-\->/s', $html, $string);
                            $string = preg_match_all('/<p>(.*?)<\/p>/', $string[1], $string1);
                            $string2 = '';
                            foreach($string1[0] as $s){
                                    $string2.=$s;
                                    //var_dump($s);
                                    //echo '<hr/>';
                            }
                            //var_dump($string2);exit;
                            $string = str_replace(array('<strong>Please turn on JavaScript.</strong>', "\n", "\r\t", "\r", "\t", '   ', 'Media requires JavaScript to play.'), '',  substr(strip_tags($string2, '<p><a><b><i><strong>'), 0, 5000));
                            //echo($string);
                            //echo '<hr/>';
                $images = array();
                foreach($m[0] as $image){
                   $v = preg_match_all('/src=\"(.*?)\"\s/', $image, $n);
                   $image = $n[1][0];
                   $dimentions = $imageParser->getImageSize($image);
                   if($dimentions[0]*$dimentions[1]> $height*$width){
                       if($dimentions[0]>10){
                        $news['img'] = $n[1][0];
                        $width = $dimentions[0];
                        $height = $dimentions[1];
                       }
                   }
                   $images[] = $news['img'];
                   $d = $imageParser->getImageSize($news['img']);
                }
                            $news['date'] = $date;
                            $news['text'] = $string;
                            $news['author'] = 'BBC';			
                $result[] = $news;
                if(!isset($news['img'])){
                    $news['img'] = '';
                }
                $this->_oDb->Insert($news);
                
                if($i >= 10)
                    break;                
                
            }
            //echo json_encode($result);
            $res = array($result, 'world');
            //return $res;
    }
    public function get_bbc_uk_top(){
        $result = $this->_oDb->select(1);
        echo json_encode($result);
       // var_dump($result);
    }
    public function actionTestrequest(){
echo "<html>
  <head>
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js\"></script>
  </head>
  <body>
    <script>
      // 1. The request domain has to be the same as server.php.
      // 2. You could replace the following url with 'server.php'
      $.getJSON('http://weber/modules/?r=bloggie/testrequest2',
        {
          param1: \"ykyuen\",
          param2: \"eureka\"
        },
        success
      );
       
      function success(data) {
        $.each(data, function(key, val) {
          $(\"body\").append(key + \" : \" + val + \"<br/>\");
        });
      }
    </script>
  </body>
</html>";        
    }
    public function actionTestrequest2(){
        var_dump(Zend_Json::decode($_REQUEST));
    }
}

?>
