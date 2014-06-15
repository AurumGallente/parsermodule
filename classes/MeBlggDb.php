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

bx_import('BxDolModuleDb');

class MeBlggDb extends BxDolModuleDb {

	function MeBlggDb(&$oConfig) {
		parent::BxDolModuleDb();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }
        function Insert($data, $src = 1){
            foreach($data as $key=>$value){
                $data[$key] = addslashes($value);                
            }
            
          //$this->query("INSERT INTO `" . $this->_sPrefix . "handlers`(`alert_unit`, `alert_action`, `module_uri`, `module_class`, `module_method`, `groupable`, `group_by`, `timeline`, `outline`) VALUES('" . $aHandler['alert_unit'] . "', '" . $aHandler['alert_action'] . "', '" . $aHandler['module_uri'] . "', '" . $aHandler['module_class'] . "', '" . $aHandler['module_method'] . "', '" . $aHandler['groupable'] . "', '" . $aHandler['group_by'] . "', '" . $aHandler['timeline'] . "', '" . $aHandler['outline'] . "')");
            $sql = "INSERT INTO par_items(`par_id`, `title`, `desc`, `link`, `image`, `text`, `author`, `date`) VALUES(".
                    "$src, '".$data['title']."', '".$data['description']."','".$data['link']."', '".$data['img']."', '".$data['text']."', '".$data['author']."', '".$data['date']."'".
                    ")";
            //echo $sql;exit;
            $this->query($sql);
        }
        function select($par_id){
            //var_dump($par_id);
            $par_id = (int)$par_id;
            $sql = "SELECT * FROM par_items WHERE par_id = '$par_id'";
            $result  =  $this->getAll($sql);
            foreach($result as &$r){
                unset($r['id']);
                unset($r['par_id']);
            }
            return $result;
        }
}

?>
