
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_investigation_log_document](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[investigation_id] [bigint] NOT NULL,
	[document] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
) WITH (
	PAD_INDEX = OFF, 
	STATISTICS_NORECOMPUTE = OFF, 
	IGNORE_DUP_KEY = OFF, 
	ALLOW_ROW_LOCKS = ON, 
	ALLOW_PAGE_LOCKS = ON, 
	OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF
) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[case_investigation_log_document]  WITH CHECK ADD CONSTRAINT [fk_case_investigation_log_doc_1] 
FOREIGN KEY([investigation_id]) REFERENCES [dbo].[case_investigation_log] ([id])
GO

ALTER TABLE [dbo].[case_investigation_log_document] CHECK CONSTRAINT [fk_case_investigation_log_doc_1]
GO

ALTER TABLE [dbo].[case_investigation_log_document]  WITH CHECK ADD CONSTRAINT [fk_case_investigation_log_doc_2] 
FOREIGN KEY([document]) REFERENCES [dbo].[documents_management_system] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO

ALTER TABLE [dbo].[case_investigation_log_document] CHECK CONSTRAINT [fk_case_investigation_log_doc_2]
GO
