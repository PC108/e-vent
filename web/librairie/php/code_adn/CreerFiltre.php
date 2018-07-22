<?php

///////////////////////////////////////////////////////////////////////////
// FONCTIONS DE CREATIONS DE LA REQUETE
///////////////////////////////////////////////////////////////////////////

/**
 * Fonction de remplacement de adn_creerFiltre. Permet de passer une requete SQL complète avec un paramêtre variable.
 * A placer avant adn_creerExists et adn_creerFiltre si les fonctions sont utilisées simultanément.
 * Si on utilise simultanément adn_creerFiltre et créer SQL, utiliser la même valeur pour $nomSession afin de remplacer les valeurs.
 * @version 29 mai 2011
 * @author Atelier Du Net
 *
 * @param <string> $debutRequete = Début de la requête
 * @param <string> $nomSession = Nom de la session dans laquelle on met les filtres. (Utilier le même nom pour les 3 fonctions adn_creerFiltre, adn_creerExists et adn_CreerSQL)
 * @param <string> $requeteSQL = Requete SQL complète qu'on souhaite utiliser avec les parmêtres à remplacer. Attention ! d'entourer les paramêtres par des []
 * @param <array> $params = Le nom des paramêtres à remplacer qui correspondent aux noms des champs du formulaire.
 * @return <string> requete finale
 *
 * @example $query = adn_CreerSQL($query, $sessionFiltre, "WHERE (JREVEN_date_debut<='[SQL_dateBD]' AND JREVEN_date_fin>='[SQL_dateBD]')", array('[SQL_dateBD]'));
 */
function adn_CreerSQL($debutRequete, $nomSession, $requeteSQL, $params=array()) {

    $tabParam = array();

// Vérifie si le $_POST ou la S_SESSION est destiné à adn_creerFiltre
// (dans le cas où on utilise simultanément les fonctions adn_creerFiltre, Creer Exists et adn_CreerSQL)
    $suiteRequete = "";
    if (adn_launchFonction('adn_CreerSQL', $nomSession)) {

	if (!empty($_POST)) {
	    $tabParam = array();
	    foreach ($_POST as $key => $value) {
		if (in_array($key, $params) && $value != "Tous" && $value != "" && $key != "fonction") {
		    $tabParam[$key] = $value;
		}
	    }
	    $_SESSION[$nomSession] = array_merge($_SESSION[$nomSession], $tabParam); // pour garder la valeur de la clé 'fonction' initialisée dans adn_launchFonction
	} elseif (isset($_SESSION[$nomSession])) {
	    $tabParam = $_SESSION[$nomSession];
	}

	if (count($tabParam) > 0) {
	    $suiteRequete = $requeteSQL;
	    foreach ($tabParam as $cle => $valeur) {
		$suiteRequete = str_replace("[" . $cle . "]", $valeur, $suiteRequete);
	    }
	} else {
	    $suiteRequete = "";
	}
    }
    return $debutRequete . $suiteRequete;
}

/**
 * Fonction permettant de créer une recherche avec EXISTS ou NOT EXISTS pour les filtres de résultats.
 * A placer après adn_CreerSQL et avant CreerFlitre si les fonctions sont utilisées simulatanément.
 * @version 20 mai 2011
 * @author Atelier Du Net
 *
 * @param <string> $debutRequete = Début de la requête
 * @param <string> $nomSession = Nom de la session dans laquelle on met les filtres
 * @param <string> $recherche = Colonne sur laquelle on va chercher les valeurs
 * @param <string> $exist = Mettre EXIST ou NOT EXIST
 * @param <string> $table = Table sur laquelle on va effectuer la requête EXIST
 * @param <string> $liaison = Jointure entre la table principale et la table où on va effectuer la requête EXIST
 * @return <string> requete finale
 */
