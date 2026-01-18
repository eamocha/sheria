ALTER TABLE [lemis].[dbo].suspect_arrest
ADD
	
    associated_party_type NVARCHAR(10) NULL,
    exhibit_status NVARCHAR(250) NULL,
    officer_remarks NVARCHAR(MAX) NULL,
    officers_involved NVARCHAR(250) NULL,
    associated_party BIGINT NULL,
    location_collected NVARCHAR(250) NULL,
    current_location NVARCHAR(250) NULL,
    reason_for_temporary NVARCHAR(250) NULL,
    disposal_remarks NVARCHAR(MAX) NULL,
	   archived NVARCHAR(2) default 'no';
