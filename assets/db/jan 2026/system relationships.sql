CREATE TABLE [dbo].[system_relationships](
    [id] [bigint] IDENTITY(1,1) NOT NULL,
    
    -- Side A (The "Source")
    [base_id] [bigint] NOT NULL,
    [base_type] [nvarchar](50) NOT NULL, -- e.g., 'correspondence', 'case', 'contract'
    
    -- Side B (The "Target")
    [target_id] [bigint] NOT NULL,
    [target_type] [nvarchar](50) NOT NULL, -- e.g., 'legal_opinion', 'conveyancing'
    
    -- Details
    [relationship_type] [nvarchar](50) NULL, -- e.g., 'reply', 'reference', 'attachment'
    [comments] [nvarchar](255) NULL,
    
    -- Metadata
    [createdBy] [bigint] NULL,
    [createdOn] [datetime] NOT NULL DEFAULT (getdate()),

    CONSTRAINT [pk_system_relationships] PRIMARY KEY CLUSTERED ([id] ASC)
) ON [PRIMARY];
GO

-- Add Index for performance on lookups
CREATE INDEX IX_Base ON [dbo].[system_relationships] (base_id, base_type);
CREATE INDEX IX_Target ON [dbo].[system_relationships] (target_id, target_type);
CREATE INDEX IX_SystemRel_Lookup ON [dbo].[system_relationships] (base_id, base_type);
 
ALTER TABLE [dbo].[system_relationships] WITH CHECK ADD CONSTRAINT [FK_sys_rel_createdBy] 
FOREIGN KEY([createdBy]) REFERENCES [dbo].[users] ([id]);
GO