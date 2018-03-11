<?php
/**
 * @package      CrowdfundingPartners
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Method to build Route
 *
 * @param array $query
 *
 * @return array
 */
function CrowdfundingPartnersBuildRoute(&$query)
{
    $segments = array();

    // get a menu item based on Itemid or currently active
//    $app  = JFactory::getApplication();
//    $menu = $app->getMenu();

    return $segments;
}

/**
 * Method to parse Route
 *
 * @param array $segments
 *
 * @return array
 */
function CrowdfundingPartnersParseRoute($segments)
{
    $query = array();

    return $query;
}
