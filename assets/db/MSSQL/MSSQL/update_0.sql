IF OBJECT_ID('dbo.contract_sla_management', 'U') IS NOT NULL DROP TABLE dbo.customer_portal_sla;
GO
CREATE TABLE contract_sla_management (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 workflow_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL,
 target nvarchar(255) NOT NULL,
 priority nvarchar(8) NOT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO

ALTER TABLE contract_sla_management
    ADD CONSTRAINT fk_contract_sla_management_1 FOREIGN KEY (workflow_id) REFERENCES contract_workflow (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

IF OBJECT_ID('dbo.contracts_sla_actions', 'U') IS NOT NULL DROP TABLE dbo.contracts_sla_actions;
GO
CREATE TABLE contracts_sla_actions (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 sla_management_id BIGINT NOT NULL,
 status_id BIGINT NOT NULL,
 type nvarchar(255) NOT NULL,
);
GO

ALTER TABLE contracts_sla_actions
    ADD CONSTRAINT fk_contracts_sla_actions_1 FOREIGN KEY (sla_management_id) REFERENCES contract_sla_management (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contracts_sla_actions
    ADD CONSTRAINT fk_contracts_sla_actions_2 FOREIGN KEY (status_id) REFERENCES contract_status (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO


IF OBJECT_ID('dbo.contracts_sla', 'U') IS NOT NULL DROP TABLE dbo.contracts_sla;
GO
CREATE TABLE contracts_sla (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 sla_management_id BIGINT NOT NULL,
 contract_id BIGINT NOT NULL,
 cycle BIGINT DEFAULT NULL,
 action nvarchar(255) DEFAULT NULL,
 actionDate datetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL,
 modifiedByChannel nvarchar(3) DEFAULT NULL
);
GO

ALTER TABLE contracts_sla
    ADD CONSTRAINT fk_contracts_sla_1 FOREIGN KEY (sla_management_id) REFERENCES contract_sla_management (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contracts_sla
    ADD CONSTRAINT fk_contracts_sla_2 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO


IF OBJECT_ID('dbo.contract_parties_sla', 'U') IS NOT NULL DROP TABLE dbo.contract_parties_sla;
GO
CREATE TABLE contract_parties_sla (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 sla_management_id BIGINT NOT NULL,
 party_id BIGINT NOT NULL,
);
GO

ALTER TABLE contract_parties_sla
    ADD CONSTRAINT fk_contract_parties_sla_1 FOREIGN KEY (sla_management_id) REFERENCES contract_sla_management (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contract_parties_sla
    ADD CONSTRAINT fk_contract_parties_sla_2 FOREIGN KEY (party_id) REFERENCES party (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

IF OBJECT_ID('dbo.contracts_type_sla', 'U') IS NOT NULL DROP TABLE dbo.contracts_type_sla;
GO
CREATE TABLE contracts_type_sla (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 sla_management_id BIGINT NOT NULL,
 type_id BIGINT NOT NULL,
);
GO

ALTER TABLE contracts_type_sla
    ADD CONSTRAINT fk_contracts_type_sla_1 FOREIGN KEY (sla_management_id) REFERENCES contract_sla_management (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contracts_type_sla
    ADD CONSTRAINT fk_contracts_type_sla_2 FOREIGN KEY (type_id) REFERENCES contract_type (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO


IF OBJECT_ID('dbo.contract_sla_notification', 'U') IS NOT NULL DROP TABLE dbo.contract_sla_notification;
GO
CREATE TABLE contract_sla_notification (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  sla_management_id BIGINT NOT NULL,
  contract_id BIGINT NOT NULL,
  notified TINYINT NOT NULL DEFAULT '0'
);
GO

ALTER TABLE contract_sla_notification
    ADD CONSTRAINT fk_contract_sla_notification_1 FOREIGN KEY (sla_management_id) REFERENCES contract_sla_management (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contract_sla_notification
    ADD CONSTRAINT fk_contract_sla_notification_2 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE tasks ADD title nvarchar(255) NOT NULL DEFAULT '';
GO

IF OBJECT_ID('dbo.tasks_detailed_view', 'V') IS NOT NULL DROP VIEW dbo.tasks_detailed_view
GO
CREATE VIEW tasks_detailed_view AS SELECT TOP(9223372036854775800)
 tasks.id, ('T' + CAST(tasks.id as nvarchar)) as taskId, tasks.title,
 CASE WHEN tasks.legal_case_id IS NULL THEN '' ELSE ('M' + CAST(tasks.legal_case_id AS nvarchar)) END as caseId,
 tasks.legal_case_id, tasks.user_id, tasks.due_date,tasks.assigned_to as assignedToId,tasks.reporter as reportedById,
 tasks.private, tasks.priority, tasks.task_location_id AS task_location_id,
 task_locations.name AS location, tasks.description as taskFullDescription, tasks.task_status_id, tasks.task_type_id, tasks.estimated_effort,
 CAST(tasks.createdOn as DATE) as createdOn, CAST(tasks.modifiedOn as DATE) as modifiedOn, tasks.modifiedBy, tasks.archived, tee.effectiveEffort,
 (assigned.firstName + ' ' + assigned.lastName) as assigned_to,
 (reporter.firstName + ' ' + reporter.lastName) as reporter,
 (created.firstName + ' ' + created.lastName) as createdBy, (modified.firstName + ' ' + modified.lastName) as modifiedByName, tasks.createdBy as createdById, ts.name as taskStatus, tasks.archived as archivedTasks,
 SUBSTRING(tasks.description, 1, 50) as description, SUBSTRING(lg.subject, 1, 50) as caseSubject, lg.subject as caseFullSubject, lg.category as caseCategory,
 tasks.contract_id, contract.name as contract_name,
assigned.status as assignee_status,reporter.status as reporter_status,created.status as creator_status, modified.status as modifier_status, tasks.stage,
contributors = STUFF((SELECT ', ' + (contr.firstName + ' ' + contr.lastName) FROM user_profiles AS contr INNER JOIN task_contributors ON tasks.id = task_contributors.task_id  AND contr.user_id = task_contributors.user_id FOR XML PATH('')), 1, 1, '')
FROM tasks
LEFT JOIN user_profiles assigned ON assigned.user_id = tasks.assigned_to
LEFT JOIN user_profiles reporter ON reporter.user_id = tasks.reporter
LEFT JOIN user_profiles created ON created.user_id = tasks.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = tasks.modifiedBy
LEFT JOIN task_statuses ts ON ts.id = tasks.task_status_id
LEFT JOIN legal_cases as lg ON lg.id = tasks.legal_case_id
LEFT JOIN contract ON contract.id = tasks.contract_id
LEFT JOIN task_effective_effort AS tee ON tee.task_id = tasks.id
LEFT JOIN task_locations ON task_locations.id = tasks.task_location_id
where tasks.legal_case_id is null or lg.isDeleted = 0;
GO

IF OBJECT_ID('dbo.user_activity_logs_full_details', 'V') IS NOT NULL DROP VIEW dbo.user_activity_logs_full_details;
GO
CREATE VIEW user_activity_logs_full_details AS
SELECT ual.id, ual.user_id, ual.task_id, ual.legal_case_id, ual.effectiveEffort, ual.createdBy, CAST(ual.createdOn as DATE) as createdOn, CAST(ual.modifiedOn as DATE) as modifiedOn, ual.rate as rate, ual.rate_system as rate_system,
       CASE WHEN ual.task_id IS NULL THEN '' ELSE ( 'T' + CAST( ual.task_id AS nvarchar ) ) END as taskId,
       tasks.title as task_title,
       CASE WHEN Datalength(tasks.description) > 63 THEN (SUBSTRING(tasks.description, 1, 63) + ' ' + '...' ) ELSE tasks.description END as taskSummary,
       tasks.description as task_full_summary,
       CASE WHEN ual.legal_case_id IS NULL THEN '' ELSE ( 'M' + CAST( ual.legal_case_id AS nvarchar ) ) END as legalCaseId,
       CASE WHEN Datalength(legal_cases.subject) > 63 THEN ( SUBSTRING(legal_cases.subject, 1, 63) + ' ' + '...' ) ELSE legal_cases.subject END as legalCaseSummary,
       CASE WHEN Datalength(legal_cases.description) > 63 THEN ( SUBSTRING(legal_cases.description, 1, 63) + ' ' + '...' ) ELSE legal_cases.description END as legalCaseDescription,
       legal_cases.category as caseCategory, legal_cases.internalReference as matterInternalReference, legal_cases.provider_group_id as provider_group_id, 
       CASE WHEN worker.status = 'inactive' THEN ( worker.firstName + ' ' + worker.lastName + '(Inactive)' ) ELSE ( worker.firstName + ' ' + worker.lastName ) END as worker,
       CASE WHEN inserter.status = 'inactive' THEN ( inserter.firstName + ' ' + inserter.lastName + '(Inactive)' ) ELSE ( inserter.firstName + ' ' + inserter.lastName ) END as inserter,
       seniorityLevels.name  as seniorityLevel,seniorityLevels.id as seniorityLevelId, ual.comments as comments, ual.logDate as logDate, time_types.id timeTypeId, time_internal_statuses.id timeInternalStatusId, ual.timeStatus,
       CASE WHEN modified.status = 'inactive' THEN ( modified.firstName + ' ' + modified.lastName + '(Inactive)' ) ELSE ( modified.firstName + ' ' + modified.lastName ) END as modifiedByName,
       CASE WHEN ualis.log_invoicing_statuses IS NULL AND ual.timeStatus='internal' THEN '-' WHEN ualis.log_invoicing_statuses IS NULL AND ual.timeStatus <> 'internal' THEN 'to-invoice' ELSE ualis.log_invoicing_statuses END as billingStatus,
       cv.id as clientId, cv.name as clientName, cv2.name as allRecordsClientName, contacts.firstName + ' ' + contacts.lastName as requestedBy
FROM user_activity_logs as ual
         LEFT JOIN tasks ON tasks.id = ual.task_id
         LEFT JOIN legal_cases ON legal_cases.id = ual.legal_case_id
         LEFT JOIN user_profiles worker ON worker.user_id = ual.user_id
         LEFT JOIN seniority_levels seniorityLevels ON seniorityLevels.id = worker.seniority_level_id
         LEFT JOIN user_profiles inserter ON inserter.user_id = ual.createdBy
         LEFT JOIN user_profiles modified ON modified.user_id = ual.modifiedBy
         LEFT JOIN time_types ON time_types.id = ual.time_type_id
         LEFT JOIN time_internal_statuses ON time_internal_statuses.id = ual.time_internal_status_id
         LEFT JOIN user_activity_log_invoicing_statuses ualis ON ualis.id = ual.id
         LEFT JOIN clients_view cv ON cv.id = ual.client_id AND cv.model = 'clients'
         LEFT JOIN clients_view cv2 ON cv2.id = legal_cases.client_id AND cv2.model = 'clients'
         LEFT JOIN contacts ON contacts.id = legal_cases.requestedBy
Where legal_cases.isDeleted = 0 or ual.legal_case_id is null;
GO

SET IDENTITY_INSERT money_dashboard_widgets_types ON
INSERT INTO money_dashboard_widgets_types (id, name, type, settings) VALUES
(12, 'revenue_per_status', 'pieChart', 'a:2:{s:7:"columns";a:5:{i:0;s:4:"open";i:1;s:7:"overdue";i:2;s:14:"partially paid";i:3;s:4:"paid";i:4;s:5:"draft";}s:11:"filter_type";s:4:"date";}'),
(13, 'bills_per_status', 'pieChart', 'a:2:{s:7:"columns";a:4:{i:0;s:4:"open";i:1;s:7:"overdue";i:2;s:14:"partially paid";i:3;s:4:"paid";}s:11:"filter_type";s:4:"date";}'),
(14, 'expenses_per_status', 'pieChart', 'a:2:{s:7:"columns";a:4:{i:0;s:4:"open";i:1;s:14:"needs_revision";i:2;s:9:"cancelled";i:3;s:8:"approved";}s:11:"filter_type";s:4:"date";}'),
(15, 'top_20_receivables_by_due_date', 'barChart', 'a:1:{s:11:"filter_type";s:4:"date";}'),
(16, 'top_20_payables_by_due_date', 'barChart', 'a:1:{s:11:"filter_type";s:4:"date";}');
SET IDENTITY_INSERT money_dashboard_widgets_types OFF
GO

INSERT INTO money_dashboard_widgets (title, filter, widget_order, money_dashboard_id, money_dashboard_widgets_type_id) VALUES
('Revenue per Status', 'a:7:{s:7:"columns";a:5:{i:0;s:4:"open";i:1;s:7:"overdue";i:2;s:14:"partially paid";i:3;s:4:"paid";i:4;s:5:"draft";}s:11:"filter_type";s:4:"date";s:4:"date";s:2:"ty";s:8:"operator";s:7:"between";s:13:"specific_date";s:8:"2022-2-3";s:9:"from_date";s:10:"2022-01-01";s:7:"to_date";s:10:"2022-12-31";}', (select isnull(max(widget_order),0) + 2 from money_dashboard_widgets), 1, 12);
GO

INSERT INTO money_dashboard_widgets (title,filter, widget_order, money_dashboard_id, money_dashboard_widgets_type_id) VALUES
('Bills per Status', 'a:7:{s:7:"columns";a:4:{i:0;s:4:"open";i:1;s:7:"overdue";i:2;s:14:"partially paid";i:3;s:4:"paid";}s:11:"filter_type";s:4:"date";s:4:"date";s:2:"ty";s:8:"operator";s:7:"between";s:13:"specific_date";s:8:"2022-2-3";s:9:"from_date";s:10:"2022-01-01";s:7:"to_date";s:10:"2022-12-31";}', (select isnull(max(widget_order),0) + 3 from money_dashboard_widgets), 1, 13);
GO

INSERT INTO money_dashboard_widgets (title, filter, widget_order, money_dashboard_id, money_dashboard_widgets_type_id) VALUES
('Expenses per Status', 'a:7:{s:7:"columns";a:4:{i:0;s:4:"open";i:1;s:14:"needs_revision";i:2;s:9:"cancelled";i:3;s:8:"approved";}s:11:"filter_type";s:4:"date";s:4:"date";s:2:"ty";s:8:"operator";s:7:"between";s:13:"specific_date";s:8:"2022-2-3";s:9:"from_date";s:10:"2022-01-01";s:7:"to_date";s:10:"2022-12-31";}', (select isnull(max(widget_order),0) + 4 from money_dashboard_widgets), 1, 14);
GO

INSERT INTO money_dashboard_widgets (title, filter, widget_order, money_dashboard_id, money_dashboard_widgets_type_id) VALUES
('Top 20 Receivables by Due Date', 'a:6:{s:11:"filter_type";s:4:"date";s:4:"date";s:2:"ty";s:8:"operator";s:7:"between";s:13:"specific_date";s:8:"2022-2-3";s:9:"from_date";s:10:"2022-01-01";s:7:"to_date";s:10:"2022-12-31";}', (select isnull(max(widget_order),0) + 5 from money_dashboard_widgets), 1, 15);
GO

INSERT INTO money_dashboard_widgets (title, filter, widget_order, money_dashboard_id, money_dashboard_widgets_type_id) VALUES
('Top 20 Payables by Due Date', 'a:6:{s:11:"filter_type";s:4:"date";s:4:"date";s:2:"ty";s:8:"operator";s:7:"between";s:13:"specific_date";s:8:"2022-2-3";s:9:"from_date";s:10:"2022-01-01";s:7:"to_date";s:10:"2022-12-31";}', (select isnull(max(widget_order),0) + 6 from money_dashboard_widgets), 1, 16);
GO

ALTER TABLE languages ADD display_name TEXT;
GO
UPDATE languages SET display_name = 'English' WHERE id = 1;
UPDATE languages SET display_name = 'العربية' WHERE id = 2;
UPDATE languages SET display_name = 'Française' WHERE id = 3;
UPDATE languages SET display_name = 'Española' WHERE id = 4;
GO
IF OBJECT_ID('dbo.money_dashboard_widgets_title_languages', 'U') IS NOT NULL DROP TABLE dbo.money_dashboard_widgets_title_languages;
GO
CREATE TABLE money_dashboard_widgets_title_languages (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  widget_id BIGINT FOREIGN KEY REFERENCES money_dashboard_widgets(id),
  language_id BIGINT FOREIGN KEY REFERENCES languages(id),
  title nvarchar(255) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.external_user_tokens', 'U') IS NOT NULL DROP TABLE dbo.external_user_tokens;
GO
CREATE TABLE external_user_tokens (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    external_user_token nvarchar(255),
    created_on datetime DEFAULT NULL,
);
GO

IF OBJECT_ID('dbo.external_share_documents', 'U') IS NOT NULL DROP TABLE dbo.external_share_documents;
GO
CREATE TABLE external_share_documents (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    token_id BIGINT NOT NULL,
    document_id BIGINT NOT NULL,
    share_type nvarchar(255),
    external_user_email nvarchar(255),
    otp nvarchar(255) DEFAULT NULL,
    otp_generated_on datetime DEFAULT NULL,
    otp_verification_failed BIGINT DEFAULT 0,
);
GO

IF OBJECT_ID('dbo.external_approvals', 'U') IS NOT NULL DROP TABLE dbo.external_approvals;
GO
CREATE TABLE external_approvals (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    token_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    user_type nvarchar(255),
    approval_status_id BIGINT NOT NULL,
);
GO

ALTER TABLE contract_approval_history ADD done_by_ip nvarchar(50) NULL DEFAULT NULL;
ALTER TABLE contract_approval_history ADD approval_channel nvarchar(50) NULL DEFAULT 'A4L';
GO

UPDATE email_notifications_scheme SET notify_cc = '' WHERE trigger_action='needs_approval';

IF OBJECT_ID('dbo.approval_signature_documents', 'U') IS NOT NULL DROP TABLE dbo.approval_signature_documents;
GO
CREATE TABLE approval_signature_documents (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    document_id BIGINT NOT NULL,
    to_be_approved TINYINT DEFAULT 0,
    to_be_signed TINYINT DEFAULT 0,
) ;

ALTER TABLE contract_workflow_status_relation ADD approval_start_point TINYINT NULL DEFAULT '0';
GO

UPDATE system_preferences SET keyValue = 1 WHERE keyName = 'cpContactCategory';

ALTER TABLE terms ADD number_of_days BIGINT DEFAULT 0;
GO
update terms set number_of_days = 0;
GO

ALTER TABLE clients ADD term_id BIGINT DEFAULT NULL; 
ALTER TABLE clients ADD discount_percentage BIGINT DEFAULT 0; 
GO

IF OBJECT_ID('dbo.clients_view', 'V') IS NOT NULL DROP VIEW dbo.clients_view;
GO
CREATE VIEW clients_view AS select clients.id AS id, clients.term_id AS term_id, clients.discount_percentage AS discount_percentage,
    CASE WHEN clients.company_id IS NULL THEN con.tax_number ELSE com.registrationTaxNb END AS tax_number,
    CASE WHEN clients.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS name,
    CASE WHEN clients.company_id IS NULL THEN isnull(con.foreignFirstName, '') + ' ' + isnull(con.foreignLastName, '') ELSE com.foreignName END AS foreignName,
    CASE WHEN clients.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS member_name,
    CASE WHEN clients.company_id IS NULL THEN 'Person' ELSE 'Company' END AS type,
    CASE WHEN clients.company_id IS NULL THEN ( '' + clients.contact_id ) ELSE ( '' + clients.company_id ) END AS member_id,
    'clients' AS model, clients.createdBy, clients.createdOn, clients.modifiedBy, clients.modifiedOn, ( created.firstName + ' ' + created.lastName) as createdByName,
    ( modified.firstName + ' ' + modified.lastName) as modifiedByName, NULL AS isThirdParty
from (((clients left join companies com on((com.id = clients.company_id)))
    left join contacts con on((con.id = clients.contact_id))
    left join user_profiles created on((created.user_id = clients.createdBy))
    left join user_profiles modified on((modified.user_id = clients.modifiedBy)))) 
UNION select vendors.id AS id, null AS term_id, 0 AS discount_percentage,
    CASE WHEN vendors.company_id IS NULL THEN con.tax_number ELSE com.registrationTaxNb END AS tax_number,
    CASE WHEN vendors.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS name,
    CASE WHEN vendors.company_id IS NULL THEN  con.foreignFirstName+' '+con.foreignLastName ELSE com.foreignName END AS foreignName,
    CASE WHEN vendors.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS member_name,
    CASE WHEN vendors.company_id IS NULL THEN 'Person' ELSE 'Company' END AS type,
    CASE WHEN vendors.company_id IS NULL THEN ( '' + vendors.contact_id ) ELSE ( '' + vendors.company_id ) END AS member_id,'suppliers' as model,
    vendors.createdBy, vendors.createdOn, vendors.modifiedBy, vendors.modifiedOn, ( created.firstName + ' ' + created.lastName) as createdByName, ( modified.firstName + ' ' + modified.lastName) as modifiedByName, NULL AS isThirdParty 
from (((vendors left join companies com on((com.id = vendors.company_id)))
    left join contacts con on((con.id = vendors.contact_id))
    left join user_profiles created on((created.user_id = vendors.createdBy))
    left join user_profiles modified on((modified.user_id = vendors.modifiedBy))))
UNION select partners.id AS id, null AS term_id, 0 AS discount_percentage,null,CASE WHEN partners.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS name,
    CASE WHEN partners.company_id IS NULL THEN  con.foreignFirstName+' '+con.foreignLastName ELSE com.foreignName END AS foreignName,
    CASE WHEN partners.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS member_name,
    CASE WHEN partners.company_id IS NULL THEN 'Person' ELSE 'Company' END AS type,
    CASE WHEN partners.company_id IS NULL THEN ( '' + partners.contact_id ) ELSE ( '' + partners.company_id ) END AS member_id,'partners' as model,
    partners.createdBy, partners.createdOn, partners.modifiedBy, partners.modifiedOn, ( created.firstName + ' ' + created.lastName) as createdByName,
    ( modified.firstName + ' ' + modified.lastName) as modifiedByName, partners.isThirdParty AS isThirdParty 
from (((partners 
    left join companies com on((com.id = partners.company_id))) 
    left join contacts con on((con.id = partners.contact_id)) 
    left join user_profiles created on((created.user_id = partners.createdBy)) 
    left join user_profiles modified on((modified.user_id = partners.modifiedBy))))
;
GO
INSERT INTO system_preferences (groupName, keyName, keyValue) VALUES
('ExpensesValues', 'notifyUsersExpenseByEmail', '0'),
('ExpensesValues', 'notifyUsersGroupToApproveExpense', NULL),
('ExpensesValues', 'notifyUsersToApproveExpense', NULL);
GO

INSERT INTO email_notifications_scheme (trigger_action, notify_to, notify_cc, createdBy, createdOn, modifiedBy, modifiedOn) VALUES
('add_expense', 'expense_users', '', '1', CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP);
GO

INSERT INTO email_notifications_scheme (trigger_action, notify_to, notify_cc, createdBy, createdOn, modifiedBy, modifiedOn) VALUES ('add_user', 'invitees', '', '1', CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP);
Go

ALTER TABLE organization_invoice_templates ADD is_default TINYINT NOT NULL DEFAULT '0'; 
Go

ALTER TABLE invoice_headers ADD invoice_template_id BIGINT NULL DEFAULT NULL; 
ALTER TABLE invoice_headers ADD CONSTRAINT invoice_template_id FOREIGN KEY (invoice_template_id) REFERENCES organization_invoice_templates (id);
GO

ALTER TABLE invoice_details
ALTER COLUMN itemDescription TEXT NULL;
GO

ALTER TABLE credit_note_details
ALTER COLUMN item_description TEXT NULL;
GO

INSERT INTO instance_data (keyName, keyValue) VALUES ('country_id', NULL);
GO

CREATE TABLE [dbo].[trigger_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [varchar](50) NOT NULL,
 CONSTRAINT [PK_trigger_types] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

CREATE TABLE [dbo].[trigger_action_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [varchar](50) NOT NULL,
 CONSTRAINT [PK_trigger_action_types] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

CREATE TABLE [dbo].[triggers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[trigger_type_id] [bigint] NOT NULL,
	[source_id] [bigint] NULL,
	[created_on] [datetime] NULL,
	[created_by] [bigint] NOT NULL,
	[modified_on] [datetime] NULL,
	[modified_by] [bigint] NOT NULL,
 CONSTRAINT [PK_triggers] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[triggers]  WITH CHECK ADD  CONSTRAINT [FK_triggers_trigger_types] FOREIGN KEY([trigger_type_id])
REFERENCES [dbo].[trigger_types] ([id])
GO

ALTER TABLE [dbo].[triggers] CHECK CONSTRAINT [FK_triggers_trigger_types]
GO

ALTER TABLE [dbo].[triggers]  WITH CHECK ADD  CONSTRAINT [FK_triggers_users_1] FOREIGN KEY([created_by])
REFERENCES [dbo].[users] ([id])
GO

ALTER TABLE [dbo].[triggers] CHECK CONSTRAINT [FK_triggers_users_1]
GO

ALTER TABLE [dbo].[triggers]  WITH CHECK ADD  CONSTRAINT [FK_triggers_users_2] FOREIGN KEY([modified_by])
REFERENCES [dbo].[users] ([id])
GO

ALTER TABLE [dbo].[triggers] CHECK CONSTRAINT [FK_triggers_users_2]
GO

CREATE TABLE [dbo].[trigger_actions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[trigger_id] [bigint] NOT NULL,
	[trigger_action_type_id] [bigint] NOT NULL,
	[created_by] [bigint] NOT NULL,
	[created_on] [datetime] NULL,
	[modified_by] [bigint] NOT NULL,
	[modified_on] [datetime] NULL,
 CONSTRAINT [PK_trigger_actions] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[trigger_actions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_actions_trigger_action_types] FOREIGN KEY([trigger_action_type_id])
REFERENCES [dbo].[trigger_action_types] ([id])
GO

ALTER TABLE [dbo].[trigger_actions] CHECK CONSTRAINT [FK_trigger_actions_trigger_action_types]
GO

ALTER TABLE [dbo].[trigger_actions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_actions_triggers] FOREIGN KEY([trigger_id])
REFERENCES [dbo].[triggers] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO

ALTER TABLE [dbo].[trigger_actions] CHECK CONSTRAINT [FK_trigger_actions_triggers]
GO

ALTER TABLE [dbo].[trigger_actions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_actions_users_1] FOREIGN KEY([modified_by])
REFERENCES [dbo].[users] ([id])
GO

ALTER TABLE [dbo].[trigger_actions] CHECK CONSTRAINT [FK_trigger_actions_users_1]
GO

ALTER TABLE [dbo].[trigger_actions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_actions_users_2] FOREIGN KEY([created_by])
REFERENCES [dbo].[users] ([id])
GO

ALTER TABLE [dbo].[trigger_actions] CHECK CONSTRAINT [FK_trigger_actions_users_2]
GO


CREATE TABLE [dbo].[trigger_matter_workflow_conditions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[trigger_id] [bigint] NOT NULL,
	[from_stage] [bigint] NOT NULL,
	[to_stage] [bigint] NOT NULL,
	[area_of_practice] [bigint] NOT NULL,
 CONSTRAINT [PK_trigger_matter_workflow_conditions] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[trigger_matter_workflow_conditions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_matter_workflow_conditions_case_types] FOREIGN KEY([area_of_practice])
REFERENCES [dbo].[case_types] ([id])
GO

ALTER TABLE [dbo].[trigger_matter_workflow_conditions] CHECK CONSTRAINT [FK_trigger_matter_workflow_conditions_case_types]
GO

ALTER TABLE [dbo].[trigger_matter_workflow_conditions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_matter_workflow_conditions_trigger_matter_workflow_conditions_workflow_status_1] FOREIGN KEY([from_stage])
REFERENCES [dbo].[workflow_status] ([id])
GO

ALTER TABLE [dbo].[trigger_matter_workflow_conditions] CHECK CONSTRAINT [FK_trigger_matter_workflow_conditions_trigger_matter_workflow_conditions_workflow_status_1]
GO

ALTER TABLE [dbo].[trigger_matter_workflow_conditions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_matter_workflow_conditions_trigger_matter_workflow_conditions_workflow_status_2] FOREIGN KEY([to_stage])
REFERENCES [dbo].[workflow_status] ([id])
GO

ALTER TABLE [dbo].[trigger_matter_workflow_conditions] CHECK CONSTRAINT [FK_trigger_matter_workflow_conditions_trigger_matter_workflow_conditions_workflow_status_2]
GO

ALTER TABLE [dbo].[trigger_matter_workflow_conditions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_matter_workflow_conditions_triggers] FOREIGN KEY([trigger_id])
REFERENCES [dbo].[triggers] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO

ALTER TABLE [dbo].[trigger_matter_workflow_conditions] CHECK CONSTRAINT [FK_trigger_matter_workflow_conditions_triggers]
GO


CREATE TABLE [dbo].[trigger_action_task_values](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[action_id] [bigint] NOT NULL,
	[due_date] [datetime] NULL,
	[task_type] [bigint] NOT NULL,
	[assigned_to] [bigint] NULL,
	[description] [varchar](50) NULL,
	[assigned_to_matter] [varchar](50) NULL,
 CONSTRAINT [PK_trigger_action_task_values] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[trigger_action_task_values]  WITH CHECK ADD  CONSTRAINT [FK_trigger_action_task_values_task_types] FOREIGN KEY([task_type])
REFERENCES [dbo].[task_types] ([id])
GO

ALTER TABLE [dbo].[trigger_action_task_values] CHECK CONSTRAINT [FK_trigger_action_task_values_task_types]
GO

ALTER TABLE [dbo].[trigger_action_task_values]  WITH CHECK ADD  CONSTRAINT [FK_trigger_action_task_values_trigger_actions] FOREIGN KEY([action_id])
REFERENCES [dbo].[trigger_actions] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO

ALTER TABLE [dbo].[trigger_action_task_values] CHECK CONSTRAINT [FK_trigger_action_task_values_trigger_actions]
GO

ALTER TABLE [dbo].[trigger_action_task_values]  WITH CHECK ADD  CONSTRAINT [FK_trigger_action_task_values_users] FOREIGN KEY([assigned_to])
REFERENCES [dbo].[users] ([id])
GO

ALTER TABLE [dbo].[trigger_action_task_values] CHECK CONSTRAINT [FK_trigger_action_task_values_users]
GO



INSERT INTO [dbo].[trigger_action_types]
           ([name])
     VALUES
           ('trigger_add_task'),
		   ('trigger_add_reminder'),
		   ('trigger_send_email')
GO

INSERT INTO [dbo].[trigger_types]
           ([name])
     VALUES
           ('matter_status_transitions')
GO

/* Modify Task Types Languages Start */
/* Spansih Start */
UPDATE task_types_languages SET name = 'Por hacer' WHERE name = 'Tareas' AND language_id = 4;
UPDATE task_types_languages SET name = 'Reunión con el cliente' WHERE name = 'Reunión con la clienta' AND language_id = 4;
UPDATE task_types_languages SET name = 'Preparar un memorando' WHERE name = 'preparar un memorando' AND language_id = 4;
UPDATE task_types_languages SET name = 'Modificación del Registro Mercantil' WHERE name = 'Modificación del registro comercial' AND language_id = 4;
UPDATE task_types_languages SET name = 'Enmienda del Consejo de Administración' WHERE name = 'Enmienda de la Junta de Directores' AND language_id = 4;
UPDATE task_types_languages SET name = 'Presentar una solicitud en línea' WHERE name = 'Enviar solicitud en línea' AND language_id = 4;
UPDATE task_types_languages SET name = 'Presentar una solicitud de compensación' WHERE name = 'Enviar una solicitud de compensación' AND language_id = 4;
UPDATE task_types_languages SET name = 'Opinión legal' WHERE name = 'opinión legal' AND language_id = 4;
UPDATE task_types_languages SET name = 'Asuntos relacionados a los RRHH' WHERE name = 'recursos humanos relacionados' AND language_id = 4;
UPDATE task_types_languages SET name = 'Revisión de contratos' WHERE name = 'Revisión de contrato' AND language_id = 4;
UPDATE task_types_languages SET name = 'Solicitud de un billete para un viaje de negocios' WHERE name = 'Solicitar un boleto de viaje de negocios' AND language_id = 4;
/* Spanish End */
/* French Start */
UPDATE task_types_languages SET name = 'Préparer un mémorandum' WHERE name = 'préparer un mémorandum' AND language_id = 3;
UPDATE task_types_languages SET name = 'Opinion légale' WHERE name = 'opinion légale' AND language_id = 3;
UPDATE task_types_languages SET name = 'Liées aux ressources humaines' WHERE name = 'liées aux ressources humaines' AND language_id = 3;
/* French End */
/* Modify Task Types Languages End */

ALTER TABLE customer_portal_users ADD flag_change_password TINYINT NULL DEFAULT '0';
GO

ALTER TABLE trigger_action_task_values ADD title nvarchar(255) NOT NULL DEFAULT '';
GO

ALTER TABLE contract ALTER COLUMN requester_id BIGINT NULL;

INSERT INTO system_preferences (groupName, keyName, keyValue) VALUES
('ContractDefaultValues', 'AllowContractSLAManagement', 'yes');
GO

UPDATE oauth_clients SET redirect = 'https://zapier.com/dashboard/auth/oauth/return/AppFourLegalCLIAPI/' WHERE id = 4;

IF OBJECT_ID('dbo.reminders_full_details', 'V') IS NOT NULL DROP VIEW dbo.reminders_full_details;
GO
CREATE VIEW reminders_full_details AS
SELECT reminders.id, reminders.user_id, reminders.summary, reminders.reminder_type_id, reminders.remindDate,
       reminders.remindTime, reminders.status, reminders.legal_case_id, reminders.company_id, reminders.contact_id,
       reminders.task_id, CAST( reminders.createdOn AS date ) AS createdOn, reminders.modifiedOn, reminders.createdBy,created.status as createdByStatus,
       (modified.firstName + ' ' + modified.lastName) AS modifiedByName, (created.firstName + ' ' + created.lastName) AS createdByName,
       reminders.parent_id, ( 'R' + CAST( reminders.id AS nvarchar ) ) as reminderID, legal_cases.subject as legal_case,
       ( CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END ) as contact, companies.name as company,
       tasks.title as task, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as remindUser ,user_profiles.status as user_status,
       ( 'M' + CAST( legal_cases.id AS nvarchar ) ) as legalCaseId,reminders.notify_before_time,reminders.notify_before_time_type,
       ( 'T' + CAST( tasks.id AS nvarchar ) ) as taskId, reminders.contract_id, contract.name as contract_name
FROM reminders LEFT JOIN legal_cases ON legal_cases.id = reminders.legal_case_id
               LEFT JOIN contract ON contract.id = reminders.contract_id
               LEFT JOIN companies ON companies.id = reminders.company_id
               LEFT JOIN contacts ON contacts.id = reminders.contact_id
               LEFT JOIN tasks ON tasks.id = reminders.task_id
               LEFT JOIN user_profiles ON user_profiles.user_id = reminders.user_id
               LEFT JOIN user_profiles created ON created.user_id = reminders.createdBy
               LEFT JOIN user_profiles modified ON modified.user_id = reminders.modifiedBy
Where legal_cases.isDeleted = 0 or reminders.legal_case_id IS NULL;
GO