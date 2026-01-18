<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-07-14 00:37:10 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-14 00:37:10 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-14 00:37:55 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-14 00:37:55 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-14 00:38:02 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Incorrect syntax near the keyword 'AND'. - Invalid query: SELECT "d"."id", "d"."type", "d"."name", "d"."extension", (case when (d.type = 'file') then concat(d.name, '.', d.extension) else d.name end) AS full_name, "d"."parent", "p"."lineage" as "parent_lineage", "d"."lineage", "d"."size", "d"."version", "d"."private", "d"."document_type_id", (SELECT count(id) FROM documents_management_system where documents_management_system.parent =d.id) children_count, "d"."document_status_id", "d"."comment", "d"."module", "d"."module_record_id", "d"."system_document", "d"."visible", "d"."visible_in_cp", "d"."visible_in_ap", "d"."createdOn", "d"."createdBy", (case when (d.createdByChannel = 'CP') then concat(creatorCP.firstName, ' ', "creatorCP"."lastName", ' (Portal User)') when (d.createdByChannel = 'AP') then concat(creatorAP.firstName, ' ', "creatorAP"."lastName", ' (Advisor)') else concat(creatorU.firstName, ' ', creatorU.lastName) end) AS creator_full_name, "d"."createdByChannel", "d"."initial_version_created_on", "d"."initial_version_created_by", "d"."initial_version_created_by_channel", (case when (d.initial_version_created_On is not null) then d.initial_version_created_On else d.createdOn end) AS display_created_on, (
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
AND "d"."module_record_id" = '10106'
AND "p"."lineage" = '\50387'
AND "d"."visible_in_cp" = 1
ORDER BY "d"."type" desc, "d"."name" asc
ERROR - 2025-07-14 00:38:45 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Cannot insert the value NULL into column 'createdBy', table 'lemis.dbo.documents_management_system'; column does not allow nulls. INSERT fails. - Invalid query: INSERT INTO "documents_management_system" ("type", "name", "extension", "parent", "lineage", "size", "version", "private", "document_type_id", "document_status_id", "comment", "module", "module_record_id", "system_document", "visible", "visible_in_cp", "visible_in_ap", "createdOn", "createdBy", "createdByChannel", "initial_version_created_on", "initial_version_created_by", "initial_version_created_by_channel", "modifiedOn", "modifiedBy", "modifiedByChannel", "is_locked", "last_locked_by", "last_locked_by_channel", "last_locked_on") VALUES ('file', '11th January 2018 - Internal Standards - Conveyancing and Legal Research', 'doc', '50387', NULL, 88064, 1, NULL, NULL, NULL, NULL, 'contract', '10106', 0, 1, 1, 0, '2025-07-14 00:38:45', NULL, 'A4L', NULL, NULL, NULL, '2025-07-14 00:38:45', NULL, 'A4L', NULL, NULL, NULL, NULL)
ERROR - 2025-07-14 00:49:46 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-14 00:49:46 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-14 00:50:31 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 00:50:31 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\modules\customer-portal\app\controllers\Conveyancing.php 487
ERROR - 2025-07-14 00:50:31 --> Severity: error --> Exception: Call to undefined method Conveyancing::set_flashmessage() D:\documents\matrix\sheria360\ca\modules\customer-portal\app\controllers\Conveyancing.php 488
ERROR - 2025-07-14 01:02:31 --> Severity: Notice --> Undefined property: Conveyancing::$dms D:\documents\matrix\sheria360\ca\modules\customer-portal\app\controllers\Conveyancing.php 483
ERROR - 2025-07-14 01:02:31 --> Severity: error --> Exception: Call to a member function download_file() on null D:\documents\matrix\sheria360\ca\modules\customer-portal\app\controllers\Conveyancing.php 483
ERROR - 2025-07-14 01:03:27 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dms.php 0
ERROR - 2025-07-14 01:03:35 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dms.php 0
ERROR - 2025-07-14 01:03:45 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dms.php 0
ERROR - 2025-07-14 01:04:20 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 01:24:20 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 01:47:23 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 01:49:32 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 02:07:36 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 02:07:44 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 02:07:52 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 02:07:55 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 02:14:19 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 02:18:14 --> Severity: Warning --> DOMXPath::query(): Invalid expression D:\documents\matrix\sheria360\ca\application\libraries\phpdocx-premium-12.5-ns\Classes\Phpdocx\Create\CreateDocxFromTemplate.php 0
ERROR - 2025-07-14 02:18:14 --> Severity: Warning --> Invalid argument supplied for foreach() D:\documents\matrix\sheria360\ca\application\libraries\phpdocx-premium-12.5-ns\Classes\Phpdocx\Create\CreateDocxFromTemplate.php 0
ERROR - 2025-07-14 13:42:28 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Incorrect syntax near the keyword 'AND'. - Invalid query: SELECT "d"."id", "d"."type", "d"."name", "d"."extension", (case when (d.type = 'file') then concat(d.name, '.', d.extension) else d.name end) AS full_name, "d"."parent", "p"."lineage" as "parent_lineage", "d"."lineage", "d"."size", "d"."version", "d"."private", "d"."document_type_id", (SELECT count(id) FROM documents_management_system where documents_management_system.parent =d.id) children_count, "d"."document_status_id", "d"."comment", "d"."module", "d"."module_record_id", "d"."system_document", "d"."visible", "d"."visible_in_cp", "d"."visible_in_ap", "d"."createdOn", "d"."createdBy", (case when (d.createdByChannel = 'CP') then concat(creatorCP.firstName, ' ', "creatorCP"."lastName", ' (Portal User)') when (d.createdByChannel = 'AP') then concat(creatorAP.firstName, ' ', "creatorAP"."lastName", ' (Advisor)') else concat(creatorU.firstName, ' ', creatorU.lastName) end) AS creator_full_name, "d"."createdByChannel", "d"."initial_version_created_on", "d"."initial_version_created_by", "d"."initial_version_created_by_channel", (case when (d.initial_version_created_On is not null) then d.initial_version_created_On else d.createdOn end) AS display_created_on, (
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
AND "d"."visible_in_cp" = 1
ORDER BY "d"."type" desc, "d"."name" asc
ERROR - 2025-07-14 11:48:17 --> 404 Page Not Found: Conveyancing/delete_document
ERROR - 2025-07-14 11:48:41 --> 404 Page Not Found: Conveyancing/delete_document
ERROR - 2025-07-14 14:53:51 --> Severity: Notice --> Undefined variable: response D:\documents\matrix\sheria360\ca\modules\customer-portal\app\controllers\Conveyancing.php 687
ERROR - 2025-07-14 14:58:21 --> Unable to load the requested class: Demsnew
ERROR - 2025-07-14 15:02:31 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 17:08:43 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 17:09:20 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-14 17:09:20 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-14 17:09:33 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-14 17:09:33 --> Severity: Notice --> Trying to access array offset on value of type bool D:\documents\matrix\sheria360\ca\modules\contract\app\models\Contract_cp_screen.php 0
ERROR - 2025-07-14 17:36:08 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 17:36:13 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 17:36:20 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
ERROR - 2025-07-14 18:37:50 --> Severity: Notice --> Undefined index: status D:\documents\matrix\sheria360\ca\application\libraries\Dmsnew.php 521
