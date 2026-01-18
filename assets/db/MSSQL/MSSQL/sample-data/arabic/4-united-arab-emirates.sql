INSERT INTO legal_case_stages (litigation, corporate) VALUES
('yes', 'no'),
('yes', 'no'),
('yes', 'no'),
('yes', 'yes'),
('yes', 'no'),
('yes', 'yes'),
('yes', 'no'),
('yes', 'yes'),
('yes', 'no');
INSERT INTO countries (countryCode, currencyCode, currencyName, isoNumeric, languages) VALUES
('IL', 'ILS', 'Israeli New Shekel', '092', 'en');
INSERT INTO countries_languages (country_id, language_id, name) VALUES
(250, 1, 'Israel'),(250, 2, 'اسرائيل'),(250,3,'Israël'),(250, 4, 'Israel');
INSERT INTO legal_case_stage_languages (legal_case_stage_id, language_id, name) VALUES
(1, 1, 'First Instance Court'),
(1, 2, 'الابتدائية'),
(1, 3, 'Tribunal de 1ere Instance'),
(1, 4, 'Tribunal de primera instancia'),
(3, 1, 'Cassation/High Court'),
(3, 2, 'العليا'),
(3, 3, 'Cour de Cassation'),
(3, 4, 'Tribunal de casación y alto'),
(2, 1, 'Appeal Court'),
(2, 2, 'الاستئناف'),
(2, 3, 'Cour d''Appel'),
(2, 4, 'Tribunal de apelación'),
(4, 1, 'Execution'),
(4, 2, 'التنفيذ'),
(4, 3, 'Execution'),
(4, 4, 'Ejecución'),
(5, 1, 'Taradi Platform'),
(5, 2, 'منصة تراضي'),
(5, 3, 'Taradi Platform'),
(5, 4, 'Taradi Platform'),
(6, 1, 'Dispute'),
(6, 2, 'النزاع'),
(6, 3, 'Dispute'),
(6, 4, 'Disputa'),
(7, 1, 'Arbitration'),
(7, 2, 'التحكيم'),
(7, 3, ' Arbitrage'),
(7, 4, 'Arbitraje'),
(8, 1, 'Other'),
(8, 2, 'أخرى'),
(8, 3, 'Autre'),
(8, 4, 'Other'),
(9, 1, 'Seek reconsideration'),
(9, 2, 'التماس اعادة النظر'),
(9, 3, 'Demander un Réexamen'),
(9, 4, 'Buscar reconsideración');

INSERT INTO case_types (name, litigation, corporate, litigationSLA, legalMatterSLA) VALUES
('جنائية','yes','no',NULL,NULL),
('تجارية','yes','no',NULL,NULL),
('آخر','yes','yes',NULL,NULL),
('عمالية','yes','no',NULL,NULL),
('إرث و حقوق ورثة','yes','yes',NULL,NULL),
('خلاف حول الإيجار','yes','no',NULL,NULL),
('إدارية','yes','no',NULL,NULL),
('ملكية فكرية','yes','no',NULL,NULL),
('عقود و اتفاقيات','no','yes',NULL,NULL),
('إستشارات','no','yes',5,NULL),
('مشاريع شركات','no','yes',NULL,NULL),
('شريعة','yes','yes',NULL,NULL),
('الاستحواذ و الدمج','yes','yes',NULL,NULL),
('مدنية','yes','no',NULL,NULL),
('تنفيذ (مالي-مباشر-أحوال)','yes','no',45,NULL),
('الزكاة و الضريبة','yes','yes',NULL,NULL),
('تركات','yes','yes',NULL,NULL),
('تأمين','yes','no',NULL,NULL),
('بنكية','yes','no',NULL,NULL),
('احتكار و منافسة','yes','no',NULL,NULL),
('احوال شخصية','yes','no',NULL,NULL),
('مالية','yes','no',NULL,NULL);

INSERT INTO case_types(name)VALUES('Case of Intellectual Property');

INSERT INTO system_preferences (groupName, keyName, keyValue) VALUES
('DefaultValues', 'shareholderVoteFactor', '1'),
('DefaultValues', 'shareholderVoteYear', '1'),
('DefaultValues', 'caseValueCurrency', 'AED'),
('SystemValues', 'sysDaysOff', 'Sun, Sat'),
('SystemValues', 'systemTimezone', 'Asia/Dubai'),
('SystemValues', 'hijriCalendarConverter', '0'),
('SystemValues', 'hijriCalendarFeature', '0');

