
--DROP TABLE IF EXISTS [dbo].[conveyancing_activity]
--GO

/****** Object:  Table [dbo].[conveyancing_activity]    Script Date: 30/04/2025 15:16:16 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[conveyancing_activity](
 [id] [bigint] NOT NULL PRIMARY KEY IDENTITY(1,1),
	[conveyancing_instrument_id] [bigint] NULL,
	[activity_type] bigint NULL,
	[activity_details] [text] NULL,
	[performed_by] [bigint] NULL,
	[activity_date] [bigint] NULL,
	[remarks] [nvarchar](250) NULL,
    [activity_status] [nvarchar](50) NULL,  -- Added activity status
    [actor_display_text] [nvarchar](255) NULL,  -- Added actor display text
    [actor_call_toaction_buttontext] [nvarchar](255) NULL,  -- Added actor call-to-action button text
    [modifiedOn] DATETIME,  -- Added modifiedOn
    [modifiedBy] BIGINT,  -- Added modifiedBy
    FOREIGN KEY ([conveyancing_instrument_id]) REFERENCES [dbo].[conveyancing_instruments]([id]),
    FOREIGN KEY ([activity_type]) REFERENCES [dbo].[conveyancing_activity_type]([id]),
    FOREIGN KEY ([performed_by]) REFERENCES [dbo].[users]([id]),
    FOREIGN KEY ([modifiedBy]) REFERENCES [dbo].[users]([id])
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