function adn_creerExists($debutRequete, $nomSession, $recherche, $exist, $table, $liaison) {

// Vérifie si le $_POST ou la S_SESSION est destiné aux fonctions adn_creerExists et adn_creerFiltre
// (dans le cas où on utilise simultanément les fonctions adn_creerFiltre, Creer Exists et adn_CreerSQL)
    $suiteRequete = "";
    if (adn_launchFonction('adn_creerFiltre', $nomSession)) {

	if (isset($_POST[$recherche])) {
	    $array_filtres = $_POST[$recherche];
	} elseif (isset($_SESSION[$nomSession][$recherche])) {
	    $array_filtres = $_SESSION[$nomSession][$recherche];
	} else {
	    return $debutRequete;
	}

// Vérification de la clause
	if (strpos($debutRequete, " EXISTS ") === FALSE) {
	    $Clause = " WHERE " . $exist;
	} else {
	    $Clause = " AND " . $exist;
	}

	$suiteRequete .= $Clause . " (SELECT * FROM $table WHERE $liaison";

	if (!is_null($recherche)) {
	    $suiteRequete .= " AND (";
	    foreach ($array_filtres as $valeur) {
		$suiteRequete .= "$recherche = $valeur OR ";
	    }
	    $suiteRequete = rtrim($suiteRequete, " OR ");
	    $suiteRequete .=")";
	}
	$suiteRequete .=")";
    }
    return $debutRequete . $suiteRequete;
}

/**
 * Crée une requête SQL à partir d'un tableau contenant les champs de recherches en clé et les valeurs à trouver en valeurs
 * A placer après adn_CreerSQL et adn_creerExists si les fonctions sont utilisées simultanément
 * Permet d'ajouter le caractère * a sa requête pour rechercher un nombre comme un caractère
 * Par exemple : 5* trouvera le chiffre 456, alors que 5 ne le trouvera pas.
 * ATTENTION : Pour une recherche dans une données de type ARRAY contenant des id, utiliser id# dans le menu déroulant
 * Par exemple : <option value="3#">mon critère de recherche</option>
 * ATTENTION ! Nécessite que session_start(); soit lancé car mise en session de $TabParam
 * ATTENTION ! Si on utilise la fonction adn_creerExists, toujours la placer avant la fonction adn_creerFiltre à cause du adn_WHEREouAND. Traite les filtres avec des tables jointes.
 * Si on utilise simultanément adn_creerFiltre et créer SQL, utiliser la même valeur pour $nomSession afin de remplacer les valeurs.
 * @version 29 avril 2011
 * @author Atelier Du Net
 *
 * @param <string> $debutRequete = Début de la requete
 * @param <string> $nomSession = Nom de la session dans laquelle on met les filtres. (Utilier le même nom pour les 3 fonctions adn_creerFiltre, adn_creerExists et adn_CreerSQL)
 * @param <array> $exception = Tableau dans lequel on rentre les critères reçus en POST qui doivent être ignorés (par exemple les tris ou les case à cocher)
 * @param <array> $checknull = champs de case à cocher uniques qui vérifie si la valeur du champ est ou n'est pas = NULL
 * @return <string> requete finale
 *
 * @example $query = adn_creerFiltre($query, $sessionFiltre, array('check_withcmt', 'select_tri', 'submit', 'chemin_retour', 'fonction'), array('FK_CMTEVEN_id', 'EVEN_image', 'EVEN_lien'));
 */
