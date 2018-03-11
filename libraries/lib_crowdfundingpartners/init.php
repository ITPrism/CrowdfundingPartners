<?php
/**
* @package      CrowdfundingFiles
* @subpackage   Library
* @author       Todor Iliev
* @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      GNU General Public License version 3 or later; see LICENSE.txt
*/

defined('JPATH_PLATFORM') or die;

if (!defined('CROWDFUNDINGPARTNERS_PATH_COMPONENT_ADMINISTRATOR')) {
    define('CROWDFUNDINGPARTNERS_PATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_crowdfundingpartners');
}

if (!defined('CROWDFUNDINGPARTNERS_PATH_COMPONENT_SITE')) {
    define('CROWDFUNDINGPARTNERS_PATH_COMPONENT_SITE', JPATH_SITE . '/components/com_crowdfundingpartners');
}

if (!defined('CROWDFUNDINGPARTNERS_PATH_LIBRARY')) {
    define('CROWDFUNDINGPARTNERS_PATH_LIBRARY', JPATH_LIBRARIES . '/Crowdfundingpartners');
}

JLoader::registerNamespace('Crowdfundingpartners', JPATH_LIBRARIES);

// Register helpers
JLoader::register('CrowdfundingPartnersHelper', CROWDFUNDINGPARTNERS_PATH_COMPONENT_ADMINISTRATOR . '/helpers/crowdfundingpartners.php');

// Register HTML helpers
JHtml::addIncludePath(CROWDFUNDINGPARTNERS_PATH_COMPONENT_SITE . '/helpers/html');
JLoader::register('JHtmlString', JPATH_LIBRARIES . '/joomla/html/html/string.php');
