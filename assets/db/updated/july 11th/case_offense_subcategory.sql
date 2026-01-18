
-- Create the case_offense_subcategory table
CREATE TABLE case_offense_subcategory (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(255) NOT NULL,
    offense_type_id BIGINT NOT NULL,
    is_active BIT DEFAULT 1,
    
    -- Foreign key constraint referencing case_types(id)
    CONSTRAINT FK_case_offense_subcategory_case_types 
    FOREIGN KEY (offense_type_id) 
    REFERENCES case_types(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- Create an index for better performance on the foreign key
CREATE INDEX IX_case_offense_subcategory_offense_type_id 
ON case_offense_subcategory(offense_type_id);

-- Optional: Add unique constraint for name+offense_type_id combination
ALTER TABLE case_offense_subcategory
ADD CONSTRAINT UQ_case_offense_subcategory_name_type 
UNIQUE (name, offense_type_id);