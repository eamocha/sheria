<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-07-13 00:30:53 --> Could not find the language line "confirmationDeleteSelectedRecord"
ERROR - 2025-07-13 00:33:18 --> Severity: Notice --> Undefined index: assignee D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 106
ERROR - 2025-07-13 00:33:18 --> Could not find the language line "confirmationDeleteSelectedRecord"
ERROR - 2025-07-13 00:39:24 --> Severity: Notice --> Undefined index: assignee D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 175
ERROR - 2025-07-13 00:39:24 --> Severity: Notice --> Undefined index: assignee_team_id D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 179
ERROR - 2025-07-13 00:39:24 --> Severity: Notice --> Undefined index: creator_name D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 248
ERROR - 2025-07-13 00:39:24 --> Severity: Notice --> Undefined index: modifier_name D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 260
ERROR - 2025-07-13 00:39:24 --> Severity: Notice --> Undefined index: assignee D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 446
ERROR - 2025-07-13 00:40:04 --> Severity: Notice --> Undefined index: assignee D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 175
ERROR - 2025-07-13 00:40:04 --> Severity: Notice --> Undefined index: assignee_team_id D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 179
ERROR - 2025-07-13 00:40:04 --> Severity: Notice --> Undefined index: creator_name D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 248
ERROR - 2025-07-13 00:40:04 --> Severity: Notice --> Undefined index: modifier_name D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 260
ERROR - 2025-07-13 00:40:04 --> Severity: Notice --> Undefined index: assignee D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 446
ERROR - 2025-07-13 00:42:21 --> Severity: Notice --> Undefined index: assignee D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 175
ERROR - 2025-07-13 00:42:21 --> Severity: Notice --> Undefined index: assignee_team_id D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 179
ERROR - 2025-07-13 00:42:21 --> Severity: Notice --> Undefined index: creator_name D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 248
ERROR - 2025-07-13 00:42:21 --> Severity: Notice --> Undefined index: modifier_name D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 260
ERROR - 2025-07-13 00:42:21 --> Severity: Notice --> Undefined index: assignee D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 446
ERROR - 2025-07-13 00:43:11 --> Severity: Notice --> Undefined index: assignee D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 175
ERROR - 2025-07-13 00:43:11 --> Severity: Notice --> Undefined index: assignee_team_id D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 179
ERROR - 2025-07-13 00:43:11 --> Severity: Notice --> Undefined index: creator_name D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 248
ERROR - 2025-07-13 00:43:11 --> Severity: Notice --> Undefined index: modifier_name D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 260
ERROR - 2025-07-13 00:43:12 --> Severity: Notice --> Undefined index: assignee D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 446
ERROR - 2025-07-13 00:43:26 --> Severity: Notice --> Undefined index: assignee D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 175
ERROR - 2025-07-13 00:43:26 --> Severity: Notice --> Undefined index: assignee_team_id D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 179
ERROR - 2025-07-13 00:43:26 --> Severity: Notice --> Undefined index: creator_name D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 248
ERROR - 2025-07-13 00:43:26 --> Severity: Notice --> Undefined index: modifier_name D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 260
ERROR - 2025-07-13 00:43:26 --> Severity: Notice --> Undefined index: assignee D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 446
ERROR - 2025-07-13 01:30:42 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'contact_id'. - Invalid query: SELECT "conveyancing_instruments".*, "instrument_type"."name" as "instrument_type", "transaction_type"."name" as "transaction_type_name", CONCAT(staff.firstName, ' ', staff.lastName) as staff, CONCAT(vendor.firstName, ' ', vendor.lastName) as vendor_name, CONCAT(assignee.firstName, ' ', assignee.lastName) as assignee, CONCAT(creator.firstName, ' ', creator.lastName) as creator_name, CONCAT(modifier.firstName, ' ', modifier.lastName) as modifier_name, CONCAT('CNV-', conveyancing_instruments.id) as conveyancing_id
FROM "conveyancing_instruments"
LEFT JOIN "user_profiles" "assigned_user" ON "assigned_user"."user_id" = "conveyancing_instruments"."assignee_id"
LEFT JOIN "contacts" "requester" ON "requester"."contact_id" = "conveyancing_instruments"."initiated_by"
LEFT JOIN "conveyancing_instrument_types" as "instrument_type" ON "instrument_type"."id" = "conveyancing_instruments"."instrument_type_id"
LEFT JOIN "conveyancing_transaction_types" as "transaction_type" ON "transaction_type"."id" = "conveyancing_instruments"."transaction_type_id"
LEFT JOIN "user_profiles" "assignee" ON "assignee"."user_id" = "conveyancing_instruments"."assignee_id"
LEFT JOIN "user_profiles" "creator" ON "creator"."user_id" = "conveyancing_instruments"."createdBy"
LEFT JOIN "user_profiles" "modifier" ON "modifier"."user_id" = "conveyancing_instruments"."modifiedBy"
LEFT JOIN "contacts" "staff" ON "staff"."id" = "conveyancing_instruments"."initiated_by"
LEFT JOIN "contacts" "vendor" ON "vendor"."id" = "conveyancing_instruments"."parties_id"
WHERE ("conveyancing_instruments"."id" = 26 and "conveyancing_instruments"."channel" = 'CP' )
 ORDER BY 1 OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY
