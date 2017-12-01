<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pnuser.php 24342 2008-06-06 12:03:14Z markwest $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function Mapas_user_main()
{
    //Permisos generales
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_OVERVIEW)) {
		return LogUtil::registerPermissionError();
	}

	//Dominio del lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');

	//Obtener los registros del menú correspondiente
	$menu = pnModFunc('h_menus', 'user', 'gethtml', array('codigo' => 'mapas'));
	
	//Obtener los últimos 10 mapas
	$lastTen = pnModAPIFunc('Mapas', 'user', 'getAll', 
						array(	'EstadoMapa'=> 'Comprobado',
								'order' 	=> 'FechaSubida',
								'numitems'	=> '10'));
								
	//Obtener los mejor valorados por los cartografos
	$TopTenCar = pnModAPIFunc('Mapas', 'user', 'getTopTen');
	
	//Obtener los mejor valorados por los usuarios
	$TopTenUser = pnModAPIFunc('Mapas', 'user', 'getTopTenUser');
	
	//Obtener los más descargados
	$mostDown = pnModAPIFunc('Mapas', 'user', 'getAll', 
						array(	'EstadoMapa'=> 'Comprobado',
								'order' 	=> 'Descargas',
								'numitems'	=> '10'));
	
	$nmapas = count($lastTen);
	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Mapas');
	
	//Pasamos las variables a la plantilla
	$render->assign('menu', $menu);
	$render->assign('lastTen', $lastTen);
	$render->assign('TopTenCar', $TopTenCar);
	$render->assign('mostDown', $mostDown);
	$render->assign('TopTenUser', $TopTenUser);
	$render->assign('nmapas', $nmapas);
	
	return $render->fetch('Mapas_user_main.htm');
	
}

function Mapas_user_view($args)
{
	//Permisos generales
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_OVERVIEW)) {
		return LogUtil::registerPermissionError();
	}
	//Obtener los parametros
	$page  = (int)FormUtil::getPassedValue('page', isset($args['page']) ? $args['page'] : 1, 'GET');
	$order = FormUtil::getPassedValue('order', isset($args['order']) ? $args['order'] : 'ID', 'GET');
	$tipoOrden = FormUtil::getPassedValue('tipoOrden', isset($args['tipoOrden']) ? $args['tipoOrden'] : 'DESC', 'GET');

	//Dominio del lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');

	//Obtener los registros del menú correspondiente
	$menu = pnModFunc('h_menus', 'user', 'gethtml', array('codigo' => 'mapas'));

	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Mapas');

	$itemsperpage = $modvars['itemsperpage'];
	
	// Primer elemento a obtener de la paginacion
	$startnum = (($page - 1) * $itemsperpage) + 1;

	//Obtener todos los mapas disponibles para descargar
	$mapas = pnModAPIFunc('Mapas', 'user', 'getAll', 
						array(	'EstadoMapa'=> 'Comprobado',
								'startnum'  => $startnum,
								'numitems'  => $itemsperpage,
								'order'     => $order,
								'tipoOrden' => $tipoOrden,
							));

	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Mapas');

	//Pasamos las variables a la plantilla
	$render->assign('menu', $menu);
	$render->assign('mapas', $mapas);
	
	// Asignar los valores al sistema de paginación
	$render->assign('pager', array(	'numitems' => pnModAPIFunc('Mapas', 'user', 'countitems',
															array(	'EstadoMapa'=> 'Comprobado')),
									'itemsperpage' => $itemsperpage));

	return $render->fetch('Mapas_user_view.htm');
	
}

