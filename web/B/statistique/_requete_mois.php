<?php

//requête qui renvoie tout les mois contenant une commande sous la forme YYYY_MM => date_sql
function	mainQueryDateSql($connexion)	{
			return"SELECT CMD_date_confirm,DATE_FORMAT(CMD_date_confirm, '%Y-%m') AS date_sql, MONTH(CMD_date_confirm) as mois, YEAR(CMD_date_confirm) as annee 
						FROM t_commande_cmd 
						WHERE FK_ECMD_id= 6
						GROUP BY mois, annee ORDER BY annee DESC, mois DESC";
}

//requête qui retourne l'ensemble des commandes confirmées du mois concerné
function	mainQueryCommandes($connexion,	$date_sql)	{
			return	"
				SELECT CMD_ref, ADH_nom, ADH_prenom,MDPAY_nom_fr,Cotisation,Don, Evenement,Hebergement,Restauration, TotalAchats,CMD_remise, TotalRemb, NbreAchats,totalCommande,CMD_encaissement,(totalCommande-TotalRemb) AS chiffreAffaire, CMD_lien
				FROM ("	.	mainQuery($connexion,	$date_sql)	.	") AS Commande2";
}

//requête qui calcule la somme des colonnes importantes de la requete mainQuery
function	mainQuerySommeCommandes($connexion,	$date_sql)	{
			return	"SELECT count(*) AS Compteur, sum(Cotisation) As TotalCotisation,  sum(Don) As TotalDon,  sum(Evenement) As TotalEvenement,  sum(Hebergement) As TotalHebergement,  sum(Restauration) As TotalRestauration,   sum(totalAchats) As TotalSommeAchats,sum(CMD_remise) AS TotalSommeRemise, sum(totalCommande) As TotalSommePercu,sum(TotalRemb) As TotalRemboursement,sum(CMD_encaissement) TotalEncaissement,sum((totalCommande-TotalRemb)) AS TotalChiffreAffaire
				FROM ("	.	mainQuery($connexion,	$date_sql)	.	") AS Commande2";
}

//requête qui calcul le total achat et le nombre d'achat pour chaque commande et qui retourne les commandes confirmées du mois passé en paramètre
function	mainQuery($connexion,	$date_sql)	{

			// Augmente la capacité du serveur SQL car sinon la page bloque sur le serveur SQL light
			$query_config	=	"SET SQL_BIG_SELECTS=1";
			mysql_query($query_config,	$connexion)	or	die(mysql_error());

			return"SELECT *,
						IFNULL((totalAchats - CMD_remise), 0) AS totalCommande
						FROM (
								SELECT *,
								IFNULL(compteAchats1.nbreAchats1,0) As nbreAchats
								FROM t_commande_cmd AS cmd1
								LEFT JOIN (SELECT ECMD_id FROM t_etatcommande_ecmd) AS ecmd ON cmd1.FK_ECMD_id = ecmd.ECMD_id
								LEFT JOIN (SELECT MDPAY_id, MDPAY_nom_fr FROM t_modepayement_mdpay) AS mdpay ON cmd1.FK_MDPAY_id = mdpay.MDPAY_id
								LEFT JOIN (SELECT ADH_id, ADH_nom, ADH_prenom FROM t_adherent_adh) AS adh ON adh.ADH_id = cmd1.FK_ADH_id							
								LEFT JOIN
										(SELECT FK_CMD_id,
										IFNULL(ROUND(SUM((ACH_montant + ACH_surcout)*ACH_ratio/100),2),0) AS TotalAchats,
										IFNULL(SUM(ACH_remb),0) AS TotalRemb,
										Count(*) As nbreAchats1
										FROM t_achat_ach
										GROUP BY FK_CMD_id) AS compteAchats1
										ON cmd1.CMD_id = compteAchats1.FK_CMD_id											
							LEFT JOIN
									(SELECT FK_CMD_id AS FK_CMD1,
										ROUND(SUM((ACH_montant + ACH_surcout)*ACH_ratio/100),2) AS Cotisation
										FROM t_achat_ach
										WHERE FK_TYACH_id =1
										GROUP BY FK_CMD_id) AS totalCotisation
										ON cmd1.CMD_id = totalCotisation.FK_CMD1
							LEFT JOIN
									(SELECT FK_CMD_id AS FK_CMD2,
										ROUND(SUM((ACH_montant + ACH_surcout)*ACH_ratio/100),2) AS Don
										FROM t_achat_ach
										WHERE FK_TYACH_id =2
										GROUP BY FK_CMD_id) AS totalDon
										ON cmd1.CMD_id = totalDon.FK_CMD2									
							LEFT JOIN
									(SELECT FK_CMD_id AS FK_CMD3,
										ROUND(SUM((ACH_montant + ACH_surcout)*ACH_ratio/100),2) AS Evenement
										FROM t_achat_ach
										WHERE FK_TYACH_id =3
										GROUP BY FK_CMD_id) AS totalEvenement
										ON cmd1.CMD_id = totalEvenement.FK_CMD3										
							LEFT JOIN
									(SELECT FK_CMD_id AS FK_CMD4,
										ROUND(SUM((ACH_montant + ACH_surcout)*ACH_ratio/100),2) AS Hebergement
										FROM t_achat_ach
										WHERE FK_TYACH_id =4
										GROUP BY FK_CMD_id) AS totalHebergement
										ON cmd1.CMD_id = totalHebergement.FK_CMD4										
						LEFT JOIN
									(SELECT FK_CMD_id AS FK_CMD5,
										ROUND(SUM((ACH_montant + ACH_surcout)*ACH_ratio/100),2) AS Restauration
										FROM t_achat_ach
										WHERE FK_TYACH_id =5
										GROUP BY FK_CMD_id) AS totalRestauration
										ON cmd1.CMD_id = totalRestauration.FK_CMD5
			WHERE cmd1.CMD_date_confirm LIKE '%$date_sql%' AND cmd1.FK_ECMD_id= 6 
	) AS Commande";
}

?>
