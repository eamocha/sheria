CREATE TABLE criminal_case_details (
    id INT IDENTITY(1,1) PRIMARY KEY,
    case_id BIGINT NOT NULL,
    origin_of_case NVARCHAR(255) NOT NULL,
    offence_subcategory_id BIGINT  NULL,
    status_of_case NVARCHAR(100) NOT NULL,
    initial_entry_document_id BIGINT NULL,
    authorization_document_id BIGINT NULL,
    date_investigation_authorized DATE NULL,
	
    
    -- Foreign key constraint referencing legal_cases(id)
    CONSTRAINT FK_criminal_case_details_legal_cases 
    FOREIGN KEY (case_id) 
    REFERENCES legal_cases(id)
    ON DELETE CASCADE,
    
    -- Optional: Add indexes for better performance
    INDEX IX_criminal_case_details_case_id NONCLUSTERED (case_id),
    INDEX IX_criminal_case_details_status NONCLUSTERED (status_of_case)
);