UPDATE system_preferences SET keyValue='a:3:{i:0;s:11:"Tax Invoice";s:3:"fl1";s:7:"Facture";s:3:"fl2";s:16:"فــاتورة";}' WHERE groupName='InvoiceLanguage' and keyName='invoice';

INSERT INTO task_locations (name) VALUES
('أبو ظبي‎'),
('عجمان'),
('دبي'),
('الفجيرة'),
('رأس الخيمة'),
('الشارقة'),
('أمّ القيوين'),
('آخر');

INSERT INTO company_legal_types (name) VALUES
('الشراكة العامة'),
('المشروع المشترك'),
('ذات مسؤولية محدودة'),
('مساهمة خاصة'),
('شراكة محدودة مع أسهم'),
('مساهمة عامة'),
('شراكة المحدودة'),
('أوفشور'),
('خارجية'),
('شركة المنطقة الحرة'),
('وحيدة الملكية'),
('أوقاف'),
('حكومية'),
('شركة أجنبية'),
('شركة فرد واحد'),
('مؤسسة');

INSERT INTO court_types (name) VALUES 
('Commercial - تجارى'),
('Commercial (Plenary) - تجاري كلي'),
('Commercial (Partial) - تجاري جزئي'),
('Rents - ايجارات'),
('Criminal - جزائي'),
('Criminal (Felony) - جزائي جنايات'),
('Criminal (Misdemeanors) - جزائي جنح'),
('Summary - مستعجل'),
(' Labor - عمالي'),
(' Labor (Plenary) - عمالي كلي'),
('Labor (Partial) - عمالي جزئي'),
('Personal Status - احوال شخصية'),
('Personal Status (Family) - احوال نفس'),
('Personal Status (Financial) - احوال مال'),
('Administrative - اداري'),
('Administrative (Plenary) - اداري كلي'),
('Administrative (Partial) - اداري جزئي'),
('Civil - مدني'),
('Civil (Partial) - مدني جزئي'),
('Civil (Plenary) - مدني كلي'),
('Real Estate - عقاري'),
('Real Estate (Plenary) - عقاري كلي'),
('Real Estate (Partial) - عقاري جزئي'),
('Inheritance - تركات'),
('Execution - تنفيذ'),
('Execution (Public) - تنفيذ عام'),
('Execution (Internal) - تنفيذ داخلي'),
('Public Prosecution (Funds) - نيابة الاموال العامة'),
('Public Prosecution (Family)  - نيابة الاسرة');

INSERT INTO court_degrees (name) VALUES 
('Arbitration - تحكيم'),
('Conciliation & Settlement- توفيق و مصالحة'),
('First Instance - ابتدائي'),
('Appeal - استئناف'),
('Cassation - طعن'),
('Execution - تنفيذ'),
('Police - الشرطة'),
('Public Prosecution - النيابة العامة'),
('Summary First Instance - مستعجل ابتدائي'),
('Summary Objection - تظلم مستعجل');

INSERT INTO court_regions (name) VALUES 
('Abu Dhabi - ابوظبي'),
('Dubai - دبي'),
('Sharjah - الشارقة'),
('Ajman - عجمان'),
('Umm Al Quwain - ام القوين'),
('Ras Al Khaimah - راس الخيمة'),
('Al Ain - العين'),
('Ruwais - الرويس'),
('Al Dhafra - الظفرة'),
('Kalba - كلباء'),
('Dibba - دبا'),
('Khorfakkan - خورفكان'),
('Rahba - الرحبة'),
('Fujairah - الفجيرة');

