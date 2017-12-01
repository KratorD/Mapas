<?php
/**
 * Mapas Module for Zikula
 *
 * @copyright (c) 2011, Krator
 * @link http://www.heroesofmightandmagic.es
 * @version $Id: pntables.php 2011-10-17 $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*/

/**
 * Populate pntables array
 *
 * @author Krator
 * @return array pntables array
 */
function Mapas_pntables()
{
    $pntable = array();

    // Tabla que contiene todos los mapas de HOMM
    $pntable['Mapas'] = DBUtil::getLimitedTablename('Mapas');
    $pntable['Mapas_column'] = array(	'ID'   	 		=> 'ID',
										'NombreMapa'	=> 'NombreMapa',
										'Autor'    		=> 'Autor',
										'NombreFich'   	=> 'NombreFich',
										'TamanoArchivo'	=> 'TamanoArchivo',
										'FechaSubida'	=> 'FechaSubida',
										'Usuario'		=> 'Usuario',
										'EstadoPruebas'	=> 'EstadoPruebas',
										'EstadoMapa'	=> 'EstadoMapa',
										'Juego'  		=> 'Juego',
										'TamanoMapa'  	=> 'TamanoMapa',
										'TieneSub'  	=> 'TieneSub',
										'Idioma'  		=> 'Idioma',
										'EmailAutor'  	=> 'EmailAutor',
										'FechaCreacion' => 'FechaCreacion',
										'Version'  		=> 'Version',
										'Dificultad'  	=> 'Dificultad',
										'NHumanos'  	=> 'NHumanos',
										'NJugadores'  	=> 'NJugadores',
										'TipoJuego'  	=> 'TipoJuego',
										'EstiloMapa'  	=> 'EstiloMapa',
										'Descripcion'  	=> 'Descripcion',
										'Superficie'  	=> 'Superficie',
										'Subterraneo'  	=> 'Subterraneo',
										'PuntuacionRevision'  	=> 'PuntuacionRevision',
										'NPuntuacionRevision'  	=> 'NPuntuacionRevision',
										'Descargas'  	=> 'Descargas'
										);
	//http://community.zikula.org/index.php?module=Wiki&tag=ADOdbDataDictionary
    $pntable['Mapas_column_def'] = array(	'ID'   	 		=> "I NOTNULL AUTO PRIMARY",
											'NombreMapa'  	=> "C(255) NOTNULL",
											'Autor'  		=> "C(255) NOTNULL",
											'NombreFich'  	=> "C(255) NOTNULL",
											'TamanoArchivo'	=> "I NOTNULL",
											'FechaSubida' 	=> "D NOTNULL",
											'Usuario' 		=> "I NOTNULL",
											'EstadoPruebas' => "C(20) NOTNULL",
											'EstadoMapa'	=> "C(15) NOTNULL",
											'Juego'			=> "C(10) NOTNULL", 
											'TamanoMapa'  	=> "C(15) NOTNULL",
											'TieneSub'  	=> "L NOTNULL",
											'Idioma'  		=> "C(8) NOTNULL",
											'EmailAutor'  	=> "C(255) NULL",
											'FechaCreacion' => "D NULL",
											'Version'  		=> "C(5) NULL",
											'Dificultad'  	=> "C(10) NULL",
											'NHumanos'  	=> "C(1) NULL",
											'NJugadores'  	=> "C(1) NULL",
											'TipoJuego'  	=> "C(34) NULL",
											'EstiloMapa'  	=> "C(235) NULL",
											'Descripcion'  	=> "X NULL",
											'Superficie'  	=> "C(255) NULL",
											'Subterraneo'  	=> "C(255) NULL",
											'PuntuacionRevision' 	=> "I NULL",
											'NPuntuacionRevision'	=> "I NULL",
											'Descargas'  	=> "I NULL"
										    );
    $pntable['Mapas_column_idx'] = array('ID' => 'ID');
	
	// add standard data fields
    ObjectUtil::addStandardFieldsToTableDefinition ($pntable['Mapas_column']);
    ObjectUtil::addStandardFieldsToTableDataDefinition($pntable['Mapas_column_def']);
	
	return $pntable;
}
