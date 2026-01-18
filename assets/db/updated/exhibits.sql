CREATE TABLE case_exhibit (
    id BIGINT PRIMARY KEY IDENTITY(1,1),
    case_id BIGINT NOT NULL,
    exhibit_label NVARCHAR(255) NOT NULL,
    description NVARCHAR(MAX) NOT NULL,
    temporary_removals NVARCHAR(MAX) NULL,
    manner_of_disposal NVARCHAR(MAX) NULL,
    date_received DATE NOT NULL,
    date_approved_for_disposal DATE NULL,
    date_disposed DATE NULL,
    createdOn DATETIME NOT NULL DEFAULT GETDATE(),
    modifiedOn DATETIME NULL DEFAULT GETDATE(),
    createdBy BIGINT NOT NULL,
    modifiedBy BIGINT NULL,

    CONSTRAINT fk_case_exhibit_case FOREIGN KEY (case_id)
        REFERENCES legal_cases(id),

    CONSTRAINT fk_case_exhibit_createdBy FOREIGN KEY (createdBy)
        REFERENCES user_profiles(user_id),

    CONSTRAINT fk_case_exhibit_modifiedBy FOREIGN KEY (modifiedBy)
        REFERENCES user_profiles(user_id)
);
