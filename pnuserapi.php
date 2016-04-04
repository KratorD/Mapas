<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pnuserapi.php 24342 2008-06-06 12:03:14Z markwest $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

//Funcion para obtener mapas
function Mapas_userapi_getAll($args)
{
	// Security check
    if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_OVERVIEW)) {
        return $items;
    }
	extract($args);
	//unset($args);
	//Construir la clausula WHERE
	if (isset($args['NombreMapa']) && $args['NombreMapa'] != ''){
		// Obtiene todos los mapas porque su nombre contiene...
		$queryargs[] = "`NombreMapa` LIKE '%".$NombreMapa."%'";
	}
	if (isset($args['Autor']) && $args['Autor'] != ''){
		// Obtiene todos los mapas porque el autor contiene...
		$queryargs[] = "`Autor` LIKE '%".$Autor."%'";
	}
	if ($EstadoMapa != ''){
		// Obtiene todos los mapas por su estado
		if ($condicion == "!="){
			$queryargs[] = "`EstadoMapa` != '".$EstadoMapa."'";
		}else{
			$queryargs[] = "`EstadoMapa` = '".$EstadoMapa."'";
		}
	}
	if (isset($args['Juego']) && $args['Juego'] != ''){
		$Juego = $args['Juego'];
		// Obtiene todos los mapas porque el juego es...
		switch ( $Juego )
		{
			case '':
			case '---': { break; }		// No se realizará búsqueda por Juego
			case 'H2': { $queryargs[] = "`Juego` IN ('H2-SW', 'H2-PoL')"; break; }
			case 'H3': { $queryargs[] = "`Juego` IN ('H3-RoE', 'H3-AB', 'H3-SoD', 'H3-WoG')"; break; }
			case 'H4': { $queryargs[] = "`Juego` IN ('H4-Std', 'H4-GS', 'H4-WoW', 'H4-Eq')"; break; }
			case 'H5': { $queryargs[] = "`Juego` IN ('H5-Std', 'H4-HoF', 'H4-ToE')"; break; }
			default: { $queryargs[]= "`Juego` = '" . $Juego . "'"; break; }
		}
	}
	if (isset($args['TamanoMapa']) && $args['TamanoMapa'] != ''){
		$TamanoMapa = $args['TamanoMapa'];
		//Obtiene todos los mapas porque el tamaño es...
		switch ( $TamanoMapa )
		{
			case '':
			case '---': { break; }		// No se realizará búsqueda por TamanoMapa
			case 'Mediano o mayor': { $queryargs[] = "`TamanoMapa` IN ('Mediano', 'Grande', 'ExtraGrande')"; break; }
			case 'Grande o mayor': { $queryargs[] = "`TamanoMapa` IN ('Grande', 'ExtraGrande')"; break; }
			case 'Mediano o menor': { $queryargs[] = "`TamanoMapa` IN ('Pequeño', 'Mediano')"; break; }
			case 'Grande o menor': { $queryargs[] = "`TamanoMapa` IN ('Pequeño', 'Mediano', 'Grande')"; break; }
			default: { $queryargs[] = "`TamanoMapa` = '" . $TamanoMapa . "'"; break; }
		}
	}
	if ( $TieneSub != '---' && $TieneSub != '' ) { 
		if ($TieneSub == "No"){
			$queryargs[] = "`TieneSub` = FALSE"; 
		}else{
			$queryargs[] = "`TieneSub` = TRUE"; 
		}
	}
	if (isset($args['Idioma']) && $args['Idioma'] != ''){
		$Idioma = $args['Idioma'];
		switch ( $Idioma )
		{
			case '':
			case '---': { break; }		// No se realizará búsqueda por Idioma
			case 'Español o Inglés': { $queryargs[] = "`Idioma` IN ('Español', 'Inglés')"; break; }
			default: { $queryargs[] = "`Idioma` = '$Idioma'"; break; }
		}
	}
	if (isset($args['Dificultad']) && $args['Dificultad'] != '' && $args['Dificultad'] != '---'){
		$queryargs[] = "`Dificultad` = '".$args['Dificultad']."'";
	}
	if (isset($args['NHumanos']) && $args['NHumanos'] != '' && $args['NHumanos'] != '---'){
		$queryargs[] = "`NHumanos` = '".$args['NHumanos']."'";
	}
	if (isset($args['NJugadores']) && $args['NJugadores'] != '' && $args['NJugadores'] != '---'){
		$queryargs[] = "`NJugadores` = '".$args['NJugadores']."'";
	}
	if (isset($args['NJugadores']) && $args['NJugadores'] != '' && $args['NJugadores'] != '---'){
		$queryargs[] = "`NJugadores` = '".$args['NJugadores']."'";
	}
	if (isset($args['TipoJuego']) && $args['TipoJuego'] != '' && $args['TipoJuego'] != '---'){
		$queryargs[] = "`TipoJuego` = '".$args['TipoJuego']."'";
	}
	if (isset($args['EstiloMapa']) && $args['EstiloMapa'] != ''){
		//Pasar la cadena a un array
		$arrEstilos = explode(",", $args['EstiloMapa']);
		foreach($arrEstilos as $item){
			$arrWhere[] = "`EstiloMapa` LIKE '%" . $item . "%'";
		}

		//Crear el WHERE para este parametro
		$queryargs[] = implode(' OR ', $arrWhere);
	}

	if (count($queryargs) > 0) {
		$where = ' WHERE ' . implode(' AND ', $queryargs);
	}
	//Ordenar la tabla de resultados
	if (!isset($args['order']) || empty($args['order'])) {
		$args['order'] = 'ID';
	}
	if (!isset($args['tipoOrden']) || empty($args['tipoOrden'])) {
		$args['order'].= ' desc';
	} else {
		$args['order'].= ' '.$args['tipoOrden'];
	}
	//Paginación y nº de resultados
	if (!isset($args['startnum']) || empty($args['startnum'])) {
		$args['startnum'] = 1;
	}
	if (!isset($args['numitems']) || empty($args['numitems'])) {
		$args['numitems'] = -1;
	}
	if (!is_numeric($args['startnum']) ||
		!is_numeric($args['numitems'])) {
		return LogUtil::registerArgsError();
	}

	$objArray = DBUtil::selectObjectArray ('Mapas', $where, $args['order'], $args['startnum']-1, $args['numitems']);
	
	if ($objArray == false){
		$dom = ZLanguage::getModuleDomain('Mapas');
		LogUtil::registerError(__('Error! Map not found.', $dom));
	}
	//Return objects retrieved
	return $objArray;
		
}

