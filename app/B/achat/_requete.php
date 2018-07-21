<?php

function	mainQueryAch($idCmd,	$connexion)	{

		// Augmente la capacité du serveur SQL car sinon la page bloque sur le serveur SQL light
		$query_config	=	"SET SQL_BIG_SELECTS=1";
		mysql_query($query_config,	$connexion)	or	die(mysql_error());

		return	"
				SELECT *,

				# calcul du nombre d'achats pour les jours évenements
				(SELECT Count(*)
				FROM t_achat_ach AS achat
					INNER JOIN (
						SELECT CMD_id, FK_ECMD_id
						FROM t_commande_cmd
						WHERE FK_ECMD_id = 6) AS confirmed_cmd
					ON achat.FK_CMD_id = confirmed_cmd.CMD_id
				WHERE FK_TYACH_id = 3
				AND ACH_participe = 1
				AND FK_JREVEN_id = v_grp_achat.FK_JREVEN_id
				GROUP BY FK_JREVEN_id) As nbreAchatsJREVEN,

				# calcul du nombre d'achats pour les hébergements
				(SELECT Count(*)
				FROM t_achat_ach AS achat
					INNER JOIN (
						SELECT CMD_id, FK_ECMD_id
						FROM t_commande_cmd
						WHERE FK_ECMD_id = 6) AS confirmed_cmd
					ON achat.FK_CMD_id = confirmed_cmd.CMD_id
				WHERE FK_TYACH_id = 4
				AND ACH_participe = 1
				AND FK_JREVEN_id = v_grp_achat.FK_JREVEN_id
				AND FK_TYHEB_id = v_grp_achat.FK_TYHEB_id
				GROUP BY FK_TYHEB_id) As nbreAchatsTYHEB,

				# calcul du nombre d'achats pour les restaurations
				(SELECT Count(*)
				FROM t_achat_ach AS achat
					INNER JOIN (
						SELECT CMD_id, FK_ECMD_id
						FROM t_commande_cmd
						WHERE FK_ECMD_id = 6) AS confirmed_cmd
					ON achat.FK_CMD_id = confirmed_cmd.CMD_id
				WHERE FK_TYACH_id = 5
				AND ACH_participe = 1
				AND FK_JREVEN_id = v_grp_achat.FK_JREVEN_id
				AND FK_TYRES_id = v_grp_achat.FK_TYRES_id
				GROUP BY FK_TYRES_id) As nbreAchatsTYRES

				FROM

				(SELECT *

				FROM t_achat_ach AS ach
				LEFT JOIN t_typeachat_tyach AS tyach ON tyach.TYACH_id = ach.FK_TYACH_id

				LEFT JOIN t_typedon_tydon AS tydon ON tydon.TYDON_id = ach.FK_TYDON_id
				LEFT JOIN t_typecotisation_tycot AS tycot ON tycot.TYCOT_id = ach.FK_TYCOT_id

				#informations sur les jours événements
				LEFT JOIN t_jourevent_jreven AS jreven ON jreven.JREVEN_id = ach.FK_JREVEN_id
				LEFT JOIN (SELECT EJREVEN_id, EJREVEN_nom_fr FROM t_etatjourevent_ejreven) AS eeven ON jreven.FK_EJREVEN_id=EJREVEN_id
				LEFT JOIN (SELECT LEVEN_id, LEVEN_nom, FK_PAYS_id FROM t_lieuevent_leven) AS leven ON jreven.FK_LEVEN_id=leven.LEVEN_id
				LEFT JOIN t_pays_pays AS pays ON pays.PAYS_id=leven.FK_PAYS_id

				# informations sur les hébergements
				LEFT JOIN (SELECT TYHEB_id, TYHEB_ordre, TYHEB_nom_fr, TYHEB_nom_en FROM t_typehebergement_tyheb) AS tyheb  ON tyheb.TYHEB_id = ach.FK_TYHEB_id
				LEFT JOIN (SELECT TJ_TYHEB_id, TJ_JREVEN_id AS TJ_JREVEN_id_HEB, TYHEB_JREVEN_capacite FROM tj_tyheb_jreven) AS tyheb_jointure ON tyheb_jointure.TJ_TYHEB_id = ach.FK_TYHEB_id AND tyheb_jointure.TJ_JREVEN_id_HEB = ach.FK_JREVEN_id

				# informations sur les restaurations
				LEFT JOIN (SELECT TYRES_id, TYRES_ordre, TYRES_nom_fr, TYRES_nom_en FROM t_typerestauration_tyres) AS tyres ON tyres.TYRES_id = ach.FK_TYRES_id
				LEFT JOIN (SELECT TJ_TYRES_id, TJ_JREVEN_id AS TJ_JREVEN_id_RES, TYRES_JREVEN_capacite FROM tj_tyres_jreven) AS tyres_jointure ON tyres_jointure.TJ_TYRES_id = ach.FK_TYRES_id AND tyres_jointure.TJ_JREVEN_id_RES = ach.FK_JREVEN_id

				# informations sur l'adhérent
				LEFT JOIN (SELECT ADH_id, FK_TYTAR_id, ADH_nom, ADH_prenom, ADH_annee_cotisation FROM t_adherent_adh) AS t_adh_ach ON t_adh_ach.ADH_id=ach.FK_ADH_id

				# informations sur l'événement
				LEFT JOIN (SELECT EVEN_id, EVEN_nom_fr, EVEN_nom_en, EVEN_pleintarif FROM t_evenement_even) AS even ON even.EVEN_id = jreven.FK_EVEN_id

				WHERE ach.FK_CMD_id = "	.	$idCmd	.	" ) as v_grp_achat ";
}

function	paypalAch($idCmd)	{

		return	"
				SELECT ADH_id, ADH_nom, ADH_prenom, count(*) as nbreAchat
				FROM t_achat_ach
				LEFT JOIN (SELECT ADH_id, ADH_nom, ADH_prenom FROM t_adherent_adh) AS adherent ON FK_ADH_id = ADH_id
				WHERE FK_CMD_id = "	.	$idCmd	.	"
				GROUP BY FK_ADH_id
				ORDER BY ADH_nom, ADH_prenom";
}

?>