<?php
/**
 * @package         CrowdfundingPartners
 * @subpackage      Plugins
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/licenses/gpl-3.0.en.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('Crowdfunding.init');
jimport('Crowdfundingpartners.init');

/**
 * Crowdfunding Partners Plugin
 *
 * @package        CrowdfundingPartners
 * @subpackage     Plugins
 */
class plgContentCrowdfundingPartners extends JPlugin
{
    protected $autoloadLanguage = true;

    /**
     * @var JApplicationSite
     */
    protected $app;

    /**
     * Generate and display a list of team members on details page.
     *
     * @param string $context
     * @param stdClass $item
     * @param Joomla\Registry\Registry $params
     *
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return null|string
     */
    public function onContentAfterDisplay($context, $item, $params)
    {
        if (strcmp('com_crowdfunding.details', $context) !== 0) {
            return null;
        }

        if ($this->app->isAdmin()) {
            return null;
        }

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        // Check document type
        $docType = $doc->getType();
        if (strcmp('html', $docType) !== 0) {
            return null;
        }

        $html = '';

        $partners = new Crowdfundingpartners\Partners(JFactory::getDbo());
        $partners->load(array('project_id' => $item->id));

        if (count($partners) > 0) {
            // Include the project owner to the team.
            if ($this->params->get('display_owner', 0)) {
                $user = JFactory::getUser($item->user_id);

                $owner = array(
                    'name'       => $user->get('name'),
                    'project_id' => $item->id,
                    'partner_id' => $item->user_id
                );

                $partners->add($owner);
            }

            // Get a social platform for integration
            // Prepare avatars.
            $this->prepareIntegration($partners, $params->get('integration_social_platform'));

            // Get the path for the layout file
            $path = JPath::clean(JPluginHelper::getLayoutPath('content', 'crowdfundingpartners'));

            // Render the login form.
            ob_start();
            include $path;
            $html = ob_get_clean();
        }

        return $html;
    }

    /**
     * Generate links to a user avatar and profile.
     *
     * @param Crowdfundingpartners\Partners $partners
     * @param string $socialPlatform
     */
    protected function prepareIntegration($partners, $socialPlatform)
    {
        $defaultAvatar = 'media/com_crowdfunding/images/no-profile.png';

        if ($socialPlatform !== null and $socialPlatform !== '') {
            $items   = $partners->toArray();

            $options = new \Joomla\Registry\Registry(array(
                'platform' => $socialPlatform,
                'user_ids' => Prism\Utilities\ArrayHelper::getIds($items, 'partner_id')
            ));

            $factory        = new Prism\Integration\Profiles\Factory($options);
            $socialProfiles = $factory->create();

            foreach ($partners as $key => $value) {
                $value['avatar'] = $socialProfiles->getAvatar($value['partner_id'], $this->params->get('image_size', 'small'));
                $value['link']   = $socialProfiles->getLink($value['partner_id']);

                $partners[$key]  = $value;
            }

        } else { // Set default avatar

            foreach ($partners as $key => $value) {
                $value['avatar'] = $defaultAvatar;
                $value['link']   = '';

                $partners[$key]  = $value;
            }
        }
    }
}
