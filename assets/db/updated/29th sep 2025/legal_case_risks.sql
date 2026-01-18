--select* from legal_case_risks
CREATE TABLE legal_case_risks (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    case_id BIGINT NOT NULL,
    risk_category NVARCHAR(100) NOT NULL,
    riskLevel NVARCHAR(50) NOT NULL,
    risk_type NVARCHAR(100) NOT NULL,
    details NVARCHAR(MAX) NULL,
    mitigation NVARCHAR(MAX) NULL,
    responsible_actor_id BIGINT NULL,
    status NVARCHAR(50) NULL,
    createdBy BIGINT NOT NULL,
    createdOn DATETIME NOT NULL DEFAULT GETDATE(),

    -- Foreign keys with cascading deletes
    CONSTRAINT FK_legal_case_risks_case FOREIGN KEY (case_id) 
        REFERENCES legal_cases(id) ON DELETE CASCADE,

    CONSTRAINT FK_legal_case_risks_user FOREIGN KEY (createdBy) 
        REFERENCES users(id),

    CONSTRAINT FK_legal_case_risks_actor FOREIGN KEY (responsible_actor_id) 
        REFERENCES users(id),

    -- Ensure one risk_category per case
    CONSTRAINT UQ_legal_case_risks_case_category UNIQUE (case_id, risk_category)
);

-- Index to optimize case-based lookups
CREATE INDEX IX_legal_case_risks_case_id 
    ON legal_case_risks (case_id);


