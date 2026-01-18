ALTER TABLE [dbo].[contract_type_language]
ADD CONSTRAINT DF_contract_type_language_applies_to -- You can choose a descriptive name for the constraint
DEFAULT 'contract' FOR [applies_to];
GO 