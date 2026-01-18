-- Drop default constraint for mfaToken if it exists
DECLARE @ConstraintName_mfaToken nvarchar(128);
SELECT @ConstraintName_mfaToken = name 
FROM sys.default_constraints 
WHERE parent_object_id = OBJECT_ID(N'dbo.user_profiles') 
AND parent_column_id = (SELECT column_id FROM sys.columns WHERE name = N'mfaToken' AND object_id = OBJECT_ID(N'dbo.user_profiles'));

IF @ConstraintName_mfaToken IS NOT NULL
BEGIN
    EXEC('ALTER TABLE dbo.user_profiles DROP CONSTRAINT ' + @ConstraintName_mfaToken);
END;

-- Drop mfaToken column if it exists
IF EXISTS (SELECT 1 
           FROM sys.columns 
           WHERE Name = N'mfaToken' 
           AND Object_ID = Object_ID(N'dbo.user_profiles'))
BEGIN
    ALTER TABLE dbo.user_profiles
    DROP COLUMN mfaToken;
END;

-- Drop default constraint for mfaTokenTimeCounter if it exists
DECLARE @ConstraintName_mfaTokenTimeCounter nvarchar(128);
SELECT @ConstraintName_mfaTokenTimeCounter = name 
FROM sys.default_constraints 
WHERE parent_object_id = OBJECT_ID(N'dbo.user_profiles') 
AND parent_column_id = (SELECT column_id FROM sys.columns WHERE name = N'mfaTokenTimeCounter' AND object_id = OBJECT_ID(N'dbo.user_profiles'));

IF @ConstraintName_mfaTokenTimeCounter IS NOT NULL
BEGIN
    EXEC('ALTER TABLE dbo.user_profiles DROP CONSTRAINT ' + @ConstraintName_mfaTokenTimeCounter);
END;

-- Drop mfaTokenTimeCounter column if it exists
IF EXISTS (SELECT 1 
           FROM sys.columns 
           WHERE Name = N'mfaTokenTimeCounter' 
           AND Object_ID = Object_ID(N'dbo.user_profiles'))
BEGIN
    ALTER TABLE dbo.user_profiles
    DROP COLUMN mfaTokenTimeCounter;
END;

-- Drop default constraint for mfaTokenChecked if it exists
DECLARE @ConstraintName_mfaTokenChecked nvarchar(128);
SELECT @ConstraintName_mfaTokenChecked = name 
FROM sys.default_constraints 
WHERE parent_object_id = OBJECT_ID(N'dbo.user_profiles') 
AND parent_column_id = (SELECT column_id FROM sys.columns WHERE name = N'mfaTokenChecked' AND object_id = OBJECT_ID(N'dbo.user_profiles'));

IF @ConstraintName_mfaTokenChecked IS NOT NULL
BEGIN
    EXEC('ALTER TABLE dbo.user_profiles DROP CONSTRAINT ' + @ConstraintName_mfaTokenChecked);
END;

-- Drop mfaTokenChecked column if it exists
IF EXISTS (SELECT 1 
           FROM sys.columns 
           WHERE Name = N'mfaTokenChecked' 
           AND Object_ID = Object_ID(N'dbo.user_profiles'))
BEGIN
    ALTER TABLE dbo.user_profiles
    DROP COLUMN mfaTokenChecked;
END;