<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pnadmin.php 31 2008-12-23 20:55:41Z Guite $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function Mapas_admin_main()
{
    if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }
	
	return Mapas_admin_view();
}

function Mapas_admin_view($args)
{
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Obtener los parametros
	$page  = (int)FormUtil::getPassedValue('page', isset($args['page']) ? $args['page'] : 1, 'GET');
	$order = FormUtil::getPassedValue('order', isset($args['order']) ? $args['order'] : 'ID', 'GET');
	$est = FormUtil::getPassedValue('est', isset($args['est']) ? $args['est'] : 'SinComprobar', 'GET');

	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Mapas');
	
	$itemsperpage = $modvars['itemsperpage'];
	$path = $modvars['path'];
	$path.= "temp/";

	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');
	
	// Primer elemento a obtener de la paginacion
	$startnum = (($page - 1) * $itemsperpage) + 1;
	
	// Procesamos los datos con los APIs necesarios
	$mapas = pnModAPIFunc('Mapas', 'user', 'getAll', 
						array(	'EstadoMapa'=> $est,
								'startnum'  => $startnum,
								'numitems'  => $itemsperpage,
								'order'     => $order));
	
	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Mapas');

	//Enviarlas a la plantilla
	$render->assign('mapas', $mapas);
	$render->assign('path', $path);
	$render->assign('est', $est);
	
	// Asignar los valores al sistema de paginación
	$render->assign('pager', array(	'numitems' => pnModAPIFunc('Mapas', 'user', 'countitems',
															array(	'EstadoMapa'=> 'SinComprobar'
																	)),
									'itemsperpage' => $itemsperpage));
	
	return $render->fetch('Mapas_admin_view.htm');
	
}

//Funcion que presenta la plantilla para ver una tarea
function Mapas_admin_display($args)
{
	
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Obtener los parametros
	$mid  = (int)FormUtil::getPassedValue('mid', isset($args['mid']) , 'GET');
	
  	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Tareas');
	
	//Obtenemos los datos del mapa
	$mapa = pnModAPIFunc('Mapas', 'user', 'get', array('ID' => $mid));
	if ($mapa == false)
		return LogUtil::registerError(__('Error! Map do not found.', $dom));
		
	$estilos = array('Clasico','GrandesEjercitos','EscasoRecurso','MuchasCiudades','Historia','PequenosEjercitos',
		'RicoRecursos','AtestadoFuertes','Exploracion','InicioRapido','Equilibrado','ControlArea','Busquedas',
		'LimTiempo','Campania','NeutralesFuertes','Torneo','GuarBarreras','Navegacion');
	$EstiloMapa = pnModAPIFunc('Mapas', 'user', 'desmontarEstilo', 
						array(	'Marcados'=> $mapa['EstiloMapa'],
								'Estilos' => $estilos));

	/*$rating = pnModAPIFunc('ratings', 'user', 'display', 
						array(	'objectid'=> '1'));*/
	
	$path = pnModGetVar('Mapas', 'path');
	$path.= "temp/";
	
	//Calculamos su puntuacion
	if ($mapa['NPuntuacionRevision'] == 0){
		$puntuacion = 0;
	}else{
		$puntuacion = round( $mapa['PuntuacionRevision'] / $mapa['NPuntuacionRevision'] );
	}
	
	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Mapas');
	
	//Pasamos las variables a la plantilla
	$render->assign('mapa', $mapa);
	$render->assign('path', $path);
	$render->assign('EstiloMapa', $EstiloMapa);
	$render->assign('puntuacion', $puntuacion);
	
	return $render->fetch('Mapas_admin_display.htm');
	
}