function adn_creerFiltre($debutRequete, $nomSession, $exception=array(), $checknull=array()) {

    $tabParam = array();

// Vérifie si le $_POST ou la S_SESSION est destiné à adn_creerFiltre
// (dans le cas où on utilise simultanément les fonctions adn_creerFiltre, Creer Exists et adn_CreerSQL)
    $suiteRequete = "";
    if (adn_launchFonction('adn_creerFiltre', $nomSession)) {

	// Vérifie si la fonction adn_creerExists a été lancée avant adn_creerFiltre à cause du adn_WHEREouAND
	$forceAND = strpos($debutRequete, "EXISTS"); // TRUE or FALSE

	if (!empty($_POST)) {
	    $tabParam = array();
	    foreach ($_POST as $key => $value) {
		if ($value != "Tous" && $value != "") {
		    $tabParam[$key] = $value;
		}
	    }
	    $_SESSION[$nomSession] = $tabParam;
	} elseif (isset($_SESSION[$nomSession])) {
	    $tabParam = $_SESSION[$nomSession];
	}

	// Transformation du $tabParam en requête SQL WHERE....
	if (count($tabParam) > 0) {
	    $PremierParam = 0;


	    foreach ($tabParam as $cle => $valeur) {
		if ((!in_array($cle, $exception)) AND (!in_array($cle, $checknull))) {
		    $Clause = adn_WHEREouAND($suiteRequete, $forceAND);

		    if (is_array($valeur)) {
			foreach ($valeur as $valeurvaleur) {
			    $Clause = adn_WHEREouAND($suiteRequete, $forceAND);
			    $suiteRequete .= $Clause . $cle . " LIKE '%" . '\"' . $valeurvaleur . '\"' . "%'";
			}
		    } elseif (!is_numeric($valeur)) {
			if (strrpos($valeur, "*") == (strlen($valeur) - 1)) { //Vérifie si on a pas forcé la recherche avec un *
			    $valeur = rtrim($valeur, "*");
			    $suiteRequete .= $Clause . $cle . " LIKE '%$valeur%'";
			} else if (strrpos($valeur, "#") == (strlen($valeur) - 1)) { //Vérifie si on ne cherche pas un id dans une donnée de type array sérialisé
			    $valeur = rtrim($valeur, "#");
			    $suiteRequete .= $Clause . $cle . " LIKE '%" . '\"' . $valeur . '\"' . "%'";
			} else { // Sinon à traiter comme une chaine
			    $suiteRequete .= $Clause . $cle . " LIKE '%$valeur%'";
			}
		    } else { // sinon à traiter comme un chiffre
			$suiteRequete .= $Clause . $cle . " LIKE '$valeur'";
		    }
		}
	    }
	}
	// Traitement des cases à cocher uniques
	foreach ($checknull as $cle) {
	    if (count($checknull) && isset($tabParam[$cle])) {
		$Clause = adn_WHEREouAND($suiteRequete, $forceAND);
		if ($tabParam[$cle] == 1) {
		    $suiteRequete .= $Clause . $cle . " IS NOT NULL";
		} else if ($tabParam[$cle] == 0) {
		    $suiteRequete .= $Clause . $cle . " IS NULL";
		}
	    }
	}
    }
    return $debutRequete . $suiteRequete;
}

/**
 * Fonction ajoutant un GROUP BY à la requête SQL si nécessaire.
 * A placer après adn_CreerSQL,adn_creerExists et adn_creerFiltre si nécessaire
 * @author Atelier Du Net
 *
 * @param <string> $debutRequete = Début de la requete
 * @param <string> $groupBy = colonne sur laquelle grouper la requête
 * @return <string> requete finale
 *
 * @example $query = adn_groupBy($query,'EVEN_id');
 */
function adn_groupBy($debutRequete, $groupBy) {
    $suiteRequete = " GROUP BY " . $groupBy;
    return $debutRequete . $suiteRequete;
}

/**
 * Fonction ajoutant un ORDER BY à la requête SQL si nécessaire.
 * A placer après adn_CreerSQL, adn_creerExists, adn_creerFiltre et adn_groupBy si nécessaire
 * $defautTri peut être renseigné directement dans la fonction ou provenir du formulaire via une liste déroulante avec name="select_tri".
 * @version 15 juin 2011
 * @author Atelier Du Net
 *
 * @param <string> $debutRequete = Début de la requete
 * @param <string> $defautTri = colonne sur laquelle trier la requête.
 * @return <string> requete finale
 *
 * @example $query = adn_orderBy($query, $sessionFiltre, 'EVEN_id DESC');
 */
function adn_orderBy($debutRequete, $nomSession, $defautTri) {

    //Ajout du filtre
    if (isset($_POST['select_tri'])) { // Commence par vérifier s'il y a un tri envoyé via search.php
	$suiteRequete = " ORDER BY " . $_POST['select_tri'];
    } elseif (isset($_SESSION[$nomSession]['select_tri'])) { // Sinon, vérifie s'il n'y a pas un tri sauvegardé en session
	$suiteRequete = " ORDER BY " . $_SESSION[$nomSession]['select_tri'];
    } elseif (!is_null($defautTri)) { // Sinon, vérifie s'il n'y a pas un tri par défaut dans adn_creerFiltre
	$suiteRequete = " ORDER BY " . $defautTri;
    }

    return $debutRequete . $suiteRequete;
}

