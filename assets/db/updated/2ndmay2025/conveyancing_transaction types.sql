-- SQL code to create the conveyancing_instrument_types table
--DROP TABLE IF EXISTS conveyancing_transaction_types 
--GO
CREATE TABLE conveyancing_transaction_types (
    id BIGINT PRIMARY KEY IDENTITY(1,1), -- Unique identifier for each instrument type, auto-incremented
    name NVARCHAR(255) NOT NULL,       -- Name of the instrument type (e.g., 'Deed', 'Mortgage')
    applies_to NVARCHAR(15) NULL,    --  Indicates where the instrument applies (e.g., 'Land', 'Property').  NULL if not applicable.
    createdOn DATETIME NOT NULL,    -- Timestamp when the record was created
    modifiedOn DATETIME NULL,       -- Timestamp when the record was last modified
    createdBy bigint  NULL,  -- User who created the record
    modifiedBy bigint  NULL     -- User who last modified the record
);

-- Optional: Add a default constraint for created_on to automatically set the current date and time
ALTER TABLE conveyancing_transaction_types
ADD CONSTRAINT DF_conveyancing_transaction_types_created_on
DEFAULT GETDATE() FOR createdon;
GO -- Batch separator

-- Add a trigger to automatically update modified_on whenever the record is updated
CREATE TRIGGER TR_conveyancing_transaction_types_modified_on
ON conveyancing_transaction_types
AFTER UPDATE
AS
BEGIN
    UPDATE conveyancing_transaction_types
    SET modifiedOn = GETDATE()
    WHERE id IN (SELECT id FROM inserted);
END;
GO -- Batch separator

-- Add foreign key constraints to created_by and modified_by
ALTER TABLE conveyancing_transaction_types
ADD CONSTRAINT FK_conveyancing_transaction_types_created_by
FOREIGN KEY (createdBy) REFERENCES users(id);

ALTER TABLE conveyancing_transaction_types
ADD CONSTRAINT FK_conveyancing_transaction_types_by
FOREIGN KEY (modifiedBy) REFERENCES users(id);
GO

-- Optionally, you can add some initial data:
INSERT INTO conveyancing_transaction_types (name, applies_to, createdBy)
VALUES
      ('Deed of Sale', 'Land', '1'),
    ('Mortgage Deed', 'Property', '1'),
    ('Lease Agreement', 'Land', '1'),
     ('Charge', 'Land', '1'),
     ('Other', 'Other', '1'),
    ('Transfer Certificate', 'Land', '1');
GO