//Funcion para obtener los datos de un mapa
function Mapas_userapi_get($args)
{
	// Valida los parámetros requeridos
	if (!isset($args['ID']) || !is_numeric($args['ID'])) {
		return LogUtil::registerArgsError();
	}
	// Security check
    if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_OVERVIEW)) {
        return $items;
    }
	// Extrae el elemento de la BD con el ID
	$mapa = DBUtil::selectObjectByID('Mapas', $args['ID'], 'ID');
	
	// Retorna el objeto
	return $mapa;
}

function Mapas_userapi_insert($args)
{
	
	if (!isset($args) || empty($args)) {
		return LogUtil::registerArgsError();
	}
	extract($args);
	
	if (!isset($NombreMapa) ||
		!isset($Autor) ||
		!isset($NombreFich) ||
		!isset($TamanoArchivo) ||
		!isset($FechaSubida) ||
		!isset($Usuario) ||
		!isset($EstadoPruebas) ||
		!isset($EstadoMapa) ||
		!isset($Juego) ||
		!isset($TamanoMapa) ||
		!isset($TieneSub) ||
		!isset($Idioma) ){
	
		$dom = ZLanguage::getModuleDomain('Mapas');
		return LogUtil::registerError(__('You do not have filled all the fields to insert to DB.', $dom));
			
	}
	
	$args['NombreMapa'] = pnVarPrepForStore($NombreMapa);
	$args['Autor'] 		= pnVarPrepForStore($Autor);
	$args['NombreFich'] = pnVarPrepForStore($NombreFich);
	$args['TamanoArchivo'] = (int)pnVarPrepForStore($TamanoArchivo);
	$args['FechaSubida'] = $FechaSubida;
	$args['Usuario'] = pnVarPrepForStore($Usuario);
	$args['EstadoPruebas'] = pnVarPrepForStore($EstadoPruebas);
	$args['EstadoMapa'] = pnVarPrepForStore($EstadoMapa);
	$args['Juego'] = pnVarPrepForStore($Juego);
	$args['TamanoMapa'] = pnVarPrepForStore($TamanoMapa);
	$args['TieneSub'] = pnVarPrepForStore($TieneSub);
	$args['Idioma'] = pnVarPrepForStore($Idioma);
	$args['EmailAutor'] = $EmailAutor;
	$args['FechaCreacion'] = pnVarPrepForStore($FechaCreacion);
	$args['Version'] = pnVarPrepForStore($Version);
	$args['Dificultad'] = pnVarPrepForStore($Dificultad);
	$args['NHumanos'] = pnVarPrepForStore($NHumanos);
	$args['NJugadores'] = pnVarPrepForStore($NJugadores);
	$args['TipoJuego'] = pnVarPrepForStore($TipoJuego);
	$args['EstiloMapa'] = pnVarPrepForStore($EstiloMapa);
	$args['Descripcion'] = pnVarPrepForStore($Descripcion);
	$args['Superficie'] = pnVarPrepForStore($Superficie);
	$args['Subterraneo'] = pnVarPrepForStore($Subterraneo);
	$args['PuntuacionRevision'] = pnVarPrepForStore($PuntuacionRevision);
	$args['NPuntuacionRevision'] = (int)pnVarPrepForStore($NPuntuacionRevision);
	$args['Descargas'] = (int)pnVarPrepForStore($Descargas);

	return DBUtil::insertObject($args, 'Mapas','ID');
	
}