//Funcion que presenta la plantilla para editar un mapa
function Mapas_admin_modify($args)
{
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Obtener los parametros
	$mid  = (int)FormUtil::getPassedValue('mid', isset($args['mid']) , 'GET');
	
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');
	
	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Mapas');
	
	$juegos = explode("/", $modvars['juegos']);
	$tamanos = explode("/", $modvars['tamanos']);
	$dificultades = explode("/", $modvars['dificultades']);
	$tipoJuegos = explode("/", $modvars['tipoJuegos']);
	
	//Recuperamos los datos del registro que queremos modificar.
	$mapa = pnModAPIFunc('Mapas', 'user', 'get', array('ID' => $mid));
	if ($mapa == false)
		return LogUtil::registerError(__('Error! Map do not found.', $dom));
	
	$estilos = array('Clasico','GrandesEjercitos','EscasoRecurso','MuchasCiudades','Historia','PequenosEjercitos',
		'RicoRecursos','AtestadoFuertes','Exploracion','InicioRapido','Equilibrado','ControlArea','Busquedas',
		'LimTiempo','Campania','NeutralesFuertes','Torneo','GuarBarreras','Navegacion');
	$EstiloMapa = pnModAPIFunc('Mapas', 'user', 'desmontarEstilo', 
						array(	'Marcados'=> $mapa['EstiloMapa'],
								'Estilos' => $estilos));
								
	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Mapas');
	
	$render->assign('mapa', $mapa);
	$render->assign('juegos', $juegos);
	$render->assign('tamanos', $tamanos);
	$render->assign('dificultades', $dificultades);
	$render->assign('tipoJuegos', $tipoJuegos);
	$render->assign('EstiloMapa', $EstiloMapa);

	return $render->fetch('Mapas_admin_modify.htm');
	
}

function Mapas_admin_update($args)
{
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	// Obtener los parametros
	$mid  = (int)FormUtil::getPassedValue('mid', isset($args['mid']) , 'GET');
	
	// Lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');
	
	// Confirm authorisation code
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('Mapas', 'user', 'main'));
    }
	
	//Recuperamos los datos del registro que queremos modificar.
	$mapa = pnModAPIFunc('Mapas', 'user', 'get', array('ID' => $mid));
	if ($mapa == false){
		return LogUtil::registerError(__('Error! Map do not found.', $dom));
	}
	
	extract($args);
	unset($args);

	list(	$NombreMapa,
			$Autor,
			$EstadoPruebas,
			$Juego,
			$TamanoMapa,
			$TieneSub,
			$Idioma,
			$TieneNav,
			$Version,
			$Fecha_Cre,
			$EmailAutor,
			$Dificultad,
			$NHumanos,
			$NJugadores,
			$TipoJuego,
			$EstiloMapa,
			$Descripcion) = pnVarCleanFromInput(
			 									'NombreMapa', 'Autor', 'EstadoPruebas', 'Juego', 'TamanoMapa', 'TieneSub',
												'Idioma', 'TieneNav', 'Version', 'Fecha_Cre', 'EmailAutor', 'Dificultad',
												'NHumanos', 'NJugadores', 'TipoJuego', 'EstiloMapa', 'Descripcion');
	
	// No html permitido para las casillas de texto
	list($NombreMapa, $Autor, $Version, $EmailAutor, $Descripcion)	
		= pnVarPrepForDisplay($NombreMapa, $Autor, $Version, $EmailAutor, $Descripcion);
	
	// Comprobar campos obligatorios.
	if ( ($NombreMapa=='') || ($Autor=='') || ($Juego=='---') || ($Idioma=='---') || ($TieneSub=='---') || 
		 ($TieneNav=='---') || ($TamanoMapa=='---') )
	{
		// Construimos y devolvemos la Vista
		$render = & pnRender::getInstance('Mapas');
		$tipo_error = 1;  //Error por parametros de entrada vacios
		$render->assign('tipo_error', $tipo_error);
		return $render->fetch('Mapas_admin_error.htm');
	}

	//Preparar los datos para modificar el registro del mapa
	$record['NombreMapa'] = $NombreMapa;
	$record['Autor'] = $Autor;
	//$record['NombreFich'] = $fileNameNew;
	//$record['TamanoArchivo'] = $fileSize;
	//$record['FechaSubida'] = date('Y-m-d');
	//$record['Usuario'] = pnUserGetVar('uid');
	$record['EstadoPruebas'] = 'EsperandoProbadores';
	//$record['EstadoMapa'] = 'SinComprobar';
	$record['Juego'] = $Juego;
	$record['TamanoMapa'] = $TamanoMapa;
	$record['TieneSub'] = $TieneSub;
	$record['TieneNav'] = $TieneNav;
	$record['Idioma'] = $Idioma;
	$record['EmailAutor'] = $EmailAutor;
	$record['FechaCreacion'] = $Fecha_Cre;
	$record['Version'] = $Version;
	$record['Dificultad'] = $Dificultad;
	$record['NHumanos'] = $NHumanos;
	$record['NJugadores'] = $NJugadores;
	$record['TipoJuego'] = $TipoJuego;
	$record['EstiloMapa'] = pnModAPIFunc('Mapas', 'user', 'montarEstilo', array('Estilos' => $EstiloMapa));
	$record['Descripcion'] = $Descripcion;
	//$record['Superficie'] = '';
	//$record['Subterraneo'] = '';
	//$record['PuntuacionRevision'] = 0;
	//$record['NPuntuacionRevision'] = 0;
	//$record['Descargas'] = 0;

	//Actualizar la BD con los nuevos datos
	$result = pnModAPIFunc('Mapas', 'admin', 'update',	
							array(	'ID' 	 	    => $mid,
									'NombreMapa' 	=> $NombreMapa,
									'Autor'    		=> $Autor,
									'EstadoPruebas' => $EstadoPruebas,
									'Juego'  	   	=> $Juego,
									'TamanoMapa'  	=> $TamanoMapa,
									'TieneSub' 		=> $TieneSub,
									'TieneNav' 		=> $TieneNav,
									'Idioma' 		=> $Idioma,
									'EmailAutor' 	=> $EmailAutor,
									'FechaCreacion' => $Fecha_Cre,
									'Version' 		=> $Version,
									'Dificultad' 	=> $Dificultad,
									'NHumanos' 		=> $NHumanos,
									'NJugadores' 	=> $NJugadores,
									'TipoJuego' 	=> $TipoJuego,
									'EstiloMapa' 	=> $record['EstiloMapa'],
									'Descripcion' 	=> $Descripcion
							));
	
	if ($result == false){
		// Error
		LogUtil::registerError(__('Error! The map was not updated.', $dom));
	}else{
		// Success
		LogUtil::registerStatus (__('Map modify sucessfully.', $dom));
	}
			
	return pnRedirect(pnModURL('Mapas', 'admin', 'view'));
  
}

