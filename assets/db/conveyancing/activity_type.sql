USE [lemis]
GO

/****** Object:  Table [dbo].[conveyancing_activity_type]    Script Date: 21/05/2025 16:25:30 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[conveyancing_activity_type](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](max) NULL,
	[createdOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
 CONSTRAINT [pk_conveyancing_activity_type] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_activity_type_name] UNIQUE NONCLUSTERED 
(
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [dbo].[conveyancing_activity_type]  WITH CHECK ADD  CONSTRAINT [FK_activity_type_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO

ALTER TABLE [dbo].[conveyancing_activity_type] CHECK CONSTRAINT [FK_activity_type_createdBy]
GO

ALTER TABLE [dbo].[conveyancing_activity_type]  WITH CHECK ADD  CONSTRAINT [FK_activity_type_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO

ALTER TABLE [dbo].[conveyancing_activity_type] CHECK CONSTRAINT [FK_activity_type_modifiedBy]
GO


