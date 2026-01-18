# App4Legal Docker

## Choosing WSL or HyperV
You can either choose to use WSL or HyperV with Docker (WSL 2 provides better performance than the legacy Hyper-V backend)
<br>
In case you want to use WSL, please follow these steps:
- Install it by following the instructions in this link: https://docs.microsoft.com/en-us/windows/wsl/install-manual
- After finishing the final step, please open your Linux Distro bash and git clone the project there

## Instructions
- Copy sample.env to .env
- Update variables in .env to your preference
- Open a terminal in the current directory and run the following 
`docker-compose up app4legal-web mysql adminer` or simply `docker-compose up`

The use of portainer (Docker Manager) is recommended just run this command
`docker run -d -p 9000:9000 -v /var/run/docker.sock:/var/run/docker.sock -v portainer_data:/data portainer/portainer`
and go to http://localhost:9000/


## What's included

- Apache 2.4.25
- PHP 7.1.24
- MySQL 5.6
- Adminer (PhpMyAdmin lightweight replacement)
- Microsoft SQL Server 2019 (or latest)

## What's missing

- Apache mod_headers
- PHP MSSQL Drivers
- Optimize php.ini values

## Notes

- This image is not for production use

- Please make sure to comment the following lines in all .htaccess files:
    * RewriteCond %{HTTPS} !on
    * RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [L]

    And remove `app4legal` so your base url will only be a forward slash `/`
 

## WSL Notes:

- To avoid permission issues when uploading files, in your Linux Distro Terminal, cd into the root directory of the project and run the following:
    * chmod -R 777 files
    * chown -R www-data:www-data files

    `NOTE`: You might need to run these commands as well for the `app` directory of each module (A4G, advisor-portal, api etc...) 

- Additional configuration for phpdocx library:
    * In phpdocxconfig.ini (in phpdocx-advanced, and phpdocx-premium-12.0) and phpdocx.ini (in application/config), modify the following:
        * path = '"/usr/bin/libreoffice"'
        * home_folder = "/var/www/html/files/tmp"
    * In TransformDocAdvLibreOffice.php (in phpdocx-advanced {line 180, 181}, and phpdocx-premium-12.0 {line 176}) and in OfficeConverter {line 130}, , add at the very beginning of the string or passthru function: 
        * "export HOME=" . getenv('Home') . " && " . 
        
        `NOTE`: in case getenv('Home') returns false, then hard code "/var/www/html/files/tmp".
    
    `NOTE`: Please be aware that these additional configurations should not be pushed and should only be kept on your local system