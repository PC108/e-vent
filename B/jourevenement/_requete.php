<?php

// RESULT /////////////////////////////////////////////////////////////
// Voir adn/sql/BO_jreven_solo1.sql
function	queryJourEvent($idEvent,	$connexion)	{

			// Augmente la capacité du serveur SQL car sinon la page bloque sur le serveur SQL light
			$query_config	=	"SET SQL_BIG_SELECTS=1";
			mysql_query($query_config,	$connexion)	or	die(mysql_error());

			return	"
		SELECT *,
		IFNULL((v_jourEvent.SousNbreAchats), 0) AS nbreAchats
		FROM (
			SELECT *,
			(SELECT min(TJ_TYHEB_id) FROM tj_tyheb_jreven WHERE TJ_JREVEN_id=JREVEN_id) AS isHeber,
			(SELECT min(TJ_TYRES_id) FROM tj_tyres_jreven WHERE TJ_JREVEN_id=JREVEN_id) AS isResto
			FROM t_jourevent_jreven AS jreven
				LEFT JOIN (SELECT LEVEN_id, LEVEN_nom FROM t_lieuevent_leven) AS leven ON FK_LEVEN_id=LEVEN_id
				LEFT JOIN t_etatjourevent_ejreven as ejreven ON FK_EJREVEN_id = EJREVEN_id
				LEFT JOIN (
					SELECT FK_CMD_id, FK_JREVEN_id, Count(*) As SousNbreAchats 
					FROM t_achat_ach AS achat
						INNER JOIN (
						SELECT CMD_id, FK_ECMD_id
						FROM t_commande_cmd
						WHERE FK_ECMD_id = 6) AS confirmed_cmd
						ON achat.FK_CMD_id = confirmed_cmd.CMD_id
					WHERE FK_TYACH_id = 3
					AND ACH_participe = 1
					GROUP BY FK_JREVEN_id) AS compteAchats
				ON jreven.JREVEN_id = compteAchats.FK_JREVEN_id
				WHERE FK_EVEN_id = $idEvent
		ORDER BY jreven.JREVEN_date_debut ASC) AS v_jourEvent ";
}

// Voir adn/sql/BO_heber_solo1.sql
function	queryOptionHebergement($idJrEvent,	$connexion)	{

			// Augmente la capacité du serveur SQL car sinon la page bloque sur le serveur SQL light
			$query_config	=	"SET SQL_BIG_SELECTS=1";
			mysql_query($query_config,	$connexion)	or	die(mysql_error());

			return	"
		SELECT *,
		IFNULL((v_hebergement.SousNbreAchats), 0) AS nbreAchats
		FROM (
			SELECT * FROM t_typehebergement_tyheb AS heber
				LEFT JOIN tj_tyheb_jreven AS heber_jointure ON TJ_TYHEB_id = TYHEB_id 
				LEFT JOIN (
					SELECT FK_TYHEB_id, Count(*) As SousNbreAchats 
					FROM t_achat_ach AS achat
						INNER JOIN (
							SELECT CMD_id, FK_ECMD_id
							FROM t_commande_cmd
							WHERE FK_ECMD_id = 6) AS confirmed_cmd
						ON achat.FK_CMD_id = confirmed_cmd.CMD_id
					WHERE FK_TYACH_id = 4
					AND ACH_participe = 1
					AND  FK_JREVEN_id = $idJrEvent
					GROUP BY FK_TYHEB_id) AS compteAchats
				ON heber_jointure.TJ_TYHEB_id = compteAchats.FK_TYHEB_id
			WHERE TJ_JREVEN_id = $idJrEvent
		ORDER BY heber.TYHEB_ordre ASC) AS v_hebergement ";
}

