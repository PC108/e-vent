<?php

// Requete de base du dossier
function	queryLieuEvent()	{
		return	"
		SELECT * ,
    (SELECT PAYS_nom_fr FROM t_pays_pays WHERE PAYS_id = FK_PAYS_id) AS PAYS_nom_fr
    FROM t_lieuevent_leven ";
}

?>
