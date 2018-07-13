<?php
function	queryNewsletter()	{
		return	"
		SELECT *,
		IFNULL(v_newsletter.compte_adh,0) As NbreAdherent
		FROM
			(SELECT * 
			FROM t_newsletter_news AS news
			LEFT JOIN
							(SELECT ADH_email AS email,
							Count(*) AS compte_adh
							FROM t_adherent_adh
							Group By ADH_email) AS CompteAdherent
			ON news.NEWS_email = CompteAdherent.email
			) AS v_newsletter ";
}
?>