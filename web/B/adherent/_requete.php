<?php

function	mainQueryAdherent()	{
			return	"
					SELECT *,
						IFNULL(v_adherent1.SousNbreCommandes,0) As NbrCommande
						FROM
						(SELECT *,
						(SELECT TYTAR_nom_fr FROM t_typetarif_tytar WHERE TYTAR_id = FK_TYTAR_id) AS TYTAR_nom_fr,
						(SELECT TYTAR_ratio FROM t_typetarif_tytar WHERE TYTAR_id = FK_TYTAR_id) AS TYTAR_ratio,
						(SELECT PAYS_nom_fr FROM t_pays_pays WHERE PAYS_id = FK_PAYS_id) AS PAYS_nom_fr
						FROM t_adherent_adh AS adherent
						LEFT JOIN t_newsletter_news ON NEWS_email=ADH_email
						LEFT JOIN t_etatadherent_eadh ON EADH_id=FK_EADH_id
						LEFT JOIN tj_adh_cmpt ON ADH_id = TJ_ADH_id
						LEFT JOIN (
							SELECT FK_ADH_id,
							Count(*) AS SousNbreCommandes
							FROM t_commande_cmd
							GROUP BY FK_ADH_id) AS compte_cmd
						ON adherent.ADH_id = compte_cmd.FK_ADH_id
						) as v_adherent1 ";
}

function listeAdherentsQuery() {
			return "SELECT ADH_nom, ADH_prenom, ADH_identifiant FROM t_adherent_adh ORDER BY ADH_nom, ADH_prenom";
}

// RESULT +
// DOUBLON -> _fonction/ajax_getInfoAdherent.php
function getCompetenceAdherents($id) {
			return "
			SELECT * FROM tj_adh_cmpt
			LEFT JOIN t_competence_cmpt ON TJ_CMPT_id=CMPT_id
			WHERE TJ_ADH_id = '$id'
			ORDER BY CMPT_nom_fr";
}

?>
