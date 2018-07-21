<!-- RELATIONS-->
<?php
if	(isset($_GET['ami']))	{

		switch	($_GET['ami'])	{
				case	"open":
						$afficheRelation	=	TRUE;
						$queryRelation	=	"
								SELECT * FROM t_adherent_adh AS adherent
								WHERE ADH_id in(
									SELECT FK_ADHAMI_id 
									FROM t_amis_amis
									WHERE FK_ADH_id ="	.	$_SESSION['info_adherent']['id_adh']	.	"
								)
								AND ADH_id !="	.	$_SESSION['info_beneficiaire']['id_benef']	.	"
								UNION
								SELECT * FROM t_adherent_adh
								WHERE ADH_id ="	.	$_SESSION['info_adherent']['id_adh']	.	"
								AND ADH_id !="	.	$_SESSION['info_beneficiaire']['id_benef']	.	"
								ORDER BY ADH_nom, ADH_prenom";

						$RSRelation	=	mysql_query($queryRelation,	$connexion)	or	die(mysql_error());
						$NbreRows_Relation	=	mysql_num_rows($RSRelation);
						break;
				case	"close":
						$afficheRelation	=	FALSE;
						break;
				default:
						$afficheRelation	=	FALSE;
						$queryLien	=	"
								SELECT * 
								FROM t_adherent_adh 
								LEFT JOIN t_typetarif_tytar ON FK_TYTAR_id=TYTAR_id
								WHERE ADH_lien='"	.	$_GET['ami']	.	"'";
						$RSLien	=	mysql_query($queryLien,	$connexion)	or	die(mysql_error());
						$row	=	mysql_fetch_object($RSLien);
						initSessionBenef($row);

						break;
		}
}	else	{
		$afficheRelation	=	FALSE;
}
?>
<div class="bloc_relations corner20-tl corner20-br" style="position: absolute">
		<table border="0" cellspacing="0" cellpadding="0">
				<tr>
						<td><?php	echo	_("Actuellement,	vous	effectuez	des	achats pour")	?>&nbsp;</td>
						<td><?php	echo	$_SESSION['info_beneficiaire']["prenom_benef"]	.	"	<b>"	.	$_SESSION['info_beneficiaire']["nom_benef"]	.	"</b	>	"	?></td>
						<td><?php	if	(!$afficheRelation)	{	?><a href="?ami=open"><div class="bt_relation corner10-all" style="margin-left:15px"><?php	echo	_("changer")	?></div></a><?php	}	?></td> 
				</tr>
				<?php
				if	($afficheRelation)	{

						if	($NbreRows_Relation	==	0)	{
								?>
								<tr>
										<td colspan="3"><?php	echo	_("Vous n'avez pas encore créé de relations.");	?></td>
								</tr>
						<?php	}	else	{	while	($row	=	mysql_fetch_object($RSRelation))	{	?>
										<tr>
												<td>&nbsp;</td>
												<td colspan="2"><a href="?ami=<?php	echo	$row->ADH_lien	?>"><?php	echo	$row->ADH_prenom	.	" <b>"	.	$row->ADH_nom	.	"</b>"	?></a></td>
										</tr>
								<?php	}	}	?>
						<tr>
								<td><a href="?ami=close"><div class="bt_relation corner10-all" style="margin-top:10px"><?php	echo	_("fermer");	?></div></a></td>
								<td colspan="2"><a href="../amis/result.php"><div class="bt_relation corner10-all" style="margin-top:10px"><?php	echo	_("ajouter une relation");	?></div></a></td>
						</tr>
				<?php	}	?>
		</table>
</div>