ERROR - 2025-07-13 01:43:52 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'extCounsel'. - Invalid query: SELECT "conveyancing_instruments".*, "instrument_type"."name" as "instrument_type", "transaction_type"."name" as "transaction_type_name", CONCAT(staff.firstName, ' ', staff.lastName) as staff, CONCAT(vendor.firstName, ' ', vendor.lastName) as vendor_name, CONCAT(assignee.firstName, ' ', assignee.lastName) as assignee, CONCAT(creator.firstName, ' ', creator.lastName) as creator_name, CONCAT(modifier.firstName, ' ', modifier.lastName) as modifier_name, CONCAT('CNV-', conveyancing_instruments.id) as conveyancing_id, "extCounsel"."name" as "external_counsel"
FROM "conveyancing_instruments"
LEFT JOIN "user_profiles" "assigned_user" ON "assigned_user"."user_id" = "conveyancing_instruments"."assignee_id"
LEFT JOIN "contacts" "requester" ON "requester"."id" = "conveyancing_instruments"."initiated_by"
LEFT JOIN "conveyancing_instrument_types" as "instrument_type" ON "instrument_type"."id" = "conveyancing_instruments"."instrument_type_id"
LEFT JOIN "conveyancing_transaction_types" as "transaction_type" ON "transaction_type"."id" = "conveyancing_instruments"."transaction_type_id"
LEFT JOIN "user_profiles" "assignee" ON "assignee"."user_id" = "conveyancing_instruments"."assignee_id"
LEFT JOIN "user_profiles" "creator" ON "creator"."user_id" = "conveyancing_instruments"."createdBy"
LEFT JOIN "user_profiles" "modifier" ON "modifier"."user_id" = "conveyancing_instruments"."modifiedBy"
LEFT JOIN "contacts" "staff" ON "staff"."id" = "conveyancing_instruments"."initiated_by"
LEFT JOIN "contacts" "vendor" ON "vendor"."id" = "conveyancing_instruments"."parties_id"
LEFT JOIN "companies" "extCounsel" ON "extCounsel"="conveyancing_instruments"."external_counsel_id"
WHERE ("conveyancing_instruments"."id" = 26 and "conveyancing_instruments"."channel" = 'CP' )
 ORDER BY 1 OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY
