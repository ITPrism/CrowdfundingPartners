<?php
/**
 * @package      CrowdfundingPartners
 * @subpackage   Files
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Crowdfundingpartners;

use Prism\Database\Collection;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage partners.
 *
 * @package      CrowdfundingPartners
 * @subpackage   Parnters
 */
class Partners extends Collection
{
    /**
     * Load partners data by ID from database.
     *
     * <code>
     * $options = array(
     *    'project_id' => 1,
     *    'ids' => array(1,2,3,4,5)
     * );
     *
     * $partners   = new Crowdfundingpartners\Partners(JFactory::getDbo());
     * $partners->load($options);
     *
     * foreach($partners as $partner) {
     *   echo $partners["name"];
     *   echo $partners["partner_id"];
     * }
     * </code>
     *
     * @param array $options
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function load(array $options = array())
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('a.id, a.name, a.project_id, a.partner_id')
            ->from($this->db->quoteName('#__cfpartners_partners', 'a'));

        $ids = ArrayHelper::getValue($options, 'ids', array(), 'array');
        $ids = ArrayHelper::toInteger($ids);
        if (count($ids) > 0) {
            $query->where('a.id IN ( ' . implode(',', $ids) . ' )');
        }

        $projectId = ArrayHelper::getValue($options, 'project_id', 0, 'int');
        if ($projectId > 0) {
            $query->where('a.project_id = ' . (int)$projectId);
        }

        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
    }

    /**
     * Add a new value to the array.
     *
     * <code>
     * $partner = array(
     *     "name" => "John Dow",
     *     "project_id" => 1,
     *     "partner_id" => 2
     * );
     *
     * $partners   = new Crowdfundingpartners\Partners();
     * $partners->add($partner);
     * </code>
     *
     * @param array $value
     * @param null|int $index
     *
     * @return $this
     */
    public function add($value, $index = null)
    {
        if ($index !== null) {
            $this->items[$index] = $value;
        } else {
            $this->items[] = $value;
        }

        return $this;
    }
}
