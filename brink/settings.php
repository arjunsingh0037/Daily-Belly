<?php
// This file is part of Ranking block for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme Brink block settings file
 *
 * @package    theme_moove
 * @author     Arjun Singh(arjunsingh@elearn10.com)
 * @copyright  2017 Dhruv Infoline Pvt Ltd
 * @license    http://lmsofindia.com
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// This is used for performance, we don't need to know about these settings on every page in Moodle, only when
// we are looking at the admin settings pages.
if ($ADMIN->fulltree) {

    // Boost provides a nice setting page which splits settings onto separate tabs. We want to use it here.
    $settings = new theme_boost_admin_settingspage_tabs('themesettingbrink', get_string('configtitle', 'theme_brink'));

    /*
    * ----------------------
    * General settings tab
    * ----------------------
    */
    $page = new admin_settingpage('theme_brink_general', get_string('generalsettings', 'theme_brink'));

    // Logo file setting.
    $name = 'theme_brink/logo';
    $title = get_string('logo', 'theme_brink');
    $description = get_string('logodesc', 'theme_brink');
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset.
    $name = 'theme_brink/preset';
    $title = get_string('preset', 'theme_brink');
    $description = get_string('preset_desc', 'theme_brink');
    $default = 'default.scss';

    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_brink', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    // These are the built in presets.
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.
    $name = 'theme_brink/presetfiles';
    $title = get_string('presetfiles', 'theme_brink');
    $description = get_string('presetfiles_desc', 'theme_brink');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Variable $brand-color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_brink/brandcolor';
    $title = get_string('brandcolor', 'theme_brink');
    $description = get_string('brandcolor_desc', 'theme_brink');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    /*
    * ----------------------
    * Advanced settings tab
    * ----------------------
    */
    $page = new admin_settingpage('theme_brink_advanced', get_string('advancedsettings', 'theme_brink'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_brink/scsspre',
        get_string('rawscsspre', 'theme_brink'), get_string('rawscsspre_desc', 'theme_brink'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_brink/scss', get_string('rawscss', 'theme_brink'),
        get_string('rawscss_desc', 'theme_brink'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    /*
    * -----------------------
    * Frontpage settings tab
    * -----------------------
    */
    $page = new admin_settingpage('theme_brink_frontpage', get_string('frontpagesettings', 'theme_brink'));

    // Headerimg file setting.
    $name = 'theme_brink/headerimg';
    $title = get_string('headerimg', 'theme_brink');
    $description = get_string('headerimgdesc', 'theme_brink');
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'headerimg', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Bannerheading.
    $name = 'theme_brink/bannerheading';
    $title = get_string('bannerheading', 'theme_brink');
    $description = get_string('bannerheadingdesc', 'theme_brink');
    $default = 'Perfect Learning System';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Bannercontent.
    $name = 'theme_brink/bannercontent';
    $title = get_string('bannercontent', 'theme_brink');
    $description = get_string('bannercontentdesc', 'theme_brink');
    $default = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_brink/displaymarketingbox';
    $title = get_string('displaymarketingbox', 'theme_brink');
    $description = get_string('displaymarketingboxdesc', 'theme_brink');
    $default = 1;
    $choices = array(0 => 'No', 1 => 'Yes');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Marketing1icon.
    $name = 'theme_brink/marketing1icon';
    $title = get_string('marketing1icon', 'theme_brink');
    $description = get_string('marketing1icondesc', 'theme_brink');
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketing1icon', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing1heading.
    $name = 'theme_brink/marketing1heading';
    $title = get_string('marketing1heading', 'theme_brink');
    $description = get_string('marketing1headingdesc', 'theme_brink');
    $default = 'We host';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing1subheading.
    $name = 'theme_brink/marketing1subheading';
    $title = get_string('marketing1subheading', 'theme_brink');
    $description = get_string('marketing1subheadingdesc', 'theme_brink');
    $default = 'your MOODLE';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing1content.
    $name = 'theme_brink/marketing1content';
    $title = get_string('marketing1content', 'theme_brink');
    $description = get_string('marketing1contentdesc', 'theme_brink');
    $default = 'Moodle hosting in a powerful cloud infrastructure';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing1url.
    $name = 'theme_brink/marketing1url';
    $title = get_string('marketing1url', 'theme_brink');
    $description = get_string('marketing1urldesc', 'theme_brink');
    $default = '#';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing2icon.
    $name = 'theme_brink/marketing2icon';
    $title = get_string('marketing2icon', 'theme_brink');
    $description = get_string('marketing2icondesc', 'theme_brink');
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketing2icon', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing2heading.
    $name = 'theme_brink/marketing2heading';
    $title = get_string('marketing2heading', 'theme_brink');
    $description = get_string('marketing2headingdesc', 'theme_brink');
    $default = 'Consulting';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing2subheading.
    $name = 'theme_brink/marketing2subheading';
    $title = get_string('marketing2subheading', 'theme_brink');
    $description = get_string('marketing2subheadingdesc', 'theme_brink');
    $default = 'for your company';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing2content.
    $name = 'theme_brink/marketing2content';
    $title = get_string('marketing2content', 'theme_brink');
    $description = get_string('marketing2contentdesc', 'theme_brink');
    $default = 'Moodle consulting and training for you';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing2url.
    $name = 'theme_brink/marketing2url';
    $title = get_string('marketing2url', 'theme_brink');
    $description = get_string('marketing2urldesc', 'theme_brink');
    $default = '#';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing3icon.
    $name = 'theme_brink/marketing3icon';
    $title = get_string('marketing3icon', 'theme_brink');
    $description = get_string('marketing3icondesc', 'theme_brink');
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketing3icon', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing3heading.
    $name = 'theme_brink/marketing3heading';
    $title = get_string('marketing3heading', 'theme_brink');
    $description = get_string('marketing3headingdesc', 'theme_brink');
    $default = 'Development';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing3subheading.
    $name = 'theme_brink/marketing3subheading';
    $title = get_string('marketing3subheading', 'theme_brink');
    $description = get_string('marketing3subheadingdesc', 'theme_brink');
    $default = 'themes and plugins';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing3content.
    $name = 'theme_brink/marketing3content';
    $title = get_string('marketing3content', 'theme_brink');
    $description = get_string('marketing3contentdesc', 'theme_brink');
    $default = 'We develop themes and plugins as your desires';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing3url.
    $name = 'theme_brink/marketing3url';
    $title = get_string('marketing3url', 'theme_brink');
    $description = get_string('marketing3urldesc', 'theme_brink');
    $default = '#';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing4icon.
    $name = 'theme_brink/marketing4icon';
    $title = get_string('marketing4icon', 'theme_brink');
    $description = get_string('marketing4icondesc', 'theme_brink');
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketing4icon', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing4heading.
    $name = 'theme_brink/marketing4heading';
    $title = get_string('marketing4heading', 'theme_brink');
    $description = get_string('marketing4headingdesc', 'theme_brink');
    $default = 'Support';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing4subheading.
    $name = 'theme_brink/marketing4subheading';
    $title = get_string('marketing4subheading', 'theme_brink');
    $description = get_string('marketing4subheadingdesc', 'theme_brink');
    $default = 'we give you answers';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing4content.
    $name = 'theme_brink/marketing4content';
    $title = get_string('marketing4content', 'theme_brink');
    $description = get_string('marketing4contentdesc', 'theme_brink');
    $default = 'MOODLE specialized support';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing4url.
    $name = 'theme_brink/marketing4url';
    $title = get_string('marketing4url', 'theme_brink');
    $description = get_string('marketing4urldesc', 'theme_brink');
    $default = '#';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

        /*
    * -----------------------
    * Category Header settings tab
    * -----------------------
    */
    $page = new admin_settingpage('theme_brink_category', get_string('categorysettings', 'theme_brink'));

    // Headerimg file setting.
    $name = 'theme_brink/categoryimg';
    $title = get_string('categoryimg', 'theme_brink');
    $description = get_string('categoryimgdesc', 'theme_brink');
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'categoryimg', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Bannercontent.
    $name = 'theme_brink/categorycontent';
    $title = get_string('categorycontent', 'theme_brink');
    $description = get_string('categorycontentdesc', 'theme_brink');
    $default = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    /*
    * --------------------
    * Footer settings tab
    * --------------------
    */
    $page = new admin_settingpage('theme_brink_footer', get_string('footersettings', 'theme_brink'));

    $name = 'theme_brink/getintouchcontent';
    $title = get_string('getintouchcontent', 'theme_brink');
    $description = get_string('getintouchcontentdesc', 'theme_brink');
    $default = 'Conecti.me';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Website.
    $name = 'theme_brink/website';
    $title = get_string('website', 'theme_brink');
    $description = get_string('websitedesc', 'theme_brink');
    $default = 'http://conecti.me';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Mobile.
    $name = 'theme_brink/mobile';
    $title = get_string('mobile', 'theme_brink');
    $description = get_string('mobiledesc', 'theme_brink');
    $default = 'Mobile : +55 (98) 00123-45678';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Mail.
    $name = 'theme_brink/mail';
    $title = get_string('mail', 'theme_brink');
    $description = get_string('maildesc', 'theme_brink');
    $default = 'willianmano@conectime.com';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Facebook url setting.
    $name = 'theme_brink/facebook';
    $title = get_string('facebook', 'theme_brink');
    $description = get_string('facebookdesc', 'theme_brink');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Twitter url setting.
    $name = 'theme_brink/twitter';
    $title = get_string('twitter', 'theme_brink');
    $description = get_string('twitterdesc', 'theme_brink');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Googleplus url setting.
    $name = 'theme_brink/googleplus';
    $title = get_string('googleplus', 'theme_brink');
    $description = get_string('googleplusdesc', 'theme_brink');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Linkdin url setting.
    $name = 'theme_brink/linkedin';
    $title = get_string('linkedin', 'theme_brink');
    $description = get_string('linkedindesc', 'theme_brink');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
