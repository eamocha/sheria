  EXEC sp_rename 'conveyancing_document_type.addedon', 'createdOn', 'COLUMN';
  ALTER TABLE conveyancing_document_type
ADD modifiedOn DATETIME NULL,
    createdBy BIGINT NULL,
    modifiedBy BIGINT NULL;