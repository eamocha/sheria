drop table if exists contract_numbering_formats
go
CREATE TABLE contract_numbering_formats (
    id INT PRIMARY KEY IDENTITY(1,1),
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    pattern VARCHAR(100) NOT NULL,
    example VARCHAR(100) NOT NULL,
    prefix VARCHAR(20) DEFAULT 'CT',
    suffix VARCHAR(20) DEFAULT NULL,
    fixed_code VARCHAR(20) DEFAULT NULL, -- For your "070" code
    sequence_reset VARCHAR(20) CHECK (sequence_reset IN ('never', 'monthly', 'yearly', 'daily')),
    sequence_length INT DEFAULT 3,
    is_active BIT DEFAULT 1,
	last_sequence INT DEFAULT 0,
    last_reset_date DATE NULL,
    created_at DATETIME2 DEFAULT SYSDATETIME()
);
CREATE UNIQUE INDEX ux_one_active_format
ON contract_numbering_formats (is_active)
WHERE is_active = 1;




-- Insert your formats
INSERT INTO contract_numbering_formats (
    name, description, pattern, example, prefix, suffix, fixed_code, 
    sequence_reset, sequence_length, is_active
) VALUES
('Sheria360 Standard Format', 'Your current format with fixed prefix', 
 'PREFIXSEQ/MM/YYYY', 'S360/SCM/070/001/08/2023', 'CA/SCM/070/', NULL, NULL, 
 'never', 3, 1),

('Monthly Reset', 'Resets sequence each month', 
 'PREFIX/YYYY/MM/SEQ', 'CT/2023/08/001', 'CT', NULL, NULL, 
 'monthly', 3, 0),

('Department Monthly', 'Includes department code', 
 'PREFIX/DEPT/YYYY/MM/SEQ', 'CT/FIN/2023/08/001', 'CT', NULL, NULL, 
 'monthly', 3, 0),

('Yearly Reset', 'Resets sequence each year', 
 'PREFIX/YYYY/SEQ', 'CT/2023/001', 'CT', NULL, NULL, 
 'yearly', 4, 0),

('Continuous', 'Never resets sequence', 
 'PREFIX/SEQ', 'CT/0001', 'CT', NULL, NULL, 
 'never', 5, 0);
 
 GO
 
 CREATE OR ALTER PROCEDURE sp_get_new_contract_ref_number
    @deptCode NVARCHAR(20) = NULL,
    @newRefNumber NVARCHAR(200) OUTPUT
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @formatId INT,
            @pattern NVARCHAR(100),
            @prefix NVARCHAR(20),
            @suffix NVARCHAR(20),
            @fixed_code NVARCHAR(20),
            @sequence_reset NVARCHAR(20),
            @sequence_length INT,
            @last_sequence INT,
            @last_reset_date DATE,
            @next_sequence INT,
            @today DATE = CAST(GETDATE() AS DATE),
            @year CHAR(4) = FORMAT(GETDATE(), 'yyyy'),
            @month CHAR(2) = FORMAT(GETDATE(), 'MM'),
            @day CHAR(2) = FORMAT(GETDATE(), 'dd');

    -- 1. Get active format (lock it for update)
    SELECT TOP 1
        @formatId = id,
        @pattern = pattern,
        @prefix = prefix,
        @suffix = suffix,
        @fixed_code = fixed_code,
        @sequence_reset = sequence_reset,
        @sequence_length = sequence_length,
        @last_sequence = last_sequence,
        @last_reset_date = last_reset_date
    FROM contract_numbering_formats WITH (UPDLOCK, ROWLOCK)
    WHERE is_active = 1;

    IF @formatId IS NULL
    BEGIN
        RAISERROR('No active contract numbering format found.', 16, 1);
        RETURN;
    END

    -- 2. Determine if reset is required
    DECLARE @resetNeeded BIT = 0;

    IF @sequence_reset = 'yearly' 
       AND (@last_reset_date IS NULL OR YEAR(@last_reset_date) <> YEAR(@today))
        SET @resetNeeded = 1;
    ELSE IF @sequence_reset = 'monthly' 
       AND (@last_reset_date IS NULL OR FORMAT(@last_reset_date, 'yyyyMM') <> FORMAT(@today, 'yyyyMM'))
        SET @resetNeeded = 1;
    ELSE IF @sequence_reset = 'daily' 
       AND (@last_reset_date IS NULL OR @last_reset_date <> @today)
        SET @resetNeeded = 1;

    IF @resetNeeded = 1
        SET @last_sequence = 0;

    -- 3. Increment sequence
    SET @next_sequence = @last_sequence + 1;

    -- 4. Update format table
    UPDATE contract_numbering_formats
    SET last_sequence = @next_sequence,
        last_reset_date = @today
    WHERE id = @formatId;

    -- 5. Pad sequence
    DECLARE @seqStr NVARCHAR(20) = RIGHT(REPLICATE('0', @sequence_length) + CAST(@next_sequence AS NVARCHAR), @sequence_length);

    -- 6. Replace tokens in pattern
    SET @newRefNumber = REPLACE(@pattern, 'PREFIX', ISNULL(@prefix,''));
    SET @newRefNumber = REPLACE(@newRefNumber, 'SEQ', @seqStr);
    SET @newRefNumber = REPLACE(@newRefNumber, 'YYYY', @year);
    SET @newRefNumber = REPLACE(@newRefNumber, 'MM', @month);
    SET @newRefNumber = REPLACE(@newRefNumber, 'DD', @day);
    SET @newRefNumber = REPLACE(@newRefNumber, 'DEPT', ISNULL(@deptCode,'GEN'));
    SET @newRefNumber = REPLACE(@newRefNumber, 'SUFFIX', ISNULL(@suffix,''));
    SET @newRefNumber = REPLACE(@newRefNumber, 'FIXED', ISNULL(@fixed_code,''));
END;
