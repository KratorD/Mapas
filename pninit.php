<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pninit.php 31 2008-12-23 20:55:41Z Guite $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function Mapas_init()
{
    $tables = array('Mapas');
    foreach ($tables as $table) {
        if (!DBUtil::createTable($table)) {
            return false;
        }
    }
	    
    pnModSetVar('Mapas', 'modulestylesheet', 'Mapas.css');
	pnModSetVar('Mapas', 'itemsperpage', '25');
	pnModSetVar('Mapas', 'path', "h/mapas/");
	pnModSetVar('Mapas', 'juegos', "H1/H2-SW/H2-PoL/H3-RoE/H3-AB/H3-SoD/H3-WoG/H4-Std/H4-GS/H4-WoW/H4-Eq/H5-Std/H5-HoF/H5-ToE/H6-Std");
	pnModSetVar('Mapas', 'tamanos', "Pequenio/Mediano/Grande/Extragrande/Campania");
	pnModSetVar('Mapas', 'dificultades', "Facil/Normal/Dificil/Experto/Imposible");
	pnModSetVar('Mapas', 'tipoJuegos', "Un solo jugador/Duelo/Aliados humanos vs CPU/Aliados vs Aliados/Multijugador Individuales/Multijugador por Equipos/Multijugador aliados contra la CPU");
	pnModSetVar('Mapas', 'allowedExtensions', "zip/rar");
	pnModSetVar('Mapas', 'filesMaxSize', "1048576");
	
    return true;
}

function Mapas_upgrade($oldversion)
{
    $dom = ZLanguage::getModuleDomain('News');

    // Upgrade dependent on old version number
    switch ($oldversion)
    {
		case '0.1':
			
			$tables = pnDBGetTables();
			//Tiene Navegacion para a ser un estilo
			$sql = "ALTER TABLE $tables[Mapas] DROP `TieneNav`";
			
			if (!DBUtil::executeSQL($sql)) {
				LogUtil::registerError(__('Error! Could not update table.', $dom));
				return '0.1';
			}
			// add standard data fields
			if (!DBUtil::changeTable('Mapas')) {
                return false;
            }
	}
	
	return true;
}

function Mapas_delete()
{
    $tables = array('Mapas');
    foreach ($tables as $table) {
        if (!DBUtil::dropTable($table)) {
            return false;
        }
    }
    
    pnModDelVar('Mapas');
    return true;
}
