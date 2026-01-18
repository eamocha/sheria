-- 1. First rename the existing column
EXEC sp_rename 'opinions.instructions', 'detailed_info', 'COLUMN';

-- 2. Then add the new column
ALTER TABLE opinions
ADD background_info TEXT NULL;
ALTER TABLE opinions
ADD requester bigint NULL;
ALTER TABLE opinions
ADD channel NVARCHAR (5) NULL;
ALTER TABLE opinions
ADD is_visible_to_cp bit NULL;  