-- Drop the existing 'category' column
ALTER TABLE opinions
DROP COLUMN category;

-- Add the 'category' column with the specified data type and default value
ALTER TABLE opinions
ADD category NVARCHAR(20) DEFAULT 'opinions';