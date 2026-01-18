CREATE TABLE legal_case_risks (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    case_id BIGINT NOT NULL,
    riskLevel NVARCHAR(50) NOT NULL,
    risk_type NVARCHAR(100) NOT NULL,
    details NVARCHAR(MAX) NULL,
    createdBy BIGINT NOT NULL,
    createdOn DATETIME NOT NULL DEFAULT GETDATE(),

    -- Foreign keys with cascading deletes
    CONSTRAINT FK_legal_case_risks_case FOREIGN KEY (case_id) 
        REFERENCES legal_cases(id) ON DELETE CASCADE,

    CONSTRAINT FK_legal_case_risks_user FOREIGN KEY (createdBy) 
        REFERENCES users(id)
);