INSERT INTO courts (name) VALUES 
('Municipality - بلدية'),
('Federal - اتحادي'),
('Citizenship & Residency - الجنسية والأقامة'),
('Labor - عمالي'),
('Abu Dhabi Commercial Concilliation & Arbitration Center (ADCCAC) - مركز ابوظبي للتوفيق والتحكيم التجاري'),
('Ras Al Khaimah Reconciliation & Commercial Arbitration Centre (RAKRCAC) - مركز رأس الخيمة للتوفيق والتحكيم التجاري'),
('Dubai International Arbitration Centre (DIAC) - مركز دبي للتحكيم الدولي'),
('International Chamber of Commerce (ICC) - غرفة التجارة الدولية'),
('Civil & Commercial - مدني و تجاري'),
('Rent Dispute Committee - لجنة النزاعات الاجارية'),
('Traffic - المرور'),
('Public Prosecution - النيابة العامة'),
('Police - الشرطة'),
('Family - الاسرة'),
('National Security - امن الدولة'),
(' Sharjah International Commercial Arbitration Centre (TAHKEEM) - مركز الشارقة للتحكيم التجاري  الدولي'),
('DIFC-LCIA Arbitration Centre - مركز دبي المالي العالمي محكمة لندن للتحكيم الدولي'),
('Umm Al Quwain Commercial Conciliation and Arbitration Center - مركز أم القيوين للتوفيق والتحكيم التجاري'),
('Ajman Commercial Conciliation and Arbitration Center (AJCCAC) - مركز عجمان للتوفيق والتحكيم التجاري'),
('Abu Dhabi Global Market Court (ADGM) -محكمة سوق أبوظبي العالمي'),
('DIFC Court -DIFC محاكم');

