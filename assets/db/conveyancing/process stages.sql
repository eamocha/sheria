USE [lemis]
GO

/****** Object:  Table [dbo].[conveyancing_process_stages]    Script Date: 21/05/2025 16:28:21 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[conveyancing_process_stages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](100) NOT NULL,
	[description] [nvarchar](500) NULL,
	[sequence_order] [bigint] NOT NULL,
	[is_active] [bit] NOT NULL,
	[created_at] [smalldatetime] NOT NULL,
	[updated_at] [smalldatetime] NOT NULL,
 CONSTRAINT [pk_conveyancing_process_stages] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[conveyancing_process_stages] ADD  DEFAULT ((1)) FOR [is_active]
GO

ALTER TABLE [dbo].[conveyancing_process_stages] ADD  DEFAULT ('CURRENT_TIMESTAMP') FOR [created_at]
GO

ALTER TABLE [dbo].[conveyancing_process_stages] ADD  DEFAULT ('CURRENT_TIMESTAMP') FOR [updated_at]
GO