ERROR - 2025-07-13 02:18:33 --> Severity: Notice --> Undefined index: transaction_type D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\conveyancing-detail.php 90
ERROR - 2025-07-13 02:22:38 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Cannot insert the value NULL into column 'createdBy', table 'lemis.dbo.documents_management_system'; column does not allow nulls. INSERT fails. - Invalid query: INSERT INTO "documents_management_system" ("type", "name", "extension", "parent", "lineage", "size", "version", "private", "document_type_id", "document_status_id", "comment", "module", "module_record_id", "system_document", "visible", "visible_in_cp", "visible_in_ap", "createdOn", "createdBy", "createdByChannel", "initial_version_created_on", "initial_version_created_by", "initial_version_created_by_channel", "modifiedOn", "modifiedBy", "modifiedByChannel", "is_locked", "last_locked_by", "last_locked_by_channel", "last_locked_on") VALUES ('file', 'retractable-banners', 'jpg', '110554', NULL, 30539, 1, NULL, '15', NULL, 'dsdsds', 'conveyancing', '21', 0, 1, 0, 0, '2025-07-13 02:22:38', NULL, 'A4L', NULL, NULL, NULL, '2025-07-13 02:22:38', NULL, 'A4L', NULL, NULL, NULL, NULL)
ERROR - 2025-07-13 03:02:20 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-13 03:02:20 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-13 03:05:17 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-13 03:05:17 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-13 03:05:24 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Incorrect syntax near the keyword 'AND'. - Invalid query: SELECT "d"."id", "d"."type", "d"."name", "d"."extension", (case when (d.type = 'file') then concat(d.name, '.', d.extension) else d.name end) AS full_name, "d"."parent", "p"."lineage" as "parent_lineage", "d"."lineage", "d"."size", "d"."version", "d"."private", "d"."document_type_id", (SELECT count(id) FROM documents_management_system where documents_management_system.parent =d.id) children_count, "d"."document_status_id", "d"."comment", "d"."module", "d"."module_record_id", "d"."system_document", "d"."visible", "d"."visible_in_cp", "d"."visible_in_ap", "d"."createdOn", "d"."createdBy", (case when (d.createdByChannel = 'CP') then concat(creatorCP.firstName, ' ', "creatorCP"."lastName", ' (Portal User)') when (d.createdByChannel = 'AP') then concat(creatorAP.firstName, ' ', "creatorAP"."lastName", ' (Advisor)') else concat(creatorU.firstName, ' ', creatorU.lastName) end) AS creator_full_name, "d"."createdByChannel", "d"."initial_version_created_on", "d"."initial_version_created_by", "d"."initial_version_created_by_channel", (case when (d.initial_version_created_On is not null) then d.initial_version_created_On else d.createdOn end) AS display_created_on, (
                    case when (
                      d.initial_version_created_by is not null
                    ) then (
                      case when (
                        d.initial_version_created_by_channel = 'CP'
                      ) then concat(
                        displayIniCreatorCP.firstName, ' ', "displayIniCreatorCP"."lastName", ' (Portal User)'
                      ) 
                      when (
                        d.initial_version_created_by_channel = 'AP'
                      ) then concat(
                        displayIniCreatorAP.firstName, ' ', "displayIniCreatorAP"."lastName", ' (Advisor)'
                      )
                      else concat(
                        displayIniCreatorU.firstName, ' ', displayIniCreatorU.lastName
                      ) end
                    ) else (
                      case when (d.createdByChannel = 'CP') then concat(
                        displayCreatorCP.firstName, ' ', "displayCreatorCP"."lastName", ' (Portal User)'
                      )
                      when (d.createdByChannel = 'AP') then concat(
                        displayCreatorAP.firstName, ' ', "displayCreatorAP"."lastName", ' (Advisor)'
                      )
                       else concat(
                        displayCreatorU.firstName, ' ', displayCreatorU.lastName
                      ) end
                    ) end
                ) AS display_creator_full_name, (
                case when (
                  d.initial_version_created_by_channel is not null
                ) then d.initial_version_created_by_channel else d.createdByChannel end
              ) AS display_created_by_channel, (
                case when (d.modifiedByChannel = 'CP') then concat(
                  modifierCP.firstName, ' ', "modifierCP"."lastName", ' (Portal User)'
                ) 
                when (d.modifiedByChannel = 'AP') then concat(
                    modifierAP.firstName, ' ', "modifierAP"."lastName", ' (Advisors)'
                  ) 
                else concat(
                  modifierU.firstName, ' ', modifierU.lastName
                ) end
              ) AS modifier_full_name, "d"."modifiedOn", "d"."modifiedBy", "d"."modifiedByChannel", CASE WHEN d.type = 'folder' AND d.private = 1 THEN (SELECT CASE WHEN dmuau.recordId IS NOT NULL THEN 1 ELSE 0 END
                FROM document_managment_users dmuau
                WHERE dmuau.user_id =  AND dmuau.recordId = d.id) ELSE (SELECT CASE WHEN dms.private = 1 THEN (SELECT CASE WHEN dmuau.recordId IS NOT NULL THEN 1 ELSE 0 END
                FROM document_managment_users dmuau
                WHERE dmuau.user_id =  AND dmuau.recordId = dms.id) ELSE 1 END
                FROM documents_management_system dms
                WHERE dms.id = d.parent) END as is_accessible, (CASE WHEN contract_approval_submission.status = 'approved' THEN 0 ELSE (CASE WHEN approval_signature_documents.to_be_approved IS NOT NULL THEN approval_signature_documents.to_be_approved ELSE 0 END)END) as to_be_approved, (CASE WHEN contract_signature_submission.status = 'signed' THEN 0 ELSE (CASE WHEN approval_signature_documents.to_be_signed IS NOT NULL THEN approval_signature_documents.to_be_signed ELSE 0 END)END) as to_be_signed
