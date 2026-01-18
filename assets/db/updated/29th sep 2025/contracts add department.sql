-- Add the department_id column (nullable)
ALTER TABLE contract 
ADD department_id BIGINT NULL;

-- Add foreign key constraint after the column exists
ALTER TABLE contract
ADD CONSTRAINT FK_contract_department
FOREIGN KEY (department_id) 
REFERENCES departments(id);

