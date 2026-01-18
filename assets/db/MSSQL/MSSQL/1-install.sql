-- Tables

IF OBJECT_ID('dbo.app_modules', 'U') IS NOT NULL DROP TABLE dbo.app_modules;
GO

CREATE TABLE app_modules (
 module varchar(255) NOT NULL DEFAULT '',
 controller varchar(255) NOT NULL,
 action varchar(255) NOT NULL DEFAULT '',
 alias varchar(255) NOT NULL DEFAULT '',
 PRIMARY KEY (module,controller,action)
) ;

IF OBJECT_ID('dbo.audit_log_details', 'U') IS NOT NULL DROP TABLE dbo.audit_log_details;
GO
IF OBJECT_ID('dbo.audit_logs', 'U') IS NOT NULL DROP TABLE dbo.audit_logs;
GO

CREATE TABLE audit_logs (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 user_id BIGINT NOT NULL,
 model varchar(255) NOT NULL,
 action varchar(255) NOT NULL,
 recordId BIGINT NOT NULL,
 created smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);
GO
--docs_documents;

CREATE TABLE docs_documents(
	id bigint NOT NULL IDENTITY,
	docs_document_status_id bigint NOT NULL,
	docs_document_type_id bigint NOT NULL,
	name varchar (255) NULL,
	path varchar (255) NULL,
	pathType varchar(255) NULL,
	comments text NULL,
	createdOn datetime NULL,
	createdBy bigint NULL,
	modifiedOn datetime NULL,
	modifiedBy bigint NULL
);
GO
--docs_document_types;

CREATE TABLE docs_document_types (
 id bigint  NOT NULL IDENTITY,
 name varchar(255) NOT NULL,
 PRIMARY KEY (id)
);
GO
--docs_document_statuses;

CREATE TABLE docs_document_statuses (
 id bigint  NOT NULL IDENTITY,
 name varchar(255) NOT NULL,
 PRIMARY KEY (id)
);
GO

--audit_log_details;
CREATE TABLE audit_log_details (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 log_id BIGINT NOT NULL,
 dataBefor TEXT NOT NULL,
 dataAfter TEXT NOT NULL
);
GO
ALTER TABLE audit_log_details
 ADD CONSTRAINT fk_audit_log_details_audit_logs1 FOREIGN KEY (log_id) REFERENCES audit_logs (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

--###
IF OBJECT_ID('dbo.board_members', 'U') IS NOT NULL DROP TABLE dbo.board_members;
GO

--board_members;
CREATE TABLE board_members (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 company_id BIGINT NOT NULL,
 member_id BIGINT NOT NULL,
 board_member_role_id BIGINT NOT NULL,
 designatedOn date NOT NULL,
 tillDate date DEFAULT NULL,
 comments TEXT,
 permanentRepresentation nvarchar(7) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.board_member_roles', 'U') IS NOT NULL DROP TABLE dbo.board_member_roles;
GO

--board_member_roles;
CREATE TABLE board_member_roles (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL
);
GO


IF OBJECT_ID('dbo.case_comments', 'U') IS NOT NULL DROP TABLE dbo.case_comments;
GO

--case_comments;
CREATE TABLE case_comments (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 case_id BIGINT NOT NULL,
 comment text NOT NULL,
 createdOn datetime2(0) NOT NULL,
 user_id BIGINT NOT NULL,
 modifiedBy BIGINT NOT NULL,
 createdByChannel nvarchar(3) DEFAULT NULL,
 modifiedByChannel nvarchar(3) DEFAULT NULL,
 isVisibleToCP CHAR( 1 ) NULL DEFAULT '0',
 isVisibleToAP CHAR( 1 ) NULL DEFAULT '0'
);
GO

IF OBJECT_ID('dbo.case_comment_attachments', 'U') IS NOT NULL DROP TABLE dbo.case_comment_attachments;
GO

--case_comment_attachments;
CREATE TABLE case_comment_attachments (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 case_comment_id BIGINT NOT NULL,
 name nvarchar(256) DEFAULT NULL,
 path nvarchar(256) NOT NULL,
 uploaded nvarchar(3) NOT NULL CHECK(uploaded IN ('Yes','No'))
);
GO

IF OBJECT_ID('dbo.case_document_classifications', 'U') IS NOT NULL DROP TABLE dbo.case_document_classifications;
GO

--case_document_classifications;
CREATE TABLE case_document_classifications (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 case_document_classification_id BIGINT DEFAULT NULL,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.case_document_statuses', 'U') IS NOT NULL DROP TABLE dbo.case_document_statuses;
GO

--case_document_statuses;
CREATE TABLE case_document_statuses (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.case_document_types', 'U') IS NOT NULL DROP TABLE dbo.case_document_types;
GO

--case_document_types;
CREATE TABLE case_document_types (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.case_types', 'U') IS NOT NULL DROP TABLE dbo.case_types;
GO

--case_types;
CREATE TABLE case_types (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL,
 litigation char(3) DEFAULT NULL,
 corporate char(3) DEFAULT NULL,
 litigationSLA BIGINT DEFAULT NULL,
 legalMatterSLA BIGINT DEFAULT NULL,
 isDeleted TINYINT NOT NULL DEFAULT '0'
);
GO

IF OBJECT_ID('dbo.ci_sessions', 'U') IS NOT NULL DROP TABLE dbo.ci_sessions;
GO

--ci_sessions;
CREATE TABLE ci_sessions (
id nvarchar(128) NOT NULL DEFAULT '0',
ip_address nvarchar(45) NOT NULL DEFAULT '0',
timestamp BIGINT NOT NULL DEFAULT '0',
data TEXT NOT NULL,
PRIMARY KEY (id)
);
GO
CREATE INDEX ci_sessions_timestamp ON ci_sessions (timestamp);
GO


IF OBJECT_ID('dbo.companies', 'U') IS NOT NULL DROP TABLE dbo.companies;
GO

--companies;
CREATE TABLE companies (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 legalName nvarchar(255) DEFAULT NULL,
 name nvarchar(255) NOT NULL,
 shortName nvarchar(255) DEFAULT '',
 foreignName nvarchar(255) DEFAULT NULL,
 status nvarchar(8) NOT NULL CHECK(status IN ('Active','Inactive')) DEFAULT 'Active',
 category nvarchar(8) NOT NULL CHECK(category IN ('Internal','Group')) ,
 company_category_id BIGINT DEFAULT NULL,
 company_sub_category_id BIGINT DEFAULT NULL,
 private CHAR( 3 ) NULL DEFAULT NULL,
 company_id BIGINT DEFAULT NULL,
 nationality_id BIGINT DEFAULT NULL,
 company_legal_type_id BIGINT DEFAULT NULL,
 object TEXT,
 show_extra_tabs char(1) NULL,
 capital decimal(22,2) NULL DEFAULT '0.00',
 capitalVisualizeDecimals nvarchar(3) NULL CHECK(capitalVisualizeDecimals IN ('yes','no')) DEFAULT 'no',
 capitalCurrency nvarchar(3) DEFAULT NULL,
 nominalShares decimal(22,0) NULL DEFAULT '0',
 bearerShares decimal(22,0) NULL DEFAULT '0',
 shareParValue decimal(22,2) NULL DEFAULT '0',
 shareParValueCurrency nvarchar(3) DEFAULT NULL,
 qualifyingShares decimal(22,2) NULL DEFAULT '0',
 registrationNb nvarchar(255) DEFAULT NULL,
 registrationDate date DEFAULT NULL,
 registrationCity nvarchar(255) DEFAULT NULL,
 registrationTaxNb nvarchar(255) DEFAULT NULL,
 registrationYearsNb BIGINT DEFAULT NULL,
 registrationByLawNotaryPublic BIGINT DEFAULT NULL,
 registrationByLawRef nvarchar(255) DEFAULT NULL,
 registrationByLawDate date DEFAULT NULL,
 registrationByLawCity nvarchar(255) DEFAULT NULL,
 sharesLocation nvarchar(255) DEFAULT NULL,
 ownedByGroup nvarchar(3) CHECK(ownedByGroup IN ('','No','Yes')) DEFAULT NULL,
 sheerLebanese nvarchar(3) CHECK(sheerLebanese IN ('','No','Yes')) DEFAULT NULL,
 contributionRatio decimal(3,2) DEFAULT NULL,
 notes NVARCHAR(MAX),
 otherNotes NVARCHAR(MAX),
 registrationAuthority BIGINT DEFAULT NULL,
 internalReference nvarchar( 255 ) DEFAULT NULL,
 crReleasedOn date DEFAULT NULL,
 crExpiresOn date DEFAULT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL,
 capitalComment text,
 additional_id_type BIGINT NULL DEFAULT NULL,
 additional_id_value nvarchar(255) NULL DEFAULT NULL
);
GO

CREATE UNIQUE INDEX shortName ON companies(shortName) WHERE shortName IS NOT NULL;
GO

IF OBJECT_ID('dbo.companies_contacts', 'U') IS NOT NULL DROP TABLE dbo.companies_contacts;
GO

--companies_contacts;
CREATE TABLE companies_contacts (
 company_id BIGINT NOT NULL,
 contact_id BIGINT NOT NULL,
 description text DEFAULT NULL,
 PRIMARY KEY (company_id,contact_id)
);
GO
IF OBJECT_ID('dbo.company_assets', 'U') IS NOT NULL DROP TABLE dbo.company_assets;
GO

--company_assets;
CREATE TABLE company_assets (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 company_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL,
 company_asset_type_id BIGINT DEFAULT NULL,
 ref nvarchar(255) DEFAULT NULL,
 description NVARCHAR(MAX)
);
GO

IF OBJECT_ID('dbo.company_asset_types', 'U') IS NOT NULL DROP TABLE dbo.company_asset_types;
GO

--company_asset_types;
CREATE TABLE company_asset_types (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL
);
GO
IF OBJECT_ID('dbo.company_auditors', 'U') IS NOT NULL DROP TABLE dbo.company_auditors;
GO

--company_auditors;
CREATE TABLE company_auditors (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 company_id BIGINT NOT NULL,
 auditor_id BIGINT NOT NULL,
 auditorType nvarchar(16) DEFAULT NULL,
 designationDate date DEFAULT NULL,
 expiryDate date DEFAULT NULL,
 comments NVARCHAR(MAX) NOT NULL,
 fees NVARCHAR(255) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.company_changes', 'U') IS NOT NULL DROP TABLE dbo.company_changes;
GO

--company_changes;
CREATE TABLE company_changes (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 company_id BIGINT NOT NULL,
 changes TEXT NOT NULL,
 user_id BIGINT NOT NULL,
 changedOn smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);
GO

IF OBJECT_ID('dbo.company_documents', 'U') IS NOT NULL DROP TABLE dbo.company_documents;
GO

--company_documents;
CREATE TABLE company_documents (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 company_id BIGINT NOT NULL,
 company_document_status_id BIGINT NOT NULL,
 company_document_type_id BIGINT NOT NULL,
 name nvarchar(255) DEFAULT NULL,
 path nvarchar(255) DEFAULT NULL,
 pathType nvarchar(255) DEFAULT NULL,
 comments NVARCHAR(MAX),
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.company_document_statuses', 'U') IS NOT NULL DROP TABLE dbo.company_document_statuses;
GO

--company_document_statuses;
CREATE TABLE company_document_statuses (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.company_document_types', 'U') IS NOT NULL DROP TABLE dbo.company_document_types;
GO

--company_document_types;
CREATE TABLE company_document_types (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.company_legal_types', 'U') IS NOT NULL DROP TABLE dbo.company_legal_types;
GO

--company_legal_types;
CREATE TABLE company_legal_types (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.contacts', 'U') IS NOT NULL DROP TABLE dbo.contacts;
GO

--contacts;
CREATE TABLE contacts (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 status nvarchar(8) NOT NULL CHECK(status IN ('Active','Inactive')) DEFAULT 'Active',
 gender nvarchar(6) CHECK(gender IN ('','Male','Female')) DEFAULT '',
 title_id BIGINT DEFAULT NULL,
 firstName nvarchar(255) NOT NULL,
 lastName nvarchar(255) NOT NULL,
 foreignFirstName nvarchar(255) DEFAULT NULL,
 foreignLastName nvarchar(255) DEFAULT NULL,
 father nvarchar(255) NOT NULL DEFAULT '',
 mother nvarchar(255) NOT NULL DEFAULT '',
 dateOfBirth date DEFAULT NULL,
 contact_category_id BIGINT DEFAULT NULL,
 contact_sub_category_id BIGINT DEFAULT NULL,
 jobTitle nvarchar(255) NOT NULL DEFAULT '',
 private CHAR( 3 ) NULL DEFAULT NULL,
 isLawyer nvarchar(3) NOT NULL CHECK(isLawyer IN ('no','yes')) DEFAULT 'no',
 lawyerForCompany nvarchar(3) NOT NULL CHECK(lawyerForCompany IN ('yes','no')) DEFAULT 'no',
 website nvarchar(255) NOT NULL DEFAULT '',
 phone nvarchar(255) NOT NULL DEFAULT '',
 fax nvarchar(255) NOT NULL DEFAULT '',
 mobile nvarchar(255) NOT NULL DEFAULT '',
 address1 nvarchar(255) NOT NULL DEFAULT '',
 address2 nvarchar(255) NOT NULL DEFAULT '',
 city nvarchar(255) NOT NULL DEFAULT '',
 state nvarchar(255) NOT NULL DEFAULT '',
 zip nvarchar(32) NOT NULL DEFAULT '',
 country_id BIGINT DEFAULT NULL,
 comments NVARCHAR(MAX) NOT NULL,
 internalReference nvarchar(255) DEFAULT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL,
 manager_id BIGINT  DEFAULT NULL,
  tax_number nvarchar(255) NULL DEFAULT NULL,
  street_name nvarchar(255) NULL DEFAULT NULL , 
  additional_street_name nvarchar(255) NULL DEFAULT NULL , 
  building_number nvarchar(255) NULL DEFAULT NULL , 
  address_additional_number nvarchar(255) NULL DEFAULT NULL , 
  district_neighborhood nvarchar(255) NULL DEFAULT NULL , 
  additional_id_type BIGINT NULL DEFAULT NULL,
  additional_id_value nvarchar(255) NULL DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.contact_documents', 'U') IS NOT NULL DROP TABLE dbo.contact_documents;
GO

--contact_documents;
CREATE TABLE contact_documents (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contact_id BIGINT NOT NULL,
 contact_document_status_id BIGINT NOT NULL,
 contact_document_type_id BIGINT NOT NULL,
 name nvarchar(255) DEFAULT NULL,
 path nvarchar(255) DEFAULT NULL,
 pathType nvarchar(255) DEFAULT NULL,
 comments text,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.contact_document_statuses', 'U') IS NOT NULL DROP TABLE dbo.contact_document_statuses;
GO

--contact_document_statuses;
CREATE TABLE contact_document_statuses (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.contact_document_types', 'U') IS NOT NULL DROP TABLE dbo.contact_document_types;
GO

--contact_document_types;
CREATE TABLE contact_document_types (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.countries', 'U') IS NOT NULL DROP TABLE dbo.countries;
GO

--countries;
CREATE TABLE countries (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 countryCode char(2) NOT NULL DEFAULT '',
 currencyCode char(3) DEFAULT NULL,
 currencyName nvarchar(255) DEFAULT NULL,
 isoNumeric char(4) DEFAULT NULL,
 languages nvarchar(30) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.legal_cases', 'U') IS NOT NULL DROP TABLE dbo.legal_cases;
GO

--legal_cases;
CREATE TABLE legal_cases (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 stage BIGINT NULL,
 channel nvarchar(3) DEFAULT NULL,
 visibleToCP TINYINT NULL DEFAULT '0',
 case_status_id BIGINT DEFAULT NULL,
 case_type_id BIGINT NOT NULL,
 legal_case_stage_id BIGINT DEFAULT NULL,
 provider_group_id BIGINT NOT NULL,
 user_id BIGINT DEFAULT NULL,
 contact_id BIGINT DEFAULT NULL,
 client_id BIGINT DEFAULT NULL,
 referredBy BIGINT DEFAULT NULL,
 requestedBy BIGINT DEFAULT NULL,
 subject nvarchar(255) NOT NULL,
 description text NOT NULL,
 latest_development text DEFAULT NULL,
 priority nvarchar(8) NOT NULL CHECK(priority IN ('critical','high','medium','low')) DEFAULT 'medium',
 arrivalDate date DEFAULT NULL,
 caseArrivalDate date DEFAULT NULL,
 dueDate date DEFAULT NULL,
 closedOn date DEFAULT NULL,
 statusComments NVARCHAR(MAX),
 category varchar(255) DEFAULT NULL,
 caseValue decimal(22,2) NULL DEFAULT '0.00',
 recoveredValue decimal(22,2) NULL DEFAULT '0.00',
 judgmentValue decimal(22,2) NULL DEFAULT '0.00',
 internalReference nvarchar(255) DEFAULT NULL,
 externalizeLawyers nvarchar(3) NOT NULL CHECK(externalizeLawyers IN ('yes','no')) DEFAULT 'no',
 estimatedEffort decimal(10,2) DEFAULT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL,
 modifiedByChannel nvarchar(3) DEFAULT NULL,
 archived nvarchar(3) NOT NULL CHECK(archived IN ('yes','no')) DEFAULT 'no',
 hideFromBoard nvarchar(3) DEFAULT NULL,
 private char(3) DEFAULT NULL,
 timeTrackingBillable char(1) NULL,
 expensesBillable char(1) NULL,
 legal_case_client_position_id BIGINT DEFAULT NULL,
 legal_case_success_probability_id BIGINT DEFAULT NULL,
 assignedOn smalldatetime DEFAULT NULL,
 isDeleted TINYINT NOT NULL DEFAULT '0',
 workflow BIGINT DEFAULT '1',
 cap_amount decimal(22,2) NULL DEFAULT '0.00',
 time_logs_cap_ratio decimal(10,2) NULL DEFAULT '100.00',
 expenses_cap_ratio decimal(10,2) NULL DEFAULT '100.00',
 cap_amount_enable TINYINT NULL DEFAULT '0',
 cap_amount_disallow TINYINT NULL DEFAULT '0'
);
GO

IF OBJECT_ID('dbo.legal_cases_companies', 'U') IS NOT NULL DROP TABLE dbo.legal_cases_companies;
GO

--legal_cases_companies;
CREATE TABLE legal_cases_companies (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 case_id BIGINT NOT NULL,
 company_id BIGINT NOT NULL,
 legal_case_company_role_id BIGINT DEFAULT NULL,
 comments nvarchar(max) NULL DEFAULT NULL,
 companyType nvarchar(15) NOT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO
CREATE UNIQUE INDEX legal_cases_companies_fu ON legal_cases_companies( case_id, company_id, legal_case_company_role_id ) WHERE legal_case_company_role_id IS NOT NULL;
GO

IF OBJECT_ID('dbo.legal_cases_contacts', 'U') IS NOT NULL DROP TABLE dbo.legal_cases_contacts;
GO

--legal_cases_contacts;
CREATE TABLE legal_cases_contacts (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 case_id BIGINT NOT NULL,
 contact_id BIGINT NOT NULL,
 legal_case_contact_role_id BIGINT NULL DEFAULT NULL,
 comments NVARCHAR(MAX),
 contactType nvarchar(15) NOT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
 );
GO

IF OBJECT_ID('dbo.legal_case_archived_hard_copies', 'U') IS NOT NULL DROP TABLE dbo.legal_case_archived_hard_copies;
GO

--legal_case_archived_hard_copies;
CREATE TABLE legal_case_archived_hard_copies (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 case_id BIGINT NOT NULL,
 case_document_classification_id BIGINT NOT NULL,
 sub_case_document_classification_id BIGINT NOT NULL,
 notes nvarchar(max) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_stage_changes', 'U') IS NOT NULL DROP TABLE dbo.legal_case_stage_changes;
GO
--legal_case_stage_changes;
CREATE TABLE legal_case_stage_changes (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 legal_case_id BIGINT DEFAULT NULL,
 legal_case_stage_id BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_changes', 'U') IS NOT NULL DROP TABLE dbo.legal_case_changes;
GO
--legal_case_changes;
CREATE TABLE legal_case_changes (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 legal_case_id BIGINT NOT NULL,
 changes nvarchar(max) NOT NULL,
 user_id BIGINT DEFAULT NULL,
 modifiedByChannel nvarchar(3) DEFAULT NULL,
 changedOn smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);
GO

IF OBJECT_ID('dbo.legal_case_documents', 'U') IS NOT NULL DROP TABLE dbo.legal_case_documents;
GO
--legal_case_documents;
CREATE TABLE legal_case_documents (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 legal_case_id BIGINT NOT NULL,
 legal_case_document_status_id BIGINT NOT NULL,
 legal_case_document_type_id BIGINT NOT NULL,
 name nvarchar(255) DEFAULT NULL,
 path nvarchar(255) DEFAULT NULL,
 pathType nvarchar(255) DEFAULT NULL,
 comments NVARCHAR(MAX),
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_users', 'U') IS NOT NULL DROP TABLE dbo.legal_case_users;
GO

--legal_case_users;
CREATE TABLE legal_case_users (
 legal_case_id BIGINT NOT NULL,
 user_id BIGINT NOT NULL
);
GO
CREATE UNIQUE INDEX legal_case_id_user_id ON legal_case_users(legal_case_id,user_id)
GO

IF OBJECT_ID('dbo.lookup_members', 'U') IS NOT NULL DROP TABLE dbo.lookup_members;
GO

--lookup_members;
CREATE TABLE lookup_members (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 company_id BIGINT DEFAULT NULL,
 contact_id BIGINT DEFAULT NULL
);
GO
CREATE UNIQUE INDEX company_id ON lookup_members(company_id) WHERE company_id IS NOT NULL;
GO
CREATE UNIQUE INDEX contact_id ON lookup_members(contact_id) WHERE contact_id IS NOT NULL;
GO

IF OBJECT_ID('dbo.notifications', 'U') IS NOT NULL DROP TABLE dbo.notifications;
GO

--notifications;
CREATE TABLE notifications (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 user_id BIGINT NOT NULL,
 status nvarchar(9) NOT NULL CHECK(status IN ('seen','unseen','dismissed')) DEFAULT 'unseen',
 message TEXT NOT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.planning_boards', 'U') IS NOT NULL DROP TABLE dbo.planning_boards;
GO

--planning_boards;
CREATE TABLE planning_boards (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO
IF OBJECT_ID('dbo.planning_board_saved_filters', 'U') IS NOT NULL DROP TABLE dbo.user_reports;
GO

CREATE TABLE planning_board_saved_filters (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 boardId  BIGINT NOT NULL,
 userId  BIGINT NOT NULL,
 keyName VARCHAR( 255 ) NOT NULL,
 keyValue NVARCHAR(MAX) NULL DEFAULT NULL
);
GO


IF OBJECT_ID('dbo.system_configurations', 'U') IS NOT NULL DROP TABLE dbo.system_configurations;
GO

CREATE TABLE system_configurations (
 keyName VARCHAR( 255 ) NOT NULL,
 keyValue NVARCHAR(MAX) NULL DEFAULT NULL
);
GO

CREATE UNIQUE INDEX unique_key ON planning_board_saved_filters(userId,keyName);
GO

IF OBJECT_ID('dbo.planning_board_columns', 'U') IS NOT NULL DROP TABLE dbo.planning_board_columns;
GO

--planning_board_columns;
CREATE TABLE planning_board_columns (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 planning_board_id BIGINT NOT NULL,
 columnOrder tinyint NOT NULL,
 name nvarchar(255) NOT NULL,
 color nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.planning_board_column_options', 'U') IS NOT NULL DROP TABLE dbo.planning_board_column_options;
GO

--planning_board_column_options;
CREATE TABLE planning_board_column_options (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 planning_board_id BIGINT DEFAULT NULL,
 planning_board_column_id BIGINT NOT NULL,
 case_status_id BIGINT NOT NULL
);
GO
CREATE UNIQUE INDEX planning_board_columns_case_status ON planning_board_column_options(planning_board_id,case_status_id)

IF OBJECT_ID('dbo.preferred_shares', 'U') IS NOT NULL DROP TABLE dbo.preferred_shares;
GO

--preferred_shares;
CREATE TABLE preferred_shares (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 company_id BIGINT NOT NULL,
 issueDate date NOT NULL,
 numberOfShares BIGINT NOT NULL,
 series nvarchar(9) NOT NULL DEFAULT '',
 retrieved nvarchar(3) NOT NULL CHECK(retrieved IN ('yes','no')) DEFAULT 'no',
 comment nvarchar(max) NOT NULL DEFAULT ''
);
GO

IF OBJECT_ID('dbo.provider_groups', 'U') IS NOT NULL DROP TABLE dbo.provider_groups;
GO

--provider_groups;
CREATE TABLE provider_groups (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL,
 allUsers tinyint NOT NULL DEFAULT '0'
);
GO

IF OBJECT_ID('dbo.seniority_levels', 'U') IS NOT NULL DROP TABLE dbo.seniority_levels;
GO

--seniority_levels;
CREATE TABLE seniority_levels (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL
);
GO


IF OBJECT_ID('dbo.provider_groups_users', 'U') IS NOT NULL DROP TABLE dbo.provider_groups_users;
GO

--provider_groups_users;
CREATE TABLE provider_groups_users (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 provider_group_id BIGINT NOT NULL,
 user_id BIGINT NOT NULL,
 isDefault nvarchar(3) NOT NULL CHECK(isDefault IN ('yes','no')) DEFAULT 'no'
);
GO
CREATE UNIQUE INDEX provider_group_user ON provider_groups_users(provider_group_id,user_id);
GO


IF OBJECT_ID('dbo.related_cases', 'U') IS NOT NULL DROP TABLE dbo.related_cases;
GO

--related_cases;
CREATE TABLE related_cases (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 case_a_id BIGINT NOT NULL,
 case_b_id BIGINT NOT NULL,
 comments NVARCHAR(MAX)
);
GO

IF OBJECT_ID('dbo.related_contacts', 'U') IS NOT NULL DROP TABLE dbo.related_contacts;
GO

--related_contacts;
CREATE TABLE related_contacts (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contact_a_id BIGINT NOT NULL,
 contact_b_id BIGINT NOT NULL,
 comments NVARCHAR(MAX)
);
GO

IF OBJECT_ID('dbo.reminders', 'U') IS NOT NULL DROP TABLE dbo.reminders;
GO

--reminders;
CREATE TABLE reminders (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 user_id BIGINT NOT NULL,
 summary nvarchar(max) NOT NULL,
 reminder_type_id BIGINT NOT NULL,
 remindDate date NOT NULL,
 remindTime time(0) NOT NULL,
 status nvarchar(9) NOT NULL CHECK(status IN ('Dismissed','Open')) DEFAULT 'Open',
 legal_case_id BIGINT DEFAULT NULL,
 company_id BIGINT DEFAULT NULL,
 contact_id BIGINT DEFAULT NULL,
 task_id BIGINT DEFAULT NULL,
 contract_id BIGINT DEFAULT NULL,
 legal_case_hearing_id BIGINT NULL,
 related_id BIGINT DEFAULT NULL,
 related_object varchar(255) DEFAULT NULL,
 parent_id BIGINT DEFAULT NULL,
 is_cloned tinyint DEFAULT 0,
 notify_before_time BIGINT DEFAULT NULL,
 notify_before_time_type varchar(5) DEFAULT NULL,
 notify_before_type varchar(15) DEFAULT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.reminder_types', 'U') IS NOT NULL DROP TABLE dbo.reminder_types;
GO

--reminder_types;
CREATE TABLE reminder_types (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY
);
GO

IF OBJECT_ID('dbo.shares_movements', 'U') IS NOT NULL DROP TABLE dbo.shares_movements;
GO

--shares_movements;
CREATE TABLE shares_movements (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 shares_movement_header_id BIGINT DEFAULT NULL,
 company_id BIGINT NOT NULL,
 member_id BIGINT NOT NULL,
 initiatedOn date DEFAULT NULL,
 executedOn date DEFAULT NULL,
 type nvarchar(30) NOT NULL CHECK(type IN ('','incorporation','increase in capital','increase in capital - profit','transfer')) ,
 numberOfShares decimal(22,2) NOT NULL,
 category nvarchar(255) DEFAULT NULL,
 comments nvarchar(255) NOT NULL DEFAULT '',
 to_member_id BIGINT DEFAULT NULL,
 from_member_id BIGINT DEFAULT NULL,
 certificationNb NVARCHAR(MAX),
 fromShareNb NVARCHAR(MAX),
 toShareNb NVARCHAR(MAX),
 rightsOnShares NVARCHAR(MAX)
);
GO

IF OBJECT_ID('dbo.shares_movement_headers', 'U') IS NOT NULL DROP TABLE dbo.shares_movement_headers;
GO

--shares_movement_headers;
CREATE TABLE shares_movement_headers (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 createdOn smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);
GO
IF OBJECT_ID('dbo.system_preferences', 'U') IS NOT NULL DROP TABLE dbo.system_preferences;
GO

--system_preferences;
CREATE TABLE system_preferences (
 groupName nvarchar(255) DEFAULT NULL,
 keyName VARCHAR( 255 ) NOT NULL,
 keyValue NVARCHAR(MAX) NULL DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.tasks', 'U') IS NOT NULL DROP TABLE dbo.tasks;
GO

--tasks
CREATE TABLE tasks (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 title nvarchar(255) NOT NULL,
 legal_case_id BIGINT DEFAULT NULL,
 contract_id BIGINT DEFAULT NULL,
 stage BIGINT DEFAULT NULL,
 user_id BIGINT NOT NULL,
 assigned_to BIGINT NOT NULL,
 due_date date NOT NULL,
 private char(3) DEFAULT NULL,
 priority nvarchar(8) NOT NULL CHECK(priority IN ('critical','high','medium','low')) DEFAULT 'medium',
 task_location_id BIGINT DEFAULT NULL,
 description TEXT,
 task_status_id BIGINT NOT NULL,
 task_type_id BIGINT NOT NULL,
 estimated_effort decimal(8,2) DEFAULT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL,
 archived nvarchar(3) NOT NULL CHECK(archived IN ('yes','no')) DEFAULT 'no',
 hideFromBoard nvarchar(3) DEFAULT NULL,
 reporter BIGINT DEFAULT NULL,
 workflow BIGINT NOT NULL DEFAULT '1'
);
GO

IF OBJECT_ID('dbo.task_boards', 'U') IS NOT NULL DROP TABLE dbo.task_boards;
GO

--task_boards;
CREATE TABLE task_boards (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.task_board_saved_filters', 'U') IS NOT NULL DROP TABLE dbo.user_reports;
GO
CREATE TABLE task_board_saved_filters (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 boardId  BIGINT NOT NULL,
 userId  BIGINT NOT NULL,
 keyName VARCHAR( 255 ) NOT NULL,
 keyValue NVARCHAR(MAX) NULL DEFAULT NULL
);
GO

CREATE UNIQUE INDEX unique_key ON task_board_saved_filters(userId,keyName);
GO

IF OBJECT_ID('dbo.task_board_columns', 'U') IS NOT NULL DROP TABLE dbo.task_board_columns;
GO

--task_board_columns;
CREATE TABLE task_board_columns (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 task_board_id BIGINT NOT NULL,
 columnOrder tinyint NOT NULL,
 name nvarchar(255) NOT NULL,
 color varchar(255) NOT NULL

);
GO

IF OBJECT_ID('dbo.task_board_column_options', 'U') IS NOT NULL DROP TABLE dbo.task_board_column_options;
GO

--task_board_column_options;
CREATE TABLE task_board_column_options (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 task_board_id BIGINT DEFAULT NULL,
 task_board_column_id BIGINT NOT NULL,
 task_status_id BIGINT NOT NULL
);
GO
CREATE UNIQUE INDEX task_board_column_task_status ON task_board_column_options(task_board_id,task_status_id);
GO

IF OBJECT_ID('dbo.task_statuses', 'U') IS NOT NULL DROP TABLE dbo.task_statuses;
GO

--task_statuses;
CREATE TABLE task_statuses (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL,
 category nvarchar(255) NOT NULL DEFAULT 'in progress',
 isGlobal TINYINT NOT NULL DEFAULT '1'
);
GO

IF OBJECT_ID('dbo.task_types', 'U') IS NOT NULL DROP TABLE dbo.task_types;
GO

--task_types;
CREATE TABLE task_types (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY
);
GO

IF OBJECT_ID('dbo.task_users', 'U') IS NOT NULL DROP TABLE dbo.task_users;
GO

--task_users;
CREATE TABLE task_users (
 task_id BIGINT NOT NULL,
 user_id BIGINT NOT NULL
);
GO

CREATE UNIQUE INDEX task_id_user_id ON task_users ( task_id , user_id );
GO
IF OBJECT_ID('dbo.contact_users', 'U') IS NOT NULL DROP TABLE dbo.contact_users;
GO

--contact_users;
CREATE TABLE contact_users (
 contact_id BIGINT NOT NULL,
 user_id BIGINT NOT NULL
);
GO

CREATE UNIQUE INDEX contact_id_user_id ON contact_users ( contact_id , user_id );
GO

IF OBJECT_ID('dbo.company_users', 'U') IS NOT NULL DROP TABLE dbo.company_users;
GO

--company_users;
CREATE TABLE company_users (
 company_id BIGINT NOT NULL,
 user_id BIGINT NOT NULL
);
GO

CREATE UNIQUE INDEX company_id_user_id ON company_users ( company_id , user_id );
GO

IF OBJECT_ID('dbo.document_managment_users', 'U') IS NOT NULL DROP TABLE dbo.document_managment_users;
GO

--document_managment_users;
CREATE TABLE document_managment_users (
 recordId BIGINT NOT NULL,
 user_id BIGINT NOT NULL
);
GO

CREATE UNIQUE INDEX recordId_user_id ON document_managment_users ( recordId , user_id );
GO

IF OBJECT_ID('dbo.users', 'U') IS NOT NULL DROP TABLE dbo.users;
GO

--users;
CREATE TABLE users (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 user_group_id BIGINT NOT NULL,
 isAd tinyint NOT NULL DEFAULT '0',
 username nvarchar(255) NOT NULL,
 password nvarchar(255)  NULL,
 email nvarchar(255) NOT NULL,
 type nvarchar(15) NOT NULL DEFAULT 'core',
 banned tinyint NOT NULL DEFAULT '0',
 ban_reason nvarchar(255) DEFAULT NULL,
 last_ip nvarchar(45) DEFAULT NULL,
 last_login smalldatetime DEFAULT NULL,
 created smalldatetime DEFAULT NULL,
 modified smalldatetime NULL DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL,
 session_id nvarchar(50) NULL,
 userDirectory nvarchar(255) NULL,
 workthrough nvarchar(max) NULL,
 user_guide char(3) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.user_activity_logs', 'U') IS NOT NULL DROP TABLE dbo.user_activity_logs;
GO

CREATE TABLE user_activity_logs (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 user_id BIGINT NOT NULL,
 task_id BIGINT DEFAULT NULL,
 legal_case_id BIGINT DEFAULT NULL,
 client_id BIGINT DEFAULT NULL,
 time_type_id BIGINT NULL,
 time_internal_status_id BIGINT NULL,
 logDate date NOT NULL,
 effectiveEffort decimal(8,2) NOT NULL,
 comments NVARCHAR(MAX),
 timeStatus nvarchar(8) NULL CHECK(timeStatus IN ('', 'internal', 'billable')),
 createdBy BIGINT DEFAULT NULL,
 createdOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 rate decimal(10,2) DEFAULT NULL,
 rate_system nvarchar(32) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.user_autologin', 'U') IS NOT NULL DROP TABLE dbo.user_autologin;
GO

--user_autologin;
CREATE TABLE user_autologin (
 key_id char(32) NOT NULL,
 user_id BIGINT NOT NULL,
 user_agent nvarchar(255) NOT NULL,
 last_ip nvarchar(45) NOT NULL,
 last_login smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
 channel nvarchar(3) DEFAULT NULL,
 PRIMARY KEY (key_id,user_id)
);
CREATE UNIQUE INDEX user_id ON user_autologin(user_id);
GO

IF OBJECT_ID('dbo.user_groups', 'U') IS NOT NULL DROP TABLE dbo.user_groups;
GO

--user_groups;
CREATE TABLE user_groups (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL,
 description NVARCHAR(MAX),
 flagNeedApproval TINYINT NULL DEFAULT '0',
 needApprovalOnAdd TINYINT NULL DEFAULT '0',
 system_group TINYINT NULL DEFAULT '0',
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
CREATE UNIQUE INDEX name ON user_groups(name);
GO

IF OBJECT_ID('dbo.user_group_permissions', 'U') IS NOT NULL DROP TABLE dbo.user_group_permissions;
GO

--user_group_permissions;
CREATE TABLE user_group_permissions (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 user_group_id BIGINT NOT NULL,
 data TEXT
);
GO

IF OBJECT_ID('dbo.user_passwords', 'U') IS NOT NULL DROP TABLE dbo.user_passwords;
GO

--user_passwords;
CREATE TABLE user_passwords (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 user_id BIGINT NOT NULL,
 password nvarchar(255) NOT NULL,
 created smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);
GO

IF OBJECT_ID('dbo.user_preferences', 'U') IS NOT NULL DROP TABLE dbo.user_preferences;
GO

--user_preferences;
CREATE TABLE user_preferences (
 user_id BIGINT NOT NULL,
 keyName VARCHAR( 255 ) NOT NULL ,
 keyValue NVARCHAR(MAX) NULL DEFAULT NULL
);
GO
ALTER TABLE user_preferences ADD PRIMARY KEY (user_id,keyName);
GO

IF OBJECT_ID('dbo.user_profiles', 'U') IS NOT NULL DROP TABLE dbo.user_profiles;
GO

--user_profiles;
CREATE TABLE user_profiles (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 user_id BIGINT NOT NULL,
 status nvarchar(8) NOT NULL  CHECK(status IN ('Active','Inactive')) DEFAULT 'Active',
 gender nvarchar(6) NOT NULL  CHECK(gender IN ('','Male','Female')) DEFAULT '',
 title nvarchar(5)  CHECK(title IN ('','Mr','Mrs','Miss','Dr','Me','Judge','Sen')) NOT NULL DEFAULT '',
 firstName nvarchar(255) NOT NULL,
 lastName nvarchar(255) NOT NULL,
 father nvarchar(255) NOT NULL DEFAULT '',
 mother nvarchar(255) NOT NULL DEFAULT '',
 employeeId nvarchar(255) DEFAULT NULL,
 ad_userCode nvarchar(255) DEFAULT NULL,
 user_code varchar(10) DEFAULT NULL,
 dateOfBirth date DEFAULT NULL,
 department nvarchar(255) DEFAULT NULL,
 nationality nvarchar(255) NOT NULL DEFAULT '',
 jobTitle nvarchar(255) NOT NULL DEFAULT '',
 overridePrivacy CHAR( 3 ) NOT NULL DEFAULT 'no',
 flagChangePassword TINYINT NULL DEFAULT '0',
 flagNeedApproval TINYINT NULL DEFAULT '0',
 isLawyer nvarchar(3) NOT NULL CHECK(isLawyer IN ('yes','no')) DEFAULT 'no',
 website nvarchar(255) NOT NULL DEFAULT '',
 phone nvarchar(255) NOT NULL DEFAULT '',
 fax nvarchar(255) NOT NULL DEFAULT '',
 mobile nvarchar(255) NOT NULL DEFAULT '',
 address1 nvarchar(255) NOT NULL DEFAULT '',
 address2 nvarchar(255) NOT NULL DEFAULT '',
 city nvarchar(255) NOT NULL DEFAULT '',
 state nvarchar(255) NOT NULL DEFAULT '',
 zip nvarchar(32) NOT NULL DEFAULT '',
 profilePicture nvarchar(255) NULL,
 country nvarchar(255) NOT NULL DEFAULT '',
 comments NVARCHAR(MAX) NOT NULL,
 seniority_level_id BIGINT DEFAULT NULL,
 forgetPasswordFlag tinyint NULL DEFAULT '0',
 forgetPasswordHashKey nvarchar(255),
 foreign_first_name nvarchar(255) NULL,
 foreign_last_name nvarchar(255) NULL,
 forgetPasswordUrlCreatedOn datetime2(0) NULL DEFAULT NULL,
);
GO
CREATE UNIQUE INDEX user_id ON user_profiles(user_id);
GO

IF OBJECT_ID('dbo.user_temp', 'U') IS NOT NULL DROP TABLE dbo.user_temp;
GO

--user_temp;
CREATE TABLE user_temp (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 username nvarchar(255) NOT NULL,
 password nvarchar(34) NOT NULL,
 email nvarchar(100) NOT NULL,
 activation_key nvarchar(50) NOT NULL,
 last_ip nvarchar(40) NOT NULL,
 created smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);
GO

IF OBJECT_ID('dbo.contact_company_categories', 'U') IS NOT NULL DROP TABLE dbo.contact_company_categories;
GO

--contact_company_categories;
CREATE TABLE contact_company_categories (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  keyName nvarchar(255) NULL,
  name nvarchar(255) NOT NULL,
  color nvarchar(255) NULL
);
GO

IF OBJECT_ID('dbo.contact_company_sub_categories', 'U') IS NOT NULL DROP TABLE dbo.contact_company_sub_categories;
GO

--contact_company_sub_categories;
CREATE TABLE contact_company_sub_categories (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.custom_fields', 'U') IS NOT NULL DROP TABLE dbo.custom_fields;
GO

--custom_fields
CREATE TABLE custom_fields (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  model NVARCHAR(30) NOT NULL,
  type NVARCHAR(30)  NOT NULL,
  type_data NVARCHAR(MAX),
  field_order BIGINT,
  category nvarchar(50) NULL,
  cp_visible TINYINT DEFAULT 0
);
GO

IF OBJECT_ID('dbo.custom_fields_languages', 'U') IS NOT NULL DROP TABLE dbo.custom_fields_languages;
GO

CREATE TABLE custom_fields_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 custom_field_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 customName nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.custom_field_values', 'U') IS NOT NULL DROP TABLE dbo.custom_field_values;
GO

--custom_field_values
CREATE TABLE custom_field_values (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  custom_field_id BIGINT NOT NULL,
  recordId BIGINT NOT NULL,
  text_value NVARCHAR(MAX),
  date_value DATE,
  time_value TIME(0)
);
GO

IF OBJECT_ID('dbo.legal_case_contact_roles', 'U') IS NOT NULL DROP TABLE dbo.legal_case_contact_roles;
GO

--legal_case_contact_roles
CREATE TABLE legal_case_contact_roles (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_company_roles', 'U') IS NOT NULL DROP TABLE dbo.legal_case_company_roles;
GO

--legal_case_company_roles
CREATE TABLE legal_case_company_roles (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.court_types', 'U') IS NOT NULL DROP TABLE dbo.court_types;
GO

--court_types
CREATE TABLE court_types (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.court_degrees', 'U') IS NOT NULL DROP TABLE dbo.court_degrees;
GO

--court_degrees
CREATE TABLE court_degrees (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.court_regions', 'U') IS NOT NULL DROP TABLE dbo.court_regions;
GO

--court_regions
CREATE TABLE court_regions (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.courts', 'U') IS NOT NULL DROP TABLE dbo.courts;
GO

--courts
CREATE TABLE courts (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_litigation_details', 'U') IS NOT NULL DROP TABLE dbo.legal_case_litigation_details;
GO

--legal_case_litigation_details
CREATE TABLE legal_case_litigation_details (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  legal_case_id BIGINT NOT NULL,
  court_type_id BIGINT NULL DEFAULT NULL,
  court_degree_id BIGINT NULL DEFAULT NULL,
  court_region_id BIGINT NULL DEFAULT NULL,
  court_id BIGINT NULL DEFAULT NULL,
  sentenceDate date NULL DEFAULT NULL,
  comments text NULL DEFAULT NULL,
  legal_case_stage BIGINT NULL,
  client_position BIGINT NULL,
  status BIGINT NULL,
  modifiedOn smalldatetime NULL,
  modifiedBy BIGINT NULL,
  modifiedByChannel NVARCHAR(3) DEFAULT NULL,
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  createdByChannel NVARCHAR(3) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_litigation_external_references', 'U') IS NOT NULL DROP TABLE dbo.legal_case_litigation_external_references;
GO

--legal_case_litigation_external_references
CREATE TABLE legal_case_litigation_external_references (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  stage BIGINT NOT NULL,
  number nvarchar(255) NOT NULL,
  refDate date NULL DEFAULT NULL,
  comments text
);
GO

IF OBJECT_ID('dbo.legal_case_hearings', 'U') IS NOT NULL DROP TABLE dbo.legal_case_hearings;
GO

--legal_case_hearings
CREATE TABLE legal_case_hearings (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  legal_case_id BIGINT NOT NULL,
  task_id BIGINT NULL DEFAULT NULL,
  startDate date NULL DEFAULT NULL,
  startTime time(0) NULL DEFAULT NULL,
  postponedDate date NULL DEFAULT NULL,
  postponedTime time(0) NULL DEFAULT NULL,
  summary text,
  summaryToClient TEXT NULL DEFAULT NULL,
  verifiedSummary nvarchar(1) NULL DEFAULT 0,
  clientReportEmailSent nvarchar(10) NULL DEFAULT 0,
  is_deleted TINYINT NOT NULL DEFAULT '0',
  judged nvarchar(3) NULL,
  judgment text NULL,
  type BIGINT NULL,
  stage BIGINT NULL,
  comments TEXT,
  reasons_of_postponement TEXT,
  hearing_outcome nvarchar(4) DEFAULT NULL,
  reason_of_win_or_lose BIGINT NULL,
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  createdByChannel NVARCHAR(3) DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL,
  modifiedByChannel NVARCHAR(3) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_hearings_users', 'U') IS NOT NULL DROP TABLE dbo.legal_case_hearings_users;
GO

--legal_case_hearings_users
CREATE TABLE legal_case_hearings_users (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  legal_case_hearing_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL,
  user_type varchar (5) NOT NULL DEFAULT 'A4L'
);
GO
CREATE UNIQUE INDEX legal_case_hearing_user_unique_key ON legal_case_hearings_users( legal_case_hearing_id, user_type, user_id);
GO

IF OBJECT_ID('dbo.legal_case_events', 'U') IS NOT NULL DROP TABLE dbo.legal_case_events;
GO

CREATE TABLE legal_case_events (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  legal_case BIGINT NOT NULL,
  stage BIGINT NULL,
  event_type BIGINT DEFAULT NULL,
  parent BIGINT DEFAULT NULL,
  fields text Default NULL,
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.contact_nationalities', 'U') IS NOT NULL DROP TABLE dbo.contact_nationalities;
GO

--contact_nationalities
CREATE TABLE contact_nationalities (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  contact_id BIGINT NOT NULL,
  nationality_id BIGINT NOT NULL
);
GO
CREATE UNIQUE INDEX contact_nationalities_unique_key ON contact_nationalities( contact_id, nationality_id );
GO

IF OBJECT_ID('dbo.legal_case_containers', 'U') IS NOT NULL DROP TABLE dbo.legal_case_containers;
GO

--legal_case_containers
CREATE TABLE legal_case_containers (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  legal_case_container_status_id BIGINT NOT NULL,
  subject nvarchar(max) NOT NULL,
  description text,
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL,
  case_type_id BIGINT NOT NULL default '1',
  provider_group_id BIGINT NOT NULL default '1',
  user_id BIGINT DEFAULT NULL,
  client_id BIGINT DEFAULT NULL,
  caseArrivalDate date DEFAULT NULL,
  closedOn DATE NULL,
  comments TEXT NULL,
  internalReference nvarchar(255) DEFAULT NULL,
  legal_case_client_position_id BIGINT DEFAULT NULL,
  requested_by BIGINT DEFAULT NULL,
  visible_in_cp TINYINT NULL DEFAULT 0,
);
GO

IF OBJECT_ID('dbo.legal_case_container_statuses', 'U') IS NOT NULL DROP TABLE dbo.legal_case_container_statuses;
GO

--legal_case_container_statuses
CREATE TABLE legal_case_container_statuses (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL,
  category nvarchar(255) NOT NULL DEFAULT 'in progress',
);
GO

IF OBJECT_ID('dbo.legal_case_related_containers', 'U') IS NOT NULL DROP TABLE dbo.legal_case_related_containers;
GO

--legal_case_related_containers
CREATE TABLE legal_case_related_containers (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  legal_case_container_id BIGINT NOT NULL,
  legal_case_id BIGINT NOT NULL
);
GO
CREATE UNIQUE INDEX legal_case_container_unique_key ON legal_case_related_containers( legal_case_container_id, legal_case_id );
GO

IF OBJECT_ID('dbo.clients', 'U') IS NOT NULL DROP TABLE dbo.clients;
GO
--clients
CREATE TABLE clients (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  company_id BIGINT DEFAULT NULL,
  contact_id BIGINT DEFAULT NULL,
  term_id BIGINT DEFAULT NULL,
  discount_percentage BIGINT DEFAULT 0,
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL
);
GO
CREATE UNIQUE INDEX company_id ON clients(company_id) WHERE company_id IS NOT NULL;
GO
CREATE UNIQUE INDEX contact_id ON clients(contact_id ) WHERE contact_id IS NOT NULL;
GO

IF OBJECT_ID('dbo.task_locations', 'U') IS NOT NULL DROP TABLE dbo.task_locations;
GO
--task_locations;
CREATE TABLE task_locations (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.company_bank_accounts', 'U') IS NOT NULL DROP TABLE dbo.company_bank_accounts;
GO
--company_bank_accounts;
CREATE TABLE company_bank_accounts (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  company_id BIGINT NOT NULL,
  bankName nvarchar(255) NOT NULL,
  bankFullAddress nvarchar(255) DEFAULT NULL,
  bankPhone nvarchar(255) DEFAULT NULL,
  bankFax nvarchar(255) DEFAULT NULL,
  accountName nvarchar(255) DEFAULT NULL,
  accountCurrency nvarchar(255) DEFAULT NULL,
  accountNb nvarchar(255) DEFAULT NULL,
  swiftCode nvarchar(255) DEFAULT NULL,
  iban nvarchar(255) DEFAULT NULL,
  comments TEXT
);
GO

IF OBJECT_ID('dbo.user_changes', 'U') IS NOT NULL DROP TABLE dbo.user_changes;
GO
--user_changes;
CREATE TABLE user_changes (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  user_id BIGINT NOT NULL,
  action nvarchar(32) NOT NULL,
  fieldName nvarchar(32) NOT NULL,
  beforeData TEXT,
  afterData TEXT NULL,
  modifiedOn smalldatetime NOT NULL,
  modifiedBy BIGINT NOT NULL,
);
GO

IF OBJECT_ID('dbo.time_types', 'U') IS NOT NULL DROP TABLE dbo.time_types;
GO

--time_types;
CREATE TABLE time_types (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 default_comment NVARCHAR(MAX) DEFAULT NULL,
 default_time_effort varchar(255) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.time_types_languages', 'U') IS NOT NULL DROP TABLE dbo.time_types_languages;
GO
CREATE TABLE time_types_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 type BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.languages', 'U') IS NOT NULL DROP TABLE dbo.languages;
GO
--languages;
CREATE TABLE languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL,
 fullName nvarchar(255) NOT NULL,
 display_name text NOT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_stages', 'U') IS NOT NULL DROP TABLE dbo.legal_case_stages;
GO
--legal_case_stages;
CREATE TABLE legal_case_stages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 litigation char(3) DEFAULT NULL,
 corporate char(3) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_stage_languages', 'U') IS NOT NULL DROP TABLE dbo.legal_case_stage_languages;
GO
--legal_case_stage_languages;
CREATE TABLE legal_case_stage_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 legal_case_stage_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_client_positions', 'U') IS NOT NULL DROP TABLE dbo.legal_case_client_positions;
GO
--legal_case_client_positions;
CREATE TABLE legal_case_client_positions (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY
);
GO

IF OBJECT_ID('dbo.legal_case_client_position_languages', 'U') IS NOT NULL DROP TABLE dbo.legal_case_client_position_languages;
GO
--legal_case_client_position_languages;
CREATE TABLE legal_case_client_position_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 legal_case_client_position_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_success_probabilities', 'U') IS NOT NULL DROP TABLE dbo.legal_case_success_probabilities;
GO
--legal_case_success_probabilities;
CREATE TABLE legal_case_success_probabilities (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY
);
GO


IF OBJECT_ID('dbo.legal_case_success_probability_languages', 'U') IS NOT NULL DROP TABLE dbo.legal_case_success_probability_languages;
GO
--legal_case_success_probability_languages;
CREATE TABLE legal_case_success_probability_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 legal_case_success_probability_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.opponents', 'U') IS NOT NULL DROP TABLE dbo.opponents;
GO
--opponents
CREATE TABLE opponents (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  company_id BIGINT DEFAULT NULL,
  contact_id BIGINT DEFAULT NULL,
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL
);
GO

CREATE UNIQUE INDEX opponent_company_id_unique_key ON opponents(company_id) WHERE company_id IS NOT NULL;
GO

CREATE UNIQUE INDEX opponent_contact_id_unique_key ON opponents(contact_id ) WHERE contact_id IS NOT NULL;
GO

IF OBJECT_ID('dbo.case_configurations', 'U') IS NOT NULL DROP TABLE dbo.case_configurations;
GO
--case_configurations;
CREATE TABLE case_configurations (
 keyName VARCHAR( 255 ) NOT NULL,
 keyValue NVARCHAR(MAX) NULL DEFAULT NULL
);
GO

--login_history_logs
IF OBJECT_ID('dbo.login_history_logs', 'U') IS NOT NULL DROP TABLE dbo.login_history_logs;
GO
CREATE TABLE login_history_logs (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 user_id BIGINT DEFAULT NULL,
 userLogin nvarchar(255) DEFAULT NULL,
 action nvarchar(6) NOT NULL CHECK(action IN ('login', 'logout')),
 source_ip nvarchar(45) NOT NULL,
 log_message nvarchar(255) NOT NULL,
 log_message_status nvarchar(255) NOT NULL,
 logDate datetime NOT NULL,
 user_agent nvarchar(120) NOT NULL
);
GO

--company_signature_authorities;
IF OBJECT_ID('dbo.company_signature_authorities', 'U') IS NOT NULL DROP TABLE dbo.company_signature_authorities;
GO

CREATE TABLE company_signature_authorities (
     id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    company_id BIGINT NOT NULL,
    sa_id BIGINT NOT NULL,
    sa_type nvarchar(20) NOT NULL CHECK(sa_type IN ('companies', 'contacts')),
    authorized_signatory NVARCHAR(MAX),
	kind_of_signature NVARCHAR(MAX) NOT NULL,
	joint_signature_with NVARCHAR(MAX),
    sole_signature NVARCHAR(MAX),
    capacity NVARCHAR(MAX),
    term_of_the_authorization  NVARCHAR(MAX),
);
GO

--login_history_log_archives
IF OBJECT_ID('dbo.login_history_log_archives', 'U') IS NOT NULL DROP TABLE dbo.login_history_log_archives;
GO
CREATE TABLE login_history_log_archives (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 user_id BIGINT DEFAULT NULL,
 userLogin nvarchar(255) DEFAULT NULL,
 action nvarchar(6) NOT NULL CHECK(action IN ('login', 'logout')),
 source_ip nvarchar(45) NOT NULL,
 log_message nvarchar(255) NOT NULL,
 log_message_status nvarchar(255) NOT NULL,
 logDate datetime NOT NULL,
 user_agent nvarchar(120) NOT NULL
);
GO

--user_reports
IF OBJECT_ID('dbo.user_reports', 'U') IS NOT NULL DROP TABLE dbo.user_reports;
GO
CREATE TABLE user_reports (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 user_id  BIGINT NOT NULL,
 keyName VARCHAR( 255 ) NOT NULL,
 keyValue NVARCHAR(MAX) NULL DEFAULT NULL
);
GO
CREATE UNIQUE INDEX unique_key ON user_reports(user_id,keyName);
GO

--shared_reports
IF OBJECT_ID('dbo.shared_reports', 'U') IS NOT NULL DROP TABLE dbo.shared_reports;
GO
CREATE TABLE shared_reports (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 user_id  BIGINT NOT NULL,
 report_id BIGINT NOT NULL
);
GO

IF OBJECT_ID('dbo.user_changes_authorization', 'U') IS NOT NULL DROP TABLE dbo.user_changes_authorization;
GO
CREATE TABLE user_changes_authorization (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 changeType nvarchar(30) DEFAULT NULL,
 columnName nvarchar(30) DEFAULT NULL,
 columnValue nvarchar(max) DEFAULT NULL,
 columnStatus nvarchar(255) DEFAULT NULL,
 columnRequestedValue nvarchar(max) DEFAULT NULL,
 columnType nvarchar(30) DEFAULT NULL,
 affectedUserId BIGINT DEFAULT NULL,
 makerId BIGINT DEFAULT NULL,
 checkerId BIGINT DEFAULT NULL,
 createdOn smalldatetime DEFAULT NULL,
 authorizedOn smalldatetime DEFAULT NULL,
);
GO

IF OBJECT_ID('dbo.user_groups_changes_authorization', 'U') IS NOT NULL DROP TABLE dbo.user_groups_changes_authorization;
GO
CREATE TABLE user_groups_changes_authorization (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 changeType nvarchar(30) DEFAULT NULL,
 columnName nvarchar(30) DEFAULT NULL,
 columnValue nvarchar(max) DEFAULT NULL,
 columnStatus nvarchar(255) DEFAULT NULL,
 columnRequestedValue nvarchar(max) DEFAULT NULL,
 columnType nvarchar(30) DEFAULT NULL,
 affectedUserGroupId BIGINT DEFAULT NULL,
 makerId BIGINT DEFAULT NULL,
 checkerId BIGINT DEFAULT NULL,
 createdOn smalldatetime DEFAULT NULL,
 authorizedOn smalldatetime DEFAULT NULL,
);
GO

IF OBJECT_ID('dbo.user_group_permissions_changes_authorization', 'U') IS NOT NULL DROP TABLE dbo.user_group_permissions_changes_authorization;
GO
CREATE TABLE user_group_permissions_changes_authorization (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 columnName nvarchar(30) DEFAULT NULL,
 module nvarchar(30) DEFAULT NULL,
 columnValue nvarchar(max) DEFAULT NULL,
 columnStatus nvarchar(255) DEFAULT NULL,
 columnRequestedValue nvarchar(max) DEFAULT NULL,
 columnApprovedValue nvarchar(max) DEFAULT NULL,
 affectedUserGroupId BIGINT DEFAULT NULL,
 makerId BIGINT DEFAULT NULL,
 checkerId BIGINT DEFAULT NULL,
 createdOn smalldatetime DEFAULT NULL,
 authorizedOn smalldatetime DEFAULT NULL,
);
GO

IF OBJECT_ID('dbo.task_types_languages', 'U') IS NOT NULL DROP TABLE dbo.task_types_languages;
GO
CREATE TABLE task_types_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 task_type_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.reminder_types_languages', 'U') IS NOT NULL DROP TABLE dbo.reminder_types_languages;
GO
CREATE TABLE reminder_types_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 reminder_type_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.company_type_of_discharges', 'U') IS NOT NULL DROP TABLE dbo.company_type_of_discharges;
GO
CREATE TABLE company_type_of_discharges (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.company_discharge_social_securities', 'U') IS NOT NULL DROP TABLE dbo.company_discharge_social_securities;
GO
CREATE TABLE company_discharge_social_securities (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  company_id BIGINT NOT NULL,
  remind_id BIGINT NULL,
  type_id BIGINT NOT NULL,
  reminder_id BIGINT DEFAULT NULL,
  releasedOn date NOT NULL,
  expiresOn date DEFAULT NULL,
  reference nvarchar(255) NULL
);
GO

IF OBJECT_ID('dbo.titles', 'U') IS NOT NULL DROP TABLE dbo.titles;
GO
CREATE TABLE titles (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY
);
GO

IF OBJECT_ID('dbo.titles_languages', 'U') IS NOT NULL DROP TABLE dbo.titles_languages;
GO
CREATE TABLE titles_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 title_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.company_lawyers', 'U') IS NOT NULL DROP TABLE dbo.company_lawyers;
GO
CREATE TABLE company_lawyers (
		id BIGINT NOT NULL PRIMARY KEY IDENTITY,
		company_id BIGINT NOT NULL,
		lawyer_id BIGINT NOT NULL,
		comments NVARCHAR(MAX),
		);
IF OBJECT_ID('dbo.legal_case_opponents', 'U') IS NOT NULL DROP TABLE dbo.legal_case_opponents;
GO
CREATE TABLE legal_case_opponents (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 case_id BIGINT NOT NULL,
 opponent_id BIGINT NOT NULL,
 opponent_member_type nvarchar(255) NOT NULL,
 opponent_position BIGINT null
);
GO

IF OBJECT_ID('dbo.workflows', 'U') IS NOT NULL DROP TABLE dbo.workflows;
GO

CREATE TABLE workflows (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL,
 type nvarchar(255) NOT NULL,
 isDeleted TINYINT NOT NULL DEFAULT '0',
 category nvarchar(50)  NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.workflow_status', 'U') IS NOT NULL DROP TABLE dbo.workflow_status;
GO

CREATE TABLE workflow_status (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL,
 isGlobal tinyint NULL DEFAULT '0',
 category nvarchar(255) NOT NULL DEFAULT 'in progress'
);
GO
IF OBJECT_ID('dbo.workflow_case_types', 'U') IS NOT NULL DROP TABLE dbo.workflow_case_types;
GO

CREATE TABLE workflow_case_types (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 workflow_id BIGINT NOT NULL,
 case_type_id BIGINT NOT NULL
);
GO

IF OBJECT_ID('dbo.workflow_status_transition', 'U') IS NOT NULL DROP TABLE dbo.workflow_status_transition;
GO

CREATE TABLE workflow_status_transition (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 workflow_id BIGINT NOT NULL,
 fromStep BIGINT NOT NULL,
 toStep BIGINT NOT NULL,
 limitToGroup BIGINT DEFAULT NULL,
 limitToUser BIGINT DEFAULT NULL,
 name nvarchar(255) NOT NULL,
 comments text NULL DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.grid_saved_filters', 'U') IS NOT NULL DROP TABLE dbo.grid_saved_filters;
GO

CREATE TABLE grid_saved_filters (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 model nvarchar(255) NOT NULL,
 user_id BIGINT DEFAULT NULL,
 filterName nvarchar(255) NOT NULL,
 formData text NOT NULL,
 isGlobalFilter tinyint NOT NULL DEFAULT '0',
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.grid_saved_filters_users', 'U') IS NOT NULL DROP TABLE dbo.grid_saved_filters_users;
GO

CREATE TABLE grid_saved_filters_users (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 filter_id BIGINT DEFAULT NULL,
 user_id BIGINT DEFAULT NULL,
 model nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.workflow_status_transition_history', 'U') IS NOT NULL DROP TABLE dbo.workflow_status_transition_history;
GO

CREATE TABLE workflow_status_transition_history (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 legal_case_id BIGINT NOT NULL,
 fromStep BIGINT DEFAULT NULL,
 toStep BIGINT NOT NULL,
 user_id BIGINT DEFAULT NULL,
 changedOn smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
 modifiedByChannel nvarchar(3) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.customer_portal_users', 'U') IS NOT NULL DROP TABLE dbo.customer_portal_users;
GO

CREATE TABLE customer_portal_users (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contact_id BIGINT NULL,
 type nvarchar(15) NOT NULL DEFAULT 'client',
 isAd char(1) NULL DEFAULT '0',
 isA4Luser char(1) NULL DEFAULT '0',
 username nvarchar(255) NOT NULL,
 email nvarchar(255) NOT NULL,
 password nvarchar(255) NULL,
 status nvarchar(45) NOT NULL,
 firstName nvarchar(255) DEFAULT NULL,
 lastName nvarchar(255) DEFAULT NULL,
 employeeId nvarchar(255) DEFAULT NULL,
 userCode nvarchar(255) DEFAULT NULL,
 department nvarchar(255) DEFAULT NULL,
 jobTitle nvarchar(255) DEFAULT NULL,
 phone nvarchar(255) DEFAULT NULL,
 mobile nvarchar(255) DEFAULT NULL,
 address nvarchar(255) DEFAULT NULL,
 banned char(1) NULL,
 ban_reason nvarchar(255) DEFAULT NULL,
 last_ip nvarchar(45) DEFAULT NULL,
 last_login smalldatetime DEFAULT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL,
 userDirectory nvarchar(255) DEFAULT NULL,
 approved char(1) DEFAULT 1,
 flag_change_password TINYINT NULL DEFAULT '0',
);
GO

IF OBJECT_ID('dbo.manage_non_business_days', 'U') IS NOT NULL DROP TABLE dbo.manage_non_business_days;
GO

CREATE TABLE manage_non_business_days (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 targetDate date DEFAULT NULL,
 comments text
);
GO

IF OBJECT_ID('dbo.customer_portal_permissions', 'U') IS NOT NULL DROP TABLE dbo.customer_portal_permissions;
GO
CREATE TABLE customer_portal_permissions (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 workflow_id BIGINT NOT NULL,
 workflow_status_transition_id BIGINT NOT NULL
);
GO

IF OBJECT_ID('dbo.documents_management_system', 'U') IS NOT NULL DROP TABLE dbo.documents_management_system;
GO

CREATE TABLE documents_management_system (
  id BIGINT PRIMARY KEY IDENTITY,
  type NVARCHAR(6) NOT NULL,
  name NVARCHAR(255) NOT NULL,
  extension NVARCHAR(6) DEFAULT NULL,
  parent BIGINT DEFAULT NULL,
  lineage NVARCHAR(MAX) DEFAULT NULL,
  size BIGINT DEFAULT NULL,
  version BIGINT DEFAULT NULL,
  private TINYINT DEFAULT NULL,
  document_type_id BIGINT DEFAULT NULL,
  document_status_id BIGINT DEFAULT NULL,
  comment NVARCHAR(MAX) DEFAULT NULL,
  module NVARCHAR(255) NOT NULL,
  module_record_id BIGINT DEFAULT NULL,
  system_document TINYINT NOT NULL DEFAULT 0,
  visible TINYINT NOT NULL DEFAULT 1,
  visible_in_cp TINYINT NOT NULL DEFAULT 0,
  visible_in_ap TINYINT NOT NULL DEFAULT 0,
  createdOn SMALLDATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  createdBy BIGINT NOT NULL,
  createdByChannel NVARCHAR(3) NOT NULL,
  initial_version_created_on SMALLDATETIME DEFAULT NULL,
  initial_version_created_by BIGINT DEFAULT NULL,
  initial_version_created_by_channel NVARCHAR(3) DEFAULT NULL,
  modifiedOn SMALLDATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  modifiedBy BIGINT NOT NULL,
  modifiedByChannel NVARCHAR(3) NOT NULL,
  is_locked TINYINT NULL DEFAULT 0,
  last_locked_by BIGINT DEFAULT NULL,
  last_locked_by_channel nvarchar(3) DEFAULT NULL,
  last_locked_on smalldatetime DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.instance_data', 'U') IS NOT NULL DROP TABLE dbo.instance_data;
GO
CREATE TABLE instance_data (
 keyName VARCHAR( 255 ) NOT NULL,
 keyValue NVARCHAR(MAX) NULL DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.customer_portal_screens', 'U') IS NOT NULL DROP TABLE dbo.customer_portal_screens;
GO
CREATE TABLE customer_portal_screens (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 case_type_id BIGINT NOT NULL,
 name nvarchar( 255 ) NOT NULL,
 description text DEFAULT NULL,
 showInPortal CHAR( 1 ) NULL DEFAULT '1',
 applicable_on VARCHAR(255) NOT NULL,
 request_type_category_id BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.customer_portal_screen_fields', 'U') IS NOT NULL DROP TABLE dbo.customer_portal_screen_fields;
GO
CREATE TABLE customer_portal_screen_fields (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 customer_portal_screen_id BIGINT NOT NULL,
 relatedCaseField nvarchar( 255 ) NOT NULL,
 isRequired tinyint NOT NULL,
 visible tinyint NOT NULL DEFAULT 1,
 requiredDefaultValue nvarchar( 255 ) DEFAULT NULL,
 fieldDescription nvarchar( 255 ) DEFAULT NULL,
 sortOrder INT NOT NULL DEFAULT(0),
);
GO

IF OBJECT_ID('dbo.customer_portal_screen_field_languages', 'U') IS NOT NULL DROP TABLE dbo.customer_portal_screen_field_languages;
GO
CREATE TABLE customer_portal_screen_field_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 customer_portal_screen_field_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 labelName nvarchar( 255 ) NOT NULL
);
GO

IF OBJECT_ID('dbo.legal_cases_countries_renewals', 'U') IS NOT NULL DROP TABLE dbo.legal_cases_countries_renewals;
GO
CREATE TABLE legal_cases_countries_renewals (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    intellectual_property_id BIGINT NOT NULL,
    comments NVARCHAR(MAX),
    expiryDate date DEFAULT NULL,
    renewalDate date DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.intellectual_property_rights', 'U') IS NOT NULL DROP TABLE dbo.intellectual_property_rights;
GO
CREATE TABLE intellectual_property_rights (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.ip_classes', 'U') IS NOT NULL DROP TABLE dbo.ip_classes;
GO
CREATE TABLE ip_classes (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.ip_petitions_oppositions_types', 'U') IS NOT NULL DROP TABLE dbo.ip_petitions_oppositions_types;
GO
CREATE TABLE ip_petitions_oppositions_types (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.ip_subcategories', 'U') IS NOT NULL DROP TABLE dbo.ip_subcategories;
GO
CREATE TABLE ip_subcategories (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.customer_portal_sla', 'U') IS NOT NULL DROP TABLE dbo.customer_portal_sla;
GO
CREATE TABLE customer_portal_sla (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 workflow_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL,
 target nvarchar(255) NOT NULL,
 start nvarchar(255) NOT NULL,
 pause nvarchar(255) DEFAULT NULL,
 stop nvarchar(255) NOT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO


ALTER TABLE dbo.customer_portal_sla ADD
	priority nvarchar(8) NULL,
	case_type_id bigint NULL,
	client_id bigint NULL
GO

ALTER TABLE dbo.customer_portal_sla ADD CONSTRAINT
	FK_customer_portal_sla_case_types FOREIGN KEY
	(
	case_type_id
	) REFERENCES dbo.case_types
	(
	id
	) ON UPDATE  NO ACTION 
	 ON DELETE  NO ACTION 
	
GO
IF OBJECT_ID('dbo.customer_portal_sla_cases', 'U') IS NOT NULL DROP TABLE dbo.customer_portal_sla_cases;
GO
CREATE TABLE customer_portal_sla_cases (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 customer_portal_sla_id BIGINT NOT NULL,
 cycle BIGINT DEFAULT NULL,
 case_id BIGINT NOT NULL,
 action nvarchar(255) DEFAULT NULL,
 actionDate smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL,
 modifiedByChannel nvarchar(3) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.organizations', 'U') IS NOT NULL DROP TABLE dbo.organizations;
GO
CREATE TABLE organizations (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL,
  currency_id BIGINT NOT NULL,
  color BIGINT NOT NULL,
  fiscalYearStartsOn tinyint NOT NULL,
  address1 nvarchar(255) NULL DEFAULT NULL,
  address2 nvarchar(255) NULL DEFAULT NULL,
  city nvarchar(255) NULL DEFAULT NULL,
  state nvarchar(255) NULL DEFAULT NULL,
  zip nvarchar(32) NULL DEFAULT NULL,
  country_id BIGINT NULL DEFAULT NULL,
  website nvarchar(255) NULL DEFAULT NULL,
  phone nvarchar(255) NULL DEFAULT NULL,
  fax nvarchar(255) NULL DEFAULT NULL,
  tax_number nvarchar(255) NULL DEFAULT NULL,
  mobile nvarchar(255) NULL DEFAULT NULL,
  organizationID nvarchar(255) NULL DEFAULT NULL,
  e_invoicing nvarchar(50) NULL DEFAULT 'inactive',
  comments text NULL,
  status nvarchar(8) NOT NULL CHECK(status IN ('Active', 'Inactive')),
  additional_id_type BIGINT NULL DEFAULT NULL, 
  additional_id_value nvarchar(255) NULL DEFAULT NULL,
  street_name nvarchar(255) NULL DEFAULT NULL,
  building_number nvarchar(255) NULL DEFAULT NULL, 
  address_additional_number nvarchar(255) NULL DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.accounts_types', 'U') IS NOT NULL DROP TABLE dbo.accounts_types;
GO
CREATE TABLE accounts_types (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(128) NOT NULL,
  type nvarchar(11) NOT NULL CHECK(type IN ('Asset', 'Equity', 'Expense', 'Income', 'Liability', 'Other', 'Third Party')),
  is_visible TINYINT NOT NULL DEFAULT '1'
);
GO

IF OBJECT_ID('dbo.accounts', 'U') IS NOT NULL DROP TABLE dbo.accounts;
GO
CREATE TABLE accounts (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  organization_id BIGINT NOT NULL,
  currency_id BIGINT NOT NULL,
  account_type_id BIGINT NOT NULL,
  name nvarchar(255) NOT NULL,
  systemAccount nvarchar(3) NOT NULL CHECK(systemAccount IN ('yes','no')),
  has_open_balance char(1) NULL,
  description text NULL,
  model_id BIGINT NULL,
  member_id BIGINT NULL,
  model_name nvarchar(255) NULL,
  model_type nvarchar(8) NOT NULL CHECK(model_type IN ('internal', 'client', 'supplier', 'partner')),
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL,
  accountData text,
  number BIGINT NOT NULL,
  show_in_dashboard TINYINT NOT NULL DEFAULT '1'
);
GO

IF OBJECT_ID('dbo.accounts_users', 'U') IS NOT NULL DROP TABLE dbo.accounts_users;
GO
CREATE TABLE accounts_users (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  userId BIGINT NOT NULL,
  accountId BIGINT NOT NULL
);
GO

IF OBJECT_ID('dbo.taxes', 'U') IS NOT NULL DROP TABLE dbo.taxes;
GO
CREATE TABLE taxes (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  code nvarchar(255) NOT NULL DEFAULT '',
  account_id BIGINT NOT NULL,
  name nvarchar(255) NOT NULL,
  fl1name nvarchar(255) NULL,
  fl2name nvarchar(255) NULL,
  description text,
  percentage decimal(10,2) NOT NULL
);
GO

IF OBJECT_ID('dbo.supplier_taxes', 'U') IS NOT NULL DROP TABLE dbo.supplier_taxes;
GO
CREATE TABLE supplier_taxes (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  account_id BIGINT NOT NULL,
  name nvarchar(255) NOT NULL,
  fl1name nvarchar(255) NULL,
  fl2name nvarchar(255) NULL,
  description text,
  percentage decimal(10,2) NOT NULL
);
GO

IF OBJECT_ID('dbo.discounts', 'U') IS NOT NULL DROP TABLE dbo.discounts;
GO
CREATE TABLE discounts (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL,
  fl1name nvarchar(255) NULL,
  fl2name nvarchar(255) NULL,
  description text,
  percentage decimal(10,4) NOT NULL
);
GO

IF OBJECT_ID('dbo.terms', 'U') IS NOT NULL DROP TABLE dbo.terms;
GO
CREATE TABLE terms (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL,
  fl1name nvarchar(255) NULL,
  fl2name nvarchar(255) NULL,
  number_of_days BIGINT DEFAULT 0
);
GO

IF OBJECT_ID('dbo.vendors', 'U') IS NOT NULL DROP TABLE dbo.vendors;
GO
CREATE TABLE vendors (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  company_id BIGINT DEFAULT NULL,
  contact_id BIGINT DEFAULT NULL,
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.expense_categories', 'U') IS NOT NULL DROP TABLE dbo.expense_categories;
GO
CREATE TABLE expense_categories (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  expense_category_id BIGINT DEFAULT NULL,
  account_id BIGINT NOT NULL,
  name nvarchar(255) NOT NULL,
  fl1name nvarchar(255) NULL,
  fl2name nvarchar(255) NULL,
  amount decimal(10,2) NULL
);
GO

IF OBJECT_ID('dbo.voucher_headers', 'U') IS NOT NULL DROP TABLE dbo.voucher_headers;
GO
CREATE TABLE voucher_headers (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  organization_id BIGINT NOT NULL,
  dated date NOT NULL,
  voucherType nvarchar(6) NOT NULL ,
  refNum BIGINT NOT NULL,
  referenceNum nvarchar(255) DEFAULT NULL,
  attachment nvarchar(255) DEFAULT NULL,
  description text,
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.voucher_details', 'U') IS NOT NULL DROP TABLE dbo.voucher_details;
GO
CREATE TABLE voucher_details (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  voucher_header_id BIGINT NOT NULL,
  account_id BIGINT NOT NULL,
  drCr char(1) NOT NULL,
  local_amount decimal(22,2) NOT NULL,
  foreign_amount decimal(22,2) NOT NULL,
  description text
);
GO

IF OBJECT_ID('dbo.expenses', 'U') IS NOT NULL DROP TABLE dbo.expenses;
GO
CREATE TABLE expenses (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  voucher_header_id BIGINT NOT NULL,
  expense_category_id BIGINT NOT NULL,
  expense_account BIGINT NOT NULL,
  paid_through BIGINT NOT NULL,
  vendor_id BIGINT DEFAULT NULL,
  client_id BIGINT DEFAULT NULL,
  client_account_id BIGINT DEFAULT NULL,
  billingStatus nvarchar(12) NOT NULL CHECK(billingStatus IN ('internal', 'invoiced', 'non-billable', 'not-set', 'reimbursed', 'to-invoice')) DEFAULT 'internal',
  tax_id BIGINT NULL,
  status nvarchar(20) NOT NULL,
  amount decimal(22,2) NOT NULL,
  paymentMethod varchar(32) NOT NULL,
  task BIGINT DEFAULT NULL,
  hearing BIGINT DEFAULT NULL,
  event BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.items', 'U') IS NOT NULL DROP TABLE dbo.items;
GO
CREATE TABLE items (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  item_id BIGINT DEFAULT NULL,
  account_id BIGINT NOT NULL,
  tax_id BIGINT DEFAULT NULL,
  unitName nvarchar(255) NOT NULL,
  fl1unitName nvarchar(255) DEFAULT NULL,
  fl2unitName nvarchar(255) DEFAULT NULL,
  unitPrice decimal(10,2) NOT NULL,
  description text
);
GO

IF OBJECT_ID('dbo.invoice_headers', 'U') IS NOT NULL DROP TABLE dbo.invoice_headers;
GO
CREATE TABLE invoice_headers (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  voucher_header_id BIGINT NOT NULL,
  original_invoice_id BIGINT NULL DEFAULT NULL,
  account_id BIGINT NOT NULL,
  billTo text,
  invoice_type_id BIGINT NULL DEFAULT NULL,
  transaction_type_id BIGINT NULL DEFAULT NULL,
  payment_method_id BIGINT NULL DEFAULT NULL,
  term_id BIGINT NOT NULL,
  prefix nvarchar(32) NOT NULL,
  suffix nvarchar(32) DEFAULT NULL,
  dueOn smalldatetime NOT NULL,
  invoiceDate smalldatetime NOT NULL,
  paidStatus nvarchar(14) NOT NULL CHECK(paidStatus IN ('draft', 'open', 'partially paid', 'paid', 'cancelled')),
  purchaseOrder nvarchar(255) DEFAULT NULL,
  total decimal(22,2) NOT NULL,
  invoiceNumber varchar(255) DEFAULT NULL,
  notes text,
  displayTax tinyint DEFAULT NULL,
  displayDiscount nvarchar(30) DEFAULT NULL,
  groupTimeLogsByUserInExport char(1) DEFAULT NULL,
  related_quote_id BIGINT  DEFAULT NULL,
  display_item_date tinyint NOT NULL DEFAULT 0,
  display_item_quantity tinyint NULL DEFAULT 1,
  exchangeRate decimal(22,10) DEFAULT NULL,
  discount_id BIGINT DEFAULT NULL,
  discount_percentage decimal(22,10) DEFAULT NULL,
  discount_amount decimal(22,2) DEFAULT NULL,
  discount_value_type VARCHAR(10) DEFAULT NULL,
  description text DEFAULT NULL,
  draft_invoice_number BIGINT DEFAULT NULL,
  debit_note_reason_id BIGINT NULL DEFAULT NULL,
  lines_total_discount DECIMAL(32,12) NOT NULL ,
  lines_total_subtotal DECIMAL(32,12) NOT NULL, 
  lines_total_tax DECIMAL(32,12) NOT NULL, 
  lines_totals DECIMAL(32,12) NOT NULL,
  invoice_template_id BIGINT NULL DEFAULT NULL,
);
GO

IF OBJECT_ID('dbo.invoice_details', 'U') IS NOT NULL DROP TABLE dbo.invoice_details;
GO
CREATE TABLE invoice_details (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  invoice_header_id BIGINT NOT NULL,
  account_id BIGINT NOT NULL,
  item_id BIGINT DEFAULT NULL,
  sub_item_id BIGINT DEFAULT NULL,
  tax_id BIGINT DEFAULT NULL,
  discount_id BIGINT DEFAULT NULL,
  expense_id BIGINT DEFAULT NULL,
  item nvarchar(255) NOT NULL,
  unitPrice decimal(22,2) NOT NULL,
  quantity decimal(22,2) NOT NULL,
  itemDescription text DEFAULT NULL,
  percentage decimal(10,2) DEFAULT NULL,
discountPercentage decimal(22,10) DEFAULT NULL,
item_date DATE DEFAULT NULL,
discountAmount decimal(22,2) DEFAULT NULL,
discount_type VARCHAR(10) NULL DEFAULT NULL,  
line_sub_total DECIMAL(32,12) NOT NULL ,  
sub_total_after_line_disc DECIMAL(32,12) NOT NULL,   
tax_amount DECIMAL(32,12) NOT NULL ,  
total DECIMAL(32,12) NOT NULL,  
);
GO

IF OBJECT_ID('dbo.invoice_notes', 'U') IS NOT NULL DROP TABLE dbo.invoice_notes;
GO
CREATE TABLE invoice_notes (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(128) NOT NULL,
  description text NOT NULL,
);
GO

IF OBJECT_ID('dbo.invoice_payments', 'U') IS NOT NULL DROP TABLE dbo.invoice_payments;
GO
CREATE TABLE invoice_payments (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  voucher_header_id BIGINT NOT NULL,
  account_id BIGINT NOT NULL,
  paymentMethod nvarchar(15) NOT NULL CHECK(paymentMethod IN ('Bank Transfer','Cash','Cheque','Credit Card','Other','Online payment', 'Trust Account')),
  total decimal(22,2) NOT NULL,
  client_account_id BIGINT NOT NULL,
  invoicePaymentTotal decimal(22,2) NOT NULL,
  exchangeRate decimal(22,10) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.invoice_payment_invoices', 'U') IS NOT NULL DROP TABLE dbo.invoice_payment_invoices;
GO
CREATE TABLE invoice_payment_invoices (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  invoice_payment_id BIGINT NOT NULL,
  invoice_header_id BIGINT NOT NULL,
  amount decimal(22,2) NOT NULL
);
GO

IF OBJECT_ID('dbo.organization_invoice_templates', 'U') IS NOT NULL DROP TABLE dbo.organization_invoice_templates;
GO
CREATE TABLE organization_invoice_templates (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  organization_id BIGINT NOT NULL,
  name nvarchar(255) NOT NULL ,
  settings TEXT NULL,
  type nvarchar(10) NULL DEFAULT  'invoice',
  is_default TINYINT NOT NULL DEFAULT '0'
);
GO

IF OBJECT_ID('dbo.user_activity_log_invoicing_statuses', 'U') IS NOT NULL DROP TABLE dbo.user_activity_log_invoicing_statuses;
GO
CREATE TABLE user_activity_log_invoicing_statuses (
  id BIGINT NOT NULL,
  log_invoicing_statuses nvarchar(12) NOT NULL CHECK(log_invoicing_statuses IN ('non-billable','to-invoice','invoiced','reimbursed'))
);
GO

IF OBJECT_ID('dbo.user_rate_per_hour', 'U') IS NOT NULL DROP TABLE dbo.user_rate_per_hour;
GO
CREATE TABLE user_rate_per_hour (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  user_id BIGINT NOT NULL,
  organization_id BIGINT NOT NULL,
  ratePerHour decimal(10,2) NOT NULL,
  yearly_billable_target INT NOT NULL DEFAULT 120000,
  working_days_per_year INT NOT NULL DEFAULT 260,
);
GO

IF OBJECT_ID('dbo.user_rate_per_hour_per_case', 'U') IS NOT NULL DROP TABLE dbo.user_rate_per_hour_per_case;
GO
CREATE TABLE user_rate_per_hour_per_case (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  user_id BIGINT NOT NULL,
  case_id BIGINT NOT NULL,
  organization_id BIGINT NOT NULL,
  ratePerHour decimal(10,2) NOT NULL
);
GO

IF OBJECT_ID('dbo.bill_headers', 'U') IS NOT NULL DROP TABLE dbo.bill_headers;
GO
CREATE TABLE bill_headers (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  voucher_header_id BIGINT NOT NULL,
  account_id BIGINT NOT NULL,
  status nvarchar(14) NOT NULL CHECK(status IN ('open','partially paid','paid')),
  dueDate smalldatetime NOT NULL,
  total decimal(22,2) NOT NULL,
  displayTax tinyint DEFAULT NULL,
  client_id BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.bill_details', 'U') IS NOT NULL DROP TABLE dbo.bill_details;
GO
CREATE TABLE bill_details (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  bill_header_id BIGINT NOT NULL,
  account_id BIGINT NOT NULL,
  description text NOT NULL,
  quantity decimal(22,2) NOT NULL,
  price decimal(22,2) NOT NULL,
  basePrice decimal(22,2) NOT NULL,
  tax_id BIGINT DEFAULT NULL,
  percentage decimal(10,2) DEFAULT NULL,
);
GO


CREATE INDEX bill_header_id ON bill_details (bill_header_id);
GO
CREATE INDEX account_id ON bill_details (account_id);
GO
CREATE INDEX module_record_id ON documents_management_system (module_record_id);
GO
CREATE INDEX parent ON documents_management_system (parent);
GO

IF OBJECT_ID('dbo.bill_payments', 'U') IS NOT NULL DROP TABLE dbo.bill_payments;
GO
CREATE TABLE bill_payments (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  voucher_header_id BIGINT NOT NULL,
  account_id BIGINT NOT NULL,
  paymentMethod nvarchar(15) NOT NULL CHECK(paymentMethod IN ('Bank Transfer','Cash','Cheque','Credit Card','Other','Online payment')),
  total decimal(22,2) NOT NULL,
  supplier_account_id BIGINT NOT NULL,
  billPaymentTotal decimal(22,2) NOT NULL
);
GO

IF OBJECT_ID('dbo.bill_payment_bills', 'U') IS NOT NULL DROP TABLE dbo.bill_payment_bills;
GO
CREATE TABLE bill_payment_bills (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  bill_payment_id BIGINT NOT NULL,
  bill_header_id BIGINT NOT NULL,
  amount decimal(22,2) NOT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_commissions', 'U') IS NOT NULL DROP TABLE dbo.legal_case_commissions;
GO
CREATE TABLE legal_case_commissions (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 case_id BIGINT NOT NULL,
 account_id BIGINT NOT NULL,
 commission BIGINT NOT NULL,
);
GO

IF OBJECT_ID('dbo.partners', 'U') IS NOT NULL DROP TABLE dbo.partners;
GO
CREATE TABLE partners (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  company_id BIGINT DEFAULT NULL,
  contact_id BIGINT DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  createdOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  isThirdParty nvarchar(10) NOT NULL CHECK(isThirdParty IN ('no','yes')) DEFAULT 'no'
);
GO

IF OBJECT_ID('dbo.item_commissions', 'U') IS NOT NULL DROP TABLE dbo.item_commissions;
GO

CREATE TABLE item_commissions (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  invoice_header_id BIGINT NOT NULL,
  invoice_details_id BIGINT NOT NULL,
  item_id BIGINT DEFAULT NULL,
  sub_item_id BIGINT DEFAULT NULL,
  expense_id BIGINT DEFAULT NULL,
  time_logs_id BIGINT DEFAULT NULL,
  account_id BIGINT NOT NULL,
  commission decimal(5,2) NOT NULL
);
GO

IF OBJECT_ID('dbo.invoice_detail_look_feel_section', 'U') IS NOT NULL DROP TABLE dbo.invoice_detail_look_feel_section;
GO
CREATE TABLE invoice_detail_look_feel_section (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    name varchar(255) NOT NULL,
    fl1name varchar(255) NOT NULL,
    fl2name varchar(255) NOT NULL,
    content varchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.invoice_detail_cover_page_template', 'U') IS NOT NULL DROP TABLE dbo.invoice_detail_cover_page_template;
GO
CREATE TABLE invoice_detail_cover_page_template (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    organization_id BIGINT NOT NULL,
    name varchar(255) NOT NULL,
    header varchar(255) NOT NULL,
    subHeader varchar(255) NOT NULL,
    footer varchar(255) NOT NULL,
    address TEXT NOT NULL,
    logo varchar(255) DEFAULT NULL,
	email varchar(255) DEFAULT NULL
);
GO
IF OBJECT_ID('dbo.email_notifications_scheme', 'U') IS NOT NULL DROP TABLE dbo.email_notifications_scheme;
GO

--email_notifications_scheme;
CREATE TABLE email_notifications_scheme (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 trigger_action nvarchar(255) NOT NULL,
 notify_to TEXT NOT NULL,
 notify_cc TEXT NOT NULL,
 createdBy BIGINT DEFAULT NULL,
 createdOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 hide_show_send_email_notification tinyint DEFAULT 1 NOT NULL
);
GO

IF OBJECT_ID('dbo.ip_details', 'U') IS NOT NULL DROP TABLE dbo.ip_details;
GO
CREATE TABLE ip_details (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    intellectual_property_right_id  BIGINT DEFAULT NULL,
    ip_class_id BIGINT DEFAULT NULL,
    ip_subcategory_id BIGINT DEFAULT NULL,
    ip_status_id BIGINT DEFAULT NULL,
    ip_name_id BIGINT DEFAULT NULL,
    filingNumber nvarchar(255) DEFAULT NULL,
    acceptanceRejection date DEFAULT NULL,
    certificationNumber nvarchar(255) DEFAULT NULL,
    registrationReference nvarchar(255) DEFAULT NULL,
    registrationDate date DEFAULT NULL,
    agentId BIGINT DEFAULT NULL,
    agentType nvarchar(255) DEFAULT NULL,
    country_id BIGINT DEFAULT NULL,
    legal_case_id BIGINT NOT NULL
);
GO

IF OBJECT_ID('dbo.ip_petitions_oppositions', 'U') IS NOT NULL DROP TABLE dbo.ip_petitions_oppositions;
GO
CREATE TABLE ip_petitions_oppositions (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    type BIGINT NOT NULL,
    description nvarchar(MAX) DEFAULT NULL,
    arrivalDate date DEFAULT NULL,
    dueDate date DEFAULT NULL,
    agentId BIGINT DEFAULT NULL,
    agentType nvarchar(255) DEFAULT NULL,
    user_id BIGINT DEFAULT NULL,
    result nvarchar(255) DEFAULT NULL,
    ip_detail_id BIGINT NOT NULL,
);
GO

IF OBJECT_ID('dbo.ip_statuses', 'U') IS NOT NULL DROP TABLE dbo.ip_statuses;
GO
CREATE TABLE ip_statuses (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL,
  category nvarchar(255) NOT NULL DEFAULT 'in progress',
);
GO

IF OBJECT_ID('dbo.ip_names', 'U') IS NOT NULL DROP TABLE dbo.ip_names;
GO
CREATE TABLE ip_names (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.events', 'U') IS NOT NULL DROP TABLE dbo.events;
GO
CREATE TABLE events (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  legal_case_id BIGINT DEFAULT NULL,
  start_date date NOT NULL,
  start_time time(0) NOT NULL,
  end_date date NOT NULL,
  end_time time(0) NOT NULL,
  private char(3) DEFAULT NULL,
  priority nvarchar(8) CHECK(priority IN ('critical','high','medium','low')) DEFAULT 'medium',
  task_location_id BIGINT DEFAULT NULL,
  title nvarchar(255) NOT NULL,
  description TEXT,
  calendar_id nvarchar(255) DEFAULT NULL,
  integration_id nvarchar(255) DEFAULT NULL,
  integration_type nvarchar(255) DEFAULT NULL,
  event_type_id BIGINT DEFAULT NULL,
  created_from  nvarchar(255) DEFAULT NULL,
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.events_attendees', 'U') IS NOT NULL DROP TABLE dbo.events_attendees;
GO
CREATE TABLE events_attendees (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  event_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL,
  mandatory TINYINT NULL DEFAULT '0',
  participant TINYINT NULL DEFAULT '0'
);
GO

IF OBJECT_ID('dbo.companies_customer_portal_users', 'U') IS NOT NULL DROP TABLE dbo.companies_customer_portal_users;
GO

CREATE TABLE companies_customer_portal_users (
   id BIGINT NOT NULL PRIMARY KEY IDENTITY,
   company_id BIGINT NOT NULL,
   customer_portal_user_id BIGINT NOT NULL
);
GO

IF OBJECT_ID('dbo.customer_portal_ticket_watchers', 'U') IS NOT NULL DROP TABLE dbo.customer_portal_ticket_watchers;
GO

CREATE TABLE customer_portal_ticket_watchers (
	id BIGINT NOT NULL PRIMARY KEY IDENTITY,
	legal_case_id BIGINT NOT NULL,
	customer_portal_user_id BIGINT NOT NULL
);
GO

IF OBJECT_ID('dbo.customer_portal_container_watchers', 'U') IS NOT NULL DROP TABLE dbo.customer_portal_container_watchers;
GO

CREATE TABLE customer_portal_container_watchers (
	id BIGINT NOT NULL PRIMARY KEY IDENTITY,
	case_container_id BIGINT NOT NULL,
	customer_portal_user_id BIGINT NOT NULL
);
GO

IF OBJECT_ID('dbo.workflow_status_transition_permissions', 'U') IS NOT NULL DROP TABLE dbo.workflow_status_transition_permissions;
GO
CREATE TABLE workflow_status_transition_permissions (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  transition BIGINT NOT NULL,
  users TEXT,
  user_groups TEXT
);
GO
CREATE UNIQUE INDEX workflow_status_transition_permissions_unique_key ON workflow_status_transition_permissions( transition );
GO

IF OBJECT_ID('dbo.grid_saved_columns', 'U') IS NOT NULL DROP TABLE dbo.grid_saved_columns;
GO
CREATE TABLE grid_saved_columns (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 model nvarchar(255) NOT NULL,
 user_id BIGINT NOT NULL,
 grid_details text NOT NULL,
 grid_saved_filter_id BIGINT
);
GO

CREATE TABLE legal_case_event_types (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 sub_event tinyint NOT NULL DEFAULT '0'
);
GO

CREATE TABLE legal_case_event_types_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 event_type BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

CREATE TABLE legal_case_event_type_forms (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 event_type BIGINT NOT NULL,
 field_type BIGINT Default NULL,
 field_required tinyint NOT NULL DEFAULT '0',
 field_order BIGINT NOT NULL,
 field_key varchar(255) Default ''
);
GO

CREATE TABLE legal_case_event_type_forms_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 field BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 field_name nvarchar(255) NOT NULL,
 field_type_details text Default '',
 field_description varchar(255) Default ''
);
GO

CREATE TABLE legal_case_events_related_data (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 event BIGINT NOT NULL,
 related_id BIGINT NOT NULL,
 related_object nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_event_data_types', 'U') IS NOT NULL DROP TABLE dbo.legal_case_event_data_types;
GO
CREATE TABLE legal_case_event_data_types (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 type varchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_event_data_types_languages', 'U') IS NOT NULL DROP TABLE dbo.legal_case_event_data_types_languages;
GO
CREATE TABLE legal_case_event_data_types_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 type BIGINT NOT NULL,
 type_name varchar(255) NOT NULL,
 type_details text Default NULL,
 language_id BIGINT NOT NULL,
);
GO

IF OBJECT_ID('dbo.exporter_audit_logs', 'U') IS NOT NULL DROP TABLE dbo.exporter_audit_logs;
GO
CREATE TABLE exporter_audit_logs (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 module nvarchar(20) NOT NULL,
 module_id nvarchar(15) NOT NULL,
 exported_data text,
 created_on smalldatetime DEFAULT NULL,
 created_by BIGINT DEFAULT NULL
);
GO

CREATE TABLE invoice_time_logs_items (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 item BIGINT NOT NULL,
 time_log BIGINT NOT NULL,
 user_id BIGINT NOT NULL,
 date date NOT NULL,
 description text NULL
);
GO

IF OBJECT_ID('dbo.company_addresses', 'U') IS NOT NULL DROP TABLE dbo.company_addresses;
GO

CREATE TABLE company_addresses(
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 company BIGINT NOT NULL,
 address nvarchar(255) DEFAULT NULL,
 city nvarchar(255) DEFAULT NULL,
 state nvarchar(255) DEFAULT NULL,
 zip nvarchar(32) DEFAULT NULL,
 country BIGINT DEFAULT NULL,
 website nvarchar(255) DEFAULT NULL,
 phone nvarchar(255) DEFAULT NULL,
 fax nvarchar(255) DEFAULT NULL,
 mobile nvarchar(255) DEFAULT NULL,
 email nvarchar( 255 ) DEFAULT NULL,
  street_name nvarchar(255) NULL DEFAULT NULL, 
  additional_street_name nvarchar(255) NULL DEFAULT NULL, 
  building_number nvarchar(255) NULL DEFAULT NULL, 
  address_additional_number nvarchar(255) NULL DEFAULT NULL, 
  district_neighborhood nvarchar(255) NULL DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.legal_cases_countries_renewals_users', 'U') IS NOT NULL DROP TABLE dbo.legal_cases_countries_renewals_users;
GO
CREATE TABLE legal_cases_countries_renewals_users (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  legal_case_country_renewal_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL
);
GO
CREATE UNIQUE INDEX uk_legal_cases_countries_renewals_users_1 ON legal_cases_countries_renewals_users( legal_case_country_renewal_id, user_id);
GO

IF OBJECT_ID('dbo.document_generator', 'U') IS NOT NULL DROP TABLE dbo.document_generator;
GO
CREATE TABLE document_generator (
 keyName VARCHAR( 255 ) NOT NULL,
 keyValue NVARCHAR(MAX) NULL DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.account_number_prefix_per_entity', 'U') IS NOT NULL DROP TABLE dbo.account_number_prefix_per_entity;
GO
CREATE TABLE account_number_prefix_per_entity (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  organization_id BIGINT NOT NULL,
  account_type_id BIGINT NOT NULL,
  account_number_prefix nvarchar(10) NOT NULL
);
GO

IF OBJECT_ID('dbo.hearings_documents', 'U') IS NOT NULL DROP TABLE dbo.hearings_documents;
GO

CREATE TABLE hearings_documents (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 hearing BIGINT NOT NULL,
 document BIGINT NOT NULL
);

GO

IF OBJECT_ID('dbo.signature_authorities_documents', 'U') IS NOT NULL DROP TABLE dbo.signature_authorities_documents;
GO

CREATE TABLE signature_authorities_documents (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 signature_authority BIGINT NOT NULL,
 document BIGINT NOT NULL
);

GO

IF OBJECT_ID('dbo.company_notes', 'U') IS NOT NULL DROP TABLE dbo.company_notes;
GO
CREATE TABLE company_notes(
	id bigint PRIMARY KEY IDENTITY,
	company_id bigint NOT NULL,
	note ntext NOT NULL,
	created_by bigint NOT NULL,
	modified_by bigint NULL,
	created_on datetime2(0) NOT NULL
)

GO
IF OBJECT_ID('dbo.company_note_details', 'U') IS NOT NULL DROP TABLE dbo.company_note_details;
GO
CREATE TABLE company_note_details(
	id bigint PRIMARY KEY IDENTITY,
	company_note_id bigint NOT NULL,
	name nvarchar(255) NOT NULL,
	path nvarchar(256) NULL,
	uploaded nvarchar(3) NOT NULL
)
Go

IF OBJECT_ID('dbo.client_trust_accounts_relation', 'U') IS NOT NULL DROP TABLE dbo.client_trust_accounts_relation;
GO

CREATE TABLE client_trust_accounts_relation (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 client BIGINT NOT NULL,
 trust_liability_account BIGINT NOT NULL,
 trust_asset_account BIGINT NOT NULL,
 organization_id BIGINT NOT NULL,
);
GO

IF OBJECT_ID('dbo.deposits', 'U') IS NOT NULL DROP TABLE dbo.deposits;
GO

CREATE TABLE deposits (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 voucher_header_id BIGINT NOT NULL,
 client_trust_accounts_id BIGINT NOT NULL,
 foreign_amount decimal(22,2) NOT NULL,
 currency BIGINT NOT NULL,
 payment_method nvarchar(15) NOT NULL,
);
GO

IF OBJECT_ID('dbo.expense_status_notes', 'U') IS NOT NULL DROP TABLE dbo.expense_status_notes;
GO
CREATE TABLE expense_status_notes (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  expense_id BIGINT  NOT NULL,
  note TEXT,
  transition TEXT   NOT NULL,
  createdOn smalldatetime NOT NULL,
  createdBy BIGINT NOT NULL,
  modifiedOn smalldatetime NOT NULL,
  modifiedBy BIGINT NOT NULL,
);
GO

IF OBJECT_ID('dbo.quote_headers', 'U') IS NOT NULL DROP TABLE dbo.quote_headers;
GO
CREATE TABLE quote_headers (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  voucher_header_id BIGINT NOT NULL,
  account_id BIGINT NOT NULL,
  billTo text,
  term_id BIGINT NOT NULL,
  prefix nvarchar(32) NOT NULL,
  suffix nvarchar(32) DEFAULT NULL,
  dueOn smalldatetime NOT NULL,
  quoteDate smalldatetime NOT NULL,
  paidStatus nvarchar(14) NOT NULL ,
  purchaseOrder nvarchar(255) DEFAULT NULL,
  total decimal(22,2) NOT NULL,
  quoteNumber varchar(255) DEFAULT NULL,
  notes text,
  displayTax tinyint DEFAULT NULL,
  displayDiscount tinyint DEFAULT NULL,
  groupTimeLogsByUserInExport char(1) DEFAULT NULL,
  related_invoice_id BIGINT  DEFAULT NULL,
  display_item_date tinyint NOT NULL DEFAULT 0,
  description text DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.quote_details', 'U') IS NOT NULL DROP TABLE dbo.quote_details;
GO
CREATE TABLE quote_details (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  quote_header_id BIGINT NOT NULL,
  account_id BIGINT NOT NULL,
  item_id BIGINT DEFAULT NULL,
  sub_item_id BIGINT DEFAULT NULL,
  tax_id BIGINT DEFAULT NULL,
  discount_id BIGINT DEFAULT NULL,
  expense_id BIGINT DEFAULT NULL,
  item nvarchar(255) NOT NULL,
  unitPrice decimal(22,2) NOT NULL,
  quantity decimal(22,2) NOT NULL,
  itemDescription text NOT NULL,
  percentage decimal(10,2) DEFAULT NULL,
  discountPercentage decimal(10,4) DEFAULT NULL,
  item_date DATE DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.quote_status_notes', 'U') IS NOT NULL DROP TABLE dbo.quote_status_notes;
GO
CREATE TABLE quote_status_notes (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  quote_id BIGINT  NOT NULL,
  note TEXT,
  transition TEXT   NOT NULL,
  createdOn smalldatetime NOT NULL,
  createdBy BIGINT NOT NULL,
  modifiedOn smalldatetime NOT NULL,
  modifiedBy BIGINT NOT NULL,
);
GO

IF OBJECT_ID('dbo.quote_time_logs_items', 'U') IS NOT NULL DROP TABLE dbo.quote_time_logs_items;
GO
CREATE TABLE quote_time_logs_items (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 item BIGINT NOT NULL,
 time_log BIGINT NOT NULL,
 user_id BIGINT NOT NULL,
 date date NOT NULL,
 description text NULL
);
GO

IF OBJECT_ID('dbo.hearing_types', 'U') IS NOT NULL DROP TABLE dbo.hearing_types;
GO
CREATE TABLE hearing_types (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY
);
GO

IF OBJECT_ID('dbo.hearing_types_languages', 'U') IS NOT NULL DROP TABLE dbo.hearing_types_languages;
GO
CREATE TABLE hearing_types_languages (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    type BIGINT NOT NULL,
    language_id BIGINT NOT NULL,
    name nvarchar(255) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.stage_statuses', 'U') IS NOT NULL DROP TABLE dbo.stage_statuses;
GO
CREATE TABLE stage_statuses (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    color nvarchar(255) NULL
);
GO

IF OBJECT_ID('dbo.stage_statuses_languages', 'U') IS NOT NULL DROP TABLE dbo.stage_statuses_languages;
GO
CREATE TABLE stage_statuses_languages (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    status BIGINT NOT NULL,
    language_id BIGINT NOT NULL,
    name nvarchar(255) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_stage_contacts', 'U') IS NOT NULL DROP TABLE dbo.legal_case_stage_contacts;
GO
CREATE TABLE legal_case_stage_contacts (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    stage BIGINT NOT NULL,
    contact BIGINT NOT NULL,
    contact_role BIGINT NULL,
    comments TEXT,
    contact_type nvarchar(30) NOT NULL,
    createdOn smalldatetime DEFAULT NULL,
    createdBy BIGINT DEFAULT NULL,
    modifiedOn smalldatetime DEFAULT NULL,
    modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_litigation_stages_opponents', 'U') IS NOT NULL DROP TABLE dbo.legal_case_litigation_stages_opponents;
GO
CREATE TABLE legal_case_litigation_stages_opponents (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    stage BIGINT NOT NULL,
    opponent_id BIGINT NOT NULL,
    opponent_position BIGINT null
);
GO
IF OBJECT_ID('dbo.event_types', 'U') IS NOT NULL DROP TABLE dbo.event_types;
GO
--event_types;
CREATE TABLE event_types (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY
);
GO
IF OBJECT_ID('dbo.event_types_languages', 'U') IS NOT NULL DROP TABLE dbo.event_types_languages;
GO
CREATE TABLE event_types_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 event_type_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.workflow_status_relation', 'U') IS NOT NULL DROP TABLE dbo.workflow_status_relation;
GO
CREATE TABLE workflow_status_relation (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 workflow_id BIGINT NOT NULL,
 status_id BIGINT NOT NULL,
 start_point TINYINT NOT NULL DEFAULT '0'
);
GO

IF OBJECT_ID('dbo.workflow_status_transition_screen_fields', 'U') IS NOT NULL DROP TABLE dbo.workflow_status_transition_screen_fields;
GO
CREATE TABLE workflow_status_transition_screen_fields (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  transition BIGINT NOT NULL,
  data TEXT
);
GO

IF OBJECT_ID('dbo.task_contributors', 'U') IS NOT NULL DROP TABLE dbo.task_contributors;
GO

CREATE TABLE task_contributors (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  task_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL
);
GO
CREATE UNIQUE INDEX task_contributors_unique_key ON task_contributors( task_id ,  user_id );
GO

IF OBJECT_ID('dbo.task_workflows', 'U') IS NOT NULL DROP TABLE dbo.task_workflows;
GO

CREATE TABLE task_workflows (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL,
  type nvarchar(255) NOT NULL,
  createdBy BIGINT DEFAULT NULL,
  createdOn DATETIME DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL,
  modifiedOn DATETIME DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.task_workflow_types', 'U') IS NOT NULL DROP TABLE dbo.task_workflow_types;
GO

CREATE TABLE task_workflow_types (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  workflow_id BIGINT NOT NULL,
  type_id BIGINT NOT NULL
);
GO


IF OBJECT_ID('dbo.task_workflow_status_relation', 'U') IS NOT NULL DROP TABLE dbo.task_workflow_status_relation;
GO

CREATE TABLE task_workflow_status_relation (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  workflow_id BIGINT NOT NULL,
  status_id BIGINT NOT NULL,
  start_point TINYINT NOT NULL DEFAULT '0'
);
GO


IF OBJECT_ID('dbo.task_workflow_status_transition', 'U') IS NOT NULL DROP TABLE dbo.task_workflow_status_transition;
GO

CREATE TABLE task_workflow_status_transition (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  workflow_id BIGINT NOT NULL,
  from_step BIGINT NOT NULL,
  to_step BIGINT NOT NULL,
  name nvarchar(255) NOT NULL,
  comments text DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.task_workflow_status_transition_permissions', 'U') IS NOT NULL DROP TABLE dbo.task_workflow_status_transition_permissions;
GO

CREATE TABLE task_workflow_status_transition_permissions (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  transition BIGINT NOT NULL,
  users text,
  user_groups text
);
GO
CREATE UNIQUE INDEX task_workflow_status_transition_permissions_unique_key ON task_workflow_status_transition_permissions( transition );
GO

IF OBJECT_ID('dbo.task_workflow_status_transition_history', 'U') IS NOT NULL DROP TABLE dbo.task_workflow_status_transition_history;
GO

CREATE TABLE task_workflow_status_transition_history (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  task_id BIGINT NOT NULL,
  from_step BIGINT DEFAULT NULL,
  to_step BIGINT NOT NULL,
  user_id BIGINT DEFAULT NULL,
  changed_on smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);
GO


IF OBJECT_ID('dbo.task_workflow_status_transition_screen_fields', 'U') IS NOT NULL DROP TABLE dbo.task_workflow_status_transition_screen_fields;
GO

CREATE TABLE task_workflow_status_transition_screen_fields (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  transition BIGINT NOT NULL,
  data text
);
GO

IF OBJECT_ID('dbo.task_comments', 'U') IS NOT NULL DROP TABLE dbo.task_comments;
GO

CREATE TABLE task_comments (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  task_id BIGINT NOT NULL,
  comment text NOT NULL,
  edited TINYINT NOT NULL DEFAULT '0',
  createdOn smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  createdBy BIGINT NOT NULL,
  modifiedOn smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  modifiedBy BIGINT NOT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_folder_templates', 'U') IS NOT NULL DROP TABLE dbo.legal_case_folder_templates;
GO

CREATE TABLE legal_case_folder_templates (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 category varchar(50) NULL,
 case_type_id varchar(255) NULL,
 folder_key varchar(255) NULL,
 parent_key varchar(255) NULL,
 name varchar(255) NOT NULL,
);
GO

IF OBJECT_ID('dbo.legal_case_container_opponents', 'U') IS NOT NULL DROP TABLE dbo.legal_case_container_opponents;
GO
CREATE TABLE legal_case_container_opponents (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 case_container_id BIGINT NOT NULL,
 opponent_id BIGINT NOT NULL,
 opponent_member_type nvarchar(255) NOT NULL,
 opponent_position BIGINT null
);
GO

IF OBJECT_ID('dbo.case_comments_emails', 'U') IS NOT NULL DROP TABLE dbo.case_comments_emails;

CREATE TABLE case_comments_emails (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 case_comment BIGINT NOT NULL,
 email_to text NOT NULL,
 email_from varchar(255) NOT NULL,
 email_from_name varchar(255) NULL,
 email_date SMALLDATETIME NULL,
 email_subject varchar(255) NULL,
 email_file VARCHAR(255) NULL,
);


IF OBJECT_ID('dbo.user_api_keys', 'U') IS NOT NULL DROP TABLE dbo.user_api_keys;
GO

CREATE TABLE user_api_keys (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 user_id BIGINT NOT NULL,
 api_key varchar(255) NOT NULL,
 key_generated_on datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);
GO
IF OBJECT_ID('dbo.assignments', 'U') IS NOT NULL DROP TABLE dbo.assignments;
GO

CREATE TABLE assignments (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  category nvarchar(255) NULL,
  type nvarchar(10) NOT NULL,
  assigned_team BIGINT DEFAULT NULL,
  assignment_rule nvarchar(255) NULL,
  visible_assignee TINYINT DEFAULT 1 NOT NULL,
  visible_assigned_team TINYINT DEFAULT 1 NOT NULL
);
GO

IF OBJECT_ID('dbo.assignments_relation', 'U') IS NOT NULL DROP TABLE dbo.assignments_relation;
GO

CREATE TABLE assignments_relation (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  relation BIGINT NOT NULL,
  user_relation BIGINT NOT NULL
);
GO

IF OBJECT_ID('dbo.customer_portal_users_assignments', 'U') IS NOT NULL DROP TABLE dbo.customer_portal_users_assignments;
GO

CREATE TABLE customer_portal_users_assignments (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  screen BIGINT NOT NULL,
  user_relation BIGINT NOT NULL
);
GO

IF OBJECT_ID('dbo.case_rate', 'U') IS NOT NULL DROP TABLE dbo.case_rate;
GO
CREATE TABLE case_rate (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    case_id BIGINT NOT NULL,
    organization_id BIGINT NOT NULL,
    rate_per_hour decimal(10,2) NOT NULL
);
GO

IF OBJECT_ID('dbo.recurring_types', 'U') IS NOT NULL DROP TABLE dbo.recurring_types;
GO
CREATE TABLE recurring_types (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    type nvarchar(20) NOT NULL,
    recurring_period nvarchar(10) NOT NULL,
    max_recurrence nvarchar(10) NOT NULL
);
GO

IF OBJECT_ID('dbo.recurrence', 'U') IS NOT NULL DROP TABLE dbo.recurrence;
GO
CREATE TABLE recurrence (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    related_id BIGINT NOT NULL,
    type_id BIGINT NOT NULL,
    stop_date date NOT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_container_related_containers', 'U') IS NOT NULL DROP TABLE dbo.legal_case_container_related_containers;
GO
CREATE TABLE legal_case_container_related_containers (
    legal_case_container_id BIGINT NOT NULL,
    related_container_id BIGINT NOT NULL,
);
GO
CREATE UNIQUE INDEX legal_case_container_related_container_unique_key ON legal_case_container_related_containers( legal_case_container_id, related_container_id );
GO

IF OBJECT_ID('dbo.customer_portal_sla_notification', 'U') IS NOT NULL DROP TABLE dbo.customer_portal_sla_notification;
GO

--customer_portal_sla_notification;
CREATE TABLE customer_portal_sla_notification (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  sla_id BIGINT NOT NULL,
  case_id BIGINT NOT NULL,
  notified TINYINT NOT NULL DEFAULT '0'
);
GO

IF OBJECT_ID('dbo.legal_case_opponent_positions', 'U') IS NOT NULL DROP TABLE dbo.legal_case_opponent_positions;
GO
--legal_case_opponent_positions;
CREATE TABLE legal_case_opponent_positions (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY
);
GO

IF OBJECT_ID('dbo.legal_case_opponent_position_languages', 'U') IS NOT NULL DROP TABLE dbo.legal_case_opponent_position_languages;
GO
--legal_case_opponent_position_languages;
CREATE TABLE legal_case_opponent_position_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 legal_case_opponent_position_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_container_documents', 'U') IS NOT NULL DROP TABLE dbo.legal_case_container_documents;
GO
--legal_case_container_documents;
CREATE TABLE legal_case_container_documents (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 legal_case_container_id BIGINT NOT NULL,
 legal_case_container_document_status_id BIGINT NOT NULL,
 legal_case_container_document_type_id BIGINT NOT NULL,
 name nvarchar(255) DEFAULT NULL,
 path nvarchar(255) DEFAULT NULL,
 pathType nvarchar(255) DEFAULT NULL,
 comments NVARCHAR(MAX),
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_container_document_statuses', 'U') IS NOT NULL DROP TABLE dbo.legal_case_container_document_statuses;
GO
--legal_case_container_document_statuses;
CREATE TABLE legal_case_container_document_statuses (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_container_document_types', 'U') IS NOT NULL DROP TABLE dbo.legal_case_container_document_types;
GO
--legal_case_container_document_types;
CREATE TABLE legal_case_container_document_types (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.countries_languages', 'U') IS NOT NULL DROP TABLE dbo.countries_languages;
GO
--countries_languages;
CREATE TABLE countries_languages (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  country_id BIGINT NOT NULL,
  language_id BIGINT NOT NULL,
  name varchar(255) NOT NULL,
);
GO

IF OBJECT_ID('dbo.custom_fields_case_types', 'U') IS NOT NULL DROP TABLE dbo.custom_fields_case_types;
GO
CREATE TABLE custom_fields_case_types (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  custom_field_id BIGINT NOT NULL,
  type_id BIGINT NOT NULL
);
GO
ALTER TABLE custom_fields_case_types
  ADD CONSTRAINT fk_custom_fields_case_types_1 FOREIGN KEY (custom_field_id) REFERENCES custom_fields (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

--Contract--
IF OBJECT_ID('dbo.contract_type', 'U') IS NOT NULL DROP TABLE dbo.contract_type;
GO
CREATE TABLE contract_type (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
);
GO

IF OBJECT_ID('dbo.contract_type_language', 'U') IS NOT NULL DROP TABLE dbo.contract_type_language;
GO
CREATE TABLE contract_type_language (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 type_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

ALTER TABLE contract_type_language
    ADD CONSTRAINT fk_contract_type_language_1 FOREIGN KEY (type_id) REFERENCES contract_type (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_type_language
    ADD CONSTRAINT fk_contract_type_language_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.status_category', 'U') IS NOT NULL DROP TABLE dbo.status_category;
GO
CREATE TABLE status_category (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 type nvarchar(255) NOT NULL,
 color nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.contract_status', 'U') IS NOT NULL DROP TABLE dbo.contract_status;
GO
CREATE TABLE contract_status (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 category_id BIGINT NOT NULL,
 is_global TINYINT NOT NULL DEFAULT '1'
);
GO
ALTER TABLE contract_status
    ADD CONSTRAINT fk_contract_status_1 FOREIGN KEY (category_id) REFERENCES status_category (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_status_language', 'U') IS NOT NULL DROP TABLE dbo.contract_status_language;
GO
CREATE TABLE contract_status_language (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 status_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO
ALTER TABLE contract_status_language
    ADD CONSTRAINT fk_contract_status_language_1 FOREIGN KEY (status_id) REFERENCES contract_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_status_language
    ADD CONSTRAINT fk_contract_status_language_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
IF OBJECT_ID('dbo.contract_workflow', 'U') IS NOT NULL DROP TABLE dbo.contract_workflow;
GO
CREATE TABLE contract_workflow (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL,
 category nvarchar(255) NOT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO
IF OBJECT_ID('dbo.contract_workflow_per_type', 'U') IS NOT NULL DROP TABLE dbo.contract_workflow_per_type;
GO
CREATE TABLE contract_workflow_per_type (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 workflow_id BIGINT NOT NULL,
 type_id BIGINT NOT NULL
);
GO

ALTER TABLE contract_workflow_per_type
    ADD CONSTRAINT fk_contract_workflow_per_type_1 FOREIGN KEY (workflow_id) REFERENCES contract_workflow (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_workflow_per_type
    ADD CONSTRAINT fk_contract_workflow_per_type_2 FOREIGN KEY (type_id) REFERENCES contract_type (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
IF OBJECT_ID('dbo.contract_workflow_status_relation', 'U') IS NOT NULL DROP TABLE dbo.contract_workflow_status_relation;
GO
CREATE TABLE contract_workflow_status_relation (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 workflow_id BIGINT NOT NULL,
 status_id BIGINT NOT NULL,
 start_point TINYINT NOT NULL DEFAULT '1',
 approval_start_point TINYINT NOT NULL DEFAULT '0'
);
GO
 ALTER TABLE contract_workflow_status_relation
    ADD CONSTRAINT fk_contract_workflow_status_relation_1 FOREIGN KEY (workflow_id) REFERENCES contract_workflow (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_workflow_status_relation
    ADD CONSTRAINT fk_contract_workflow_status_relation_2 FOREIGN KEY (status_id) REFERENCES contract_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
IF OBJECT_ID('dbo.contract_workflow_status_transition', 'U') IS NOT NULL DROP TABLE dbo.contract_workflow_status_transition;
GO
CREATE TABLE contract_workflow_status_transition (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 workflow_id BIGINT NOT NULL,
 from_step BIGINT NOT NULL,
 to_step BIGINT NOT NULL,
 name nvarchar(255) NOT NULL,
 comment text NULL DEFAULT NULL,
 approval_needed TINYINT NOT NULL DEFAULT '1'
);
GO

 ALTER TABLE contract_workflow_status_transition
    ADD CONSTRAINT fk_contract_workflow_status_transition_1 FOREIGN KEY (workflow_id) REFERENCES contract_workflow (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_workflow_status_transition
    ADD CONSTRAINT fk_contract_workflow_status_transition_2 FOREIGN KEY (from_step) REFERENCES contract_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_workflow_status_transition
    ADD CONSTRAINT fk_contract_workflow_status_transition_3 FOREIGN KEY (to_step) REFERENCES contract_status (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

IF OBJECT_ID('dbo.contract_workflow_status_transition_permission', 'U') IS NOT NULL DROP TABLE dbo.contract_workflow_status_transition_permission;
GO
CREATE TABLE contract_workflow_status_transition_permission (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 transition_id BIGINT NOT NULL,
 users text,
 user_groups text
);
GO

ALTER TABLE contract_workflow_status_transition_permission
  ADD CONSTRAINT fk_contract_workflow_status_transition_permission_1 FOREIGN KEY (transition_id) REFERENCES contract_workflow_status_transition (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

IF OBJECT_ID('dbo.contract_workflow_status_transition_screen_field', 'U') IS NOT NULL DROP TABLE dbo.contract_workflow_status_transition_screen_field;
GO
CREATE TABLE contract_workflow_status_transition_screen_field (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 transition_id BIGINT NOT NULL,
 data text
);
GO

ALTER TABLE contract_workflow_status_transition_screen_field
  ADD CONSTRAINT fk_contract_workflow_status_transition_screen_field_1 FOREIGN KEY (transition_id) REFERENCES contract_workflow_status_transition (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO


IF OBJECT_ID('dbo.iso_currencies', 'U') IS NOT NULL DROP TABLE dbo.iso_currencies;
GO
CREATE TABLE iso_currencies (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 code char(4) NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.applicable_law', 'U') IS NOT NULL DROP TABLE dbo.applicable_law;
GO
CREATE TABLE applicable_law (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
);
GO

IF OBJECT_ID('dbo.applicable_law_language', 'U') IS NOT NULL DROP TABLE dbo.applicable_law_language;
GO
CREATE TABLE applicable_law_language (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 app_law_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

ALTER TABLE applicable_law_language
    ADD CONSTRAINT fk_applicable_law_language_1 FOREIGN KEY (app_law_id) REFERENCES applicable_law (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE applicable_law_language
    ADD CONSTRAINT fk_applicable_law_language_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.sub_contract_type', 'U') IS NOT NULL DROP TABLE dbo.sub_contract_type;
GO
CREATE TABLE sub_contract_type
(
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    type_id BIGINT NOT NULL,
);
GO
CREATE INDEX sub_contract_type ON sub_contract_type ( type_id );
GO
ALTER TABLE sub_contract_type
    ADD CONSTRAINT fk_sub_contract_type_1 FOREIGN KEY (type_id) REFERENCES contract_type (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.sub_contract_type_language', 'U') IS NOT NULL DROP TABLE dbo.sub_contract_type_language;
GO
CREATE TABLE sub_contract_type_language
(
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    sub_type_id BIGINT NOT NULL,
    language_id BIGINT NOT NULL,
    name nvarchar(255) NOT NULL
);
GO
CREATE INDEX sub_type_id ON sub_contract_type_language ( sub_type_id );
GO
CREATE INDEX language_id ON sub_contract_type_language ( language_id );
GO
ALTER TABLE sub_contract_type_language
    ADD CONSTRAINT fk_sub_contract_type_language_1 FOREIGN KEY (sub_type_id) REFERENCES sub_contract_type (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE sub_contract_type_language
    ADD CONSTRAINT fk_sub_contract_type_language_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract', 'U') IS NOT NULL DROP TABLE dbo.contract;
GO
CREATE TABLE contract (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL,
 status nvarchar(8) NOT NULL DEFAULT 'Active',
 description TEXT,
 value BIGINT DEFAULT NULL,
 type_id BIGINT NOT NULL,
 sub_type_id BIGINT DEFAULT NULL,
 contract_date date DEFAULT NULL,
 start_date    date DEFAULT NULL,
 end_date      date DEFAULT NULL,
 reference_number nvarchar(255) DEFAULT NULL,
 assigned_team_id BIGINT DEFAULT NULL,
 assignee_id BIGINT DEFAULT NULL,
 authorized_signatory BIGINT DEFAULT NULL,
 amendment_of  BIGINT       DEFAULT NULL,
 app_law_id BIGINT NULL,
 country_id BIGINT NULL,
 requester_id BIGINT NULL,
 status_comments TEXT NULL,
 priority nvarchar(10) NOT NULL DEFAULT 'medium',
 workflow_id BIGINT NOT NULL,
 status_id BIGINT NOT NULL,
 renewal_type nvarchar(25) DEFAULT NULL,
 currency_id BIGINT DEFAULT NULL,
 private TINYINT DEFAULT NULL,
 channel char(3) DEFAULT NULL,
 visible_to_cp TINYINT DEFAULT '0',
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL,
 modifiedByChannel char(3) DEFAULT NULL,
 archived nvarchar(3) NOT NULL DEFAULT 'no',
 hideFromBoard nvarchar(3) DEFAULT NULL,
);
GO


ALTER TABLE contract
  ADD CONSTRAINT fk_contract_1 FOREIGN KEY (type_id) REFERENCES contract_type (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contract
  ADD CONSTRAINT fk_contract_2 FOREIGN KEY (assigned_team_id) REFERENCES provider_groups (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
  GO
ALTER TABLE contract
  ADD CONSTRAINT fk_contract_3 FOREIGN KEY (assignee_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
  GO
ALTER TABLE contract
  ADD CONSTRAINT fk_contract_4 FOREIGN KEY (requester_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
  GO
ALTER TABLE contract
  ADD CONSTRAINT fk_contract_5 FOREIGN KEY (workflow_id) REFERENCES contract_workflow (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
  GO
ALTER TABLE contract
  ADD CONSTRAINT fk_contract_6 FOREIGN KEY (status_id) REFERENCES contract_status (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
  GO
ALTER TABLE contract
  ADD CONSTRAINT fk_contract_7 FOREIGN KEY (currency_id) REFERENCES iso_currencies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contract
  ADD CONSTRAINT fk_contract_8 FOREIGN KEY (amendment_of) REFERENCES contract (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contract
  ADD CONSTRAINT fk_contract_9 FOREIGN KEY (app_law_id) REFERENCES applicable_law (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contract
  ADD CONSTRAINT fk_contract_10 FOREIGN KEY (country_id) REFERENCES countries (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contract
    ADD CONSTRAINT fk_contract_11 FOREIGN KEY (sub_type_id) REFERENCES sub_contract_type (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.party_category', 'U') IS NOT NULL DROP TABLE dbo.party_category;
GO
CREATE TABLE party_category (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
);
GO

IF OBJECT_ID('dbo.party_category_language', 'U') IS NOT NULL DROP TABLE dbo.party_category_language;
GO
CREATE TABLE party_category_language (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 category_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

ALTER TABLE party_category_language
    ADD CONSTRAINT fk_party_category_language_1 FOREIGN KEY (category_id) REFERENCES party_category (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE party_category_language
    ADD CONSTRAINT fk_party_category_language_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
IF OBJECT_ID('dbo.party', 'U') IS NOT NULL DROP TABLE dbo.party;
GO
CREATE TABLE party (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 company_id BIGINT DEFAULT NULL,
 contact_id BIGINT DEFAULT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO
CREATE UNIQUE INDEX company_id ON party(company_id) WHERE company_id IS NOT NULL;
GO
CREATE UNIQUE INDEX contact_id ON party(contact_id ) WHERE contact_id IS NOT NULL;
GO
ALTER TABLE party
 ADD CONSTRAINT fk_party_1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE party
 ADD CONSTRAINT fk_party_2 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO


IF OBJECT_ID('dbo.contract_party', 'U') IS NOT NULL DROP TABLE dbo.contract_party;
GO
CREATE TABLE contract_party (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    contract_id       BIGINT       NOT NULL,
    party_id          BIGINT       NOT NULL,
    party_member_type nvarchar(255) NULL,
    party_category_id BIGINT      NULL
);
GO

CREATE UNIQUE INDEX contract_key ON contract_party(contract_id,party_id);
GO

CREATE INDEX contract_id_party ON contract_party (contract_id);
GO

ALTER TABLE contract_party
    ADD CONSTRAINT fk_contract_party_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_party
    ADD CONSTRAINT fk_contract_party_2 FOREIGN KEY (party_id) REFERENCES party (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_party
    ADD CONSTRAINT fk_contract_party_3 FOREIGN KEY (party_category_id) REFERENCES party_category (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.custom_fields_per_model_types', 'U') IS NOT NULL DROP TABLE dbo.custom_fields_per_model_types;
GO
CREATE TABLE custom_fields_per_model_types (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  custom_field_id BIGINT NOT NULL,
  type_id BIGINT NOT NULL
);
GO
ALTER TABLE custom_fields_per_model_types
  ADD CONSTRAINT fk_custom_fields_per_model_types_1 FOREIGN KEY (custom_field_id) REFERENCES custom_fields (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

IF OBJECT_ID('dbo.contract_contributors', 'U') IS NOT NULL DROP TABLE dbo.contract_contributors;
GO
CREATE TABLE contract_contributors (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  contract_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL
);
GO
CREATE UNIQUE INDEX contract_contributors_unique_key ON contract_contributors( contract_id ,  user_id );
GO
ALTER TABLE contract_contributors
    ADD CONSTRAINT fk_contract_contributors_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_contributors
    ADD CONSTRAINT fk_contract_contributors_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_users', 'U') IS NOT NULL DROP TABLE dbo.contract_users;
GO
CREATE TABLE contract_users (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_id BIGINT NOT NULL,
 user_id BIGINT NOT NULL
);
GO

CREATE UNIQUE INDEX contract_user ON contract_users ( contract_id , user_id );
GO

ALTER TABLE contract_users
    ADD CONSTRAINT fk_contract_users_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_users
    ADD CONSTRAINT fk_contract_users_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;


IF OBJECT_ID('dbo.approval', 'U') IS NOT NULL DROP TABLE dbo.approval;
GO
CREATE TABLE approval (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL,
 rank BIGINT      NOT NULL
);
GO

IF OBJECT_ID('dbo.approval_criteria', 'U') IS NOT NULL DROP TABLE dbo.approval_criteria;
GO
CREATE TABLE approval_criteria (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 approval_id BIGINT       NOT NULL,
 field       nvarchar(255) NOT NULL,
 operator    nvarchar(255) NOT NULL,
 value       nvarchar(255) NOT NULL
);
GO

ALTER TABLE approval_criteria
    ADD CONSTRAINT fk_approval_criteria_1 FOREIGN KEY (approval_id) REFERENCES approval (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO


IF OBJECT_ID('dbo.approval_assignee', 'U') IS NOT NULL DROP TABLE dbo.approval_assignee;
GO
CREATE TABLE approval_assignee (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 approval_id BIGINT       NOT NULL,
 users                text,
 user_groups          text,
 is_requester_manager TINYINT DEFAULT 0,
 is_board_member TINYINT DEFAULT 0,
 rank                BIGINT      NOT NULL,
 label       nvarchar(255) NOT NULL,
 collaborators text,
 contacts text,
 is_shareholder TINYINT DEFAULT 0
);
GO

ALTER TABLE approval_assignee
    ADD CONSTRAINT fk_approval_assignee_1 FOREIGN KEY (approval_id) REFERENCES approval (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.approval_assignee_bm_role', 'U') IS NOT NULL DROP TABLE dbo.approval_assignee_bm_role;
GO
CREATE TABLE approval_assignee_bm_role (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 assignee_id BIGINT NOT NULL,
 role_id BIGINT NOT NULL,
);
GO

ALTER TABLE approval_assignee_bm_role
    ADD CONSTRAINT fk_approval_assignee_bm_role_1 FOREIGN KEY (assignee_id) REFERENCES approval_assignee (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE approval_assignee_bm_role
    ADD CONSTRAINT fk_approval_assignee_bm_role_2 FOREIGN KEY (role_id) REFERENCES board_member_roles (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_approval_submission', 'U') IS NOT NULL DROP TABLE dbo.contract_approval_submission;
GO
CREATE TABLE contract_approval_submission (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_id BIGINT       NOT NULL,
 status       nvarchar(255) NOT NULL
);
GO

ALTER TABLE contract_approval_submission
    ADD CONSTRAINT fk_contract_approval_submission_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_approval_status', 'U') IS NOT NULL DROP TABLE dbo.contract_approval_status;
GO
CREATE TABLE contract_approval_status (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_id BIGINT       NOT NULL,
 is_requester_manager TINYINT DEFAULT 0,
 is_board_member TINYINT DEFAULT 0,
 party_id BIGINT DEFAULT 0,
 rank                BIGINT      NOT NULL,
 label       nvarchar(255) NOT NULL,
 status       nvarchar(255) NOT NULL,
 summary text DEFAULT NULL,
 is_shareholder TINYINT DEFAULT 0
);
GO

ALTER TABLE contract_approval_status
    ADD CONSTRAINT fk_contract_approval_status_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_approval_status
    ADD CONSTRAINT fk_contract_approval_status_2 FOREIGN KEY (party_id) REFERENCES party (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
CREATE INDEX status ON contract_approval_status (status);
GO

IF OBJECT_ID('dbo.contract_approval_bm_role', 'U') IS NOT NULL DROP TABLE dbo.contract_approval_bm_role;
GO
CREATE TABLE contract_approval_bm_role (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_approval_status_id BIGINT NOT NULL,
 role_id BIGINT NOT NULL,
);
GO

ALTER TABLE contract_approval_bm_role
    ADD CONSTRAINT fk_contract_approval_bm_role_1 FOREIGN KEY (contract_approval_status_id) REFERENCES contract_approval_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_approval_bm_role
    ADD CONSTRAINT fk_contract_approval_bm_role_2 FOREIGN KEY (role_id) REFERENCES board_member_roles (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_approval_users', 'U') IS NOT NULL DROP TABLE dbo.contract_approval_users;
GO
CREATE TABLE contract_approval_users (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_approval_status_id BIGINT       NOT NULL,
 user_id BIGINT  NOT NULL
);
GO

ALTER TABLE contract_approval_users
    ADD CONSTRAINT fk_contract_approval_users_1 FOREIGN KEY (contract_approval_status_id) REFERENCES contract_approval_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_approval_users
    ADD CONSTRAINT fk_contract_approval_users_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO


IF OBJECT_ID('dbo.contract_approval_user_groups', 'U') IS NOT NULL DROP TABLE dbo.contract_approval_user_groups;
GO
CREATE TABLE contract_approval_user_groups (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_approval_status_id BIGINT       NOT NULL,
 user_group_id BIGINT  NOT NULL
);
GO

ALTER TABLE contract_approval_user_groups
    ADD CONSTRAINT fk_contract_approval_user_groups_1 FOREIGN KEY (contract_approval_status_id) REFERENCES contract_approval_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_approval_user_groups
    ADD CONSTRAINT fk_contract_approval_user_groups_2 FOREIGN KEY (user_group_id) REFERENCES user_groups (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_approval_contacts', 'U') IS NOT NULL DROP TABLE dbo.contract_approval_contacts;
GO
CREATE TABLE contract_approval_contacts
(
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    contract_approval_status_id BIGINT NOT NULL,
    contact_id BIGINT NOT NULL
);
GO

ALTER TABLE contract_approval_contacts
    ADD CONSTRAINT fk_contract_approval_contacts_1 FOREIGN KEY (contract_approval_status_id) REFERENCES contract_approval_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_approval_contacts
    ADD CONSTRAINT fk_contract_approval_contacts_2 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_approval_documents', 'U') IS NOT NULL DROP TABLE dbo.contract_approval_documents;
GO
CREATE TABLE contract_approval_documents
(
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    document_id BIGINT NOT NULL,
    contract_approval_status_id BIGINT NOT NULL
);
GO

ALTER TABLE contract_approval_documents
    ADD CONSTRAINT fk_contract_approval_documents_1 FOREIGN KEY (contract_approval_status_id) REFERENCES contract_approval_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_approval_documents
    ADD CONSTRAINT fk_contract_approval_documents_2 FOREIGN KEY (document_id) REFERENCES documents_management_system (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_approval_history', 'U') IS NOT NULL DROP TABLE dbo.contract_approval_history;
GO
CREATE TABLE contract_approval_history (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_id BIGINT NOT NULL,
 done_by BIGINT NOT NULL,
 enforce_previous_approvals TINYINT NOT NULL DEFAULT '0',
 action nvarchar(255) NOT NULL,
 from_action nvarchar(255) NOT NULL DEFAULT '-',
 to_action nvarchar(255) NOT NULL DEFAULT '-',
 comment text NULL DEFAULT NULL,
 done_on    datetime DEFAULT NULL,
 label nvarchar(255)     NOT NULL,
 done_by_type nvarchar(25) NOT NULL DEFAULT  'user',
 signature_id BIGINT NULL,
 done_by_ip nvarchar(50) NULL DEFAULT NULL,
 approval_channel nvarchar(50) NULL DEFAULT 'A4L'
 );
GO

ALTER TABLE contract_approval_history
    ADD CONSTRAINT fk_contract_approval_history_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_approval_negotiation', 'U') IS NOT NULL DROP TABLE dbo.contract_approval_negotiation;
GO
CREATE TABLE contract_approval_negotiation
(
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    contract_approval_status_id BIGINT NOT NULL,
    done_by BIGINT NOT NULL,
    done_by_type nvarchar(25) NOT NULL DEFAULT  'user',
    done_on datetime DEFAULT NULL,
    status nvarchar(255) NOT NULL
);
GO
CREATE INDEX contract_approval_negotiation_status_id ON contract_approval_negotiation (contract_approval_status_id);
GO
ALTER TABLE contract_approval_negotiation
    ADD CONSTRAINT fk_contract_approval_negotiation_1 FOREIGN KEY (contract_approval_status_id) REFERENCES contract_approval_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
IF OBJECT_ID('dbo.contract_approval_negotiation_comments', 'U') IS NOT NULL DROP TABLE dbo.contract_approval_negotiation_comments;
GO
CREATE TABLE contract_approval_negotiation_comments
(
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    negotiation_id BIGINT NOT NULL,
    done_by BIGINT NOT NULL,
    done_by_type nvarchar(25) NOT NULL DEFAULT  'user',
    done_on datetime DEFAULT NULL,
    comment text NULL DEFAULT NULL
);
GO
CREATE INDEX contract_approval_negotiation_comments_negotiation_id ON contract_approval_negotiation_comments (negotiation_id);
GO
ALTER TABLE contract_approval_negotiation_comments
    ADD CONSTRAINT fk_contract_approval_negotiation_comments_1 FOREIGN KEY (negotiation_id) REFERENCES contract_approval_negotiation (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_templates', 'U') IS NOT NULL DROP TABLE dbo.contract_templates;
GO
CREATE TABLE contract_templates
(
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    type_id BIGINT NOT NULL,
    sub_type_id BIGINT DEFAULT NULL,
    name nvarchar(255) NOT NULL,
    status nvarchar(255) NOT NULL,
    document_id BIGINT NOT NULL,
    show_in_cp tinyint NOT NULL,
    createdBy BIGINT DEFAULT NULL,
    createdOn smalldatetime DEFAULT NULL,
    modifiedBy BIGINT DEFAULT NULL,
    modifiedOn smalldatetime DEFAULT NULL
);
GO
CREATE INDEX type_id ON contract_templates ( type_id );
GO
CREATE INDEX sub_type_id ON contract_templates ( sub_type_id );
GO
ALTER TABLE contract_templates
  ADD CONSTRAINT fk_contract_templates_1 FOREIGN KEY (type_id) REFERENCES contract_type (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_templates
  ADD CONSTRAINT fk_contract_templates_2 FOREIGN KEY (sub_type_id) REFERENCES sub_contract_type (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contract_templates
  ADD CONSTRAINT fk_contract_templates_3 FOREIGN KEY (document_id) REFERENCES documents_management_system (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_template_pages', 'U') IS NOT NULL DROP TABLE dbo.contract_template_pages;
GO
CREATE TABLE contract_template_pages
(
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    template_id BIGINT NOT NULL,
    title nvarchar(255) NOT NULL,
    description text DEFAULT NULL
);
GO
CREATE INDEX contract_template_pages ON contract_template_pages ( template_id );
GO
ALTER TABLE contract_template_pages
    ADD CONSTRAINT fk_contract_template_pages_1 FOREIGN KEY (template_id) REFERENCES contract_templates (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_template_groups', 'U') IS NOT NULL DROP TABLE dbo.contract_template_groups;
GO
CREATE TABLE contract_template_groups
(
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    page_id BIGINT NOT NULL,
    title nvarchar(255) NOT NULL
);
GO
CREATE INDEX contract_template_groups ON contract_template_groups ( page_id );
GO
ALTER TABLE contract_template_groups
    ADD CONSTRAINT fk_contract_template_groups_1 FOREIGN KEY (page_id) REFERENCES contract_template_pages (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_template_variables', 'U') IS NOT NULL DROP TABLE dbo.contract_template_variables;
GO
CREATE TABLE contract_template_variables
(
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    group_id BIGINT NOT NULL,
    variable_property nvarchar(20) NOT NULL,
    property_details nvarchar(20) NOT NULL,
    name nvarchar(255) NOT NULL,
    is_required TINYINT NOT NULL,
    question nvarchar(255) NOT NULL,
    property_data TEXT DEFAULT NULL,
    description text DEFAULT NULL
);
GO
CREATE INDEX contract_template_variables ON contract_template_variables ( group_id );
GO
ALTER TABLE contract_template_variables
    ADD CONSTRAINT fk_contract_template_variables_1 FOREIGN KEY (group_id) REFERENCES contract_template_groups (id) ON DELETE CASCADE ON UPDATE CASCADE;


IF OBJECT_ID('dbo.contract_document_status', 'U') IS NOT NULL DROP TABLE dbo.contract_document_status;
GO
CREATE TABLE contract_document_status (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
);
GO

IF OBJECT_ID('dbo.contract_document_status_language', 'U') IS NOT NULL DROP TABLE dbo.contract_document_status_language;
GO
CREATE TABLE contract_document_status_language (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 status_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

ALTER TABLE contract_document_status_language
    ADD CONSTRAINT fk_contract_document_status_language_1 FOREIGN KEY (status_id) REFERENCES contract_document_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_document_status_language
    ADD CONSTRAINT fk_contract_document_status_language_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_document_type', 'U') IS NOT NULL DROP TABLE dbo.contract_document_type;
GO
CREATE TABLE contract_document_type (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY
);
GO

IF OBJECT_ID('dbo.contract_document_type_language', 'U') IS NOT NULL DROP TABLE dbo.contract_document_type_language;
GO
CREATE TABLE contract_document_type_language (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 type_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

ALTER TABLE contract_document_type_language
    ADD CONSTRAINT fk_contract_document_type_language_1 FOREIGN KEY (type_id) REFERENCES contract_document_type (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_document_type_language
    ADD CONSTRAINT fk_contract_document_type_language_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO


IF OBJECT_ID('dbo.contract_comment', 'U') IS NOT NULL DROP TABLE dbo.contract_comment;
GO
CREATE TABLE contract_comment (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_id BIGINT DEFAULT NULL,
 comment     text NOT NULL,
 edited      TINYINT DEFAULT 0 NOT NULL,
 channel char(3) DEFAULT NULL,
 modifiedByChannel char(3) DEFAULT NULL,
 visible_to_cp TINYINT NULL DEFAULT '0',
 createdOn datetime2(0) NOT NULL,
 createdBy BIGINT NOT NULL,
 modifiedOn smalldatetime NOT NULL,
 modifiedBy BIGINT NOT NULL
);
GO

ALTER TABLE contract_comment
    ADD CONSTRAINT fk_contract_comment_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_comments_emails', 'U') IS NOT NULL DROP TABLE dbo.contract_comments_emails;

CREATE TABLE contract_comments_emails (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_comment BIGINT NOT NULL,
 email_to varchar(255) NOT NULL,
 email_from varchar(255) NOT NULL,
 email_from_name varchar(255) NULL,
 email_date SMALLDATETIME NULL,
 email_subject varchar(255) NULL,
 email_file VARCHAR(255) NULL,
);
GO

ALTER TABLE contract_comments_emails
 ADD CONSTRAINT fk_contract_comments_emails_1 FOREIGN KEY (contract_comment) REFERENCES contract_comment (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

IF OBJECT_ID('dbo.signature', 'U') IS NOT NULL DROP TABLE dbo.signature;
GO
CREATE TABLE signature (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name nvarchar(255) NOT NULL,
 rank BIGINT      NOT NULL
);
GO

IF OBJECT_ID('dbo.signature_criteria', 'U') IS NOT NULL DROP TABLE dbo.signature_criteria;
GO
CREATE TABLE signature_criteria (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 signature_id BIGINT       NOT NULL,
 field       nvarchar(255) NOT NULL,
 operator    nvarchar(255) NOT NULL,
 value       nvarchar(255) NOT NULL
);
GO

ALTER TABLE signature_criteria
    ADD CONSTRAINT fk_signature_criteria_1 FOREIGN KEY (signature_id) REFERENCES signature (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO


IF OBJECT_ID('dbo.signature_signee', 'U') IS NOT NULL DROP TABLE dbo.signature_signee;
GO
CREATE TABLE signature_signee (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 signature_id BIGINT       NOT NULL,
 users                text,
 user_groups          text,
 is_requester_manager TINYINT DEFAULT 0,
 is_board_member TINYINT DEFAULT 0,
 rank                BIGINT      NOT NULL,
 label       nvarchar(255) NOT NULL,
 collaborators text,
 is_shareholder TINYINT DEFAULT 0
);
GO

ALTER TABLE signature_signee
    ADD CONSTRAINT fk_signature_signee_1 FOREIGN KEY (signature_id) REFERENCES signature (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.signature_signee_bm_role', 'U') IS NOT NULL DROP TABLE dbo.signature_signee_bm_role;
GO
CREATE TABLE signature_signee_bm_role (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 assignee_id BIGINT NOT NULL,
 role_id BIGINT NOT NULL,
);
GO

ALTER TABLE signature_signee_bm_role
    ADD CONSTRAINT fk_signature_signee_bm_role_1 FOREIGN KEY (assignee_id) REFERENCES signature_signee (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE signature_signee_bm_role
    ADD CONSTRAINT fk_signature_signee_bm_role_2 FOREIGN KEY (role_id) REFERENCES board_member_roles (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_signature_submission', 'U') IS NOT NULL DROP TABLE dbo.contract_signature_submission;
GO
CREATE TABLE contract_signature_submission (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_id BIGINT       NOT NULL,
 status       nvarchar(255) NOT NULL
);
GO

ALTER TABLE contract_signature_submission
    ADD CONSTRAINT fk_contract_signature_submission_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_signature_status', 'U') IS NOT NULL DROP TABLE dbo.contract_signature_status;
GO
CREATE TABLE contract_signature_status (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_id BIGINT       NOT NULL,
 is_requester_manager TINYINT DEFAULT 0,
 is_board_member TINYINT DEFAULT 0,
 party_id BIGINT DEFAULT 0,
 rank                BIGINT      NOT NULL,
 label       nvarchar(255) NOT NULL,
 status       nvarchar(255) NOT NULL,
 summary text DEFAULT NULL,
 is_shareholder TINYINT DEFAULT 0
);
GO

ALTER TABLE contract_signature_status
    ADD CONSTRAINT fk_contract_signature_status_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_signature_status
    ADD CONSTRAINT fk_contract_signature_status_2 FOREIGN KEY (party_id) REFERENCES party (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_signature_bm_role', 'U') IS NOT NULL DROP TABLE dbo.contract_signature_bm_role;
GO
CREATE TABLE contract_signature_bm_role (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_signature_status_id BIGINT NOT NULL,
 role_id BIGINT NOT NULL,
);
GO

ALTER TABLE contract_signature_bm_role
    ADD CONSTRAINT fk_contract_signature_bm_role_1 FOREIGN KEY (contract_signature_status_id) REFERENCES contract_signature_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_signature_bm_role
    ADD CONSTRAINT fk_contract_signature_bm_role_2 FOREIGN KEY (role_id) REFERENCES board_member_roles (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO


IF OBJECT_ID('dbo.contract_signature_users', 'U') IS NOT NULL DROP TABLE dbo.contract_signature_users;
GO
CREATE TABLE contract_signature_users (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_signature_status_id BIGINT       NOT NULL,
 user_id BIGINT  NOT NULL
);
GO


ALTER TABLE contract_signature_users
    ADD CONSTRAINT fk_contract_signature_users_1 FOREIGN KEY (contract_signature_status_id) REFERENCES contract_signature_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_signature_users
    ADD CONSTRAINT fk_contract_signature_users_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_signature_user_groups', 'U') IS NOT NULL DROP TABLE dbo.contract_signature_user_groups;
GO
CREATE TABLE contract_signature_user_groups (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_signature_status_id BIGINT       NOT NULL,
 user_group_id BIGINT  NOT NULL
);
GO

ALTER TABLE contract_signature_user_groups
    ADD CONSTRAINT fk_contract_signature_user_groups_1 FOREIGN KEY (contract_signature_status_id) REFERENCES contract_signature_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_signature_user_groups
    ADD CONSTRAINT fk_contract_signature_user_groups_2 FOREIGN KEY (user_group_id) REFERENCES user_groups (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_signature_history', 'U') IS NOT NULL DROP TABLE dbo.contract_signature_history;
GO
CREATE TABLE contract_signature_history (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_id BIGINT NOT NULL,
 done_by BIGINT NOT NULL,
 action nvarchar(255) NOT NULL,
 from_action nvarchar(255) NOT NULL DEFAULT '-',
 to_action nvarchar(255) NOT NULL DEFAULT '-',
 comment text NULL DEFAULT NULL,
 done_on    datetime DEFAULT NULL,
 label nvarchar(255)     NOT NULL,
 done_by_type nvarchar(25) NOT NULL DEFAULT  'user'
 );
GO

ALTER TABLE contract_signature_history
    ADD CONSTRAINT fk_contract_signature_history_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_category', 'U') IS NOT NULL DROP TABLE dbo.contract_category;
GO
CREATE TABLE contract_category (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
);
GO

IF OBJECT_ID('dbo.contract_category_language', 'U') IS NOT NULL DROP TABLE dbo.contract_category_language;
GO
CREATE TABLE contract_category_language (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 category_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO
ALTER TABLE contract_category_language
    ADD CONSTRAINT fk_contract_category_language_1 FOREIGN KEY (category_id) REFERENCES contract_category (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_category_language
    ADD CONSTRAINT fk_contract_category_language_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO


IF OBJECT_ID('dbo.iso_languages', 'U') IS NOT NULL DROP TABLE dbo.iso_languages;
GO
CREATE TABLE iso_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 code char(2) NOT NULL,
 name nvarchar(255) NOT NULL
);
GO
IF OBJECT_ID('dbo.contract_clause', 'U') IS NOT NULL DROP TABLE dbo.contract_clause;
GO
CREATE TABLE contract_clause (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 name            nvarchar(255) NOT NULL,
 reference       nvarchar(255) DEFAULT NULL,
 label           text    DEFAULT NULL,
 iso_language_id BIGINT DEFAULT NULL,
 content         text    NOT NULL,
 private         TINYINT  DEFAULT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL
);
GO
ALTER TABLE contract_clause
    ADD CONSTRAINT fk_contract_clause_2 FOREIGN KEY (iso_language_id) REFERENCES iso_languages (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_clause_user', 'U') IS NOT NULL DROP TABLE dbo.contract_clause_user;
GO
CREATE TABLE contract_clause_user (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_clause_id BIGINT NOT NULL,
 user_id BIGINT NOT NULL
);
GO
CREATE UNIQUE INDEX clause_user ON contract_clause_user ( contract_clause_id , user_id );
GO
ALTER TABLE contract_clause_user
    ADD CONSTRAINT fk_contract_clause_user_1 FOREIGN KEY (contract_clause_id) REFERENCES contract_clause (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_clause_user
    ADD CONSTRAINT fk_contract_clause_user_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_clause_editor', 'U') IS NOT NULL DROP TABLE dbo.contract_clause_editor;
GO
CREATE TABLE contract_clause_editor (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_clause_id BIGINT NOT NULL,
 user_id BIGINT NOT NULL
);
GO
CREATE UNIQUE INDEX clause_editor ON contract_clause_editor ( contract_clause_id , user_id );
GO
ALTER TABLE contract_clause_editor
    ADD CONSTRAINT fk_contract_clause_editor_1 FOREIGN KEY (contract_clause_id) REFERENCES contract_clause (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_clause_editor
    ADD CONSTRAINT fk_contract_clause_editor_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
IF OBJECT_ID('dbo.contract_clause_type', 'U') IS NOT NULL DROP TABLE dbo.contract_clause_type;
GO
CREATE TABLE contract_clause_type (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_clause_id BIGINT NOT NULL,
 type_id BIGINT NOT NULL
);
GO
CREATE UNIQUE INDEX contract_clause_type ON contract_clause_type ( contract_clause_id , type_id );
GO
ALTER TABLE contract_clause_type
    ADD CONSTRAINT fk_contract_clause_type_1 FOREIGN KEY (contract_clause_id) REFERENCES contract_clause (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_clause_type
    ADD CONSTRAINT fk_contract_clause_type_id FOREIGN KEY (type_id) REFERENCES contract_type (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.customer_portal_contract_permissions', 'U') IS NOT NULL DROP TABLE dbo.customer_portal_contract_permissions;
GO
CREATE TABLE customer_portal_contract_permissions (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 workflow_id BIGINT NOT NULL,
 workflow_status_transition_id BIGINT NOT NULL
);
GO

ALTER TABLE customer_portal_contract_permissions
  ADD CONSTRAINT fk_customer_portal_contract_permissions_1 FOREIGN KEY (workflow_id) REFERENCES contract_workflow (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE customer_portal_contract_permissions
  ADD CONSTRAINT fk_customer_portal_contract_permissions_2 FOREIGN KEY (workflow_status_transition_id) REFERENCES contract_workflow_status_transition (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

IF OBJECT_ID('dbo.customer_portal_contract_watchers', 'U') IS NOT NULL DROP TABLE dbo.customer_portal_contract_watchers;
GO
CREATE TABLE customer_portal_contract_watchers (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_id BIGINT NOT NULL,
 customer_portal_user_id BIGINT NOT NULL
);
GO


ALTER TABLE customer_portal_contract_watchers
    ADD CONSTRAINT fk_customer_portal_contract_watchers_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE customer_portal_contract_watchers
    ADD CONSTRAINT fk_customer_portal_contract_watchers_2 FOREIGN KEY (customer_portal_user_id) REFERENCES customer_portal_users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO



IF OBJECT_ID('dbo.contract_approval_signature_configuration', 'U') IS NOT NULL DROP TABLE dbo.contract_approval_signature_configuration;
GO
CREATE TABLE contract_approval_signature_configuration (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 type   varchar(10)  NOT NULL,
 include_no_status TINYINT NOT NULL DEFAULT '0'
);
GO

IF OBJECT_ID('dbo.contract_approval_signature_status', 'U') IS NOT NULL DROP TABLE dbo.contract_approval_signature_status;
GO
CREATE TABLE contract_approval_signature_status (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 configuration_id   BIGINT  NOT NULL,
 status_id   BIGINT  NOT NULL
);
GO

ALTER TABLE contract_approval_signature_status
    ADD CONSTRAINT fk_contract_approval_signature_status_1 FOREIGN KEY (configuration_id) REFERENCES contract_approval_signature_configuration (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_approval_signature_status
    ADD CONSTRAINT fk_contract_approval_signature_status_2 FOREIGN KEY (status_id) REFERENCES contract_document_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.user_signature_attachments', 'U') IS NOT NULL DROP TABLE dbo.user_signature_attachments;
GO
CREATE TABLE user_signature_attachments (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 user_id   BIGINT  NOT NULL,
 label nvarchar(255) NULL,
 signature nvarchar(255) NULL,
 type nvarchar(10) NOT NULL DEFAULT 'signature',
 is_default TINYINT NOT NULL DEFAULT '0'
);
GO
ALTER TABLE user_signature_attachments
    ADD CONSTRAINT fk_user_signature_attachments_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_url', 'U') IS NOT NULL DROP TABLE dbo.contract_url;
GO
CREATE TABLE contract_url (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  contract_id BIGINT NOT NULL,
  document_type_id BIGINT NOT NULL,
  document_status_id BIGINT NOT NULL,
  name nvarchar(255) DEFAULT NULL,
  path nvarchar(255) DEFAULT NULL,
  path_type nvarchar(255) DEFAULT NULL,
  comments text,
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL
 );
GO

ALTER TABLE contract_url
    ADD CONSTRAINT fk_contract_url_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_url
    ADD CONSTRAINT fk_contract_url_2 FOREIGN KEY (document_type_id) REFERENCES contract_document_type (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_url
    ADD CONSTRAINT fk_contract_url_3 FOREIGN KEY (document_status_id) REFERENCES contract_document_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_signature_contacts', 'U') IS NOT NULL DROP TABLE dbo.contract_signature_contacts;
GO
CREATE TABLE contract_signature_contacts (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_signature_status_id BIGINT       NOT NULL,
 contact_id BIGINT  NOT NULL
);
GO


ALTER TABLE contract_signature_contacts
    ADD CONSTRAINT fk_contract_signature_contacts_1 FOREIGN KEY (contract_signature_status_id) REFERENCES contract_signature_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_signature_contacts
    ADD CONSTRAINT fk_contract_signature_contacts_2 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_signed_document', 'U') IS NOT NULL DROP TABLE dbo.contract_signed_document;
GO
CREATE TABLE contract_signed_document (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 document_id BIGINT       NOT NULL,
contract_signature_status_id BIGINT       NOT NULL,
 signed_on SMALLDATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 signed_by BIGINT NOT NULL,
 signed_by_type nvarchar (25) NOT NULL
);
GO

ALTER TABLE contract_signed_document
    ADD CONSTRAINT fk_contract_signed_document_1 FOREIGN KEY (document_id) REFERENCES documents_management_system (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_signed_document
    ADD CONSTRAINT fk_contract_signed_document_2 FOREIGN KEY (contract_signature_status_id) REFERENCES contract_signature_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_renewal_notification_emails', 'U') IS NOT NULL DROP TABLE dbo.contract_renewal_notification_emails;
GO
CREATE TABLE contract_renewal_notification_emails (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_id BIGINT NOT NULL,
 emails TEXT NOT NULL
);
GO
ALTER TABLE contract_renewal_notification_emails
    ADD CONSTRAINT fk_contract_renewal_notification_emails_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_renewal_notification_assigned_teams', 'U') IS NOT NULL DROP TABLE dbo.contract_renewal_notification_assigned_teams;
GO
CREATE TABLE contract_renewal_notification_assigned_teams (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_id BIGINT NOT NULL,
 assigned_team BIGINT NOT NULL
);
GO
ALTER TABLE contract_renewal_notification_assigned_teams
    ADD CONSTRAINT fk_contract_renewal_notification_assigned_teams_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO


IF OBJECT_ID('dbo.contract_approval_collaborators', 'U') IS NOT NULL DROP TABLE dbo.contract_approval_collaborators;
GO
CREATE TABLE contract_approval_collaborators (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_approval_status_id BIGINT       NOT NULL,
 user_id BIGINT  NOT NULL,
 type VARCHAR(15) DEFAULT NULL,
);
GO

ALTER TABLE contract_approval_collaborators
    ADD CONSTRAINT fk_contract_approval_collaborators_1 FOREIGN KEY (contract_approval_status_id) REFERENCES contract_approval_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_approval_collaborators
    ADD CONSTRAINT fk_contract_approval_collaborators_2 FOREIGN KEY (user_id) REFERENCES customer_portal_users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_signature_collaborators', 'U') IS NOT NULL DROP TABLE dbo.contract_signature_collaborators;
GO
CREATE TABLE contract_signature_collaborators (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_signature_status_id BIGINT       NOT NULL,
 user_id BIGINT  NOT NULL,
 type VARCHAR(15) DEFAULT NULL,
);
GO

ALTER TABLE contract_signature_collaborators
    ADD CONSTRAINT fk_contract_signature_collaborators_1 FOREIGN KEY (contract_signature_status_id) REFERENCES contract_signature_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_signature_collaborators
    ADD CONSTRAINT fk_contract_signature_collaborators_2 FOREIGN KEY (user_id) REFERENCES customer_portal_users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_collaborators', 'U') IS NOT NULL DROP TABLE dbo.contract_collaborators;
GO
CREATE TABLE contract_collaborators (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  contract_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL
);
GO
CREATE UNIQUE INDEX contract_collaborators_unique_key ON contract_collaborators( contract_id ,  user_id );
GO
ALTER TABLE contract_collaborators
    ADD CONSTRAINT fk_contract_collaborators_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_collaborators
    ADD CONSTRAINT fk_contract_collaborators_2 FOREIGN KEY (user_id) REFERENCES customer_portal_users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.cp_user_signature_attachments', 'U') IS NOT NULL DROP TABLE dbo.cp_user_signature_attachments;
GO
CREATE TABLE cp_user_signature_attachments (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    user_id   BIGINT  NOT NULL,
    label nvarchar(255) NULL,
    signature nvarchar(255) NULL,
    type nvarchar(10) NOT NULL DEFAULT 'signature',
    is_default TINYINT NOT NULL DEFAULT '0'
    );
GO
ALTER TABLE cp_user_signature_attachments
    ADD CONSTRAINT fk_cp_user_signature_attachments_1 FOREIGN KEY (user_id) REFERENCES customer_portal_users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_renewal_history', 'U') IS NOT NULL DROP TABLE dbo.contract_renewal_history;
GO
CREATE TABLE contract_renewal_history (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_id BIGINT  NOT NULL,
 renewed_on smalldatetime NOT NULL,
 renewed_by BIGINT NOT NULL,
 comment TEXT NOT NULL,
 renewal_id BIGINT  NOT NULL
);
GO

ALTER TABLE contract_renewal_history
    ADD CONSTRAINT fk_contract_renewal_history_1 FOREIGN KEY (renewed_by) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_renewal_history
    ADD CONSTRAINT fk_contract_renewal_history_2 FOREIGN KEY (renewal_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.related_contracts', 'U') IS NOT NULL DROP TABLE dbo.related_contracts;
GO

CREATE TABLE related_contracts (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_a_id BIGINT NOT NULL,
 contract_b_id BIGINT NOT NULL,
 comments NVARCHAR(MAX)
);
GO

ALTER TABLE related_contracts
 ADD CONSTRAINT fk_related_contracts_1 FOREIGN KEY (contract_a_id) REFERENCES contract (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE related_contracts
 ADD CONSTRAINT fk_related_contracts_2 FOREIGN KEY (contract_b_id) REFERENCES contract (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

IF OBJECT_ID('dbo.contract_amendment_history', 'U') IS NOT NULL DROP TABLE dbo.contract_amendment_history;
GO
CREATE TABLE contract_amendment_history (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    contract_id       BIGINT       NOT NULL,
    amended_on datetime         NOT NULL,
    amended_by BIGINT      NOT NULL,
    comment TEXT          NOT NULL,
    amended_id BIGINT  NOT NULL
);
GO
ALTER TABLE contract_amendment_history
    ADD CONSTRAINT fk_contract_amendment_history_1 FOREIGN KEY (amended_by) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_amendment_history
    ADD CONSTRAINT fk_contract_amendment_history_2 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_amendment_history
    ADD CONSTRAINT fk_contract_amendment_history_3 FOREIGN KEY (amended_id) REFERENCES contract (id);
GO

 IF OBJECT_ID('dbo.companies_related_contracts', 'U') IS NOT NULL DROP TABLE dbo.companies_related_contracts;
GO
CREATE TABLE companies_related_contracts (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 company_id BIGINT NOT NULL,
 contract_id BIGINT NOT NULL
 );
GO
ALTER TABLE companies_related_contracts
    ADD CONSTRAINT fk_companies_related_contracts_1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE companies_related_contracts
    ADD CONSTRAINT fk_companies_related_contracts_2 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
CREATE INDEX company_id ON companies_related_contracts (company_id);
GO

IF OBJECT_ID('dbo.contacts_related_contracts', 'U') IS NOT NULL DROP TABLE dbo.contacts_related_contracts;
GO
CREATE TABLE contacts_related_contracts (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contact_id BIGINT NOT NULL,
 contract_id BIGINT NOT NULL
 );
GO
ALTER TABLE contacts_related_contracts
    ADD CONSTRAINT fk_contacts_related_contracts_1 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contacts_related_contracts
    ADD CONSTRAINT fk_contacts_related_contracts_2 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
CREATE INDEX contact_id ON contacts_related_contracts (contact_id);
GO

IF OBJECT_ID('dbo.contact_emails', 'U') IS NOT NULL DROP TABLE dbo.contact_emails;
GO
CREATE TABLE contact_emails (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  contact_id BIGINT NOT NULL,
  email nvarchar(255) NOT NULL DEFAULT ''
);
GO

IF OBJECT_ID('dbo.cp_user_preferences', 'U') IS NOT NULL DROP TABLE dbo.cp_user_preferences;
GO

CREATE TABLE cp_user_preferences
(
    cp_user_id BIGINT NOT NULL,
    keyName VARCHAR( 255 ) NOT NULL ,
    keyValue NVARCHAR(MAX) NULL DEFAULT NULL
);
GO
ALTER TABLE cp_user_preferences ADD PRIMARY KEY (cp_user_id,keyName);
GO

ALTER TABLE cp_user_preferences
 ADD CONSTRAINT fk_cp_user_preferences_users1 FOREIGN KEY (cp_user_id) REFERENCES customer_portal_users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

IF OBJECT_ID('dbo.contract_boards', 'U') IS NOT NULL DROP TABLE dbo.contract_boards;
GO

CREATE TABLE contract_boards (
   id BIGINT NOT NULL PRIMARY KEY IDENTITY,
   name nvarchar(255) NOT NULL,
   createdOn smalldatetime DEFAULT NULL,
   createdBy BIGINT DEFAULT NULL,
   modifiedOn smalldatetime DEFAULT NULL,
   modifiedBy BIGINT DEFAULT NULL
) ;
GO

ALTER TABLE contract_boards
    ADD CONSTRAINT fk_contract_boards_1 FOREIGN KEY (createdBy) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE contract_boards
    ADD CONSTRAINT fk_contract_boards_2 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO

IF OBJECT_ID('dbo.contract_board_columns', 'U') IS NOT NULL DROP TABLE dbo.contract_board_columns;
GO

CREATE TABLE contract_board_columns (
   id BIGINT NOT NULL PRIMARY KEY IDENTITY,
   board_id BIGINT NOT NULL,
   column_order tinyint NOT NULL,
   name nvarchar(255) NOT NULL,
   color nvarchar(255) NOT NULL
) 
GO

ALTER TABLE contract_board_columns
    ADD CONSTRAINT fk_contract_board_columns_1 FOREIGN KEY (board_id) REFERENCES contract_boards (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_board_column_options', 'U') IS NOT NULL DROP TABLE dbo.contract_board_column_options;
GO

CREATE TABLE contract_board_column_options (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  board_column_id BIGINT NOT NULL,
  status_id BIGINT NOT NULL
) ;
GO

ALTER TABLE contract_board_column_options
    ADD CONSTRAINT fk_contract_board_column_opts_1 FOREIGN KEY (status_id) REFERENCES contract_status (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contract_board_column_options
    ADD CONSTRAINT fk_contract_board_column_opts_3 FOREIGN KEY (board_column_id) REFERENCES contract_board_columns (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

IF OBJECT_ID('dbo.contract_board_post_filters', 'U') IS NOT NULL DROP TABLE dbo.contract_board_post_filters;
GO

CREATE TABLE contract_board_post_filters (
   id BIGINT NOT NULL PRIMARY KEY IDENTITY,
   board_id BIGINT NOT NULL,
   name nvarchar(255) NOT NULL,
   field nvarchar(255) NOT NULL,
   operator nvarchar(255) NOT NULL,
   value nvarchar(255) NOT NULL
) ;
GO

ALTER TABLE contract_board_post_filters
    ADD CONSTRAINT fk_contract_board_post_filters_1 FOREIGN KEY (board_id) REFERENCES contract_boards (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_board_post_filters_user', 'U') IS NOT NULL DROP TABLE dbo.contract_board_post_filters_user;
GO

CREATE TABLE contract_board_post_filters_user (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  board_post_filters_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL
) ;
GO
CREATE UNIQUE INDEX board_post_filters_id_user_id ON contract_board_post_filters_user(board_post_filters_id,user_id)
    GO

ALTER TABLE contract_board_post_filters_user
    ADD CONSTRAINT fk_contract_board_post_filters_user_1 FOREIGN KEY (board_post_filters_id) REFERENCES contract_board_post_filters (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contract_board_post_filters_user
    ADD CONSTRAINT fk_contract_board_post_filters_user_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.contract_board_grid_saved_filters_users', 'U') IS NOT NULL DROP TABLE dbo.contract_board_grid_saved_filters_users;
GO
CREATE TABLE contract_board_grid_saved_filters_users (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  filter_id BIGINT DEFAULT NULL,
  user_id BIGINT DEFAULT NULL,
  board_id BIGINT NOT NULL,
);
GO

CREATE UNIQUE INDEX board_id_user_id ON contract_board_grid_saved_filters_users(board_id,user_id)
GO

ALTER TABLE contract_board_grid_saved_filters_users
    ADD CONSTRAINT contract_board_grid_saved_filters_users_ibfk_1 FOREIGN KEY (board_id) REFERENCES contract_boards (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE contract_board_grid_saved_filters_users
    ADD CONSTRAINT contract_board_grid_saved_filters_users_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
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

IF OBJECT_ID('dbo.approval_signature_documents', 'U') IS NOT NULL DROP TABLE dbo.approval_signature_documents;
GO
CREATE TABLE approval_signature_documents (
    id BIGINT NOT NULL PRIMARY KEY IDENTITY,
    document_id BIGINT NOT NULL,
    to_be_approved TINYINT DEFAULT 0,
    to_be_signed TINYINT DEFAULT 0,
);

IF OBJECT_ID('dbo.advisor_users', 'U') IS NOT NULL DROP TABLE dbo.advisor_users;
--advisor_users;
CREATE TABLE advisor_users (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  email nvarchar(255) NOT NULL,
  password nvarchar(255) NULL,
  banned char(1) NULL,
  ban_reason nvarchar(255) DEFAULT NULL,
  last_ip nvarchar(45) DEFAULT NULL,
  last_login smalldatetime DEFAULT NULL,
  status nvarchar(45) NOT NULL,
  firstName nvarchar(255) DEFAULT NULL,
  lastName nvarchar(255) DEFAULT NULL,
  jobTitle nvarchar(255) DEFAULT NULL,
  phone nvarchar(255) DEFAULT NULL,
  mobile nvarchar(255) DEFAULT NULL,
  address nvarchar(255) DEFAULT NULL,
  contact_id BIGINT NULL,
  company_id BIGINT NULL,
  flagChangePassword TINYINT NULL DEFAULT '0',
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  createdByChannel NVARCHAR(3) DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL,
  modifiedByChannel NVARCHAR(3) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.advisor_tasks', 'U') IS NOT NULL DROP TABLE dbo.advisor_tasks;
GO
--advisor_tasks;
CREATE TABLE advisor_tasks (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  legal_case_id BIGINT DEFAULT NULL,
  stage BIGINT DEFAULT NULL,
  advisor_id BIGINT NOT NULL,
  assigned_to BIGINT NOT NULL,
  due_date date NOT NULL,
  private char(3) DEFAULT NULL,
  priority nvarchar(8) NOT NULL CHECK(priority IN ('critical','high','medium','low')) DEFAULT 'medium',
  advisor_task_location_id BIGINT DEFAULT NULL,
  description nvarchar(max),
  advisor_task_status_id BIGINT NOT NULL,
  advisor_task_type_id BIGINT NOT NULL,
  estimated_effort decimal(8,2) DEFAULT NULL,
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL,
  archived nvarchar(3) NOT NULL CHECK(archived IN ('yes','no')) DEFAULT 'no',
  reporter BIGINT DEFAULT NULL,
  workflow BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.advisor_task_statuses', 'U') IS NOT NULL DROP TABLE dbo.advisor_task_statuses;
GO
--advisor_task_statuses;
CREATE TABLE advisor_task_statuses (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL,
  category nvarchar(255) NOT NULL DEFAULT 'in progress',
  isGlobal TINYINT NOT NULL DEFAULT '1'
);
GO

IF OBJECT_ID('dbo.advisor_task_types', 'U') IS NOT NULL DROP TABLE dbo.advisor_task_types;
GO
--advisor_task_types;
CREATE TABLE advisor_task_types (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY
);
GO

IF OBJECT_ID('dbo.advisor_task_types_languages', 'U') IS NOT NULL DROP TABLE dbo.advisor_task_types_languages;
GO
--advisor_task_types_languages;
CREATE TABLE advisor_task_types_languages (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  advisor_task_type_id BIGINT NOT NULL,
  language_id BIGINT NOT NULL,
  name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.advisor_task_comments', 'U') IS NOT NULL DROP TABLE dbo.advisor_task_comments;
GO
--advisor_task_comments;
CREATE TABLE advisor_task_comments (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  advisor_task_id BIGINT NOT NULL,
  comment nvarchar(max) NOT NULL,
  edited TINYINT NOT NULL DEFAULT '0',
  createdOn smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  createdBy BIGINT NOT NULL,
  createdByChannel nvarchar(3) DEFAULT 'AP',
  modifiedOn smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  modifiedBy BIGINT NOT NULL,
  modifiedByChannel nvarchar(3) DEFAULT 'AP'
);
GO

IF OBJECT_ID('dbo.advisor_task_sharedwith_users', 'U') IS NOT NULL DROP TABLE dbo.advisor_task_sharedwith_users;
GO
--advisor_task_sharedwith_users;
CREATE TABLE advisor_task_sharedwith_users (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  advisor_task_id BIGINT NOT NULL,
  advisor_id BIGINT NOT NULL
);
GO
CREATE UNIQUE INDEX advisor_task_id_advisor_id ON advisor_task_sharedwith_users ( advisor_task_id , advisor_id );
GO

IF OBJECT_ID('dbo.advisor_task_locations', 'U') IS NOT NULL DROP TABLE dbo.advisor_task_locations;
GO
--advisor_task_locations;
CREATE TABLE advisor_task_locations (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.advisor_permissions', 'U') IS NOT NULL DROP TABLE dbo.advisor_permissions;
GO
--advisor_permissions;
CREATE TABLE advisor_permissions (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  workflow_id BIGINT NULL,
  workflow_status_transition_id BIGINT NULL
);
GO

IF OBJECT_ID('dbo.advisor_user_activity_logs', 'U') IS NOT NULL DROP TABLE dbo.advisor_user_activity_logs;
GO
--advisor_user_activity_logs;
CREATE TABLE advisor_user_activity_logs (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  advisor_id BIGINT NOT NULL,
  advisor_task_id BIGINT DEFAULT NULL,
  legal_case_id BIGINT DEFAULT NULL,
  client_id BIGINT DEFAULT NULL,
  time_type_id BIGINT NULL,
  logDate date NOT NULL,
  effectiveEffort decimal(8,2) NOT NULL,
  comments NVARCHAR(MAX),
  timeStatus nvarchar(8) NULL CHECK(timeStatus IN ('', 'internal', 'billable')),
  createdBy BIGINT DEFAULT NULL,
  createdOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.advisor_task_workflows', 'U') IS NOT NULL DROP TABLE dbo.advisor_task_workflows;
GO
--advisor_task_workflows;
CREATE TABLE advisor_task_workflows (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL,
  type nvarchar(255) NOT NULL,
  createdBy BIGINT DEFAULT NULL,
  createdOn DATETIME DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL,
  modifiedOn DATETIME DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.advisor_task_workflows_permissions', 'U') IS NOT NULL DROP TABLE dbo.advisor_task_workflows_permissions;
GO
--advisor_task_workflows_permissions;
CREATE TABLE advisor_task_workflows_permissions (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  advisor_task_workflow_id BIGINT DEFAULT NULL,
  advisor_task_workflow_status_transition_id BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.advisor_task_workflow_statuses', 'U') IS NOT NULL DROP TABLE dbo.advisor_task_workflow_statuses;
GO
--advisor_task_workflow_statuses;
CREATE TABLE advisor_task_workflow_statuses (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  advisor_task_workflow_id BIGINT NOT NULL,
  advisor_task_status_id BIGINT NOT NULL,
  start_point TINYINT NOT NULL DEFAULT '0'
);
GO

IF OBJECT_ID('dbo.oauth_access_tokens', 'U') IS NOT NULL DROP TABLE dbo.oauth_access_tokens;
GO
--oauth_access_tokens;
CREATE TABLE oauth_access_tokens (
  id NVARCHAR(100) NOT NULL PRIMARY KEY,
  user_id BIGINT DEFAULT NULL,
  client_id BIGINT NOT NULL,
  name NVARCHAR(191) DEFAULT NULL,
  scopes NVARCHAR(max),
  revoked SMALLINT NOT NULL,
  created_at datetime2(0) NULL DEFAULT NULL,
  updated_at datetime2(0) NULL DEFAULT NULL,
  expires_at datetime2(0) DEFAULT NULL
);
GO
CREATE INDEX oauth_access_tokens_user_id_index ON oauth_access_tokens (user_id);
GO

IF OBJECT_ID('dbo.oauth_auth_codes', 'U') IS NOT NULL DROP TABLE dbo.oauth_auth_codes;
GO
--oauth_auth_codes;
CREATE TABLE oauth_auth_codes (
  id NVARCHAR(100) PRIMARY KEY NOT NULL,
  user_id BIGINT NOT NULL,
  client_id BIGINT NOT NULL,
  scopes NVARCHAR(max),
  revoked SMALLINT NOT NULL,
  expires_at datetime2(0) DEFAULT NULL
);
GO
CREATE INDEX oauth_auth_codes_user_id_index ON oauth_auth_codes (user_id);
GO

IF OBJECT_ID('dbo.oauth_clients', 'U') IS NOT NULL DROP TABLE dbo.oauth_clients;
GO
--oauth_clients;
CREATE TABLE oauth_clients (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  user_id bigint DEFAULT NULL,
  name NVARCHAR(191) NOT NULL,
  secret NVARCHAR(100) DEFAULT NULL,
  provider NVARCHAR(191) DEFAULT NULL,
  redirect NVARCHAR(max) NOT NULL,
  personal_access_client smallint NOT NULL,
  password_client SMALLINT NOT NULL,
  revoked SMALLINT NOT NULL,
  created_at datetime2(0) NULL DEFAULT NULL,
  updated_at datetime2(0) NULL DEFAULT NULL
);
GO
CREATE INDEX oauth_clients_user_id_index ON oauth_clients (user_id);
GO

IF OBJECT_ID('dbo.oauth_personal_access_clients', 'U') IS NOT NULL DROP TABLE dbo.oauth_personal_access_clients;
GO
--oauth_personal_access_clients;
CREATE TABLE oauth_personal_access_clients (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  client_id BIGINT NOT NULL,
  created_at datetime2(0) NULL DEFAULT NULL,
  updated_at datetime2(0) NULL DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.oauth_refresh_tokens', 'U') IS NOT NULL DROP TABLE dbo.oauth_refresh_tokens;
GO
--oauth_refresh_tokens;
CREATE TABLE oauth_refresh_tokens (
  id NVARCHAR(100) NOT NULL PRIMARY KEY,
  access_token_id NVARCHAR(100) NOT NULL,
  revoked SMALLINT NOT NULL,
  expires_at datetime2(0) DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.permissions', 'U') IS NOT NULL DROP TABLE dbo.permissions;
GO
--permissions;
CREATE TABLE permissions (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name NVARCHAR(191) NOT NULL,
  guard_name NVARCHAR(191) NOT NULL,
  created_at datetime2(0) NULL DEFAULT NULL,
  updated_at datetime2(0) NULL DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.roles', 'U') IS NOT NULL DROP TABLE dbo.roles;
GO
--roles;
CREATE TABLE roles (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name NVARCHAR(191) NOT NULL,
  guard_name NVARCHAR(191) NOT NULL,
  created_at datetime2(0) NULL DEFAULT NULL,
  updated_at datetime2(0) NULL DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.model_has_permissions', 'U') IS NOT NULL DROP TABLE dbo.model_has_permissions;
GO
--model_has_permissions;
CREATE TABLE model_has_permissions (
  permission_id BIGINT NOT NULL PRIMARY KEY,
  model_type NVARCHAR(191) NOT NULL,
  model_id BIGINT NOT NULL
);
GO
CREATE INDEX model_has_permissions_model_id_model_type_index ON model_has_permissions (model_id, model_type);
GO

IF OBJECT_ID('dbo.model_has_roles', 'U') IS NOT NULL DROP TABLE dbo.model_has_roles;
GO
--model_has_roles;
CREATE TABLE model_has_roles (
  role_id BIGINT NOT NULL PRIMARY KEY,
  model_type NVARCHAR(191) NOT NULL,
  model_id BIGINT NOT NULL
);
GO
CREATE INDEX model_has_roles_model_id_model_type_index ON model_has_roles (model_id, model_type);
GO

IF OBJECT_ID('dbo.role_has_permissions', 'U') IS NOT NULL DROP TABLE dbo.role_has_permissions;
GO
--role_has_permissions;
CREATE TABLE role_has_permissions (
  permission_id BIGINT NOT NULL PRIMARY KEY,
  role_id BIGINT NOT NULL
);
GO
CREATE INDEX role_has_permissions_role_id_foreign ON role_has_permissions (role_id);
GO

IF OBJECT_ID('dbo.password_reset_token', 'U') IS NOT NULL DROP TABLE dbo.password_reset_token;
GO

--password_reset_token
CREATE TABLE password_reset_token (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  user_id BIGINT NOT NULL,
  user_type tinyint NOT NULL DEFAULT '1',
  token nvarchar(255) NOT NULL,
  used tinyint NOT NULL DEFAULT '0',
);
GO

IF OBJECT_ID('dbo.voucher_related_cases', 'U') IS NOT NULL DROP TABLE dbo.voucher_related_cases;
GO

CREATE TABLE voucher_related_cases (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 legal_case_id BIGINT NOT NULL,
 voucher_header_id BIGINT NOT NULL,
);
GO
ALTER TABLE voucher_related_cases
    ADD CONSTRAINT fk_voucher_related_cases_1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE voucher_related_cases
    ADD CONSTRAINT fk_voucher_related_cases_2 FOREIGN KEY (voucher_header_id) REFERENCES voucher_headers (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

IF OBJECT_ID('dbo.time_internal_statuses', 'U') IS NOT NULL DROP TABLE dbo.time_internal_statuses;
GO
CREATE TABLE time_internal_statuses (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY
);
GO

IF OBJECT_ID('dbo.time_internal_statuses_languages', 'U') IS NOT NULL DROP TABLE dbo.time_internal_statuses_languages;
GO
CREATE TABLE time_internal_statuses_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 internal_status BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.hearing_outcome_reasons', 'U') IS NOT NULL DROP TABLE dbo.hearing_outcome_reasons;
GO
--hearing_outcome_reasons;
CREATE TABLE hearing_outcome_reasons (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY
);
GO

IF OBJECT_ID('dbo.hearing_outcome_reasons_languages', 'U') IS NOT NULL DROP TABLE dbo.hearing_outcome_reasons_languages;
GO
--hearing_outcome_reasons_languages;
CREATE TABLE hearing_outcome_reasons_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 hearing_outcome_reason BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

IF OBJECT_ID('dbo.litigation_stage_status_history', 'U') IS NOT NULL DROP TABLE dbo.litigation_stage_status_history;
GO
--litigation_stage_status_history;
CREATE TABLE litigation_stage_status_history (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 litigation_stage BIGINT NOT NULL,
 status BIGINT NOT NULL,
 action_maker BIGINT NOT NULL,
 movedOn smalldatetime NOT NULL
);
GO

IF OBJECT_ID('dbo.legal_case_hearing_client_report_history', 'U') IS NOT NULL DROP TABLE dbo.legal_case_hearing_client_report_history;
GO
--legal_case_hearing_client_report_history;
CREATE TABLE legal_case_hearing_client_report_history (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 legal_case_hearing_id BIGINT NOT NULL,
 email_data nvarchar(max) NOT NULL,
 createdOn smalldatetime DEFAULT NULL,
 createdBy BIGINT DEFAULT NULL,
 modifiedOn smalldatetime DEFAULT NULL,
 modifiedBy BIGINT DEFAULT NULL,
);
GO

--board_post_filters
IF OBJECT_ID('dbo.board_post_filters', 'U') IS NOT NULL DROP TABLE dbo.board_post_filters;
GO
CREATE TABLE board_post_filters
(
    id       BIGINT        NOT NULL PRIMARY KEY IDENTITY,
    board_id BIGINT        NOT NULL,
    name     nvarchar(255) NOT NULL,
    field    nvarchar(255) NOT NULL,
    operator nvarchar(255) NOT NULL,
    value    nvarchar(255) NOT NULL,
);
GO

--board_post_filters_user
IF OBJECT_ID('dbo.board_post_filters_user', 'U') IS NOT NULL DROP TABLE dbo.board_post_filters_user;
GO
CREATE TABLE board_post_filters_user (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  board_post_filters_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL
);
GO

CREATE UNIQUE INDEX board_post_filters_id_user_id ON board_post_filters_user(board_post_filters_id,user_id)
GO

--grid_saved_board_filters_users
IF OBJECT_ID('dbo.grid_saved_board_filters_users', 'U') IS NOT NULL DROP TABLE dbo.grid_saved_board_filters_users;
GO
CREATE TABLE grid_saved_board_filters_users (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  filter_id BIGINT DEFAULT NULL,
  user_id BIGINT DEFAULT NULL,
  board_id BIGINT NOT NULL,
  is_board TINYINT DEFAULT NULL
);
GO

CREATE UNIQUE INDEX board_id_user_id ON grid_saved_board_filters_users(board_id,user_id)
GO

CREATE TABLE legal_case_container_advanced_export_slots (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  legal_case_container_id BIGINT NOT NULL,
  slot_name nvarchar(max) NOT NULL,
  slot_data nvarchar(max) NOT NULL
);
GO

IF OBJECT_ID('dbo.partner_settlements_invoices', 'U') IS NOT NULL DROP TABLE dbo.partner_settlements_invoices;
GO
CREATE TABLE partner_settlements_invoices (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  voucher_header_id BIGINT FOREIGN KEY REFERENCES voucher_headers(id),
  invoice_header_id BIGINT FOREIGN KEY REFERENCES invoice_headers(id)
);
GO

--advisor_email_templates
IF OBJECT_ID('dbo.advisor_email_templates', 'U') IS NOT NULL DROP TABLE dbo.advisor_email_templates;
GO

CREATE TABLE advisor_email_templates (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(50) NOT NULL,
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL
);
GO

CREATE UNIQUE INDEX "name" ON advisor_email_templates("name")
GO

--advisor_email_template_languages
IF OBJECT_ID('dbo.advisor_email_template_languages', 'U') IS NOT NULL DROP TABLE dbo.advisor_email_template_languages;
GO

CREATE TABLE advisor_email_template_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 advisor_email_template_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 static_content nvarchar(max) NOT NULL,
 content nvarchar(max) NULL
);
GO

IF OBJECT_ID('dbo.advisor_user_preferences', 'U') IS NOT NULL DROP TABLE dbo.advisor_user_preferences;
GO

--advisor_user_preferences;
CREATE TABLE advisor_user_preferences (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  advisor_user_id BIGINT NOT NULL,
  keyName nvarchar(255) NOT NULL,
  keyValue nvarchar(max) DEFAULT NULL
);
GO

--legal_case_outsources
IF OBJECT_ID('dbo.legal_case_outsources', 'U') IS NOT NULL DROP TABLE dbo.legal_case_outsources;
GO

CREATE TABLE legal_case_outsources (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  legal_case_id BIGINT NOT NULL,
  company_id BIGINT NOT NULL,
  createdOn smalldatetime DEFAULT NULL,
  createdBy BIGINT DEFAULT NULL,
  modifiedOn smalldatetime DEFAULT NULL,
  modifiedBy BIGINT DEFAULT NULL
);
GO

CREATE UNIQUE INDEX legal_case_id_company_id ON legal_case_outsources(legal_case_id, company_id)
GO

--legal_case_outsource_contacts
IF OBJECT_ID('dbo.legal_case_outsource_contacts', 'U') IS NOT NULL DROP TABLE dbo.legal_case_outsource_contacts;
GO

CREATE TABLE legal_case_outsource_contacts (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  legal_case_outsource_id BIGINT NOT NULL,
  contact_id BIGINT NOT NULL
);
GO

CREATE UNIQUE INDEX legal_case_outsource_id_contact_id ON legal_case_outsource_contacts(legal_case_outsource_id, contact_id)
CREATE TABLE money_dashboards (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL,
  columns_nb tinyint NOT NULL,
);
GO

CREATE TABLE money_dashboard_widgets_types (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name nvarchar(255) NOT NULL,
  type nvarchar(255) NOT NULL,
  settings NVARCHAR(MAX) NULL DEFAULT NULL,
);
GO

CREATE TABLE money_dashboard_widgets (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  filter NVARCHAR(MAX) NULL DEFAULT NULL,
  widget_order INT NOT NULL DEFAULT(0),
  money_dashboard_id BIGINT NOT NULL,
  money_dashboard_widgets_type_id BIGINT NOT NULL,
);
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

--board_task_post_filters
IF OBJECT_ID('dbo.board_task_post_filters', 'U') IS NOT NULL DROP TABLE dbo.board_task_post_filters;
GO
CREATE TABLE board_task_post_filters
(
    id       BIGINT        NOT NULL PRIMARY KEY IDENTITY,
    board_id BIGINT        NOT NULL,
    name     nvarchar(255) NOT NULL,
    field    nvarchar(255) NOT NULL,
    operator nvarchar(255) NOT NULL,
    value    nvarchar(255) NOT NULL,
);
GO

--board_task_post_filters_user
IF OBJECT_ID('dbo.board_task_post_filters_user', 'U') IS NOT NULL DROP TABLE dbo.board_task_post_filters_user;
GO
CREATE TABLE board_task_post_filters_user (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  board_post_filters_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL
);
GO

CREATE UNIQUE INDEX board_task_post_filters_id_user_id ON board_task_post_filters_user(board_post_filters_id,user_id)
GO

--grid_saved_board_filters_users
IF OBJECT_ID('dbo.grid_saved_board_task_filters_users', 'U') IS NOT NULL DROP TABLE dbo.grid_saved_board_task_filters_users;
GO
CREATE TABLE grid_saved_board_task_filters_users (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  filter_id BIGINT DEFAULT NULL,
  user_id BIGINT DEFAULT NULL,
  board_id BIGINT NOT NULL,
  is_board TINYINT DEFAULT NULL
);
GO

CREATE UNIQUE INDEX board_id_user_id ON grid_saved_board_task_filters_users(board_id,user_id)
GO

IF OBJECT_ID('dbo.advisor_timers', 'U') IS NOT NULL DROP TABLE dbo.advisor_timers;
GO
CREATE TABLE advisor_timers(
	id BIGINT NOT NULL PRIMARY KEY IDENTITY,
	advisor_id BIGINT NOT NULL,
	advisor_task_id BIGINT NULL,
	legal_case_id BIGINT NULL,
	time_type_id BIGINT NULL,
	comments NVARCHAR(MAX) NULL,
	timeStatus NVARCHAR(8) NULL,
	status NVARCHAR(8) NULL
);
GO

IF OBJECT_ID('dbo.advisor_timer_time_logs', 'U') IS NOT NULL DROP TABLE dbo.advisor_timer_time_logs;	
GO
CREATE TABLE advisor_timer_time_logs(
	id BIGINT NOT NULL PRIMARY KEY IDENTITY,
	advisor_timer_id BIGINT NOT NULL,
	startDate BIGINT NOT NULL,
	endDate BIGINT NULL
);
GO

IF OBJECT_ID('dbo.advisor_task_type_workflows', 'U') IS NOT NULL DROP TABLE dbo.advisor_task_type_workflows;
GO
CREATE TABLE [dbo].[advisor_task_type_workflows](
	[id] [bigint] IDENTITY(1,1) NOT NULL ,
	[advisor_task_workflow_id] [bigint] NULL,
	[advisor_task_type_id] [bigint] NULL
) ON [PRIMARY]
GO

IF OBJECT_ID('dbo.saml_configuration', 'U') IS NOT NULL DROP TABLE dbo.saml_configuration;
GO

CREATE TABLE saml_configuration (
 keyName VARCHAR( 255 ) NOT NULL,
 keyValue NVARCHAR(MAX) NULL DEFAULT NULL
);
GO

CREATE TABLE client_partner_shares (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 client_id BIGINT NOT NULL,
 account_id BIGINT NOT NULL,
 percentage decimal(22,2) NULL DEFAULT '0.00'
);
GO

CREATE TABLE legal_case_partner_shares (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 case_id BIGINT NOT NULL,
 account_id BIGINT NOT NULL,
 percentage decimal(22,2) NULL DEFAULT '0.00'
);
GO

IF OBJECT_ID('dbo.tasks_documents', 'U') IS NOT NULL DROP TABLE dbo.tasks_documents;
GO

CREATE TABLE tasks_documents (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 task_id BIGINT NOT NULL,
 document_id BIGINT NOT NULL
);
GO

IF OBJECT_ID('dbo.case_related_contracts', 'U') IS NOT NULL DROP TABLE dbo.case_related_contracts;
GO
CREATE TABLE case_related_contracts (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 legal_case_id BIGINT NOT NULL,
 contract_id BIGINT NOT NULL
 );
GO

IF OBJECT_ID('dbo.exchange_rates', 'U') IS NOT NULL DROP TABLE dbo.exchange_rates;
GO
CREATE TABLE exchange_rates (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  currency_id BIGINT FOREIGN KEY REFERENCES countries(id),
  organization_id BIGINT FOREIGN KEY REFERENCES organizations(id),
  rate decimal(22,10) DEFAULT NULL
);
GO

CREATE TABLE credit_note_headers (
  id BIGINT  NOT NULL PRIMARY KEY IDENTITY,
  organization_id BIGINT  NOT NULL,
  voucher_header_id BIGINT  DEFAULT NULL,
  account_id BIGINT  NOT NULL,
  bill_to text,
  credit_note_type_id BIGINT NULL DEFAULT NULL,
  transaction_type_id BIGINT NULL DEFAULT NULL,
  credit_note_reason_id BIGINT NULL DEFAULT NULL,
  term_id BIGINT  NOT NULL,
  prefix nvarchar(32) NOT NULL,
  suffix nvarchar(32) DEFAULT NULL,
  due_on smalldatetime DEFAULT NULL,
  credit_note_date smalldatetime NOT NULL,
  paid_status nvarchar(20) NOT NULL CHECK(paid_status in ('draft','open','partially refund','refund','cancelled')),
  total decimal(32,12) NOT NULL,
  credit_note_number nvarchar(255) DEFAULT NULL,
  reference_num nvarchar(255) NULL DEFAULT NULL,
  notes text,
  display_tax BIGINT  DEFAULT NULL,
  display_discount nvarchar(30) DEFAULT NULL,
  group_time_logs_by_user_in_export char(1) DEFAULT NULL,
  display_item_date tinyint DEFAULT '0',
  display_item_quantity tinyint DEFAULT '1',
  exchange_rate decimal(32,12) DEFAULT NULL,
  discount_id BIGINT  DEFAULT NULL,
  discount_percentage decimal(15,12) DEFAULT NULL,
  discount_amount decimal(32,12) DEFAULT NULL,
  discount_value_type nvarchar(10) DEFAULT NULL,
  description text,
  draft_credit_note_number BIGINT DEFAULT NULL,
  created_on smalldatetime DEFAULT NULL,
  created_by BIGINT  DEFAULT NULL,
  modified_on smalldatetime DEFAULT NULL,
  modified_by BIGINT  DEFAULT NULL,
  lines_total_discount DECIMAL(32,12) NOT NULL, 
  lines_total_subtotal DECIMAL(32,12) NOT NULL, 
  lines_total_tax DECIMAL(32,12) NOT NULL, 
  lines_totals DECIMAL(32,12) NOT NULL,
) ;

CREATE TABLE credit_note_details (
  id BIGINT  NOT NULL PRIMARY KEY IDENTITY,
  credit_note_header_id BIGINT  NOT NULL,
  account_id BIGINT  NOT NULL,
  item_id BIGINT  DEFAULT NULL,
  tax_id BIGINT  DEFAULT NULL,
  discount_id BIGINT  DEFAULT NULL,
  expense_id BIGINT  DEFAULT NULL,
  item_title nvarchar(255) NOT NULL,
  unit_price decimal(32,12) NOT NULL,
  quantity decimal(32,12) NOT NULL,
  item_description text DEFAULT NULL,
  tax_percentage decimal(15,12) DEFAULT NULL,
  discount_percentage decimal(15,12) DEFAULT NULL,
  discount_amount decimal(32,12) DEFAULT NULL,
  item_date date DEFAULT NULL,
  discount_type VARCHAR(10) NULL DEFAULT NULL,
  line_sub_total DECIMAL(32,12) NOT NULL ,
  sub_total_after_line_disc DECIMAL(32,12) NOT NULL, 
  tax_amount DECIMAL(32,12) NOT NULL, 
  total DECIMAL(32,12) NOT NULL
) ;

CREATE TABLE credit_note_time_logs_items (
  id BIGINT  NOT NULL PRIMARY KEY IDENTITY,
  credit_note_details_id BIGINT  NOT NULL,
  time_log_id BIGINT  NOT NULL,
  user_id BIGINT  NOT NULL,
  date date NOT NULL,
  description text
) ;

CREATE TABLE credit_note_refunds (
  id BIGINT  NOT NULL PRIMARY KEY IDENTITY,
  credit_note_header_id BIGINT  NOT NULL,
  voucher_header_id BIGINT  NOT NULL,
  account_id BIGINT  NOT NULL,
  refund_method nvarchar(20) NOT NULL CHECK(refund_method in ('Bank Transfer','Cash','Cheque','Credit Card','Other','Online payment','Trust Account')),
  total decimal(32,12) NOT NULL,
  client_account_id BIGINT  NOT NULL,
  credit_note_refund_total decimal(32,12) NOT NULL,
  exchange_rate decimal(32,12) DEFAULT NULL
) ;

/*credit note and invoice always on the same currency as the client currency*/
CREATE TABLE credit_note_invoices (
  id BIGINT  NOT NULL PRIMARY KEY IDENTITY,
  credit_note_header_id BIGINT  NOT NULL,
  invoice_header_id BIGINT  NOT NULL,
  total decimal(32,12) NOT NULL
) ;

CREATE TABLE credit_note_item_commissions (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  credit_note_header_id BIGINT  NOT NULL,
  credit_note_details_id BIGINT  NOT NULL,
  item_id BIGINT  DEFAULT NULL,
  expense_id BIGINT  DEFAULT NULL,
  time_logs_id BIGINT  DEFAULT NULL,
  account_id BIGINT  NOT NULL,
  commission_percent decimal(15,12) NOT NULL
) ;


CREATE TABLE credit_note_related_cases (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  legal_case_id BIGINT NOT NULL,
  credit_note_header_id BIGINT NOT NULL
) ;
GO

CREATE TABLE dbo.case_types_due_conditions
	(
	id bigint NOT NULL IDENTITY (1, 1),
	case_type_id bigint NOT NULL,
	client_id bigint NULL,
	priority nvarchar(8) NULL,
	due_in int NOT NULL
	)  ON [PRIMARY]
GO

IF OBJECT_ID('dbo.folder_templates', 'U') IS NOT NULL DROP TABLE dbo.folder_templates;
GO

CREATE TABLE folder_templates (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 category varchar(50) NULL,
 type_id varchar(255) NULL,
 folder_key varchar(255) NULL,
 parent_key varchar(255) NULL,
 name varchar(255) NOT NULL,
);
GO

IF OBJECT_ID('dbo.contract_cp_screens', 'U') IS NOT NULL DROP TABLE dbo.contract_cp_screens;
GO
CREATE TABLE contract_cp_screens (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 type_id BIGINT NOT NULL,
 sub_type_id BIGINT NOT NULL,
 name nvarchar( 255 ) NOT NULL,
 description text DEFAULT NULL,
 showInPortal CHAR( 1 ) NULL DEFAULT '1',
 contract_request_type_category_id BIGINT DEFAULT NULL
);
GO

IF OBJECT_ID('dbo.contract_cp_screen_fields', 'U') IS NOT NULL DROP TABLE dbo.contract_cp_screen_fields;
GO
CREATE TABLE contract_cp_screen_fields (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 screen_id BIGINT NOT NULL,
 related_field nvarchar( 255 ) NOT NULL,
 isRequired tinyint NOT NULL,
 visible tinyint NOT NULL DEFAULT 1,
 requiredDefaultValue nvarchar( 255 ) DEFAULT NULL,
 fieldDescription nvarchar( 255 ) DEFAULT NULL,
 sortOrder INT NOT NULL DEFAULT(0),
);
GO

IF OBJECT_ID('dbo.contract_cp_screen_field_languages', 'U') IS NOT NULL DROP TABLE dbo.contract_cp_screen_field_languages;
GO
CREATE TABLE contract_cp_screen_field_languages (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 screen_field_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 labelName nvarchar( 255 ) NOT NULL
);
GO

IF OBJECT_ID('dbo.request_type_categories', 'U') IS NOT NULL DROP TABLE dbo.request_type_categories;
GO

CREATE TABLE request_type_categories (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
);
GO


IF OBJECT_ID('dbo.contract_request_type_categories', 'U') IS NOT NULL DROP TABLE dbo.contract_request_type_categories;
GO

CREATE TABLE contract_request_type_categories(
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
);
GO

IF OBJECT_ID('dbo.integrations', 'U') IS NOT NULL DROP TABLE dbo.integrations;	
GO
CREATE TABLE integrations(
	id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  code NVARCHAR(50) NOT NULL,
	name NVARCHAR(50) NOT NULL,
	is_active TINYINT NULL DEFAULT '0'
);
GO

IF OBJECT_ID('dbo.user_integrations', 'U') IS NOT NULL DROP TABLE dbo.user_integrations;	
GO
CREATE TABLE user_integrations(
	id BIGINT NOT NULL PRIMARY KEY IDENTITY,
	user_id BIGINT NOT NULL,
	integration_id BIGINT NOT NULL,
	keyName NVARCHAR(255) NOT NULL ,
	keyValue NVARCHAR(MAX) NOT NULL,
  CONSTRAINT user_integration_key UNIQUE (user_id, integration_id, keyName)
);
GO

IF OBJECT_ID('dbo.module_preferences', 'U') IS NOT NULL DROP TABLE dbo.module_preferences;	
GO
CREATE TABLE module_preferences(
	id BIGINT NOT NULL PRIMARY KEY IDENTITY,
	module_name NVARCHAR(50) NOT NULL,
	module_record_id  BIGINT NOT NULL,
	integration_id BIGINT NOT NULL,
	keyName NVARCHAR(255) NOT NULL ,
	keyValue NVARCHAR(MAX) NOT NULL,
	CONSTRAINT module_preference_key UNIQUE (module_name, module_record_id, integration_id, keyName)
);
GO


IF OBJECT_ID('dbo.credit_note_reasons', 'U') IS NOT NULL DROP TABLE dbo.credit_note_reasons;
GO
CREATE TABLE credit_note_reasons ( id BIGINT  NOT NULL PRIMARY KEY IDENTITY , name nvarchar(255) NOT NULL ,
    fl1name nvarchar(255) NULL DEFAULT NULL, fl2name nvarchar(255) NULL DEFAULT NULL,
    is_debit_note tinyint NOT NULL DEFAULT 0) ;
GO


IF OBJECT_ID('dbo.payment_methods', 'U') IS NOT NULL DROP TABLE dbo.payment_methods;
GO
CREATE TABLE payment_methods ( id BIGINT  NOT NULL PRIMARY KEY IDENTITY , code nvarchar(255) NOT NULL , name nvarchar(255) NOT NULL ,
    fl1name nvarchar(255) NULL DEFAULT NULL, fl2name nvarchar(255) NULL DEFAULT NULL) ;
GO


IF OBJECT_ID('dbo.additional_id_types', 'U') IS NOT NULL DROP TABLE dbo.additional_id_types;
GO
CREATE TABLE additional_id_types ( id BIGINT  NOT NULL PRIMARY KEY IDENTITY , code nvarchar(255) NOT NULL , lang_msg nvarchar(255) NOT NULL ,
    module nvarchar(50) NULL DEFAULT NULL) ;
GO


IF OBJECT_ID('dbo.invoice_transaction_types', 'U') IS NOT NULL DROP TABLE dbo.invoice_transaction_types;
GO
CREATE TABLE invoice_transaction_types ( id BIGINT  NOT NULL PRIMARY KEY IDENTITY , name nvarchar(255) NOT NULL ,
    fl1name nvarchar(255) NULL DEFAULT NULL, fl2name nvarchar(255) NULL DEFAULT NULL) ;
GO


IF OBJECT_ID('dbo.invoice_types', 'U') IS NOT NULL DROP TABLE dbo.invoice_types;
GO
CREATE TABLE invoice_types ( id BIGINT  NOT NULL PRIMARY KEY IDENTITY , name nvarchar(255) NOT NULL ,
    fl1name nvarchar(255) NULL DEFAULT NULL, fl2name nvarchar(255) NULL DEFAULT NULL) ;
GO


IF OBJECT_ID('dbo.license_and_waiver_reminds', 'U') IS NOT NULL DROP TABLE dbo.license_and_waiver_reminds;
GO
CREATE TABLE license_and_waiver_reminds (
  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
  license_and_waiver_id BIGINT NOT NULL,
  user_id BIGINT DEFAULT NULL,
  user_group_id BIGINT DEFAULT NULL,
  reminder_id BIGINT DEFAULT NULL,
);
GO


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

IF OBJECT_ID('dbo.contracts_sla_actions', 'U') IS NOT NULL DROP TABLE dbo.contracts_sla_actions;
GO
CREATE TABLE contracts_sla_actions (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 sla_management_id BIGINT NOT NULL,
 status_id BIGINT NOT NULL,
 type nvarchar(255) NOT NULL,
);
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

IF OBJECT_ID('dbo.contract_parties_sla', 'U') IS NOT NULL DROP TABLE dbo.contract_parties_sla;
GO
CREATE TABLE contract_parties_sla (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 sla_management_id BIGINT NOT NULL,
 party_id BIGINT NOT NULL,
);
GO

IF OBJECT_ID('dbo.contracts_type_sla', 'U') IS NOT NULL DROP TABLE dbo.contracts_type_sla;
GO
CREATE TABLE contracts_type_sla (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 sla_management_id BIGINT NOT NULL,
 type_id BIGINT NOT NULL,
);
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

IF OBJECT_ID('dbo.trigger_types', 'U') IS NOT NULL DROP TABLE dbo.trigger_types;
GO
CREATE TABLE [dbo].[trigger_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [varchar](50) NOT NULL,
  CONSTRAINT [PK_trigger_types] PRIMARY KEY CLUSTERED ([id] ASC) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

IF OBJECT_ID('dbo.trigger_action_types', 'U') IS NOT NULL DROP TABLE dbo.trigger_action_types;
GO
CREATE TABLE [dbo].[trigger_action_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [varchar](50) NOT NULL,
 CONSTRAINT [PK_trigger_action_types] PRIMARY KEY CLUSTERED ([id] ASC) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

IF OBJECT_ID('dbo.triggers', 'U') IS NOT NULL DROP TABLE dbo.triggers;
GO
CREATE TABLE [dbo].[triggers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[trigger_type_id] [bigint] NOT NULL,
	[source_id] [bigint] NULL,
	[created_on] [datetime] NULL,
	[created_by] [bigint] NOT NULL,
	[modified_on] [datetime] NULL,
	[modified_by] [bigint] NOT NULL,
  CONSTRAINT [PK_triggers] PRIMARY KEY CLUSTERED ([id] ASC) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

IF OBJECT_ID('dbo.trigger_actions', 'U') IS NOT NULL DROP TABLE dbo.trigger_actions;
GO
CREATE TABLE [dbo].[trigger_actions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[trigger_id] [bigint] NOT NULL,
	[trigger_action_type_id] [bigint] NOT NULL,
	[created_by] [bigint] NOT NULL,
	[created_on] [datetime] NULL,
	[modified_by] [bigint] NOT NULL,
	[modified_on] [datetime] NULL,
  CONSTRAINT [PK_trigger_actions] PRIMARY KEY CLUSTERED ([id] ASC) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

IF OBJECT_ID('dbo.trigger_matter_workflow_conditions', 'U') IS NOT NULL DROP TABLE dbo.trigger_matter_workflow_conditions;
GO
CREATE TABLE [dbo].[trigger_matter_workflow_conditions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[trigger_id] [bigint] NOT NULL,
	[from_stage] [bigint] NOT NULL,
	[to_stage] [bigint] NOT NULL,
	[area_of_practice] [bigint] NOT NULL,
  CONSTRAINT [PK_trigger_matter_workflow_conditions] PRIMARY KEY CLUSTERED ([id] ASC) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

IF OBJECT_ID('dbo.trigger_action_task_values', 'U') IS NOT NULL DROP TABLE dbo.trigger_action_task_values;
GO
CREATE TABLE [dbo].[trigger_action_task_values](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[action_id] [bigint] NOT NULL,
	[due_date] [datetime] NULL,
	[task_type] [bigint] NOT NULL,
	[assigned_to] [bigint] NULL,
	[description] [varchar](50) NULL,
	[assigned_to_matter] [varchar](50) NULL,
	[title] [nvarchar](255) NOT NULL DEFAULT '',
  CONSTRAINT [PK_trigger_action_task_values] PRIMARY KEY CLUSTERED ([id] ASC) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

-- Views

-- audit_log_max_id;
IF OBJECT_ID('dbo.audit_log_max_id', 'V') IS NOT NULL DROP VIEW dbo.audit_log_max_id;
GO
CREATE VIEW audit_log_max_id AS
	SELECT TOP(9223372036854775800) MAX(audit_logs.id) AS id
	FROM audit_logs
	GROUP BY audit_logs.model, audit_logs.action, audit_logs.recordId
	ORDER BY MIN(audit_logs.id);
GO

IF OBJECT_ID('dbo.audit_log_last_action', 'V') IS NOT NULL DROP VIEW dbo.audit_log_last_action;
GO
--audit_log_last_action;
CREATE VIEW audit_log_last_action AS
	SELECT TOP(9223372036854775800) audit_logs.id AS id, audit_logs.user_id AS user_id,audit_logs.model AS model,audit_logs.action AS action,audit_logs.recordId AS recordId,audit_logs.created AS created,
	 user_profiles.firstName + ' ' + user_profiles.lastName AS fullName, users.username AS username, users.email AS email
	FROM audit_logs
	LEFT JOIN users ON users.id = audit_logs.user_id
	LEFT JOIN user_profiles ON user_profiles.user_id = audit_logs.user_id
	WHERE audit_logs.id IN (SELECT audit_log_max_id.id FROM audit_log_max_id);
GO

/****** Object:  UserDefinedFunction [dbo].[DelimitedSplit8K]  ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

 CREATE FUNCTION [dbo].[DelimitedSplit8K]
/**********************************************************************************************************************
 Purpose:
 Split a given string at a given delimiter and return a list of the split elements (items).

 Notes:
 1.  Leading a trailing delimiters are treated as if an empty string element were present.
 2.  Consecutive delimiters are treated as if an empty string element were present between them.
 3.  Except when spaces are used as a delimiter, all spaces present in each element are preserved.

 Returns:
 iTVF containing the following:
 ItemNumber = Element position of Item as a BIGINT (not converted to INT to eliminate a CAST)
 Item       = Element value as a VARCHAR(8000)

 Statistics on this function may be found at the following URL:
 http://www.sqlservercentral.com/Forums/Topic1101315-203-4.aspx

 CROSS APPLY Usage Examples and Tests:
--=====================================================================================================================
-- TEST 1:
-- This tests for various possible conditions in a string using a comma as the delimiter.  The expected results are
-- laid out in the comments
--=====================================================================================================================
--===== Conditionally drop the test tables to make reruns easier for testing.
     -- (this is NOT a part of the solution)
     IF OBJECT_ID('tempdb..#JBMTest') IS NOT NULL DROP TABLE #JBMTest
;
--===== Create and populate a test table on the fly (this is NOT a part of the solution).
     -- In the following comments, "b" is a blank and "E" is an element in the left to right order.
     -- Double Quotes are used to encapsulate the output of "Item" so that you can see that all blanks
     -- are preserved no matter where they may appear.
 SELECT *
   INTO #JBMTest
   FROM (                                               --# & type of Return Row(s)
         SELECT  0, NULL                      UNION ALL --1 NULL
         SELECT  1, SPACE(0)                  UNION ALL --1 b (Empty String)
         SELECT  2, SPACE(1)                  UNION ALL --1 b (1 space)
         SELECT  3, SPACE(5)                  UNION ALL --1 b (5 spaces)
         SELECT  4, ','                       UNION ALL --2 b b (both are empty strings)
         SELECT  5, '55555'                   UNION ALL --1 E
         SELECT  6, ',55555'                  UNION ALL --2 b E
         SELECT  7, ',55555,'                 UNION ALL --3 b E b
         SELECT  8, '55555,'                  UNION ALL --2 b B
         SELECT  9, '55555,1'                 UNION ALL --2 E E
         SELECT 10, '1,55555'                 UNION ALL --2 E E
         SELECT 11, '55555,4444,333,22,1'     UNION ALL --5 E E E E E
         SELECT 12, '55555,4444,,333,22,1'    UNION ALL --6 E E b E E E
         SELECT 13, ',55555,4444,,333,22,1,'  UNION ALL --8 b E E b E E E b
         SELECT 14, ',55555,4444,,,333,22,1,' UNION ALL --9 b E E b b E E E b
         SELECT 15, ' 4444,55555 '            UNION ALL --2 E (w/Leading Space) E (w/Trailing Space)
         SELECT 16, 'This,is,a,test.'                   --E E E E
        ) d (SomeID, SomeValue)
;
--===== Split the CSV column for the whole table using CROSS APPLY (this is the solution)
 SELECT test.SomeID, test.SomeValue, split.ItemNumber, Item = QUOTENAME(split.Item,'"')
   FROM #JBMTest test
  CROSS APPLY dbo.DelimitedSplit8K(test.SomeValue,',') split
;
--=====================================================================================================================
-- TEST 2:
-- This tests for various "alpha" splits and COLLATION using all ASCII characters from 0 to 255 as a delimiter against
-- a given string.  Note that not all of the delimiters will be visible and some will show up as tiny squares because
-- they are "control" characters.  More specifically, this test will show you what happens to various non-accented
-- letters for your given collation depending on the delimiter you chose.
--=====================================================================================================================
WITH
cteBuildAllCharacters (String,Delimiter) AS
(
 SELECT TOP 256
        'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
        CHAR(ROW_NUMBER() OVER (ORDER BY (SELECT NULL))-1)
   FROM master.sys.all_columns
)
 SELECT ASCII_Value = ASCII(c.Delimiter), c.Delimiter, split.ItemNumber, Item = QUOTENAME(split.Item,'"')
   FROM cteBuildAllCharacters c
  CROSS APPLY dbo.DelimitedSplit8K(c.String,c.Delimiter) split
  ORDER BY ASCII_Value, split.ItemNumber
;
-----------------------------------------------------------------------------------------------------------------------
 Other Notes:
 1. Optimized for VARCHAR(8000) or less.  No testing or error reporting for truncation at 8000 characters is done.
 2. Optimized for single character delimiter.  Multi-character delimiters should be resolvedexternally from this
    function.
 3. Optimized for use with CROSS APPLY.
 4. Does not "trim" elements just in case leading or trailing blanks are intended.
 5. If you don't know how a Tally table can be used to replace loops, please see the following...
    http://www.sqlservercentral.com/articles/T-SQL/62867/
 6. Changing this function to use NVARCHAR(MAX) will cause it to run twice as slow.  It's just the nature of
    VARCHAR(MAX) whether it fits in-row or not.
 7. Multi-machine testing for the method of using UNPIVOT instead of 10 SELECT/UNION ALLs shows that the UNPIVOT method
    is quite machine dependent and can slow things down quite a bit.
-----------------------------------------------------------------------------------------------------------------------
 Credits:
 This code is the product of many people's efforts including but not limited to the following:
 cteTally concept originally by Iztek Ben Gan and "decimalized" by Lynn Pettis (and others) for a bit of extra speed
 and finally redacted by Jeff Moden for a different slant on readability and compactness. Hat's off to Paul White for
 his simple explanations of CROSS APPLY and for his detailed testing efforts. Last but not least, thanks to
 Ron "BitBucket" McCullough and Wayne Sheffield for their extreme performance testing across multiple machines and
 versions of SQL Server.  The latest improvement brought an additional 15-20% improvement over Rev 05.  Special thanks
 to "Nadrek" and "peter-757102" (aka Peter de Heer) for bringing such improvements to light.  Nadrek's original
 improvement brought about a 10% performance gain and Peter followed that up with the content of Rev 07.

 I also thank whoever wrote the first article I ever saw on "numbers tables" which is located at the following URL
 and to Adam Machanic for leading me to it many years ago.
 http://sqlserver2000.databases.aspfaq.com/why-should-i-consider-using-an-auxiliary-numbers-table.html
-----------------------------------------------------------------------------------------------------------------------
 Revision History:
 Rev 00 - 20 Jan 2010 - Concept for inline cteTally: Lynn Pettis and others.
                        Redaction/Implementation: Jeff Moden
        - Base 10 redaction and reduction for CTE.  (Total rewrite)

 Rev 01 - 13 Mar 2010 - Jeff Moden
        - Removed one additional concatenation and one subtraction from the SUBSTRING in the SELECT List for that tiny
          bit of extra speed.

 Rev 02 - 14 Apr 2010 - Jeff Moden
        - No code changes.  Added CROSS APPLY usage example to the header, some additional credits, and extra
          documentation.

 Rev 03 - 18 Apr 2010 - Jeff Moden
        - No code changes.  Added notes 7, 8, and 9 about certain "optimizations" that don't actually work for this
          type of function.

 Rev 04 - 29 Jun 2010 - Jeff Moden
        - Added WITH SCHEMABINDING thanks to a note by Paul White.  This prevents an unnecessary "Table Spool" when the
          function is used in an UPDATE statement even though the function makes no external references.

 Rev 05 - 02 Apr 2011 - Jeff Moden
        - Rewritten for extreme performance improvement especially for larger strings approaching the 8K boundary and
          for strings that have wider elements.  The redaction of this code involved removing ALL concatenation of
          delimiters, optimization of the maximum "N" value by using TOP instead of including it in the WHERE clause,
          and the reduction of all previous calculations (thanks to the switch to a "zero based" cteTally) to just one
          instance of one add and one instance of a subtract. The length calculation for the final element (not
          followed by a delimiter) in the string to be split has been greatly simplified by using the ISNULL/NULLIF
          combination to determine when the CHARINDEX returned a 0 which indicates there are no more delimiters to be
          had or to start with. Depending on the width of the elements, this code is between 4 and 8 times faster on a
          single CPU box than the original code especially near the 8K boundary.
        - Modified comments to include more sanity checks on the usage example, etc.
        - Removed "other" notes 8 and 9 as they were no longer applicable.

 Rev 06 - 12 Apr 2011 - Jeff Moden
        - Based on a suggestion by Ron "Bitbucket" McCullough, additional test rows were added to the sample code and
          the code was changed to encapsulate the output in pipes so that spaces and empty strings could be perceived
          in the output.  The first "Notes" section was added.  Finally, an extra test was added to the comments above.

 Rev 07 - 06 May 2011 - Peter de Heer, a further 15-20% performance enhancement has been discovered and incorporated
          into this code which also eliminated the need for a "zero" position in the cteTally table.
**********************************************************************************************************************/
--===== Define I/O parameters
        (@pString VARCHAR(8000), @pDelimiter CHAR(1))
RETURNS TABLE WITH SCHEMABINDING AS
 RETURN
--===== "Inline" CTE Driven "Tally Table" produces values from 0 up to 10,000...
     -- enough to cover NVARCHAR(4000)
  WITH E1(N) AS (
                 SELECT 1 UNION ALL SELECT 1 UNION ALL SELECT 1 UNION ALL
                 SELECT 1 UNION ALL SELECT 1 UNION ALL SELECT 1 UNION ALL
                 SELECT 1 UNION ALL SELECT 1 UNION ALL SELECT 1 UNION ALL SELECT 1
                ),                          --10E+1 or 10 rows
       E2(N) AS (SELECT 1 FROM E1 a, E1 b), --10E+2 or 100 rows
       E4(N) AS (SELECT 1 FROM E2 a, E2 b), --10E+4 or 10,000 rows max
 cteTally(N) AS (--==== This provides the "base" CTE and limits the number of rows right up front
                     -- for both a performance gain and prevention of accidental "overruns"
                 SELECT TOP (ISNULL(DATALENGTH(@pString),0)) ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) FROM E4
                ),
cteStart(N1) AS (--==== This returns N+1 (starting position of each "element" just once for each delimiter)
                 SELECT 1 UNION ALL
                 SELECT t.N+1 FROM cteTally t WHERE SUBSTRING(@pString,t.N,1) = @pDelimiter
                ),
cteLen(N1,L1) AS(--==== Return start and length (for use in substring)
                 SELECT s.N1,
                        ISNULL(NULLIF(CHARINDEX(@pDelimiter,@pString,s.N1),0)-s.N1,8000)
                   FROM cteStart s
                )
--===== Do the actual split. The ISNULL/NULLIF combo handles the length for the final element when no delimiter is found.
 SELECT ItemNumber = ROW_NUMBER() OVER(ORDER BY l.N1),
        Item       = SUBSTRING(@pString, l.N1, l.L1)
   FROM cteLen l
;
GO

set ansi_padding on;
go
create function dbo.SubstringIndex(
    @SourceString varchar(8000),
    @delim char(1),
    @idx int
)
returns table with schemabinding
return
with stritems as (
select
    ItemNumber,
    Item
from
    dbo.DelimitedSplit8k(@SourceString,@delim)
)
select
    Item = stuff((select @delim + si.Item
                  from stritems si
                  where si.ItemNumber <= @idx
                  order by si.ItemNumber
                  for xml path(''),TYPE).value('.','varchar(8000)'),1,1,'')
go

IF OBJECT_ID('dbo.legal_case_effective_effort', 'V') IS NOT NULL DROP VIEW dbo.legal_case_effective_effort;
GO

--legal_case_effective_effort;
CREATE VIEW legal_case_effective_effort AS
	SELECT TOP(9223372036854775800) user_activity_logs.legal_case_id AS legal_case_id, SUM(user_activity_logs.effectiveEffort) AS effectiveEffort
	FROM user_activity_logs
	WHERE user_activity_logs.legal_case_id IS NOT NULL
	GROUP BY user_activity_logs.legal_case_id;
GO

IF OBJECT_ID('dbo.members', 'V') IS NOT NULL DROP VIEW dbo.members;
GO
--members;
CREATE VIEW members AS
	SELECT TOP(9223372036854775800) lookm.id AS id, CASE WHEN lookm.contact_id IS NOT NULL THEN 'PER' ELSE 'COM' END AS modelCode,
		CASE WHEN lookm.contact_id IS NOT NULL THEN lookm.contact_id ELSE lookm.company_id END AS linkId,
		CASE WHEN lookm.contact_id IS NOT NULL THEN 'Person' ELSE 'Company' END AS type,
		CASE WHEN lookm.contact_id IS NOT NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS name
	FROM lookup_members lookm
	LEFT JOIN companies com ON com.id = lookm.company_id
	LEFT JOIN contacts con  ON con.id = lookm.contact_id;
GO

IF OBJECT_ID('dbo.shares_movement_history', 'V') IS NOT NULL DROP VIEW dbo.shares_movement_history;
GO
--shares_movement_history;
CREATE VIEW shares_movement_history AS
	SELECT TOP(9223372036854775800) mem.linkId AS id, sha.id as share_id, mem.type AS shareholderType, mem.name AS shareholderName, sha.company_id AS company_id, sha.member_id AS member_id, sha.initiatedOn AS initiatedOn,
	sha.executedOn AS executedOn, sha.type AS type, sha.numberOfShares AS numberOfShares, sha.comments AS comments
	FROM shares_movements sha
	LEFT JOIN members mem ON mem.id = sha.member_id;
GO

IF OBJECT_ID('dbo.shareholders', 'V') IS NOT NULL DROP VIEW dbo.shareholders;
GO
--shareholders;
CREATE VIEW shareholders AS
	SELECT TOP(9223372036854775800) smh.id AS id,
	smh.company_id AS company_id, MIN(smh.member_id) AS member_id,
	smh.shareholderType AS shareholderType, MIN(smh.shareholderName) AS shareholderName, MIN(sm.id) AS shareholderId,
	MIN(smh.type) AS type, SUM(smh.numberOfShares) AS numberOfShares,
	CASE WHEN c.bearerShares = 0 and c.nominalShares = 0 THEN 0 ELSE  ROUND((SUM(smh.numberOfShares) / ((c.bearerShares) + (c.nominalShares))),8) End as percentage,
	(AVG(c.shareParValue) * SUM(smh.numberOfShares)) AS sharesValue, MAX(c.shareParValueCurrency) AS currency, MIN(smh.comments) AS comments
	FROM shares_movement_history smh
	LEFT JOIN companies c ON c.id = smh.company_id
	LEFT JOIN shares_movements sm ON (sm.id = smh.share_id AND sm.member_id = smh.member_id AND sm.company_id = smh.company_id)
	GROUP BY smh.company_id,smh.id, smh.shareholderType ,c.nominalShares,c.bearerShares
	HAVING SUM(smh.numberOfShares) <> 0;
GO

IF OBJECT_ID('dbo.task_effective_effort', 'V') IS NOT NULL DROP VIEW dbo.task_effective_effort;
GO
--task_effective_effort;
CREATE VIEW task_effective_effort AS
	SELECT TOP(9223372036854775800) user_activity_logs.task_id AS task_id, SUM(user_activity_logs.effectiveEffort) AS effectiveEffort
	FROM user_activity_logs
	WHERE user_activity_logs.task_id IS NOT NULL
	GROUP BY user_activity_logs.task_id;
GO

IF OBJECT_ID('dbo.contacts_grid', 'V') IS NOT NULL DROP VIEW dbo.contacts_grid;
GO
--contacts_grid;
CREATE VIEW contacts_grid AS SELECT TOP(9223372036854775800) contacts.id, contacts.status, contacts.gender, contacts.title_id, contacts.firstName, contacts.lastName, CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END AS fullName,
    contacts.foreignFirstName, contacts.foreignLastName, ISNULL(contacts.foreignFirstName, '') +  ' ' +  ISNULL(contacts.foreignLastName, '') AS foreignFullName, contacts.father, contacts.mother,
    contacts.dateOfBirth, contacts.contact_category_id, contacts.contact_sub_category_id, contacts.jobTitle, contacts.private, contacts.isLawyer, contacts.lawyerForCompany, contacts.website,
    contacts.phone, contacts.fax, contacts.mobile, contacts.address1, contacts.address2, contacts.city, contacts.state, contacts.zip, contacts.country_id, contacts.comments, contacts.createdOn,
    (created.firstName + ' ' + created.lastName) AS createdByName, contacts.createdBy, contacts.modifiedOn,(modified.firstName + ' ' + modified.lastName) AS modifiedByName, contacts.modifiedBy, CAST(contacts.createdOn AS DATE) AS createdOnDate ,  'PER' +  CAST(contacts.id AS nvarchar) as contactID,
    contact_company_categories.name AS category, contact_company_categories.keyName AS category_keyName, contact_company_sub_categories.name AS subCategory, contacts.internalReference AS internalReference,
    email=STUFF( (SELECT '; '+ contact_emails.email FROM contact_emails INNER JOIN contacts ct ON ct.id = contact_emails.contact_id  where contacts.id = contact_emails.contact_id  FOR XML PATH('')), 1, 1, ''),
    company=STUFF( (SELECT '; '+ companies.name FROM companies_contacts INNER JOIN companies ON companies.id = companies_contacts.company_id WHERE companies_contacts.contact_id = contacts.id FOR XML PATH('')), 1, 1, '')
    ,contacts.tax_number, contacts.street_name,contacts.additional_street_name, contacts.building_number, contacts.address_additional_number, contacts.district_neighborhood, contacts.additional_id_type, contacts.additional_id_value
    FROM contacts
        LEFT JOIN user_profiles created ON created.user_id = contacts.createdBy
        LEFT JOIN user_profiles modified ON modified.user_id = contacts.modifiedBy
        LEFT JOIN contact_company_categories ON contact_company_categories.id = contacts.contact_category_id
        LEFT JOIN contact_company_sub_categories ON contact_company_sub_categories.id = contacts.contact_sub_category_id;
GO

IF OBJECT_ID('dbo.tasks_detailed_view', 'V') IS NOT NULL DROP VIEW dbo.tasks_detailed_view
GO
--tasks_detailed_view
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

--legal_cases_per_company;
IF OBJECT_ID('dbo.legal_cases_per_company', 'V') IS NOT NULL DROP VIEW dbo.legal_cases_per_company;
GO
CREATE VIEW legal_cases_per_company AS SELECT TOP(9223372036854775800) legal_cases.id, legal_cases.case_status_id, legal_cases.case_type_id, legal_cases.provider_group_id,
	legal_cases.user_id, legal_cases.isDeleted as isDeleted, legal_cases.contact_id, legal_cases.client_id, legal_cases.subject, legal_cases.description, legal_cases.priority, legal_cases.arrivalDate, legal_cases.dueDate,
	legal_cases.statusComments, legal_cases.category, legal_cases.caseValue, legal_cases.internalReference, legal_cases.externalizeLawyers, legal_cases.estimatedEffort,
	CAST(legal_cases.createdOn as DATE) as createdOn, CAST(legal_cases.modifiedOn as DATE) modifiedOn,
	legal_cases.createdBy, legal_cases.modifiedBy, legal_cases.archived, legal_cases.private, lcee.effectiveEffort, 'M' + CAST(legal_cases.id AS nvarchar) AS caseID, workflow_status.name AS status,
	case_types.name AS type, provider_groups.name AS providerGroup, UP.firstName + ' ' + UP.lastName AS assignee, legal_cases.archived AS archivedCases, com.name AS company, lccr.name AS role,
	lccr.id AS role_id,
        lcld.sentenceDate,lcld.court_type_id,lcld.court_degree_id,lcld.court_region_id,lcld.court_id,
        contactContributor = STUFF((SELECT ', ' +
            (CASE WHEN conHE.father!='' THEN conHE.firstName + ' '+ conHE.father + ' ' + conHE.lastName ELSE conHE.firstName+' '+conHE.lastName END)
            FROM legal_cases legal_case_contributor
            LEFT JOIN legal_cases_contacts lccch ON lccch.case_id = legal_cases.id AND lccch.contactType = 'contributor'
            LEFT JOIN contacts conHE ON conHE.id = lccch.contact_id
            WHERE legal_case_contributor.id = legal_cases.id
            FOR XML PATH('')), 1, 1, ''),
        contactOutsourceTo = STUFF((SELECT ', ' +
            (CASE WHEN conExtLaw.father!='' THEN conExtLaw.firstName + ' '+ conExtLaw.father + ' ' + conExtLaw.lastName ELSE conExtLaw.firstName+' '+conExtLaw.lastName END)
             FROM legal_cases legal_case_outsource
             LEFT JOIN legal_cases_contacts lccExtLaw ON lccExtLaw.case_id = legal_cases.id AND lccExtLaw.contactType = 'external lawyer'
        LEFT JOIN contacts conExtLaw ON conExtLaw.id = lccExtLaw.contact_id
         WHERE legal_case_outsource.id = legal_cases.id
            FOR XML PATH('')), 1, 1, ''),
		companyOutsourceTo = STUFF((SELECT ', ' + companiesExtLaw.name
             FROM legal_cases legal_case_outsource
             LEFT JOIN
                                             legal_cases_companies lccompaniesExtLaw
                                             ON
                                                       lccompaniesExtLaw.case_id         = legal_case_outsource.id
                                                       AND lccompaniesExtLaw.companyType = 'external lawyer'
                                   LEFT JOIN
                                             companies companiesExtLaw
                                             ON
                                                       companiesExtLaw.id = lccompaniesExtLaw.company_id
                         WHERE
                                   legal_case_outsource.id = legal_cases.id
            FOR XML PATH('')), 1, 1, '')
FROM legal_cases
LEFT JOIN workflow_status ON workflow_status.id = legal_cases.case_status_id
INNER JOIN case_types ON case_types.id = legal_cases.case_type_id
INNER JOIN provider_groups ON provider_groups.id = legal_cases.provider_group_id
LEFT JOIN user_profiles AS UP ON UP.user_id = legal_cases.user_id
LEFT JOIN legal_cases_companies lcccom ON lcccom.case_id = legal_cases.id
LEFT JOIN companies com ON lcccom.company_id = com.id
LEFT JOIN legal_case_company_roles lccr ON lccr.id = lcccom.legal_case_company_role_id
LEFT JOIN legal_case_effective_effort AS lcee ON lcee.legal_case_id = legal_cases.id
LEFT JOIN legal_case_litigation_details lcld ON lcld.legal_case_id = legal_cases.id AND lcld.id = legal_cases.stage
Where legal_cases.isDeleted = 0;
GO

--legal_cases_per_contact;
IF OBJECT_ID('dbo.legal_cases_per_contact', 'V') IS NOT NULL DROP VIEW dbo.legal_cases_per_contact;
GO
CREATE VIEW legal_cases_per_contact AS SELECT TOP(9223372036854775800) legal_cases.id,  legal_cases.isDeleted as isDeleted, legal_cases.case_status_id, legal_cases.case_type_id, legal_cases.provider_group_id, legal_cases.user_id,
	legal_cases.contact_id, legal_cases.client_id, legal_cases.subject, legal_cases.description, legal_cases.priority, legal_cases.arrivalDate, legal_cases.dueDate, legal_cases.statusComments,
	legal_cases.category, legal_cases.caseValue, legal_cases.internalReference, legal_cases.externalizeLawyers, legal_cases.estimatedEffort,
	CAST(legal_cases.createdOn as DATE) createdOn, CAST(legal_cases.modifiedOn as DATE) modifiedOn,
	legal_cases.createdBy, legal_cases.modifiedBy, legal_cases.archived, legal_cases.private, lcee.effectiveEffort, 'M' + CAST(legal_cases.id AS nvarchar) AS caseID, workflow_status.name AS status,
	case_types.name AS type, provider_groups.name AS providerGroup, UP.firstName + ' ' + UP.lastName AS assignee, legal_cases.archived AS archivedCases,
	CASE WHEN conRE.father!='' THEN conRE.firstName + ' '+ conRE.father + ' ' + conRE.lastName ELSE conRE.firstName+' '+conRE.lastName END AS contact, lccr.name AS role, lccr.id AS role_id,
        lcld.sentenceDate,lcld.court_type_id,lcld.court_degree_id,lcld.court_region_id,lcld.court_id,
        contactContributor = STUFF((SELECT ', ' +
        (CASE WHEN conHE.father!='' THEN conHE.firstName + ' '+ conHE.father + ' ' + conHE.lastName ELSE conHE.firstName+' '+conHE.lastName END)
            FROM legal_cases legal_case_contributor
            LEFT JOIN legal_cases_contacts lccch ON lccch.case_id = legal_cases.id AND lccch.contactType = 'contributor'
            LEFT JOIN contacts conHE ON conHE.id = lccch.contact_id
            WHERE legal_case_contributor.id = legal_cases.id
            FOR XML PATH('')), 1, 1, ''),
        contactOutsourceTo = STUFF((SELECT ', ' +
                (CASE WHEN conExtLaw.father!='' THEN conExtLaw.firstName + ' '+ conExtLaw.father + ' ' + conExtLaw.lastName ELSE conExtLaw.firstName+' '+conExtLaw.lastName END)
             FROM legal_cases legal_case_outsource
             LEFT JOIN legal_cases_contacts lccExtLaw ON lccExtLaw.case_id = legal_cases.id AND lccExtLaw.contactType = 'external lawyer'
             LEFT JOIN contacts conExtLaw ON conExtLaw.id = lccExtLaw.contact_id
             WHERE legal_case_outsource.id = legal_cases.id
            FOR XML PATH('')), 1, 1, ''),
		companyOutsourceTo = STUFF((SELECT ', ' + companiesExtLaw.name
             FROM legal_cases legal_case_outsource
             LEFT JOIN
                                             legal_cases_companies lccompaniesExtLaw
                                             ON
                                                       lccompaniesExtLaw.case_id         = legal_case_outsource.id
                                                       AND lccompaniesExtLaw.companyType = 'external lawyer'
                                   LEFT JOIN
                                             companies companiesExtLaw
                                             ON
                                                       companiesExtLaw.id = lccompaniesExtLaw.company_id
                         WHERE
                                   legal_case_outsource.id = legal_cases.id
            FOR XML PATH('')), 1, 1, '')
FROM legal_cases
LEFT JOIN workflow_status ON workflow_status.id = legal_cases.case_status_id
INNER JOIN case_types ON case_types.id = legal_cases.case_type_id
INNER JOIN provider_groups ON provider_groups.id = legal_cases.provider_group_id
LEFT JOIN user_profiles AS UP ON UP.user_id = legal_cases.user_id
LEFT JOIN legal_cases_contacts lccre ON lccre.case_id = legal_cases.id AND lccre.contactType = 'contact'
LEFT JOIN contacts conRE ON conRE.id = lccre.contact_id
LEFT JOIN legal_case_contact_roles lccr ON lccr.id = lccre.legal_case_contact_role_id
LEFT JOIN legal_case_effective_effort AS lcee ON lcee.legal_case_id = legal_cases.id
LEFT JOIN legal_case_litigation_details lcld ON lcld.legal_case_id = legal_cases.id AND lcld.id = legal_cases.stage
Where legal_cases.isDeleted = 0;
GO

--legal_cases_per_external_lawyer;
IF OBJECT_ID('dbo.legal_cases_per_external_lawyer', 'V') IS NOT NULL DROP VIEW dbo.legal_cases_per_external_lawyer;
GO
CREATE VIEW legal_cases_per_external_lawyer AS SELECT TOP(9223372036854775800) legal_cases.id, legal_cases.case_status_id, legal_cases.case_type_id, legal_cases.provider_group_id, legal_cases.user_id,
	legal_cases.contact_id, legal_cases.client_id, legal_cases.subject, legal_cases.description, legal_cases.priority, legal_cases.arrivalDate, legal_cases.dueDate, legal_cases.statusComments,
	legal_cases.category, legal_cases.caseValue, legal_cases.internalReference, legal_cases.externalizeLawyers, legal_cases.estimatedEffort,
	CAST(legal_cases.createdOn as DATE) createdOn, CAST(legal_cases.modifiedOn as DATE) as modifiedOn, legal_cases.createdBy, legal_cases.modifiedBy,
	legal_cases.archived, legal_cases.private, lcee.effectiveEffort, 'M' + CAST(legal_cases.id AS nvarchar) AS caseID, workflow_status.name AS status,
	case_types.name AS type, provider_groups.name AS providerGroup, UP.firstName + ' ' + UP.lastName AS assignee, legal_cases.archived AS archivedCases, legal_cases.isDeleted AS isDeleted,
	CASE WHEN conRE.father!='' THEN conRE.firstName + ' '+ conRE.father + ' ' + conRE.lastName ELSE conRE.firstName+' '+conRE.lastName END AS contact, lccr.name AS role, lccr.id AS role_id,
        lcld.sentenceDate,lcld.court_type_id,lcld.court_degree_id,lcld.court_region_id,lcld.court_id,
    contactContributor = STUFF((SELECT ', ' +
        (CASE WHEN conHE.father!='' THEN conHE.firstName + ' '+ conHE.father + ' ' + conHE.lastName ELSE conHE.firstName+' '+conHE.lastName END)
         FROM legal_cases legal_case_contributor
         LEFT JOIN legal_cases_contacts lccch ON lccch.case_id = legal_cases.id AND lccch.contactType = 'contributor'
        LEFT JOIN contacts conHE ON conHE.id = lccch.contact_id
        WHERE legal_case_contributor.id = legal_cases.id
        FOR XML PATH('')), 1, 1, ''),
    contactOutsourceTo = STUFF((SELECT ', ' +
        (CASE WHEN conExtLaw.father!='' THEN conExtLaw.firstName + ' '+ conExtLaw.father + ' ' + conExtLaw.lastName ELSE conExtLaw.firstName+' '+conExtLaw.lastName END)
         FROM legal_cases legal_case_outsource
         LEFT JOIN legal_cases_contacts lccExtLaw ON lccExtLaw.case_id = legal_cases.id AND lccExtLaw.contactType = 'external lawyer'
    LEFT JOIN contacts conExtLaw ON conExtLaw.id = lccExtLaw.contact_id
     WHERE legal_case_outsource.id = legal_cases.id
        FOR XML PATH('')), 1, 1, ''),
    companyOutsourceTo = STUFF((SELECT ', ' + companiesExtLaw.name
             FROM legal_cases legal_case_outsource
             LEFT JOIN
                                             legal_cases_companies lccompaniesExtLaw
                                             ON
                                                       lccompaniesExtLaw.case_id         = legal_case_outsource.id
                                                       AND lccompaniesExtLaw.companyType = 'external lawyer'
                                   LEFT JOIN
                                             companies companiesExtLaw
                                             ON
                                                       companiesExtLaw.id = lccompaniesExtLaw.company_id
                         WHERE
                                   legal_case_outsource.id = legal_cases.id
            FOR XML PATH('')), 1, 1, '')
FROM legal_cases
LEFT JOIN workflow_status ON workflow_status.id = legal_cases.case_status_id
INNER JOIN case_types ON case_types.id = legal_cases.case_type_id
INNER JOIN provider_groups ON provider_groups.id = legal_cases.provider_group_id
LEFT JOIN user_profiles AS UP ON UP.user_id = legal_cases.user_id
LEFT JOIN legal_cases_contacts lccre ON lccre.case_id = legal_cases.id AND lccre.contactType = 'external lawyer'
LEFT JOIN contacts conRE ON conRE.id = lccre.contact_id
LEFT JOIN legal_case_contact_roles lccr ON lccr.id = lccre.legal_case_contact_role_id
LEFT JOIN legal_case_effective_effort AS lcee ON lcee.legal_case_id = legal_cases.id
LEFT JOIN legal_case_litigation_details lcld ON lcld.legal_case_id = legal_cases.id AND lcld.id = legal_cases.stage
Where legal_cases.isDeleted = 0;
GO


--clients_view;
IF OBJECT_ID('dbo.clients_view', 'V') IS NOT NULL DROP VIEW dbo.clients_view;
GO
CREATE VIEW clients_view AS
 select clients.id AS id,
 CASE WHEN clients.company_id IS NOT NULL THEN com.name ELSE ( con.firstName + ' ' + con.lastName ) END AS name,
 CASE WHEN clients.company_id IS NOT NULL THEN com.foreignName ELSE ( isnull(con.foreignFirstName, '') + ' ' + isnull(con.foreignLastName, '') ) END AS foreignName,
 CASE WHEN clients.company_id IS NOT NULL THEN 'Company' ELSE 'Person' END AS type,
  CASE WHEN clients.company_id IS NOT NULL THEN clients.company_id ELSE clients.contact_id END AS member_id,
 'clients' AS model
 from clients
 left join companies com on com.id = clients.company_id
 left join contacts con on con.id = clients.contact_id;
 GO

--contact_nationalities_details;
IF OBJECT_ID('dbo.contact_nationalities_details', 'V') IS NOT NULL DROP VIEW dbo.contact_nationalities_details;
GO
CREATE VIEW contact_nationalities_details AS
 SELECT TOP(9223372036854775800)
	contact_nationalities.contact_id as contact_id,
	contact_nationalities.nationality_id as nationality_id,
	( CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END ) as contactName
 FROM contact_nationalities
 LEFT JOIN contacts ON contacts.id = contact_nationalities.contact_id;
GO


--legal_cases_grid;

IF OBJECT_ID('dbo.legal_cases_grid', 'V') IS NOT NULL DROP VIEW dbo.legal_cases_grid;
GO
CREATE VIEW legal_cases_grid AS SELECT TOP(9223372036854775800) legal_cases.id,CASE WHEN legal_cases.channel = 'CP' THEN 'yes' ELSE 'no' END AS isCP,legal_cases.channel, legal_cases.case_status_id, legal_cases.case_type_id, legal_cases.provider_group_id, legal_cases.user_id,
	legal_cases.contact_id, legal_cases.client_id, legal_cases.subject, legal_cases.description, legal_cases.latest_development, legal_cases.priority, legal_cases.arrivalDate,  legal_cases.caseArrivalDate, legal_cases.dueDate, legal_cases.closedOn, legal_cases.statusComments,
	legal_cases.category, legal_cases.caseValue, legal_cases.recoveredValue, legal_cases.judgmentValue, legal_cases.internalReference, legal_cases.externalizeLawyers, legal_cases.estimatedEffort,
	CAST(legal_cases.createdOn as DATE) as createdOn, CAST(legal_cases.modifiedOn as DATE) as modifiedOn, legal_cases.createdBy, legal_cases.modifiedBy,
	legal_cases.archived, legal_cases.private,legal_cases.timeTrackingBillable,legal_cases.expensesBillable, lcee.effectiveEffort, 'M' + CAST(legal_cases.id AS nvarchar) AS caseID, workflow_status.name as status, case_types.name as type,
	provider_groups.name as providerGroup, UP.firstName + ' ' + UP.lastName AS assignee, legal_cases.archived as archivedCases, legal_case_litigation_details.id AS litigation_details_id, legal_case_litigation_details.court_type_id AS court_type_id,
	legal_case_litigation_details.court_degree_id AS court_degree_id, legal_case_litigation_details.court_region_id AS court_region_id, legal_case_litigation_details.court_id AS court_id,
	legal_case_litigation_details.comments AS comments, legal_case_litigation_details.sentenceDate AS sentenceDate,
	com.name AS company, com.id AS company_id, (CASE WHEN conRE.father!='' THEN conRE.firstName + ' '+ conRE.father + ' ' + conRE.lastName ELSE conRE.firstName+' '+conRE.lastName END) AS contact, (CASE WHEN conHE.father!='' THEN conHE.firstName + ' '+ conHE.father + ' ' + conHE.lastName ELSE conHE.firstName+' '+conHE.lastName END) AS contactContributor, (CASE WHEN conExtLaw.father!='' THEN conExtLaw.firstName + ' '+ conExtLaw.father + ' ' + conExtLaw.lastName ELSE conExtLaw.firstName+' '+conExtLaw.lastName END) AS contactOutsourceTo, compiesExtLaw.name AS companyOutsourceTo,
    legal_case_litigation_external_references.number AS litigationExternalRef, clients_view.name AS clientName, clients_view.type AS clientType, legal_cases.referredBy, legal_cases.requestedBy,
    ( CASE WHEN referredByContact.father!='' THEN referredByContact.firstName + ' '+ referredByContact.father + ' ' + referredByContact.lastName ELSE referredByContact.firstName+' '+referredByContact.lastName END ) AS referredByName, ( CASE WHEN requestedByContact.father!='' THEN requestedByContact.firstName + ' '+ requestedByContact.father + ' ' + requestedByContact.lastName ELSE requestedByContact.firstName+' '+requestedByContact.lastName END ) AS requestedByName,
	legal_case_containers.subject AS legalCaseContainerSubject, legal_cases.legal_case_stage_id as legal_case_stage_id,
	opponentNames = STUFF((SELECT ', ' +
		(CASE WHEN legal_case_opponents.opponent_member_type = 'company'
		THEN opponentCompany.name
		ELSE (CASE WHEN opponentContact.father!='' THEN opponentContact.firstName + ' '+ opponentContact.father + ' ' + opponentContact.lastName ELSE opponentContact.firstName+' '+opponentContact.lastName END) END )
		 FROM legal_case_opponents
		 INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id
		 LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company'
		 LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact'
		 WHERE legal_case_opponents.case_id = legal_cases.id
		FOR XML PATH('')), 1, 1, ''),
	legal_cases.legal_case_client_position_id as legal_case_client_position_id ,
        legal_cases.legal_case_success_probability_id as legal_case_success_probability_id
FROM legal_cases
LEFT JOIN legal_cases_companies lcccom ON lcccom.case_id = legal_cases.id
LEFT JOIN companies com ON lcccom.company_id = com.id
LEFT JOIN legal_cases_contacts lccre ON lccre.case_id = legal_cases.id AND lccre.contactType = 'contact'
LEFT JOIN contacts conRE ON conRE.id = lccre.contact_id
LEFT JOIN legal_cases_contacts lccch ON lccch.case_id = legal_cases.id AND lccch.contactType = 'contributor'
LEFT JOIN contacts conHE ON conHE.id = lccch.contact_id
LEFT JOIN legal_cases_contacts lccExtLaw ON lccExtLaw.case_id = legal_cases.id AND lccExtLaw.contactType = 'external lawyer'
LEFT JOIN contacts conExtLaw ON conExtLaw.id = lccExtLaw.contact_id
LEFT JOIN legal_cases_companies lccompaniesExtLaw ON lccompaniesExtLaw.case_id = legal_cases.id AND lccompaniesExtLaw.companyType = 'external lawyer'
LEFT JOIN companies compiesExtLaw ON compiesExtLaw.id = lccompaniesExtLaw.company_id
LEFT JOIN workflow_status ON workflow_status.id = legal_cases.case_status_id
INNER JOIN case_types ON case_types.id = legal_cases.case_type_id
INNER JOIN provider_groups ON provider_groups.id = legal_cases.provider_group_id
LEFT JOIN user_profiles as UP ON UP.user_id = legal_cases.user_id
LEFT JOIN legal_case_effective_effort AS lcee ON lcee.legal_case_id = legal_cases.id
LEFT JOIN legal_case_litigation_details ON legal_case_litigation_details.id = legal_cases.stage
LEFT JOIN legal_case_litigation_external_references ON legal_case_litigation_external_references.stage = legal_cases.stage
LEFT JOIN clients_view ON clients_view.id = legal_cases.client_id AND clients_view.model = 'clients'
LEFT JOIN contacts as referredByContact ON referredByContact.id = legal_cases.referredBy
LEFT JOIN contacts as requestedByContact ON requestedByContact.id = legal_cases.requestedBy
LEFT JOIN legal_case_related_containers ON legal_case_related_containers.legal_case_id = legal_cases.id
LEFT JOIN legal_case_containers ON legal_case_containers.id = legal_case_related_containers.legal_case_container_id
Where legal_cases.isDeleted = 0;
GO

IF OBJECT_ID('dbo.intellectual_properties_grid', 'V') IS NOT NULL DROP VIEW dbo.intellectual_properties_grid;
GO
CREATE VIEW intellectual_properties_grid AS
select lc.id, lc.channel, lc.provider_group_id, pg.name as providerGroup, lc.user_id as legalCaseAssigneeId,
       (up.firstName + ' ' + up.lastName) as legalCaseAssignee, up.status as legalCaseAssigneeStatus, lc.client_id, cliv.name as client, cliv.type as clientType,
       lc.subject, lc.description, lc.arrivalDate, lc.statusComments, lc.category,
       lc.createdOn, lc.createdBy, lc.modifiedOn, lc.modifiedBy, lc.modifiedByChannel, lc.timeTrackingBillable, lc.expensesBillable, lc.isDeleted,
       ipd.intellectual_property_right_id, ipri.name as intellectualPropertyRight, ipd.ip_class_id, ipcl.name as ipClass, ipd.ip_subcategory_id,
       ipsc.name as ipSubcategory, ipd.ip_status_id, ipst.name as ipStatus, ipst.category as ipStatusCategory, ipd.ip_name_id, ipna.name as ipName, ipd.filingNumber, ipd.acceptanceRejection,
       ipd.certificationNumber, ipd.registrationDate, ipd.registrationReference, ipd.agentId, ipd.agentType,
       case when ipd.agentType = 'Company' then ipcomp.name else (CASE WHEN ipcont.father!='' THEN ipcont.firstName + ' '+ ipcont.father + ' ' + ipcont.lastName ELSE ipcont.firstName+' '+ipcont.lastName END) end as agent, ipd.country_id,
       (SELECT TOP 1 lccr.expiryDate from legal_cases_countries_renewals lccr where lccr.intellectual_property_id = lc.id ORDER BY lccr.renewalDate DESC ) as renewalExpiryDate,
       (SELECT MAX(renewalDate) from legal_cases_countries_renewals lccr where lccr.intellectual_property_id = lc.id) as renewalDate,
       (modified.firstName + ' ' + modified.lastName) AS modifiedByName, (created.firstName + ' ' + created.lastName) AS createdByName,
        renewalUsersToRemind = STUFF((SELECT '; ' + (lccrup.firstName + ' ' + lccrup.lastName) FROM legal_cases_countries_renewals_users lccru LEFT JOIN user_profiles lccrup ON lccrup.user_id = lccru.user_id left join legal_cases_countries_renewals lccr on lccr.intellectual_property_id = lc.id WHERE lccru.legal_case_country_renewal_id = lccr.id FOR XML PATH('')), 1, 1, '')
from legal_cases lc
         left join provider_groups pg on pg.id = lc.provider_group_id
         left join user_profiles up on up.user_id = lc.user_id
         left join clients_view cliv on cliv.id = lc.client_id and cliv.model = 'clients'
         left join ip_details ipd on ipd.legal_case_id = lc.id
         left join intellectual_property_rights ipri on ipri.id = ipd.intellectual_property_right_id
         left join ip_classes ipcl on ipcl.id = ipd.ip_class_id
         left join ip_subcategories ipsc on ipsc.id = ipd.ip_subcategory_id
         left join ip_statuses ipst on ipst.id = ipd.ip_status_id
         left join ip_names ipna on ipna.id = ipd.ip_name_id
         left join companies ipcomp on ipcomp.id = ipd.agentId
         left join contacts ipcont on ipcont.id = ipd.agentId
         LEFT JOIN user_profiles created ON created.user_id = lc.createdBy
         LEFT JOIN user_profiles modified ON modified.user_id = lc.modifiedBy
where lc.category = 'IP' and lc.isDeleted = 0
Go

--reminders_full_details;
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

--legal_case_containers_full_details;

IF OBJECT_ID('dbo.legal_case_containers_full_details', 'V') IS NOT NULL DROP VIEW dbo.legal_case_containers_full_details;
GO
CREATE VIEW legal_case_containers_full_details AS
SELECT legal_case_containers.id AS id,
legal_case_containers.case_type_id,
legal_case_containers.provider_group_id, legal_case_containers.user_id, legal_case_containers.visible_in_cp,
legal_case_containers.caseArrivalDate, legal_case_containers.closedOn AS containerClosedOn, legal_case_containers.comments AS containerComments, legal_case_containers.internalReference,
legal_cases.subject AS caseSubject,
('M' + CAST(legal_cases.id as nvarchar))  as legalCaseId,
( 'MC' + CAST( legal_case_containers.id AS nvarchar ) ) AS containerId,
legal_case_containers.subject AS containerSubject, legal_case_containers.description AS containerDescription,
legal_case_containers.legal_case_container_status_id AS legal_case_container_status_id,
legal_case_container_statuses.name AS containerStatus,
case_types.name as type,provider_groups.name as providerGroup,
 ( UP.firstName +  ' ' + UP.lastName ) as assignee, legal_case_containers.client_id,
 clients_view.name AS clientName, clients_view.foreignName AS client_foreign_name, clients_view.type AS clientType,
 ( CASE WHEN requested_by_contact.father!='' THEN requested_by_contact.firstName + ' '+ requested_by_contact.father + ' ' + requested_by_contact.lastName ELSE requested_by_contact.firstName+' '+requested_by_contact.lastName END ) AS requested_by_name,
opponentNames = STUFF((SELECT ', ' +
    (CASE WHEN legal_case_container_opponents.opponent_member_type = 'company'
    THEN opponentCompany.name
    ELSE (CASE WHEN opponentContact.father!='' THEN opponentContact.firstName + ' '+ opponentContact.father + ' ' + opponentContact.lastName ELSE opponentContact.firstName+' '+opponentContact.lastName END) END )
     FROM legal_case_container_opponents
     INNER JOIN opponents ON opponents.id = legal_case_container_opponents.opponent_id
     LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_container_opponents.opponent_member_type = 'company'
     LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_container_opponents.opponent_member_type = 'contact'
     WHERE legal_case_container_opponents.case_container_id = legal_case_containers.id
    FOR XML PATH('')), 1, 1, ''),
opponent_foreign_name = STUFF((SELECT ', ' +
    (
        CASE WHEN legal_case_container_opponents.opponent_member_type = 'company'
        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END)
        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) END
    )
     FROM legal_case_container_opponents
     INNER JOIN opponents ON opponents.id = legal_case_container_opponents.opponent_id
     LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_container_opponents.opponent_member_type = 'company'
     LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_container_opponents.opponent_member_type = 'contact'
     WHERE legal_case_container_opponents.case_container_id = legal_case_containers.id
    FOR XML PATH('')), 1, 1, ''),
 legal_case_containers.legal_case_client_position_id as legal_case_client_position_id,
( created.firstName + ' ' + created.lastName ) AS createdBy, legal_cases.id as legal_case_id,
( modified.firstName + ' ' + modified.lastName ) AS modifiedBy, CAST(legal_case_containers.createdOn as DATE) as createdOn, CAST(legal_case_containers.modifiedOn as DATE) as modifiedOn
FROM legal_case_containers
LEFT JOIN legal_case_related_containers ON legal_case_related_containers.legal_case_container_id = legal_case_containers.id
LEFT JOIN legal_cases ON legal_cases.id = legal_case_related_containers.legal_case_id
LEFT JOIN user_profiles created ON created.user_id = legal_case_containers.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = legal_case_containers.modifiedBy
LEFT JOIN legal_case_container_statuses ON legal_case_container_statuses.id = legal_case_containers.legal_case_container_status_id
INNER JOIN case_types ON case_types.id = legal_case_containers.case_type_id 
INNER JOIN provider_groups ON provider_groups.id = legal_case_containers.provider_group_id 
LEFT JOIN user_profiles as UP ON UP.user_id = legal_case_containers.user_id 
LEFT JOIN clients_view ON clients_view.id = legal_case_containers.client_id AND clients_view.model = 'clients'
LEFT JOIN contacts as requested_by_contact ON requested_by_contact.id = legal_case_containers.requested_by;
GO

IF OBJECT_ID('dbo.companies_full_details', 'V') IS NOT NULL DROP VIEW dbo.companies_full_details;
GO
--companies_full_details;
CREATE VIEW companies_full_details AS SELECT
    TOP(9223372036854775800)
    companies.id, ('COM' + CAST( companies.id AS nvarchar )) AS companyID, companies.legalName, companies.name, companies.shortName,
    companies.foreignName, companies.status, companies.category, companies.company_category_id, contact_company_categories.name AS company_category,
    contact_company_categories.keyName  AS company_category_keyName,
    companies.company_sub_category_id, contact_company_sub_categories.name AS company_sub_category, companies.private,
    companies.company_id, companies.nationality_id, companies.company_legal_type_id,
    companies.object, companies.capital, companies.capitalCurrency, companies.nominalShares,
    companies.bearerShares, companies.shareParValue, companies.shareParValueCurrency,
    companies.qualifyingShares, companies.registrationNb, companies.registrationDate,
    companies.registrationCity, companies.registrationTaxNb, companies.registrationYearsNb,
    companies.registrationByLawNotaryPublic, companies.registrationByLawRef, companies.registrationByLawDate,
    companies.registrationByLawCity, companies.sharesLocation,
    companies.ownedByGroup, companies.sheerLebanese, companies.contributionRatio, companies.notes,
    companies.otherNotes, companies.createdOn, companies.createdBy, companies.modifiedOn, companies.modifiedBy,
    (modified.firstName + ' ' + modified.lastName) AS modifiedByName, (created.firstName + ' ' + created.lastName) AS createdByName,
    clt.name as legalType, cp.name as majorParent, lawyer=STUFF( (SELECT '; '+ (CASE WHEN cont.father!='' THEN cont.firstName + ' '+ cont.father + ' ' + cont.lastName ELSE cont.firstName+' '+cont.lastName END) from company_lawyers LEFT JOIN contacts cont ON cont.id = company_lawyers.lawyer_id where company_lawyers.company_id=companies.id FOR XML PATH('')), 1, 1, '') ,
    companyRegistrationAuthority.name as registrationAuthorityName, companies.internalReference, companies.crReleasedOn, companies.crExpiresOn,company_addresses.email
    ,companies.additional_id_type, companies.additional_id_value 
    FROM companies
        LEFT JOIN company_legal_types clt ON clt.id = companies.company_legal_type_id
        LEFT JOIN companies cp ON companies.company_id = cp.id
        LEFT JOIN contact_company_categories ON contact_company_categories.id = companies.company_category_id
        LEFT JOIN contact_company_sub_categories ON contact_company_sub_categories.id = companies.company_sub_category_id
        LEFT JOIN companies companyRegistrationAuthority ON companyRegistrationAuthority.id = companies.registrationAuthority AND companyRegistrationAuthority.category = 'Internal'
        LEFT JOIN company_addresses ON company_addresses.company = companies.id AND company_addresses.email IS NOT NULL
        LEFT JOIN user_profiles created ON created.user_id = companies.createdBy
        LEFT JOIN user_profiles modified ON modified.user_id = companies.modifiedBy;
GO

IF OBJECT_ID('dbo.user_changes_full_details', 'V') IS NOT NULL DROP VIEW dbo.user_changes_full_details;
GO
--user_changes_full_details;
CREATE VIEW user_changes_full_details AS SELECT
	TOP(9223372036854775800)
	user_changes.id,
	user_changes.user_id,
	user_changes.action,
	user_changes.fieldName,
	CAST(user_changes.modifiedOn AS DATE) AS modifiedOn,
	user_changes.modifiedBy,
	( userProfile.firstName + ' ' + userProfile.lastName ) AS userFullName,
	( modified.firstName + ' ' + modified.lastName ) AS modifiedFullName,
	CASE WHEN user_changes.fieldName = 'country' OR user_changes.fieldName = 'nationality'
	THEN (select name beforeData from countries_languages where countries_languages.id = CAST( user_changes.beforeData AS nvarchar ) AND countries_languages.language_id = 1)
	ELSE (CASE WHEN user_changes.fieldName = 'user_group_id' THEN (select user_groups.name beforeData from user_groups where user_groups.id = CAST( user_changes.beforeData AS nvarchar ) ) ELSE ( CASE WHEN user_changes.fieldName = 'seniority_level_id' THEN (select seniority_levels.name beforeData from seniority_levels where seniority_levels.id = CAST( user_changes.beforeData AS nvarchar ) ) ELSE user_changes.beforeData END) END) END as beforeData,
	CASE  WHEN user_changes.fieldName = 'country' OR user_changes.fieldName = 'nationality'  THEN (select name afterData from countries_languages where countries_languages.id = CAST( user_changes.afterData AS nvarchar ) AND countries_languages.language_id = 1)
	ELSE (CASE WHEN user_changes.fieldName = 'user_group_id' THEN (select user_groups.name afterData from user_groups where user_groups.id = CAST( user_changes.afterData AS nvarchar ))ELSE (CASE WHEN user_changes.fieldName = 'seniority_level_id' THEN (select seniority_levels.name afterData from seniority_levels where seniority_levels.id = CAST( user_changes.afterData AS nvarchar ))ELSE user_changes.afterData END	)END)
	END as afterData
	FROM user_changes
	INNER JOIN user_profiles userProfile ON userProfile.user_id = user_changes.user_id
	INNER JOIN user_profiles modified ON modified.user_id = user_changes.modifiedBy;
GO

--opponents_view;
IF OBJECT_ID('dbo.opponents_view', 'V') IS NOT NULL DROP VIEW dbo.opponents_view;
GO
CREATE VIEW opponents_view AS
 select opponents.id AS id,
 CASE WHEN opponents.company_id IS NOT NULL THEN com.name ELSE ( CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) END AS name,
 CASE WHEN opponents.company_id IS NOT NULL THEN 'Company' ELSE 'Person' END AS type,
  CASE WHEN opponents.company_id IS NOT NULL THEN opponents.company_id ELSE opponents.contact_id END AS member_id,
 'opponents' AS model
 from opponents
 left join companies com on com.id = opponents.company_id
 left join contacts con on con.id = opponents.contact_id;
 GO

IF OBJECT_ID('dbo.legal_case_hearings_full_details', 'V') IS NOT NULL DROP VIEW dbo.legal_case_hearings_full_details;
GO
--legal_case_hearings_full_details;
CREATE VIEW legal_case_hearings_full_details AS SELECT
    TOP (9223372036854775800)
    legal_case_hearings.id,legal_case_hearings.legal_case_id, ('H'+CAST( legal_case_hearings.id AS nvarchar )) as hearingID, legal_cases.subject as caseSubject, legal_cases.internalReference AS caseReference, legal_case_hearings.task_id,legal_cases.caseValue,legal_cases.statusComments,legal_cases.latest_development,legal_cases.caseArrivalDate,legal_cases.closedOn, legal_cases.description as case_description, legal_cases.arrivalDate as filed_on,
    legal_cases.legal_case_client_position_id AS hearing_client_position_id,ld.legal_case_stage, ld.court_type_id, ld.court_degree_id, ld.court_region_id, ld.court_id,
    legal_case_hearings.startDate, SUBSTRING(CAST(legal_case_hearings.startTime AS nvarchar), 1, 5) AS startTime, legal_case_hearings.postponedDate, SUBSTRING(CAST(legal_case_hearings.postponedTime AS nvarchar), 1, 5) AS postponedTime,
    legal_case_hearings.type,ld.status as stage_status,legal_case_hearings.summary,legal_case_hearings.comments, legal_case_hearings.reasons_of_postponement,
    reference = STUFF((SELECT ' ; ' + lcler.number FROM legal_case_litigation_external_references lcler WHERE lcler.stage=ld.id FOR XML PATH('')), 1, 3, ''),
    reference_date = STUFF((SELECT ' ; ' + CAST(lcler.refDate as nvarchar) FROM legal_case_litigation_external_references lcler WHERE lcler.stage=ld.id FOR XML PATH('')), 1, 3, ''),
    legal_case_hearings.judgment, legal_case_hearings.judged, ('M'+CAST( legal_case_hearings.legal_case_id AS nvarchar )) as caseID,
    stage_opponents = STUFF((SELECT ' ; ' + stage_opponents_view.name FROM opponents_view as stage_opponents_view INNER JOIN legal_case_litigation_stages_opponents stages_opponents ON stages_opponents.stage = ld.id AND stage_opponents_view.id = stages_opponents.opponent_id FOR XML PATH('')), 1, 3, ''),
    opponents = STUFF((SELECT ' ; ' + opponents_view.name FROM opponents_view INNER JOIN legal_case_opponents lcho ON lcho.case_id = legal_cases.id AND opponents_view.id = lcho.opponent_id FOR XML PATH('')), 1, 3, ''),
    clients = STUFF((SELECT ' ; ' + clients_view.name FROM clients_view WHERE clients_view.id = legal_cases.client_id AND clients_view.model = 'clients' FOR XML PATH('')), 1, 3, ''),
    judges = STUFF((SELECT ' ; ' + ( CASE WHEN contJud.father!='' THEN contJud.firstName + ' '+ contJud.father + ' ' + contJud.lastName ELSE contJud.firstName+' '+contJud.lastName END ) FROM contacts AS contJud INNER JOIN legal_case_stage_contacts lchcj ON lchcj.stage = legal_case_hearings.stage AND lchcj.contact_type = 'judge' AND contJud.id = lchcj.contact FOR XML PATH('')), 1, 3, ''),
    opponentLawyers = STUFF((SELECT ' ; ' + ( CASE WHEN contOppLaw.father!='' THEN contOppLaw.firstName + ' '+ contOppLaw.father + ' ' + contOppLaw.lastName ELSE contOppLaw.firstName+' '+contOppLaw.lastName END ) FROM contacts AS contOppLaw INNER JOIN legal_case_stage_contacts lchcol ON lchcol.stage = legal_case_hearings.stage AND lchcol.contact_type = 'opponent-lawyer' AND contOppLaw.id = lchcol.contact FOR XML PATH('')), 1, 3, ''),
    lawyers =
    COALESCE (STUFF((SELECT' ; ' + (userLaw.firstName + ' ' + userLaw.lastName + CASE WHEN userLaw.status = 'Inactive' THEN ' (Inactive)'ELSE '' END) FROM user_profiles AS userLaw INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userLaw.user_id = legal_case_hearings_users.user_id AND legal_case_hearings_users.user_type != 'AP' FOR xml PATH ('')), 1, 3, '') ,'')
    + CASE WHEN (COALESCE (STUFF((SELECT ' ; ' + (userLaw.firstName + ' ' + userLaw.lastName + CASE  WHEN userLaw.status = 'Inactive' THEN ' (Inactive)'  ELSE '' END) FROM user_profiles AS userLaw INNER JOIN legal_case_hearings_users   ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userLaw.user_id = legal_case_hearings_users.user_id  AND legal_case_hearings_users.user_type != 'AP'  FOR xml PATH ('')), 1, 3, '') ,'') = '' or
                 COALESCE(STUFF((SELECT  ' ; ' + (userAdv.firstName + ' ' + userAdv.lastName + CASE WHEN userAdv.status = 'Inactive' THEN ' (Inactive)'ELSE ''  END) FROM advisor_users AS userAdv INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userAdv.id = legal_case_hearings_users.user_id AND legal_case_hearings_users.user_type = 'AP' FOR xml PATH ('')), 1, 3, '') ,'') = '' ) THEN
     '' ELSE ','  END +
    COALESCE(STUFF((SELECT  ' ; ' + (userAdv.firstName + ' ' + userAdv.lastName + CASE WHEN userAdv.status = 'Inactive' THEN ' (Inactive)'ELSE ''  END) FROM advisor_users AS userAdv INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userAdv.id = legal_case_hearings_users.user_id AND legal_case_hearings_users.user_type = 'AP' FOR xml PATH ('')), 1, 3, '') ,'')  ,
    case_assignee = STUFF((SELECT ' ; ' + ( userLegalCase.firstName + ' ' + userLegalCase.lastName+ CASE WHEN userLegalCase.status = 'Inactive' THEN ' (Inactive)' ELSE '' END ) FROM user_profiles AS userLegalCase INNER JOIN legal_cases ON legal_cases.id = legal_case_hearings.legal_case_id AND userLegalCase.user_id = legal_cases.user_id FOR XML PATH('')), 1, 3, ''),
    court_types.name AS courtType, court_degrees.name AS courtDegree, court_regions.name AS courtRegion, courts.name AS court,
    lccplen.name AS clientPosition_en, lccplfr.name AS clientPosition_fr, lccplar.name AS clientPosition_ar, lccplsp.name AS clientPosition_sp, ld.sentenceDate,
    legal_cases.case_type_id as matter_type_id, legal_case_hearings.stage as stage, legal_case_stage_languages_default.name as legal_case_stage_name, legal_case_stage_languages_en.name as legal_case_stage_name_en, legal_case_stage_languages_ar.name as legal_case_stage_name_ar, legal_case_stage_languages_fr.name as legal_case_stage_name_fr, legal_case_stage_languages_es.name as legal_case_stage_name_es, case_types.name as areaOfPractice,legal_cases.case_type_id as area_of_practice,
    containerID = STUFF((SELECT ' ; ' + lccfd.containerId FROM legal_case_containers_full_details as lccfd where lccfd.legal_case_id = legal_case_hearings.legal_case_id FOR XML PATH('')), 1, 3, ''),
    legal_case_hearings.createdOn, legal_case_hearings.createdBy, legal_case_hearings.modifiedBy, legal_case_hearings.modifiedOn,
    (modified.firstName + ' ' + modified.lastName) AS modifiedByName, (created.firstName + ' ' + created.lastName) AS createdByName
    FROM legal_case_hearings
             LEFT JOIN legal_case_litigation_details as ld on ld.legal_case_id = legal_case_hearings.legal_case_id AND  ld.id = legal_case_hearings.stage
             LEFT JOIN legal_case_stages on legal_case_stages.id = ld.legal_case_stage
             LEFT JOIN legal_case_stage_languages legal_case_stage_languages_default on legal_case_stage_languages_default.legal_case_stage_id = legal_case_stages.id and legal_case_stage_languages_default.language_id = 1
             LEFT JOIN legal_case_stage_languages legal_case_stage_languages_en on legal_case_stage_languages_en.legal_case_stage_id = legal_case_stages.id and legal_case_stage_languages_en.language_id = 1
             LEFT JOIN legal_case_stage_languages legal_case_stage_languages_ar on legal_case_stage_languages_ar.legal_case_stage_id = legal_case_stages.id and legal_case_stage_languages_ar.language_id = 2
             LEFT JOIN legal_case_stage_languages legal_case_stage_languages_fr on legal_case_stage_languages_fr.legal_case_stage_id = legal_case_stages.id and legal_case_stage_languages_fr.language_id = 3
             LEFT JOIN legal_case_stage_languages legal_case_stage_languages_es on legal_case_stage_languages_es.legal_case_stage_id = legal_case_stages.id and legal_case_stage_languages_es.language_id = 4
             LEFT JOIN legal_cases ON legal_cases.id = legal_case_hearings.legal_case_id
             LEFT JOIN courts ON courts.id = ld.court_id
             LEFT JOIN court_types ON court_types.id = ld.court_type_id
             LEFT JOIN court_degrees ON court_degrees.id = ld.court_degree_id
             LEFT JOIN court_regions ON court_regions.id = ld.court_region_id
             LEFT JOIN legal_case_client_position_languages lccplen ON lccplen.legal_case_client_position_id = legal_cases.legal_case_client_position_id AND lccplen.language_id = '1'
             LEFT JOIN legal_case_client_position_languages lccplar ON lccplar.legal_case_client_position_id = legal_cases.legal_case_client_position_id AND lccplar.language_id = '2'
             LEFT JOIN legal_case_client_position_languages lccplfr ON lccplfr.legal_case_client_position_id = legal_cases.legal_case_client_position_id AND lccplfr.language_id = '3'
             LEFT JOIN legal_case_client_position_languages lccplsp ON lccplsp.legal_case_client_position_id = legal_cases.legal_case_client_position_id AND lccplsp.language_id = '4'
             LEFT JOIN case_types ON case_types.id = legal_cases.case_type_id
             LEFT JOIN user_profiles created ON created.user_id = legal_case_hearings.createdBy
             LEFT JOIN user_profiles modified ON modified.user_id = legal_case_hearings.modifiedBy
    Where legal_cases.isDeleted = 0 AND legal_case_hearings.is_deleted = 0;
GO

--users_authorizations;
IF OBJECT_ID('dbo.users_authorizations', 'V') IS NOT NULL DROP VIEW dbo.users_authorizations;
GO
CREATE VIEW users_authorizations AS SELECT max(affectedUserId) AS affectedUserId, max(checkerId) AS checkerId from user_changes_authorization where user_changes_authorization.changeType = 'add' AND user_changes_authorization.columnStatus = 'Approved' group by affectedUserId
GO

--users_full_details;

IF OBJECT_ID('dbo.users_full_details', 'V') IS NOT NULL DROP VIEW dbo.users_full_details;
GO
CREATE VIEW users_full_details AS SELECT TOP(9223372036854775800) users.id,users.isAd, users.user_group_id, users.username, users.email, users.type, LEFT(users.email, charindex('@', users.email)-1) as activeDirectoryId,( AutthorizedUser.firstName + ' ' + AutthorizedUser.lastName ) AS authorized_by, users.banned, users.ban_reason, users.last_ip, CAST(users.last_login AS DATE) AS last_login, CAST(users.created AS DATE) AS created, users.modifiedBy, users.userDirectory, ( userModified.firstName + ' ' + userModified.lastName ) AS userModifiedName, CAST(users.modified AS DATE) AS modified, user_profiles.flagChangePassword AS flagChangePassword, user_profiles.status, user_profiles.gender, user_profiles.title, user_profiles.firstName, user_profiles.lastName, user_profiles.father, user_profiles.mother, user_profiles.dateOfBirth, user_profiles.jobTitle, user_profiles.isLawyer, user_profiles.website, user_profiles.phone, user_profiles.fax, user_profiles.mobile, user_profiles.address1, user_profiles.address2, user_profiles.city, user_profiles.state, user_profiles.zip, user_profiles.overridePrivacy,user_profiles.employeeId,user_profiles.department,user_profiles.ad_userCode, user_profiles.user_code, seniorityLevels.name  as seniorityLevel,seniorityLevels.id as seniorityLevelId,user_profiles.country AS country_id, user_profiles.nationality AS nationality_id, user_groups.name as userGroupName, user_groups.description as userGroupDescription, providerGroup=STUFF( (SELECT ', '+ provider_groups.name FROM provider_groups_users INNER JOIN provider_groups ON provider_groups.id = provider_groups_users.provider_group_id WHERE provider_groups_users.user_id = users.id FOR XML PATH('')), 1, 2, ''), provider_group_id=STUFF( (SELECT ', ' + CAST( provider_groups.id AS nvarchar ) FROM provider_groups_users INNER JOIN provider_groups ON provider_groups.id = provider_groups_users.provider_group_id WHERE provider_groups_users.user_id = users.id FOR XML PATH('')), 1, 2, ''), user_profiles.flagNeedApproval AS flagNeedApproval FROM users INNER JOIN user_profiles ON user_profiles.user_id = users.id  LEFT JOIN seniority_levels seniorityLevels ON seniorityLevels.id = user_profiles.seniority_level_id LEFT JOIN user_groups ON user_groups.id = users.user_group_id LEFT JOIN user_profiles userModified ON userModified.user_id = users.modifiedBy LEFT JOIN users_authorizations ON users_authorizations.affectedUserId = users.id  LEFT JOIN user_profiles AutthorizedUser ON AutthorizedUser.user_id = users_authorizations.checkerId
GO

--login_logs_full_details
IF OBJECT_ID('dbo.login_logs_full_details', 'V') IS NOT NULL DROP VIEW dbo.login_logs_full_details;
GO
CREATE VIEW login_logs_full_details AS
	SELECT TOP(9223372036854775800)
		login_history_logs.id AS id,
		login_history_logs.user_id AS user_id,
		login_history_logs.userLogin AS userLogin,
		users_full_details.userGroupName AS userGroupName,
		users_full_details.user_group_id AS user_group_id,
		login_history_logs.action AS action,
		login_history_logs.source_ip AS source_ip,
		login_history_logs.log_message AS log_message,
		login_history_logs.log_message_status AS log_message_status,
		CAST(login_history_logs.logDate AS DATE) AS logDate,
		CONVERT(varchar(20), login_history_logs.logDate, 120) AS fullLogDate,
		CASE WHEN login_history_logs.log_message_status = 'log_msg_status_2' THEN 'failed' ELSE 'successful' END AS status,
		login_history_logs.user_agent
	FROM login_history_logs
	LEFT JOIN users_full_details ON users_full_details.id = login_history_logs.user_id;
GO

IF OBJECT_ID('dbo.maker_checker_user_changes', 'V') IS NOT NULL DROP VIEW dbo.maker_checker_user_changes;
GO
CREATE VIEW maker_checker_user_changes AS SELECT TOP(9223372036854775800) UCA.id, UCA.changeType, UCA.columnName, CASE WHEN UCA.columnName = 'banned' THEN (CASE WHEN UCA.columnValue = '0' THEN 'no' ELSE 'yes' END) ELSE UCA.columnValue END AS columnValue, UCA.columnStatus, CASE WHEN UCA.columnName = 'banned' THEN (CASE WHEN UCA.columnRequestedValue = '0' THEN 'no' ELSE 'yes' END) ELSE UCA.columnRequestedValue END AS columnRequestedValue, UCA.columnType, CAST(UCA.createdOn AS DATE) AS createdOn, CAST(UCA.authorizedOn AS DATE) AS authorizedOn, UCA.affectedUserId, ( affectedUser.firstName + ' ' + affectedUser.lastName ) AS affectedUserProfile, UCA.makerId, ( makerUser.firstName + ' ' + makerUser.lastName ) AS makerUserProfile, UCA.checkerId, ( checkerUser.firstName + ' ' + checkerUser.lastName ) AS checkerUserProfile FROM user_changes_authorization as UCA LEFT JOIN user_profiles affectedUser ON affectedUser.user_id = UCA.affectedUserId LEFT JOIN user_profiles makerUser ON makerUser.user_id = UCA.makerId LEFT JOIN user_profiles checkerUser ON checkerUser.user_id = UCA.checkerId
GO

IF OBJECT_ID('dbo.maker_checker_user_groups_changes', 'V') IS NOT NULL DROP VIEW dbo.maker_checker_user_groups_changes;
GO
CREATE VIEW maker_checker_user_groups_changes AS SELECT TOP(9223372036854775800) UGCA.id, UGCA.changeType, UGCA.columnName, UGCA.columnValue, UGCA.columnStatus, columnRequestedValue, UGCA.columnType, CAST(UGCA.createdOn AS DATE) AS createdOn, CAST(UGCA.authorizedOn AS DATE) AS authorizedOn, UGCA.affectedUserGroupId, affectedUserGroup.name AS affectedUserGroupName, UGCA.makerId, ( makerUser.firstName + ' ' + makerUser.lastName ) AS makerUserProfile, UGCA.checkerId, ( checkerUser.firstName + ' ' + checkerUser.lastName ) AS checkerUserProfile FROM user_groups_changes_authorization as UGCA LEFT JOIN user_groups affectedUserGroup ON affectedUserGroup.id = UGCA.affectedUserGroupId LEFT JOIN user_profiles makerUser ON makerUser.user_id = UGCA.makerId LEFT JOIN user_profiles checkerUser ON checkerUser.user_id = UGCA.checkerId
GO

IF OBJECT_ID('dbo.maker_checker_user_group_permissions_changes', 'V') IS NOT NULL DROP VIEW dbo.maker_checker_user_group_permissions_changes;
GO
CREATE VIEW maker_checker_user_group_permissions_changes AS SELECT TOP(9223372036854775800) UGPCA.id, UGPCA.columnName, UGPCA.module, UGPCA.columnValue, UGPCA.columnStatus, UGPCA.columnRequestedValue, UGPCA.columnApprovedValue, UGPCA.affectedUserGroupId, affectedUserGroup.name AS affectedUserGroupName, UGPCA.makerId, UGPCA.checkerId, CAST(UGPCA.createdOn AS DATE) AS createdOn, CAST(UGPCA.authorizedOn AS DATE) AS authorizedOn, ( makerUser.firstName + ' ' + makerUser.lastName ) AS makerUserProfile, ( checkerUser.firstName + ' ' + checkerUser.lastName ) AS checkerUserProfile FROM user_group_permissions_changes_authorization as UGPCA LEFT JOIN user_groups affectedUserGroup ON affectedUserGroup.id = UGPCA.affectedUserGroupId LEFT JOIN user_profiles makerUser ON makerUser.user_id = UGPCA.makerId LEFT JOIN user_profiles checkerUser ON checkerUser.user_id = UGPCA.checkerId
GO

IF OBJECT_ID('dbo.user_groups_authorization', 'V') IS NOT NULL DROP VIEW dbo.user_groups_authorization;
GO
CREATE VIEW user_groups_authorization AS SELECT TOP(9223372036854775800)  max(affectedUserGroupId) AS affectedUserGroupId, max(checkerId)  AS checkerId FROM user_groups_changes_authorization WHERE user_groups_changes_authorization.changeType = 'add' AND user_groups_changes_authorization.columnStatus = 'Approved' GROUP BY affectedUserGroupId
GO

IF OBJECT_ID('dbo.user_groups_full_details', 'V') IS NOT NULL DROP VIEW dbo.user_groups_full_details;
GO
CREATE VIEW user_groups_full_details AS SELECT TOP(9223372036854775800) user_groups.id,user_groups.system_group,user_groups.createdBy as createdById,user_groups.modifiedBy as modifiedById, user_groups.name, user_groups.description , user_groups.flagNeedApproval, user_groups.needApprovalOnAdd,  CAST(user_groups.createdOn AS DATE) AS createdOn, (created.firstName + ' ' + created.lastName) AS createdBy,  CAST(user_groups.modifiedOn AS DATE) modifiedOn, (modified.firstName + ' ' + modified.lastName) AS modifiedBy, ( userGroupAuthorized.firstName + ' ' + userGroupAuthorized.lastName ) AS AuthorizedByFullName FROM user_groups LEFT JOIN user_groups_authorization ON user_groups_authorization.affectedUserGroupId = user_groups.id LEFT JOIN user_profiles userGroupAuthorized ON userGroupAuthorized.user_id = user_groups_authorization.checkerId LEFT JOIN user_profiles created ON created.user_id = user_groups.createdBy LEFT JOIN user_profiles modified ON modified.user_id = user_groups.modifiedBy

GO
IF OBJECT_ID('dbo.comapnies_ss_expiry_dates', 'V') IS NOT NULL DROP VIEW dbo.comapnies_ss_expiry_dates;
GO
--comapnies_ss_expiry_dates;
CREATE VIEW comapnies_ss_expiry_dates AS SELECT
	TOP(9223372036854775800)
    company_discharge_social_securities.id, STUFF((SELECT ', ' + user_profiles.firstName + ' ' + user_profiles.lastName + CASE WHEN user_profiles.status = 'Active' THEN '' ELSE ' (Inactive)' END 
    FROM user_profiles INNER JOIN license_and_waiver_reminds ON user_profiles.user_id = license_and_waiver_reminds.user_id AND license_and_waiver_reminds.license_and_waiver_id = company_discharge_social_securities.id FOR XML PATH('')), 1, 1, '') AS remindNames,
	STUFF((SELECT ', ' + CAST(user_profiles.user_id AS nvarchar) FROM user_profiles INNER JOIN license_and_waiver_reminds ON user_profiles.user_id = license_and_waiver_reminds.user_id AND license_and_waiver_reminds.license_and_waiver_id = company_discharge_social_securities.id FOR XML PATH('')), 1, 1, '') AS remindIds,
    discharges.name AS typeOfDischarge, company_discharge_social_securities.releasedOn, company_discharge_social_securities.expiresOn, company_discharge_social_securities.reference, 
    companies.name AS companyName,companies.id AS companyId,company_legal_types.name AS companyLegalType, companies.nationality_id,
    STUFF((SELECT DISTINCT ', ' + user_groups.name FROM user_groups INNER JOIN license_and_waiver_reminds ON user_groups.id = license_and_waiver_reminds.user_group_id AND license_and_waiver_reminds.license_and_waiver_id = company_discharge_social_securities.id FOR XML PATH('')), 1, 1, '') as remindGroups
	FROM license_and_waiver_reminds
	LEFT JOIN  company_discharge_social_securities ON company_discharge_social_securities.id = license_and_waiver_reminds.license_and_waiver_id
	LEFT JOIN company_type_of_discharges discharges ON discharges.id = company_discharge_social_securities.type_id
	LEFT JOIN companies ON companies.id = company_discharge_social_securities.company_id
	LEFT JOIN company_legal_types ON company_legal_types.id = companies.company_legal_type_id
	GROUP BY company_discharge_social_securities.id, discharges.name, company_discharge_social_securities.releasedOn, company_discharge_social_securities.expiresOn, company_discharge_social_securities.reference, companies.name, companies.id, companies.nationality_id, company_legal_types.name
	ORDER BY company_discharge_social_securities.expiresOn ASC;
GO

IF OBJECT_ID('dbo.legal_case_notes_history', 'V') IS NOT NULL DROP VIEW dbo.legal_case_notes_history;
GO
CREATE VIEW legal_case_notes_history AS
SELECT case_comments.id, case_comments.case_id  AS caseId, case_comments.comment, case_comments.createdOn,
(CASE WHEN case_comments.createdByChannel='CP' THEN (customer_portal_users.firstName + ' ' + customer_portal_users.lastName) ELSE (user_profiles.firstName+ ' ' + user_profiles.lastName ) END) as createdBy, case_comments.user_id AS createdById, case_comments.createdByChannel,
(CASE WHEN case_comments.modifiedByChannel='CP' THEN (cpmodified.firstName + ' ' + cpmodified.lastName) ELSE ( modified.firstName + ' ' + modified.lastName ) END) AS modifiedBy, case_comments.modifiedBy as modifiedById, case_comments.modifiedByChannel
FROM case_comments
LEFT JOIN user_profiles ON user_profiles.user_id = case_comments.user_id
LEFT JOIN user_profiles modified ON modified.user_id = case_comments.modifiedBy
LEFT JOIN customer_portal_users ON customer_portal_users.id = case_comments.user_id AND case_comments.createdByChannel='CP'
LEFT JOIN customer_portal_users cpmodified ON cpmodified.id = case_comments.modifiedBy AND case_comments.modifiedByChannel='CP';
GO

IF OBJECT_ID('dbo.accounts_details_lookup', 'V') IS NOT NULL DROP VIEW dbo.accounts_details_lookup;
GO
CREATE VIEW accounts_details_lookup AS
SELECT accounts.*,CASE WHEN prefixes.account_number_prefix IS NOT NULL THEN (prefixes.account_number_prefix + CAST(accounts.number as nvarchar)) ELSE CAST(accounts.number as nvarchar) END as account_number,
    accounts_types.name as accountType,accounts_types.type as accountCategory,countries.currencyCode,countries.currencyName,
    CASE WHEN accounts.model_name = 'Person' THEN ( CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END )
    WHEN accounts.model_name = 'Company' THEN companies.name END AS fullName,CASE WHEN accounts.model_name = 'Person' THEN contacts.address1 WHEN accounts.model_name = 'Company' THEN company_addresses.address END AS address1,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.address2 WHEN accounts.model_name = 'Company' THEN '' END AS address2,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.zip WHEN accounts.model_name = 'Company' THEN company_addresses.zip END AS zip,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.city WHEN accounts.model_name = 'Company' THEN company_addresses.city END AS city,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.country_id WHEN accounts.model_name = 'Company' THEN company_addresses.country END AS country_id,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.state WHEN accounts.model_name = 'Company' THEN company_addresses.state END AS state,
    CASE WHEN accounts.model_type = 'partner' THEN partners.isThirdParty ELSE '' END AS isThirdParty,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.tax_number WHEN accounts.model_name = 'Company' THEN companies.registrationTaxNb END AS tax_number,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.additional_id_type WHEN accounts.model_name = 'Company' THEN companies.additional_id_type END AS additional_id_type,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.additional_id_value WHEN accounts.model_name = 'Company' THEN companies.additional_id_value END AS additional_id_value,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.street_name WHEN accounts.model_name = 'Company' THEN company_addresses.street_name END AS street_name,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.additional_street_name WHEN accounts.model_name = 'Company' THEN company_addresses.additional_street_name END AS additional_street_name,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.building_number WHEN accounts.model_name = 'Company' THEN company_addresses.building_number END AS building_number,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.address_additional_number WHEN accounts.model_name = 'Company' THEN company_addresses.address_additional_number END AS address_additional_number,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.district_neighborhood WHEN accounts.model_name = 'Company' THEN company_addresses.district_neighborhood END AS district_neighborhood
FROM accounts
    left join companies on companies.id = accounts.member_id
    left join company_addresses on company_addresses.id = (SELECT TOP 1 c1.id FROM company_addresses c1 WHERE companies.id = c1.company ORDER BY c1.id ASC)
    left join contacts on contacts.id = accounts.member_id join countries on countries.id = accounts.currency_id join accounts_types on accounts_types.id = accounts.account_type_id
    left join partners on partners.id = accounts.model_id and accounts.model_type = 'partner'
    left join account_number_prefix_per_entity  as prefixes on accounts_types.id = prefixes.account_type_id AND accounts.organization_id = prefixes.organization_id;
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

IF OBJECT_ID('dbo.bills_full_details', 'V') IS NOT NULL DROP VIEW dbo.bills_full_details;
GO
CREATE VIEW bills_full_details AS
SELECT voucher_headers.id, voucher_headers.organization_id, 
voucher_headers.dated, voucher_headers.voucherType, voucher_headers.referenceNum,
( 'M' + (SELECT CAST(legal_cases.id AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id ) ) as caseID,
voucher_headers.attachment, voucher_headers.description,voucher_headers.refNum,
( adl.name + ' - ' + adl.currencyCode ) as supplierAccount, adl.name AS accountName, adl.fullName AS supplierName,
voucher_headers.createdOn,voucher_headers.createdBy,voucher_headers.modifiedOn,voucher_headers.modifiedBy,
case_id = STUFF((
    SELECT ','+ CAST(voucher_related_cases.legal_case_id AS nvarchar) 
    FROM voucher_related_cases 
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id  
    FOR XML PATH('')), 1, 1, ''),
( created.firstName + ' ' + created.lastName ) AS createdByName,
( modified.firstName + ' ' + modified.lastName ) AS modifiedByName,adl.account_number,
(SELECT ( Sum(( price * quantity )) ) FROM bill_details WHERE bill_header_id = bill_headers.id) AS subtotal,
(SELECT ( Sum(( ( ( price * quantity ) * Isnull(percentage, 0) ) / 100 ))) FROM bill_details WHERE bill_header_id = bill_headers.id) AS totaltax,
adl.tax_number AS tax_number,
B.payemntsMade as payemntsMade,
CASE WHEN ( bill_headers.status <> 'paid' AND bill_headers.dueDate < GETDATE()) THEN 'overdue' ELSE bill_headers.status END AS billStatus,
bill_headers.id as billID, bill_headers.dueDate as dueDate, bill_headers.account_id as accountID, bill_headers.total as total,
bill_headers.total-B.balanceDueNet AS balanceDue,
caseSubject = STUFF((
    SELECT ':/;'+ CAST(legal_cases.subject AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id 
    FOR XML PATH('')), 1, 3, ''),
caseCategory = STUFF((
    SELECT ','+ CAST(legal_cases.category AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id 
    FOR XML PATH('')), 1, 1, ''),
 cli.name as clientName,bill_headers.client_id as clientID,
displayTax
FROM voucher_headers
INNER JOIN (
select bill_headers.voucher_header_id, bill_headers.id,  SUM(bpb.amount) as payemntsMade, ISNULL(SUM(bpb.amount), 0.0) as balanceDueNet
FROM bill_headers
LEFT JOIN bill_payment_bills bpb ON bpb.bill_header_id = bill_headers.id
GROUP BY bill_headers.id, bill_headers.voucher_header_id
) B ON B.voucher_header_id = voucher_headers.id
INNER JOIN bill_headers ON bill_headers.id = B.id
INNER JOIN accounts_details_lookup adl ON adl.id = bill_headers.account_id

LEFT JOIN clients_view cli ON cli.id = bill_headers.client_id and cli.model = 'clients'
LEFT JOIN user_profiles created ON created.user_id = voucher_headers.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = voucher_headers.modifiedBy
LEFT JOIN accounts acc ON acc.id = bill_headers.account_id AND acc.model_type = 'supplier'
GO

IF OBJECT_ID('dbo.journals_full_details', 'V') IS NOT NULL DROP VIEW dbo.journals_full_details;
GO
CREATE VIEW journals_full_details AS
SELECT voucher_headers.id, voucher_headers.organization_id, 
case_id = STUFF((
    SELECT ','+ CAST(voucher_related_cases.legal_case_id AS nvarchar) 
    FROM voucher_related_cases 
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id  
    FOR XML PATH('')), 1, 1, ''),
voucher_headers.dated,
voucher_headers.voucherType, voucher_headers.referenceNum, voucher_headers.attachment, voucher_headers.description,
voucher_headers.refNum, VD.amount as amount, CAST(voucher_headers.createdOn as DATE) as createdOn ,voucher_headers.createdBy,CAST(voucher_headers.modifiedOn as DATE) as modifiedOn,
voucher_headers.modifiedBy,( created.firstName + ' ' + created.lastName ) AS createdByName,( modified.firstName + ' ' + modified.lastName ) AS modifiedByName
FROM voucher_headers
INNER JOIN (
SELECT voucher_details.id, voucher_details.voucher_header_id, SUM(voucher_details.local_amount) AS amount
FROM voucher_details
GROUP BY voucher_details.id, voucher_details.voucher_header_id
) VD ON VD.voucher_header_id = voucher_headers.id and voucher_headers.voucherType = 'JV'
INNER JOIN voucher_details ON voucher_details.id = VD.id and voucher_details.drCr = 'C'
LEFT JOIN user_profiles created ON created.user_id = voucher_headers.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = voucher_headers.modifiedBy;
GO

IF OBJECT_ID('dbo.expenses_full_details', 'V') IS NOT NULL DROP VIEW dbo.expenses_full_details;
GO

CREATE VIEW expenses_full_details AS
SELECT voucher_headers.id, voucher_headers.organization_id,
voucher_headers.dated, voucher_headers.voucherType, voucher_headers.refNum,
voucher_headers.referenceNum, voucher_headers.attachment, voucher_headers.description,
case_id = STUFF((
    SELECT ','+ CAST(voucher_related_cases.legal_case_id AS nvarchar) 
    FROM voucher_related_cases 
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id  
    FOR XML PATH('')), 1, 1, ''),
( 'M' + (SELECT CAST(legal_cases.id AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id ) ) as caseID,
caseSubject = STUFF((
    SELECT ':/;'+ CAST(legal_cases.subject AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id 
    FOR XML PATH('')), 1, 3, ''),
caseCategory = STUFF((
    SELECT ','+ CAST(legal_cases.category AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id 
    FOR XML PATH('')), 1, 1, ''),
expenses.id as expenseID,expenses.paymentMethod as paymentMethod, expenses.amount as amount, expenses.billingStatus as billingStatus,
expenses.client_id as clientID, expenses.client_account_id as clientAccountID,
adl1.id as paidThroughID, adl1.name as paidThroughAccount, adl1.currency_id as currency_id, adl1.currencyCode as currency,adl1.account_number as paid_through_account_number,
ven.name as supplier, cli.name as clientName, adl2.name as clientAccount,adl2.currency_id as clientAccountCurrencyId,adl2.currencyCode as clientAccountCurrency,adl2.account_number as client_account_number,
expense_categories.id as expenseCategoryId,( 'T' +  CAST( expenses.task AS nvarchar ) ) as task, expenses.task as task_id , expenses.hearing, expenses.event, expenses.status as status,
CASE WHEN expense_categories.expense_category_id is null THEN expense_categories.name ELSE ( ec.name + ' / ' + expense_categories.name ) END as expenseCategory,
CASE WHEN expense_categories.expense_category_id is null THEN expense_categories.fl1name ELSE ( ec.fl1name + ' / ' + expense_categories.fl1name ) END as expenseCategoryfl1,
CASE WHEN expense_categories.expense_category_id is null THEN expense_categories.fl2name ELSE ( ec.fl2name + ' / ' + expense_categories.fl2name ) END as expenseCategoryfl2,
voucher_headers.createdOn,voucher_headers.createdBy,voucher_headers.modifiedOn,voucher_headers.modifiedBy,
CASE WHEN created.status='inactive' THEN ( created.firstName + ' ' + created.lastName + ' (Inactive) ') ELSE ( created.firstName + ' ' + created.lastName ) END AS createdByName,
(CASE WHEN modified.status='inactive' THEN( modified.firstName + ' ' + modified.lastName + ' (Inactive) ') ELSE ( modified.firstName + ' ' + modified.lastName ) END) AS modifiedByName,
(SELECT Sum(exp.amount - (exp.amount / ((Isnull(taxes.percentage, 0) / 100) + 1))) FROM taxes LEFT JOIN expenses exp ON taxes.id = exp.tax_id AND exp.voucher_header_id = voucher_headers.id) AS totaltax,
(SELECT Sum(exp.amount - (exp.amount - (exp.amount / ((Isnull(taxes.percentage, 0) / 100) + 1)))) FROM taxes LEFT JOIN expenses exp ON taxes.id = exp.tax_id AND exp.voucher_header_id = voucher_headers.id) AS subtotal,
ven.tax_number AS tax_number
FROM voucher_headers
INNER JOIN expenses ON expenses.voucher_header_id = voucher_headers.id
INNER JOIN expense_categories ON expense_categories.id = expenses.expense_category_id
INNER JOIN accounts_details_lookup adl1 ON adl1.id = expenses.paid_through
LEFT JOIN accounts_details_lookup adl2 ON adl2.id = expenses.client_account_id
LEFT JOIN clients_view ven ON ven.id = expenses.vendor_id and ven.model = 'suppliers'
LEFT JOIN clients_view cli ON cli.id = expenses.client_id and cli.model = 'clients'
LEFT JOIN expense_categories ec ON ec.id = expense_categories.expense_category_id
LEFT JOIN user_profiles created ON created.user_id = voucher_headers.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = voucher_headers.modifiedBy;
GO

IF OBJECT_ID('dbo.chart_of_accounts', 'V') IS NOT NULL DROP VIEW dbo.chart_of_accounts;
GO
CREATE VIEW chart_of_accounts AS
SELECT
accounts_details_lookup.*,
( accounts_details_lookup.accountCategory + ' / ' + accounts_details_lookup.accountType ) as accountCategoryType,
CASE
WHEN VD.localAmountVD IS NOT NULL
THEN VD.localAmountVD
ELSE 0 END as localAmount,
CASE
WHEN VD.foreignAmountVD IS NOT NULL
THEN VD.foreignAmountVD
ELSE 0 END as foreignAmount,
CASE
WHEN VD.totalDebitVD IS NOT NULL
THEN VD.totalDebitVD
ELSE 0 END as totalDebit,
CASE
WHEN VD.totalCreditVD IS NOT NULL
THEN VD.totalCreditVD
ELSE 0 END as totalCredit
FROM accounts_details_lookup
LEFT JOIN (
SELECT voucher_details.account_id,
SUM( CASE
WHEN voucher_details.drCr = 'D'
THEN (CASE
WHEN voucher_details.foreign_amount IS NOT NULL
THEN voucher_details.foreign_amount ELSE 0 END)
ELSE (CASE
WHEN voucher_details.foreign_amount IS NOT NULL
THEN (voucher_details.foreign_amount*-1) ELSE 0 END)
END ) AS foreignAmountVD,
SUM( CASE
WHEN voucher_details.drCr = 'D'
THEN (CASE
WHEN voucher_details.local_amount IS NOT NULL
THEN voucher_details.local_amount ELSE 0 END)
ELSE (CASE
WHEN voucher_details.local_amount IS NOT NULL
THEN (voucher_details.local_amount*-1) ELSE 0 END)
END ) AS localAmountVD,
SUM(
CASE WHEN voucher_details.drCr = 'D'
THEN (CASE WHEN voucher_details.foreign_amount IS NOT NULL THEN voucher_details.foreign_amount ELSE 0 END)
ELSE 0
END ) AS totalDebitVD,
SUM(
CASE WHEN voucher_details.drCr = 'C'
THEN (CASE WHEN voucher_details.foreign_amount IS NOT NULL THEN (voucher_details.foreign_amount*-1) ELSE 0 END)
ELSE 0
END ) AS totalCreditVD
FROM voucher_details
inner JOIN voucher_headers ON voucher_headers.id = voucher_details.voucher_header_id
GROUP BY voucher_details.account_id ) VD ON VD.account_id = accounts_details_lookup.id
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

IF OBJECT_ID('dbo.account_user_mapping', 'V') IS NOT NULL DROP VIEW dbo.account_user_mapping
GO
CREATE VIEW account_user_mapping AS SELECT TOP(9223372036854775800)
accounts.id AS accountId, accounts.organization_id AS organizationId, accounts.currency_id AS currencyId, countries.currencyCode,
CASE WHEN prefixes.account_number_prefix IS NOT NULL THEN (accounts.name + ' - ' + countries.currencyCode + ' (' + prefixes.account_number_prefix + CAST(accounts.number as nvarchar) + ')') ELSE (accounts.name  + ' - ' + countries.currencyCode + ' (' + CAST(accounts.number as nvarchar) + ')') END as accountName,
 accounts_users.userId,(user_profiles.firstName + ' ' + user_profiles.lastName) as userName
FROM accounts
INNER JOIN countries ON countries.id = accounts.currency_id
INNER JOIN accounts_types ON accounts_types.id = accounts.account_type_id
INNER JOIN accounts_users ON accounts_users.accountId = accounts.id
INNER JOIN user_profiles ON user_profiles.user_id = accounts_users.userId
left join account_number_prefix_per_entity  as prefixes on accounts.account_type_id = prefixes.account_type_id AND accounts.organization_id = prefixes.organization_id
GO

IF OBJECT_ID('dbo.customer_portal_users_grid', 'V') IS NOT NULL DROP VIEW dbo.customer_portal_users_grid
GO
CREATE VIEW customer_portal_users_grid AS SELECT TOP(9223372036854775800)
customer_portal_users.id, customer_portal_users.userDirectory, customer_portal_users.isAd, customer_portal_users.isA4Luser, customer_portal_users.username, customer_portal_users.email, customer_portal_users.password
, customer_portal_users.status, customer_portal_users.firstName, customer_portal_users.lastName, customer_portal_users.employeeId, customer_portal_users.type as userType
, customer_portal_users.userCode, customer_portal_users.department, customer_portal_users.jobTitle, customer_portal_users.phone, customer_portal_users.mobile, customer_portal_users.banned, customer_portal_users.ban_reason, customer_portal_users.last_ip, customer_portal_users.last_login
, customer_portal_users.approved, customer_portal_users.createdOn, (created.firstName + ' ' + created.lastName) AS createdByName, customer_portal_users.modifiedOn
, (modified.firstName + ' ' + modified.lastName ) AS modifiedByName, modified.status as modifiedStatus, created.status as createdStatus
, company=STUFF( (SELECT '; '+ companies.name FROM companies_customer_portal_users INNER JOIN companies ON companies.id = companies_customer_portal_users.company_id WHERE companies_customer_portal_users.customer_portal_user_id = customer_portal_users.id FOR XML PATH('')), 1, 1, '')
FROM customer_portal_users
LEFT JOIN user_profiles created ON created.user_id = customer_portal_users.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = customer_portal_users.modifiedBy
GO

IF OBJECT_ID('dbo.legal_cases_event_details', 'V') IS NOT NULL DROP VIEW dbo.legal_cases_event_details
GO
CREATE VIEW legal_cases_event_details AS
    SELECT TOP(9223372036854775800) events.id, events.legal_case,
    events.event_type,events.fields,events .parent,events.modifiedBy,( modified.firstName + ' ' + modified.lastName ) as modified_by_name,modified.status as modified_by_status,events.modifiedOn,ld.legal_case_stage,events.stage,
    event_related_tasks = STUFF((SELECT ',' + CAST( related_data.related_id AS nvarchar ) from legal_case_events_related_data as related_data WHERE related_data.related_object = 'Task' AND related_data.event=events.id FOR XML PATH('')), 1, 1, ''),
    case_related_tasks = STUFF((SELECT ',' + CAST( related_data.related_id AS nvarchar)  from legal_case_events_related_data as related_data WHERE related_data.related_object = 'Task' FOR XML PATH('')), 1, 1, ''),
    event_related_reminders = STUFF((SELECT ',' + CAST( related_data.related_id AS nvarchar)  from legal_case_events_related_data as related_data WHERE related_data.related_object = 'Reminder' AND related_data.event=events.id FOR XML PATH('')), 1, 1, ''),
    case_related_reminders = STUFF((SELECT ',' + CAST( related_data.related_id AS nvarchar)  from legal_case_events_related_data as related_data WHERE related_data.related_object = 'Reminder' FOR XML PATH('')), 1, 1, '')
    FROM legal_case_events as events
    LEFT JOIN legal_case_litigation_details as ld on ld.id = events.stage
    LEFT JOIN user_profiles modified ON modified.user_id = events.modifiedBy;
GO

IF OBJECT_ID('dbo.deposits_full_details', 'V') IS NOT NULL DROP VIEW dbo.deposits_full_details;
GO
CREATE VIEW deposits_full_details AS
SELECT deposits.id,('DP' + CAST(deposits.id AS nvarchar)) as deposit_id,
deposits.payment_method,
deposits.foreign_amount,deposits.currency as foreign_currency, VD.local_amount,
accounts.client AS client_id,
voucher_headers.id as voucher_header_id, voucher_headers.organization_id, voucher_headers.dated as deposited_on, voucher_headers.voucherType as voucher_type,voucher_headers.refNum as ref_num,
voucher_headers.description,
liability_acc.id AS liability_acc_id,
(liability_acc.name + ' - '  + liability_acc.currencyCode + ' (' +  liability_acc.account_number +  ')' ) AS liability_account,asset_acc.id AS asset_acc_id,
(asset_acc.name + ' - '  + asset_acc.currencyCode + ' (' + asset_acc.account_number +  ')' )  AS asset_account,
clients.name AS client_name
FROM voucher_headers
INNER JOIN deposits ON deposits.voucher_header_id = voucher_headers.id
LEFT JOIN (
SELECT voucher_details.voucher_header_id, voucher_details.local_amount
FROM voucher_details
GROUP BY voucher_details.voucher_header_id,voucher_details.local_amount )VD on deposits.voucher_header_id=VD.voucher_header_id
INNER JOIN client_trust_accounts_relation as accounts ON accounts.id = deposits.client_trust_accounts_id
INNER JOIN accounts_details_lookup liability_acc ON liability_acc.id = accounts.trust_liability_account
INNER JOIN accounts_details_lookup asset_acc ON asset_acc.id = accounts.trust_asset_account
LEFT JOIN clients_view clients ON clients.id = accounts.client AND clients.model = 'clients'
GO

IF OBJECT_ID('dbo.quotes_full_details', 'V') IS NOT NULL DROP VIEW dbo.quotes_full_details;
GO
CREATE VIEW quotes_full_details AS SELECT
voucher_headers.id,
voucher_headers.organization_id,
voucher_headers.dated,
voucher_headers.voucherType,
voucher_headers.refNum,
voucher_headers.referenceNum,
voucher_headers.attachment,
voucher_headers.description,
ih.prefix AS prefix,
ih.suffix as suffix,
ih.paidStatus  AS paidStatus,
ih.purchaseOrder AS purchaseOrder,
ih.dueOn as dueOn, ih.account_id as accountID,
ih.total as total,
CASE WHEN ih.suffix is null THEN ( ih.prefix + CAST( voucher_headers.refNum AS nvarchar ) ) ELSE ( ih.prefix + CAST( voucher_headers.refNum AS nvarchar ) + ih.suffix ) END AS quoteId,
( adl.name + ' - ' + adl.currencyCode ) as clientAccount, adl.name AS accountName,
adl.currencyCode as clientCurrency, adl.fullName AS clientName,
adl.member_id as member_id, adl.model_name as model_type, adl.model_id as model_id,
voucher_headers.createdOn,voucher_headers.createdBy,voucher_headers.modifiedOn,voucher_headers.modifiedBy,
( created.firstName + ' ' + created.lastName ) AS createdByName,
( modified.firstName + ' ' + modified.lastName ) AS modifiedByName,
displayTax,
displayDiscount,
case_id = STUFF((
    SELECT ','+ CAST(voucher_related_cases.legal_case_id AS nvarchar) 
    FROM voucher_related_cases 
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id  
    FOR XML PATH('')), 1, 1, ''),
caseSubject = STUFF((
    SELECT ':/;'+ CAST(legal_cases.subject AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id 
    FOR XML PATH('')), 1, 3, ''),
caseCategory = STUFF((
    SELECT ','+ CAST(legal_cases.category AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id 
    FOR XML PATH('')), 1, 1, ''),
caseInternalReference = STUFF((
    SELECT ','+ CAST(legal_cases.internalReference AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id 
    FOR XML PATH('')), 1, 1, ''), adl.account_number,  
	(
	SELECT ( sum((unitprice * quantity)) )
	FROM   quote_details
	WHERE  quote_header_id = ih.id ) AS subtotal,
	(
	SELECT ( sum( ROUND( ( (unitprice * quantity) * ISNULL(discountpercentage, 0) ) / 100, 2 ) ) )
	FROM   quote_details
	WHERE  quote_header_id = ih.id ) AS totaldiscount,
  (
	SELECT ( sum( ROUND( ( ( (unitprice * quantity) -( ( (unitprice * quantity) * ISNULL(discountpercentage, 0) ) / 100 ) ) * ISNULL(percentage, 0) ) / 100, 2 ) ) )
	FROM   quote_details
	WHERE  quote_header_id = ih.id ) AS totaltax,
	(
	SELECT ( sum( ( (unitprice * quantity) -( ( (unitprice * quantity) * ISNULL(discountpercentage, 0) ) / 100 ) ) +( ( ( (unitprice * quantity) -( ( (unitprice * quantity) * ISNULL(discountpercentage, 0) ) / 100 ) ) * ISNULL(percentage, 0) ) / 100 ) ) )
	FROM   quote_details
	WHERE  quote_header_id = ih.id ) AS total_after_discount_amount,
  (
	SELECT ( sum(
		   CASE
				  WHEN percentage > 0 THEN (( ( ( (unitprice * quantity) -( ( (unitprice * quantity) * ISNULL(discountpercentage, 0) ) / 100 ) ) * ISNULL(percentage, 0) ) ) / percentage)
				  ELSE 0
		   end ))
	FROM   quote_details
	WHERE  quote_header_id = ih.id ) AS taxable,
    (
        (
            SELECT ( sum((unitprice * quantity)) )
            FROM   quote_details
            WHERE  quote_header_id = ih.id 
        ) - 
        (
            SELECT ( sum( ( ( (unitprice * quantity) * ISNULL(discountpercentage, 0) ) / 100 ) ) )
            FROM   quote_details
            WHERE  quote_header_id = ih.id 
        ) - 
        (
            SELECT ( 
                sum(
                    CASE
                        WHEN percentage > 0 THEN (( ( ( (unitprice * quantity) -( ( (unitprice * quantity) * ISNULL(discountpercentage, 0) ) / 100 ) ) * ISNULL(percentage, 0) ) ) / percentage)
                        ELSE 0
                    END 
                )
            )
            FROM   quote_details
            WHERE  quote_header_id = ih.id 
        )
    ) AS nonTaxable,
	(
         SELECT  (SUM((unitPrice * quantity))) - (
                SUM(
                    ROUND(
                        (
                            (unitPrice * quantity) * ISNULL(discountPercentage, 0)
                        ) / 100, 2
                    )
                )
            )
        FROM   quote_details
        WHERE  quote_header_id = ih.id 
    ) AS sub_total_after_discount
FROM voucher_headers
INNER JOIN quote_headers as ih ON ih.voucher_header_id = voucher_headers.id
INNER JOIN accounts_details_lookup adl ON adl.id = ih.account_id
LEFT JOIN user_profiles created ON created.user_id = voucher_headers.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = voucher_headers.modifiedBy;
GO

IF OBJECT_ID('dbo.legal_case_litigation_stages_full_details', 'V') IS NOT NULL DROP VIEW dbo.legal_case_litigation_stages_full_details;
GO
CREATE VIEW legal_case_litigation_stages_full_details AS SELECT
    TOP (9223372036854775800)
    stages.id as id,stages.legal_case_id,stages.sentenceDate,stages.comments,stages.legal_case_stage,stages.client_position,stages.status,stages.modifiedBy, stages.modifiedOn,
    court_types.name as court_type,court_degrees.name as court_degree,court_regions.name as court_region, courts.name as court,clients_view.name as client_name, stages.createdOn, stages.createdBy,
    ( UP.firstName + ' ' + UP.lastName ) as modifiedByName, ( creator.firstName + ' ' + creator.lastName ) as createdByName,
    ext_references =  STUFF((SELECT DISTINCT ' , ' + (ref.number) from legal_case_litigation_external_references as ref WHERE ref.stage = stages.id FOR XML PATH('')), 1, 3, ''),
    case_opponents = STUFF((SELECT ' , ' +(CASE WHEN legal_case_opponents.opponent_member_type IS NULL THEN NULL ELSE (CASE WHEN legal_case_opponents.opponent_member_type = 'company' THEN opponentCompany.name ELSE
    (CASE WHEN opponentContact.father!='' THEN (opponentContact.firstName + ' ' + opponentContact.father + ' ' + opponentContact.lastName) ELSE (opponentContact.firstName + ' ' + opponentContact.lastName) END) END)END ) from legal_case_opponents INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id
    LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company'
    LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact'
    WHERE legal_case_opponents.case_id = stages.legal_case_id FOR XML PATH('')), 1, 3, ''),
    opponents = STUFF((SELECT ' , ' + (CASE WHEN opponents.company_id IS NOT NULL THEN opponentCompany.name ELSE (CASE WHEN opponentContact.father!=''
    THEN (opponentContact.firstName + ' ' + opponentContact.father + ' ' + opponentContact.lastName) ELSE (opponentContact.firstName + ' ' + opponentContact.lastName) END) END  )
    from  legal_case_litigation_stages_opponents as stages_opponents
    INNER JOIN opponents ON opponents.id = stages_opponents.opponent_id
    LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id
    LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id
    WHERE stages_opponents.stage = stages.id FOR XML PATH('')), 1, 3, '')
    FROM legal_case_litigation_details as stages
    LEFT JOIN court_types ON court_types.id = stages.court_type_id
    LEFT JOIN court_degrees ON court_degrees.id = stages.court_degree_id
    LEFT JOIN court_regions ON court_regions.id = stages.court_region_id
    LEFT JOIN courts ON courts.id = stages.court_id
    LEFT JOIN legal_cases ON legal_cases.id = stages.legal_case_id
    LEFT JOIN clients_view ON clients_view.id=legal_cases.client_id AND clients_view.model = 'clients'
    LEFT JOIN user_profiles as UP ON UP.user_id = stages.modifiedBy
    LEFT JOIN user_profiles as creator ON creator.user_id = stages.createdBy
    ORDER BY stages.modifiedBy DESC;
GO

IF OBJECT_ID('dbo.invoice_details_full_details', 'V') IS NOT NULL DROP VIEW dbo.invoice_details_full_details;
GO

CREATE VIEW invoice_details_full_details AS 
SELECT
   invoice_details.id,
   invoice_details.invoice_header_id,
   invoice_headers.account_id,
   invoice_details.item,
   invoice_details.unitPrice as unit_price,
   invoice_details.quantity,
   cast( invoice_details.unitPrice * invoice_details.quantity as DECIMAL(38, 2) ) as total_price,
   invoice_details.itemDescription as item_description,
   invoice_details.percentage as tax_percentage,
   CASE
      WHEN
         invoice_headers.displayDiscount = 'invoice_level_before_tax' 
      THEN
         invoice_headers.discount_percentage 
      ELSE
         invoice_details.discountPercentage 
   END
   as discount_percentage, 
   CASE
      WHEN
         invoice_headers.displayDiscount = 'invoice_level_before_tax' 
      THEN
         cast(( invoice_details.unitPrice * invoice_details.quantity * invoice_headers.discount_percentage / 100) as DECIMAL(38, 2) ) 
      ELSE
         invoice_details.discountAmount 
   END
   as discount_amount, invoice_details.item_date, 
   CASE
      WHEN
         invoice_headers.displayDiscount = 'invoice_level_before_tax' 
      THEN
         cast( ( invoice_details.unitPrice * invoice_details.quantity ) - ( invoice_details.unitPrice * invoice_details.quantity * invoice_headers.discount_percentage / 100) as DECIMAL(38, 2) ) 
      ELSE
         cast( invoice_details.unitPrice * invoice_details.quantity - ( invoice_details.unitPrice * invoice_details.quantity * ISNULL(invoice_details.discountPercentage, 0) / 100 ) - ISNULL(invoice_details.discountAmount, 0) as DECIMAL(38, 2) ) 
   END
   as price_after_discount, 
   CASE
      WHEN
         invoice_headers.displayDiscount = 'invoice_level_before_tax' 
      THEN
         cast( (((invoice_details.unitPrice * invoice_details.quantity) - ( invoice_details.unitPrice * invoice_details.quantity * invoice_headers.discount_percentage / 100))) + ((((invoice_details.unitPrice * invoice_details.quantity) - ( invoice_details.unitPrice * invoice_details.quantity * invoice_headers.discount_percentage / 100)) * ISNULL(invoice_details.percentage, 0) / 100 )) as DECIMAL(38, 2)) 
      ELSE
         cast( (invoice_details.unitPrice * invoice_details.quantity) - ((((invoice_details.unitPrice * invoice_details.quantity) * ISNULL(invoice_details.discountPercentage, 0) / 100) - ISNULL(invoice_details.discountAmount, 0))) + (((invoice_details.unitPrice * invoice_details.quantity) - (((invoice_details.unitPrice * invoice_details.quantity) * ISNULL(invoice_details.discountPercentage, 0) / 100) - ISNULL(invoice_details.discountAmount, 0))) * ISNULL(invoice_details.percentage, 0) / 100 ) as DECIMAL(38, 2)) 
   END
   as price_after_tax 
FROM
   invoice_details 
   inner join
      invoice_headers 
      on invoice_details.invoice_header_id = invoice_headers.id;
	  GO

IF OBJECT_ID('dbo.credit_notes_full_details', 'V') IS NOT NULL 
DROP VIEW dbo.credit_notes_full_details;
GO 
CREATE VIEW credit_notes_full_details AS 
select cnh.id as id, cnh.id as credit_note_header_id, cnh.voucher_header_id as voucher_header_id,cnh.organization_id as organization_id,cast(cnh.credit_note_date as date) AS credit_note_date
	,cnh.reference_num AS credit_note_reference,cnh.credit_note_number AS credit_note_number, cnh.description AS description,cnh.prefix AS prefix,cnh.suffix AS suffix
	,(case when (cnh.paid_status = 'open' and cnh.due_on < getdate()) then 'overdue' else cnh.paid_status end) AS paid_status
	,cast(cnh.due_on as date) AS due_on, cnh.term_id AS term_id, cnh.account_id AS account_id, cnh.notes AS notes, cnh.total AS total
	,concat(isnull(cnh.prefix,''),cnh.credit_note_number,isnull(cnh.suffix,'')) AS credit_note_number_full
	,concat(adl.name,' - ',adl.currencyCode) AS client_account, cnh.exchange_rate AS exchange_rate, cnh.bill_to AS bill_to, adl.name AS account_name,adl.currencyCode AS client_currency, adl.currency_id AS currency_id, adl.member_id AS member_id
	,adl.fullName AS client_name,adl.model_id AS model_id,adl.model_name AS model_type
	,(select sum(credit_note_refunds.credit_note_refund_total) from credit_note_refunds where (credit_note_refunds.credit_note_header_id = cnh.id)) AS refunds_made
	,(select sum(credit_note_invoices.total) from credit_note_invoices where (credit_note_invoices.credit_note_header_id = cnh.id)) AS invoices_close
	,(cnh.total - isnull((select sum(credit_note_refunds.credit_note_refund_total) from credit_note_refunds where (credit_note_refunds.credit_note_header_id = cnh.id)),0.0)
		- isnull((select sum(credit_note_invoices.total) from credit_note_invoices where (credit_note_invoices.credit_note_header_id = cnh.id)),0.0) ) AS balance_due
	,cnh.created_on AS created_on,cnh.created_by AS created_by,cnh.modified_on AS modified_on,cnh.modified_by AS modified_by
	,concat(created.firstName,' ',created.lastName) AS created_by_name,concat(modified.firstName,' ',modified.lastName) AS modified_by_name,cnh.display_tax AS display_tax, cnh.display_item_date AS display_item_date
	,cnh.display_discount AS display_discount,cnh.discount_percentage AS discount_percentage, cnh.discount_amount AS discount_amount
	,case_id = STUFF((
      SELECT concat(',', credit_note_related_cases.legal_case_id) FROM credit_note_related_cases 
	  WHERE credit_note_related_cases.credit_note_header_id = cnh.id FOR XML PATH('')
    ), 1, 1, '')
	,case_subject = STUFF((
      SELECT concat(':/;', legal_cases.subject) FROM credit_note_related_cases 
		INNER JOIN legal_cases ON legal_cases.id = credit_note_related_cases.legal_case_id 
	  WHERE credit_note_related_cases.credit_note_header_id = cnh.id FOR XML PATH('')
    ), 1, 3, '')
	,assignee = STUFF((
      SELECT concat(',', user_profiles.firstName,' ',user_profiles.lastName) FROM credit_note_related_cases 
		INNER JOIN legal_cases ON legal_cases.id = credit_note_related_cases.legal_case_id 
		LEFT JOIN users ON users.id = legal_cases.user_id 
        LEFT JOIN user_profiles ON user_profiles.user_id = users.id 
	  WHERE credit_note_related_cases.credit_note_header_id = cnh.id FOR XML PATH('')
    ), 1, 1, '')
	,case_internal_reference = STUFF((
      SELECT concat(',', legal_cases.internalReference) FROM credit_note_related_cases 
		INNER JOIN legal_cases ON legal_cases.id = credit_note_related_cases.legal_case_id 
	  WHERE credit_note_related_cases.credit_note_header_id = cnh.id FOR XML PATH('')
    ), 1, 1, '')
  ,invoice_credited = STUFF((
      SELECT concat(',', isnull(inv_hdr.prefix, ''),inv_vhcr.refNum,isnull(inv_hdr.suffix, '')) FROM credit_note_invoices 
		inner join invoice_headers inv_hdr on inv_hdr.id = credit_note_invoices.invoice_header_id 
		inner join voucher_headers inv_vhcr on inv_vhcr.id = inv_hdr.voucher_header_id
	  WHERE credit_note_invoices.credit_note_header_id = cnh.id FOR XML PATH('')
    ), 1, 1, '')
	,practice_area = STUFF((
      SELECT concat(',', case_types.name) FROM credit_note_related_cases 
		INNER JOIN legal_cases ON legal_cases.id = credit_note_related_cases.legal_case_id 
		LEFT JOIN case_types ON case_types.id = legal_cases.case_type_id 
	  WHERE credit_note_related_cases.credit_note_header_id = cnh.id FOR XML PATH('')
    ), 1, 1, '')
	,case_category = STUFF((
      SELECT concat(',', legal_cases.category) FROM credit_note_related_cases 
		INNER JOIN legal_cases ON legal_cases.id = credit_note_related_cases.legal_case_id 
	  WHERE credit_note_related_cases.credit_note_header_id = cnh.id FOR XML PATH('')
    ), 1, 1, '')
	,adl.account_number AS account_number
	,(select sum((credit_note_details.line_sub_total)) from credit_note_details where (credit_note_details.credit_note_header_id = cnh.id)) AS sub_total
	,round((cnh.lines_total_discount + cnh.discount_amount), 2) AS total_discount
	,round(cnh.lines_total_tax, 2) AS total_tax
	,(select sum(round((case when (credit_note_details.tax_percentage > 0) then ((((credit_note_details.line_sub_total) - (case when (crdtnh.display_discount = 'item_level') then (case when (credit_note_details.discount_percentage is not null) then (((credit_note_details.line_sub_total) * isnull(credit_note_details.discount_percentage,0)) / 100) else (case when (credit_note_details.discount_amount is not null) then (credit_note_details.discount_amount * 1) else 0 end) end) else (case when (crdtnh.display_discount = 'invoice_level_before_tax') then (((credit_note_details.line_sub_total) * isnull(crdtnh.discount_percentage,0)) / 100) else 0 end) end)) * isnull(credit_note_details.tax_percentage,0)) / credit_note_details.tax_percentage) else 0 end),2)) 
		from credit_note_details inner join credit_note_headers crdtnh on (credit_note_details.credit_note_header_id = crdtnh.id) where crdtnh.id = cnh.id) AS taxable
	,(((select sum(credit_note_details.line_sub_total) 
		from credit_note_details inner join credit_note_headers crdtnh on (credit_note_details.credit_note_header_id = crdtnh.id) where crdtnh.id = cnh.id)
		- (select sum(round((case when (crdtnh.display_discount = 'item_level') then (case when (credit_note_details.discount_percentage is not null) then (((credit_note_details.line_sub_total) * isnull(credit_note_details.discount_percentage,0)) / 100) else (case when (credit_note_details.discount_amount is not null) then (credit_note_details.discount_amount * 1) else 0 end) end) else (case when (crdtnh.display_discount = 'invoice_level_before_tax') then (((credit_note_details.line_sub_total) * isnull(crdtnh.discount_percentage,0)) / 100) else 0 end) end),2)) 
		from credit_note_details inner join credit_note_headers crdtnh on (credit_note_details.credit_note_header_id = crdtnh.id) where crdtnh.id = cnh.id)
		- (select sum(round((case when (credit_note_details.tax_percentage > 0) then ((((credit_note_details.line_sub_total) - (case when (crdtnh.display_discount = 'item_level') then (case when (credit_note_details.discount_percentage is not null) then (((credit_note_details.line_sub_total) * isnull(credit_note_details.discount_percentage,0)) / 100) else (case when (credit_note_details.discount_amount is not null) then (credit_note_details.discount_amount * 1) else 0 end) end) else (case when (crdtnh.display_discount = 'invoice_level_before_tax') then (((credit_note_details.line_sub_total) * isnull(crdtnh.discount_percentage,0)) / 100) else 0 end) end)) * isnull(credit_note_details.tax_percentage,0)) / credit_note_details.tax_percentage) else 0 end),2)) 
		from credit_note_details inner join credit_note_headers crdtnh on (credit_note_details.credit_note_header_id = crdtnh.id) where crdtnh.id = cnh.id))) AS non_taxable
	,(cnh.lines_total_subtotal - cnh.lines_total_discount - (CASE WHEN cnh.display_discount = 'invoice_level_before_tax' OR cnh.display_discount = 'both_item_before_level' THEN cnh.discount_amount ELSE 0 END)) AS sub_total_after_discount 
	,adl.tax_number as tax_number, cnh.lines_total_discount, cnh.lines_total_subtotal, cnh.lines_total_tax, cnh.lines_totals
from credit_note_headers cnh
	left join voucher_headers on cnh.voucher_header_id = voucher_headers.id
	inner join accounts_details_lookup adl on adl.id = cnh.account_id
	inner join user_profiles created on created.user_id = cnh.created_by 
	inner join user_profiles modified on modified.user_id = cnh.modified_by
;

GO 

IF OBJECT_ID('dbo.invoices_full_details', 'V') IS NOT NULL DROP VIEW dbo.invoices_full_details;
GO 

CREATE VIEW invoices_full_details AS 
 SELECT voucher_headers.id,
       voucher_headers.organization_id,
       voucher_headers.dated,
       voucher_headers.voucherType,
       voucher_headers.refNum,
       voucher_headers.referenceNum,
       voucher_headers.attachment,
       voucher_headers.description,
       ih.prefix                                                                                                                AS prefix,
       ih.suffix                                                                                                                AS suffix,
       CASE
         WHEN ( ih.paidStatus <> 'paid'
                AND ih.paidStatus <> 'draft'
                AND ih.paidStatus <> 'cancelled'
                AND ih.dueOn < Getdate() ) THEN 'overdue'
         ELSE ih.paidStatus
       END                                                                                                                      AS paidStatus,
       ih.purchaseOrder                                                                                                         AS purchaseOrder,
       ih.dueOn                                                                                                                 AS dueOn,
       ih.account_id                                                                                                            AS accountID,
       ih.total                                                                                                                 AS total,
       CASE
         WHEN ih.suffix is null THEN ( ih.prefix
                                       + Cast( voucher_headers.refNum AS nvarchar ) )
         ELSE ( ih.prefix
                + Cast( voucher_headers.refNum AS nvarchar )
                + ih.suffix )
       END                                                                                                                      AS invoiceId,
       ( adl.name + ' - ' + adl.currencyCode )                                                                                  AS clientAccount,
       adl.name                                                                                                                 AS accountName,
       adl.currencyCode                                                                                                         AS clientCurrency,
       adl.fullName                                                                                                             AS clientName,
       adl.member_id                                                                                                            AS member_id,
       adl.model_id,
       adl.model_name                                                                                                           AS model_type,
       IPI.payemntsMade                                                                                                         AS payemntsMade,
       ICN.totalCreditNotes                                                                                                     AS totalCreditNotes,
       total - Isnull(IPI.payemntsMade, 0.0) - Isnull(ICN.totalCreditNotes, 0.0)                                                AS balanceDue,
       voucher_headers.createdOn,
       voucher_headers.createdBy,
       voucher_headers.modifiedOn,
       voucher_headers.modifiedBy,
       ( created.firstName + ' ' + created.lastName )                                                                           AS createdByName,
       ( modified.firstName + ' ' + modified.lastName )                                                                         AS modifiedByName,
       displayTax,
       displayDiscount,
       case_id = Stuff((SELECT ','
                               + Cast( voucher_related_cases.legal_case_id AS nvarchar )
                        FROM   voucher_related_cases
                        WHERE  voucher_related_cases.voucher_header_id = voucher_headers.id
                        FOR XML PATH('')), 1, 1, ''),
       caseSubject = Stuff((SELECT ':/;'
                                   + Cast(legal_cases.subject AS nvarchar)
                            FROM   voucher_related_cases
                                   INNER JOIN legal_cases
                                           ON legal_cases.id = voucher_related_cases.legal_case_id
                            WHERE  voucher_related_cases.voucher_header_id = voucher_headers.id
                            FOR XML PATH('')), 1, 3, ''),
       assignee = Stuff((SELECT ':/;'
                                + Cast( user_profiles.firstName AS nvarchar )
                                + ' '
                                + Cast( user_profiles.lastName AS nvarchar )
                         FROM   voucher_related_cases
                                INNER JOIN legal_cases
                                        ON legal_cases.id = voucher_related_cases.legal_case_id
                                LEFT JOIN users
                                       ON users.id = legal_cases.user_id
                                LEFT JOIN user_profiles
                                       ON user_profiles.user_id = users.id
                         WHERE  voucher_related_cases.voucher_header_id = voucher_headers.id
                         FOR XML PATH('')), 1, 3, ''),
       caseCategory = Stuff((SELECT ','
                                    + Cast(legal_cases.category AS nvarchar)
                             FROM   voucher_related_cases
                                    INNER JOIN legal_cases
                                            ON legal_cases.id = voucher_related_cases.legal_case_id
                             WHERE  voucher_related_cases.voucher_header_id = voucher_headers.id
                             FOR XML PATH('')), 1, 1, ''),
       caseInternalReference = Stuff((SELECT ','
                                             + Cast( legal_cases.internalReference AS nvarchar )
                                      FROM   voucher_related_cases
                                             INNER JOIN legal_cases
                                                     ON legal_cases.id = voucher_related_cases.legal_case_id
                                      WHERE  voucher_related_cases.voucher_header_id = voucher_headers.id
                                      FOR XML PATH('')), 1, 1, ''),
       adl.account_number,
       practiceArea = Stuff((SELECT ',' + Cast(case_types.name AS nvarchar)
                             FROM   voucher_related_cases
                                    INNER JOIN legal_cases
                                            ON legal_cases.id = voucher_related_cases.legal_case_id
                                    LEFT JOIN case_types
                                           ON case_types.id = legal_cases.case_type_id
                             WHERE  voucher_related_cases.voucher_header_id = voucher_headers.id
                             FOR XML PATH('')), 1, 1, ''),
       (SELECT ( Sum(line_sub_total) )
        FROM   invoice_details
        WHERE  invoice_header_id = ih.id)  AS subTotal,
       ROUND((ih.lines_total_discount + ih.discount_amount), 2)  AS totalDiscount,
       ROUND(ih.lines_total_tax, 2)  AS totalTax,
       (SELECT ( Sum(Round(CASE
                             WHEN percentage > 0 THEN( (( ( line_sub_total - CASE
                                                                                         WHEN inv_hd.displayDiscount = 'item_level' THEN( CASE
                                                                                                                                            WHEN discountPercentage IS NOT NULL THEN ( ( line_sub_total *
                                                                                                                                                                                         ISNULL(discountPercentage, 0)
                                                                                                                                                                                       ) /
                                                                                                                                                                                       100 )
                                                                                                                                            ELSE
                                                                                                                                              CASE
                                                                                                                                                WHEN discountAmount IS NOT NULL THEN ( discountAmount * 1 )
                                                                                                                                                ELSE 0
                                                                                                                                              END
                                                                                                                                          END )
                                                                                         ELSE
                                                                                           CASE
                                                                                             WHEN inv_hd.displayDiscount = 'invoice_level_before_tax' THEN ( ( line_sub_total * ISNULL(inv_hd.discount_percentage, 0) ) / 100 )
                                                                                             ELSE 0
                                                                                           END
                                                                                       END ) * ISNULL(percentage, 0) )) / percentage )
                             ELSE 0
                           END, 2)) )
        FROM   invoice_details,
               invoice_headers inv_hd
        WHERE  inv_hd.id = invoice_details.invoice_header_id
               AND invoice_header_id = ih.id)                                                                                   AS taxable,
       ( (SELECT ( Sum(line_sub_total) )
          FROM   invoice_details
          WHERE  invoice_header_id = ih.id) - (SELECT ( Sum(Round(CASE
                                                                    WHEN inv_hd.displayDiscount = 'item_level' THEN( CASE
                                                                                                                       WHEN discountPercentage IS NOT NULL THEN ( ( line_sub_total * ISNULL(discountPercentage, 0)
                                                                                                                                                                  ) /
                                                                                                                                                                  100
                                                                                                                                                                )
                                                                                                                       ELSE
                                                                                                                         CASE
                                                                                                                           WHEN discountAmount IS NOT NULL THEN ( discountAmount * 1 )
                                                                                                                           ELSE 0
                                                                                                                         END
                                                                                                                     END )
                                                                    ELSE
                                                                      CASE
                                                                        WHEN inv_hd.displayDiscount = 'invoice_level_before_tax' THEN ( ( line_sub_total * ISNULL(inv_hd.discount_percentage, 0) ) / 100 )
                                                                        ELSE 0
                                                                      END
                                                                  END, 2)) )
                                               FROM   invoice_details,
                                                      invoice_headers inv_hd
                                               WHERE  inv_hd.id = invoice_details.invoice_header_id
                                                      AND invoice_header_id = ih.id) - (SELECT ( Sum(Round(CASE
                                                                                                             WHEN percentage > 0 THEN( (( ( ( line_sub_total ) - CASE
                                                                                                                                                                         WHEN inv_hd.displayDiscount = 'item_level' THEN(
                                                                                                                                                                         CASE
                                                                                                                                                                           WHEN
                                                                                                                                                                         discountPercentage IS NOT NULL THEN
                                                                                                                                                                         ( ( (
                                                                                                                                                                         line_sub_total ) *
                                                                                                                                                                             ISNULL(discountPercentage, 0)
                                                                                                                                                                                                               ) /
                                                                                                                                                                                                               100 )
                                                                                                                                                                         ELSE
                                                                                                                                                                           CASE
                                                                                                                                                                         WHEN
                                                                                                                                                                         discountAmount IS NOT NULL THEN (
                                                                                                                                                                         discountAmount * 1 )
                                                                                                                                                                         ELSE
                                                                                                                                                                         0
                                                                                                                                                                           END
                                                                                                                                                                         END )
                                                                                                                                                                         ELSE
                                                                                                                                                                           CASE
                                                                                                                                                                             WHEN inv_hd.displayDiscount = 'invoice_level_before_tax'
                                                                                                                                                                           THEN ( (
                                                                                                                                                                             line_sub_total *
                                                                                                                                                                             ISNULL(inv_hd.discount_percentage, 0) ) / 100 )
                                                                                                                                                                             ELSE 0
                                                                                                                                                                           END
                                                                                                                                                                       END ) * ISNULL(percentage, 0) )) / percentage )
                                                                                                             ELSE 0
                                                                                                           END, 2)) )
                                                                                        FROM   invoice_details,
                                                                                               invoice_headers inv_hd
                                                                                        WHERE  inv_hd.id = invoice_details.invoice_header_id
                                                                                               AND invoice_header_id = ih.id) ) AS nonTaxable,
        (ih.lines_total_subtotal - ih.lines_total_discount - (CASE WHEN ih.displayDiscount = 'invoice_level_before_tax' OR ih.displayDiscount = 'both_item_before_level' THEN ih.discount_amount ELSE 0 END))  AS sub_total_after_discount,
        ih.lines_total_discount, ih.lines_total_subtotal, ih.lines_total_tax, ih.lines_totals
FROM   voucher_headers
       INNER JOIN invoice_headers AS ih
               ON ih.voucher_header_id = voucher_headers.id
       LEFT JOIN (SELECT invoice_payment_invoices.invoice_header_id,
                         Sum(invoice_payment_invoices.amount)              AS payemntsMade
                  FROM   invoice_payment_invoices
                  GROUP  BY invoice_payment_invoices.invoice_header_id) AS IPI
              ON IPI.invoice_header_id = ih.id
       LEFT JOIN (SELECT credit_note_invoices.invoice_header_id,
                         Sum(credit_note_invoices.total)              AS totalCreditNotes
                  FROM   credit_note_invoices
				  inner join credit_note_headers on credit_note_headers.id = credit_note_invoices.credit_note_header_id
				  where credit_note_headers.paid_status in ('open', 'partially refund', 'refund')
                  GROUP  BY credit_note_invoices.invoice_header_id) AS ICN
              ON ICN.invoice_header_id = ih.id
       INNER JOIN accounts_details_lookup adl
               ON adl.id = ih.account_id
       LEFT JOIN user_profiles created
              ON created.user_id = voucher_headers.createdBy
       LEFT JOIN user_profiles modified
              ON modified.user_id = voucher_headers.modifiedBy;

GO 

-- Foreign Keys

ALTER TABLE company_signature_authorities
 ADD CONSTRAINT company_signature_authorities_ibfk_1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE board_members
 ADD CONSTRAINT fk_board_members_board_member_roles1 FOREIGN KEY (board_member_role_id) REFERENCES board_member_roles (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE board_members
 ADD CONSTRAINT fk_board_members_companies1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE board_members
 ADD CONSTRAINT fk_board_members_lookup_members1 FOREIGN KEY (member_id) REFERENCES lookup_members (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE case_comments
 ADD CONSTRAINT fk_case_comments_legal_cases1 FOREIGN KEY (case_id) REFERENCES legal_cases (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE companies
 ADD CONSTRAINT fk_companies_companies1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE companies
 ADD CONSTRAINT fk_companies_company_legal_types1 FOREIGN KEY (company_legal_type_id) REFERENCES company_legal_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE companies
 ADD CONSTRAINT fk_companies_contacts2 FOREIGN KEY (registrationByLawNotaryPublic) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE companies
 ADD CONSTRAINT fk_companies_companies2 FOREIGN KEY (registrationAuthority) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE companies
 ADD CONSTRAINT fk_companies_contact_company_categories FOREIGN KEY (company_category_id) REFERENCES contact_company_categories (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE companies
 ADD CONSTRAINT fk_companies_contact_company_sub_categories FOREIGN KEY (company_sub_category_id) REFERENCES contact_company_sub_categories (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE companies_contacts
 ADD CONSTRAINT fk_companies_contacts_companies1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE companies_contacts
 ADD CONSTRAINT fk_companies_contacts_contacts1 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE company_assets
 ADD CONSTRAINT company_assets_companies_ibfk_1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE company_assets
 ADD CONSTRAINT company_assets_company_asset_types_ibfk_1 FOREIGN KEY (company_asset_type_id) REFERENCES company_asset_types (id);
GO
ALTER TABLE company_auditors
 ADD CONSTRAINT fk_company_auditors_companies1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE company_changes
 ADD CONSTRAINT fk_company_changes_companies1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE company_documents
 ADD CONSTRAINT company_documents_ibfk_1 FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE company_documents
 ADD CONSTRAINT company_documents_ibfk_2 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE company_documents
 ADD CONSTRAINT company_documents_ibfk_3 FOREIGN KEY (company_document_status_id) REFERENCES company_document_statuses (id);
GO
ALTER TABLE company_documents
 ADD CONSTRAINT company_documents_ibfk_4 FOREIGN KEY (company_document_type_id) REFERENCES company_document_types (id);
GO
ALTER TABLE company_documents
 ADD CONSTRAINT fk_company_documents_companies1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contacts
 ADD CONSTRAINT fk_contacts_contact_company_categories FOREIGN KEY (contact_category_id) REFERENCES contact_company_categories (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contacts
 ADD CONSTRAINT fk_contacts_contact_company_sub_categories FOREIGN KEY (contact_sub_category_id) REFERENCES contact_company_sub_categories (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contacts
 ADD CONSTRAINT fk_contacts_manager FOREIGN KEY (manager_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contact_documents
 ADD CONSTRAINT contact_documents_ibfk_1 FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contact_documents
 ADD CONSTRAINT contact_documents_ibfk_2 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contact_documents
 ADD CONSTRAINT contact_documents_ibfk_3 FOREIGN KEY (contact_document_status_id) REFERENCES contact_document_statuses (id);
GO
ALTER TABLE contact_documents
 ADD CONSTRAINT contact_documents_ibfk_4 FOREIGN KEY (contact_document_type_id) REFERENCES contact_document_types (id);
GO
ALTER TABLE contact_documents
 ADD CONSTRAINT fk_contact_documents_contacts1 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_cases
 ADD CONSTRAINT fk_legal_cases_workflow_status1 FOREIGN KEY (case_status_id) REFERENCES workflow_status (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_cases
 ADD CONSTRAINT fk_legal_cases_case_types1 FOREIGN KEY (case_type_id) REFERENCES case_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_cases
 ADD CONSTRAINT fk_legal_cases_contacts1 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_cases
 ADD CONSTRAINT fk_legal_cases_provider_groups1 FOREIGN KEY (provider_group_id) REFERENCES provider_groups (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_cases
 ADD CONSTRAINT fk_legal_cases_users1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_cases
ADD CONSTRAINT fk_legal_cases_9 FOREIGN KEY (stage) REFERENCES legal_case_litigation_details (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_cases_companies
 ADD CONSTRAINT fk_legal_cases_companies_companies1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_cases_companies
 ADD CONSTRAINT fk_legal_cases_companies_legal_cases1 FOREIGN KEY (case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_cases_companies
 ADD CONSTRAINT fk_legal_cases_companies_legal_case_company_roles1 FOREIGN KEY (legal_case_company_role_id) REFERENCES legal_case_company_roles (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_cases_contacts
 ADD CONSTRAINT fk_legal_cases_contacts_contacts1 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_cases_contacts
 ADD CONSTRAINT fk_legal_cases_contacts_legal_cases1 FOREIGN KEY (case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_cases_contacts
 ADD CONSTRAINT fk_legal_cases_contacts_legal_case_contact_roles1 FOREIGN KEY (legal_case_contact_role_id) REFERENCES legal_case_contact_roles (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_archived_hard_copies
 ADD CONSTRAINT fk_legal_case_archived_hard_copies_case_document_classificati1 FOREIGN KEY (case_document_classification_id) REFERENCES case_document_classifications (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_archived_hard_copies
 ADD CONSTRAINT fk_legal_case_archived_hard_copies_case_document_classificati2 FOREIGN KEY (sub_case_document_classification_id) REFERENCES case_document_classifications (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_archived_hard_copies
 ADD CONSTRAINT fk_legal_case_archived_hard_copies_legal_cases1 FOREIGN KEY (case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_stage_changes
 ADD CONSTRAINT legal_case_stage_changes_ibfk_2 FOREIGN KEY (legal_case_stage_id) REFERENCES legal_case_stages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_stage_changes
 ADD CONSTRAINT legal_case_stage_changes_ibfk_1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_changes
 ADD CONSTRAINT fk_legal_case_changes_legal_cases1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_documents
 ADD CONSTRAINT fk_legal_case_documents_legal_cases1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_documents
 ADD CONSTRAINT legal_case_documents_ibfk_1 FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_documents
 ADD CONSTRAINT legal_case_documents_ibfk_2 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_documents
 ADD CONSTRAINT legal_case_documents_ibfk_3 FOREIGN KEY (legal_case_document_status_id) REFERENCES case_document_statuses (id);
GO
ALTER TABLE legal_case_documents
 ADD CONSTRAINT legal_case_documents_ibfk_4 FOREIGN KEY (legal_case_document_type_id) REFERENCES case_document_types (id);
GO
ALTER TABLE legal_case_users
 ADD CONSTRAINT legal_case_users_ibfk_1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE legal_case_users
 ADD CONSTRAINT legal_case_users_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE lookup_members
 ADD CONSTRAINT fk_lookup_members_companies1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE lookup_members
 ADD CONSTRAINT fk_lookup_members_contacts1 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE notifications
 ADD CONSTRAINT notifications_users_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id);
GO
ALTER TABLE notifications
 ADD CONSTRAINT notifications_users_ibfk_2 FOREIGN KEY (createdBy) REFERENCES users (id);
GO
ALTER TABLE notifications
 ADD CONSTRAINT notifications_users_ibfk_3 FOREIGN KEY (modifiedBy) REFERENCES users (id);
GO
ALTER TABLE planning_boards
 ADD FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE planning_boards
 ADD FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE planning_board_columns
 ADD CONSTRAINT fk_planning_board_columns_planning_boards1 FOREIGN KEY (planning_board_id) REFERENCES planning_boards (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE planning_board_column_options
 ADD CONSTRAINT fk_planning_board_column_options_workflow_status1 FOREIGN KEY (case_status_id) REFERENCES workflow_status (id);
GO
ALTER TABLE planning_board_column_options
 ADD CONSTRAINT fk_planning_board_column_options_planning_boards1 FOREIGN KEY (planning_board_id) REFERENCES planning_boards (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE planning_board_column_options
 ADD CONSTRAINT fk_planning_board_column_options_planning_board_columns1 FOREIGN KEY (planning_board_column_id) REFERENCES planning_board_columns (id) ON DELETE  NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE preferred_shares
 ADD CONSTRAINT fk_preferred_shares_companies1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE provider_groups_users
 ADD CONSTRAINT fk_provider_groups_users_provider_groups1 FOREIGN KEY (provider_group_id) REFERENCES provider_groups (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE provider_groups_users
 ADD CONSTRAINT fk_provider_groups_users_users1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE related_cases
 ADD CONSTRAINT fk_related_cases_legal_cases1 FOREIGN KEY (case_a_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE related_cases
 ADD CONSTRAINT fk_related_cases_legal_cases2 FOREIGN KEY (case_b_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE related_contacts
 ADD CONSTRAINT fk_related_contacts_contacts1 FOREIGN KEY (contact_a_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE related_contacts
 ADD CONSTRAINT fk_related_contacts_contacts2 FOREIGN KEY (contact_b_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE reminders
 ADD CONSTRAINT reminders_companies_ibfk_1 FOREIGN KEY (company_id) REFERENCES companies (id);
GO
ALTER TABLE reminders
 ADD CONSTRAINT reminders_contacts_ibfk_1 FOREIGN KEY (contact_id) REFERENCES contacts (id);
GO
ALTER TABLE reminders
 ADD CONSTRAINT reminders_legal_cases_ibfk_1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id);
GO
ALTER TABLE reminders
 ADD CONSTRAINT reminders_reminder_types_ibfk_1 FOREIGN KEY (reminder_type_id) REFERENCES reminder_types (id);
GO
ALTER TABLE reminders
 ADD CONSTRAINT reminders_tasks_ibfk_1 FOREIGN KEY (task_id) REFERENCES tasks (id);
GO
ALTER TABLE reminders
 ADD CONSTRAINT reminders_users_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id);
GO
ALTER TABLE reminders
 ADD CONSTRAINT reminders_legal_case_hearings_ibfk_1 FOREIGN KEY ( legal_case_hearing_id ) REFERENCES legal_case_hearings (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE reminders
    ADD CONSTRAINT fk_reminders_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE shares_movements
 ADD CONSTRAINT fk_shares_movements_lookup_members1 FOREIGN KEY (member_id) REFERENCES lookup_members (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE shares_movements
 ADD CONSTRAINT fk_shares_movements_shares_movement_headers1 FOREIGN KEY (shares_movement_header_id) REFERENCES shares_movement_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE tasks
 ADD CONSTRAINT fk_tasks_1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE tasks
 ADD CONSTRAINT fk_tasks_2 FOREIGN KEY (task_status_id) REFERENCES task_statuses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE tasks
 ADD CONSTRAINT fk_tasks_3 FOREIGN KEY (task_type_id) REFERENCES task_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE tasks
 ADD CONSTRAINT fk_tasks_4 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE tasks
 ADD CONSTRAINT fk_tasks_5 FOREIGN KEY (assigned_to) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE tasks
 ADD CONSTRAINT fk_tasks_6 FOREIGN KEY (reporter) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE tasks
 ADD CONSTRAINT fk_tasks_7 FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE tasks
 ADD CONSTRAINT fk_tasks_8 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE task_boards
 ADD FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE task_boards
 ADD FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE task_board_columns
 ADD CONSTRAINT fk_task_board_columns_task_boards1 FOREIGN KEY (task_board_id) REFERENCES task_boards (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE task_board_column_options
 ADD CONSTRAINT fk_task_board_column_options_task_boards1 FOREIGN KEY (task_board_id) REFERENCES task_boards (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE task_board_column_options
 ADD CONSTRAINT fk_task_board_column_options_task_board_columns1 FOREIGN KEY (task_board_column_id) REFERENCES task_board_columns (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE task_board_column_options
 ADD CONSTRAINT fk_task_board_column_options_task_statuses1 FOREIGN KEY (task_status_id) REFERENCES task_statuses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE task_users
 ADD CONSTRAINT task_users_ibfk_1 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE task_users
 ADD CONSTRAINT task_users_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contact_users
 ADD CONSTRAINT contact_users_ibfk_1 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE contact_users
 ADD CONSTRAINT contact_users_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE company_users
 ADD CONSTRAINT company_users_ibfk_1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE company_users
 ADD CONSTRAINT company_users_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE document_managment_users
 ADD CONSTRAINT document_managment_users_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE users
 ADD CONSTRAINT users_ibfk_1 FOREIGN KEY (user_group_id) REFERENCES user_groups (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE users
 ADD CONSTRAINT users_ibfk_2 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE users ADD CONSTRAINT users_unique_email UNIQUE(email)
GO
ALTER TABLE user_activity_logs
 ADD CONSTRAINT user_activity_logs_legal_cases_ibfk_1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON UPDATE CASCADE;
GO
ALTER TABLE user_activity_logs
 ADD CONSTRAINT user_activity_logs_tasks_ibfk_1 FOREIGN KEY (task_id) REFERENCES tasks (id) ON UPDATE CASCADE;
GO
ALTER TABLE user_activity_logs
 ADD CONSTRAINT user_activity_logs_users_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE CASCADE;
GO
ALTER TABLE user_activity_logs
 ADD CONSTRAINT user_activity_logs_time_types_ibfk_1 FOREIGN KEY (time_type_id) REFERENCES time_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_activity_logs
 ADD CONSTRAINT user_activity_logs_time_internal_statuses_ibfk_1 FOREIGN KEY (time_internal_status_id) REFERENCES time_internal_statuses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_activity_logs
 ADD CONSTRAINT fk_user_activity_logs_5 FOREIGN KEY (client_id) REFERENCES clients (id) ON UPDATE CASCADE;
GO
ALTER TABLE user_group_permissions
 ADD CONSTRAINT user_group_permissions_ibfk_1 FOREIGN KEY (user_group_id) REFERENCES user_groups (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_passwords
 ADD CONSTRAINT user_passwords_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE user_preferences
 ADD CONSTRAINT fk_user_preferences_users1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_profiles
 ADD CONSTRAINT fk_user_profiles_users FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_profiles
 ADD CONSTRAINT fk1_user_profiles_users FOREIGN KEY (seniority_level_id) REFERENCES seniority_levels (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE custom_field_values
  ADD CONSTRAINT fk_custom_field_values FOREIGN KEY (custom_field_id) REFERENCES custom_fields (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_litigation_details
  ADD CONSTRAINT fk_legal_case_litigation_details1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_litigation_details
  ADD CONSTRAINT fk_legal_case_litigation_details2 FOREIGN KEY (court_id) REFERENCES courts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_litigation_details
  ADD CONSTRAINT fk_legal_case_litigation_details3 FOREIGN KEY (court_type_id) REFERENCES court_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_litigation_details
  ADD CONSTRAINT fk_legal_case_litigation_details4 FOREIGN KEY (court_degree_id) REFERENCES court_degrees (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_litigation_details
  ADD CONSTRAINT fk_legal_case_litigation_details5 FOREIGN KEY (court_region_id) REFERENCES court_regions (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_litigation_details
ADD CONSTRAINT fk_legal_case_litigation_details_6 FOREIGN KEY ( legal_case_stage ) REFERENCES legal_case_stages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_litigation_details
ADD CONSTRAINT fk_legal_case_litigation_details_7 FOREIGN KEY ( client_position ) REFERENCES legal_case_client_positions (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_litigation_details
ADD CONSTRAINT fk_legal_case_litigation_details_8 FOREIGN KEY ( status ) REFERENCES stage_statuses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_hearings
  ADD CONSTRAINT legal_case_hearings_ibfk_1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_hearings
  ADD CONSTRAINT legal_case_hearings_ibfk_2 FOREIGN KEY (task_id) REFERENCES events (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_hearings
ADD CONSTRAINT fk_legal_case_hearings_3 FOREIGN KEY ( type ) REFERENCES hearing_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_hearings
ADD CONSTRAINT fk_legal_case_hearings_4 FOREIGN KEY ( stage ) REFERENCES legal_case_litigation_details (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_hearings_users
  ADD CONSTRAINT fk_legal_case_hearings_users1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_hearings_users
  ADD CONSTRAINT fk_legal_case_hearings_users2 FOREIGN KEY (legal_case_hearing_id) REFERENCES legal_case_hearings (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contact_nationalities
  ADD CONSTRAINT fk_contact_nationalities1 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contact_nationalities
  ADD CONSTRAINT fk_contact_nationalities2 FOREIGN KEY (nationality_id) REFERENCES countries (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_containers
  ADD CONSTRAINT fk_legal_case_containers1 FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_containers
  ADD CONSTRAINT fk_legal_case_containers2 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_containers
  ADD CONSTRAINT fk_legal_case_containers3 FOREIGN KEY (case_type_id) REFERENCES case_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_containers
  ADD CONSTRAINT fk_legal_case_containers4 FOREIGN KEY (provider_group_id) REFERENCES provider_groups (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_containers
  ADD CONSTRAINT fk_legal_case_containers5 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE clients
  ADD CONSTRAINT fk_clients_contacts1 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE clients
  ADD CONSTRAINT fk_clients_companies1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE clients
  ADD CONSTRAINT clients_users_createdBy FOREIGN KEY (createdBy) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE clients
  ADD CONSTRAINT clients_users_modifiedBy FOREIGN KEY (modifiedBy) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO

ALTER TABLE company_bank_accounts
  ADD CONSTRAINT company_bank_accounts_ibfk_1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE user_changes
  ADD CONSTRAINT user_changes_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE user_changes
  ADD CONSTRAINT user_changes_ibfk_2 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_stage_languages
 ADD CONSTRAINT legal_case_stage_languages_ibfk_1 FOREIGN KEY (legal_case_stage_id) REFERENCES legal_case_stages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_stage_languages
 ADD CONSTRAINT legal_case_stage_languages_ibfk_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_cases
 ADD FOREIGN KEY (legal_case_stage_id) REFERENCES legal_case_stages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_client_position_languages
 ADD CONSTRAINT legal_case_client_position_languages_ibfk_1 FOREIGN KEY (legal_case_client_position_id) REFERENCES legal_case_client_positions (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_client_position_languages
 ADD CONSTRAINT legal_case_client_position_languages_ibfk_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_success_probability_languages
 ADD CONSTRAINT legal_case_success_probability_languages_ibfk_1 FOREIGN KEY (legal_case_success_probability_id) REFERENCES legal_case_success_probabilities (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_success_probability_languages
 ADD CONSTRAINT legal_case_success_probability_languages_ibfk_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_cases
 ADD FOREIGN KEY (legal_case_client_position_id) REFERENCES legal_case_client_positions (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_cases
 ADD FOREIGN KEY (legal_case_success_probability_id) REFERENCES legal_case_success_probabilities (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE login_history_logs
 ADD FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE login_history_log_archives
 ADD FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE user_reports
 ADD CONSTRAINT user_reports_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE shared_reports
 ADD CONSTRAINT shared_reports_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ;
 GO
 ALTER TABLE shared_reports
 ADD CONSTRAINT shared_reports_ibfk_2 FOREIGN KEY (report_id) REFERENCES user_reports (id)
GO

ALTER TABLE user_changes_authorization
  ADD CONSTRAINT user_changes_authorization_ibfk_1 FOREIGN KEY (affectedUserId) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_changes_authorization
  ADD CONSTRAINT user_changes_authorization_ibfk_2 FOREIGN KEY (makerId) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_changes_authorization
  ADD CONSTRAINT user_changes_authorization_ibfk_3 FOREIGN KEY (checkerId) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE user_groups_changes_authorization
  ADD CONSTRAINT user_groups_changes_authorization_ibfk_1 FOREIGN KEY (affectedUserGroupId) REFERENCES user_groups (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_groups_changes_authorization
  ADD CONSTRAINT user_groups_changes_authorization_ibfk_2 FOREIGN KEY (makerId) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_groups_changes_authorization
  ADD CONSTRAINT user_groups_changes_authorization_ibfk_3 FOREIGN KEY (checkerId) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE user_group_permissions_changes_authorization
  ADD CONSTRAINT user_group_permissions_changes_authorization_ibfk_1 FOREIGN KEY (affectedUserGroupId) REFERENCES user_groups (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_group_permissions_changes_authorization
  ADD CONSTRAINT user_group_permissions_changes_authorization_ibfk_2 FOREIGN KEY (makerId) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_group_permissions_changes_authorization
  ADD CONSTRAINT user_group_permissions_changes_authorization_ibfk_3 FOREIGN KEY (checkerId) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE planning_board_saved_filters
 ADD CONSTRAINT planning_board_saved_filters_ibfk_1 FOREIGN KEY (userId) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE planning_board_saved_filters
 ADD CONSTRAINT planning_board_saved_filters_ibfk_2 FOREIGN KEY (boardId) REFERENCES planning_boards (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE task_board_saved_filters
 ADD CONSTRAINT task_board_saved_filters_ibfk_1 FOREIGN KEY (userId) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE task_board_saved_filters
 ADD CONSTRAINT task_board_saved_filters_ibfk_2 FOREIGN KEY (boardId) REFERENCES task_boards (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE task_types_languages
 ADD CONSTRAINT fk_task_types_languages1 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE task_types_languages
 ADD CONSTRAINT fk_task_types_languages2 FOREIGN KEY (task_type_id) REFERENCES task_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE reminder_types_languages
 ADD CONSTRAINT fk_reminder_types_languages1 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE reminder_types_languages
 ADD CONSTRAINT fk_reminder_types_languages2 FOREIGN KEY (reminder_type_id) REFERENCES reminder_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE company_discharge_social_securities
 ADD CONSTRAINT fk_company_discharge_social_securities_companies1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE company_discharge_social_securities
 ADD CONSTRAINT fk_company_discharge_social_securities_company_type_of_discharges1 FOREIGN KEY (type_id) REFERENCES company_type_of_discharges (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE company_discharge_social_securities
 ADD CONSTRAINT fk_company_discharge_social_securities_users1 FOREIGN KEY (remind_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE company_discharge_social_securities
 ADD CONSTRAINT fk_company_discharge_social_securities_reminders1 FOREIGN KEY (reminder_id) REFERENCES reminders (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE titles_languages
 ADD CONSTRAINT fk_titles_languages1 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE titles_languages
 ADD CONSTRAINT fk_titles_languages2 FOREIGN KEY (title_id) REFERENCES titles (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE custom_fields_languages
 ADD CONSTRAINT fk_custom_fields_languages1 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE custom_fields_languages
 ADD CONSTRAINT fk_custom_fields_languages2 FOREIGN KEY (custom_field_id) REFERENCES custom_fields (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE company_lawyers
 ADD CONSTRAINT fk_company_lawyers_companies1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE company_lawyers
 ADD CONSTRAINT fk_company_lawyers_companies2 FOREIGN KEY (lawyer_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_opponents
 ADD CONSTRAINT fk_legal_case_opponents1 FOREIGN KEY (case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_opponents
 ADD CONSTRAINT fk_legal_case_opponents2 FOREIGN KEY (opponent_id) REFERENCES opponents (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE workflows
 ADD CONSTRAINT fk_workflows1 FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE workflows
 ADD CONSTRAINT fk_workflows2 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE workflow_status_transition
 ADD CONSTRAINT fk_workflow_status_transition1 FOREIGN KEY (workflow_id) REFERENCES workflows (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE grid_saved_filters
 ADD CONSTRAINT fk_grid_saved_filters1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE grid_saved_filters
 ADD CONSTRAINT fk_grid_saved_filters2 FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE grid_saved_filters
 ADD CONSTRAINT fk_grid_saved_filters3 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE grid_saved_filters_users
 ADD CONSTRAINT fk_grid_saved_filters_users1 FOREIGN KEY (filter_id) REFERENCES grid_saved_filters (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE grid_saved_filters_users
 ADD CONSTRAINT fk_grid_saved_filters_users2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE workflow_status_transition_history
 ADD CONSTRAINT fk_workflow_status_transition_history1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE workflow_status_transition_history
 ADD CONSTRAINT fk_workflow_status_transition_history2 FOREIGN KEY (fromStep) REFERENCES workflow_status (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE workflow_status_transition_history
 ADD CONSTRAINT fk_workflow_status_transition_history3 FOREIGN KEY (toStep) REFERENCES workflow_status (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_permissions
 ADD CONSTRAINT fk_customer_portal_permissions1 FOREIGN KEY (workflow_id) REFERENCES workflows (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_permissions
 ADD CONSTRAINT fk_customer_portal_permissions2 FOREIGN KEY (workflow_status_transition_id) REFERENCES workflow_status_transition (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_screen_fields
 ADD CONSTRAINT fk_customer_portal_screen_fields1 FOREIGN KEY (customer_portal_screen_id) REFERENCES customer_portal_screens (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_screen_field_languages
 ADD CONSTRAINT fk_customer_portal_screen_field_languages1 FOREIGN KEY (customer_portal_screen_field_id) REFERENCES customer_portal_screen_fields (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_screen_field_languages
 ADD CONSTRAINT fk_customer_portal_screen_field_languages2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_cases_countries_renewals
 ADD CONSTRAINT legal_cases_countries_renewals_ibfk_1 FOREIGN KEY (intellectual_property_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_sla
 ADD CONSTRAINT fk_customer_portal_sla1 FOREIGN KEY (workflow_id) REFERENCES workflows (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_sla
 ADD CONSTRAINT fk_customer_portal_sla2 FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_sla
 ADD CONSTRAINT fk_customer_portal_sla3 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_sla_cases
 ADD CONSTRAINT fk_customer_portal_sla_cases1 FOREIGN KEY (case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_sla_cases
 ADD CONSTRAINT fk_customer_portal_sla_cases3 FOREIGN KEY (customer_portal_sla_id) REFERENCES customer_portal_sla (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE companies_customer_portal_users
    ADD CONSTRAINT fk_companies_customer_portal_users_1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE companies_customer_portal_users
    ADD CONSTRAINT fk_companies_customer_portal_users_2 FOREIGN KEY (customer_portal_user_id) REFERENCES customer_portal_users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_ticket_watchers
    ADD CONSTRAINT fk_customer_portal_ticket_watchers_1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_ticket_watchers
    ADD CONSTRAINT fk_customer_portal_ticket_watchers_2 FOREIGN KEY (customer_portal_user_id) REFERENCES customer_portal_users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_container_watchers
    ADD CONSTRAINT fk_customer_portal_container_watchers_1 FOREIGN KEY (case_container_id) REFERENCES legal_case_containers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_container_watchers
    ADD CONSTRAINT fk_customer_portal_container_watchers_2 FOREIGN KEY (customer_portal_user_id) REFERENCES customer_portal_users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_cases_countries_renewals_users
  ADD CONSTRAINT fk_legal_cases_countries_renewals_users_1 FOREIGN KEY (legal_case_country_renewal_id) REFERENCES legal_cases_countries_renewals (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_cases_countries_renewals_users
  ADD CONSTRAINT fk_legal_cases_countries_renewals_users_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE expense_status_notes
 ADD CONSTRAINT fk_expense_status_notes_1 FOREIGN KEY (expense_id) REFERENCES expenses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE quote_status_notes
 ADD CONSTRAINT fk_quote_status_notes_1 FOREIGN KEY (quote_id) REFERENCES quote_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE quote_time_logs_items
 ADD CONSTRAINT fk_quote_time_logs_items_1 FOREIGN KEY (item) REFERENCES quote_details (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE quote_time_logs_items
 ADD CONSTRAINT fk_quote_time_logs_items_2 FOREIGN KEY (time_log) REFERENCES user_activity_logs (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE quote_time_logs_items
 ADD CONSTRAINT fk_quote_time_logs_items_3 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE quote_headers
  ADD CONSTRAINT fk_quote_headers_4 FOREIGN KEY (related_invoice_id) REFERENCES voucher_headers (id)ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE invoice_headers
  ADD CONSTRAINT fk_invoice_headers_4 FOREIGN KEY (related_quote_id) REFERENCES voucher_headers (id)ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE event_types_languages
 ADD CONSTRAINT fk_event_types_languages1 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE event_types_languages
 ADD CONSTRAINT fk_event_types_languages2 FOREIGN KEY (event_type_id) REFERENCES event_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE hearings_documents
 ADD CONSTRAINT fk_hearings_documents_1 FOREIGN KEY (hearing) REFERENCES legal_case_hearings (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE hearings_documents
 ADD CONSTRAINT fk_hearings_documents_2 FOREIGN KEY (document) REFERENCES documents_management_system (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE signature_authorities_documents
 ADD CONSTRAINT fk_signature_authorities_documents_1 FOREIGN KEY (signature_authority) REFERENCES company_signature_authorities (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE signature_authorities_documents
 ADD CONSTRAINT fk_signature_authorities_documents_2 FOREIGN KEY (document) REFERENCES documents_management_system (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

CREATE UNIQUE INDEX name ON organizations (name);
GO
CREATE INDEX currency_id ON organizations (currency_id);
GO
CREATE INDEX organization_id ON accounts (organization_id);
GO
CREATE INDEX currency_id ON accounts (currency_id);
GO
CREATE INDEX account_type_id ON accounts (account_type_id);
GO
CREATE UNIQUE INDEX userId_accountId ON accounts_users(userId,accountId);
GO
CREATE UNIQUE INDEX company_id ON vendors(company_id) WHERE company_id IS NOT NULL;
GO
CREATE UNIQUE INDEX contact_id ON vendors(contact_id ) WHERE contact_id IS NOT NULL;
GO
CREATE INDEX createdBy ON vendors (createdBy);
GO
CREATE INDEX modifiedBy ON vendors (modifiedBy);
GO
CREATE INDEX expense_category_id ON expense_categories (expense_category_id);
GO
CREATE INDEX account_id ON expense_categories (account_id);
GO
CREATE UNIQUE INDEX name ON expense_categories (name, account_id);
GO
CREATE INDEX organization_id ON voucher_headers (organization_id);
GO
CREATE INDEX voucher_header_id ON voucher_details (voucher_header_id);
GO
CREATE INDEX account_id ON voucher_details (account_id);
GO
CREATE INDEX vendor_id ON expenses (vendor_id);
GO
CREATE INDEX client_id ON expenses (client_id);
GO
CREATE INDEX paid_through ON expenses (paid_through);
GO
CREATE INDEX client_account_id ON expenses (client_account_id);
GO
CREATE INDEX tax_id ON expenses (tax_id);
GO
CREATE INDEX expense_account ON expenses (expense_account);
GO
CREATE INDEX voucher_header_id ON expenses (voucher_header_id);
GO
CREATE INDEX expense_category_id ON expenses (expense_category_id);
GO
CREATE INDEX item_id ON items (item_id);
GO
CREATE INDEX account_id ON items (account_id);
GO
CREATE INDEX tax_id ON items (tax_id);
GO
CREATE UNIQUE INDEX unitName ON items (unitName, account_id);
GO
CREATE INDEX voucher_header_id ON invoice_headers (voucher_header_id);
GO
CREATE INDEX account_id ON invoice_headers (account_id);
GO
CREATE INDEX term_id ON invoice_headers (term_id);
GO
CREATE INDEX invoice_header_id ON invoice_details (invoice_header_id);
GO
CREATE INDEX renewalDate ON legal_cases_countries_renewals (renewalDate);
GO
CREATE INDEX account_id ON invoice_details (account_id);
GO
CREATE INDEX item_id ON invoice_details (item_id);
GO
CREATE INDEX sub_item_id ON invoice_details (sub_item_id);
GO
CREATE INDEX tax_id ON invoice_details (tax_id);
GO
CREATE INDEX expense_id ON invoice_details (expense_id);
GO
CREATE INDEX voucher_header_id ON invoice_payments (voucher_header_id);
GO
CREATE INDEX account_id ON invoice_payments (account_id);
GO
CREATE INDEX client_account_id ON invoice_payments (client_account_id);
GO
CREATE INDEX invoice_payment_id ON invoice_payment_invoices (invoice_payment_id);
GO
CREATE INDEX invoice_header_id ON invoice_payment_invoices (invoice_header_id);
GO
CREATE INDEX organization_id ON organization_invoice_templates (organization_id);
GO
CREATE INDEX user_id ON user_rate_per_hour (user_id);
GO
CREATE UNIQUE INDEX user_rate_per_hour_per_case_fu ON user_rate_per_hour_per_case( user_id, case_id, organization_id ) WHERE organization_id IS NOT NULL;
GO
CREATE INDEX voucher_header_id ON bill_headers (voucher_header_id);
GO
CREATE INDEX account_id ON bill_headers (account_id);
GO
CREATE INDEX client_id ON bill_headers (client_id);
GO
CREATE INDEX voucher_header_id ON bill_payments (voucher_header_id);
GO
CREATE INDEX account_id ON bill_payments (account_id);
GO
CREATE INDEX supplier_account_id ON bill_payments (supplier_account_id);
GO
CREATE INDEX bill_payment_id ON bill_payment_bills (bill_payment_id);
GO
CREATE INDEX bill_header_id ON bill_payment_bills (bill_header_id);
GO
CREATE UNIQUE INDEX company_id ON partners(company_id) WHERE company_id IS NOT NULL;
GO
CREATE UNIQUE INDEX contact_id ON partners(contact_id ) WHERE contact_id IS NOT NULL;
GO
CREATE INDEX language_id ON event_types_languages (language_id);
GO
CREATE INDEX event_type_id ON event_types_languages (event_type_id);
GO
ALTER TABLE organizations
 ADD CONSTRAINT organizations_ibfk_1 FOREIGN KEY (currency_id) REFERENCES countries (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE accounts
 ADD CONSTRAINT accounts_ibfk_1 FOREIGN KEY (account_type_id) REFERENCES accounts_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE accounts
 ADD CONSTRAINT accounts_ibfk_2 FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE accounts
 ADD CONSTRAINT accounts_ibfk_3 FOREIGN KEY (currency_id) REFERENCES countries (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE taxes
 ADD CONSTRAINT taxes_ibfk_1 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE vendors
 ADD CONSTRAINT vendors_ibfk_1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE vendors
 ADD CONSTRAINT vendors_ibfk_2 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE vendors
 ADD CONSTRAINT vendors_ibfk_3 FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE vendors
 ADD CONSTRAINT vendors_ibfk_4 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE expense_categories
 ADD CONSTRAINT expense_categories_ibfk_1 FOREIGN KEY (expense_category_id) REFERENCES expense_categories (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE expense_categories
 ADD CONSTRAINT expense_categories_ibfk_2 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE voucher_headers
 ADD CONSTRAINT voucher_headers_ibfk_1 FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE voucher_details
 ADD CONSTRAINT voucher_details_ibfk_1 FOREIGN KEY (voucher_header_id) REFERENCES voucher_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE voucher_details
 ADD CONSTRAINT voucher_details_ibfk_2 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE expenses
 ADD CONSTRAINT expenses_headers_ibfk_1 FOREIGN KEY (vendor_id) REFERENCES vendors (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE expenses
 ADD CONSTRAINT expenses_headers_ibfk_2 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE expenses
 ADD CONSTRAINT expenses_headers_ibfk_3 FOREIGN KEY (expense_account) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE expenses
 ADD CONSTRAINT expenses_headers_ibfk_4 FOREIGN KEY (paid_through) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE expenses
 ADD CONSTRAINT expenses_headers_ibfk_5 FOREIGN KEY (client_account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE expenses
 ADD CONSTRAINT expenses_headers_ibfk_6 FOREIGN KEY (voucher_header_id) REFERENCES voucher_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE expenses
 ADD CONSTRAINT expenses_headers_ibfk_7 FOREIGN KEY (expense_category_id) REFERENCES expense_categories (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE expenses
 ADD CONSTRAINT expenses_headers_ibfk_8 FOREIGN KEY (tax_id) REFERENCES taxes (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE expenses
 ADD CONSTRAINT fk_expenses_9 FOREIGN KEY (task) REFERENCES tasks (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE expenses
 ADD CONSTRAINT fk_expenses_10 FOREIGN KEY (hearing) REFERENCES legal_case_hearings (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE expenses
 ADD CONSTRAINT fk_expenses_11 FOREIGN KEY (event) REFERENCES legal_case_events (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE items
 ADD CONSTRAINT items_headers_ibfk_1 FOREIGN KEY (item_id) REFERENCES items (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE items
 ADD CONSTRAINT items_headers_ibfk_2 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE items
 ADD CONSTRAINT items_headers_ibfk_3 FOREIGN KEY (tax_id) REFERENCES taxes (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_headers
 ADD CONSTRAINT invoice_headers_headers_ibfk_1 FOREIGN KEY (voucher_header_id) REFERENCES voucher_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_headers
 ADD CONSTRAINT invoice_headers_headers_ibfk_2 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_headers
 ADD CONSTRAINT invoice_headers_headers_ibfk_3 FOREIGN KEY (term_id) REFERENCES terms (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_headers
 ADD CONSTRAINT invoice_headers_headers_ibfk_4 FOREIGN KEY (discount_id) REFERENCES discounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_headers
 ADD CONSTRAINT invoice_headers_headers_ibfk_5 FOREIGN KEY (original_invoice_id) REFERENCES invoice_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_headers
 ADD CONSTRAINT invoice_headers_headers_ibfk_6 FOREIGN KEY (debit_note_reason_id) REFERENCES credit_note_reasons (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_headers
 ADD CONSTRAINT invoice_headers_headers_ibfk_7 FOREIGN KEY (invoice_type_id) REFERENCES invoice_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_headers
 ADD CONSTRAINT invoice_headers_headers_ibfk_8 FOREIGN KEY (invoice_template_id) REFERENCES organization_invoice_templates (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_details
 ADD CONSTRAINT invoice_details_headers_ibfk_1 FOREIGN KEY (invoice_header_id) REFERENCES invoice_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_details
 ADD CONSTRAINT invoice_details_headers_ibfk_2 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_details
 ADD CONSTRAINT invoice_details_headers_ibfk_3 FOREIGN KEY (item_id) REFERENCES items (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_details
 ADD CONSTRAINT invoice_details_headers_ibfk_4 FOREIGN KEY (sub_item_id) REFERENCES items (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_details
 ADD CONSTRAINT invoice_details_headers_ibfk_5 FOREIGN KEY (tax_id) REFERENCES taxes (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_details
 ADD CONSTRAINT invoice_details_headers_ibfk_6 FOREIGN KEY (expense_id) REFERENCES expenses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_details
 ADD CONSTRAINT invoice_details_headers_ibfk_7 FOREIGN KEY (discount_id) REFERENCES discounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_payments
 ADD CONSTRAINT invoice_payments_headers_ibfk_1 FOREIGN KEY (voucher_header_id) REFERENCES voucher_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_payments
 ADD CONSTRAINT invoice_payments_headers_ibfk_2 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_payments
 ADD CONSTRAINT invoice_payments_headers_ibfk_3 FOREIGN KEY (client_account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_payment_invoices
 ADD CONSTRAINT invoice_payment_invoices_headers_ibfk_1 FOREIGN KEY (invoice_payment_id) REFERENCES invoice_payments (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_payment_invoices
 ADD CONSTRAINT invoice_payment_invoices_headers_ibfk_2 FOREIGN KEY (invoice_header_id) REFERENCES invoice_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE organization_invoice_templates
 ADD CONSTRAINT organization_invoice_templates_ibfk_1 FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_rate_per_hour
 ADD CONSTRAINT user_rate_per_hour_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_rate_per_hour
 ADD CONSTRAINT user_rate_per_hour_ibfk_2 FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_rate_per_hour_per_case
 ADD CONSTRAINT user_rate_per_hour_per_case_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_rate_per_hour_per_case
 ADD CONSTRAINT user_rate_per_hour_per_case_ibfk_2 FOREIGN KEY (case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE user_rate_per_hour_per_case
 ADD CONSTRAINT user_rate_per_hour_per_case_ibfk_3 FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE bill_headers
 ADD CONSTRAINT bill_headers_ibfk_1 FOREIGN KEY (voucher_header_id) REFERENCES voucher_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE bill_headers
 ADD CONSTRAINT bill_headers_ibfk_2 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE bill_headers
 ADD CONSTRAINT bill_headers_ibfk_3 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE bill_details
 ADD CONSTRAINT bill_details_ibfk_1 FOREIGN KEY (bill_header_id) REFERENCES bill_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE bill_details
 ADD CONSTRAINT bill_details_ibfk_2 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE bill_payments
 ADD CONSTRAINT bill_payments_ibfk_1 FOREIGN KEY (voucher_header_id) REFERENCES voucher_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE bill_payments
 ADD CONSTRAINT bill_payments_ibfk_2 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE bill_payments
 ADD CONSTRAINT bill_payments_ibfk_3 FOREIGN KEY (supplier_account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE bill_payment_bills
 ADD CONSTRAINT bill_payment_bills_ibfk_1 FOREIGN KEY (bill_payment_id) REFERENCES bill_payments (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE bill_payment_bills
 ADD CONSTRAINT bill_payment_bills_ibfk_2 FOREIGN KEY (bill_header_id) REFERENCES bill_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_commissions
  ADD CONSTRAINT legal_case_commissions_ibfk_1 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_commissions
  ADD CONSTRAINT legal_case_commissions_ibfk_2 FOREIGN KEY (case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE partners
  ADD CONSTRAINT partners_ibfk_1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE partners
  ADD CONSTRAINT partners_ibfk_2 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE partners
  ADD CONSTRAINT partners_ibfk_3 FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE partners
  ADD CONSTRAINT partners_ibfk_4 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE item_commissions
  ADD CONSTRAINT item_commissions_ibfk_1 FOREIGN KEY (invoice_header_id) REFERENCES invoice_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE item_commissions
  ADD CONSTRAINT item_commissions_ibfk_2 FOREIGN KEY (invoice_details_id) REFERENCES invoice_details (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE item_commissions
  ADD CONSTRAINT item_commissions_ibfk_3 FOREIGN KEY (item_id) REFERENCES items (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE item_commissions
  ADD CONSTRAINT item_commissions_ibfk_4 FOREIGN KEY (sub_item_id) REFERENCES items (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE item_commissions
  ADD CONSTRAINT item_commissions_ibfk_5 FOREIGN KEY (expense_id) REFERENCES expenses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE item_commissions
  ADD CONSTRAINT item_commissions_ibfk_6 FOREIGN KEY (time_logs_id) REFERENCES user_activity_logs (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE item_commissions
  ADD CONSTRAINT item_commissions_ibfk_7 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_detail_cover_page_template
 ADD FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE supplier_taxes
 ADD CONSTRAINT supplier_taxes_ibfk_1 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE accounts_users
 ADD CONSTRAINT accounts_users_ibfk_1 FOREIGN KEY (userId) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE accounts_users
 ADD CONSTRAINT accounts_users_ibfk_2 FOREIGN KEY (accountId) REFERENCES accounts (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE ip_petitions_oppositions
 ADD CONSTRAINT fk_ip_petitions_oppositions1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE ip_petitions_oppositions
 ADD CONSTRAINT fk_ip_petitions_oppositions2 FOREIGN KEY (ip_detail_id) REFERENCES ip_details (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE ip_petitions_oppositions
 ADD CONSTRAINT fk_ip_petitions_oppositions3 FOREIGN KEY (type) REFERENCES ip_petitions_oppositions_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE events
 ADD CONSTRAINT fk_events_1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE events
 ADD CONSTRAINT fk_events_2 FOREIGN KEY (task_location_id) REFERENCES task_locations (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE events
 ADD CONSTRAINT fk_events_3 FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE events
 ADD CONSTRAINT fk_events_4 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE events_attendees
  ADD CONSTRAINT fk_events_attendees_1 FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE events_attendees
  ADD CONSTRAINT fk_events_attendees_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE workflow_status_transition_permissions
 ADD CONSTRAINT fk_workflow_status_transition_permissions_1 FOREIGN KEY (transition) REFERENCES workflow_status_transition (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE grid_saved_columns ADD CONSTRAINT fk_grid_saved_columns_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE grid_saved_columns ADD CONSTRAINT fk_grid_saved_columns_2 FOREIGN KEY (grid_saved_filter_id) REFERENCES grid_saved_filters (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_event_types_languages ADD CONSTRAINT fk_legal_case_event_types_languages_1 FOREIGN KEY (event_type) REFERENCES legal_case_event_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_event_types_languages ADD CONSTRAINT fk_legal_case_event_types_languages_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_event_type_forms ADD CONSTRAINT fk_legal_case_event_type_forms_1 FOREIGN KEY (event_type) REFERENCES legal_case_event_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_event_type_forms ADD CONSTRAINT fk_legal_case_event_type_forms_2 FOREIGN KEY ( field_type ) REFERENCES legal_case_event_data_types ( id ) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_event_type_forms_languages ADD CONSTRAINT fk_legal_case_event_type_forms_languages_1 FOREIGN KEY (field) REFERENCES legal_case_event_type_forms (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_event_type_forms_languages ADD CONSTRAINT fk_legal_case_event_type_forms_languages_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_events ADD CONSTRAINT fk_legal_case_events_1 FOREIGN KEY (legal_case) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_events ADD CONSTRAINT fk_legal_case_events_2 FOREIGN KEY (event_type) REFERENCES legal_case_event_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_events ADD CONSTRAINT fk_legal_case_events_3 FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_events ADD CONSTRAINT fk_legal_case_events_4 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_events ADD CONSTRAINT fk_legal_case_events_5 FOREIGN KEY (stage) REFERENCES legal_case_litigation_details (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_events_related_data ADD CONSTRAINT fk_legal_case_events_related_data_1 FOREIGN KEY (event) REFERENCES legal_case_events (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_event_data_types_languages
 ADD CONSTRAINT fk_legal_case_event_data_types_languages_1 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_event_data_types_languages
 ADD CONSTRAINT fk_legal_case_event_data_types_languages_2 FOREIGN KEY (type) REFERENCES legal_case_event_data_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE invoice_time_logs_items
 ADD CONSTRAINT fk_invoice_time_logs_items_1 FOREIGN KEY (item) REFERENCES invoice_details (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_time_logs_items
 ADD CONSTRAINT fk_invoice_time_logs_items_2 FOREIGN KEY (time_log) REFERENCES user_activity_logs (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE invoice_time_logs_items
 ADD CONSTRAINT fk_invoice_time_logs_items_3 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE company_addresses
ADD CONSTRAINT fk_company_addresses_1 FOREIGN KEY (company) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE account_number_prefix_per_entity
 ADD CONSTRAINT fk_account_number_prefix_per_entity_1 FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE account_number_prefix_per_entity
 ADD CONSTRAINT fk_account_number_prefix_per_entity_2 FOREIGN KEY (account_type_id) REFERENCES accounts_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE client_trust_accounts_relation
 ADD CONSTRAINT fk_client_trust_accounts_relation_1 FOREIGN KEY (client) REFERENCES clients (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE client_trust_accounts_relation
 ADD CONSTRAINT fk_client_trust_accounts_relation_2 FOREIGN KEY (trust_liability_account) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE client_trust_accounts_relation
 ADD CONSTRAINT fk_client_trust_accounts_relation_3 FOREIGN KEY (trust_asset_account) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE client_trust_accounts_relation
 ADD CONSTRAINT fk_client_trust_accounts_relation_4 FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE deposits
 ADD CONSTRAINT fk_deposits_1 FOREIGN KEY (voucher_header_id) REFERENCES voucher_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE deposits
 ADD CONSTRAINT fk_deposits_2 FOREIGN KEY (client_trust_accounts_id) REFERENCES client_trust_accounts_relation (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE deposits
 ADD CONSTRAINT fk_deposits_3 FOREIGN KEY (currency) REFERENCES countries (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE company_notes ADD CONSTRAINT fk_company_notes_1 FOREIGN KEY(company_id)
REFERENCES companies (id)
ON UPDATE CASCADE
ON DELETE CASCADE
GO

ALTER TABLE company_notes ADD  CONSTRAINT fk_company_notes_2 FOREIGN KEY(created_by)
REFERENCES users (id)
GO

ALTER TABLE company_notes ADD  CONSTRAINT fk_company_notes_3 FOREIGN KEY(modified_by)
REFERENCES users (id)
GO

ALTER TABLE company_note_details ADD  CONSTRAINT fk_company_note_details_1 FOREIGN KEY(company_note_id)
REFERENCES company_notes (id)
ON UPDATE CASCADE
ON DELETE CASCADE
GO

ALTER TABLE company_note_details ADD CONSTRAINT df_company_note_details_uploaded  DEFAULT (N'NO') FOR uploaded
GO

ALTER TABLE hearing_types_languages
 ADD CONSTRAINT fk_hearing_types_languages_1 FOREIGN KEY (type) REFERENCES hearing_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE hearing_types_languages
 ADD CONSTRAINT fk_hearing_types_languages_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE stage_statuses_languages
ADD CONSTRAINT fk_stage_statuses_languages_1 FOREIGN KEY ( status ) REFERENCES stage_statuses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE stage_statuses_languages
ADD CONSTRAINT fk_stage_statuses_languages_2 FOREIGN KEY ( language_id ) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_stage_contacts
ADD CONSTRAINT fk_legal_case_stage_contacts_1 FOREIGN KEY (stage) REFERENCES legal_case_litigation_details (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
CONSTRAINT fk_legal_case_stage_contacts_2 FOREIGN KEY (contact) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
CONSTRAINT fk_legal_case_stage_contacts_3 FOREIGN KEY (contact_role) REFERENCES legal_case_contact_roles (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
CONSTRAINT fk_legal_case_stage_contacts_4 FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
CONSTRAINT fk_legal_case_stage_contacts_5 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_litigation_stages_opponents
ADD CONSTRAINT fk_legal_case_litigation_stages_opponents_1 FOREIGN KEY ( stage ) REFERENCES legal_case_litigation_details (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
CONSTRAINT fk_legal_case_litigation_stages_opponents_2 FOREIGN KEY ( opponent_id ) REFERENCES opponents (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
CONSTRAINT fk_legal_case_litigation_stages_opponents_3 FOREIGN KEY ( opponent_position ) REFERENCES legal_case_opponent_positions (id) ON DELETE NO ACTION ON UPDATE NO ACTION
GO

ALTER TABLE legal_case_litigation_external_references
ADD CONSTRAINT fk_litigation_external_references_1 FOREIGN KEY ( stage ) REFERENCES legal_case_litigation_details (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_cases
    ADD CONSTRAINT fk_legal_cases_10 FOREIGN KEY (workflow) REFERENCES workflows (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE workflow_status_relation
    ADD CONSTRAINT fk_workflow_status_relation_1 FOREIGN KEY (workflow_id) REFERENCES workflows (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE workflow_status_transition_screen_fields
 ADD CONSTRAINT fk_workflow_status_transition_screen_fields_1 FOREIGN KEY (transition) REFERENCES workflow_status_transition (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE task_contributors
  ADD CONSTRAINT fk_task_contributors_1 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE task_contributors
  ADD CONSTRAINT fk_task_contributors_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE task_workflows
    ADD CONSTRAINT fk_task_workflows_1 FOREIGN KEY (createdBy) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE task_workflows
    ADD CONSTRAINT fk_task_workflows_2 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO

ALTER TABLE task_workflow_types
    ADD CONSTRAINT fk_task_workflow_types_1 FOREIGN KEY (workflow_id) REFERENCES task_workflows (id) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE task_workflow_types
    ADD CONSTRAINT fk_task_workflow_types_2 FOREIGN KEY (type_id) REFERENCES task_types (id) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO

ALTER TABLE task_workflow_status_relation
    ADD CONSTRAINT fk_task_workflow_status_relation_1 FOREIGN KEY (workflow_id) REFERENCES task_workflows (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE task_workflow_status_relation
    ADD CONSTRAINT fk_task_workflow_status_relation_2 FOREIGN KEY (status_id) REFERENCES task_statuses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE task_workflow_status_transition
    ADD CONSTRAINT fk_task_workflow_status_transition_1 FOREIGN KEY (workflow_id) REFERENCES task_workflows (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE task_workflow_status_transition
    ADD CONSTRAINT fk_task_workflow_status_transition_2 FOREIGN KEY (from_step) REFERENCES task_statuses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE task_workflow_status_transition
    ADD CONSTRAINT fk_task_workflow_status_transition_3 FOREIGN KEY (to_step) REFERENCES task_statuses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE task_workflow_status_transition_permissions
    ADD CONSTRAINT fk_task_workflow_status_transition_permissions_1 FOREIGN KEY (transition) REFERENCES task_workflow_status_transition (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE task_workflow_status_transition_history
    ADD CONSTRAINT fk_task_workflow_status_transition_history_1 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE task_workflow_status_transition_history
    ADD CONSTRAINT fk_task_workflow_status_transition_history_2 FOREIGN KEY (from_step) REFERENCES task_statuses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE task_workflow_status_transition_history
    ADD CONSTRAINT fk_task_workflow_status_transition_history_3 FOREIGN KEY (to_step) REFERENCES task_statuses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE task_workflow_status_transition_screen_fields
 ADD CONSTRAINT fk_task_workflow_status_transition_screen_fields_1 FOREIGN KEY (transition) REFERENCES task_workflow_status_transition (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE tasks
 ADD CONSTRAINT fk_tasks_9 FOREIGN KEY (workflow) REFERENCES task_workflows (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE tasks
 ADD CONSTRAINT fk_tasks_10 FOREIGN KEY (stage) REFERENCES legal_case_litigation_details (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE tasks
    ADD CONSTRAINT fk_tasks_11 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE task_comments
  ADD CONSTRAINT fk_task_comments_1 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE legal_case_container_opponents
 ADD CONSTRAINT fk_legal_case_container_opponents1 FOREIGN KEY (case_container_id) REFERENCES legal_case_containers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_container_opponents
 ADD CONSTRAINT fk_legal_case_container_opponents2 FOREIGN KEY (opponent_id) REFERENCES opponents (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE case_comments_emails
 ADD CONSTRAINT fk_case_comments_emails_1 FOREIGN KEY (case_comment) REFERENCES case_comments (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE user_api_keys
 ADD CONSTRAINT user_api_keys_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE assignments_relation
 ADD CONSTRAINT fk_assignments_1 FOREIGN KEY (relation) REFERENCES assignments (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_users_assignments
 ADD CONSTRAINT fk_customer_portal_users_assignments_1 FOREIGN KEY (screen) REFERENCES customer_portal_screens (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE recurrence ADD CONSTRAINT fk_recurrence_1  FOREIGN KEY (type_id) REFERENCES recurring_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

CREATE UNIQUE INDEX user_api_key ON user_api_keys(user_id, api_key);
GO

CREATE UNIQUE INDEX case_rate_fu ON case_rate( case_id, organization_id ) WHERE organization_id IS NOT NULL;
GO
ALTER TABLE case_rate
    ADD CONSTRAINT fk_case_rate_1 FOREIGN KEY (case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE case_rate
    ADD CONSTRAINT fk_case_rate_2 FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_sla_notification
    ADD CONSTRAINT fk_customer_portal_sla_notification_1 FOREIGN KEY (sla_id) REFERENCES customer_portal_sla (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE customer_portal_sla_notification
    ADD CONSTRAINT fk_customer_portal_sla_notification_2 FOREIGN KEY (case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_opponent_position_languages
 ADD CONSTRAINT fk_legal_case_opponent_position_languages_1 FOREIGN KEY (legal_case_opponent_position_id) REFERENCES legal_case_opponent_positions (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_opponent_position_languages
 ADD CONSTRAINT fk_legal_case_opponent_position_languages_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_opponents
 ADD CONSTRAINT fk_legal_case_opponents3 FOREIGN KEY (opponent_position) REFERENCES legal_case_opponent_positions (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_container_opponents
 ADD CONSTRAINT fk_legal_case_container_opponents3 FOREIGN KEY (opponent_position) REFERENCES legal_case_opponent_positions (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_container_documents
 ADD CONSTRAINT fk_legal_case_container_documents_legal_case_containers1 FOREIGN KEY (legal_case_container_id) REFERENCES legal_case_containers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_container_documents
 ADD CONSTRAINT legal_case_container_documents_ibfk_1 FOREIGN KEY (createdBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_container_documents
 ADD CONSTRAINT legal_case_container_documents_ibfk_2 FOREIGN KEY (modifiedBy) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_container_documents
 ADD CONSTRAINT legal_case_container_documents_ibfk_3 FOREIGN KEY (legal_case_container_document_status_id) REFERENCES legal_case_container_document_statuses (id);
GO
ALTER TABLE legal_case_container_documents
 ADD CONSTRAINT legal_case_container_documents_ibfk_4 FOREIGN KEY (legal_case_container_document_type_id) REFERENCES legal_case_container_document_types (id);
GO

ALTER TABLE time_types_languages
  ADD CONSTRAINT fk_time_types_languages_1 FOREIGN KEY (type) REFERENCES time_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE time_types_languages
  ADD CONSTRAINT fk_time_types_languages_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE countries_languages
  ADD CONSTRAINT fk_countries_languages_1 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE countries_languages
  ADD CONSTRAINT fk_countries_languages_2 FOREIGN KEY (country_id) REFERENCES countries (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE contact_emails
  ADD CONSTRAINT fk_contact_emails_1 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE advisor_users
  ADD CONSTRAINT fk_advisor_users_1 FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_advisor_users_2 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE advisor_tasks
  ADD CONSTRAINT fk_advisor_tasks_1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_advisor_tasks_2 FOREIGN KEY (advisor_task_status_id) REFERENCES advisor_task_statuses (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_advisor_tasks_3 FOREIGN KEY (advisor_task_type_id) REFERENCES advisor_task_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_advisor_tasks_4 FOREIGN KEY (advisor_task_location_id) REFERENCES advisor_task_locations (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_advisor_tasks_5 FOREIGN KEY (advisor_id) REFERENCES advisor_users (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_advisor_tasks_6 FOREIGN KEY (assigned_to) REFERENCES advisor_users (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_advisor_tasks_7 FOREIGN KEY (reporter) REFERENCES advisor_users (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_advisor_tasks_8 FOREIGN KEY (createdBy) REFERENCES advisor_users (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_advisor_tasks_9 FOREIGN KEY (modifiedBy) REFERENCES advisor_users (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_advisor_tasks_10 FOREIGN KEY (workflow) REFERENCES advisor_task_workflows (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE advisor_task_types_languages
  ADD CONSTRAINT fk_advisor_task_types_languages_1 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_advisor_task_types_languages_2 FOREIGN KEY (advisor_task_type_id) REFERENCES advisor_task_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE advisor_task_comments
  ADD CONSTRAINT fk_advisor_task_comments_1 FOREIGN KEY (advisor_task_id) REFERENCES advisor_tasks (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE advisor_task_sharedwith_users
  ADD CONSTRAINT fk_advisor_task_sharedwith_users_1 FOREIGN KEY (advisor_task_id) REFERENCES advisor_tasks (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_advisor_task_sharedwith_users_2 FOREIGN KEY (advisor_id) REFERENCES advisor_users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
CREATE UNIQUE INDEX advisor_task_sharedwith_users_advisor_task_id_advisor_id ON advisor_task_sharedwith_users(advisor_task_id, advisor_id);
GO

ALTER TABLE advisor_permissions
  ADD CONSTRAINT fk_advisor_permissions_1 FOREIGN KEY (workflow_status_transition_id) REFERENCES workflow_status_transition (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_advisor_permissions_2 FOREIGN KEY (workflow_id) REFERENCES workflows (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE advisor_user_activity_logs
  ADD CONSTRAINT fk_advisor_user_activity_logs_1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT fk_advisor_user_activity_logs_2 FOREIGN KEY (advisor_task_id) REFERENCES advisor_tasks (id) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT fk_advisor_user_activity_logs_3 FOREIGN KEY (advisor_id) REFERENCES advisor_users (id) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT fk_advisor_user_activity_logs_4 FOREIGN KEY (time_type_id) REFERENCES time_types (id) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT fk_advisor_user_activity_logs_5 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE NO ACTION ON UPDATE CASCADE;
GO

ALTER TABLE advisor_task_workflows_permissions
    ADD CONSTRAINT fk_advisor_task_workflows_permissions_1 FOREIGN KEY (advisor_task_workflow_id) REFERENCES advisor_task_workflows (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE advisor_task_workflow_statuses
  ADD CONSTRAINT fk_advisor_task_workflow_statuses_1 FOREIGN KEY (advisor_task_workflow_id) REFERENCES advisor_task_workflows (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_advisor_task_workflow_statuses_2 FOREIGN KEY (advisor_task_status_id) REFERENCES advisor_task_statuses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE model_has_permissions
  ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE;
GO

ALTER TABLE model_has_roles
  ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE;
GO

ALTER TABLE role_has_permissions
  ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE,
  CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE;
GO

ALTER TABLE time_internal_statuses_languages
  ADD CONSTRAINT fk_time_internal_statuses_languages_1 FOREIGN KEY (internal_status) REFERENCES time_internal_statuses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE time_internal_statuses_languages
  ADD CONSTRAINT fk_time_internal_statuses_languages_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_hearing_client_report_history
 ADD CONSTRAINT fk_legal_case_hearing_client_report_history_1 FOREIGN KEY ( legal_case_hearing_id ) REFERENCES legal_case_hearings (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_hearing_client_report_history
 ADD CONSTRAINT fk_legal_case_hearing_client_report_history_2 FOREIGN KEY ( createdBy ) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_hearing_client_report_history
 ADD CONSTRAINT fk_legal_case_hearing_client_report_history_3 FOREIGN KEY ( modifiedBy ) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE litigation_stage_status_history
 ADD CONSTRAINT fk_litigation_stage_status_history_1 FOREIGN KEY ( litigation_stage ) REFERENCES legal_case_litigation_details (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE litigation_stage_status_history
 ADD CONSTRAINT fk_litigation_stage_status_history_2 FOREIGN KEY ( status ) REFERENCES stage_statuses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE litigation_stage_status_history
 ADD CONSTRAINT fk_litigation_stage_status_history_3 FOREIGN KEY ( action_maker ) REFERENCES users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE hearing_outcome_reasons_languages
 ADD CONSTRAINT hearing_outcome_reasons_languages_ibfk_1 FOREIGN KEY (hearing_outcome_reason) REFERENCES hearing_outcome_reasons (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE hearing_outcome_reasons_languages
 ADD CONSTRAINT hearing_outcome_reasons_languages_ibfk_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_hearings
 ADD CONSTRAINT fk_legal_case_hearings_5 FOREIGN KEY ( reason_of_win_or_lose ) REFERENCES hearing_outcome_reasons (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE board_post_filters
    ADD CONSTRAINT fk_board_post_filters_1 FOREIGN KEY (board_id) REFERENCES planning_boards (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE board_post_filters_user
    ADD CONSTRAINT board_post_filters_user_ibfk_1 FOREIGN KEY (board_post_filters_id) REFERENCES board_post_filters (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE board_post_filters_user
    ADD CONSTRAINT board_post_filters_user_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE grid_saved_board_filters_users
    ADD CONSTRAINT grid_saved_board_filters_users_ibfk_1 FOREIGN KEY (board_id) REFERENCES planning_boards (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE grid_saved_board_filters_users
    ADD CONSTRAINT grid_saved_board_filters_users_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE legal_case_container_advanced_export_slots
    ADD CONSTRAINT legal_case_container_advanced_export_slots_ibfk_1 FOREIGN KEY (legal_case_container_id) REFERENCES legal_case_containers (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE legal_case_outsources
 ADD CONSTRAINT fk_legal_case_outsources_1 FOREIGN KEY ( legal_case_id ) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_outsources
 ADD CONSTRAINT fk_legal_case_outsources_2 FOREIGN KEY ( company_id ) REFERENCES companies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_outsource_contacts
 ADD CONSTRAINT fk_legal_case_outsource_contacts_1 FOREIGN KEY ( legal_case_outsource_id ) REFERENCES legal_case_outsources (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_outsource_contacts
 ADD CONSTRAINT fk_legal_case_outsource_contacts_2 FOREIGN KEY ( contact_id ) REFERENCES contacts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE dbo.advisor_user_preferences
 ADD CONSTRAINT uk_advisor_user_preferences_1 UNIQUE (advisor_user_id,keyName);
GO

ALTER TABLE dbo.advisor_user_preferences
 ADD CONSTRAINT fk_advisor_user_preferences_1 FOREIGN KEY (advisor_user_id) REFERENCES advisor_users(id) ;
GO

ALTER TABLE advisor_email_template_languages
 ADD CONSTRAINT fk_advisor_email_template_languages_1 FOREIGN KEY (advisor_email_template_id) REFERENCES advisor_email_templates (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE advisor_email_template_languages
 ADD CONSTRAINT fk_advisor_email_template_languages_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE money_dashboard_widgets
    ADD CONSTRAINT fk_money_dashboard_widgets_1 FOREIGN KEY (money_dashboard_id) REFERENCES money_dashboards (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE money_dashboard_widgets
    ADD CONSTRAINT fk_money_dashboard_widgets_2 FOREIGN KEY (money_dashboard_widgets_type_id) REFERENCES money_dashboard_widgets_types (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE board_task_post_filters
    ADD CONSTRAINT fk_board_task_post_filters_1 FOREIGN KEY (board_id) REFERENCES task_boards (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE board_task_post_filters_user
    ADD CONSTRAINT board_task_post_filters_user_ibfk_1 FOREIGN KEY (board_post_filters_id) REFERENCES board_task_post_filters (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE board_task_post_filters_user
    ADD CONSTRAINT board_task_post_filters_user_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE grid_saved_board_task_filters_users
    ADD CONSTRAINT grid_saved_board_task_filters_users_ibfk_1 FOREIGN KEY (board_id) REFERENCES task_boards (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE grid_saved_board_task_filters_users
    ADD CONSTRAINT grid_saved_board_task_filters_users_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE advisor_timers
	ADD CONSTRAINT fk_advisor_timers_1 FOREIGN KEY(advisor_id) REFERENCES advisor_users (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE advisor_timers
	ADD CONSTRAINT fk_advisor_timers_2 FOREIGN KEY(advisor_task_id) REFERENCES advisor_tasks (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE advisor_timers
	ADD CONSTRAINT fk_advisor_timers_3 FOREIGN KEY(legal_case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE advisor_timers
	ADD CONSTRAINT fk_advisor_timers_4 FOREIGN KEY(time_type_id) REFERENCES time_types (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE advisor_timer_time_logs
	ADD CONSTRAINT fk_advisor_timer_time_logs_1 FOREIGN KEY(advisor_timer_id) REFERENCES advisor_timers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE docs_documents  WITH CHECK ADD  CONSTRAINT docs_documents_ibfk1 FOREIGN KEY(createdBy)
REFERENCES users (id)
GO

ALTER TABLE docs_documents  WITH CHECK ADD  CONSTRAINT docs_documents_ibfk2 FOREIGN KEY(modifiedBy)
REFERENCES users (id)
GO

ALTER TABLE docs_documents  WITH CHECK ADD  CONSTRAINT docs_documents_ibfk3 FOREIGN KEY(docs_document_status_id)
REFERENCES docs_document_statuses (id)
GO

ALTER TABLE docs_documents  WITH CHECK ADD  CONSTRAINT docs_documents_ibfk4 FOREIGN KEY(docs_document_type_id)
REFERENCES docs_document_types (id)
GO

-- legal_case_dueDate_legal_case_id
begin
    IF EXISTS (SELECT *  FROM sys.indexes  WHERE name='legal_case_dueDate_legal_case_id' AND object_id = OBJECT_ID('legal_cases'))
        begin
            DROP INDEX legal_case_dueDate_legal_case_id ON legal_cases;
        end
end
GO
CREATE NONCLUSTERED INDEX legal_case_dueDate_legal_case_id
    ON legal_cases(id, dueDate)
GO

-- legal_case_category
begin
    IF EXISTS (SELECT *  FROM sys.indexes  WHERE name='legal_case_category' AND object_id = OBJECT_ID('legal_cases'))
        begin
            DROP INDEX legal_case_category ON legal_cases;
        end
end
GO
CREATE NONCLUSTERED INDEX legal_case_category
    ON legal_cases(category)
GO

-- legal_case_dueDate
begin
    IF EXISTS (SELECT *  FROM sys.indexes  WHERE name='legal_case_dueDate' AND object_id = OBJECT_ID('legal_cases'))
        begin
            DROP INDEX legal_case_dueDate ON legal_cases;
        end
end
GO
CREATE NONCLUSTERED INDEX legal_case_dueDate
    ON legal_cases(dueDate)
GO

-- legal_case_archived
begin
    IF EXISTS (SELECT *  FROM sys.indexes  WHERE name='legal_case_archived' AND object_id = OBJECT_ID('legal_cases'))
        begin
            DROP INDEX legal_case_archived ON legal_cases;
        end
end
GO
CREATE NONCLUSTERED INDEX legal_case_archived
    ON legal_cases(archived)
GO

-- legal_case_provider_group_id
begin
    IF EXISTS (SELECT *  FROM sys.indexes  WHERE name='legal_case_provider_group_id' AND object_id = OBJECT_ID('legal_cases'))
        begin
            DROP INDEX legal_case_provider_group_id ON legal_cases;
        end
end
GO
CREATE NONCLUSTERED INDEX legal_case_provider_group_id
    ON legal_cases(provider_group_id)
GO

-- legal_case_user_id
begin
    IF EXISTS (SELECT *  FROM sys.indexes  WHERE name='legal_case_user_id' AND object_id = OBJECT_ID('legal_cases'))
        begin
            DROP INDEX legal_case_user_id ON legal_cases;
        end
end
GO
CREATE NONCLUSTERED INDEX legal_case_user_id
    ON legal_cases(user_id)
GO

-- legal_case_createdBy
begin
    IF EXISTS (SELECT *  FROM sys.indexes  WHERE name='legal_case_createdBy' AND object_id = OBJECT_ID('legal_cases'))
        begin
            DROP INDEX legal_case_createdBy ON legal_cases;
        end
end
GO
CREATE NONCLUSTERED INDEX legal_case_createdBy
    ON legal_cases(createdBy)
GO

-- legal_case_private
begin
    IF EXISTS (SELECT *  FROM sys.indexes  WHERE name='legal_case_private' AND object_id = OBJECT_ID('legal_cases'))
        begin
            DROP INDEX legal_case_private ON legal_cases;
        end
end
GO
CREATE NONCLUSTERED INDEX legal_case_private
    ON legal_cases(private)
GO

-- legal_case_priority
begin
    IF EXISTS (SELECT *  FROM sys.indexes  WHERE name='legal_case_priority' AND object_id = OBJECT_ID('legal_cases'))
        begin
            DROP INDEX legal_case_priority ON legal_cases;
        end
end
GO
CREATE NONCLUSTERED INDEX legal_case_priority
    ON legal_cases(priority)
GO

-- legal_case_case_type_id
begin
    IF EXISTS (SELECT *  FROM sys.indexes  WHERE name='legal_case_case_type_id' AND object_id = OBJECT_ID('legal_cases'))
        begin
            DROP INDEX legal_case_case_type_id ON legal_cases;
        end
end
GO
CREATE NONCLUSTERED INDEX legal_case_case_type_id
    ON legal_cases(case_type_id)
GO

-- legal_case_caseArrivalDate
begin
    IF EXISTS (SELECT *  FROM sys.indexes  WHERE name='legal_case_caseArrivalDate' AND object_id = OBJECT_ID('legal_cases'))
        begin
            DROP INDEX legal_case_caseArrivalDate ON legal_cases;
        end
end
GO
CREATE NONCLUSTERED INDEX legal_case_caseArrivalDate
    ON legal_cases(caseArrivalDate)
GO

-- legal_case_client_id
begin
    IF EXISTS (SELECT *  FROM sys.indexes  WHERE name='legal_case_client_id' AND object_id = OBJECT_ID('legal_cases'))
        begin
            DROP INDEX legal_case_client_id ON legal_cases;
        end
end
GO
CREATE NONCLUSTERED INDEX legal_case_client_id
    ON legal_cases(client_id)
GO

ALTER TABLE client_partner_shares
 ADD CONSTRAINT fk_client_partner_shares_1 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE client_partner_shares
 ADD CONSTRAINT fk_client_partner_shares_2 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE legal_case_partner_shares
 ADD CONSTRAINT fk_legal_case_partner_shares_1 FOREIGN KEY (case_id) REFERENCES legal_cases (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE legal_case_partner_shares
 ADD CONSTRAINT fk_legal_case_partner_shares_2 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;


ALTER TABLE tasks_documents
 ADD CONSTRAINT fk_tasks_documents_1 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE tasks_documents
 ADD CONSTRAINT fk_tasks_documents_2 FOREIGN KEY (document_id) REFERENCES documents_management_system (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE case_related_contracts
    ADD CONSTRAINT fk_case_related_contracts_1 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE case_related_contracts
    ADD CONSTRAINT fk_case_related_contracts_2 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
CREATE INDEX legal_case_id ON case_related_contracts (legal_case_id);
GO

ALTER TABLE [dbo].[advisor_task_type_workflows] ADD CONSTRAINT [fk_advisor_task_workflow_types_1] FOREIGN KEY([advisor_task_workflow_id])
REFERENCES [dbo].[advisor_task_workflows] ([id])
GO

ALTER TABLE [dbo].[advisor_task_type_workflows] ADD CONSTRAINT [fk_advisor_task_workflow_types_2] FOREIGN KEY([advisor_task_type_id])
REFERENCES [dbo].[advisor_task_types] ([id])
GO

ALTER TABLE credit_note_headers  ADD CONSTRAINT credit_note_headers_ibfk_1 FOREIGN KEY (organization_id) REFERENCES organizations (id);
ALTER TABLE credit_note_headers  ADD CONSTRAINT credit_note_headers_ibfk_2 FOREIGN KEY (account_id) REFERENCES accounts (id);
ALTER TABLE credit_note_headers  ADD CONSTRAINT credit_note_headers_ibfk_3 FOREIGN KEY (term_id) REFERENCES terms (id);
ALTER TABLE credit_note_headers  ADD CONSTRAINT credit_note_headers_ibfk_4 FOREIGN KEY (voucher_header_id) REFERENCES voucher_headers (id);
ALTER TABLE credit_note_headers  ADD CONSTRAINT credit_note_headers_ibfk_5 FOREIGN KEY (discount_id) REFERENCES discounts (id);

ALTER TABLE credit_note_details  ADD CONSTRAINT credit_note_details_ibfk_1 FOREIGN KEY (credit_note_header_id) REFERENCES credit_note_headers (id);
ALTER TABLE credit_note_details  ADD CONSTRAINT credit_note_details_ibfk_2 FOREIGN KEY (account_id) REFERENCES accounts (id);
ALTER TABLE credit_note_details  ADD CONSTRAINT credit_note_details_ibfk_3 FOREIGN KEY (item_id) REFERENCES items (id);
ALTER TABLE credit_note_details  ADD CONSTRAINT credit_note_details_ibfk_4 FOREIGN KEY (tax_id) REFERENCES taxes (id);
ALTER TABLE credit_note_details  ADD CONSTRAINT credit_note_details_ibfk_5 FOREIGN KEY (expense_id) REFERENCES expenses (id);
ALTER TABLE credit_note_details  ADD CONSTRAINT credit_note_details_ibfk_6 FOREIGN KEY (discount_id) REFERENCES discounts (id);

ALTER TABLE credit_note_time_logs_items   ADD CONSTRAINT credit_note_time_logs_items_ibfk_1 FOREIGN KEY (credit_note_details_id) REFERENCES credit_note_details (id);
ALTER TABLE credit_note_time_logs_items ADD CONSTRAINT credit_note_time_logs_items_ibfk_2 FOREIGN KEY (time_log_id) REFERENCES user_activity_logs (id);
ALTER TABLE credit_note_time_logs_items ADD CONSTRAINT credit_note_time_logs_items_ibfk_3 FOREIGN KEY (user_id) REFERENCES users (id);

ALTER TABLE credit_note_refunds   ADD CONSTRAINT credit_note_refunds_ibfk_1 FOREIGN KEY (credit_note_header_id) REFERENCES credit_note_headers (id);
ALTER TABLE credit_note_refunds   ADD CONSTRAINT credit_note_refunds_ibfk_2 FOREIGN KEY (voucher_header_id) REFERENCES voucher_headers (id);
ALTER TABLE credit_note_refunds   ADD CONSTRAINT credit_note_refunds_ibfk_3 FOREIGN KEY (account_id) REFERENCES accounts (id);
ALTER TABLE credit_note_refunds   ADD CONSTRAINT credit_note_refunds_ibfk_4 FOREIGN KEY (client_account_id) REFERENCES accounts (id);

ALTER TABLE credit_note_invoices   ADD CONSTRAINT credit_note_invoices_ibfk_1 FOREIGN KEY (credit_note_header_id) REFERENCES credit_note_headers (id);
ALTER TABLE credit_note_invoices   ADD CONSTRAINT credit_note_invoices_ibfk_2 FOREIGN KEY (invoice_header_id) REFERENCES invoice_headers (id);

ALTER TABLE credit_note_item_commissions   ADD CONSTRAINT credit_note_item_commissions_ibfk_1 FOREIGN KEY (credit_note_header_id) REFERENCES credit_note_headers (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE credit_note_item_commissions   ADD CONSTRAINT credit_note_item_commissions_ibfk_2 FOREIGN KEY (credit_note_details_id) REFERENCES credit_note_details (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE credit_note_item_commissions   ADD CONSTRAINT credit_note_item_commissions_ibfk_3 FOREIGN KEY (item_id) REFERENCES items (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE credit_note_item_commissions   ADD CONSTRAINT credit_note_item_commissions_ibfk_4 FOREIGN KEY (expense_id) REFERENCES expenses (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE credit_note_item_commissions   ADD CONSTRAINT credit_note_item_commissions_ibfk_5 FOREIGN KEY (time_logs_id) REFERENCES user_activity_logs (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE credit_note_item_commissions   ADD CONSTRAINT credit_note_item_commissions_ibfk_6 FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE credit_note_related_cases  ADD CONSTRAINT credit_note_related_cases_ibfk_1 FOREIGN KEY (credit_note_header_id) REFERENCES credit_note_headers (id);
ALTER TABLE credit_note_related_cases  ADD CONSTRAINT credit_note_related_cases_ibfk_2 FOREIGN KEY (legal_case_id) REFERENCES legal_cases (id);

ALTER TABLE dbo.case_types_due_conditions ADD CONSTRAINT
	PK_case_types_due_conditions PRIMARY KEY CLUSTERED 
	(
	id
	) WITH( STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]

GO

ALTER TABLE dbo.case_types_due_conditions ADD CONSTRAINT
	FK_case_types_due_conditions_case_types FOREIGN KEY
	(
	case_type_id
	) REFERENCES dbo.case_types
	(
	id
	) ON UPDATE  NO ACTION 
	 ON DELETE  NO ACTION 
	
GO
ALTER TABLE dbo.case_types_due_conditions ADD CONSTRAINT
	FK_case_types_due_conditions_clients FOREIGN KEY
	(
	client_id
	) REFERENCES dbo.clients
	(
	id
	) ON UPDATE  NO ACTION 
	 ON DELETE  NO ACTION 
	
GO

ALTER TABLE contract_cp_screen_fields
 ADD CONSTRAINT fk_contract_cp_screen_fields1 FOREIGN KEY (screen_id) REFERENCES contract_cp_screens (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE contract_cp_screen_field_languages
 ADD CONSTRAINT fk_contract_cp_screen_field_languages1 FOREIGN KEY (screen_field_id) REFERENCES contract_cp_screen_fields (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE contract_cp_screen_field_languages
 ADD CONSTRAINT fk_contract_cp_screen_field_languages2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE customer_portal_screens ADD CONSTRAINT fk_customer_portal_screens_1 FOREIGN KEY (request_type_category_id) REFERENCES request_type_categories (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE contract_cp_screens ADD CONSTRAINT fk_contract_cp_screens_1 FOREIGN KEY (contract_request_type_category_id) REFERENCES contract_request_type_categories (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE license_and_waiver_reminds
 ADD CONSTRAINT fk_license_and_waiver_reminds_company_discharge1 FOREIGN KEY (license_and_waiver_id) REFERENCES company_discharge_social_securities (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE license_and_waiver_reminds
 ADD CONSTRAINT fk_license_and_waiver_reminds_users1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO

ALTER TABLE license_and_waiver_reminds
 ADD CONSTRAINT fk_license_and_waiver_reminds_user_groups1 FOREIGN KEY (user_group_id) REFERENCES user_groups (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE license_and_waiver_reminds
 ADD CONSTRAINT fk_license_and_waiver_reminds_reminders1 FOREIGN KEY (reminder_id) REFERENCES reminders (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

CREATE UNIQUE INDEX licenseAndWaiverUser ON license_and_waiver_reminds(license_and_waiver_id, user_id);
GO

CREATE UNIQUE INDEX licenseAndWaiverUserGroup ON license_and_waiver_reminds(license_and_waiver_id, user_group_id, reminder_id);
GO

ALTER TABLE contract_sla_management
    ADD CONSTRAINT fk_contract_sla_management_1 FOREIGN KEY (workflow_id) REFERENCES contract_workflow (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE contracts_sla_actions
    ADD CONSTRAINT fk_contracts_sla_actions_1 FOREIGN KEY (sla_management_id) REFERENCES contract_sla_management (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contracts_sla_actions
    ADD CONSTRAINT fk_contracts_sla_actions_2 FOREIGN KEY (status_id) REFERENCES contract_status (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE contracts_sla
    ADD CONSTRAINT fk_contracts_sla_1 FOREIGN KEY (sla_management_id) REFERENCES contract_sla_management (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contracts_sla
    ADD CONSTRAINT fk_contracts_sla_2 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE contract_parties_sla
    ADD CONSTRAINT fk_contract_parties_sla_1 FOREIGN KEY (sla_management_id) REFERENCES contract_sla_management (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contract_parties_sla
    ADD CONSTRAINT fk_contract_parties_sla_2 FOREIGN KEY (party_id) REFERENCES party (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE contracts_type_sla
    ADD CONSTRAINT fk_contracts_type_sla_1 FOREIGN KEY (sla_management_id) REFERENCES contract_sla_management (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contracts_type_sla
    ADD CONSTRAINT fk_contracts_type_sla_2 FOREIGN KEY (type_id) REFERENCES contract_type (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

ALTER TABLE contract_sla_notification
    ADD CONSTRAINT fk_contract_sla_notification_1 FOREIGN KEY (sla_management_id) REFERENCES contract_sla_management (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contract_sla_notification
    ADD CONSTRAINT fk_contract_sla_notification_2 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE NO ACTION ON UPDATE NO ACTION;

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

-- Functions

IF OBJECT_ID (N'TotalCaseValuesByClientId', N'IF') IS NOT NULL
    DROP FUNCTION TotalCaseValuesByClientId;
GO
CREATE FUNCTION TotalCaseValuesByClientId (@client_id BIGINT)
RETURNS TABLE
AS
RETURN
(
   select sum(legal_cases.caseValue) as caseValue from legal_cases where legal_cases.client_id = @client_id
);
GO

IF OBJECT_ID (N'caseValuesSummationByClientId', N'IF') IS NOT NULL
    DROP FUNCTION caseValuesSummationByClientId;
GO
CREATE FUNCTION caseValuesSummationByClientId (@client_id BIGINT)
RETURNS TABLE
AS
RETURN
(
   select sum(legal_cases.caseValue) as caseValue from legal_cases where legal_cases.client_id = @client_id and legal_cases.category = 'Litigation' and legal_cases.isDeleted = 0
);
GO

IF OBJECT_ID (N'RecoveredValuesSummationByClientId', N'IF') IS NOT NULL
    DROP FUNCTION RecoveredValuesSummationByClientId;
GO
CREATE FUNCTION RecoveredValuesSummationByClientId (@client_id BIGINT)
RETURNS TABLE
AS
RETURN
(
   select sum(legal_cases.recoveredValue) as recoveredValue from legal_cases where legal_cases.client_id = @client_id and legal_cases.category = 'Litigation'and legal_cases.isDeleted = 0
);
GO

IF OBJECT_ID (N'JudgementValuesSummationByClientId', N'IF') IS NOT NULL
    DROP FUNCTION JudgementValuesSummationByClientId;
GO
CREATE FUNCTION JudgementValuesSummationByClientId (@client_id BIGINT)
RETURNS TABLE
AS
RETURN
(
   select sum(legal_cases.judgmentValue) as recoveredValue from legal_cases where legal_cases.client_id = @client_id and legal_cases.category = 'Litigation' and legal_cases.isDeleted = 0
);
GO

IF OBJECT_ID (N'TotalExpensesByCaseId', N'IF') IS NOT NULL
    DROP FUNCTION TotalExpensesByCaseId;
GO
CREATE FUNCTION TotalExpensesByCaseId(@case_id BIGINT,@organization_id BIGINT)
RETURNS TABLE
AS
RETURN
(
   select sum(voucher_details.local_amount) as TotalExpenses from voucher_headers left   join voucher_details on
   voucher_headers.id=voucher_details.voucher_header_id  and voucher_details.drCr='C'
   left join voucher_related_cases on voucher_related_cases.voucher_header_id = voucher_headers.id
    where voucher_related_cases.legal_case_id=@case_id and
    voucher_headers.organization_id=@organization_id and voucher_headers.voucherType='EXP'
);
GO

IF OBJECT_ID (N'TotalExpensesPerCategory', N'IF') IS NOT NULL
    DROP FUNCTION TotalExpensesPerCategory;
GO
CREATE FUNCTION TotalExpensesPerCategory(@expenses_id BIGINT)
RETURNS TABLE
AS
RETURN
(
   select sum(voucher_details.local_amount) as TotalExpenses from expenses left   join voucher_details on
   expenses.voucher_header_id=voucher_details.voucher_header_id and voucher_details.drCr='D'
    where expenses.id=@expenses_id
);
GO