// Voir adn/sql/BO_resto_solo1.sql
function	queryOptionRestauration($idJrEvent,	$connexion)	{

			// Augmente la capacité du serveur SQL car sinon la page bloque sur le serveur SQL light
			$query_config	=	"SET SQL_BIG_SELECTS=1";
			mysql_query($query_config,	$connexion)	or	die(mysql_error());

			return	"
		SELECT *,
		IFNULL((v_restauration.SousNbreAchats), 0) AS nbreAchats
		FROM (
			SELECT * FROM t_typerestauration_tyres AS resto
				LEFT JOIN tj_tyres_jreven AS resto_jointure ON TYRES_id = TJ_TYRES_id
				LEFT JOIN (
					SELECT FK_TYRES_id, Count(*) As SousNbreAchats 
					FROM t_achat_ach AS achat
						INNER JOIN (
							SELECT CMD_id, FK_ECMD_id
							FROM t_commande_cmd
							WHERE FK_ECMD_id = 6) AS confirmed_cmd
						ON achat.FK_CMD_id = confirmed_cmd.CMD_id
					WHERE FK_TYACH_id = 5
					AND ACH_participe = 1
					AND  FK_JREVEN_id = $idJrEvent
					GROUP BY FK_TYRES_id) AS compteAchats
				ON resto_jointure.TJ_TYRES_id = compteAchats.FK_TYRES_id
			WHERE TJ_JREVEN_id = $idJrEvent
		ORDER BY resto.TYRES_ordre ASC) AS v_restauration ";
}

// A cause du NULL, récupère les paramêtres sous forme de conditions (chaines de caractères)
// par exemple " = 14" ou " IS NULL"
function	queryInscrits($condition_JREVENT,	$condition_TYHEB,	$condition_TYRES, $connexion)	{

			// Augmente la capacité du serveur SQL car sinon la page bloque sur le serveur SQL light
			$query_config	=	"SET SQL_BIG_SELECTS=1";
			mysql_query($query_config,	$connexion)	or	die(mysql_error());

			return	"
				SELECT *
				FROM t_achat_ach AS achat
				LEFT JOIN t_adherent_adh AS adh ON FK_ADH_id=ADH_id
				LEFT JOIN t_newsletter_news ON NEWS_email=ADH_email
				LEFT JOIN (
					SELECT TYTAR_id, TYTAR_nom_fr, TYTAR_ratio 
					FROM t_typetarif_tytar) AS typeTarif
				ON adh.FK_TYTAR_id = typeTarif.TYTAR_id
				LEFT JOIN (
					SELECT PAYS_id, PAYS_nom_fr
					FROM t_pays_pays) AS pays
				ON adh.FK_PAYS_id = pays.PAYS_id
				INNER JOIN (
					SELECT CMD_id, FK_ECMD_id
					FROM t_commande_cmd
					WHERE FK_ECMD_id = 6) AS confirmed_cmd
				ON achat.FK_CMD_id = confirmed_cmd.CMD_id
				WHERE ACH_participe = 1
				AND FK_JREVEN_id $condition_JREVENT
				AND FK_TYHEB_id $condition_TYHEB
				AND FK_TYRES_id $condition_TYRES
				ORDER BY ADH_nom, ADH_prenom DESC
		 ";
}

// ADD /////////////////////////////////////////////////////////////
function	listeEtatJreven()	{
			return
											"SELECT *
            FROM t_etatjourevent_ejreven
            ORDER BY EJREVEN_ordre ASC ";
}

function	listeLieuxJreven()	{
			return
											"SELECT *
            FROM t_lieuevent_leven
            ORDER BY LEVEN_nom ASC ";
}

function	listeHebJreven()	{
			return
											"SELECT TYHEB_id, TYHEB_nom_fr, TYHEB_montant_defaut, TYHEB_capacite_defaut
            FROM t_typehebergement_tyheb
            ORDER BY TYHEB_nom_fr ASC";
}

function	listeResJreven()	{
			return
											"SELECT TYRES_id, TYRES_nom_fr, TYRES_montant_defaut, TYRES_capacite_defaut
            FROM t_typerestauration_tyres";
}

function	nomEvenJreven($idEven)	{
			return	"
        SELECT EVEN_nom_fr
            FROM  t_evenement_even
            WHERE EVEN_id=$idEven  ";
}

function	infoEvenJreven($id)	{
			return	"
        SELECT *
            FROM t_jourevent_jreven
            LEFT JOIN t_etatjourevent_ejreven ON
                FK_EJREVEN_id=EJREVEN_id
            WHERE JREVEN_id=$id ";
}

?>