function Mapas_user_display($args)
{
	//Permisos generales
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_OVERVIEW)) {
		return LogUtil::registerPermissionError();
	}
	
	//Obtener los parametros
	$mid = (int)FormUtil::getPassedValue('mid', isset($args['mid']), 'GET');

	$dom = ZLanguage::getModuleDomain('Mapas');

	//Obtener los registros del menú correspondiente
	$menu = pnModFunc('h_menus', 'user', 'gethtml', array('codigo' => 'mapas'));

	//Obtenemos los datos del mapa
	$mapa = pnModAPIFunc('Mapas', 'user', 'get', array('ID' => $mid));
	if ($mapa == false)
		return LogUtil::registerError(__('Error! Map do not found.', $dom));

	if ($mapa['EstadoMapa'] == 'SinComprobar')
		return LogUtil::registerError(__('Error! Map do not found.', $dom));

	$estilos = array('Clasico','GrandesEjercitos','EscasoRecurso','MuchasCiudades','Historia','PequenosEjercitos',
		'RicoRecursos','AtestadoFuertes','Exploracion','InicioRapido','Equilibrado','ControlArea','Busquedas',
		'LimTiempo','Campania','NeutralesFuertes','Torneo','GuarBarreras','Navegacion');
	$EstiloMapa = pnModAPIFunc('Mapas', 'user', 'desmontarEstilo', 
						array(	'Marcados'=> $mapa['EstiloMapa'],
								'Estilos' => $estilos));

	//Calculamos su puntuacion
	if ($mapa['NPuntuacionRevision'] == 0){
		$puntuacion = 0;
	}else{
		$puntuacion = round( $mapa['PuntuacionRevision'] / $mapa['NPuntuacionRevision'] );
	}

	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Mapas');
	
	$path 	 = $modvars['path'] . $mapa['ID'] . "/";

	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Mapas');
	
	//Pasamos las variables a la plantilla
	$render->assign('menu', $menu);
	$render->assign('mapa', $mapa);
	$render->assign('path', $path);
	$render->assign('EstiloMapa', $EstiloMapa);
	$render->assign('puntuacion', $puntuacion);
	
	return $render->fetch('Mapas_user_display.htm');
	
}

//Funcion que presenta la plantilla para subir/agregar un mapa
function Mapas_user_new()
{
	//Permisos generales
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_OVERVIEW)) {
		return LogUtil::registerPermissionError();
	}
  	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');
	
	// only logged-ins are allowed to see the overview.
    if (!pnUserLoggedIn()) {
        return LogUtil::registerError(__('Error! You aren\'t a registered user.', $dom), null, pnConfigGetVar('entrypoint', 'index.php'));
    }

	//Obtener los registros del menú correspondiente
	$menu = pnModFunc('h_menus', 'user', 'gethtml', array('codigo' => 'mapas'));

	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Mapas');
	
	$juegos = explode("/", $modvars['juegos']);
	$tamanos = explode("/", $modvars['tamanos']);
	$dificultades = explode("/", $modvars['dificultades']);
	$tipoJuegos = explode("/", $modvars['tipoJuegos']);
	
	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Mapas');
	//Pasamos variables a plantilla
	$render->assign('juegos', $juegos);
	$render->assign('tamanos', $tamanos);
	$render->assign('dificultades', $dificultades);
	$render->assign('tipoJuegos', $tipoJuegos);
	$render->assign('menu', $menu);

	return $render->fetch('Mapas_user_new.htm');
  
}

