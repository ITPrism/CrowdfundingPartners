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
class plgCrowdfundingPartners extends JPlugin
{
    protected $autoloadLanguage = true;

    /**
     * @var JApplicationSite
     */
    protected $app;

    protected $version = '2.2';

    /**
     * This method prepares a code that will be included to step "Extras" on project wizard.
     *
     * @param string    $context This string gives information about that where it has been executed the trigger.
     * @param stdClass  $item    A project data.
     * @param Joomla\Registry\Registry $params  The parameters of the component
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return null|string
     */
    public function onExtrasDisplay($context, $item, $params)
    {
        if (strcmp('com_crowdfunding.project.extras', $context) !== 0) {
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
        
        if (!isset($item->user_id) or !$item->user_id) {
            return null;
        }

        $partners = new Crowdfundingpartners\Partners(JFactory::getDbo());
        $partners->load(array('project_id' => $item->id));

        // Prepare avatars.
        $this->prepareIntegration($partners, $params->get('integration_social_platform'));

        // Load jQuery
        JHtml::_('jquery.framework');
        JHtml::_('Prism.ui.pnotify');
        JHtml::_('Prism.ui.joomlaHelper');

        // Include the translation of the confirmation question.
        JText::script('PLG_CROWDFUNDING_PARTNERS_DELETE_QUESTION');

        // Get the path for the layout file
        $path = JPath::clean(JPluginHelper::getLayoutPath('crowdfunding', 'partners'));

        // Render the login form.
        ob_start();
        include $path;
        $html = ob_get_clean();

        return $html;
    }

    /**
     * @param Crowdfundingpartners\Partners $partners
     * @param string $socialPlatform
     */
    protected function prepareIntegration($partners, $socialPlatform)
    {
        $defaultAvatar = 'media/com_crowdfunding/images/no-profile.png';

        if ($socialPlatform !== null) {
            $items   = $partners->toArray();
            $userIds = Prism\Utilities\ArrayHelper::getIds($items, 'partner_id');

            $config = new Joomla\Registry\Registry(array(
                'platform' => $socialPlatform,
                'user_ids' => $userIds
            ));

            $socialProfilesBuilder = new Prism\Integration\Profiles\Factory($config);
            $socialProfiles = $socialProfilesBuilder->create();

            if ($socialProfiles !== null) {
                foreach ($partners as $key => $value) {
                    $value['avatar'] = $socialProfiles->getAvatar($value['partner_id'], $this->params->get('image_size', 'small'));
                    $partners[$key]  = $value;
                }
            }

        } else { // Set default avatar
            foreach ($partners as $key => $value) {
                $value['avatar'] = $defaultAvatar;
                $partners[$key]  = $value;
            }
        }
    }
}
