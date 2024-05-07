<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
include_file('core', 'lgWebOSTV', 'config', 'lgWebOSTV');
class lgWebOSTV extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    public static function dependancy_info() {


        $return = array();
        $return['log'] = 'lgWebOSTV_update';
        $return['progress_file'] = jeedom::getTmpFolder('lgWebOSTV') . '/dependance';
        $return['state'] = 'ok' ;

//      if (exec(system::getCmdSudo() . system::get('cmd_check') . '-E "python\-serial|python\-request|python\-pyudev" | wc -l') >= 3) {
        log::add('lgWebOSTV','DEBUG','PIP LIST: ' . $checks);

        $checks = exec("pip list | grep ws4py");
        if (strpos($checks, 'ws4py') !== false) {
            if ($checks[strpos($checks, 'ws4py') + 9] >= 4) {
                if ($checks[strpos($checks, 'ws4py') + 11] >= 3) {
                    log::add('lgWebOSTV', 'DEBUG', 'Vérification de ws4py: OK');
                } else {
                    log::add('lgWebOSTV', 'INFO', 'Vérification de ws4py: PB VERSION x.x.3 !' . $checks[13]);
                    $return['state'] = 'nok';
                }
            } else {
                log::add('lgWebOSTV', 'INFO', 'Vérification de ws4py: PB VERSION x.4.x !' . $checks[11]);
                $return['state'] = 'nok';
            }
        } else {
            log::add('lgWebOSTV','INFO','Vérification de ws4py: MANQUANT !');
            $return['state'] = 'nok' ;
        }

        $checks = exec("pip list | grep wakeonlan");
        if (strpos($checks, 'wakeonlan') !== false) {
            log::add('lgWebOSTV','DEBUG','Vérification de wakeonlan: OK');
        } else {
            log::add('lgWebOSTV','INFO','Vérification de wakeonlan: MANQUANT !');
            $return['state'] = 'nok' ;
        }

