ALTER TABLE opinions add legal_question text null;
go

alter table opinions ADD opinion_file varchar(10)  DEFAULT null;
go
alter table opinions add category nchar(30) null
go


IF OBJECT_ID('dbo.opinion_document_status', 'U') IS NOT NULL DROP TABLE dbo.opinion_document_status;
GO
CREATE TABLE opinion_document_status (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
);
GO

IF OBJECT_ID('dbo.opinion_document_status_language', 'U') IS NOT NULL DROP TABLE dbo.opinion_document_status_language;
GO
CREATE TABLE opinion_document_status_language (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 status_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

ALTER TABLE opinion_document_status_language
    ADD CONSTRAINT fk_opinion_document_status_language_1 FOREIGN KEY (status_id) REFERENCES opinion_document_status (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE opinion_document_status_language
    ADD CONSTRAINT fk_opinion_document_status_language_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
IF OBJECT_ID('dbo.opinion_document_type', 'U') IS NOT NULL DROP TABLE dbo.opinion_document_type;
GO
CREATE TABLE opinion_document_type (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY
);
GO

IF OBJECT_ID('dbo.opinion_document_type_language', 'U') IS NOT NULL DROP TABLE dbo.opinion_document_type_language;
GO
CREATE TABLE opinion_document_type_language (
 id BIGINT NOT NULL PRIMARY KEY IDENTITY,
 type_id BIGINT NOT NULL,
 language_id BIGINT NOT NULL,
 name nvarchar(255) NOT NULL
);
GO

ALTER TABLE opinion_document_type_language
    ADD CONSTRAINT fk_opinion_document_type_language_1 FOREIGN KEY (type_id) REFERENCES opinion_document_type (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO
ALTER TABLE opinion_document_type_language
    ADD CONSTRAINT fk_opinion_document_type_language_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE ON UPDATE CASCADE;
GO