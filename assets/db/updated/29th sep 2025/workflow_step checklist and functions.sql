CREATE TABLE contract_workflow_step_checklist (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    step_id BIGINT NOT NULL,
    item_text NVARCHAR(250) NOT NULL,
    input_type VARCHAR(50) NOT NULL DEFAULT 'yesno', -- Options: yesno, text, date, number, etc.
    is_required BIT NOT NULL DEFAULT 1,
	sort_order BIGINT DEFAULT 0,
    FOREIGN KEY (step_id) REFERENCES contract_status_language(id) ON DELETE CASCADE
);

Go
-- First, check if the constraint already exists.................................................
IF NOT EXISTS (
    SELECT * 
    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_TYPE = 'UNIQUE' 
      AND TABLE_NAME = 'contract_workflow_step_checklist'
      AND CONSTRAINT_NAME = 'UQ_contract_workflow_step_checklist_step_item'
)
BEGIN
    -- Add the unique constraint
    ALTER TABLE [dbo].[contract_workflow_step_checklist]
    ADD CONSTRAINT [UQ_contract_workflow_step_checklist_step_item] 
    UNIQUE ([step_id], [item_text]);

    PRINT 'Unique constraint added successfully';
END
ELSE
BEGIN
    PRINT 'Unique constraint already exists';
END
GO


CREATE TABLE contract_workflow_step_functions (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    step_id BIGINT NOT NULL,
    function_name VARCHAR(100) NOT NULL,
    label VARCHAR(255) NOT NULL,
    icon_class VARCHAR(100) NOT NULL,
	 sort_order bigint default 0,
    data_action VARCHAR(100),
    created_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (step_id) REFERENCES contract_status_language(id) ON DELETE CASCADE
);

-- First, check if the constraint already exists
IF NOT EXISTS (
    SELECT * 
    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_TYPE = 'UNIQUE' 
    AND TABLE_NAME = 'contract_workflow_step_functions'
    AND CONSTRAINT_NAME = 'UQ_contract_workflow_step_functions_step_function'
)
BEGIN
    -- Add the unique constraint
    ALTER TABLE [dbo].[contract_workflow_step_functions]
    ADD CONSTRAINT [UQ_contract_workflow_step_functions_step_function] 
    UNIQUE ([step_id], [function_name])
    
    PRINT 'Unique constraint added successfully'
END
ELSE
BEGIN
    PRINT 'Unique constraint already exists'
END
GO