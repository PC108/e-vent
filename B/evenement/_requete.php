<?php

// RESULT /////////////////////////////////////////////////////////////
function	mainQueryEvent($connexion)	{

		// Augmente la capacité du serveur SQL car sinon la page bloque sur le serveur SQL light
		$query_config	=	"SET SQL_BIG_SELECTS=1";
		mysql_query($query_config,	$connexion)	or	die(mysql_error());
		
		return	"
SELECT *,
	IFNULL(v_evenement.compte_desactive,0) As NbreDesactive,
	IFNULL(v_evenement.compte_annule,0) As NbreAnnule,
	IFNULL(v_evenement.compte_envente,0) As NbreEnVente,
	IFNULL(v_evenement.compte_depasse,0) As NbreDepasse,
	IFNULL(v_evenement.compte_all,0) As NbreAll,
	IFNULL(v_evenement.compte_display,0) As NbreDisplay
	FROM
	(SELECT *,
	(SELECT min(JREVEN_date_debut) FROM t_jourevent_jreven WHERE FK_EVEN_id=EVEN_id) AS minDate,
	(SELECT max(JREVEN_date_fin) FROM t_jourevent_jreven WHERE FK_EVEN_id=EVEN_id) AS maxDate
	FROM t_evenement_even AS even
	
	# Decompte des jours événements désactivés
	LEFT JOIN
	    (SELECT FK_EVEN_id AS evenid_1,
	    Count(*) AS compte_desactive,
	    EJREVEN_nom_fr AS desactive_fr,
	    EJREVEN_nom_en AS desactive_en
	    FROM t_jourevent_jreven
	    	INNER JOIN t_etatjourevent_ejreven
	    	ON FK_EJREVEN_id = EJREVEN_id
	    WHERE FK_EJREVEN_id = 3
	    Group By FK_EVEN_id) AS CompteDesactive
	ON even.EVEN_id = CompteDesactive.evenid_1
	
	# Decompte des jours événements annulés
	LEFT JOIN
	    (SELECT FK_EVEN_id AS evenid_2,
	    Count(*) AS compte_annule,
	    EJREVEN_nom_fr AS annule_fr,
	    EJREVEN_nom_en AS annule_en
	    FROM t_jourevent_jreven
	    	INNER JOIN t_etatjourevent_ejreven
	    	ON FK_EJREVEN_id = EJREVEN_id
	    WHERE FK_EJREVEN_id = 5
	    Group By FK_EVEN_id) AS CompteAnnule
	ON even.EVEN_id = CompteAnnule.evenid_2
	
	# Decompte des jours événements envente
	LEFT JOIN
	    (SELECT FK_EVEN_id AS evenid_3,
	    Count(*) AS compte_envente,
	    EJREVEN_nom_fr AS envente_fr,
	    EJREVEN_nom_en AS envente_en
	    FROM t_jourevent_jreven
	    	INNER JOIN t_etatjourevent_ejreven
	    	ON FK_EJREVEN_id = EJREVEN_id
	    WHERE FK_EJREVEN_id = 2
	    Group By FK_EVEN_id) AS CompteEnVente
	ON even.EVEN_id = CompteEnVente.evenid_3
	
	# Decompte des jours événements dépassé
	LEFT JOIN
	    (SELECT FK_EVEN_id AS evenid_4,
	    Count(*) AS compte_depasse
	    FROM t_jourevent_jreven
	    WHERE JREVEN_date_fin < CURDATE()
	    Group By FK_EVEN_id) AS CompteDepasse
	ON even.EVEN_id = CompteDepasse.evenid_4
	
	# Decompte de tous les jours événements
	LEFT JOIN
	    (SELECT FK_EVEN_id AS evenid_5,
	    Count(*) AS compte_all
	    FROM t_jourevent_jreven
	    Group By FK_EVEN_id) AS CompteAll
	ON even.EVEN_id = CompteAll.evenid_5
	
	# Decompte des jours événements a afficher
	LEFT JOIN
	    (SELECT FK_EVEN_id AS evenid_6,
	    Count(*) AS compte_display
	    FROM t_jourevent_jreven
	    WHERE JREVEN_date_fin >= CURDATE() 
	    AND FK_EJREVEN_id IN (2, 3, 5)
	    Group By FK_EVEN_id) AS CompteDisplay
	ON even.EVEN_id = CompteDisplay.evenid_6
	
) AS v_evenement
";
}

// SEARCH /////////////////////////////////////////////////////////////
function	listeLieuEvent()	{
		return	"SELECT * FROM t_lieuevent_leven ORDER BY LEVEN_nom ASC ";
}
?>