//Funcion que sube el fichero del mapa e inserta el registro en la BD
function Mapas_user_create()
{

	//Permisos generales
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_OVERVIEW)) {
		return LogUtil::registerPermissionError();
	}
  	
	// Obtener los parametros
	$newFile = FormUtil::getPassedValue('Ruta', isset($args['Ruta']) ? $args['Ruta'] : 0, 'REQUEST');
	
	// Lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');
	
	// Confirm authorisation code
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('Mapas', 'user', 'main'));
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
												'Idioma', 'Version', 'Fecha_Cre', 'EmailAutor', 'Dificultad',
												'NHumanos', 'NJugadores', 'TipoJuego', 'EstiloMapa', 'Descripcion');
	
	// No html permitido para las casillas de texto
	list($NombreMapa, $Autor, $Version, $EmailAutor, $Descripcion)	
		= pnVarPrepForDisplay($NombreMapa, $Autor, $Version, $EmailAutor, $Descripcion);
	
	// Comprobar campos obligatorios.
	if ( ($NombreMapa=='') || ($Autor=='') || ($Juego=='---') || ($Idioma=='---') || ($TieneSub=='---') || 
		 ($TamanoMapa=='---') || ($_FILES['Ruta']['name']=='') )
	{
		// Construimos y devolvemos la Vista
		$render = & pnRender::getInstance('Mapas');
		$tipo_error = 1;  //Error por parametros de entrada vacios
		$render->assign('tipo_error', $tipo_error);
		return $render->fetch('Mapas_user_error.htm');
	}

	// Nombre del fichero del servidor
    $fileName = $_FILES['Ruta']['name'];
	
	if ($fileName != '') {
		// Obtener la extensión del fichero
        $file_extension = FileUtil::getExtension($fileName);
        $allowedExtensions = pnModGetVar('Mapas', 'allowedExtensions');
        $allowedExtensionsArray = explode('/', $allowedExtensions);
        if (!in_array(strtolower($file_extension), $allowedExtensionsArray) && !in_array(strtoupper(($file_extension)), $allowedExtensionsArray)) {
            return LogUtil::registerError(__f('The extension <strong>%s</strong> is not allowed. The allowed extensions are: <strong>%s</strong>.', array(
                $file_extension,
                str_replace('/', ', ', $allowedExtensions)), $dom));
        }
		
		// Obtener el tamaño del fichero
        $fileSize = $_FILES['Ruta']['size'];
        if ($fileSize > pnModGetVar('Mapas', 'filesMaxSize')) {
            return LogUtil::registerError(__f('The file is too big. The maximum size allowed is "%s" bytes.', pnModGetVar('Mapas', 'filesMaxSize'), $dom));
        }
		
		//Limpiar los nombres de espacios, tildes y ñ ...
		$char_no_val = array(" ", "í", "ó", "ñ", "Ñ");
		$fileNameNew = str_replace($char_no_val, "", $_FILES['Ruta']['name']);
		// Comprobar si el nombre del fichero ya existe en la carpeta. En ese caso, cambiar el nombre
		$fitxer = $fileNameNew;
        $i = 1;
		$path = pnModGetVar('Mapas', 'path'). 'temp/';
        while (file_exists($path . $fileNameNew)) {
			$fileNameNew = substr($fitxer, 0, strlen($fitxer) - strlen($file_extension) - (1)) . '(' . $i . ')' . '.' . $file_extension;
            $i++;
        }
		
		// Subir el fichero (al fin!)
        if (!FileUtil::uploadFile('Ruta', $path, $fileNameNew, true)) {
            return LogUtil::registerError(__('Failed to upload', $dom));
        }
		$fileNombreMapa = $fileNameNew;
		
		//Marcar un flag indicador de que todo ha ido bien.
		$c_process_ok = 'X';
					
	}
	
	// Nombre de la imagen superficie del mapa
    $fileImageMap = $_FILES['ImageMap']['name'];
	
	if ($fileImageMap != '') {
		// Obtener la extensión del fichero
        $file_extension = FileUtil::getExtension($fileImageMap);
        $allowedExtensions = 'jpg/JPG';
        $allowedExtensionsArray = explode('/', $allowedExtensions);
        if (!in_array(strtolower($file_extension), $allowedExtensionsArray) && !in_array(strtoupper(($file_extension)), $allowedExtensionsArray)) {
            return LogUtil::registerError(__f('The extension <strong>%s</strong> is not allowed. The allowed extensions are: <strong>%s</strong>.', array(
                $file_extension,
                str_replace('/', ', ', $allowedExtensions)), $dom));
        }
		
		//Limpiar los nombres de espacios, tildes y ñ ...
		$char_no_val = array(" ", "í", "ó", "ñ", "Ñ");
		$fileNameNew = str_replace($char_no_val, "", $_FILES['ImageMap']['name']);
		// Comprobar si el nombre del fichero ya existe en la carpeta. En ese caso, cambiar el nombre
		$fitxer = $fileNameNew;
        $i = 1;
		$path = pnModGetVar('Mapas', 'path'). 'temp/';
        while (file_exists($path . $fileNameNew)) {
			$fileNameNew = substr($fitxer, 0, strlen($fitxer) - strlen($file_extension) - (1)) . '(' . $i . ')' . '.' . $file_extension;
            $i++;
        }
		
		// Subir el fichero (al fin!)
        if (!FileUtil::uploadFile('ImageMap', $path, $fileNameNew, true)) {
            return LogUtil::registerError(__('Failed to upload', $dom));
        }
		$fileImagSup = $fileNameNew;

	}
	
	// Nombre de la imagen del subterraneo del mapa
    $fileImageSub = $_FILES['ImageSub']['name'];
	
	if ($fileImageSub != '') {
		// Obtener la extensión del fichero
        $file_extension = FileUtil::getExtension($fileImageSub);
        $allowedExtensions = 'jpg/JPG';
        $allowedExtensionsArray = explode('/', $allowedExtensions);
        if (!in_array(strtolower($file_extension), $allowedExtensionsArray) && !in_array(strtoupper(($file_extension)), $allowedExtensionsArray)) {
            return LogUtil::registerError(__f('The extension <strong>%s</strong> is not allowed. The allowed extensions are: <strong>%s</strong>.', array(
                $file_extension,
                str_replace('/', ', ', $allowedExtensions)), $dom));
        }
		
		//Limpiar los nombres de espacios, tildes y ñ ...
		$char_no_val = array(" ", "í", "ó", "ñ", "Ñ");
		$fileNameNew = str_replace($char_no_val, "", $_FILES['ImageSub']['name']);
		// Comprobar si el nombre del fichero ya existe en la carpeta. En ese caso, cambiar el nombre
		$fitxer = $fileNameNew;
        $i = 1;
		$path = pnModGetVar('Mapas', 'path'). 'temp/';
        while (file_exists($path . $fileNameNew)) {
			$fileNameNew = substr($fitxer, 0, strlen($fitxer) - strlen($file_extension) - (1)) . '(' . $i . ')' . '.' . $file_extension;
            $i++;
        }
		
		// Subir el fichero (al fin!)
        if (!FileUtil::uploadFile('ImageSub', $path, $fileNameNew, true)) {
            return LogUtil::registerError(__('Failed to upload', $dom));
        }
		$fileImagSub = $fileNameNew;

	}
	
	if ($c_process_ok == 'X'){
		//Preparar los datos para añadir el registro con el nuevo mapa
		$record['NombreMapa'] = $NombreMapa;
		$record['Autor'] = $Autor;
		$record['NombreFich'] = $fileNombreMapa;
		$record['TamanoArchivo'] = $fileSize;
		$record['FechaSubida'] = date('Y-m-d');
		$record['Usuario'] = pnUserGetVar('uid');
		$record['EstadoPruebas'] = $EstadoPruebas;
		$record['EstadoMapa'] = 'SinComprobar';
		$record['Juego'] = $Juego;
		$record['TamanoMapa'] = $TamanoMapa;
		$record['TieneSub'] = $TieneSub;
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
		$record['Superficie'] = $fileImagSup;
		$record['Subterraneo'] = $fileImagSub;
		$record['PuntuacionRevision'] = 0;
		$record['NPuntuacionRevision'] = 0;
		$record['Descargas'] = 0;

		//Añadir el registro a la base de datos
		$result = pnModAPIFunc('Mapas', 'user', 'insert', $record);
		if ($result == false)
			return LogUtil::registerError(__('Error trying to insert in DB! .', $dom));
		
		//Enviar un correo a un grupo de admins			
		if (pnModAvailable('Mailer')) 
		{
			pnModAPIFunc('Mapas', 'user', 'sendEmail', 
	  					array(	'autor'	  => $record['Usuario'],
    							'nombre'  => $record['NombreMapa'])); 
		}
		
		// Success
		/*LogUtil::registerStatus (__('Map added sucessfully.', $dom));
		return pnRedirect(pnModURL('Mapas', 'user', 'main'));*/
		
		// Construimos y devolvemos la Vista
		$render = & pnRender::getInstance('Mapas');
		//Pasamos variables a plantilla
		return $render->fetch('Mapas_user_created.htm');
	
	}

}

