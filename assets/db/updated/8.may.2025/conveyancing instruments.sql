IF NOT EXISTS (
    SELECT * FROM sys.columns 
    WHERE object_id = OBJECT_ID('conveyancing_instruments') 
    AND name = 'requested_by'
)
BEGIN
    ALTER TABLE conveyancing_instruments ADD requested_by bigint NULL;
END