//Funciones para obtener una cadena con todos los estilos de mapas
function Mapas_userapi_montarEstiloCadena($args)
{
	$EstiloMapa = $args['EstiloMapa'];
	$boton = $args['boton'];
	$EstiloMapa_e = $args['cadena'];
	
	if ( $EstiloMapa[$boton] == 'on' )
	{
		if ( $EstiloMapa_e == '' )
		{
			$EstiloMapa_e .= $boton;
		} else {
			$EstiloMapa_e .= ',' . $boton;
		}
	}
	return($EstiloMapa_e);
}

function Mapas_userapi_montarEstilo($args)
{
	// Valida los parámetros requeridos
	if (!isset($args['Estilos']) || empty($args['Estilos'])) {
		return LogUtil::registerArgsError();
	}

	$EstiloMapa = $args['Estilos'];
	$EstiloMapa_e = '';
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'Clasico',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'GrandesEjercitos',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'EscasoRecurso',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'Historia',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'PequenosEjercitos',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'RicoRecursos',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'Exploracion',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'InicioRapido',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'Equilibrado',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'Busquedas',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'MuchasCiudades',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'AtestadoFuertes',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'ControlArea',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'LimTiempo',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'NeutralesFuertes',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'GuarBarreras',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'Campania',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'Torneo',
									'cadena' => $EstiloMapa_e
								));
	$EstiloMapa_e = pnModAPIFunc('Mapas', 'user', 'montarEstiloCadena', 
							array(	'EstiloMapa' => $EstiloMapa,
									'boton' => 'Navegacion',
									'cadena' => $EstiloMapa_e
								));
	
	return $EstiloMapa_e;
}

function Mapas_userapi_desmontarEstilo($args)
{
	if (!isset($args['Marcados']) || empty($args['Marcados'])) {
		return LogUtil::registerArgsError();
	}
	
	$Marcados = explode(',', $args['Marcados']);
	foreach ($args['Estilos'] as $boton){
	
		if (in_array($boton, $Marcados))
		{
			$EstiloF[$boton] = 'on';
		} else {
			$EstiloF[$boton] = 'off';
		}
	}
	return $EstiloF;
}

//Funcion para enviar un email de notificacion a administradores
function RepeticionBatalla_userapi_sendEmail($args)
{
	
	//Mandatory
	if (!isset($args['autor']) || empty($args['autor'])) {
		return LogUtil::registerArgsError();
	}
	if (!isset($args['nombre']) || empty($args['nombre'])) {
		return LogUtil::registerArgsError();
	}
	$autor = $args['autor'];
	$nombre = $args['nombre'];	
	
	if(pnModAvailable('Mailer')) {
		/*if ($args['coautor'] != ''){
			//Obtener la direccion de mail del coautor
			$detUser = pnModAPIFunc('User', 'user', 'get', array('uname' => $coautor));
			$emailCoa = $detUser['email'];
		}*/
		$sitename  = pnConfigGetVar('sitename');
		$toname    = 'Administradores de Repeticiones de Batalla';
		$toaddress = 'torredemarfil@heroesofmightandmagic.es';
		$subject   = 'Nuevo mapa añadido';
		$hoy = date("d-m-Y H:i");
		$body = 'El usuario '.$autor.' ha subido un nuevo mapa. Visita 
							http://www.heroesmightmagic.es/index.php?module=Mapas&type=admin&func=view
							para gestionarla. <br>'.
							'Nombre: '.$nombre.'<br>'.
							'Fecha: '.$hoy;
		$res = pnModAPIFunc('Mailer', 'user', 'sendmessage',
						array(	'toname'      => $toname,
                                'toaddress'   => $toaddress,
								//'cc'					 => $emailCoa,
                                'subject'     => $sitename." - ".$subject,
                                'body'        => $body));	
		return $res;
	}
	// no mailer module - error!
	return $false;
}

