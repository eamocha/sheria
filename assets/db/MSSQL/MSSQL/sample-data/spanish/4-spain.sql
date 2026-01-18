INSERT INTO system_preferences (groupName, keyName, keyValue) VALUES
('DefaultValues', 'shareholderVoteFactor', '1'),
('DefaultValues', 'shareholderVoteYear', '1'),
('DefaultValues', 'caseValueCurrency', 'EUR'),
('SystemValues', 'sysDaysOff', 'Sat, Sun'),
('SystemValues', 'systemTimezone', 'Europe/London'),
('SystemValues', 'hijriCalendarConverter', '0'),
('SystemValues', 'hijriCalendarFeature', '0');

INSERT INTO task_locations (name) VALUES
('Madrid'),
('Barcelona'),
('Seville'),
('Valencia'),
('Málaga');

INSERT INTO company_legal_types (name) VALUES
('Asociación General'),
('Joint Venture'),
('Sociedad de responsabilidad limitada'),
('Anónima privada'),
('Limited Partnership con acciones'),
('Pública por acciones'),
('Sociedad limitada de simple'),
('Offshore'),
('Extranjero'),
('Empresa de zona franca'),
('Propiedad exclusiva');

INSERT INTO organizations (name, currency_id, color, fiscalYearStartsOn, address1, address2, city, state, zip, country_id, website, phone, fax, mobile, organizationID, comments, status) VALUES
('My Entity', 68, 0, 1, NULL, NULL, NULL, NULL, NULL, 68, NULL, NULL, NULL, NULL, NULL, NULL, 'Active');

INSERT INTO invoice_detail_cover_page_template (organization_id, name, header, subHeader,footer, address, logo) VALUES
(1, 'Default Template', 'Default Header', 'Default SubHeader','Default Footer', 'Address', '');
INSERT INTO countries (countryCode, currencyCode, currencyName, isoNumeric, languages) VALUES
('IL', 'ILS', 'Israeli New Shekel', '092', 'en');
INSERT INTO countries_languages (country_id, language_id, name) VALUES
(250, 1, 'Israel'),(250, 2, 'اسرائيل'),(250,3,'Israël'),(250, 4, 'Israel');
-- removing/updating an account should be followed by updating all tables with account_id column and system_preferences with serialized invoice accounts
INSERT INTO accounts (organization_id, currency_id, account_type_id, name, systemAccount, description, model_id, member_id, model_name, model_type, createdBy, createdOn, modifiedBy, modifiedOn,number) VALUES
(1, 68, 3, 'Furniture & Equipment', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,1),
(1, 68, 8, 'Opening Balance Offset', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,1),
(1, 68, 8, 'Owner''s Equity', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,2),
(1, 68, 11, 'Office Supplies', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,1),
(1, 68, 11, 'Advertising & Marketing', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,2),
(1, 68, 11, 'Travel Expenses', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,3),
(1, 68, 11, 'Internet & Telephone', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,4),
(1, 68, 11, 'IT Expenses', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,5),
(1, 68, 11, 'Rent', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,6),
(1, 68, 11, 'Meals & Entertainment', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,7),
(1, 68, 11, 'Car & Taxi', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,8),
(1, 68, 11, 'Salaries', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,9),
(1, 68, 13, 'Other Expenses', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,1),
(1, 68, 9, 'Sales', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,1),
(1, 68, 9, 'Other Income', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,2),
(1, 68, 7, 'Tax Payable', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,1),
(1, 68, 11, 'Legal Expenses', 'yes', NULL, NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,10),
(1, 68, 11, 'Partner Expenses', 'yes', NULL, NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,11),
(1, 68, 7, 'Tax Receivables', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,2),
(1, 68, 18, 'Trust Asset Account', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,1),
(1, 68, 11, 'Exchange gain or loss', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,12),
(1, 68, 9, 'Discount', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,13);

INSERT INTO system_preferences (groupName, keyName, keyValue) VALUES
('MoneyCurrency', 'currencies', '68, 232'),
('UsersValues', 'userRatePerHour', 'a:1:{i:1;s:4:"1000";}');

INSERT INTO exchange_rates (currency_id, organization_id, rate) VALUES (68, 1, 1.00), (232, 1, 0.85);

INSERT INTO advisor_task_locations (name) VALUES
('Madrid'),
('Barcelona'),
('Seville'),
('Valencia'),
('Málaga');