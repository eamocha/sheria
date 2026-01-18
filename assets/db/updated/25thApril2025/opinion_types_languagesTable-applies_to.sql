ALTER TABLE [dbo].opinion_types_languages
ADD [applies_to] nvarchar(15) NULL
CONSTRAINT DF_opinion_types_languages_applies_to DEFAULT 'Opinions'
GO