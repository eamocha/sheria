<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_5_4 extends CI_Controller
{
    use MigrationLogTrait;

    public $log_path = null;

    public function __construct()
    {
        parent::__construct();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->load->database();
        $this->write_log($this->log_path, 'start migration script');
    }

    public function index()
    {
        $this->create_advisor_email_templates_table();
        $this->create_advisor_email_template_languages_table();
        $this->insert_advisor_email_template_languages_sample_data();
        
        $this->write_log($this->log_path, 'End migration script');
    }

    public function create_advisor_email_templates_table()
    {
        $this->write_log($this->log_path, 'Started create_advisor_email_templates_table');

        if ($this->db->dbdriver === 'sqlsrv') {
            $this->db->query("IF OBJECT_ID('dbo.advisor_email_templates', 'U') IS NOT NULL DROP TABLE dbo.advisor_email_templates;");
            
            $this->db->query("
            CREATE TABLE advisor_email_templates (
                id BIGINT NOT NULL PRIMARY KEY IDENTITY,
                name nvarchar(50) NOT NULL,
                createdOn smalldatetime DEFAULT NULL,
                createdBy BIGINT DEFAULT NULL,
                modifiedOn smalldatetime DEFAULT NULL,
                modifiedBy BIGINT DEFAULT NULL
             );");

            $this->db->query('CREATE UNIQUE INDEX "name" ON advisor_email_templates("name")');
        } else {
            $this->db->query("DROP TABLE IF EXISTS `advisor_email_templates`;");
            
            $this->db->query("
            CREATE TABLE IF NOT EXISTS `advisor_email_templates` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(50) NOT NULL,
                `createdOn` DATETIME NULL,
                `createdBy` INT(10) NOT NULL,
                `modifiedOn` DATETIME NULL,
                `modifiedBy` INT(10) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `name` (`name`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
        
        $this->write_log($this->log_path, 'Done from create_advisor_email_templates_table');
    }

    public function create_advisor_email_template_languages_table()
    {
        $this->write_log($this->log_path, 'Started create_advisor_email_template_languages_table');

        if ($this->db->dbdriver === 'sqlsrv') {
            $this->db->query("IF OBJECT_ID('dbo.advisor_email_template_languages', 'U') IS NOT NULL DROP TABLE dbo.advisor_email_template_languages;");
            
            $this->db->query("
            CREATE TABLE advisor_email_template_languages (
                id BIGINT NOT NULL PRIMARY KEY IDENTITY,
                advisor_email_template_id BIGINT NOT NULL,
                language_id BIGINT NOT NULL,
                static_content nvarchar(max) NOT NULL,
                content nvarchar(max) NULL
              );");

            $this->db->query("ALTER TABLE advisor_email_template_languages
            ADD CONSTRAINT fk_advisor_email_template_languages_1 FOREIGN KEY (advisor_email_template_id) REFERENCES advisor_email_templates (id) ON DELETE NO ACTION ON UPDATE NO ACTION;");

            $this->db->query(" ALTER TABLE advisor_email_template_languages
            ADD CONSTRAINT fk_advisor_email_template_languages_2 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE NO ACTION ON UPDATE NO ACTION;");
        } else {
            $this->db->query("DROP TABLE IF EXISTS `advisor_email_template_languages`;");
            
            $this->db->query("
            CREATE TABLE IF NOT EXISTS `advisor_email_template_languages` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `advisor_email_template_id` int(10) NOT NULL,
                `language_id` int(10) unsigned NOT NULL,
                `static_content` LONGTEXT NOT NULL,
                `content` LONGTEXT NULL,
                PRIMARY KEY (`id`),
                KEY `advisor_email_template_id` (`advisor_email_template_id`),
                KEY `language_id` (`language_id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            
            $this->db->query("ALTER TABLE `advisor_email_template_languages`
            ADD CONSTRAINT `fk_advisor_email_template_languages_1` FOREIGN KEY (`advisor_email_template_id`) REFERENCES `advisor_email_templates` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");

            $this->db->query("ALTER TABLE `advisor_email_template_languages`
            ADD CONSTRAINT `fk_advisor_email_template_languages_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
        }
        
        $this->write_log($this->log_path, 'Done from create_advisor_email_template_languages_table');
    }

    public function insert_advisor_email_template_languages_sample_data()
    {
        $this->write_log($this->log_path, 'Started insert_advisor_email_template_languages_sample_data');

        if ($this->db->dbdriver === 'sqlsrv') {
            $this->db->query("INSERT INTO advisor_email_templates (name, createdOn, createdBy, modifiedOn, modifiedBy) VALUES
            ('advisor_registration_email', CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP, '1');");

            $this->db->query("INSERT INTO advisor_email_template_languages (advisor_email_template_id, language_id, static_content, content) VALUES
            (1, 1, 'Hi [advisor_name],<br/><br/>
            You have been added to [instance_name] system as an Advisor, here are your login credentials:<br/>
            E-mail: [advisor_email]<br/>
            Password: [random_password]<br/><br/>
            [instance_url]<br/><br/>
            [content]
            <br/><br/>', 'Thanks,'),
            (1, 2, 'عزيزي [advisor_name],<br/><br/>
            لقد تمت إضافتك ل [instance_name] كمستشار، وإليك تفاصيل حسابك:<br/>
            البريد الإلكتروني: [advisor_email]<br/>
            كلمة السر: [random_password]<br/><br/>
            [instance_url]<br/><br/>
            [content]
            <br/><br/>', 'شكراً,'),
            (1, 3, 'Salut [advisor_name],<br/><br/>
            Vous avez été ajouté au système [instance_name] en tant que conseiller, voici vos identifiants de connexion:<br/>
            E-mail: [advisor_email]<br/>
            Le mot de passe: [random_password]<br/><br/>
            [instance_url]<br/><br/>
            [content]
            <br/><br/>', 'Merci,'),
            (1, 4, 'Hola [advisor_name],<br/><br/>
            Ha sido agregado al sistema [instance_name] como asesor, aquí están sus credenciales de inicio de sesión:<br/>
            E-mail: [advisor_email]<br/>
            Contraseña: [random_password]<br/><br/>
            [instance_url]<br/><br/>
            [content]
            <br/><br/>', 'Gracias,');");
        } else {
            $this->db->query("INSERT INTO `advisor_email_templates` (`id`, `name`, `createdOn`, `createdBy`, `modifiedOn`, `modifiedBy`) VALUES
            (1, 'advisor_registration_email', CURRENT_TIMESTAMP, 1, CURRENT_TIMESTAMP, 1);");

            $this->db->query("INSERT INTO `advisor_email_template_languages` (`id`, `advisor_email_template_id`, `language_id`, `static_content`, `content`) VALUES
            (1, 1, 1, 'Hi [advisor_name],<br/><br/>
            You have been added to [instance_name] system as an Advisor, here are your login credentials:<br/>
            E-mail: [advisor_email]<br/>
            Password: [random_password]<br/><br/>
            [instance_url]<br/><br/>
            [content]
            <br/><br/>', 'Thanks,'),
            (2, 1, 2, 'عزيزي [advisor_name],<br/><br/>
            لقد تمت إضافتك ل [instance_name] كمستشار، وإليك تفاصيل حسابك:<br/>
            البريد الإلكتروني: [advisor_email]<br/>
            كلمة السر: [random_password]<br/><br/>
            [instance_url]<br/><br/>
            [content]
            <br/><br/>', 'شكراً,'),
            (3, 1, 3, 'Salut [advisor_name],<br/><br/>
            Vous avez été ajouté au système [instance_name] en tant que conseiller, voici vos identifiants de connexion:<br/>
            E-mail: [advisor_email]<br/>
            Le mot de passe: [random_password]<br/><br/>
            [instance_url]<br/><br/>
            [content]
            <br/><br/>', 'Merci,'),
            (4, 1, 4, 'Hola [advisor_name],<br/><br/>
            Ha sido agregado al sistema [instance_name] como asesor, aquí están sus credenciales de inicio de sesión:<br/>
            E-mail: [advisor_email]<br/>
            Contraseña: [random_password]<br/><br/>
            [instance_url]<br/><br/>
            [content]
            <br/><br/>', 'Gracias,');");
        }

        $this->write_log($this->log_path, 'Done from insert_advisor_email_template_languages_sample_data');
    }
}
