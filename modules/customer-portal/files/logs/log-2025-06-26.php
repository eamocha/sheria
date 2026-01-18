<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-06-26 19:52:58 --> Query error: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Invalid column name 'transaction_type_id'. - Invalid query: SELECT "conveyancing_instruments".*, "instrument_type"."name" as "instrument_type", "transaction_type"."name" as "transaction_type_name", CONCAT(staff.firstName, ' ', staff.lastName) as staff, CONCAT(assignee.firstName, ' ', assignee.lastName) as assignee, CONCAT(creator.firstName, ' ', creator.lastName) as creator_name, CONCAT(modifier.firstName, ' ', modifier.lastName) as modifier_name, CONCAT('CNV-', conveyancing_instruments.id) as conveyancing_id
FROM "conveyancing_instruments"
LEFT JOIN "user_profiles" "assigned_user" ON "assigned_user"."user_id" = "conveyancing_instruments"."assignee_id"
LEFT JOIN "customer_portal_users" "requester" ON "requester"."contact_id" = "conveyancing_instruments"."initiated_by"
LEFT JOIN "conveyancing_instrument_types" as "instrument_type" ON "instrument_type"."id" = "conveyancing_instruments"."instrument_type_id"
LEFT JOIN "conveyancing_transaction_types" as "transaction_type" ON "transaction_type"."id" = "conveyancing_instruments"."transaction_type_id"
LEFT JOIN "user_profiles" "assignee" ON "assignee"."user_id" = "conveyancing_instruments"."assignee_id"
LEFT JOIN "user_profiles" "creator" ON "creator"."user_id" = "conveyancing_instruments"."createdBy"
LEFT JOIN "user_profiles" "modifier" ON "modifier"."user_id" = "conveyancing_instruments"."modifiedBy"
LEFT JOIN "contacts" "staff" ON "staff"."id" = "conveyancing_instruments"."initiated_by"
WHERE ("conveyancing_instruments"."createdBy" = 17 and "conveyancing_instruments"."channel" = 'CP' )
ORDER BY "conveyancing_instruments"."id" DESC
