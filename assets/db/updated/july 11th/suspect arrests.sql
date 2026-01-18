CREATE TABLE suspect_arrest (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    case_id BIGINT NOT NULL,
    arrest_date DATE NOT NULL,
    arrested_contact_id BIGINT NOT NULL,
    arrested_gender NVARCHAR(50) NULL,
    arrested_age BIGINT NULL,
    arrest_police_station NVARCHAR(255) NOT NULL,
    arrest_ob_number NVARCHAR(100) NULL,
    arrest_case_file_number NVARCHAR(100) NULL,
    arrest_attachments BIGINT NULL, -- reference to attachments table/file id
    arrest_remarks NVARCHAR(MAX) NULL,
	arrest_location NVARCHAR(250) NULL,
	bail_status NVARCHAR(250)NULL,
    createdBy BIGINT NULL,
    createdOn DATETIME DEFAULT GETDATE(),
    modifiedBy BIGINT NULL,
    modifiedOn DATETIME NULL
);