<?php

function	listeDocuments()	{
			return	"SELECT * FROM t_doceven_doceven
						LEFT JOIN t_log_log ON DOCEVEN_id = LOG_idrow AND LOG_table = 'DOCEVEN' AND LOG_action = 'add'
						LEFT JOIN t_acces_acs ON ACS_id = FK_ACS_id ";
}

function eventsForThisDoc($id_doc) {
			return "SELECT EVEN_nom_fr
						FROM t_evenement_even
						LEFT JOIN tj_even_doceven ON EVEN_id = TJ_EVEN_id
						WHERE TJ_DOCEVEN_id = $id_doc";
}

function DocsForThisEvent($idEvent) {
			return "SELECT * FROM t_doceven_doceven
			LEFT JOIN tj_even_doceven ON DOCEVEN_id = TJ_DOCEVEN_id
			WHERE TJ_EVEN_id = $idEvent ";
}
?>
