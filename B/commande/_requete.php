<?php

// la vue v2_commande est nécessaire pour faire fonctionner les filtres sur totalCommande
function	mainQueryCmd($connexion)	{

		// Augmente la capacité du serveur SQL car sinon la page bloque sur le serveur SQL light
		$query_config	=	"SET SQL_BIG_SELECTS=1";
		mysql_query($query_config,	$connexion)	or	die(mysql_error());

		return	"
				SELECT * FROM (
						SELECT *,
						IFNULL((totalAchats - CMD_remise), 0) AS totalCommande
						FROM (
								SELECT *,
								IFNULL(compteAchats.nbreAchats1,0) As nbreAchats
								FROM t_commande_cmd
								LEFT JOIN t_etatcommande_ecmd ON FK_ECMD_id = ECMD_id
								LEFT JOIN t_modepayement_mdpay ON FK_MDPAY_id = MDPAY_id
								LEFT JOIN t_adherent_adh AS t_adh_cmd ON t_adh_cmd.ADH_id = t_commande_cmd.FK_ADH_id
								LEFT JOIN t_pays_pays AS t_pays_cmd ON t_pays_cmd.PAYS_id = t_adh_cmd.FK_PAYS_id
								LEFT JOIN
										(SELECT FK_CMD_id,
										IFNULL(ROUND(SUM((ACH_montant + ACH_surcout)*ACH_ratio/100),2),0) AS totalAchats,
										IFNULL(SUM(ACH_remb),0) AS totalRemb,
										Count(*) As nbreAchats1
										FROM t_achat_ach
										GROUP BY FK_CMD_id) AS compteAchats
								ON t_commande_cmd.CMD_id = compteAchats.FK_CMD_id
							) AS v1_commande
					) AS v2_commande ";
}

// ADD
// Ne prends que les états assignables
function	listeEtatsQuery()	{
		return	"SELECT * FROM t_etatcommande_ecmd ORDER BY ECMD_ordre ASC "; //
}

function	listeModePayeQuery()	{
		return	"SELECT * FROM t_modepayement_mdpay ORDER BY MDPAY_ordre ASC ";
}
?>