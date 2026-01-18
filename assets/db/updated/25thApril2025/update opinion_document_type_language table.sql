ALTER TABLE [dbo].[opinion_document_type_language]
ADD [applies_to] nvarchar(15) NULL
CONSTRAINT DF_opinion_document_type_language_applies_to DEFAULT 'opinions'
GO