
CREATE TABLE case_closure_recommendation (
    id BIGINT PRIMARY KEY IDENTITY(1,1) NOT NULL,
    case_id BIGINT NOT NULL,
    investigation_officer_recommendation NVARCHAR(250) NULL,
    date_recommended DATE NULL,
    approving_officer_remarks NVARCHAR(250) NULL,
    approval_date DATE NULL,
    approval_status NVARCHAR(50) NULL,
    approved_by BIGINT NULL, -- Made NULLable if it's not always set immediately
    createdOn DATETIME NOT NULL DEFAULT GETDATE(),
    createdBy BIGINT NULL,

    -- Foreign Key to legal_case table
    CONSTRAINT FK_CaseClosure_LegalCase FOREIGN KEY (case_id)
        REFERENCES legal_cases(id),

    -- Foreign Key to users table for approved_by
    CONSTRAINT FK_CaseClosure_ApprovedBy_Users FOREIGN KEY (approved_by)
        REFERENCES users(id),

    -- Foreign Key to users table for createdBy
    CONSTRAINT FK_CaseClosure_CreatedBy_Users FOREIGN KEY (createdBy)
        REFERENCES users(id)
);