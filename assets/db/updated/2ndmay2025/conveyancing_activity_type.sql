
--DROP TABLE IF EXISTS [dbo].[conveyancing_activity_type]
--GO

/****** Object:  Table [dbo].[conveyancing_activity_type]    Script Date: 30/04/2025 15:16:16 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[conveyancing_activity_type](
    [id] [bigint] NOT NULL PRIMARY KEY IDENTITY(1,1),
    [name] [nvarchar](255) NOT NULL,
    [description] [nvarchar](max) NULL,
    [createdOn] DATETIME,
    [createdBy] BIGINT,
    [modifiedOn] DATETIME,
    [modifiedBy] BIGINT
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[conveyancing_activity_type]
ADD CONSTRAINT FK_conveyancing_activity_type_createdBy FOREIGN KEY (createdBy) REFERENCES dbo.users(id);

ALTER TABLE [dbo].[conveyancing_activity_type]
ADD CONSTRAINT FK_conveyancing_activity_type_modifiedBy FOREIGN KEY (modifiedBy) REFERENCES dbo.users(id);
GO
