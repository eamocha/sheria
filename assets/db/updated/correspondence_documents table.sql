
 CREATE TABLE correspondence_document (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(255) NOT NULL,
    size BIGINT,
    extension NVARCHAR(20), -- Corrected spelling from 'extention'
    correspondence_id BIGINT NOT NULL,
    document_type_id BIGINT,
    document_status_id BIGINT,
    comments NVARCHAR(MAX),
    createdOn DATETIME DEFAULT GETDATE(),
    modifiedOn DATETIME NULL,
    createdBy BIGINT,
    modifiedBy BIGINT,
    -- Adding Foreign Key Constraints (recommended)
    CONSTRAINT FK_CorrespondenceDocument_Correspondence FOREIGN KEY (correspondence_id)
        REFERENCES correspondences(id)
    
);