-- Add department_id column to user_profiles if it doesn't exist
IF NOT EXISTS (SELECT 1 
               FROM sys.columns 
               WHERE Name = N'department_id' 
               AND Object_ID = Object_ID(N'dbo.user_profiles'))
BEGIN
    ALTER TABLE dbo.user_profiles
    ADD department_id BIGINT NULL; -- Use NULL if you want to allow optional department assignments

    -- Add foreign key constraint to reference departments(id)
    ALTER TABLE dbo.user_profiles
    ADD CONSTRAINT FK_user_profiles_department_id 
    FOREIGN KEY (department_id) 
    REFERENCES dbo.departments(id);
END;