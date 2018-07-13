<?php
include('../_shared_action.php');
require_once('../../librairie/php/code_adn/CreerFiltre.php');

if ((!isset($_SESSION['user_info'])) || !in_array($_SESSION['user_info'][1], array('admin', 'adher'))) {
    $_SESSION['message_user'] = "acces_ko";
    adn_myRedirection('../login/menu.php');
}
?>
<?php
// Desactive le formatage HTML de Xdebug pour pouvoir lire les erreurs dans le fichier .xls
ini_set('html_errors', 0);

/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2011 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.6, 2011-02-27
 */




/** Error reporting */
error_reporting(E_ALL);

/** PHPExcel */
include '../../librairie/php/phpexcel/PHPExcel.php';
include '../../librairie/php/phpexcel/PHPExcel/Writer/Excel5.php';

/*$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_memcache;
$cacheSettings = array( 'memcacheServer'  => '127.0.0.1',
                        'memcachePort'    => 11211,
                        'cacheTime'       => 600
                      );
if (!PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings))
   die('CACHEING ERROR');*/

// Requetes
include("_requete.php");
if (isset($_SESSION['FiltreADH']['TJ_CMPT_id'])) {
    $query = adn_creerExists($query, 'tj_adh_cmpt', 'TJ_ADH_id = ADH_id', 'TJ_CMPT_id', $_SESSION['FiltreADH']['TJ_CMPT_id']);
}
$query_RS = adn_creerFiltre($query, 'FiltreADH', array('check_withcmt', 'select_tri', 'submit', 'chemin_retour', 'TJ_CMPT_id'), array('FK_CMTADH_id'), NULL, 'ADH_id DESC');
$query_RS .= " LIMIT 0,500";
$result = mysql_query($query_RS, $connexion) or die(mysql_error());


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Mise en place des titres
$row = 1;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".$row, "Inscription")
            ->setCellValue("C".$row, "Informations Personnelles")
            ->setCellValue("J".$row, "Communication")
            ->setCellValue("N".$row, "Adresse")
            ->setCellValue("S".$row, "Sangha")
            ->setCellValue("U".$row, "Benevolat");

$row++;

// mise en place des sous titres
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".$row, "Etat")
            ->setCellValue("B".$row, "Cotisation")
            ->setCellValue("C".$row, "Nom")
            ->setCellValue("D".$row, "Prénom")
            ->setCellValue("E".$row, "Identifiant")
            ->setCellValue("F".$row, "Genre")
            ->setCellValue("G".$row, "Année")
            ->setCellValue("H".$row, "Langue")
            ->setCellValue("I".$row, "Type tarif")
            ->setCellValue("J".$row, "Email")
            ->setCellValue("K".$row, "Newsletter")
            ->setCellValue("L".$row, "Télephone")
            ->setCellValue("M".$row, "Portable")
            ->setCellValue("N".$row, "Adresse 1")
            ->setCellValue("O".$row, "Adresse 2")
            ->setCellValue("P".$row, "Zip")
            ->setCellValue("Q".$row, "Ville")
            ->setCellValue("R".$row, "Pays")
            ->setCellValue("S".$row, "Ordination")
            ->setCellValue("T".$row, "Nom de Dharma")
            ->setCellValue("U".$row, "Bénevolat")
            ->setCellValue("V".$row, "Profession")
            ->setCellValue("W".$row, "Disponibilités")
            ->setCellValue("X".$row, "Compétences");
$row++;
while ($row = mysql_fetch_object($result)){

    // Ajout des données
    if($row->ADH_ordination){
        $ordination = "x";
    } else {
        $ordination = "";
    }

    if($row->ADH_benevolat){
        $benevolat = "x";
    } else {
        $benevolat = "";
    }

     if($row->MAIL_newsletter){
        $news = "x";
    } else {
        $news = "";
    }

    $queryCmpt = "SELECT CMPT_nom_fr FROM t_competence_cmpt LEFT JOIN tj_adh_cmpt ON TJ_CMPT_id=CMPT_id
        WHERE TJ_ADH_id=$row->ADH_id";
    $res = mysql_query($queryCmpt, $connexion) or die(mysql_error());
    $strCmpt = "";
    while($cmpt = mysql_fetch_object($res)){
        $strCmpt .= $cmpt->CMPT_nom_fr ." / ";
    }
    rtrim($strCmpt, " / ");

    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A".$row, $row->ADH_etat)
                ->setCellValue("B".$row, $row->ADH_annee_cotisation)
                ->setCellValue("C".$row, $row->ADH_nom)
                ->setCellValue("D".$row, $row->ADH_prenom)
                ->setCellValue("E".$row, $row->ADH_identifiant)
                ->setCellValue("F".$row, $row->ADH_genre)
                ->setCellValueExplicit("G".$row, $row->ADH_annee_naissance, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue("H".$row, $row->MAIL_langue)
                ->setCellValue("I".$row, $row->TYTAR_nom_fr)
                ->setCellValue("J".$row, $row->MAIL_email)
                ->setCellValue("K".$row, $news)
                ->setCellValueExplicit("L".$row, $row->ADH_telephone, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("M".$row, $row->ADH_portable, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue("N".$row, $row->ADH_adresse1)
                ->setCellValue("O".$row, $row->ADH_adresse2)
                ->setCellValueExplicit("P".$row, $row->ADH_zip, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue("Q".$row, $row->ADH_ville)
                ->setCellValue("R".$row, $row->PAYS_nom_fr)
                ->setCellValue("S".$row, $ordination)
                ->setCellValue("T".$row, $row->ADH_nom_dharma)
                ->setCellValue("U".$row, $benevolat)
                ->setCellValue("V".$row, $row->ADH_profession)
                ->setCellValue("W".$row, $row->ADH_disponibilite)
                ->setCellValue("X".$row, $strCmpt);

    // Cas pour les commentaires
    if (isSet($_GET['com']) && $_GET['com'] == "1" && $row->FK_CMTADH_id != 0) {
	$text = "Commentaire : ";
	$query = "SELECT CMT_commentaire FROM t_commentaire_cmt WHERE CMT_id = $row->FK_CMTADH_id";
	$rs = mysql_query($query, $connexion) or die(mysql_error());
	$res = mysql_fetch_object($rs);
	$text .= $res->CMT_commentaire;
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A" . $row, $text);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells("A" . $row . ":Z" . $row);

	$objPHPExcel->getActiveSheet()->getStyle("A" . $row)->applyFromArray(array(
	    'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'color' => array('argb' => 'FFEEEEEE')
	    ),
	    'font' => array(
		'italic' => true
		)));
    }
    $row++;
}
// Autosize des colonnes
$objPHPExcel->getActiveSheet()->setTitle($nom);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);

// On merge les cellules
$row = 1;
$objPHPExcel->setActiveSheetIndex(0)
            ->mergeCells("A".$row.":B".$row)
            ->mergeCells("C".$row.":I".$row)
            ->mergeCells("J".$row.":M".$row)
            ->mergeCells("N".$row.":R".$row)
            ->mergeCells("S".$row.":T".$row)
            ->mergeCells("U".$row.":X".$row);

// Mise en place du freezePane
$objPHPExcel->getActiveSheet()->freezePane('A3');

// Définition de la mise en forme
$objPHPExcel->getActiveSheet()->getStyle('A1:X2')->applyFromArray(array(
    'fill'=>array(
        'type'=> PHPExcel_Style_Fill::FILL_SOLID,
        'color'=> array('argb'=>'FFCCCCCC')
    )));


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Export_'.$nom.'_'.date("Ymd_Hi").'.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>
