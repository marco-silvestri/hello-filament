SELECT
    ID,
    post_date_gmt,
    post_content,
    post_title,
    post_excerpt,
    post_author,
    post_status,
    comment_status,
    post_name,
    post_modified_gmt,
    guid,
        (
            SELECT
                guid
            FROM
                wp_posts
            WHERE
                ID = (
                    SELECT
                        meta_value
                    FROM
                        wp_postmeta
                    WHERE
                        meta_key = '_thumbnail_id'
                        AND post_id = p.ID
                    LIMIT 1
                )
                AND post_type = 'attachment'
            LIMIT 1
        ) AS feature_image_url
FROM
    wp_posts p
WHERE
    post_type = 'post'
   	AND (post_status = 'future'
   	OR post_status = 'publish')
ORDER BY ID
