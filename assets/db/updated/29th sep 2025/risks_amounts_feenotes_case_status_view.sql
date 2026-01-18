ALTER VIEW [dbo].[legal_cases_risks_and_feeNotes] AS
SELECT
    legal_cases.id,
    legal_cases.case_status_id,
    legal_cases.case_type_id,
    legal_cases.provider_group_id,
    legal_cases.user_id,
    legal_cases.contact_id,
    legal_cases.client_id,
    legal_cases.subject,
    legal_cases.description,
    legal_cases.priority,
    legal_cases.arrivalDate,
    legal_cases.dueDate,
    legal_cases.statusComments,
    legal_cases.category,
	legal_cases.next_actions,
    legal_cases.caseValue,
    legal_cases.internalReference,
    legal_cases.externalizeLawyers,
    legal_cases.estimatedEffort,
    CAST(legal_cases.createdOn AS DATE) createdOn,
    CAST(legal_cases.modifiedOn AS DATE) AS modifiedOn,
    legal_cases.createdBy,
    legal_cases.modifiedBy,
    legal_cases.archived,
    legal_cases.private,
    lcee.effectiveEffort,
    'M' + CAST(legal_cases.id AS nvarchar) AS caseID,
    workflow_status.name AS status,
    case_types.name AS type,
    provider_groups.name AS providerGroup,
    UP.firstName + ' ' + UP.lastName AS assignee,
    legal_cases.archived AS archivedCases,
    legal_cases.isDeleted AS isDeleted,
    CASE WHEN conRE.father != '' THEN conRE.firstName + ' ' + conRE.father + ' ' + conRE.lastName ELSE conRE.firstName + ' ' + conRE.lastName END AS contact,
    lccr.name AS role,
    lccr.id AS role_id,
    lcld.sentenceDate,
    lcld.court_type_id,
    lcld.court_degree_id,
    lcld.court_region_id,
    lcld.court_id,
    
    -- Existing outsourcing fields
    contactContributor = STUFF((
        SELECT ', ' + (CASE WHEN conHE.father != '' THEN conHE.firstName + ' ' + conHE.father + ' ' + conHE.lastName ELSE conHE.firstName + ' ' + conHE.lastName END)
        FROM legal_cases AS legal_case_contributor
        LEFT JOIN legal_cases_contacts lccch ON lccch.case_id = legal_cases.id AND lccch.contactType = 'contributor'
        LEFT JOIN contacts conHE ON conHE.id = lccch.contact_id
        WHERE legal_case_contributor.id = legal_cases.id
        FOR XML PATH('')), 1, 1, ''),
        
    contactOutsourceTo = STUFF((
        SELECT ', ' + (CASE WHEN conExtLaw.father != '' THEN conExtLaw.firstName + ' ' + conExtLaw.father + ' ' + conExtLaw.lastName ELSE conExtLaw.firstName + ' ' + conExtLaw.lastName END)
        FROM legal_cases AS legal_case_outsource
        LEFT JOIN legal_cases_contacts lccExtLaw ON lccExtLaw.case_id = legal_cases.id AND lccExtLaw.contactType = 'external lawyer'
        LEFT JOIN contacts conExtLaw ON conExtLaw.id = lccExtLaw.contact_id
        WHERE legal_case_outsource.id = legal_cases.id
        FOR XML PATH('')), 1, 1, ''),
        
    companyOutsourceTo = STUFF((
        SELECT ', ' + companiesExtLaw.name
        FROM legal_cases AS legal_case_outsource
        LEFT JOIN legal_cases_companies lccompaniesExtLaw ON lccompaniesExtLaw.case_id = legal_case_outsource.id AND lccompaniesExtLaw.companyType = 'external lawyer'
        LEFT JOIN companies companiesExtLaw ON companiesExtLaw.id = lccompaniesExtLaw.company_id
        WHERE legal_case_outsource.id = legal_cases.id
        FOR XML PATH('')), 1, 1, ''),
    
    -- NEW: Companies outsourced to from legal_case_outsources table
    outsourcedCompanies = STUFF((
        SELECT ', ' + companies.name
        FROM legal_case_outsources lco
        LEFT JOIN companies ON companies.id = lco.company_id
        WHERE lco.legal_case_id = legal_cases.id
        FOR XML PATH('')), 1, 1, ''),
    
    -- Aggregated related risks column
    related_risks = (
        SELECT STRING_AGG(
            CONCAT(
                lcr.risk_category,
                '(',
                lcr.riskLevel,
                '): ',
                lcr.details,
                ' Mitigation: ',
                lcr.mitigation,
                ' Responsibility: ',
                UAC.firstName + ' ' + UAC.lastName,
                ' Status: ',
                lcr.status
            ), CHAR(13) + CHAR(10)
        ) WITHIN GROUP (ORDER BY lcr.risk_category)
        FROM legal_case_risks AS lcr
        LEFT JOIN user_profiles AS UAC ON UAC.user_id = lcr.responsible_actor_id
        WHERE lcr.case_id = legal_cases.id
    ),

    -- Aggregated billing information
    totalBill = (SELECT ISNULL(SUM(bfd.total), 0) FROM bills_full_details AS bfd WHERE bfd.case_id = legal_cases.id),
    totalPaymentsMade = (SELECT ISNULL(SUM(bfd.payemntsMade), 0) FROM bills_full_details AS bfd WHERE bfd.case_id = legal_cases.id),
    totalBalanceDue = (SELECT ISNULL(SUM(bfd.balanceDue), 0) FROM bills_full_details AS bfd WHERE bfd.case_id = legal_cases.id)
FROM
    legal_cases
LEFT JOIN
    workflow_status ON workflow_status.id = legal_cases.case_status_id
INNER JOIN
    case_types ON case_types.id = legal_cases.case_type_id
INNER JOIN
    provider_groups ON provider_groups.id = legal_cases.provider_group_id
LEFT JOIN
    user_profiles AS UP ON UP.user_id = legal_cases.user_id
LEFT JOIN
    legal_cases_contacts lccre ON lccre.case_id = legal_cases.id AND lccre.contactType = 'external lawyer'
LEFT JOIN
    contacts conRE ON conRE.id = lccre.contact_id
LEFT JOIN
    legal_case_contact_roles lccr ON lccr.id = lccre.legal_case_contact_role_id
LEFT JOIN
    legal_case_effective_effort AS lcee ON lcee.legal_case_id = legal_cases.id
LEFT JOIN
    legal_case_litigation_details lcld ON lcld.legal_case_id = legal_cases.id AND lcld.id = legal_cases.stage
WHERE
    legal_cases.isDeleted = 0