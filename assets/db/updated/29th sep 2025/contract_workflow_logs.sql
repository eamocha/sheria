CREATE TABLE contract_workflow_steps_log (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    step_id BIGINT NOT NULL,
	contract_id BIGINT NOT NULL,
    user_id BIGINT NULL,
    action_type NVARCHAR(100) NOT NULL,
	 action_type_id BIGINT NOT NULL,
    details NVARCHAR(MAX) NULL,
    createdBy BIGINT NOT NULL 

    -- Foreign keys

    CONSTRAINT fk_step_id FOREIGN KEY (step_id)
        REFERENCES contract_status_language(id) ON DELETE CASCADE,

    CONSTRAINT fk_actor FOREIGN KEY (createdBy)
        REFERENCES users(id) ON DELETE CASCADE,
		
    CONSTRAINT fk_workflow_contract_id FOREIGN KEY (contract_id)
        REFERENCES contract(id) ON DELETE CASCADE
);



