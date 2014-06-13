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
 
    function __construct(){
            
            ini_set('display_errors',1);
            ini_set('display_startup_errors',1);
            error_reporting(1);
            error_reporting(E_ALL);
            header('Content-Type: application/json');

    }

    function MeBlggModule(&$aModule) {        
        parent::BxDolModule($aModule);
    }

    function actionHome () {
        $this->_oTemplate->pageStart();
        $aVars = array ();
        echo $this->_oTemplate->parseHtmlByName('main', $aVars);
        $this->_oTemplate->pageCode(_t('_me_blgg'), true);
    }
    function actionJson() {
        require 'ImageClass.php';
        $imageParser = new Parser_Provider_Image();
        //$channel = new Zend_Feed_Rss('http://feeds.bbci.co.uk/news/rss.xml?edition=uk');    
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
                if($i >= 2)
                    break;                
                
            }
            echo json_encode($result);
    }
}

?>