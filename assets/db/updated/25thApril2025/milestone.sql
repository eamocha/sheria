
IF OBJECT_ID('dbo.contract_milestone', 'U') IS NOT NULL DROP TABLE dbo.contract_milestone;
GO
CREATE TABLE contract_milestone (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 contract_id BIGINT NOT NULL,
 title nvarchar(255) NOT NULL,
 serial_number nvarchar(255) DEFAULT NULL,
 deliverables TEXT NULL,
 status nvarchar(11) NULL DEFAULT 'open',
 financial_status nvarchar(15) NULL DEFAULT NULL,
 amount DECIMAL(32,12) DEFAULT NULL,
 currency_id BIGINT DEFAULT NULL,
 percentage decimal(22,10) DEFAULT NULL,
 start_date date DEFAULT NULL,
 due_date date DEFAULT NULL,
 createdOn datetime NULL,
 createdBy bigint NULL,
 modifiedOn datetime NULL,
 modifiedBy bigint NULL,
 channel nvarchar(3) DEFAULT NULL
 );
GO

ALTER TABLE contract_milestone
  ADD CONSTRAINT fk_contract_milestone_1 FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contract_milestone
  ADD CONSTRAINT fk_contract_milestone_2 FOREIGN KEY (currency_id) REFERENCES iso_currencies (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO

IF OBJECT_ID('dbo.contract_milestone_documents', 'U') IS NOT NULL DROP TABLE dbo.contract_milestone_documents;
GO
CREATE TABLE contract_milestone_documents (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 document_id BIGINT NOT NULL,
 milestone_id BIGINT NOT NULL,
 );
GO

ALTER TABLE contract_milestone_documents
    ADD CONSTRAINT fk_contract_milestone_documents_1 FOREIGN KEY (document_id) REFERENCES documents_management_system (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO
ALTER TABLE contract_milestone_documents
  ADD CONSTRAINT fk_contract_milestone_documentse_2 FOREIGN KEY (milestone_id) REFERENCES contract_milestone (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
GO



ALTER TABLE contract ADD milestone_visible_to_cp TINYINT NULL DEFAULT '0';
GO