//Funcion para borrar un mapa
function Mapas_admin_delete($args)
{
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	$confirmation = FormUtil::getPassedValue('confirmation', null, 'POST');
	
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');
	
	//Recuperar los parametros
	$mid = isset($args['mid']) ? $args['mid'] : FormUtil::getPassedValue('mid', null, 'REQUEST');

	// Check for confirmation.
	if (empty($confirmation)) {
  	// No confirmation yet
		// Construimos y devolvemos la Vista
		$render = & pnRender::getInstance('Mapas');
		$render->assign('mid', $mid);

		// Return the output that has been generated by this function
		return $render->fetch('Mapas_admin_delete.htm');
	}
	// Confirm authorisation code
	if (!SecurityUtil::confirmAuthKey()) {
		return LogUtil::registerAuthidError (pnModURL('Mapas', 'admin', 'view'));
	}
	
	//Confirmamos que el registro que queremos borrar, existe.
	$lista = pnModAPIFunc('Mapas', 'user', 'get', array('ID' => $mid));
	
	if ($lista == false) {
		return LogUtil::registerError(__('Error! The map do not exists.', $dom));
	}
	
	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Mapas');
	
	$filename = $modvars['path'].'temp/'.$lista['NombreFich'];
	$filesupe = $modvars['path'].'temp/'.$lista['Superficie'];
	$filesubt = $modvars['path'].'temp/'.$lista['Subterraneo'];
	
	//Almacenar el registro en la papelera
	if (pnModAvailable('Papelera')){
		$usuario = pnUserGetVar('uname');
		$bin = pnModAPIFunc('Papelera', 'admin', 'store', array('Modulo' => 'Mapas',
																'Tabla' => 'Mapas',
																'Usuario' => $usuario,
																'Registro' => $mid));
	}
	
	if (pnModAPIFunc('Mapas', 'admin', 'delete', array('id' =>$mid))) {
		// Success
		LogUtil::registerStatus (__('Map deleted sucessfully.', $dom));
		//Borrar el fichero del mapa
		if (!unlink($filename)) {
            LogUtil::registerError(__('Error deleting file') . ': ' . $filename);
        }
		//Borrar el fichero de la superficie
		if (!unlink($filesupe)) {
            LogUtil::registerError(__('Error deleting file') . ': ' . $filesupe);
        }
		//Borrar el fichero del subterraneo
		if (!unlink($filesubt)) {
            LogUtil::registerError(__('Error deleting file') . ': ' . $filesubt);
        }
	}else{
		// Error
		return LogUtil::registerError(__('Error! The map was not deleted.', $dom));
	}
	return pnRedirect(pnModURL('Mapas', 'admin', 'view'));
	
}

