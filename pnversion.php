<?php
/**
 * Mapas  Module
 *
 * @package      Mapas
 * @version      $Id: pnversion.php 2011-10-17$
 * @author       Krator
 * @link         http://www.heroesofmightandmagic.es
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

$dom = ZLanguage::getModuleDomain('Mapas');
$modversion['name']           = 'Mapas';
$modversion['displayname']    = __('Mapas', $dom);
$modversion['url']            = __('Mapas', $dom);
$modversion['version']        = '0.5';
$modversion['description']    = __('Upload, Rating, Validate and Download of maps HOMM', $dom);
$modversion['credits']        = 'pndocs/changelog.txt';
$modversion['help']           = 'pndocs/readme.txt';
$modversion['changelog']      = 'pndocs/changelog.txt';
$modversion['license']        = 'pndocs/license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Krator';
$modversion['contact']        = 'http://www.heroesofmightandmagic.es';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['securityschema'] = array('Mapas::' => 'Mapas::');
