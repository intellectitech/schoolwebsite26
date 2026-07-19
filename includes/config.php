<?php
/**
 * includes/config.php
 * ---------------------------------------------------------------
 * Site-wide settings: name, contact details, social links.
 * Edit this file to update information across the whole site.
 * ---------------------------------------------------------------
 */

define('SITE_NAME', "Mbuya Parents' School");
define('SITE_MOTTO', 'The Sky Is Our Limit');
define('SITE_ADDRESS', 'Plot 22-25 Nadiope Lane, off Ismael Road, Mbuya II Parish, Nakawa Division, Kampala, Uganda');
define('SITE_POBOX', 'P.O. Box 22323, Kampala, Uganda');
define('SITE_PHONE_1', '0776 977554');
define('SITE_PHONE_2', '0776 522987');
define('SITE_EMAIL', 'info@mbuyaparentsschool.ug');
define('SITE_INSTAGRAM', 'https://www.instagram.com/mbuyaparents/');

// Base path helper so links/images work regardless of subfolder depth
if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}
