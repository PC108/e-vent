<?php
//Requête qui retourne l'événement et le nombre de jours événements qu'il contient.
function	QueryEvent($connexion)	{
			return"SELECT EVEN_id, EVEN_nom_fr,
	IFNULL(evenement.compte_jour,0) As NbreJour
	FROM
	(SELECT *
	FROM t_evenement_even AS even	
	# Tous les jours événements
	LEFT JOIN
	    (SELECT FK_EVEN_id,
	    Count(*) AS compte_jour
	    FROM t_jourevent_jreven
	    Group By FK_EVEN_id) AS CompteJour
	ON even.EVEN_id = CompteJour.FK_EVEN_id	
) AS evenement ORDER BY EVEN_id DESC";
}

//Requête pour récupérer le nom de l'événement par rapport au even_id
function QueryNomEvent($connexion,$even_id){
			return"SELECT EVEN_id, EVEN_nom_fr 
FROM t_evenement_even
WHERE EVEN_id=$even_id";
}

//Requête pour le calcul de la somme des jours événements et des options (hébergements,restaurations) ainsi que le chiffre d'affaire pour un événement.
function QuerySommeJourEvent($connexion,$even_id){
			return"SELECT SUM(TotalAchatsEvent) AS Somme_Event,SUM(nbreAchatsEvent)AS Nbre_All_Event,SUM(TotalAchatsHeber) AS Somme_Heber,SUM(nbreAchatsHeber)AS Nbre_All_Heber,SUM(TotalAchatsResto) AS Somme_Resto, SUM(nbreAchatsResto)AS Nbre_All_Resto,SUM(CAJourEvent) AS CAEvent
FROM(" .mainQueryJourEvent($connexion,$even_id).") AS JourEventSomme";
}

//Requête principale pour le calcul des achats par rapport à un jour événement, hébergement et restauration ainsi que le chiffre d'affaire. 
function mainQueryJourEvent($connexion,$even_id){
			// Augmente la capacité du serveur SQL car sinon la page bloque sur le serveur SQL light
			$query_config	=	"SET SQL_BIG_SELECTS=1";
			mysql_query($query_config,	$connexion)	or	die(mysql_error());
			
			return"SELECT JREVEN_id,FK_EVEN_id,JREVEN_date_debut,LEVEN_nom, DATE_FORMAT(JREVEN_date_debut, '%d/%m/%Y') AS date_jevent,
		TotalAchatsEvent,IFNULL((JourEvent.SousNbreAchatsEvent), 0) AS nbreAchatsEvent,
		TotalAchatsHeber,IFNULL((JourEvent.SousNbreAchatsHeber), 0) AS nbreAchatsHeber,
		TotalAchatsResto,IFNULL((JourEvent.SousNbreAchatsResto), 0) AS nbreAchatsResto,
		(IFNULL(TotalAchatsEvent,0)+IFNULL(TotalAchatsHeber,0)+IFNULL(TotalAchatsResto,0))AS CAJourEvent
		FROM (
			SELECT *
			FROM t_jourevent_jreven AS jreven
			LEFT JOIN (SELECT LEVEN_id, LEVEN_nom FROM t_lieuevent_leven) AS leven ON FK_LEVEN_id=LEVEN_id
			
				############## Evenement ################
				LEFT JOIN (
					SELECT FK_CMD_id, FK_JREVEN_id,
					ROUND(SUM((ACH_montant + ACH_surcout)*ACH_ratio/100),2) AS TotalAchatsEvent,
					Count(*) As SousNbreAchatsEvent 
					FROM t_achat_ach AS achat
						INNER JOIN (
						SELECT CMD_id, FK_ECMD_id
						FROM t_commande_cmd
						WHERE FK_ECMD_id = 6) AS confirmed_cmd
						ON achat.FK_CMD_id = confirmed_cmd.CMD_id
					WHERE FK_TYACH_id = 3
					GROUP BY FK_JREVEN_id) AS compteAchatsEvent
				ON jreven.JREVEN_id = compteAchatsEvent.FK_JREVEN_id
				
				############## Hebergement ################	
				LEFT JOIN (
					SELECT FK_CMD_id AS FK_CMD_id_heber , FK_JREVEN_id AS FK_JREVEN_id_heber,
					ROUND(SUM((ACH_montant + ACH_surcout)*ACH_ratio/100),2) AS TotalAchatsHeber,
					Count(*) As SousNbreAchatsHeber 
					FROM t_achat_ach AS achat
						INNER JOIN (
						SELECT CMD_id, FK_ECMD_id
						FROM t_commande_cmd
						WHERE FK_ECMD_id = 6) AS confirmed_cmd
						ON achat.FK_CMD_id = confirmed_cmd.CMD_id
					WHERE FK_TYACH_id = 4
					GROUP BY FK_JREVEN_id) AS compteAchatsHeber
				ON jreven.JREVEN_id = compteAchatsHeber.FK_JREVEN_id_heber
				
				############## Restauration ################
				LEFT JOIN (
					SELECT FK_CMD_id AS FK_CMD_id_resto , FK_JREVEN_id AS FK_JREVEN_id_resto,
					ROUND(SUM((ACH_montant + ACH_surcout)*ACH_ratio/100),2) AS TotalAchatsResto,
					Count(*) As SousNbreAchatsResto 
					FROM t_achat_ach AS achat
						INNER JOIN (
						SELECT CMD_id, FK_ECMD_id
						FROM t_commande_cmd
						WHERE FK_ECMD_id = 6) AS confirmed_cmd
						ON achat.FK_CMD_id = confirmed_cmd.CMD_id
					WHERE FK_TYACH_id = 5
					GROUP BY FK_JREVEN_id) AS compteAchatsResto
				ON jreven.JREVEN_id = compteAchatsResto.FK_JREVEN_id_resto

				WHERE jreven.FK_EVEN_id = $even_id
				GROUP BY JREVEN_id
		ORDER BY jreven.JREVEN_date_debut ASC) AS JourEvent";
}


?>
