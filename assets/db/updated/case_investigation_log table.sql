CREATE TABLE case_investigation_log (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    case_id BIGINT NOT NULL,
    log_date DATE NOT NULL,
    details NVARCHAR(MAX) NOT NULL,
    action_taken NVARCHAR(100), -- e.g., 'statement_recorded', 'arrest_made'
    createdBy BIGINT,
    createdOn DATETIME DEFAULT GETDATE(),
    modifiedBy BIGINT,
    modifiedOn DATETIME NULL
);
