<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2026-01-14 23:46:11 --> Severity: Notice --> Trying to access array offset on value of type bool E:\apps\sheria\modules\contract\app\controllers\Contracts.php 108
ERROR - 2026-01-14 23:46:11 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid object name 'contract_payments_view'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, contract.id, contract.name AS name, provider_groups.name AS assigned_team, contract.description AS description, contract.priority AS priority, contract.status AS contract_status, contract.contract_date AS contract_date, contract.start_date AS start_date, contract.end_date AS end_date, contract.status_comments AS status_comments, contract.value AS value, contract.amendment_of as amendment_of, amended.name as amendment_of_name, contract.reference_number AS reference_number, contract.archived AS archived, contract.private AS private, contract.channel, contract.visible_to_cp, countries_languages.name as country, applicable_law.name as applicable_law, CONCAT('CT', CAST(contract.id AS nvarchar)) AS contract_id, status.name AS status, contract.renewal_type as renewal, (CASE 
            WHEN requester.father != '' 
            THEN CONCAT(requester.firstName, ' ', requester.father, ' ', requester.lastName) 
            ELSE CONCAT(requester.firstName, ' ', requester.lastName) 
         END) AS requester, requester.status AS requester_status, CONCAT(up.firstName, ' ', up.lastName) AS assignee, type.name AS type, up.status AS userStatus, contract.createdOn AS createdOn, contract.createdBy AS createdBy, (CASE 
            WHEN contract.channel != 'CP' 
            THEN (SELECT CONCAT(user_profiles.firstName, ' ', user_profiles.lastName) 
                  FROM user_profiles
                  WHERE user_profiles.user_id = contract.createdBy) 
            ELSE (SELECT CONCAT(customer_portal_users.firstName, ' ', customer_portal_users.lastName, ' (Portal User)')
                  FROM customer_portal_users 
                  WHERE customer_portal_users.id = contract.createdBy) 
         END) as createdByName, contract.modifiedOn AS modifiedOn, contract.modifiedBy AS modifiedBy, (CASE 
            WHEN contract.modifiedByChannel != 'CP' 
            THEN (SELECT CONCAT(user_profiles.firstName, ' ', user_profiles.lastName) 
                  FROM user_profiles
                  WHERE user_profiles.user_id = contract.modifiedBy) 
            ELSE (SELECT CONCAT(customer_portal_users.firstName, ' ', customer_portal_users.lastName, ' (Portal User)')
                  FROM customer_portal_users 
                  WHERE customer_portal_users.id = contract.modifiedBy) 
         END) as modifiedByName, iso_currencies.code as currency, ISNULL(cpv.amount_paid_so_far, 0) AS amount_paid_so_far, ISNULL(cpv.balance_due, 0) AS balance_due, parties = STUFF(
            (SELECT ', ' + (CASE 
                WHEN contract_party.party_member_type IS NULL THEN NULL
                ELSE (CASE 
                    WHEN party_category_language.name != '' THEN 
                        (CASE 
                            WHEN contract_party.party_member_type = 'company'
                            THEN CONCAT(party_company.name, ' - ', party_category_language.name)
                            ELSE (CASE 
                                WHEN party_contact.father != '' 
                                THEN CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name)
                                ELSE CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name)
                                END)
                            END)
                    ELSE (CASE 
                        WHEN contract_party.party_member_type = 'company'
                        THEN party_company.name
                        ELSE (CASE 
                            WHEN party_contact.father != '' 
                            THEN CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName)
                            ELSE CONCAT(party_contact.firstName, ' ', party_contact.lastName)
                            END)
                        END)
                    END)
                END)
            FROM contract_party
            LEFT JOIN party ON party.id = contract_party.party_id
            LEFT JOIN companies AS party_company
                ON party_company.id = party.company_id 
                AND contract_party.party_member_type = 'company'
            LEFT JOIN contacts AS party_contact
                ON party_contact.id = party.contact_id 
                AND contract_party.party_member_type = 'contact'
            LEFT JOIN party_category_language
                ON party_category_language.category_id = contract_party.party_category_id 
                AND party_category_language.language_id = '1'
            WHERE contract_party.contract_id = contract.id
            FOR XML PATH('')), 1, 1, '')
FROM "contract"
LEFT JOIN "user_profiles" "up" ON "up"."user_id" = "contract"."assignee_id"
LEFT JOIN "contacts" "requester" ON "requester"."id" = "contract"."requester_id"
LEFT JOIN "provider_groups" ON "provider_groups"."id" = "contract"."assigned_team_id"
LEFT JOIN "user_profiles" "created_users" ON "created_users"."user_id" = "contract"."createdBy"
LEFT JOIN "user_profiles" "modified_users" ON "modified_users"."user_id" = "contract"."modifiedBy"
LEFT JOIN "contract_type_language" as "type" ON "type"."type_id" = "contract"."type_id" and "type"."language_id" = '1'
LEFT JOIN "contract_status_language" as "status" ON "status"."status_id" = "contract"."status_id" and "status"."language_id" = '1'
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "contract"."currency_id"
LEFT JOIN "contract" as "amended" ON "amended"."id" = "contract"."amendment_of"
LEFT JOIN "countries_languages" ON "countries_languages"."country_id" = "contract"."country_id" AND "countries_languages"."language_id" = 1
LEFT JOIN "applicable_law_language" as "applicable_law" ON "applicable_law"."app_law_id" = "contract"."app_law_id" and "applicable_law"."language_id" = '1'
LEFT JOIN "contract_payments_view" AS "cpv" ON "cpv"."contract_id" = "contract"."id"
WHERE "contract"."archived" = 'no'
AND "contract"."category" = 'contract'
AND ('yes' = 'yes' or contract.private IS NULL OR contract.private = '0' OR (contract.private = 1 AND (contract.createdBy = '1' OR contract.assignee_id = '1' OR contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '1'))))
ORDER BY "contract"."id" desc
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2026-01-14 23:58:53 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Could not find stored procedure 'sp_get_new_contract_ref_number'. - Invalid query: DECLARE @ref NVARCHAR(200);
            EXEC sp_get_new_contract_ref_number @deptCode = NULL, @newRefNumber = @ref OUTPUT;
            SELECT @ref AS ref;
ERROR - 2026-01-14 23:36:58 --> Severity: error --> Exception: syntax error, unexpected 'if' (T_IF) E:\apps\sheria\application\libraries\Dmsnew.php 4
