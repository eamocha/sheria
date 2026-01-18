USE [master]
GO
/****** Object:  Database [ca_prod]    Script Date: 1/16/2026 12:39:16 PM ******/
CREATE DATABASE [ca_prod]
 CONTAINMENT = NONE
 ON  PRIMARY 
( NAME = N'ca_prod', FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL15.MSSQLSERVER\MSSQL\DATA\ca_prod.mdf' , SIZE = 154624KB , MAXSIZE = UNLIMITED, FILEGROWTH = 65536KB )
 LOG ON 
( NAME = N'ca_prod_log', FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL15.MSSQLSERVER\MSSQL\DATA\ca_prod_log.ldf' , SIZE = 1597440KB , MAXSIZE = 2048GB , FILEGROWTH = 65536KB )
 WITH CATALOG_COLLATION = DATABASE_DEFAULT
GO
ALTER DATABASE [ca_prod] SET COMPATIBILITY_LEVEL = 150
GO
IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [ca_prod].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO
ALTER DATABASE [ca_prod] SET ANSI_NULL_DEFAULT OFF 
GO
ALTER DATABASE [ca_prod] SET ANSI_NULLS OFF 
GO
ALTER DATABASE [ca_prod] SET ANSI_PADDING OFF 
GO
ALTER DATABASE [ca_prod] SET ANSI_WARNINGS OFF 
GO
ALTER DATABASE [ca_prod] SET ARITHABORT OFF 
GO
ALTER DATABASE [ca_prod] SET AUTO_CLOSE OFF 
GO
ALTER DATABASE [ca_prod] SET AUTO_SHRINK OFF 
GO
ALTER DATABASE [ca_prod] SET AUTO_UPDATE_STATISTICS ON 
GO
ALTER DATABASE [ca_prod] SET CURSOR_CLOSE_ON_COMMIT OFF 
GO
ALTER DATABASE [ca_prod] SET CURSOR_DEFAULT  GLOBAL 
GO
ALTER DATABASE [ca_prod] SET CONCAT_NULL_YIELDS_NULL OFF 
GO
ALTER DATABASE [ca_prod] SET NUMERIC_ROUNDABORT OFF 
GO
ALTER DATABASE [ca_prod] SET QUOTED_IDENTIFIER OFF 
GO
ALTER DATABASE [ca_prod] SET RECURSIVE_TRIGGERS OFF 
GO
ALTER DATABASE [ca_prod] SET  DISABLE_BROKER 
GO
ALTER DATABASE [ca_prod] SET AUTO_UPDATE_STATISTICS_ASYNC OFF 
GO
ALTER DATABASE [ca_prod] SET DATE_CORRELATION_OPTIMIZATION OFF 
GO
ALTER DATABASE [ca_prod] SET TRUSTWORTHY OFF 
GO
ALTER DATABASE [ca_prod] SET ALLOW_SNAPSHOT_ISOLATION OFF 
GO
ALTER DATABASE [ca_prod] SET PARAMETERIZATION SIMPLE 
GO
ALTER DATABASE [ca_prod] SET READ_COMMITTED_SNAPSHOT OFF 
GO
ALTER DATABASE [ca_prod] SET HONOR_BROKER_PRIORITY OFF 
GO
ALTER DATABASE [ca_prod] SET RECOVERY FULL 
GO
ALTER DATABASE [ca_prod] SET  MULTI_USER 
GO
ALTER DATABASE [ca_prod] SET PAGE_VERIFY CHECKSUM  
GO
ALTER DATABASE [ca_prod] SET DB_CHAINING OFF 
GO
ALTER DATABASE [ca_prod] SET FILESTREAM( NON_TRANSACTED_ACCESS = OFF ) 
GO
ALTER DATABASE [ca_prod] SET TARGET_RECOVERY_TIME = 60 SECONDS 
GO
ALTER DATABASE [ca_prod] SET DELAYED_DURABILITY = DISABLED 
GO
ALTER DATABASE [ca_prod] SET ACCELERATED_DATABASE_RECOVERY = OFF  
GO
EXEC sys.sp_db_vardecimal_storage_format N'ca_prod', N'ON'
GO
ALTER DATABASE [ca_prod] SET QUERY_STORE = OFF
GO
USE [ca_prod]
GO
/****** Object:  Table [dbo].[user_activity_logs]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_activity_logs](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[task_id] [bigint] NULL,
	[legal_case_id] [bigint] NULL,
	[client_id] [bigint] NULL,
	[time_type_id] [bigint] NULL,
	[time_internal_status_id] [bigint] NULL,
	[logDate] [date] NOT NULL,
	[effectiveEffort] [decimal](8, 2) NOT NULL,
	[comments] [nvarchar](max) NULL,
	[timeStatus] [nvarchar](8) NULL,
	[createdBy] [bigint] NULL,
	[createdOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[rate] [decimal](10, 2) NULL,
	[rate_system] [nvarchar](32) NULL,
	[opinion_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[task_effective_effort]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
--task_effective_effort;
CREATE VIEW [dbo].[task_effective_effort] AS
	SELECT TOP(9223372036854775800) user_activity_logs.task_id AS task_id, SUM(user_activity_logs.effectiveEffort) AS effectiveEffort
	FROM user_activity_logs
	WHERE user_activity_logs.task_id IS NOT NULL
	GROUP BY user_activity_logs.task_id;
GO
/****** Object:  Table [dbo].[companies_contacts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[companies_contacts](
	[company_id] [bigint] NOT NULL,
	[contact_id] [bigint] NOT NULL,
	[description] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[company_id] ASC,
	[contact_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contacts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contacts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[status] [nvarchar](8) NOT NULL,
	[gender] [nvarchar](6) NULL,
	[title_id] [bigint] NULL,
	[firstName] [nvarchar](255) NOT NULL,
	[lastName] [nvarchar](255) NOT NULL,
	[foreignFirstName] [nvarchar](255) NULL,
	[foreignLastName] [nvarchar](255) NULL,
	[father] [nvarchar](255) NOT NULL,
	[mother] [nvarchar](255) NOT NULL,
	[dateOfBirth] [date] NULL,
	[contact_category_id] [bigint] NULL,
	[contact_sub_category_id] [bigint] NULL,
	[jobTitle] [nvarchar](255) NOT NULL,
	[private] [char](3) NULL,
	[isLawyer] [nvarchar](3) NOT NULL,
	[lawyerForCompany] [nvarchar](3) NOT NULL,
	[website] [nvarchar](255) NOT NULL,
	[phone] [nvarchar](255) NOT NULL,
	[fax] [nvarchar](255) NOT NULL,
	[mobile] [nvarchar](255) NOT NULL,
	[address1] [nvarchar](255) NOT NULL,
	[address2] [nvarchar](255) NOT NULL,
	[city] [nvarchar](255) NOT NULL,
	[state] [nvarchar](255) NOT NULL,
	[zip] [nvarchar](32) NOT NULL,
	[country_id] [bigint] NULL,
	[comments] [nvarchar](max) NOT NULL,
	[internalReference] [nvarchar](255) NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[manager_id] [bigint] NULL,
	[tax_number] [nvarchar](255) NULL,
	[street_name] [nvarchar](255) NULL,
	[additional_street_name] [nvarchar](255) NULL,
	[building_number] [nvarchar](255) NULL,
	[address_additional_number] [nvarchar](255) NULL,
	[district_neighborhood] [nvarchar](255) NULL,
	[additional_id_type] [bigint] NULL,
	[additional_id_value] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_profiles]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_profiles](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[status] [nvarchar](8) NOT NULL,
	[gender] [nvarchar](6) NOT NULL,
	[title] [nvarchar](5) NOT NULL,
	[firstName] [nvarchar](255) NOT NULL,
	[lastName] [nvarchar](255) NOT NULL,
	[father] [nvarchar](255) NOT NULL,
	[mother] [nvarchar](255) NOT NULL,
	[employeeId] [nvarchar](255) NULL,
	[ad_userCode] [nvarchar](255) NULL,
	[user_code] [varchar](10) NULL,
	[dateOfBirth] [date] NULL,
	[department] [nvarchar](255) NULL,
	[nationality] [nvarchar](255) NOT NULL,
	[jobTitle] [nvarchar](255) NOT NULL,
	[overridePrivacy] [char](3) NOT NULL,
	[flagChangePassword] [tinyint] NULL,
	[flagNeedApproval] [tinyint] NULL,
	[isLawyer] [nvarchar](3) NOT NULL,
	[website] [nvarchar](255) NOT NULL,
	[phone] [nvarchar](255) NOT NULL,
	[fax] [nvarchar](255) NOT NULL,
	[mobile] [nvarchar](255) NOT NULL,
	[address1] [nvarchar](255) NOT NULL,
	[address2] [nvarchar](255) NOT NULL,
	[city] [nvarchar](255) NOT NULL,
	[state] [nvarchar](255) NOT NULL,
	[zip] [nvarchar](32) NOT NULL,
	[profilePicture] [nvarchar](255) NULL,
	[country] [nvarchar](255) NOT NULL,
	[comments] [nvarchar](max) NOT NULL,
	[seniority_level_id] [bigint] NULL,
	[forgetPasswordFlag] [tinyint] NULL,
	[forgetPasswordHashKey] [nvarchar](255) NULL,
	[foreign_first_name] [nvarchar](255) NULL,
	[foreign_last_name] [nvarchar](255) NULL,
	[forgetPasswordUrlCreatedOn] [datetime2](0) NULL,
	[department_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[companies]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[companies](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legalName] [nvarchar](255) NULL,
	[name] [nvarchar](255) NOT NULL,
	[shortName] [nvarchar](255) NULL,
	[foreignName] [nvarchar](255) NULL,
	[status] [nvarchar](8) NOT NULL,
	[category] [nvarchar](8) NOT NULL,
	[company_category_id] [bigint] NULL,
	[company_sub_category_id] [bigint] NULL,
	[private] [char](3) NULL,
	[company_id] [bigint] NULL,
	[nationality_id] [bigint] NULL,
	[company_legal_type_id] [bigint] NULL,
	[object] [text] NULL,
	[show_extra_tabs] [char](1) NULL,
	[capital] [decimal](22, 2) NULL,
	[capitalVisualizeDecimals] [nvarchar](3) NULL,
	[capitalCurrency] [nvarchar](3) NULL,
	[nominalShares] [decimal](22, 0) NULL,
	[bearerShares] [decimal](22, 0) NULL,
	[shareParValue] [decimal](22, 2) NULL,
	[shareParValueCurrency] [nvarchar](3) NULL,
	[qualifyingShares] [decimal](22, 2) NULL,
	[registrationNb] [nvarchar](255) NULL,
	[registrationDate] [date] NULL,
	[registrationCity] [nvarchar](255) NULL,
	[registrationTaxNb] [nvarchar](255) NULL,
	[registrationYearsNb] [bigint] NULL,
	[registrationByLawNotaryPublic] [bigint] NULL,
	[registrationByLawRef] [nvarchar](255) NULL,
	[registrationByLawDate] [date] NULL,
	[registrationByLawCity] [nvarchar](255) NULL,
	[sharesLocation] [nvarchar](255) NULL,
	[ownedByGroup] [nvarchar](3) NULL,
	[sheerLebanese] [nvarchar](3) NULL,
	[contributionRatio] [decimal](3, 2) NULL,
	[notes] [nvarchar](max) NULL,
	[otherNotes] [nvarchar](max) NULL,
	[registrationAuthority] [bigint] NULL,
	[internalReference] [nvarchar](255) NULL,
	[crReleasedOn] [date] NULL,
	[crExpiresOn] [date] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[capitalComment] [text] NULL,
	[additional_id_type] [bigint] NULL,
	[additional_id_value] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contact_emails]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contact_emails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contact_id] [bigint] NOT NULL,
	[email] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contact_company_categories]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contact_company_categories](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[keyName] [nvarchar](255) NULL,
	[name] [nvarchar](255) NOT NULL,
	[color] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contact_company_sub_categories]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contact_company_sub_categories](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[contacts_grid]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
--contacts_grid;
CREATE VIEW [dbo].[contacts_grid] AS SELECT TOP(9223372036854775800) contacts.id, contacts.status, contacts.gender, contacts.title_id, contacts.firstName, contacts.lastName, CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END AS fullName,
    contacts.foreignFirstName, contacts.foreignLastName, ISNULL(contacts.foreignFirstName, '') +  ' ' +  ISNULL(contacts.foreignLastName, '') AS foreignFullName, contacts.father, contacts.mother,
    contacts.dateOfBirth, contacts.contact_category_id, contacts.contact_sub_category_id, contacts.jobTitle, contacts.private, contacts.isLawyer, contacts.lawyerForCompany, contacts.website,
    contacts.phone, contacts.fax, contacts.mobile, contacts.address1, contacts.address2, contacts.city, contacts.state, contacts.zip, contacts.country_id, contacts.comments, contacts.createdOn,
    (created.firstName + ' ' + created.lastName) AS createdByName, contacts.createdBy, contacts.modifiedOn,(modified.firstName + ' ' + modified.lastName) AS modifiedByName, contacts.modifiedBy, CAST(contacts.createdOn AS DATE) AS createdOnDate ,  'PER' +  CAST(contacts.id AS nvarchar) as contactID,
    contact_company_categories.name AS category, contact_company_categories.keyName AS category_keyName, contact_company_sub_categories.name AS subCategory, contacts.internalReference AS internalReference,
    email=STUFF( (SELECT '; '+ contact_emails.email FROM contact_emails INNER JOIN contacts ct ON ct.id = contact_emails.contact_id  where contacts.id = contact_emails.contact_id  FOR XML PATH('')), 1, 1, ''),
    company=STUFF( (SELECT '; '+ companies.name FROM companies_contacts INNER JOIN companies ON companies.id = companies_contacts.company_id WHERE companies_contacts.contact_id = contacts.id FOR XML PATH('')), 1, 1, '')
    ,contacts.tax_number, contacts.street_name,contacts.additional_street_name, contacts.building_number, contacts.address_additional_number, contacts.district_neighborhood, contacts.additional_id_type, contacts.additional_id_value
    FROM contacts
        LEFT JOIN user_profiles created ON created.user_id = contacts.createdBy
        LEFT JOIN user_profiles modified ON modified.user_id = contacts.modifiedBy
        LEFT JOIN contact_company_categories ON contact_company_categories.id = contacts.contact_category_id
        LEFT JOIN contact_company_sub_categories ON contact_company_sub_categories.id = contacts.contact_sub_category_id;
GO
/****** Object:  Table [dbo].[task_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_statuses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[category] [nvarchar](255) NOT NULL,
	[isGlobal] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_contributors]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_contributors](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[task_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[status] [nvarchar](10) NULL,
	[description] [text] NULL,
	[value] [bigint] NULL,
	[type_id] [bigint] NOT NULL,
	[sub_type_id] [bigint] NULL,
	[contract_date] [date] NULL,
	[start_date] [date] NULL,
	[end_date] [date] NULL,
	[reference_number] [nvarchar](255) NULL,
	[assigned_team_id] [bigint] NULL,
	[assignee_id] [bigint] NULL,
	[authorized_signatory] [bigint] NULL,
	[amendment_of] [bigint] NULL,
	[app_law_id] [bigint] NULL,
	[country_id] [bigint] NULL,
	[requester_id] [bigint] NULL,
	[status_comments] [text] NULL,
	[priority] [nvarchar](10) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[status_id] [bigint] NOT NULL,
	[renewal_type] [nvarchar](25) NULL,
	[currency_id] [bigint] NULL,
	[private] [tinyint] NULL,
	[channel] [char](3) NULL,
	[visible_to_cp] [tinyint] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedByChannel] [char](3) NULL,
	[archived] [nvarchar](3) NOT NULL,
	[hideFromBoard] [nvarchar](3) NULL,
	[category] [nvarchar](50) NULL,
	[stage] [nvarchar](50) NULL,
	[milestone_visible_to_cp] [tinyint] NULL,
	[contract_duration] [int] NULL,
	[perf_security_commencement_date] [date] NULL,
	[perf_security_expiry_date] [date] NULL,
	[expected_completion_date] [date] NULL,
	[actual_completion_date] [date] NULL,
	[advance_payment_guarantee] [nvarchar](100) NULL,
	[letter_of_credit_details] [nvarchar](max) NULL,
	[effective_date] [date] NULL,
	[department_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_locations]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_locations](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_cases]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_cases](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[stage] [bigint] NULL,
	[channel] [nvarchar](3) NULL,
	[visibleToCP] [tinyint] NULL,
	[case_status_id] [bigint] NULL,
	[case_type_id] [bigint] NOT NULL,
	[legal_case_stage_id] [bigint] NULL,
	[provider_group_id] [bigint] NOT NULL,
	[user_id] [bigint] NULL,
	[contact_id] [bigint] NULL,
	[client_id] [bigint] NULL,
	[referredBy] [bigint] NULL,
	[requestedBy] [bigint] NULL,
	[subject] [nvarchar](255) NOT NULL,
	[description] [text] NOT NULL,
	[latest_development] [text] NULL,
	[priority] [nvarchar](8) NOT NULL,
	[arrivalDate] [date] NULL,
	[caseArrivalDate] [date] NULL,
	[dueDate] [date] NULL,
	[closedOn] [date] NULL,
	[statusComments] [nvarchar](max) NULL,
	[category] [varchar](255) NULL,
	[caseValue] [decimal](22, 2) NULL,
	[recoveredValue] [decimal](22, 2) NULL,
	[judgmentValue] [decimal](22, 2) NULL,
	[internalReference] [nvarchar](255) NULL,
	[externalizeLawyers] [nvarchar](3) NOT NULL,
	[estimatedEffort] [decimal](10, 2) NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedByChannel] [nvarchar](3) NULL,
	[archived] [nvarchar](3) NOT NULL,
	[hideFromBoard] [nvarchar](3) NULL,
	[private] [char](3) NULL,
	[timeTrackingBillable] [char](1) NULL,
	[expensesBillable] [char](1) NULL,
	[legal_case_client_position_id] [bigint] NULL,
	[legal_case_success_probability_id] [bigint] NULL,
	[assignedOn] [smalldatetime] NULL,
	[isDeleted] [tinyint] NOT NULL,
	[workflow] [bigint] NULL,
	[cap_amount] [decimal](22, 2) NULL,
	[time_logs_cap_ratio] [decimal](10, 2) NULL,
	[expenses_cap_ratio] [decimal](10, 2) NULL,
	[cap_amount_enable] [tinyint] NULL,
	[cap_amount_disallow] [tinyint] NULL,
	[closure_requested_by] [bigint] NULL,
	[closed_by] [bigint] NULL,
	[closure_comments] [nvarchar](250) NULL,
	[first_litigation_case_court_activity_purpose] [nvarchar](250) NULL,
	[closedBy_comments] [nvarchar](250) NULL,
	[approval_step] [bigint] NULL,
	[next_actions] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[tasks]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tasks](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[title] [nvarchar](255) NOT NULL,
	[legal_case_id] [bigint] NULL,
	[contract_id] [bigint] NULL,
	[stage] [bigint] NULL,
	[user_id] [bigint] NOT NULL,
	[assigned_to] [bigint] NOT NULL,
	[due_date] [date] NOT NULL,
	[private] [char](3) NULL,
	[priority] [nvarchar](8) NOT NULL,
	[task_location_id] [bigint] NULL,
	[description] [text] NULL,
	[task_status_id] [bigint] NOT NULL,
	[task_type_id] [bigint] NOT NULL,
	[estimated_effort] [decimal](8, 2) NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[archived] [nvarchar](3) NOT NULL,
	[hideFromBoard] [nvarchar](3) NULL,
	[reporter] [bigint] NULL,
	[workflow] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[tasks_detailed_view]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
--tasks_detailed_view
CREATE VIEW [dbo].[tasks_detailed_view] AS SELECT TOP(9223372036854775800)
 tasks.id, ('T' + CAST(tasks.id as nvarchar)) as taskId, tasks.title,
 CASE WHEN tasks.legal_case_id IS NULL THEN '' ELSE ('M' + CAST(tasks.legal_case_id AS nvarchar)) END as caseId,
 tasks.legal_case_id, tasks.user_id, tasks.due_date,tasks.assigned_to as assignedToId,tasks.reporter as reportedById,
 tasks.private, tasks.priority, tasks.task_location_id AS task_location_id,
 task_locations.name AS location, tasks.description as taskFullDescription, tasks.task_status_id, tasks.task_type_id, tasks.estimated_effort,
 CAST(tasks.createdOn as DATE) as createdOn, CAST(tasks.modifiedOn as DATE) as modifiedOn, tasks.modifiedBy, tasks.archived, tee.effectiveEffort,
 (assigned.firstName + ' ' + assigned.lastName) as assigned_to,
 (reporter.firstName + ' ' + reporter.lastName) as reporter,
 (created.firstName + ' ' + created.lastName) as createdBy, (modified.firstName + ' ' + modified.lastName) as modifiedByName, tasks.createdBy as createdById, ts.name as taskStatus, tasks.archived as archivedTasks,
 SUBSTRING(tasks.description, 1, 50) as description, SUBSTRING(lg.subject, 1, 50) as caseSubject, lg.subject as caseFullSubject, lg.category as caseCategory,
 tasks.contract_id, contract.name as contract_name,
assigned.status as assignee_status,reporter.status as reporter_status,created.status as creator_status, modified.status as modifier_status, tasks.stage,
contributors = STUFF((SELECT ', ' + (contr.firstName + ' ' + contr.lastName) FROM user_profiles AS contr INNER JOIN task_contributors ON tasks.id = task_contributors.task_id  AND contr.user_id = task_contributors.user_id FOR XML PATH('')), 1, 1, '')
FROM tasks
LEFT JOIN user_profiles assigned ON assigned.user_id = tasks.assigned_to
LEFT JOIN user_profiles reporter ON reporter.user_id = tasks.reporter
LEFT JOIN user_profiles created ON created.user_id = tasks.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = tasks.modifiedBy
LEFT JOIN task_statuses ts ON ts.id = tasks.task_status_id
LEFT JOIN legal_cases as lg ON lg.id = tasks.legal_case_id
LEFT JOIN contract ON contract.id = tasks.contract_id
LEFT JOIN task_effective_effort AS tee ON tee.task_id = tasks.id
LEFT JOIN task_locations ON task_locations.id = tasks.task_location_id
where tasks.legal_case_id is null or lg.isDeleted = 0;
GO
/****** Object:  Table [dbo].[legal_case_company_roles]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_company_roles](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_cases_companies]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_cases_companies](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[company_id] [bigint] NOT NULL,
	[legal_case_company_role_id] [bigint] NULL,
	[comments] [nvarchar](max) NULL,
	[companyType] [nvarchar](15) NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_litigation_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_litigation_details](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_id] [bigint] NOT NULL,
	[court_type_id] [bigint] NULL,
	[court_degree_id] [bigint] NULL,
	[court_region_id] [bigint] NULL,
	[court_id] [bigint] NULL,
	[sentenceDate] [date] NULL,
	[comments] [text] NULL,
	[legal_case_stage] [bigint] NULL,
	[client_position] [bigint] NULL,
	[status] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedByChannel] [nvarchar](3) NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[createdByChannel] [nvarchar](3) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_cases_contacts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_cases_contacts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[contact_id] [bigint] NOT NULL,
	[legal_case_contact_role_id] [bigint] NULL,
	[comments] [nvarchar](max) NULL,
	[contactType] [nvarchar](15) NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[litigation] [char](3) NULL,
	[corporate] [char](3) NULL,
	[litigationSLA] [bigint] NULL,
	[legalMatterSLA] [bigint] NULL,
	[isDeleted] [tinyint] NOT NULL,
	[criminal] [char](3) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[provider_groups]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[provider_groups](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[allUsers] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[workflow_status]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[workflow_status](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[isGlobal] [tinyint] NULL,
	[category] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[legal_case_effective_effort]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

--legal_case_effective_effort;
CREATE VIEW [dbo].[legal_case_effective_effort] AS
	SELECT TOP(9223372036854775800) user_activity_logs.legal_case_id AS legal_case_id, SUM(user_activity_logs.effectiveEffort) AS effectiveEffort
	FROM user_activity_logs
	WHERE user_activity_logs.legal_case_id IS NOT NULL
	GROUP BY user_activity_logs.legal_case_id;
GO
/****** Object:  View [dbo].[legal_cases_per_company]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[legal_cases_per_company] AS SELECT TOP(9223372036854775800) legal_cases.id, legal_cases.case_status_id, legal_cases.case_type_id, legal_cases.provider_group_id,
	legal_cases.user_id, legal_cases.isDeleted as isDeleted, legal_cases.contact_id, legal_cases.client_id, legal_cases.subject, legal_cases.description, legal_cases.priority, legal_cases.arrivalDate, legal_cases.dueDate,
	legal_cases.statusComments, legal_cases.category, legal_cases.caseValue, legal_cases.internalReference, legal_cases.externalizeLawyers, legal_cases.estimatedEffort,
	CAST(legal_cases.createdOn as DATE) as createdOn, CAST(legal_cases.modifiedOn as DATE) modifiedOn,
	legal_cases.createdBy, legal_cases.modifiedBy, legal_cases.archived, legal_cases.private, lcee.effectiveEffort, 'M' + CAST(legal_cases.id AS nvarchar) AS caseID, workflow_status.name AS status,
	case_types.name AS type, provider_groups.name AS providerGroup, UP.firstName + ' ' + UP.lastName AS assignee, legal_cases.archived AS archivedCases, com.name AS company, lccr.name AS role,
	lccr.id AS role_id,
        lcld.sentenceDate,lcld.court_type_id,lcld.court_degree_id,lcld.court_region_id,lcld.court_id,
        contactContributor = STUFF((SELECT ', ' +
            (CASE WHEN conHE.father!='' THEN conHE.firstName + ' '+ conHE.father + ' ' + conHE.lastName ELSE conHE.firstName+' '+conHE.lastName END)
            FROM legal_cases legal_case_contributor
            LEFT JOIN legal_cases_contacts lccch ON lccch.case_id = legal_cases.id AND lccch.contactType = 'contributor'
            LEFT JOIN contacts conHE ON conHE.id = lccch.contact_id
            WHERE legal_case_contributor.id = legal_cases.id
            FOR XML PATH('')), 1, 1, ''),
        contactOutsourceTo = STUFF((SELECT ', ' +
            (CASE WHEN conExtLaw.father!='' THEN conExtLaw.firstName + ' '+ conExtLaw.father + ' ' + conExtLaw.lastName ELSE conExtLaw.firstName+' '+conExtLaw.lastName END)
             FROM legal_cases legal_case_outsource
             LEFT JOIN legal_cases_contacts lccExtLaw ON lccExtLaw.case_id = legal_cases.id AND lccExtLaw.contactType = 'external lawyer'
        LEFT JOIN contacts conExtLaw ON conExtLaw.id = lccExtLaw.contact_id
         WHERE legal_case_outsource.id = legal_cases.id
            FOR XML PATH('')), 1, 1, ''),
		companyOutsourceTo = STUFF((SELECT ', ' + companiesExtLaw.name
             FROM legal_cases legal_case_outsource
             LEFT JOIN
                                             legal_cases_companies lccompaniesExtLaw
                                             ON
                                                       lccompaniesExtLaw.case_id         = legal_case_outsource.id
                                                       AND lccompaniesExtLaw.companyType = 'external lawyer'
                                   LEFT JOIN
                                             companies companiesExtLaw
                                             ON
                                                       companiesExtLaw.id = lccompaniesExtLaw.company_id
                         WHERE
                                   legal_case_outsource.id = legal_cases.id
            FOR XML PATH('')), 1, 1, '')
FROM legal_cases
LEFT JOIN workflow_status ON workflow_status.id = legal_cases.case_status_id
INNER JOIN case_types ON case_types.id = legal_cases.case_type_id
INNER JOIN provider_groups ON provider_groups.id = legal_cases.provider_group_id
LEFT JOIN user_profiles AS UP ON UP.user_id = legal_cases.user_id
LEFT JOIN legal_cases_companies lcccom ON lcccom.case_id = legal_cases.id
LEFT JOIN companies com ON lcccom.company_id = com.id
LEFT JOIN legal_case_company_roles lccr ON lccr.id = lcccom.legal_case_company_role_id
LEFT JOIN legal_case_effective_effort AS lcee ON lcee.legal_case_id = legal_cases.id
LEFT JOIN legal_case_litigation_details lcld ON lcld.legal_case_id = legal_cases.id AND lcld.id = legal_cases.stage
Where legal_cases.isDeleted = 0;
GO
/****** Object:  Table [dbo].[legal_case_contact_roles]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_contact_roles](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[legal_cases_per_contact]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[legal_cases_per_contact] AS SELECT TOP(9223372036854775800) legal_cases.id,  legal_cases.isDeleted as isDeleted, legal_cases.case_status_id, legal_cases.case_type_id, legal_cases.provider_group_id, legal_cases.user_id,
	legal_cases.contact_id, legal_cases.client_id, legal_cases.subject, legal_cases.description, legal_cases.priority, legal_cases.arrivalDate, legal_cases.dueDate, legal_cases.statusComments,
	legal_cases.category, legal_cases.caseValue, legal_cases.internalReference, legal_cases.externalizeLawyers, legal_cases.estimatedEffort,
	CAST(legal_cases.createdOn as DATE) createdOn, CAST(legal_cases.modifiedOn as DATE) modifiedOn,
	legal_cases.createdBy, legal_cases.modifiedBy, legal_cases.archived, legal_cases.private, lcee.effectiveEffort, 'M' + CAST(legal_cases.id AS nvarchar) AS caseID, workflow_status.name AS status,
	case_types.name AS type, provider_groups.name AS providerGroup, UP.firstName + ' ' + UP.lastName AS assignee, legal_cases.archived AS archivedCases,
	CASE WHEN conRE.father!='' THEN conRE.firstName + ' '+ conRE.father + ' ' + conRE.lastName ELSE conRE.firstName+' '+conRE.lastName END AS contact, lccr.name AS role, lccr.id AS role_id,
        lcld.sentenceDate,lcld.court_type_id,lcld.court_degree_id,lcld.court_region_id,lcld.court_id,
        contactContributor = STUFF((SELECT ', ' +
        (CASE WHEN conHE.father!='' THEN conHE.firstName + ' '+ conHE.father + ' ' + conHE.lastName ELSE conHE.firstName+' '+conHE.lastName END)
            FROM legal_cases legal_case_contributor
            LEFT JOIN legal_cases_contacts lccch ON lccch.case_id = legal_cases.id AND lccch.contactType = 'contributor'
            LEFT JOIN contacts conHE ON conHE.id = lccch.contact_id
            WHERE legal_case_contributor.id = legal_cases.id
            FOR XML PATH('')), 1, 1, ''),
        contactOutsourceTo = STUFF((SELECT ', ' +
                (CASE WHEN conExtLaw.father!='' THEN conExtLaw.firstName + ' '+ conExtLaw.father + ' ' + conExtLaw.lastName ELSE conExtLaw.firstName+' '+conExtLaw.lastName END)
             FROM legal_cases legal_case_outsource
             LEFT JOIN legal_cases_contacts lccExtLaw ON lccExtLaw.case_id = legal_cases.id AND lccExtLaw.contactType = 'external lawyer'
             LEFT JOIN contacts conExtLaw ON conExtLaw.id = lccExtLaw.contact_id
             WHERE legal_case_outsource.id = legal_cases.id
            FOR XML PATH('')), 1, 1, ''),
		companyOutsourceTo = STUFF((SELECT ', ' + companiesExtLaw.name
             FROM legal_cases legal_case_outsource
             LEFT JOIN
                                             legal_cases_companies lccompaniesExtLaw
                                             ON
                                                       lccompaniesExtLaw.case_id         = legal_case_outsource.id
                                                       AND lccompaniesExtLaw.companyType = 'external lawyer'
                                   LEFT JOIN
                                             companies companiesExtLaw
                                             ON
                                                       companiesExtLaw.id = lccompaniesExtLaw.company_id
                         WHERE
                                   legal_case_outsource.id = legal_cases.id
            FOR XML PATH('')), 1, 1, '')
FROM legal_cases
LEFT JOIN workflow_status ON workflow_status.id = legal_cases.case_status_id
INNER JOIN case_types ON case_types.id = legal_cases.case_type_id
INNER JOIN provider_groups ON provider_groups.id = legal_cases.provider_group_id
LEFT JOIN user_profiles AS UP ON UP.user_id = legal_cases.user_id
LEFT JOIN legal_cases_contacts lccre ON lccre.case_id = legal_cases.id AND lccre.contactType = 'contact'
LEFT JOIN contacts conRE ON conRE.id = lccre.contact_id
LEFT JOIN legal_case_contact_roles lccr ON lccr.id = lccre.legal_case_contact_role_id
LEFT JOIN legal_case_effective_effort AS lcee ON lcee.legal_case_id = legal_cases.id
LEFT JOIN legal_case_litigation_details lcld ON lcld.legal_case_id = legal_cases.id AND lcld.id = legal_cases.stage
Where legal_cases.isDeleted = 0;
GO
/****** Object:  Table [dbo].[legal_case_litigation_external_references]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_litigation_external_references](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[stage] [bigint] NOT NULL,
	[number] [nvarchar](255) NOT NULL,
	[refDate] [date] NULL,
	[comments] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_containers]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_containers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_container_status_id] [bigint] NOT NULL,
	[subject] [nvarchar](max) NOT NULL,
	[description] [text] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[case_type_id] [bigint] NOT NULL,
	[provider_group_id] [bigint] NOT NULL,
	[user_id] [bigint] NULL,
	[client_id] [bigint] NULL,
	[caseArrivalDate] [date] NULL,
	[closedOn] [date] NULL,
	[comments] [text] NULL,
	[internalReference] [nvarchar](255) NULL,
	[legal_case_client_position_id] [bigint] NULL,
	[requested_by] [bigint] NULL,
	[visible_in_cp] [tinyint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_opponents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_opponents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[opponent_id] [bigint] NOT NULL,
	[opponent_member_type] [nvarchar](255) NOT NULL,
	[opponent_position] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_related_containers]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_related_containers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_container_id] [bigint] NOT NULL,
	[legal_case_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_addresses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_addresses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company] [bigint] NOT NULL,
	[address] [nvarchar](255) NULL,
	[city] [nvarchar](255) NULL,
	[state] [nvarchar](255) NULL,
	[zip] [nvarchar](32) NULL,
	[country] [bigint] NULL,
	[website] [nvarchar](255) NULL,
	[phone] [nvarchar](255) NULL,
	[fax] [nvarchar](255) NULL,
	[mobile] [nvarchar](255) NULL,
	[email] [nvarchar](255) NULL,
	[street_name] [nvarchar](255) NULL,
	[additional_street_name] [nvarchar](255) NULL,
	[building_number] [nvarchar](255) NULL,
	[address_additional_number] [nvarchar](255) NULL,
	[district_neighborhood] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[clients]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[clients](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NULL,
	[contact_id] [bigint] NULL,
	[term_id] [bigint] NULL,
	[discount_percentage] [bigint] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[partners]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[partners](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NULL,
	[contact_id] [bigint] NULL,
	[createdBy] [bigint] NULL,
	[createdOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[isThirdParty] [nvarchar](10) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[vendors]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[vendors](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NULL,
	[contact_id] [bigint] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[clients_view]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[clients_view] AS
        SELECT
            clients.id AS id,
            clients.term_id AS term_id,
            clients.discount_percentage AS discount_percentage,
            CASE WHEN clients.company_id IS NULL THEN con.tax_number ELSE com.registrationTaxNb END AS tax_number,
            CASE WHEN clients.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS name,
            CASE WHEN clients.company_id IS NULL THEN isnull(con.foreignFirstName, '') + ' ' + isnull(con.foreignLastName, '') ELSE com.foreignName END AS foreignName,
            CASE WHEN clients.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS member_name,
            CASE WHEN clients.company_id IS NULL THEN 'Person' ELSE 'Company' END AS type,
            CASE WHEN clients.company_id IS NULL THEN ( '' + CAST(clients.contact_id AS NVARCHAR(MAX)) ) ELSE ( '' + CAST(clients.company_id AS NVARCHAR(MAX)) ) END AS member_id, -- Cast to NVARCHAR
            'clients' AS model,
            clients.createdBy,
            clients.createdOn,
            clients.modifiedBy,
            clients.modifiedOn,
            ( created.firstName + ' ' + created.lastName) AS createdByName,
            ( modified.firstName + ' ' + modified.lastName) AS modifiedByName,
            NULL AS isThirdParty,
            -- NEW: Email column for clients
            CASE WHEN clients.company_id IS NOT NULL THEN ca.email ELSE ce.email END AS email
        FROM
            clients
        LEFT JOIN companies com ON com.id = clients.company_id
        LEFT JOIN contacts con ON con.id = clients.contact_id
        OUTER APPLY (SELECT TOP 1 email FROM company_addresses WHERE company = com.id ORDER BY email ASC) AS ca -- Join for company email
        OUTER APPLY (SELECT TOP 1 email FROM contact_emails WHERE contact_id = con.id ORDER BY email ASC) AS ce -- Join for contact email
        LEFT JOIN user_profiles created ON created.user_id = clients.createdBy
        LEFT JOIN user_profiles modified ON modified.user_id = clients.modifiedBy

        UNION ALL

        SELECT
            vendors.id AS id,
            NULL AS term_id,
            0 AS discount_percentage,
            CASE WHEN vendors.company_id IS NULL THEN con.tax_number ELSE com.registrationTaxNb END AS tax_number,
            CASE WHEN vendors.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS name,
            CASE WHEN vendors.company_id IS NULL THEN con.foreignFirstName+' '+con.foreignLastName ELSE com.foreignName END AS foreignName,
            CASE WHEN vendors.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS member_name,
            CASE WHEN vendors.company_id IS NULL THEN 'Person' ELSE 'Company' END AS type,
            CASE WHEN vendors.company_id IS NULL THEN ( '' + CAST(vendors.contact_id AS NVARCHAR(MAX)) ) ELSE ( '' + CAST(vendors.company_id AS NVARCHAR(MAX)) ) END AS member_id, -- Cast to NVARCHAR
            'suppliers' AS model,
            vendors.createdBy,
            vendors.createdOn,
            vendors.modifiedBy,
            vendors.modifiedOn,
            ( created.firstName + ' ' + created.lastName) AS createdByName,
            ( modified.firstName + ' ' + modified.lastName) AS modifiedByName,
            NULL AS isThirdParty,
            -- NEW: Email column for vendors
            CASE WHEN vendors.company_id IS NOT NULL THEN ca.email ELSE ce.email END AS email
        FROM
            vendors
        LEFT JOIN companies com ON com.id = vendors.company_id
        LEFT JOIN contacts con ON con.id = vendors.contact_id
        OUTER APPLY (SELECT TOP 1 email FROM company_addresses WHERE company = com.id ORDER BY email ASC) AS ca -- Join for company email
        OUTER APPLY (SELECT TOP 1 email FROM contact_emails WHERE contact_id = con.id ORDER BY email ASC) AS ce -- Join for contact email
        LEFT JOIN user_profiles created ON created.user_id = vendors.createdBy
        LEFT JOIN user_profiles modified ON modified.user_id = vendors.modifiedBy

        UNION ALL

        SELECT
            partners.id AS id,
            NULL AS term_id,
            0 AS discount_percentage,
            NULL AS tax_number, -- Original view had NULL here for partners
            CASE WHEN partners.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS name,
            CASE WHEN partners.company_id IS NULL THEN con.foreignFirstName+' '+con.foreignLastName ELSE com.foreignName END AS foreignName,
            CASE WHEN partners.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS member_name,
            CASE WHEN partners.company_id IS NULL THEN 'Person' ELSE 'Company' END AS type,
            CASE WHEN partners.company_id IS NULL THEN ( '' + CAST(partners.contact_id AS NVARCHAR(MAX)) ) ELSE ( '' + CAST(partners.company_id AS NVARCHAR(MAX)) ) END AS member_id, -- Cast to NVARCHAR
            'partners' AS model,
            partners.createdBy,
            partners.createdOn,
            partners.modifiedBy,
            partners.modifiedOn,
            ( created.firstName + ' ' + created.lastName) AS createdByName,
            ( modified.firstName + ' ' + modified.lastName) AS modifiedByName,
            partners.isThirdParty AS isThirdParty,
            -- NEW: Email column for partners
            CASE WHEN partners.company_id IS NOT NULL THEN ca.email ELSE ce.email END AS email
        FROM
            partners
        LEFT JOIN companies com ON com.id = partners.company_id
        LEFT JOIN contacts con ON con.id = partners.contact_id
        OUTER APPLY (SELECT TOP 1 email FROM company_addresses WHERE company = com.id ORDER BY email ASC) AS ca -- Join for company email
        OUTER APPLY (SELECT TOP 1 email FROM contact_emails WHERE contact_id = con.id ORDER BY email ASC) AS ce -- Join for contact email
        LEFT JOIN user_profiles created ON created.user_id = partners.createdBy
        LEFT JOIN user_profiles modified ON modified.user_id = partners.modifiedBy;
GO
/****** Object:  Table [dbo].[opponents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opponents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NULL,
	[contact_id] [bigint] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[legal_cases_grid]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

            CREATE VIEW [dbo].[legal_cases_grid] AS
            SELECT TOP(9223372036854775800)
                legal_cases.id,
                CASE WHEN legal_cases.channel = 'CP' THEN 'yes' ELSE 'no' END AS isCP,
                legal_cases.channel,
                legal_cases.case_status_id,
                legal_cases.case_type_id,
                legal_cases.provider_group_id,
                legal_cases.user_id,
                legal_cases.contact_id,
                legal_cases.client_id,
                legal_cases.subject,
                legal_cases.description,
                legal_cases.latest_development,
                legal_cases.priority,
                legal_cases.arrivalDate,
                legal_cases.caseArrivalDate,
                legal_cases.dueDate,
                legal_cases.closedOn,
                legal_cases.statusComments,
                legal_cases.category,
                legal_cases.caseValue,
                legal_cases.recoveredValue,
                legal_cases.judgmentValue,
                legal_cases.internalReference,
                legal_cases.externalizeLawyers,
                legal_cases.estimatedEffort,
                CAST(legal_cases.createdOn as DATE) as createdOn,
                CAST(legal_cases.modifiedOn as DATE) as modifiedOn,
                legal_cases.createdBy,
                legal_cases.modifiedBy,
                legal_cases.archived,
                legal_cases.private,
                legal_cases.timeTrackingBillable,
                legal_cases.expensesBillable,
                lcee.effectiveEffort,
                'M' + CAST(legal_cases.id AS NVARCHAR(20)) AS caseID, -- Increased NVARCHAR constraint
                workflow_status.name as status,
                case_types.name as type,
                provider_groups.name as providerGroup,
                UP.firstName + ' ' + UP.lastName AS assignee,
                legal_cases.archived as archivedCases,
                legal_case_litigation_details.id AS litigation_details_id,
                legal_case_litigation_details.court_type_id AS court_type_id,
                legal_case_litigation_details.court_degree_id AS court_degree_id,
                legal_case_litigation_details.court_region_id AS court_region_id,
                legal_case_litigation_details.court_id AS court_id,
                legal_case_litigation_details.comments AS comments,
                legal_case_litigation_details.sentenceDate AS sentenceDate,
                com.name AS company,
                com.id AS company_id,
                (CASE WHEN conRE.father!='' THEN conRE.firstName + ' '+ conRE.father + ' ' + conRE.lastName ELSE conRE.firstName+' '+conRE.lastName END) AS contact,
                (CASE WHEN conHE.father!='' THEN conHE.firstName + ' '+ conHE.father + ' ' + conHE.lastName ELSE conHE.firstName+' '+conHE.lastName END) AS contactContributor,
                (CASE WHEN conExtLaw.father!='' THEN conExtLaw.firstName + ' '+ conExtLaw.father + ' ' + conExtLaw.lastName ELSE conExtLaw.firstName+' '+conExtLaw.lastName END) AS contactOutsourceTo,
                compiesExtLaw.name AS companyOutsourceTo,
                legal_case_litigation_external_references.number AS litigationExternalRef,
                clients_view.name AS clientName,
                clients_view.type AS clientType,
                legal_cases.referredBy,
                legal_cases.requestedBy,
                (CASE WHEN referredByContact.father!='' THEN referredByContact.firstName + ' '+ referredByContact.father + ' ' + referredByContact.lastName ELSE referredByContact.firstName+' '+referredByContact.lastName END) AS referredByName,
                (CASE WHEN requestedByContact.father!='' THEN requestedByContact.firstName + ' '+ requestedByContact.father + ' ' + requestedByContact.lastName ELSE requestedByContact.firstName+' '+requestedByContact.lastName END) AS requestedByName,
                legal_case_containers.subject AS legalCaseContainerSubject,
                legal_cases.legal_case_stage_id as legal_case_stage_id,
                -- New closure-related columns
                legal_cases.closure_requested_by,
                (CASE WHEN closureRequestedByContact.father!='' THEN closureRequestedByContact.firstName + ' '+ closureRequestedByContact.father + ' ' + closureRequestedByContact.lastName ELSE closureRequestedByContact.firstName+' '+closureRequestedByContact.lastName END) AS closureRequestedByName,
                legal_cases.closed_by,
                (CASE WHEN closedByContact.father!='' THEN closedByContact.firstName + ' '+ closedByContact.father + ' ' + closedByContact.lastName ELSE closedByContact.firstName+' '+closedByContact.lastName END) AS closedByName,
                legal_cases.closure_comments,
                legal_cases.approval_step,
                legal_cases.first_litigation_case_court_activity_purpose,
                legal_cases.closedBy_comments,
                opponentNames = STUFF((SELECT ', ' +
                    (CASE WHEN legal_case_opponents.opponent_member_type = 'company'
                    THEN opponentCompany.name
                    ELSE (CASE WHEN opponentContact.father!='' THEN opponentContact.firstName + ' '+ opponentContact.father + ' ' + opponentContact.lastName ELSE opponentContact.firstName+' '+opponentContact.lastName END) END)
                    FROM legal_case_opponents
                    INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id
                    LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company'
                    LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact'
                    WHERE legal_case_opponents.case_id = legal_cases.id
                    FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 2, ''), -- Changed from 1,1, to 1,2, for ', ' and added .value('.', 'NVARCHAR(MAX)')
                legal_cases.legal_case_client_position_id as legal_case_client_position_id,
                legal_cases.legal_case_success_probability_id as legal_case_success_probability_id
            FROM legal_cases
            LEFT JOIN legal_cases_companies lcccom ON lcccom.case_id = legal_cases.id
            LEFT JOIN companies com ON lcccom.company_id = com.id
            LEFT JOIN legal_cases_contacts lccre ON lccre.case_id = legal_cases.id AND lccre.contactType = 'contact'
            LEFT JOIN contacts conRE ON conRE.id = lccre.contact_id
            LEFT JOIN legal_cases_contacts lccch ON lccch.case_id = legal_cases.id AND lccch.contactType = 'contributor'
            LEFT JOIN contacts conHE ON conHE.id = lccch.contact_id
            LEFT JOIN legal_cases_contacts lccExtLaw ON lccExtLaw.case_id = legal_cases.id AND lccExtLaw.contactType = 'external lawyer'
            LEFT JOIN contacts conExtLaw ON conExtLaw.id = lccExtLaw.contact_id
            LEFT JOIN legal_cases_companies lccompaniesExtLaw ON lccompaniesExtLaw.case_id = legal_cases.id AND lccompaniesExtLaw.companyType = 'external lawyer'
            LEFT JOIN companies compiesExtLaw ON compiesExtLaw.id = lccompaniesExtLaw.company_id
            LEFT JOIN workflow_status ON workflow_status.id = legal_cases.case_status_id
            INNER JOIN case_types ON case_types.id = legal_cases.case_type_id
            INNER JOIN provider_groups ON provider_groups.id = legal_cases.provider_group_id
            LEFT JOIN user_profiles as UP ON UP.user_id = legal_cases.user_id
            LEFT JOIN legal_case_effective_effort AS lcee ON lcee.legal_case_id = legal_cases.id
            LEFT JOIN legal_case_litigation_details ON legal_case_litigation_details.id = legal_cases.stage
            LEFT JOIN legal_case_litigation_external_references ON legal_case_litigation_external_references.stage = legal_cases.stage
            LEFT JOIN clients_view ON clients_view.id = legal_cases.client_id AND clients_view.model = 'clients'
            LEFT JOIN contacts as referredByContact ON referredByContact.id = legal_cases.referredBy
            LEFT JOIN contacts as requestedByContact ON requestedByContact.id = legal_cases.requestedBy
            LEFT JOIN legal_case_related_containers ON legal_case_related_containers.legal_case_id = legal_cases.id
            LEFT JOIN legal_case_containers ON legal_case_containers.id = legal_case_related_containers.legal_case_container_id
            -- New joins for the closure contacts
            LEFT JOIN contacts as closureRequestedByContact ON closureRequestedByContact.id = legal_cases.closure_requested_by
            LEFT JOIN contacts as closedByContact ON closedByContact.id = legal_cases.closed_by
            WHERE legal_cases.isDeleted = 0;
        
GO
/****** Object:  View [dbo].[legal_cases_per_external_lawyer]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[legal_cases_per_external_lawyer] AS SELECT TOP(9223372036854775800) legal_cases.id, legal_cases.case_status_id, legal_cases.case_type_id, legal_cases.provider_group_id, legal_cases.user_id,
	legal_cases.contact_id, legal_cases.client_id, legal_cases.subject, legal_cases.description, legal_cases.priority, legal_cases.arrivalDate, legal_cases.dueDate, legal_cases.statusComments,
	legal_cases.category, legal_cases.caseValue, legal_cases.internalReference, legal_cases.externalizeLawyers, legal_cases.estimatedEffort,
	CAST(legal_cases.createdOn as DATE) createdOn, CAST(legal_cases.modifiedOn as DATE) as modifiedOn, legal_cases.createdBy, legal_cases.modifiedBy,
	legal_cases.archived, legal_cases.private, lcee.effectiveEffort, 'M' + CAST(legal_cases.id AS nvarchar) AS caseID, workflow_status.name AS status,
	case_types.name AS type, provider_groups.name AS providerGroup, UP.firstName + ' ' + UP.lastName AS assignee, legal_cases.archived AS archivedCases, legal_cases.isDeleted AS isDeleted,
	CASE WHEN conRE.father!='' THEN conRE.firstName + ' '+ conRE.father + ' ' + conRE.lastName ELSE conRE.firstName+' '+conRE.lastName END AS contact, lccr.name AS role, lccr.id AS role_id,
        lcld.sentenceDate,lcld.court_type_id,lcld.court_degree_id,lcld.court_region_id,lcld.court_id,
    contactContributor = STUFF((SELECT ', ' +
        (CASE WHEN conHE.father!='' THEN conHE.firstName + ' '+ conHE.father + ' ' + conHE.lastName ELSE conHE.firstName+' '+conHE.lastName END)
         FROM legal_cases legal_case_contributor
         LEFT JOIN legal_cases_contacts lccch ON lccch.case_id = legal_cases.id AND lccch.contactType = 'contributor'
        LEFT JOIN contacts conHE ON conHE.id = lccch.contact_id
        WHERE legal_case_contributor.id = legal_cases.id
        FOR XML PATH('')), 1, 1, ''),
    contactOutsourceTo = STUFF((SELECT ', ' +
        (CASE WHEN conExtLaw.father!='' THEN conExtLaw.firstName + ' '+ conExtLaw.father + ' ' + conExtLaw.lastName ELSE conExtLaw.firstName+' '+conExtLaw.lastName END)
         FROM legal_cases legal_case_outsource
         LEFT JOIN legal_cases_contacts lccExtLaw ON lccExtLaw.case_id = legal_cases.id AND lccExtLaw.contactType = 'external lawyer'
    LEFT JOIN contacts conExtLaw ON conExtLaw.id = lccExtLaw.contact_id
     WHERE legal_case_outsource.id = legal_cases.id
        FOR XML PATH('')), 1, 1, ''),
    companyOutsourceTo = STUFF((SELECT ', ' + companiesExtLaw.name
             FROM legal_cases legal_case_outsource
             LEFT JOIN
                                             legal_cases_companies lccompaniesExtLaw
                                             ON
                                                       lccompaniesExtLaw.case_id         = legal_case_outsource.id
                                                       AND lccompaniesExtLaw.companyType = 'external lawyer'
                                   LEFT JOIN
                                             companies companiesExtLaw
                                             ON
                                                       companiesExtLaw.id = lccompaniesExtLaw.company_id
                         WHERE
                                   legal_case_outsource.id = legal_cases.id
            FOR XML PATH('')), 1, 1, '')
FROM legal_cases
LEFT JOIN workflow_status ON workflow_status.id = legal_cases.case_status_id
INNER JOIN case_types ON case_types.id = legal_cases.case_type_id
INNER JOIN provider_groups ON provider_groups.id = legal_cases.provider_group_id
LEFT JOIN user_profiles AS UP ON UP.user_id = legal_cases.user_id
LEFT JOIN legal_cases_contacts lccre ON lccre.case_id = legal_cases.id AND lccre.contactType = 'external lawyer'
LEFT JOIN contacts conRE ON conRE.id = lccre.contact_id
LEFT JOIN legal_case_contact_roles lccr ON lccr.id = lccre.legal_case_contact_role_id
LEFT JOIN legal_case_effective_effort AS lcee ON lcee.legal_case_id = legal_cases.id
LEFT JOIN legal_case_litigation_details lcld ON lcld.legal_case_id = legal_cases.id AND lcld.id = legal_cases.stage
Where legal_cases.isDeleted = 0;
GO
/****** Object:  Table [dbo].[contact_nationalities]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contact_nationalities](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contact_id] [bigint] NOT NULL,
	[nationality_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[contact_nationalities_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[contact_nationalities_details] AS
 SELECT TOP(9223372036854775800)
	contact_nationalities.contact_id as contact_id,
	contact_nationalities.nationality_id as nationality_id,
	( CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END ) as contactName
 FROM contact_nationalities
 LEFT JOIN contacts ON contacts.id = contact_nationalities.contact_id;
GO
/****** Object:  Table [dbo].[ip_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ip_statuses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[category] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[ip_names]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ip_names](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_cases_countries_renewals]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_cases_countries_renewals](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[intellectual_property_id] [bigint] NOT NULL,
	[comments] [nvarchar](max) NULL,
	[expiryDate] [date] NULL,
	[renewalDate] [date] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[intellectual_property_rights]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[intellectual_property_rights](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[ip_classes]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ip_classes](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[ip_subcategories]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ip_subcategories](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_cases_countries_renewals_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_cases_countries_renewals_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_country_renewal_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[ip_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ip_details](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[intellectual_property_right_id] [bigint] NULL,
	[ip_class_id] [bigint] NULL,
	[ip_subcategory_id] [bigint] NULL,
	[ip_status_id] [bigint] NULL,
	[ip_name_id] [bigint] NULL,
	[filingNumber] [nvarchar](255) NULL,
	[acceptanceRejection] [date] NULL,
	[certificationNumber] [nvarchar](255) NULL,
	[registrationReference] [nvarchar](255) NULL,
	[registrationDate] [date] NULL,
	[agentId] [bigint] NULL,
	[agentType] [nvarchar](255) NULL,
	[country_id] [bigint] NULL,
	[legal_case_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[intellectual_properties_grid]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[intellectual_properties_grid] AS
select lc.id, lc.channel, lc.provider_group_id, pg.name as providerGroup, lc.user_id as legalCaseAssigneeId,
       (up.firstName + ' ' + up.lastName) as legalCaseAssignee, up.status as legalCaseAssigneeStatus, lc.client_id, cliv.name as client, cliv.type as clientType,
       lc.subject, lc.description, lc.arrivalDate, lc.statusComments, lc.category,
       lc.createdOn, lc.createdBy, lc.modifiedOn, lc.modifiedBy, lc.modifiedByChannel, lc.timeTrackingBillable, lc.expensesBillable, lc.isDeleted,
       ipd.intellectual_property_right_id, ipri.name as intellectualPropertyRight, ipd.ip_class_id, ipcl.name as ipClass, ipd.ip_subcategory_id,
       ipsc.name as ipSubcategory, ipd.ip_status_id, ipst.name as ipStatus, ipst.category as ipStatusCategory, ipd.ip_name_id, ipna.name as ipName, ipd.filingNumber, ipd.acceptanceRejection,
       ipd.certificationNumber, ipd.registrationDate, ipd.registrationReference, ipd.agentId, ipd.agentType,
       case when ipd.agentType = 'Company' then ipcomp.name else (CASE WHEN ipcont.father!='' THEN ipcont.firstName + ' '+ ipcont.father + ' ' + ipcont.lastName ELSE ipcont.firstName+' '+ipcont.lastName END) end as agent, ipd.country_id,
       (SELECT TOP 1 lccr.expiryDate from legal_cases_countries_renewals lccr where lccr.intellectual_property_id = lc.id ORDER BY lccr.renewalDate DESC ) as renewalExpiryDate,
       (SELECT MAX(renewalDate) from legal_cases_countries_renewals lccr where lccr.intellectual_property_id = lc.id) as renewalDate,
       (modified.firstName + ' ' + modified.lastName) AS modifiedByName, (created.firstName + ' ' + created.lastName) AS createdByName,
        renewalUsersToRemind = STUFF((SELECT '; ' + (lccrup.firstName + ' ' + lccrup.lastName) FROM legal_cases_countries_renewals_users lccru LEFT JOIN user_profiles lccrup ON lccrup.user_id = lccru.user_id left join legal_cases_countries_renewals lccr on lccr.intellectual_property_id = lc.id WHERE lccru.legal_case_country_renewal_id = lccr.id FOR XML PATH('')), 1, 1, '')
from legal_cases lc
         left join provider_groups pg on pg.id = lc.provider_group_id
         left join user_profiles up on up.user_id = lc.user_id
         left join clients_view cliv on cliv.id = lc.client_id and cliv.model = 'clients'
         left join ip_details ipd on ipd.legal_case_id = lc.id
         left join intellectual_property_rights ipri on ipri.id = ipd.intellectual_property_right_id
         left join ip_classes ipcl on ipcl.id = ipd.ip_class_id
         left join ip_subcategories ipsc on ipsc.id = ipd.ip_subcategory_id
         left join ip_statuses ipst on ipst.id = ipd.ip_status_id
         left join ip_names ipna on ipna.id = ipd.ip_name_id
         left join companies ipcomp on ipcomp.id = ipd.agentId
         left join contacts ipcont on ipcont.id = ipd.agentId
         LEFT JOIN user_profiles created ON created.user_id = lc.createdBy
         LEFT JOIN user_profiles modified ON modified.user_id = lc.modifiedBy
where lc.category = 'IP' and lc.isDeleted = 0
GO
/****** Object:  Table [dbo].[reminders]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[reminders](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[summary] [nvarchar](max) NOT NULL,
	[reminder_type_id] [bigint] NOT NULL,
	[remindDate] [date] NOT NULL,
	[remindTime] [time](0) NOT NULL,
	[status] [nvarchar](9) NOT NULL,
	[legal_case_id] [bigint] NULL,
	[company_id] [bigint] NULL,
	[contact_id] [bigint] NULL,
	[task_id] [bigint] NULL,
	[contract_id] [bigint] NULL,
	[legal_case_hearing_id] [bigint] NULL,
	[related_id] [bigint] NULL,
	[related_object] [varchar](255) NULL,
	[parent_id] [bigint] NULL,
	[is_cloned] [tinyint] NULL,
	[notify_before_time] [bigint] NULL,
	[notify_before_time_type] [varchar](5) NULL,
	[notify_before_type] [varchar](15) NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[opinion_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[reminders_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[reminders_full_details] AS
SELECT reminders.id, reminders.user_id, reminders.summary, reminders.reminder_type_id, reminders.remindDate,
       reminders.remindTime, reminders.status, reminders.legal_case_id, reminders.company_id, reminders.contact_id,
       reminders.task_id, CAST( reminders.createdOn AS date ) AS createdOn, reminders.modifiedOn, reminders.createdBy,created.status as createdByStatus,
       (modified.firstName + ' ' + modified.lastName) AS modifiedByName, (created.firstName + ' ' + created.lastName) AS createdByName,
       reminders.parent_id, ( 'R' + CAST( reminders.id AS nvarchar ) ) as reminderID, legal_cases.subject as legal_case,
       ( CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END ) as contact, companies.name as company,
       tasks.title as task, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as remindUser ,user_profiles.status as user_status,
       ( 'M' + CAST( legal_cases.id AS nvarchar ) ) as legalCaseId,reminders.notify_before_time,reminders.notify_before_time_type,
       ( 'T' + CAST( tasks.id AS nvarchar ) ) as taskId, reminders.contract_id, contract.name as contract_name
FROM reminders LEFT JOIN legal_cases ON legal_cases.id = reminders.legal_case_id
               LEFT JOIN contract ON contract.id = reminders.contract_id
               LEFT JOIN companies ON companies.id = reminders.company_id
               LEFT JOIN contacts ON contacts.id = reminders.contact_id
               LEFT JOIN tasks ON tasks.id = reminders.task_id
               LEFT JOIN user_profiles ON user_profiles.user_id = reminders.user_id
               LEFT JOIN user_profiles created ON created.user_id = reminders.createdBy
               LEFT JOIN user_profiles modified ON modified.user_id = reminders.modifiedBy
Where legal_cases.isDeleted = 0 or reminders.legal_case_id IS NULL;
GO
/****** Object:  Table [dbo].[legal_case_container_opponents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_container_opponents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_container_id] [bigint] NOT NULL,
	[opponent_id] [bigint] NOT NULL,
	[opponent_member_type] [nvarchar](255) NOT NULL,
	[opponent_position] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_container_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_container_statuses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[category] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[legal_case_containers_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[legal_case_containers_full_details] AS
SELECT legal_case_containers.id AS id,
legal_case_containers.case_type_id,
legal_case_containers.provider_group_id, legal_case_containers.user_id, legal_case_containers.visible_in_cp,
legal_case_containers.caseArrivalDate, legal_case_containers.closedOn AS containerClosedOn, legal_case_containers.comments AS containerComments, legal_case_containers.internalReference,
legal_cases.subject AS caseSubject,
('M' + CAST(legal_cases.id as nvarchar))  as legalCaseId,
( 'MC' + CAST( legal_case_containers.id AS nvarchar ) ) AS containerId,
legal_case_containers.subject AS containerSubject, legal_case_containers.description AS containerDescription,
legal_case_containers.legal_case_container_status_id AS legal_case_container_status_id,
legal_case_container_statuses.name AS containerStatus,
case_types.name as type,provider_groups.name as providerGroup,
 ( UP.firstName +  ' ' + UP.lastName ) as assignee, legal_case_containers.client_id,
 clients_view.name AS clientName, clients_view.foreignName AS client_foreign_name, clients_view.type AS clientType,
 ( CASE WHEN requested_by_contact.father!='' THEN requested_by_contact.firstName + ' '+ requested_by_contact.father + ' ' + requested_by_contact.lastName ELSE requested_by_contact.firstName+' '+requested_by_contact.lastName END ) AS requested_by_name,
opponentNames = STUFF((SELECT ', ' +
    (CASE WHEN legal_case_container_opponents.opponent_member_type = 'company'
    THEN opponentCompany.name
    ELSE (CASE WHEN opponentContact.father!='' THEN opponentContact.firstName + ' '+ opponentContact.father + ' ' + opponentContact.lastName ELSE opponentContact.firstName+' '+opponentContact.lastName END) END )
     FROM legal_case_container_opponents
     INNER JOIN opponents ON opponents.id = legal_case_container_opponents.opponent_id
     LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_container_opponents.opponent_member_type = 'company'
     LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_container_opponents.opponent_member_type = 'contact'
     WHERE legal_case_container_opponents.case_container_id = legal_case_containers.id
    FOR XML PATH('')), 1, 1, ''),
opponent_foreign_name = STUFF((SELECT ', ' +
    (
        CASE WHEN legal_case_container_opponents.opponent_member_type = 'company'
        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END)
        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) END
    )
     FROM legal_case_container_opponents
     INNER JOIN opponents ON opponents.id = legal_case_container_opponents.opponent_id
     LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_container_opponents.opponent_member_type = 'company'
     LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_container_opponents.opponent_member_type = 'contact'
     WHERE legal_case_container_opponents.case_container_id = legal_case_containers.id
    FOR XML PATH('')), 1, 1, ''),
 legal_case_containers.legal_case_client_position_id as legal_case_client_position_id,
( created.firstName + ' ' + created.lastName ) AS createdBy, legal_cases.id as legal_case_id,
( modified.firstName + ' ' + modified.lastName ) AS modifiedBy, CAST(legal_case_containers.createdOn as DATE) as createdOn, CAST(legal_case_containers.modifiedOn as DATE) as modifiedOn
FROM legal_case_containers
LEFT JOIN legal_case_related_containers ON legal_case_related_containers.legal_case_container_id = legal_case_containers.id
LEFT JOIN legal_cases ON legal_cases.id = legal_case_related_containers.legal_case_id
LEFT JOIN user_profiles created ON created.user_id = legal_case_containers.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = legal_case_containers.modifiedBy
LEFT JOIN legal_case_container_statuses ON legal_case_container_statuses.id = legal_case_containers.legal_case_container_status_id
INNER JOIN case_types ON case_types.id = legal_case_containers.case_type_id 
INNER JOIN provider_groups ON provider_groups.id = legal_case_containers.provider_group_id 
LEFT JOIN user_profiles as UP ON UP.user_id = legal_case_containers.user_id 
LEFT JOIN clients_view ON clients_view.id = legal_case_containers.client_id AND clients_view.model = 'clients'
LEFT JOIN contacts as requested_by_contact ON requested_by_contact.id = legal_case_containers.requested_by;
GO
/****** Object:  Table [dbo].[company_legal_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_legal_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_lawyers]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_lawyers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NOT NULL,
	[lawyer_id] [bigint] NOT NULL,
	[comments] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[companies_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
--companies_full_details;
CREATE VIEW [dbo].[companies_full_details] AS SELECT
    TOP(9223372036854775800)
    companies.id, ('COM' + CAST( companies.id AS nvarchar )) AS companyID, companies.legalName, companies.name, companies.shortName,
    companies.foreignName, companies.status, companies.category, companies.company_category_id, contact_company_categories.name AS company_category,
    contact_company_categories.keyName  AS company_category_keyName,
    companies.company_sub_category_id, contact_company_sub_categories.name AS company_sub_category, companies.private,
    companies.company_id, companies.nationality_id, companies.company_legal_type_id,
    companies.object, companies.capital, companies.capitalCurrency, companies.nominalShares,
    companies.bearerShares, companies.shareParValue, companies.shareParValueCurrency,
    companies.qualifyingShares, companies.registrationNb, companies.registrationDate,
    companies.registrationCity, companies.registrationTaxNb, companies.registrationYearsNb,
    companies.registrationByLawNotaryPublic, companies.registrationByLawRef, companies.registrationByLawDate,
    companies.registrationByLawCity, companies.sharesLocation,
    companies.ownedByGroup, companies.sheerLebanese, companies.contributionRatio, companies.notes,
    companies.otherNotes, companies.createdOn, companies.createdBy, companies.modifiedOn, companies.modifiedBy,
    (modified.firstName + ' ' + modified.lastName) AS modifiedByName, (created.firstName + ' ' + created.lastName) AS createdByName,
    clt.name as legalType, cp.name as majorParent, lawyer=STUFF( (SELECT '; '+ (CASE WHEN cont.father!='' THEN cont.firstName + ' '+ cont.father + ' ' + cont.lastName ELSE cont.firstName+' '+cont.lastName END) from company_lawyers LEFT JOIN contacts cont ON cont.id = company_lawyers.lawyer_id where company_lawyers.company_id=companies.id FOR XML PATH('')), 1, 1, '') ,
    companyRegistrationAuthority.name as registrationAuthorityName, companies.internalReference, companies.crReleasedOn, companies.crExpiresOn,company_addresses.email
    ,companies.additional_id_type, companies.additional_id_value 
    FROM companies
        LEFT JOIN company_legal_types clt ON clt.id = companies.company_legal_type_id
        LEFT JOIN companies cp ON companies.company_id = cp.id
        LEFT JOIN contact_company_categories ON contact_company_categories.id = companies.company_category_id
        LEFT JOIN contact_company_sub_categories ON contact_company_sub_categories.id = companies.company_sub_category_id
        LEFT JOIN companies companyRegistrationAuthority ON companyRegistrationAuthority.id = companies.registrationAuthority AND companyRegistrationAuthority.category = 'Internal'
        LEFT JOIN company_addresses ON company_addresses.company = companies.id AND company_addresses.email IS NOT NULL
        LEFT JOIN user_profiles created ON created.user_id = companies.createdBy
        LEFT JOIN user_profiles modified ON modified.user_id = companies.modifiedBy;
GO
/****** Object:  Table [dbo].[user_groups]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_groups](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](max) NULL,
	[flagNeedApproval] [tinyint] NULL,
	[needApprovalOnAdd] [tinyint] NULL,
	[system_group] [tinyint] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[seniority_levels]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[seniority_levels](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_changes]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_changes](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[action] [nvarchar](32) NOT NULL,
	[fieldName] [nvarchar](32) NOT NULL,
	[beforeData] [text] NULL,
	[afterData] [text] NULL,
	[modifiedOn] [smalldatetime] NOT NULL,
	[modifiedBy] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[countries_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[countries_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[country_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[user_changes_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
--user_changes_full_details;
CREATE VIEW [dbo].[user_changes_full_details] AS SELECT
	TOP(9223372036854775800)
	user_changes.id,
	user_changes.user_id,
	user_changes.action,
	user_changes.fieldName,
	CAST(user_changes.modifiedOn AS DATE) AS modifiedOn,
	user_changes.modifiedBy,
	( userProfile.firstName + ' ' + userProfile.lastName ) AS userFullName,
	( modified.firstName + ' ' + modified.lastName ) AS modifiedFullName,
	CASE WHEN user_changes.fieldName = 'country' OR user_changes.fieldName = 'nationality'
	THEN (select name beforeData from countries_languages where countries_languages.id = CAST( user_changes.beforeData AS nvarchar ) AND countries_languages.language_id = 1)
	ELSE (CASE WHEN user_changes.fieldName = 'user_group_id' THEN (select user_groups.name beforeData from user_groups where user_groups.id = CAST( user_changes.beforeData AS nvarchar ) ) ELSE ( CASE WHEN user_changes.fieldName = 'seniority_level_id' THEN (select seniority_levels.name beforeData from seniority_levels where seniority_levels.id = CAST( user_changes.beforeData AS nvarchar ) ) ELSE user_changes.beforeData END) END) END as beforeData,
	CASE  WHEN user_changes.fieldName = 'country' OR user_changes.fieldName = 'nationality'  THEN (select name afterData from countries_languages where countries_languages.id = CAST( user_changes.afterData AS nvarchar ) AND countries_languages.language_id = 1)
	ELSE (CASE WHEN user_changes.fieldName = 'user_group_id' THEN (select user_groups.name afterData from user_groups where user_groups.id = CAST( user_changes.afterData AS nvarchar ))ELSE (CASE WHEN user_changes.fieldName = 'seniority_level_id' THEN (select seniority_levels.name afterData from seniority_levels where seniority_levels.id = CAST( user_changes.afterData AS nvarchar ))ELSE user_changes.afterData END	)END)
	END as afterData
	FROM user_changes
	INNER JOIN user_profiles userProfile ON userProfile.user_id = user_changes.user_id
	INNER JOIN user_profiles modified ON modified.user_id = user_changes.modifiedBy;
GO
/****** Object:  View [dbo].[opponents_view]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[opponents_view] AS
 select opponents.id AS id,
 CASE WHEN opponents.company_id IS NOT NULL THEN com.name ELSE ( CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) END AS name,
 CASE WHEN opponents.company_id IS NOT NULL THEN 'Company' ELSE 'Person' END AS type,
  CASE WHEN opponents.company_id IS NOT NULL THEN opponents.company_id ELSE opponents.contact_id END AS member_id,
 'opponents' AS model
 from opponents
 left join companies com on com.id = opponents.company_id
 left join contacts con on con.id = opponents.contact_id;
GO
/****** Object:  Table [dbo].[court_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[court_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[court_degrees]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[court_degrees](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[court_regions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[court_regions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[courts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[courts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[court_rank_id] [bigint] NULL,
	[court_region_id] [bigint] NULL,
	[court_type_id] [bigint] NULL,
	[court_hierarchy] [int] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_stage_contacts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_stage_contacts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[stage] [bigint] NOT NULL,
	[contact] [bigint] NOT NULL,
	[contact_role] [bigint] NULL,
	[comments] [text] NULL,
	[contact_type] [nvarchar](30) NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_litigation_stages_opponents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_litigation_stages_opponents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[stage] [bigint] NOT NULL,
	[opponent_id] [bigint] NOT NULL,
	[opponent_position] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_hearings]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_hearings](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_id] [bigint] NOT NULL,
	[task_id] [bigint] NULL,
	[startDate] [date] NULL,
	[startTime] [time](0) NULL,
	[postponedDate] [date] NULL,
	[postponedTime] [time](0) NULL,
	[summary] [text] NULL,
	[summaryToClient] [text] NULL,
	[verifiedSummary] [nvarchar](1) NULL,
	[clientReportEmailSent] [nvarchar](10) NULL,
	[is_deleted] [tinyint] NOT NULL,
	[judged] [nvarchar](3) NULL,
	[judgment] [text] NULL,
	[type] [bigint] NULL,
	[stage] [bigint] NULL,
	[comments] [text] NULL,
	[reasons_of_postponement] [text] NULL,
	[hearing_outcome] [nvarchar](4) NULL,
	[reason_of_win_or_lose] [bigint] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[createdByChannel] [nvarchar](3) NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedByChannel] [nvarchar](3) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[email] [nvarchar](255) NOT NULL,
	[password] [nvarchar](255) NULL,
	[banned] [char](1) NULL,
	[ban_reason] [nvarchar](255) NULL,
	[last_ip] [nvarchar](45) NULL,
	[last_login] [smalldatetime] NULL,
	[status] [nvarchar](45) NOT NULL,
	[firstName] [nvarchar](255) NULL,
	[lastName] [nvarchar](255) NULL,
	[jobTitle] [nvarchar](255) NULL,
	[phone] [nvarchar](255) NULL,
	[mobile] [nvarchar](255) NULL,
	[address] [nvarchar](255) NULL,
	[contact_id] [bigint] NULL,
	[company_id] [bigint] NULL,
	[flagChangePassword] [tinyint] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[createdByChannel] [nvarchar](3) NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedByChannel] [nvarchar](3) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_hearings_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_hearings_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_hearing_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
	[user_type] [varchar](5) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_stages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_stages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[litigation] [char](3) NULL,
	[corporate] [char](3) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_stage_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_stage_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_stage_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_client_position_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_client_position_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_client_position_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[legal_case_hearings_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
--legal_case_hearings_full_details;
CREATE VIEW [dbo].[legal_case_hearings_full_details] AS SELECT
    TOP (9223372036854775800)
    legal_case_hearings.id,legal_case_hearings.legal_case_id, ('H'+CAST( legal_case_hearings.id AS nvarchar )) as hearingID, legal_cases.subject as caseSubject, legal_cases.internalReference AS caseReference, legal_case_hearings.task_id,legal_cases.caseValue,legal_cases.statusComments,legal_cases.latest_development,legal_cases.caseArrivalDate,legal_cases.closedOn, legal_cases.description as case_description, legal_cases.arrivalDate as filed_on,
    legal_cases.legal_case_client_position_id AS hearing_client_position_id,ld.legal_case_stage, ld.court_type_id, ld.court_degree_id, ld.court_region_id, ld.court_id,
    legal_case_hearings.startDate, SUBSTRING(CAST(legal_case_hearings.startTime AS nvarchar), 1, 5) AS startTime, legal_case_hearings.postponedDate, SUBSTRING(CAST(legal_case_hearings.postponedTime AS nvarchar), 1, 5) AS postponedTime,
    legal_case_hearings.type,ld.status as stage_status,legal_case_hearings.summary,legal_case_hearings.comments, legal_case_hearings.reasons_of_postponement,
    reference = STUFF((SELECT ' ; ' + lcler.number FROM legal_case_litigation_external_references lcler WHERE lcler.stage=ld.id FOR XML PATH('')), 1, 3, ''),
    reference_date = STUFF((SELECT ' ; ' + CAST(lcler.refDate as nvarchar) FROM legal_case_litigation_external_references lcler WHERE lcler.stage=ld.id FOR XML PATH('')), 1, 3, ''),
    legal_case_hearings.judgment, legal_case_hearings.judged, ('M'+CAST( legal_case_hearings.legal_case_id AS nvarchar )) as caseID,
    stage_opponents = STUFF((SELECT ' ; ' + stage_opponents_view.name FROM opponents_view as stage_opponents_view INNER JOIN legal_case_litigation_stages_opponents stages_opponents ON stages_opponents.stage = ld.id AND stage_opponents_view.id = stages_opponents.opponent_id FOR XML PATH('')), 1, 3, ''),
    opponents = STUFF((SELECT ' ; ' + opponents_view.name FROM opponents_view INNER JOIN legal_case_opponents lcho ON lcho.case_id = legal_cases.id AND opponents_view.id = lcho.opponent_id FOR XML PATH('')), 1, 3, ''),
    clients = STUFF((SELECT ' ; ' + clients_view.name FROM clients_view WHERE clients_view.id = legal_cases.client_id AND clients_view.model = 'clients' FOR XML PATH('')), 1, 3, ''),
    judges = STUFF((SELECT ' ; ' + ( CASE WHEN contJud.father!='' THEN contJud.firstName + ' '+ contJud.father + ' ' + contJud.lastName ELSE contJud.firstName+' '+contJud.lastName END ) FROM contacts AS contJud INNER JOIN legal_case_stage_contacts lchcj ON lchcj.stage = legal_case_hearings.stage AND lchcj.contact_type = 'judge' AND contJud.id = lchcj.contact FOR XML PATH('')), 1, 3, ''),
    opponentLawyers = STUFF((SELECT ' ; ' + ( CASE WHEN contOppLaw.father!='' THEN contOppLaw.firstName + ' '+ contOppLaw.father + ' ' + contOppLaw.lastName ELSE contOppLaw.firstName+' '+contOppLaw.lastName END ) FROM contacts AS contOppLaw INNER JOIN legal_case_stage_contacts lchcol ON lchcol.stage = legal_case_hearings.stage AND lchcol.contact_type = 'opponent-lawyer' AND contOppLaw.id = lchcol.contact FOR XML PATH('')), 1, 3, ''),
    lawyers =
    COALESCE (STUFF((SELECT' ; ' + (userLaw.firstName + ' ' + userLaw.lastName + CASE WHEN userLaw.status = 'Inactive' THEN ' (Inactive)'ELSE '' END) FROM user_profiles AS userLaw INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userLaw.user_id = legal_case_hearings_users.user_id AND legal_case_hearings_users.user_type != 'AP' FOR xml PATH ('')), 1, 3, '') ,'')
    + CASE WHEN (COALESCE (STUFF((SELECT ' ; ' + (userLaw.firstName + ' ' + userLaw.lastName + CASE  WHEN userLaw.status = 'Inactive' THEN ' (Inactive)'  ELSE '' END) FROM user_profiles AS userLaw INNER JOIN legal_case_hearings_users   ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userLaw.user_id = legal_case_hearings_users.user_id  AND legal_case_hearings_users.user_type != 'AP'  FOR xml PATH ('')), 1, 3, '') ,'') = '' or
                 COALESCE(STUFF((SELECT  ' ; ' + (userAdv.firstName + ' ' + userAdv.lastName + CASE WHEN userAdv.status = 'Inactive' THEN ' (Inactive)'ELSE ''  END) FROM advisor_users AS userAdv INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userAdv.id = legal_case_hearings_users.user_id AND legal_case_hearings_users.user_type = 'AP' FOR xml PATH ('')), 1, 3, '') ,'') = '' ) THEN
     '' ELSE ','  END +
    COALESCE(STUFF((SELECT  ' ; ' + (userAdv.firstName + ' ' + userAdv.lastName + CASE WHEN userAdv.status = 'Inactive' THEN ' (Inactive)'ELSE ''  END) FROM advisor_users AS userAdv INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userAdv.id = legal_case_hearings_users.user_id AND legal_case_hearings_users.user_type = 'AP' FOR xml PATH ('')), 1, 3, '') ,'')  ,
    case_assignee = STUFF((SELECT ' ; ' + ( userLegalCase.firstName + ' ' + userLegalCase.lastName+ CASE WHEN userLegalCase.status = 'Inactive' THEN ' (Inactive)' ELSE '' END ) FROM user_profiles AS userLegalCase INNER JOIN legal_cases ON legal_cases.id = legal_case_hearings.legal_case_id AND userLegalCase.user_id = legal_cases.user_id FOR XML PATH('')), 1, 3, ''),
    court_types.name AS courtType, court_degrees.name AS courtDegree, court_regions.name AS courtRegion, courts.name AS court,
    lccplen.name AS clientPosition_en, lccplfr.name AS clientPosition_fr, lccplar.name AS clientPosition_ar, lccplsp.name AS clientPosition_sp, ld.sentenceDate,
    legal_cases.case_type_id as matter_type_id, legal_case_hearings.stage as stage, legal_case_stage_languages_default.name as legal_case_stage_name, legal_case_stage_languages_en.name as legal_case_stage_name_en, legal_case_stage_languages_ar.name as legal_case_stage_name_ar, legal_case_stage_languages_fr.name as legal_case_stage_name_fr, legal_case_stage_languages_es.name as legal_case_stage_name_es, case_types.name as areaOfPractice,legal_cases.case_type_id as area_of_practice,
    containerID = STUFF((SELECT ' ; ' + lccfd.containerId FROM legal_case_containers_full_details as lccfd where lccfd.legal_case_id = legal_case_hearings.legal_case_id FOR XML PATH('')), 1, 3, ''),
    legal_case_hearings.createdOn, legal_case_hearings.createdBy, legal_case_hearings.modifiedBy, legal_case_hearings.modifiedOn,
    (modified.firstName + ' ' + modified.lastName) AS modifiedByName, (created.firstName + ' ' + created.lastName) AS createdByName
    FROM legal_case_hearings
             LEFT JOIN legal_case_litigation_details as ld on ld.legal_case_id = legal_case_hearings.legal_case_id AND  ld.id = legal_case_hearings.stage
             LEFT JOIN legal_case_stages on legal_case_stages.id = ld.legal_case_stage
             LEFT JOIN legal_case_stage_languages legal_case_stage_languages_default on legal_case_stage_languages_default.legal_case_stage_id = legal_case_stages.id and legal_case_stage_languages_default.language_id = 1
             LEFT JOIN legal_case_stage_languages legal_case_stage_languages_en on legal_case_stage_languages_en.legal_case_stage_id = legal_case_stages.id and legal_case_stage_languages_en.language_id = 1
             LEFT JOIN legal_case_stage_languages legal_case_stage_languages_ar on legal_case_stage_languages_ar.legal_case_stage_id = legal_case_stages.id and legal_case_stage_languages_ar.language_id = 2
             LEFT JOIN legal_case_stage_languages legal_case_stage_languages_fr on legal_case_stage_languages_fr.legal_case_stage_id = legal_case_stages.id and legal_case_stage_languages_fr.language_id = 3
             LEFT JOIN legal_case_stage_languages legal_case_stage_languages_es on legal_case_stage_languages_es.legal_case_stage_id = legal_case_stages.id and legal_case_stage_languages_es.language_id = 4
             LEFT JOIN legal_cases ON legal_cases.id = legal_case_hearings.legal_case_id
             LEFT JOIN courts ON courts.id = ld.court_id
             LEFT JOIN court_types ON court_types.id = ld.court_type_id
             LEFT JOIN court_degrees ON court_degrees.id = ld.court_degree_id
             LEFT JOIN court_regions ON court_regions.id = ld.court_region_id
             LEFT JOIN legal_case_client_position_languages lccplen ON lccplen.legal_case_client_position_id = legal_cases.legal_case_client_position_id AND lccplen.language_id = '1'
             LEFT JOIN legal_case_client_position_languages lccplar ON lccplar.legal_case_client_position_id = legal_cases.legal_case_client_position_id AND lccplar.language_id = '2'
             LEFT JOIN legal_case_client_position_languages lccplfr ON lccplfr.legal_case_client_position_id = legal_cases.legal_case_client_position_id AND lccplfr.language_id = '3'
             LEFT JOIN legal_case_client_position_languages lccplsp ON lccplsp.legal_case_client_position_id = legal_cases.legal_case_client_position_id AND lccplsp.language_id = '4'
             LEFT JOIN case_types ON case_types.id = legal_cases.case_type_id
             LEFT JOIN user_profiles created ON created.user_id = legal_case_hearings.createdBy
             LEFT JOIN user_profiles modified ON modified.user_id = legal_case_hearings.modifiedBy
    Where legal_cases.isDeleted = 0 AND legal_case_hearings.is_deleted = 0;
GO
/****** Object:  Table [dbo].[user_changes_authorization]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_changes_authorization](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[changeType] [nvarchar](30) NULL,
	[columnName] [nvarchar](30) NULL,
	[columnValue] [nvarchar](max) NULL,
	[columnStatus] [nvarchar](255) NULL,
	[columnRequestedValue] [nvarchar](max) NULL,
	[columnType] [nvarchar](30) NULL,
	[affectedUserId] [bigint] NULL,
	[makerId] [bigint] NULL,
	[checkerId] [bigint] NULL,
	[createdOn] [smalldatetime] NULL,
	[authorizedOn] [smalldatetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[users_authorizations]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[users_authorizations] AS SELECT max(affectedUserId) AS affectedUserId, max(checkerId) AS checkerId from user_changes_authorization where user_changes_authorization.changeType = 'add' AND user_changes_authorization.columnStatus = 'Approved' group by affectedUserId
GO
/****** Object:  Table [dbo].[users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_group_id] [bigint] NOT NULL,
	[isAd] [tinyint] NOT NULL,
	[username] [nvarchar](255) NOT NULL,
	[password] [nvarchar](255) NULL,
	[email] [nvarchar](255) NOT NULL,
	[type] [nvarchar](15) NOT NULL,
	[banned] [tinyint] NOT NULL,
	[ban_reason] [nvarchar](255) NULL,
	[last_ip] [nvarchar](45) NULL,
	[last_login] [smalldatetime] NULL,
	[created] [smalldatetime] NULL,
	[modified] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[session_id] [nvarchar](50) NULL,
	[userDirectory] [nvarchar](255) NULL,
	[workthrough] [nvarchar](max) NULL,
	[user_guide] [char](3) NULL,
	[otp_code] [nvarchar](10) NULL,
	[otp_expiry] [datetime] NULL,
	[last_otp_verified_at] [datetime] NULL,
	[last_login_device_fingerprint] [nvarchar](255) NULL,
	[department_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [users_unique_email] UNIQUE NONCLUSTERED 
(
	[email] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[provider_groups_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[provider_groups_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[provider_group_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
	[isDefault] [nvarchar](3) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[users_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

            CREATE VIEW [dbo].[users_full_details] AS
            SELECT TOP(9223372036854775800)
                users.id,
                users.isAd,
                users.user_group_id,
                users.username,
                users.email,
                users.type,
                LEFT(users.email, CHARINDEX('@', users.email)-1) AS activeDirectoryId,
                (AutthorizedUser.firstName + ' ' + AutthorizedUser.lastName) AS authorized_by,
                users.banned,
                users.ban_reason,
                users.last_ip,
                CAST(users.last_login AS DATE) AS last_login,
                CAST(users.created AS DATE) AS created,
                users.modifiedBy,
                users.userDirectory,
                (userModified.firstName + ' ' + userModified.lastName) AS userModifiedName,
                CAST(users.modified AS DATE) AS modified,
                user_profiles.flagChangePassword AS flagChangePassword,
                user_profiles.status,
                user_profiles.gender,
                user_profiles.title,
                user_profiles.firstName,
                user_profiles.lastName,
                user_profiles.father,
                user_profiles.mother,
                user_profiles.dateOfBirth,
                user_profiles.jobTitle,
                user_profiles.isLawyer,
                user_profiles.website,
                user_profiles.phone,
                user_profiles.fax,
                user_profiles.mobile,
                user_profiles.address1,
                user_profiles.address2,
                user_profiles.city,
                user_profiles.state,
                user_profiles.zip,
                user_profiles.overridePrivacy,
                user_profiles.employeeId,
                user_profiles.department_id,  -- NEW COLUMN
                user_profiles.department,
                user_profiles.ad_userCode,
                user_profiles.user_code,
                seniorityLevels.name AS seniorityLevel,
                seniorityLevels.id AS seniorityLevelId,
                user_profiles.country AS country_id,
                user_profiles.nationality AS nationality_id,
                user_groups.name AS userGroupName,
                user_groups.description AS userGroupDescription,
                providerGroup = STUFF(
                    (SELECT ', ' + provider_groups.name
                    FROM provider_groups_users
                    INNER JOIN provider_groups ON provider_groups.id = provider_groups_users.provider_group_id
                    WHERE provider_groups_users.user_id = users.id
                    FOR XML PATH('')), 1, 2, ''),
                provider_group_id = STUFF(
                    (SELECT ', ' + CAST(provider_groups.id AS NVARCHAR)
                    FROM provider_groups_users
                    INNER JOIN provider_groups ON provider_groups.id = provider_groups_users.provider_group_id
                    WHERE provider_groups_users.user_id = users.id
                    FOR XML PATH('')), 1, 2, ''),
                user_profiles.flagNeedApproval AS flagNeedApproval
            FROM users
            INNER JOIN user_profiles ON user_profiles.user_id = users.id
            LEFT JOIN seniority_levels seniorityLevels ON seniorityLevels.id = user_profiles.seniority_level_id
            LEFT JOIN user_groups ON user_groups.id = users.user_group_id
            LEFT JOIN user_profiles userModified ON userModified.user_id = users.modifiedBy
            LEFT JOIN users_authorizations ON users_authorizations.affectedUserId = users.id
            LEFT JOIN user_profiles AutthorizedUser ON AutthorizedUser.user_id = users_authorizations.checkerId
        
GO
/****** Object:  Table [dbo].[login_history_logs]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[login_history_logs](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NULL,
	[userLogin] [nvarchar](255) NULL,
	[action] [nvarchar](6) NOT NULL,
	[source_ip] [nvarchar](45) NOT NULL,
	[log_message] [nvarchar](255) NOT NULL,
	[log_message_status] [nvarchar](255) NOT NULL,
	[logDate] [datetime] NOT NULL,
	[user_agent] [nvarchar](120) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[login_logs_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[login_logs_full_details] AS
	SELECT TOP(9223372036854775800)
		login_history_logs.id AS id,
		login_history_logs.user_id AS user_id,
		login_history_logs.userLogin AS userLogin,
		users_full_details.userGroupName AS userGroupName,
		users_full_details.user_group_id AS user_group_id,
		login_history_logs.action AS action,
		login_history_logs.source_ip AS source_ip,
		login_history_logs.log_message AS log_message,
		login_history_logs.log_message_status AS log_message_status,
		CAST(login_history_logs.logDate AS DATE) AS logDate,
		CONVERT(varchar(20), login_history_logs.logDate, 120) AS fullLogDate,
		CASE WHEN login_history_logs.log_message_status = 'log_msg_status_2' THEN 'failed' ELSE 'successful' END AS status,
		login_history_logs.user_agent
	FROM login_history_logs
	LEFT JOIN users_full_details ON users_full_details.id = login_history_logs.user_id;
GO
/****** Object:  View [dbo].[maker_checker_user_changes]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[maker_checker_user_changes] AS SELECT TOP(9223372036854775800) UCA.id, UCA.changeType, UCA.columnName, CASE WHEN UCA.columnName = 'banned' THEN (CASE WHEN UCA.columnValue = '0' THEN 'no' ELSE 'yes' END) ELSE UCA.columnValue END AS columnValue, UCA.columnStatus, CASE WHEN UCA.columnName = 'banned' THEN (CASE WHEN UCA.columnRequestedValue = '0' THEN 'no' ELSE 'yes' END) ELSE UCA.columnRequestedValue END AS columnRequestedValue, UCA.columnType, CAST(UCA.createdOn AS DATE) AS createdOn, CAST(UCA.authorizedOn AS DATE) AS authorizedOn, UCA.affectedUserId, ( affectedUser.firstName + ' ' + affectedUser.lastName ) AS affectedUserProfile, UCA.makerId, ( makerUser.firstName + ' ' + makerUser.lastName ) AS makerUserProfile, UCA.checkerId, ( checkerUser.firstName + ' ' + checkerUser.lastName ) AS checkerUserProfile FROM user_changes_authorization as UCA LEFT JOIN user_profiles affectedUser ON affectedUser.user_id = UCA.affectedUserId LEFT JOIN user_profiles makerUser ON makerUser.user_id = UCA.makerId LEFT JOIN user_profiles checkerUser ON checkerUser.user_id = UCA.checkerId
GO
/****** Object:  Table [dbo].[user_groups_changes_authorization]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_groups_changes_authorization](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[changeType] [nvarchar](30) NULL,
	[columnName] [nvarchar](30) NULL,
	[columnValue] [nvarchar](max) NULL,
	[columnStatus] [nvarchar](255) NULL,
	[columnRequestedValue] [nvarchar](max) NULL,
	[columnType] [nvarchar](30) NULL,
	[affectedUserGroupId] [bigint] NULL,
	[makerId] [bigint] NULL,
	[checkerId] [bigint] NULL,
	[createdOn] [smalldatetime] NULL,
	[authorizedOn] [smalldatetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[maker_checker_user_groups_changes]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[maker_checker_user_groups_changes] AS SELECT TOP(9223372036854775800) UGCA.id, UGCA.changeType, UGCA.columnName, UGCA.columnValue, UGCA.columnStatus, columnRequestedValue, UGCA.columnType, CAST(UGCA.createdOn AS DATE) AS createdOn, CAST(UGCA.authorizedOn AS DATE) AS authorizedOn, UGCA.affectedUserGroupId, affectedUserGroup.name AS affectedUserGroupName, UGCA.makerId, ( makerUser.firstName + ' ' + makerUser.lastName ) AS makerUserProfile, UGCA.checkerId, ( checkerUser.firstName + ' ' + checkerUser.lastName ) AS checkerUserProfile FROM user_groups_changes_authorization as UGCA LEFT JOIN user_groups affectedUserGroup ON affectedUserGroup.id = UGCA.affectedUserGroupId LEFT JOIN user_profiles makerUser ON makerUser.user_id = UGCA.makerId LEFT JOIN user_profiles checkerUser ON checkerUser.user_id = UGCA.checkerId
GO
/****** Object:  Table [dbo].[user_group_permissions_changes_authorization]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_group_permissions_changes_authorization](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[columnName] [nvarchar](30) NULL,
	[module] [nvarchar](30) NULL,
	[columnValue] [nvarchar](max) NULL,
	[columnStatus] [nvarchar](255) NULL,
	[columnRequestedValue] [nvarchar](max) NULL,
	[columnApprovedValue] [nvarchar](max) NULL,
	[affectedUserGroupId] [bigint] NULL,
	[makerId] [bigint] NULL,
	[checkerId] [bigint] NULL,
	[createdOn] [smalldatetime] NULL,
	[authorizedOn] [smalldatetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[maker_checker_user_group_permissions_changes]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[maker_checker_user_group_permissions_changes] AS SELECT TOP(9223372036854775800) UGPCA.id, UGPCA.columnName, UGPCA.module, UGPCA.columnValue, UGPCA.columnStatus, UGPCA.columnRequestedValue, UGPCA.columnApprovedValue, UGPCA.affectedUserGroupId, affectedUserGroup.name AS affectedUserGroupName, UGPCA.makerId, UGPCA.checkerId, CAST(UGPCA.createdOn AS DATE) AS createdOn, CAST(UGPCA.authorizedOn AS DATE) AS authorizedOn, ( makerUser.firstName + ' ' + makerUser.lastName ) AS makerUserProfile, ( checkerUser.firstName + ' ' + checkerUser.lastName ) AS checkerUserProfile FROM user_group_permissions_changes_authorization as UGPCA LEFT JOIN user_groups affectedUserGroup ON affectedUserGroup.id = UGPCA.affectedUserGroupId LEFT JOIN user_profiles makerUser ON makerUser.user_id = UGPCA.makerId LEFT JOIN user_profiles checkerUser ON checkerUser.user_id = UGPCA.checkerId
GO
/****** Object:  View [dbo].[user_groups_authorization]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[user_groups_authorization] AS SELECT TOP(9223372036854775800)  max(affectedUserGroupId) AS affectedUserGroupId, max(checkerId)  AS checkerId FROM user_groups_changes_authorization WHERE user_groups_changes_authorization.changeType = 'add' AND user_groups_changes_authorization.columnStatus = 'Approved' GROUP BY affectedUserGroupId
GO
/****** Object:  View [dbo].[user_groups_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[user_groups_full_details] AS SELECT TOP(9223372036854775800) user_groups.id,user_groups.system_group,user_groups.createdBy as createdById,user_groups.modifiedBy as modifiedById, user_groups.name, user_groups.description , user_groups.flagNeedApproval, user_groups.needApprovalOnAdd,  CAST(user_groups.createdOn AS DATE) AS createdOn, (created.firstName + ' ' + created.lastName) AS createdBy,  CAST(user_groups.modifiedOn AS DATE) modifiedOn, (modified.firstName + ' ' + modified.lastName) AS modifiedBy, ( userGroupAuthorized.firstName + ' ' + userGroupAuthorized.lastName ) AS AuthorizedByFullName FROM user_groups LEFT JOIN user_groups_authorization ON user_groups_authorization.affectedUserGroupId = user_groups.id LEFT JOIN user_profiles userGroupAuthorized ON userGroupAuthorized.user_id = user_groups_authorization.checkerId LEFT JOIN user_profiles created ON created.user_id = user_groups.createdBy LEFT JOIN user_profiles modified ON modified.user_id = user_groups.modifiedBy

GO
/****** Object:  Table [dbo].[company_type_of_discharges]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_type_of_discharges](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_discharge_social_securities]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_discharge_social_securities](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NOT NULL,
	[remind_id] [bigint] NULL,
	[type_id] [bigint] NOT NULL,
	[reminder_id] [bigint] NULL,
	[releasedOn] [date] NOT NULL,
	[expiresOn] [date] NULL,
	[reference] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[license_and_waiver_reminds]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[license_and_waiver_reminds](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[license_and_waiver_id] [bigint] NOT NULL,
	[user_id] [bigint] NULL,
	[user_group_id] [bigint] NULL,
	[reminder_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[comapnies_ss_expiry_dates]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
--comapnies_ss_expiry_dates;
CREATE VIEW [dbo].[comapnies_ss_expiry_dates] AS SELECT
	TOP(9223372036854775800)
    company_discharge_social_securities.id, STUFF((SELECT ', ' + user_profiles.firstName + ' ' + user_profiles.lastName + CASE WHEN user_profiles.status = 'Active' THEN '' ELSE ' (Inactive)' END 
    FROM user_profiles INNER JOIN license_and_waiver_reminds ON user_profiles.user_id = license_and_waiver_reminds.user_id AND license_and_waiver_reminds.license_and_waiver_id = company_discharge_social_securities.id FOR XML PATH('')), 1, 1, '') AS remindNames,
	STUFF((SELECT ', ' + CAST(user_profiles.user_id AS nvarchar) FROM user_profiles INNER JOIN license_and_waiver_reminds ON user_profiles.user_id = license_and_waiver_reminds.user_id AND license_and_waiver_reminds.license_and_waiver_id = company_discharge_social_securities.id FOR XML PATH('')), 1, 1, '') AS remindIds,
    discharges.name AS typeOfDischarge, company_discharge_social_securities.releasedOn, company_discharge_social_securities.expiresOn, company_discharge_social_securities.reference, 
    companies.name AS companyName,companies.id AS companyId,company_legal_types.name AS companyLegalType, companies.nationality_id,
    STUFF((SELECT DISTINCT ', ' + user_groups.name FROM user_groups INNER JOIN license_and_waiver_reminds ON user_groups.id = license_and_waiver_reminds.user_group_id AND license_and_waiver_reminds.license_and_waiver_id = company_discharge_social_securities.id FOR XML PATH('')), 1, 1, '') as remindGroups
	FROM license_and_waiver_reminds
	LEFT JOIN  company_discharge_social_securities ON company_discharge_social_securities.id = license_and_waiver_reminds.license_and_waiver_id
	LEFT JOIN company_type_of_discharges discharges ON discharges.id = company_discharge_social_securities.type_id
	LEFT JOIN companies ON companies.id = company_discharge_social_securities.company_id
	LEFT JOIN company_legal_types ON company_legal_types.id = companies.company_legal_type_id
	GROUP BY company_discharge_social_securities.id, discharges.name, company_discharge_social_securities.releasedOn, company_discharge_social_securities.expiresOn, company_discharge_social_securities.reference, companies.name, companies.id, companies.nationality_id, company_legal_types.name
	ORDER BY company_discharge_social_securities.expiresOn ASC;
GO
/****** Object:  Table [dbo].[case_comments]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_comments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[comment] [text] NOT NULL,
	[createdOn] [datetime2](0) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[modifiedBy] [bigint] NOT NULL,
	[createdByChannel] [nvarchar](3) NULL,
	[modifiedByChannel] [nvarchar](3) NULL,
	[isVisibleToCP] [char](1) NULL,
	[isVisibleToAP] [char](1) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[customer_portal_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[customer_portal_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contact_id] [bigint] NULL,
	[type] [nvarchar](15) NOT NULL,
	[isAd] [char](1) NULL,
	[isA4Luser] [char](1) NULL,
	[username] [nvarchar](255) NOT NULL,
	[email] [nvarchar](255) NOT NULL,
	[password] [nvarchar](255) NULL,
	[status] [nvarchar](45) NOT NULL,
	[firstName] [nvarchar](255) NULL,
	[lastName] [nvarchar](255) NULL,
	[employeeId] [nvarchar](255) NULL,
	[userCode] [nvarchar](255) NULL,
	[department] [nvarchar](255) NULL,
	[jobTitle] [nvarchar](255) NULL,
	[phone] [nvarchar](255) NULL,
	[mobile] [nvarchar](255) NULL,
	[address] [nvarchar](255) NULL,
	[banned] [char](1) NULL,
	[ban_reason] [nvarchar](255) NULL,
	[last_ip] [nvarchar](45) NULL,
	[last_login] [smalldatetime] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[userDirectory] [nvarchar](255) NULL,
	[approved] [char](1) NULL,
	[flag_change_password] [tinyint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[legal_case_notes_history]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[legal_case_notes_history] AS
SELECT case_comments.id, case_comments.case_id  AS caseId, case_comments.comment, case_comments.createdOn,
(CASE WHEN case_comments.createdByChannel='CP' THEN (customer_portal_users.firstName + ' ' + customer_portal_users.lastName) ELSE (user_profiles.firstName+ ' ' + user_profiles.lastName ) END) as createdBy, case_comments.user_id AS createdById, case_comments.createdByChannel,
(CASE WHEN case_comments.modifiedByChannel='CP' THEN (cpmodified.firstName + ' ' + cpmodified.lastName) ELSE ( modified.firstName + ' ' + modified.lastName ) END) AS modifiedBy, case_comments.modifiedBy as modifiedById, case_comments.modifiedByChannel
FROM case_comments
LEFT JOIN user_profiles ON user_profiles.user_id = case_comments.user_id
LEFT JOIN user_profiles modified ON modified.user_id = case_comments.modifiedBy
LEFT JOIN customer_portal_users ON customer_portal_users.id = case_comments.user_id AND case_comments.createdByChannel='CP'
LEFT JOIN customer_portal_users cpmodified ON cpmodified.id = case_comments.modifiedBy AND case_comments.modifiedByChannel='CP';
GO
/****** Object:  Table [dbo].[countries]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[countries](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[countryCode] [char](2) NOT NULL,
	[currencyCode] [char](3) NULL,
	[currencyName] [nvarchar](255) NULL,
	[isoNumeric] [char](4) NULL,
	[languages] [nvarchar](30) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[accounts_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[accounts_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](128) NOT NULL,
	[type] [nvarchar](11) NOT NULL,
	[is_visible] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[account_number_prefix_per_entity]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[account_number_prefix_per_entity](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[organization_id] [bigint] NOT NULL,
	[account_type_id] [bigint] NOT NULL,
	[account_number_prefix] [nvarchar](10) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[accounts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[accounts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[organization_id] [bigint] NOT NULL,
	[currency_id] [bigint] NOT NULL,
	[account_type_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[systemAccount] [nvarchar](3) NOT NULL,
	[has_open_balance] [char](1) NULL,
	[description] [text] NULL,
	[model_id] [bigint] NULL,
	[member_id] [bigint] NULL,
	[model_name] [nvarchar](255) NULL,
	[model_type] [nvarchar](8) NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[accountData] [text] NULL,
	[number] [bigint] NOT NULL,
	[show_in_dashboard] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[accounts_details_lookup]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[accounts_details_lookup] AS
SELECT accounts.*,CASE WHEN prefixes.account_number_prefix IS NOT NULL THEN (prefixes.account_number_prefix + CAST(accounts.number as nvarchar)) ELSE CAST(accounts.number as nvarchar) END as account_number,
    accounts_types.name as accountType,accounts_types.type as accountCategory,countries.currencyCode,countries.currencyName,
    CASE WHEN accounts.model_name = 'Person' THEN ( CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END )
    WHEN accounts.model_name = 'Company' THEN companies.name END AS fullName,CASE WHEN accounts.model_name = 'Person' THEN contacts.address1 WHEN accounts.model_name = 'Company' THEN company_addresses.address END AS address1,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.address2 WHEN accounts.model_name = 'Company' THEN '' END AS address2,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.zip WHEN accounts.model_name = 'Company' THEN company_addresses.zip END AS zip,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.city WHEN accounts.model_name = 'Company' THEN company_addresses.city END AS city,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.country_id WHEN accounts.model_name = 'Company' THEN company_addresses.country END AS country_id,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.state WHEN accounts.model_name = 'Company' THEN company_addresses.state END AS state,
    CASE WHEN accounts.model_type = 'partner' THEN partners.isThirdParty ELSE '' END AS isThirdParty,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.tax_number WHEN accounts.model_name = 'Company' THEN companies.registrationTaxNb END AS tax_number,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.additional_id_type WHEN accounts.model_name = 'Company' THEN companies.additional_id_type END AS additional_id_type,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.additional_id_value WHEN accounts.model_name = 'Company' THEN companies.additional_id_value END AS additional_id_value,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.street_name WHEN accounts.model_name = 'Company' THEN company_addresses.street_name END AS street_name,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.additional_street_name WHEN accounts.model_name = 'Company' THEN company_addresses.additional_street_name END AS additional_street_name,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.building_number WHEN accounts.model_name = 'Company' THEN company_addresses.building_number END AS building_number,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.address_additional_number WHEN accounts.model_name = 'Company' THEN company_addresses.address_additional_number END AS address_additional_number,
    CASE WHEN accounts.model_name = 'Person' THEN contacts.district_neighborhood WHEN accounts.model_name = 'Company' THEN company_addresses.district_neighborhood END AS district_neighborhood
FROM accounts
    left join companies on companies.id = accounts.member_id
    left join company_addresses on company_addresses.id = (SELECT TOP 1 c1.id FROM company_addresses c1 WHERE companies.id = c1.company ORDER BY c1.id ASC)
    left join contacts on contacts.id = accounts.member_id join countries on countries.id = accounts.currency_id join accounts_types on accounts_types.id = accounts.account_type_id
    left join partners on partners.id = accounts.model_id and accounts.model_type = 'partner'
    left join account_number_prefix_per_entity  as prefixes on accounts_types.id = prefixes.account_type_id AND accounts.organization_id = prefixes.organization_id;
GO
/****** Object:  Table [dbo].[voucher_related_cases]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[voucher_related_cases](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_id] [bigint] NOT NULL,
	[voucher_header_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[bill_headers]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[bill_headers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[voucher_header_id] [bigint] NOT NULL,
	[account_id] [bigint] NOT NULL,
	[status] [nvarchar](14) NOT NULL,
	[dueDate] [smalldatetime] NOT NULL,
	[total] [decimal](22, 2) NOT NULL,
	[displayTax] [tinyint] NULL,
	[client_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[bill_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[bill_details](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[bill_header_id] [bigint] NOT NULL,
	[account_id] [bigint] NOT NULL,
	[description] [text] NOT NULL,
	[quantity] [decimal](22, 2) NOT NULL,
	[price] [decimal](22, 2) NOT NULL,
	[basePrice] [decimal](22, 2) NOT NULL,
	[tax_id] [bigint] NULL,
	[percentage] [decimal](10, 2) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[bill_payment_bills]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[bill_payment_bills](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[bill_payment_id] [bigint] NOT NULL,
	[bill_header_id] [bigint] NOT NULL,
	[amount] [decimal](22, 2) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[voucher_headers]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[voucher_headers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[organization_id] [bigint] NOT NULL,
	[dated] [date] NOT NULL,
	[voucherType] [nvarchar](6) NOT NULL,
	[refNum] [bigint] NOT NULL,
	[referenceNum] [nvarchar](255) NULL,
	[attachment] [nvarchar](255) NULL,
	[description] [text] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[bills_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[bills_full_details] AS
SELECT voucher_headers.id, voucher_headers.organization_id, 
voucher_headers.dated, voucher_headers.voucherType, voucher_headers.referenceNum,
( 'M' + (SELECT CAST(legal_cases.id AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id ) ) as caseID,
voucher_headers.attachment, voucher_headers.description,voucher_headers.refNum,
( adl.name + ' - ' + adl.currencyCode ) as supplierAccount, adl.name AS accountName, adl.fullName AS supplierName,
voucher_headers.createdOn,voucher_headers.createdBy,voucher_headers.modifiedOn,voucher_headers.modifiedBy,
case_id = STUFF((
    SELECT ','+ CAST(voucher_related_cases.legal_case_id AS nvarchar) 
    FROM voucher_related_cases 
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id  
    FOR XML PATH('')), 1, 1, ''),
( created.firstName + ' ' + created.lastName ) AS createdByName,
( modified.firstName + ' ' + modified.lastName ) AS modifiedByName,adl.account_number,
(SELECT ( Sum(( price * quantity )) ) FROM bill_details WHERE bill_header_id = bill_headers.id) AS subtotal,
(SELECT ( Sum(( ( ( price * quantity ) * Isnull(percentage, 0) ) / 100 ))) FROM bill_details WHERE bill_header_id = bill_headers.id) AS totaltax,
adl.tax_number AS tax_number,
B.payemntsMade as payemntsMade,
CASE WHEN ( bill_headers.status <> 'paid' AND bill_headers.dueDate < GETDATE()) THEN 'overdue' ELSE bill_headers.status END AS billStatus,
bill_headers.id as billID, bill_headers.dueDate as dueDate, bill_headers.account_id as accountID, bill_headers.total as total,
bill_headers.total-B.balanceDueNet AS balanceDue,
caseSubject = STUFF((
    SELECT ':/;'+ CAST(legal_cases.subject AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id 
    FOR XML PATH('')), 1, 3, ''),
caseCategory = STUFF((
    SELECT ','+ CAST(legal_cases.category AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id 
    FOR XML PATH('')), 1, 1, ''),
 cli.name as clientName,bill_headers.client_id as clientID,
displayTax
FROM voucher_headers
INNER JOIN (
select bill_headers.voucher_header_id, bill_headers.id,  SUM(bpb.amount) as payemntsMade, ISNULL(SUM(bpb.amount), 0.0) as balanceDueNet
FROM bill_headers
LEFT JOIN bill_payment_bills bpb ON bpb.bill_header_id = bill_headers.id
GROUP BY bill_headers.id, bill_headers.voucher_header_id
) B ON B.voucher_header_id = voucher_headers.id
INNER JOIN bill_headers ON bill_headers.id = B.id
INNER JOIN accounts_details_lookup adl ON adl.id = bill_headers.account_id

LEFT JOIN clients_view cli ON cli.id = bill_headers.client_id and cli.model = 'clients'
LEFT JOIN user_profiles created ON created.user_id = voucher_headers.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = voucher_headers.modifiedBy
LEFT JOIN accounts acc ON acc.id = bill_headers.account_id AND acc.model_type = 'supplier'
GO
/****** Object:  Table [dbo].[voucher_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[voucher_details](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[voucher_header_id] [bigint] NOT NULL,
	[account_id] [bigint] NOT NULL,
	[drCr] [char](1) NOT NULL,
	[local_amount] [decimal](22, 2) NOT NULL,
	[foreign_amount] [decimal](22, 2) NOT NULL,
	[description] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[journals_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[journals_full_details] AS
SELECT voucher_headers.id, voucher_headers.organization_id, 
case_id = STUFF((
    SELECT ','+ CAST(voucher_related_cases.legal_case_id AS nvarchar) 
    FROM voucher_related_cases 
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id  
    FOR XML PATH('')), 1, 1, ''),
voucher_headers.dated,
voucher_headers.voucherType, voucher_headers.referenceNum, voucher_headers.attachment, voucher_headers.description,
voucher_headers.refNum, VD.amount as amount, CAST(voucher_headers.createdOn as DATE) as createdOn ,voucher_headers.createdBy,CAST(voucher_headers.modifiedOn as DATE) as modifiedOn,
voucher_headers.modifiedBy,( created.firstName + ' ' + created.lastName ) AS createdByName,( modified.firstName + ' ' + modified.lastName ) AS modifiedByName
FROM voucher_headers
INNER JOIN (
SELECT voucher_details.id, voucher_details.voucher_header_id, SUM(voucher_details.local_amount) AS amount
FROM voucher_details
GROUP BY voucher_details.id, voucher_details.voucher_header_id
) VD ON VD.voucher_header_id = voucher_headers.id and voucher_headers.voucherType = 'JV'
INNER JOIN voucher_details ON voucher_details.id = VD.id and voucher_details.drCr = 'C'
LEFT JOIN user_profiles created ON created.user_id = voucher_headers.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = voucher_headers.modifiedBy;
GO
/****** Object:  Table [dbo].[expenses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[expenses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[voucher_header_id] [bigint] NOT NULL,
	[expense_category_id] [bigint] NOT NULL,
	[expense_account] [bigint] NOT NULL,
	[paid_through] [bigint] NOT NULL,
	[vendor_id] [bigint] NULL,
	[client_id] [bigint] NULL,
	[client_account_id] [bigint] NULL,
	[billingStatus] [nvarchar](12) NOT NULL,
	[tax_id] [bigint] NULL,
	[status] [nvarchar](20) NOT NULL,
	[amount] [decimal](22, 2) NOT NULL,
	[paymentMethod] [varchar](32) NOT NULL,
	[task] [bigint] NULL,
	[hearing] [bigint] NULL,
	[event] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[taxes]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[taxes](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[code] [nvarchar](255) NOT NULL,
	[account_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[fl1name] [nvarchar](255) NULL,
	[fl2name] [nvarchar](255) NULL,
	[description] [text] NULL,
	[percentage] [decimal](10, 2) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[expense_categories]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[expense_categories](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[expense_category_id] [bigint] NULL,
	[account_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[fl1name] [nvarchar](255) NULL,
	[fl2name] [nvarchar](255) NULL,
	[amount] [decimal](10, 2) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[expenses_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE VIEW [dbo].[expenses_full_details] AS
SELECT voucher_headers.id, voucher_headers.organization_id,
voucher_headers.dated, voucher_headers.voucherType, voucher_headers.refNum,
voucher_headers.referenceNum, voucher_headers.attachment, voucher_headers.description,
case_id = STUFF((
    SELECT ','+ CAST(voucher_related_cases.legal_case_id AS nvarchar) 
    FROM voucher_related_cases 
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id  
    FOR XML PATH('')), 1, 1, ''),
( 'M' + (SELECT CAST(legal_cases.id AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id ) ) as caseID,
caseSubject = STUFF((
    SELECT ':/;'+ CAST(legal_cases.subject AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id 
    FOR XML PATH('')), 1, 3, ''),
caseCategory = STUFF((
    SELECT ','+ CAST(legal_cases.category AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id 
    FOR XML PATH('')), 1, 1, ''),
expenses.id as expenseID,expenses.paymentMethod as paymentMethod, expenses.amount as amount, expenses.billingStatus as billingStatus,
expenses.client_id as clientID, expenses.client_account_id as clientAccountID,
adl1.id as paidThroughID, adl1.name as paidThroughAccount, adl1.currency_id as currency_id, adl1.currencyCode as currency,adl1.account_number as paid_through_account_number,
ven.name as supplier, cli.name as clientName, adl2.name as clientAccount,adl2.currency_id as clientAccountCurrencyId,adl2.currencyCode as clientAccountCurrency,adl2.account_number as client_account_number,
expense_categories.id as expenseCategoryId,( 'T' +  CAST( expenses.task AS nvarchar ) ) as task, expenses.task as task_id , expenses.hearing, expenses.event, expenses.status as status,
CASE WHEN expense_categories.expense_category_id is null THEN expense_categories.name ELSE ( ec.name + ' / ' + expense_categories.name ) END as expenseCategory,
CASE WHEN expense_categories.expense_category_id is null THEN expense_categories.fl1name ELSE ( ec.fl1name + ' / ' + expense_categories.fl1name ) END as expenseCategoryfl1,
CASE WHEN expense_categories.expense_category_id is null THEN expense_categories.fl2name ELSE ( ec.fl2name + ' / ' + expense_categories.fl2name ) END as expenseCategoryfl2,
voucher_headers.createdOn,voucher_headers.createdBy,voucher_headers.modifiedOn,voucher_headers.modifiedBy,
CASE WHEN created.status='inactive' THEN ( created.firstName + ' ' + created.lastName + ' (Inactive) ') ELSE ( created.firstName + ' ' + created.lastName ) END AS createdByName,
(CASE WHEN modified.status='inactive' THEN( modified.firstName + ' ' + modified.lastName + ' (Inactive) ') ELSE ( modified.firstName + ' ' + modified.lastName ) END) AS modifiedByName,
(SELECT Sum(exp.amount - (exp.amount / ((Isnull(taxes.percentage, 0) / 100) + 1))) FROM taxes LEFT JOIN expenses exp ON taxes.id = exp.tax_id AND exp.voucher_header_id = voucher_headers.id) AS totaltax,
(SELECT Sum(exp.amount - (exp.amount - (exp.amount / ((Isnull(taxes.percentage, 0) / 100) + 1)))) FROM taxes LEFT JOIN expenses exp ON taxes.id = exp.tax_id AND exp.voucher_header_id = voucher_headers.id) AS subtotal,
ven.tax_number AS tax_number
FROM voucher_headers
INNER JOIN expenses ON expenses.voucher_header_id = voucher_headers.id
INNER JOIN expense_categories ON expense_categories.id = expenses.expense_category_id
INNER JOIN accounts_details_lookup adl1 ON adl1.id = expenses.paid_through
LEFT JOIN accounts_details_lookup adl2 ON adl2.id = expenses.client_account_id
LEFT JOIN clients_view ven ON ven.id = expenses.vendor_id and ven.model = 'suppliers'
LEFT JOIN clients_view cli ON cli.id = expenses.client_id and cli.model = 'clients'
LEFT JOIN expense_categories ec ON ec.id = expense_categories.expense_category_id
LEFT JOIN user_profiles created ON created.user_id = voucher_headers.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = voucher_headers.modifiedBy;
GO
/****** Object:  View [dbo].[chart_of_accounts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[chart_of_accounts] AS
SELECT
accounts_details_lookup.*,
( accounts_details_lookup.accountCategory + ' / ' + accounts_details_lookup.accountType ) as accountCategoryType,
CASE
WHEN VD.localAmountVD IS NOT NULL
THEN VD.localAmountVD
ELSE 0 END as localAmount,
CASE
WHEN VD.foreignAmountVD IS NOT NULL
THEN VD.foreignAmountVD
ELSE 0 END as foreignAmount,
CASE
WHEN VD.totalDebitVD IS NOT NULL
THEN VD.totalDebitVD
ELSE 0 END as totalDebit,
CASE
WHEN VD.totalCreditVD IS NOT NULL
THEN VD.totalCreditVD
ELSE 0 END as totalCredit
FROM accounts_details_lookup
LEFT JOIN (
SELECT voucher_details.account_id,
SUM( CASE
WHEN voucher_details.drCr = 'D'
THEN (CASE
WHEN voucher_details.foreign_amount IS NOT NULL
THEN voucher_details.foreign_amount ELSE 0 END)
ELSE (CASE
WHEN voucher_details.foreign_amount IS NOT NULL
THEN (voucher_details.foreign_amount*-1) ELSE 0 END)
END ) AS foreignAmountVD,
SUM( CASE
WHEN voucher_details.drCr = 'D'
THEN (CASE
WHEN voucher_details.local_amount IS NOT NULL
THEN voucher_details.local_amount ELSE 0 END)
ELSE (CASE
WHEN voucher_details.local_amount IS NOT NULL
THEN (voucher_details.local_amount*-1) ELSE 0 END)
END ) AS localAmountVD,
SUM(
CASE WHEN voucher_details.drCr = 'D'
THEN (CASE WHEN voucher_details.foreign_amount IS NOT NULL THEN voucher_details.foreign_amount ELSE 0 END)
ELSE 0
END ) AS totalDebitVD,
SUM(
CASE WHEN voucher_details.drCr = 'C'
THEN (CASE WHEN voucher_details.foreign_amount IS NOT NULL THEN (voucher_details.foreign_amount*-1) ELSE 0 END)
ELSE 0
END ) AS totalCreditVD
FROM voucher_details
inner JOIN voucher_headers ON voucher_headers.id = voucher_details.voucher_header_id
GROUP BY voucher_details.account_id ) VD ON VD.account_id = accounts_details_lookup.id
GO
/****** Object:  Table [dbo].[time_internal_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[time_internal_statuses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_activity_log_invoicing_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_activity_log_invoicing_statuses](
	[id] [bigint] NOT NULL,
	[log_invoicing_statuses] [nvarchar](12) NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[time_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[time_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[default_comment] [nvarchar](max) NULL,
	[default_time_effort] [varchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[user_activity_logs_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[user_activity_logs_full_details] AS
SELECT ual.id, ual.user_id, ual.task_id, ual.legal_case_id, ual.effectiveEffort, ual.createdBy, CAST(ual.createdOn as DATE) as createdOn, CAST(ual.modifiedOn as DATE) as modifiedOn, ual.rate as rate, ual.rate_system as rate_system,
       CASE WHEN ual.task_id IS NULL THEN '' ELSE ( 'T' + CAST( ual.task_id AS nvarchar ) ) END as taskId,
       tasks.title as task_title,
       CASE WHEN Datalength(tasks.description) > 63 THEN (SUBSTRING(tasks.description, 1, 63) + ' ' + '...' ) ELSE tasks.description END as taskSummary,
       tasks.description as task_full_summary,
       CASE WHEN ual.legal_case_id IS NULL THEN '' ELSE ( 'M' + CAST( ual.legal_case_id AS nvarchar ) ) END as legalCaseId,
       CASE WHEN Datalength(legal_cases.subject) > 63 THEN ( SUBSTRING(legal_cases.subject, 1, 63) + ' ' + '...' ) ELSE legal_cases.subject END as legalCaseSummary,
       CASE WHEN Datalength(legal_cases.description) > 63 THEN ( SUBSTRING(legal_cases.description, 1, 63) + ' ' + '...' ) ELSE legal_cases.description END as legalCaseDescription,
       legal_cases.category as caseCategory, legal_cases.internalReference as matterInternalReference, legal_cases.provider_group_id as provider_group_id, 
       CASE WHEN worker.status = 'inactive' THEN ( worker.firstName + ' ' + worker.lastName + '(Inactive)' ) ELSE ( worker.firstName + ' ' + worker.lastName ) END as worker,
       CASE WHEN inserter.status = 'inactive' THEN ( inserter.firstName + ' ' + inserter.lastName + '(Inactive)' ) ELSE ( inserter.firstName + ' ' + inserter.lastName ) END as inserter,
       seniorityLevels.name  as seniorityLevel,seniorityLevels.id as seniorityLevelId, ual.comments as comments, ual.logDate as logDate, time_types.id timeTypeId, time_internal_statuses.id timeInternalStatusId, ual.timeStatus,
       CASE WHEN modified.status = 'inactive' THEN ( modified.firstName + ' ' + modified.lastName + '(Inactive)' ) ELSE ( modified.firstName + ' ' + modified.lastName ) END as modifiedByName,
       CASE WHEN ualis.log_invoicing_statuses IS NULL AND ual.timeStatus='internal' THEN '-' WHEN ualis.log_invoicing_statuses IS NULL AND ual.timeStatus <> 'internal' THEN 'to-invoice' ELSE ualis.log_invoicing_statuses END as billingStatus,
       cv.id as clientId, cv.name as clientName, cv2.name as allRecordsClientName, contacts.firstName + ' ' + contacts.lastName as requestedBy
FROM user_activity_logs as ual
         LEFT JOIN tasks ON tasks.id = ual.task_id
         LEFT JOIN legal_cases ON legal_cases.id = ual.legal_case_id
         LEFT JOIN user_profiles worker ON worker.user_id = ual.user_id
         LEFT JOIN seniority_levels seniorityLevels ON seniorityLevels.id = worker.seniority_level_id
         LEFT JOIN user_profiles inserter ON inserter.user_id = ual.createdBy
         LEFT JOIN user_profiles modified ON modified.user_id = ual.modifiedBy
         LEFT JOIN time_types ON time_types.id = ual.time_type_id
         LEFT JOIN time_internal_statuses ON time_internal_statuses.id = ual.time_internal_status_id
         LEFT JOIN user_activity_log_invoicing_statuses ualis ON ualis.id = ual.id
         LEFT JOIN clients_view cv ON cv.id = ual.client_id AND cv.model = 'clients'
         LEFT JOIN clients_view cv2 ON cv2.id = legal_cases.client_id AND cv2.model = 'clients'
         LEFT JOIN contacts ON contacts.id = legal_cases.requestedBy
Where legal_cases.isDeleted = 0 or ual.legal_case_id is null;
GO
/****** Object:  Table [dbo].[accounts_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[accounts_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[userId] [bigint] NOT NULL,
	[accountId] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[account_user_mapping]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[account_user_mapping] AS SELECT TOP(9223372036854775800)
accounts.id AS accountId, accounts.organization_id AS organizationId, accounts.currency_id AS currencyId, countries.currencyCode,
CASE WHEN prefixes.account_number_prefix IS NOT NULL THEN (accounts.name + ' - ' + countries.currencyCode + ' (' + prefixes.account_number_prefix + CAST(accounts.number as nvarchar) + ')') ELSE (accounts.name  + ' - ' + countries.currencyCode + ' (' + CAST(accounts.number as nvarchar) + ')') END as accountName,
 accounts_users.userId,(user_profiles.firstName + ' ' + user_profiles.lastName) as userName
FROM accounts
INNER JOIN countries ON countries.id = accounts.currency_id
INNER JOIN accounts_types ON accounts_types.id = accounts.account_type_id
INNER JOIN accounts_users ON accounts_users.accountId = accounts.id
INNER JOIN user_profiles ON user_profiles.user_id = accounts_users.userId
left join account_number_prefix_per_entity  as prefixes on accounts.account_type_id = prefixes.account_type_id AND accounts.organization_id = prefixes.organization_id
GO
/****** Object:  Table [dbo].[companies_customer_portal_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[companies_customer_portal_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NOT NULL,
	[customer_portal_user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[customer_portal_users_grid]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[customer_portal_users_grid] AS SELECT TOP(9223372036854775800)
customer_portal_users.id, customer_portal_users.userDirectory, customer_portal_users.isAd, customer_portal_users.isA4Luser, customer_portal_users.username, customer_portal_users.email, customer_portal_users.password
, customer_portal_users.status, customer_portal_users.firstName, customer_portal_users.lastName, customer_portal_users.employeeId, customer_portal_users.type as userType
, customer_portal_users.userCode, customer_portal_users.department, customer_portal_users.jobTitle, customer_portal_users.phone, customer_portal_users.mobile, customer_portal_users.banned, customer_portal_users.ban_reason, customer_portal_users.last_ip, customer_portal_users.last_login
, customer_portal_users.approved, customer_portal_users.createdOn, (created.firstName + ' ' + created.lastName) AS createdByName, customer_portal_users.modifiedOn
, (modified.firstName + ' ' + modified.lastName ) AS modifiedByName, modified.status as modifiedStatus, created.status as createdStatus
, company=STUFF( (SELECT '; '+ companies.name FROM companies_customer_portal_users INNER JOIN companies ON companies.id = companies_customer_portal_users.company_id WHERE companies_customer_portal_users.customer_portal_user_id = customer_portal_users.id FOR XML PATH('')), 1, 1, '')
FROM customer_portal_users
LEFT JOIN user_profiles created ON created.user_id = customer_portal_users.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = customer_portal_users.modifiedBy
GO
/****** Object:  Table [dbo].[legal_case_events]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_events](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case] [bigint] NOT NULL,
	[stage] [bigint] NULL,
	[event_type] [bigint] NULL,
	[parent] [bigint] NULL,
	[fields] [text] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_events_related_data]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_events_related_data](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[event] [bigint] NOT NULL,
	[related_id] [bigint] NOT NULL,
	[related_object] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[legal_cases_event_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[legal_cases_event_details] AS
    SELECT TOP(9223372036854775800) events.id, events.legal_case,
    events.event_type,events.fields,events .parent,events.modifiedBy,( modified.firstName + ' ' + modified.lastName ) as modified_by_name,modified.status as modified_by_status,events.modifiedOn,ld.legal_case_stage,events.stage,
    event_related_tasks = STUFF((SELECT ',' + CAST( related_data.related_id AS nvarchar ) from legal_case_events_related_data as related_data WHERE related_data.related_object = 'Task' AND related_data.event=events.id FOR XML PATH('')), 1, 1, ''),
    case_related_tasks = STUFF((SELECT ',' + CAST( related_data.related_id AS nvarchar)  from legal_case_events_related_data as related_data WHERE related_data.related_object = 'Task' FOR XML PATH('')), 1, 1, ''),
    event_related_reminders = STUFF((SELECT ',' + CAST( related_data.related_id AS nvarchar)  from legal_case_events_related_data as related_data WHERE related_data.related_object = 'Reminder' AND related_data.event=events.id FOR XML PATH('')), 1, 1, ''),
    case_related_reminders = STUFF((SELECT ',' + CAST( related_data.related_id AS nvarchar)  from legal_case_events_related_data as related_data WHERE related_data.related_object = 'Reminder' FOR XML PATH('')), 1, 1, '')
    FROM legal_case_events as events
    LEFT JOIN legal_case_litigation_details as ld on ld.id = events.stage
    LEFT JOIN user_profiles modified ON modified.user_id = events.modifiedBy;
GO
/****** Object:  Table [dbo].[client_trust_accounts_relation]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[client_trust_accounts_relation](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[client] [bigint] NOT NULL,
	[trust_liability_account] [bigint] NOT NULL,
	[trust_asset_account] [bigint] NOT NULL,
	[organization_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[deposits]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[deposits](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[voucher_header_id] [bigint] NOT NULL,
	[client_trust_accounts_id] [bigint] NOT NULL,
	[foreign_amount] [decimal](22, 2) NOT NULL,
	[currency] [bigint] NOT NULL,
	[payment_method] [nvarchar](15) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[deposits_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[deposits_full_details] AS
SELECT deposits.id,('DP' + CAST(deposits.id AS nvarchar)) as deposit_id,
deposits.payment_method,
deposits.foreign_amount,deposits.currency as foreign_currency, VD.local_amount,
accounts.client AS client_id,
voucher_headers.id as voucher_header_id, voucher_headers.organization_id, voucher_headers.dated as deposited_on, voucher_headers.voucherType as voucher_type,voucher_headers.refNum as ref_num,
voucher_headers.description,
liability_acc.id AS liability_acc_id,
(liability_acc.name + ' - '  + liability_acc.currencyCode + ' (' +  liability_acc.account_number +  ')' ) AS liability_account,asset_acc.id AS asset_acc_id,
(asset_acc.name + ' - '  + asset_acc.currencyCode + ' (' + asset_acc.account_number +  ')' )  AS asset_account,
clients.name AS client_name
FROM voucher_headers
INNER JOIN deposits ON deposits.voucher_header_id = voucher_headers.id
LEFT JOIN (
SELECT voucher_details.voucher_header_id, voucher_details.local_amount
FROM voucher_details
GROUP BY voucher_details.voucher_header_id,voucher_details.local_amount )VD on deposits.voucher_header_id=VD.voucher_header_id
INNER JOIN client_trust_accounts_relation as accounts ON accounts.id = deposits.client_trust_accounts_id
INNER JOIN accounts_details_lookup liability_acc ON liability_acc.id = accounts.trust_liability_account
INNER JOIN accounts_details_lookup asset_acc ON asset_acc.id = accounts.trust_asset_account
LEFT JOIN clients_view clients ON clients.id = accounts.client AND clients.model = 'clients'
GO
/****** Object:  Table [dbo].[contract_milestone]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_milestone](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[title] [nvarchar](255) NOT NULL,
	[serial_number] [nvarchar](255) NULL,
	[deliverables] [nvarchar](max) NULL,
	[status] [nvarchar](11) NULL,
	[financial_status] [nvarchar](15) NULL,
	[amount] [decimal](32, 12) NULL,
	[currency_id] [bigint] NULL,
	[percentage] [decimal](22, 10) NULL,
	[start_date] [date] NULL,
	[due_date] [date] NULL,
	[createdOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
	[channel] [nchar](3) NULL,
 CONSTRAINT [pk_contract_milestone] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[contract_payments_view]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

            CREATE   VIEW [dbo].[contract_payments_view] AS
            SELECT
                c.id AS contract_id,
                CAST(ROUND(
                    COALESCE(SUM(cm.amount), 0),
                    2
                ) AS DECIMAL(19,2)) AS amount_paid_so_far,
                
                CAST(ROUND(
                    CASE
                        WHEN c.value IS NULL THEN 0
                        WHEN c.value = 0 THEN 0
                        ELSE c.value - COALESCE(SUM(cm.amount), 0)
                    END,
                    2
                ) AS DECIMAL(19,2)) AS balance_due
            FROM
                dbo.contract c
            LEFT JOIN
                dbo.contract_milestone cm
                    ON cm.contract_id = c.id
                    AND cm.financial_status IN ('paid', 'partially_paid')
            GROUP BY
                c.id, c.value;
        
GO
/****** Object:  Table [dbo].[quote_headers]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[quote_headers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[voucher_header_id] [bigint] NOT NULL,
	[account_id] [bigint] NOT NULL,
	[billTo] [text] NULL,
	[term_id] [bigint] NOT NULL,
	[prefix] [nvarchar](32) NOT NULL,
	[suffix] [nvarchar](32) NULL,
	[dueOn] [smalldatetime] NOT NULL,
	[quoteDate] [smalldatetime] NOT NULL,
	[paidStatus] [nvarchar](14) NOT NULL,
	[purchaseOrder] [nvarchar](255) NULL,
	[total] [decimal](22, 2) NOT NULL,
	[quoteNumber] [varchar](255) NULL,
	[notes] [text] NULL,
	[displayTax] [tinyint] NULL,
	[displayDiscount] [tinyint] NULL,
	[groupTimeLogsByUserInExport] [char](1) NULL,
	[related_invoice_id] [bigint] NULL,
	[display_item_date] [tinyint] NOT NULL,
	[description] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[quote_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[quote_details](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[quote_header_id] [bigint] NOT NULL,
	[account_id] [bigint] NOT NULL,
	[item_id] [bigint] NULL,
	[sub_item_id] [bigint] NULL,
	[tax_id] [bigint] NULL,
	[discount_id] [bigint] NULL,
	[expense_id] [bigint] NULL,
	[item] [nvarchar](255) NOT NULL,
	[unitPrice] [decimal](22, 2) NOT NULL,
	[quantity] [decimal](22, 2) NOT NULL,
	[itemDescription] [text] NOT NULL,
	[percentage] [decimal](10, 2) NULL,
	[discountPercentage] [decimal](10, 4) NULL,
	[item_date] [date] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[quotes_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[quotes_full_details] AS SELECT
voucher_headers.id,
voucher_headers.organization_id,
voucher_headers.dated,
voucher_headers.voucherType,
voucher_headers.refNum,
voucher_headers.referenceNum,
voucher_headers.attachment,
voucher_headers.description,
ih.prefix AS prefix,
ih.suffix as suffix,
ih.paidStatus  AS paidStatus,
ih.purchaseOrder AS purchaseOrder,
ih.dueOn as dueOn, ih.account_id as accountID,
ih.total as total,
CASE WHEN ih.suffix is null THEN ( ih.prefix + CAST( voucher_headers.refNum AS nvarchar ) ) ELSE ( ih.prefix + CAST( voucher_headers.refNum AS nvarchar ) + ih.suffix ) END AS quoteId,
( adl.name + ' - ' + adl.currencyCode ) as clientAccount, adl.name AS accountName,
adl.currencyCode as clientCurrency, adl.fullName AS clientName,
adl.member_id as member_id, adl.model_name as model_type, adl.model_id as model_id,
voucher_headers.createdOn,voucher_headers.createdBy,voucher_headers.modifiedOn,voucher_headers.modifiedBy,
( created.firstName + ' ' + created.lastName ) AS createdByName,
( modified.firstName + ' ' + modified.lastName ) AS modifiedByName,
displayTax,
displayDiscount,
case_id = STUFF((
    SELECT ','+ CAST(voucher_related_cases.legal_case_id AS nvarchar) 
    FROM voucher_related_cases 
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id  
    FOR XML PATH('')), 1, 1, ''),
caseSubject = STUFF((
    SELECT ':/;'+ CAST(legal_cases.subject AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id 
    FOR XML PATH('')), 1, 3, ''),
caseCategory = STUFF((
    SELECT ','+ CAST(legal_cases.category AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id 
    FOR XML PATH('')), 1, 1, ''),
caseInternalReference = STUFF((
    SELECT ','+ CAST(legal_cases.internalReference AS nvarchar) 
    FROM voucher_related_cases 
    INNER JOIN legal_cases on legal_cases.id = voucher_related_cases.legal_case_id
    WHERE voucher_related_cases.voucher_header_id = voucher_headers.id 
    FOR XML PATH('')), 1, 1, ''), adl.account_number,  
	(
	SELECT ( sum((unitprice * quantity)) )
	FROM   quote_details
	WHERE  quote_header_id = ih.id ) AS subtotal,
	(
	SELECT ( sum( ROUND( ( (unitprice * quantity) * ISNULL(discountpercentage, 0) ) / 100, 2 ) ) )
	FROM   quote_details
	WHERE  quote_header_id = ih.id ) AS totaldiscount,
  (
	SELECT ( sum( ROUND( ( ( (unitprice * quantity) -( ( (unitprice * quantity) * ISNULL(discountpercentage, 0) ) / 100 ) ) * ISNULL(percentage, 0) ) / 100, 2 ) ) )
	FROM   quote_details
	WHERE  quote_header_id = ih.id ) AS totaltax,
	(
	SELECT ( sum( ( (unitprice * quantity) -( ( (unitprice * quantity) * ISNULL(discountpercentage, 0) ) / 100 ) ) +( ( ( (unitprice * quantity) -( ( (unitprice * quantity) * ISNULL(discountpercentage, 0) ) / 100 ) ) * ISNULL(percentage, 0) ) / 100 ) ) )
	FROM   quote_details
	WHERE  quote_header_id = ih.id ) AS total_after_discount_amount,
  (
	SELECT ( sum(
		   CASE
				  WHEN percentage > 0 THEN (( ( ( (unitprice * quantity) -( ( (unitprice * quantity) * ISNULL(discountpercentage, 0) ) / 100 ) ) * ISNULL(percentage, 0) ) ) / percentage)
				  ELSE 0
		   end ))
	FROM   quote_details
	WHERE  quote_header_id = ih.id ) AS taxable,
    (
        (
            SELECT ( sum((unitprice * quantity)) )
            FROM   quote_details
            WHERE  quote_header_id = ih.id 
        ) - 
        (
            SELECT ( sum( ( ( (unitprice * quantity) * ISNULL(discountpercentage, 0) ) / 100 ) ) )
            FROM   quote_details
            WHERE  quote_header_id = ih.id 
        ) - 
        (
            SELECT ( 
                sum(
                    CASE
                        WHEN percentage > 0 THEN (( ( ( (unitprice * quantity) -( ( (unitprice * quantity) * ISNULL(discountpercentage, 0) ) / 100 ) ) * ISNULL(percentage, 0) ) ) / percentage)
                        ELSE 0
                    END 
                )
            )
            FROM   quote_details
            WHERE  quote_header_id = ih.id 
        )
    ) AS nonTaxable,
	(
         SELECT  (SUM((unitPrice * quantity))) - (
                SUM(
                    ROUND(
                        (
                            (unitPrice * quantity) * ISNULL(discountPercentage, 0)
                        ) / 100, 2
                    )
                )
            )
        FROM   quote_details
        WHERE  quote_header_id = ih.id 
    ) AS sub_total_after_discount
FROM voucher_headers
INNER JOIN quote_headers as ih ON ih.voucher_header_id = voucher_headers.id
INNER JOIN accounts_details_lookup adl ON adl.id = ih.account_id
LEFT JOIN user_profiles created ON created.user_id = voucher_headers.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = voucher_headers.modifiedBy;
GO
/****** Object:  View [dbo].[legal_case_litigation_stages_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[legal_case_litigation_stages_full_details] AS SELECT
    TOP (9223372036854775800)
    stages.id as id,stages.legal_case_id,stages.sentenceDate,stages.comments,stages.legal_case_stage,stages.client_position,stages.status,stages.modifiedBy, stages.modifiedOn,
    court_types.name as court_type,court_degrees.name as court_degree,court_regions.name as court_region, courts.name as court,clients_view.name as client_name, stages.createdOn, stages.createdBy,
    ( UP.firstName + ' ' + UP.lastName ) as modifiedByName, ( creator.firstName + ' ' + creator.lastName ) as createdByName,
    ext_references =  STUFF((SELECT DISTINCT ' , ' + (ref.number) from legal_case_litigation_external_references as ref WHERE ref.stage = stages.id FOR XML PATH('')), 1, 3, ''),
    case_opponents = STUFF((SELECT ' , ' +(CASE WHEN legal_case_opponents.opponent_member_type IS NULL THEN NULL ELSE (CASE WHEN legal_case_opponents.opponent_member_type = 'company' THEN opponentCompany.name ELSE
    (CASE WHEN opponentContact.father!='' THEN (opponentContact.firstName + ' ' + opponentContact.father + ' ' + opponentContact.lastName) ELSE (opponentContact.firstName + ' ' + opponentContact.lastName) END) END)END ) from legal_case_opponents INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id
    LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company'
    LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact'
    WHERE legal_case_opponents.case_id = stages.legal_case_id FOR XML PATH('')), 1, 3, ''),
    opponents = STUFF((SELECT ' , ' + (CASE WHEN opponents.company_id IS NOT NULL THEN opponentCompany.name ELSE (CASE WHEN opponentContact.father!=''
    THEN (opponentContact.firstName + ' ' + opponentContact.father + ' ' + opponentContact.lastName) ELSE (opponentContact.firstName + ' ' + opponentContact.lastName) END) END  )
    from  legal_case_litigation_stages_opponents as stages_opponents
    INNER JOIN opponents ON opponents.id = stages_opponents.opponent_id
    LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id
    LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id
    WHERE stages_opponents.stage = stages.id FOR XML PATH('')), 1, 3, '')
    FROM legal_case_litigation_details as stages
    LEFT JOIN court_types ON court_types.id = stages.court_type_id
    LEFT JOIN court_degrees ON court_degrees.id = stages.court_degree_id
    LEFT JOIN court_regions ON court_regions.id = stages.court_region_id
    LEFT JOIN courts ON courts.id = stages.court_id
    LEFT JOIN legal_cases ON legal_cases.id = stages.legal_case_id
    LEFT JOIN clients_view ON clients_view.id=legal_cases.client_id AND clients_view.model = 'clients'
    LEFT JOIN user_profiles as UP ON UP.user_id = stages.modifiedBy
    LEFT JOIN user_profiles as creator ON creator.user_id = stages.createdBy
    ORDER BY stages.modifiedBy DESC;
GO
/****** Object:  Table [dbo].[invoice_headers]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[invoice_headers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[voucher_header_id] [bigint] NOT NULL,
	[original_invoice_id] [bigint] NULL,
	[account_id] [bigint] NOT NULL,
	[billTo] [text] NULL,
	[invoice_type_id] [bigint] NULL,
	[transaction_type_id] [bigint] NULL,
	[payment_method_id] [bigint] NULL,
	[term_id] [bigint] NOT NULL,
	[prefix] [nvarchar](32) NOT NULL,
	[suffix] [nvarchar](32) NULL,
	[dueOn] [smalldatetime] NOT NULL,
	[invoiceDate] [smalldatetime] NOT NULL,
	[paidStatus] [nvarchar](14) NOT NULL,
	[purchaseOrder] [nvarchar](255) NULL,
	[total] [decimal](22, 2) NOT NULL,
	[invoiceNumber] [varchar](255) NULL,
	[notes] [text] NULL,
	[displayTax] [tinyint] NULL,
	[displayDiscount] [nvarchar](30) NULL,
	[groupTimeLogsByUserInExport] [char](1) NULL,
	[related_quote_id] [bigint] NULL,
	[display_item_date] [tinyint] NOT NULL,
	[display_item_quantity] [tinyint] NULL,
	[exchangeRate] [decimal](22, 10) NULL,
	[discount_id] [bigint] NULL,
	[discount_percentage] [decimal](22, 10) NULL,
	[discount_amount] [decimal](22, 2) NULL,
	[discount_value_type] [varchar](10) NULL,
	[description] [text] NULL,
	[draft_invoice_number] [bigint] NULL,
	[debit_note_reason_id] [bigint] NULL,
	[lines_total_discount] [decimal](32, 12) NOT NULL,
	[lines_total_subtotal] [decimal](32, 12) NOT NULL,
	[lines_total_tax] [decimal](32, 12) NOT NULL,
	[lines_totals] [decimal](32, 12) NOT NULL,
	[invoice_template_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[invoice_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[invoice_details](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[invoice_header_id] [bigint] NOT NULL,
	[account_id] [bigint] NOT NULL,
	[item_id] [bigint] NULL,
	[sub_item_id] [bigint] NULL,
	[tax_id] [bigint] NULL,
	[discount_id] [bigint] NULL,
	[expense_id] [bigint] NULL,
	[item] [nvarchar](255) NOT NULL,
	[unitPrice] [decimal](22, 2) NOT NULL,
	[quantity] [decimal](22, 2) NOT NULL,
	[itemDescription] [text] NULL,
	[percentage] [decimal](10, 2) NULL,
	[discountPercentage] [decimal](22, 10) NULL,
	[item_date] [date] NULL,
	[discountAmount] [decimal](22, 2) NULL,
	[discount_type] [varchar](10) NULL,
	[line_sub_total] [decimal](32, 12) NOT NULL,
	[sub_total_after_line_disc] [decimal](32, 12) NOT NULL,
	[tax_amount] [decimal](32, 12) NOT NULL,
	[total] [decimal](32, 12) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[invoice_details_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE VIEW [dbo].[invoice_details_full_details] AS 
SELECT
   invoice_details.id,
   invoice_details.invoice_header_id,
   invoice_headers.account_id,
   invoice_details.item,
   invoice_details.unitPrice as unit_price,
   invoice_details.quantity,
   cast( invoice_details.unitPrice * invoice_details.quantity as DECIMAL(38, 2) ) as total_price,
   invoice_details.itemDescription as item_description,
   invoice_details.percentage as tax_percentage,
   CASE
      WHEN
         invoice_headers.displayDiscount = 'invoice_level_before_tax' 
      THEN
         invoice_headers.discount_percentage 
      ELSE
         invoice_details.discountPercentage 
   END
   as discount_percentage, 
   CASE
      WHEN
         invoice_headers.displayDiscount = 'invoice_level_before_tax' 
      THEN
         cast(( invoice_details.unitPrice * invoice_details.quantity * invoice_headers.discount_percentage / 100) as DECIMAL(38, 2) ) 
      ELSE
         invoice_details.discountAmount 
   END
   as discount_amount, invoice_details.item_date, 
   CASE
      WHEN
         invoice_headers.displayDiscount = 'invoice_level_before_tax' 
      THEN
         cast( ( invoice_details.unitPrice * invoice_details.quantity ) - ( invoice_details.unitPrice * invoice_details.quantity * invoice_headers.discount_percentage / 100) as DECIMAL(38, 2) ) 
      ELSE
         cast( invoice_details.unitPrice * invoice_details.quantity - ( invoice_details.unitPrice * invoice_details.quantity * ISNULL(invoice_details.discountPercentage, 0) / 100 ) - ISNULL(invoice_details.discountAmount, 0) as DECIMAL(38, 2) ) 
   END
   as price_after_discount, 
   CASE
      WHEN
         invoice_headers.displayDiscount = 'invoice_level_before_tax' 
      THEN
         cast( (((invoice_details.unitPrice * invoice_details.quantity) - ( invoice_details.unitPrice * invoice_details.quantity * invoice_headers.discount_percentage / 100))) + ((((invoice_details.unitPrice * invoice_details.quantity) - ( invoice_details.unitPrice * invoice_details.quantity * invoice_headers.discount_percentage / 100)) * ISNULL(invoice_details.percentage, 0) / 100 )) as DECIMAL(38, 2)) 
      ELSE
         cast( (invoice_details.unitPrice * invoice_details.quantity) - ((((invoice_details.unitPrice * invoice_details.quantity) * ISNULL(invoice_details.discountPercentage, 0) / 100) - ISNULL(invoice_details.discountAmount, 0))) + (((invoice_details.unitPrice * invoice_details.quantity) - (((invoice_details.unitPrice * invoice_details.quantity) * ISNULL(invoice_details.discountPercentage, 0) / 100) - ISNULL(invoice_details.discountAmount, 0))) * ISNULL(invoice_details.percentage, 0) / 100 ) as DECIMAL(38, 2)) 
   END
   as price_after_tax 
FROM
   invoice_details 
   inner join
      invoice_headers 
      on invoice_details.invoice_header_id = invoice_headers.id;
GO
/****** Object:  Table [dbo].[credit_note_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[credit_note_details](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[credit_note_header_id] [bigint] NOT NULL,
	[account_id] [bigint] NOT NULL,
	[item_id] [bigint] NULL,
	[tax_id] [bigint] NULL,
	[discount_id] [bigint] NULL,
	[expense_id] [bigint] NULL,
	[item_title] [nvarchar](255) NOT NULL,
	[unit_price] [decimal](32, 12) NOT NULL,
	[quantity] [decimal](32, 12) NOT NULL,
	[item_description] [text] NULL,
	[tax_percentage] [decimal](15, 12) NULL,
	[discount_percentage] [decimal](15, 12) NULL,
	[discount_amount] [decimal](32, 12) NULL,
	[item_date] [date] NULL,
	[discount_type] [varchar](10) NULL,
	[line_sub_total] [decimal](32, 12) NOT NULL,
	[sub_total_after_line_disc] [decimal](32, 12) NOT NULL,
	[tax_amount] [decimal](32, 12) NOT NULL,
	[total] [decimal](32, 12) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[credit_note_refunds]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[credit_note_refunds](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[credit_note_header_id] [bigint] NOT NULL,
	[voucher_header_id] [bigint] NOT NULL,
	[account_id] [bigint] NOT NULL,
	[refund_method] [nvarchar](20) NOT NULL,
	[total] [decimal](32, 12) NOT NULL,
	[client_account_id] [bigint] NOT NULL,
	[credit_note_refund_total] [decimal](32, 12) NOT NULL,
	[exchange_rate] [decimal](32, 12) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[credit_note_invoices]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[credit_note_invoices](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[credit_note_header_id] [bigint] NOT NULL,
	[invoice_header_id] [bigint] NOT NULL,
	[total] [decimal](32, 12) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[credit_note_related_cases]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[credit_note_related_cases](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_id] [bigint] NOT NULL,
	[credit_note_header_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[credit_note_headers]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[credit_note_headers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[organization_id] [bigint] NOT NULL,
	[voucher_header_id] [bigint] NULL,
	[account_id] [bigint] NOT NULL,
	[bill_to] [text] NULL,
	[credit_note_type_id] [bigint] NULL,
	[transaction_type_id] [bigint] NULL,
	[credit_note_reason_id] [bigint] NULL,
	[term_id] [bigint] NOT NULL,
	[prefix] [nvarchar](32) NOT NULL,
	[suffix] [nvarchar](32) NULL,
	[due_on] [smalldatetime] NULL,
	[credit_note_date] [smalldatetime] NOT NULL,
	[paid_status] [nvarchar](20) NOT NULL,
	[total] [decimal](32, 12) NOT NULL,
	[credit_note_number] [nvarchar](255) NULL,
	[reference_num] [nvarchar](255) NULL,
	[notes] [text] NULL,
	[display_tax] [bigint] NULL,
	[display_discount] [nvarchar](30) NULL,
	[group_time_logs_by_user_in_export] [char](1) NULL,
	[display_item_date] [tinyint] NULL,
	[display_item_quantity] [tinyint] NULL,
	[exchange_rate] [decimal](32, 12) NULL,
	[discount_id] [bigint] NULL,
	[discount_percentage] [decimal](15, 12) NULL,
	[discount_amount] [decimal](32, 12) NULL,
	[discount_value_type] [nvarchar](10) NULL,
	[description] [text] NULL,
	[draft_credit_note_number] [bigint] NULL,
	[created_on] [smalldatetime] NULL,
	[created_by] [bigint] NULL,
	[modified_on] [smalldatetime] NULL,
	[modified_by] [bigint] NULL,
	[lines_total_discount] [decimal](32, 12) NOT NULL,
	[lines_total_subtotal] [decimal](32, 12) NOT NULL,
	[lines_total_tax] [decimal](32, 12) NOT NULL,
	[lines_totals] [decimal](32, 12) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[credit_notes_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[credit_notes_full_details] AS 
select cnh.id as id, cnh.id as credit_note_header_id, cnh.voucher_header_id as voucher_header_id,cnh.organization_id as organization_id,cast(cnh.credit_note_date as date) AS credit_note_date
	,cnh.reference_num AS credit_note_reference,cnh.credit_note_number AS credit_note_number, cnh.description AS description,cnh.prefix AS prefix,cnh.suffix AS suffix
	,(case when (cnh.paid_status = 'open' and cnh.due_on < getdate()) then 'overdue' else cnh.paid_status end) AS paid_status
	,cast(cnh.due_on as date) AS due_on, cnh.term_id AS term_id, cnh.account_id AS account_id, cnh.notes AS notes, cnh.total AS total
	,concat(isnull(cnh.prefix,''),cnh.credit_note_number,isnull(cnh.suffix,'')) AS credit_note_number_full
	,concat(adl.name,' - ',adl.currencyCode) AS client_account, cnh.exchange_rate AS exchange_rate, cnh.bill_to AS bill_to, adl.name AS account_name,adl.currencyCode AS client_currency, adl.currency_id AS currency_id, adl.member_id AS member_id
	,adl.fullName AS client_name,adl.model_id AS model_id,adl.model_name AS model_type
	,(select sum(credit_note_refunds.credit_note_refund_total) from credit_note_refunds where (credit_note_refunds.credit_note_header_id = cnh.id)) AS refunds_made
	,(select sum(credit_note_invoices.total) from credit_note_invoices where (credit_note_invoices.credit_note_header_id = cnh.id)) AS invoices_close
	,(cnh.total - isnull((select sum(credit_note_refunds.credit_note_refund_total) from credit_note_refunds where (credit_note_refunds.credit_note_header_id = cnh.id)),0.0)
		- isnull((select sum(credit_note_invoices.total) from credit_note_invoices where (credit_note_invoices.credit_note_header_id = cnh.id)),0.0) ) AS balance_due
	,cnh.created_on AS created_on,cnh.created_by AS created_by,cnh.modified_on AS modified_on,cnh.modified_by AS modified_by
	,concat(created.firstName,' ',created.lastName) AS created_by_name,concat(modified.firstName,' ',modified.lastName) AS modified_by_name,cnh.display_tax AS display_tax, cnh.display_item_date AS display_item_date
	,cnh.display_discount AS display_discount,cnh.discount_percentage AS discount_percentage, cnh.discount_amount AS discount_amount
	,case_id = STUFF((
      SELECT concat(',', credit_note_related_cases.legal_case_id) FROM credit_note_related_cases 
	  WHERE credit_note_related_cases.credit_note_header_id = cnh.id FOR XML PATH('')
    ), 1, 1, '')
	,case_subject = STUFF((
      SELECT concat(':/;', legal_cases.subject) FROM credit_note_related_cases 
		INNER JOIN legal_cases ON legal_cases.id = credit_note_related_cases.legal_case_id 
	  WHERE credit_note_related_cases.credit_note_header_id = cnh.id FOR XML PATH('')
    ), 1, 3, '')
	,assignee = STUFF((
      SELECT concat(',', user_profiles.firstName,' ',user_profiles.lastName) FROM credit_note_related_cases 
		INNER JOIN legal_cases ON legal_cases.id = credit_note_related_cases.legal_case_id 
		LEFT JOIN users ON users.id = legal_cases.user_id 
        LEFT JOIN user_profiles ON user_profiles.user_id = users.id 
	  WHERE credit_note_related_cases.credit_note_header_id = cnh.id FOR XML PATH('')
    ), 1, 1, '')
	,case_internal_reference = STUFF((
      SELECT concat(',', legal_cases.internalReference) FROM credit_note_related_cases 
		INNER JOIN legal_cases ON legal_cases.id = credit_note_related_cases.legal_case_id 
	  WHERE credit_note_related_cases.credit_note_header_id = cnh.id FOR XML PATH('')
    ), 1, 1, '')
  ,invoice_credited = STUFF((
      SELECT concat(',', isnull(inv_hdr.prefix, ''),inv_vhcr.refNum,isnull(inv_hdr.suffix, '')) FROM credit_note_invoices 
		inner join invoice_headers inv_hdr on inv_hdr.id = credit_note_invoices.invoice_header_id 
		inner join voucher_headers inv_vhcr on inv_vhcr.id = inv_hdr.voucher_header_id
	  WHERE credit_note_invoices.credit_note_header_id = cnh.id FOR XML PATH('')
    ), 1, 1, '')
	,practice_area = STUFF((
      SELECT concat(',', case_types.name) FROM credit_note_related_cases 
		INNER JOIN legal_cases ON legal_cases.id = credit_note_related_cases.legal_case_id 
		LEFT JOIN case_types ON case_types.id = legal_cases.case_type_id 
	  WHERE credit_note_related_cases.credit_note_header_id = cnh.id FOR XML PATH('')
    ), 1, 1, '')
	,case_category = STUFF((
      SELECT concat(',', legal_cases.category) FROM credit_note_related_cases 
		INNER JOIN legal_cases ON legal_cases.id = credit_note_related_cases.legal_case_id 
	  WHERE credit_note_related_cases.credit_note_header_id = cnh.id FOR XML PATH('')
    ), 1, 1, '')
	,adl.account_number AS account_number
	,(select sum((credit_note_details.line_sub_total)) from credit_note_details where (credit_note_details.credit_note_header_id = cnh.id)) AS sub_total
	,round((cnh.lines_total_discount + cnh.discount_amount), 2) AS total_discount
	,round(cnh.lines_total_tax, 2) AS total_tax
	,(select sum(round((case when (credit_note_details.tax_percentage > 0) then ((((credit_note_details.line_sub_total) - (case when (crdtnh.display_discount = 'item_level') then (case when (credit_note_details.discount_percentage is not null) then (((credit_note_details.line_sub_total) * isnull(credit_note_details.discount_percentage,0)) / 100) else (case when (credit_note_details.discount_amount is not null) then (credit_note_details.discount_amount * 1) else 0 end) end) else (case when (crdtnh.display_discount = 'invoice_level_before_tax') then (((credit_note_details.line_sub_total) * isnull(crdtnh.discount_percentage,0)) / 100) else 0 end) end)) * isnull(credit_note_details.tax_percentage,0)) / credit_note_details.tax_percentage) else 0 end),2)) 
		from credit_note_details inner join credit_note_headers crdtnh on (credit_note_details.credit_note_header_id = crdtnh.id) where crdtnh.id = cnh.id) AS taxable
	,(((select sum(credit_note_details.line_sub_total) 
		from credit_note_details inner join credit_note_headers crdtnh on (credit_note_details.credit_note_header_id = crdtnh.id) where crdtnh.id = cnh.id)
		- (select sum(round((case when (crdtnh.display_discount = 'item_level') then (case when (credit_note_details.discount_percentage is not null) then (((credit_note_details.line_sub_total) * isnull(credit_note_details.discount_percentage,0)) / 100) else (case when (credit_note_details.discount_amount is not null) then (credit_note_details.discount_amount * 1) else 0 end) end) else (case when (crdtnh.display_discount = 'invoice_level_before_tax') then (((credit_note_details.line_sub_total) * isnull(crdtnh.discount_percentage,0)) / 100) else 0 end) end),2)) 
		from credit_note_details inner join credit_note_headers crdtnh on (credit_note_details.credit_note_header_id = crdtnh.id) where crdtnh.id = cnh.id)
		- (select sum(round((case when (credit_note_details.tax_percentage > 0) then ((((credit_note_details.line_sub_total) - (case when (crdtnh.display_discount = 'item_level') then (case when (credit_note_details.discount_percentage is not null) then (((credit_note_details.line_sub_total) * isnull(credit_note_details.discount_percentage,0)) / 100) else (case when (credit_note_details.discount_amount is not null) then (credit_note_details.discount_amount * 1) else 0 end) end) else (case when (crdtnh.display_discount = 'invoice_level_before_tax') then (((credit_note_details.line_sub_total) * isnull(crdtnh.discount_percentage,0)) / 100) else 0 end) end)) * isnull(credit_note_details.tax_percentage,0)) / credit_note_details.tax_percentage) else 0 end),2)) 
		from credit_note_details inner join credit_note_headers crdtnh on (credit_note_details.credit_note_header_id = crdtnh.id) where crdtnh.id = cnh.id))) AS non_taxable
	,(cnh.lines_total_subtotal - cnh.lines_total_discount - (CASE WHEN cnh.display_discount = 'invoice_level_before_tax' OR cnh.display_discount = 'both_item_before_level' THEN cnh.discount_amount ELSE 0 END)) AS sub_total_after_discount 
	,adl.tax_number as tax_number, cnh.lines_total_discount, cnh.lines_total_subtotal, cnh.lines_total_tax, cnh.lines_totals
from credit_note_headers cnh
	left join voucher_headers on cnh.voucher_header_id = voucher_headers.id
	inner join accounts_details_lookup adl on adl.id = cnh.account_id
	inner join user_profiles created on created.user_id = cnh.created_by 
	inner join user_profiles modified on modified.user_id = cnh.modified_by
;

GO
/****** Object:  Table [dbo].[invoice_payment_invoices]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[invoice_payment_invoices](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[invoice_payment_id] [bigint] NOT NULL,
	[invoice_header_id] [bigint] NOT NULL,
	[amount] [decimal](22, 2) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[invoices_full_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE VIEW [dbo].[invoices_full_details] AS 
 SELECT voucher_headers.id,
       voucher_headers.organization_id,
       voucher_headers.dated,
       voucher_headers.voucherType,
       voucher_headers.refNum,
       voucher_headers.referenceNum,
       voucher_headers.attachment,
       voucher_headers.description,
       ih.prefix                                                                                                                AS prefix,
       ih.suffix                                                                                                                AS suffix,
       CASE
         WHEN ( ih.paidStatus <> 'paid'
                AND ih.paidStatus <> 'draft'
                AND ih.paidStatus <> 'cancelled'
                AND ih.dueOn < Getdate() ) THEN 'overdue'
         ELSE ih.paidStatus
       END                                                                                                                      AS paidStatus,
       ih.purchaseOrder                                                                                                         AS purchaseOrder,
       ih.dueOn                                                                                                                 AS dueOn,
       ih.account_id                                                                                                            AS accountID,
       ih.total                                                                                                                 AS total,
       CASE
         WHEN ih.suffix is null THEN ( ih.prefix
                                       + Cast( voucher_headers.refNum AS nvarchar ) )
         ELSE ( ih.prefix
                + Cast( voucher_headers.refNum AS nvarchar )
                + ih.suffix )
       END                                                                                                                      AS invoiceId,
       ( adl.name + ' - ' + adl.currencyCode )                                                                                  AS clientAccount,
       adl.name                                                                                                                 AS accountName,
       adl.currencyCode                                                                                                         AS clientCurrency,
       adl.fullName                                                                                                             AS clientName,
       adl.member_id                                                                                                            AS member_id,
       adl.model_id,
       adl.model_name                                                                                                           AS model_type,
       IPI.payemntsMade                                                                                                         AS payemntsMade,
       ICN.totalCreditNotes                                                                                                     AS totalCreditNotes,
       total - Isnull(IPI.payemntsMade, 0.0) - Isnull(ICN.totalCreditNotes, 0.0)                                                AS balanceDue,
       voucher_headers.createdOn,
       voucher_headers.createdBy,
       voucher_headers.modifiedOn,
       voucher_headers.modifiedBy,
       ( created.firstName + ' ' + created.lastName )                                                                           AS createdByName,
       ( modified.firstName + ' ' + modified.lastName )                                                                         AS modifiedByName,
       displayTax,
       displayDiscount,
       case_id = Stuff((SELECT ','
                               + Cast( voucher_related_cases.legal_case_id AS nvarchar )
                        FROM   voucher_related_cases
                        WHERE  voucher_related_cases.voucher_header_id = voucher_headers.id
                        FOR XML PATH('')), 1, 1, ''),
       caseSubject = Stuff((SELECT ':/;'
                                   + Cast(legal_cases.subject AS nvarchar)
                            FROM   voucher_related_cases
                                   INNER JOIN legal_cases
                                           ON legal_cases.id = voucher_related_cases.legal_case_id
                            WHERE  voucher_related_cases.voucher_header_id = voucher_headers.id
                            FOR XML PATH('')), 1, 3, ''),
       assignee = Stuff((SELECT ':/;'
                                + Cast( user_profiles.firstName AS nvarchar )
                                + ' '
                                + Cast( user_profiles.lastName AS nvarchar )
                         FROM   voucher_related_cases
                                INNER JOIN legal_cases
                                        ON legal_cases.id = voucher_related_cases.legal_case_id
                                LEFT JOIN users
                                       ON users.id = legal_cases.user_id
                                LEFT JOIN user_profiles
                                       ON user_profiles.user_id = users.id
                         WHERE  voucher_related_cases.voucher_header_id = voucher_headers.id
                         FOR XML PATH('')), 1, 3, ''),
       caseCategory = Stuff((SELECT ','
                                    + Cast(legal_cases.category AS nvarchar)
                             FROM   voucher_related_cases
                                    INNER JOIN legal_cases
                                            ON legal_cases.id = voucher_related_cases.legal_case_id
                             WHERE  voucher_related_cases.voucher_header_id = voucher_headers.id
                             FOR XML PATH('')), 1, 1, ''),
       caseInternalReference = Stuff((SELECT ','
                                             + Cast( legal_cases.internalReference AS nvarchar )
                                      FROM   voucher_related_cases
                                             INNER JOIN legal_cases
                                                     ON legal_cases.id = voucher_related_cases.legal_case_id
                                      WHERE  voucher_related_cases.voucher_header_id = voucher_headers.id
                                      FOR XML PATH('')), 1, 1, ''),
       adl.account_number,
       practiceArea = Stuff((SELECT ',' + Cast(case_types.name AS nvarchar)
                             FROM   voucher_related_cases
                                    INNER JOIN legal_cases
                                            ON legal_cases.id = voucher_related_cases.legal_case_id
                                    LEFT JOIN case_types
                                           ON case_types.id = legal_cases.case_type_id
                             WHERE  voucher_related_cases.voucher_header_id = voucher_headers.id
                             FOR XML PATH('')), 1, 1, ''),
       (SELECT ( Sum(line_sub_total) )
        FROM   invoice_details
        WHERE  invoice_header_id = ih.id)  AS subTotal,
       ROUND((ih.lines_total_discount + ih.discount_amount), 2)  AS totalDiscount,
       ROUND(ih.lines_total_tax, 2)  AS totalTax,
       (SELECT ( Sum(Round(CASE
                             WHEN percentage > 0 THEN( (( ( line_sub_total - CASE
                                                                                         WHEN inv_hd.displayDiscount = 'item_level' THEN( CASE
                                                                                                                                            WHEN discountPercentage IS NOT NULL THEN ( ( line_sub_total *
                                                                                                                                                                                         ISNULL(discountPercentage, 0)
                                                                                                                                                                                       ) /
                                                                                                                                                                                       100 )
                                                                                                                                            ELSE
                                                                                                                                              CASE
                                                                                                                                                WHEN discountAmount IS NOT NULL THEN ( discountAmount * 1 )
                                                                                                                                                ELSE 0
                                                                                                                                              END
                                                                                                                                          END )
                                                                                         ELSE
                                                                                           CASE
                                                                                             WHEN inv_hd.displayDiscount = 'invoice_level_before_tax' THEN ( ( line_sub_total * ISNULL(inv_hd.discount_percentage, 0) ) / 100 )
                                                                                             ELSE 0
                                                                                           END
                                                                                       END ) * ISNULL(percentage, 0) )) / percentage )
                             ELSE 0
                           END, 2)) )
        FROM   invoice_details,
               invoice_headers inv_hd
        WHERE  inv_hd.id = invoice_details.invoice_header_id
               AND invoice_header_id = ih.id)                                                                                   AS taxable,
       ( (SELECT ( Sum(line_sub_total) )
          FROM   invoice_details
          WHERE  invoice_header_id = ih.id) - (SELECT ( Sum(Round(CASE
                                                                    WHEN inv_hd.displayDiscount = 'item_level' THEN( CASE
                                                                                                                       WHEN discountPercentage IS NOT NULL THEN ( ( line_sub_total * ISNULL(discountPercentage, 0)
                                                                                                                                                                  ) /
                                                                                                                                                                  100
                                                                                                                                                                )
                                                                                                                       ELSE
                                                                                                                         CASE
                                                                                                                           WHEN discountAmount IS NOT NULL THEN ( discountAmount * 1 )
                                                                                                                           ELSE 0
                                                                                                                         END
                                                                                                                     END )
                                                                    ELSE
                                                                      CASE
                                                                        WHEN inv_hd.displayDiscount = 'invoice_level_before_tax' THEN ( ( line_sub_total * ISNULL(inv_hd.discount_percentage, 0) ) / 100 )
                                                                        ELSE 0
                                                                      END
                                                                  END, 2)) )
                                               FROM   invoice_details,
                                                      invoice_headers inv_hd
                                               WHERE  inv_hd.id = invoice_details.invoice_header_id
                                                      AND invoice_header_id = ih.id) - (SELECT ( Sum(Round(CASE
                                                                                                             WHEN percentage > 0 THEN( (( ( ( line_sub_total ) - CASE
                                                                                                                                                                         WHEN inv_hd.displayDiscount = 'item_level' THEN(
                                                                                                                                                                         CASE
                                                                                                                                                                           WHEN
                                                                                                                                                                         discountPercentage IS NOT NULL THEN
                                                                                                                                                                         ( ( (
                                                                                                                                                                         line_sub_total ) *
                                                                                                                                                                             ISNULL(discountPercentage, 0)
                                                                                                                                                                                                               ) /
                                                                                                                                                                                                               100 )
                                                                                                                                                                         ELSE
                                                                                                                                                                           CASE
                                                                                                                                                                         WHEN
                                                                                                                                                                         discountAmount IS NOT NULL THEN (
                                                                                                                                                                         discountAmount * 1 )
                                                                                                                                                                         ELSE
                                                                                                                                                                         0
                                                                                                                                                                           END
                                                                                                                                                                         END )
                                                                                                                                                                         ELSE
                                                                                                                                                                           CASE
                                                                                                                                                                             WHEN inv_hd.displayDiscount = 'invoice_level_before_tax'
                                                                                                                                                                           THEN ( (
                                                                                                                                                                             line_sub_total *
                                                                                                                                                                             ISNULL(inv_hd.discount_percentage, 0) ) / 100 )
                                                                                                                                                                             ELSE 0
                                                                                                                                                                           END
                                                                                                                                                                       END ) * ISNULL(percentage, 0) )) / percentage )
                                                                                                             ELSE 0
                                                                                                           END, 2)) )
                                                                                        FROM   invoice_details,
                                                                                               invoice_headers inv_hd
                                                                                        WHERE  inv_hd.id = invoice_details.invoice_header_id
                                                                                               AND invoice_header_id = ih.id) ) AS nonTaxable,
        (ih.lines_total_subtotal - ih.lines_total_discount - (CASE WHEN ih.displayDiscount = 'invoice_level_before_tax' OR ih.displayDiscount = 'both_item_before_level' THEN ih.discount_amount ELSE 0 END))  AS sub_total_after_discount,
        ih.lines_total_discount, ih.lines_total_subtotal, ih.lines_total_tax, ih.lines_totals
FROM   voucher_headers
       INNER JOIN invoice_headers AS ih
               ON ih.voucher_header_id = voucher_headers.id
       LEFT JOIN (SELECT invoice_payment_invoices.invoice_header_id,
                         Sum(invoice_payment_invoices.amount)              AS payemntsMade
                  FROM   invoice_payment_invoices
                  GROUP  BY invoice_payment_invoices.invoice_header_id) AS IPI
              ON IPI.invoice_header_id = ih.id
       LEFT JOIN (SELECT credit_note_invoices.invoice_header_id,
                         Sum(credit_note_invoices.total)              AS totalCreditNotes
                  FROM   credit_note_invoices
				  inner join credit_note_headers on credit_note_headers.id = credit_note_invoices.credit_note_header_id
				  where credit_note_headers.paid_status in ('open', 'partially refund', 'refund')
                  GROUP  BY credit_note_invoices.invoice_header_id) AS ICN
              ON ICN.invoice_header_id = ih.id
       INNER JOIN accounts_details_lookup adl
               ON adl.id = ih.account_id
       LEFT JOIN user_profiles created
              ON created.user_id = voucher_headers.createdBy
       LEFT JOIN user_profiles modified
              ON modified.user_id = voucher_headers.modifiedBy;

GO
/****** Object:  View [dbo].[opinion_effective_effort]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[opinion_effective_effort] AS

        SELECT TOP(9223372036854775800) user_activity_logs.opinion_id AS opinion_id, SUM(user_activity_logs.effectiveEffort) AS effectiveEffort

        FROM user_activity_logs

        WHERE user_activity_logs.opinion_id IS NOT NULL

        GROUP BY user_activity_logs.opinion_id;
GO
/****** Object:  Table [dbo].[opinion_locations]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_locations](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
 CONSTRAINT [pk_opinion_locations] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[title] [nvarchar](255) NOT NULL,
	[legal_case_id] [bigint] NULL,
	[contract_id] [bigint] NULL,
	[stage] [bigint] NULL,
	[user_id] [bigint] NOT NULL,
	[assigned_to] [bigint] NOT NULL,
	[due_date] [date] NOT NULL,
	[private] [char](3) NULL,
	[priority] [nvarchar](8) NOT NULL,
	[opinion_location_id] [bigint] NULL,
	[detailed_info] [nvarchar](max) NULL,
	[opinion_status_id] [bigint] NOT NULL,
	[opinion_type_id] [bigint] NOT NULL,
	[estimated_effort] [decimal](8, 2) NULL,
	[createdOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
	[archived] [nvarchar](3) NOT NULL,
	[hideFromBoard] [nvarchar](3) NULL,
	[reporter] [bigint] NULL,
	[workflow] [bigint] NOT NULL,
	[legal_question] [nvarchar](max) NULL,
	[opinion_file] [nvarchar](255) NULL,
	[category] [nvarchar](20) NULL,
	[background_info] [nvarchar](max) NULL,
	[requester] [bigint] NULL,
	[channel] [nvarchar](5) NULL,
	[is_visible_to_cp] [bit] NULL,
 CONSTRAINT [pk_opinions] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_contributors]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_contributors](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[opinion_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
 CONSTRAINT [pk_opinion_contributors] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_statuses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[category] [nvarchar](255) NOT NULL,
	[isGlobal] [tinyint] NOT NULL,
 CONSTRAINT [pk_opinion_statuses] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[opinions_detailed_view]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[opinions_detailed_view] AS

        SELECT TOP (9223372036854775800)

            opinions.id,

            ('LO' + CAST(opinions.id AS NVARCHAR(MAX))) AS opinionId, -- Cast to NVARCHAR(MAX) for string concatenation

            opinions.title,

            CASE

                WHEN opinions.legal_case_id IS NULL THEN ''

                ELSE ('M' + CAST(opinions.legal_case_id AS NVARCHAR(MAX)))

            END AS caseId,

            opinions.legal_case_id,

            opinions.user_id,

            opinions.due_date,

            opinions.assigned_to AS assignedToId,

            opinions.reporter AS reportedById,

            opinions.private,

            opinions.priority,

            opinions.opinion_location_id AS opinion_location_id,

            opinion_locations.name AS location,

            opinions.detailed_info AS opinionFulldetailed_info,

            opinions.background_info,

            opinions.legal_question,

            opinions.opinion_status_id,

            opinions.opinion_type_id,

            opinions.estimated_effort,

            opinions.channel,

            opinions.requester,

            opinions.is_visible_to_cp,

            CAST(opinions.createdOn AS DATE) AS createdOn,

            CAST(opinions.modifiedOn AS DATE) AS modifiedOn,

            opinions.modifiedBy,

            opinions.archived,

            tee.effectiveEffort,

            (assigned.firstName + ' ' + assigned.lastName) AS assigned_to, -- SQL Server string concat

            (reporter.firstName + ' ' + reporter.lastName) AS reporter, -- SQL Server string concat

            (created.firstName + ' ' + created.lastName) AS createdBy, -- SQL Server string concat

            (modified.firstName + ' ' + modified.lastName) AS modifiedByName, -- SQL Server string concat

            opinions.createdBy AS createdById,

            ts.name AS opinionStatus,

            opinions.archived AS archivedOpinions,

            SUBSTRING(opinions.detailed_info, 1, 50) AS detailed_info,

            SUBSTRING(lg.subject, 1, 50) AS caseSubject,

            lg.subject AS caseFullSubject,

            lg.category AS caseCategory,

            opinions.contract_id,

            contract.name AS contract_name,

            assigned.status AS assignee_status,

            reporter.status AS reporter_status,

            created.status AS creator_status,

            modified.status AS modifier_status,

            opinions.stage,

            opinions.category AS opinionCategory,

            contributors = STUFF(

                (

                    SELECT ', ' + (contr.firstName + ' ' + contr.lastName)

                    FROM user_profiles AS contr

                    INNER JOIN opinion_contributors ON opinions.id = opinion_contributors.opinion_id AND contr.user_id = opinion_contributors.user_id

                    FOR XML PATH(''), TYPE

                ).value('.', 'NVARCHAR(MAX)'), -- Proper way to extract text from XML in SQL Server

                1,

                2, -- Changed from 1 to 2 to remove the leading ', '

                ''

            )

        FROM opinions

        LEFT JOIN user_profiles assigned ON assigned.user_id = opinions.assigned_to

        LEFT JOIN user_profiles reporter ON reporter.user_id = opinions.reporter

        LEFT JOIN user_profiles created ON created.user_id = opinions.createdBy

        LEFT JOIN user_profiles modified ON modified.user_id = opinions.modifiedBy

        LEFT JOIN opinion_statuses ts ON ts.id = opinions.opinion_status_id

        LEFT JOIN legal_cases AS lg ON lg.id = opinions.legal_case_id

        LEFT JOIN contract ON contract.id = opinions.contract_id

        LEFT JOIN opinion_effective_effort AS tee ON tee.opinion_id = opinions.id

        LEFT JOIN opinion_locations ON opinion_locations.id = opinions.opinion_location_id

        WHERE

            opinions.legal_case_id IS NULL OR lg.isDeleted = 0;
GO
/****** Object:  UserDefinedFunction [dbo].[TotalCaseValuesByClientId]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE FUNCTION [dbo].[TotalCaseValuesByClientId] (@client_id BIGINT)
RETURNS TABLE
AS
RETURN
(
   select sum(legal_cases.caseValue) as caseValue from legal_cases where legal_cases.client_id = @client_id
);
GO
/****** Object:  UserDefinedFunction [dbo].[caseValuesSummationByClientId]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE FUNCTION [dbo].[caseValuesSummationByClientId] (@client_id BIGINT)
RETURNS TABLE
AS
RETURN
(
   select sum(legal_cases.caseValue) as caseValue from legal_cases where legal_cases.client_id = @client_id and legal_cases.category = 'Litigation' and legal_cases.isDeleted = 0
);
GO
/****** Object:  UserDefinedFunction [dbo].[RecoveredValuesSummationByClientId]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE FUNCTION [dbo].[RecoveredValuesSummationByClientId] (@client_id BIGINT)
RETURNS TABLE
AS
RETURN
(
   select sum(legal_cases.recoveredValue) as recoveredValue from legal_cases where legal_cases.client_id = @client_id and legal_cases.category = 'Litigation'and legal_cases.isDeleted = 0
);
GO
/****** Object:  UserDefinedFunction [dbo].[JudgementValuesSummationByClientId]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE FUNCTION [dbo].[JudgementValuesSummationByClientId] (@client_id BIGINT)
RETURNS TABLE
AS
RETURN
(
   select sum(legal_cases.judgmentValue) as recoveredValue from legal_cases where legal_cases.client_id = @client_id and legal_cases.category = 'Litigation' and legal_cases.isDeleted = 0
);
GO
/****** Object:  UserDefinedFunction [dbo].[TotalExpensesByCaseId]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE FUNCTION [dbo].[TotalExpensesByCaseId](@case_id BIGINT,@organization_id BIGINT)
RETURNS TABLE
AS
RETURN
(
   select sum(voucher_details.local_amount) as TotalExpenses from voucher_headers left   join voucher_details on
   voucher_headers.id=voucher_details.voucher_header_id  and voucher_details.drCr='C'
   left join voucher_related_cases on voucher_related_cases.voucher_header_id = voucher_headers.id
    where voucher_related_cases.legal_case_id=@case_id and
    voucher_headers.organization_id=@organization_id and voucher_headers.voucherType='EXP'
);
GO
/****** Object:  UserDefinedFunction [dbo].[TotalExpensesPerCategory]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE FUNCTION [dbo].[TotalExpensesPerCategory](@expenses_id BIGINT)
RETURNS TABLE
AS
RETURN
(
   select sum(voucher_details.local_amount) as TotalExpenses from expenses left   join voucher_details on
   expenses.voucher_header_id=voucher_details.voucher_header_id and voucher_details.drCr='D'
    where expenses.id=@expenses_id
);
GO
/****** Object:  Table [dbo].[legal_case_outsources]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_outsources](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_id] [bigint] NOT NULL,
	[company_id] [bigint] NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_risks]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_risks](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[risk_category] [nvarchar](100) NOT NULL,
	[riskLevel] [nvarchar](50) NOT NULL,
	[risk_type] [nvarchar](100) NOT NULL,
	[details] [nvarchar](max) NULL,
	[mitigation] [nvarchar](max) NULL,
	[responsible_actor_id] [bigint] NULL,
	[status] [nvarchar](50) NULL,
	[createdBy] [bigint] NOT NULL,
	[createdOn] [datetime] NOT NULL,
 CONSTRAINT [pk_legal_case_risks] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[legal_cases_risks_and_feeNotes]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

            CREATE   VIEW [dbo].[legal_cases_risks_and_feeNotes] AS
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
                legal_cases.next_actions, -- NEW COLUMN ADDED
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
        
GO
/****** Object:  Table [dbo].[audit_logs]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[audit_logs](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[model] [varchar](255) NOT NULL,
	[action] [varchar](255) NOT NULL,
	[recordId] [bigint] NOT NULL,
	[created] [smalldatetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[audit_log_max_id]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[audit_log_max_id] AS
	SELECT TOP(9223372036854775800) MAX(audit_logs.id) AS id
	FROM audit_logs
	GROUP BY audit_logs.model, audit_logs.action, audit_logs.recordId
	ORDER BY MIN(audit_logs.id);
GO
/****** Object:  View [dbo].[audit_log_last_action]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
--audit_log_last_action;
CREATE VIEW [dbo].[audit_log_last_action] AS
	SELECT TOP(9223372036854775800) audit_logs.id AS id, audit_logs.user_id AS user_id,audit_logs.model AS model,audit_logs.action AS action,audit_logs.recordId AS recordId,audit_logs.created AS created,
	 user_profiles.firstName + ' ' + user_profiles.lastName AS fullName, users.username AS username, users.email AS email
	FROM audit_logs
	LEFT JOIN users ON users.id = audit_logs.user_id
	LEFT JOIN user_profiles ON user_profiles.user_id = audit_logs.user_id
	WHERE audit_logs.id IN (SELECT audit_log_max_id.id FROM audit_log_max_id);
GO
/****** Object:  UserDefinedFunction [dbo].[DelimitedSplit8K]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

 CREATE FUNCTION [dbo].[DelimitedSplit8K]
/**********************************************************************************************************************
 Purpose:
 Split a given string at a given delimiter and return a list of the split elements (items).

 Notes:
 1.  Leading a trailing delimiters are treated as if an empty string element were present.
 2.  Consecutive delimiters are treated as if an empty string element were present between them.
 3.  Except when spaces are used as a delimiter, all spaces present in each element are preserved.

 Returns:
 iTVF containing the following:
 ItemNumber = Element position of Item as a BIGINT (not converted to INT to eliminate a CAST)
 Item       = Element value as a VARCHAR(8000)

 Statistics on this function may be found at the following URL:
 http://www.sqlservercentral.com/Forums/Topic1101315-203-4.aspx

 CROSS APPLY Usage Examples and Tests:
--=====================================================================================================================
-- TEST 1:
-- This tests for various possible conditions in a string using a comma as the delimiter.  The expected results are
-- laid out in the comments
--=====================================================================================================================
--===== Conditionally drop the test tables to make reruns easier for testing.
     -- (this is NOT a part of the solution)
     IF OBJECT_ID('tempdb..#JBMTest') IS NOT NULL DROP TABLE #JBMTest
;
--===== Create and populate a test table on the fly (this is NOT a part of the solution).
     -- In the following comments, "b" is a blank and "E" is an element in the left to right order.
     -- Double Quotes are used to encapsulate the output of "Item" so that you can see that all blanks
     -- are preserved no matter where they may appear.
 SELECT *
   INTO #JBMTest
   FROM (                                               --# & type of Return Row(s)
         SELECT  0, NULL                      UNION ALL --1 NULL
         SELECT  1, SPACE(0)                  UNION ALL --1 b (Empty String)
         SELECT  2, SPACE(1)                  UNION ALL --1 b (1 space)
         SELECT  3, SPACE(5)                  UNION ALL --1 b (5 spaces)
         SELECT  4, ','                       UNION ALL --2 b b (both are empty strings)
         SELECT  5, '55555'                   UNION ALL --1 E
         SELECT  6, ',55555'                  UNION ALL --2 b E
         SELECT  7, ',55555,'                 UNION ALL --3 b E b
         SELECT  8, '55555,'                  UNION ALL --2 b B
         SELECT  9, '55555,1'                 UNION ALL --2 E E
         SELECT 10, '1,55555'                 UNION ALL --2 E E
         SELECT 11, '55555,4444,333,22,1'     UNION ALL --5 E E E E E
         SELECT 12, '55555,4444,,333,22,1'    UNION ALL --6 E E b E E E
         SELECT 13, ',55555,4444,,333,22,1,'  UNION ALL --8 b E E b E E E b
         SELECT 14, ',55555,4444,,,333,22,1,' UNION ALL --9 b E E b b E E E b
         SELECT 15, ' 4444,55555 '            UNION ALL --2 E (w/Leading Space) E (w/Trailing Space)
         SELECT 16, 'This,is,a,test.'                   --E E E E
        ) d (SomeID, SomeValue)
;
--===== Split the CSV column for the whole table using CROSS APPLY (this is the solution)
 SELECT test.SomeID, test.SomeValue, split.ItemNumber, Item = QUOTENAME(split.Item,'"')
   FROM #JBMTest test
  CROSS APPLY dbo.DelimitedSplit8K(test.SomeValue,',') split
;
--=====================================================================================================================
-- TEST 2:
-- This tests for various "alpha" splits and COLLATION using all ASCII characters from 0 to 255 as a delimiter against
-- a given string.  Note that not all of the delimiters will be visible and some will show up as tiny squares because
-- they are "control" characters.  More specifically, this test will show you what happens to various non-accented
-- letters for your given collation depending on the delimiter you chose.
--=====================================================================================================================
WITH
cteBuildAllCharacters (String,Delimiter) AS
(
 SELECT TOP 256
        'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
        CHAR(ROW_NUMBER() OVER (ORDER BY (SELECT NULL))-1)
   FROM master.sys.all_columns
)
 SELECT ASCII_Value = ASCII(c.Delimiter), c.Delimiter, split.ItemNumber, Item = QUOTENAME(split.Item,'"')
   FROM cteBuildAllCharacters c
  CROSS APPLY dbo.DelimitedSplit8K(c.String,c.Delimiter) split
  ORDER BY ASCII_Value, split.ItemNumber
;
-----------------------------------------------------------------------------------------------------------------------
 Other Notes:
 1. Optimized for VARCHAR(8000) or less.  No testing or error reporting for truncation at 8000 characters is done.
 2. Optimized for single character delimiter.  Multi-character delimiters should be resolvedexternally from this
    function.
 3. Optimized for use with CROSS APPLY.
 4. Does not "trim" elements just in case leading or trailing blanks are intended.
 5. If you don't know how a Tally table can be used to replace loops, please see the following...
    http://www.sqlservercentral.com/articles/T-SQL/62867/
 6. Changing this function to use NVARCHAR(MAX) will cause it to run twice as slow.  It's just the nature of
    VARCHAR(MAX) whether it fits in-row or not.
 7. Multi-machine testing for the method of using UNPIVOT instead of 10 SELECT/UNION ALLs shows that the UNPIVOT method
    is quite machine dependent and can slow things down quite a bit.
-----------------------------------------------------------------------------------------------------------------------
 Credits:
 This code is the product of many people's efforts including but not limited to the following:
 cteTally concept originally by Iztek Ben Gan and "decimalized" by Lynn Pettis (and others) for a bit of extra speed
 and finally redacted by Jeff Moden for a different slant on readability and compactness. Hat's off to Paul White for
 his simple explanations of CROSS APPLY and for his detailed testing efforts. Last but not least, thanks to
 Ron "BitBucket" McCullough and Wayne Sheffield for their extreme performance testing across multiple machines and
 versions of SQL Server.  The latest improvement brought an additional 15-20% improvement over Rev 05.  Special thanks
 to "Nadrek" and "peter-757102" (aka Peter de Heer) for bringing such improvements to light.  Nadrek's original
 improvement brought about a 10% performance gain and Peter followed that up with the content of Rev 07.

 I also thank whoever wrote the first article I ever saw on "numbers tables" which is located at the following URL
 and to Adam Machanic for leading me to it many years ago.
 http://sqlserver2000.databases.aspfaq.com/why-should-i-consider-using-an-auxiliary-numbers-table.html
-----------------------------------------------------------------------------------------------------------------------
 Revision History:
 Rev 00 - 20 Jan 2010 - Concept for inline cteTally: Lynn Pettis and others.
                        Redaction/Implementation: Jeff Moden
        - Base 10 redaction and reduction for CTE.  (Total rewrite)

 Rev 01 - 13 Mar 2010 - Jeff Moden
        - Removed one additional concatenation and one subtraction from the SUBSTRING in the SELECT List for that tiny
          bit of extra speed.

 Rev 02 - 14 Apr 2010 - Jeff Moden
        - No code changes.  Added CROSS APPLY usage example to the header, some additional credits, and extra
          documentation.

 Rev 03 - 18 Apr 2010 - Jeff Moden
        - No code changes.  Added notes 7, 8, and 9 about certain "optimizations" that don't actually work for this
          type of function.

 Rev 04 - 29 Jun 2010 - Jeff Moden
        - Added WITH SCHEMABINDING thanks to a note by Paul White.  This prevents an unnecessary "Table Spool" when the
          function is used in an UPDATE statement even though the function makes no external references.

 Rev 05 - 02 Apr 2011 - Jeff Moden
        - Rewritten for extreme performance improvement especially for larger strings approaching the 8K boundary and
          for strings that have wider elements.  The redaction of this code involved removing ALL concatenation of
          delimiters, optimization of the maximum "N" value by using TOP instead of including it in the WHERE clause,
          and the reduction of all previous calculations (thanks to the switch to a "zero based" cteTally) to just one
          instance of one add and one instance of a subtract. The length calculation for the final element (not
          followed by a delimiter) in the string to be split has been greatly simplified by using the ISNULL/NULLIF
          combination to determine when the CHARINDEX returned a 0 which indicates there are no more delimiters to be
          had or to start with. Depending on the width of the elements, this code is between 4 and 8 times faster on a
          single CPU box than the original code especially near the 8K boundary.
        - Modified comments to include more sanity checks on the usage example, etc.
        - Removed "other" notes 8 and 9 as they were no longer applicable.

 Rev 06 - 12 Apr 2011 - Jeff Moden
        - Based on a suggestion by Ron "Bitbucket" McCullough, additional test rows were added to the sample code and
          the code was changed to encapsulate the output in pipes so that spaces and empty strings could be perceived
          in the output.  The first "Notes" section was added.  Finally, an extra test was added to the comments above.

 Rev 07 - 06 May 2011 - Peter de Heer, a further 15-20% performance enhancement has been discovered and incorporated
          into this code which also eliminated the need for a "zero" position in the cteTally table.
**********************************************************************************************************************/
--===== Define I/O parameters
        (@pString VARCHAR(8000), @pDelimiter CHAR(1))
RETURNS TABLE WITH SCHEMABINDING AS
 RETURN
--===== "Inline" CTE Driven "Tally Table" produces values from 0 up to 10,000...
     -- enough to cover NVARCHAR(4000)
  WITH E1(N) AS (
                 SELECT 1 UNION ALL SELECT 1 UNION ALL SELECT 1 UNION ALL
                 SELECT 1 UNION ALL SELECT 1 UNION ALL SELECT 1 UNION ALL
                 SELECT 1 UNION ALL SELECT 1 UNION ALL SELECT 1 UNION ALL SELECT 1
                ),                          --10E+1 or 10 rows
       E2(N) AS (SELECT 1 FROM E1 a, E1 b), --10E+2 or 100 rows
       E4(N) AS (SELECT 1 FROM E2 a, E2 b), --10E+4 or 10,000 rows max
 cteTally(N) AS (--==== This provides the "base" CTE and limits the number of rows right up front
                     -- for both a performance gain and prevention of accidental "overruns"
                 SELECT TOP (ISNULL(DATALENGTH(@pString),0)) ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) FROM E4
                ),
cteStart(N1) AS (--==== This returns N+1 (starting position of each "element" just once for each delimiter)
                 SELECT 1 UNION ALL
                 SELECT t.N+1 FROM cteTally t WHERE SUBSTRING(@pString,t.N,1) = @pDelimiter
                ),
cteLen(N1,L1) AS(--==== Return start and length (for use in substring)
                 SELECT s.N1,
                        ISNULL(NULLIF(CHARINDEX(@pDelimiter,@pString,s.N1),0)-s.N1,8000)
                   FROM cteStart s
                )
--===== Do the actual split. The ISNULL/NULLIF combo handles the length for the final element when no delimiter is found.
 SELECT ItemNumber = ROW_NUMBER() OVER(ORDER BY l.N1),
        Item       = SUBSTRING(@pString, l.N1, l.L1)
   FROM cteLen l
;
GO
/****** Object:  UserDefinedFunction [dbo].[SubstringIndex]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create function [dbo].[SubstringIndex](
    @SourceString varchar(8000),
    @delim char(1),
    @idx int
)
returns table with schemabinding
return
with stritems as (
select
    ItemNumber,
    Item
from
    dbo.DelimitedSplit8k(@SourceString,@delim)
)
select
    Item = stuff((select @delim + si.Item
                  from stritems si
                  where si.ItemNumber <= @idx
                  order by si.ItemNumber
                  for xml path(''),TYPE).value('.','varchar(8000)'),1,1,'')
GO
/****** Object:  Table [dbo].[lookup_members]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[lookup_members](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NULL,
	[contact_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[members]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
--members;
CREATE VIEW [dbo].[members] AS
	SELECT TOP(9223372036854775800) lookm.id AS id, CASE WHEN lookm.contact_id IS NOT NULL THEN 'PER' ELSE 'COM' END AS modelCode,
		CASE WHEN lookm.contact_id IS NOT NULL THEN lookm.contact_id ELSE lookm.company_id END AS linkId,
		CASE WHEN lookm.contact_id IS NOT NULL THEN 'Person' ELSE 'Company' END AS type,
		CASE WHEN lookm.contact_id IS NOT NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS name
	FROM lookup_members lookm
	LEFT JOIN companies com ON com.id = lookm.company_id
	LEFT JOIN contacts con  ON con.id = lookm.contact_id;
GO
/****** Object:  Table [dbo].[shares_movements]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[shares_movements](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[shares_movement_header_id] [bigint] NULL,
	[company_id] [bigint] NOT NULL,
	[member_id] [bigint] NOT NULL,
	[initiatedOn] [date] NULL,
	[executedOn] [date] NULL,
	[type] [nvarchar](30) NOT NULL,
	[numberOfShares] [decimal](22, 2) NOT NULL,
	[category] [nvarchar](255) NULL,
	[comments] [nvarchar](255) NOT NULL,
	[to_member_id] [bigint] NULL,
	[from_member_id] [bigint] NULL,
	[certificationNb] [nvarchar](max) NULL,
	[fromShareNb] [nvarchar](max) NULL,
	[toShareNb] [nvarchar](max) NULL,
	[rightsOnShares] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  View [dbo].[shares_movement_history]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
--shares_movement_history;
CREATE VIEW [dbo].[shares_movement_history] AS
	SELECT TOP(9223372036854775800) mem.linkId AS id, sha.id as share_id, mem.type AS shareholderType, mem.name AS shareholderName, sha.company_id AS company_id, sha.member_id AS member_id, sha.initiatedOn AS initiatedOn,
	sha.executedOn AS executedOn, sha.type AS type, sha.numberOfShares AS numberOfShares, sha.comments AS comments
	FROM shares_movements sha
	LEFT JOIN members mem ON mem.id = sha.member_id;
GO
/****** Object:  View [dbo].[shareholders]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
--shareholders;
CREATE VIEW [dbo].[shareholders] AS
	SELECT TOP(9223372036854775800) smh.id AS id,
	smh.company_id AS company_id, MIN(smh.member_id) AS member_id,
	smh.shareholderType AS shareholderType, MIN(smh.shareholderName) AS shareholderName, MIN(sm.id) AS shareholderId,
	MIN(smh.type) AS type, SUM(smh.numberOfShares) AS numberOfShares,
	CASE WHEN c.bearerShares = 0 and c.nominalShares = 0 THEN 0 ELSE  ROUND((SUM(smh.numberOfShares) / ((c.bearerShares) + (c.nominalShares))),8) End as percentage,
	(AVG(c.shareParValue) * SUM(smh.numberOfShares)) AS sharesValue, MAX(c.shareParValueCurrency) AS currency, MIN(smh.comments) AS comments
	FROM shares_movement_history smh
	LEFT JOIN companies c ON c.id = smh.company_id
	LEFT JOIN shares_movements sm ON (sm.id = smh.share_id AND sm.member_id = smh.member_id AND sm.company_id = smh.company_id)
	GROUP BY smh.company_id,smh.id, smh.shareholderType ,c.nominalShares,c.bearerShares
	HAVING SUM(smh.numberOfShares) <> 0;
GO
/****** Object:  Table [dbo].[additional_id_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[additional_id_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[code] [nvarchar](255) NOT NULL,
	[lang_msg] [nvarchar](255) NOT NULL,
	[module] [nvarchar](50) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_email_template_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_email_template_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[advisor_email_template_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[static_content] [nvarchar](max) NOT NULL,
	[content] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_email_templates]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_email_templates](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](50) NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_permissions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_permissions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NULL,
	[workflow_status_transition_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_task_comments]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_task_comments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[advisor_task_id] [bigint] NOT NULL,
	[comment] [nvarchar](max) NOT NULL,
	[edited] [tinyint] NOT NULL,
	[createdOn] [smalldatetime] NOT NULL,
	[createdBy] [bigint] NOT NULL,
	[createdByChannel] [nvarchar](3) NULL,
	[modifiedOn] [smalldatetime] NOT NULL,
	[modifiedBy] [bigint] NOT NULL,
	[modifiedByChannel] [nvarchar](3) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_task_locations]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_task_locations](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_task_sharedwith_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_task_sharedwith_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[advisor_task_id] [bigint] NOT NULL,
	[advisor_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_task_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_task_statuses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[category] [nvarchar](255) NOT NULL,
	[isGlobal] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_task_type_workflows]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_task_type_workflows](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[advisor_task_workflow_id] [bigint] NULL,
	[advisor_task_type_id] [bigint] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_task_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_task_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_task_types_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_task_types_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[advisor_task_type_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_task_workflow_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_task_workflow_statuses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[advisor_task_workflow_id] [bigint] NOT NULL,
	[advisor_task_status_id] [bigint] NOT NULL,
	[start_point] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_task_workflows]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_task_workflows](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[type] [nvarchar](255) NOT NULL,
	[createdBy] [bigint] NULL,
	[createdOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_task_workflows_permissions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_task_workflows_permissions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[advisor_task_workflow_id] [bigint] NULL,
	[advisor_task_workflow_status_transition_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_tasks]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_tasks](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_id] [bigint] NULL,
	[stage] [bigint] NULL,
	[advisor_id] [bigint] NOT NULL,
	[assigned_to] [bigint] NOT NULL,
	[due_date] [date] NOT NULL,
	[private] [char](3) NULL,
	[priority] [nvarchar](8) NOT NULL,
	[advisor_task_location_id] [bigint] NULL,
	[description] [nvarchar](max) NULL,
	[advisor_task_status_id] [bigint] NOT NULL,
	[advisor_task_type_id] [bigint] NOT NULL,
	[estimated_effort] [decimal](8, 2) NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[archived] [nvarchar](3) NOT NULL,
	[reporter] [bigint] NULL,
	[workflow] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_timer_time_logs]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_timer_time_logs](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[advisor_timer_id] [bigint] NOT NULL,
	[startDate] [bigint] NOT NULL,
	[endDate] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_timers]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_timers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[advisor_id] [bigint] NOT NULL,
	[advisor_task_id] [bigint] NULL,
	[legal_case_id] [bigint] NULL,
	[time_type_id] [bigint] NULL,
	[comments] [nvarchar](max) NULL,
	[timeStatus] [nvarchar](8) NULL,
	[status] [nvarchar](8) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_user_activity_logs]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_user_activity_logs](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[advisor_id] [bigint] NOT NULL,
	[advisor_task_id] [bigint] NULL,
	[legal_case_id] [bigint] NULL,
	[client_id] [bigint] NULL,
	[time_type_id] [bigint] NULL,
	[logDate] [date] NOT NULL,
	[effectiveEffort] [decimal](8, 2) NOT NULL,
	[comments] [nvarchar](max) NULL,
	[timeStatus] [nvarchar](8) NULL,
	[createdBy] [bigint] NULL,
	[createdOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[advisor_user_preferences]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[advisor_user_preferences](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[advisor_user_id] [bigint] NOT NULL,
	[keyName] [nvarchar](255) NOT NULL,
	[keyValue] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [uk_advisor_user_preferences_1] UNIQUE NONCLUSTERED 
(
	[advisor_user_id] ASC,
	[keyName] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[app_modules]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[app_modules](
	[module] [varchar](255) NOT NULL,
	[controller] [varchar](255) NOT NULL,
	[action] [varchar](255) NOT NULL,
	[alias] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[module] ASC,
	[controller] ASC,
	[action] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[applicable_law]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[applicable_law](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[applicable_law_language]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[applicable_law_language](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[app_law_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[approval]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[approval](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[rank] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[approval_assignee]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[approval_assignee](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[approval_id] [bigint] NOT NULL,
	[users] [text] NULL,
	[user_groups] [text] NULL,
	[is_requester_manager] [tinyint] NULL,
	[is_board_member] [tinyint] NULL,
	[rank] [bigint] NOT NULL,
	[label] [nvarchar](255) NOT NULL,
	[collaborators] [text] NULL,
	[contacts] [text] NULL,
	[is_shareholder] [tinyint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[approval_assignee_bm_role]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[approval_assignee_bm_role](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[assignee_id] [bigint] NOT NULL,
	[role_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[approval_criteria]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[approval_criteria](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[approval_id] [bigint] NOT NULL,
	[field] [nvarchar](255) NOT NULL,
	[operator] [nvarchar](255) NOT NULL,
	[value] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[approval_signature_documents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[approval_signature_documents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[document_id] [bigint] NOT NULL,
	[to_be_approved] [tinyint] NULL,
	[to_be_signed] [tinyint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[assignments]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[assignments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[category] [nvarchar](255) NULL,
	[type] [nvarchar](10) NOT NULL,
	[assigned_team] [bigint] NULL,
	[assignment_rule] [nvarchar](255) NULL,
	[visible_assignee] [tinyint] NOT NULL,
	[visible_assigned_team] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[assignments_relation]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[assignments_relation](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[relation] [bigint] NOT NULL,
	[user_relation] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[audit_log_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[audit_log_details](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[log_id] [bigint] NOT NULL,
	[dataBefor] [text] NOT NULL,
	[dataAfter] [text] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[bill_payments]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[bill_payments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[voucher_header_id] [bigint] NOT NULL,
	[account_id] [bigint] NOT NULL,
	[paymentMethod] [nvarchar](15) NOT NULL,
	[total] [decimal](22, 2) NOT NULL,
	[supplier_account_id] [bigint] NOT NULL,
	[billPaymentTotal] [decimal](22, 2) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[board_member_roles]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[board_member_roles](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[board_members]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[board_members](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NOT NULL,
	[member_id] [bigint] NOT NULL,
	[board_member_role_id] [bigint] NOT NULL,
	[designatedOn] [date] NOT NULL,
	[tillDate] [date] NULL,
	[comments] [text] NULL,
	[permanentRepresentation] [nvarchar](7) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[board_post_filters]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[board_post_filters](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[board_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[field] [nvarchar](255) NOT NULL,
	[operator] [nvarchar](255) NOT NULL,
	[value] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[board_post_filters_user]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[board_post_filters_user](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[board_post_filters_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[board_task_post_filters]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[board_task_post_filters](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[board_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[field] [nvarchar](255) NOT NULL,
	[operator] [nvarchar](255) NOT NULL,
	[value] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[board_task_post_filters_user]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[board_task_post_filters_user](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[board_post_filters_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_closure_recommendation]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_closure_recommendation](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[investigation_officer_recommendation] [nvarchar](250) NULL,
	[date_recommended] [date] NULL,
	[approval_remarks] [nvarchar](250) NULL,
	[approval_date] [date] NULL,
	[approval_status] [nvarchar](50) NULL,
	[approvedBy] [bigint] NULL,
	[createdOn] [datetime] NOT NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
	[recommendation_status] [nvarchar](50) NULL,
 CONSTRAINT [pk_case_closure_recommendation] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_comment_attachments]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_comment_attachments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_comment_id] [bigint] NOT NULL,
	[name] [nvarchar](256) NULL,
	[path] [nvarchar](256) NOT NULL,
	[uploaded] [nvarchar](3) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_comments_emails]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_comments_emails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_comment] [bigint] NOT NULL,
	[email_to] [text] NOT NULL,
	[email_from] [varchar](255) NOT NULL,
	[email_from_name] [varchar](255) NULL,
	[email_date] [smalldatetime] NULL,
	[email_subject] [varchar](255) NULL,
	[email_file] [varchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_configurations]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_configurations](
	[keyName] [varchar](255) NOT NULL,
	[keyValue] [nvarchar](max) NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_document_classifications]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_document_classifications](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_document_classification_id] [bigint] NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_document_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_document_statuses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_document_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_document_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_exhibit_document]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_exhibit_document](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[exhibit_id] [bigint] NOT NULL,
	[document] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_investigation_log]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_investigation_log](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[log_date] [date] NOT NULL,
	[details] [nvarchar](max) NOT NULL,
	[action_taken] [nvarchar](100) NULL,
	[createdBy] [bigint] NULL,
	[createdOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
 CONSTRAINT [pk_case_investigation_log] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_investigation_log_document]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_investigation_log_document](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[investigation_id] [bigint] NOT NULL,
	[document] [bigint] NOT NULL,
 CONSTRAINT [pk_case_investigation_log_document] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_offense_subcategory]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_offense_subcategory](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[offense_type_id] [bigint] NOT NULL,
	[is_active] [bit] NULL,
 CONSTRAINT [pk_case_offense_subcategory] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_case_offense_subcategory_name_type] UNIQUE NONCLUSTERED 
(
	[name] ASC,
	[offense_type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_rate]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_rate](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[organization_id] [bigint] NOT NULL,
	[rate_per_hour] [decimal](10, 2) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_related_contracts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_related_contracts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_id] [bigint] NOT NULL,
	[contract_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[case_types_due_conditions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[case_types_due_conditions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_type_id] [bigint] NOT NULL,
	[client_id] [bigint] NULL,
	[priority] [nvarchar](8) NULL,
	[due_in] [int] NOT NULL,
 CONSTRAINT [PK_case_types_due_conditions] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[ci_sessions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ci_sessions](
	[id] [nvarchar](128) NOT NULL,
	[ip_address] [nvarchar](45) NOT NULL,
	[timestamp] [bigint] NOT NULL,
	[data] [text] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[client_partner_shares]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[client_partner_shares](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[client_id] [bigint] NOT NULL,
	[account_id] [bigint] NOT NULL,
	[percentage] [decimal](22, 2) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[companies_related_contracts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[companies_related_contracts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NOT NULL,
	[contract_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_asset_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_asset_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_assets]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_assets](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[company_asset_type_id] [bigint] NULL,
	[ref] [nvarchar](255) NULL,
	[description] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_auditors]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_auditors](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NOT NULL,
	[auditor_id] [bigint] NOT NULL,
	[auditorType] [nvarchar](16) NULL,
	[designationDate] [date] NULL,
	[expiryDate] [date] NULL,
	[comments] [nvarchar](max) NOT NULL,
	[fees] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_bank_accounts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_bank_accounts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NOT NULL,
	[bankName] [nvarchar](255) NOT NULL,
	[bankFullAddress] [nvarchar](255) NULL,
	[bankPhone] [nvarchar](255) NULL,
	[bankFax] [nvarchar](255) NULL,
	[accountName] [nvarchar](255) NULL,
	[accountCurrency] [nvarchar](255) NULL,
	[accountNb] [nvarchar](255) NULL,
	[swiftCode] [nvarchar](255) NULL,
	[iban] [nvarchar](255) NULL,
	[comments] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_changes]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_changes](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NOT NULL,
	[changes] [text] NOT NULL,
	[user_id] [bigint] NOT NULL,
	[changedOn] [smalldatetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_document_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_document_statuses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_document_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_document_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_documents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_documents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NOT NULL,
	[company_document_status_id] [bigint] NOT NULL,
	[company_document_type_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NULL,
	[path] [nvarchar](255) NULL,
	[pathType] [nvarchar](255) NULL,
	[comments] [nvarchar](max) NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_note_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_note_details](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_note_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[path] [nvarchar](256) NULL,
	[uploaded] [nvarchar](3) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_notes]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_notes](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NOT NULL,
	[note] [ntext] NOT NULL,
	[created_by] [bigint] NOT NULL,
	[modified_by] [bigint] NULL,
	[created_on] [datetime2](0) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_signature_authorities]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_signature_authorities](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NOT NULL,
	[sa_id] [bigint] NOT NULL,
	[sa_type] [nvarchar](20) NOT NULL,
	[authorized_signatory] [nvarchar](max) NULL,
	[kind_of_signature] [nvarchar](max) NOT NULL,
	[joint_signature_with] [nvarchar](max) NULL,
	[sole_signature] [nvarchar](max) NULL,
	[capacity] [nvarchar](max) NULL,
	[term_of_the_authorization] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[company_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[company_users](
	[company_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contact_document_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contact_document_statuses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contact_document_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contact_document_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contact_documents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contact_documents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contact_id] [bigint] NOT NULL,
	[contact_document_status_id] [bigint] NOT NULL,
	[contact_document_type_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NULL,
	[path] [nvarchar](255) NULL,
	[pathType] [nvarchar](255) NULL,
	[comments] [text] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contact_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contact_users](
	[contact_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contacts_related_contracts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contacts_related_contracts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contact_id] [bigint] NOT NULL,
	[contract_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_amendment_history]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_amendment_history](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[amended_on] [datetime] NOT NULL,
	[amended_by] [bigint] NOT NULL,
	[comment] [text] NOT NULL,
	[amended_id] [bigint] NOT NULL,
	[amendment_document_id] [bigint] NULL,
	[amendment_approval_status] [nvarchar](20) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_amendment_history_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_amendment_history_details](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[amendment_history_id] [bigint] NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[field_name] [nvarchar](50) NOT NULL,
	[old_value] [nvarchar](max) NULL,
	[new_value] [nvarchar](max) NULL,
	[createdOn] [datetime] NULL,
 CONSTRAINT [pk_contract_amendment_history_details] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_approval_bm_role]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_approval_bm_role](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_approval_status_id] [bigint] NOT NULL,
	[role_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_approval_collaborators]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_approval_collaborators](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_approval_status_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
	[type] [varchar](15) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_approval_contacts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_approval_contacts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_approval_status_id] [bigint] NOT NULL,
	[contact_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_approval_documents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_approval_documents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[document_id] [bigint] NOT NULL,
	[contract_approval_status_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_approval_history]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_approval_history](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[done_by] [bigint] NOT NULL,
	[enforce_previous_approvals] [tinyint] NOT NULL,
	[action] [nvarchar](255) NOT NULL,
	[from_action] [nvarchar](255) NOT NULL,
	[to_action] [nvarchar](255) NOT NULL,
	[comment] [text] NULL,
	[done_on] [datetime] NULL,
	[label] [nvarchar](255) NOT NULL,
	[done_by_type] [nvarchar](25) NOT NULL,
	[signature_id] [bigint] NULL,
	[done_by_ip] [nvarchar](50) NULL,
	[approval_channel] [nvarchar](50) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_approval_negotiation]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_approval_negotiation](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_approval_status_id] [bigint] NOT NULL,
	[done_by] [bigint] NOT NULL,
	[done_by_type] [nvarchar](25) NOT NULL,
	[done_on] [datetime] NULL,
	[status] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_approval_negotiation_comments]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_approval_negotiation_comments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[negotiation_id] [bigint] NOT NULL,
	[done_by] [bigint] NOT NULL,
	[done_by_type] [nvarchar](25) NOT NULL,
	[done_on] [datetime] NULL,
	[comment] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_approval_signature_configuration]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_approval_signature_configuration](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type] [varchar](10) NOT NULL,
	[include_no_status] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_approval_signature_status]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_approval_signature_status](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[configuration_id] [bigint] NOT NULL,
	[status_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_approval_status]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_approval_status](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[is_requester_manager] [tinyint] NULL,
	[is_board_member] [tinyint] NULL,
	[party_id] [bigint] NULL,
	[rank] [bigint] NOT NULL,
	[label] [nvarchar](255) NOT NULL,
	[status] [nvarchar](255) NOT NULL,
	[summary] [text] NULL,
	[is_shareholder] [tinyint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_approval_submission]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_approval_submission](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[status] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_approval_user_groups]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_approval_user_groups](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_approval_status_id] [bigint] NOT NULL,
	[user_group_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_approval_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_approval_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_approval_status_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_board_column_options]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_board_column_options](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[board_column_id] [bigint] NOT NULL,
	[status_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_board_columns]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_board_columns](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[board_id] [bigint] NOT NULL,
	[column_order] [tinyint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[color] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_board_grid_saved_filters_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_board_grid_saved_filters_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[filter_id] [bigint] NULL,
	[user_id] [bigint] NULL,
	[board_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_board_post_filters]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_board_post_filters](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[board_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[field] [nvarchar](255) NOT NULL,
	[operator] [nvarchar](255) NOT NULL,
	[value] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_board_post_filters_user]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_board_post_filters_user](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[board_post_filters_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_boards]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_boards](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_category]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_category](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_category_language]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_category_language](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[category_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_clause]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_clause](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[reference] [nvarchar](255) NULL,
	[label] [text] NULL,
	[iso_language_id] [bigint] NULL,
	[content] [text] NOT NULL,
	[private] [tinyint] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_clause_editor]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_clause_editor](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_clause_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_clause_type]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_clause_type](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_clause_id] [bigint] NOT NULL,
	[type_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_clause_user]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_clause_user](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_clause_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_collaborators]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_collaborators](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_comment]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_comment](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NULL,
	[comment] [text] NOT NULL,
	[edited] [tinyint] NOT NULL,
	[channel] [char](3) NULL,
	[modifiedByChannel] [char](3) NULL,
	[visible_to_cp] [tinyint] NULL,
	[createdOn] [datetime2](0) NOT NULL,
	[createdBy] [bigint] NOT NULL,
	[modifiedOn] [smalldatetime] NOT NULL,
	[modifiedBy] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_comments_emails]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_comments_emails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_comment] [bigint] NOT NULL,
	[email_to] [varchar](255) NOT NULL,
	[email_from] [varchar](255) NOT NULL,
	[email_from_name] [varchar](255) NULL,
	[email_date] [smalldatetime] NULL,
	[email_subject] [varchar](255) NULL,
	[email_file] [varchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_contributors]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_contributors](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_cp_screen_field_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_cp_screen_field_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[screen_field_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[labelName] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_cp_screen_fields]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_cp_screen_fields](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[screen_id] [bigint] NOT NULL,
	[related_field] [nvarchar](255) NOT NULL,
	[isRequired] [tinyint] NOT NULL,
	[visible] [tinyint] NOT NULL,
	[requiredDefaultValue] [nvarchar](255) NULL,
	[fieldDescription] [nvarchar](255) NULL,
	[sortOrder] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_cp_screens]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_cp_screens](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type_id] [bigint] NOT NULL,
	[sub_type_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [text] NULL,
	[showInPortal] [char](1) NULL,
	[contract_request_type_category_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_document_status]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_document_status](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_document_status_language]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_document_status_language](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[status_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_document_type]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_document_type](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_document_type_language]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_document_type_language](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_milestone_documents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_milestone_documents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[document_id] [bigint] NOT NULL,
	[milestone_id] [bigint] NOT NULL,
 CONSTRAINT [pk_contract_milestone_documents] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_numbering_formats]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_numbering_formats](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[name] [varchar](100) NOT NULL,
	[description] [varchar](255) NULL,
	[pattern] [varchar](100) NOT NULL,
	[example] [varchar](100) NOT NULL,
	[prefix] [varchar](20) NOT NULL,
	[suffix] [varchar](20) NULL,
	[fixed_code] [varchar](20) NULL,
	[sequence_reset] [varchar](20) NOT NULL,
	[sequence_length] [int] NOT NULL,
	[is_active] [bit] NOT NULL,
	[last_sequence] [int] NOT NULL,
	[last_reset_date] [date] NULL,
	[created_at] [datetime2](7) NULL,
 CONSTRAINT [pk_contract_numbering_formats] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_parties_sla]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_parties_sla](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[sla_management_id] [bigint] NOT NULL,
	[party_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_party]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_party](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[party_id] [bigint] NOT NULL,
	[party_member_type] [nvarchar](255) NULL,
	[party_category_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_renewal_history]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_renewal_history](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[renewed_on] [smalldatetime] NOT NULL,
	[renewed_by] [bigint] NOT NULL,
	[comment] [text] NOT NULL,
	[renewal_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_renewal_notification_assigned_teams]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_renewal_notification_assigned_teams](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[assigned_team] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_renewal_notification_emails]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_renewal_notification_emails](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[emails] [text] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_request_type_categories]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_request_type_categories](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [varchar](255) NOT NULL,
	[description] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_signature_bm_role]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_signature_bm_role](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_signature_status_id] [bigint] NOT NULL,
	[role_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_signature_collaborators]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_signature_collaborators](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_signature_status_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
	[type] [varchar](15) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_signature_contacts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_signature_contacts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_signature_status_id] [bigint] NOT NULL,
	[contact_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_signature_history]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_signature_history](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[done_by] [bigint] NOT NULL,
	[action] [nvarchar](255) NOT NULL,
	[from_action] [nvarchar](255) NOT NULL,
	[to_action] [nvarchar](255) NOT NULL,
	[comment] [text] NULL,
	[done_on] [datetime] NULL,
	[label] [nvarchar](255) NOT NULL,
	[done_by_type] [nvarchar](25) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_signature_status]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_signature_status](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[is_requester_manager] [tinyint] NULL,
	[is_board_member] [tinyint] NULL,
	[party_id] [bigint] NULL,
	[rank] [bigint] NOT NULL,
	[label] [nvarchar](255) NOT NULL,
	[status] [nvarchar](255) NOT NULL,
	[summary] [text] NULL,
	[is_shareholder] [tinyint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_signature_submission]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_signature_submission](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[status] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_signature_user_groups]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_signature_user_groups](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_signature_status_id] [bigint] NOT NULL,
	[user_group_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_signature_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_signature_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_signature_status_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_signed_document]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_signed_document](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[document_id] [bigint] NOT NULL,
	[contract_signature_status_id] [bigint] NOT NULL,
	[signed_on] [smalldatetime] NOT NULL,
	[signed_by] [bigint] NOT NULL,
	[signed_by_type] [nvarchar](25) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_sla_management]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_sla_management](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[target] [nvarchar](255) NOT NULL,
	[priority] [nvarchar](8) NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_sla_notification]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_sla_notification](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[sla_management_id] [bigint] NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[notified] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_status]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_status](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[category_id] [bigint] NOT NULL,
	[is_global] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_status_language]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_status_language](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[status_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[responsible_user_roles] [nvarchar](100) NULL,
	[step_icon] [nvarchar](50) NULL,
	[activity] [nvarchar](max) NULL,
	[step_input] [nvarchar](255) NULL,
	[step_output] [nvarchar](250) NULL,
	[description] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_template_groups]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_template_groups](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[page_id] [bigint] NOT NULL,
	[title] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_template_pages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_template_pages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[template_id] [bigint] NOT NULL,
	[title] [nvarchar](255) NOT NULL,
	[description] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_template_variables]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_template_variables](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[group_id] [bigint] NOT NULL,
	[variable_property] [nvarchar](20) NOT NULL,
	[property_details] [nvarchar](20) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[is_required] [tinyint] NOT NULL,
	[question] [nvarchar](255) NOT NULL,
	[property_data] [text] NULL,
	[description] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_templates]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_templates](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type_id] [bigint] NOT NULL,
	[sub_type_id] [bigint] NULL,
	[name] [nvarchar](255) NOT NULL,
	[status] [nvarchar](255) NOT NULL,
	[document_id] [bigint] NOT NULL,
	[show_in_cp] [tinyint] NOT NULL,
	[createdBy] [bigint] NULL,
	[createdOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_type]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_type](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_type_language]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_type_language](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[applies_to] [nchar](10) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_url]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_url](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[document_type_id] [bigint] NOT NULL,
	[document_status_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NULL,
	[path] [nvarchar](255) NULL,
	[path_type] [nvarchar](255) NULL,
	[comments] [text] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_workflow]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_workflow](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[category] [nvarchar](255) NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_workflow_per_type]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_workflow_per_type](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[type_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_workflow_status_relation]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_workflow_status_relation](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[status_id] [bigint] NOT NULL,
	[start_point] [tinyint] NOT NULL,
	[approval_start_point] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_workflow_status_transition]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_workflow_status_transition](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[from_step] [bigint] NOT NULL,
	[to_step] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[comment] [text] NULL,
	[approval_needed] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_workflow_status_transition_log]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_workflow_status_transition_log](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[transition_id] [bigint] NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[remarks] [nvarchar](max) NULL,
	[doneBy] [bigint] NOT NULL,
	[doneOn] [datetime] NOT NULL,
	[status] [nvarchar](50) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_workflow_status_transition_permission]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_workflow_status_transition_permission](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[transition_id] [bigint] NOT NULL,
	[users] [text] NULL,
	[user_groups] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_workflow_status_transition_screen_field]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_workflow_status_transition_screen_field](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[transition_id] [bigint] NOT NULL,
	[data] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_workflow_step_checklist]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_workflow_step_checklist](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[step_id] [bigint] NOT NULL,
	[item_text] [nvarchar](250) NOT NULL,
	[input_type] [varchar](50) NOT NULL,
	[is_required] [bit] NOT NULL,
	[sort_order] [bigint] NOT NULL,
 CONSTRAINT [pk_checklist] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_contract_workflow_step_checklist_step_item] UNIQUE NONCLUSTERED 
(
	[step_id] ASC,
	[item_text] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_workflow_step_functions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_workflow_step_functions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[step_id] [bigint] NOT NULL,
	[function_name] [varchar](100) NOT NULL,
	[label] [varchar](255) NOT NULL,
	[icon_class] [varchar](100) NOT NULL,
	[sort_order] [bigint] NOT NULL,
	[data_action] [varchar](100) NULL,
	[created_at] [datetime] NOT NULL,
 CONSTRAINT [pk_functions] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_contract_workflow_step_functions_step_function] UNIQUE NONCLUSTERED 
(
	[step_id] ASC,
	[function_name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contract_workflow_steps_log]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contract_workflow_steps_log](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[step_id] [bigint] NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[user_id] [bigint] NULL,
	[action_type] [nvarchar](100) NOT NULL,
	[action_type_id] [bigint] NOT NULL,
	[details] [nvarchar](max) NULL,
	[createdBy] [bigint] NOT NULL,
	[createdOn] [datetime2](7) NULL,
 CONSTRAINT [pk_workflow_steps_log] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contracts_sla]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contracts_sla](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[sla_management_id] [bigint] NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[cycle] [bigint] NULL,
	[action] [nvarchar](255) NULL,
	[actionDate] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedByChannel] [nvarchar](3) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contracts_sla_actions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contracts_sla_actions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[sla_management_id] [bigint] NOT NULL,
	[status_id] [bigint] NOT NULL,
	[type] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[contracts_type_sla]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[contracts_type_sla](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[sla_management_id] [bigint] NOT NULL,
	[type_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[conveyancing_activity]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[conveyancing_activity](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[conveyancing_instrument_id] [bigint] NULL,
	[activity_type_id] [bigint] NULL,
	[action] [nvarchar](50) NOT NULL,
	[activity_details] [nvarchar](max) NULL,
	[activity_status] [nvarchar](50) NULL,
	[modifiedOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
	[createdOn] [datetime] NOT NULL,
	[createdBy] [bigint] NOT NULL,
	[createdByChannel] [nvarchar](3) NULL,
 CONSTRAINT [pk_conveyancing_activity] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[conveyancing_activity_type]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[conveyancing_activity_type](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](max) NULL,
	[createdOn] [datetime] NOT NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
 CONSTRAINT [pk_conveyancing_activity_type] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_activity_type_name] UNIQUE NONCLUSTERED 
(
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[conveyancing_document_status]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[conveyancing_document_status](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[addedon] [datetime] NOT NULL,
 CONSTRAINT [pk_conveyancing_document_status] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_document_status_name] UNIQUE NONCLUSTERED 
(
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[conveyancing_document_type]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[conveyancing_document_type](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[addedOn] [datetime] NOT NULL,
	[modifiedOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedBy] [bigint] NULL,
 CONSTRAINT [pk_conveyancing_document_type] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_document_type_name] UNIQUE NONCLUSTERED 
(
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[conveyancing_instrument_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[conveyancing_instrument_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[applies_to] [nvarchar](15) NULL,
	[createdOn] [datetime] NOT NULL,
	[modifiedOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedBy] [bigint] NULL,
 CONSTRAINT [pk_conveyancing_instrument_types] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_instrument_name_applies] UNIQUE NONCLUSTERED 
(
	[name] ASC,
	[applies_to] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[conveyancing_instruments]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[conveyancing_instruments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[title] [nvarchar](255) NOT NULL,
	[instrument_type_id] [bigint] NOT NULL,
	[transaction_type_id] [bigint] NULL,
	[reference_number] [nvarchar](50) NULL,
	[parties] [nvarchar](500) NOT NULL,
	[initiated_by] [bigint] NULL,
	[assignee_id] [bigint] NULL,
	[staff_pf_no] [nvarchar](30) NULL,
	[date_initiated] [date] NOT NULL,
	[due_date] [date] NULL,
	[description] [nvarchar](max) NOT NULL,
	[external_counsel_id] [bigint] NULL,
	[property_value] [decimal](22, 2) NULL,
	[amount_requested] [decimal](22, 2) NULL,
	[amount_approved] [decimal](22, 2) NULL,
	[createdOn] [datetime] NOT NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
	[channel] [nvarchar](10) NULL,
	[visible_to_CP] [bit] NOT NULL,
	[date_received] [date] NULL,
	[status_id] [bigint] NULL,
	[assignee_team_id] [bigint] NULL,
	[current_stage_id] [bigint] NULL,
	[priority] [tinyint] NULL,
	[completion_date] [date] NULL,
	[archived] [nvarchar](3) NOT NULL,
	[status] [nvarchar](20) NULL,
	[parties_id] [bigint] NULL,
	[contact_type] [nvarchar](20) NULL,
	[transaction_type] [nvarchar](50) NULL,
 CONSTRAINT [pk_conveyancing_instruments] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_conveyancing_reference] UNIQUE NONCLUSTERED 
(
	[reference_number] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[conveyancing_process_stages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[conveyancing_process_stages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](100) NOT NULL,
	[description] [nvarchar](500) NULL,
	[sequence_order] [int] NOT NULL,
	[is_active] [bit] NOT NULL,
	[created_at] [datetime] NOT NULL,
	[updated_at] [datetime] NOT NULL,
 CONSTRAINT [pk_conveyancing_process_stages] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[conveyancing_stage_progress]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[conveyancing_stage_progress](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[instrument_id] [bigint] NOT NULL,
	[stage_id] [bigint] NOT NULL,
	[status] [nvarchar](20) NOT NULL,
	[start_date] [datetime] NULL,
	[completion_date] [datetime] NULL,
	[updated_by] [bigint] NOT NULL,
	[updated_on] [datetime] NOT NULL,
	[comments] [nvarchar](max) NULL,
 CONSTRAINT [pk_conveyancing_stage_progress] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[conveyancing_transaction_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[conveyancing_transaction_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[applies_to] [nvarchar](15) NULL,
	[createdOn] [datetime] NOT NULL,
	[modifiedOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedBy] [bigint] NULL,
 CONSTRAINT [pk_conveyancing_transaction_types] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_transaction_name_applies] UNIQUE NONCLUSTERED 
(
	[name] ASC,
	[applies_to] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[correspondence_activity_log]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[correspondence_activity_log](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[correspondence_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
	[action] [nvarchar](100) NOT NULL,
	[details] [nvarchar](max) NULL,
	[createdOn] [datetime] NOT NULL,
	[modifiedOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedBy] [bigint] NULL,
 CONSTRAINT [pk_correspondence_activity_log] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[correspondence_document]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[correspondence_document](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[size] [bigint] NULL,
	[extension] [nvarchar](20) NULL,
	[correspondence_id] [bigint] NOT NULL,
	[document_type_id] [bigint] NULL,
	[document_status_id] [bigint] NULL,
	[comments] [nvarchar](max) NULL,
	[createdOn] [datetime] NOT NULL,
	[modifiedOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedBy] [bigint] NULL,
 CONSTRAINT [pk_correspondence_document] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[correspondence_document_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[correspondence_document_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](max) NULL,
	[createdOn] [datetime] NOT NULL,
	[modifiedOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedBy] [bigint] NULL,
 CONSTRAINT [pk_correspondence_document_types] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[correspondence_relationships]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[correspondence_relationships](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[correspondence_id1] [bigint] NOT NULL,
	[correspondence_id2] [bigint] NOT NULL,
	[comments] [nvarchar](255) NULL,
	[createdBy] [bigint] NULL,
	[createdOn] [datetime] NOT NULL,
 CONSTRAINT [pk_correspondence_relationships] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[correspondence_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[correspondence_statuses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[createdOn] [datetime] NOT NULL,
	[modifiedOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedBy] [bigint] NULL,
 CONSTRAINT [pk_correspondence_statuses] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[correspondence_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[correspondence_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[createdOn] [datetime] NOT NULL,
	[modifiedOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedBy] [bigint] NULL,
 CONSTRAINT [pk_correspondence_types] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[correspondence_workflow]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[correspondence_workflow](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[correspondence_id] [bigint] NOT NULL,
	[workflow_step_id] [bigint] NOT NULL,
	[status] [nvarchar](50) NOT NULL,
	[createdOn] [datetime] NOT NULL,
	[modifiedOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedBy] [bigint] NULL,
	[comments] [nvarchar](max) NULL,
	[completion_date] [datetime] NULL,
 CONSTRAINT [pk_correspondence_workflow] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[correspondence_workflow_steps]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[correspondence_workflow_steps](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[correspondence_type_id] [bigint] NOT NULL,
	[sequence_order] [int] NOT NULL,
	[comment] [nvarchar](max) NULL,
	[createdOn] [datetime] NOT NULL,
	[modifiedOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedBy] [bigint] NULL,
	[category] [nvarchar](50) NULL,
 CONSTRAINT [pk_correspondence_workflow_steps] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[correspondences]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[correspondences](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[correspondence_type_id] [bigint] NOT NULL,
	[sender] [bigint] NULL,
	[recipient] [bigint] NULL,
	[subject] [nvarchar](255) NOT NULL,
	[body] [nvarchar](max) NULL,
	[date_received] [datetime] NULL,
	[document_date] [datetime] NULL,
	[reference_number] [nvarchar](255) NULL,
	[status_id] [bigint] NOT NULL,
	[assigned_to] [bigint] NULL,
	[filename] [nvarchar](255) NULL,
	[comments] [nvarchar](max) NULL,
	[createdOn] [datetime] NOT NULL,
	[modifiedOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedBy] [bigint] NULL,
	[document_id] [bigint] NULL,
	[action_required] [nvarchar](50) NULL,
	[priority] [nvarchar](20) NULL,
	[requires_signature] [varchar](3) NOT NULL,
	[mode_of_dispatch] [nvarchar](50) NULL,
	[assignee_team_id] [bigint] NULL,
	[category] [nvarchar](50) NULL,
	[related_to_object] [nvarchar](50) NULL,
	[related_to_object_id] [bigint] NULL,
	[sender_contact_type] [nvarchar](10) NULL,
	[recipient_contact_type] [nvarchar](10) NULL,
	[date_dispatched] [datetime] NULL,
	[due_date] [datetime] NULL,
	[mode_of_receipt] [nvarchar](50) NULL,
	[document_type_id] [bigint] NULL,
 CONSTRAINT [pk_correspondences] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_correspondences_reference_number] UNIQUE NONCLUSTERED 
(
	[reference_number] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[cp_user_preferences]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[cp_user_preferences](
	[cp_user_id] [bigint] NOT NULL,
	[keyName] [varchar](255) NOT NULL,
	[keyValue] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[cp_user_id] ASC,
	[keyName] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[cp_user_signature_attachments]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[cp_user_signature_attachments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[label] [nvarchar](255) NULL,
	[signature] [nvarchar](255) NULL,
	[type] [nvarchar](10) NOT NULL,
	[is_default] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[credit_note_item_commissions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[credit_note_item_commissions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[credit_note_header_id] [bigint] NOT NULL,
	[credit_note_details_id] [bigint] NOT NULL,
	[item_id] [bigint] NULL,
	[expense_id] [bigint] NULL,
	[time_logs_id] [bigint] NULL,
	[account_id] [bigint] NOT NULL,
	[commission_percent] [decimal](15, 12) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[credit_note_reasons]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[credit_note_reasons](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[fl1name] [nvarchar](255) NULL,
	[fl2name] [nvarchar](255) NULL,
	[is_debit_note] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[credit_note_time_logs_items]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[credit_note_time_logs_items](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[credit_note_details_id] [bigint] NOT NULL,
	[time_log_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
	[date] [date] NOT NULL,
	[description] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[criminal_case_details]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[criminal_case_details](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[origin_of_case] [nvarchar](255) NOT NULL,
	[offence_subcategory_id] [bigint] NULL,
	[status_of_case] [nvarchar](100) NOT NULL,
	[initial_entry_document_id] [bigint] NULL,
	[authorization_document_id] [bigint] NULL,
	[date_investigation_authorized] [date] NULL,
	[police_station_reported] [nvarchar](50) NULL,
	[police_station_ob_number] [nvarchar](20) NULL,
	[police_case_file_number] [nvarchar](30) NULL,
 CONSTRAINT [pk_criminal_case_details] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[custom_field_values]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[custom_field_values](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[custom_field_id] [bigint] NOT NULL,
	[recordId] [bigint] NOT NULL,
	[text_value] [nvarchar](max) NULL,
	[date_value] [date] NULL,
	[time_value] [time](0) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[custom_fields]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[custom_fields](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[model] [nvarchar](30) NOT NULL,
	[type] [nvarchar](30) NOT NULL,
	[type_data] [nvarchar](max) NULL,
	[field_order] [bigint] NULL,
	[category] [nvarchar](50) NULL,
	[cp_visible] [tinyint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[custom_fields_case_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[custom_fields_case_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[custom_field_id] [bigint] NOT NULL,
	[type_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[custom_fields_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[custom_fields_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[custom_field_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[customName] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[custom_fields_per_model_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[custom_fields_per_model_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[custom_field_id] [bigint] NOT NULL,
	[type_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[customer_portal_container_watchers]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[customer_portal_container_watchers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_container_id] [bigint] NOT NULL,
	[customer_portal_user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[customer_portal_contract_permissions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[customer_portal_contract_permissions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[workflow_status_transition_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[customer_portal_contract_watchers]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[customer_portal_contract_watchers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[customer_portal_user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[customer_portal_permissions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[customer_portal_permissions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[workflow_status_transition_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[customer_portal_screen_field_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[customer_portal_screen_field_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[customer_portal_screen_field_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[labelName] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[customer_portal_screen_fields]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[customer_portal_screen_fields](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[customer_portal_screen_id] [bigint] NOT NULL,
	[relatedCaseField] [nvarchar](255) NOT NULL,
	[isRequired] [tinyint] NOT NULL,
	[visible] [tinyint] NOT NULL,
	[requiredDefaultValue] [nvarchar](255) NULL,
	[fieldDescription] [nvarchar](255) NULL,
	[sortOrder] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[customer_portal_screens]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[customer_portal_screens](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_type_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [text] NULL,
	[showInPortal] [char](1) NULL,
	[applicable_on] [varchar](255) NOT NULL,
	[request_type_category_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[customer_portal_sla]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[customer_portal_sla](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[target] [nvarchar](255) NOT NULL,
	[start] [nvarchar](255) NOT NULL,
	[pause] [nvarchar](255) NULL,
	[stop] [nvarchar](255) NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[priority] [nvarchar](8) NULL,
	[case_type_id] [bigint] NULL,
	[client_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[customer_portal_sla_cases]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[customer_portal_sla_cases](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[customer_portal_sla_id] [bigint] NOT NULL,
	[cycle] [bigint] NULL,
	[case_id] [bigint] NOT NULL,
	[action] [nvarchar](255) NULL,
	[actionDate] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedByChannel] [nvarchar](3) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[customer_portal_sla_notification]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[customer_portal_sla_notification](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[sla_id] [bigint] NOT NULL,
	[case_id] [bigint] NOT NULL,
	[notified] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[customer_portal_ticket_watchers]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[customer_portal_ticket_watchers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_id] [bigint] NOT NULL,
	[customer_portal_user_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[customer_portal_users_assignments]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[customer_portal_users_assignments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[screen] [bigint] NOT NULL,
	[user_relation] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[departments]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[departments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](100) NOT NULL,
 CONSTRAINT [pk_departments] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[discounts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[discounts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[fl1name] [nvarchar](255) NULL,
	[fl2name] [nvarchar](255) NULL,
	[description] [text] NULL,
	[percentage] [decimal](10, 4) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[docs_document_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[docs_document_statuses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[docs_document_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[docs_document_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[docs_documents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[docs_documents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[docs_document_status_id] [bigint] NOT NULL,
	[docs_document_type_id] [bigint] NOT NULL,
	[name] [varchar](255) NULL,
	[path] [varchar](255) NULL,
	[pathType] [varchar](255) NULL,
	[comments] [text] NULL,
	[createdOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[document_generator]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[document_generator](
	[keyName] [varchar](255) NOT NULL,
	[keyValue] [nvarchar](max) NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[document_managment_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[document_managment_users](
	[recordId] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[documents_management_system]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[documents_management_system](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type] [nvarchar](6) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[extension] [nvarchar](6) NULL,
	[parent] [bigint] NULL,
	[lineage] [nvarchar](max) NULL,
	[size] [bigint] NULL,
	[version] [bigint] NULL,
	[private] [tinyint] NULL,
	[document_type_id] [bigint] NULL,
	[document_status_id] [bigint] NULL,
	[comment] [nvarchar](max) NULL,
	[module] [nvarchar](255) NOT NULL,
	[module_record_id] [bigint] NULL,
	[system_document] [tinyint] NOT NULL,
	[visible] [tinyint] NOT NULL,
	[visible_in_cp] [tinyint] NOT NULL,
	[visible_in_ap] [tinyint] NOT NULL,
	[createdOn] [smalldatetime] NOT NULL,
	[createdBy] [bigint] NOT NULL,
	[createdByChannel] [nvarchar](3) NOT NULL,
	[initial_version_created_on] [smalldatetime] NULL,
	[initial_version_created_by] [bigint] NULL,
	[initial_version_created_by_channel] [nvarchar](3) NULL,
	[modifiedOn] [smalldatetime] NOT NULL,
	[modifiedBy] [bigint] NOT NULL,
	[modifiedByChannel] [nvarchar](3) NOT NULL,
	[is_locked] [tinyint] NULL,
	[last_locked_by] [bigint] NULL,
	[last_locked_by_channel] [nvarchar](3) NULL,
	[last_locked_on] [smalldatetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[email_notifications_scheme]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[email_notifications_scheme](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[trigger_action] [nvarchar](255) NOT NULL,
	[notify_to] [text] NOT NULL,
	[notify_cc] [text] NOT NULL,
	[createdBy] [bigint] NULL,
	[createdOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[hide_show_send_email_notification] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[email_templates]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[email_templates](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[template_key] [nvarchar](100) NOT NULL,
	[template_name] [nvarchar](255) NOT NULL,
	[subject] [nvarchar](255) NULL,
	[body_content] [nvarchar](max) NOT NULL,
	[is_active] [bit] NOT NULL,
	[variable_count] [int] NOT NULL,
	[last_modified_by] [int] NULL,
	[updated_at] [datetime2](0) NOT NULL,
 CONSTRAINT [PK_email_templates] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_email_templates_key] UNIQUE NONCLUSTERED 
(
	[template_key] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[event_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[event_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[event_types_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[event_types_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[event_type_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[events]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[events](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_id] [bigint] NULL,
	[start_date] [date] NOT NULL,
	[start_time] [time](0) NOT NULL,
	[end_date] [date] NOT NULL,
	[end_time] [time](0) NOT NULL,
	[private] [char](3) NULL,
	[priority] [nvarchar](8) NULL,
	[task_location_id] [bigint] NULL,
	[title] [nvarchar](255) NOT NULL,
	[description] [text] NULL,
	[calendar_id] [nvarchar](255) NULL,
	[integration_id] [nvarchar](255) NULL,
	[integration_type] [nvarchar](255) NULL,
	[event_type_id] [bigint] NULL,
	[created_from] [nvarchar](255) NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[events_attendees]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[events_attendees](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[event_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
	[mandatory] [tinyint] NULL,
	[participant] [tinyint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[exchange_rates]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[exchange_rates](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[currency_id] [bigint] NULL,
	[organization_id] [bigint] NULL,
	[rate] [decimal](22, 10) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[exhibit]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[exhibit](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[exhibit_label] [nvarchar](255) NOT NULL,
	[description] [nvarchar](max) NOT NULL,
	[temporary_removals] [nvarchar](max) NULL,
	[manner_of_disposal] [nvarchar](max) NULL,
	[date_received] [date] NOT NULL,
	[date_approved_for_disposal] [date] NULL,
	[date_disposed] [date] NULL,
	[createdOn] [datetime] NOT NULL,
	[modifiedOn] [datetime] NULL,
	[createdBy] [bigint] NOT NULL,
	[modifiedBy] [bigint] NULL,
	[associated_party_type] [nvarchar](10) NULL,
	[exhibit_status] [nvarchar](250) NULL,
	[officer_remarks] [nvarchar](max) NULL,
	[officers_involved_id] [bigint] NULL,
	[associated_party] [bigint] NULL,
	[pickup_location_id] [bigint] NULL,
	[current_location_id] [bigint] NULL,
	[reason_for_temporary] [nvarchar](250) NULL,
	[disposal_remarks] [nvarchar](max) NULL,
	[status_on_pickup] [nvarchar](250) NULL,
	[archived] [nvarchar](3) NULL,
 CONSTRAINT [pk_exhibit] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[exhibit_activities_log]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[exhibit_activities_log](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[subject] [varchar](255) NOT NULL,
	[exhibit_id] [bigint] NULL,
	[remarks] [varchar](max) NULL,
	[createdBy] [bigint] NULL,
	[createdOn] [datetime] NULL,
	[requires_followup] [nvarchar](3) NULL,
	[tags] [nvarchar](250) NULL,
	[priority] [nvarchar](10) NULL,
	[note_type] [nvarchar](10) NULL,
	[modifiedOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[exhibit_chain_of_movement]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[exhibit_chain_of_movement](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[transfer_from_id] [bigint] NOT NULL,
	[transfer_to_id] [bigint] NOT NULL,
	[purpose] [nvarchar](255) NULL,
	[remarks] [nvarchar](max) NULL,
	[action_date_time] [datetime] NOT NULL,
	[officer_receiving] [bigint] NULL,
	[createdBy] [bigint] NULL,
	[createdOn] [datetime] NULL,
	[exhibit_id] [bigint] NULL,
	[condition_check] [int] NULL,
	[modifiedOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
 CONSTRAINT [pk_exhibit_chain_of_movement] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[exhibit_document]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[exhibit_document](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[exhibit_id] [bigint] NOT NULL,
	[document] [bigint] NOT NULL,
 CONSTRAINT [pk_exhibit_document] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[exhibit_document_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[exhibit_document_statuses](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](100) NOT NULL,
 CONSTRAINT [pk_exhibit_document_statuses] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[exhibit_document_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[exhibit_document_types](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](100) NOT NULL,
 CONSTRAINT [pk_exhibit_document_types] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[exhibit_locations]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[exhibit_locations](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[longitude] [nvarchar](50) NULL,
	[latitude] [nvarchar](50) NULL,
	[description] [nvarchar](max) NULL,
	[createdOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
 CONSTRAINT [pk_exhibit_locations] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_exhibit_locations_name] UNIQUE NONCLUSTERED 
(
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[expense_status_notes]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[expense_status_notes](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[expense_id] [bigint] NOT NULL,
	[note] [text] NULL,
	[transition] [text] NOT NULL,
	[createdOn] [smalldatetime] NOT NULL,
	[createdBy] [bigint] NOT NULL,
	[modifiedOn] [smalldatetime] NOT NULL,
	[modifiedBy] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[exporter_audit_logs]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[exporter_audit_logs](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[module] [nvarchar](20) NOT NULL,
	[module_id] [nvarchar](15) NOT NULL,
	[exported_data] [text] NULL,
	[created_on] [smalldatetime] NULL,
	[created_by] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[external_approvals]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[external_approvals](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[token_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
	[user_type] [nvarchar](255) NULL,
	[approval_status_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[external_share_documents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[external_share_documents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[token_id] [bigint] NOT NULL,
	[document_id] [bigint] NOT NULL,
	[share_type] [nvarchar](255) NULL,
	[external_user_email] [nvarchar](255) NULL,
	[otp] [nvarchar](255) NULL,
	[otp_generated_on] [datetime] NULL,
	[otp_verification_failed] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[external_user_tokens]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[external_user_tokens](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[external_user_token] [nvarchar](255) NULL,
	[created_on] [datetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[folder_templates]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[folder_templates](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[category] [varchar](50) NULL,
	[type_id] [varchar](255) NULL,
	[folder_key] [varchar](255) NULL,
	[parent_key] [varchar](255) NULL,
	[name] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[grid_saved_board_filters_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[grid_saved_board_filters_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[filter_id] [bigint] NULL,
	[user_id] [bigint] NULL,
	[board_id] [bigint] NOT NULL,
	[is_board] [tinyint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[grid_saved_board_task_filters_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[grid_saved_board_task_filters_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[filter_id] [bigint] NULL,
	[user_id] [bigint] NULL,
	[board_id] [bigint] NOT NULL,
	[is_board] [tinyint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[grid_saved_columns]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[grid_saved_columns](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[model] [nvarchar](255) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[grid_details] [text] NOT NULL,
	[grid_saved_filter_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[grid_saved_filters]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[grid_saved_filters](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[model] [nvarchar](255) NOT NULL,
	[user_id] [bigint] NULL,
	[filterName] [nvarchar](255) NOT NULL,
	[formData] [text] NOT NULL,
	[isGlobalFilter] [tinyint] NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[grid_saved_filters_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[grid_saved_filters_users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[filter_id] [bigint] NULL,
	[user_id] [bigint] NULL,
	[model] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[hearing_outcome_reasons]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[hearing_outcome_reasons](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[hearing_outcome_reasons_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[hearing_outcome_reasons_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[hearing_outcome_reason] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[hearing_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[hearing_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[hearing_types_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[hearing_types_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[hearings_documents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[hearings_documents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[hearing] [bigint] NOT NULL,
	[document] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[instance_data]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[instance_data](
	[keyName] [varchar](255) NOT NULL,
	[keyValue] [nvarchar](max) NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[integrations]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[integrations](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[code] [nvarchar](50) NOT NULL,
	[name] [nvarchar](50) NOT NULL,
	[is_active] [tinyint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[invoice_detail_cover_page_template]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[invoice_detail_cover_page_template](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[organization_id] [bigint] NOT NULL,
	[name] [varchar](255) NOT NULL,
	[header] [varchar](255) NOT NULL,
	[subHeader] [varchar](255) NOT NULL,
	[footer] [varchar](255) NOT NULL,
	[address] [text] NOT NULL,
	[logo] [varchar](255) NULL,
	[email] [varchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[invoice_detail_look_feel_section]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[invoice_detail_look_feel_section](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [varchar](255) NOT NULL,
	[fl1name] [varchar](255) NOT NULL,
	[fl2name] [varchar](255) NOT NULL,
	[content] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[invoice_notes]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[invoice_notes](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](128) NOT NULL,
	[description] [text] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[invoice_payments]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[invoice_payments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[voucher_header_id] [bigint] NOT NULL,
	[account_id] [bigint] NOT NULL,
	[paymentMethod] [nvarchar](15) NOT NULL,
	[total] [decimal](22, 2) NOT NULL,
	[client_account_id] [bigint] NOT NULL,
	[invoicePaymentTotal] [decimal](22, 2) NOT NULL,
	[exchangeRate] [decimal](22, 10) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[invoice_time_logs_items]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[invoice_time_logs_items](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[item] [bigint] NOT NULL,
	[time_log] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
	[date] [date] NOT NULL,
	[description] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[invoice_transaction_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[invoice_transaction_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[fl1name] [nvarchar](255) NULL,
	[fl2name] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[invoice_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[invoice_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[fl1name] [nvarchar](255) NULL,
	[fl2name] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[ip_petitions_oppositions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ip_petitions_oppositions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type] [bigint] NOT NULL,
	[description] [nvarchar](max) NULL,
	[arrivalDate] [date] NULL,
	[dueDate] [date] NULL,
	[agentId] [bigint] NULL,
	[agentType] [nvarchar](255) NULL,
	[user_id] [bigint] NULL,
	[result] [nvarchar](255) NULL,
	[ip_detail_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[ip_petitions_oppositions_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ip_petitions_oppositions_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[iso_currencies]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[iso_currencies](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[code] [char](4) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[iso_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[iso_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[code] [char](2) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[item_commissions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[item_commissions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[invoice_header_id] [bigint] NOT NULL,
	[invoice_details_id] [bigint] NOT NULL,
	[item_id] [bigint] NULL,
	[sub_item_id] [bigint] NULL,
	[expense_id] [bigint] NULL,
	[time_logs_id] [bigint] NULL,
	[account_id] [bigint] NOT NULL,
	[commission] [decimal](5, 2) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[items]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[items](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[item_id] [bigint] NULL,
	[account_id] [bigint] NOT NULL,
	[tax_id] [bigint] NULL,
	[unitName] [nvarchar](255) NOT NULL,
	[fl1unitName] [nvarchar](255) NULL,
	[fl2unitName] [nvarchar](255) NULL,
	[unitPrice] [decimal](10, 2) NOT NULL,
	[description] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[fullName] [nvarchar](255) NOT NULL,
	[display_name] [text] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_archived_hard_copies]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_archived_hard_copies](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[case_document_classification_id] [bigint] NOT NULL,
	[sub_case_document_classification_id] [bigint] NOT NULL,
	[notes] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_changes]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_changes](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_id] [bigint] NOT NULL,
	[changes] [nvarchar](max) NOT NULL,
	[user_id] [bigint] NULL,
	[modifiedByChannel] [nvarchar](3) NULL,
	[changedOn] [smalldatetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_client_positions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_client_positions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_commissions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_commissions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[account_id] [bigint] NOT NULL,
	[commission] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_container_advanced_export_slots]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_container_advanced_export_slots](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_container_id] [bigint] NOT NULL,
	[slot_name] [nvarchar](max) NOT NULL,
	[slot_data] [nvarchar](max) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_container_document_statuses]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_container_document_statuses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_container_document_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_container_document_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_container_documents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_container_documents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_container_id] [bigint] NOT NULL,
	[legal_case_container_document_status_id] [bigint] NOT NULL,
	[legal_case_container_document_type_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NULL,
	[path] [nvarchar](255) NULL,
	[pathType] [nvarchar](255) NULL,
	[comments] [nvarchar](max) NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_container_related_containers]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_container_related_containers](
	[legal_case_container_id] [bigint] NOT NULL,
	[related_container_id] [bigint] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_documents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_documents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_id] [bigint] NOT NULL,
	[legal_case_document_status_id] [bigint] NOT NULL,
	[legal_case_document_type_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NULL,
	[path] [nvarchar](255) NULL,
	[pathType] [nvarchar](255) NULL,
	[comments] [nvarchar](max) NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_event_data_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_event_data_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_event_data_types_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_event_data_types_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type] [bigint] NOT NULL,
	[type_name] [varchar](255) NOT NULL,
	[type_details] [text] NULL,
	[language_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_event_type_forms]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_event_type_forms](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[event_type] [bigint] NOT NULL,
	[field_type] [bigint] NULL,
	[field_required] [tinyint] NOT NULL,
	[field_order] [bigint] NOT NULL,
	[field_key] [varchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_event_type_forms_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_event_type_forms_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[field] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[field_name] [nvarchar](255) NOT NULL,
	[field_type_details] [text] NULL,
	[field_description] [varchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_event_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_event_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[sub_event] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_event_types_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_event_types_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[event_type] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_folder_templates]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_folder_templates](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[category] [varchar](50) NULL,
	[case_type_id] [varchar](255) NULL,
	[folder_key] [varchar](255) NULL,
	[parent_key] [varchar](255) NULL,
	[name] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_hearing_client_report_history]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_hearing_client_report_history](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_hearing_id] [bigint] NOT NULL,
	[email_data] [nvarchar](max) NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_opponent_position_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_opponent_position_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_opponent_position_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_opponent_positions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_opponent_positions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_outsource_contacts]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_outsource_contacts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_outsource_id] [bigint] NOT NULL,
	[contact_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_partner_shares]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_partner_shares](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[account_id] [bigint] NOT NULL,
	[percentage] [decimal](22, 2) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_stage_changes]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_stage_changes](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_id] [bigint] NULL,
	[legal_case_stage_id] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_success_probabilities]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_success_probabilities](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_success_probability_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_success_probability_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_success_probability_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[legal_case_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[legal_case_users](
	[legal_case_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[litigation_stage_status_history]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[litigation_stage_status_history](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[litigation_stage] [bigint] NOT NULL,
	[status] [bigint] NOT NULL,
	[action_maker] [bigint] NOT NULL,
	[movedOn] [smalldatetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[login_history_log_archives]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[login_history_log_archives](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NULL,
	[userLogin] [nvarchar](255) NULL,
	[action] [nvarchar](6) NOT NULL,
	[source_ip] [nvarchar](45) NOT NULL,
	[log_message] [nvarchar](255) NOT NULL,
	[log_message_status] [nvarchar](255) NOT NULL,
	[logDate] [datetime] NOT NULL,
	[user_agent] [nvarchar](120) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[manage_non_business_days]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[manage_non_business_days](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[targetDate] [date] NULL,
	[comments] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[migrations]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[migrations](
	[version] [bigint] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[model_has_permissions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[model_has_permissions](
	[permission_id] [bigint] NOT NULL,
	[model_type] [nvarchar](191) NOT NULL,
	[model_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[permission_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[model_has_roles]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[model_has_roles](
	[role_id] [bigint] NOT NULL,
	[model_type] [nvarchar](191) NOT NULL,
	[model_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[role_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[module_preferences]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[module_preferences](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[module_name] [nvarchar](50) NOT NULL,
	[module_record_id] [bigint] NOT NULL,
	[integration_id] [bigint] NOT NULL,
	[keyName] [nvarchar](255) NOT NULL,
	[keyValue] [nvarchar](max) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [module_preference_key] UNIQUE NONCLUSTERED 
(
	[module_name] ASC,
	[module_record_id] ASC,
	[integration_id] ASC,
	[keyName] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[money_dashboard_widgets]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[money_dashboard_widgets](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[filter] [nvarchar](max) NULL,
	[widget_order] [int] NOT NULL,
	[money_dashboard_id] [bigint] NOT NULL,
	[money_dashboard_widgets_type_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[money_dashboard_widgets_title_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[money_dashboard_widgets_title_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[widget_id] [bigint] NULL,
	[language_id] [bigint] NULL,
	[title] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[money_dashboard_widgets_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[money_dashboard_widgets_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[type] [nvarchar](255) NOT NULL,
	[settings] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[money_dashboards]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[money_dashboards](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[columns_nb] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[notifications]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[notifications](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[status] [nvarchar](9) NOT NULL,
	[message] [text] NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[oauth_access_tokens]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[oauth_access_tokens](
	[id] [nvarchar](100) NOT NULL,
	[user_id] [bigint] NULL,
	[client_id] [bigint] NOT NULL,
	[name] [nvarchar](191) NULL,
	[scopes] [nvarchar](max) NULL,
	[revoked] [smallint] NOT NULL,
	[created_at] [datetime2](0) NULL,
	[updated_at] [datetime2](0) NULL,
	[expires_at] [datetime2](0) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[oauth_auth_codes]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[oauth_auth_codes](
	[id] [nvarchar](100) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[client_id] [bigint] NOT NULL,
	[scopes] [nvarchar](max) NULL,
	[revoked] [smallint] NOT NULL,
	[expires_at] [datetime2](0) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[oauth_clients]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[oauth_clients](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NULL,
	[name] [nvarchar](191) NOT NULL,
	[secret] [nvarchar](100) NULL,
	[provider] [nvarchar](191) NULL,
	[redirect] [nvarchar](max) NOT NULL,
	[personal_access_client] [smallint] NOT NULL,
	[password_client] [smallint] NOT NULL,
	[revoked] [smallint] NOT NULL,
	[created_at] [datetime2](0) NULL,
	[updated_at] [datetime2](0) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[oauth_personal_access_clients]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[oauth_personal_access_clients](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[client_id] [bigint] NOT NULL,
	[created_at] [datetime2](0) NULL,
	[updated_at] [datetime2](0) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[oauth_refresh_tokens]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[oauth_refresh_tokens](
	[id] [nvarchar](100) NOT NULL,
	[access_token_id] [nvarchar](100) NOT NULL,
	[revoked] [smallint] NOT NULL,
	[expires_at] [datetime2](0) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_comments]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_comments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[opinion_id] [bigint] NOT NULL,
	[comment] [nvarchar](max) NOT NULL,
	[edited] [tinyint] NOT NULL,
	[createdOn] [datetime] NOT NULL,
	[createdBy] [bigint] NOT NULL,
	[modifiedOn] [datetime] NOT NULL,
	[modifiedBy] [bigint] NOT NULL,
	[added_from_channel] [nvarchar](5) NULL,
 CONSTRAINT [pk_opinion_comments] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_document_status]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_document_status](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
 CONSTRAINT [pk_opinion_document_status] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_document_status_language]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_document_status_language](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[status_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
 CONSTRAINT [pk_opinion_document_status_language] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_document_type]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_document_type](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
 CONSTRAINT [pk_opinion_document_type] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_document_type_language]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_document_type_language](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[applies_to] [nvarchar](15) NULL,
 CONSTRAINT [pk_opinion_document_type_language] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
 CONSTRAINT [pk_opinion_types] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_types_languages]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_types_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[opinion_type_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[applies_to] [nvarchar](15) NULL,
 CONSTRAINT [pk_opinion_types_languages] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_url]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_url](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[opinion_id] [bigint] NOT NULL,
	[document_type_id] [bigint] NOT NULL,
	[document_status_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NULL,
	[path] [nvarchar](255) NULL,
	[path_type] [nvarchar](255) NULL,
	[comments] [nvarchar](max) NULL,
	[createdOn] [datetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
 CONSTRAINT [pk_opinion_url] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_users]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_users](
	[opinion_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
 CONSTRAINT [pk_opinion_users] PRIMARY KEY CLUSTERED 
(
	[opinion_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_workflow_status_relation]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_workflow_status_relation](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[status_id] [bigint] NOT NULL,
	[start_point] [tinyint] NOT NULL,
 CONSTRAINT [pk_opinion_workflow_status_relation] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_workflow_status_transition]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_workflow_status_transition](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[from_step] [bigint] NOT NULL,
	[to_step] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[comments] [nvarchar](max) NULL,
 CONSTRAINT [pk_opinion_workflow_status_transition] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_workflow_status_transition_history]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_workflow_status_transition_history](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[opinion_id] [bigint] NOT NULL,
	[from_step] [bigint] NULL,
	[to_step] [bigint] NOT NULL,
	[user_id] [bigint] NULL,
	[changed_on] [datetime] NOT NULL,
 CONSTRAINT [pk_opinion_workflow_status_transition_history] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_workflow_status_transition_permissions]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_workflow_status_transition_permissions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[transition] [bigint] NOT NULL,
	[users] [nvarchar](max) NULL,
	[user_groups] [nvarchar](max) NULL,
 CONSTRAINT [pk_opinion_workflow_status_transition_permissions] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_workflow_status_transition_screen_fields]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_workflow_status_transition_screen_fields](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[transition] [bigint] NOT NULL,
	[data] [nvarchar](max) NULL,
 CONSTRAINT [pk_opinion_workflow_status_transition_screen_fields] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_workflow_types]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_workflow_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[type_id] [bigint] NOT NULL,
 CONSTRAINT [pk_opinion_workflow_types] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinion_workflows]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinion_workflows](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[type] [nvarchar](255) NOT NULL,
	[createdBy] [bigint] NULL,
	[createdOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
 CONSTRAINT [pk_opinion_workflows] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[opinions_documents]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[opinions_documents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[opinion_id] [bigint] NOT NULL,
	[document_id] [bigint] NOT NULL,
 CONSTRAINT [pk_opinions_documents] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[organization_invoice_templates]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[organization_invoice_templates](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[organization_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[settings] [text] NULL,
	[type] [nvarchar](10) NULL,
	[is_default] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[organizations]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[organizations](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[currency_id] [bigint] NOT NULL,
	[color] [bigint] NOT NULL,
	[fiscalYearStartsOn] [tinyint] NOT NULL,
	[address1] [nvarchar](255) NULL,
	[address2] [nvarchar](255) NULL,
	[city] [nvarchar](255) NULL,
	[state] [nvarchar](255) NULL,
	[zip] [nvarchar](32) NULL,
	[country_id] [bigint] NULL,
	[website] [nvarchar](255) NULL,
	[phone] [nvarchar](255) NULL,
	[fax] [nvarchar](255) NULL,
	[tax_number] [nvarchar](255) NULL,
	[mobile] [nvarchar](255) NULL,
	[organizationID] [nvarchar](255) NULL,
	[e_invoicing] [nvarchar](50) NULL,
	[comments] [text] NULL,
	[status] [nvarchar](8) NOT NULL,
	[additional_id_type] [bigint] NULL,
	[additional_id_value] [nvarchar](255) NULL,
	[street_name] [nvarchar](255) NULL,
	[building_number] [nvarchar](255) NULL,
	[address_additional_number] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[partner_settlements_invoices]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[partner_settlements_invoices](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[voucher_header_id] [bigint] NULL,
	[invoice_header_id] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[party]    Script Date: 1/16/2026 12:39:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[party](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NULL,
	[contact_id] [bigint] NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[party_category]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[party_category](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[party_category_language]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[party_category_language](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[category_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[password_reset_token]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[password_reset_token](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[user_type] [tinyint] NOT NULL,
	[token] [nvarchar](255) NOT NULL,
	[used] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[payment_methods]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[payment_methods](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[code] [nvarchar](255) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[fl1name] [nvarchar](255) NULL,
	[fl2name] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[permissions]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[permissions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](191) NOT NULL,
	[guard_name] [nvarchar](191) NOT NULL,
	[created_at] [datetime2](0) NULL,
	[updated_at] [datetime2](0) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[planning_board_column_options]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[planning_board_column_options](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[planning_board_id] [bigint] NULL,
	[planning_board_column_id] [bigint] NOT NULL,
	[case_status_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[planning_board_columns]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[planning_board_columns](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[planning_board_id] [bigint] NOT NULL,
	[columnOrder] [tinyint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[color] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[planning_board_saved_filters]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[planning_board_saved_filters](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[boardId] [bigint] NOT NULL,
	[userId] [bigint] NOT NULL,
	[keyName] [varchar](255) NOT NULL,
	[keyValue] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[planning_boards]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[planning_boards](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[preferred_shares]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[preferred_shares](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[company_id] [bigint] NOT NULL,
	[issueDate] [date] NOT NULL,
	[numberOfShares] [bigint] NOT NULL,
	[series] [nvarchar](9) NOT NULL,
	[retrieved] [nvarchar](3) NOT NULL,
	[comment] [nvarchar](max) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[quote_status_notes]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[quote_status_notes](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[quote_id] [bigint] NOT NULL,
	[note] [text] NULL,
	[transition] [text] NOT NULL,
	[createdOn] [smalldatetime] NOT NULL,
	[createdBy] [bigint] NOT NULL,
	[modifiedOn] [smalldatetime] NOT NULL,
	[modifiedBy] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[quote_time_logs_items]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[quote_time_logs_items](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[item] [bigint] NOT NULL,
	[time_log] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
	[date] [date] NOT NULL,
	[description] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[recurrence]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[recurrence](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[related_id] [bigint] NOT NULL,
	[type_id] [bigint] NOT NULL,
	[stop_date] [date] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[recurring_types]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[recurring_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type] [nvarchar](20) NOT NULL,
	[recurring_period] [nvarchar](10) NOT NULL,
	[max_recurrence] [nvarchar](10) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[related_cases]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[related_cases](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_a_id] [bigint] NOT NULL,
	[case_b_id] [bigint] NOT NULL,
	[comments] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[related_contacts]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[related_contacts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contact_a_id] [bigint] NOT NULL,
	[contact_b_id] [bigint] NOT NULL,
	[comments] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[related_contracts]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[related_contracts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_a_id] [bigint] NOT NULL,
	[contract_b_id] [bigint] NOT NULL,
	[comments] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[reminder_types]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[reminder_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[reminder_types_languages]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[reminder_types_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[reminder_type_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[request_type_categories]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[request_type_categories](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [varchar](255) NOT NULL,
	[description] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[role_has_permissions]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[role_has_permissions](
	[permission_id] [bigint] NOT NULL,
	[role_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[permission_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[roles]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[roles](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](191) NOT NULL,
	[guard_name] [nvarchar](191) NOT NULL,
	[created_at] [datetime2](0) NULL,
	[updated_at] [datetime2](0) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[saml__kvstore]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[saml__kvstore](
	[_type] [varchar](30) NOT NULL,
	[_key] [varchar](50) NOT NULL,
	[_value] [text] NOT NULL,
	[_expire] [datetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[_key] ASC,
	[_type] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[saml__tableVersion]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[saml__tableVersion](
	[_name] [varchar](30) NOT NULL,
	[_version] [int] NOT NULL,
UNIQUE NONCLUSTERED 
(
	[_name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[saml_configuration]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[saml_configuration](
	[keyName] [varchar](255) NOT NULL,
	[keyValue] [nvarchar](max) NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[shared_reports]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[shared_reports](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[report_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[shares_movement_headers]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[shares_movement_headers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[createdOn] [smalldatetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[signature]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[signature](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[rank] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[signature_authorities_documents]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[signature_authorities_documents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[signature_authority] [bigint] NOT NULL,
	[document] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[signature_criteria]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[signature_criteria](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[signature_id] [bigint] NOT NULL,
	[field] [nvarchar](255) NOT NULL,
	[operator] [nvarchar](255) NOT NULL,
	[value] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[signature_signee]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[signature_signee](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[signature_id] [bigint] NOT NULL,
	[users] [text] NULL,
	[user_groups] [text] NULL,
	[is_requester_manager] [tinyint] NULL,
	[is_board_member] [tinyint] NULL,
	[rank] [bigint] NOT NULL,
	[label] [nvarchar](255) NOT NULL,
	[collaborators] [text] NULL,
	[is_shareholder] [tinyint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[signature_signee_bm_role]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[signature_signee_bm_role](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[assignee_id] [bigint] NOT NULL,
	[role_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[stage_statuses]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[stage_statuses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[color] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[stage_statuses_languages]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[stage_statuses_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[status] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[status_category]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[status_category](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type] [nvarchar](255) NOT NULL,
	[color] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[sub_contract_type]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[sub_contract_type](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[sub_contract_type_language]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[sub_contract_type_language](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[sub_type_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[supplier_taxes]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[supplier_taxes](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[account_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[fl1name] [nvarchar](255) NULL,
	[fl2name] [nvarchar](255) NULL,
	[description] [text] NULL,
	[percentage] [decimal](10, 2) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[surety_bonds]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[surety_bonds](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[contract_id] [bigint] NOT NULL,
	[bond_type] [nvarchar](50) NOT NULL,
	[bond_amount] [decimal](18, 2) NOT NULL,
	[currency_id] [bigint] NOT NULL,
	[surety_provider] [nvarchar](255) NOT NULL,
	[bond_number] [nvarchar](100) NOT NULL,
	[effective_date] [date] NOT NULL,
	[expiry_date] [date] NULL,
	[released_date] [date] NULL,
	[bond_status] [nvarchar](50) NOT NULL,
	[document_id] [bigint] NULL,
	[remarks] [nvarchar](max) NULL,
	[createdOn] [datetime2](7) NULL,
	[createdBy] [bigint] NOT NULL,
	[modifiedOn] [datetime2](7) NULL,
	[modifiedBy] [bigint] NULL,
	[archived] [nvarchar](3) NOT NULL,
 CONSTRAINT [pk_surety_bonds] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
UNIQUE NONCLUSTERED 
(
	[bond_number] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[suspect_arrest]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[suspect_arrest](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[case_id] [bigint] NOT NULL,
	[arrest_date] [date] NOT NULL,
	[arrested_contact_id] [bigint] NOT NULL,
	[arrested_gender] [nvarchar](50) NULL,
	[arrested_age] [bigint] NULL,
	[arrest_police_station] [nvarchar](255) NOT NULL,
	[arrest_ob_number] [nvarchar](100) NULL,
	[arrest_case_file_number] [nvarchar](100) NULL,
	[arrest_attachments] [bigint] NULL,
	[arrest_remarks] [nvarchar](max) NULL,
	[createdBy] [bigint] NULL,
	[createdOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
	[bail_status] [nvarchar](50) NULL,
	[arrest_location] [nvarchar](100) NULL,
	[place_arrested] [nvarchar](100) NULL,
	[arresting_officers] [nvarchar](100) NULL,
	[archived] [nvarchar](2) NULL,
 CONSTRAINT [pk_suspect_arrest] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[system_configurations]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[system_configurations](
	[keyName] [varchar](255) NOT NULL,
	[keyValue] [nvarchar](max) NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[system_preferences]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[system_preferences](
	[groupName] [nvarchar](255) NULL,
	[keyName] [varchar](255) NOT NULL,
	[keyValue] [nvarchar](max) NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_board_column_options]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_board_column_options](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[task_board_id] [bigint] NULL,
	[task_board_column_id] [bigint] NOT NULL,
	[task_status_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_board_columns]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_board_columns](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[task_board_id] [bigint] NOT NULL,
	[columnOrder] [tinyint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[color] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_board_saved_filters]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_board_saved_filters](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[boardId] [bigint] NOT NULL,
	[userId] [bigint] NOT NULL,
	[keyName] [varchar](255) NOT NULL,
	[keyValue] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_boards]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_boards](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_comments]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_comments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[task_id] [bigint] NOT NULL,
	[comment] [text] NOT NULL,
	[edited] [tinyint] NOT NULL,
	[createdOn] [smalldatetime] NOT NULL,
	[createdBy] [bigint] NOT NULL,
	[modifiedOn] [smalldatetime] NOT NULL,
	[modifiedBy] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_types]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_types_languages]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_types_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[task_type_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_users]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_users](
	[task_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_workflow_status_relation]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_workflow_status_relation](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[status_id] [bigint] NOT NULL,
	[start_point] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_workflow_status_transition]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_workflow_status_transition](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[from_step] [bigint] NOT NULL,
	[to_step] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[comments] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_workflow_status_transition_history]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_workflow_status_transition_history](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[task_id] [bigint] NOT NULL,
	[from_step] [bigint] NULL,
	[to_step] [bigint] NOT NULL,
	[user_id] [bigint] NULL,
	[changed_on] [smalldatetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_workflow_status_transition_permissions]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_workflow_status_transition_permissions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[transition] [bigint] NOT NULL,
	[users] [text] NULL,
	[user_groups] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_workflow_status_transition_screen_fields]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_workflow_status_transition_screen_fields](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[transition] [bigint] NOT NULL,
	[data] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_workflow_types]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_workflow_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[type_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[task_workflows]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[task_workflows](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[type] [nvarchar](255) NOT NULL,
	[createdBy] [bigint] NULL,
	[createdOn] [datetime] NULL,
	[modifiedBy] [bigint] NULL,
	[modifiedOn] [datetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[tasks_documents]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tasks_documents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[task_id] [bigint] NOT NULL,
	[document_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[terms]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[terms](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[fl1name] [nvarchar](255) NULL,
	[fl2name] [nvarchar](255) NULL,
	[number_of_days] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[time_internal_statuses_languages]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[time_internal_statuses_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[internal_status] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[time_types_languages]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[time_types_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[titles]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[titles](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[titles_languages]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[titles_languages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[title_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[trigger_action_task_values]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[trigger_action_task_values](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[action_id] [bigint] NOT NULL,
	[due_date] [datetime] NULL,
	[task_type] [bigint] NOT NULL,
	[assigned_to] [bigint] NULL,
	[description] [varchar](50) NULL,
	[assigned_to_matter] [varchar](50) NULL,
	[title] [nvarchar](255) NOT NULL,
 CONSTRAINT [PK_trigger_action_task_values] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[trigger_action_types]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[trigger_action_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [varchar](50) NOT NULL,
 CONSTRAINT [PK_trigger_action_types] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[trigger_actions]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[trigger_actions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[trigger_id] [bigint] NOT NULL,
	[trigger_action_type_id] [bigint] NOT NULL,
	[created_by] [bigint] NOT NULL,
	[created_on] [datetime] NULL,
	[modified_by] [bigint] NOT NULL,
	[modified_on] [datetime] NULL,
 CONSTRAINT [PK_trigger_actions] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[trigger_matter_workflow_conditions]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[trigger_matter_workflow_conditions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[trigger_id] [bigint] NOT NULL,
	[from_stage] [bigint] NOT NULL,
	[to_stage] [bigint] NOT NULL,
	[area_of_practice] [bigint] NOT NULL,
 CONSTRAINT [PK_trigger_matter_workflow_conditions] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[trigger_types]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[trigger_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [varchar](50) NOT NULL,
 CONSTRAINT [PK_trigger_types] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[triggers]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[triggers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[trigger_type_id] [bigint] NOT NULL,
	[source_id] [bigint] NULL,
	[created_on] [datetime] NULL,
	[created_by] [bigint] NOT NULL,
	[modified_on] [datetime] NULL,
	[modified_by] [bigint] NOT NULL,
 CONSTRAINT [PK_triggers] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_api_keys]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_api_keys](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[api_key] [varchar](255) NOT NULL,
	[key_generated_on] [datetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_autologin]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_autologin](
	[key_id] [char](32) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[user_agent] [nvarchar](255) NOT NULL,
	[last_ip] [nvarchar](45) NOT NULL,
	[last_login] [smalldatetime] NOT NULL,
	[channel] [nvarchar](3) NULL,
PRIMARY KEY CLUSTERED 
(
	[key_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_group_permissions]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_group_permissions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_group_id] [bigint] NOT NULL,
	[data] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_integrations]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_integrations](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[integration_id] [bigint] NOT NULL,
	[keyName] [nvarchar](255) NOT NULL,
	[keyValue] [nvarchar](max) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [user_integration_key] UNIQUE NONCLUSTERED 
(
	[user_id] ASC,
	[integration_id] ASC,
	[keyName] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_passwords]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_passwords](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[password] [nvarchar](255) NOT NULL,
	[created] [smalldatetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_preferences]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_preferences](
	[user_id] [bigint] NOT NULL,
	[keyName] [varchar](255) NOT NULL,
	[keyValue] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[user_id] ASC,
	[keyName] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_rate_per_hour]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_rate_per_hour](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[organization_id] [bigint] NOT NULL,
	[ratePerHour] [decimal](10, 2) NOT NULL,
	[yearly_billable_target] [int] NOT NULL,
	[working_days_per_year] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_rate_per_hour_per_case]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_rate_per_hour_per_case](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[case_id] [bigint] NOT NULL,
	[organization_id] [bigint] NOT NULL,
	[ratePerHour] [decimal](10, 2) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_reports]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_reports](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[keyName] [varchar](255) NOT NULL,
	[keyValue] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_signature_attachments]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_signature_attachments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[label] [nvarchar](255) NULL,
	[signature] [nvarchar](255) NULL,
	[type] [nvarchar](10) NOT NULL,
	[is_default] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_temp]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_temp](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[username] [nvarchar](255) NOT NULL,
	[password] [nvarchar](34) NOT NULL,
	[email] [nvarchar](100) NOT NULL,
	[activation_key] [nvarchar](50) NOT NULL,
	[last_ip] [nvarchar](40) NOT NULL,
	[created] [smalldatetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[workflow_case_types]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[workflow_case_types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[case_type_id] [bigint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[workflow_status_relation]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[workflow_status_relation](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[status_id] [bigint] NOT NULL,
	[start_point] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[workflow_status_transition]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[workflow_status_transition](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[workflow_id] [bigint] NOT NULL,
	[fromStep] [bigint] NOT NULL,
	[toStep] [bigint] NOT NULL,
	[limitToGroup] [bigint] NULL,
	[limitToUser] [bigint] NULL,
	[name] [nvarchar](255) NOT NULL,
	[comments] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[workflow_status_transition_history]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[workflow_status_transition_history](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[legal_case_id] [bigint] NOT NULL,
	[fromStep] [bigint] NULL,
	[toStep] [bigint] NOT NULL,
	[user_id] [bigint] NULL,
	[changedOn] [smalldatetime] NOT NULL,
	[modifiedByChannel] [nvarchar](3) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[workflow_status_transition_permissions]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[workflow_status_transition_permissions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[transition] [bigint] NOT NULL,
	[users] [text] NULL,
	[user_groups] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[workflow_status_transition_screen_fields]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[workflow_status_transition_screen_fields](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[transition] [bigint] NOT NULL,
	[data] [text] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[workflows]    Script Date: 1/16/2026 12:39:17 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[workflows](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[type] [nvarchar](255) NOT NULL,
	[isDeleted] [tinyint] NOT NULL,
	[category] [nvarchar](50) NULL,
	[createdOn] [smalldatetime] NULL,
	[createdBy] [bigint] NULL,
	[modifiedOn] [smalldatetime] NULL,
	[modifiedBy] [bigint] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Index [account_type_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [account_type_id] ON [dbo].[accounts]
(
	[account_type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [currency_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [currency_id] ON [dbo].[accounts]
(
	[currency_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [organization_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [organization_id] ON [dbo].[accounts]
(
	[organization_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [userId_accountId]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [userId_accountId] ON [dbo].[accounts_users]
(
	[userId] ASC,
	[accountId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [name]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [name] ON [dbo].[advisor_email_templates]
(
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [advisor_task_id_advisor_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [advisor_task_id_advisor_id] ON [dbo].[advisor_task_sharedwith_users]
(
	[advisor_task_id] ASC,
	[advisor_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [advisor_task_sharedwith_users_advisor_task_id_advisor_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [advisor_task_sharedwith_users_advisor_task_id_advisor_id] ON [dbo].[advisor_task_sharedwith_users]
(
	[advisor_task_id] ASC,
	[advisor_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [account_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [account_id] ON [dbo].[bill_details]
(
	[account_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [bill_header_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [bill_header_id] ON [dbo].[bill_details]
(
	[bill_header_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [account_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [account_id] ON [dbo].[bill_headers]
(
	[account_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [client_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [client_id] ON [dbo].[bill_headers]
(
	[client_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [voucher_header_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [voucher_header_id] ON [dbo].[bill_headers]
(
	[voucher_header_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [bill_header_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [bill_header_id] ON [dbo].[bill_payment_bills]
(
	[bill_header_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [bill_payment_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [bill_payment_id] ON [dbo].[bill_payment_bills]
(
	[bill_payment_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [account_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [account_id] ON [dbo].[bill_payments]
(
	[account_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [supplier_account_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [supplier_account_id] ON [dbo].[bill_payments]
(
	[supplier_account_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [voucher_header_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [voucher_header_id] ON [dbo].[bill_payments]
(
	[voucher_header_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [board_post_filters_id_user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [board_post_filters_id_user_id] ON [dbo].[board_post_filters_user]
(
	[board_post_filters_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [board_task_post_filters_id_user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [board_task_post_filters_id_user_id] ON [dbo].[board_task_post_filters_user]
(
	[board_post_filters_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [case_rate_fu]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [case_rate_fu] ON [dbo].[case_rate]
(
	[case_id] ASC,
	[organization_id] ASC
)
WHERE ([organization_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_case_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [legal_case_id] ON [dbo].[case_related_contracts]
(
	[legal_case_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [ci_sessions_timestamp]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [ci_sessions_timestamp] ON [dbo].[ci_sessions]
(
	[timestamp] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_ci_sessions_timestamp]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_ci_sessions_timestamp] ON [dbo].[ci_sessions]
(
	[timestamp] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [company_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [company_id] ON [dbo].[clients]
(
	[company_id] ASC
)
WHERE ([company_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contact_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [contact_id] ON [dbo].[clients]
(
	[contact_id] ASC
)
WHERE ([contact_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [shortName]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [shortName] ON [dbo].[companies]
(
	[shortName] ASC
)
WHERE ([shortName] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [company_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [company_id] ON [dbo].[companies_related_contracts]
(
	[company_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [company_id_user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [company_id_user_id] ON [dbo].[company_users]
(
	[company_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contact_nationalities_unique_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [contact_nationalities_unique_key] ON [dbo].[contact_nationalities]
(
	[contact_id] ASC,
	[nationality_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contact_id_user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [contact_id_user_id] ON [dbo].[contact_users]
(
	[contact_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contact_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [contact_id] ON [dbo].[contacts_related_contracts]
(
	[contact_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contract_approval_negotiation_status_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [contract_approval_negotiation_status_id] ON [dbo].[contract_approval_negotiation]
(
	[contract_approval_status_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contract_approval_negotiation_comments_negotiation_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [contract_approval_negotiation_comments_negotiation_id] ON [dbo].[contract_approval_negotiation_comments]
(
	[negotiation_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [status]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [status] ON [dbo].[contract_approval_status]
(
	[status] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [board_id_user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [board_id_user_id] ON [dbo].[contract_board_grid_saved_filters_users]
(
	[board_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [board_post_filters_id_user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [board_post_filters_id_user_id] ON [dbo].[contract_board_post_filters_user]
(
	[board_post_filters_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [clause_editor]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [clause_editor] ON [dbo].[contract_clause_editor]
(
	[contract_clause_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contract_clause_type]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [contract_clause_type] ON [dbo].[contract_clause_type]
(
	[contract_clause_id] ASC,
	[type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [clause_user]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [clause_user] ON [dbo].[contract_clause_user]
(
	[contract_clause_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contract_collaborators_unique_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [contract_collaborators_unique_key] ON [dbo].[contract_collaborators]
(
	[contract_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contract_contributors_unique_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [contract_contributors_unique_key] ON [dbo].[contract_contributors]
(
	[contract_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [ux_one_active_format]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [ux_one_active_format] ON [dbo].[contract_numbering_formats]
(
	[is_active] ASC
)
WHERE ([is_active]=(1))
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contract_id_party]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [contract_id_party] ON [dbo].[contract_party]
(
	[contract_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contract_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [contract_key] ON [dbo].[contract_party]
(
	[contract_id] ASC,
	[party_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contract_template_groups]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [contract_template_groups] ON [dbo].[contract_template_groups]
(
	[page_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contract_template_pages]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [contract_template_pages] ON [dbo].[contract_template_pages]
(
	[template_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contract_template_variables]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [contract_template_variables] ON [dbo].[contract_template_variables]
(
	[group_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [sub_type_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [sub_type_id] ON [dbo].[contract_templates]
(
	[sub_type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [type_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [type_id] ON [dbo].[contract_templates]
(
	[type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contract_user]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [contract_user] ON [dbo].[contract_users]
(
	[contract_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_conveyancing_activity_instrument]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_conveyancing_activity_instrument] ON [dbo].[conveyancing_activity]
(
	[conveyancing_instrument_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_conveyancing_activity_type]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_conveyancing_activity_type] ON [dbo].[conveyancing_activity]
(
	[activity_type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_conveyancing_instruments_assignee]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_conveyancing_instruments_assignee] ON [dbo].[conveyancing_instruments]
(
	[assignee_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_conveyancing_instruments_dates]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_conveyancing_instruments_dates] ON [dbo].[conveyancing_instruments]
(
	[date_initiated] ASC,
	[due_date] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_conveyancing_instruments_status]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_conveyancing_instruments_status] ON [dbo].[conveyancing_instruments]
(
	[status_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_conveyancing_instruments_type]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_conveyancing_instruments_type] ON [dbo].[conveyancing_instruments]
(
	[instrument_type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_conveyancing_stage_progress_instrument]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_conveyancing_stage_progress_instrument] ON [dbo].[conveyancing_stage_progress]
(
	[instrument_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_conveyancing_stage_progress_stage]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_conveyancing_stage_progress_stage] ON [dbo].[conveyancing_stage_progress]
(
	[stage_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_activity_log_correspondence]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_activity_log_correspondence] ON [dbo].[correspondence_activity_log]
(
	[correspondence_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_activity_log_user]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_activity_log_user] ON [dbo].[correspondence_activity_log]
(
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_document_correspondence]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_document_correspondence] ON [dbo].[correspondence_document]
(
	[correspondence_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_document_type]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_document_type] ON [dbo].[correspondence_document]
(
	[document_type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_relationships_correspondence1]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_relationships_correspondence1] ON [dbo].[correspondence_relationships]
(
	[correspondence_id1] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_relationships_correspondence2]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_relationships_correspondence2] ON [dbo].[correspondence_relationships]
(
	[correspondence_id2] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_workflow_correspondence]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_workflow_correspondence] ON [dbo].[correspondence_workflow]
(
	[correspondence_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_workflow_step]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_workflow_step] ON [dbo].[correspondence_workflow]
(
	[workflow_step_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_correspondences_createdOn]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_correspondences_createdOn] ON [dbo].[correspondences]
(
	[createdOn] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [IX_correspondences_reference]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_correspondences_reference] ON [dbo].[correspondences]
(
	[reference_number] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_correspondences_status]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_correspondences_status] ON [dbo].[correspondences]
(
	[status_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_correspondences_type]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_correspondences_type] ON [dbo].[correspondences]
(
	[correspondence_type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [recordId_user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [recordId_user_id] ON [dbo].[document_managment_users]
(
	[recordId] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [module_record_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [module_record_id] ON [dbo].[documents_management_system]
(
	[module_record_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [parent]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [parent] ON [dbo].[documents_management_system]
(
	[parent] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [IX_email_templates_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_email_templates_key] ON [dbo].[email_templates]
(
	[template_key] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [event_type_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [event_type_id] ON [dbo].[event_types_languages]
(
	[event_type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [language_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [language_id] ON [dbo].[event_types_languages]
(
	[language_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_exhibit_case_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_exhibit_case_id] ON [dbo].[exhibit]
(
	[case_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_exhibit_createdBy]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_exhibit_createdBy] ON [dbo].[exhibit]
(
	[createdBy] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_exhibit_current_location]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_exhibit_current_location] ON [dbo].[exhibit]
(
	[current_location_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_exhibit_modifiedBy]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_exhibit_modifiedBy] ON [dbo].[exhibit]
(
	[modifiedBy] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_exhibit_pickup_location]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_exhibit_pickup_location] ON [dbo].[exhibit]
(
	[pickup_location_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_chain_createdBy]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_chain_createdBy] ON [dbo].[exhibit_chain_of_movement]
(
	[createdBy] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_chain_exhibit_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_chain_exhibit_id] ON [dbo].[exhibit_chain_of_movement]
(
	[exhibit_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_chain_modifiedBy]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_chain_modifiedBy] ON [dbo].[exhibit_chain_of_movement]
(
	[modifiedBy] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_chain_officer_receiving]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_chain_officer_receiving] ON [dbo].[exhibit_chain_of_movement]
(
	[officer_receiving] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_chain_transfer_from_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_chain_transfer_from_id] ON [dbo].[exhibit_chain_of_movement]
(
	[transfer_from_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_chain_transfer_to_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_chain_transfer_to_id] ON [dbo].[exhibit_chain_of_movement]
(
	[transfer_to_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_exhibit_document_document_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_exhibit_document_document_id] ON [dbo].[exhibit_document]
(
	[document] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_exhibit_document_exhibit_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_exhibit_document_exhibit_id] ON [dbo].[exhibit_document]
(
	[exhibit_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [account_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [account_id] ON [dbo].[expense_categories]
(
	[account_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [expense_category_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [expense_category_id] ON [dbo].[expense_categories]
(
	[expense_category_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [name]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [name] ON [dbo].[expense_categories]
(
	[name] ASC,
	[account_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [client_account_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [client_account_id] ON [dbo].[expenses]
(
	[client_account_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [client_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [client_id] ON [dbo].[expenses]
(
	[client_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [expense_account]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [expense_account] ON [dbo].[expenses]
(
	[expense_account] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [expense_category_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [expense_category_id] ON [dbo].[expenses]
(
	[expense_category_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [paid_through]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [paid_through] ON [dbo].[expenses]
(
	[paid_through] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [tax_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [tax_id] ON [dbo].[expenses]
(
	[tax_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [vendor_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [vendor_id] ON [dbo].[expenses]
(
	[vendor_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [voucher_header_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [voucher_header_id] ON [dbo].[expenses]
(
	[voucher_header_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [board_id_user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [board_id_user_id] ON [dbo].[grid_saved_board_filters_users]
(
	[board_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [board_id_user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [board_id_user_id] ON [dbo].[grid_saved_board_task_filters_users]
(
	[board_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [account_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [account_id] ON [dbo].[invoice_details]
(
	[account_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [expense_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [expense_id] ON [dbo].[invoice_details]
(
	[expense_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [invoice_header_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [invoice_header_id] ON [dbo].[invoice_details]
(
	[invoice_header_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [item_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [item_id] ON [dbo].[invoice_details]
(
	[item_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [sub_item_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [sub_item_id] ON [dbo].[invoice_details]
(
	[sub_item_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [tax_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [tax_id] ON [dbo].[invoice_details]
(
	[tax_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [account_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [account_id] ON [dbo].[invoice_headers]
(
	[account_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [term_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [term_id] ON [dbo].[invoice_headers]
(
	[term_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [voucher_header_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [voucher_header_id] ON [dbo].[invoice_headers]
(
	[voucher_header_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [invoice_header_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [invoice_header_id] ON [dbo].[invoice_payment_invoices]
(
	[invoice_header_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [invoice_payment_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [invoice_payment_id] ON [dbo].[invoice_payment_invoices]
(
	[invoice_payment_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [account_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [account_id] ON [dbo].[invoice_payments]
(
	[account_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [client_account_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [client_account_id] ON [dbo].[invoice_payments]
(
	[client_account_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [voucher_header_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [voucher_header_id] ON [dbo].[invoice_payments]
(
	[voucher_header_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [account_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [account_id] ON [dbo].[items]
(
	[account_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [item_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [item_id] ON [dbo].[items]
(
	[item_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [tax_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [tax_id] ON [dbo].[items]
(
	[tax_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [unitName]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [unitName] ON [dbo].[items]
(
	[unitName] ASC,
	[account_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_case_container_related_container_unique_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [legal_case_container_related_container_unique_key] ON [dbo].[legal_case_container_related_containers]
(
	[legal_case_container_id] ASC,
	[related_container_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [legal_case_hearing_user_unique_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [legal_case_hearing_user_unique_key] ON [dbo].[legal_case_hearings_users]
(
	[legal_case_hearing_id] ASC,
	[user_type] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_case_outsource_id_contact_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [legal_case_outsource_id_contact_id] ON [dbo].[legal_case_outsource_contacts]
(
	[legal_case_outsource_id] ASC,
	[contact_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_case_id_company_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [legal_case_id_company_id] ON [dbo].[legal_case_outsources]
(
	[legal_case_id] ASC,
	[company_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_case_container_unique_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [legal_case_container_unique_key] ON [dbo].[legal_case_related_containers]
(
	[legal_case_container_id] ASC,
	[legal_case_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [UQ_legal_case_risks_case_category]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [UQ_legal_case_risks_case_category] ON [dbo].[legal_case_risks]
(
	[case_id] ASC,
	[risk_category] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_case_id_user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [legal_case_id_user_id] ON [dbo].[legal_case_users]
(
	[legal_case_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_legal_cases_closed_by]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_legal_cases_closed_by] ON [dbo].[legal_cases]
(
	[closed_by] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_legal_cases_closure_requested_by]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_legal_cases_closure_requested_by] ON [dbo].[legal_cases]
(
	[closure_requested_by] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [legal_case_archived]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [legal_case_archived] ON [dbo].[legal_cases]
(
	[archived] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_case_case_type_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [legal_case_case_type_id] ON [dbo].[legal_cases]
(
	[case_type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_case_caseArrivalDate]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [legal_case_caseArrivalDate] ON [dbo].[legal_cases]
(
	[caseArrivalDate] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [legal_case_category]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [legal_case_category] ON [dbo].[legal_cases]
(
	[category] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_case_client_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [legal_case_client_id] ON [dbo].[legal_cases]
(
	[client_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_case_createdBy]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [legal_case_createdBy] ON [dbo].[legal_cases]
(
	[createdBy] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_case_dueDate]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [legal_case_dueDate] ON [dbo].[legal_cases]
(
	[dueDate] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_case_dueDate_legal_case_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [legal_case_dueDate_legal_case_id] ON [dbo].[legal_cases]
(
	[id] ASC,
	[dueDate] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [legal_case_priority]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [legal_case_priority] ON [dbo].[legal_cases]
(
	[priority] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [legal_case_private]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [legal_case_private] ON [dbo].[legal_cases]
(
	[private] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_case_provider_group_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [legal_case_provider_group_id] ON [dbo].[legal_cases]
(
	[provider_group_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_case_user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [legal_case_user_id] ON [dbo].[legal_cases]
(
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [legal_cases_companies_fu]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [legal_cases_companies_fu] ON [dbo].[legal_cases_companies]
(
	[case_id] ASC,
	[company_id] ASC,
	[legal_case_company_role_id] ASC
)
WHERE ([legal_case_company_role_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [renewalDate]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [renewalDate] ON [dbo].[legal_cases_countries_renewals]
(
	[renewalDate] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [uk_legal_cases_countries_renewals_users_1]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [uk_legal_cases_countries_renewals_users_1] ON [dbo].[legal_cases_countries_renewals_users]
(
	[legal_case_country_renewal_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [licenseAndWaiverUser]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [licenseAndWaiverUser] ON [dbo].[license_and_waiver_reminds]
(
	[license_and_waiver_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [licenseAndWaiverUserGroup]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [licenseAndWaiverUserGroup] ON [dbo].[license_and_waiver_reminds]
(
	[license_and_waiver_id] ASC,
	[user_group_id] ASC,
	[reminder_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [company_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [company_id] ON [dbo].[lookup_members]
(
	[company_id] ASC
)
WHERE ([company_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contact_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [contact_id] ON [dbo].[lookup_members]
(
	[contact_id] ASC
)
WHERE ([contact_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [model_has_permissions_model_id_model_type_index]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [model_has_permissions_model_id_model_type_index] ON [dbo].[model_has_permissions]
(
	[model_id] ASC,
	[model_type] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [model_has_roles_model_id_model_type_index]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [model_has_roles_model_id_model_type_index] ON [dbo].[model_has_roles]
(
	[model_id] ASC,
	[model_type] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [oauth_access_tokens_user_id_index]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [oauth_access_tokens_user_id_index] ON [dbo].[oauth_access_tokens]
(
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [oauth_auth_codes_user_id_index]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [oauth_auth_codes_user_id_index] ON [dbo].[oauth_auth_codes]
(
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [oauth_clients_user_id_index]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [oauth_clients_user_id_index] ON [dbo].[oauth_clients]
(
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_opinion_comments_opinion]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_opinion_comments_opinion] ON [dbo].[opinion_comments]
(
	[opinion_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_opinion_contributors_opinion]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_opinion_contributors_opinion] ON [dbo].[opinion_contributors]
(
	[opinion_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_opinion_contributors_user]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_opinion_contributors_user] ON [dbo].[opinion_contributors]
(
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_opinion_url_opinion]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_opinion_url_opinion] ON [dbo].[opinion_url]
(
	[opinion_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_opinion_users_opinion]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_opinion_users_opinion] ON [dbo].[opinion_users]
(
	[opinion_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_opinion_users_user]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_opinion_users_user] ON [dbo].[opinion_users]
(
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_workflow_status_relation_status]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_workflow_status_relation_status] ON [dbo].[opinion_workflow_status_relation]
(
	[status_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_workflow_status_relation_workflow]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_workflow_status_relation_workflow] ON [dbo].[opinion_workflow_status_relation]
(
	[workflow_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_workflow_transition_from]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_workflow_transition_from] ON [dbo].[opinion_workflow_status_transition]
(
	[from_step] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_workflow_transition_to]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_workflow_transition_to] ON [dbo].[opinion_workflow_status_transition]
(
	[to_step] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_workflow_transition_workflow]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_workflow_transition_workflow] ON [dbo].[opinion_workflow_status_transition]
(
	[workflow_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_opinion_transition_history_opinion]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_opinion_transition_history_opinion] ON [dbo].[opinion_workflow_status_transition_history]
(
	[opinion_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_workflow_transition_screen_fields]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_workflow_transition_screen_fields] ON [dbo].[opinion_workflow_status_transition_screen_fields]
(
	[transition] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_opinions_assigned_to]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_opinions_assigned_to] ON [dbo].[opinions]
(
	[assigned_to] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_opinions_createdOn]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_opinions_createdOn] ON [dbo].[opinions]
(
	[createdOn] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_opinions_status]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_opinions_status] ON [dbo].[opinions]
(
	[opinion_status_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_opinions_type]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_opinions_type] ON [dbo].[opinions]
(
	[opinion_type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_opinions_workflow]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_opinions_workflow] ON [dbo].[opinions]
(
	[workflow] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [IX_opinions_documents_opinion]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [IX_opinions_documents_opinion] ON [dbo].[opinions_documents]
(
	[opinion_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [opponent_company_id_unique_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [opponent_company_id_unique_key] ON [dbo].[opponents]
(
	[company_id] ASC
)
WHERE ([company_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [opponent_contact_id_unique_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [opponent_contact_id_unique_key] ON [dbo].[opponents]
(
	[contact_id] ASC
)
WHERE ([contact_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [organization_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [organization_id] ON [dbo].[organization_invoice_templates]
(
	[organization_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [currency_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [currency_id] ON [dbo].[organizations]
(
	[currency_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [name]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [name] ON [dbo].[organizations]
(
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [company_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [company_id] ON [dbo].[partners]
(
	[company_id] ASC
)
WHERE ([company_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contact_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [contact_id] ON [dbo].[partners]
(
	[contact_id] ASC
)
WHERE ([contact_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [company_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [company_id] ON [dbo].[party]
(
	[company_id] ASC
)
WHERE ([company_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contact_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [contact_id] ON [dbo].[party]
(
	[contact_id] ASC
)
WHERE ([contact_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [planning_board_columns_case_status]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [planning_board_columns_case_status] ON [dbo].[planning_board_column_options]
(
	[planning_board_id] ASC,
	[case_status_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [unique_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [unique_key] ON [dbo].[planning_board_saved_filters]
(
	[userId] ASC,
	[keyName] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [provider_group_user]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [provider_group_user] ON [dbo].[provider_groups_users]
(
	[provider_group_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [role_has_permissions_role_id_foreign]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [role_has_permissions_role_id_foreign] ON [dbo].[role_has_permissions]
(
	[role_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [saml__kvstore_expire]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [saml__kvstore_expire] ON [dbo].[saml__kvstore]
(
	[_expire] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [sub_contract_type]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [sub_contract_type] ON [dbo].[sub_contract_type]
(
	[type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [language_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [language_id] ON [dbo].[sub_contract_type_language]
(
	[language_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [sub_type_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [sub_type_id] ON [dbo].[sub_contract_type_language]
(
	[sub_type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [task_board_column_task_status]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [task_board_column_task_status] ON [dbo].[task_board_column_options]
(
	[task_board_id] ASC,
	[task_status_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [unique_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [unique_key] ON [dbo].[task_board_saved_filters]
(
	[userId] ASC,
	[keyName] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [task_contributors_unique_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [task_contributors_unique_key] ON [dbo].[task_contributors]
(
	[task_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [task_id_user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [task_id_user_id] ON [dbo].[task_users]
(
	[task_id] ASC,
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [task_workflow_status_transition_permissions_unique_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [task_workflow_status_transition_permissions_unique_key] ON [dbo].[task_workflow_status_transition_permissions]
(
	[transition] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [user_api_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [user_api_key] ON [dbo].[user_api_keys]
(
	[user_id] ASC,
	[api_key] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [user_id] ON [dbo].[user_autologin]
(
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [name]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [name] ON [dbo].[user_groups]
(
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [user_id] ON [dbo].[user_profiles]
(
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [user_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [user_id] ON [dbo].[user_rate_per_hour]
(
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [user_rate_per_hour_per_case_fu]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [user_rate_per_hour_per_case_fu] ON [dbo].[user_rate_per_hour_per_case]
(
	[user_id] ASC,
	[case_id] ASC,
	[organization_id] ASC
)
WHERE ([organization_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [unique_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [unique_key] ON [dbo].[user_reports]
(
	[user_id] ASC,
	[keyName] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [company_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [company_id] ON [dbo].[vendors]
(
	[company_id] ASC
)
WHERE ([company_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [contact_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [contact_id] ON [dbo].[vendors]
(
	[contact_id] ASC
)
WHERE ([contact_id] IS NOT NULL)
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [createdBy]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [createdBy] ON [dbo].[vendors]
(
	[createdBy] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [modifiedBy]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [modifiedBy] ON [dbo].[vendors]
(
	[modifiedBy] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [account_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [account_id] ON [dbo].[voucher_details]
(
	[account_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [voucher_header_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [voucher_header_id] ON [dbo].[voucher_details]
(
	[voucher_header_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [organization_id]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE NONCLUSTERED INDEX [organization_id] ON [dbo].[voucher_headers]
(
	[organization_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
/****** Object:  Index [workflow_status_transition_permissions_unique_key]    Script Date: 1/16/2026 12:39:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [workflow_status_transition_permissions_unique_key] ON [dbo].[workflow_status_transition_permissions]
(
	[transition] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
ALTER TABLE [dbo].[accounts] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[accounts] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[accounts] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[accounts] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[accounts] ADD  DEFAULT ('1') FOR [show_in_dashboard]
GO
ALTER TABLE [dbo].[accounts_types] ADD  DEFAULT ('1') FOR [is_visible]
GO
ALTER TABLE [dbo].[additional_id_types] ADD  DEFAULT (NULL) FOR [module]
GO
ALTER TABLE [dbo].[advisor_email_templates] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[advisor_email_templates] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[advisor_email_templates] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[advisor_email_templates] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[advisor_task_comments] ADD  DEFAULT ('0') FOR [edited]
GO
ALTER TABLE [dbo].[advisor_task_comments] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[advisor_task_comments] ADD  DEFAULT ('AP') FOR [createdByChannel]
GO
ALTER TABLE [dbo].[advisor_task_comments] ADD  DEFAULT (getdate()) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[advisor_task_comments] ADD  DEFAULT ('AP') FOR [modifiedByChannel]
GO
ALTER TABLE [dbo].[advisor_task_statuses] ADD  DEFAULT ('in progress') FOR [category]
GO
ALTER TABLE [dbo].[advisor_task_statuses] ADD  DEFAULT ('1') FOR [isGlobal]
GO
ALTER TABLE [dbo].[advisor_task_workflow_statuses] ADD  DEFAULT ('0') FOR [start_point]
GO
ALTER TABLE [dbo].[advisor_task_workflows] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[advisor_task_workflows] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[advisor_task_workflows] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[advisor_task_workflows] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[advisor_task_workflows_permissions] ADD  DEFAULT (NULL) FOR [advisor_task_workflow_id]
GO
ALTER TABLE [dbo].[advisor_task_workflows_permissions] ADD  DEFAULT (NULL) FOR [advisor_task_workflow_status_transition_id]
GO
ALTER TABLE [dbo].[advisor_tasks] ADD  DEFAULT (NULL) FOR [legal_case_id]
GO
ALTER TABLE [dbo].[advisor_tasks] ADD  DEFAULT (NULL) FOR [stage]
GO
ALTER TABLE [dbo].[advisor_tasks] ADD  DEFAULT (NULL) FOR [private]
GO
ALTER TABLE [dbo].[advisor_tasks] ADD  DEFAULT ('medium') FOR [priority]
GO
ALTER TABLE [dbo].[advisor_tasks] ADD  DEFAULT (NULL) FOR [advisor_task_location_id]
GO
ALTER TABLE [dbo].[advisor_tasks] ADD  DEFAULT (NULL) FOR [estimated_effort]
GO
ALTER TABLE [dbo].[advisor_tasks] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[advisor_tasks] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[advisor_tasks] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[advisor_tasks] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[advisor_tasks] ADD  DEFAULT ('no') FOR [archived]
GO
ALTER TABLE [dbo].[advisor_tasks] ADD  DEFAULT (NULL) FOR [reporter]
GO
ALTER TABLE [dbo].[advisor_tasks] ADD  DEFAULT (NULL) FOR [workflow]
GO
ALTER TABLE [dbo].[advisor_user_activity_logs] ADD  DEFAULT (NULL) FOR [advisor_task_id]
GO
ALTER TABLE [dbo].[advisor_user_activity_logs] ADD  DEFAULT (NULL) FOR [legal_case_id]
GO
ALTER TABLE [dbo].[advisor_user_activity_logs] ADD  DEFAULT (NULL) FOR [client_id]
GO
ALTER TABLE [dbo].[advisor_user_activity_logs] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[advisor_user_activity_logs] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[advisor_user_activity_logs] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[advisor_user_activity_logs] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[advisor_user_preferences] ADD  DEFAULT (NULL) FOR [keyValue]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [ban_reason]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [last_ip]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [last_login]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [firstName]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [lastName]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [jobTitle]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [phone]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [mobile]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [address]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT ('0') FOR [flagChangePassword]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [createdByChannel]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[advisor_users] ADD  DEFAULT (NULL) FOR [modifiedByChannel]
GO
ALTER TABLE [dbo].[app_modules] ADD  DEFAULT ('') FOR [module]
GO
ALTER TABLE [dbo].[app_modules] ADD  DEFAULT ('') FOR [action]
GO
ALTER TABLE [dbo].[app_modules] ADD  DEFAULT ('') FOR [alias]
GO
ALTER TABLE [dbo].[approval_assignee] ADD  DEFAULT ((0)) FOR [is_requester_manager]
GO
ALTER TABLE [dbo].[approval_assignee] ADD  DEFAULT ((0)) FOR [is_board_member]
GO
ALTER TABLE [dbo].[approval_assignee] ADD  DEFAULT ((0)) FOR [is_shareholder]
GO
ALTER TABLE [dbo].[approval_signature_documents] ADD  DEFAULT ((0)) FOR [to_be_approved]
GO
ALTER TABLE [dbo].[approval_signature_documents] ADD  DEFAULT ((0)) FOR [to_be_signed]
GO
ALTER TABLE [dbo].[assignments] ADD  DEFAULT (NULL) FOR [assigned_team]
GO
ALTER TABLE [dbo].[assignments] ADD  DEFAULT ((1)) FOR [visible_assignee]
GO
ALTER TABLE [dbo].[assignments] ADD  DEFAULT ((1)) FOR [visible_assigned_team]
GO
ALTER TABLE [dbo].[audit_logs] ADD  DEFAULT (getdate()) FOR [created]
GO
ALTER TABLE [dbo].[bill_details] ADD  DEFAULT (NULL) FOR [tax_id]
GO
ALTER TABLE [dbo].[bill_details] ADD  DEFAULT (NULL) FOR [percentage]
GO
ALTER TABLE [dbo].[bill_headers] ADD  DEFAULT (NULL) FOR [displayTax]
GO
ALTER TABLE [dbo].[bill_headers] ADD  DEFAULT (NULL) FOR [client_id]
GO
ALTER TABLE [dbo].[board_members] ADD  DEFAULT (NULL) FOR [tillDate]
GO
ALTER TABLE [dbo].[board_members] ADD  DEFAULT (NULL) FOR [permanentRepresentation]
GO
ALTER TABLE [dbo].[case_closure_recommendation] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[case_comment_attachments] ADD  DEFAULT (NULL) FOR [name]
GO
ALTER TABLE [dbo].[case_comments] ADD  DEFAULT (NULL) FOR [createdByChannel]
GO
ALTER TABLE [dbo].[case_comments] ADD  DEFAULT (NULL) FOR [modifiedByChannel]
GO
ALTER TABLE [dbo].[case_comments] ADD  DEFAULT ('0') FOR [isVisibleToCP]
GO
ALTER TABLE [dbo].[case_comments] ADD  DEFAULT ('0') FOR [isVisibleToAP]
GO
ALTER TABLE [dbo].[case_configurations] ADD  DEFAULT (NULL) FOR [keyValue]
GO
ALTER TABLE [dbo].[case_document_classifications] ADD  DEFAULT (NULL) FOR [case_document_classification_id]
GO
ALTER TABLE [dbo].[case_investigation_log] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[case_offense_subcategory] ADD  DEFAULT ((1)) FOR [is_active]
GO
ALTER TABLE [dbo].[case_types] ADD  DEFAULT (NULL) FOR [litigation]
GO
ALTER TABLE [dbo].[case_types] ADD  DEFAULT (NULL) FOR [corporate]
GO
ALTER TABLE [dbo].[case_types] ADD  DEFAULT (NULL) FOR [litigationSLA]
GO
ALTER TABLE [dbo].[case_types] ADD  DEFAULT (NULL) FOR [legalMatterSLA]
GO
ALTER TABLE [dbo].[case_types] ADD  DEFAULT ('0') FOR [isDeleted]
GO
ALTER TABLE [dbo].[ci_sessions] ADD  DEFAULT ('0') FOR [id]
GO
ALTER TABLE [dbo].[ci_sessions] ADD  DEFAULT ('0') FOR [ip_address]
GO
ALTER TABLE [dbo].[ci_sessions] ADD  DEFAULT ('0') FOR [timestamp]
GO
ALTER TABLE [dbo].[client_partner_shares] ADD  DEFAULT ('0.00') FOR [percentage]
GO
ALTER TABLE [dbo].[clients] ADD  DEFAULT (NULL) FOR [company_id]
GO
ALTER TABLE [dbo].[clients] ADD  DEFAULT (NULL) FOR [contact_id]
GO
ALTER TABLE [dbo].[clients] ADD  DEFAULT (NULL) FOR [term_id]
GO
ALTER TABLE [dbo].[clients] ADD  DEFAULT ((0)) FOR [discount_percentage]
GO
ALTER TABLE [dbo].[clients] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[clients] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[clients] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[clients] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [legalName]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT ('') FOR [shortName]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [foreignName]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT ('Active') FOR [status]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [company_category_id]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [company_sub_category_id]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [private]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [company_id]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [nationality_id]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [company_legal_type_id]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT ('0.00') FOR [capital]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT ('no') FOR [capitalVisualizeDecimals]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [capitalCurrency]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT ('0') FOR [nominalShares]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT ('0') FOR [bearerShares]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT ('0') FOR [shareParValue]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [shareParValueCurrency]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT ('0') FOR [qualifyingShares]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [registrationNb]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [registrationDate]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [registrationCity]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [registrationTaxNb]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [registrationYearsNb]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [registrationByLawNotaryPublic]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [registrationByLawRef]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [registrationByLawDate]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [registrationByLawCity]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [sharesLocation]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [ownedByGroup]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [sheerLebanese]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [contributionRatio]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [registrationAuthority]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [internalReference]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [crReleasedOn]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [crExpiresOn]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [additional_id_type]
GO
ALTER TABLE [dbo].[companies] ADD  DEFAULT (NULL) FOR [additional_id_value]
GO
ALTER TABLE [dbo].[companies_contacts] ADD  DEFAULT (NULL) FOR [description]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [address]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [city]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [state]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [zip]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [country]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [website]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [phone]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [fax]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [mobile]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [email]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [street_name]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [additional_street_name]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [building_number]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [address_additional_number]
GO
ALTER TABLE [dbo].[company_addresses] ADD  DEFAULT (NULL) FOR [district_neighborhood]
GO
ALTER TABLE [dbo].[company_assets] ADD  DEFAULT (NULL) FOR [company_asset_type_id]
GO
ALTER TABLE [dbo].[company_assets] ADD  DEFAULT (NULL) FOR [ref]
GO
ALTER TABLE [dbo].[company_auditors] ADD  DEFAULT (NULL) FOR [auditorType]
GO
ALTER TABLE [dbo].[company_auditors] ADD  DEFAULT (NULL) FOR [designationDate]
GO
ALTER TABLE [dbo].[company_auditors] ADD  DEFAULT (NULL) FOR [expiryDate]
GO
ALTER TABLE [dbo].[company_auditors] ADD  DEFAULT (NULL) FOR [fees]
GO
ALTER TABLE [dbo].[company_bank_accounts] ADD  DEFAULT (NULL) FOR [bankFullAddress]
GO
ALTER TABLE [dbo].[company_bank_accounts] ADD  DEFAULT (NULL) FOR [bankPhone]
GO
ALTER TABLE [dbo].[company_bank_accounts] ADD  DEFAULT (NULL) FOR [bankFax]
GO
ALTER TABLE [dbo].[company_bank_accounts] ADD  DEFAULT (NULL) FOR [accountName]
GO
ALTER TABLE [dbo].[company_bank_accounts] ADD  DEFAULT (NULL) FOR [accountCurrency]
GO
ALTER TABLE [dbo].[company_bank_accounts] ADD  DEFAULT (NULL) FOR [accountNb]
GO
ALTER TABLE [dbo].[company_bank_accounts] ADD  DEFAULT (NULL) FOR [swiftCode]
GO
ALTER TABLE [dbo].[company_bank_accounts] ADD  DEFAULT (NULL) FOR [iban]
GO
ALTER TABLE [dbo].[company_changes] ADD  DEFAULT (getdate()) FOR [changedOn]
GO
ALTER TABLE [dbo].[company_discharge_social_securities] ADD  DEFAULT (NULL) FOR [reminder_id]
GO
ALTER TABLE [dbo].[company_discharge_social_securities] ADD  DEFAULT (NULL) FOR [expiresOn]
GO
ALTER TABLE [dbo].[company_documents] ADD  DEFAULT (NULL) FOR [name]
GO
ALTER TABLE [dbo].[company_documents] ADD  DEFAULT (NULL) FOR [path]
GO
ALTER TABLE [dbo].[company_documents] ADD  DEFAULT (NULL) FOR [pathType]
GO
ALTER TABLE [dbo].[company_documents] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[company_documents] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[company_documents] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[company_documents] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[company_note_details] ADD  CONSTRAINT [df_company_note_details_uploaded]  DEFAULT (N'NO') FOR [uploaded]
GO
ALTER TABLE [dbo].[contact_documents] ADD  DEFAULT (NULL) FOR [name]
GO
ALTER TABLE [dbo].[contact_documents] ADD  DEFAULT (NULL) FOR [path]
GO
ALTER TABLE [dbo].[contact_documents] ADD  DEFAULT (NULL) FOR [pathType]
GO
ALTER TABLE [dbo].[contact_documents] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[contact_documents] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[contact_documents] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[contact_documents] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[contact_emails] ADD  DEFAULT ('') FOR [email]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('Active') FOR [status]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('') FOR [gender]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [title_id]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [foreignFirstName]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [foreignLastName]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('') FOR [father]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('') FOR [mother]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [dateOfBirth]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [contact_category_id]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [contact_sub_category_id]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('') FOR [jobTitle]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [private]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('no') FOR [isLawyer]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('no') FOR [lawyerForCompany]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('') FOR [website]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('') FOR [phone]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('') FOR [fax]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('') FOR [mobile]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('') FOR [address1]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('') FOR [address2]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('') FOR [city]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('') FOR [state]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT ('') FOR [zip]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [country_id]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [internalReference]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [manager_id]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [tax_number]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [street_name]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [additional_street_name]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [building_number]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [address_additional_number]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [district_neighborhood]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [additional_id_type]
GO
ALTER TABLE [dbo].[contacts] ADD  DEFAULT (NULL) FOR [additional_id_value]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT ('Active') FOR [status]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [value]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [sub_type_id]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [contract_date]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [start_date]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [end_date]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [reference_number]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [assigned_team_id]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [assignee_id]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [authorized_signatory]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [amendment_of]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT ('medium') FOR [priority]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [renewal_type]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [currency_id]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [private]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [channel]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT ('0') FOR [visible_to_cp]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [modifiedByChannel]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT ('no') FOR [archived]
GO
ALTER TABLE [dbo].[contract] ADD  DEFAULT (NULL) FOR [hideFromBoard]
GO
ALTER TABLE [dbo].[contract_amendment_history_details] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[contract_approval_collaborators] ADD  DEFAULT (NULL) FOR [type]
GO
ALTER TABLE [dbo].[contract_approval_history] ADD  DEFAULT ('0') FOR [enforce_previous_approvals]
GO
ALTER TABLE [dbo].[contract_approval_history] ADD  DEFAULT ('-') FOR [from_action]
GO
ALTER TABLE [dbo].[contract_approval_history] ADD  DEFAULT ('-') FOR [to_action]
GO
ALTER TABLE [dbo].[contract_approval_history] ADD  DEFAULT (NULL) FOR [comment]
GO
ALTER TABLE [dbo].[contract_approval_history] ADD  DEFAULT (NULL) FOR [done_on]
GO
ALTER TABLE [dbo].[contract_approval_history] ADD  DEFAULT ('user') FOR [done_by_type]
GO
ALTER TABLE [dbo].[contract_approval_history] ADD  DEFAULT (NULL) FOR [done_by_ip]
GO
ALTER TABLE [dbo].[contract_approval_history] ADD  DEFAULT ('A4L') FOR [approval_channel]
GO
ALTER TABLE [dbo].[contract_approval_negotiation] ADD  DEFAULT ('user') FOR [done_by_type]
GO
ALTER TABLE [dbo].[contract_approval_negotiation] ADD  DEFAULT (NULL) FOR [done_on]
GO
ALTER TABLE [dbo].[contract_approval_negotiation_comments] ADD  DEFAULT ('user') FOR [done_by_type]
GO
ALTER TABLE [dbo].[contract_approval_negotiation_comments] ADD  DEFAULT (NULL) FOR [done_on]
GO
ALTER TABLE [dbo].[contract_approval_negotiation_comments] ADD  DEFAULT (NULL) FOR [comment]
GO
ALTER TABLE [dbo].[contract_approval_signature_configuration] ADD  DEFAULT ('0') FOR [include_no_status]
GO
ALTER TABLE [dbo].[contract_approval_status] ADD  DEFAULT ((0)) FOR [is_requester_manager]
GO
ALTER TABLE [dbo].[contract_approval_status] ADD  DEFAULT ((0)) FOR [is_board_member]
GO
ALTER TABLE [dbo].[contract_approval_status] ADD  DEFAULT ((0)) FOR [party_id]
GO
ALTER TABLE [dbo].[contract_approval_status] ADD  DEFAULT (NULL) FOR [summary]
GO
ALTER TABLE [dbo].[contract_approval_status] ADD  DEFAULT ((0)) FOR [is_shareholder]
GO
ALTER TABLE [dbo].[contract_board_grid_saved_filters_users] ADD  DEFAULT (NULL) FOR [filter_id]
GO
ALTER TABLE [dbo].[contract_board_grid_saved_filters_users] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[contract_boards] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[contract_boards] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[contract_boards] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[contract_boards] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[contract_clause] ADD  DEFAULT (NULL) FOR [reference]
GO
ALTER TABLE [dbo].[contract_clause] ADD  DEFAULT (NULL) FOR [label]
GO
ALTER TABLE [dbo].[contract_clause] ADD  DEFAULT (NULL) FOR [iso_language_id]
GO
ALTER TABLE [dbo].[contract_clause] ADD  DEFAULT (NULL) FOR [private]
GO
ALTER TABLE [dbo].[contract_clause] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[contract_clause] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[contract_clause] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[contract_clause] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[contract_comment] ADD  DEFAULT (NULL) FOR [contract_id]
GO
ALTER TABLE [dbo].[contract_comment] ADD  DEFAULT ((0)) FOR [edited]
GO
ALTER TABLE [dbo].[contract_comment] ADD  DEFAULT (NULL) FOR [channel]
GO
ALTER TABLE [dbo].[contract_comment] ADD  DEFAULT (NULL) FOR [modifiedByChannel]
GO
ALTER TABLE [dbo].[contract_comment] ADD  DEFAULT ('0') FOR [visible_to_cp]
GO
ALTER TABLE [dbo].[contract_cp_screen_fields] ADD  DEFAULT ((1)) FOR [visible]
GO
ALTER TABLE [dbo].[contract_cp_screen_fields] ADD  DEFAULT (NULL) FOR [requiredDefaultValue]
GO
ALTER TABLE [dbo].[contract_cp_screen_fields] ADD  DEFAULT (NULL) FOR [fieldDescription]
GO
ALTER TABLE [dbo].[contract_cp_screen_fields] ADD  DEFAULT ((0)) FOR [sortOrder]
GO
ALTER TABLE [dbo].[contract_cp_screens] ADD  DEFAULT (NULL) FOR [description]
GO
ALTER TABLE [dbo].[contract_cp_screens] ADD  DEFAULT ('1') FOR [showInPortal]
GO
ALTER TABLE [dbo].[contract_cp_screens] ADD  DEFAULT (NULL) FOR [contract_request_type_category_id]
GO
ALTER TABLE [dbo].[contract_milestone] ADD  DEFAULT ('open') FOR [status]
GO
ALTER TABLE [dbo].[contract_numbering_formats] ADD  DEFAULT ('CT') FOR [prefix]
GO
ALTER TABLE [dbo].[contract_numbering_formats] ADD  DEFAULT ((3)) FOR [sequence_length]
GO
ALTER TABLE [dbo].[contract_numbering_formats] ADD  DEFAULT ((1)) FOR [is_active]
GO
ALTER TABLE [dbo].[contract_numbering_formats] ADD  DEFAULT ((0)) FOR [last_sequence]
GO
ALTER TABLE [dbo].[contract_numbering_formats] ADD  DEFAULT ('SYSDATETIME()') FOR [created_at]
GO
ALTER TABLE [dbo].[contract_signature_collaborators] ADD  DEFAULT (NULL) FOR [type]
GO
ALTER TABLE [dbo].[contract_signature_history] ADD  DEFAULT ('-') FOR [from_action]
GO
ALTER TABLE [dbo].[contract_signature_history] ADD  DEFAULT ('-') FOR [to_action]
GO
ALTER TABLE [dbo].[contract_signature_history] ADD  DEFAULT (NULL) FOR [comment]
GO
ALTER TABLE [dbo].[contract_signature_history] ADD  DEFAULT (NULL) FOR [done_on]
GO
ALTER TABLE [dbo].[contract_signature_history] ADD  DEFAULT ('user') FOR [done_by_type]
GO
ALTER TABLE [dbo].[contract_signature_status] ADD  DEFAULT ((0)) FOR [is_requester_manager]
GO
ALTER TABLE [dbo].[contract_signature_status] ADD  DEFAULT ((0)) FOR [is_board_member]
GO
ALTER TABLE [dbo].[contract_signature_status] ADD  DEFAULT ((0)) FOR [party_id]
GO
ALTER TABLE [dbo].[contract_signature_status] ADD  DEFAULT (NULL) FOR [summary]
GO
ALTER TABLE [dbo].[contract_signature_status] ADD  DEFAULT ((0)) FOR [is_shareholder]
GO
ALTER TABLE [dbo].[contract_signed_document] ADD  DEFAULT (getdate()) FOR [signed_on]
GO
ALTER TABLE [dbo].[contract_sla_management] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[contract_sla_management] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[contract_sla_management] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[contract_sla_management] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[contract_sla_notification] ADD  DEFAULT ('0') FOR [notified]
GO
ALTER TABLE [dbo].[contract_status] ADD  DEFAULT ('1') FOR [is_global]
GO
ALTER TABLE [dbo].[contract_template_pages] ADD  DEFAULT (NULL) FOR [description]
GO
ALTER TABLE [dbo].[contract_template_variables] ADD  DEFAULT (NULL) FOR [property_data]
GO
ALTER TABLE [dbo].[contract_template_variables] ADD  DEFAULT (NULL) FOR [description]
GO
ALTER TABLE [dbo].[contract_templates] ADD  DEFAULT (NULL) FOR [sub_type_id]
GO
ALTER TABLE [dbo].[contract_templates] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[contract_templates] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[contract_templates] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[contract_templates] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[contract_type_language] ADD  CONSTRAINT [DF_contract_type_language_applies_to]  DEFAULT ('contract') FOR [applies_to]
GO
ALTER TABLE [dbo].[contract_url] ADD  DEFAULT (NULL) FOR [name]
GO
ALTER TABLE [dbo].[contract_url] ADD  DEFAULT (NULL) FOR [path]
GO
ALTER TABLE [dbo].[contract_url] ADD  DEFAULT (NULL) FOR [path_type]
GO
ALTER TABLE [dbo].[contract_url] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[contract_url] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[contract_url] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[contract_url] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[contract_workflow] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[contract_workflow] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[contract_workflow] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[contract_workflow] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[contract_workflow_status_relation] ADD  DEFAULT ('1') FOR [start_point]
GO
ALTER TABLE [dbo].[contract_workflow_status_relation] ADD  DEFAULT ('0') FOR [approval_start_point]
GO
ALTER TABLE [dbo].[contract_workflow_status_transition] ADD  DEFAULT (NULL) FOR [comment]
GO
ALTER TABLE [dbo].[contract_workflow_status_transition] ADD  DEFAULT ('1') FOR [approval_needed]
GO
ALTER TABLE [dbo].[contract_workflow_status_transition_log] ADD  DEFAULT (getdate()) FOR [doneOn]
GO
ALTER TABLE [dbo].[contract_workflow_step_checklist] ADD  DEFAULT ('yesno') FOR [input_type]
GO
ALTER TABLE [dbo].[contract_workflow_step_checklist] ADD  DEFAULT ((1)) FOR [is_required]
GO
ALTER TABLE [dbo].[contract_workflow_step_checklist] ADD  DEFAULT ((0)) FOR [sort_order]
GO
ALTER TABLE [dbo].[contract_workflow_step_functions] ADD  DEFAULT ((0)) FOR [sort_order]
GO
ALTER TABLE [dbo].[contract_workflow_step_functions] ADD  DEFAULT ('GETDATE()') FOR [created_at]
GO
ALTER TABLE [dbo].[contract_workflow_steps_log] ADD  DEFAULT ('SYSDATETIME()') FOR [createdOn]
GO
ALTER TABLE [dbo].[contracts_sla] ADD  DEFAULT (NULL) FOR [cycle]
GO
ALTER TABLE [dbo].[contracts_sla] ADD  DEFAULT (NULL) FOR [action]
GO
ALTER TABLE [dbo].[contracts_sla] ADD  DEFAULT (NULL) FOR [actionDate]
GO
ALTER TABLE [dbo].[contracts_sla] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[contracts_sla] ADD  DEFAULT (NULL) FOR [modifiedByChannel]
GO
ALTER TABLE [dbo].[conveyancing_activity] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[conveyancing_activity_type] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[conveyancing_document_status] ADD  DEFAULT (getdate()) FOR [addedon]
GO
ALTER TABLE [dbo].[conveyancing_document_type] ADD  DEFAULT (getdate()) FOR [addedOn]
GO
ALTER TABLE [dbo].[conveyancing_instrument_types] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[conveyancing_instruments] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[conveyancing_instruments] ADD  DEFAULT ((0)) FOR [visible_to_CP]
GO
ALTER TABLE [dbo].[conveyancing_instruments] ADD  DEFAULT ((3)) FOR [priority]
GO
ALTER TABLE [dbo].[conveyancing_instruments] ADD  DEFAULT ('no') FOR [archived]
GO
ALTER TABLE [dbo].[conveyancing_process_stages] ADD  DEFAULT ((1)) FOR [is_active]
GO
ALTER TABLE [dbo].[conveyancing_process_stages] ADD  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[conveyancing_process_stages] ADD  DEFAULT (getdate()) FOR [updated_at]
GO
ALTER TABLE [dbo].[conveyancing_stage_progress] ADD  DEFAULT (getdate()) FOR [start_date]
GO
ALTER TABLE [dbo].[conveyancing_stage_progress] ADD  DEFAULT (getdate()) FOR [updated_on]
GO
ALTER TABLE [dbo].[conveyancing_transaction_types] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[correspondence_activity_log] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[correspondence_document] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[correspondence_document_types] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[correspondence_relationships] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[correspondence_statuses] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[correspondence_types] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[correspondence_workflow] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[correspondence_workflow_steps] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[correspondences] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[correspondences] ADD  CONSTRAINT [DF_correspondences_requires_signature]  DEFAULT ('no') FOR [requires_signature]
GO
ALTER TABLE [dbo].[countries] ADD  DEFAULT ('') FOR [countryCode]
GO
ALTER TABLE [dbo].[countries] ADD  DEFAULT (NULL) FOR [currencyCode]
GO
ALTER TABLE [dbo].[countries] ADD  DEFAULT (NULL) FOR [currencyName]
GO
ALTER TABLE [dbo].[countries] ADD  DEFAULT (NULL) FOR [isoNumeric]
GO
ALTER TABLE [dbo].[countries] ADD  DEFAULT (NULL) FOR [languages]
GO
ALTER TABLE [dbo].[cp_user_preferences] ADD  DEFAULT (NULL) FOR [keyValue]
GO
ALTER TABLE [dbo].[cp_user_signature_attachments] ADD  DEFAULT ('signature') FOR [type]
GO
ALTER TABLE [dbo].[cp_user_signature_attachments] ADD  DEFAULT ('0') FOR [is_default]
GO
ALTER TABLE [dbo].[credit_note_details] ADD  DEFAULT (NULL) FOR [item_id]
GO
ALTER TABLE [dbo].[credit_note_details] ADD  DEFAULT (NULL) FOR [tax_id]
GO
ALTER TABLE [dbo].[credit_note_details] ADD  DEFAULT (NULL) FOR [discount_id]
GO
ALTER TABLE [dbo].[credit_note_details] ADD  DEFAULT (NULL) FOR [expense_id]
GO
ALTER TABLE [dbo].[credit_note_details] ADD  DEFAULT (NULL) FOR [item_description]
GO
ALTER TABLE [dbo].[credit_note_details] ADD  DEFAULT (NULL) FOR [tax_percentage]
GO
ALTER TABLE [dbo].[credit_note_details] ADD  DEFAULT (NULL) FOR [discount_percentage]
GO
ALTER TABLE [dbo].[credit_note_details] ADD  DEFAULT (NULL) FOR [discount_amount]
GO
ALTER TABLE [dbo].[credit_note_details] ADD  DEFAULT (NULL) FOR [item_date]
GO
ALTER TABLE [dbo].[credit_note_details] ADD  DEFAULT (NULL) FOR [discount_type]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [voucher_header_id]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [credit_note_type_id]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [transaction_type_id]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [credit_note_reason_id]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [suffix]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [due_on]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [credit_note_number]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [reference_num]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [display_tax]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [display_discount]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [group_time_logs_by_user_in_export]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT ('0') FOR [display_item_date]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT ('1') FOR [display_item_quantity]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [exchange_rate]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [discount_id]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [discount_percentage]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [discount_amount]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [discount_value_type]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [draft_credit_note_number]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [created_on]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [created_by]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [modified_on]
GO
ALTER TABLE [dbo].[credit_note_headers] ADD  DEFAULT (NULL) FOR [modified_by]
GO
ALTER TABLE [dbo].[credit_note_item_commissions] ADD  DEFAULT (NULL) FOR [item_id]
GO
ALTER TABLE [dbo].[credit_note_item_commissions] ADD  DEFAULT (NULL) FOR [expense_id]
GO
ALTER TABLE [dbo].[credit_note_item_commissions] ADD  DEFAULT (NULL) FOR [time_logs_id]
GO
ALTER TABLE [dbo].[credit_note_reasons] ADD  DEFAULT (NULL) FOR [fl1name]
GO
ALTER TABLE [dbo].[credit_note_reasons] ADD  DEFAULT (NULL) FOR [fl2name]
GO
ALTER TABLE [dbo].[credit_note_reasons] ADD  DEFAULT ((0)) FOR [is_debit_note]
GO
ALTER TABLE [dbo].[credit_note_refunds] ADD  DEFAULT (NULL) FOR [exchange_rate]
GO
ALTER TABLE [dbo].[custom_fields] ADD  DEFAULT ((0)) FOR [cp_visible]
GO
ALTER TABLE [dbo].[customer_portal_screen_fields] ADD  DEFAULT ((1)) FOR [visible]
GO
ALTER TABLE [dbo].[customer_portal_screen_fields] ADD  DEFAULT (NULL) FOR [requiredDefaultValue]
GO
ALTER TABLE [dbo].[customer_portal_screen_fields] ADD  DEFAULT (NULL) FOR [fieldDescription]
GO
ALTER TABLE [dbo].[customer_portal_screen_fields] ADD  DEFAULT ((0)) FOR [sortOrder]
GO
ALTER TABLE [dbo].[customer_portal_screens] ADD  DEFAULT (NULL) FOR [description]
GO
ALTER TABLE [dbo].[customer_portal_screens] ADD  DEFAULT ('1') FOR [showInPortal]
GO
ALTER TABLE [dbo].[customer_portal_screens] ADD  DEFAULT (NULL) FOR [request_type_category_id]
GO
ALTER TABLE [dbo].[customer_portal_sla] ADD  DEFAULT (NULL) FOR [pause]
GO
ALTER TABLE [dbo].[customer_portal_sla] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[customer_portal_sla] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[customer_portal_sla] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[customer_portal_sla] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[customer_portal_sla_cases] ADD  DEFAULT (NULL) FOR [cycle]
GO
ALTER TABLE [dbo].[customer_portal_sla_cases] ADD  DEFAULT (NULL) FOR [action]
GO
ALTER TABLE [dbo].[customer_portal_sla_cases] ADD  DEFAULT (NULL) FOR [actionDate]
GO
ALTER TABLE [dbo].[customer_portal_sla_cases] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[customer_portal_sla_cases] ADD  DEFAULT (NULL) FOR [modifiedByChannel]
GO
ALTER TABLE [dbo].[customer_portal_sla_notification] ADD  DEFAULT ('0') FOR [notified]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT ('client') FOR [type]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT ('0') FOR [isAd]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT ('0') FOR [isA4Luser]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [firstName]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [lastName]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [employeeId]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [userCode]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [department]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [jobTitle]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [phone]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [mobile]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [address]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [ban_reason]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [last_ip]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [last_login]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT (NULL) FOR [userDirectory]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT ((1)) FOR [approved]
GO
ALTER TABLE [dbo].[customer_portal_users] ADD  DEFAULT ('0') FOR [flag_change_password]
GO
ALTER TABLE [dbo].[document_generator] ADD  DEFAULT (NULL) FOR [keyValue]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [extension]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [parent]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [lineage]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [size]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [version]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [private]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [document_type_id]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [document_status_id]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [comment]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [module_record_id]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT ((0)) FOR [system_document]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT ((1)) FOR [visible]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT ((0)) FOR [visible_in_cp]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT ((0)) FOR [visible_in_ap]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [initial_version_created_on]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [initial_version_created_by]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [initial_version_created_by_channel]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (getdate()) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT ((0)) FOR [is_locked]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [last_locked_by]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [last_locked_by_channel]
GO
ALTER TABLE [dbo].[documents_management_system] ADD  DEFAULT (NULL) FOR [last_locked_on]
GO
ALTER TABLE [dbo].[email_notifications_scheme] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[email_notifications_scheme] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[email_notifications_scheme] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[email_notifications_scheme] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[email_notifications_scheme] ADD  DEFAULT ((1)) FOR [hide_show_send_email_notification]
GO
ALTER TABLE [dbo].[email_templates] ADD  CONSTRAINT [DF_email_templates_is_active]  DEFAULT ((1)) FOR [is_active]
GO
ALTER TABLE [dbo].[email_templates] ADD  CONSTRAINT [DF_email_templates_updated_at]  DEFAULT (getdate()) FOR [updated_at]
GO
ALTER TABLE [dbo].[events] ADD  DEFAULT (NULL) FOR [legal_case_id]
GO
ALTER TABLE [dbo].[events] ADD  DEFAULT (NULL) FOR [private]
GO
ALTER TABLE [dbo].[events] ADD  DEFAULT ('medium') FOR [priority]
GO
ALTER TABLE [dbo].[events] ADD  DEFAULT (NULL) FOR [task_location_id]
GO
ALTER TABLE [dbo].[events] ADD  DEFAULT (NULL) FOR [calendar_id]
GO
ALTER TABLE [dbo].[events] ADD  DEFAULT (NULL) FOR [integration_id]
GO
ALTER TABLE [dbo].[events] ADD  DEFAULT (NULL) FOR [integration_type]
GO
ALTER TABLE [dbo].[events] ADD  DEFAULT (NULL) FOR [event_type_id]
GO
ALTER TABLE [dbo].[events] ADD  DEFAULT (NULL) FOR [created_from]
GO
ALTER TABLE [dbo].[events] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[events] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[events] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[events] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[events_attendees] ADD  DEFAULT ('0') FOR [mandatory]
GO
ALTER TABLE [dbo].[events_attendees] ADD  DEFAULT ('0') FOR [participant]
GO
ALTER TABLE [dbo].[exchange_rates] ADD  DEFAULT (NULL) FOR [rate]
GO
ALTER TABLE [dbo].[exhibit] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[exhibit] ADD  DEFAULT (getdate()) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[exhibit_activities_log] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[exhibit_chain_of_movement] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[expense_categories] ADD  DEFAULT (NULL) FOR [expense_category_id]
GO
ALTER TABLE [dbo].[expenses] ADD  DEFAULT (NULL) FOR [vendor_id]
GO
ALTER TABLE [dbo].[expenses] ADD  DEFAULT (NULL) FOR [client_id]
GO
ALTER TABLE [dbo].[expenses] ADD  DEFAULT (NULL) FOR [client_account_id]
GO
ALTER TABLE [dbo].[expenses] ADD  DEFAULT ('internal') FOR [billingStatus]
GO
ALTER TABLE [dbo].[expenses] ADD  DEFAULT (NULL) FOR [task]
GO
ALTER TABLE [dbo].[expenses] ADD  DEFAULT (NULL) FOR [hearing]
GO
ALTER TABLE [dbo].[expenses] ADD  DEFAULT (NULL) FOR [event]
GO
ALTER TABLE [dbo].[exporter_audit_logs] ADD  DEFAULT (NULL) FOR [created_on]
GO
ALTER TABLE [dbo].[exporter_audit_logs] ADD  DEFAULT (NULL) FOR [created_by]
GO
ALTER TABLE [dbo].[external_share_documents] ADD  DEFAULT (NULL) FOR [otp]
GO
ALTER TABLE [dbo].[external_share_documents] ADD  DEFAULT (NULL) FOR [otp_generated_on]
GO
ALTER TABLE [dbo].[external_share_documents] ADD  DEFAULT ((0)) FOR [otp_verification_failed]
GO
ALTER TABLE [dbo].[external_user_tokens] ADD  DEFAULT (NULL) FOR [created_on]
GO
ALTER TABLE [dbo].[grid_saved_board_filters_users] ADD  DEFAULT (NULL) FOR [filter_id]
GO
ALTER TABLE [dbo].[grid_saved_board_filters_users] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[grid_saved_board_filters_users] ADD  DEFAULT (NULL) FOR [is_board]
GO
ALTER TABLE [dbo].[grid_saved_board_task_filters_users] ADD  DEFAULT (NULL) FOR [filter_id]
GO
ALTER TABLE [dbo].[grid_saved_board_task_filters_users] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[grid_saved_board_task_filters_users] ADD  DEFAULT (NULL) FOR [is_board]
GO
ALTER TABLE [dbo].[grid_saved_filters] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[grid_saved_filters] ADD  DEFAULT ('0') FOR [isGlobalFilter]
GO
ALTER TABLE [dbo].[grid_saved_filters] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[grid_saved_filters] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[grid_saved_filters] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[grid_saved_filters] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[grid_saved_filters_users] ADD  DEFAULT (NULL) FOR [filter_id]
GO
ALTER TABLE [dbo].[grid_saved_filters_users] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[hearing_types_languages] ADD  DEFAULT (NULL) FOR [name]
GO
ALTER TABLE [dbo].[instance_data] ADD  DEFAULT (NULL) FOR [keyValue]
GO
ALTER TABLE [dbo].[integrations] ADD  DEFAULT ('0') FOR [is_active]
GO
ALTER TABLE [dbo].[invoice_detail_cover_page_template] ADD  DEFAULT (NULL) FOR [logo]
GO
ALTER TABLE [dbo].[invoice_detail_cover_page_template] ADD  DEFAULT (NULL) FOR [email]
GO
ALTER TABLE [dbo].[invoice_details] ADD  DEFAULT (NULL) FOR [item_id]
GO
ALTER TABLE [dbo].[invoice_details] ADD  DEFAULT (NULL) FOR [sub_item_id]
GO
ALTER TABLE [dbo].[invoice_details] ADD  DEFAULT (NULL) FOR [tax_id]
GO
ALTER TABLE [dbo].[invoice_details] ADD  DEFAULT (NULL) FOR [discount_id]
GO
ALTER TABLE [dbo].[invoice_details] ADD  DEFAULT (NULL) FOR [expense_id]
GO
ALTER TABLE [dbo].[invoice_details] ADD  DEFAULT (NULL) FOR [itemDescription]
GO
ALTER TABLE [dbo].[invoice_details] ADD  DEFAULT (NULL) FOR [percentage]
GO
ALTER TABLE [dbo].[invoice_details] ADD  DEFAULT (NULL) FOR [discountPercentage]
GO
ALTER TABLE [dbo].[invoice_details] ADD  DEFAULT (NULL) FOR [item_date]
GO
ALTER TABLE [dbo].[invoice_details] ADD  DEFAULT (NULL) FOR [discountAmount]
GO
ALTER TABLE [dbo].[invoice_details] ADD  DEFAULT (NULL) FOR [discount_type]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [original_invoice_id]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [invoice_type_id]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [transaction_type_id]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [payment_method_id]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [suffix]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [purchaseOrder]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [invoiceNumber]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [displayTax]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [displayDiscount]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [groupTimeLogsByUserInExport]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [related_quote_id]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT ((0)) FOR [display_item_date]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT ((1)) FOR [display_item_quantity]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [exchangeRate]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [discount_id]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [discount_percentage]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [discount_amount]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [discount_value_type]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [description]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [draft_invoice_number]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [debit_note_reason_id]
GO
ALTER TABLE [dbo].[invoice_headers] ADD  DEFAULT (NULL) FOR [invoice_template_id]
GO
ALTER TABLE [dbo].[invoice_payments] ADD  DEFAULT (NULL) FOR [exchangeRate]
GO
ALTER TABLE [dbo].[invoice_transaction_types] ADD  DEFAULT (NULL) FOR [fl1name]
GO
ALTER TABLE [dbo].[invoice_transaction_types] ADD  DEFAULT (NULL) FOR [fl2name]
GO
ALTER TABLE [dbo].[invoice_types] ADD  DEFAULT (NULL) FOR [fl1name]
GO
ALTER TABLE [dbo].[invoice_types] ADD  DEFAULT (NULL) FOR [fl2name]
GO
ALTER TABLE [dbo].[ip_details] ADD  DEFAULT (NULL) FOR [intellectual_property_right_id]
GO
ALTER TABLE [dbo].[ip_details] ADD  DEFAULT (NULL) FOR [ip_class_id]
GO
ALTER TABLE [dbo].[ip_details] ADD  DEFAULT (NULL) FOR [ip_subcategory_id]
GO
ALTER TABLE [dbo].[ip_details] ADD  DEFAULT (NULL) FOR [ip_status_id]
GO
ALTER TABLE [dbo].[ip_details] ADD  DEFAULT (NULL) FOR [ip_name_id]
GO
ALTER TABLE [dbo].[ip_details] ADD  DEFAULT (NULL) FOR [filingNumber]
GO
ALTER TABLE [dbo].[ip_details] ADD  DEFAULT (NULL) FOR [acceptanceRejection]
GO
ALTER TABLE [dbo].[ip_details] ADD  DEFAULT (NULL) FOR [certificationNumber]
GO
ALTER TABLE [dbo].[ip_details] ADD  DEFAULT (NULL) FOR [registrationReference]
GO
ALTER TABLE [dbo].[ip_details] ADD  DEFAULT (NULL) FOR [registrationDate]
GO
ALTER TABLE [dbo].[ip_details] ADD  DEFAULT (NULL) FOR [agentId]
GO
ALTER TABLE [dbo].[ip_details] ADD  DEFAULT (NULL) FOR [agentType]
GO
ALTER TABLE [dbo].[ip_details] ADD  DEFAULT (NULL) FOR [country_id]
GO
ALTER TABLE [dbo].[ip_petitions_oppositions] ADD  DEFAULT (NULL) FOR [description]
GO
ALTER TABLE [dbo].[ip_petitions_oppositions] ADD  DEFAULT (NULL) FOR [arrivalDate]
GO
ALTER TABLE [dbo].[ip_petitions_oppositions] ADD  DEFAULT (NULL) FOR [dueDate]
GO
ALTER TABLE [dbo].[ip_petitions_oppositions] ADD  DEFAULT (NULL) FOR [agentId]
GO
ALTER TABLE [dbo].[ip_petitions_oppositions] ADD  DEFAULT (NULL) FOR [agentType]
GO
ALTER TABLE [dbo].[ip_petitions_oppositions] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[ip_petitions_oppositions] ADD  DEFAULT (NULL) FOR [result]
GO
ALTER TABLE [dbo].[ip_statuses] ADD  DEFAULT ('in progress') FOR [category]
GO
ALTER TABLE [dbo].[item_commissions] ADD  DEFAULT (NULL) FOR [item_id]
GO
ALTER TABLE [dbo].[item_commissions] ADD  DEFAULT (NULL) FOR [sub_item_id]
GO
ALTER TABLE [dbo].[item_commissions] ADD  DEFAULT (NULL) FOR [expense_id]
GO
ALTER TABLE [dbo].[item_commissions] ADD  DEFAULT (NULL) FOR [time_logs_id]
GO
ALTER TABLE [dbo].[items] ADD  DEFAULT (NULL) FOR [item_id]
GO
ALTER TABLE [dbo].[items] ADD  DEFAULT (NULL) FOR [tax_id]
GO
ALTER TABLE [dbo].[items] ADD  DEFAULT (NULL) FOR [fl1unitName]
GO
ALTER TABLE [dbo].[items] ADD  DEFAULT (NULL) FOR [fl2unitName]
GO
ALTER TABLE [dbo].[legal_case_archived_hard_copies] ADD  DEFAULT (NULL) FOR [notes]
GO
ALTER TABLE [dbo].[legal_case_changes] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[legal_case_changes] ADD  DEFAULT (NULL) FOR [modifiedByChannel]
GO
ALTER TABLE [dbo].[legal_case_changes] ADD  DEFAULT (getdate()) FOR [changedOn]
GO
ALTER TABLE [dbo].[legal_case_container_documents] ADD  DEFAULT (NULL) FOR [name]
GO
ALTER TABLE [dbo].[legal_case_container_documents] ADD  DEFAULT (NULL) FOR [path]
GO
ALTER TABLE [dbo].[legal_case_container_documents] ADD  DEFAULT (NULL) FOR [pathType]
GO
ALTER TABLE [dbo].[legal_case_container_documents] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[legal_case_container_documents] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[legal_case_container_documents] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[legal_case_container_documents] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[legal_case_container_statuses] ADD  DEFAULT ('in progress') FOR [category]
GO
ALTER TABLE [dbo].[legal_case_containers] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[legal_case_containers] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[legal_case_containers] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[legal_case_containers] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[legal_case_containers] ADD  DEFAULT ('1') FOR [case_type_id]
GO
ALTER TABLE [dbo].[legal_case_containers] ADD  DEFAULT ('1') FOR [provider_group_id]
GO
ALTER TABLE [dbo].[legal_case_containers] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[legal_case_containers] ADD  DEFAULT (NULL) FOR [client_id]
GO
ALTER TABLE [dbo].[legal_case_containers] ADD  DEFAULT (NULL) FOR [caseArrivalDate]
GO
ALTER TABLE [dbo].[legal_case_containers] ADD  DEFAULT (NULL) FOR [internalReference]
GO
ALTER TABLE [dbo].[legal_case_containers] ADD  DEFAULT (NULL) FOR [legal_case_client_position_id]
GO
ALTER TABLE [dbo].[legal_case_containers] ADD  DEFAULT (NULL) FOR [requested_by]
GO
ALTER TABLE [dbo].[legal_case_containers] ADD  DEFAULT ((0)) FOR [visible_in_cp]
GO
ALTER TABLE [dbo].[legal_case_documents] ADD  DEFAULT (NULL) FOR [name]
GO
ALTER TABLE [dbo].[legal_case_documents] ADD  DEFAULT (NULL) FOR [path]
GO
ALTER TABLE [dbo].[legal_case_documents] ADD  DEFAULT (NULL) FOR [pathType]
GO
ALTER TABLE [dbo].[legal_case_documents] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[legal_case_documents] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[legal_case_documents] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[legal_case_documents] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[legal_case_event_data_types_languages] ADD  DEFAULT (NULL) FOR [type_details]
GO
ALTER TABLE [dbo].[legal_case_event_type_forms] ADD  DEFAULT (NULL) FOR [field_type]
GO
ALTER TABLE [dbo].[legal_case_event_type_forms] ADD  DEFAULT ('0') FOR [field_required]
GO
ALTER TABLE [dbo].[legal_case_event_type_forms] ADD  DEFAULT ('') FOR [field_key]
GO
ALTER TABLE [dbo].[legal_case_event_type_forms_languages] ADD  DEFAULT ('') FOR [field_type_details]
GO
ALTER TABLE [dbo].[legal_case_event_type_forms_languages] ADD  DEFAULT ('') FOR [field_description]
GO
ALTER TABLE [dbo].[legal_case_event_types] ADD  DEFAULT ('0') FOR [sub_event]
GO
ALTER TABLE [dbo].[legal_case_events] ADD  DEFAULT (NULL) FOR [event_type]
GO
ALTER TABLE [dbo].[legal_case_events] ADD  DEFAULT (NULL) FOR [parent]
GO
ALTER TABLE [dbo].[legal_case_events] ADD  DEFAULT (NULL) FOR [fields]
GO
ALTER TABLE [dbo].[legal_case_events] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[legal_case_events] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[legal_case_events] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[legal_case_events] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[legal_case_hearing_client_report_history] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[legal_case_hearing_client_report_history] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[legal_case_hearing_client_report_history] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[legal_case_hearing_client_report_history] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT (NULL) FOR [task_id]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT (NULL) FOR [startDate]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT (NULL) FOR [startTime]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT (NULL) FOR [postponedDate]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT (NULL) FOR [postponedTime]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT (NULL) FOR [summaryToClient]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT ((0)) FOR [verifiedSummary]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT ((0)) FOR [clientReportEmailSent]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT ('0') FOR [is_deleted]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT (NULL) FOR [hearing_outcome]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT (NULL) FOR [createdByChannel]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[legal_case_hearings] ADD  DEFAULT (NULL) FOR [modifiedByChannel]
GO
ALTER TABLE [dbo].[legal_case_hearings_users] ADD  DEFAULT ('A4L') FOR [user_type]
GO
ALTER TABLE [dbo].[legal_case_litigation_details] ADD  DEFAULT (NULL) FOR [court_type_id]
GO
ALTER TABLE [dbo].[legal_case_litigation_details] ADD  DEFAULT (NULL) FOR [court_degree_id]
GO
ALTER TABLE [dbo].[legal_case_litigation_details] ADD  DEFAULT (NULL) FOR [court_region_id]
GO
ALTER TABLE [dbo].[legal_case_litigation_details] ADD  DEFAULT (NULL) FOR [court_id]
GO
ALTER TABLE [dbo].[legal_case_litigation_details] ADD  DEFAULT (NULL) FOR [sentenceDate]
GO
ALTER TABLE [dbo].[legal_case_litigation_details] ADD  DEFAULT (NULL) FOR [comments]
GO
ALTER TABLE [dbo].[legal_case_litigation_details] ADD  DEFAULT (NULL) FOR [modifiedByChannel]
GO
ALTER TABLE [dbo].[legal_case_litigation_details] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[legal_case_litigation_details] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[legal_case_litigation_details] ADD  DEFAULT (NULL) FOR [createdByChannel]
GO
ALTER TABLE [dbo].[legal_case_litigation_external_references] ADD  DEFAULT (NULL) FOR [refDate]
GO
ALTER TABLE [dbo].[legal_case_outsources] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[legal_case_outsources] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[legal_case_outsources] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[legal_case_outsources] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[legal_case_partner_shares] ADD  DEFAULT ('0.00') FOR [percentage]
GO
ALTER TABLE [dbo].[legal_case_risks] ADD  DEFAULT ('GETDATE()') FOR [createdOn]
GO
ALTER TABLE [dbo].[legal_case_stage_changes] ADD  DEFAULT (NULL) FOR [legal_case_id]
GO
ALTER TABLE [dbo].[legal_case_stage_changes] ADD  DEFAULT (NULL) FOR [legal_case_stage_id]
GO
ALTER TABLE [dbo].[legal_case_stage_changes] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[legal_case_stage_changes] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[legal_case_stage_contacts] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[legal_case_stage_contacts] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[legal_case_stage_contacts] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[legal_case_stage_contacts] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[legal_case_stages] ADD  DEFAULT (NULL) FOR [litigation]
GO
ALTER TABLE [dbo].[legal_case_stages] ADD  DEFAULT (NULL) FOR [corporate]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [channel]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT ('0') FOR [visibleToCP]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [case_status_id]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [legal_case_stage_id]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [contact_id]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [client_id]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [referredBy]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [requestedBy]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [latest_development]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT ('medium') FOR [priority]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [arrivalDate]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [caseArrivalDate]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [dueDate]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [closedOn]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [category]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT ('0.00') FOR [caseValue]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT ('0.00') FOR [recoveredValue]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT ('0.00') FOR [judgmentValue]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [internalReference]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT ('no') FOR [externalizeLawyers]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [estimatedEffort]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [modifiedByChannel]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT ('no') FOR [archived]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [hideFromBoard]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [private]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [legal_case_client_position_id]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [legal_case_success_probability_id]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT (NULL) FOR [assignedOn]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT ('0') FOR [isDeleted]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT ('1') FOR [workflow]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT ('0.00') FOR [cap_amount]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT ('100.00') FOR [time_logs_cap_ratio]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT ('100.00') FOR [expenses_cap_ratio]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT ('0') FOR [cap_amount_enable]
GO
ALTER TABLE [dbo].[legal_cases] ADD  DEFAULT ('0') FOR [cap_amount_disallow]
GO
ALTER TABLE [dbo].[legal_cases_companies] ADD  DEFAULT (NULL) FOR [legal_case_company_role_id]
GO
ALTER TABLE [dbo].[legal_cases_companies] ADD  DEFAULT (NULL) FOR [comments]
GO
ALTER TABLE [dbo].[legal_cases_companies] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[legal_cases_companies] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[legal_cases_companies] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[legal_cases_companies] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[legal_cases_contacts] ADD  DEFAULT (NULL) FOR [legal_case_contact_role_id]
GO
ALTER TABLE [dbo].[legal_cases_contacts] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[legal_cases_contacts] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[legal_cases_contacts] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[legal_cases_contacts] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[legal_cases_countries_renewals] ADD  DEFAULT (NULL) FOR [expiryDate]
GO
ALTER TABLE [dbo].[legal_cases_countries_renewals] ADD  DEFAULT (NULL) FOR [renewalDate]
GO
ALTER TABLE [dbo].[license_and_waiver_reminds] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[license_and_waiver_reminds] ADD  DEFAULT (NULL) FOR [user_group_id]
GO
ALTER TABLE [dbo].[license_and_waiver_reminds] ADD  DEFAULT (NULL) FOR [reminder_id]
GO
ALTER TABLE [dbo].[login_history_log_archives] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[login_history_log_archives] ADD  DEFAULT (NULL) FOR [userLogin]
GO
ALTER TABLE [dbo].[login_history_logs] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[login_history_logs] ADD  DEFAULT (NULL) FOR [userLogin]
GO
ALTER TABLE [dbo].[lookup_members] ADD  DEFAULT (NULL) FOR [company_id]
GO
ALTER TABLE [dbo].[lookup_members] ADD  DEFAULT (NULL) FOR [contact_id]
GO
ALTER TABLE [dbo].[manage_non_business_days] ADD  DEFAULT (NULL) FOR [targetDate]
GO
ALTER TABLE [dbo].[money_dashboard_widgets] ADD  DEFAULT (NULL) FOR [filter]
GO
ALTER TABLE [dbo].[money_dashboard_widgets] ADD  DEFAULT ((0)) FOR [widget_order]
GO
ALTER TABLE [dbo].[money_dashboard_widgets_title_languages] ADD  DEFAULT (NULL) FOR [title]
GO
ALTER TABLE [dbo].[money_dashboard_widgets_types] ADD  DEFAULT (NULL) FOR [settings]
GO
ALTER TABLE [dbo].[notifications] ADD  DEFAULT ('unseen') FOR [status]
GO
ALTER TABLE [dbo].[notifications] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[notifications] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[notifications] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[notifications] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[oauth_access_tokens] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[oauth_access_tokens] ADD  DEFAULT (NULL) FOR [name]
GO
ALTER TABLE [dbo].[oauth_access_tokens] ADD  DEFAULT (NULL) FOR [created_at]
GO
ALTER TABLE [dbo].[oauth_access_tokens] ADD  DEFAULT (NULL) FOR [updated_at]
GO
ALTER TABLE [dbo].[oauth_access_tokens] ADD  DEFAULT (NULL) FOR [expires_at]
GO
ALTER TABLE [dbo].[oauth_auth_codes] ADD  DEFAULT (NULL) FOR [expires_at]
GO
ALTER TABLE [dbo].[oauth_clients] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[oauth_clients] ADD  DEFAULT (NULL) FOR [secret]
GO
ALTER TABLE [dbo].[oauth_clients] ADD  DEFAULT (NULL) FOR [provider]
GO
ALTER TABLE [dbo].[oauth_clients] ADD  DEFAULT (NULL) FOR [created_at]
GO
ALTER TABLE [dbo].[oauth_clients] ADD  DEFAULT (NULL) FOR [updated_at]
GO
ALTER TABLE [dbo].[oauth_personal_access_clients] ADD  DEFAULT (NULL) FOR [created_at]
GO
ALTER TABLE [dbo].[oauth_personal_access_clients] ADD  DEFAULT (NULL) FOR [updated_at]
GO
ALTER TABLE [dbo].[oauth_refresh_tokens] ADD  DEFAULT (NULL) FOR [expires_at]
GO
ALTER TABLE [dbo].[opinion_comments] ADD  DEFAULT ((0)) FOR [edited]
GO
ALTER TABLE [dbo].[opinion_comments] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[opinion_comments] ADD  DEFAULT (getdate()) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[opinion_document_type_language] ADD  DEFAULT ('opinions') FOR [applies_to]
GO
ALTER TABLE [dbo].[opinion_statuses] ADD  DEFAULT ('in progress') FOR [category]
GO
ALTER TABLE [dbo].[opinion_statuses] ADD  DEFAULT ((1)) FOR [isGlobal]
GO
ALTER TABLE [dbo].[opinion_types_languages] ADD  DEFAULT ('Opinions') FOR [applies_to]
GO
ALTER TABLE [dbo].[opinion_url] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[opinion_url] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[opinion_workflow_status_relation] ADD  DEFAULT ((0)) FOR [start_point]
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition_history] ADD  DEFAULT (getdate()) FOR [changed_on]
GO
ALTER TABLE [dbo].[opinion_workflows] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[opinion_workflows] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[opinions] ADD  DEFAULT ('medium') FOR [priority]
GO
ALTER TABLE [dbo].[opinions] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[opinions] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[opinions] ADD  DEFAULT ('no') FOR [archived]
GO
ALTER TABLE [dbo].[opinions] ADD  DEFAULT ((1)) FOR [workflow]
GO
ALTER TABLE [dbo].[opinions] ADD  DEFAULT ('opinions') FOR [category]
GO
ALTER TABLE [dbo].[opponents] ADD  DEFAULT (NULL) FOR [company_id]
GO
ALTER TABLE [dbo].[opponents] ADD  DEFAULT (NULL) FOR [contact_id]
GO
ALTER TABLE [dbo].[opponents] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[opponents] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[opponents] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[opponents] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[organization_invoice_templates] ADD  DEFAULT ('invoice') FOR [type]
GO
ALTER TABLE [dbo].[organization_invoice_templates] ADD  DEFAULT ('0') FOR [is_default]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [address1]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [address2]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [city]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [state]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [zip]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [country_id]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [website]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [phone]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [fax]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [tax_number]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [mobile]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [organizationID]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT ('inactive') FOR [e_invoicing]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [additional_id_type]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [additional_id_value]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [street_name]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [building_number]
GO
ALTER TABLE [dbo].[organizations] ADD  DEFAULT (NULL) FOR [address_additional_number]
GO
ALTER TABLE [dbo].[partners] ADD  DEFAULT (NULL) FOR [company_id]
GO
ALTER TABLE [dbo].[partners] ADD  DEFAULT (NULL) FOR [contact_id]
GO
ALTER TABLE [dbo].[partners] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[partners] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[partners] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[partners] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[partners] ADD  DEFAULT ('no') FOR [isThirdParty]
GO
ALTER TABLE [dbo].[party] ADD  DEFAULT (NULL) FOR [company_id]
GO
ALTER TABLE [dbo].[party] ADD  DEFAULT (NULL) FOR [contact_id]
GO
ALTER TABLE [dbo].[party] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[party] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[party] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[party] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[password_reset_token] ADD  DEFAULT ('1') FOR [user_type]
GO
ALTER TABLE [dbo].[password_reset_token] ADD  DEFAULT ('0') FOR [used]
GO
ALTER TABLE [dbo].[payment_methods] ADD  DEFAULT (NULL) FOR [fl1name]
GO
ALTER TABLE [dbo].[payment_methods] ADD  DEFAULT (NULL) FOR [fl2name]
GO
ALTER TABLE [dbo].[permissions] ADD  DEFAULT (NULL) FOR [created_at]
GO
ALTER TABLE [dbo].[permissions] ADD  DEFAULT (NULL) FOR [updated_at]
GO
ALTER TABLE [dbo].[planning_board_column_options] ADD  DEFAULT (NULL) FOR [planning_board_id]
GO
ALTER TABLE [dbo].[planning_board_saved_filters] ADD  DEFAULT (NULL) FOR [keyValue]
GO
ALTER TABLE [dbo].[planning_boards] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[planning_boards] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[planning_boards] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[planning_boards] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[preferred_shares] ADD  DEFAULT ('') FOR [series]
GO
ALTER TABLE [dbo].[preferred_shares] ADD  DEFAULT ('no') FOR [retrieved]
GO
ALTER TABLE [dbo].[preferred_shares] ADD  DEFAULT ('') FOR [comment]
GO
ALTER TABLE [dbo].[provider_groups] ADD  DEFAULT ('0') FOR [allUsers]
GO
ALTER TABLE [dbo].[provider_groups_users] ADD  DEFAULT ('no') FOR [isDefault]
GO
ALTER TABLE [dbo].[quote_details] ADD  DEFAULT (NULL) FOR [item_id]
GO
ALTER TABLE [dbo].[quote_details] ADD  DEFAULT (NULL) FOR [sub_item_id]
GO
ALTER TABLE [dbo].[quote_details] ADD  DEFAULT (NULL) FOR [tax_id]
GO
ALTER TABLE [dbo].[quote_details] ADD  DEFAULT (NULL) FOR [discount_id]
GO
ALTER TABLE [dbo].[quote_details] ADD  DEFAULT (NULL) FOR [expense_id]
GO
ALTER TABLE [dbo].[quote_details] ADD  DEFAULT (NULL) FOR [percentage]
GO
ALTER TABLE [dbo].[quote_details] ADD  DEFAULT (NULL) FOR [discountPercentage]
GO
ALTER TABLE [dbo].[quote_details] ADD  DEFAULT (NULL) FOR [item_date]
GO
ALTER TABLE [dbo].[quote_headers] ADD  DEFAULT (NULL) FOR [suffix]
GO
ALTER TABLE [dbo].[quote_headers] ADD  DEFAULT (NULL) FOR [purchaseOrder]
GO
ALTER TABLE [dbo].[quote_headers] ADD  DEFAULT (NULL) FOR [quoteNumber]
GO
ALTER TABLE [dbo].[quote_headers] ADD  DEFAULT (NULL) FOR [displayTax]
GO
ALTER TABLE [dbo].[quote_headers] ADD  DEFAULT (NULL) FOR [displayDiscount]
GO
ALTER TABLE [dbo].[quote_headers] ADD  DEFAULT (NULL) FOR [groupTimeLogsByUserInExport]
GO
ALTER TABLE [dbo].[quote_headers] ADD  DEFAULT (NULL) FOR [related_invoice_id]
GO
ALTER TABLE [dbo].[quote_headers] ADD  DEFAULT ((0)) FOR [display_item_date]
GO
ALTER TABLE [dbo].[quote_headers] ADD  DEFAULT (NULL) FOR [description]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT ('Open') FOR [status]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [legal_case_id]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [company_id]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [contact_id]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [task_id]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [contract_id]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [related_id]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [related_object]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [parent_id]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT ((0)) FOR [is_cloned]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [notify_before_time]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [notify_before_time_type]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [notify_before_type]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[reminders] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[roles] ADD  DEFAULT (NULL) FOR [created_at]
GO
ALTER TABLE [dbo].[roles] ADD  DEFAULT (NULL) FOR [updated_at]
GO
ALTER TABLE [dbo].[saml_configuration] ADD  DEFAULT (NULL) FOR [keyValue]
GO
ALTER TABLE [dbo].[shares_movement_headers] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[shares_movements] ADD  DEFAULT (NULL) FOR [shares_movement_header_id]
GO
ALTER TABLE [dbo].[shares_movements] ADD  DEFAULT (NULL) FOR [initiatedOn]
GO
ALTER TABLE [dbo].[shares_movements] ADD  DEFAULT (NULL) FOR [executedOn]
GO
ALTER TABLE [dbo].[shares_movements] ADD  DEFAULT (NULL) FOR [category]
GO
ALTER TABLE [dbo].[shares_movements] ADD  DEFAULT ('') FOR [comments]
GO
ALTER TABLE [dbo].[shares_movements] ADD  DEFAULT (NULL) FOR [to_member_id]
GO
ALTER TABLE [dbo].[shares_movements] ADD  DEFAULT (NULL) FOR [from_member_id]
GO
ALTER TABLE [dbo].[signature_signee] ADD  DEFAULT ((0)) FOR [is_requester_manager]
GO
ALTER TABLE [dbo].[signature_signee] ADD  DEFAULT ((0)) FOR [is_board_member]
GO
ALTER TABLE [dbo].[signature_signee] ADD  DEFAULT ((0)) FOR [is_shareholder]
GO
ALTER TABLE [dbo].[stage_statuses_languages] ADD  DEFAULT (NULL) FOR [name]
GO
ALTER TABLE [dbo].[surety_bonds] ADD  DEFAULT ('GETDATE()') FOR [createdOn]
GO
ALTER TABLE [dbo].[surety_bonds] ADD  DEFAULT ('GETDATE()') FOR [modifiedOn]
GO
ALTER TABLE [dbo].[surety_bonds] ADD  DEFAULT ('no') FOR [archived]
GO
ALTER TABLE [dbo].[suspect_arrest] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[suspect_arrest] ADD  DEFAULT ('no') FOR [archived]
GO
ALTER TABLE [dbo].[system_configurations] ADD  DEFAULT (NULL) FOR [keyValue]
GO
ALTER TABLE [dbo].[system_preferences] ADD  DEFAULT (NULL) FOR [groupName]
GO
ALTER TABLE [dbo].[system_preferences] ADD  DEFAULT (NULL) FOR [keyValue]
GO
ALTER TABLE [dbo].[task_board_column_options] ADD  DEFAULT (NULL) FOR [task_board_id]
GO
ALTER TABLE [dbo].[task_board_saved_filters] ADD  DEFAULT (NULL) FOR [keyValue]
GO
ALTER TABLE [dbo].[task_boards] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[task_boards] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[task_boards] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[task_boards] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[task_comments] ADD  DEFAULT ('0') FOR [edited]
GO
ALTER TABLE [dbo].[task_comments] ADD  DEFAULT (getdate()) FOR [createdOn]
GO
ALTER TABLE [dbo].[task_comments] ADD  DEFAULT (getdate()) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[task_statuses] ADD  DEFAULT ('in progress') FOR [category]
GO
ALTER TABLE [dbo].[task_statuses] ADD  DEFAULT ('1') FOR [isGlobal]
GO
ALTER TABLE [dbo].[task_workflow_status_relation] ADD  DEFAULT ('0') FOR [start_point]
GO
ALTER TABLE [dbo].[task_workflow_status_transition] ADD  DEFAULT (NULL) FOR [comments]
GO
ALTER TABLE [dbo].[task_workflow_status_transition_history] ADD  DEFAULT (NULL) FOR [from_step]
GO
ALTER TABLE [dbo].[task_workflow_status_transition_history] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[task_workflow_status_transition_history] ADD  DEFAULT (getdate()) FOR [changed_on]
GO
ALTER TABLE [dbo].[task_workflows] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[task_workflows] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[task_workflows] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[task_workflows] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT (NULL) FOR [legal_case_id]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT (NULL) FOR [contract_id]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT (NULL) FOR [stage]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT (NULL) FOR [private]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT ('medium') FOR [priority]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT (NULL) FOR [task_location_id]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT (NULL) FOR [estimated_effort]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT ('no') FOR [archived]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT (NULL) FOR [hideFromBoard]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT (NULL) FOR [reporter]
GO
ALTER TABLE [dbo].[tasks] ADD  DEFAULT ('1') FOR [workflow]
GO
ALTER TABLE [dbo].[taxes] ADD  DEFAULT ('') FOR [code]
GO
ALTER TABLE [dbo].[terms] ADD  DEFAULT ((0)) FOR [number_of_days]
GO
ALTER TABLE [dbo].[time_types] ADD  DEFAULT (NULL) FOR [default_comment]
GO
ALTER TABLE [dbo].[time_types] ADD  DEFAULT (NULL) FOR [default_time_effort]
GO
ALTER TABLE [dbo].[trigger_action_task_values] ADD  DEFAULT ('') FOR [title]
GO
ALTER TABLE [dbo].[user_activity_logs] ADD  DEFAULT (NULL) FOR [task_id]
GO
ALTER TABLE [dbo].[user_activity_logs] ADD  DEFAULT (NULL) FOR [legal_case_id]
GO
ALTER TABLE [dbo].[user_activity_logs] ADD  DEFAULT (NULL) FOR [client_id]
GO
ALTER TABLE [dbo].[user_activity_logs] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[user_activity_logs] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[user_activity_logs] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[user_activity_logs] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[user_activity_logs] ADD  DEFAULT (NULL) FOR [rate]
GO
ALTER TABLE [dbo].[user_activity_logs] ADD  DEFAULT (NULL) FOR [rate_system]
GO
ALTER TABLE [dbo].[user_api_keys] ADD  DEFAULT (getdate()) FOR [key_generated_on]
GO
ALTER TABLE [dbo].[user_autologin] ADD  DEFAULT (getdate()) FOR [last_login]
GO
ALTER TABLE [dbo].[user_autologin] ADD  DEFAULT (NULL) FOR [channel]
GO
ALTER TABLE [dbo].[user_changes_authorization] ADD  DEFAULT (NULL) FOR [changeType]
GO
ALTER TABLE [dbo].[user_changes_authorization] ADD  DEFAULT (NULL) FOR [columnName]
GO
ALTER TABLE [dbo].[user_changes_authorization] ADD  DEFAULT (NULL) FOR [columnValue]
GO
ALTER TABLE [dbo].[user_changes_authorization] ADD  DEFAULT (NULL) FOR [columnStatus]
GO
ALTER TABLE [dbo].[user_changes_authorization] ADD  DEFAULT (NULL) FOR [columnRequestedValue]
GO
ALTER TABLE [dbo].[user_changes_authorization] ADD  DEFAULT (NULL) FOR [columnType]
GO
ALTER TABLE [dbo].[user_changes_authorization] ADD  DEFAULT (NULL) FOR [affectedUserId]
GO
ALTER TABLE [dbo].[user_changes_authorization] ADD  DEFAULT (NULL) FOR [makerId]
GO
ALTER TABLE [dbo].[user_changes_authorization] ADD  DEFAULT (NULL) FOR [checkerId]
GO
ALTER TABLE [dbo].[user_changes_authorization] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[user_changes_authorization] ADD  DEFAULT (NULL) FOR [authorizedOn]
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization] ADD  DEFAULT (NULL) FOR [columnName]
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization] ADD  DEFAULT (NULL) FOR [module]
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization] ADD  DEFAULT (NULL) FOR [columnValue]
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization] ADD  DEFAULT (NULL) FOR [columnStatus]
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization] ADD  DEFAULT (NULL) FOR [columnRequestedValue]
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization] ADD  DEFAULT (NULL) FOR [columnApprovedValue]
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization] ADD  DEFAULT (NULL) FOR [affectedUserGroupId]
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization] ADD  DEFAULT (NULL) FOR [makerId]
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization] ADD  DEFAULT (NULL) FOR [checkerId]
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization] ADD  DEFAULT (NULL) FOR [authorizedOn]
GO
ALTER TABLE [dbo].[user_groups] ADD  DEFAULT ('0') FOR [flagNeedApproval]
GO
ALTER TABLE [dbo].[user_groups] ADD  DEFAULT ('0') FOR [needApprovalOnAdd]
GO
ALTER TABLE [dbo].[user_groups] ADD  DEFAULT ('0') FOR [system_group]
GO
ALTER TABLE [dbo].[user_groups] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[user_groups] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[user_groups] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[user_groups] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[user_groups_changes_authorization] ADD  DEFAULT (NULL) FOR [changeType]
GO
ALTER TABLE [dbo].[user_groups_changes_authorization] ADD  DEFAULT (NULL) FOR [columnName]
GO
ALTER TABLE [dbo].[user_groups_changes_authorization] ADD  DEFAULT (NULL) FOR [columnValue]
GO
ALTER TABLE [dbo].[user_groups_changes_authorization] ADD  DEFAULT (NULL) FOR [columnStatus]
GO
ALTER TABLE [dbo].[user_groups_changes_authorization] ADD  DEFAULT (NULL) FOR [columnRequestedValue]
GO
ALTER TABLE [dbo].[user_groups_changes_authorization] ADD  DEFAULT (NULL) FOR [columnType]
GO
ALTER TABLE [dbo].[user_groups_changes_authorization] ADD  DEFAULT (NULL) FOR [affectedUserGroupId]
GO
ALTER TABLE [dbo].[user_groups_changes_authorization] ADD  DEFAULT (NULL) FOR [makerId]
GO
ALTER TABLE [dbo].[user_groups_changes_authorization] ADD  DEFAULT (NULL) FOR [checkerId]
GO
ALTER TABLE [dbo].[user_groups_changes_authorization] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[user_groups_changes_authorization] ADD  DEFAULT (NULL) FOR [authorizedOn]
GO
ALTER TABLE [dbo].[user_passwords] ADD  DEFAULT (getdate()) FOR [created]
GO
ALTER TABLE [dbo].[user_preferences] ADD  DEFAULT (NULL) FOR [keyValue]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('Active') FOR [status]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [gender]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [title]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [father]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [mother]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT (NULL) FOR [employeeId]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT (NULL) FOR [ad_userCode]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT (NULL) FOR [user_code]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT (NULL) FOR [dateOfBirth]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT (NULL) FOR [department]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [nationality]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [jobTitle]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('no') FOR [overridePrivacy]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('0') FOR [flagChangePassword]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('0') FOR [flagNeedApproval]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('no') FOR [isLawyer]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [website]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [phone]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [fax]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [mobile]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [address1]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [address2]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [city]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [state]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [zip]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('') FOR [country]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT (NULL) FOR [seniority_level_id]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT ('0') FOR [forgetPasswordFlag]
GO
ALTER TABLE [dbo].[user_profiles] ADD  DEFAULT (NULL) FOR [forgetPasswordUrlCreatedOn]
GO
ALTER TABLE [dbo].[user_rate_per_hour] ADD  DEFAULT ((120000)) FOR [yearly_billable_target]
GO
ALTER TABLE [dbo].[user_rate_per_hour] ADD  DEFAULT ((260)) FOR [working_days_per_year]
GO
ALTER TABLE [dbo].[user_reports] ADD  DEFAULT (NULL) FOR [keyValue]
GO
ALTER TABLE [dbo].[user_signature_attachments] ADD  DEFAULT ('signature') FOR [type]
GO
ALTER TABLE [dbo].[user_signature_attachments] ADD  DEFAULT ('0') FOR [is_default]
GO
ALTER TABLE [dbo].[user_temp] ADD  DEFAULT (getdate()) FOR [created]
GO
ALTER TABLE [dbo].[users] ADD  DEFAULT ('0') FOR [isAd]
GO
ALTER TABLE [dbo].[users] ADD  DEFAULT ('core') FOR [type]
GO
ALTER TABLE [dbo].[users] ADD  DEFAULT ('0') FOR [banned]
GO
ALTER TABLE [dbo].[users] ADD  DEFAULT (NULL) FOR [ban_reason]
GO
ALTER TABLE [dbo].[users] ADD  DEFAULT (NULL) FOR [last_ip]
GO
ALTER TABLE [dbo].[users] ADD  DEFAULT (NULL) FOR [last_login]
GO
ALTER TABLE [dbo].[users] ADD  DEFAULT (NULL) FOR [created]
GO
ALTER TABLE [dbo].[users] ADD  DEFAULT (NULL) FOR [modified]
GO
ALTER TABLE [dbo].[users] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[users] ADD  DEFAULT (NULL) FOR [user_guide]
GO
ALTER TABLE [dbo].[vendors] ADD  DEFAULT (NULL) FOR [company_id]
GO
ALTER TABLE [dbo].[vendors] ADD  DEFAULT (NULL) FOR [contact_id]
GO
ALTER TABLE [dbo].[vendors] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[vendors] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[vendors] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[vendors] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[voucher_headers] ADD  DEFAULT (NULL) FOR [referenceNum]
GO
ALTER TABLE [dbo].[voucher_headers] ADD  DEFAULT (NULL) FOR [attachment]
GO
ALTER TABLE [dbo].[voucher_headers] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[voucher_headers] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[voucher_headers] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[voucher_headers] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[workflow_status] ADD  DEFAULT ('0') FOR [isGlobal]
GO
ALTER TABLE [dbo].[workflow_status] ADD  DEFAULT ('in progress') FOR [category]
GO
ALTER TABLE [dbo].[workflow_status_relation] ADD  DEFAULT ('0') FOR [start_point]
GO
ALTER TABLE [dbo].[workflow_status_transition] ADD  DEFAULT (NULL) FOR [limitToGroup]
GO
ALTER TABLE [dbo].[workflow_status_transition] ADD  DEFAULT (NULL) FOR [limitToUser]
GO
ALTER TABLE [dbo].[workflow_status_transition] ADD  DEFAULT (NULL) FOR [comments]
GO
ALTER TABLE [dbo].[workflow_status_transition_history] ADD  DEFAULT (NULL) FOR [fromStep]
GO
ALTER TABLE [dbo].[workflow_status_transition_history] ADD  DEFAULT (NULL) FOR [user_id]
GO
ALTER TABLE [dbo].[workflow_status_transition_history] ADD  DEFAULT (getdate()) FOR [changedOn]
GO
ALTER TABLE [dbo].[workflow_status_transition_history] ADD  DEFAULT (NULL) FOR [modifiedByChannel]
GO
ALTER TABLE [dbo].[workflows] ADD  DEFAULT ('0') FOR [isDeleted]
GO
ALTER TABLE [dbo].[workflows] ADD  DEFAULT (NULL) FOR [createdOn]
GO
ALTER TABLE [dbo].[workflows] ADD  DEFAULT (NULL) FOR [createdBy]
GO
ALTER TABLE [dbo].[workflows] ADD  DEFAULT (NULL) FOR [modifiedOn]
GO
ALTER TABLE [dbo].[workflows] ADD  DEFAULT (NULL) FOR [modifiedBy]
GO
ALTER TABLE [dbo].[account_number_prefix_per_entity]  WITH CHECK ADD  CONSTRAINT [fk_account_number_prefix_per_entity_1] FOREIGN KEY([organization_id])
REFERENCES [dbo].[organizations] ([id])
GO
ALTER TABLE [dbo].[account_number_prefix_per_entity] CHECK CONSTRAINT [fk_account_number_prefix_per_entity_1]
GO
ALTER TABLE [dbo].[account_number_prefix_per_entity]  WITH CHECK ADD  CONSTRAINT [fk_account_number_prefix_per_entity_2] FOREIGN KEY([account_type_id])
REFERENCES [dbo].[accounts_types] ([id])
GO
ALTER TABLE [dbo].[account_number_prefix_per_entity] CHECK CONSTRAINT [fk_account_number_prefix_per_entity_2]
GO
ALTER TABLE [dbo].[accounts]  WITH CHECK ADD  CONSTRAINT [accounts_ibfk_1] FOREIGN KEY([account_type_id])
REFERENCES [dbo].[accounts_types] ([id])
GO
ALTER TABLE [dbo].[accounts] CHECK CONSTRAINT [accounts_ibfk_1]
GO
ALTER TABLE [dbo].[accounts]  WITH CHECK ADD  CONSTRAINT [accounts_ibfk_2] FOREIGN KEY([organization_id])
REFERENCES [dbo].[organizations] ([id])
GO
ALTER TABLE [dbo].[accounts] CHECK CONSTRAINT [accounts_ibfk_2]
GO
ALTER TABLE [dbo].[accounts]  WITH CHECK ADD  CONSTRAINT [accounts_ibfk_3] FOREIGN KEY([currency_id])
REFERENCES [dbo].[countries] ([id])
GO
ALTER TABLE [dbo].[accounts] CHECK CONSTRAINT [accounts_ibfk_3]
GO
ALTER TABLE [dbo].[accounts_users]  WITH CHECK ADD  CONSTRAINT [accounts_users_ibfk_1] FOREIGN KEY([userId])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[accounts_users] CHECK CONSTRAINT [accounts_users_ibfk_1]
GO
ALTER TABLE [dbo].[accounts_users]  WITH CHECK ADD  CONSTRAINT [accounts_users_ibfk_2] FOREIGN KEY([accountId])
REFERENCES [dbo].[accounts] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[accounts_users] CHECK CONSTRAINT [accounts_users_ibfk_2]
GO
ALTER TABLE [dbo].[advisor_email_template_languages]  WITH CHECK ADD  CONSTRAINT [fk_advisor_email_template_languages_1] FOREIGN KEY([advisor_email_template_id])
REFERENCES [dbo].[advisor_email_templates] ([id])
GO
ALTER TABLE [dbo].[advisor_email_template_languages] CHECK CONSTRAINT [fk_advisor_email_template_languages_1]
GO
ALTER TABLE [dbo].[advisor_email_template_languages]  WITH CHECK ADD  CONSTRAINT [fk_advisor_email_template_languages_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[advisor_email_template_languages] CHECK CONSTRAINT [fk_advisor_email_template_languages_2]
GO
ALTER TABLE [dbo].[advisor_permissions]  WITH CHECK ADD  CONSTRAINT [fk_advisor_permissions_1] FOREIGN KEY([workflow_status_transition_id])
REFERENCES [dbo].[workflow_status_transition] ([id])
GO
ALTER TABLE [dbo].[advisor_permissions] CHECK CONSTRAINT [fk_advisor_permissions_1]
GO
ALTER TABLE [dbo].[advisor_permissions]  WITH CHECK ADD  CONSTRAINT [fk_advisor_permissions_2] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[workflows] ([id])
GO
ALTER TABLE [dbo].[advisor_permissions] CHECK CONSTRAINT [fk_advisor_permissions_2]
GO
ALTER TABLE [dbo].[advisor_task_comments]  WITH CHECK ADD  CONSTRAINT [fk_advisor_task_comments_1] FOREIGN KEY([advisor_task_id])
REFERENCES [dbo].[advisor_tasks] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[advisor_task_comments] CHECK CONSTRAINT [fk_advisor_task_comments_1]
GO
ALTER TABLE [dbo].[advisor_task_sharedwith_users]  WITH CHECK ADD  CONSTRAINT [fk_advisor_task_sharedwith_users_1] FOREIGN KEY([advisor_task_id])
REFERENCES [dbo].[advisor_tasks] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[advisor_task_sharedwith_users] CHECK CONSTRAINT [fk_advisor_task_sharedwith_users_1]
GO
ALTER TABLE [dbo].[advisor_task_sharedwith_users]  WITH CHECK ADD  CONSTRAINT [fk_advisor_task_sharedwith_users_2] FOREIGN KEY([advisor_id])
REFERENCES [dbo].[advisor_users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[advisor_task_sharedwith_users] CHECK CONSTRAINT [fk_advisor_task_sharedwith_users_2]
GO
ALTER TABLE [dbo].[advisor_task_type_workflows]  WITH CHECK ADD  CONSTRAINT [fk_advisor_task_workflow_types_1] FOREIGN KEY([advisor_task_workflow_id])
REFERENCES [dbo].[advisor_task_workflows] ([id])
GO
ALTER TABLE [dbo].[advisor_task_type_workflows] CHECK CONSTRAINT [fk_advisor_task_workflow_types_1]
GO
ALTER TABLE [dbo].[advisor_task_type_workflows]  WITH CHECK ADD  CONSTRAINT [fk_advisor_task_workflow_types_2] FOREIGN KEY([advisor_task_type_id])
REFERENCES [dbo].[advisor_task_types] ([id])
GO
ALTER TABLE [dbo].[advisor_task_type_workflows] CHECK CONSTRAINT [fk_advisor_task_workflow_types_2]
GO
ALTER TABLE [dbo].[advisor_task_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_advisor_task_types_languages_1] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[advisor_task_types_languages] CHECK CONSTRAINT [fk_advisor_task_types_languages_1]
GO
ALTER TABLE [dbo].[advisor_task_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_advisor_task_types_languages_2] FOREIGN KEY([advisor_task_type_id])
REFERENCES [dbo].[advisor_task_types] ([id])
GO
ALTER TABLE [dbo].[advisor_task_types_languages] CHECK CONSTRAINT [fk_advisor_task_types_languages_2]
GO
ALTER TABLE [dbo].[advisor_task_workflow_statuses]  WITH CHECK ADD  CONSTRAINT [fk_advisor_task_workflow_statuses_1] FOREIGN KEY([advisor_task_workflow_id])
REFERENCES [dbo].[advisor_task_workflows] ([id])
GO
ALTER TABLE [dbo].[advisor_task_workflow_statuses] CHECK CONSTRAINT [fk_advisor_task_workflow_statuses_1]
GO
ALTER TABLE [dbo].[advisor_task_workflow_statuses]  WITH CHECK ADD  CONSTRAINT [fk_advisor_task_workflow_statuses_2] FOREIGN KEY([advisor_task_status_id])
REFERENCES [dbo].[advisor_task_statuses] ([id])
GO
ALTER TABLE [dbo].[advisor_task_workflow_statuses] CHECK CONSTRAINT [fk_advisor_task_workflow_statuses_2]
GO
ALTER TABLE [dbo].[advisor_task_workflows_permissions]  WITH CHECK ADD  CONSTRAINT [fk_advisor_task_workflows_permissions_1] FOREIGN KEY([advisor_task_workflow_id])
REFERENCES [dbo].[advisor_task_workflows] ([id])
GO
ALTER TABLE [dbo].[advisor_task_workflows_permissions] CHECK CONSTRAINT [fk_advisor_task_workflows_permissions_1]
GO
ALTER TABLE [dbo].[advisor_tasks]  WITH CHECK ADD  CONSTRAINT [fk_advisor_tasks_1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[advisor_tasks] CHECK CONSTRAINT [fk_advisor_tasks_1]
GO
ALTER TABLE [dbo].[advisor_tasks]  WITH CHECK ADD  CONSTRAINT [fk_advisor_tasks_10] FOREIGN KEY([workflow])
REFERENCES [dbo].[advisor_task_workflows] ([id])
GO
ALTER TABLE [dbo].[advisor_tasks] CHECK CONSTRAINT [fk_advisor_tasks_10]
GO
ALTER TABLE [dbo].[advisor_tasks]  WITH CHECK ADD  CONSTRAINT [fk_advisor_tasks_2] FOREIGN KEY([advisor_task_status_id])
REFERENCES [dbo].[advisor_task_statuses] ([id])
GO
ALTER TABLE [dbo].[advisor_tasks] CHECK CONSTRAINT [fk_advisor_tasks_2]
GO
ALTER TABLE [dbo].[advisor_tasks]  WITH CHECK ADD  CONSTRAINT [fk_advisor_tasks_3] FOREIGN KEY([advisor_task_type_id])
REFERENCES [dbo].[advisor_task_types] ([id])
GO
ALTER TABLE [dbo].[advisor_tasks] CHECK CONSTRAINT [fk_advisor_tasks_3]
GO
ALTER TABLE [dbo].[advisor_tasks]  WITH CHECK ADD  CONSTRAINT [fk_advisor_tasks_4] FOREIGN KEY([advisor_task_location_id])
REFERENCES [dbo].[advisor_task_locations] ([id])
GO
ALTER TABLE [dbo].[advisor_tasks] CHECK CONSTRAINT [fk_advisor_tasks_4]
GO
ALTER TABLE [dbo].[advisor_tasks]  WITH CHECK ADD  CONSTRAINT [fk_advisor_tasks_5] FOREIGN KEY([advisor_id])
REFERENCES [dbo].[advisor_users] ([id])
GO
ALTER TABLE [dbo].[advisor_tasks] CHECK CONSTRAINT [fk_advisor_tasks_5]
GO
ALTER TABLE [dbo].[advisor_tasks]  WITH CHECK ADD  CONSTRAINT [fk_advisor_tasks_6] FOREIGN KEY([assigned_to])
REFERENCES [dbo].[advisor_users] ([id])
GO
ALTER TABLE [dbo].[advisor_tasks] CHECK CONSTRAINT [fk_advisor_tasks_6]
GO
ALTER TABLE [dbo].[advisor_tasks]  WITH CHECK ADD  CONSTRAINT [fk_advisor_tasks_7] FOREIGN KEY([reporter])
REFERENCES [dbo].[advisor_users] ([id])
GO
ALTER TABLE [dbo].[advisor_tasks] CHECK CONSTRAINT [fk_advisor_tasks_7]
GO
ALTER TABLE [dbo].[advisor_tasks]  WITH CHECK ADD  CONSTRAINT [fk_advisor_tasks_8] FOREIGN KEY([createdBy])
REFERENCES [dbo].[advisor_users] ([id])
GO
ALTER TABLE [dbo].[advisor_tasks] CHECK CONSTRAINT [fk_advisor_tasks_8]
GO
ALTER TABLE [dbo].[advisor_tasks]  WITH CHECK ADD  CONSTRAINT [fk_advisor_tasks_9] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[advisor_users] ([id])
GO
ALTER TABLE [dbo].[advisor_tasks] CHECK CONSTRAINT [fk_advisor_tasks_9]
GO
ALTER TABLE [dbo].[advisor_timer_time_logs]  WITH CHECK ADD  CONSTRAINT [fk_advisor_timer_time_logs_1] FOREIGN KEY([advisor_timer_id])
REFERENCES [dbo].[advisor_timers] ([id])
GO
ALTER TABLE [dbo].[advisor_timer_time_logs] CHECK CONSTRAINT [fk_advisor_timer_time_logs_1]
GO
ALTER TABLE [dbo].[advisor_timers]  WITH CHECK ADD  CONSTRAINT [fk_advisor_timers_1] FOREIGN KEY([advisor_id])
REFERENCES [dbo].[advisor_users] ([id])
GO
ALTER TABLE [dbo].[advisor_timers] CHECK CONSTRAINT [fk_advisor_timers_1]
GO
ALTER TABLE [dbo].[advisor_timers]  WITH CHECK ADD  CONSTRAINT [fk_advisor_timers_2] FOREIGN KEY([advisor_task_id])
REFERENCES [dbo].[advisor_tasks] ([id])
GO
ALTER TABLE [dbo].[advisor_timers] CHECK CONSTRAINT [fk_advisor_timers_2]
GO
ALTER TABLE [dbo].[advisor_timers]  WITH CHECK ADD  CONSTRAINT [fk_advisor_timers_3] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[advisor_timers] CHECK CONSTRAINT [fk_advisor_timers_3]
GO
ALTER TABLE [dbo].[advisor_timers]  WITH CHECK ADD  CONSTRAINT [fk_advisor_timers_4] FOREIGN KEY([time_type_id])
REFERENCES [dbo].[time_types] ([id])
GO
ALTER TABLE [dbo].[advisor_timers] CHECK CONSTRAINT [fk_advisor_timers_4]
GO
ALTER TABLE [dbo].[advisor_user_activity_logs]  WITH CHECK ADD  CONSTRAINT [fk_advisor_user_activity_logs_1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
ON UPDATE CASCADE
GO
ALTER TABLE [dbo].[advisor_user_activity_logs] CHECK CONSTRAINT [fk_advisor_user_activity_logs_1]
GO
ALTER TABLE [dbo].[advisor_user_activity_logs]  WITH CHECK ADD  CONSTRAINT [fk_advisor_user_activity_logs_2] FOREIGN KEY([advisor_task_id])
REFERENCES [dbo].[advisor_tasks] ([id])
ON UPDATE CASCADE
GO
ALTER TABLE [dbo].[advisor_user_activity_logs] CHECK CONSTRAINT [fk_advisor_user_activity_logs_2]
GO
ALTER TABLE [dbo].[advisor_user_activity_logs]  WITH CHECK ADD  CONSTRAINT [fk_advisor_user_activity_logs_3] FOREIGN KEY([advisor_id])
REFERENCES [dbo].[advisor_users] ([id])
ON UPDATE CASCADE
GO
ALTER TABLE [dbo].[advisor_user_activity_logs] CHECK CONSTRAINT [fk_advisor_user_activity_logs_3]
GO
ALTER TABLE [dbo].[advisor_user_activity_logs]  WITH CHECK ADD  CONSTRAINT [fk_advisor_user_activity_logs_4] FOREIGN KEY([time_type_id])
REFERENCES [dbo].[time_types] ([id])
ON UPDATE CASCADE
GO
ALTER TABLE [dbo].[advisor_user_activity_logs] CHECK CONSTRAINT [fk_advisor_user_activity_logs_4]
GO
ALTER TABLE [dbo].[advisor_user_activity_logs]  WITH CHECK ADD  CONSTRAINT [fk_advisor_user_activity_logs_5] FOREIGN KEY([client_id])
REFERENCES [dbo].[clients] ([id])
ON UPDATE CASCADE
GO
ALTER TABLE [dbo].[advisor_user_activity_logs] CHECK CONSTRAINT [fk_advisor_user_activity_logs_5]
GO
ALTER TABLE [dbo].[advisor_user_preferences]  WITH CHECK ADD  CONSTRAINT [fk_advisor_user_preferences_1] FOREIGN KEY([advisor_user_id])
REFERENCES [dbo].[advisor_users] ([id])
GO
ALTER TABLE [dbo].[advisor_user_preferences] CHECK CONSTRAINT [fk_advisor_user_preferences_1]
GO
ALTER TABLE [dbo].[advisor_users]  WITH CHECK ADD  CONSTRAINT [fk_advisor_users_1] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[advisor_users] CHECK CONSTRAINT [fk_advisor_users_1]
GO
ALTER TABLE [dbo].[advisor_users]  WITH CHECK ADD  CONSTRAINT [fk_advisor_users_2] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[advisor_users] CHECK CONSTRAINT [fk_advisor_users_2]
GO
ALTER TABLE [dbo].[applicable_law_language]  WITH CHECK ADD  CONSTRAINT [fk_applicable_law_language_1] FOREIGN KEY([app_law_id])
REFERENCES [dbo].[applicable_law] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[applicable_law_language] CHECK CONSTRAINT [fk_applicable_law_language_1]
GO
ALTER TABLE [dbo].[applicable_law_language]  WITH CHECK ADD  CONSTRAINT [fk_applicable_law_language_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[applicable_law_language] CHECK CONSTRAINT [fk_applicable_law_language_2]
GO
ALTER TABLE [dbo].[approval_assignee]  WITH CHECK ADD  CONSTRAINT [fk_approval_assignee_1] FOREIGN KEY([approval_id])
REFERENCES [dbo].[approval] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[approval_assignee] CHECK CONSTRAINT [fk_approval_assignee_1]
GO
ALTER TABLE [dbo].[approval_assignee_bm_role]  WITH CHECK ADD  CONSTRAINT [fk_approval_assignee_bm_role_1] FOREIGN KEY([assignee_id])
REFERENCES [dbo].[approval_assignee] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[approval_assignee_bm_role] CHECK CONSTRAINT [fk_approval_assignee_bm_role_1]
GO
ALTER TABLE [dbo].[approval_assignee_bm_role]  WITH CHECK ADD  CONSTRAINT [fk_approval_assignee_bm_role_2] FOREIGN KEY([role_id])
REFERENCES [dbo].[board_member_roles] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[approval_assignee_bm_role] CHECK CONSTRAINT [fk_approval_assignee_bm_role_2]
GO
ALTER TABLE [dbo].[approval_criteria]  WITH CHECK ADD  CONSTRAINT [fk_approval_criteria_1] FOREIGN KEY([approval_id])
REFERENCES [dbo].[approval] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[approval_criteria] CHECK CONSTRAINT [fk_approval_criteria_1]
GO
ALTER TABLE [dbo].[assignments_relation]  WITH CHECK ADD  CONSTRAINT [fk_assignments_1] FOREIGN KEY([relation])
REFERENCES [dbo].[assignments] ([id])
GO
ALTER TABLE [dbo].[assignments_relation] CHECK CONSTRAINT [fk_assignments_1]
GO
ALTER TABLE [dbo].[audit_log_details]  WITH CHECK ADD  CONSTRAINT [fk_audit_log_details_audit_logs1] FOREIGN KEY([log_id])
REFERENCES [dbo].[audit_logs] ([id])
GO
ALTER TABLE [dbo].[audit_log_details] CHECK CONSTRAINT [fk_audit_log_details_audit_logs1]
GO
ALTER TABLE [dbo].[bill_details]  WITH CHECK ADD  CONSTRAINT [bill_details_ibfk_1] FOREIGN KEY([bill_header_id])
REFERENCES [dbo].[bill_headers] ([id])
GO
ALTER TABLE [dbo].[bill_details] CHECK CONSTRAINT [bill_details_ibfk_1]
GO
ALTER TABLE [dbo].[bill_details]  WITH CHECK ADD  CONSTRAINT [bill_details_ibfk_2] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[bill_details] CHECK CONSTRAINT [bill_details_ibfk_2]
GO
ALTER TABLE [dbo].[bill_headers]  WITH CHECK ADD  CONSTRAINT [bill_headers_ibfk_1] FOREIGN KEY([voucher_header_id])
REFERENCES [dbo].[voucher_headers] ([id])
GO
ALTER TABLE [dbo].[bill_headers] CHECK CONSTRAINT [bill_headers_ibfk_1]
GO
ALTER TABLE [dbo].[bill_headers]  WITH CHECK ADD  CONSTRAINT [bill_headers_ibfk_2] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[bill_headers] CHECK CONSTRAINT [bill_headers_ibfk_2]
GO
ALTER TABLE [dbo].[bill_headers]  WITH CHECK ADD  CONSTRAINT [bill_headers_ibfk_3] FOREIGN KEY([client_id])
REFERENCES [dbo].[clients] ([id])
GO
ALTER TABLE [dbo].[bill_headers] CHECK CONSTRAINT [bill_headers_ibfk_3]
GO
ALTER TABLE [dbo].[bill_payment_bills]  WITH CHECK ADD  CONSTRAINT [bill_payment_bills_ibfk_1] FOREIGN KEY([bill_payment_id])
REFERENCES [dbo].[bill_payments] ([id])
GO
ALTER TABLE [dbo].[bill_payment_bills] CHECK CONSTRAINT [bill_payment_bills_ibfk_1]
GO
ALTER TABLE [dbo].[bill_payment_bills]  WITH CHECK ADD  CONSTRAINT [bill_payment_bills_ibfk_2] FOREIGN KEY([bill_header_id])
REFERENCES [dbo].[bill_headers] ([id])
GO
ALTER TABLE [dbo].[bill_payment_bills] CHECK CONSTRAINT [bill_payment_bills_ibfk_2]
GO
ALTER TABLE [dbo].[bill_payments]  WITH CHECK ADD  CONSTRAINT [bill_payments_ibfk_1] FOREIGN KEY([voucher_header_id])
REFERENCES [dbo].[voucher_headers] ([id])
GO
ALTER TABLE [dbo].[bill_payments] CHECK CONSTRAINT [bill_payments_ibfk_1]
GO
ALTER TABLE [dbo].[bill_payments]  WITH CHECK ADD  CONSTRAINT [bill_payments_ibfk_2] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[bill_payments] CHECK CONSTRAINT [bill_payments_ibfk_2]
GO
ALTER TABLE [dbo].[bill_payments]  WITH CHECK ADD  CONSTRAINT [bill_payments_ibfk_3] FOREIGN KEY([supplier_account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[bill_payments] CHECK CONSTRAINT [bill_payments_ibfk_3]
GO
ALTER TABLE [dbo].[board_members]  WITH CHECK ADD  CONSTRAINT [fk_board_members_board_member_roles1] FOREIGN KEY([board_member_role_id])
REFERENCES [dbo].[board_member_roles] ([id])
GO
ALTER TABLE [dbo].[board_members] CHECK CONSTRAINT [fk_board_members_board_member_roles1]
GO
ALTER TABLE [dbo].[board_members]  WITH CHECK ADD  CONSTRAINT [fk_board_members_companies1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[board_members] CHECK CONSTRAINT [fk_board_members_companies1]
GO
ALTER TABLE [dbo].[board_members]  WITH CHECK ADD  CONSTRAINT [fk_board_members_lookup_members1] FOREIGN KEY([member_id])
REFERENCES [dbo].[lookup_members] ([id])
GO
ALTER TABLE [dbo].[board_members] CHECK CONSTRAINT [fk_board_members_lookup_members1]
GO
ALTER TABLE [dbo].[board_post_filters]  WITH CHECK ADD  CONSTRAINT [fk_board_post_filters_1] FOREIGN KEY([board_id])
REFERENCES [dbo].[planning_boards] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[board_post_filters] CHECK CONSTRAINT [fk_board_post_filters_1]
GO
ALTER TABLE [dbo].[board_post_filters_user]  WITH CHECK ADD  CONSTRAINT [board_post_filters_user_ibfk_1] FOREIGN KEY([board_post_filters_id])
REFERENCES [dbo].[board_post_filters] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[board_post_filters_user] CHECK CONSTRAINT [board_post_filters_user_ibfk_1]
GO
ALTER TABLE [dbo].[board_post_filters_user]  WITH CHECK ADD  CONSTRAINT [board_post_filters_user_ibfk_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[board_post_filters_user] CHECK CONSTRAINT [board_post_filters_user_ibfk_2]
GO
ALTER TABLE [dbo].[board_task_post_filters]  WITH CHECK ADD  CONSTRAINT [fk_board_task_post_filters_1] FOREIGN KEY([board_id])
REFERENCES [dbo].[task_boards] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[board_task_post_filters] CHECK CONSTRAINT [fk_board_task_post_filters_1]
GO
ALTER TABLE [dbo].[board_task_post_filters_user]  WITH CHECK ADD  CONSTRAINT [board_task_post_filters_user_ibfk_1] FOREIGN KEY([board_post_filters_id])
REFERENCES [dbo].[board_task_post_filters] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[board_task_post_filters_user] CHECK CONSTRAINT [board_task_post_filters_user_ibfk_1]
GO
ALTER TABLE [dbo].[board_task_post_filters_user]  WITH CHECK ADD  CONSTRAINT [board_task_post_filters_user_ibfk_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[board_task_post_filters_user] CHECK CONSTRAINT [board_task_post_filters_user_ibfk_2]
GO
ALTER TABLE [dbo].[case_closure_recommendation]  WITH CHECK ADD  CONSTRAINT [FK_CaseClosure_ApprovedBy_Users] FOREIGN KEY([approvedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[case_closure_recommendation] CHECK CONSTRAINT [FK_CaseClosure_ApprovedBy_Users]
GO
ALTER TABLE [dbo].[case_closure_recommendation]  WITH CHECK ADD  CONSTRAINT [FK_CaseClosure_CreatedBy_Users] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[case_closure_recommendation] CHECK CONSTRAINT [FK_CaseClosure_CreatedBy_Users]
GO
ALTER TABLE [dbo].[case_closure_recommendation]  WITH CHECK ADD  CONSTRAINT [FK_CaseClosure_LegalCase] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[case_closure_recommendation] CHECK CONSTRAINT [FK_CaseClosure_LegalCase]
GO
ALTER TABLE [dbo].[case_comments]  WITH CHECK ADD  CONSTRAINT [fk_case_comments_legal_cases1] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[case_comments] CHECK CONSTRAINT [fk_case_comments_legal_cases1]
GO
ALTER TABLE [dbo].[case_comments_emails]  WITH CHECK ADD  CONSTRAINT [fk_case_comments_emails_1] FOREIGN KEY([case_comment])
REFERENCES [dbo].[case_comments] ([id])
GO
ALTER TABLE [dbo].[case_comments_emails] CHECK CONSTRAINT [fk_case_comments_emails_1]
GO
ALTER TABLE [dbo].[case_investigation_log]  WITH CHECK ADD  CONSTRAINT [FK_case_investigation_log_createdBy_users] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[case_investigation_log] CHECK CONSTRAINT [FK_case_investigation_log_createdBy_users]
GO
ALTER TABLE [dbo].[case_investigation_log]  WITH CHECK ADD  CONSTRAINT [FK_case_investigation_log_legal_cases] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[case_investigation_log] CHECK CONSTRAINT [FK_case_investigation_log_legal_cases]
GO
ALTER TABLE [dbo].[case_investigation_log]  WITH CHECK ADD  CONSTRAINT [FK_case_investigation_log_modifiedBy_users] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[case_investigation_log] CHECK CONSTRAINT [FK_case_investigation_log_modifiedBy_users]
GO
ALTER TABLE [dbo].[case_investigation_log_document]  WITH CHECK ADD  CONSTRAINT [fk_case_investigation_log_doc_1] FOREIGN KEY([investigation_id])
REFERENCES [dbo].[case_investigation_log] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[case_investigation_log_document] CHECK CONSTRAINT [fk_case_investigation_log_doc_1]
GO
ALTER TABLE [dbo].[case_investigation_log_document]  WITH CHECK ADD  CONSTRAINT [fk_case_investigation_log_doc_2] FOREIGN KEY([document])
REFERENCES [dbo].[documents_management_system] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[case_investigation_log_document] CHECK CONSTRAINT [fk_case_investigation_log_doc_2]
GO
ALTER TABLE [dbo].[case_offense_subcategory]  WITH CHECK ADD  CONSTRAINT [FK_case_offense_subcategory_case_types] FOREIGN KEY([offense_type_id])
REFERENCES [dbo].[case_types] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[case_offense_subcategory] CHECK CONSTRAINT [FK_case_offense_subcategory_case_types]
GO
ALTER TABLE [dbo].[case_rate]  WITH CHECK ADD  CONSTRAINT [fk_case_rate_1] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[case_rate] CHECK CONSTRAINT [fk_case_rate_1]
GO
ALTER TABLE [dbo].[case_rate]  WITH CHECK ADD  CONSTRAINT [fk_case_rate_2] FOREIGN KEY([organization_id])
REFERENCES [dbo].[organizations] ([id])
GO
ALTER TABLE [dbo].[case_rate] CHECK CONSTRAINT [fk_case_rate_2]
GO
ALTER TABLE [dbo].[case_related_contracts]  WITH CHECK ADD  CONSTRAINT [fk_case_related_contracts_1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[case_related_contracts] CHECK CONSTRAINT [fk_case_related_contracts_1]
GO
ALTER TABLE [dbo].[case_related_contracts]  WITH CHECK ADD  CONSTRAINT [fk_case_related_contracts_2] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[case_related_contracts] CHECK CONSTRAINT [fk_case_related_contracts_2]
GO
ALTER TABLE [dbo].[case_types_due_conditions]  WITH CHECK ADD  CONSTRAINT [FK_case_types_due_conditions_case_types] FOREIGN KEY([case_type_id])
REFERENCES [dbo].[case_types] ([id])
GO
ALTER TABLE [dbo].[case_types_due_conditions] CHECK CONSTRAINT [FK_case_types_due_conditions_case_types]
GO
ALTER TABLE [dbo].[case_types_due_conditions]  WITH CHECK ADD  CONSTRAINT [FK_case_types_due_conditions_clients] FOREIGN KEY([client_id])
REFERENCES [dbo].[clients] ([id])
GO
ALTER TABLE [dbo].[case_types_due_conditions] CHECK CONSTRAINT [FK_case_types_due_conditions_clients]
GO
ALTER TABLE [dbo].[client_partner_shares]  WITH CHECK ADD  CONSTRAINT [fk_client_partner_shares_1] FOREIGN KEY([client_id])
REFERENCES [dbo].[clients] ([id])
GO
ALTER TABLE [dbo].[client_partner_shares] CHECK CONSTRAINT [fk_client_partner_shares_1]
GO
ALTER TABLE [dbo].[client_partner_shares]  WITH CHECK ADD  CONSTRAINT [fk_client_partner_shares_2] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[client_partner_shares] CHECK CONSTRAINT [fk_client_partner_shares_2]
GO
ALTER TABLE [dbo].[client_trust_accounts_relation]  WITH CHECK ADD  CONSTRAINT [fk_client_trust_accounts_relation_1] FOREIGN KEY([client])
REFERENCES [dbo].[clients] ([id])
GO
ALTER TABLE [dbo].[client_trust_accounts_relation] CHECK CONSTRAINT [fk_client_trust_accounts_relation_1]
GO
ALTER TABLE [dbo].[client_trust_accounts_relation]  WITH CHECK ADD  CONSTRAINT [fk_client_trust_accounts_relation_2] FOREIGN KEY([trust_liability_account])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[client_trust_accounts_relation] CHECK CONSTRAINT [fk_client_trust_accounts_relation_2]
GO
ALTER TABLE [dbo].[client_trust_accounts_relation]  WITH CHECK ADD  CONSTRAINT [fk_client_trust_accounts_relation_3] FOREIGN KEY([trust_asset_account])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[client_trust_accounts_relation] CHECK CONSTRAINT [fk_client_trust_accounts_relation_3]
GO
ALTER TABLE [dbo].[client_trust_accounts_relation]  WITH CHECK ADD  CONSTRAINT [fk_client_trust_accounts_relation_4] FOREIGN KEY([organization_id])
REFERENCES [dbo].[organizations] ([id])
GO
ALTER TABLE [dbo].[client_trust_accounts_relation] CHECK CONSTRAINT [fk_client_trust_accounts_relation_4]
GO
ALTER TABLE [dbo].[clients]  WITH CHECK ADD  CONSTRAINT [clients_users_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[clients] CHECK CONSTRAINT [clients_users_createdBy]
GO
ALTER TABLE [dbo].[clients]  WITH CHECK ADD  CONSTRAINT [clients_users_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[clients] CHECK CONSTRAINT [clients_users_modifiedBy]
GO
ALTER TABLE [dbo].[clients]  WITH CHECK ADD  CONSTRAINT [fk_clients_companies1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[clients] CHECK CONSTRAINT [fk_clients_companies1]
GO
ALTER TABLE [dbo].[clients]  WITH CHECK ADD  CONSTRAINT [fk_clients_contacts1] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[clients] CHECK CONSTRAINT [fk_clients_contacts1]
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD  CONSTRAINT [fk_companies_companies1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[companies] CHECK CONSTRAINT [fk_companies_companies1]
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD  CONSTRAINT [fk_companies_companies2] FOREIGN KEY([registrationAuthority])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[companies] CHECK CONSTRAINT [fk_companies_companies2]
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD  CONSTRAINT [fk_companies_company_legal_types1] FOREIGN KEY([company_legal_type_id])
REFERENCES [dbo].[company_legal_types] ([id])
GO
ALTER TABLE [dbo].[companies] CHECK CONSTRAINT [fk_companies_company_legal_types1]
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD  CONSTRAINT [fk_companies_contact_company_categories] FOREIGN KEY([company_category_id])
REFERENCES [dbo].[contact_company_categories] ([id])
GO
ALTER TABLE [dbo].[companies] CHECK CONSTRAINT [fk_companies_contact_company_categories]
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD  CONSTRAINT [fk_companies_contact_company_sub_categories] FOREIGN KEY([company_sub_category_id])
REFERENCES [dbo].[contact_company_sub_categories] ([id])
GO
ALTER TABLE [dbo].[companies] CHECK CONSTRAINT [fk_companies_contact_company_sub_categories]
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD  CONSTRAINT [fk_companies_contacts2] FOREIGN KEY([registrationByLawNotaryPublic])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[companies] CHECK CONSTRAINT [fk_companies_contacts2]
GO
ALTER TABLE [dbo].[companies_contacts]  WITH CHECK ADD  CONSTRAINT [fk_companies_contacts_companies1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[companies_contacts] CHECK CONSTRAINT [fk_companies_contacts_companies1]
GO
ALTER TABLE [dbo].[companies_contacts]  WITH CHECK ADD  CONSTRAINT [fk_companies_contacts_contacts1] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[companies_contacts] CHECK CONSTRAINT [fk_companies_contacts_contacts1]
GO
ALTER TABLE [dbo].[companies_customer_portal_users]  WITH CHECK ADD  CONSTRAINT [fk_companies_customer_portal_users_1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[companies_customer_portal_users] CHECK CONSTRAINT [fk_companies_customer_portal_users_1]
GO
ALTER TABLE [dbo].[companies_customer_portal_users]  WITH CHECK ADD  CONSTRAINT [fk_companies_customer_portal_users_2] FOREIGN KEY([customer_portal_user_id])
REFERENCES [dbo].[customer_portal_users] ([id])
GO
ALTER TABLE [dbo].[companies_customer_portal_users] CHECK CONSTRAINT [fk_companies_customer_portal_users_2]
GO
ALTER TABLE [dbo].[companies_related_contracts]  WITH CHECK ADD  CONSTRAINT [fk_companies_related_contracts_1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[companies_related_contracts] CHECK CONSTRAINT [fk_companies_related_contracts_1]
GO
ALTER TABLE [dbo].[companies_related_contracts]  WITH CHECK ADD  CONSTRAINT [fk_companies_related_contracts_2] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[companies_related_contracts] CHECK CONSTRAINT [fk_companies_related_contracts_2]
GO
ALTER TABLE [dbo].[company_addresses]  WITH CHECK ADD  CONSTRAINT [fk_company_addresses_1] FOREIGN KEY([company])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[company_addresses] CHECK CONSTRAINT [fk_company_addresses_1]
GO
ALTER TABLE [dbo].[company_assets]  WITH CHECK ADD  CONSTRAINT [company_assets_companies_ibfk_1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[company_assets] CHECK CONSTRAINT [company_assets_companies_ibfk_1]
GO
ALTER TABLE [dbo].[company_assets]  WITH CHECK ADD  CONSTRAINT [company_assets_company_asset_types_ibfk_1] FOREIGN KEY([company_asset_type_id])
REFERENCES [dbo].[company_asset_types] ([id])
GO
ALTER TABLE [dbo].[company_assets] CHECK CONSTRAINT [company_assets_company_asset_types_ibfk_1]
GO
ALTER TABLE [dbo].[company_auditors]  WITH CHECK ADD  CONSTRAINT [fk_company_auditors_companies1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[company_auditors] CHECK CONSTRAINT [fk_company_auditors_companies1]
GO
ALTER TABLE [dbo].[company_bank_accounts]  WITH CHECK ADD  CONSTRAINT [company_bank_accounts_ibfk_1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[company_bank_accounts] CHECK CONSTRAINT [company_bank_accounts_ibfk_1]
GO
ALTER TABLE [dbo].[company_changes]  WITH CHECK ADD  CONSTRAINT [fk_company_changes_companies1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[company_changes] CHECK CONSTRAINT [fk_company_changes_companies1]
GO
ALTER TABLE [dbo].[company_discharge_social_securities]  WITH CHECK ADD  CONSTRAINT [fk_company_discharge_social_securities_companies1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[company_discharge_social_securities] CHECK CONSTRAINT [fk_company_discharge_social_securities_companies1]
GO
ALTER TABLE [dbo].[company_discharge_social_securities]  WITH CHECK ADD  CONSTRAINT [fk_company_discharge_social_securities_company_type_of_discharges1] FOREIGN KEY([type_id])
REFERENCES [dbo].[company_type_of_discharges] ([id])
GO
ALTER TABLE [dbo].[company_discharge_social_securities] CHECK CONSTRAINT [fk_company_discharge_social_securities_company_type_of_discharges1]
GO
ALTER TABLE [dbo].[company_discharge_social_securities]  WITH CHECK ADD  CONSTRAINT [fk_company_discharge_social_securities_reminders1] FOREIGN KEY([reminder_id])
REFERENCES [dbo].[reminders] ([id])
GO
ALTER TABLE [dbo].[company_discharge_social_securities] CHECK CONSTRAINT [fk_company_discharge_social_securities_reminders1]
GO
ALTER TABLE [dbo].[company_discharge_social_securities]  WITH CHECK ADD  CONSTRAINT [fk_company_discharge_social_securities_users1] FOREIGN KEY([remind_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[company_discharge_social_securities] CHECK CONSTRAINT [fk_company_discharge_social_securities_users1]
GO
ALTER TABLE [dbo].[company_documents]  WITH CHECK ADD  CONSTRAINT [company_documents_ibfk_1] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[company_documents] CHECK CONSTRAINT [company_documents_ibfk_1]
GO
ALTER TABLE [dbo].[company_documents]  WITH CHECK ADD  CONSTRAINT [company_documents_ibfk_2] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[company_documents] CHECK CONSTRAINT [company_documents_ibfk_2]
GO
ALTER TABLE [dbo].[company_documents]  WITH CHECK ADD  CONSTRAINT [company_documents_ibfk_3] FOREIGN KEY([company_document_status_id])
REFERENCES [dbo].[company_document_statuses] ([id])
GO
ALTER TABLE [dbo].[company_documents] CHECK CONSTRAINT [company_documents_ibfk_3]
GO
ALTER TABLE [dbo].[company_documents]  WITH CHECK ADD  CONSTRAINT [company_documents_ibfk_4] FOREIGN KEY([company_document_type_id])
REFERENCES [dbo].[company_document_types] ([id])
GO
ALTER TABLE [dbo].[company_documents] CHECK CONSTRAINT [company_documents_ibfk_4]
GO
ALTER TABLE [dbo].[company_documents]  WITH CHECK ADD  CONSTRAINT [fk_company_documents_companies1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[company_documents] CHECK CONSTRAINT [fk_company_documents_companies1]
GO
ALTER TABLE [dbo].[company_lawyers]  WITH CHECK ADD  CONSTRAINT [fk_company_lawyers_companies1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[company_lawyers] CHECK CONSTRAINT [fk_company_lawyers_companies1]
GO
ALTER TABLE [dbo].[company_lawyers]  WITH CHECK ADD  CONSTRAINT [fk_company_lawyers_companies2] FOREIGN KEY([lawyer_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[company_lawyers] CHECK CONSTRAINT [fk_company_lawyers_companies2]
GO
ALTER TABLE [dbo].[company_note_details]  WITH CHECK ADD  CONSTRAINT [fk_company_note_details_1] FOREIGN KEY([company_note_id])
REFERENCES [dbo].[company_notes] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[company_note_details] CHECK CONSTRAINT [fk_company_note_details_1]
GO
ALTER TABLE [dbo].[company_notes]  WITH CHECK ADD  CONSTRAINT [fk_company_notes_1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[company_notes] CHECK CONSTRAINT [fk_company_notes_1]
GO
ALTER TABLE [dbo].[company_notes]  WITH CHECK ADD  CONSTRAINT [fk_company_notes_2] FOREIGN KEY([created_by])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[company_notes] CHECK CONSTRAINT [fk_company_notes_2]
GO
ALTER TABLE [dbo].[company_notes]  WITH CHECK ADD  CONSTRAINT [fk_company_notes_3] FOREIGN KEY([modified_by])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[company_notes] CHECK CONSTRAINT [fk_company_notes_3]
GO
ALTER TABLE [dbo].[company_signature_authorities]  WITH CHECK ADD  CONSTRAINT [company_signature_authorities_ibfk_1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[company_signature_authorities] CHECK CONSTRAINT [company_signature_authorities_ibfk_1]
GO
ALTER TABLE [dbo].[company_users]  WITH CHECK ADD  CONSTRAINT [company_users_ibfk_1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[company_users] CHECK CONSTRAINT [company_users_ibfk_1]
GO
ALTER TABLE [dbo].[company_users]  WITH CHECK ADD  CONSTRAINT [company_users_ibfk_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[company_users] CHECK CONSTRAINT [company_users_ibfk_2]
GO
ALTER TABLE [dbo].[contact_documents]  WITH CHECK ADD  CONSTRAINT [contact_documents_ibfk_1] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[contact_documents] CHECK CONSTRAINT [contact_documents_ibfk_1]
GO
ALTER TABLE [dbo].[contact_documents]  WITH CHECK ADD  CONSTRAINT [contact_documents_ibfk_2] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[contact_documents] CHECK CONSTRAINT [contact_documents_ibfk_2]
GO
ALTER TABLE [dbo].[contact_documents]  WITH CHECK ADD  CONSTRAINT [contact_documents_ibfk_3] FOREIGN KEY([contact_document_status_id])
REFERENCES [dbo].[contact_document_statuses] ([id])
GO
ALTER TABLE [dbo].[contact_documents] CHECK CONSTRAINT [contact_documents_ibfk_3]
GO
ALTER TABLE [dbo].[contact_documents]  WITH CHECK ADD  CONSTRAINT [contact_documents_ibfk_4] FOREIGN KEY([contact_document_type_id])
REFERENCES [dbo].[contact_document_types] ([id])
GO
ALTER TABLE [dbo].[contact_documents] CHECK CONSTRAINT [contact_documents_ibfk_4]
GO
ALTER TABLE [dbo].[contact_documents]  WITH CHECK ADD  CONSTRAINT [fk_contact_documents_contacts1] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[contact_documents] CHECK CONSTRAINT [fk_contact_documents_contacts1]
GO
ALTER TABLE [dbo].[contact_emails]  WITH CHECK ADD  CONSTRAINT [fk_contact_emails_1] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[contact_emails] CHECK CONSTRAINT [fk_contact_emails_1]
GO
ALTER TABLE [dbo].[contact_nationalities]  WITH CHECK ADD  CONSTRAINT [fk_contact_nationalities1] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[contact_nationalities] CHECK CONSTRAINT [fk_contact_nationalities1]
GO
ALTER TABLE [dbo].[contact_nationalities]  WITH CHECK ADD  CONSTRAINT [fk_contact_nationalities2] FOREIGN KEY([nationality_id])
REFERENCES [dbo].[countries] ([id])
GO
ALTER TABLE [dbo].[contact_nationalities] CHECK CONSTRAINT [fk_contact_nationalities2]
GO
ALTER TABLE [dbo].[contact_users]  WITH CHECK ADD  CONSTRAINT [contact_users_ibfk_1] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contact_users] CHECK CONSTRAINT [contact_users_ibfk_1]
GO
ALTER TABLE [dbo].[contact_users]  WITH CHECK ADD  CONSTRAINT [contact_users_ibfk_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contact_users] CHECK CONSTRAINT [contact_users_ibfk_2]
GO
ALTER TABLE [dbo].[contacts]  WITH CHECK ADD  CONSTRAINT [fk_contacts_contact_company_categories] FOREIGN KEY([contact_category_id])
REFERENCES [dbo].[contact_company_categories] ([id])
GO
ALTER TABLE [dbo].[contacts] CHECK CONSTRAINT [fk_contacts_contact_company_categories]
GO
ALTER TABLE [dbo].[contacts]  WITH CHECK ADD  CONSTRAINT [fk_contacts_contact_company_sub_categories] FOREIGN KEY([contact_sub_category_id])
REFERENCES [dbo].[contact_company_sub_categories] ([id])
GO
ALTER TABLE [dbo].[contacts] CHECK CONSTRAINT [fk_contacts_contact_company_sub_categories]
GO
ALTER TABLE [dbo].[contacts]  WITH CHECK ADD  CONSTRAINT [fk_contacts_manager] FOREIGN KEY([manager_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[contacts] CHECK CONSTRAINT [fk_contacts_manager]
GO
ALTER TABLE [dbo].[contacts_related_contracts]  WITH CHECK ADD  CONSTRAINT [fk_contacts_related_contracts_1] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contacts_related_contracts] CHECK CONSTRAINT [fk_contacts_related_contracts_1]
GO
ALTER TABLE [dbo].[contacts_related_contracts]  WITH CHECK ADD  CONSTRAINT [fk_contacts_related_contracts_2] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contacts_related_contracts] CHECK CONSTRAINT [fk_contacts_related_contracts_2]
GO
ALTER TABLE [dbo].[contract]  WITH CHECK ADD  CONSTRAINT [fk_contract_1] FOREIGN KEY([type_id])
REFERENCES [dbo].[contract_type] ([id])
GO
ALTER TABLE [dbo].[contract] CHECK CONSTRAINT [fk_contract_1]
GO
ALTER TABLE [dbo].[contract]  WITH CHECK ADD  CONSTRAINT [fk_contract_10] FOREIGN KEY([country_id])
REFERENCES [dbo].[countries] ([id])
GO
ALTER TABLE [dbo].[contract] CHECK CONSTRAINT [fk_contract_10]
GO
ALTER TABLE [dbo].[contract]  WITH CHECK ADD  CONSTRAINT [fk_contract_11] FOREIGN KEY([sub_type_id])
REFERENCES [dbo].[sub_contract_type] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract] CHECK CONSTRAINT [fk_contract_11]
GO
ALTER TABLE [dbo].[contract]  WITH CHECK ADD  CONSTRAINT [fk_contract_2] FOREIGN KEY([assigned_team_id])
REFERENCES [dbo].[provider_groups] ([id])
GO
ALTER TABLE [dbo].[contract] CHECK CONSTRAINT [fk_contract_2]
GO
ALTER TABLE [dbo].[contract]  WITH CHECK ADD  CONSTRAINT [fk_contract_3] FOREIGN KEY([assignee_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[contract] CHECK CONSTRAINT [fk_contract_3]
GO
ALTER TABLE [dbo].[contract]  WITH CHECK ADD  CONSTRAINT [fk_contract_4] FOREIGN KEY([requester_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[contract] CHECK CONSTRAINT [fk_contract_4]
GO
ALTER TABLE [dbo].[contract]  WITH CHECK ADD  CONSTRAINT [fk_contract_5] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[contract_workflow] ([id])
GO
ALTER TABLE [dbo].[contract] CHECK CONSTRAINT [fk_contract_5]
GO
ALTER TABLE [dbo].[contract]  WITH CHECK ADD  CONSTRAINT [fk_contract_6] FOREIGN KEY([status_id])
REFERENCES [dbo].[contract_status] ([id])
GO
ALTER TABLE [dbo].[contract] CHECK CONSTRAINT [fk_contract_6]
GO
ALTER TABLE [dbo].[contract]  WITH CHECK ADD  CONSTRAINT [fk_contract_7] FOREIGN KEY([currency_id])
REFERENCES [dbo].[iso_currencies] ([id])
GO
ALTER TABLE [dbo].[contract] CHECK CONSTRAINT [fk_contract_7]
GO
ALTER TABLE [dbo].[contract]  WITH CHECK ADD  CONSTRAINT [fk_contract_8] FOREIGN KEY([amendment_of])
REFERENCES [dbo].[contract] ([id])
GO
ALTER TABLE [dbo].[contract] CHECK CONSTRAINT [fk_contract_8]
GO
ALTER TABLE [dbo].[contract]  WITH CHECK ADD  CONSTRAINT [fk_contract_9] FOREIGN KEY([app_law_id])
REFERENCES [dbo].[applicable_law] ([id])
GO
ALTER TABLE [dbo].[contract] CHECK CONSTRAINT [fk_contract_9]
GO
ALTER TABLE [dbo].[contract]  WITH CHECK ADD  CONSTRAINT [FK_contract_department] FOREIGN KEY([department_id])
REFERENCES [dbo].[departments] ([id])
GO
ALTER TABLE [dbo].[contract] CHECK CONSTRAINT [FK_contract_department]
GO
ALTER TABLE [dbo].[contract_amendment_history]  WITH CHECK ADD  CONSTRAINT [fk_contract_amendment_history_1] FOREIGN KEY([amended_by])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_amendment_history] CHECK CONSTRAINT [fk_contract_amendment_history_1]
GO
ALTER TABLE [dbo].[contract_amendment_history]  WITH CHECK ADD  CONSTRAINT [fk_contract_amendment_history_2] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_amendment_history] CHECK CONSTRAINT [fk_contract_amendment_history_2]
GO
ALTER TABLE [dbo].[contract_amendment_history]  WITH CHECK ADD  CONSTRAINT [fk_contract_amendment_history_3] FOREIGN KEY([amended_id])
REFERENCES [dbo].[contract] ([id])
GO
ALTER TABLE [dbo].[contract_amendment_history] CHECK CONSTRAINT [fk_contract_amendment_history_3]
GO
ALTER TABLE [dbo].[contract_amendment_history_details]  WITH CHECK ADD  CONSTRAINT [FK_contract_amendment_history_details_amendment] FOREIGN KEY([amendment_history_id])
REFERENCES [dbo].[contract_amendment_history] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_amendment_history_details] CHECK CONSTRAINT [FK_contract_amendment_history_details_amendment]
GO
ALTER TABLE [dbo].[contract_amendment_history_details]  WITH CHECK ADD  CONSTRAINT [FK_contract_amendment_history_details_contract] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
GO
ALTER TABLE [dbo].[contract_amendment_history_details] CHECK CONSTRAINT [FK_contract_amendment_history_details_contract]
GO
ALTER TABLE [dbo].[contract_approval_bm_role]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_bm_role_1] FOREIGN KEY([contract_approval_status_id])
REFERENCES [dbo].[contract_approval_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_bm_role] CHECK CONSTRAINT [fk_contract_approval_bm_role_1]
GO
ALTER TABLE [dbo].[contract_approval_bm_role]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_bm_role_2] FOREIGN KEY([role_id])
REFERENCES [dbo].[board_member_roles] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_bm_role] CHECK CONSTRAINT [fk_contract_approval_bm_role_2]
GO
ALTER TABLE [dbo].[contract_approval_collaborators]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_collaborators_1] FOREIGN KEY([contract_approval_status_id])
REFERENCES [dbo].[contract_approval_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_collaborators] CHECK CONSTRAINT [fk_contract_approval_collaborators_1]
GO
ALTER TABLE [dbo].[contract_approval_collaborators]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_collaborators_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[customer_portal_users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_collaborators] CHECK CONSTRAINT [fk_contract_approval_collaborators_2]
GO
ALTER TABLE [dbo].[contract_approval_contacts]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_contacts_1] FOREIGN KEY([contract_approval_status_id])
REFERENCES [dbo].[contract_approval_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_contacts] CHECK CONSTRAINT [fk_contract_approval_contacts_1]
GO
ALTER TABLE [dbo].[contract_approval_contacts]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_contacts_2] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_contacts] CHECK CONSTRAINT [fk_contract_approval_contacts_2]
GO
ALTER TABLE [dbo].[contract_approval_documents]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_documents_1] FOREIGN KEY([contract_approval_status_id])
REFERENCES [dbo].[contract_approval_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_documents] CHECK CONSTRAINT [fk_contract_approval_documents_1]
GO
ALTER TABLE [dbo].[contract_approval_documents]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_documents_2] FOREIGN KEY([document_id])
REFERENCES [dbo].[documents_management_system] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_documents] CHECK CONSTRAINT [fk_contract_approval_documents_2]
GO
ALTER TABLE [dbo].[contract_approval_history]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_history_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_history] CHECK CONSTRAINT [fk_contract_approval_history_1]
GO
ALTER TABLE [dbo].[contract_approval_negotiation]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_negotiation_1] FOREIGN KEY([contract_approval_status_id])
REFERENCES [dbo].[contract_approval_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_negotiation] CHECK CONSTRAINT [fk_contract_approval_negotiation_1]
GO
ALTER TABLE [dbo].[contract_approval_negotiation_comments]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_negotiation_comments_1] FOREIGN KEY([negotiation_id])
REFERENCES [dbo].[contract_approval_negotiation] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_negotiation_comments] CHECK CONSTRAINT [fk_contract_approval_negotiation_comments_1]
GO
ALTER TABLE [dbo].[contract_approval_signature_status]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_signature_status_1] FOREIGN KEY([configuration_id])
REFERENCES [dbo].[contract_approval_signature_configuration] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_signature_status] CHECK CONSTRAINT [fk_contract_approval_signature_status_1]
GO
ALTER TABLE [dbo].[contract_approval_signature_status]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_signature_status_2] FOREIGN KEY([status_id])
REFERENCES [dbo].[contract_document_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_signature_status] CHECK CONSTRAINT [fk_contract_approval_signature_status_2]
GO
ALTER TABLE [dbo].[contract_approval_status]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_status_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_status] CHECK CONSTRAINT [fk_contract_approval_status_1]
GO
ALTER TABLE [dbo].[contract_approval_status]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_status_2] FOREIGN KEY([party_id])
REFERENCES [dbo].[party] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_status] CHECK CONSTRAINT [fk_contract_approval_status_2]
GO
ALTER TABLE [dbo].[contract_approval_submission]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_submission_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_submission] CHECK CONSTRAINT [fk_contract_approval_submission_1]
GO
ALTER TABLE [dbo].[contract_approval_user_groups]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_user_groups_1] FOREIGN KEY([contract_approval_status_id])
REFERENCES [dbo].[contract_approval_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_user_groups] CHECK CONSTRAINT [fk_contract_approval_user_groups_1]
GO
ALTER TABLE [dbo].[contract_approval_user_groups]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_user_groups_2] FOREIGN KEY([user_group_id])
REFERENCES [dbo].[user_groups] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_user_groups] CHECK CONSTRAINT [fk_contract_approval_user_groups_2]
GO
ALTER TABLE [dbo].[contract_approval_users]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_users_1] FOREIGN KEY([contract_approval_status_id])
REFERENCES [dbo].[contract_approval_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_users] CHECK CONSTRAINT [fk_contract_approval_users_1]
GO
ALTER TABLE [dbo].[contract_approval_users]  WITH CHECK ADD  CONSTRAINT [fk_contract_approval_users_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_approval_users] CHECK CONSTRAINT [fk_contract_approval_users_2]
GO
ALTER TABLE [dbo].[contract_board_column_options]  WITH CHECK ADD  CONSTRAINT [fk_contract_board_column_opts_1] FOREIGN KEY([status_id])
REFERENCES [dbo].[contract_status] ([id])
GO
ALTER TABLE [dbo].[contract_board_column_options] CHECK CONSTRAINT [fk_contract_board_column_opts_1]
GO
ALTER TABLE [dbo].[contract_board_column_options]  WITH CHECK ADD  CONSTRAINT [fk_contract_board_column_opts_3] FOREIGN KEY([board_column_id])
REFERENCES [dbo].[contract_board_columns] ([id])
GO
ALTER TABLE [dbo].[contract_board_column_options] CHECK CONSTRAINT [fk_contract_board_column_opts_3]
GO
ALTER TABLE [dbo].[contract_board_columns]  WITH CHECK ADD  CONSTRAINT [fk_contract_board_columns_1] FOREIGN KEY([board_id])
REFERENCES [dbo].[contract_boards] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_board_columns] CHECK CONSTRAINT [fk_contract_board_columns_1]
GO
ALTER TABLE [dbo].[contract_board_grid_saved_filters_users]  WITH CHECK ADD  CONSTRAINT [contract_board_grid_saved_filters_users_ibfk_1] FOREIGN KEY([board_id])
REFERENCES [dbo].[contract_boards] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_board_grid_saved_filters_users] CHECK CONSTRAINT [contract_board_grid_saved_filters_users_ibfk_1]
GO
ALTER TABLE [dbo].[contract_board_grid_saved_filters_users]  WITH CHECK ADD  CONSTRAINT [contract_board_grid_saved_filters_users_ibfk_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_board_grid_saved_filters_users] CHECK CONSTRAINT [contract_board_grid_saved_filters_users_ibfk_2]
GO
ALTER TABLE [dbo].[contract_board_post_filters]  WITH CHECK ADD  CONSTRAINT [fk_contract_board_post_filters_1] FOREIGN KEY([board_id])
REFERENCES [dbo].[contract_boards] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_board_post_filters] CHECK CONSTRAINT [fk_contract_board_post_filters_1]
GO
ALTER TABLE [dbo].[contract_board_post_filters_user]  WITH CHECK ADD  CONSTRAINT [fk_contract_board_post_filters_user_1] FOREIGN KEY([board_post_filters_id])
REFERENCES [dbo].[contract_board_post_filters] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_board_post_filters_user] CHECK CONSTRAINT [fk_contract_board_post_filters_user_1]
GO
ALTER TABLE [dbo].[contract_board_post_filters_user]  WITH CHECK ADD  CONSTRAINT [fk_contract_board_post_filters_user_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_board_post_filters_user] CHECK CONSTRAINT [fk_contract_board_post_filters_user_2]
GO
ALTER TABLE [dbo].[contract_boards]  WITH CHECK ADD  CONSTRAINT [fk_contract_boards_1] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[contract_boards] CHECK CONSTRAINT [fk_contract_boards_1]
GO
ALTER TABLE [dbo].[contract_boards]  WITH CHECK ADD  CONSTRAINT [fk_contract_boards_2] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[contract_boards] CHECK CONSTRAINT [fk_contract_boards_2]
GO
ALTER TABLE [dbo].[contract_category_language]  WITH CHECK ADD  CONSTRAINT [fk_contract_category_language_1] FOREIGN KEY([category_id])
REFERENCES [dbo].[contract_category] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_category_language] CHECK CONSTRAINT [fk_contract_category_language_1]
GO
ALTER TABLE [dbo].[contract_category_language]  WITH CHECK ADD  CONSTRAINT [fk_contract_category_language_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_category_language] CHECK CONSTRAINT [fk_contract_category_language_2]
GO
ALTER TABLE [dbo].[contract_clause]  WITH CHECK ADD  CONSTRAINT [fk_contract_clause_2] FOREIGN KEY([iso_language_id])
REFERENCES [dbo].[iso_languages] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_clause] CHECK CONSTRAINT [fk_contract_clause_2]
GO
ALTER TABLE [dbo].[contract_clause_editor]  WITH CHECK ADD  CONSTRAINT [fk_contract_clause_editor_1] FOREIGN KEY([contract_clause_id])
REFERENCES [dbo].[contract_clause] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_clause_editor] CHECK CONSTRAINT [fk_contract_clause_editor_1]
GO
ALTER TABLE [dbo].[contract_clause_editor]  WITH CHECK ADD  CONSTRAINT [fk_contract_clause_editor_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_clause_editor] CHECK CONSTRAINT [fk_contract_clause_editor_2]
GO
ALTER TABLE [dbo].[contract_clause_type]  WITH CHECK ADD  CONSTRAINT [fk_contract_clause_type_1] FOREIGN KEY([contract_clause_id])
REFERENCES [dbo].[contract_clause] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_clause_type] CHECK CONSTRAINT [fk_contract_clause_type_1]
GO
ALTER TABLE [dbo].[contract_clause_type]  WITH CHECK ADD  CONSTRAINT [fk_contract_clause_type_id] FOREIGN KEY([type_id])
REFERENCES [dbo].[contract_type] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_clause_type] CHECK CONSTRAINT [fk_contract_clause_type_id]
GO
ALTER TABLE [dbo].[contract_clause_user]  WITH CHECK ADD  CONSTRAINT [fk_contract_clause_user_1] FOREIGN KEY([contract_clause_id])
REFERENCES [dbo].[contract_clause] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_clause_user] CHECK CONSTRAINT [fk_contract_clause_user_1]
GO
ALTER TABLE [dbo].[contract_clause_user]  WITH CHECK ADD  CONSTRAINT [fk_contract_clause_user_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_clause_user] CHECK CONSTRAINT [fk_contract_clause_user_2]
GO
ALTER TABLE [dbo].[contract_collaborators]  WITH CHECK ADD  CONSTRAINT [fk_contract_collaborators_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_collaborators] CHECK CONSTRAINT [fk_contract_collaborators_1]
GO
ALTER TABLE [dbo].[contract_collaborators]  WITH CHECK ADD  CONSTRAINT [fk_contract_collaborators_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[customer_portal_users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_collaborators] CHECK CONSTRAINT [fk_contract_collaborators_2]
GO
ALTER TABLE [dbo].[contract_comment]  WITH CHECK ADD  CONSTRAINT [fk_contract_comment_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_comment] CHECK CONSTRAINT [fk_contract_comment_1]
GO
ALTER TABLE [dbo].[contract_comments_emails]  WITH CHECK ADD  CONSTRAINT [fk_contract_comments_emails_1] FOREIGN KEY([contract_comment])
REFERENCES [dbo].[contract_comment] ([id])
GO
ALTER TABLE [dbo].[contract_comments_emails] CHECK CONSTRAINT [fk_contract_comments_emails_1]
GO
ALTER TABLE [dbo].[contract_contributors]  WITH CHECK ADD  CONSTRAINT [fk_contract_contributors_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_contributors] CHECK CONSTRAINT [fk_contract_contributors_1]
GO
ALTER TABLE [dbo].[contract_contributors]  WITH CHECK ADD  CONSTRAINT [fk_contract_contributors_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_contributors] CHECK CONSTRAINT [fk_contract_contributors_2]
GO
ALTER TABLE [dbo].[contract_cp_screen_field_languages]  WITH CHECK ADD  CONSTRAINT [fk_contract_cp_screen_field_languages1] FOREIGN KEY([screen_field_id])
REFERENCES [dbo].[contract_cp_screen_fields] ([id])
GO
ALTER TABLE [dbo].[contract_cp_screen_field_languages] CHECK CONSTRAINT [fk_contract_cp_screen_field_languages1]
GO
ALTER TABLE [dbo].[contract_cp_screen_field_languages]  WITH CHECK ADD  CONSTRAINT [fk_contract_cp_screen_field_languages2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[contract_cp_screen_field_languages] CHECK CONSTRAINT [fk_contract_cp_screen_field_languages2]
GO
ALTER TABLE [dbo].[contract_cp_screen_fields]  WITH CHECK ADD  CONSTRAINT [fk_contract_cp_screen_fields1] FOREIGN KEY([screen_id])
REFERENCES [dbo].[contract_cp_screens] ([id])
GO
ALTER TABLE [dbo].[contract_cp_screen_fields] CHECK CONSTRAINT [fk_contract_cp_screen_fields1]
GO
ALTER TABLE [dbo].[contract_cp_screens]  WITH CHECK ADD  CONSTRAINT [fk_contract_cp_screens_1] FOREIGN KEY([contract_request_type_category_id])
REFERENCES [dbo].[contract_request_type_categories] ([id])
GO
ALTER TABLE [dbo].[contract_cp_screens] CHECK CONSTRAINT [fk_contract_cp_screens_1]
GO
ALTER TABLE [dbo].[contract_document_status_language]  WITH CHECK ADD  CONSTRAINT [fk_contract_document_status_language_1] FOREIGN KEY([status_id])
REFERENCES [dbo].[contract_document_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_document_status_language] CHECK CONSTRAINT [fk_contract_document_status_language_1]
GO
ALTER TABLE [dbo].[contract_document_status_language]  WITH CHECK ADD  CONSTRAINT [fk_contract_document_status_language_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_document_status_language] CHECK CONSTRAINT [fk_contract_document_status_language_2]
GO
ALTER TABLE [dbo].[contract_document_type_language]  WITH CHECK ADD  CONSTRAINT [fk_contract_document_type_language_1] FOREIGN KEY([type_id])
REFERENCES [dbo].[contract_document_type] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_document_type_language] CHECK CONSTRAINT [fk_contract_document_type_language_1]
GO
ALTER TABLE [dbo].[contract_document_type_language]  WITH CHECK ADD  CONSTRAINT [fk_contract_document_type_language_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_document_type_language] CHECK CONSTRAINT [fk_contract_document_type_language_2]
GO
ALTER TABLE [dbo].[contract_milestone]  WITH CHECK ADD  CONSTRAINT [fk_contract_milestone_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
GO
ALTER TABLE [dbo].[contract_milestone] CHECK CONSTRAINT [fk_contract_milestone_1]
GO
ALTER TABLE [dbo].[contract_milestone]  WITH CHECK ADD  CONSTRAINT [fk_contract_milestone_2] FOREIGN KEY([currency_id])
REFERENCES [dbo].[iso_currencies] ([id])
GO
ALTER TABLE [dbo].[contract_milestone] CHECK CONSTRAINT [fk_contract_milestone_2]
GO
ALTER TABLE [dbo].[contract_milestone_documents]  WITH CHECK ADD  CONSTRAINT [fk_contract_milestone_documents_1] FOREIGN KEY([document_id])
REFERENCES [dbo].[documents_management_system] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_milestone_documents] CHECK CONSTRAINT [fk_contract_milestone_documents_1]
GO
ALTER TABLE [dbo].[contract_milestone_documents]  WITH CHECK ADD  CONSTRAINT [fk_contract_milestone_documentse_2] FOREIGN KEY([milestone_id])
REFERENCES [dbo].[contract_milestone] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_milestone_documents] CHECK CONSTRAINT [fk_contract_milestone_documentse_2]
GO
ALTER TABLE [dbo].[contract_parties_sla]  WITH CHECK ADD  CONSTRAINT [fk_contract_parties_sla_1] FOREIGN KEY([sla_management_id])
REFERENCES [dbo].[contract_sla_management] ([id])
GO
ALTER TABLE [dbo].[contract_parties_sla] CHECK CONSTRAINT [fk_contract_parties_sla_1]
GO
ALTER TABLE [dbo].[contract_parties_sla]  WITH CHECK ADD  CONSTRAINT [fk_contract_parties_sla_2] FOREIGN KEY([party_id])
REFERENCES [dbo].[party] ([id])
GO
ALTER TABLE [dbo].[contract_parties_sla] CHECK CONSTRAINT [fk_contract_parties_sla_2]
GO
ALTER TABLE [dbo].[contract_party]  WITH CHECK ADD  CONSTRAINT [fk_contract_party_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_party] CHECK CONSTRAINT [fk_contract_party_1]
GO
ALTER TABLE [dbo].[contract_party]  WITH CHECK ADD  CONSTRAINT [fk_contract_party_2] FOREIGN KEY([party_id])
REFERENCES [dbo].[party] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_party] CHECK CONSTRAINT [fk_contract_party_2]
GO
ALTER TABLE [dbo].[contract_party]  WITH CHECK ADD  CONSTRAINT [fk_contract_party_3] FOREIGN KEY([party_category_id])
REFERENCES [dbo].[party_category] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_party] CHECK CONSTRAINT [fk_contract_party_3]
GO
ALTER TABLE [dbo].[contract_renewal_history]  WITH CHECK ADD  CONSTRAINT [fk_contract_renewal_history_1] FOREIGN KEY([renewed_by])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_renewal_history] CHECK CONSTRAINT [fk_contract_renewal_history_1]
GO
ALTER TABLE [dbo].[contract_renewal_history]  WITH CHECK ADD  CONSTRAINT [fk_contract_renewal_history_2] FOREIGN KEY([renewal_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_renewal_history] CHECK CONSTRAINT [fk_contract_renewal_history_2]
GO
ALTER TABLE [dbo].[contract_renewal_notification_assigned_teams]  WITH CHECK ADD  CONSTRAINT [fk_contract_renewal_notification_assigned_teams_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_renewal_notification_assigned_teams] CHECK CONSTRAINT [fk_contract_renewal_notification_assigned_teams_1]
GO
ALTER TABLE [dbo].[contract_renewal_notification_emails]  WITH CHECK ADD  CONSTRAINT [fk_contract_renewal_notification_emails_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_renewal_notification_emails] CHECK CONSTRAINT [fk_contract_renewal_notification_emails_1]
GO
ALTER TABLE [dbo].[contract_signature_bm_role]  WITH CHECK ADD  CONSTRAINT [fk_contract_signature_bm_role_1] FOREIGN KEY([contract_signature_status_id])
REFERENCES [dbo].[contract_signature_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signature_bm_role] CHECK CONSTRAINT [fk_contract_signature_bm_role_1]
GO
ALTER TABLE [dbo].[contract_signature_bm_role]  WITH CHECK ADD  CONSTRAINT [fk_contract_signature_bm_role_2] FOREIGN KEY([role_id])
REFERENCES [dbo].[board_member_roles] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signature_bm_role] CHECK CONSTRAINT [fk_contract_signature_bm_role_2]
GO
ALTER TABLE [dbo].[contract_signature_collaborators]  WITH CHECK ADD  CONSTRAINT [fk_contract_signature_collaborators_1] FOREIGN KEY([contract_signature_status_id])
REFERENCES [dbo].[contract_signature_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signature_collaborators] CHECK CONSTRAINT [fk_contract_signature_collaborators_1]
GO
ALTER TABLE [dbo].[contract_signature_collaborators]  WITH CHECK ADD  CONSTRAINT [fk_contract_signature_collaborators_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[customer_portal_users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signature_collaborators] CHECK CONSTRAINT [fk_contract_signature_collaborators_2]
GO
ALTER TABLE [dbo].[contract_signature_contacts]  WITH CHECK ADD  CONSTRAINT [fk_contract_signature_contacts_1] FOREIGN KEY([contract_signature_status_id])
REFERENCES [dbo].[contract_signature_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signature_contacts] CHECK CONSTRAINT [fk_contract_signature_contacts_1]
GO
ALTER TABLE [dbo].[contract_signature_contacts]  WITH CHECK ADD  CONSTRAINT [fk_contract_signature_contacts_2] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signature_contacts] CHECK CONSTRAINT [fk_contract_signature_contacts_2]
GO
ALTER TABLE [dbo].[contract_signature_history]  WITH CHECK ADD  CONSTRAINT [fk_contract_signature_history_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signature_history] CHECK CONSTRAINT [fk_contract_signature_history_1]
GO
ALTER TABLE [dbo].[contract_signature_status]  WITH CHECK ADD  CONSTRAINT [fk_contract_signature_status_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signature_status] CHECK CONSTRAINT [fk_contract_signature_status_1]
GO
ALTER TABLE [dbo].[contract_signature_status]  WITH CHECK ADD  CONSTRAINT [fk_contract_signature_status_2] FOREIGN KEY([party_id])
REFERENCES [dbo].[party] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signature_status] CHECK CONSTRAINT [fk_contract_signature_status_2]
GO
ALTER TABLE [dbo].[contract_signature_submission]  WITH CHECK ADD  CONSTRAINT [fk_contract_signature_submission_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signature_submission] CHECK CONSTRAINT [fk_contract_signature_submission_1]
GO
ALTER TABLE [dbo].[contract_signature_user_groups]  WITH CHECK ADD  CONSTRAINT [fk_contract_signature_user_groups_1] FOREIGN KEY([contract_signature_status_id])
REFERENCES [dbo].[contract_signature_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signature_user_groups] CHECK CONSTRAINT [fk_contract_signature_user_groups_1]
GO
ALTER TABLE [dbo].[contract_signature_user_groups]  WITH CHECK ADD  CONSTRAINT [fk_contract_signature_user_groups_2] FOREIGN KEY([user_group_id])
REFERENCES [dbo].[user_groups] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signature_user_groups] CHECK CONSTRAINT [fk_contract_signature_user_groups_2]
GO
ALTER TABLE [dbo].[contract_signature_users]  WITH CHECK ADD  CONSTRAINT [fk_contract_signature_users_1] FOREIGN KEY([contract_signature_status_id])
REFERENCES [dbo].[contract_signature_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signature_users] CHECK CONSTRAINT [fk_contract_signature_users_1]
GO
ALTER TABLE [dbo].[contract_signature_users]  WITH CHECK ADD  CONSTRAINT [fk_contract_signature_users_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signature_users] CHECK CONSTRAINT [fk_contract_signature_users_2]
GO
ALTER TABLE [dbo].[contract_signed_document]  WITH CHECK ADD  CONSTRAINT [fk_contract_signed_document_1] FOREIGN KEY([document_id])
REFERENCES [dbo].[documents_management_system] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signed_document] CHECK CONSTRAINT [fk_contract_signed_document_1]
GO
ALTER TABLE [dbo].[contract_signed_document]  WITH CHECK ADD  CONSTRAINT [fk_contract_signed_document_2] FOREIGN KEY([contract_signature_status_id])
REFERENCES [dbo].[contract_signature_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_signed_document] CHECK CONSTRAINT [fk_contract_signed_document_2]
GO
ALTER TABLE [dbo].[contract_sla_management]  WITH CHECK ADD  CONSTRAINT [fk_contract_sla_management_1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[contract_workflow] ([id])
GO
ALTER TABLE [dbo].[contract_sla_management] CHECK CONSTRAINT [fk_contract_sla_management_1]
GO
ALTER TABLE [dbo].[contract_sla_notification]  WITH CHECK ADD  CONSTRAINT [fk_contract_sla_notification_1] FOREIGN KEY([sla_management_id])
REFERENCES [dbo].[contract_sla_management] ([id])
GO
ALTER TABLE [dbo].[contract_sla_notification] CHECK CONSTRAINT [fk_contract_sla_notification_1]
GO
ALTER TABLE [dbo].[contract_sla_notification]  WITH CHECK ADD  CONSTRAINT [fk_contract_sla_notification_2] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
GO
ALTER TABLE [dbo].[contract_sla_notification] CHECK CONSTRAINT [fk_contract_sla_notification_2]
GO
ALTER TABLE [dbo].[contract_status]  WITH CHECK ADD  CONSTRAINT [fk_contract_status_1] FOREIGN KEY([category_id])
REFERENCES [dbo].[status_category] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_status] CHECK CONSTRAINT [fk_contract_status_1]
GO
ALTER TABLE [dbo].[contract_status_language]  WITH CHECK ADD  CONSTRAINT [fk_contract_status_language_1] FOREIGN KEY([status_id])
REFERENCES [dbo].[contract_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_status_language] CHECK CONSTRAINT [fk_contract_status_language_1]
GO
ALTER TABLE [dbo].[contract_status_language]  WITH CHECK ADD  CONSTRAINT [fk_contract_status_language_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_status_language] CHECK CONSTRAINT [fk_contract_status_language_2]
GO
ALTER TABLE [dbo].[contract_template_groups]  WITH CHECK ADD  CONSTRAINT [fk_contract_template_groups_1] FOREIGN KEY([page_id])
REFERENCES [dbo].[contract_template_pages] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_template_groups] CHECK CONSTRAINT [fk_contract_template_groups_1]
GO
ALTER TABLE [dbo].[contract_template_pages]  WITH CHECK ADD  CONSTRAINT [fk_contract_template_pages_1] FOREIGN KEY([template_id])
REFERENCES [dbo].[contract_templates] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_template_pages] CHECK CONSTRAINT [fk_contract_template_pages_1]
GO
ALTER TABLE [dbo].[contract_template_variables]  WITH CHECK ADD  CONSTRAINT [fk_contract_template_variables_1] FOREIGN KEY([group_id])
REFERENCES [dbo].[contract_template_groups] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_template_variables] CHECK CONSTRAINT [fk_contract_template_variables_1]
GO
ALTER TABLE [dbo].[contract_templates]  WITH CHECK ADD  CONSTRAINT [fk_contract_templates_1] FOREIGN KEY([type_id])
REFERENCES [dbo].[contract_type] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_templates] CHECK CONSTRAINT [fk_contract_templates_1]
GO
ALTER TABLE [dbo].[contract_templates]  WITH CHECK ADD  CONSTRAINT [fk_contract_templates_2] FOREIGN KEY([sub_type_id])
REFERENCES [dbo].[sub_contract_type] ([id])
GO
ALTER TABLE [dbo].[contract_templates] CHECK CONSTRAINT [fk_contract_templates_2]
GO
ALTER TABLE [dbo].[contract_templates]  WITH CHECK ADD  CONSTRAINT [fk_contract_templates_3] FOREIGN KEY([document_id])
REFERENCES [dbo].[documents_management_system] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_templates] CHECK CONSTRAINT [fk_contract_templates_3]
GO
ALTER TABLE [dbo].[contract_type_language]  WITH CHECK ADD  CONSTRAINT [fk_contract_type_language_1] FOREIGN KEY([type_id])
REFERENCES [dbo].[contract_type] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_type_language] CHECK CONSTRAINT [fk_contract_type_language_1]
GO
ALTER TABLE [dbo].[contract_type_language]  WITH CHECK ADD  CONSTRAINT [fk_contract_type_language_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_type_language] CHECK CONSTRAINT [fk_contract_type_language_2]
GO
ALTER TABLE [dbo].[contract_url]  WITH CHECK ADD  CONSTRAINT [fk_contract_url_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_url] CHECK CONSTRAINT [fk_contract_url_1]
GO
ALTER TABLE [dbo].[contract_url]  WITH CHECK ADD  CONSTRAINT [fk_contract_url_2] FOREIGN KEY([document_type_id])
REFERENCES [dbo].[contract_document_type] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_url] CHECK CONSTRAINT [fk_contract_url_2]
GO
ALTER TABLE [dbo].[contract_url]  WITH CHECK ADD  CONSTRAINT [fk_contract_url_3] FOREIGN KEY([document_status_id])
REFERENCES [dbo].[contract_document_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_url] CHECK CONSTRAINT [fk_contract_url_3]
GO
ALTER TABLE [dbo].[contract_users]  WITH CHECK ADD  CONSTRAINT [fk_contract_users_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_users] CHECK CONSTRAINT [fk_contract_users_1]
GO
ALTER TABLE [dbo].[contract_users]  WITH CHECK ADD  CONSTRAINT [fk_contract_users_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_users] CHECK CONSTRAINT [fk_contract_users_2]
GO
ALTER TABLE [dbo].[contract_workflow_per_type]  WITH CHECK ADD  CONSTRAINT [fk_contract_workflow_per_type_1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[contract_workflow] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_workflow_per_type] CHECK CONSTRAINT [fk_contract_workflow_per_type_1]
GO
ALTER TABLE [dbo].[contract_workflow_per_type]  WITH CHECK ADD  CONSTRAINT [fk_contract_workflow_per_type_2] FOREIGN KEY([type_id])
REFERENCES [dbo].[contract_type] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_workflow_per_type] CHECK CONSTRAINT [fk_contract_workflow_per_type_2]
GO
ALTER TABLE [dbo].[contract_workflow_status_relation]  WITH CHECK ADD  CONSTRAINT [fk_contract_workflow_status_relation_1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[contract_workflow] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_workflow_status_relation] CHECK CONSTRAINT [fk_contract_workflow_status_relation_1]
GO
ALTER TABLE [dbo].[contract_workflow_status_relation]  WITH CHECK ADD  CONSTRAINT [fk_contract_workflow_status_relation_2] FOREIGN KEY([status_id])
REFERENCES [dbo].[contract_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_workflow_status_relation] CHECK CONSTRAINT [fk_contract_workflow_status_relation_2]
GO
ALTER TABLE [dbo].[contract_workflow_status_transition]  WITH CHECK ADD  CONSTRAINT [fk_contract_workflow_status_transition_1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[contract_workflow] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_workflow_status_transition] CHECK CONSTRAINT [fk_contract_workflow_status_transition_1]
GO
ALTER TABLE [dbo].[contract_workflow_status_transition]  WITH CHECK ADD  CONSTRAINT [fk_contract_workflow_status_transition_2] FOREIGN KEY([from_step])
REFERENCES [dbo].[contract_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_workflow_status_transition] CHECK CONSTRAINT [fk_contract_workflow_status_transition_2]
GO
ALTER TABLE [dbo].[contract_workflow_status_transition]  WITH CHECK ADD  CONSTRAINT [fk_contract_workflow_status_transition_3] FOREIGN KEY([to_step])
REFERENCES [dbo].[contract_status] ([id])
GO
ALTER TABLE [dbo].[contract_workflow_status_transition] CHECK CONSTRAINT [fk_contract_workflow_status_transition_3]
GO
ALTER TABLE [dbo].[contract_workflow_status_transition_log]  WITH CHECK ADD  CONSTRAINT [fk_cwstlog_contract] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_workflow_status_transition_log] CHECK CONSTRAINT [fk_cwstlog_contract]
GO
ALTER TABLE [dbo].[contract_workflow_status_transition_log]  WITH CHECK ADD  CONSTRAINT [fk_cwstlog_transition] FOREIGN KEY([transition_id])
REFERENCES [dbo].[contract_workflow_status_transition] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_workflow_status_transition_log] CHECK CONSTRAINT [fk_cwstlog_transition]
GO
ALTER TABLE [dbo].[contract_workflow_status_transition_log]  WITH CHECK ADD  CONSTRAINT [fk_cwstlog_user] FOREIGN KEY([doneBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[contract_workflow_status_transition_log] CHECK CONSTRAINT [fk_cwstlog_user]
GO
ALTER TABLE [dbo].[contract_workflow_status_transition_permission]  WITH CHECK ADD  CONSTRAINT [fk_contract_workflow_status_transition_permission_1] FOREIGN KEY([transition_id])
REFERENCES [dbo].[contract_workflow_status_transition] ([id])
GO
ALTER TABLE [dbo].[contract_workflow_status_transition_permission] CHECK CONSTRAINT [fk_contract_workflow_status_transition_permission_1]
GO
ALTER TABLE [dbo].[contract_workflow_status_transition_screen_field]  WITH CHECK ADD  CONSTRAINT [fk_contract_workflow_status_transition_screen_field_1] FOREIGN KEY([transition_id])
REFERENCES [dbo].[contract_workflow_status_transition] ([id])
GO
ALTER TABLE [dbo].[contract_workflow_status_transition_screen_field] CHECK CONSTRAINT [fk_contract_workflow_status_transition_screen_field_1]
GO
ALTER TABLE [dbo].[contract_workflow_step_checklist]  WITH CHECK ADD  CONSTRAINT [FK_Checklist_Step] FOREIGN KEY([step_id])
REFERENCES [dbo].[contract_status_language] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_workflow_step_checklist] CHECK CONSTRAINT [FK_Checklist_Step]
GO
ALTER TABLE [dbo].[contract_workflow_step_functions]  WITH CHECK ADD  CONSTRAINT [FK_Functions_Step] FOREIGN KEY([step_id])
REFERENCES [dbo].[contract_status_language] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_workflow_step_functions] CHECK CONSTRAINT [FK_Functions_Step]
GO
ALTER TABLE [dbo].[contract_workflow_steps_log]  WITH CHECK ADD  CONSTRAINT [fk_log_actor] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_workflow_steps_log] CHECK CONSTRAINT [fk_log_actor]
GO
ALTER TABLE [dbo].[contract_workflow_steps_log]  WITH CHECK ADD  CONSTRAINT [fk_log_step_id] FOREIGN KEY([step_id])
REFERENCES [dbo].[contract_status_language] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_workflow_steps_log] CHECK CONSTRAINT [fk_log_step_id]
GO
ALTER TABLE [dbo].[contract_workflow_steps_log]  WITH CHECK ADD  CONSTRAINT [fk_workflow_contract_id] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[contract_workflow_steps_log] CHECK CONSTRAINT [fk_workflow_contract_id]
GO
ALTER TABLE [dbo].[contracts_sla]  WITH CHECK ADD  CONSTRAINT [fk_contracts_sla_1] FOREIGN KEY([sla_management_id])
REFERENCES [dbo].[contract_sla_management] ([id])
GO
ALTER TABLE [dbo].[contracts_sla] CHECK CONSTRAINT [fk_contracts_sla_1]
GO
ALTER TABLE [dbo].[contracts_sla]  WITH CHECK ADD  CONSTRAINT [fk_contracts_sla_2] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
GO
ALTER TABLE [dbo].[contracts_sla] CHECK CONSTRAINT [fk_contracts_sla_2]
GO
ALTER TABLE [dbo].[contracts_sla_actions]  WITH CHECK ADD  CONSTRAINT [fk_contracts_sla_actions_1] FOREIGN KEY([sla_management_id])
REFERENCES [dbo].[contract_sla_management] ([id])
GO
ALTER TABLE [dbo].[contracts_sla_actions] CHECK CONSTRAINT [fk_contracts_sla_actions_1]
GO
ALTER TABLE [dbo].[contracts_sla_actions]  WITH CHECK ADD  CONSTRAINT [fk_contracts_sla_actions_2] FOREIGN KEY([status_id])
REFERENCES [dbo].[contract_status] ([id])
GO
ALTER TABLE [dbo].[contracts_sla_actions] CHECK CONSTRAINT [fk_contracts_sla_actions_2]
GO
ALTER TABLE [dbo].[contracts_type_sla]  WITH CHECK ADD  CONSTRAINT [fk_contracts_type_sla_1] FOREIGN KEY([sla_management_id])
REFERENCES [dbo].[contract_sla_management] ([id])
GO
ALTER TABLE [dbo].[contracts_type_sla] CHECK CONSTRAINT [fk_contracts_type_sla_1]
GO
ALTER TABLE [dbo].[contracts_type_sla]  WITH CHECK ADD  CONSTRAINT [fk_contracts_type_sla_2] FOREIGN KEY([type_id])
REFERENCES [dbo].[contract_type] ([id])
GO
ALTER TABLE [dbo].[contracts_type_sla] CHECK CONSTRAINT [fk_contracts_type_sla_2]
GO
ALTER TABLE [dbo].[conveyancing_activity]  WITH CHECK ADD  CONSTRAINT [FK_activity_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[conveyancing_activity] CHECK CONSTRAINT [FK_activity_createdBy]
GO
ALTER TABLE [dbo].[conveyancing_activity]  WITH CHECK ADD  CONSTRAINT [FK_activity_instrument] FOREIGN KEY([conveyancing_instrument_id])
REFERENCES [dbo].[conveyancing_instruments] ([id])
GO
ALTER TABLE [dbo].[conveyancing_activity] CHECK CONSTRAINT [FK_activity_instrument]
GO
ALTER TABLE [dbo].[conveyancing_activity]  WITH CHECK ADD  CONSTRAINT [FK_activity_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[conveyancing_activity] CHECK CONSTRAINT [FK_activity_modifiedBy]
GO
ALTER TABLE [dbo].[conveyancing_activity]  WITH CHECK ADD  CONSTRAINT [FK_activity_type] FOREIGN KEY([activity_type_id])
REFERENCES [dbo].[conveyancing_activity_type] ([id])
GO
ALTER TABLE [dbo].[conveyancing_activity] CHECK CONSTRAINT [FK_activity_type]
GO
ALTER TABLE [dbo].[conveyancing_activity_type]  WITH CHECK ADD  CONSTRAINT [FK_activity_type_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[conveyancing_activity_type] CHECK CONSTRAINT [FK_activity_type_createdBy]
GO
ALTER TABLE [dbo].[conveyancing_activity_type]  WITH CHECK ADD  CONSTRAINT [FK_activity_type_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[conveyancing_activity_type] CHECK CONSTRAINT [FK_activity_type_modifiedBy]
GO
ALTER TABLE [dbo].[conveyancing_instrument_types]  WITH CHECK ADD  CONSTRAINT [FK_instrument_types_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[conveyancing_instrument_types] CHECK CONSTRAINT [FK_instrument_types_createdBy]
GO
ALTER TABLE [dbo].[conveyancing_instrument_types]  WITH CHECK ADD  CONSTRAINT [FK_instrument_types_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[conveyancing_instrument_types] CHECK CONSTRAINT [FK_instrument_types_modifiedBy]
GO
ALTER TABLE [dbo].[conveyancing_instruments]  WITH CHECK ADD  CONSTRAINT [FK_instruments_assignee] FOREIGN KEY([assignee_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[conveyancing_instruments] CHECK CONSTRAINT [FK_instruments_assignee]
GO
ALTER TABLE [dbo].[conveyancing_instruments]  WITH CHECK ADD  CONSTRAINT [FK_instruments_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[conveyancing_instruments] CHECK CONSTRAINT [FK_instruments_createdBy]
GO
ALTER TABLE [dbo].[conveyancing_instruments]  WITH CHECK ADD  CONSTRAINT [FK_instruments_current_stage] FOREIGN KEY([current_stage_id])
REFERENCES [dbo].[conveyancing_process_stages] ([id])
GO
ALTER TABLE [dbo].[conveyancing_instruments] CHECK CONSTRAINT [FK_instruments_current_stage]
GO
ALTER TABLE [dbo].[conveyancing_instruments]  WITH CHECK ADD  CONSTRAINT [FK_instruments_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[conveyancing_instruments] CHECK CONSTRAINT [FK_instruments_modifiedBy]
GO
ALTER TABLE [dbo].[conveyancing_instruments]  WITH CHECK ADD  CONSTRAINT [FK_instruments_transaction_type] FOREIGN KEY([transaction_type_id])
REFERENCES [dbo].[conveyancing_transaction_types] ([id])
GO
ALTER TABLE [dbo].[conveyancing_instruments] CHECK CONSTRAINT [FK_instruments_transaction_type]
GO
ALTER TABLE [dbo].[conveyancing_instruments]  WITH CHECK ADD  CONSTRAINT [FK_instruments_type] FOREIGN KEY([instrument_type_id])
REFERENCES [dbo].[conveyancing_instrument_types] ([id])
GO
ALTER TABLE [dbo].[conveyancing_instruments] CHECK CONSTRAINT [FK_instruments_type]
GO
ALTER TABLE [dbo].[conveyancing_stage_progress]  WITH CHECK ADD  CONSTRAINT [FK_progress_instrument_id] FOREIGN KEY([instrument_id])
REFERENCES [dbo].[conveyancing_instruments] ([id])
GO
ALTER TABLE [dbo].[conveyancing_stage_progress] CHECK CONSTRAINT [FK_progress_instrument_id]
GO
ALTER TABLE [dbo].[conveyancing_stage_progress]  WITH CHECK ADD  CONSTRAINT [FK_progress_stage_id] FOREIGN KEY([stage_id])
REFERENCES [dbo].[conveyancing_process_stages] ([id])
GO
ALTER TABLE [dbo].[conveyancing_stage_progress] CHECK CONSTRAINT [FK_progress_stage_id]
GO
ALTER TABLE [dbo].[conveyancing_stage_progress]  WITH CHECK ADD  CONSTRAINT [FK_progress_updated_by] FOREIGN KEY([updated_by])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[conveyancing_stage_progress] CHECK CONSTRAINT [FK_progress_updated_by]
GO
ALTER TABLE [dbo].[conveyancing_transaction_types]  WITH CHECK ADD  CONSTRAINT [FK_transaction_types_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[conveyancing_transaction_types] CHECK CONSTRAINT [FK_transaction_types_createdBy]
GO
ALTER TABLE [dbo].[conveyancing_transaction_types]  WITH CHECK ADD  CONSTRAINT [FK_transaction_types_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[conveyancing_transaction_types] CHECK CONSTRAINT [FK_transaction_types_modifiedBy]
GO
ALTER TABLE [dbo].[correspondence_activity_log]  WITH CHECK ADD  CONSTRAINT [FK_activity_log_correspondence] FOREIGN KEY([correspondence_id])
REFERENCES [dbo].[correspondences] ([id])
GO
ALTER TABLE [dbo].[correspondence_activity_log] CHECK CONSTRAINT [FK_activity_log_correspondence]
GO
ALTER TABLE [dbo].[correspondence_activity_log]  WITH CHECK ADD  CONSTRAINT [FK_activity_log_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[correspondence_activity_log] CHECK CONSTRAINT [FK_activity_log_createdBy]
GO
ALTER TABLE [dbo].[correspondence_activity_log]  WITH CHECK ADD  CONSTRAINT [FK_activity_log_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[correspondence_activity_log] CHECK CONSTRAINT [FK_activity_log_modifiedBy]
GO
ALTER TABLE [dbo].[correspondence_activity_log]  WITH CHECK ADD  CONSTRAINT [FK_activity_log_user] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[correspondence_activity_log] CHECK CONSTRAINT [FK_activity_log_user]
GO
ALTER TABLE [dbo].[correspondence_document]  WITH CHECK ADD  CONSTRAINT [FK_document_correspondence] FOREIGN KEY([correspondence_id])
REFERENCES [dbo].[correspondences] ([id])
GO
ALTER TABLE [dbo].[correspondence_document] CHECK CONSTRAINT [FK_document_correspondence]
GO
ALTER TABLE [dbo].[correspondence_document]  WITH CHECK ADD  CONSTRAINT [FK_document_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[correspondence_document] CHECK CONSTRAINT [FK_document_createdBy]
GO
ALTER TABLE [dbo].[correspondence_document]  WITH CHECK ADD  CONSTRAINT [FK_document_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[correspondence_document] CHECK CONSTRAINT [FK_document_modifiedBy]
GO
ALTER TABLE [dbo].[correspondence_document]  WITH CHECK ADD  CONSTRAINT [FK_document_status] FOREIGN KEY([document_status_id])
REFERENCES [dbo].[correspondence_statuses] ([id])
GO
ALTER TABLE [dbo].[correspondence_document] CHECK CONSTRAINT [FK_document_status]
GO
ALTER TABLE [dbo].[correspondence_document]  WITH CHECK ADD  CONSTRAINT [FK_document_type] FOREIGN KEY([document_type_id])
REFERENCES [dbo].[correspondence_document_types] ([id])
GO
ALTER TABLE [dbo].[correspondence_document] CHECK CONSTRAINT [FK_document_type]
GO
ALTER TABLE [dbo].[correspondence_relationships]  WITH CHECK ADD  CONSTRAINT [FK_relationship_correspondence1] FOREIGN KEY([correspondence_id1])
REFERENCES [dbo].[correspondences] ([id])
GO
ALTER TABLE [dbo].[correspondence_relationships] CHECK CONSTRAINT [FK_relationship_correspondence1]
GO
ALTER TABLE [dbo].[correspondence_relationships]  WITH CHECK ADD  CONSTRAINT [FK_relationship_correspondence2] FOREIGN KEY([correspondence_id2])
REFERENCES [dbo].[correspondences] ([id])
GO
ALTER TABLE [dbo].[correspondence_relationships] CHECK CONSTRAINT [FK_relationship_correspondence2]
GO
ALTER TABLE [dbo].[correspondence_relationships]  WITH CHECK ADD  CONSTRAINT [FK_relationship_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[correspondence_relationships] CHECK CONSTRAINT [FK_relationship_createdBy]
GO
ALTER TABLE [dbo].[correspondence_workflow]  WITH CHECK ADD  CONSTRAINT [FK_workflow_correspondence] FOREIGN KEY([correspondence_id])
REFERENCES [dbo].[correspondences] ([id])
GO
ALTER TABLE [dbo].[correspondence_workflow] CHECK CONSTRAINT [FK_workflow_correspondence]
GO
ALTER TABLE [dbo].[correspondence_workflow]  WITH CHECK ADD  CONSTRAINT [FK_workflow_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[correspondence_workflow] CHECK CONSTRAINT [FK_workflow_createdBy]
GO
ALTER TABLE [dbo].[correspondence_workflow]  WITH CHECK ADD  CONSTRAINT [FK_workflow_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[correspondence_workflow] CHECK CONSTRAINT [FK_workflow_modifiedBy]
GO
ALTER TABLE [dbo].[correspondence_workflow]  WITH CHECK ADD  CONSTRAINT [FK_workflow_step] FOREIGN KEY([workflow_step_id])
REFERENCES [dbo].[correspondence_workflow_steps] ([id])
GO
ALTER TABLE [dbo].[correspondence_workflow] CHECK CONSTRAINT [FK_workflow_step]
GO
ALTER TABLE [dbo].[correspondence_workflow_steps]  WITH CHECK ADD  CONSTRAINT [FK_workflow_steps_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[correspondence_workflow_steps] CHECK CONSTRAINT [FK_workflow_steps_createdBy]
GO
ALTER TABLE [dbo].[correspondence_workflow_steps]  WITH CHECK ADD  CONSTRAINT [FK_workflow_steps_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[correspondence_workflow_steps] CHECK CONSTRAINT [FK_workflow_steps_modifiedBy]
GO
ALTER TABLE [dbo].[correspondence_workflow_steps]  WITH CHECK ADD  CONSTRAINT [FK_workflow_steps_type] FOREIGN KEY([correspondence_type_id])
REFERENCES [dbo].[correspondence_types] ([id])
GO
ALTER TABLE [dbo].[correspondence_workflow_steps] CHECK CONSTRAINT [FK_workflow_steps_type]
GO
ALTER TABLE [dbo].[correspondences]  WITH CHECK ADD  CONSTRAINT [FK_correspondences_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[correspondences] CHECK CONSTRAINT [FK_correspondences_createdBy]
GO
ALTER TABLE [dbo].[correspondences]  WITH CHECK ADD  CONSTRAINT [FK_correspondences_status] FOREIGN KEY([status_id])
REFERENCES [dbo].[correspondence_statuses] ([id])
GO
ALTER TABLE [dbo].[correspondences] CHECK CONSTRAINT [FK_correspondences_status]
GO
ALTER TABLE [dbo].[correspondences]  WITH CHECK ADD  CONSTRAINT [FK_correspondences_type] FOREIGN KEY([correspondence_type_id])
REFERENCES [dbo].[correspondence_types] ([id])
GO
ALTER TABLE [dbo].[correspondences] CHECK CONSTRAINT [FK_correspondences_type]
GO
ALTER TABLE [dbo].[countries_languages]  WITH CHECK ADD  CONSTRAINT [fk_countries_languages_1] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[countries_languages] CHECK CONSTRAINT [fk_countries_languages_1]
GO
ALTER TABLE [dbo].[countries_languages]  WITH CHECK ADD  CONSTRAINT [fk_countries_languages_2] FOREIGN KEY([country_id])
REFERENCES [dbo].[countries] ([id])
GO
ALTER TABLE [dbo].[countries_languages] CHECK CONSTRAINT [fk_countries_languages_2]
GO
ALTER TABLE [dbo].[courts]  WITH CHECK ADD  CONSTRAINT [FK_courts_rank] FOREIGN KEY([court_rank_id])
REFERENCES [dbo].[court_degrees] ([id])
GO
ALTER TABLE [dbo].[courts] CHECK CONSTRAINT [FK_courts_rank]
GO
ALTER TABLE [dbo].[courts]  WITH CHECK ADD  CONSTRAINT [FK_courts_region] FOREIGN KEY([court_region_id])
REFERENCES [dbo].[court_regions] ([id])
GO
ALTER TABLE [dbo].[courts] CHECK CONSTRAINT [FK_courts_region]
GO
ALTER TABLE [dbo].[courts]  WITH CHECK ADD  CONSTRAINT [FK_courts_type] FOREIGN KEY([court_type_id])
REFERENCES [dbo].[court_types] ([id])
GO
ALTER TABLE [dbo].[courts] CHECK CONSTRAINT [FK_courts_type]
GO
ALTER TABLE [dbo].[cp_user_preferences]  WITH CHECK ADD  CONSTRAINT [fk_cp_user_preferences_users1] FOREIGN KEY([cp_user_id])
REFERENCES [dbo].[customer_portal_users] ([id])
GO
ALTER TABLE [dbo].[cp_user_preferences] CHECK CONSTRAINT [fk_cp_user_preferences_users1]
GO
ALTER TABLE [dbo].[cp_user_signature_attachments]  WITH CHECK ADD  CONSTRAINT [fk_cp_user_signature_attachments_1] FOREIGN KEY([user_id])
REFERENCES [dbo].[customer_portal_users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[cp_user_signature_attachments] CHECK CONSTRAINT [fk_cp_user_signature_attachments_1]
GO
ALTER TABLE [dbo].[credit_note_details]  WITH CHECK ADD  CONSTRAINT [credit_note_details_ibfk_1] FOREIGN KEY([credit_note_header_id])
REFERENCES [dbo].[credit_note_headers] ([id])
GO
ALTER TABLE [dbo].[credit_note_details] CHECK CONSTRAINT [credit_note_details_ibfk_1]
GO
ALTER TABLE [dbo].[credit_note_details]  WITH CHECK ADD  CONSTRAINT [credit_note_details_ibfk_2] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[credit_note_details] CHECK CONSTRAINT [credit_note_details_ibfk_2]
GO
ALTER TABLE [dbo].[credit_note_details]  WITH CHECK ADD  CONSTRAINT [credit_note_details_ibfk_3] FOREIGN KEY([item_id])
REFERENCES [dbo].[items] ([id])
GO
ALTER TABLE [dbo].[credit_note_details] CHECK CONSTRAINT [credit_note_details_ibfk_3]
GO
ALTER TABLE [dbo].[credit_note_details]  WITH CHECK ADD  CONSTRAINT [credit_note_details_ibfk_4] FOREIGN KEY([tax_id])
REFERENCES [dbo].[taxes] ([id])
GO
ALTER TABLE [dbo].[credit_note_details] CHECK CONSTRAINT [credit_note_details_ibfk_4]
GO
ALTER TABLE [dbo].[credit_note_details]  WITH CHECK ADD  CONSTRAINT [credit_note_details_ibfk_5] FOREIGN KEY([expense_id])
REFERENCES [dbo].[expenses] ([id])
GO
ALTER TABLE [dbo].[credit_note_details] CHECK CONSTRAINT [credit_note_details_ibfk_5]
GO
ALTER TABLE [dbo].[credit_note_details]  WITH CHECK ADD  CONSTRAINT [credit_note_details_ibfk_6] FOREIGN KEY([discount_id])
REFERENCES [dbo].[discounts] ([id])
GO
ALTER TABLE [dbo].[credit_note_details] CHECK CONSTRAINT [credit_note_details_ibfk_6]
GO
ALTER TABLE [dbo].[credit_note_headers]  WITH CHECK ADD  CONSTRAINT [credit_note_headers_ibfk_1] FOREIGN KEY([organization_id])
REFERENCES [dbo].[organizations] ([id])
GO
ALTER TABLE [dbo].[credit_note_headers] CHECK CONSTRAINT [credit_note_headers_ibfk_1]
GO
ALTER TABLE [dbo].[credit_note_headers]  WITH CHECK ADD  CONSTRAINT [credit_note_headers_ibfk_2] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[credit_note_headers] CHECK CONSTRAINT [credit_note_headers_ibfk_2]
GO
ALTER TABLE [dbo].[credit_note_headers]  WITH CHECK ADD  CONSTRAINT [credit_note_headers_ibfk_3] FOREIGN KEY([term_id])
REFERENCES [dbo].[terms] ([id])
GO
ALTER TABLE [dbo].[credit_note_headers] CHECK CONSTRAINT [credit_note_headers_ibfk_3]
GO
ALTER TABLE [dbo].[credit_note_headers]  WITH CHECK ADD  CONSTRAINT [credit_note_headers_ibfk_4] FOREIGN KEY([voucher_header_id])
REFERENCES [dbo].[voucher_headers] ([id])
GO
ALTER TABLE [dbo].[credit_note_headers] CHECK CONSTRAINT [credit_note_headers_ibfk_4]
GO
ALTER TABLE [dbo].[credit_note_headers]  WITH CHECK ADD  CONSTRAINT [credit_note_headers_ibfk_5] FOREIGN KEY([discount_id])
REFERENCES [dbo].[discounts] ([id])
GO
ALTER TABLE [dbo].[credit_note_headers] CHECK CONSTRAINT [credit_note_headers_ibfk_5]
GO
ALTER TABLE [dbo].[credit_note_invoices]  WITH CHECK ADD  CONSTRAINT [credit_note_invoices_ibfk_1] FOREIGN KEY([credit_note_header_id])
REFERENCES [dbo].[credit_note_headers] ([id])
GO
ALTER TABLE [dbo].[credit_note_invoices] CHECK CONSTRAINT [credit_note_invoices_ibfk_1]
GO
ALTER TABLE [dbo].[credit_note_invoices]  WITH CHECK ADD  CONSTRAINT [credit_note_invoices_ibfk_2] FOREIGN KEY([invoice_header_id])
REFERENCES [dbo].[invoice_headers] ([id])
GO
ALTER TABLE [dbo].[credit_note_invoices] CHECK CONSTRAINT [credit_note_invoices_ibfk_2]
GO
ALTER TABLE [dbo].[credit_note_item_commissions]  WITH CHECK ADD  CONSTRAINT [credit_note_item_commissions_ibfk_1] FOREIGN KEY([credit_note_header_id])
REFERENCES [dbo].[credit_note_headers] ([id])
GO
ALTER TABLE [dbo].[credit_note_item_commissions] CHECK CONSTRAINT [credit_note_item_commissions_ibfk_1]
GO
ALTER TABLE [dbo].[credit_note_item_commissions]  WITH CHECK ADD  CONSTRAINT [credit_note_item_commissions_ibfk_2] FOREIGN KEY([credit_note_details_id])
REFERENCES [dbo].[credit_note_details] ([id])
GO
ALTER TABLE [dbo].[credit_note_item_commissions] CHECK CONSTRAINT [credit_note_item_commissions_ibfk_2]
GO
ALTER TABLE [dbo].[credit_note_item_commissions]  WITH CHECK ADD  CONSTRAINT [credit_note_item_commissions_ibfk_3] FOREIGN KEY([item_id])
REFERENCES [dbo].[items] ([id])
GO
ALTER TABLE [dbo].[credit_note_item_commissions] CHECK CONSTRAINT [credit_note_item_commissions_ibfk_3]
GO
ALTER TABLE [dbo].[credit_note_item_commissions]  WITH CHECK ADD  CONSTRAINT [credit_note_item_commissions_ibfk_4] FOREIGN KEY([expense_id])
REFERENCES [dbo].[expenses] ([id])
GO
ALTER TABLE [dbo].[credit_note_item_commissions] CHECK CONSTRAINT [credit_note_item_commissions_ibfk_4]
GO
ALTER TABLE [dbo].[credit_note_item_commissions]  WITH CHECK ADD  CONSTRAINT [credit_note_item_commissions_ibfk_5] FOREIGN KEY([time_logs_id])
REFERENCES [dbo].[user_activity_logs] ([id])
GO
ALTER TABLE [dbo].[credit_note_item_commissions] CHECK CONSTRAINT [credit_note_item_commissions_ibfk_5]
GO
ALTER TABLE [dbo].[credit_note_item_commissions]  WITH CHECK ADD  CONSTRAINT [credit_note_item_commissions_ibfk_6] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[credit_note_item_commissions] CHECK CONSTRAINT [credit_note_item_commissions_ibfk_6]
GO
ALTER TABLE [dbo].[credit_note_refunds]  WITH CHECK ADD  CONSTRAINT [credit_note_refunds_ibfk_1] FOREIGN KEY([credit_note_header_id])
REFERENCES [dbo].[credit_note_headers] ([id])
GO
ALTER TABLE [dbo].[credit_note_refunds] CHECK CONSTRAINT [credit_note_refunds_ibfk_1]
GO
ALTER TABLE [dbo].[credit_note_refunds]  WITH CHECK ADD  CONSTRAINT [credit_note_refunds_ibfk_2] FOREIGN KEY([voucher_header_id])
REFERENCES [dbo].[voucher_headers] ([id])
GO
ALTER TABLE [dbo].[credit_note_refunds] CHECK CONSTRAINT [credit_note_refunds_ibfk_2]
GO
ALTER TABLE [dbo].[credit_note_refunds]  WITH CHECK ADD  CONSTRAINT [credit_note_refunds_ibfk_3] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[credit_note_refunds] CHECK CONSTRAINT [credit_note_refunds_ibfk_3]
GO
ALTER TABLE [dbo].[credit_note_refunds]  WITH CHECK ADD  CONSTRAINT [credit_note_refunds_ibfk_4] FOREIGN KEY([client_account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[credit_note_refunds] CHECK CONSTRAINT [credit_note_refunds_ibfk_4]
GO
ALTER TABLE [dbo].[credit_note_related_cases]  WITH CHECK ADD  CONSTRAINT [credit_note_related_cases_ibfk_1] FOREIGN KEY([credit_note_header_id])
REFERENCES [dbo].[credit_note_headers] ([id])
GO
ALTER TABLE [dbo].[credit_note_related_cases] CHECK CONSTRAINT [credit_note_related_cases_ibfk_1]
GO
ALTER TABLE [dbo].[credit_note_related_cases]  WITH CHECK ADD  CONSTRAINT [credit_note_related_cases_ibfk_2] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[credit_note_related_cases] CHECK CONSTRAINT [credit_note_related_cases_ibfk_2]
GO
ALTER TABLE [dbo].[credit_note_time_logs_items]  WITH CHECK ADD  CONSTRAINT [credit_note_time_logs_items_ibfk_1] FOREIGN KEY([credit_note_details_id])
REFERENCES [dbo].[credit_note_details] ([id])
GO
ALTER TABLE [dbo].[credit_note_time_logs_items] CHECK CONSTRAINT [credit_note_time_logs_items_ibfk_1]
GO
ALTER TABLE [dbo].[credit_note_time_logs_items]  WITH CHECK ADD  CONSTRAINT [credit_note_time_logs_items_ibfk_2] FOREIGN KEY([time_log_id])
REFERENCES [dbo].[user_activity_logs] ([id])
GO
ALTER TABLE [dbo].[credit_note_time_logs_items] CHECK CONSTRAINT [credit_note_time_logs_items_ibfk_2]
GO
ALTER TABLE [dbo].[credit_note_time_logs_items]  WITH CHECK ADD  CONSTRAINT [credit_note_time_logs_items_ibfk_3] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[credit_note_time_logs_items] CHECK CONSTRAINT [credit_note_time_logs_items_ibfk_3]
GO
ALTER TABLE [dbo].[criminal_case_details]  WITH CHECK ADD  CONSTRAINT [FK_criminal_case_details_legal_cases] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[criminal_case_details] CHECK CONSTRAINT [FK_criminal_case_details_legal_cases]
GO
ALTER TABLE [dbo].[custom_field_values]  WITH CHECK ADD  CONSTRAINT [fk_custom_field_values] FOREIGN KEY([custom_field_id])
REFERENCES [dbo].[custom_fields] ([id])
GO
ALTER TABLE [dbo].[custom_field_values] CHECK CONSTRAINT [fk_custom_field_values]
GO
ALTER TABLE [dbo].[custom_fields_case_types]  WITH CHECK ADD  CONSTRAINT [fk_custom_fields_case_types_1] FOREIGN KEY([custom_field_id])
REFERENCES [dbo].[custom_fields] ([id])
GO
ALTER TABLE [dbo].[custom_fields_case_types] CHECK CONSTRAINT [fk_custom_fields_case_types_1]
GO
ALTER TABLE [dbo].[custom_fields_languages]  WITH CHECK ADD  CONSTRAINT [fk_custom_fields_languages1] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[custom_fields_languages] CHECK CONSTRAINT [fk_custom_fields_languages1]
GO
ALTER TABLE [dbo].[custom_fields_languages]  WITH CHECK ADD  CONSTRAINT [fk_custom_fields_languages2] FOREIGN KEY([custom_field_id])
REFERENCES [dbo].[custom_fields] ([id])
GO
ALTER TABLE [dbo].[custom_fields_languages] CHECK CONSTRAINT [fk_custom_fields_languages2]
GO
ALTER TABLE [dbo].[custom_fields_per_model_types]  WITH CHECK ADD  CONSTRAINT [fk_custom_fields_per_model_types_1] FOREIGN KEY([custom_field_id])
REFERENCES [dbo].[custom_fields] ([id])
GO
ALTER TABLE [dbo].[custom_fields_per_model_types] CHECK CONSTRAINT [fk_custom_fields_per_model_types_1]
GO
ALTER TABLE [dbo].[customer_portal_container_watchers]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_container_watchers_1] FOREIGN KEY([case_container_id])
REFERENCES [dbo].[legal_case_containers] ([id])
GO
ALTER TABLE [dbo].[customer_portal_container_watchers] CHECK CONSTRAINT [fk_customer_portal_container_watchers_1]
GO
ALTER TABLE [dbo].[customer_portal_container_watchers]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_container_watchers_2] FOREIGN KEY([customer_portal_user_id])
REFERENCES [dbo].[customer_portal_users] ([id])
GO
ALTER TABLE [dbo].[customer_portal_container_watchers] CHECK CONSTRAINT [fk_customer_portal_container_watchers_2]
GO
ALTER TABLE [dbo].[customer_portal_contract_permissions]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_contract_permissions_1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[contract_workflow] ([id])
GO
ALTER TABLE [dbo].[customer_portal_contract_permissions] CHECK CONSTRAINT [fk_customer_portal_contract_permissions_1]
GO
ALTER TABLE [dbo].[customer_portal_contract_permissions]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_contract_permissions_2] FOREIGN KEY([workflow_status_transition_id])
REFERENCES [dbo].[contract_workflow_status_transition] ([id])
GO
ALTER TABLE [dbo].[customer_portal_contract_permissions] CHECK CONSTRAINT [fk_customer_portal_contract_permissions_2]
GO
ALTER TABLE [dbo].[customer_portal_contract_watchers]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_contract_watchers_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[customer_portal_contract_watchers] CHECK CONSTRAINT [fk_customer_portal_contract_watchers_1]
GO
ALTER TABLE [dbo].[customer_portal_contract_watchers]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_contract_watchers_2] FOREIGN KEY([customer_portal_user_id])
REFERENCES [dbo].[customer_portal_users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[customer_portal_contract_watchers] CHECK CONSTRAINT [fk_customer_portal_contract_watchers_2]
GO
ALTER TABLE [dbo].[customer_portal_permissions]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_permissions1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[workflows] ([id])
GO
ALTER TABLE [dbo].[customer_portal_permissions] CHECK CONSTRAINT [fk_customer_portal_permissions1]
GO
ALTER TABLE [dbo].[customer_portal_permissions]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_permissions2] FOREIGN KEY([workflow_status_transition_id])
REFERENCES [dbo].[workflow_status_transition] ([id])
GO
ALTER TABLE [dbo].[customer_portal_permissions] CHECK CONSTRAINT [fk_customer_portal_permissions2]
GO
ALTER TABLE [dbo].[customer_portal_screen_field_languages]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_screen_field_languages1] FOREIGN KEY([customer_portal_screen_field_id])
REFERENCES [dbo].[customer_portal_screen_fields] ([id])
GO
ALTER TABLE [dbo].[customer_portal_screen_field_languages] CHECK CONSTRAINT [fk_customer_portal_screen_field_languages1]
GO
ALTER TABLE [dbo].[customer_portal_screen_field_languages]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_screen_field_languages2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[customer_portal_screen_field_languages] CHECK CONSTRAINT [fk_customer_portal_screen_field_languages2]
GO
ALTER TABLE [dbo].[customer_portal_screen_fields]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_screen_fields1] FOREIGN KEY([customer_portal_screen_id])
REFERENCES [dbo].[customer_portal_screens] ([id])
GO
ALTER TABLE [dbo].[customer_portal_screen_fields] CHECK CONSTRAINT [fk_customer_portal_screen_fields1]
GO
ALTER TABLE [dbo].[customer_portal_screens]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_screens_1] FOREIGN KEY([request_type_category_id])
REFERENCES [dbo].[request_type_categories] ([id])
GO
ALTER TABLE [dbo].[customer_portal_screens] CHECK CONSTRAINT [fk_customer_portal_screens_1]
GO
ALTER TABLE [dbo].[customer_portal_sla]  WITH CHECK ADD  CONSTRAINT [FK_customer_portal_sla_case_types] FOREIGN KEY([case_type_id])
REFERENCES [dbo].[case_types] ([id])
GO
ALTER TABLE [dbo].[customer_portal_sla] CHECK CONSTRAINT [FK_customer_portal_sla_case_types]
GO
ALTER TABLE [dbo].[customer_portal_sla]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_sla1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[workflows] ([id])
GO
ALTER TABLE [dbo].[customer_portal_sla] CHECK CONSTRAINT [fk_customer_portal_sla1]
GO
ALTER TABLE [dbo].[customer_portal_sla]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_sla2] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[customer_portal_sla] CHECK CONSTRAINT [fk_customer_portal_sla2]
GO
ALTER TABLE [dbo].[customer_portal_sla]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_sla3] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[customer_portal_sla] CHECK CONSTRAINT [fk_customer_portal_sla3]
GO
ALTER TABLE [dbo].[customer_portal_sla_cases]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_sla_cases1] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[customer_portal_sla_cases] CHECK CONSTRAINT [fk_customer_portal_sla_cases1]
GO
ALTER TABLE [dbo].[customer_portal_sla_cases]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_sla_cases3] FOREIGN KEY([customer_portal_sla_id])
REFERENCES [dbo].[customer_portal_sla] ([id])
GO
ALTER TABLE [dbo].[customer_portal_sla_cases] CHECK CONSTRAINT [fk_customer_portal_sla_cases3]
GO
ALTER TABLE [dbo].[customer_portal_sla_notification]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_sla_notification_1] FOREIGN KEY([sla_id])
REFERENCES [dbo].[customer_portal_sla] ([id])
GO
ALTER TABLE [dbo].[customer_portal_sla_notification] CHECK CONSTRAINT [fk_customer_portal_sla_notification_1]
GO
ALTER TABLE [dbo].[customer_portal_sla_notification]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_sla_notification_2] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[customer_portal_sla_notification] CHECK CONSTRAINT [fk_customer_portal_sla_notification_2]
GO
ALTER TABLE [dbo].[customer_portal_ticket_watchers]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_ticket_watchers_1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[customer_portal_ticket_watchers] CHECK CONSTRAINT [fk_customer_portal_ticket_watchers_1]
GO
ALTER TABLE [dbo].[customer_portal_ticket_watchers]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_ticket_watchers_2] FOREIGN KEY([customer_portal_user_id])
REFERENCES [dbo].[customer_portal_users] ([id])
GO
ALTER TABLE [dbo].[customer_portal_ticket_watchers] CHECK CONSTRAINT [fk_customer_portal_ticket_watchers_2]
GO
ALTER TABLE [dbo].[customer_portal_users_assignments]  WITH CHECK ADD  CONSTRAINT [fk_customer_portal_users_assignments_1] FOREIGN KEY([screen])
REFERENCES [dbo].[customer_portal_screens] ([id])
GO
ALTER TABLE [dbo].[customer_portal_users_assignments] CHECK CONSTRAINT [fk_customer_portal_users_assignments_1]
GO
ALTER TABLE [dbo].[deposits]  WITH CHECK ADD  CONSTRAINT [fk_deposits_1] FOREIGN KEY([voucher_header_id])
REFERENCES [dbo].[voucher_headers] ([id])
GO
ALTER TABLE [dbo].[deposits] CHECK CONSTRAINT [fk_deposits_1]
GO
ALTER TABLE [dbo].[deposits]  WITH CHECK ADD  CONSTRAINT [fk_deposits_2] FOREIGN KEY([client_trust_accounts_id])
REFERENCES [dbo].[client_trust_accounts_relation] ([id])
GO
ALTER TABLE [dbo].[deposits] CHECK CONSTRAINT [fk_deposits_2]
GO
ALTER TABLE [dbo].[deposits]  WITH CHECK ADD  CONSTRAINT [fk_deposits_3] FOREIGN KEY([currency])
REFERENCES [dbo].[countries] ([id])
GO
ALTER TABLE [dbo].[deposits] CHECK CONSTRAINT [fk_deposits_3]
GO
ALTER TABLE [dbo].[docs_documents]  WITH CHECK ADD  CONSTRAINT [docs_documents_ibfk1] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[docs_documents] CHECK CONSTRAINT [docs_documents_ibfk1]
GO
ALTER TABLE [dbo].[docs_documents]  WITH CHECK ADD  CONSTRAINT [docs_documents_ibfk2] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[docs_documents] CHECK CONSTRAINT [docs_documents_ibfk2]
GO
ALTER TABLE [dbo].[docs_documents]  WITH CHECK ADD  CONSTRAINT [docs_documents_ibfk3] FOREIGN KEY([docs_document_status_id])
REFERENCES [dbo].[docs_document_statuses] ([id])
GO
ALTER TABLE [dbo].[docs_documents] CHECK CONSTRAINT [docs_documents_ibfk3]
GO
ALTER TABLE [dbo].[docs_documents]  WITH CHECK ADD  CONSTRAINT [docs_documents_ibfk4] FOREIGN KEY([docs_document_type_id])
REFERENCES [dbo].[docs_document_types] ([id])
GO
ALTER TABLE [dbo].[docs_documents] CHECK CONSTRAINT [docs_documents_ibfk4]
GO
ALTER TABLE [dbo].[document_managment_users]  WITH CHECK ADD  CONSTRAINT [document_managment_users_ibfk_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[document_managment_users] CHECK CONSTRAINT [document_managment_users_ibfk_2]
GO
ALTER TABLE [dbo].[event_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_event_types_languages1] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[event_types_languages] CHECK CONSTRAINT [fk_event_types_languages1]
GO
ALTER TABLE [dbo].[event_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_event_types_languages2] FOREIGN KEY([event_type_id])
REFERENCES [dbo].[event_types] ([id])
GO
ALTER TABLE [dbo].[event_types_languages] CHECK CONSTRAINT [fk_event_types_languages2]
GO
ALTER TABLE [dbo].[events]  WITH CHECK ADD  CONSTRAINT [fk_events_1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[events] CHECK CONSTRAINT [fk_events_1]
GO
ALTER TABLE [dbo].[events]  WITH CHECK ADD  CONSTRAINT [fk_events_2] FOREIGN KEY([task_location_id])
REFERENCES [dbo].[task_locations] ([id])
GO
ALTER TABLE [dbo].[events] CHECK CONSTRAINT [fk_events_2]
GO
ALTER TABLE [dbo].[events]  WITH CHECK ADD  CONSTRAINT [fk_events_3] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[events] CHECK CONSTRAINT [fk_events_3]
GO
ALTER TABLE [dbo].[events]  WITH CHECK ADD  CONSTRAINT [fk_events_4] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[events] CHECK CONSTRAINT [fk_events_4]
GO
ALTER TABLE [dbo].[events_attendees]  WITH CHECK ADD  CONSTRAINT [fk_events_attendees_1] FOREIGN KEY([event_id])
REFERENCES [dbo].[events] ([id])
GO
ALTER TABLE [dbo].[events_attendees] CHECK CONSTRAINT [fk_events_attendees_1]
GO
ALTER TABLE [dbo].[events_attendees]  WITH CHECK ADD  CONSTRAINT [fk_events_attendees_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[events_attendees] CHECK CONSTRAINT [fk_events_attendees_2]
GO
ALTER TABLE [dbo].[exchange_rates]  WITH CHECK ADD FOREIGN KEY([currency_id])
REFERENCES [dbo].[countries] ([id])
GO
ALTER TABLE [dbo].[exchange_rates]  WITH CHECK ADD FOREIGN KEY([currency_id])
REFERENCES [dbo].[countries] ([id])
GO
ALTER TABLE [dbo].[exchange_rates]  WITH CHECK ADD FOREIGN KEY([organization_id])
REFERENCES [dbo].[organizations] ([id])
GO
ALTER TABLE [dbo].[exchange_rates]  WITH CHECK ADD FOREIGN KEY([organization_id])
REFERENCES [dbo].[organizations] ([id])
GO
ALTER TABLE [dbo].[exhibit]  WITH CHECK ADD  CONSTRAINT [fk_case_exhibit_case] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[exhibit] CHECK CONSTRAINT [fk_case_exhibit_case]
GO
ALTER TABLE [dbo].[exhibit]  WITH CHECK ADD  CONSTRAINT [fk_case_exhibit_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[exhibit] CHECK CONSTRAINT [fk_case_exhibit_createdBy]
GO
ALTER TABLE [dbo].[exhibit]  WITH CHECK ADD  CONSTRAINT [fk_case_exhibit_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[exhibit] CHECK CONSTRAINT [fk_case_exhibit_modifiedBy]
GO
ALTER TABLE [dbo].[exhibit]  WITH CHECK ADD  CONSTRAINT [fk_exhibit_current_location] FOREIGN KEY([current_location_id])
REFERENCES [dbo].[exhibit_locations] ([id])
GO
ALTER TABLE [dbo].[exhibit] CHECK CONSTRAINT [fk_exhibit_current_location]
GO
ALTER TABLE [dbo].[exhibit]  WITH CHECK ADD  CONSTRAINT [fk_exhibit_pickup_location] FOREIGN KEY([pickup_location_id])
REFERENCES [dbo].[exhibit_locations] ([id])
GO
ALTER TABLE [dbo].[exhibit] CHECK CONSTRAINT [fk_exhibit_pickup_location]
GO
ALTER TABLE [dbo].[exhibit_activities_log]  WITH CHECK ADD FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[exhibit_activities_log]  WITH CHECK ADD FOREIGN KEY([exhibit_id])
REFERENCES [dbo].[exhibit] ([id])
GO
ALTER TABLE [dbo].[exhibit_activities_log]  WITH CHECK ADD  CONSTRAINT [FK_exhibit_activities_log_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[exhibit_activities_log] CHECK CONSTRAINT [FK_exhibit_activities_log_createdBy]
GO
ALTER TABLE [dbo].[exhibit_activities_log]  WITH CHECK ADD  CONSTRAINT [FK_exhibit_activities_log_exhibit] FOREIGN KEY([exhibit_id])
REFERENCES [dbo].[exhibit] ([id])
GO
ALTER TABLE [dbo].[exhibit_activities_log] CHECK CONSTRAINT [FK_exhibit_activities_log_exhibit]
GO
ALTER TABLE [dbo].[exhibit_chain_of_movement]  WITH CHECK ADD  CONSTRAINT [fk_chain_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[exhibit_chain_of_movement] CHECK CONSTRAINT [fk_chain_createdBy]
GO
ALTER TABLE [dbo].[exhibit_chain_of_movement]  WITH CHECK ADD  CONSTRAINT [fk_chain_exhibit_id] FOREIGN KEY([exhibit_id])
REFERENCES [dbo].[exhibit] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[exhibit_chain_of_movement] CHECK CONSTRAINT [fk_chain_exhibit_id]
GO
ALTER TABLE [dbo].[exhibit_chain_of_movement]  WITH CHECK ADD  CONSTRAINT [fk_chain_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[exhibit_chain_of_movement] CHECK CONSTRAINT [fk_chain_modifiedBy]
GO
ALTER TABLE [dbo].[exhibit_chain_of_movement]  WITH CHECK ADD  CONSTRAINT [fk_chain_officer_receiving] FOREIGN KEY([officer_receiving])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[exhibit_chain_of_movement] CHECK CONSTRAINT [fk_chain_officer_receiving]
GO
ALTER TABLE [dbo].[exhibit_chain_of_movement]  WITH CHECK ADD  CONSTRAINT [fk_chain_transfer_from] FOREIGN KEY([transfer_from_id])
REFERENCES [dbo].[exhibit_locations] ([id])
GO
ALTER TABLE [dbo].[exhibit_chain_of_movement] CHECK CONSTRAINT [fk_chain_transfer_from]
GO
ALTER TABLE [dbo].[exhibit_chain_of_movement]  WITH CHECK ADD  CONSTRAINT [fk_chain_transfer_to] FOREIGN KEY([transfer_to_id])
REFERENCES [dbo].[exhibit_locations] ([id])
GO
ALTER TABLE [dbo].[exhibit_chain_of_movement] CHECK CONSTRAINT [fk_chain_transfer_to]
GO
ALTER TABLE [dbo].[exhibit_document]  WITH CHECK ADD  CONSTRAINT [fk_exhibit_document_1] FOREIGN KEY([exhibit_id])
REFERENCES [dbo].[exhibit] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[exhibit_document] CHECK CONSTRAINT [fk_exhibit_document_1]
GO
ALTER TABLE [dbo].[exhibit_document]  WITH CHECK ADD  CONSTRAINT [fk_exhibit_document_2] FOREIGN KEY([document])
REFERENCES [dbo].[documents_management_system] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[exhibit_document] CHECK CONSTRAINT [fk_exhibit_document_2]
GO
ALTER TABLE [dbo].[expense_categories]  WITH CHECK ADD  CONSTRAINT [expense_categories_ibfk_1] FOREIGN KEY([expense_category_id])
REFERENCES [dbo].[expense_categories] ([id])
GO
ALTER TABLE [dbo].[expense_categories] CHECK CONSTRAINT [expense_categories_ibfk_1]
GO
ALTER TABLE [dbo].[expense_categories]  WITH CHECK ADD  CONSTRAINT [expense_categories_ibfk_2] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[expense_categories] CHECK CONSTRAINT [expense_categories_ibfk_2]
GO
ALTER TABLE [dbo].[expense_status_notes]  WITH CHECK ADD  CONSTRAINT [fk_expense_status_notes_1] FOREIGN KEY([expense_id])
REFERENCES [dbo].[expenses] ([id])
GO
ALTER TABLE [dbo].[expense_status_notes] CHECK CONSTRAINT [fk_expense_status_notes_1]
GO
ALTER TABLE [dbo].[expenses]  WITH CHECK ADD  CONSTRAINT [expenses_headers_ibfk_1] FOREIGN KEY([vendor_id])
REFERENCES [dbo].[vendors] ([id])
GO
ALTER TABLE [dbo].[expenses] CHECK CONSTRAINT [expenses_headers_ibfk_1]
GO
ALTER TABLE [dbo].[expenses]  WITH CHECK ADD  CONSTRAINT [expenses_headers_ibfk_2] FOREIGN KEY([client_id])
REFERENCES [dbo].[clients] ([id])
GO
ALTER TABLE [dbo].[expenses] CHECK CONSTRAINT [expenses_headers_ibfk_2]
GO
ALTER TABLE [dbo].[expenses]  WITH CHECK ADD  CONSTRAINT [expenses_headers_ibfk_3] FOREIGN KEY([expense_account])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[expenses] CHECK CONSTRAINT [expenses_headers_ibfk_3]
GO
ALTER TABLE [dbo].[expenses]  WITH CHECK ADD  CONSTRAINT [expenses_headers_ibfk_4] FOREIGN KEY([paid_through])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[expenses] CHECK CONSTRAINT [expenses_headers_ibfk_4]
GO
ALTER TABLE [dbo].[expenses]  WITH CHECK ADD  CONSTRAINT [expenses_headers_ibfk_5] FOREIGN KEY([client_account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[expenses] CHECK CONSTRAINT [expenses_headers_ibfk_5]
GO
ALTER TABLE [dbo].[expenses]  WITH CHECK ADD  CONSTRAINT [expenses_headers_ibfk_6] FOREIGN KEY([voucher_header_id])
REFERENCES [dbo].[voucher_headers] ([id])
GO
ALTER TABLE [dbo].[expenses] CHECK CONSTRAINT [expenses_headers_ibfk_6]
GO
ALTER TABLE [dbo].[expenses]  WITH CHECK ADD  CONSTRAINT [expenses_headers_ibfk_7] FOREIGN KEY([expense_category_id])
REFERENCES [dbo].[expense_categories] ([id])
GO
ALTER TABLE [dbo].[expenses] CHECK CONSTRAINT [expenses_headers_ibfk_7]
GO
ALTER TABLE [dbo].[expenses]  WITH CHECK ADD  CONSTRAINT [expenses_headers_ibfk_8] FOREIGN KEY([tax_id])
REFERENCES [dbo].[taxes] ([id])
GO
ALTER TABLE [dbo].[expenses] CHECK CONSTRAINT [expenses_headers_ibfk_8]
GO
ALTER TABLE [dbo].[expenses]  WITH CHECK ADD  CONSTRAINT [fk_expenses_10] FOREIGN KEY([hearing])
REFERENCES [dbo].[legal_case_hearings] ([id])
GO
ALTER TABLE [dbo].[expenses] CHECK CONSTRAINT [fk_expenses_10]
GO
ALTER TABLE [dbo].[expenses]  WITH CHECK ADD  CONSTRAINT [fk_expenses_11] FOREIGN KEY([event])
REFERENCES [dbo].[legal_case_events] ([id])
GO
ALTER TABLE [dbo].[expenses] CHECK CONSTRAINT [fk_expenses_11]
GO
ALTER TABLE [dbo].[expenses]  WITH CHECK ADD  CONSTRAINT [fk_expenses_9] FOREIGN KEY([task])
REFERENCES [dbo].[tasks] ([id])
GO
ALTER TABLE [dbo].[expenses] CHECK CONSTRAINT [fk_expenses_9]
GO
ALTER TABLE [dbo].[grid_saved_board_filters_users]  WITH CHECK ADD  CONSTRAINT [grid_saved_board_filters_users_ibfk_1] FOREIGN KEY([board_id])
REFERENCES [dbo].[planning_boards] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[grid_saved_board_filters_users] CHECK CONSTRAINT [grid_saved_board_filters_users_ibfk_1]
GO
ALTER TABLE [dbo].[grid_saved_board_filters_users]  WITH CHECK ADD  CONSTRAINT [grid_saved_board_filters_users_ibfk_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[grid_saved_board_filters_users] CHECK CONSTRAINT [grid_saved_board_filters_users_ibfk_2]
GO
ALTER TABLE [dbo].[grid_saved_board_task_filters_users]  WITH CHECK ADD  CONSTRAINT [grid_saved_board_task_filters_users_ibfk_1] FOREIGN KEY([board_id])
REFERENCES [dbo].[task_boards] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[grid_saved_board_task_filters_users] CHECK CONSTRAINT [grid_saved_board_task_filters_users_ibfk_1]
GO
ALTER TABLE [dbo].[grid_saved_board_task_filters_users]  WITH CHECK ADD  CONSTRAINT [grid_saved_board_task_filters_users_ibfk_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[grid_saved_board_task_filters_users] CHECK CONSTRAINT [grid_saved_board_task_filters_users_ibfk_2]
GO
ALTER TABLE [dbo].[grid_saved_columns]  WITH CHECK ADD  CONSTRAINT [fk_grid_saved_columns_1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[grid_saved_columns] CHECK CONSTRAINT [fk_grid_saved_columns_1]
GO
ALTER TABLE [dbo].[grid_saved_columns]  WITH CHECK ADD  CONSTRAINT [fk_grid_saved_columns_2] FOREIGN KEY([grid_saved_filter_id])
REFERENCES [dbo].[grid_saved_filters] ([id])
GO
ALTER TABLE [dbo].[grid_saved_columns] CHECK CONSTRAINT [fk_grid_saved_columns_2]
GO
ALTER TABLE [dbo].[grid_saved_filters]  WITH CHECK ADD  CONSTRAINT [fk_grid_saved_filters1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[grid_saved_filters] CHECK CONSTRAINT [fk_grid_saved_filters1]
GO
ALTER TABLE [dbo].[grid_saved_filters]  WITH CHECK ADD  CONSTRAINT [fk_grid_saved_filters2] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[grid_saved_filters] CHECK CONSTRAINT [fk_grid_saved_filters2]
GO
ALTER TABLE [dbo].[grid_saved_filters]  WITH CHECK ADD  CONSTRAINT [fk_grid_saved_filters3] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[grid_saved_filters] CHECK CONSTRAINT [fk_grid_saved_filters3]
GO
ALTER TABLE [dbo].[grid_saved_filters_users]  WITH CHECK ADD  CONSTRAINT [fk_grid_saved_filters_users1] FOREIGN KEY([filter_id])
REFERENCES [dbo].[grid_saved_filters] ([id])
GO
ALTER TABLE [dbo].[grid_saved_filters_users] CHECK CONSTRAINT [fk_grid_saved_filters_users1]
GO
ALTER TABLE [dbo].[grid_saved_filters_users]  WITH CHECK ADD  CONSTRAINT [fk_grid_saved_filters_users2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[grid_saved_filters_users] CHECK CONSTRAINT [fk_grid_saved_filters_users2]
GO
ALTER TABLE [dbo].[hearing_outcome_reasons_languages]  WITH CHECK ADD  CONSTRAINT [hearing_outcome_reasons_languages_ibfk_1] FOREIGN KEY([hearing_outcome_reason])
REFERENCES [dbo].[hearing_outcome_reasons] ([id])
GO
ALTER TABLE [dbo].[hearing_outcome_reasons_languages] CHECK CONSTRAINT [hearing_outcome_reasons_languages_ibfk_1]
GO
ALTER TABLE [dbo].[hearing_outcome_reasons_languages]  WITH CHECK ADD  CONSTRAINT [hearing_outcome_reasons_languages_ibfk_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[hearing_outcome_reasons_languages] CHECK CONSTRAINT [hearing_outcome_reasons_languages_ibfk_2]
GO
ALTER TABLE [dbo].[hearing_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_hearing_types_languages_1] FOREIGN KEY([type])
REFERENCES [dbo].[hearing_types] ([id])
GO
ALTER TABLE [dbo].[hearing_types_languages] CHECK CONSTRAINT [fk_hearing_types_languages_1]
GO
ALTER TABLE [dbo].[hearing_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_hearing_types_languages_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[hearing_types_languages] CHECK CONSTRAINT [fk_hearing_types_languages_2]
GO
ALTER TABLE [dbo].[hearings_documents]  WITH CHECK ADD  CONSTRAINT [fk_hearings_documents_1] FOREIGN KEY([hearing])
REFERENCES [dbo].[legal_case_hearings] ([id])
GO
ALTER TABLE [dbo].[hearings_documents] CHECK CONSTRAINT [fk_hearings_documents_1]
GO
ALTER TABLE [dbo].[hearings_documents]  WITH CHECK ADD  CONSTRAINT [fk_hearings_documents_2] FOREIGN KEY([document])
REFERENCES [dbo].[documents_management_system] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[hearings_documents] CHECK CONSTRAINT [fk_hearings_documents_2]
GO
ALTER TABLE [dbo].[invoice_detail_cover_page_template]  WITH CHECK ADD FOREIGN KEY([organization_id])
REFERENCES [dbo].[organizations] ([id])
GO
ALTER TABLE [dbo].[invoice_detail_cover_page_template]  WITH CHECK ADD FOREIGN KEY([organization_id])
REFERENCES [dbo].[organizations] ([id])
GO
ALTER TABLE [dbo].[invoice_details]  WITH CHECK ADD  CONSTRAINT [invoice_details_headers_ibfk_1] FOREIGN KEY([invoice_header_id])
REFERENCES [dbo].[invoice_headers] ([id])
GO
ALTER TABLE [dbo].[invoice_details] CHECK CONSTRAINT [invoice_details_headers_ibfk_1]
GO
ALTER TABLE [dbo].[invoice_details]  WITH CHECK ADD  CONSTRAINT [invoice_details_headers_ibfk_2] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[invoice_details] CHECK CONSTRAINT [invoice_details_headers_ibfk_2]
GO
ALTER TABLE [dbo].[invoice_details]  WITH CHECK ADD  CONSTRAINT [invoice_details_headers_ibfk_3] FOREIGN KEY([item_id])
REFERENCES [dbo].[items] ([id])
GO
ALTER TABLE [dbo].[invoice_details] CHECK CONSTRAINT [invoice_details_headers_ibfk_3]
GO
ALTER TABLE [dbo].[invoice_details]  WITH CHECK ADD  CONSTRAINT [invoice_details_headers_ibfk_4] FOREIGN KEY([sub_item_id])
REFERENCES [dbo].[items] ([id])
GO
ALTER TABLE [dbo].[invoice_details] CHECK CONSTRAINT [invoice_details_headers_ibfk_4]
GO
ALTER TABLE [dbo].[invoice_details]  WITH CHECK ADD  CONSTRAINT [invoice_details_headers_ibfk_5] FOREIGN KEY([tax_id])
REFERENCES [dbo].[taxes] ([id])
GO
ALTER TABLE [dbo].[invoice_details] CHECK CONSTRAINT [invoice_details_headers_ibfk_5]
GO
ALTER TABLE [dbo].[invoice_details]  WITH CHECK ADD  CONSTRAINT [invoice_details_headers_ibfk_6] FOREIGN KEY([expense_id])
REFERENCES [dbo].[expenses] ([id])
GO
ALTER TABLE [dbo].[invoice_details] CHECK CONSTRAINT [invoice_details_headers_ibfk_6]
GO
ALTER TABLE [dbo].[invoice_details]  WITH CHECK ADD  CONSTRAINT [invoice_details_headers_ibfk_7] FOREIGN KEY([discount_id])
REFERENCES [dbo].[discounts] ([id])
GO
ALTER TABLE [dbo].[invoice_details] CHECK CONSTRAINT [invoice_details_headers_ibfk_7]
GO
ALTER TABLE [dbo].[invoice_headers]  WITH CHECK ADD  CONSTRAINT [fk_invoice_headers_4] FOREIGN KEY([related_quote_id])
REFERENCES [dbo].[voucher_headers] ([id])
GO
ALTER TABLE [dbo].[invoice_headers] CHECK CONSTRAINT [fk_invoice_headers_4]
GO
ALTER TABLE [dbo].[invoice_headers]  WITH CHECK ADD  CONSTRAINT [invoice_headers_headers_ibfk_1] FOREIGN KEY([voucher_header_id])
REFERENCES [dbo].[voucher_headers] ([id])
GO
ALTER TABLE [dbo].[invoice_headers] CHECK CONSTRAINT [invoice_headers_headers_ibfk_1]
GO
ALTER TABLE [dbo].[invoice_headers]  WITH CHECK ADD  CONSTRAINT [invoice_headers_headers_ibfk_2] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[invoice_headers] CHECK CONSTRAINT [invoice_headers_headers_ibfk_2]
GO
ALTER TABLE [dbo].[invoice_headers]  WITH CHECK ADD  CONSTRAINT [invoice_headers_headers_ibfk_3] FOREIGN KEY([term_id])
REFERENCES [dbo].[terms] ([id])
GO
ALTER TABLE [dbo].[invoice_headers] CHECK CONSTRAINT [invoice_headers_headers_ibfk_3]
GO
ALTER TABLE [dbo].[invoice_headers]  WITH CHECK ADD  CONSTRAINT [invoice_headers_headers_ibfk_4] FOREIGN KEY([discount_id])
REFERENCES [dbo].[discounts] ([id])
GO
ALTER TABLE [dbo].[invoice_headers] CHECK CONSTRAINT [invoice_headers_headers_ibfk_4]
GO
ALTER TABLE [dbo].[invoice_headers]  WITH CHECK ADD  CONSTRAINT [invoice_headers_headers_ibfk_5] FOREIGN KEY([original_invoice_id])
REFERENCES [dbo].[invoice_headers] ([id])
GO
ALTER TABLE [dbo].[invoice_headers] CHECK CONSTRAINT [invoice_headers_headers_ibfk_5]
GO
ALTER TABLE [dbo].[invoice_headers]  WITH CHECK ADD  CONSTRAINT [invoice_headers_headers_ibfk_6] FOREIGN KEY([debit_note_reason_id])
REFERENCES [dbo].[credit_note_reasons] ([id])
GO
ALTER TABLE [dbo].[invoice_headers] CHECK CONSTRAINT [invoice_headers_headers_ibfk_6]
GO
ALTER TABLE [dbo].[invoice_headers]  WITH CHECK ADD  CONSTRAINT [invoice_headers_headers_ibfk_7] FOREIGN KEY([invoice_type_id])
REFERENCES [dbo].[invoice_types] ([id])
GO
ALTER TABLE [dbo].[invoice_headers] CHECK CONSTRAINT [invoice_headers_headers_ibfk_7]
GO
ALTER TABLE [dbo].[invoice_headers]  WITH CHECK ADD  CONSTRAINT [invoice_headers_headers_ibfk_8] FOREIGN KEY([invoice_template_id])
REFERENCES [dbo].[organization_invoice_templates] ([id])
GO
ALTER TABLE [dbo].[invoice_headers] CHECK CONSTRAINT [invoice_headers_headers_ibfk_8]
GO
ALTER TABLE [dbo].[invoice_payment_invoices]  WITH CHECK ADD  CONSTRAINT [invoice_payment_invoices_headers_ibfk_1] FOREIGN KEY([invoice_payment_id])
REFERENCES [dbo].[invoice_payments] ([id])
GO
ALTER TABLE [dbo].[invoice_payment_invoices] CHECK CONSTRAINT [invoice_payment_invoices_headers_ibfk_1]
GO
ALTER TABLE [dbo].[invoice_payment_invoices]  WITH CHECK ADD  CONSTRAINT [invoice_payment_invoices_headers_ibfk_2] FOREIGN KEY([invoice_header_id])
REFERENCES [dbo].[invoice_headers] ([id])
GO
ALTER TABLE [dbo].[invoice_payment_invoices] CHECK CONSTRAINT [invoice_payment_invoices_headers_ibfk_2]
GO
ALTER TABLE [dbo].[invoice_payments]  WITH CHECK ADD  CONSTRAINT [invoice_payments_headers_ibfk_1] FOREIGN KEY([voucher_header_id])
REFERENCES [dbo].[voucher_headers] ([id])
GO
ALTER TABLE [dbo].[invoice_payments] CHECK CONSTRAINT [invoice_payments_headers_ibfk_1]
GO
ALTER TABLE [dbo].[invoice_payments]  WITH CHECK ADD  CONSTRAINT [invoice_payments_headers_ibfk_2] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[invoice_payments] CHECK CONSTRAINT [invoice_payments_headers_ibfk_2]
GO
ALTER TABLE [dbo].[invoice_payments]  WITH CHECK ADD  CONSTRAINT [invoice_payments_headers_ibfk_3] FOREIGN KEY([client_account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[invoice_payments] CHECK CONSTRAINT [invoice_payments_headers_ibfk_3]
GO
ALTER TABLE [dbo].[invoice_time_logs_items]  WITH CHECK ADD  CONSTRAINT [fk_invoice_time_logs_items_1] FOREIGN KEY([item])
REFERENCES [dbo].[invoice_details] ([id])
GO
ALTER TABLE [dbo].[invoice_time_logs_items] CHECK CONSTRAINT [fk_invoice_time_logs_items_1]
GO
ALTER TABLE [dbo].[invoice_time_logs_items]  WITH CHECK ADD  CONSTRAINT [fk_invoice_time_logs_items_2] FOREIGN KEY([time_log])
REFERENCES [dbo].[user_activity_logs] ([id])
GO
ALTER TABLE [dbo].[invoice_time_logs_items] CHECK CONSTRAINT [fk_invoice_time_logs_items_2]
GO
ALTER TABLE [dbo].[invoice_time_logs_items]  WITH CHECK ADD  CONSTRAINT [fk_invoice_time_logs_items_3] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[invoice_time_logs_items] CHECK CONSTRAINT [fk_invoice_time_logs_items_3]
GO
ALTER TABLE [dbo].[ip_petitions_oppositions]  WITH CHECK ADD  CONSTRAINT [fk_ip_petitions_oppositions1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[ip_petitions_oppositions] CHECK CONSTRAINT [fk_ip_petitions_oppositions1]
GO
ALTER TABLE [dbo].[ip_petitions_oppositions]  WITH CHECK ADD  CONSTRAINT [fk_ip_petitions_oppositions2] FOREIGN KEY([ip_detail_id])
REFERENCES [dbo].[ip_details] ([id])
GO
ALTER TABLE [dbo].[ip_petitions_oppositions] CHECK CONSTRAINT [fk_ip_petitions_oppositions2]
GO
ALTER TABLE [dbo].[ip_petitions_oppositions]  WITH CHECK ADD  CONSTRAINT [fk_ip_petitions_oppositions3] FOREIGN KEY([type])
REFERENCES [dbo].[ip_petitions_oppositions_types] ([id])
GO
ALTER TABLE [dbo].[ip_petitions_oppositions] CHECK CONSTRAINT [fk_ip_petitions_oppositions3]
GO
ALTER TABLE [dbo].[item_commissions]  WITH CHECK ADD  CONSTRAINT [item_commissions_ibfk_1] FOREIGN KEY([invoice_header_id])
REFERENCES [dbo].[invoice_headers] ([id])
GO
ALTER TABLE [dbo].[item_commissions] CHECK CONSTRAINT [item_commissions_ibfk_1]
GO
ALTER TABLE [dbo].[item_commissions]  WITH CHECK ADD  CONSTRAINT [item_commissions_ibfk_2] FOREIGN KEY([invoice_details_id])
REFERENCES [dbo].[invoice_details] ([id])
GO
ALTER TABLE [dbo].[item_commissions] CHECK CONSTRAINT [item_commissions_ibfk_2]
GO
ALTER TABLE [dbo].[item_commissions]  WITH CHECK ADD  CONSTRAINT [item_commissions_ibfk_3] FOREIGN KEY([item_id])
REFERENCES [dbo].[items] ([id])
GO
ALTER TABLE [dbo].[item_commissions] CHECK CONSTRAINT [item_commissions_ibfk_3]
GO
ALTER TABLE [dbo].[item_commissions]  WITH CHECK ADD  CONSTRAINT [item_commissions_ibfk_4] FOREIGN KEY([sub_item_id])
REFERENCES [dbo].[items] ([id])
GO
ALTER TABLE [dbo].[item_commissions] CHECK CONSTRAINT [item_commissions_ibfk_4]
GO
ALTER TABLE [dbo].[item_commissions]  WITH CHECK ADD  CONSTRAINT [item_commissions_ibfk_5] FOREIGN KEY([expense_id])
REFERENCES [dbo].[expenses] ([id])
GO
ALTER TABLE [dbo].[item_commissions] CHECK CONSTRAINT [item_commissions_ibfk_5]
GO
ALTER TABLE [dbo].[item_commissions]  WITH CHECK ADD  CONSTRAINT [item_commissions_ibfk_6] FOREIGN KEY([time_logs_id])
REFERENCES [dbo].[user_activity_logs] ([id])
GO
ALTER TABLE [dbo].[item_commissions] CHECK CONSTRAINT [item_commissions_ibfk_6]
GO
ALTER TABLE [dbo].[item_commissions]  WITH CHECK ADD  CONSTRAINT [item_commissions_ibfk_7] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[item_commissions] CHECK CONSTRAINT [item_commissions_ibfk_7]
GO
ALTER TABLE [dbo].[items]  WITH CHECK ADD  CONSTRAINT [items_headers_ibfk_1] FOREIGN KEY([item_id])
REFERENCES [dbo].[items] ([id])
GO
ALTER TABLE [dbo].[items] CHECK CONSTRAINT [items_headers_ibfk_1]
GO
ALTER TABLE [dbo].[items]  WITH CHECK ADD  CONSTRAINT [items_headers_ibfk_2] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[items] CHECK CONSTRAINT [items_headers_ibfk_2]
GO
ALTER TABLE [dbo].[items]  WITH CHECK ADD  CONSTRAINT [items_headers_ibfk_3] FOREIGN KEY([tax_id])
REFERENCES [dbo].[taxes] ([id])
GO
ALTER TABLE [dbo].[items] CHECK CONSTRAINT [items_headers_ibfk_3]
GO
ALTER TABLE [dbo].[legal_case_archived_hard_copies]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_archived_hard_copies_case_document_classificati1] FOREIGN KEY([case_document_classification_id])
REFERENCES [dbo].[case_document_classifications] ([id])
GO
ALTER TABLE [dbo].[legal_case_archived_hard_copies] CHECK CONSTRAINT [fk_legal_case_archived_hard_copies_case_document_classificati1]
GO
ALTER TABLE [dbo].[legal_case_archived_hard_copies]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_archived_hard_copies_case_document_classificati2] FOREIGN KEY([sub_case_document_classification_id])
REFERENCES [dbo].[case_document_classifications] ([id])
GO
ALTER TABLE [dbo].[legal_case_archived_hard_copies] CHECK CONSTRAINT [fk_legal_case_archived_hard_copies_case_document_classificati2]
GO
ALTER TABLE [dbo].[legal_case_archived_hard_copies]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_archived_hard_copies_legal_cases1] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[legal_case_archived_hard_copies] CHECK CONSTRAINT [fk_legal_case_archived_hard_copies_legal_cases1]
GO
ALTER TABLE [dbo].[legal_case_changes]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_changes_legal_cases1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[legal_case_changes] CHECK CONSTRAINT [fk_legal_case_changes_legal_cases1]
GO
ALTER TABLE [dbo].[legal_case_client_position_languages]  WITH CHECK ADD  CONSTRAINT [legal_case_client_position_languages_ibfk_1] FOREIGN KEY([legal_case_client_position_id])
REFERENCES [dbo].[legal_case_client_positions] ([id])
GO
ALTER TABLE [dbo].[legal_case_client_position_languages] CHECK CONSTRAINT [legal_case_client_position_languages_ibfk_1]
GO
ALTER TABLE [dbo].[legal_case_client_position_languages]  WITH CHECK ADD  CONSTRAINT [legal_case_client_position_languages_ibfk_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[legal_case_client_position_languages] CHECK CONSTRAINT [legal_case_client_position_languages_ibfk_2]
GO
ALTER TABLE [dbo].[legal_case_commissions]  WITH CHECK ADD  CONSTRAINT [legal_case_commissions_ibfk_1] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[legal_case_commissions] CHECK CONSTRAINT [legal_case_commissions_ibfk_1]
GO
ALTER TABLE [dbo].[legal_case_commissions]  WITH CHECK ADD  CONSTRAINT [legal_case_commissions_ibfk_2] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[legal_case_commissions] CHECK CONSTRAINT [legal_case_commissions_ibfk_2]
GO
ALTER TABLE [dbo].[legal_case_container_advanced_export_slots]  WITH CHECK ADD  CONSTRAINT [legal_case_container_advanced_export_slots_ibfk_1] FOREIGN KEY([legal_case_container_id])
REFERENCES [dbo].[legal_case_containers] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[legal_case_container_advanced_export_slots] CHECK CONSTRAINT [legal_case_container_advanced_export_slots_ibfk_1]
GO
ALTER TABLE [dbo].[legal_case_container_documents]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_container_documents_legal_case_containers1] FOREIGN KEY([legal_case_container_id])
REFERENCES [dbo].[legal_case_containers] ([id])
GO
ALTER TABLE [dbo].[legal_case_container_documents] CHECK CONSTRAINT [fk_legal_case_container_documents_legal_case_containers1]
GO
ALTER TABLE [dbo].[legal_case_container_documents]  WITH CHECK ADD  CONSTRAINT [legal_case_container_documents_ibfk_1] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_container_documents] CHECK CONSTRAINT [legal_case_container_documents_ibfk_1]
GO
ALTER TABLE [dbo].[legal_case_container_documents]  WITH CHECK ADD  CONSTRAINT [legal_case_container_documents_ibfk_2] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_container_documents] CHECK CONSTRAINT [legal_case_container_documents_ibfk_2]
GO
ALTER TABLE [dbo].[legal_case_container_documents]  WITH CHECK ADD  CONSTRAINT [legal_case_container_documents_ibfk_3] FOREIGN KEY([legal_case_container_document_status_id])
REFERENCES [dbo].[legal_case_container_document_statuses] ([id])
GO
ALTER TABLE [dbo].[legal_case_container_documents] CHECK CONSTRAINT [legal_case_container_documents_ibfk_3]
GO
ALTER TABLE [dbo].[legal_case_container_documents]  WITH CHECK ADD  CONSTRAINT [legal_case_container_documents_ibfk_4] FOREIGN KEY([legal_case_container_document_type_id])
REFERENCES [dbo].[legal_case_container_document_types] ([id])
GO
ALTER TABLE [dbo].[legal_case_container_documents] CHECK CONSTRAINT [legal_case_container_documents_ibfk_4]
GO
ALTER TABLE [dbo].[legal_case_container_opponents]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_container_opponents1] FOREIGN KEY([case_container_id])
REFERENCES [dbo].[legal_case_containers] ([id])
GO
ALTER TABLE [dbo].[legal_case_container_opponents] CHECK CONSTRAINT [fk_legal_case_container_opponents1]
GO
ALTER TABLE [dbo].[legal_case_container_opponents]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_container_opponents2] FOREIGN KEY([opponent_id])
REFERENCES [dbo].[opponents] ([id])
GO
ALTER TABLE [dbo].[legal_case_container_opponents] CHECK CONSTRAINT [fk_legal_case_container_opponents2]
GO
ALTER TABLE [dbo].[legal_case_container_opponents]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_container_opponents3] FOREIGN KEY([opponent_position])
REFERENCES [dbo].[legal_case_opponent_positions] ([id])
GO
ALTER TABLE [dbo].[legal_case_container_opponents] CHECK CONSTRAINT [fk_legal_case_container_opponents3]
GO
ALTER TABLE [dbo].[legal_case_containers]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_containers1] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_containers] CHECK CONSTRAINT [fk_legal_case_containers1]
GO
ALTER TABLE [dbo].[legal_case_containers]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_containers2] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_containers] CHECK CONSTRAINT [fk_legal_case_containers2]
GO
ALTER TABLE [dbo].[legal_case_containers]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_containers3] FOREIGN KEY([case_type_id])
REFERENCES [dbo].[case_types] ([id])
GO
ALTER TABLE [dbo].[legal_case_containers] CHECK CONSTRAINT [fk_legal_case_containers3]
GO
ALTER TABLE [dbo].[legal_case_containers]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_containers4] FOREIGN KEY([provider_group_id])
REFERENCES [dbo].[provider_groups] ([id])
GO
ALTER TABLE [dbo].[legal_case_containers] CHECK CONSTRAINT [fk_legal_case_containers4]
GO
ALTER TABLE [dbo].[legal_case_containers]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_containers5] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_containers] CHECK CONSTRAINT [fk_legal_case_containers5]
GO
ALTER TABLE [dbo].[legal_case_documents]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_documents_legal_cases1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[legal_case_documents] CHECK CONSTRAINT [fk_legal_case_documents_legal_cases1]
GO
ALTER TABLE [dbo].[legal_case_documents]  WITH CHECK ADD  CONSTRAINT [legal_case_documents_ibfk_1] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_documents] CHECK CONSTRAINT [legal_case_documents_ibfk_1]
GO
ALTER TABLE [dbo].[legal_case_documents]  WITH CHECK ADD  CONSTRAINT [legal_case_documents_ibfk_2] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_documents] CHECK CONSTRAINT [legal_case_documents_ibfk_2]
GO
ALTER TABLE [dbo].[legal_case_documents]  WITH CHECK ADD  CONSTRAINT [legal_case_documents_ibfk_3] FOREIGN KEY([legal_case_document_status_id])
REFERENCES [dbo].[case_document_statuses] ([id])
GO
ALTER TABLE [dbo].[legal_case_documents] CHECK CONSTRAINT [legal_case_documents_ibfk_3]
GO
ALTER TABLE [dbo].[legal_case_documents]  WITH CHECK ADD  CONSTRAINT [legal_case_documents_ibfk_4] FOREIGN KEY([legal_case_document_type_id])
REFERENCES [dbo].[case_document_types] ([id])
GO
ALTER TABLE [dbo].[legal_case_documents] CHECK CONSTRAINT [legal_case_documents_ibfk_4]
GO
ALTER TABLE [dbo].[legal_case_event_data_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_event_data_types_languages_1] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[legal_case_event_data_types_languages] CHECK CONSTRAINT [fk_legal_case_event_data_types_languages_1]
GO
ALTER TABLE [dbo].[legal_case_event_data_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_event_data_types_languages_2] FOREIGN KEY([type])
REFERENCES [dbo].[legal_case_event_data_types] ([id])
GO
ALTER TABLE [dbo].[legal_case_event_data_types_languages] CHECK CONSTRAINT [fk_legal_case_event_data_types_languages_2]
GO
ALTER TABLE [dbo].[legal_case_event_type_forms]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_event_type_forms_1] FOREIGN KEY([event_type])
REFERENCES [dbo].[legal_case_event_types] ([id])
GO
ALTER TABLE [dbo].[legal_case_event_type_forms] CHECK CONSTRAINT [fk_legal_case_event_type_forms_1]
GO
ALTER TABLE [dbo].[legal_case_event_type_forms]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_event_type_forms_2] FOREIGN KEY([field_type])
REFERENCES [dbo].[legal_case_event_data_types] ([id])
GO
ALTER TABLE [dbo].[legal_case_event_type_forms] CHECK CONSTRAINT [fk_legal_case_event_type_forms_2]
GO
ALTER TABLE [dbo].[legal_case_event_type_forms_languages]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_event_type_forms_languages_1] FOREIGN KEY([field])
REFERENCES [dbo].[legal_case_event_type_forms] ([id])
GO
ALTER TABLE [dbo].[legal_case_event_type_forms_languages] CHECK CONSTRAINT [fk_legal_case_event_type_forms_languages_1]
GO
ALTER TABLE [dbo].[legal_case_event_type_forms_languages]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_event_type_forms_languages_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[legal_case_event_type_forms_languages] CHECK CONSTRAINT [fk_legal_case_event_type_forms_languages_2]
GO
ALTER TABLE [dbo].[legal_case_event_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_event_types_languages_1] FOREIGN KEY([event_type])
REFERENCES [dbo].[legal_case_event_types] ([id])
GO
ALTER TABLE [dbo].[legal_case_event_types_languages] CHECK CONSTRAINT [fk_legal_case_event_types_languages_1]
GO
ALTER TABLE [dbo].[legal_case_event_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_event_types_languages_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[legal_case_event_types_languages] CHECK CONSTRAINT [fk_legal_case_event_types_languages_2]
GO
ALTER TABLE [dbo].[legal_case_events]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_events_1] FOREIGN KEY([legal_case])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[legal_case_events] CHECK CONSTRAINT [fk_legal_case_events_1]
GO
ALTER TABLE [dbo].[legal_case_events]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_events_2] FOREIGN KEY([event_type])
REFERENCES [dbo].[legal_case_event_types] ([id])
GO
ALTER TABLE [dbo].[legal_case_events] CHECK CONSTRAINT [fk_legal_case_events_2]
GO
ALTER TABLE [dbo].[legal_case_events]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_events_3] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_events] CHECK CONSTRAINT [fk_legal_case_events_3]
GO
ALTER TABLE [dbo].[legal_case_events]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_events_4] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_events] CHECK CONSTRAINT [fk_legal_case_events_4]
GO
ALTER TABLE [dbo].[legal_case_events]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_events_5] FOREIGN KEY([stage])
REFERENCES [dbo].[legal_case_litigation_details] ([id])
GO
ALTER TABLE [dbo].[legal_case_events] CHECK CONSTRAINT [fk_legal_case_events_5]
GO
ALTER TABLE [dbo].[legal_case_events_related_data]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_events_related_data_1] FOREIGN KEY([event])
REFERENCES [dbo].[legal_case_events] ([id])
GO
ALTER TABLE [dbo].[legal_case_events_related_data] CHECK CONSTRAINT [fk_legal_case_events_related_data_1]
GO
ALTER TABLE [dbo].[legal_case_hearing_client_report_history]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_hearing_client_report_history_1] FOREIGN KEY([legal_case_hearing_id])
REFERENCES [dbo].[legal_case_hearings] ([id])
GO
ALTER TABLE [dbo].[legal_case_hearing_client_report_history] CHECK CONSTRAINT [fk_legal_case_hearing_client_report_history_1]
GO
ALTER TABLE [dbo].[legal_case_hearing_client_report_history]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_hearing_client_report_history_2] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_hearing_client_report_history] CHECK CONSTRAINT [fk_legal_case_hearing_client_report_history_2]
GO
ALTER TABLE [dbo].[legal_case_hearing_client_report_history]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_hearing_client_report_history_3] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_hearing_client_report_history] CHECK CONSTRAINT [fk_legal_case_hearing_client_report_history_3]
GO
ALTER TABLE [dbo].[legal_case_hearings]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_hearings_3] FOREIGN KEY([type])
REFERENCES [dbo].[hearing_types] ([id])
GO
ALTER TABLE [dbo].[legal_case_hearings] CHECK CONSTRAINT [fk_legal_case_hearings_3]
GO
ALTER TABLE [dbo].[legal_case_hearings]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_hearings_4] FOREIGN KEY([stage])
REFERENCES [dbo].[legal_case_litigation_details] ([id])
GO
ALTER TABLE [dbo].[legal_case_hearings] CHECK CONSTRAINT [fk_legal_case_hearings_4]
GO
ALTER TABLE [dbo].[legal_case_hearings]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_hearings_5] FOREIGN KEY([reason_of_win_or_lose])
REFERENCES [dbo].[hearing_outcome_reasons] ([id])
GO
ALTER TABLE [dbo].[legal_case_hearings] CHECK CONSTRAINT [fk_legal_case_hearings_5]
GO
ALTER TABLE [dbo].[legal_case_hearings]  WITH CHECK ADD  CONSTRAINT [legal_case_hearings_ibfk_1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[legal_case_hearings] CHECK CONSTRAINT [legal_case_hearings_ibfk_1]
GO
ALTER TABLE [dbo].[legal_case_hearings]  WITH CHECK ADD  CONSTRAINT [legal_case_hearings_ibfk_2] FOREIGN KEY([task_id])
REFERENCES [dbo].[events] ([id])
GO
ALTER TABLE [dbo].[legal_case_hearings] CHECK CONSTRAINT [legal_case_hearings_ibfk_2]
GO
ALTER TABLE [dbo].[legal_case_hearings_users]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_hearings_users1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_hearings_users] CHECK CONSTRAINT [fk_legal_case_hearings_users1]
GO
ALTER TABLE [dbo].[legal_case_hearings_users]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_hearings_users2] FOREIGN KEY([legal_case_hearing_id])
REFERENCES [dbo].[legal_case_hearings] ([id])
GO
ALTER TABLE [dbo].[legal_case_hearings_users] CHECK CONSTRAINT [fk_legal_case_hearings_users2]
GO
ALTER TABLE [dbo].[legal_case_litigation_details]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_litigation_details_6] FOREIGN KEY([legal_case_stage])
REFERENCES [dbo].[legal_case_stages] ([id])
GO
ALTER TABLE [dbo].[legal_case_litigation_details] CHECK CONSTRAINT [fk_legal_case_litigation_details_6]
GO
ALTER TABLE [dbo].[legal_case_litigation_details]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_litigation_details_7] FOREIGN KEY([client_position])
REFERENCES [dbo].[legal_case_client_positions] ([id])
GO
ALTER TABLE [dbo].[legal_case_litigation_details] CHECK CONSTRAINT [fk_legal_case_litigation_details_7]
GO
ALTER TABLE [dbo].[legal_case_litigation_details]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_litigation_details_8] FOREIGN KEY([status])
REFERENCES [dbo].[stage_statuses] ([id])
GO
ALTER TABLE [dbo].[legal_case_litigation_details] CHECK CONSTRAINT [fk_legal_case_litigation_details_8]
GO
ALTER TABLE [dbo].[legal_case_litigation_details]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_litigation_details1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[legal_case_litigation_details] CHECK CONSTRAINT [fk_legal_case_litigation_details1]
GO
ALTER TABLE [dbo].[legal_case_litigation_details]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_litigation_details2] FOREIGN KEY([court_id])
REFERENCES [dbo].[courts] ([id])
GO
ALTER TABLE [dbo].[legal_case_litigation_details] CHECK CONSTRAINT [fk_legal_case_litigation_details2]
GO
ALTER TABLE [dbo].[legal_case_litigation_details]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_litigation_details3] FOREIGN KEY([court_type_id])
REFERENCES [dbo].[court_types] ([id])
GO
ALTER TABLE [dbo].[legal_case_litigation_details] CHECK CONSTRAINT [fk_legal_case_litigation_details3]
GO
ALTER TABLE [dbo].[legal_case_litigation_details]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_litigation_details4] FOREIGN KEY([court_degree_id])
REFERENCES [dbo].[court_degrees] ([id])
GO
ALTER TABLE [dbo].[legal_case_litigation_details] CHECK CONSTRAINT [fk_legal_case_litigation_details4]
GO
ALTER TABLE [dbo].[legal_case_litigation_details]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_litigation_details5] FOREIGN KEY([court_region_id])
REFERENCES [dbo].[court_regions] ([id])
GO
ALTER TABLE [dbo].[legal_case_litigation_details] CHECK CONSTRAINT [fk_legal_case_litigation_details5]
GO
ALTER TABLE [dbo].[legal_case_litigation_external_references]  WITH CHECK ADD  CONSTRAINT [fk_litigation_external_references_1] FOREIGN KEY([stage])
REFERENCES [dbo].[legal_case_litigation_details] ([id])
GO
ALTER TABLE [dbo].[legal_case_litigation_external_references] CHECK CONSTRAINT [fk_litigation_external_references_1]
GO
ALTER TABLE [dbo].[legal_case_litigation_stages_opponents]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_litigation_stages_opponents_1] FOREIGN KEY([stage])
REFERENCES [dbo].[legal_case_litigation_details] ([id])
GO
ALTER TABLE [dbo].[legal_case_litigation_stages_opponents] CHECK CONSTRAINT [fk_legal_case_litigation_stages_opponents_1]
GO
ALTER TABLE [dbo].[legal_case_litigation_stages_opponents]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_litigation_stages_opponents_2] FOREIGN KEY([opponent_id])
REFERENCES [dbo].[opponents] ([id])
GO
ALTER TABLE [dbo].[legal_case_litigation_stages_opponents] CHECK CONSTRAINT [fk_legal_case_litigation_stages_opponents_2]
GO
ALTER TABLE [dbo].[legal_case_litigation_stages_opponents]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_litigation_stages_opponents_3] FOREIGN KEY([opponent_position])
REFERENCES [dbo].[legal_case_opponent_positions] ([id])
GO
ALTER TABLE [dbo].[legal_case_litigation_stages_opponents] CHECK CONSTRAINT [fk_legal_case_litigation_stages_opponents_3]
GO
ALTER TABLE [dbo].[legal_case_opponent_position_languages]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_opponent_position_languages_1] FOREIGN KEY([legal_case_opponent_position_id])
REFERENCES [dbo].[legal_case_opponent_positions] ([id])
GO
ALTER TABLE [dbo].[legal_case_opponent_position_languages] CHECK CONSTRAINT [fk_legal_case_opponent_position_languages_1]
GO
ALTER TABLE [dbo].[legal_case_opponent_position_languages]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_opponent_position_languages_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[legal_case_opponent_position_languages] CHECK CONSTRAINT [fk_legal_case_opponent_position_languages_2]
GO
ALTER TABLE [dbo].[legal_case_opponents]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_opponents1] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[legal_case_opponents] CHECK CONSTRAINT [fk_legal_case_opponents1]
GO
ALTER TABLE [dbo].[legal_case_opponents]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_opponents2] FOREIGN KEY([opponent_id])
REFERENCES [dbo].[opponents] ([id])
GO
ALTER TABLE [dbo].[legal_case_opponents] CHECK CONSTRAINT [fk_legal_case_opponents2]
GO
ALTER TABLE [dbo].[legal_case_opponents]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_opponents3] FOREIGN KEY([opponent_position])
REFERENCES [dbo].[legal_case_opponent_positions] ([id])
GO
ALTER TABLE [dbo].[legal_case_opponents] CHECK CONSTRAINT [fk_legal_case_opponents3]
GO
ALTER TABLE [dbo].[legal_case_outsource_contacts]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_outsource_contacts_1] FOREIGN KEY([legal_case_outsource_id])
REFERENCES [dbo].[legal_case_outsources] ([id])
GO
ALTER TABLE [dbo].[legal_case_outsource_contacts] CHECK CONSTRAINT [fk_legal_case_outsource_contacts_1]
GO
ALTER TABLE [dbo].[legal_case_outsource_contacts]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_outsource_contacts_2] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[legal_case_outsource_contacts] CHECK CONSTRAINT [fk_legal_case_outsource_contacts_2]
GO
ALTER TABLE [dbo].[legal_case_outsources]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_outsources_1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[legal_case_outsources] CHECK CONSTRAINT [fk_legal_case_outsources_1]
GO
ALTER TABLE [dbo].[legal_case_outsources]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_outsources_2] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[legal_case_outsources] CHECK CONSTRAINT [fk_legal_case_outsources_2]
GO
ALTER TABLE [dbo].[legal_case_partner_shares]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_partner_shares_1] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[legal_case_partner_shares] CHECK CONSTRAINT [fk_legal_case_partner_shares_1]
GO
ALTER TABLE [dbo].[legal_case_partner_shares]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_partner_shares_2] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[legal_case_partner_shares] CHECK CONSTRAINT [fk_legal_case_partner_shares_2]
GO
ALTER TABLE [dbo].[legal_case_risks]  WITH CHECK ADD  CONSTRAINT [FK_legal_case_risks_actor] FOREIGN KEY([responsible_actor_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_risks] CHECK CONSTRAINT [FK_legal_case_risks_actor]
GO
ALTER TABLE [dbo].[legal_case_risks]  WITH CHECK ADD  CONSTRAINT [FK_legal_case_risks_case] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[legal_case_risks] CHECK CONSTRAINT [FK_legal_case_risks_case]
GO
ALTER TABLE [dbo].[legal_case_risks]  WITH CHECK ADD  CONSTRAINT [FK_legal_case_risks_user] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_risks] CHECK CONSTRAINT [FK_legal_case_risks_user]
GO
ALTER TABLE [dbo].[legal_case_stage_changes]  WITH CHECK ADD  CONSTRAINT [legal_case_stage_changes_ibfk_1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[legal_case_stage_changes] CHECK CONSTRAINT [legal_case_stage_changes_ibfk_1]
GO
ALTER TABLE [dbo].[legal_case_stage_changes]  WITH CHECK ADD  CONSTRAINT [legal_case_stage_changes_ibfk_2] FOREIGN KEY([legal_case_stage_id])
REFERENCES [dbo].[legal_case_stages] ([id])
GO
ALTER TABLE [dbo].[legal_case_stage_changes] CHECK CONSTRAINT [legal_case_stage_changes_ibfk_2]
GO
ALTER TABLE [dbo].[legal_case_stage_contacts]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_stage_contacts_1] FOREIGN KEY([stage])
REFERENCES [dbo].[legal_case_litigation_details] ([id])
GO
ALTER TABLE [dbo].[legal_case_stage_contacts] CHECK CONSTRAINT [fk_legal_case_stage_contacts_1]
GO
ALTER TABLE [dbo].[legal_case_stage_contacts]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_stage_contacts_2] FOREIGN KEY([contact])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[legal_case_stage_contacts] CHECK CONSTRAINT [fk_legal_case_stage_contacts_2]
GO
ALTER TABLE [dbo].[legal_case_stage_contacts]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_stage_contacts_3] FOREIGN KEY([contact_role])
REFERENCES [dbo].[legal_case_contact_roles] ([id])
GO
ALTER TABLE [dbo].[legal_case_stage_contacts] CHECK CONSTRAINT [fk_legal_case_stage_contacts_3]
GO
ALTER TABLE [dbo].[legal_case_stage_contacts]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_stage_contacts_4] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_stage_contacts] CHECK CONSTRAINT [fk_legal_case_stage_contacts_4]
GO
ALTER TABLE [dbo].[legal_case_stage_contacts]  WITH CHECK ADD  CONSTRAINT [fk_legal_case_stage_contacts_5] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_case_stage_contacts] CHECK CONSTRAINT [fk_legal_case_stage_contacts_5]
GO
ALTER TABLE [dbo].[legal_case_stage_languages]  WITH CHECK ADD  CONSTRAINT [legal_case_stage_languages_ibfk_1] FOREIGN KEY([legal_case_stage_id])
REFERENCES [dbo].[legal_case_stages] ([id])
GO
ALTER TABLE [dbo].[legal_case_stage_languages] CHECK CONSTRAINT [legal_case_stage_languages_ibfk_1]
GO
ALTER TABLE [dbo].[legal_case_stage_languages]  WITH CHECK ADD  CONSTRAINT [legal_case_stage_languages_ibfk_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[legal_case_stage_languages] CHECK CONSTRAINT [legal_case_stage_languages_ibfk_2]
GO
ALTER TABLE [dbo].[legal_case_success_probability_languages]  WITH CHECK ADD  CONSTRAINT [legal_case_success_probability_languages_ibfk_1] FOREIGN KEY([legal_case_success_probability_id])
REFERENCES [dbo].[legal_case_success_probabilities] ([id])
GO
ALTER TABLE [dbo].[legal_case_success_probability_languages] CHECK CONSTRAINT [legal_case_success_probability_languages_ibfk_1]
GO
ALTER TABLE [dbo].[legal_case_success_probability_languages]  WITH CHECK ADD  CONSTRAINT [legal_case_success_probability_languages_ibfk_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[legal_case_success_probability_languages] CHECK CONSTRAINT [legal_case_success_probability_languages_ibfk_2]
GO
ALTER TABLE [dbo].[legal_case_users]  WITH CHECK ADD  CONSTRAINT [legal_case_users_ibfk_1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[legal_case_users] CHECK CONSTRAINT [legal_case_users_ibfk_1]
GO
ALTER TABLE [dbo].[legal_case_users]  WITH CHECK ADD  CONSTRAINT [legal_case_users_ibfk_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[legal_case_users] CHECK CONSTRAINT [legal_case_users_ibfk_2]
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD FOREIGN KEY([legal_case_stage_id])
REFERENCES [dbo].[legal_case_stages] ([id])
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD FOREIGN KEY([legal_case_client_position_id])
REFERENCES [dbo].[legal_case_client_positions] ([id])
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD FOREIGN KEY([legal_case_success_probability_id])
REFERENCES [dbo].[legal_case_success_probabilities] ([id])
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD FOREIGN KEY([legal_case_stage_id])
REFERENCES [dbo].[legal_case_stages] ([id])
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD FOREIGN KEY([legal_case_client_position_id])
REFERENCES [dbo].[legal_case_client_positions] ([id])
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD FOREIGN KEY([legal_case_success_probability_id])
REFERENCES [dbo].[legal_case_success_probabilities] ([id])
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_10] FOREIGN KEY([workflow])
REFERENCES [dbo].[workflows] ([id])
GO
ALTER TABLE [dbo].[legal_cases] CHECK CONSTRAINT [fk_legal_cases_10]
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_9] FOREIGN KEY([stage])
REFERENCES [dbo].[legal_case_litigation_details] ([id])
GO
ALTER TABLE [dbo].[legal_cases] CHECK CONSTRAINT [fk_legal_cases_9]
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_case_types1] FOREIGN KEY([case_type_id])
REFERENCES [dbo].[case_types] ([id])
GO
ALTER TABLE [dbo].[legal_cases] CHECK CONSTRAINT [fk_legal_cases_case_types1]
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD  CONSTRAINT [FK_legal_cases_closed_by] FOREIGN KEY([closed_by])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_cases] CHECK CONSTRAINT [FK_legal_cases_closed_by]
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD  CONSTRAINT [FK_legal_cases_closure_requested_by] FOREIGN KEY([closure_requested_by])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_cases] CHECK CONSTRAINT [FK_legal_cases_closure_requested_by]
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_contacts1] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[legal_cases] CHECK CONSTRAINT [fk_legal_cases_contacts1]
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD  CONSTRAINT [FK_legal_cases_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_cases] CHECK CONSTRAINT [FK_legal_cases_createdBy]
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD  CONSTRAINT [FK_legal_cases_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_cases] CHECK CONSTRAINT [FK_legal_cases_modifiedBy]
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_provider_groups1] FOREIGN KEY([provider_group_id])
REFERENCES [dbo].[provider_groups] ([id])
GO
ALTER TABLE [dbo].[legal_cases] CHECK CONSTRAINT [fk_legal_cases_provider_groups1]
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD  CONSTRAINT [FK_legal_cases_user_id] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_cases] CHECK CONSTRAINT [FK_legal_cases_user_id]
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_users1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_cases] CHECK CONSTRAINT [fk_legal_cases_users1]
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_workflow_status1] FOREIGN KEY([case_status_id])
REFERENCES [dbo].[workflow_status] ([id])
GO
ALTER TABLE [dbo].[legal_cases] CHECK CONSTRAINT [fk_legal_cases_workflow_status1]
GO
ALTER TABLE [dbo].[legal_cases_companies]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_companies_companies1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[legal_cases_companies] CHECK CONSTRAINT [fk_legal_cases_companies_companies1]
GO
ALTER TABLE [dbo].[legal_cases_companies]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_companies_legal_case_company_roles1] FOREIGN KEY([legal_case_company_role_id])
REFERENCES [dbo].[legal_case_company_roles] ([id])
GO
ALTER TABLE [dbo].[legal_cases_companies] CHECK CONSTRAINT [fk_legal_cases_companies_legal_case_company_roles1]
GO
ALTER TABLE [dbo].[legal_cases_companies]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_companies_legal_cases1] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[legal_cases_companies] CHECK CONSTRAINT [fk_legal_cases_companies_legal_cases1]
GO
ALTER TABLE [dbo].[legal_cases_contacts]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_contacts_contacts1] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[legal_cases_contacts] CHECK CONSTRAINT [fk_legal_cases_contacts_contacts1]
GO
ALTER TABLE [dbo].[legal_cases_contacts]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_contacts_legal_case_contact_roles1] FOREIGN KEY([legal_case_contact_role_id])
REFERENCES [dbo].[legal_case_contact_roles] ([id])
GO
ALTER TABLE [dbo].[legal_cases_contacts] CHECK CONSTRAINT [fk_legal_cases_contacts_legal_case_contact_roles1]
GO
ALTER TABLE [dbo].[legal_cases_contacts]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_contacts_legal_cases1] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[legal_cases_contacts] CHECK CONSTRAINT [fk_legal_cases_contacts_legal_cases1]
GO
ALTER TABLE [dbo].[legal_cases_countries_renewals]  WITH CHECK ADD  CONSTRAINT [legal_cases_countries_renewals_ibfk_1] FOREIGN KEY([intellectual_property_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[legal_cases_countries_renewals] CHECK CONSTRAINT [legal_cases_countries_renewals_ibfk_1]
GO
ALTER TABLE [dbo].[legal_cases_countries_renewals_users]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_countries_renewals_users_1] FOREIGN KEY([legal_case_country_renewal_id])
REFERENCES [dbo].[legal_cases_countries_renewals] ([id])
GO
ALTER TABLE [dbo].[legal_cases_countries_renewals_users] CHECK CONSTRAINT [fk_legal_cases_countries_renewals_users_1]
GO
ALTER TABLE [dbo].[legal_cases_countries_renewals_users]  WITH CHECK ADD  CONSTRAINT [fk_legal_cases_countries_renewals_users_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[legal_cases_countries_renewals_users] CHECK CONSTRAINT [fk_legal_cases_countries_renewals_users_2]
GO
ALTER TABLE [dbo].[license_and_waiver_reminds]  WITH CHECK ADD  CONSTRAINT [fk_license_and_waiver_reminds_company_discharge1] FOREIGN KEY([license_and_waiver_id])
REFERENCES [dbo].[company_discharge_social_securities] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[license_and_waiver_reminds] CHECK CONSTRAINT [fk_license_and_waiver_reminds_company_discharge1]
GO
ALTER TABLE [dbo].[license_and_waiver_reminds]  WITH CHECK ADD  CONSTRAINT [fk_license_and_waiver_reminds_reminders1] FOREIGN KEY([reminder_id])
REFERENCES [dbo].[reminders] ([id])
GO
ALTER TABLE [dbo].[license_and_waiver_reminds] CHECK CONSTRAINT [fk_license_and_waiver_reminds_reminders1]
GO
ALTER TABLE [dbo].[license_and_waiver_reminds]  WITH CHECK ADD  CONSTRAINT [fk_license_and_waiver_reminds_user_groups1] FOREIGN KEY([user_group_id])
REFERENCES [dbo].[user_groups] ([id])
GO
ALTER TABLE [dbo].[license_and_waiver_reminds] CHECK CONSTRAINT [fk_license_and_waiver_reminds_user_groups1]
GO
ALTER TABLE [dbo].[license_and_waiver_reminds]  WITH CHECK ADD  CONSTRAINT [fk_license_and_waiver_reminds_users1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[license_and_waiver_reminds] CHECK CONSTRAINT [fk_license_and_waiver_reminds_users1]
GO
ALTER TABLE [dbo].[litigation_stage_status_history]  WITH CHECK ADD  CONSTRAINT [fk_litigation_stage_status_history_1] FOREIGN KEY([litigation_stage])
REFERENCES [dbo].[legal_case_litigation_details] ([id])
GO
ALTER TABLE [dbo].[litigation_stage_status_history] CHECK CONSTRAINT [fk_litigation_stage_status_history_1]
GO
ALTER TABLE [dbo].[litigation_stage_status_history]  WITH CHECK ADD  CONSTRAINT [fk_litigation_stage_status_history_2] FOREIGN KEY([status])
REFERENCES [dbo].[stage_statuses] ([id])
GO
ALTER TABLE [dbo].[litigation_stage_status_history] CHECK CONSTRAINT [fk_litigation_stage_status_history_2]
GO
ALTER TABLE [dbo].[litigation_stage_status_history]  WITH CHECK ADD  CONSTRAINT [fk_litigation_stage_status_history_3] FOREIGN KEY([action_maker])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[litigation_stage_status_history] CHECK CONSTRAINT [fk_litigation_stage_status_history_3]
GO
ALTER TABLE [dbo].[login_history_log_archives]  WITH CHECK ADD FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[login_history_log_archives]  WITH CHECK ADD FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[login_history_logs]  WITH CHECK ADD FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[login_history_logs]  WITH CHECK ADD FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[lookup_members]  WITH CHECK ADD  CONSTRAINT [fk_lookup_members_companies1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[lookup_members] CHECK CONSTRAINT [fk_lookup_members_companies1]
GO
ALTER TABLE [dbo].[lookup_members]  WITH CHECK ADD  CONSTRAINT [fk_lookup_members_contacts1] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[lookup_members] CHECK CONSTRAINT [fk_lookup_members_contacts1]
GO
ALTER TABLE [dbo].[model_has_permissions]  WITH CHECK ADD  CONSTRAINT [model_has_permissions_permission_id_foreign] FOREIGN KEY([permission_id])
REFERENCES [dbo].[permissions] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[model_has_permissions] CHECK CONSTRAINT [model_has_permissions_permission_id_foreign]
GO
ALTER TABLE [dbo].[model_has_roles]  WITH CHECK ADD  CONSTRAINT [model_has_roles_role_id_foreign] FOREIGN KEY([role_id])
REFERENCES [dbo].[roles] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[model_has_roles] CHECK CONSTRAINT [model_has_roles_role_id_foreign]
GO
ALTER TABLE [dbo].[money_dashboard_widgets]  WITH CHECK ADD  CONSTRAINT [fk_money_dashboard_widgets_1] FOREIGN KEY([money_dashboard_id])
REFERENCES [dbo].[money_dashboards] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[money_dashboard_widgets] CHECK CONSTRAINT [fk_money_dashboard_widgets_1]
GO
ALTER TABLE [dbo].[money_dashboard_widgets]  WITH CHECK ADD  CONSTRAINT [fk_money_dashboard_widgets_2] FOREIGN KEY([money_dashboard_widgets_type_id])
REFERENCES [dbo].[money_dashboard_widgets_types] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[money_dashboard_widgets] CHECK CONSTRAINT [fk_money_dashboard_widgets_2]
GO
ALTER TABLE [dbo].[money_dashboard_widgets_title_languages]  WITH CHECK ADD FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[money_dashboard_widgets_title_languages]  WITH CHECK ADD FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[money_dashboard_widgets_title_languages]  WITH CHECK ADD FOREIGN KEY([widget_id])
REFERENCES [dbo].[money_dashboard_widgets] ([id])
GO
ALTER TABLE [dbo].[money_dashboard_widgets_title_languages]  WITH CHECK ADD FOREIGN KEY([widget_id])
REFERENCES [dbo].[money_dashboard_widgets] ([id])
GO
ALTER TABLE [dbo].[notifications]  WITH CHECK ADD  CONSTRAINT [notifications_users_ibfk_1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[notifications] CHECK CONSTRAINT [notifications_users_ibfk_1]
GO
ALTER TABLE [dbo].[notifications]  WITH CHECK ADD  CONSTRAINT [notifications_users_ibfk_2] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[notifications] CHECK CONSTRAINT [notifications_users_ibfk_2]
GO
ALTER TABLE [dbo].[notifications]  WITH CHECK ADD  CONSTRAINT [notifications_users_ibfk_3] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[notifications] CHECK CONSTRAINT [notifications_users_ibfk_3]
GO
ALTER TABLE [dbo].[opinion_comments]  WITH CHECK ADD  CONSTRAINT [fk_opinion_comments_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[opinion_comments] CHECK CONSTRAINT [fk_opinion_comments_createdBy]
GO
ALTER TABLE [dbo].[opinion_comments]  WITH CHECK ADD  CONSTRAINT [fk_opinion_comments_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[opinion_comments] CHECK CONSTRAINT [fk_opinion_comments_modifiedBy]
GO
ALTER TABLE [dbo].[opinion_comments]  WITH CHECK ADD  CONSTRAINT [fk_opinion_comments_opinion] FOREIGN KEY([opinion_id])
REFERENCES [dbo].[opinions] ([id])
GO
ALTER TABLE [dbo].[opinion_comments] CHECK CONSTRAINT [fk_opinion_comments_opinion]
GO
ALTER TABLE [dbo].[opinion_contributors]  WITH CHECK ADD  CONSTRAINT [fk_opinion_contributors_1] FOREIGN KEY([opinion_id])
REFERENCES [dbo].[opinions] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[opinion_contributors] CHECK CONSTRAINT [fk_opinion_contributors_1]
GO
ALTER TABLE [dbo].[opinion_contributors]  WITH CHECK ADD  CONSTRAINT [fk_opinion_contributors_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[opinion_contributors] CHECK CONSTRAINT [fk_opinion_contributors_2]
GO
ALTER TABLE [dbo].[opinion_document_status_language]  WITH CHECK ADD  CONSTRAINT [fk_opinion_document_status_language_1] FOREIGN KEY([status_id])
REFERENCES [dbo].[opinion_document_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[opinion_document_status_language] CHECK CONSTRAINT [fk_opinion_document_status_language_1]
GO
ALTER TABLE [dbo].[opinion_document_status_language]  WITH CHECK ADD  CONSTRAINT [fk_opinion_document_status_language_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[opinion_document_status_language] CHECK CONSTRAINT [fk_opinion_document_status_language_2]
GO
ALTER TABLE [dbo].[opinion_document_type_language]  WITH CHECK ADD  CONSTRAINT [fk_opinion_document_type_language_1] FOREIGN KEY([type_id])
REFERENCES [dbo].[opinion_document_type] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[opinion_document_type_language] CHECK CONSTRAINT [fk_opinion_document_type_language_1]
GO
ALTER TABLE [dbo].[opinion_document_type_language]  WITH CHECK ADD  CONSTRAINT [fk_opinion_document_type_language_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[opinion_document_type_language] CHECK CONSTRAINT [fk_opinion_document_type_language_2]
GO
ALTER TABLE [dbo].[opinion_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_opinion_types_languages1] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[opinion_types_languages] CHECK CONSTRAINT [fk_opinion_types_languages1]
GO
ALTER TABLE [dbo].[opinion_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_opinion_types_languages2] FOREIGN KEY([opinion_type_id])
REFERENCES [dbo].[opinion_types] ([id])
GO
ALTER TABLE [dbo].[opinion_types_languages] CHECK CONSTRAINT [fk_opinion_types_languages2]
GO
ALTER TABLE [dbo].[opinion_url]  WITH CHECK ADD  CONSTRAINT [fk_opinion_url_1] FOREIGN KEY([opinion_id])
REFERENCES [dbo].[opinions] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[opinion_url] CHECK CONSTRAINT [fk_opinion_url_1]
GO
ALTER TABLE [dbo].[opinion_url]  WITH CHECK ADD  CONSTRAINT [fk_opinion_url_2] FOREIGN KEY([document_type_id])
REFERENCES [dbo].[opinion_document_type] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[opinion_url] CHECK CONSTRAINT [fk_opinion_url_2]
GO
ALTER TABLE [dbo].[opinion_url]  WITH CHECK ADD  CONSTRAINT [fk_opinion_url_3] FOREIGN KEY([document_status_id])
REFERENCES [dbo].[opinion_document_status] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[opinion_url] CHECK CONSTRAINT [fk_opinion_url_3]
GO
ALTER TABLE [dbo].[opinion_url]  WITH CHECK ADD  CONSTRAINT [fk_opinion_url_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[opinion_url] CHECK CONSTRAINT [fk_opinion_url_createdBy]
GO
ALTER TABLE [dbo].[opinion_url]  WITH CHECK ADD  CONSTRAINT [fk_opinion_url_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[opinion_url] CHECK CONSTRAINT [fk_opinion_url_modifiedBy]
GO
ALTER TABLE [dbo].[opinion_users]  WITH CHECK ADD  CONSTRAINT [opinion_users_ibfk_1] FOREIGN KEY([opinion_id])
REFERENCES [dbo].[opinions] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[opinion_users] CHECK CONSTRAINT [opinion_users_ibfk_1]
GO
ALTER TABLE [dbo].[opinion_users]  WITH CHECK ADD  CONSTRAINT [opinion_users_ibfk_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[opinion_users] CHECK CONSTRAINT [opinion_users_ibfk_2]
GO
ALTER TABLE [dbo].[opinion_workflow_status_relation]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflow_status_relation_1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[opinion_workflows] ([id])
GO
ALTER TABLE [dbo].[opinion_workflow_status_relation] CHECK CONSTRAINT [fk_opinion_workflow_status_relation_1]
GO
ALTER TABLE [dbo].[opinion_workflow_status_relation]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflow_status_relation_2] FOREIGN KEY([status_id])
REFERENCES [dbo].[opinion_statuses] ([id])
GO
ALTER TABLE [dbo].[opinion_workflow_status_relation] CHECK CONSTRAINT [fk_opinion_workflow_status_relation_2]
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflow_status_transition_1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[opinion_workflows] ([id])
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition] CHECK CONSTRAINT [fk_opinion_workflow_status_transition_1]
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflow_status_transition_2] FOREIGN KEY([from_step])
REFERENCES [dbo].[opinion_statuses] ([id])
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition] CHECK CONSTRAINT [fk_opinion_workflow_status_transition_2]
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflow_status_transition_3] FOREIGN KEY([to_step])
REFERENCES [dbo].[opinion_statuses] ([id])
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition] CHECK CONSTRAINT [fk_opinion_workflow_status_transition_3]
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition_history]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflow_status_transition_history_1] FOREIGN KEY([opinion_id])
REFERENCES [dbo].[opinions] ([id])
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition_history] CHECK CONSTRAINT [fk_opinion_workflow_status_transition_history_1]
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition_history]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflow_status_transition_history_2] FOREIGN KEY([from_step])
REFERENCES [dbo].[opinion_statuses] ([id])
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition_history] CHECK CONSTRAINT [fk_opinion_workflow_status_transition_history_2]
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition_history]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflow_status_transition_history_3] FOREIGN KEY([to_step])
REFERENCES [dbo].[opinion_statuses] ([id])
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition_history] CHECK CONSTRAINT [fk_opinion_workflow_status_transition_history_3]
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition_history]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflow_status_transition_history_user] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition_history] CHECK CONSTRAINT [fk_opinion_workflow_status_transition_history_user]
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition_permissions]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflow_status_transition_permissions_1] FOREIGN KEY([transition])
REFERENCES [dbo].[opinion_workflow_status_transition] ([id])
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition_permissions] CHECK CONSTRAINT [fk_opinion_workflow_status_transition_permissions_1]
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition_screen_fields]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflow_status_transition_screen_fields_1] FOREIGN KEY([transition])
REFERENCES [dbo].[opinion_workflow_status_transition] ([id])
GO
ALTER TABLE [dbo].[opinion_workflow_status_transition_screen_fields] CHECK CONSTRAINT [fk_opinion_workflow_status_transition_screen_fields_1]
GO
ALTER TABLE [dbo].[opinion_workflow_types]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflow_types_1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[opinion_workflows] ([id])
GO
ALTER TABLE [dbo].[opinion_workflow_types] CHECK CONSTRAINT [fk_opinion_workflow_types_1]
GO
ALTER TABLE [dbo].[opinion_workflow_types]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflow_types_2] FOREIGN KEY([type_id])
REFERENCES [dbo].[opinion_types] ([id])
GO
ALTER TABLE [dbo].[opinion_workflow_types] CHECK CONSTRAINT [fk_opinion_workflow_types_2]
GO
ALTER TABLE [dbo].[opinion_workflows]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflows_1] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[opinion_workflows] CHECK CONSTRAINT [fk_opinion_workflows_1]
GO
ALTER TABLE [dbo].[opinion_workflows]  WITH CHECK ADD  CONSTRAINT [fk_opinion_workflows_2] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[opinion_workflows] CHECK CONSTRAINT [fk_opinion_workflows_2]
GO
ALTER TABLE [dbo].[opinions]  WITH CHECK ADD  CONSTRAINT [fk_opinions_1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[opinions] CHECK CONSTRAINT [fk_opinions_1]
GO
ALTER TABLE [dbo].[opinions]  WITH CHECK ADD  CONSTRAINT [fk_opinions_10] FOREIGN KEY([stage])
REFERENCES [dbo].[legal_case_litigation_details] ([id])
GO
ALTER TABLE [dbo].[opinions] CHECK CONSTRAINT [fk_opinions_10]
GO
ALTER TABLE [dbo].[opinions]  WITH CHECK ADD  CONSTRAINT [fk_opinions_11] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[opinions] CHECK CONSTRAINT [fk_opinions_11]
GO
ALTER TABLE [dbo].[opinions]  WITH CHECK ADD  CONSTRAINT [fk_opinions_2] FOREIGN KEY([opinion_status_id])
REFERENCES [dbo].[opinion_statuses] ([id])
GO
ALTER TABLE [dbo].[opinions] CHECK CONSTRAINT [fk_opinions_2]
GO
ALTER TABLE [dbo].[opinions]  WITH CHECK ADD  CONSTRAINT [fk_opinions_3] FOREIGN KEY([opinion_type_id])
REFERENCES [dbo].[opinion_types] ([id])
GO
ALTER TABLE [dbo].[opinions] CHECK CONSTRAINT [fk_opinions_3]
GO
ALTER TABLE [dbo].[opinions]  WITH CHECK ADD  CONSTRAINT [fk_opinions_4] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[opinions] CHECK CONSTRAINT [fk_opinions_4]
GO
ALTER TABLE [dbo].[opinions]  WITH CHECK ADD  CONSTRAINT [fk_opinions_5] FOREIGN KEY([assigned_to])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[opinions] CHECK CONSTRAINT [fk_opinions_5]
GO
ALTER TABLE [dbo].[opinions]  WITH CHECK ADD  CONSTRAINT [fk_opinions_6] FOREIGN KEY([reporter])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[opinions] CHECK CONSTRAINT [fk_opinions_6]
GO
ALTER TABLE [dbo].[opinions]  WITH CHECK ADD  CONSTRAINT [fk_opinions_7] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[opinions] CHECK CONSTRAINT [fk_opinions_7]
GO
ALTER TABLE [dbo].[opinions]  WITH CHECK ADD  CONSTRAINT [fk_opinions_8] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[opinions] CHECK CONSTRAINT [fk_opinions_8]
GO
ALTER TABLE [dbo].[opinions]  WITH CHECK ADD  CONSTRAINT [fk_opinions_9] FOREIGN KEY([workflow])
REFERENCES [dbo].[opinion_workflows] ([id])
GO
ALTER TABLE [dbo].[opinions] CHECK CONSTRAINT [fk_opinions_9]
GO
ALTER TABLE [dbo].[opinions_documents]  WITH CHECK ADD  CONSTRAINT [fk_opinions_documents_1] FOREIGN KEY([opinion_id])
REFERENCES [dbo].[opinions] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[opinions_documents] CHECK CONSTRAINT [fk_opinions_documents_1]
GO
ALTER TABLE [dbo].[opinions_documents]  WITH CHECK ADD  CONSTRAINT [fk_opinions_documents_2] FOREIGN KEY([document_id])
REFERENCES [dbo].[documents_management_system] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[opinions_documents] CHECK CONSTRAINT [fk_opinions_documents_2]
GO
ALTER TABLE [dbo].[organization_invoice_templates]  WITH CHECK ADD  CONSTRAINT [organization_invoice_templates_ibfk_1] FOREIGN KEY([organization_id])
REFERENCES [dbo].[organizations] ([id])
GO
ALTER TABLE [dbo].[organization_invoice_templates] CHECK CONSTRAINT [organization_invoice_templates_ibfk_1]
GO
ALTER TABLE [dbo].[organizations]  WITH CHECK ADD  CONSTRAINT [organizations_ibfk_1] FOREIGN KEY([currency_id])
REFERENCES [dbo].[countries] ([id])
GO
ALTER TABLE [dbo].[organizations] CHECK CONSTRAINT [organizations_ibfk_1]
GO
ALTER TABLE [dbo].[partner_settlements_invoices]  WITH CHECK ADD FOREIGN KEY([invoice_header_id])
REFERENCES [dbo].[invoice_headers] ([id])
GO
ALTER TABLE [dbo].[partner_settlements_invoices]  WITH CHECK ADD FOREIGN KEY([invoice_header_id])
REFERENCES [dbo].[invoice_headers] ([id])
GO
ALTER TABLE [dbo].[partner_settlements_invoices]  WITH CHECK ADD FOREIGN KEY([voucher_header_id])
REFERENCES [dbo].[voucher_headers] ([id])
GO
ALTER TABLE [dbo].[partner_settlements_invoices]  WITH CHECK ADD FOREIGN KEY([voucher_header_id])
REFERENCES [dbo].[voucher_headers] ([id])
GO
ALTER TABLE [dbo].[partners]  WITH CHECK ADD  CONSTRAINT [partners_ibfk_1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[partners] CHECK CONSTRAINT [partners_ibfk_1]
GO
ALTER TABLE [dbo].[partners]  WITH CHECK ADD  CONSTRAINT [partners_ibfk_2] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[partners] CHECK CONSTRAINT [partners_ibfk_2]
GO
ALTER TABLE [dbo].[partners]  WITH CHECK ADD  CONSTRAINT [partners_ibfk_3] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[partners] CHECK CONSTRAINT [partners_ibfk_3]
GO
ALTER TABLE [dbo].[partners]  WITH CHECK ADD  CONSTRAINT [partners_ibfk_4] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[partners] CHECK CONSTRAINT [partners_ibfk_4]
GO
ALTER TABLE [dbo].[party]  WITH CHECK ADD  CONSTRAINT [fk_party_1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[party] CHECK CONSTRAINT [fk_party_1]
GO
ALTER TABLE [dbo].[party]  WITH CHECK ADD  CONSTRAINT [fk_party_2] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[party] CHECK CONSTRAINT [fk_party_2]
GO
ALTER TABLE [dbo].[party_category_language]  WITH CHECK ADD  CONSTRAINT [fk_party_category_language_1] FOREIGN KEY([category_id])
REFERENCES [dbo].[party_category] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[party_category_language] CHECK CONSTRAINT [fk_party_category_language_1]
GO
ALTER TABLE [dbo].[party_category_language]  WITH CHECK ADD  CONSTRAINT [fk_party_category_language_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[party_category_language] CHECK CONSTRAINT [fk_party_category_language_2]
GO
ALTER TABLE [dbo].[planning_board_column_options]  WITH CHECK ADD  CONSTRAINT [fk_planning_board_column_options_planning_board_columns1] FOREIGN KEY([planning_board_column_id])
REFERENCES [dbo].[planning_board_columns] ([id])
GO
ALTER TABLE [dbo].[planning_board_column_options] CHECK CONSTRAINT [fk_planning_board_column_options_planning_board_columns1]
GO
ALTER TABLE [dbo].[planning_board_column_options]  WITH CHECK ADD  CONSTRAINT [fk_planning_board_column_options_planning_boards1] FOREIGN KEY([planning_board_id])
REFERENCES [dbo].[planning_boards] ([id])
GO
ALTER TABLE [dbo].[planning_board_column_options] CHECK CONSTRAINT [fk_planning_board_column_options_planning_boards1]
GO
ALTER TABLE [dbo].[planning_board_column_options]  WITH CHECK ADD  CONSTRAINT [fk_planning_board_column_options_workflow_status1] FOREIGN KEY([case_status_id])
REFERENCES [dbo].[workflow_status] ([id])
GO
ALTER TABLE [dbo].[planning_board_column_options] CHECK CONSTRAINT [fk_planning_board_column_options_workflow_status1]
GO
ALTER TABLE [dbo].[planning_board_columns]  WITH CHECK ADD  CONSTRAINT [fk_planning_board_columns_planning_boards1] FOREIGN KEY([planning_board_id])
REFERENCES [dbo].[planning_boards] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[planning_board_columns] CHECK CONSTRAINT [fk_planning_board_columns_planning_boards1]
GO
ALTER TABLE [dbo].[planning_board_saved_filters]  WITH CHECK ADD  CONSTRAINT [planning_board_saved_filters_ibfk_1] FOREIGN KEY([userId])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[planning_board_saved_filters] CHECK CONSTRAINT [planning_board_saved_filters_ibfk_1]
GO
ALTER TABLE [dbo].[planning_board_saved_filters]  WITH CHECK ADD  CONSTRAINT [planning_board_saved_filters_ibfk_2] FOREIGN KEY([boardId])
REFERENCES [dbo].[planning_boards] ([id])
GO
ALTER TABLE [dbo].[planning_board_saved_filters] CHECK CONSTRAINT [planning_board_saved_filters_ibfk_2]
GO
ALTER TABLE [dbo].[planning_boards]  WITH CHECK ADD FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[planning_boards]  WITH CHECK ADD FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[planning_boards]  WITH CHECK ADD FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[planning_boards]  WITH CHECK ADD FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[preferred_shares]  WITH CHECK ADD  CONSTRAINT [fk_preferred_shares_companies1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[preferred_shares] CHECK CONSTRAINT [fk_preferred_shares_companies1]
GO
ALTER TABLE [dbo].[provider_groups_users]  WITH CHECK ADD  CONSTRAINT [fk_provider_groups_users_provider_groups1] FOREIGN KEY([provider_group_id])
REFERENCES [dbo].[provider_groups] ([id])
GO
ALTER TABLE [dbo].[provider_groups_users] CHECK CONSTRAINT [fk_provider_groups_users_provider_groups1]
GO
ALTER TABLE [dbo].[provider_groups_users]  WITH CHECK ADD  CONSTRAINT [fk_provider_groups_users_users1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[provider_groups_users] CHECK CONSTRAINT [fk_provider_groups_users_users1]
GO
ALTER TABLE [dbo].[quote_headers]  WITH CHECK ADD  CONSTRAINT [fk_quote_headers_4] FOREIGN KEY([related_invoice_id])
REFERENCES [dbo].[voucher_headers] ([id])
GO
ALTER TABLE [dbo].[quote_headers] CHECK CONSTRAINT [fk_quote_headers_4]
GO
ALTER TABLE [dbo].[quote_status_notes]  WITH CHECK ADD  CONSTRAINT [fk_quote_status_notes_1] FOREIGN KEY([quote_id])
REFERENCES [dbo].[quote_headers] ([id])
GO
ALTER TABLE [dbo].[quote_status_notes] CHECK CONSTRAINT [fk_quote_status_notes_1]
GO
ALTER TABLE [dbo].[quote_time_logs_items]  WITH CHECK ADD  CONSTRAINT [fk_quote_time_logs_items_1] FOREIGN KEY([item])
REFERENCES [dbo].[quote_details] ([id])
GO
ALTER TABLE [dbo].[quote_time_logs_items] CHECK CONSTRAINT [fk_quote_time_logs_items_1]
GO
ALTER TABLE [dbo].[quote_time_logs_items]  WITH CHECK ADD  CONSTRAINT [fk_quote_time_logs_items_2] FOREIGN KEY([time_log])
REFERENCES [dbo].[user_activity_logs] ([id])
GO
ALTER TABLE [dbo].[quote_time_logs_items] CHECK CONSTRAINT [fk_quote_time_logs_items_2]
GO
ALTER TABLE [dbo].[quote_time_logs_items]  WITH CHECK ADD  CONSTRAINT [fk_quote_time_logs_items_3] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[quote_time_logs_items] CHECK CONSTRAINT [fk_quote_time_logs_items_3]
GO
ALTER TABLE [dbo].[recurrence]  WITH CHECK ADD  CONSTRAINT [fk_recurrence_1] FOREIGN KEY([type_id])
REFERENCES [dbo].[recurring_types] ([id])
GO
ALTER TABLE [dbo].[recurrence] CHECK CONSTRAINT [fk_recurrence_1]
GO
ALTER TABLE [dbo].[related_cases]  WITH CHECK ADD  CONSTRAINT [fk_related_cases_legal_cases1] FOREIGN KEY([case_a_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[related_cases] CHECK CONSTRAINT [fk_related_cases_legal_cases1]
GO
ALTER TABLE [dbo].[related_cases]  WITH CHECK ADD  CONSTRAINT [fk_related_cases_legal_cases2] FOREIGN KEY([case_b_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[related_cases] CHECK CONSTRAINT [fk_related_cases_legal_cases2]
GO
ALTER TABLE [dbo].[related_contacts]  WITH CHECK ADD  CONSTRAINT [fk_related_contacts_contacts1] FOREIGN KEY([contact_a_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[related_contacts] CHECK CONSTRAINT [fk_related_contacts_contacts1]
GO
ALTER TABLE [dbo].[related_contacts]  WITH CHECK ADD  CONSTRAINT [fk_related_contacts_contacts2] FOREIGN KEY([contact_b_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[related_contacts] CHECK CONSTRAINT [fk_related_contacts_contacts2]
GO
ALTER TABLE [dbo].[related_contracts]  WITH CHECK ADD  CONSTRAINT [fk_related_contracts_1] FOREIGN KEY([contract_a_id])
REFERENCES [dbo].[contract] ([id])
GO
ALTER TABLE [dbo].[related_contracts] CHECK CONSTRAINT [fk_related_contracts_1]
GO
ALTER TABLE [dbo].[related_contracts]  WITH CHECK ADD  CONSTRAINT [fk_related_contracts_2] FOREIGN KEY([contract_b_id])
REFERENCES [dbo].[contract] ([id])
GO
ALTER TABLE [dbo].[related_contracts] CHECK CONSTRAINT [fk_related_contracts_2]
GO
ALTER TABLE [dbo].[reminder_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_reminder_types_languages1] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[reminder_types_languages] CHECK CONSTRAINT [fk_reminder_types_languages1]
GO
ALTER TABLE [dbo].[reminder_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_reminder_types_languages2] FOREIGN KEY([reminder_type_id])
REFERENCES [dbo].[reminder_types] ([id])
GO
ALTER TABLE [dbo].[reminder_types_languages] CHECK CONSTRAINT [fk_reminder_types_languages2]
GO
ALTER TABLE [dbo].[reminders]  WITH CHECK ADD  CONSTRAINT [fk_reminders_1] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[reminders] CHECK CONSTRAINT [fk_reminders_1]
GO
ALTER TABLE [dbo].[reminders]  WITH CHECK ADD  CONSTRAINT [reminders_companies_ibfk_1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[reminders] CHECK CONSTRAINT [reminders_companies_ibfk_1]
GO
ALTER TABLE [dbo].[reminders]  WITH CHECK ADD  CONSTRAINT [reminders_contacts_ibfk_1] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[reminders] CHECK CONSTRAINT [reminders_contacts_ibfk_1]
GO
ALTER TABLE [dbo].[reminders]  WITH CHECK ADD  CONSTRAINT [reminders_legal_case_hearings_ibfk_1] FOREIGN KEY([legal_case_hearing_id])
REFERENCES [dbo].[legal_case_hearings] ([id])
GO
ALTER TABLE [dbo].[reminders] CHECK CONSTRAINT [reminders_legal_case_hearings_ibfk_1]
GO
ALTER TABLE [dbo].[reminders]  WITH CHECK ADD  CONSTRAINT [reminders_legal_cases_ibfk_1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[reminders] CHECK CONSTRAINT [reminders_legal_cases_ibfk_1]
GO
ALTER TABLE [dbo].[reminders]  WITH CHECK ADD  CONSTRAINT [reminders_reminder_types_ibfk_1] FOREIGN KEY([reminder_type_id])
REFERENCES [dbo].[reminder_types] ([id])
GO
ALTER TABLE [dbo].[reminders] CHECK CONSTRAINT [reminders_reminder_types_ibfk_1]
GO
ALTER TABLE [dbo].[reminders]  WITH CHECK ADD  CONSTRAINT [reminders_tasks_ibfk_1] FOREIGN KEY([task_id])
REFERENCES [dbo].[tasks] ([id])
GO
ALTER TABLE [dbo].[reminders] CHECK CONSTRAINT [reminders_tasks_ibfk_1]
GO
ALTER TABLE [dbo].[reminders]  WITH CHECK ADD  CONSTRAINT [reminders_users_ibfk_1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[reminders] CHECK CONSTRAINT [reminders_users_ibfk_1]
GO
ALTER TABLE [dbo].[role_has_permissions]  WITH CHECK ADD  CONSTRAINT [role_has_permissions_permission_id_foreign] FOREIGN KEY([permission_id])
REFERENCES [dbo].[permissions] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[role_has_permissions] CHECK CONSTRAINT [role_has_permissions_permission_id_foreign]
GO
ALTER TABLE [dbo].[role_has_permissions]  WITH CHECK ADD  CONSTRAINT [role_has_permissions_role_id_foreign] FOREIGN KEY([role_id])
REFERENCES [dbo].[roles] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[role_has_permissions] CHECK CONSTRAINT [role_has_permissions_role_id_foreign]
GO
ALTER TABLE [dbo].[shared_reports]  WITH CHECK ADD  CONSTRAINT [shared_reports_ibfk_1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[shared_reports] CHECK CONSTRAINT [shared_reports_ibfk_1]
GO
ALTER TABLE [dbo].[shared_reports]  WITH CHECK ADD  CONSTRAINT [shared_reports_ibfk_2] FOREIGN KEY([report_id])
REFERENCES [dbo].[user_reports] ([id])
GO
ALTER TABLE [dbo].[shared_reports] CHECK CONSTRAINT [shared_reports_ibfk_2]
GO
ALTER TABLE [dbo].[shares_movements]  WITH CHECK ADD  CONSTRAINT [fk_shares_movements_lookup_members1] FOREIGN KEY([member_id])
REFERENCES [dbo].[lookup_members] ([id])
GO
ALTER TABLE [dbo].[shares_movements] CHECK CONSTRAINT [fk_shares_movements_lookup_members1]
GO
ALTER TABLE [dbo].[shares_movements]  WITH CHECK ADD  CONSTRAINT [fk_shares_movements_shares_movement_headers1] FOREIGN KEY([shares_movement_header_id])
REFERENCES [dbo].[shares_movement_headers] ([id])
GO
ALTER TABLE [dbo].[shares_movements] CHECK CONSTRAINT [fk_shares_movements_shares_movement_headers1]
GO
ALTER TABLE [dbo].[signature_authorities_documents]  WITH CHECK ADD  CONSTRAINT [fk_signature_authorities_documents_1] FOREIGN KEY([signature_authority])
REFERENCES [dbo].[company_signature_authorities] ([id])
GO
ALTER TABLE [dbo].[signature_authorities_documents] CHECK CONSTRAINT [fk_signature_authorities_documents_1]
GO
ALTER TABLE [dbo].[signature_authorities_documents]  WITH CHECK ADD  CONSTRAINT [fk_signature_authorities_documents_2] FOREIGN KEY([document])
REFERENCES [dbo].[documents_management_system] ([id])
GO
ALTER TABLE [dbo].[signature_authorities_documents] CHECK CONSTRAINT [fk_signature_authorities_documents_2]
GO
ALTER TABLE [dbo].[signature_criteria]  WITH CHECK ADD  CONSTRAINT [fk_signature_criteria_1] FOREIGN KEY([signature_id])
REFERENCES [dbo].[signature] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[signature_criteria] CHECK CONSTRAINT [fk_signature_criteria_1]
GO
ALTER TABLE [dbo].[signature_signee]  WITH CHECK ADD  CONSTRAINT [fk_signature_signee_1] FOREIGN KEY([signature_id])
REFERENCES [dbo].[signature] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[signature_signee] CHECK CONSTRAINT [fk_signature_signee_1]
GO
ALTER TABLE [dbo].[signature_signee_bm_role]  WITH CHECK ADD  CONSTRAINT [fk_signature_signee_bm_role_1] FOREIGN KEY([assignee_id])
REFERENCES [dbo].[signature_signee] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[signature_signee_bm_role] CHECK CONSTRAINT [fk_signature_signee_bm_role_1]
GO
ALTER TABLE [dbo].[signature_signee_bm_role]  WITH CHECK ADD  CONSTRAINT [fk_signature_signee_bm_role_2] FOREIGN KEY([role_id])
REFERENCES [dbo].[board_member_roles] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[signature_signee_bm_role] CHECK CONSTRAINT [fk_signature_signee_bm_role_2]
GO
ALTER TABLE [dbo].[stage_statuses_languages]  WITH CHECK ADD  CONSTRAINT [fk_stage_statuses_languages_1] FOREIGN KEY([status])
REFERENCES [dbo].[stage_statuses] ([id])
GO
ALTER TABLE [dbo].[stage_statuses_languages] CHECK CONSTRAINT [fk_stage_statuses_languages_1]
GO
ALTER TABLE [dbo].[stage_statuses_languages]  WITH CHECK ADD  CONSTRAINT [fk_stage_statuses_languages_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[stage_statuses_languages] CHECK CONSTRAINT [fk_stage_statuses_languages_2]
GO
ALTER TABLE [dbo].[sub_contract_type]  WITH CHECK ADD  CONSTRAINT [fk_sub_contract_type_1] FOREIGN KEY([type_id])
REFERENCES [dbo].[contract_type] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[sub_contract_type] CHECK CONSTRAINT [fk_sub_contract_type_1]
GO
ALTER TABLE [dbo].[sub_contract_type_language]  WITH CHECK ADD  CONSTRAINT [fk_sub_contract_type_language_1] FOREIGN KEY([sub_type_id])
REFERENCES [dbo].[sub_contract_type] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[sub_contract_type_language] CHECK CONSTRAINT [fk_sub_contract_type_language_1]
GO
ALTER TABLE [dbo].[sub_contract_type_language]  WITH CHECK ADD  CONSTRAINT [fk_sub_contract_type_language_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[sub_contract_type_language] CHECK CONSTRAINT [fk_sub_contract_type_language_2]
GO
ALTER TABLE [dbo].[supplier_taxes]  WITH CHECK ADD  CONSTRAINT [supplier_taxes_ibfk_1] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[supplier_taxes] CHECK CONSTRAINT [supplier_taxes_ibfk_1]
GO
ALTER TABLE [dbo].[surety_bonds]  WITH CHECK ADD  CONSTRAINT [FK_SuretyBond_Contract] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
GO
ALTER TABLE [dbo].[surety_bonds] CHECK CONSTRAINT [FK_SuretyBond_Contract]
GO
ALTER TABLE [dbo].[surety_bonds]  WITH CHECK ADD  CONSTRAINT [FK_SuretyBond_Currency] FOREIGN KEY([currency_id])
REFERENCES [dbo].[iso_currencies] ([id])
GO
ALTER TABLE [dbo].[surety_bonds] CHECK CONSTRAINT [FK_SuretyBond_Currency]
GO
ALTER TABLE [dbo].[surety_bonds]  WITH CHECK ADD  CONSTRAINT [FK_SuretyBond_Document] FOREIGN KEY([document_id])
REFERENCES [dbo].[documents_management_system] ([id])
GO
ALTER TABLE [dbo].[surety_bonds] CHECK CONSTRAINT [FK_SuretyBond_Document]
GO
ALTER TABLE [dbo].[suspect_arrest]  WITH CHECK ADD  CONSTRAINT [FK_suspect_arrest_attachments] FOREIGN KEY([arrest_attachments])
REFERENCES [dbo].[documents_management_system] ([id])
ON DELETE SET NULL
GO
ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [FK_suspect_arrest_attachments]
GO
ALTER TABLE [dbo].[suspect_arrest]  WITH CHECK ADD  CONSTRAINT [FK_suspect_arrest_contacts] FOREIGN KEY([arrested_contact_id])
REFERENCES [dbo].[contacts] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [FK_suspect_arrest_contacts]
GO
ALTER TABLE [dbo].[suspect_arrest]  WITH CHECK ADD  CONSTRAINT [FK_suspect_arrest_createdBy] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [FK_suspect_arrest_createdBy]
GO
ALTER TABLE [dbo].[suspect_arrest]  WITH CHECK ADD  CONSTRAINT [FK_suspect_arrest_legal_cases] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [FK_suspect_arrest_legal_cases]
GO
ALTER TABLE [dbo].[suspect_arrest]  WITH CHECK ADD  CONSTRAINT [FK_suspect_arrest_modifiedBy] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [FK_suspect_arrest_modifiedBy]
GO
ALTER TABLE [dbo].[task_board_column_options]  WITH CHECK ADD  CONSTRAINT [fk_task_board_column_options_task_board_columns1] FOREIGN KEY([task_board_column_id])
REFERENCES [dbo].[task_board_columns] ([id])
GO
ALTER TABLE [dbo].[task_board_column_options] CHECK CONSTRAINT [fk_task_board_column_options_task_board_columns1]
GO
ALTER TABLE [dbo].[task_board_column_options]  WITH CHECK ADD  CONSTRAINT [fk_task_board_column_options_task_boards1] FOREIGN KEY([task_board_id])
REFERENCES [dbo].[task_boards] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[task_board_column_options] CHECK CONSTRAINT [fk_task_board_column_options_task_boards1]
GO
ALTER TABLE [dbo].[task_board_column_options]  WITH CHECK ADD  CONSTRAINT [fk_task_board_column_options_task_statuses1] FOREIGN KEY([task_status_id])
REFERENCES [dbo].[task_statuses] ([id])
GO
ALTER TABLE [dbo].[task_board_column_options] CHECK CONSTRAINT [fk_task_board_column_options_task_statuses1]
GO
ALTER TABLE [dbo].[task_board_columns]  WITH CHECK ADD  CONSTRAINT [fk_task_board_columns_task_boards1] FOREIGN KEY([task_board_id])
REFERENCES [dbo].[task_boards] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[task_board_columns] CHECK CONSTRAINT [fk_task_board_columns_task_boards1]
GO
ALTER TABLE [dbo].[task_board_saved_filters]  WITH CHECK ADD  CONSTRAINT [task_board_saved_filters_ibfk_1] FOREIGN KEY([userId])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[task_board_saved_filters] CHECK CONSTRAINT [task_board_saved_filters_ibfk_1]
GO
ALTER TABLE [dbo].[task_board_saved_filters]  WITH CHECK ADD  CONSTRAINT [task_board_saved_filters_ibfk_2] FOREIGN KEY([boardId])
REFERENCES [dbo].[task_boards] ([id])
GO
ALTER TABLE [dbo].[task_board_saved_filters] CHECK CONSTRAINT [task_board_saved_filters_ibfk_2]
GO
ALTER TABLE [dbo].[task_boards]  WITH CHECK ADD FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[task_boards]  WITH CHECK ADD FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[task_boards]  WITH CHECK ADD FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[task_boards]  WITH CHECK ADD FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[task_comments]  WITH CHECK ADD  CONSTRAINT [fk_task_comments_1] FOREIGN KEY([task_id])
REFERENCES [dbo].[tasks] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[task_comments] CHECK CONSTRAINT [fk_task_comments_1]
GO
ALTER TABLE [dbo].[task_contributors]  WITH CHECK ADD  CONSTRAINT [fk_task_contributors_1] FOREIGN KEY([task_id])
REFERENCES [dbo].[tasks] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[task_contributors] CHECK CONSTRAINT [fk_task_contributors_1]
GO
ALTER TABLE [dbo].[task_contributors]  WITH CHECK ADD  CONSTRAINT [fk_task_contributors_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[task_contributors] CHECK CONSTRAINT [fk_task_contributors_2]
GO
ALTER TABLE [dbo].[task_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_task_types_languages1] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[task_types_languages] CHECK CONSTRAINT [fk_task_types_languages1]
GO
ALTER TABLE [dbo].[task_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_task_types_languages2] FOREIGN KEY([task_type_id])
REFERENCES [dbo].[task_types] ([id])
GO
ALTER TABLE [dbo].[task_types_languages] CHECK CONSTRAINT [fk_task_types_languages2]
GO
ALTER TABLE [dbo].[task_users]  WITH CHECK ADD  CONSTRAINT [task_users_ibfk_1] FOREIGN KEY([task_id])
REFERENCES [dbo].[tasks] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[task_users] CHECK CONSTRAINT [task_users_ibfk_1]
GO
ALTER TABLE [dbo].[task_users]  WITH CHECK ADD  CONSTRAINT [task_users_ibfk_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[task_users] CHECK CONSTRAINT [task_users_ibfk_2]
GO
ALTER TABLE [dbo].[task_workflow_status_relation]  WITH CHECK ADD  CONSTRAINT [fk_task_workflow_status_relation_1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[task_workflows] ([id])
GO
ALTER TABLE [dbo].[task_workflow_status_relation] CHECK CONSTRAINT [fk_task_workflow_status_relation_1]
GO
ALTER TABLE [dbo].[task_workflow_status_relation]  WITH CHECK ADD  CONSTRAINT [fk_task_workflow_status_relation_2] FOREIGN KEY([status_id])
REFERENCES [dbo].[task_statuses] ([id])
GO
ALTER TABLE [dbo].[task_workflow_status_relation] CHECK CONSTRAINT [fk_task_workflow_status_relation_2]
GO
ALTER TABLE [dbo].[task_workflow_status_transition]  WITH CHECK ADD  CONSTRAINT [fk_task_workflow_status_transition_1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[task_workflows] ([id])
GO
ALTER TABLE [dbo].[task_workflow_status_transition] CHECK CONSTRAINT [fk_task_workflow_status_transition_1]
GO
ALTER TABLE [dbo].[task_workflow_status_transition]  WITH CHECK ADD  CONSTRAINT [fk_task_workflow_status_transition_2] FOREIGN KEY([from_step])
REFERENCES [dbo].[task_statuses] ([id])
GO
ALTER TABLE [dbo].[task_workflow_status_transition] CHECK CONSTRAINT [fk_task_workflow_status_transition_2]
GO
ALTER TABLE [dbo].[task_workflow_status_transition]  WITH CHECK ADD  CONSTRAINT [fk_task_workflow_status_transition_3] FOREIGN KEY([to_step])
REFERENCES [dbo].[task_statuses] ([id])
GO
ALTER TABLE [dbo].[task_workflow_status_transition] CHECK CONSTRAINT [fk_task_workflow_status_transition_3]
GO
ALTER TABLE [dbo].[task_workflow_status_transition_history]  WITH CHECK ADD  CONSTRAINT [fk_task_workflow_status_transition_history_1] FOREIGN KEY([task_id])
REFERENCES [dbo].[tasks] ([id])
GO
ALTER TABLE [dbo].[task_workflow_status_transition_history] CHECK CONSTRAINT [fk_task_workflow_status_transition_history_1]
GO
ALTER TABLE [dbo].[task_workflow_status_transition_history]  WITH CHECK ADD  CONSTRAINT [fk_task_workflow_status_transition_history_2] FOREIGN KEY([from_step])
REFERENCES [dbo].[task_statuses] ([id])
GO
ALTER TABLE [dbo].[task_workflow_status_transition_history] CHECK CONSTRAINT [fk_task_workflow_status_transition_history_2]
GO
ALTER TABLE [dbo].[task_workflow_status_transition_history]  WITH CHECK ADD  CONSTRAINT [fk_task_workflow_status_transition_history_3] FOREIGN KEY([to_step])
REFERENCES [dbo].[task_statuses] ([id])
GO
ALTER TABLE [dbo].[task_workflow_status_transition_history] CHECK CONSTRAINT [fk_task_workflow_status_transition_history_3]
GO
ALTER TABLE [dbo].[task_workflow_status_transition_permissions]  WITH CHECK ADD  CONSTRAINT [fk_task_workflow_status_transition_permissions_1] FOREIGN KEY([transition])
REFERENCES [dbo].[task_workflow_status_transition] ([id])
GO
ALTER TABLE [dbo].[task_workflow_status_transition_permissions] CHECK CONSTRAINT [fk_task_workflow_status_transition_permissions_1]
GO
ALTER TABLE [dbo].[task_workflow_status_transition_screen_fields]  WITH CHECK ADD  CONSTRAINT [fk_task_workflow_status_transition_screen_fields_1] FOREIGN KEY([transition])
REFERENCES [dbo].[task_workflow_status_transition] ([id])
GO
ALTER TABLE [dbo].[task_workflow_status_transition_screen_fields] CHECK CONSTRAINT [fk_task_workflow_status_transition_screen_fields_1]
GO
ALTER TABLE [dbo].[task_workflow_types]  WITH CHECK ADD  CONSTRAINT [fk_task_workflow_types_1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[task_workflows] ([id])
GO
ALTER TABLE [dbo].[task_workflow_types] CHECK CONSTRAINT [fk_task_workflow_types_1]
GO
ALTER TABLE [dbo].[task_workflow_types]  WITH CHECK ADD  CONSTRAINT [fk_task_workflow_types_2] FOREIGN KEY([type_id])
REFERENCES [dbo].[task_types] ([id])
GO
ALTER TABLE [dbo].[task_workflow_types] CHECK CONSTRAINT [fk_task_workflow_types_2]
GO
ALTER TABLE [dbo].[task_workflows]  WITH CHECK ADD  CONSTRAINT [fk_task_workflows_1] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[task_workflows] CHECK CONSTRAINT [fk_task_workflows_1]
GO
ALTER TABLE [dbo].[task_workflows]  WITH CHECK ADD  CONSTRAINT [fk_task_workflows_2] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[task_workflows] CHECK CONSTRAINT [fk_task_workflows_2]
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD  CONSTRAINT [fk_tasks_1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[tasks] CHECK CONSTRAINT [fk_tasks_1]
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD  CONSTRAINT [fk_tasks_10] FOREIGN KEY([stage])
REFERENCES [dbo].[legal_case_litigation_details] ([id])
GO
ALTER TABLE [dbo].[tasks] CHECK CONSTRAINT [fk_tasks_10]
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD  CONSTRAINT [fk_tasks_11] FOREIGN KEY([contract_id])
REFERENCES [dbo].[contract] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[tasks] CHECK CONSTRAINT [fk_tasks_11]
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD  CONSTRAINT [fk_tasks_2] FOREIGN KEY([task_status_id])
REFERENCES [dbo].[task_statuses] ([id])
GO
ALTER TABLE [dbo].[tasks] CHECK CONSTRAINT [fk_tasks_2]
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD  CONSTRAINT [fk_tasks_3] FOREIGN KEY([task_type_id])
REFERENCES [dbo].[task_types] ([id])
GO
ALTER TABLE [dbo].[tasks] CHECK CONSTRAINT [fk_tasks_3]
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD  CONSTRAINT [fk_tasks_4] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[tasks] CHECK CONSTRAINT [fk_tasks_4]
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD  CONSTRAINT [fk_tasks_5] FOREIGN KEY([assigned_to])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[tasks] CHECK CONSTRAINT [fk_tasks_5]
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD  CONSTRAINT [fk_tasks_6] FOREIGN KEY([reporter])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[tasks] CHECK CONSTRAINT [fk_tasks_6]
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD  CONSTRAINT [fk_tasks_7] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[tasks] CHECK CONSTRAINT [fk_tasks_7]
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD  CONSTRAINT [fk_tasks_8] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[tasks] CHECK CONSTRAINT [fk_tasks_8]
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD  CONSTRAINT [fk_tasks_9] FOREIGN KEY([workflow])
REFERENCES [dbo].[task_workflows] ([id])
GO
ALTER TABLE [dbo].[tasks] CHECK CONSTRAINT [fk_tasks_9]
GO
ALTER TABLE [dbo].[tasks_documents]  WITH CHECK ADD  CONSTRAINT [fk_tasks_documents_1] FOREIGN KEY([task_id])
REFERENCES [dbo].[tasks] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[tasks_documents] CHECK CONSTRAINT [fk_tasks_documents_1]
GO
ALTER TABLE [dbo].[tasks_documents]  WITH CHECK ADD  CONSTRAINT [fk_tasks_documents_2] FOREIGN KEY([document_id])
REFERENCES [dbo].[documents_management_system] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[tasks_documents] CHECK CONSTRAINT [fk_tasks_documents_2]
GO
ALTER TABLE [dbo].[taxes]  WITH CHECK ADD  CONSTRAINT [taxes_ibfk_1] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[taxes] CHECK CONSTRAINT [taxes_ibfk_1]
GO
ALTER TABLE [dbo].[time_internal_statuses_languages]  WITH CHECK ADD  CONSTRAINT [fk_time_internal_statuses_languages_1] FOREIGN KEY([internal_status])
REFERENCES [dbo].[time_internal_statuses] ([id])
GO
ALTER TABLE [dbo].[time_internal_statuses_languages] CHECK CONSTRAINT [fk_time_internal_statuses_languages_1]
GO
ALTER TABLE [dbo].[time_internal_statuses_languages]  WITH CHECK ADD  CONSTRAINT [fk_time_internal_statuses_languages_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[time_internal_statuses_languages] CHECK CONSTRAINT [fk_time_internal_statuses_languages_2]
GO
ALTER TABLE [dbo].[time_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_time_types_languages_1] FOREIGN KEY([type])
REFERENCES [dbo].[time_types] ([id])
GO
ALTER TABLE [dbo].[time_types_languages] CHECK CONSTRAINT [fk_time_types_languages_1]
GO
ALTER TABLE [dbo].[time_types_languages]  WITH CHECK ADD  CONSTRAINT [fk_time_types_languages_2] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[time_types_languages] CHECK CONSTRAINT [fk_time_types_languages_2]
GO
ALTER TABLE [dbo].[titles_languages]  WITH CHECK ADD  CONSTRAINT [fk_titles_languages1] FOREIGN KEY([language_id])
REFERENCES [dbo].[languages] ([id])
GO
ALTER TABLE [dbo].[titles_languages] CHECK CONSTRAINT [fk_titles_languages1]
GO
ALTER TABLE [dbo].[titles_languages]  WITH CHECK ADD  CONSTRAINT [fk_titles_languages2] FOREIGN KEY([title_id])
REFERENCES [dbo].[titles] ([id])
GO
ALTER TABLE [dbo].[titles_languages] CHECK CONSTRAINT [fk_titles_languages2]
GO
ALTER TABLE [dbo].[trigger_action_task_values]  WITH CHECK ADD  CONSTRAINT [FK_trigger_action_task_values_task_types] FOREIGN KEY([task_type])
REFERENCES [dbo].[task_types] ([id])
GO
ALTER TABLE [dbo].[trigger_action_task_values] CHECK CONSTRAINT [FK_trigger_action_task_values_task_types]
GO
ALTER TABLE [dbo].[trigger_action_task_values]  WITH CHECK ADD  CONSTRAINT [FK_trigger_action_task_values_trigger_actions] FOREIGN KEY([action_id])
REFERENCES [dbo].[trigger_actions] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[trigger_action_task_values] CHECK CONSTRAINT [FK_trigger_action_task_values_trigger_actions]
GO
ALTER TABLE [dbo].[trigger_action_task_values]  WITH CHECK ADD  CONSTRAINT [FK_trigger_action_task_values_users] FOREIGN KEY([assigned_to])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[trigger_action_task_values] CHECK CONSTRAINT [FK_trigger_action_task_values_users]
GO
ALTER TABLE [dbo].[trigger_actions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_actions_trigger_action_types] FOREIGN KEY([trigger_action_type_id])
REFERENCES [dbo].[trigger_action_types] ([id])
GO
ALTER TABLE [dbo].[trigger_actions] CHECK CONSTRAINT [FK_trigger_actions_trigger_action_types]
GO
ALTER TABLE [dbo].[trigger_actions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_actions_triggers] FOREIGN KEY([trigger_id])
REFERENCES [dbo].[triggers] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[trigger_actions] CHECK CONSTRAINT [FK_trigger_actions_triggers]
GO
ALTER TABLE [dbo].[trigger_actions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_actions_users_1] FOREIGN KEY([modified_by])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[trigger_actions] CHECK CONSTRAINT [FK_trigger_actions_users_1]
GO
ALTER TABLE [dbo].[trigger_actions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_actions_users_2] FOREIGN KEY([created_by])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[trigger_actions] CHECK CONSTRAINT [FK_trigger_actions_users_2]
GO
ALTER TABLE [dbo].[trigger_matter_workflow_conditions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_matter_workflow_conditions_case_types] FOREIGN KEY([area_of_practice])
REFERENCES [dbo].[case_types] ([id])
GO
ALTER TABLE [dbo].[trigger_matter_workflow_conditions] CHECK CONSTRAINT [FK_trigger_matter_workflow_conditions_case_types]
GO
ALTER TABLE [dbo].[trigger_matter_workflow_conditions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_matter_workflow_conditions_trigger_matter_workflow_conditions_workflow_status_1] FOREIGN KEY([from_stage])
REFERENCES [dbo].[workflow_status] ([id])
GO
ALTER TABLE [dbo].[trigger_matter_workflow_conditions] CHECK CONSTRAINT [FK_trigger_matter_workflow_conditions_trigger_matter_workflow_conditions_workflow_status_1]
GO
ALTER TABLE [dbo].[trigger_matter_workflow_conditions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_matter_workflow_conditions_trigger_matter_workflow_conditions_workflow_status_2] FOREIGN KEY([to_stage])
REFERENCES [dbo].[workflow_status] ([id])
GO
ALTER TABLE [dbo].[trigger_matter_workflow_conditions] CHECK CONSTRAINT [FK_trigger_matter_workflow_conditions_trigger_matter_workflow_conditions_workflow_status_2]
GO
ALTER TABLE [dbo].[trigger_matter_workflow_conditions]  WITH CHECK ADD  CONSTRAINT [FK_trigger_matter_workflow_conditions_triggers] FOREIGN KEY([trigger_id])
REFERENCES [dbo].[triggers] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[trigger_matter_workflow_conditions] CHECK CONSTRAINT [FK_trigger_matter_workflow_conditions_triggers]
GO
ALTER TABLE [dbo].[triggers]  WITH CHECK ADD  CONSTRAINT [FK_triggers_trigger_types] FOREIGN KEY([trigger_type_id])
REFERENCES [dbo].[trigger_types] ([id])
GO
ALTER TABLE [dbo].[triggers] CHECK CONSTRAINT [FK_triggers_trigger_types]
GO
ALTER TABLE [dbo].[triggers]  WITH CHECK ADD  CONSTRAINT [FK_triggers_users_1] FOREIGN KEY([created_by])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[triggers] CHECK CONSTRAINT [FK_triggers_users_1]
GO
ALTER TABLE [dbo].[triggers]  WITH CHECK ADD  CONSTRAINT [FK_triggers_users_2] FOREIGN KEY([modified_by])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[triggers] CHECK CONSTRAINT [FK_triggers_users_2]
GO
ALTER TABLE [dbo].[user_activity_logs]  WITH CHECK ADD  CONSTRAINT [fk_user_activity_logs_5] FOREIGN KEY([client_id])
REFERENCES [dbo].[clients] ([id])
ON UPDATE CASCADE
GO
ALTER TABLE [dbo].[user_activity_logs] CHECK CONSTRAINT [fk_user_activity_logs_5]
GO
ALTER TABLE [dbo].[user_activity_logs]  WITH CHECK ADD  CONSTRAINT [user_activity_logs_legal_cases_ibfk_1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
ON UPDATE CASCADE
GO
ALTER TABLE [dbo].[user_activity_logs] CHECK CONSTRAINT [user_activity_logs_legal_cases_ibfk_1]
GO
ALTER TABLE [dbo].[user_activity_logs]  WITH CHECK ADD  CONSTRAINT [user_activity_logs_tasks_ibfk_1] FOREIGN KEY([task_id])
REFERENCES [dbo].[tasks] ([id])
ON UPDATE CASCADE
GO
ALTER TABLE [dbo].[user_activity_logs] CHECK CONSTRAINT [user_activity_logs_tasks_ibfk_1]
GO
ALTER TABLE [dbo].[user_activity_logs]  WITH CHECK ADD  CONSTRAINT [user_activity_logs_time_internal_statuses_ibfk_1] FOREIGN KEY([time_internal_status_id])
REFERENCES [dbo].[time_internal_statuses] ([id])
GO
ALTER TABLE [dbo].[user_activity_logs] CHECK CONSTRAINT [user_activity_logs_time_internal_statuses_ibfk_1]
GO
ALTER TABLE [dbo].[user_activity_logs]  WITH CHECK ADD  CONSTRAINT [user_activity_logs_time_types_ibfk_1] FOREIGN KEY([time_type_id])
REFERENCES [dbo].[time_types] ([id])
GO
ALTER TABLE [dbo].[user_activity_logs] CHECK CONSTRAINT [user_activity_logs_time_types_ibfk_1]
GO
ALTER TABLE [dbo].[user_activity_logs]  WITH CHECK ADD  CONSTRAINT [user_activity_logs_users_ibfk_1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
GO
ALTER TABLE [dbo].[user_activity_logs] CHECK CONSTRAINT [user_activity_logs_users_ibfk_1]
GO
ALTER TABLE [dbo].[user_api_keys]  WITH CHECK ADD  CONSTRAINT [user_api_keys_ibfk_1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_api_keys] CHECK CONSTRAINT [user_api_keys_ibfk_1]
GO
ALTER TABLE [dbo].[user_changes]  WITH CHECK ADD  CONSTRAINT [user_changes_ibfk_1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_changes] CHECK CONSTRAINT [user_changes_ibfk_1]
GO
ALTER TABLE [dbo].[user_changes]  WITH CHECK ADD  CONSTRAINT [user_changes_ibfk_2] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_changes] CHECK CONSTRAINT [user_changes_ibfk_2]
GO
ALTER TABLE [dbo].[user_changes_authorization]  WITH CHECK ADD  CONSTRAINT [user_changes_authorization_ibfk_1] FOREIGN KEY([affectedUserId])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_changes_authorization] CHECK CONSTRAINT [user_changes_authorization_ibfk_1]
GO
ALTER TABLE [dbo].[user_changes_authorization]  WITH CHECK ADD  CONSTRAINT [user_changes_authorization_ibfk_2] FOREIGN KEY([makerId])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_changes_authorization] CHECK CONSTRAINT [user_changes_authorization_ibfk_2]
GO
ALTER TABLE [dbo].[user_changes_authorization]  WITH CHECK ADD  CONSTRAINT [user_changes_authorization_ibfk_3] FOREIGN KEY([checkerId])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_changes_authorization] CHECK CONSTRAINT [user_changes_authorization_ibfk_3]
GO
ALTER TABLE [dbo].[user_group_permissions]  WITH CHECK ADD  CONSTRAINT [user_group_permissions_ibfk_1] FOREIGN KEY([user_group_id])
REFERENCES [dbo].[user_groups] ([id])
GO
ALTER TABLE [dbo].[user_group_permissions] CHECK CONSTRAINT [user_group_permissions_ibfk_1]
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization]  WITH CHECK ADD  CONSTRAINT [user_group_permissions_changes_authorization_ibfk_1] FOREIGN KEY([affectedUserGroupId])
REFERENCES [dbo].[user_groups] ([id])
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization] CHECK CONSTRAINT [user_group_permissions_changes_authorization_ibfk_1]
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization]  WITH CHECK ADD  CONSTRAINT [user_group_permissions_changes_authorization_ibfk_2] FOREIGN KEY([makerId])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization] CHECK CONSTRAINT [user_group_permissions_changes_authorization_ibfk_2]
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization]  WITH CHECK ADD  CONSTRAINT [user_group_permissions_changes_authorization_ibfk_3] FOREIGN KEY([checkerId])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_group_permissions_changes_authorization] CHECK CONSTRAINT [user_group_permissions_changes_authorization_ibfk_3]
GO
ALTER TABLE [dbo].[user_groups_changes_authorization]  WITH CHECK ADD  CONSTRAINT [user_groups_changes_authorization_ibfk_1] FOREIGN KEY([affectedUserGroupId])
REFERENCES [dbo].[user_groups] ([id])
GO
ALTER TABLE [dbo].[user_groups_changes_authorization] CHECK CONSTRAINT [user_groups_changes_authorization_ibfk_1]
GO
ALTER TABLE [dbo].[user_groups_changes_authorization]  WITH CHECK ADD  CONSTRAINT [user_groups_changes_authorization_ibfk_2] FOREIGN KEY([makerId])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_groups_changes_authorization] CHECK CONSTRAINT [user_groups_changes_authorization_ibfk_2]
GO
ALTER TABLE [dbo].[user_groups_changes_authorization]  WITH CHECK ADD  CONSTRAINT [user_groups_changes_authorization_ibfk_3] FOREIGN KEY([checkerId])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_groups_changes_authorization] CHECK CONSTRAINT [user_groups_changes_authorization_ibfk_3]
GO
ALTER TABLE [dbo].[user_passwords]  WITH CHECK ADD  CONSTRAINT [user_passwords_ibfk_2] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[user_passwords] CHECK CONSTRAINT [user_passwords_ibfk_2]
GO
ALTER TABLE [dbo].[user_preferences]  WITH CHECK ADD  CONSTRAINT [fk_user_preferences_users1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_preferences] CHECK CONSTRAINT [fk_user_preferences_users1]
GO
ALTER TABLE [dbo].[user_profiles]  WITH CHECK ADD  CONSTRAINT [fk_user_profiles_users] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_profiles] CHECK CONSTRAINT [fk_user_profiles_users]
GO
ALTER TABLE [dbo].[user_profiles]  WITH CHECK ADD  CONSTRAINT [fk1_user_profiles_users] FOREIGN KEY([seniority_level_id])
REFERENCES [dbo].[seniority_levels] ([id])
GO
ALTER TABLE [dbo].[user_profiles] CHECK CONSTRAINT [fk1_user_profiles_users]
GO
ALTER TABLE [dbo].[user_rate_per_hour]  WITH CHECK ADD  CONSTRAINT [user_rate_per_hour_ibfk_1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_rate_per_hour] CHECK CONSTRAINT [user_rate_per_hour_ibfk_1]
GO
ALTER TABLE [dbo].[user_rate_per_hour]  WITH CHECK ADD  CONSTRAINT [user_rate_per_hour_ibfk_2] FOREIGN KEY([organization_id])
REFERENCES [dbo].[organizations] ([id])
GO
ALTER TABLE [dbo].[user_rate_per_hour] CHECK CONSTRAINT [user_rate_per_hour_ibfk_2]
GO
ALTER TABLE [dbo].[user_rate_per_hour_per_case]  WITH CHECK ADD  CONSTRAINT [user_rate_per_hour_per_case_ibfk_1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_rate_per_hour_per_case] CHECK CONSTRAINT [user_rate_per_hour_per_case_ibfk_1]
GO
ALTER TABLE [dbo].[user_rate_per_hour_per_case]  WITH CHECK ADD  CONSTRAINT [user_rate_per_hour_per_case_ibfk_2] FOREIGN KEY([case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[user_rate_per_hour_per_case] CHECK CONSTRAINT [user_rate_per_hour_per_case_ibfk_2]
GO
ALTER TABLE [dbo].[user_rate_per_hour_per_case]  WITH CHECK ADD  CONSTRAINT [user_rate_per_hour_per_case_ibfk_3] FOREIGN KEY([organization_id])
REFERENCES [dbo].[organizations] ([id])
GO
ALTER TABLE [dbo].[user_rate_per_hour_per_case] CHECK CONSTRAINT [user_rate_per_hour_per_case_ibfk_3]
GO
ALTER TABLE [dbo].[user_reports]  WITH CHECK ADD  CONSTRAINT [user_reports_ibfk_1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[user_reports] CHECK CONSTRAINT [user_reports_ibfk_1]
GO
ALTER TABLE [dbo].[user_signature_attachments]  WITH CHECK ADD  CONSTRAINT [fk_user_signature_attachments_1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[user_signature_attachments] CHECK CONSTRAINT [fk_user_signature_attachments_1]
GO
ALTER TABLE [dbo].[users]  WITH CHECK ADD  CONSTRAINT [users_ibfk_1] FOREIGN KEY([user_group_id])
REFERENCES [dbo].[user_groups] ([id])
GO
ALTER TABLE [dbo].[users] CHECK CONSTRAINT [users_ibfk_1]
GO
ALTER TABLE [dbo].[users]  WITH CHECK ADD  CONSTRAINT [users_ibfk_2] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[users] CHECK CONSTRAINT [users_ibfk_2]
GO
ALTER TABLE [dbo].[vendors]  WITH CHECK ADD  CONSTRAINT [vendors_ibfk_1] FOREIGN KEY([company_id])
REFERENCES [dbo].[companies] ([id])
GO
ALTER TABLE [dbo].[vendors] CHECK CONSTRAINT [vendors_ibfk_1]
GO
ALTER TABLE [dbo].[vendors]  WITH CHECK ADD  CONSTRAINT [vendors_ibfk_2] FOREIGN KEY([contact_id])
REFERENCES [dbo].[contacts] ([id])
GO
ALTER TABLE [dbo].[vendors] CHECK CONSTRAINT [vendors_ibfk_2]
GO
ALTER TABLE [dbo].[vendors]  WITH CHECK ADD  CONSTRAINT [vendors_ibfk_3] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[vendors] CHECK CONSTRAINT [vendors_ibfk_3]
GO
ALTER TABLE [dbo].[vendors]  WITH CHECK ADD  CONSTRAINT [vendors_ibfk_4] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[vendors] CHECK CONSTRAINT [vendors_ibfk_4]
GO
ALTER TABLE [dbo].[voucher_details]  WITH CHECK ADD  CONSTRAINT [voucher_details_ibfk_1] FOREIGN KEY([voucher_header_id])
REFERENCES [dbo].[voucher_headers] ([id])
GO
ALTER TABLE [dbo].[voucher_details] CHECK CONSTRAINT [voucher_details_ibfk_1]
GO
ALTER TABLE [dbo].[voucher_details]  WITH CHECK ADD  CONSTRAINT [voucher_details_ibfk_2] FOREIGN KEY([account_id])
REFERENCES [dbo].[accounts] ([id])
GO
ALTER TABLE [dbo].[voucher_details] CHECK CONSTRAINT [voucher_details_ibfk_2]
GO
ALTER TABLE [dbo].[voucher_headers]  WITH CHECK ADD  CONSTRAINT [voucher_headers_ibfk_1] FOREIGN KEY([organization_id])
REFERENCES [dbo].[organizations] ([id])
GO
ALTER TABLE [dbo].[voucher_headers] CHECK CONSTRAINT [voucher_headers_ibfk_1]
GO
ALTER TABLE [dbo].[voucher_related_cases]  WITH CHECK ADD  CONSTRAINT [fk_voucher_related_cases_1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[voucher_related_cases] CHECK CONSTRAINT [fk_voucher_related_cases_1]
GO
ALTER TABLE [dbo].[voucher_related_cases]  WITH CHECK ADD  CONSTRAINT [fk_voucher_related_cases_2] FOREIGN KEY([voucher_header_id])
REFERENCES [dbo].[voucher_headers] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[voucher_related_cases] CHECK CONSTRAINT [fk_voucher_related_cases_2]
GO
ALTER TABLE [dbo].[workflow_status_relation]  WITH CHECK ADD  CONSTRAINT [fk_workflow_status_relation_1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[workflows] ([id])
GO
ALTER TABLE [dbo].[workflow_status_relation] CHECK CONSTRAINT [fk_workflow_status_relation_1]
GO
ALTER TABLE [dbo].[workflow_status_transition]  WITH CHECK ADD  CONSTRAINT [fk_workflow_status_transition1] FOREIGN KEY([workflow_id])
REFERENCES [dbo].[workflows] ([id])
GO
ALTER TABLE [dbo].[workflow_status_transition] CHECK CONSTRAINT [fk_workflow_status_transition1]
GO
ALTER TABLE [dbo].[workflow_status_transition_history]  WITH CHECK ADD  CONSTRAINT [fk_workflow_status_transition_history1] FOREIGN KEY([legal_case_id])
REFERENCES [dbo].[legal_cases] ([id])
GO
ALTER TABLE [dbo].[workflow_status_transition_history] CHECK CONSTRAINT [fk_workflow_status_transition_history1]
GO
ALTER TABLE [dbo].[workflow_status_transition_history]  WITH CHECK ADD  CONSTRAINT [fk_workflow_status_transition_history2] FOREIGN KEY([fromStep])
REFERENCES [dbo].[workflow_status] ([id])
GO
ALTER TABLE [dbo].[workflow_status_transition_history] CHECK CONSTRAINT [fk_workflow_status_transition_history2]
GO
ALTER TABLE [dbo].[workflow_status_transition_history]  WITH CHECK ADD  CONSTRAINT [fk_workflow_status_transition_history3] FOREIGN KEY([toStep])
REFERENCES [dbo].[workflow_status] ([id])
GO
ALTER TABLE [dbo].[workflow_status_transition_history] CHECK CONSTRAINT [fk_workflow_status_transition_history3]
GO
ALTER TABLE [dbo].[workflow_status_transition_permissions]  WITH CHECK ADD  CONSTRAINT [fk_workflow_status_transition_permissions_1] FOREIGN KEY([transition])
REFERENCES [dbo].[workflow_status_transition] ([id])
GO
ALTER TABLE [dbo].[workflow_status_transition_permissions] CHECK CONSTRAINT [fk_workflow_status_transition_permissions_1]
GO
ALTER TABLE [dbo].[workflow_status_transition_screen_fields]  WITH CHECK ADD  CONSTRAINT [fk_workflow_status_transition_screen_fields_1] FOREIGN KEY([transition])
REFERENCES [dbo].[workflow_status_transition] ([id])
GO
ALTER TABLE [dbo].[workflow_status_transition_screen_fields] CHECK CONSTRAINT [fk_workflow_status_transition_screen_fields_1]
GO
ALTER TABLE [dbo].[workflows]  WITH CHECK ADD  CONSTRAINT [fk_workflows1] FOREIGN KEY([createdBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[workflows] CHECK CONSTRAINT [fk_workflows1]
GO
ALTER TABLE [dbo].[workflows]  WITH CHECK ADD  CONSTRAINT [fk_workflows2] FOREIGN KEY([modifiedBy])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[workflows] CHECK CONSTRAINT [fk_workflows2]
GO
ALTER TABLE [dbo].[accounts]  WITH CHECK ADD CHECK  (([model_type]='partner' OR [model_type]='supplier' OR [model_type]='client' OR [model_type]='internal'))
GO
ALTER TABLE [dbo].[accounts]  WITH CHECK ADD CHECK  (([model_type]='partner' OR [model_type]='supplier' OR [model_type]='client' OR [model_type]='internal'))
GO
ALTER TABLE [dbo].[accounts]  WITH CHECK ADD CHECK  (([systemAccount]='no' OR [systemAccount]='yes'))
GO
ALTER TABLE [dbo].[accounts]  WITH CHECK ADD CHECK  (([systemAccount]='no' OR [systemAccount]='yes'))
GO
ALTER TABLE [dbo].[accounts_types]  WITH CHECK ADD CHECK  (([type]='Third Party' OR [type]='Other' OR [type]='Liability' OR [type]='Income' OR [type]='Expense' OR [type]='Equity' OR [type]='Asset'))
GO
ALTER TABLE [dbo].[accounts_types]  WITH CHECK ADD CHECK  (([type]='Third Party' OR [type]='Other' OR [type]='Liability' OR [type]='Income' OR [type]='Expense' OR [type]='Equity' OR [type]='Asset'))
GO
ALTER TABLE [dbo].[advisor_tasks]  WITH CHECK ADD CHECK  (([archived]='no' OR [archived]='yes'))
GO
ALTER TABLE [dbo].[advisor_tasks]  WITH CHECK ADD CHECK  (([archived]='no' OR [archived]='yes'))
GO
ALTER TABLE [dbo].[advisor_tasks]  WITH CHECK ADD CHECK  (([priority]='low' OR [priority]='medium' OR [priority]='high' OR [priority]='critical'))
GO
ALTER TABLE [dbo].[advisor_tasks]  WITH CHECK ADD CHECK  (([priority]='low' OR [priority]='medium' OR [priority]='high' OR [priority]='critical'))
GO
ALTER TABLE [dbo].[advisor_user_activity_logs]  WITH CHECK ADD CHECK  (([timeStatus]='billable' OR [timeStatus]='internal' OR [timeStatus]=''))
GO
ALTER TABLE [dbo].[advisor_user_activity_logs]  WITH CHECK ADD CHECK  (([timeStatus]='billable' OR [timeStatus]='internal' OR [timeStatus]=''))
GO
ALTER TABLE [dbo].[bill_headers]  WITH CHECK ADD CHECK  (([status]='paid' OR [status]='partially paid' OR [status]='open'))
GO
ALTER TABLE [dbo].[bill_headers]  WITH CHECK ADD CHECK  (([status]='paid' OR [status]='partially paid' OR [status]='open'))
GO
ALTER TABLE [dbo].[bill_payments]  WITH CHECK ADD CHECK  (([paymentMethod]='Online payment' OR [paymentMethod]='Other' OR [paymentMethod]='Credit Card' OR [paymentMethod]='Cheque' OR [paymentMethod]='Cash' OR [paymentMethod]='Bank Transfer'))
GO
ALTER TABLE [dbo].[bill_payments]  WITH CHECK ADD CHECK  (([paymentMethod]='Online payment' OR [paymentMethod]='Other' OR [paymentMethod]='Credit Card' OR [paymentMethod]='Cheque' OR [paymentMethod]='Cash' OR [paymentMethod]='Bank Transfer'))
GO
ALTER TABLE [dbo].[case_comment_attachments]  WITH CHECK ADD CHECK  (([uploaded]='No' OR [uploaded]='Yes'))
GO
ALTER TABLE [dbo].[case_comment_attachments]  WITH CHECK ADD CHECK  (([uploaded]='No' OR [uploaded]='Yes'))
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD CHECK  (([capitalVisualizeDecimals]='no' OR [capitalVisualizeDecimals]='yes'))
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD CHECK  (([capitalVisualizeDecimals]='no' OR [capitalVisualizeDecimals]='yes'))
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD CHECK  (([category]='Group' OR [category]='Internal'))
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD CHECK  (([category]='Group' OR [category]='Internal'))
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD CHECK  (([ownedByGroup]='Yes' OR [ownedByGroup]='No' OR [ownedByGroup]=''))
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD CHECK  (([ownedByGroup]='Yes' OR [ownedByGroup]='No' OR [ownedByGroup]=''))
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD CHECK  (([sheerLebanese]='Yes' OR [sheerLebanese]='No' OR [sheerLebanese]=''))
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD CHECK  (([sheerLebanese]='Yes' OR [sheerLebanese]='No' OR [sheerLebanese]=''))
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD CHECK  (([status]='Inactive' OR [status]='Active'))
GO
ALTER TABLE [dbo].[companies]  WITH CHECK ADD CHECK  (([status]='Inactive' OR [status]='Active'))
GO
ALTER TABLE [dbo].[company_signature_authorities]  WITH CHECK ADD CHECK  (([sa_type]='contacts' OR [sa_type]='companies'))
GO
ALTER TABLE [dbo].[company_signature_authorities]  WITH CHECK ADD CHECK  (([sa_type]='contacts' OR [sa_type]='companies'))
GO
ALTER TABLE [dbo].[contacts]  WITH CHECK ADD CHECK  (([gender]='Female' OR [gender]='Male' OR [gender]=''))
GO
ALTER TABLE [dbo].[contacts]  WITH CHECK ADD CHECK  (([gender]='Female' OR [gender]='Male' OR [gender]=''))
GO
ALTER TABLE [dbo].[contacts]  WITH CHECK ADD CHECK  (([isLawyer]='yes' OR [isLawyer]='no'))
GO
ALTER TABLE [dbo].[contacts]  WITH CHECK ADD CHECK  (([isLawyer]='yes' OR [isLawyer]='no'))
GO
ALTER TABLE [dbo].[contacts]  WITH CHECK ADD CHECK  (([lawyerForCompany]='no' OR [lawyerForCompany]='yes'))
GO
ALTER TABLE [dbo].[contacts]  WITH CHECK ADD CHECK  (([lawyerForCompany]='no' OR [lawyerForCompany]='yes'))
GO
ALTER TABLE [dbo].[contacts]  WITH CHECK ADD CHECK  (([status]='Inactive' OR [status]='Active'))
GO
ALTER TABLE [dbo].[contacts]  WITH CHECK ADD CHECK  (([status]='Inactive' OR [status]='Active'))
GO
ALTER TABLE [dbo].[contract_numbering_formats]  WITH CHECK ADD  CONSTRAINT [CK_sequence_reset] CHECK  (([sequence_reset]='daily' OR [sequence_reset]='yearly' OR [sequence_reset]='monthly' OR [sequence_reset]='never'))
GO
ALTER TABLE [dbo].[contract_numbering_formats] CHECK CONSTRAINT [CK_sequence_reset]
GO
ALTER TABLE [dbo].[conveyancing_instruments]  WITH CHECK ADD  CONSTRAINT [CHK_amount_approved] CHECK  (([amount_approved]>=(0)))
GO
ALTER TABLE [dbo].[conveyancing_instruments] CHECK CONSTRAINT [CHK_amount_approved]
GO
ALTER TABLE [dbo].[conveyancing_instruments]  WITH CHECK ADD  CONSTRAINT [CHK_amount_requested] CHECK  (([amount_requested]>=(0)))
GO
ALTER TABLE [dbo].[conveyancing_instruments] CHECK CONSTRAINT [CHK_amount_requested]
GO
ALTER TABLE [dbo].[conveyancing_instruments]  WITH CHECK ADD  CONSTRAINT [CHK_dates] CHECK  (([date_initiated]<=isnull([due_date],'9999-12-31') AND [date_initiated]<=isnull([completion_date],'9999-12-31') AND isnull([due_date],'9999-12-31')>=isnull([completion_date],'1900-01-01')))
GO
ALTER TABLE [dbo].[conveyancing_instruments] CHECK CONSTRAINT [CHK_dates]
GO
ALTER TABLE [dbo].[conveyancing_instruments]  WITH CHECK ADD  CONSTRAINT [CHK_priority] CHECK  (([priority]>=(1) AND [priority]<=(5)))
GO
ALTER TABLE [dbo].[conveyancing_instruments] CHECK CONSTRAINT [CHK_priority]
GO
ALTER TABLE [dbo].[conveyancing_instruments]  WITH CHECK ADD  CONSTRAINT [CHK_property_value] CHECK  (([property_value]>=(0)))
GO
ALTER TABLE [dbo].[conveyancing_instruments] CHECK CONSTRAINT [CHK_property_value]
GO
ALTER TABLE [dbo].[conveyancing_stage_progress]  WITH NOCHECK ADD  CONSTRAINT [CK_conveyancing_date_logic] CHECK  (([start_date] IS NULL OR [completion_date] IS NULL OR [start_date]<=[completion_date]))
GO
ALTER TABLE [dbo].[conveyancing_stage_progress] NOCHECK CONSTRAINT [CK_conveyancing_date_logic]
GO
ALTER TABLE [dbo].[correspondence_workflow]  WITH CHECK ADD  CONSTRAINT [CHK_workflow_dates] CHECK  (([createdOn]<=isnull([completion_date],'9999-12-31')))
GO
ALTER TABLE [dbo].[correspondence_workflow] CHECK CONSTRAINT [CHK_workflow_dates]
GO
ALTER TABLE [dbo].[correspondences]  WITH CHECK ADD  CONSTRAINT [CHK_correspondence_dates] CHECK  (([date_received] IS NULL OR [document_date] IS NULL OR [date_received]>=[document_date]))
GO
ALTER TABLE [dbo].[correspondences] CHECK CONSTRAINT [CHK_correspondence_dates]
GO
ALTER TABLE [dbo].[correspondences]  WITH CHECK ADD  CONSTRAINT [CHK_correspondence_priority] CHECK  (([priority] IS NULL OR ([priority]='Critical' OR [priority]='High' OR [priority]='Medium' OR [priority]='Low')))
GO
ALTER TABLE [dbo].[correspondences] CHECK CONSTRAINT [CHK_correspondence_priority]
GO
ALTER TABLE [dbo].[credit_note_headers]  WITH CHECK ADD CHECK  (([paid_status]='cancelled' OR [paid_status]='refund' OR [paid_status]='partially refund' OR [paid_status]='open' OR [paid_status]='draft'))
GO
ALTER TABLE [dbo].[credit_note_headers]  WITH CHECK ADD CHECK  (([paid_status]='cancelled' OR [paid_status]='refund' OR [paid_status]='partially refund' OR [paid_status]='open' OR [paid_status]='draft'))
GO
ALTER TABLE [dbo].[credit_note_refunds]  WITH CHECK ADD CHECK  (([refund_method]='Trust Account' OR [refund_method]='Online payment' OR [refund_method]='Other' OR [refund_method]='Credit Card' OR [refund_method]='Cheque' OR [refund_method]='Cash' OR [refund_method]='Bank Transfer'))
GO
ALTER TABLE [dbo].[credit_note_refunds]  WITH CHECK ADD CHECK  (([refund_method]='Trust Account' OR [refund_method]='Online payment' OR [refund_method]='Other' OR [refund_method]='Credit Card' OR [refund_method]='Cheque' OR [refund_method]='Cash' OR [refund_method]='Bank Transfer'))
GO
ALTER TABLE [dbo].[events]  WITH CHECK ADD CHECK  (([priority]='low' OR [priority]='medium' OR [priority]='high' OR [priority]='critical'))
GO
ALTER TABLE [dbo].[events]  WITH CHECK ADD CHECK  (([priority]='low' OR [priority]='medium' OR [priority]='high' OR [priority]='critical'))
GO
ALTER TABLE [dbo].[expenses]  WITH CHECK ADD CHECK  (([billingStatus]='to-invoice' OR [billingStatus]='reimbursed' OR [billingStatus]='not-set' OR [billingStatus]='non-billable' OR [billingStatus]='invoiced' OR [billingStatus]='internal'))
GO
ALTER TABLE [dbo].[expenses]  WITH CHECK ADD CHECK  (([billingStatus]='to-invoice' OR [billingStatus]='reimbursed' OR [billingStatus]='not-set' OR [billingStatus]='non-billable' OR [billingStatus]='invoiced' OR [billingStatus]='internal'))
GO
ALTER TABLE [dbo].[invoice_headers]  WITH CHECK ADD CHECK  (([paidStatus]='cancelled' OR [paidStatus]='paid' OR [paidStatus]='partially paid' OR [paidStatus]='open' OR [paidStatus]='draft'))
GO
ALTER TABLE [dbo].[invoice_headers]  WITH CHECK ADD CHECK  (([paidStatus]='cancelled' OR [paidStatus]='paid' OR [paidStatus]='partially paid' OR [paidStatus]='open' OR [paidStatus]='draft'))
GO
ALTER TABLE [dbo].[invoice_payments]  WITH CHECK ADD CHECK  (([paymentMethod]='Trust Account' OR [paymentMethod]='Online payment' OR [paymentMethod]='Other' OR [paymentMethod]='Credit Card' OR [paymentMethod]='Cheque' OR [paymentMethod]='Cash' OR [paymentMethod]='Bank Transfer'))
GO
ALTER TABLE [dbo].[invoice_payments]  WITH CHECK ADD CHECK  (([paymentMethod]='Trust Account' OR [paymentMethod]='Online payment' OR [paymentMethod]='Other' OR [paymentMethod]='Credit Card' OR [paymentMethod]='Cheque' OR [paymentMethod]='Cash' OR [paymentMethod]='Bank Transfer'))
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD CHECK  (([archived]='no' OR [archived]='yes'))
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD CHECK  (([archived]='no' OR [archived]='yes'))
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD CHECK  (([externalizeLawyers]='no' OR [externalizeLawyers]='yes'))
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD CHECK  (([externalizeLawyers]='no' OR [externalizeLawyers]='yes'))
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD CHECK  (([priority]='low' OR [priority]='medium' OR [priority]='high' OR [priority]='critical'))
GO
ALTER TABLE [dbo].[legal_cases]  WITH CHECK ADD CHECK  (([priority]='low' OR [priority]='medium' OR [priority]='high' OR [priority]='critical'))
GO
ALTER TABLE [dbo].[login_history_log_archives]  WITH CHECK ADD CHECK  (([action]='logout' OR [action]='login'))
GO
ALTER TABLE [dbo].[login_history_log_archives]  WITH CHECK ADD CHECK  (([action]='logout' OR [action]='login'))
GO
ALTER TABLE [dbo].[login_history_logs]  WITH CHECK ADD CHECK  (([action]='logout' OR [action]='login'))
GO
ALTER TABLE [dbo].[login_history_logs]  WITH CHECK ADD CHECK  (([action]='logout' OR [action]='login'))
GO
ALTER TABLE [dbo].[notifications]  WITH CHECK ADD CHECK  (([status]='dismissed' OR [status]='unseen' OR [status]='seen'))
GO
ALTER TABLE [dbo].[notifications]  WITH CHECK ADD CHECK  (([status]='dismissed' OR [status]='unseen' OR [status]='seen'))
GO
ALTER TABLE [dbo].[opinions]  WITH CHECK ADD  CONSTRAINT [CHK_opinions_archived] CHECK  (([archived]='yes' OR [archived]='no'))
GO
ALTER TABLE [dbo].[opinions] CHECK CONSTRAINT [CHK_opinions_archived]
GO
ALTER TABLE [dbo].[opinions]  WITH CHECK ADD  CONSTRAINT [CHK_opinions_priority] CHECK  (([priority]='critical' OR [priority]='high' OR [priority]='medium' OR [priority]='low'))
GO
ALTER TABLE [dbo].[opinions] CHECK CONSTRAINT [CHK_opinions_priority]
GO
ALTER TABLE [dbo].[organizations]  WITH CHECK ADD CHECK  (([status]='Inactive' OR [status]='Active'))
GO
ALTER TABLE [dbo].[organizations]  WITH CHECK ADD CHECK  (([status]='Inactive' OR [status]='Active'))
GO
ALTER TABLE [dbo].[partners]  WITH CHECK ADD CHECK  (([isThirdParty]='yes' OR [isThirdParty]='no'))
GO
ALTER TABLE [dbo].[partners]  WITH CHECK ADD CHECK  (([isThirdParty]='yes' OR [isThirdParty]='no'))
GO
ALTER TABLE [dbo].[preferred_shares]  WITH CHECK ADD CHECK  (([retrieved]='no' OR [retrieved]='yes'))
GO
ALTER TABLE [dbo].[preferred_shares]  WITH CHECK ADD CHECK  (([retrieved]='no' OR [retrieved]='yes'))
GO
ALTER TABLE [dbo].[provider_groups_users]  WITH CHECK ADD CHECK  (([isDefault]='no' OR [isDefault]='yes'))
GO
ALTER TABLE [dbo].[provider_groups_users]  WITH CHECK ADD CHECK  (([isDefault]='no' OR [isDefault]='yes'))
GO
ALTER TABLE [dbo].[reminders]  WITH CHECK ADD CHECK  (([status]='Open' OR [status]='Dismissed'))
GO
ALTER TABLE [dbo].[reminders]  WITH CHECK ADD CHECK  (([status]='Open' OR [status]='Dismissed'))
GO
ALTER TABLE [dbo].[shares_movements]  WITH CHECK ADD CHECK  (([type]='transfer' OR [type]='increase in capital - profit' OR [type]='increase in capital' OR [type]='incorporation' OR [type]=''))
GO
ALTER TABLE [dbo].[shares_movements]  WITH CHECK ADD CHECK  (([type]='transfer' OR [type]='increase in capital - profit' OR [type]='increase in capital' OR [type]='incorporation' OR [type]=''))
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD CHECK  (([archived]='no' OR [archived]='yes'))
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD CHECK  (([archived]='no' OR [archived]='yes'))
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD CHECK  (([priority]='low' OR [priority]='medium' OR [priority]='high' OR [priority]='critical'))
GO
ALTER TABLE [dbo].[tasks]  WITH CHECK ADD CHECK  (([priority]='low' OR [priority]='medium' OR [priority]='high' OR [priority]='critical'))
GO
ALTER TABLE [dbo].[user_activity_log_invoicing_statuses]  WITH CHECK ADD CHECK  (([log_invoicing_statuses]='reimbursed' OR [log_invoicing_statuses]='invoiced' OR [log_invoicing_statuses]='to-invoice' OR [log_invoicing_statuses]='non-billable'))
GO
ALTER TABLE [dbo].[user_activity_log_invoicing_statuses]  WITH CHECK ADD CHECK  (([log_invoicing_statuses]='reimbursed' OR [log_invoicing_statuses]='invoiced' OR [log_invoicing_statuses]='to-invoice' OR [log_invoicing_statuses]='non-billable'))
GO
ALTER TABLE [dbo].[user_activity_logs]  WITH CHECK ADD CHECK  (([timeStatus]='billable' OR [timeStatus]='internal' OR [timeStatus]=''))
GO
ALTER TABLE [dbo].[user_activity_logs]  WITH CHECK ADD CHECK  (([timeStatus]='billable' OR [timeStatus]='internal' OR [timeStatus]=''))
GO
ALTER TABLE [dbo].[user_profiles]  WITH CHECK ADD CHECK  (([gender]='Female' OR [gender]='Male' OR [gender]=''))
GO
ALTER TABLE [dbo].[user_profiles]  WITH CHECK ADD CHECK  (([gender]='Female' OR [gender]='Male' OR [gender]=''))
GO
ALTER TABLE [dbo].[user_profiles]  WITH CHECK ADD CHECK  (([isLawyer]='no' OR [isLawyer]='yes'))
GO
ALTER TABLE [dbo].[user_profiles]  WITH CHECK ADD CHECK  (([isLawyer]='no' OR [isLawyer]='yes'))
GO
ALTER TABLE [dbo].[user_profiles]  WITH CHECK ADD CHECK  (([status]='Inactive' OR [status]='Active'))
GO
ALTER TABLE [dbo].[user_profiles]  WITH CHECK ADD CHECK  (([status]='Inactive' OR [status]='Active'))
GO
ALTER TABLE [dbo].[user_profiles]  WITH CHECK ADD CHECK  (([title]='Sen' OR [title]='Judge' OR [title]='Me' OR [title]='Dr' OR [title]='Miss' OR [title]='Mrs' OR [title]='Mr' OR [title]=''))
GO
ALTER TABLE [dbo].[user_profiles]  WITH CHECK ADD CHECK  (([title]='Sen' OR [title]='Judge' OR [title]='Me' OR [title]='Dr' OR [title]='Miss' OR [title]='Mrs' OR [title]='Mr' OR [title]=''))
GO
/****** Object:  StoredProcedure [dbo].[sp_add_workflow_to_new_contracts]    Script Date: 1/16/2026 12:39:18 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

            CREATE   PROCEDURE [dbo].[sp_add_workflow_to_new_contracts]
            AS
            BEGIN
                SET NOCOUNT ON;
                
                -- Check if there is an active workflow to assign to new contracts
                IF NOT EXISTS(SELECT 1 FROM contract_workflows WHERE is_active = 1)
                BEGIN
                    RAISERROR('No active contract workflow found.', 16, 1);
                    RETURN;
                END

                -- Find the active workflow
                DECLARE @active_workflow_id BIGINT;
                SELECT TOP 1 @active_workflow_id = id FROM contract_workflows WHERE is_active = 1;

                -- Find the entry point of the active workflow
                DECLARE @entry_point_status_id BIGINT;
                SELECT TOP 1 @entry_point_status_id = id FROM contract_workflow_status WHERE workflow_id = @active_workflow_id AND is_entry_point = 1;
                
                IF @entry_point_status_id IS NULL
                BEGIN
                    RAISERROR('No entry point status found for the active workflow.', 16, 1);
                    RETURN;
                END
                
                -- Update any contracts with status_id = 1 (pending) to the new workflow's entry point status_id
                UPDATE contracts
                SET status_id = @entry_point_status_id
                WHERE status_id = 1;
            END;
        
GO
/****** Object:  StoredProcedure [dbo].[sp_get_new_contract_ref_number]    Script Date: 1/16/2026 12:39:18 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

            CREATE   PROCEDURE [dbo].[sp_get_new_contract_ref_number]
                @deptCode NVARCHAR(20) = NULL,
                @newRefNumber NVARCHAR(200) OUTPUT
            AS
            BEGIN
                SET NOCOUNT ON;

                DECLARE @formatId INT,
                        @pattern NVARCHAR(100),
                        @prefix NVARCHAR(20),
                        @suffix NVARCHAR(20),
                        @fixed_code NVARCHAR(20),
                        @sequence_reset NVARCHAR(20),
                        @sequence_length INT,
                        @last_sequence INT,
                        @last_reset_date DATE,
                        @next_sequence INT,
                        @today DATE = CAST(GETDATE() AS DATE),
                        @year CHAR(4) = FORMAT(GETDATE(), 'yyyy'),
                        @month CHAR(2) = FORMAT(GETDATE(), 'MM'),
                        @day CHAR(2) = FORMAT(GETDATE(), 'dd');

                -- 1. Get active format (lock it for update)
                SELECT TOP 1
                    @formatId = id,
                    @pattern = pattern,
                    @prefix = prefix,
                    @suffix = suffix,
                    @fixed_code = fixed_code,
                    @sequence_reset = sequence_reset,
                    @sequence_length = sequence_length,
                    @last_sequence = last_sequence,
                    @last_reset_date = last_reset_date
                FROM contract_numbering_formats WITH (UPDLOCK, ROWLOCK)
                WHERE is_active = 1;

                IF @formatId IS NULL
                BEGIN
                    RAISERROR('No active contract numbering format found.', 16, 1);
                    RETURN;
                END

                -- 2. Determine if reset is required
                DECLARE @resetNeeded BIT = 0;

                IF @sequence_reset = 'yearly'
                    AND (@last_reset_date IS NULL OR YEAR(@last_reset_date) <> YEAR(@today))
                    SET @resetNeeded = 1;
                ELSE IF @sequence_reset = 'monthly'
                    AND (@last_reset_date IS NULL OR FORMAT(@last_reset_date, 'yyyyMM') <> FORMAT(@today, 'yyyyMM'))
                    SET @resetNeeded = 1;
                ELSE IF @sequence_reset = 'daily'
                    AND (@last_reset_date IS NULL OR @last_reset_date <> @today)
                    SET @resetNeeded = 1;

                IF @resetNeeded = 1
                    SET @last_sequence = 0;

                -- 3. Increment sequence
                SET @next_sequence = @last_sequence + 1;

                -- 4. Update format table
                UPDATE contract_numbering_formats
                SET last_sequence = @next_sequence,
                    last_reset_date = @today
                WHERE id = @formatId;

                -- 5. Pad sequence
                DECLARE @seqStr NVARCHAR(20) = RIGHT(REPLICATE('0', @sequence_length) + CAST(@next_sequence AS NVARCHAR), @sequence_length);

                -- 6. Replace tokens in pattern
                SET @newRefNumber = REPLACE(@pattern, 'PREFIX', ISNULL(@prefix,''));
                SET @newRefNumber = REPLACE(@newRefNumber, 'SEQ', @seqStr);
                SET @newRefNumber = REPLACE(@newRefNumber, 'YYYY', @year);
                SET @newRefNumber = REPLACE(@newRefNumber, 'MM', @month);
                SET @newRefNumber = REPLACE(@newRefNumber, 'DD', @day);
                SET @newRefNumber = REPLACE(@newRefNumber, 'DEPT', ISNULL(@deptCode,'GEN'));
                SET @newRefNumber = REPLACE(@newRefNumber, 'SUFFIX', ISNULL(@suffix,''));
                SET @newRefNumber = REPLACE(@newRefNumber, 'FIXED', ISNULL(@fixed_code,''));
            END;
        
GO
USE [master]
GO
ALTER DATABASE [ca_prod] SET  READ_WRITE 
GO
