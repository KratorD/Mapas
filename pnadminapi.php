<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pnadminapi.php 31 2008-12-23 20:55:41Z Guite $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

/**
 * Actualizar un mapa
 * @param $args['ID'] ID del mapa
 * @return bool true on success, false on failure
 */
function Mapas_adminapi_update($args)
{
	//Mandatory
	if (!isset($args['ID']) || empty($args['ID'])) {
		return LogUtil::registerArgsError();
	}
	$ID = $args['ID'];
	extract ($args);

	if ($NombreMapa != ''){
		$cadena.= $mas."`NombreMapa` = '".$NombreMapa."'";
		$mas = ",";
	}
	if ($Autor != ''){
		$cadena.= $mas."`Autor` = '".$Autor."'";
		$mas = ",";
	}
	if ($EstadoPruebas != ''){
		$cadena.= $mas."`EstadoPruebas` = '".$EstadoPruebas."'";
		$mas = ",";
	}
	if ($EstadoMapa != ''){
		$cadena.= $mas."`EstadoMapa` = '".$EstadoMapa."'";
		$mas = ",";
	}
	if ($Juego != ''){
		$cadena.= $mas."`Juego` = '".$Juego."'";
		$mas = ",";
	}
	if ($TamanoMapa != ''){
		$cadena.= $mas."`TamanoMapa` = '".$TamanoMapa."'";
		$mas = ",";
	}
	if ($TieneSub != ''){
		$cadena.= $mas."`TieneSub` = '".$TieneSub."'";
		$mas = ",";
	}
	if ($TieneNav != ''){
		$cadena.= $mas."`TieneNav` = '".$TieneNav."'";
		$mas = ",";
	}
	if ($Idioma != ''){
		$cadena.= $mas."`Idioma` = '".$Idioma."'";
		$mas = ",";
	}
	if ($EmailAutor != ''){
		$cadena.= $mas."`EmailAutor` = '".$EmailAutor."'";
		$mas = ",";
	}else{
		$cadena.= $mas."`EmailAutor` = NULL";
	}
	if ($FechaCreacion != ''){
		$cadena.= $mas."`FechaCreacion` = '".$FechaCreacion."'";
		$mas = ",";
	}else{
		$cadena.= $mas."`FechaCreacion` = NULL";
	}
	if ($Version != ''){
		$cadena.= $mas."`Version` = '".$Version."'";
		$mas = ",";
	}else{
		$cadena.= $mas."`Version` = NULL";
	}
	if ($Dificultad != ''){
		$cadena.= $mas."`Dificultad` = '".$Dificultad."'";
		$mas = ",";
	}else{
		$cadena.= $mas."`Dificultad` = NULL";
	}
	if ($NHumanos != ''){
		$cadena.= $mas."`NHumanos` = '".$NHumanos."'";
		$mas = ",";
	}
	if ($NJugadores != ''){
		$cadena.= $mas."`NJugadores` = '".$NJugadores."'";
		$mas = ",";
	}
	if ($TipoJuego != ''){
		$cadena.= $mas."`TipoJuego` = '".$TipoJuego."'";
		$mas = ",";
	}
	if ($EstiloMapa != ''){
		$cadena.= $mas."`EstiloMapa` = '".$EstiloMapa."'";
		$mas = ",";
	}else{
		$cadena.= $mas."`EstiloMapa` = NULL";
	}
	if ($Descripcion != ''){
		$cadena.= $mas."`Descripcion` = '".$Descripcion."'";
		$mas = ",";
	}else{
		$cadena.= $mas."`Descripcion` = NULL";
	}
	
	//Actualizar los campos standards
	$cadena.= ", `lu_date` = '". DateUtil::getDatetime() . "' ,";
	$cadena.= "`lu_uid` = '". pnUserGetVar('uid') . "'";
	//Recuperar el nombre de la tabla completa
	$pntable = &pnDBGetTables();	
	$table  = $pntable['Mapas'];
	
	$sql = "UPDATE $table SET ".$cadena." WHERE `ID` = ".$ID;

	return DBUtil::executeSQL($sql);
	
}

/**
 * Borrar un Mapa
 * @param $args['id'] ID del Mapa
 * @return bool true on success, false on failure
 */
function Mapas_adminapi_delete($args)
{
	// Argument check
	if (!isset($args['id'])) {
		return LogUtil::registerArgsError();
	}
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');

	//Confirmamos que el registro que queremos borrar, existe.
	$item = pnModAPIFunc('Mapas', 'user', 'get', array('ID' => $args['id']));

	if ($item === false) {
		return LogUtil::registerError(__('No such item found.', $dom));
	}

	if (!DBUtil::deleteObjectByID('Mapas', $args['id'], 'ID')) {
		return LogUtil::registerError(__('Error! Deletion attempt failed.', $dom));
	}

	// The item has been modified, so we clear all cached pages of this item.
	$render = & pnRender::getInstance('Mapas');
	$render->clear_cache(null, $args['id']);
	$render->clear_cache('Mapas_admin_view.htm');

	return true;
}

/**
 * Puntuar un mapa
 * @param $args['mid'] Mapa
 * @param $args['score'] Puntuación
 * @return bool true on success, false on failure
 */
function Mapas_adminapi_score($args)
{
	
	// Valida los parámetros requeridos
	if (!isset($args['mid']) ) {
		return LogUtil::registerArgsError();
	}else{
		$mid = $args['mid'];
	}
	if (!isset($args['score']) ) {
		return LogUtil::registerArgsError();
	}else{
		$score = $args['score'];
	}
	
	//Obtener los datos del mapa
	$mapa = pnModAPIFunc('Mapas', 'user', 'get', array('ID' => $mid));
	
	$mapa['PuntuacionRevision'] += $score;
	$mapa['NPuntuacionRevision']++;
	
	$cadena = "`PuntuacionRevision` = ".$mapa['PuntuacionRevision'].", `NPuntuacionRevision` = ".$mapa['NPuntuacionRevision'];
	//Actualizar la BD con los nuevos datos
	$pntable = &pnDBGetTables();	
	$table  = $pntable['Mapas'];
	
	$sql = "UPDATE $table SET ".$cadena." WHERE `ID` = ".$mid;
	
	return DBUtil::executeSQL($sql);

}
