<?php
/**
 * @package      CrowdfundingPartners
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Crowdfunding Partners controller class.
 *
 * @package        CrowdfundingPartners
 * @subpackage     Component
 * @since          1.6
 */
class CrowdfundingPartnersControllerPartners extends Prism\Controller\Admin
{
    public function getModel($name = 'Partner', $prefix = 'CrowdfundingPartnersModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}
