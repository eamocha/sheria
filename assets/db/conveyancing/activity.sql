USE [lemis]
GO

/****** Object:  Table [dbo].[conveyancing_activity]    Script Date: 21/05/2025 16:24:37 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[conveyancing_activity](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[conveyancing_instrument_id] [bigint] NULL,
	[activity_type_id] [bigint] NULL,
	[action] [nvarchar](50) NOT NULL,
	[activity_details] [nvarchar](max) NULL,
	[activity_status] [nvarchar](50) NULL,
	[modifiedOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
	[createdOn] [datetime] NOT NULL,
	[createdBy] [bigint] NOT NULL,
	[createdByChannel] [nvarchar](3) NULL,
 CONSTRAINT [pk_conveyancing_activity] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [dbo].[conveyancing_activity] ADD  CONSTRAINT [DF_conveyancing_activity_createdOn]  DEFAULT (getdate()) FOR [createdOn]
GO

ALTER TABLE [dbo].[conveyancing_activity]  WITH CHECK ADD  CONSTRAINT [FK_activity_instrument] FOREIGN KEY([conveyancing_instrument_id])
REFERENCES [dbo].[conveyancing_instruments] ([id])
GO

ALTER TABLE [dbo].[conveyancing_activity] CHECK CONSTRAINT [FK_activity_instrument]
GO


