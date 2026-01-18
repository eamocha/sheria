USE [lemis]
GO

/****** Object:  Table [dbo].[conveyancing_instruments]    Script Date: 21/05/2025 16:27:50 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[conveyancing_instruments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[title] [nvarchar](255) NOT NULL,
	[instrument_type_id] [bigint] NOT NULL,
	[transaction_type] [nvarchar](50) NULL,
	[reference_number] [nvarchar](50) NULL,
	[parties] [varchar](255) NOT NULL,
	[initiated_by] [bigint] NULL,
	[assignee_id] [bigint] NULL,
	[staff_pf_no] [nvarchar](30) NULL,
	[date_initiated] [date] NOT NULL,
	[due_date] [date] NULL,
	[description] [text] NOT NULL,
	[external_counsel] [bigint] NULL,
	[property_value] [decimal](22, 2) NULL,
	[amount_requested] [decimal](22, 2) NULL,
	[amount_approved] [decimal](22, 2) NULL,
	[createdOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
	[archived] [nvarchar](3) NOT NULL,
	[channel] [nvarchar](5) NULL,
	[visible_to_CP] [tinyint] NULL,
	[date_received] [date] NULL,
	[status] [nvarchar](100) NULL,
	[assignee_team_id] [bigint] NULL,
 CONSTRAINT [pk_conveyancing_instruments] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [dbo].[conveyancing_instruments] ADD  CONSTRAINT [DF_conveyance_createdOn]  DEFAULT (getdate()) FOR [createdOn]
GO

ALTER TABLE [dbo].[conveyancing_instruments] ADD  DEFAULT ('NO') FOR [archived]
GO

ALTER TABLE [dbo].[conveyancing_instruments] ADD  DEFAULT ((0)) FOR [visible_to_CP]
GO

ALTER TABLE [dbo].[conveyancing_instruments]  WITH CHECK ADD  CONSTRAINT [FK_instruments_assignee] FOREIGN KEY([assignee_id])
REFERENCES [dbo].[users] ([id])
GO

ALTER TABLE [dbo].[conveyancing_instruments] CHECK CONSTRAINT [FK_instruments_assignee]
GO

ALTER TABLE [dbo].[conveyancing_instruments]  WITH CHECK ADD  CONSTRAINT [FK_instruments_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO

ALTER TABLE [dbo].[conveyancing_instruments] CHECK CONSTRAINT [FK_instruments_modifiedBy]
GO

ALTER TABLE [dbo].[conveyancing_instruments]  WITH CHECK ADD  CONSTRAINT [FK_instruments_type] FOREIGN KEY([instrument_type_id])
REFERENCES [dbo].[conveyancing_instrument_types] ([id])
GO

ALTER TABLE [dbo].[conveyancing_instruments] CHECK CONSTRAINT [FK_instruments_type]
GO


