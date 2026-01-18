
/****** Object:  Table [dbo].[case_closure_recommendation]    Script Date: 20/06/2025 07:58:09 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[case_closure_recommendation](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[investigation_officer_recommendation] [nvarchar](250) NULL,
	[date_recommended] [date] NULL,
	[approval_remarks] [nvarchar](250) NULL,
	[approval_date] [date] NULL,
	[approval_status] [nvarchar](50) NULL,
	[approvedBy] [bigint] NULL,
	[createdOn] [datetime] NOT NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
	[recommendation_status] [nvarchar](50) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[case_closure_recommendation] ADD  DEFAULT (getdate()) FOR [createdOn]
GO

ALTER TABLE [dbo].[case_closure_recommendation]  WITH CHECK ADD  CONSTRAINT [FK_CaseClosure_ApprovedBy_Users] FOREIGN KEY([approvedBy])
REFERENCES [dbo].[users] ([id])
GO

ALTER TABLE [dbo].[case_closure_recommendation] CHECK CONSTRAINT [FK_CaseClosure_ApprovedBy_Users]
GO

ALTER TABLE [dbo].[case_closure_recommendation]  WITH CHECK ADD  CONSTRAINT [FK_CaseClosure_CreatedBy_Users] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO

ALTER TABLE [dbo].[case_closure_recommendation] CHECK CONSTRAINT [FK_CaseClosure_CreatedBy_Users]
GO

ALTER TABLE [dbo].[case_closure_recommendation]  WITH CHECK ADD  CONSTRAINT [FK_CaseClosure_LegalCase] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO

ALTER TABLE [dbo].[case_closure_recommendation] CHECK CONSTRAINT [FK_CaseClosure_LegalCase]
GO


