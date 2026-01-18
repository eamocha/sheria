CREATE TABLE departments (
    id BIGINT PRIMARY KEY IDENTITY(1,1), 
	name NVARCHAR(100) NOT NULL          

ALTER TABLE contract
ADD department_id BIGINT NULL;  

ALTER TABLE contract
ADD CONSTRAINT FK_contract_department
    FOREIGN KEY (department_id) 
    REFERENCES departments(id);