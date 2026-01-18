CREATE TABLE departments (
    id BIGINT PRIMARY KEY IDENTITY(1,1), -- BIGINT for large integer IDs, PRIMARY KEY for uniqueness, IDENTITY(1,1) for auto-incrementing starting from 1
    name NVARCHAR(100) NOT NULL           -- NVARCHAR(100) for string names, NOT NULL as name is typically required
);