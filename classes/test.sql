SELECT ra.id, ra.userid, profiledata.data FROM mdl_context c
INNER JOIN mdl_role_assignments ra ON c.id = ra.contextid
INNER JOIN mdl_role r ON r.id = ra.roleid
INNER JOIN mdl_user_info_data profiledata ON profiledata.userid = ra.userid
INNER JOIN mdl_user_info_field profilefield ON profiledata.fieldid = profilefield.id
WHERE c.instanceid = 2659
AND r.shortname = 'student'
AND profilefield.name = 'canonicalclassnames';