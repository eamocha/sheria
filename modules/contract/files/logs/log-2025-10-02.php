<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-10-02 09:58:07 --> Could not find the language line "PD start Date"
ERROR - 2025-10-02 09:58:07 --> Could not find the language line "PD end date"
ERROR - 2025-10-02 09:58:07 --> Could not find the language line "Performance Bond amount"
ERROR - 2025-10-02 09:58:10 --> Severity: Notice --> Trying to access array offset on value of type bool E:\matrix\sheria\ca\modules\contract\app\views\contracts\view\header\statuses_section.php 0
ERROR - 2025-10-02 09:58:11 --> Severity: Notice --> Trying to access array offset on value of type bool E:\matrix\sheria\ca\modules\contract\app\views\contracts\view\header\statuses_section.php 0
ERROR - 2025-10-02 09:58:11 --> Severity: Notice --> Trying to access array offset on value of type bool E:\matrix\sheria\ca\modules\contract\app\views\contracts\view\header\statuses_section.php 0
ERROR - 2025-10-02 10:36:50 --> Severity: Notice --> Trying to access array offset on value of type bool E:\matrix\sheria\ca\modules\contract\app\controllers\Surety_bonds.php 47
ERROR - 2025-10-02 10:37:06 --> Severity: Notice --> Trying to access array offset on value of type bool E:\matrix\sheria\ca\modules\contract\app\controllers\Surety_bonds.php 47
ERROR - 2025-10-02 10:41:12 --> Severity: Notice --> Trying to access array offset on value of type bool E:\matrix\sheria\ca\modules\contract\app\controllers\Surety_bonds.php 47
ERROR - 2025-10-02 10:42:06 --> Severity: Notice --> Trying to access array offset on value of type bool E:\matrix\sheria\ca\modules\contract\app\controllers\Surety_bonds.php 47
ERROR - 2025-10-02 10:42:55 --> Severity: Notice --> Trying to access array offset on value of type bool E:\matrix\sheria\ca\modules\contract\app\controllers\Surety_bonds.php 47
ERROR - 2025-10-02 10:45:01 --> Severity: Notice --> Trying to access array offset on value of type bool E:\matrix\sheria\ca\modules\contract\app\controllers\Surety_bonds.php 47
ERROR - 2025-10-02 10:51:02 --> Severity: Notice --> Trying to access array offset on value of type bool E:\matrix\sheria\ca\modules\contract\app\controllers\Surety_bonds.php 47
ERROR - 2025-10-02 10:53:50 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:53:50 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "name" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:53:52 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:53:52 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "year" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:53:54 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:53:54 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "year" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:54:06 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:06 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "parties" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:54:07 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:07 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "parties" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:54:12 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:12 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "start_date" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:54:14 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:14 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "start_date" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:54:15 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:15 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "contract_period" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:54:16 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:16 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "end_date" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:54:18 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:18 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "end_date" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:54:19 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:19 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "value" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:54:20 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:20 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "user_department" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:54:21 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:21 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "user_department" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:54:21 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:21 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "bond_type" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:54:35 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:35 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:35 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND remarks LIKE '%eric%'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "bond_type" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:54:49 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:49 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:49 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:54:49 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND remarks LIKE '%eric%'
AND "value" >= '100'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "bond_type" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
ERROR - 2025-10-02 10:55:02 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:55:02 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:55:02 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:55:02 --> Severity: Warning --> Invalid argument supplied for foreach() E:\matrix\sheria\ca\application\libraries\My_model.php 918
ERROR - 2025-10-02 10:55:02 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'year'. - Invalid query: SELECT COUNT(*) OVER() AS total_rows, ISNULL(surety_bonds.id, 0) AS suretyId, ct.id, ct.name AS name, ct.reference_number AS reference_number, ct.start_date AS start_date, ct.end_date AS end_date, ct.value AS value, iso_currencies.code AS currency, surety_bonds.bond_type AS bond_type, surety_bonds.surety_provider AS surety_provider, surety_bonds.bond_amount AS bond_amount, surety_bonds.bond_number AS bond_number, surety_bonds.effective_date AS effective_date, surety_bonds.expiry_date AS expiry_date, surety_bonds.bond_status AS bond_status, surety_bonds.remarks AS remarks, departments.name AS user_department, YEAR(ct.start_date) AS year, CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END, CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END, CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END, CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months, DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period, parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '1'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')
FROM "surety_bonds"
LEFT JOIN "contract" "ct" ON "ct"."id" = "surety_bonds"."contract_id"
LEFT JOIN "iso_currencies" ON "iso_currencies"."id" = "surety_bonds"."currency_id"
LEFT JOIN "departments" ON "departments"."id" = "ct"."department_id"
WHERE "year" = '2'
AND remarks LIKE '%eric%'
AND "value" >= '100'
AND "user_department" = 'finance'
AND ('' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '' OR 
                      ct.assignee_id = '' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = ''))))
ORDER BY "bond_type" ASC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
