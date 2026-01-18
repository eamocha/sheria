ALTER TABLE legal_cases
ADD 
    closure_requested_by BIGINT NULL,
    closed_by BIGINT NULL,
    closure_comments NVARCHAR(255) NULL