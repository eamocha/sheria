-- First, identify all duplicates
SELECT 
    [groupName],
    [keyName],
    COUNT(*) AS DuplicateCount
FROM [dbo].[system_preferences]
GROUP BY [groupName], [keyName]
HAVING COUNT(*) > 1
ORDER BY DuplicateCount DESC
GO

-- Then delete the duplicates (keeping one record for each groupName/keyName combination)
WITH Duplicates AS (
    SELECT 
        [groupName],
        [keyName],
        [keyValue],
        ROW_NUMBER() OVER (PARTITION BY [groupName], [keyName] ORDER BY (SELECT NULL)) AS RowNum
    FROM [dbo].[system_preferences]
)
DELETE FROM Duplicates
WHERE RowNum > 1
GO