//Añade 1 descarga más al contador y permite bajar el fichero
function Mapas_user_downloads($arg)
{

	$mid = isset($args['mid']) ? $args['mid'] : FormUtil::getPassedValue('mid', null, 'REQUEST');
	
	//Obtener el registro solicitado
	$mapa = pnModAPIFunc('Mapas', 'user', 'get', array('ID' => $mid));
	
	if ($mapa == false)
		return LogUtil::registerError(__('Error! Map do not found.', $dom));
	
	if ($mapa['EstadoMapa'] == 'SinComprobar')
		return LogUtil::registerError(__('Error! Map do not found.', $dom));
	
	// Lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');
	
	//Sumar una descarga
	pnModAPIFunc('Mapas', 'user', 'incrementhits', array('ID' => $mid));

	// Obtener el directorio
	$path = pnModGetVar('Mapas', 'path');
	
	//fichero
	$fichero = $path . $mapa['ID'] . "/" . $mapa['NombreFich'];
	
	//Descarga	
	return pnRedirect($fichero);

}

//Función que presenta la ficha de busqueda
function Mapas_user_search()
{
	//Permisos generales
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_OVERVIEW)) {
		return LogUtil::registerPermissionError();
	}

	$dom = ZLanguage::getModuleDomain('Mapas');

	//Obtener los registros del menú correspondiente
	$menu = pnModFunc('h_menus', 'user', 'gethtml', array('codigo' => 'mapas'));

	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Mapas');
	
	$juegos = explode("/", $modvars['juegos']);
	$tamanos = explode("/", $modvars['tamanos']);
	$dificultades = explode("/", $modvars['dificultades']);
	$tipoJuegos = explode("/", $modvars['tipoJuegos']);
	
	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Mapas');
	//Pasamos variables a plantilla
	$render->assign('juegos', $juegos);
	$render->assign('tamanos', $tamanos);
	$render->assign('dificultades', $dificultades);
	$render->assign('tipoJuegos', $tipoJuegos);
	$render->assign('menu', $menu);

	return $render->fetch('Mapas_user_search.htm');

}