/**
 * Contar los registros por una condicion
 * @param $args['usuario'] Usuario
 * @return Número de registros encontrados
 */
function Mapas_userapi_countitems($args)
{
	
	$queryargs = array();

	if (isset($args['NombreMapa']) && $args['NombreMapa'] != ''){
		$queryargs[] = "`NombreMapa` LIKE '%".$args['NombreMapa']."%'";
	}
	if (isset($args['Autor']) && $args['Autor'] != ''){
		$queryargs[] = "`Autor` LIKE '%".$args['Autor']."%'";
	}
	if (isset($args['EstadoMapa']) && $args['EstadoMapa'] != ''){
		$queryargs[] = "`EstadoMapa` = '".$args['EstadoMapa']."'";
	}
	if (isset($args['Juego']) && $args['Juego'] != ''){
		$Juego = $args['Juego'];
		// Obtiene todos los mapas porque el juego es...
		switch ( $Juego )
		{
			case '':
			case '---': { break; }		// No se realizará búsqueda por Juego
			case 'H2': { $queryargs[] = "`Juego` IN ('H2-SW', 'H2-PoL')"; break; }
			case 'H3': { $queryargs[] = "`Juego` IN ('H3-RoE', 'H3-AB', 'H3-SoD', 'H3-WoG')"; break; }
			case 'H4': { $queryargs[] = "`Juego` IN ('H4-Std', 'H4-GS', 'H4-WoW', 'H4-Eq')"; break; }
			case 'H5': { $queryargs[] = "`Juego` IN ('H5-Std', 'H4-HoF', 'H4-ToE')"; break; }
			default: { $queryargs[]= "`Juego` = '" . $Juego . "'"; break; }
		}
	}
	if (isset($args['TamanoMapa']) && $args['TamanoMapa'] != ''){
		$TamanoMapa = $args['TamanoMapa'];
		//Obtiene todos los mapas porque el tamaño es...
		switch ( $TamanoMapa )
		{
			case '':
			case '---': { break; }		// No se realizará búsqueda por TamanoMapa
			case 'Mediano o mayor': { $queryargs[] = "`TamanoMapa` IN ('Mediano', 'Grande', 'ExtraGrande')"; break; }
			case 'Grande o mayor': { $queryargs[] = "`TamanoMapa` IN ('Grande', 'ExtraGrande')"; break; }
			case 'Mediano o menor': { $queryargs[] = "`TamanoMapa` IN ('Pequeño', 'Mediano')"; break; }
			case 'Grande o menor': { $queryargs[] = "`TamanoMapa` IN ('Pequeño', 'Mediano', 'Grande')"; break; }
			default: { $queryargs[] = "`TamanoMapa` = '" . $TamanoMapa . "'"; break; }
		}
	}
	$TieneSub = $args['TieneSub'];
	if ( $TieneSub != '---' && $TieneSub != '' ) { 
		if ($TieneSub == "No"){
			$queryargs[] = "`TieneSub` = FALSE"; 
		}else{
			$queryargs[] = "`TieneSub` = TRUE"; 
		}
	}
	if (isset($args['Idioma']) && $args['Idioma'] != ''){
		$Idioma = $args['Idioma'];
		switch ( $Idioma )
		{
			case '':
			case '---': { break; }		// No se realizará búsqueda por Idioma
			case 'Español o Inglés': { $queryargs[] = "`Idioma` IN ('Español', 'Inglés')"; break; }
			default: { $queryargs[] = "`Idioma` = '$Idioma'"; break; }
		}
	}
	if (isset($args['Dificultad']) && $args['Dificultad'] != '' && $args['Dificultad'] != '---'){
		$queryargs[] = "`Dificultad` = '".$args['Dificultad']."'";
	}
	if (isset($args['NHumanos']) && $args['NHumanos'] != '' && $args['NHumanos'] != '---'){
		$queryargs[] = "`NHumanos` = '".$args['NHumanos']."'";
	}
	if (isset($args['NJugadores']) && $args['NJugadores'] != '' && $args['NJugadores'] != '---'){
		$queryargs[] = "`NJugadores` = '".$args['NJugadores']."'";
	}
	if (isset($args['TipoJuego']) && $args['TipoJuego'] != '' && $args['TipoJuego'] != '---'){
		$queryargs[] = "`TipoJuego` = '".$args['TipoJuego']."'";
	}
	if (isset($args['EstiloMapa']) && $args['EstiloMapa'] != ''){
		//Pasar la cadena a un array
		$arrEstilos = explode(",", $args['EstiloMapa']);
		foreach($arrEstilos as $item){
			$arrWhere[] = "`EstiloMapa` LIKE '%" . $item . "%'";
		}

		//Crear el WHERE para este parametro
		$queryargs[] = implode(' OR ', $arrWhere);
	}
	
	$where = '';
	if (count($queryargs) > 0) {
		$where = ' WHERE ' . implode(' AND ', $queryargs);
	}

	return DBUtil::selectObjectCount ('Mapas', $where, 'ID', false, '');

}