FROM "documents_management_system" "d"
LEFT JOIN "documents_management_system" "p" ON "p"."id" = "d"."parent"
LEFT JOIN "customer_portal_users" "creatorCP" ON "creatorCP"."id" = "d"."createdBy" AND "d"."createdByChannel" = 'CP'
LEFT JOIN "user_profiles" "creatorU" ON "creatorU"."user_id" = "d"."createdBy" AND "d"."createdByChannel" NOT IN ('AP', 'CP')
LEFT JOIN "advisor_users" "creatorAP" ON "creatorAP"."id" = "d"."createdBy" AND "d"."createdByChannel" != 'AP'
LEFT JOIN "customer_portal_users" "displayIniCreatorCP" ON "displayIniCreatorCP"."id" = "d"."initial_version_created_by" AND "d"."initial_version_created_by" is not null and "d"."initial_version_created_by_channel" = 'CP'
LEFT JOIN "user_profiles" "displayIniCreatorU" ON "displayIniCreatorU"."user_id" = "d"."initial_version_created_by" AND "d"."initial_version_created_by" is not null and "d"."initial_version_created_by_channel" NOT IN ('AP', 'CP')
LEFT JOIN "advisor_users" "displayIniCreatorAP" ON "displayIniCreatorAP"."id" = "d"."initial_version_created_by" AND "d"."initial_version_created_by" is not null and "d"."initial_version_created_by_channel" = 'AP'
LEFT JOIN "customer_portal_users" "displayCreatorCP" ON "displayCreatorCP"."id" = "d"."createdBy" AND "d"."initial_version_created_by" is null and "d"."createdByChannel" = 'CP'
LEFT JOIN "user_profiles" "displayCreatorU" ON "displayCreatorU"."user_id" = "d"."createdBy" AND "d"."initial_version_created_by" is null and "d"."createdByChannel" NOT IN ('AP', 'CP')
LEFT JOIN "advisor_users" "displayCreatorAP" ON "displayCreatorAP"."id" = "d"."createdBy" AND "d"."initial_version_created_by" is null and "d"."createdByChannel" = 'AP'
LEFT JOIN "customer_portal_users" "modifierCP" ON "modifierCP"."id" = "d"."modifiedBy" AND "d"."modifiedByChannel" = 'CP'
LEFT JOIN "user_profiles" "modifierU" ON "modifierU"."user_id" = "d"."modifiedBy" AND "d"."modifiedByChannel" NOT IN ('AP', 'CP')
LEFT JOIN "advisor_users" "modifierAP" ON "modifierAP"."id" = "d"."modifiedBy" AND "d"."modifiedByChannel" = 'AP'
LEFT JOIN "approval_signature_documents" ON "approval_signature_documents"."document_id" = "d"."id"
LEFT JOIN "contract_approval_submission" ON "contract_approval_submission"."contract_id" = "d"."module_record_id"
LEFT JOIN "contract_signature_submission" ON "contract_signature_submission"."contract_id" = "d"."module_record_id"
WHERE "d"."module" = 'contract'
AND "d"."visible" = 1
AND "d"."module_record_id" = '10112'
AND "p"."lineage" = '\50444'
AND "d"."visible_in_cp" = 1
ORDER BY "d"."type" desc, "d"."name" asc
ERROR - 2025-07-13 03:05:45 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Cannot insert the value NULL into column 'createdBy', table 'lemis.dbo.documents_management_system'; column does not allow nulls. INSERT fails. - Invalid query: INSERT INTO "documents_management_system" ("type", "name", "extension", "parent", "lineage", "size", "version", "private", "document_type_id", "document_status_id", "comment", "module", "module_record_id", "system_document", "visible", "visible_in_cp", "visible_in_ap", "createdOn", "createdBy", "createdByChannel", "initial_version_created_on", "initial_version_created_by", "initial_version_created_by_channel", "modifiedOn", "modifiedBy", "modifiedByChannel", "is_locked", "last_locked_by", "last_locked_by_channel", "last_locked_on") VALUES ('file', 'Questionnaire- Erick1', 'docx', '50444', NULL, 23290, 2, NULL, NULL, NULL, NULL, 'contract', '10112', 0, 1, 1, 0, '2025-07-13 03:05:45', NULL, 'A4L', '2025-07-13 03:03:00', '1', 'A4L', '2025-07-13 03:05:45', NULL, 'A4L', NULL, NULL, NULL, NULL)
ERROR - 2025-07-13 03:06:31 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-13 03:06:31 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-13 03:14:42 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'user_id'. - Invalid query: SELECT "conveyancing_instruments".*, "instrument_type"."name" as "instrument_type", "transaction_type"."name" as "transaction_type_name", CONCAT(staff.firstName, ' ', staff.lastName) as staff, CONCAT(vendor.firstName, ' ', vendor.lastName) as vendor_name, CONCAT(assignee.firstName, ' ', assignee.lastName) as assignee, CONCAT(creator.firstName, ' ', creator.lastName) as creator_name, CONCAT(modifier.firstName, ' ', modifier.lastName) as modifier_name, CONCAT('CNV-', conveyancing_instruments.id) as conveyancing_id, "extCounsel"."name" as "external_counsel"
FROM "conveyancing_instruments"
LEFT JOIN "user_profiles" "assigned_user" ON "assigned_user"."user_id" = "conveyancing_instruments"."assignee_id"
LEFT JOIN "contacts" "requester" ON "requester"."id" = "conveyancing_instruments"."initiated_by"
LEFT JOIN "conveyancing_instrument_types" as "instrument_type" ON "instrument_type"."id" = "conveyancing_instruments"."instrument_type_id"
LEFT JOIN "conveyancing_transaction_types" as "transaction_type" ON "transaction_type"."id" = "conveyancing_instruments"."transaction_type_id"
LEFT JOIN "user_profiles" "assignee" ON "assignee"."user_id" = "conveyancing_instruments"."assignee_id"
LEFT JOIN "contacts" "creator" ON "creator"."user_id" = "conveyancing_instruments"."createdBy"
LEFT JOIN "user_profiles" "modifier" ON "modifier"."user_id" = "conveyancing_instruments"."modifiedBy"
LEFT JOIN "contacts" "staff" ON "staff"."id" = "conveyancing_instruments"."initiated_by"
LEFT JOIN "contacts" "vendor" ON "vendor"."id" = "conveyancing_instruments"."parties_id"
LEFT JOIN "companies" "extCounsel" ON "extCounsel"."id"="conveyancing_instruments"."external_counsel_id"
WHERE ("conveyancing_instruments"."id" = 21 and "conveyancing_instruments"."channel" = 'CP' )
 ORDER BY 1 OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY
