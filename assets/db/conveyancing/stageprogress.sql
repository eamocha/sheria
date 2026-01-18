USE [lemis]
GO

/****** Object:  Table [dbo].[conveyancing_stage_progress]    Script Date: 21/05/2025 16:28:42 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[conveyancing_stage_progress](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[instrument_id] [bigint] NOT NULL,
	[stage_id] [bigint] NOT NULL,
	[status] [nvarchar](20) NOT NULL,
	[start_date] [datetime] NULL,
	[completion_date] [datetime] NULL,
	[updated_by] [bigint] NOT NULL,
	[updated_on] [datetime] NOT NULL,
	[comments] [nvarchar](max) NULL,
 CONSTRAINT [pk_conveyancing_stage_progress] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [dbo].[conveyancing_stage_progress] ADD  CONSTRAINT [DF_conveyancing_stage_progress_updated_on]  DEFAULT (getdate()) FOR [updated_on]
GO

ALTER TABLE [dbo].[conveyancing_stage_progress]  WITH CHECK ADD  CONSTRAINT [FK_progress_stage_id] FOREIGN KEY([stage_id])
REFERENCES [dbo].[conveyancing_process_stages] ([id])
GO

ALTER TABLE [dbo].[conveyancing_stage_progress] CHECK CONSTRAINT [FK_progress_stage_id]
GO

ALTER TABLE [dbo].[conveyancing_stage_progress]  WITH CHECK ADD  CONSTRAINT [FK_progress_updated_by] FOREIGN KEY([updated_by])
REFERENCES [dbo].[users] ([id])
GO

ALTER TABLE [dbo].[conveyancing_stage_progress] CHECK CONSTRAINT [FK_progress_updated_by]
GO


