SELECT wu.ID, wu.user_login, wu.user_pass, wu.user_nicename, wu.user_url, wu.user_registered, wu.display_name, um.meta_value AS permissions
FROM wp_users wu
JOIN wp_usermeta um
ON um.user_id = wu.ID
WHERE um.meta_key = "wp_capabilities"