ERROR - 2025-07-13 23:37:11 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Incorrect syntax near the keyword 'AND'. - Invalid query: SELECT "d"."id", "d"."type", "d"."name", "d"."extension", (case when (d.type = 'file') then concat(d.name, '.', d.extension) else d.name end) AS full_name, "d"."parent", "p"."lineage" as "parent_lineage", "d"."lineage", "d"."size", "d"."version", "d"."private", "d"."document_type_id", (SELECT count(id) FROM documents_management_system where documents_management_system.parent =d.id) children_count, "d"."document_status_id", "d"."comment", "d"."module", "d"."module_record_id", "d"."system_document", "d"."visible", "d"."visible_in_cp", "d"."visible_in_ap", "d"."createdOn", "d"."createdBy", (case when (d.createdByChannel = 'CP') then concat(creatorCP.firstName, ' ', "creatorCP"."lastName", ' (Portal User)') when (d.createdByChannel = 'AP') then concat(creatorAP.firstName, ' ', "creatorAP"."lastName", ' (Advisor)') else concat(creatorU.firstName, ' ', creatorU.lastName) end) AS creator_full_name, "d"."createdByChannel", "d"."initial_version_created_on", "d"."initial_version_created_by", "d"."initial_version_created_by_channel", (case when (d.initial_version_created_On is not null) then d.initial_version_created_On else d.createdOn end) AS display_created_on, (
                    case when (
                      d.initial_version_created_by is not null
                    ) then (
                      case when (
                        d.initial_version_created_by_channel = 'CP'
                      ) then concat(
                        displayIniCreatorCP.firstName, ' ', "displayIniCreatorCP"."lastName", ' (Portal User)'
                      ) 
                      when (
                        d.initial_version_created_by_channel = 'AP'
                      ) then concat(
                        displayIniCreatorAP.firstName, ' ', "displayIniCreatorAP"."lastName", ' (Advisor)'
                      )
                      else concat(
                        displayIniCreatorU.firstName, ' ', displayIniCreatorU.lastName
                      ) end
                    ) else (
                      case when (d.createdByChannel = 'CP') then concat(
                        displayCreatorCP.firstName, ' ', "displayCreatorCP"."lastName", ' (Portal User)'
                      )
                      when (d.createdByChannel = 'AP') then concat(
                        displayCreatorAP.firstName, ' ', "displayCreatorAP"."lastName", ' (Advisor)'
                      )
                       else concat(
                        displayCreatorU.firstName, ' ', displayCreatorU.lastName
                      ) end
                    ) end
                ) AS display_creator_full_name, (
                case when (
                  d.initial_version_created_by_channel is not null
                ) then d.initial_version_created_by_channel else d.createdByChannel end
              ) AS display_created_by_channel, (
                case when (d.modifiedByChannel = 'CP') then concat(
                  modifierCP.firstName, ' ', "modifierCP"."lastName", ' (Portal User)'
                ) 
                when (d.modifiedByChannel = 'AP') then concat(
                    modifierAP.firstName, ' ', "modifierAP"."lastName", ' (Advisors)'
                  ) 
                else concat(
                  modifierU.firstName, ' ', modifierU.lastName
                ) end
              ) AS modifier_full_name, "d"."modifiedOn", "d"."modifiedBy", "d"."modifiedByChannel", CASE WHEN d.type = 'folder' AND d.private = 1 THEN (SELECT CASE WHEN dmuau.recordId IS NOT NULL THEN 1 ELSE 0 END
                FROM document_managment_users dmuau
                WHERE dmuau.user_id =  AND dmuau.recordId = d.id) ELSE (SELECT CASE WHEN dms.private = 1 THEN (SELECT CASE WHEN dmuau.recordId IS NOT NULL THEN 1 ELSE 0 END
                FROM document_managment_users dmuau
                WHERE dmuau.user_id =  AND dmuau.recordId = dms.id) ELSE 1 END
                FROM documents_management_system dms
                WHERE dms.id = d.parent) END as is_accessible