//Funcion para puntuar un admin un mapa
function Mapas_admin_score($args)
{
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	$confirmation = FormUtil::getPassedValue('confirmation', null, 'POST');
	
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');
	
	//Recuperar los parametros
	$mid = isset($args['mid']) ? $args['mid'] : FormUtil::getPassedValue('mid', null, 'REQUEST');
	$score = (int)isset($args['Score']) ? $args['Score'] : FormUtil::getPassedValue('Score', null, 'REQUEST');

	// Check for confirmation.
	if (empty($confirmation)) {
  	// No confirmation yet
		// Construimos y devolvemos la Vista
		$render = & pnRender::getInstance('Mapas');
		$render->assign('mid', $mid);

		// Return the output that has been generated by this function
		return $render->fetch('Mapas_admin_score.htm');
	}
	// Confirm authorisation code
	if (!SecurityUtil::confirmAuthKey()) {
		return LogUtil::registerAuthidError (pnModURL('Mapas', 'admin', 'view'));
	}
	
	//Confirmamos que el registro que queremos puntuar, existe.
	$lista = pnModAPIFunc('Mapas', 'user', 'get', array('ID' => $mid));
	
	if ($lista == false) {
		return LogUtil::registerError(__('Error! The map do not exists.', $dom));
	}
	
	if (pnModAPIFunc('Mapas', 'admin', 'score', array('mid' =>$mid, 'score' => $score))) {
		// Success
		LogUtil::registerStatus (__('Map scored sucessfully.', $dom));
	}else{
		// Error
		return LogUtil::registerError(__('Error! The map was not scored.', $dom));
	}
	return pnRedirect(pnModURL('Mapas', 'admin', 'view'));
	
}

//Funcion para validar un mapa
function Mapas_admin_validate($args)
{
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}

	//Recuperar los parametros
	$mid = isset($args['mid']) ? $args['mid'] : FormUtil::getPassedValue('mid', null, 'REQUEST');
	
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');
	
	//Confirmamos que el mapa que queremos validar, existe.
	$lista = pnModAPIFunc('Mapas', 'user', 'get', array('ID' => $mid));
	
	if ($lista == false) {
		return LogUtil::registerError(__('Error! The map do not exists.', $dom));
	}
	
	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Mapas');
	$render->assign('mid', $mid);

	return $render->fetch('Mapas_admin_validate.htm');


}

//Funcion para validar un mapa
function Mapas_admin_confirmValidate($args)
{
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');
	
	//Recuperar los parametros
	$mid = isset($args['mid']) ? $args['mid'] : FormUtil::getPassedValue('mid', null, 'REQUEST');

	//Confirmamos que el mapa que queremos validar, existe.
	$mapa = pnModAPIFunc('Mapas', 'user', 'get', array('ID' => $mid));
	
	if ($mapa == false) {
		return LogUtil::registerError(__('Error! The map do not exists.', $dom));
	}
	
	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Mapas');
	
	$path = $modvars['path'];
	
	//Actualizar campos
	$cadena = "`EstadoMapa` = 'Comprobado', ";
	$fechaActual = date('Y-m-d');
	$cadena.= "`FechaSubida` = ".$fechaActual;
	
	//Actualizar la BD con los nuevos datos
	$pntable = &pnDBGetTables();	
	$table  = $pntable['Mapas'];
	
	$sql = "UPDATE $table SET ".$cadena." WHERE `ID` = ".$mid;
	
	if (!DBUtil::executeSQL($sql)){
		// Error
		return LogUtil::registerError(__('Error! The map was not updated.', $dom));
	}
	
	//Copiar los ficheros a la ruta definitiva
	$source = $path . "temp/" ;
	$dest   = $path . $mapa['ID'] . "/";
	//Preparar el directorio
	// checks if the thumbnail folder exists and if it not exists create it
    if (!file_exists($dest)) {
        FileUtil::mkdirs($dest, 0777, true);
    }
	
	//Mover el fichero del mapa
	$origmapa = $source . $mapa['NombreFich'];
	$destmapa = $dest . $mapa['NombreFich'];
	if (!rename($origmapa, $destmapa)){
		return LogUtil::registerError(__('Error! The map was not moved.', $dom));
	}
	//Mover el fichero de la superficie
	$origsup = $source . $mapa['Superficie'];
	$destsup = $dest . $mapa['Superficie'];
	if (!rename($origsup, $destsup)){
		return LogUtil::registerError(__('Error! The image was not moved.', $dom));
	}
	//Mover el fichero del subterraneo
	$origsub = $source . $mapa['Subterraneo'];
	$destsub = $dest . $mapa['Subterraneo'];
	if (!rename($origsub, $destsub)){
		return LogUtil::registerError(__('Error! The image of underground was not moved.', $dom));
	}
	
	// Success
	LogUtil::registerStatus (__('Map validated sucessfully.', $dom));
	
	return pnRedirect(pnModURL('Mapas', 'admin', 'view'));
	
}

