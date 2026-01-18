alter table contract_amendment_history add amendment_document_id bigint NULL;
alter table contract_amendment_history add amendment_approval_status NVARCHAR(20) NULL;
GO

CREATE TABLE contract_amendment_history_details (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    amendment_history_id BIGINT NOT NULL, 
    contract_id BIGINT NOT NULL,
    field_name NVARCHAR(50) NOT NULL,
    old_value NVARCHAR(MAX),
    new_value NVARCHAR(MAX),
    createdOn DATETIME DEFAULT GETDATE(),
    
    CONSTRAINT FK_contract_amendment_history_details_amendment 
        FOREIGN KEY (amendment_history_id) 
        REFERENCES contract_amendment_history(id) 
        ON DELETE CASCADE,

    CONSTRAINT FK_contract_amendment_history_details_contract 
        FOREIGN KEY (contract_id) 
        REFERENCES contract(id)
);
