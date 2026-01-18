alter table conveyancing_instruments add parties_id bigint null
Go
alter table conveyancing_instruments add contact_type NVARCHAR(20) Null

ALTER TABLE dbo.conveyancing_instruments
DROP CONSTRAINT CHK_amounts;
