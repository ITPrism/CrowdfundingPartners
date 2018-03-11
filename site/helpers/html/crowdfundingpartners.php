<?php
/**
 * @package      CrowdfundingPartners
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * CrowdfundingPartners Html Helper
 *
 * @package        CrowdfundingPartners
 * @subpackage     Components
 * @since          1.6
 */
abstract class JHtmlCrowdfundingPartners
{
    /**
     * Display an avatar and link to user profile.
     *
     * @param array $partner
     * @param array $options
     *
     * @return string
     */
    public static function partner($partner, array $options = array())
    {
        $html = array();

        $width  = array_key_exists('width', $options) ? $options['width'] : 50;
        $height = array_key_exists('height', $options) ? $options['height'] : 50;

        if (!empty($partner['link'])) {
            $html[] = '<a href="'. JRoute::_($partner['link']) .'"><img src="' . $partner['avatar'] .'" class="img-thumbnail" width="'.$width.'" height="'.$height.'" /></a>';
        } else {
            $html[] = '<img src="' . $partner['avatar'] .'" class="img-thumbnail" width="'.$width.'" height="'.$height.'" />';
        }

        if (!empty($partner['link'])) {
            $html[] = '<a href="'. JRoute::_($partner['link']) .'">';
            $html[] = htmlentities($partner['name'], ENT_QUOTES, 'UTF-8');
            $html[] = '</a>';
        } else {
            $html[] = htmlentities($partner['name'], ENT_QUOTES, 'UTF-8');
        }

        return implode("\n", $html);
    }
}
