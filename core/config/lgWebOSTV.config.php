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

global $listCmdlgWebOSTV;

//log::add('lgWebOSTV', 'debug', '$listCmdlgWebOSTV ACCESS CALLED');

$jsonFile = realpath(dirname(__FILE__)) . "/../../ressources/TV_LG.json";

if (!file_exists($jsonFile)) {

    $listCmdlgWebOSTV[] = array(
        'name' => 'Eteindre',
        'configuration' => array(
            'request' => 'Arreter la TV',
            'parameters' => 'off',
            'dashicon' => 'fa fa-power-off',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Eteindre la TV',
        'group' => 'base',
    );

    $listCmdlgWebOSTV[] = array(
        'name' => 'Allumer',
        'configuration' => array(
            'request' => 'Allumer la TV',
            'parameters' => 'on',
            'dashicon' => '',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Allumer la TV',
        'group' => 'base',
    );

    $listCmdlgWebOSTV[] = array(
        'name' => 'Mute',
        'configuration' => array(
            'request' => 'Couper le son',
            'parameters' => 'mute 1',
            'dashicon' => '',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Couper le son',
        'group' => 'base',
    );

    $listCmdlgWebOSTV[] = array(
        'name' => 'Vol+',
        'configuration' => array(
            'request' => 'Augmenter le Volume',
            'parameters' => 'volumeUp',
            'dashicon' => 'fa fa-volume-up',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Augmenter le Volume',
        'group' => 'base',
    );

    $listCmdlgWebOSTV[] = array(
        'name' => 'Vol-',
        'configuration' => array(
            'request' => 'Baisser le Volume',
            'parameters' => 'volumeDown',
            'dashicon' => 'fa fa-volume-down',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Baisser le Volume',
        'group' => 'base',
    );

    $listCmdlgWebOSTV[] = array(
        'name' => 'Régler le volume',
        'configuration' => array(
            'request' => 'Regler le volume',
            'parameters' => 'setVolume #value#',
            'dashicon' => '',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Regler le volume',
        'group' => 'base',
    );

    $listCmdlgWebOSTV[] = array(
        'name' => 'Activer la 3D',
        'configuration' => array(
            'request' => 'Activer la 3D',
            'parameters' => 'input3DOn',
            'dashicon' => '',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Activer la 3D',
        'group' => 'base',
    );
    $listCmdlgWebOSTV[] = array(
        'name' => 'désactiver la 3D',
        'configuration' => array(
            'request' => 'Désactiver la 3D',
            'parameters' => 'input3DOff',
            'dashicon' => '3D off',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Désactiver la 3D',
        'group' => 'base',
    );

    $listCmdlgWebOSTV[] = array(
        'name' => 'Envoyer un message sur la TV',
        'configuration' => array(
            'request' => 'Envoyer un message sur la TV',
            'parameters' => 'notification #message#',
            'dashicon' => 'message',
        ),
        'type' => 'action',
        'subType' => 'message',
        'description' => 'Envoyer un message sur la TV',
        'group' => 'base',
    );

/*    $listCmdlgWebOSTV[] = array(
        'name' => 'Ouvrir une URL YouTube',
        'configuration' => array(
            'request' => 'Ouvrir une URL YouTube',
            'parameters' => 'openYoutubeURL #message#',
            'dashicon' => '',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Ouvrir une URL YouTube',
        'group' => 'base',
    );
*/

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $lgcommand = 'listInputs';
    $json_in = shell_exec('python ' . realpath(dirname(__FILE__)) . '/../../3rdparty/lgtv.py ' . $lgcommand . "");
    $json = str_replace('{"closing": {"reason": "", "code": 1000}}', '', $json_in);
    $ret = json_decode($json, true);
    log::add('lgWebOSTV', 'debug', '/------ Scan TV: INPUTs -----------------------------');

    foreach ($ret["payload"]["devices"] as $inputs) {
        log::add('lgWebOSTV', 'debug', '$inputs:' . print_r($inputs, true));
        $inputs["label"] = str_replace("'", " ", $inputs["label"]);
        $inputs["label"] = str_replace("&", " ", $inputs["label"]);
        log::add('lgWebOSTV', 'debug', '| NEW INPUT FOUND:' . $inputs["label"]);
        if ($inputs["icon"] != "") {
            $downcheck = file_put_contents(realpath(dirname(__FILE__)) . "/../template/images/icons_inputs/" . $inputs["label"] . ".png", fopen($inputs["icon"], 'r'));
            log::add('lgWebOSTV', 'debug', '| Download icon of Input (' . $inputs["label"] . ") : " . $downcheck . " octets");
        }
        $listCmdlgWebOSTV[] = array(
            'name' => $inputs["label"],
            'configuration' => array(
                'request' => 'Passer sur l entree ' . $inputs["label"],
                'parameters' => 'setInput ' . $inputs["id"],
                'dashicon' => $inputs["label"],
            ),
            'type' => 'action',
            'subType' => 'other',
            'description' => $inputs["label"],
            'group' => 'inputs',
        );
        //log::add('lgWebOSTV','debug','$listCmdlgWebOSTV:' . print_r($listCmdlgWebOSTV, true));
    }
    log::add('lgWebOSTV', 'debug', 'L_______________________________________________');


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $lgcommand = 'listApps';
    $json_in = shell_exec('python ' . realpath(dirname(__FILE__)) . '/../../3rdparty/lgtv.py ' . $lgcommand . "");
    $json = str_replace('{"closing": {"reason": "", "code": 1000}}', '', $json_in);
    $ret = json_decode($json, true);
    log::add('lgWebOSTV', 'debug', '/------------- Scan APPs ---------------------------');

    foreach ($ret["payload"]["launchPoints"] as $inputs) {
        //log::add('lgWebOSTV','debug','$inputs:' . print_r($inputs, true));
        $inputs["title"] = str_replace("'", " ", $inputs["title"]);
        $inputs["title"] = str_replace("&", " ", $inputs["title"]);
        log::add('lgWebOSTV', 'debug', '| NEW APP FOUND:' . $inputs["title"]);
        if ($inputs["icon"] != "") {
            $downcheck = file_put_contents(realpath(dirname(__FILE__)) . "/../template/images/icons_apps/" . $inputs["title"] . ".png", fopen($inputs["icon"], 'r'));
            log::add('lgWebOSTV', 'debug', '| Download icon of App (' . $inputs["label"] . ") : " . $downcheck . " octets");
        }
        $listCmdlgWebOSTV[] = array(
            'name' => $inputs["title"],
            'configuration' => array(
                'request' => 'startApp ' . $inputs["id"],
                'parameters' => 'startApp ' . $inputs["id"],
                'dashicon' => $inputs["title"],
            ),
            'type' => 'action',
            'subType' => 'other',
            'description' => $inputs["icon"],
            'group' => 'apps',
        );
        //log::add('lgWebOSTV','debug','$listCmdlgWebOSTV:' . print_r($listCmdlgWebOSTV, true));
    }
    log::add('lgWebOSTV', 'debug', 'L_______________________________________________');


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $lgcommand = 'listChannels';
    $json_in = shell_exec('python ' . realpath(dirname(__FILE__)) . '/../../3rdparty/lgtv.py ' . $lgcommand . "");
    $json = str_replace('{"closing": {"reason": "", "code": 1000}}', '', $json_in);
    $ret = json_decode($json, true);
    log::add('lgWebOSTV', 'debug', '/------------- Scan CHANNELs -------------------');

    foreach ($ret["payload"]["channelList"] as $inputs) {
        //log::add('lgWebOSTV','debug','$inputs:' . print_r($inputs, true));
        $inputs["channelName"] = str_replace("'", " ", $inputs["channelName"]);
        $inputs["channelName"] = str_replace("&", " ", $inputs["channelName"]);
        if ($inputs["channelName"] != "") {
            log::add('lgWebOSTV', 'debug', '| NEW CHANNEL FOUND:' . $inputs["channelName"]);
            if ($inputs["icon"] != "") {
                log::add('lgWebOSTV', 'debug', '| Download icon of :' . $inputs["channelName"]);
                $downcheck = file_put_contents(realpath(dirname(__FILE__)) . "/../template/images/icons_channels/" . $inputs["channelName"] . ".png", fopen($inputs["icon"], 'r'));
                log::add('lgWebOSTV', 'debug', '| Download :' . $downcheck);
            }
            $listCmdlgWebOSTV[] = array(
                'name' => $inputs["channelName"],
                'configuration' => array(
                    'request' => 'Mettre la chaine ' . $inputs["channelNumber"],
                    'parameters' => 'setTVChannel ' . $inputs["channelId"],
                    'dashicon' => $inputs["channelName"],
                ),
                'type' => 'action',
                'subType' => 'other',
                'description' => $inputs["icon"],
                'group' => 'channels',
            );
            //log::add('lgWebOSTV','debug','$listCmdlgWebOSTV:' . print_r($listCmdlgWebOSTV, true));
        }
    }
    log::add('lgWebOSTV', 'debug', 'L_______________________________________________');

    log::add('lgWebOSTV', 'debug', 'Ecriture de la config de la TV sur fichier...');

/*
    if (!file_exists($jsonFile)) {
        log::add('arduidom', 'info', "OK, le fichier de config TV n'existe pas.");
    } else {
        log::add('arduidom', 'info', "le fichier de config TV existe deja, suppression...");
        $unlinkcheck = unlink($jsonFile);
        if ($unlinkcheck == false) {
            log::add('arduidom', 'info', "Erreur lors de la suppression du fichier de config TV");
        }
    }
*/
    // serialize your input array (say $array)
    $serializedData = serialize($listCmdlgWebOSTV);
    // save serialized data in a text file
    log::add('lgWebOSTV', 'debug', 'Ecriture de ' . $jsonFile . ' ...');

    $savecheck = file_put_contents($jsonFile, $serializedData);

    log::add('lgWebOSTV', 'debug', 'Ecriture de la config de la TV sur fichier...');

    if ($savecheck != false) {
        log::add('arduidom', 'info', "ecriture du fichier de config TV : OK");
    } else {
        log::add('arduidom', 'error', "ERREUR lors de l'ecriture du fichier de config TV");
    }



} else {

    log::add('lgWebOSTV', 'debug', 'Lecture du fichier de configuration TV.');


// at a later point, you can convert it back to array like:
    $recoveredData = file_get_contents($jsonFile);

// unserializing to get actual array
    $listCmdlgWebOSTV = unserialize($recoveredData);


}

/*
    array(
        'name' => 'Haut',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '12',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton haut',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-arrow-up',
    ),
    array(
        'name' => 'Bas',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '13',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton bas',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-arrow-down',
    ),
    array(
        'name' => 'Gauche',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '14',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton gauche',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-arrow-left',
    ),
    array(
        'name' => 'Droite',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '15',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton droit',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-arrow-right',
    ),
    array(
        'name' => 'OK',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '20',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton OK',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
    ),
    array(
        'name' => 'Home',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '21',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton home',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-home',
    ),
    array(
        'name' => 'Menu',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '22',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton menu',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-wrench',
    ),
    array(
        'name' => 'Back',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '23',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton back',
        'version' => '0.1',
		'group' => 'basic',
		'icon' => 'fa fa-reply',
    ),
    array(
        'name' => 'Vol+',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '24',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Volume haut',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-volume-up',
    ),
    array(
        'name' => 'Vol-',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '25',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Volume bas',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-volume-down',
    ),
    array(
        'name' => 'Muet',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '26',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Sourdine on-off',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-volume-off',
    ),
    array(
        'name' => 'Chaine+',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '27',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Chaine haut',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-plus',
    ),
    array(
        'name' => 'Chaine-',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '28',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Chaine bas',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-minus',
    ),
    array(
        'name' => 'Bleu',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '29',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton bleu',
        'version' => '0.1',
        'required' => '',
		'group' => 'couleur',
    ),
    array(
        'name' => 'Vert',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '30',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton vert',
        'version' => '0.1',
        'required' => '',
		'group' => 'couleur',
    ),
    array(
        'name' => 'Rouge',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '31',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton rouge',
        'version' => '0.1',
        'required' => '',
		'group' => 'couleur',
    ),
    array(
        'name' => 'Jaune',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '32',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton jaune',
        'version' => '0.1',
        'required' => '',
		'group' => 'couleur',
    ),
    array(
        'name' => 'Play',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '33',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton lecture',
        'version' => '0.1',
        'required' => '',
		'group' => 'magneto',
		'icon' => 'fa fa-play',
    ),
    array(
        'name' => 'Pause',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '34',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton pause',
        'version' => '0.1',
        'required' => '',
		'group' => 'magneto',
		'icon' => 'fa fa-pause',
    ),
    array(
        'name' => 'Stop',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '35',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton stop',
        'version' => '0.1',
        'required' => '',
		'group' => 'magneto',
		'icon' => 'fa fa-stop',
    ),
    array(
        'name' => 'Avance rapide',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '36',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton avance rapide',
        'version' => '0.1',
        'required' => '',
		'group' => 'magneto',
		'icon' => 'fa fa-forward',
    ),
    array(
        'name' => 'Retour rapide',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '37',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton retour rapide',
        'version' => '0.1',
        'required' => '',
		'group' => 'magneto',
		'icon' => 'fa fa-backward',
    ),
    array(
        'name' => 'Suivant',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '38',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton suivant',
        'version' => '0.1',
        'required' => '',
		'group' => 'magneto',
		'icon' => 'fa fa-step-forward',
    ),
    array(
        'name' => 'Precedent',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '39',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton precedent',
        'version' => '0.1',
        'required' => '',
		'group' => 'magneto',
		'icon' => 'fa fa-step-backward',
    ),
    array(
        'name' => 'Enregistrer',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '40',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton record',
        'version' => '0.1',
        'required' => '',
		'group' => 'magneto',
		'icon' => 'fa fa-circle',
    ),
    array(
        'name' => 'Liste enregistrement',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '41',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton recording list',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-film',
    ),
    array(
        'name' => 'Repetition',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '42',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton repeat',
        'version' => '0.1',
        'required' => '',
		'group' => 'magneto',
		'icon' => 'fa fa-refresh',
    ),
    array(
        'name' => 'Live TV',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '43',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton Live TV',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa techno-tv',
    ),
    array(
        'name' => 'EPG',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '44',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton EPG',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-calendar-o',
    ),
    array(
        'name' => 'Info programme',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '45',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton info programme',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-info-circle',
    ),
    array(
        'name' => 'Aspect',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '46',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton Aspect-Ratio',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-arrows-alt',
    ),
    array(
        'name' => 'Sources',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '47',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton sources',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-sign-in',
    ),
    array(
        'name' => 'PIP',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '48',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton picture in picture',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-folder-open',
    ),
    array(
        'name' => 'Sous-titre',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '49',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton sous-titre',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-table',
    ),
    array(
        'name' => 'Liste programme',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '50',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton program list',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-columns',
    ),
    array(
        'name' => 'Teletexte',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '51',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton teletexte',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-building-o',
    ),
    array(
        'name' => 'Mark',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '52',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton Mark',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-comment',
    ),
    array(
        'name' => '3D',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '400',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton 3D',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
    ),
    array(
        'name' => '3D Gauche-Droite',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '401',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton 3D gauche droite',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-random',
    ),
    array(
        'name' => '-',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '402',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton dash',
        'version' => '0.1',
        'required' => '',
		'group' => 'numero',
    ),
    array(
        'name' => 'Flashback',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '403',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton flashback',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-chevron-left',
    ),
    array(
        'name' => 'Chaines favorites',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '404',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton chaines favorites',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-star-o',
    ),
    array(
        'name' => 'Quick menu',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '405',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton quick menu',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-cog',
    ),
    array(
        'name' => 'Options text',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '406',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton text options',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-trello',
    ),
    array(
        'name' => 'Audio description',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '407',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton audio description',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-headphones',
    ),
    array(
        'name' => 'Netcast',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '408',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton netcast',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-rss',
    ),
    array(
        'name' => 'Eco-Energie',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '409',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton energy saving',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa techno-prise-eco',
    ),
    array(
        'name' => 'AV Mode',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '410',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton AV',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'techno-tv6',
    ),
    array(
        'name' => 'Simplink',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '411',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton simplink',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa techno-fleches',
    ),
    array(
        'name' => 'Exit',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '412',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton exit',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
    ),
    array(
        'name' => 'Reserv',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '413',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton reserv program list',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-tag',
    ),
    array(
        'name' => 'PIP+',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '414',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton pip chaine+',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-plus-circle',
    ),
    array(
        'name' => 'PIP-',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '415',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton pip chaine-',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-minus-circle',
    ),
    array(
        'name' => 'Switch',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '416',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton switch video',
        'version' => '0.1',
        'required' => '',
		'group' => 'divers',
		'icon' => 'fa fa-exclamation-circle',
    ),
    array(
        'name' => 'Myapps',
        'configuration' => array(
            'request' => 'key',
            'parameters' => '417',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Bouton myapps',
        'version' => '0.1',
        'required' => '',
		'group' => 'basic',
		'icon' => 'fa fa-windows',
    )
);
*/
?>