//Muestra los mapas encontrados segun los criterios de seleccion
function Mapas_user_found($args)
{

	//Permisos generales
	if (!SecurityUtil::checkPermission('Mapas::', '::', ACCESS_OVERVIEW)) {
		return LogUtil::registerPermissionError();
	}

	// Lenguaje
	$dom = ZLanguage::getModuleDomain('Mapas');

	extract($args);
	unset($args);

	list(	$NombreMapa,
			$Autor,
			$Juego,
			$TamanoMapa,
			$TieneSub,
			$Idioma,
			$Dificultad,
			$NHumanos,
			$NJugadores,
			$TipoJuego,
			$EstiloMapa) = pnVarCleanFromInput(
			 									'NombreMapa', 'Autor', 'Juego', 'TamanoMapa', 'TieneSub', 'Idioma', 
												'Dificultad', 'NHumanos', 'NJugadores', 'TipoJuego', 'EstiloMapa');
	
	// No html permitido para las casillas de texto
	list($NombreMapa, $Autor) = pnVarPrepForDisplay($NombreMapa, $Autor);
	
	//Obtener los parametros
	$page  = (int)FormUtil::getPassedValue('page', isset($args['page']) ? $args['page'] : 1, 'GET');
	$order = FormUtil::getPassedValue('order', isset($args['order']) ? $args['order'] : 'ID', 'POST');
	$tipoOrden = FormUtil::getPassedValue('tipoOrden', isset($args['tipoOrden']) ? $args['tipoOrden'] : 'DESC', 'POST');

	$estilos = pnModAPIFunc('Mapas', 'user', 'montarEstilo', array('Estilos' => $EstiloMapa));

	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Mapas');

	$itemsperpage = $modvars['itemsperpage'];

	// Primer elemento a obtener de la paginacion
	$startnum = (($page - 1) * $itemsperpage) + 1;

	// Procesamos los datos con los APIs necesarios
	$mapas = pnModAPIFunc('Mapas', 'user', 'getAll', 
						array(	'NombreMapa'=> $NombreMapa,
								'Autor' 	=> $Autor,
								'Juego' 	=> $Juego,
								'TamanoMapa'=> $TamanoMapa,
								'TieneSub' 	=> $TieneSub,
								'Idioma' 	=> $Idioma,
								'Dificultad'=> $Dificultad,
								'NHumanos'  => $NHumanos,
								'NJugadores'=> $NJugadores,
								'TipoJuego' => $TipoJuego,
								'EstiloMapa'=> $estilos,
								'startnum'  => $startnum,
								'numitems'  => $itemsperpage,
								'order'     => $order,
								'tipoOrden' => $tipoOrden,
							));

	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Mapas');

	//Enviarlas a la plantilla
	$render->assign('mapas', $mapas);
	
	// Asignar los valores al sistema de paginación
	$render->assign('pager', array(	'numitems' => pnModAPIFunc('Mapas', 'user', 'countitems',
															array(	'NombreMapa'    => $NombreMapa,
																	'Autor' => $Autor,
																	'Juego' => $Juego,
																	'TamanoMapa' => $TamanoMapa,
																	'TieneSub' => $TieneSub,
																	'Idioma' => $Idioma,
																	'Dificultad' => $Dificultad,
																	'NHumanos' => $NHumanos,
																	'NJugadores' => $NJugadores,
																	'TipoJuego' => $TipoJuego,
																	'EstiloMapa'=> $estilos)),
									'itemsperpage' => $itemsperpage));
	
	return $render->fetch('Mapas_user_found.htm');

}