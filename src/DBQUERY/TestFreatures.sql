-- Active: 1728917800550@@127.0.0.1@3306@request_manager

SELECT
    repairman_id,
    username,
    email,
    password,
    created_at,
    label as domain_label
FROM
    repairmen r
    LEFT JOIN repairmen_has_domains rd ON r.repairman_id = rd.repairmen_repairman_id
    LEFT JOIN domains d ON d.domain_id = rd.domains_domain_id
WHERE
    repairman_id = 1

SELECT
    r.request_id,
    r.label,
    r.description,
    r.address,
    r.created_at,
    c.username AS client_username,
    d.label AS domain_label,
    rs.status_type as request_status
FROM
    requests r
    LEFT JOIN clients c ON c.client_id = r.clients_client_id
    LEFT JOIN domains d ON d.domain_id = r.domains_domain_id
    LEFT JOIN requests_status rs ON rs.requests_request_id = r.request_id;

SELECT 
    r.request_id,
    r.label,
    r.description,
    r.address,
    r.created_at,
    r.clients_client_id,
    r.domains_domain_id,
    r.repairmen_repairman_id,
    rs.status_type as request_status,
    d.label as domain_name
FROM requests r
LEFT JOIN requests_status rs ON r.request_id = rs.requests_request_id
LEFT JOIN domains d ON r.domains_domain_id = d.domain_id
WHERE r.clients_client_id = :client_id
AND rs.status_type = 'pending'
ORDER BY r.created_at DESC;