INSERT INTO organizations (name, currency_id, color, fiscalYearStartsOn, address1, address2, city, state, zip, country_id, website, phone, fax, mobile, organizationID, comments, status) VALUES
('My Entity', 2, 0, 1, NULL, NULL, 'Dubai', NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Active');

INSERT INTO invoice_detail_cover_page_template (organization_id, name, header, subHeader,footer, address, logo) VALUES
(1, 'Default Template', 'Default Header', 'Default SubHeader','Default Footer', 'Address', '');

-- removing/updating an account should be followed by updating all tables with account_id column and system_preferences with serialized invoice accounts
INSERT INTO accounts (organization_id, currency_id, account_type_id, name, systemAccount, description, model_id, member_id, model_name, model_type, createdBy, createdOn, modifiedBy, modifiedOn,number) VALUES
(1, 2, 3, 'Furniture & Equipment', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,1),
(1, 2, 8, 'Opening Balance Offset', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,1),
(1, 2, 8, 'Owner''s Equity', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,2),
(1, 2, 11, 'Office Supplies', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,1),
(1, 2, 11, 'Advertising & Marketing', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,2),
(1, 2, 11, 'Travel Expenses', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,3),
(1, 2, 11, 'Internet & Telephone', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,4),
(1, 2, 11, 'IT Expenses', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,5),
(1, 2, 11, 'Rent', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,6),
(1, 2, 11, 'Meals & Entertainment', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,7),
(1, 2, 11, 'Car & Taxi', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,8),
(1, 2, 11, 'Salaries', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,9),
(1, 2, 13, 'Other Expenses', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,1),
(1, 2, 9, 'Sales', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,1),
(1, 2, 9, 'Other Income', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,2),
(1, 2, 7, 'Tax Payable', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,1),
(1, 2, 11, 'Legal Expenses', 'yes', NULL, NULL, NULL, 'internal', 'internal',NULL, NULL, NULL, NULL,10),
(1, 2, 11, 'Partner Expenses', 'yes', NULL, NULL, NULL, 'internal', 'internal',NULL, NULL, NULL, NULL,11),
(1, 2, 7, 'Tax Receivables', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,2),
(1, 2, 18, 'Trust Asset Account', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,1),
(1, 2, 11, 'Exchange gain or loss', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,12),
(1, 2, 9, 'Discount', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,13);

INSERT INTO expense_categories (expense_category_id, account_id, name, fl1name, fl2name) VALUES
(NULL, 6, 'Travel', 'Voyage', 'سفر'),
(1, 6, 'Flight', 'vol', 'طيران'),
(1, 6, 'Hotel', 'Hotel', 'فندق'),
(1, 6, 'Meal', 'Repas', 'وجبة'),
(1, 6, 'Transportation', 'Transport', 'تنقلات'),
(NULL, 9, 'Office', 'Bureau', 'المكتب'),
(6, 9, 'Electricity', 'électricité', 'كهرباء'),
(6, 7, 'Internet & Telephone', 'Internet & téléphone', 'انترنت & تلفون'),
(6, 8, 'IT Expenses', 'Dépenses IT', 'نفقات تقنية'),
(6, 4, 'Office Supplies', 'Fournitures de bureau', 'موارد للمكتب'),
(6, 9, 'Rent', 'Loyer', 'إيجار'),
(NULL, 11, 'Transportation', 'Transport', 'تنقلات'),
(12, 11, 'Fuel', 'Carburant', 'وقود'),
(12, 11, 'Parking', 'Parking', 'موقف'),
(12, 11, 'Taxi', 'Taxi', 'تاكسي'),
(NULL, 17, 'Company Incorporation Fee', 'Frais d''enregistrement d''entreprise', 'تكاليف تأسيس الشركة'),
(16, 17, 'Legal Docs', 'Docs juridiques', 'وثائق قانونية'),
(NULL, 17, 'Stamps', 'Timbre', 'طوابع'),
(16, 17, 'Translation', 'Traduction', 'ترجمة'),
(NULL, 17, 'Court Fee', 'Frais de court', 'تكاليف المحكمة'),
(NULL, 17, 'External Lawyer Fee', 'Honoraires d''avocat externe', 'تكاليف المحامين الخارجيين'),
(NULL, 17, 'External Lawyer Legal Opinion Fee', 'Frais d''avis juridique d''avocat externe', 'تكاليف إستشارة محامي خارجي'),
(NULL, 17, 'Expert Fee', 'Honoraires d’expert', 'تكاليف الخبير'),
(NULL, 17, 'Power of Attorney Fee', 'Mandat', 'تكاليف التفويض'),
(NULL, 17, 'Trade License Fee ', 'Frais de licence de commerce', 'تكاليف الترخيص التجاري');

INSERT INTO taxes (account_id, code, name, description, percentage, fl1name, fl2name) VALUES
(19, 'S', 'VAT', 'VAT 5%', 5.00, '', '');

INSERT INTO supplier_taxes (account_id, name, description, percentage, fl1name, fl2name) VALUES
(16, 'VAT', 'VAT 5%', 5.00, '', '');

INSERT INTO items (item_id, account_id, tax_id, unitName, fl1unitName, fl2unitName, unitPrice, description) VALUES
(NULL, 14, NULL, 'Consultancy', '', '', 500.00, ''),
(NULL, 14, NULL, 'Incorporation', '', '', 2000.00, ''),
(3, 14, NULL, 'Legal Docs', '', '', 300.00, ''),
(3, 14, NULL, 'Stamps', '', '', 100.00, ''),
(3, 14, NULL, 'Translation', '', '', 100.00, ''),
(NULL, 14, NULL, 'Services', '', '', 1000.00, ''),
(NULL, 14, NULL, 'Legal Fees', '', '', 500.00, ''),
(NULL, 14, NULL, 'Other Fees', '', '', 100.00, ''),
(NULL, 14, NULL, 'Expenses', '', '', 200.00, ''),
(NULL, 14, NULL, 'Miscellaneous', '', '', 50.00, ''),
(NULL, 14, NULL, 'Retainer', '', '', 50.00, '');

INSERT INTO system_preferences (groupName, keyName, keyValue) VALUES
('MoneyCurrency', 'currencies', '2, 232'),
('UsersValues', 'userRatePerHour', 'a:1:{i:1;s:4:"1000";}');

INSERT INTO exchange_rates (currency_id, organization_id, rate) VALUES (2, 1, 1.00), (232, 1, 3.67);

INSERT INTO advisor_task_locations (name) VALUES
('أبو ظبي‎'),
('عجمان'),
('دبي'),
('الفجيرة'),
('رأس الخيمة'),
('الشارقة'),
('أمّ القيوين'),
('آخر');

INSERT INTO contact_company_sub_categories (name) VALUES ('ناظر وقف');

DECLARE @cnt_titles_additional INT = 0;

WHILE @cnt_titles_additional < 3
BEGIN
   INSERT INTO titles DEFAULT VALUES
   SET @cnt_titles_additional = @cnt_titles_additional + 1;
END;

INSERT INTO titles_languages (title_id, language_id, name) VALUES
('8', '1', 'Sheikh'),
('8', '2', 'الشيخ'),
('8', '3', 'Sheikh'),
('8', '4', 'Sheikh'),
('9', '1', 'Excellency'),
('9', '2', 'سمو'),
('9', '3', 'Excellence'),
('9', '4', 'Excelencia'),
('10', '1', 'His Excellency'),
('10', '2', 'معالي'),
('10', '3', 'Son Excellence'),
('10', '4', 'Su excelencia');