///////////////////////////////////////////////////////////////////////////
// FONCTIONS ANNEXES
///////////////////////////////////////////////////////////////////////////

/**
 * Vérifie si le $_POST ou la S_SESSION est destiné à la fonction adn_creerFiltre, adn_creerExists ou adn_CreerSQL
 * (dans le cas où on utilise simultanément les fonctions adn_creerFiltre, Creer Exists et adn_CreerSQL)
 * Le script est placé en début de chaque fonction
 * @version 30 mai 2011
 * @author Atelier Du Net
 *
 * @param <string> $nomFonction = Nom de la fonction dans laquelle est lancé le script
 * @param <string> $nomSession = Nom de la session dans laquelle on met les filtres. (Utilier le même nom pour les 3 fonctions adn_creerFiltre, adn_creerExists et adn_CreerSQL)
 *
 * @return <boolean> TRUE or FLASE
 */
function adn_launchFonction($nomFonction, $nomSession) {
    if (isset($_POST['fonction'])) {
	if ($_POST['fonction'] == $nomFonction) {
	    if (isset($_SESSION[$nomSession]['fonction'])) {
		if ($_SESSION[$nomSession]['fonction'] == $nomFonction) {
		    return TRUE;
		} else {
		    unset($_SESSION[$nomSession]); // Efface la session pour la remplacer par une nouvelle
		    $_SESSION[$nomSession]['fonction'] = $nomFonction;
		    return TRUE;
		}
	    } else {
		$_SESSION[$nomSession]['fonction'] = $nomFonction;
		return TRUE;
	    }
	} else {
	    return FALSE;
	}
    } else {
	if (isset($_SESSION[$nomSession]['fonction'])) {
	    if ($_SESSION[$nomSession]['fonction'] == $nomFonction) {
		return TRUE;
	    } else {
		return FALSE;
	    }
	} else {
	    return FALSE;
	}
    }
}

/**
 * Fonction retournant 'WHERE' ou 'AND' après analyse d'une requête. S'il n'y a pas de 'WHERE' dans la requete, on retourne 'WHERE', sinon 'AND'.
 * @author Atelier Du Net
 *
 * @param <string> $Requete = Requete dans laquelle on cherche si on doit mettre un 'WHERE' ou un 'AND'
 * @param <boolean> $forceAND = Si TRUE, on est forcément dans un 'AND'
 * @return string = 'WHERE' ou 'AND'
 */
function adn_WHEREouAND($Requete, $forceAND) {
    if ((strpos($Requete, " WHERE ") === FALSE) AND $forceAND === FALSE) {
	$Clause = " WHERE ";
    } else {
	$Clause = " AND ";
    }
    return $Clause;
}

/**
 * Fonction retournant le nombre de filtres
 * @author Atelier Du Net
 *
 * @param <string> $NomSession = Nom de la session dans laquelle on met les filtres
 * @param <array> $exception = tableau des critères qui doivent être ignorés.
 * @return int = nombre de filtres
 */
function adn_afficheNbreFiltres($NomSession, $exception=array()) {
    $nbreFiltres = 0;
    if (isset($_SESSION[$NomSession])) {
	if (isset($_SESSION[$NomSession]['requeteSQL'])) {
	    $nbreFiltres = 1;
	} else {
	    foreach ($_SESSION[$NomSession] as $cle => $valeur) {
		if (!in_array($cle, $exception)) {
		    if (is_array($valeur)) {
			foreach ($valeur as $sscle => $ssvaleur) {
			    $nbreFiltres++;
			}
		    } else {
			$nbreFiltres++;
		    }
		}
	    }
	}
    }
    return $nbreFiltres;
}

/**
 * Fonction pour savoir si un filtre est activé ou pas
 * @author Atelier Du Net
 *
 * @param <type> $nomInput = nom du filtre à analyser
 * @param <type> $nomSession = Nom de la session dans laquelle on met les filtres
 * @return <string> Retourne "filtre_actif" s'il est activé
 */
