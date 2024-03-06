SELECT
	comment_ID,
	comment_post_ID,
	comment_author,
	comment_author_email,
	comment_date,
	comment_content
FROM
	wp_comments
WHERE
	comment_approved = 1
	AND comment_author != 'WooCommerce'
	AND comment_author != 'ActionScheduler'
	AND comment_author_email != "marco.zani@quine.it"
	AND comment_author != "chiarascelsi"
	AND comment_content NOT LIKE "%Membership%"
	AND comment_content NOT LIKE "%Lo stato dell'ordine%"