FROM "documents_management_system" "d"
LEFT JOIN "documents_management_system" "p" ON "p"."id" = "d"."parent"
LEFT JOIN "customer_portal_users" "creatorCP" ON "creatorCP"."id" = "d"."createdBy" AND "d"."createdByChannel" = 'CP'
LEFT JOIN "user_profiles" "creatorU" ON "creatorU"."user_id" = "d"."createdBy" AND "d"."createdByChannel" NOT IN ('AP', 'CP')
LEFT JOIN "advisor_users" "creatorAP" ON "creatorAP"."id" = "d"."createdBy" AND "d"."createdByChannel" != 'AP'
LEFT JOIN "customer_portal_users" "displayIniCreatorCP" ON "displayIniCreatorCP"."id" = "d"."initial_version_created_by" AND "d"."initial_version_created_by" is not null and "d"."initial_version_created_by_channel" = 'CP'
LEFT JOIN "user_profiles" "displayIniCreatorU" ON "displayIniCreatorU"."user_id" = "d"."initial_version_created_by" AND "d"."initial_version_created_by" is not null and "d"."initial_version_created_by_channel" NOT IN ('AP', 'CP')
LEFT JOIN "advisor_users" "displayIniCreatorAP" ON "displayIniCreatorAP"."id" = "d"."initial_version_created_by" AND "d"."initial_version_created_by" is not null and "d"."initial_version_created_by_channel" = 'AP'
LEFT JOIN "customer_portal_users" "displayCreatorCP" ON "displayCreatorCP"."id" = "d"."createdBy" AND "d"."initial_version_created_by" is null and "d"."createdByChannel" = 'CP'
LEFT JOIN "user_profiles" "displayCreatorU" ON "displayCreatorU"."user_id" = "d"."createdBy" AND "d"."initial_version_created_by" is null and "d"."createdByChannel" NOT IN ('AP', 'CP')
LEFT JOIN "advisor_users" "displayCreatorAP" ON "displayCreatorAP"."id" = "d"."createdBy" AND "d"."initial_version_created_by" is null and "d"."createdByChannel" = 'AP'
LEFT JOIN "customer_portal_users" "modifierCP" ON "modifierCP"."id" = "d"."modifiedBy" AND "d"."modifiedByChannel" = 'CP'
LEFT JOIN "user_profiles" "modifierU" ON "modifierU"."user_id" = "d"."modifiedBy" AND "d"."modifiedByChannel" NOT IN ('AP', 'CP')
LEFT JOIN "advisor_users" "modifierAP" ON "modifierAP"."id" = "d"."modifiedBy" AND "d"."modifiedByChannel" = 'AP'
WHERE "d"."module" = 'conveyancing'
AND "d"."visible" = 1
AND "d"."module_record_id" = '26'
AND "p"."lineage" = '\110602'
ORDER BY "d"."type" desc, "d"."name" asc
ERROR - 2025-07-13 20:44:27 --> 404 Page Not Found: Customer-portal/conveyancing
ERROR - 2025-07-13 20:44:52 --> 404 Page Not Found: Customer-portal/conveyancing
ERROR - 2025-07-13 23:45:01 --> Severity: error --> Exception: syntax error, unexpected '?>', expecting ')' D:\documents\matrix\sheria360\ca\modules\customer-portal\app\views\conveyancing\document_item.php 7
ERROR - 2025-07-13 23:45:49 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-13 23:45:49 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\modules\customer-portal\app\controllers\Conveyancing.php 487
ERROR - 2025-07-13 23:45:49 --> Severity: error --> Exception: Call to undefined method Conveyancing::set_flashmessage() D:\documents\matrix\sheria360\ca\modules\customer-portal\app\controllers\Conveyancing.php 488
