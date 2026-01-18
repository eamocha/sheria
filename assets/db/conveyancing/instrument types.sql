USE [lemis]
GO

/****** Object:  Table [dbo].[conveyancing_instrument_types]    Script Date: 21/05/2025 16:27:00 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[conveyancing_instrument_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[applies_to] [nvarchar](15) NULL,
	[createdOn] [datetime] NOT NULL,
	[modifiedOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedBy] [bigint] NULL,
 CONSTRAINT [pk_conveyancing_instrument_types] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_instrument_name_applies] UNIQUE NONCLUSTERED 
(
	[name] ASC,
	[applies_to] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[conveyancing_instrument_types] ADD  DEFAULT ('CURRENT_TIMESTAMP') FOR [createdOn]
GO