        return $return;
    }

    public static function dependancy_install()
    {
        log::remove(__CLASS__ . '_update');
        return array('script' => dirname(__FILE__) . '/../../ressources/install.sh ' . jeedom::getTmpFolder('lgWebOSTV') . '/dependance',
                     'log' => log::getPathToLog(__CLASS__ . '_update'));
    }



    /*     * *********************Methode d'instance************************* */

    public function preUpdate() {
        $tvip = $this->getConfiguration('addr');
        $key = $this->getConfiguration('key');
        $lg_path = realpath(dirname(__FILE__) . '/../../3rdparty');

        if ($this->getConfiguration('addr') == '') {
            throw new Exception(__('L\'adresse IP ne peut etre vide. Vous pouvez la trouver dans les paramètres de votre TV ou de votre routeur (box).',__FILE__));
        }
		if ($this->getConfiguration('key') == '') {
            if (file_exists($lg_path . "/lgtv.json")) unlink($lg_path . "/lgtv.json");
            $cmd = '/usr/bin/python ' . $lg_path . '/lgtv.py auth ' . $tvip;
            $json_in = shell_exec($cmd); // . " > /tmp/tv");
            $ret = json_decode($json_in, true);
            log::add('lgWebOSTV','debug','$$$ EXEC: ' . $cmd);

            $json_in = file_get_contents($lg_path . "/lgtv.json");
            $ret = json_decode($json_in, true);
            //print_r($ret);

            if ($ret["client-key"] != "") {
                $this->setConfiguration('key', $ret["client-key"]);
                $this->setConfiguration('mac', $ret["mac-address"]);

                //print("OK, la clé est " . $ret["client-key"]);
            } else {
                throw new Exception(__('Vous n avez pas accepte la demande de connexion sur la TV', __FILE__));
            }
        }
		
    }
	
	public function getGroups() {
       return array('base', 'inputs', 'apps', 'channels');
    }
	
	public function commandByName($name) {
        global $listCmdlgWebOSTV;


        foreach ($listCmdlgWebOSTV as $cmd) {
           if ($cmd['name'] == $name)
            return $cmd;
        }
        
        return null;
    }
	
	 public function addCommand($cmd) {
       if (cmd::byEqLogicIdCmdName($this->getId(), $cmd['name']))
            return;
            
       if ($cmd) {
            $lgWebOSTVCmd = new lgWebOSTVCmd();
            $lgWebOSTVCmd->setName(__($cmd['name'], __FILE__));
            $lgWebOSTVCmd->setEqLogic_id($this->id);

            $lgWebOSTVCmd->setConfiguration('dashicon', $cmd['configuration']['dashicon']);
            $lgWebOSTVCmd->setConfiguration('request', $cmd['configuration']['request']);
		    $lgWebOSTVCmd->setConfiguration('parameters', $cmd['configuration']['parameters']);
		    $lgWebOSTVCmd->setConfiguration('group', $cmd['group']);
            $lgWebOSTVCmd->setType($cmd['type']);
            $lgWebOSTVCmd->setSubType($cmd['subType']);
			if ($cmd['icon'] != '')
				$lgWebOSTVCmd->setDisplay('icon', '<i class=" '.$cmd['icon'].'"></i>');
		    $lgWebOSTVCmd->save();
       }
    }
    
    public function addCommandByName($name, $cmd_name) {
       if ($cmd = $this->commandByName($name)) {
			$this->addCommand($cmd);
       }
    }

    public function removeCommand($name) {
        if (($cmd = cmd::byEqLogicIdCmdName($this->getId(), $name)))
			$cmd->remove();
    }
    
    public function addCommands($groupname) {

        log::add('lgWebOSTV','debug','addCommands Called');
        $jsonFile = realpath(dirname(__FILE__)) . "/../../ressources/TV_LG.json";
        if (file_exists($jsonFile)) unlink($jsonFile);

        global $listCmdlgWebOSTV;
        foreach ($listCmdlgWebOSTV as $cmd) {
           if ($cmd['group'] == $groupname)
				$this->addCommand($cmd);
        }

    }
    
    public function removeCommands($groupname) {
        global $listCmdlgWebOSTV;

        foreach ($listCmdlgWebOSTV as $cmd) {
           if ($cmd['group'] == $groupname)
				$this->removeCommand($cmd['name']);
        }
    }
	
	
    public function preSave() {
		if (!$this->getId())
          return;
		  
		if ($this->getConfiguration('has_base') == 1) {
			$this->addCommands('base');
        } else {
            $this->removeCommands('base');
        }
		if ($this->getConfiguration('has_apps') == 1) {
			$this->addCommands('apps');
        } else {
            $this->removeCommands('apps');
        }
		 if ($this->getConfiguration('has_inputs') == 1) {
			$this->addCommands('inputs');
        } else {
            $this->removeCommands('inputs');
        }
		
        if ($this->getConfiguration('has_channels') == 1) {
			$this->addCommands('channels');
        } else {
            $this->removeCommands('channels');
        }
		

    }
	
    public function postSave() {
	}
    

	public function postInsert() {
	   
    
    }
	
	public function toHtml($_version = 'dashboard') {
		if ($this->getIsEnable() != 1) {
            return '';
        }
		if (!$this->hasRight('r')) {
			return '';
		}
        $_version = jeedom::versionAlias($_version);
		$replace = array(
			'#id#' => $this->getId(),
			'#info#' => (isset($info)) ? $info : '',
			'#name#' => ($this->getIsEnable()) ? $this->getName() : '<del>' . $this->getName() . '</del>',
			'#eqLink#' => $this->getLinkToConfiguration(),
			'#action#' => (isset($action)) ? $action : '',
			'#background_color#' => $this->getBackgroundColor($_version),
		);
		
		// Charger les template de groupe
        $groups_template = array();
        $group_names = $this->getGroups();
		foreach ($group_names as $group) {
            $groups_template[$group] = getTemplate('core', $_version, $group, 'lgWebOSTV');
            $replace['#group_'.$group.'#'] = '';
        }
		
		// Afficher les commandes dans les bonnes templates
        // html_groups: permet de gérer le #cmd# dans la template.
        $html_groups = array();
        if ($this->getIsEnable()) {
            foreach ($this->getCmd() as $cmd) {
                $cmd_html = ' ';
                $group    = $cmd->getConfiguration('group');
                if ($cmd->getIsVisible()) {
				
					if ($cmd->getType() == 'info') {
						log::add('lgWebOSTV','debug','cmd = info');
						$cmd_html = $cmd->toHtml();
					} else {
						$cmd_template = getTemplate('core', $_version, $group.'_cmd', 'lgWebOSTV');

                        log::add('lgWebOSTV','DEBUG','/-------------------------------');
                        log::add('lgWebOSTV','DEBUG','| #id# = ' . $cmd->getId());
                        log::add('lgWebOSTV','DEBUG','| #name# = ' . $cmd->getName());
                        log::add('lgWebOSTV','DEBUG','| #dashicon# = ' . $cmd->getConfiguration('dashicon'));
                        log::add('lgWebOSTV','DEBUG','| #request# = ' . $cmd->getConfiguration('request'));
                        log::add('lgWebOSTV','DEBUG','| #parameters# = ' . $cmd->getConfiguration('parameters'));
                        log::add('lgWebOSTV','DEBUG','| GROUP = ' . $cmd->getConfiguration('group'));
                        log::add('lgWebOSTV','DEBUG','L-------------------------------');

						$cmd_replace = array(
							'#id#' => $cmd->getId(),
							'#name#' => $cmd->getName(), //($cmd->getDisplay('icon') != '') ? $cmd->getDisplay('icon') : $cmd->getName(),
                            '#dashicon#' => $cmd->getConfiguration('dashicon'), //getName(),
						);
						
						// Construction du HTML pour #cmd#
						$cmd_html = template_replace($cmd_replace, $cmd_template);
					}
                    if (isset($html_groups[$group]))
					{
						$html_groups[$group]++;
						$html_groups[$group] .= $cmd_html;
					} else {
						$html_groups[$group] = $cmd_html; 
					}    
                } 
                $cmd_replace = array(
                    '#'.strtolower($cmd->getName()).'#' => $cmd_html,
                    );
                $groups_template[$group] = template_replace($cmd_replace, $groups_template[$group]);
            }
        }
        
        // Remplacer #group_xxx de la template globale
        $replace['#cmd'] = "";
        $keys = array_keys($html_groups);
		foreach ($html_groups as $group => $html_cmd) {      
            $group_template =  $groups_template[$group]; 
            $group_replace = array(
                '#cmd#' => $html_cmd,
            );
            $replace['#group_'.$group.'#'] .= template_replace($group_replace, $group_template);
        }
		$parameters = $this->getDisplay('parameters');
        if (is_array($parameters)) {
            foreach ($parameters as $key => $value) {
                $replace['#' . $key . '#'] = $value;
            }
        }
	
        return template_replace($replace, getTemplate('core', $_version, 'eqLogic', 'lgWebOSTV'));
    }
	
	public static function event() {
		$cmd =  lgWebOSTVCmd::byId(init('id'));
	   
		if (!is_object($cmd)) {
			throw new Exception('Commande ID virtuel inconnu : ' . init('id'));
		}
	   
		$value = init('value');
       
		if ($cmd->getEqLogic()->getEqType_name() != 'lgWebOSTV') {
			throw new Exception(__('La cible de la commande lgWebOSTV n\'est pas un équipement de type lgWebOSTV', __FILE__));
		}
		   
		$cmd->event($value);
	   
		$cmd->setConfiguration('valeur', $value);
		log::add('lgWebOSTV','debug','set:'.$cmd->getName().' to '. $value);
		$cmd->save();
		
   }
   
}

class lgWebOSTVCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function preSave() {
        if ($this->getConfiguration('request') == '') {
            throw new Exception(__('La requete ne peut etre vide',__FILE__));
		}
		
		
    }

    public function execute($_options = null) {
    	$lgWebOSTV = $this->getEqLogic();
        $lg_path = realpath(dirname(__FILE__) . '/../../3rdparty');
		$tvip = $lgWebOSTV->getConfiguration('addr');
    	$key = $lgWebOSTV->getConfiguration('key');
		$volnum = $lgWebOSTV->getConfiguration('volnum');
		if ($this->type == 'action') {
				$command=$this->getConfiguration('parameters');
                if ($this->getSubType() == 'message') {
                    if ($_options['message'] != "") {
                        $message = '"' . $_options['message'] . '"';
                    } else {
                        $message = '"Message TEST"';
                    }
                    $command = str_replace("#message#", $message, $command);
                }
                $commande= $command;
				$ret = shell_exec('/usr/bin/python ' . $lg_path . '/lgtv.py ' .$commande);// . " > /tmp/tv");
                log::add('lgWebOSTV','debug','$$$ EXEC: ' . '/usr/bin/python ' . $lg_path . '/lgtv.py ' .$commande . " > " . $ret);
                if ($command=='volumeDown' or $command=='volumeUp') {
					for ($i = 1; $i <= $volnum-1; $i++) {
						shell_exec('/usr/bin/python ' . $lg_path . '/lgtv.py ' .$commande);
					}
				}
		}
    }
		


    /*     * **********************Getteur Setteur*************************** */
}
?>