<?php
/**
 * @package      CrowdfundingPartners
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Get a list of items
 */
class CrowdfundingPartnersModelPartners extends JModelLegacy
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  CrowdfundingpartnersTablePartner|bool  A database object
     * @since   1.6
     */
    public function getTable($type = 'Partner', $prefix = 'CrowdfundingpartnersTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Return user ID searching by username or email.
     *
     * @param string $username
     *
     * @throws \RuntimeException
     * @return int
     */
    public function getUserId($username)
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true);
        $query
            ->select('a.id')
            ->from($db->quoteName('#__users', 'a'))
            ->where('a.username = ' . $db->quote($username), 'OR')
            ->where('a.email = ' .$db->quote($username));

        $db->setQuery($query, 0, 1);

        return (int)$db->loadResult();
    }

    /**
     * Check if the user has been assigned to a project.
     *
     * @param int $partnerId
     * @param int $projectId
     *
     * @throws \RuntimeException
     * @return bool
     */
    public function hasAssigned($partnerId, $projectId)
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true);
        $query
            ->select('COUNT(*)')
            ->from($db->quoteName('#__cfpartners_partners', 'a'))
            ->where('a.partner_id = ' . (int)$partnerId)
            ->where('a.project_id = ' .(int)$projectId);

        $db->setQuery($query, 0, 1);
        $result = (int)$db->loadResult();

        return (bool)$result;
    }

    /**
     * Store the partner in database.
     *
     * @param JUser $partner
     * @param int $projectId
     *
     * @throws \RuntimeException
     * @return int
     */
    public function addPartner($partner, $projectId)
    {
        $db = $this->getDbo();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);
        $query
            ->insert($db->quoteName('#__cfpartners_partners'))
            ->set($db->quoteName('name') . '=' . $db->quote($partner->get('name')))
            ->set($db->quoteName('project_id') . '=' . (int)$projectId)
            ->set($db->quoteName('partner_id') . '=' . (int)$partner->get('id'));

        $db->setQuery($query);
        $db->execute();

        return (int)$db->insertid();
    }

    /**
     * Delete a partner record.
     *
     * @param integer $itemId
     *
     * @throws \RuntimeException
     */
    public function remove($itemId)
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        $query
            ->delete($db->quoteName('#__cfpartners_partners'))
            ->where($db->quoteName('id') .'='.(int)$itemId);

        $db->setQuery($query);
        $db->execute();
    }
}
