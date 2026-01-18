BEGIN TRANSACTION;

-- Insert main categories into case_types
INSERT INTO case_types (name, criminal, isDeleted) VALUES
('Telecommunication Offences', 'yes', 0),
('Radio Communication/Frequency Spectrum', 'yes', 0),
('Broadcasting Offences', 'yes', 0),
('Postal/Courier Offences', 'yes', 0),
('Electronic Transactions Offences', 'yes', 0),
('Cyber Offences', 'yes', 0),
('Standards and Type Approval', 'yes', 0),
('Consumer Protection Offences', 'yes', 0),
('Tariff Regulations Offences', 'yes', 0);

-- Insert subcategories into case_offense_subcategory with proper offense_type_id references
-- Telecommunication Offences subcategories
INSERT INTO case_offense_subcategory (name, offense_type_id, is_active)
SELECT subcat, id, 1
FROM (VALUES 
    ('SIM Card Hawking'),
    ('Using numbering or addresses contrary to Regulations'),
    ('Unlicensed telecommunications systems'),
    ('Obtaining service dishonestly'),
    ('Improper use of system'),
    ('Modification of messages'),
    ('Interception and disclosure of information'),
    ('Tampering with telecommunication plant'),
    ('Severing with intent to steal'),
    ('Trespass and wilful obstruction of telecommunication officer')
) AS subcats(subcat)
CROSS JOIN (SELECT id FROM case_types WHERE name = 'Telecommunication Offences') AS parent;

-- Radio Communication/Frequency Spectrum subcategories
INSERT INTO case_offense_subcategory (name, offense_type_id, is_active)
SELECT subcat, id, 1
FROM (VALUES 
    ('Unlicensed radio communication systems'),
    ('Unlawfully sending of misleading messages'),
    ('Deliberate interference with radio communication')
) AS subcats(subcat)
CROSS JOIN (SELECT id FROM case_types WHERE name = 'Radio Communication/Frequency Spectrum') AS parent;

-- Broadcasting Offences subcategories
INSERT INTO case_offense_subcategory (name, offense_type_id, is_active)
SELECT subcat, id, 1
FROM (VALUES 
    ('Unlicensed broadcasting services'),
    ('Providing broadcasting service which is not of a description specified in the license'),
    ('Providing broadcasting services in an area not licensed'),
    ('Broadcasting in contravention of the Act or the license conditions')
) AS subcats(subcat)
CROSS JOIN (SELECT id FROM case_types WHERE name = 'Broadcasting Offences') AS parent;

-- Postal/Courier Offences subcategories
INSERT INTO case_offense_subcategory (name, offense_type_id, is_active)
SELECT subcat, id, 1
FROM (VALUES 
    ('Unlicensed postal/courier services'),
    ('Unlawful conveying letter or postal article'),
    ('Performing any service incidental to conveying letter/postal article'),
    ('Unlawful delivering or tendering letter/postal article'),
    ('Unlawful collection of letters or postal articles'),
    ('Damaging letter box'),
    ('Unlawful affixing materials on post office'),
    ('Unlawful opening or delivery of postal articles'),
    ('Transmitting offensive material by post'),
    ('Use of fictitious stamps'),
    ('Unlawful use of certain words'),
    ('Transmitting prohibited articles by post'),
    ('Interfering with postal installation')
) AS subcats(subcat)
CROSS JOIN (SELECT id FROM case_types WHERE name = 'Postal/Courier Offences') AS parent;

-- Electronic Transactions Offences subcategories
INSERT INTO case_offense_subcategory (name, offense_type_id, is_active)
SELECT subcat, id, 1
FROM (VALUES 
    ('Unauthorized operation of an electronic certification system')
) AS subcats(subcat)
CROSS JOIN (SELECT id FROM case_types WHERE name = 'Electronic Transactions Offences') AS parent;

-- Cyber Offences subcategories
INSERT INTO case_offense_subcategory (name, offense_type_id, is_active)
SELECT subcat, id, 1
FROM (VALUES 
    ('Damaging/denying access to a computer system'),
    ('Tampering with computer source documents'),
    ('Publishing of obscene information in electronic form'),
    ('Publication for fraudulent purpose'),
    ('Re-programming of mobile telephone'),
    ('Possession or supply of anything for reprogramming mobile telephone')
) AS subcats(subcat)
CROSS JOIN (SELECT id FROM case_types WHERE name = 'Cyber Offences') AS parent;

-- Standards and Type Approval subcategories
INSERT INTO case_offense_subcategory (name, offense_type_id, is_active)
SELECT subcat, id, 1
FROM (VALUES 
    ('Selling/offering for sale radio communication apparatus'),
    ('Letting/hiring a radio communication apparatus'),
    ('Advertisement/dealing in radio communication apparatus')
) AS subcats(subcat)
CROSS JOIN (SELECT id FROM case_types WHERE name = 'Standards and Type Approval') AS parent;

-- Consumer Protection Offences subcategories
INSERT INTO case_offense_subcategory (name, offense_type_id, is_active)
SELECT subcat, id, 1
FROM (VALUES 
    ('Promoting, glamorizing or marketing alcohol and tobacco products or other harmful substances to children'),
    ('Using automated calling systems without prior consent of the subscriber'),
    ('Sending electronic mail without a valid address'),
    ('Failing to perform measurement, reporting and record keeping'),
    ('Failing to reach a target'),
    ('Failing to submit information'),
    ('Submits or publishes false or misleading information'),
    ('Obstructing or preventing an inspection or investigation'),
    ('Engaging in any act or omission to defeat the purposes of these Regulations')
) AS subcats(subcat)
CROSS JOIN (SELECT id FROM case_types WHERE name = 'Consumer Protection Offences') AS parent;

-- Tariff Regulations Offences subcategories
INSERT INTO case_offense_subcategory (name, offense_type_id, is_active)
SELECT subcat, id, 1
FROM (VALUES 
    ('Contravening tariff regulations')
) AS subcats(subcat)
CROSS JOIN (SELECT id FROM case_types WHERE name = 'Tariff Regulations Offences') AS parent;

COMMIT TRANSACTION;