/**
 * Añadir una descarga para el mapa
 * @param $args['ID'] ID del mapa
 * @return true or false
 */
function Mapas_userapi_incrementhits($args)
{

	if (!isset($args['ID']) || !is_numeric($args['ID'])) {
		return LogUtil::registerArgsError();
	} else {
		return DBUtil::incrementObjectFieldByID('Mapas', 'Descargas', $args['ID'], 'ID');
	}

}

/**
 * Obtener los 10 mapas mejor valorados por cartografos
 * @param $args['ID'] ID del mapa
 * @return true or false
 */
function Mapas_userapi_getTopTen($args)
{

	//Obtener el nombre de la tabla completa
	$tables = pnDBGetTables();
	//Obtener los 10 mapas
	$sql = "SELECT `ID`, `NombreMapa`, (`PuntuacionRevision` / `NPuntuacionRevision`) AS `Puntuacion` FROM $tables[Mapas] WHERE `EstadoMapa` = 'Comprobado' ORDER BY `Puntuacion` DESC LIMIT 10";
	$rs = DBUtil::executeSQL($sql);
	
	//Devolver en un objeto más manejable
	$cont = 0;
	while (!$rs->EOF) {
		$objArray[$cont]['ID'] = $rs->fields[0];
		$objArray[$cont]['NombreMapa'] = $rs->fields[1];
		$objArray[$cont]['Puntuacion'] = round ($rs->fields[2], 1);
		
		$rs->MoveNext();
		$cont++;
	}

	return $objArray;
}

/**
 * Obtener los 10 mapas mejor valorados por usuarios
 * @param $args['ID'] ID del mapa
 * @return true or false
 */
function Mapas_userapi_getTopTenUser($args)
{

	//Obtener los mejor valorados por los usuarios
	$TopTenUser = pnModAPIFunc('ratings', 'user', 'getAll', 
						array(	'modname'=> 'Mapas',
								'sortby' 	=> 'rating',
								'numitems'	=> '10'));

	if (count($TopTenUser) > 0){
		//Recuperar los datos del mapa a partir de los items de rating
		foreach ($TopTenUser as $items){
			$cad[]= $items['itemid'];
		}
		$test = implode(",", $cad);
		
		//Obtener el nombre de la tabla completa
		$tables = pnDBGetTables();
		//Obtener los 10 mapas
		$sql = "SELECT `ID`, `NombreMapa` FROM $tables[Mapas] WHERE `ID` IN (".$test.") AND `EstadoMapa` = 'Comprobado' ";
		$rs = DBUtil::executeSQL($sql);
		
		//Devolver en un objeto más manejable
		$cont = 0;
		while (!$rs->EOF) {
			$objArray[$cont]['ID'] = $rs->fields[0];
			$objArray[$cont]['NombreMapa'] = $rs->fields[1];
			foreach ($TopTenUser as $items){
				if ($items['itemid'] == $rs->fields[0]){
					$objArray[$cont]['Puntuacion'] = $items['rating'];
					break;
				}
			}
			
			$rs->MoveNext();
			$cont++;
		}
		
		//Ordenar el resultado
		foreach($objArray as $key => $row){
			 $id[$key]  = $row['ID'];
		}
		array_multisort($id, SORT_DESC, $objArray);
	}

	return $objArray;
}

/**
 * get meta data for the module
 */
function Mapas_userapi_getmodulemeta()
{
   return array('viewfunc'    => 'main',
                'displayfunc' => 'display',
                'newfunc'     => 'new',
                'modifyfunc'  => 'modify',
                'updatefunc'  => 'update',
                'deletefunc'  => 'delete',
                'titlefield'  => 'title',
                'itemid'      => 'mid');
}