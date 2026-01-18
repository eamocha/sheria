CREATE TABLE [dbo].[email_templates] (
    [id]                   INT            IDENTITY (1, 1) NOT NULL,
    [template_key]         NVARCHAR (100) NOT NULL,
    [template_name]        NVARCHAR (255) NOT NULL,
    [subject]              NVARCHAR (255) NULL,
    [body_content]         NVARCHAR (MAX) NOT NULL,
    [is_active]            BIT            CONSTRAINT [DF_email_templates_is_active] DEFAULT ((1)) NOT NULL,
    [variable_count]       INT            NOT NULL,
    [last_modified_by]     INT            NULL,
    [updated_at]           DATETIME2 (0)  CONSTRAINT [DF_email_templates_updated_at] DEFAULT (GETDATE()) NOT NULL,
    
    -- Constraints
    CONSTRAINT [PK_email_templates] PRIMARY KEY CLUSTERED ([id] ASC),
    CONSTRAINT [UQ_email_templates_key] UNIQUE NONCLUSTERED ([template_key] ASC)
);
GO

-- Optional: Create an index on template_key for faster lookup
CREATE NONCLUSTERED INDEX [IX_email_templates_key]
ON [dbo].[email_templates] ([template_key]);
GO