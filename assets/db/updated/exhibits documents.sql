CREATE TABLE [dbo].[case_exhibit_document] (
    [id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [exhibit_id] BIGINT NOT NULL,
    [document] BIGINT NOT NULL
);

ALTER TABLE [dbo].[case_exhibit_document] WITH CHECK ADD CONSTRAINT [fk_case_exhibit_document_1]
    FOREIGN KEY ([exhibit_id]) REFERENCES [dbo].exhibit ([id]);

ALTER TABLE [dbo].[case_exhibit_document] WITH CHECK ADD CONSTRAINT [fk_case_exhibit_document_2]
    FOREIGN KEY ([document]) REFERENCES [dbo].[documents_management_system] ([id])
    ON DELETE CASCADE ON UPDATE CASCADE;
