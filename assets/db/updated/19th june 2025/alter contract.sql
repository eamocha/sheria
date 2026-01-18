BEGIN TRY
    BEGIN TRANSACTION;

    -- Check if any of the columns already exist to avoid errors
    IF NOT EXISTS (
        SELECT 1 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = 'dbo' 
            AND TABLE_NAME = 'contract' 
            AND COLUMN_NAME IN (
                'contract_duration',
                'perf_security_commencement_date',
                'perf_security_expiry_date',
                'expected_completion_date',
                'actual_completion_date',
                'advance_payment_guarantee',
                'letter_of_credit_details'
            )
    )
    BEGIN
        -- Add new columns to the contract table
        ALTER TABLE [lemis].[dbo].[contract]
        ADD 
            contract_duration INT NULL, -- Duration in days
            perf_security_commencement_date DATE NULL, -- Start date for performance security
            perf_security_expiry_date DATE NULL, -- End date for performance security
            expected_completion_date DATE NULL, -- Planned completion date
            actual_completion_date DATE NULL, -- Actual completion date
            advance_payment_guarantee text NULL, -- Advance payment guarantee details
            letter_of_credit_details text NULL; -- Letter of credit details

        -- Verify the changes
        SELECT 
            COLUMN_NAME, 
            DATA_TYPE, 
            CHARACTER_MAXIMUM_LENGTH, 
            IS_NULLABLE
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = 'dbo' 
            AND TABLE_NAME = 'contract' 
            AND COLUMN_NAME IN (
                'contract_duration',
                'perf_security_commencement_date',
                'perf_security_expiry_date',
                'expected_completion_date',
                'actual_completion_date',
                'advance_payment_guarantee',
                'letter_of_credit_details'
            );

        PRINT 'Successfully added new columns to the contract table.';
    END
    ELSE
    BEGIN
        PRINT 'One or more columns already exist in the contract table. No changes made.';
    END;

    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    IF @@TRANCOUNT > 0
        ROLLBACK TRANSACTION;

    SELECT 
        ERROR_NUMBER() AS ErrorNumber,
        ERROR_MESSAGE() AS ErrorMessage;
END CATCH;