//Funcion para mostrar la configuracion
function Mapas_admin_modifyconfig($args)
{
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');
	
	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Mapas');
	
	$path = $modvars['path'];
	$juegos = $modvars['juegos'];
	$tamanos = $modvars['tamanos'];
	$dificultades = $modvars['dificultades'];
	$tipoJuegos = $modvars['tipoJuegos'];
	$allowedExtensions = $modvars['allowedExtensions'];
	$filesMaxSize = $modvars['filesMaxSize'];
	
	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Mapas');

	$render->assign('path', $path);
	$render->assign('juegos', $juegos);
	$render->assign('tamanos', $tamanos);
	$render->assign('dificultades', $dificultades);
	$render->assign('tipoJuegos', $tipoJuegos);
	$render->assign('allowedExtensions', $allowedExtensions);
	$render->assign('filesMaxSize', $filesMaxSize);

	return $render->fetch('Mapas_admin_modifyconfig.htm');
	
}

//Funciona para actualizar la configuracion del modulo
function Mapas_admin_updateconfig($args)
{
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');
	
	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Mapas');
	
	$path = $modvars['path'];
	$juegos = $modvars['juegos'];
	$tamanos = $modvars['tamanos'];
	$dificultades = $modvars['dificultades'];
	$tipoJuegos = $modvars['tipoJuegos'];
	
	//Valor de path
	$txtpath = FormUtil::getPassedValue('txtPath', null, 'POST');
	if ($txtpath != $path){
		pnModSetVar('Mapas', 'path', $txtpath);
	}
	
	//Valor de juegos
	$txtjuegos = FormUtil::getPassedValue('txtJuegos', null, 'POST');
	if ($txtJuegos != $juegos){
		pnModSetVar('Mapas', 'juegos', $txtjuegos);
	}
	
	//Valor de tamaños
	$txtTamanos = FormUtil::getPassedValue('txtTamanos', null, 'POST');
	if ($txtJuegos != $tamanos){
		pnModSetVar('Mapas', 'tamanos', $txtTamanos);
	}
	
	//Valor de dificultades
	$txtDificultades = FormUtil::getPassedValue('txtDificultades', null, 'POST');
	if ($txtJuegos != $dificultades){
		pnModSetVar('Mapas', 'dificultades', $txtDificultades);
	}
	
	//Valor de tipos de juego
	$txtTipoJuegos = FormUtil::getPassedValue('txtTipoJuegos', null, 'POST');
	if ($txtJuegos != $tipoJuegos){
		pnModSetVar('Mapas', 'tipoJuegos', $txtTipoJuegos);
	}
	
	//Valor de extensiones permitidas
	$txtallowedExtensions = FormUtil::getPassedValue('txtallowedExtensions', null, 'POST');
	if ($txtJuegos != $allowedExtensions){
		pnModSetVar('Mapas', 'allowedExtensions', $txtallowedExtensions);
	}
	
	//Valor de tamaño máximo de fichero
	$txtfilesMaxSize = FormUtil::getPassedValue('txtfilesMaxSize', null, 'POST');
	if ($txtJuegos != $filesMaxSize){
		pnModSetVar('Mapas', 'filesMaxSize', $txtfilesMaxSize);
	}
	
	// the module configuration has been updated successfuly
    LogUtil::registerStatus (__('Done! Module configuration updated.', $dom));

    return pnRedirect(pnModURL('Mapas', 'admin', 'main'));
	
}