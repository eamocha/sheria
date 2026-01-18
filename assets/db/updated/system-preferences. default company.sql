-- Check if the preference already exists before inserting
IF NOT EXISTS (SELECT 1 FROM [dbo].[system_preferences] 
               WHERE [groupName] = 'DefaultValues' AND [keyName] = 'defaultCompany')
BEGIN
    INSERT INTO [dbo].[system_preferences] 
        ([groupName], [keyName], [keyValue])
    VALUES 
        ('DefaultValues', 'defaultCompany', NULL);
    
    PRINT 'Default company preference added successfully';
END
ELSE
BEGIN
    PRINT 'Default company preference already exists';
END
---------------------------------- authentyication type------
-- Check if the preference already exists before inserting
IF NOT EXISTS (SELECT 1 FROM [dbo].[system_preferences] 
               WHERE [groupName] = 'SMSGateway' AND [keyName] = 'smsAuthType')
BEGIN
    INSERT INTO [dbo].[system_preferences] 
        ([groupName], [keyName], [keyValue])
    VALUES 
        ('SMSGateway', 'smsAuthType', NULL);
    
END
ELSE
BEGIN
    PRINT 'Key:Value already exists';
END

