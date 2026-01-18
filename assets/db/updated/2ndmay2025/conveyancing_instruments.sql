
/****** Object:  Table [dbo].[conveyancing_instruments]    Script Date: 27/04/2025 17:02:44 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO
--DROP TABLE IF EXISTS [dbo].[conveyancing_instruments];
--GO
CREATE TABLE [dbo].[conveyancing_instruments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[title] [nvarchar](255) NOT NULL,
	[instrument_type_id] [bigint] NOT NULL,
	transaction_type nvarchar(50) null,
	reference_number nvarchar (50) null,
	[parties] [varchar](255) NOT NULL,
	[initiated_by] bigint NULL,
	assignee_id bigint NULL,
	[staff_pf_no] [nvarchar](30) NULL,
	[date_initiated] [date] NOT NULL,
	[due_date] [date]  NULL,
	[description] [text] NOT NULL,
	external_counsel [bigint] NULL,
	property_value  decimal(22,2) NULL,
	amount_requested decimal(22,2) NULL,
	amount_approved  decimal(22,2) NULL,
	[createdOn] [smalldatetime]  DEFAULT GETDATE(),
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[archived] [nvarchar](3) NOT NULL,
	channel nvarchar (5) null,
	visible_to_CP tinyint null,
	date_received date null,
	status [nvarchar] (100) 
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO


-- Add foreign key constraints to conveyancing_instruments table

ALTER TABLE [dbo].[conveyancing_instruments]
ADD CONSTRAINT FK_conveyancing_instruments_type
FOREIGN KEY ([instrument_type_id]) REFERENCES [dbo].[conveyancing_instrument_types]([id]);

ALTER TABLE [dbo].[conveyancing_instruments]
ADD CONSTRAINT FK_conveyancing_instruments_assignee_id
FOREIGN KEY ([assignee_id]) REFERENCES [dbo].[users]([id]);

ALTER TABLE [dbo].[conveyancing_instruments]
ADD CONSTRAINT FK_conveyancing_instruments_modifiedBy
FOREIGN KEY ([modifiedBy]) REFERENCES [dbo].[users]([id]);
GO

-- Add a trigger to automatically update modified_on whenever the record is updated
CREATE TRIGGER TR_conveyancing_instrument_modified_on
ON [dbo].[conveyancing_instruments]
AFTER UPDATE
AS
BEGIN
    UPDATE [dbo].[conveyancing_instruments]
    SET modifiedOn = GETDATE()
    WHERE id IN (SELECT id FROM inserted);
END;
GO