function adn_showFiltre($nomInput, $nomSession) {
    if ((isset($_SESSION[$nomSession])) && (array_key_exists($nomInput, $_SESSION[$nomSession]))) {
	return ' filtre_actif';
    }
}

/**
 * Clean la session 
 * @author Atelier Du Net
 *
 * @param <type> $nomSession = Nom de la session dans laquelle on met les filtres
 */
function adn_checkEffaceFiltres($nomSession) {
    if (isset($_GET['clean']) && ($_GET['clean'] == 1)) {
	unset($_SESSION[$nomSession]);
    }
}

/*
 * Document enregistré en utf-8
 * Modifié le 16/03/2011
 * Réaffiche les filtres enregistrés dans la session dans le formulaire de filtre search.php
 * $nomSession = Nom du tableau dans la session qui stocke les valeurs du filtre. par ex : FiltreBen
 * $valSession = Nom de la clé dans le tableau qui stocke la valeur à réafficher. Normalement le nom de l'input. par ex : array_dispos_ben ou nom_ben
 * $typeInput = Type d'input du formulaire : LISTE, CHECKBOX (liste de case à cocher), CHAMP, ONOFF (1 case à cocher, par ex pour valider une licence)
 * $valSession = Valeur à vérifier selon l'input :
 * Pour LISTE, mettre la valeur de l'option.
 * Ex : <option value="18" <?php echo adn_reafficheLastFiltre('FiltreBen', 'age_ben', "18", 'LISTE') ?>>moins de 18 ans</option>
 * Pour CHECKBOX, mettre la valeur de l'input type="checkbox".
 * Ex : <input type="checkbox" name="array_lgspoken_ben[]" value="GBR" <?php echo adn_reafficheLastFiltre('FiltreBen', 'array_lgspoken_ben', 'GBR', 'CHECKBOX') ?>> Anglais
 * Pour CHAMP, mettre "Tous", la valeur par défaut.
 * Ex : <input name="nom_ben" type="text" value="<?php echo adn_reafficheLastFiltre('FiltreBen', 'nom_ben', 'Tous', 'CHAMP') ?>" size="35">
 * Pour ONOFF, c'est sans importance, mettre null. Ex : echo adn_reafficheLastFiltre('FiltreBen', 'check_withcmt', null, 'ONOFF')
 * Ex : <input type="checkbox" name="check_withcmt" <?php echo adn_reafficheLastFiltre('FiltreBen', 'check_withcmt', null, 'ONOFF') ?>> Rechercher uniquement les bénévoles avec des commentaires
 * Pour RADIO, mettre la valeur de l'input type="radio". Mettre en premier un radio "Tous" pour désactiver le choix.
 * <input type="radio" name="photo_prs" value="Tous" checked="checked" />Tous
 * <input type="radio" name="photo_prs" value="1" <?php echo adn_reafficheLastFiltre('FiltrePrs', 'photo_prs', '1', 'RADIO') ?> />oui
 * <input type="radio" name="photo_prs" value="0" <?php echo adn_reafficheLastFiltre('FiltrePrs', 'photo_prs', '0', 'RADIO') ?> />non
 */

function adn_reafficheLastFiltre($nomSession, $valSession, $valInput, $typeInput) {

    $retour = "";

    if (isset($_SESSION[$nomSession][$valSession])) {
	switch ($typeInput) {
	    case 'LISTE':
		if (strcmp($valInput, $_SESSION[$nomSession][$valSession]) == 0) {
		    $retour = 'selected="selected"';
		}
		break;
	    case 'RADIO':
		if ($valInput == $_SESSION[$nomSession][$valSession]) {
		    $retour = 'checked="checked"';
		}
		break;
	    case 'CHECKBOX':
		if (in_array($valInput, $_SESSION[$nomSession][$valSession])) {
		    $retour = 'checked="checked"';
		}
		break;
	    case 'CHAMP':
		$retour = htmlspecialchars($_SESSION[$nomSession][$valSession]);
		break;
//	    case 'ONOFF':
//		$retour = 'checked="checked"';
//		break;
	}
    } else {
	switch ($typeInput) {
	    case 'CHAMP':
		$retour = htmlspecialchars($valInput);
		break;
	}
    }
    return $retour;
}

?>