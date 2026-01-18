CREATE TABLE exhibit_locations (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    longitude VARCHAR(50),
    latitude VARCHAR(50),
    description VARCHAR(MAX),
	createdBy BIGINT NULL,
    createdOn DATETIME DEFAULT GETDATE(),
);

CREATE TABLE exhibit_chain_of_movement (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    location_from BIGINT NOT NULL,
    location_to BIGINT NOT NULL,
    purpose VARCHAR(255),
    remarks VARCHAR(MAX),
    action_date_time DATETIME NOT NULL,
    officer_receiving BIGINT,
	exhibit_id BIGINT,
    createdBy BIGINT,
    createdOn DATETIME DEFAULT GETDATE(),

    FOREIGN KEY (location_from) REFERENCES exhibit_locations(id),
    FOREIGN KEY (location_to) REFERENCES exhibit_locations(id),
    FOREIGN KEY (officer_receiving) REFERENCES users(id),
	 FOREIGN KEY (createdBy) REFERENCES users(id), 
	  FOREIGN KEY (exhibit_id) REFERENCES exhibit(id) 
);

CREATE TABLE exhibit_activities_log (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    subject VARCHAR(255) NOT NULL,
	exhibit_id BIGINT,
    remarks VARCHAR(MAX),
    createdBy BIGINT,
    createdOn DATETIME DEFAULT GETDATE(),

	FOREIGN KEY (exhibit_id) references exhibit(id),
	FOREIGN KEY (createdBy) REFERENCES users(id) 
);

CREATE TABLE [dbo].[exhibit_document](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[exhibit_id] [bigint] NOT NULL,
	[document] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[exhibit_document]  WITH CHECK ADD  CONSTRAINT [fk_exhibit_document_1] FOREIGN KEY([exhibit_id])
REFERENCES [dbo].exhibit ([id])
GO

ALTER TABLE [dbo].[exhibit_document] CHECK CONSTRAINT [fk_exhibit_document_1]
GO

ALTER TABLE [dbo].[exhibit_document]  WITH CHECK ADD  CONSTRAINT [fk_exhibit_document_2] FOREIGN KEY([document])
REFERENCES [dbo].[documents_management_system] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO

ALTER TABLE [dbo].[exhibit_document] CHECK CONSTRAINT [fk_exhibit_document_2]
GO


