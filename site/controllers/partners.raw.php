<?php
/**
 * @package      CrowdfundingPartners
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Crowdfunding Files controller class.
 *
 * @package        CrowdfundingPartners
 * @subpackage     Component
 * @since          1.6
 */
class CrowdfundingPartnersControllerPartners extends JControllerLegacy
{
    protected $allowedAvatarSizes = array('icon', 'small', 'medium', 'large');

    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return   CrowdfundingPartnersModelPartners|bool    The model.
     * @since    1.5
     */
    public function getModel($name = 'Partners', $prefix = 'CrowdfundingPartnersModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function addPartner()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */
        
        $response = new Prism\Response\Json();

        $user   = JFactory::getUser();

        // Check for registered user.
        $userId = $user->get('id');
        if (!$userId) {
            $response
                ->setTitle(JText::_('COM_CROWDFUNDINGPARTNERS_FAIL'))
                ->setText(JText::_('COM_CROWDFUNDINGPARTNERS_ERROR_NOT_LOG_IN'))
                ->failure();

            echo $response;
            $app->close();
        }

        $projectId = $this->input->post->get('project_id');
        $username  = $this->input->post->getString('username');

        // Get image size.
        $imageSize = $this->input->post->getString('image_size');
        if (!in_array($imageSize, $this->allowedAvatarSizes, true)) {
            $imageSize = 'small';
        }

        $itemId    = 0;
        $avatar    = 'media/com_crowdfunding/images/no-profile.png';

        // Validate user.
        if (!$username) {
            $response
                ->setTitle(JText::_('COM_CROWDFUNDINGPARTNERS_FAIL'))
                ->setText(JText::_('COM_CROWDFUNDINGPARTNERS_ERROR_INVALID_USERNAME'))
                ->failure();

            echo $response;
            $app->close();
        }

        // Validate project owner.
        $validator = new Crowdfunding\Validator\Project\Owner(JFactory::getDbo(), $projectId, $userId);
        if (!$projectId or !$validator->isValid()) {
            $response
                ->setTitle(JText::_('COM_CROWDFUNDINGPARTNERS_FAIL'))
                ->setText(JText::_('COM_CROWDFUNDINGPARTNERS_ERROR_INVALID_PROJECT'))
                ->failure();

            echo $response;
            $app->close();
        }

        // Get the model
        $model = $this->getModel();
        /** @var $model CrowdfundingPartnersModelPartners */

        // Get user ID by username or email
        $partnerId = $model->getUserId($username);

        // Get user data for the partner.
        $partner   = JFactory::getUser($partnerId);

        // Validate partner.
        if (!$partner->id) {
            $response
                ->setTitle(JText::_('COM_CROWDFUNDINGPARTNERS_FAIL'))
                ->setText(JText::_('COM_CROWDFUNDINGPARTNERS_ERROR_USERNAME_DOES_NOT_EXISTS'))
                ->failure();

            echo $response;
            $app->close();
        }

        // Validate for owner and partner to be different users.
        // Check partner about that he has not been assigned.
        if (((int)$userId === (int)$partner->id) or $model->hasAssigned($partnerId, $projectId)) {
            $response
                ->setTitle(JText::_('COM_CROWDFUNDINGPARTNERS_FAIL'))
                ->setText(JText::_('COM_CROWDFUNDINGPARTNERS_ERROR_CANNOT_ASSIGN'))
                ->failure();

            echo $response;
            $app->close();
        }

        try {
            $itemId = $model->addPartner($partner, $projectId);
        } catch (Exception $e) {
            JLog::add($e->getMessage());

            $response
                ->setTitle(JText::_('COM_CROWDFUNDINGPARTNERS_FAIL'))
                ->setText(JText::_('COM_CROWDFUNDINGPARTNERS_ERROR_SYSTEM'))
                ->failure();

            echo $response;
            $app->close();
        }

        // Get component parameters
        $params = JComponentHelper::getParams('com_crowdfunding');
        /** @var  $params Joomla\Registry\Registry */

        // Get a social platform for integration
        $socialPlatform = $params->get('integration_social_platform');

        // Get social profile
        if (strlen($socialPlatform) > 0) {
            $options = new \Joomla\Registry\Registry(array(
                'platform' => $socialPlatform,
                'user_id'  => $partnerId
            ));

            $socialIntegrationFactory = new Prism\Integration\Profile\Factory($options);
            $socialProfile = $socialIntegrationFactory->create();

            // Get avatar from social profile.
            if ($socialProfile !== null) {
                $avatar     = $socialProfile->getAvatar($imageSize);
            }
        }
        
        $partnerData= array(
            'id' => $itemId,
            'name' => $partner->name,
            'avatar' => $avatar
        );

        $response
            ->setTitle(JText::_('COM_CROWDFUNDINGPARTNERS_SUCCESS'))
            ->setText(JText::_('COM_CROWDFUNDINGPARTNERS_PARTNER_ADDED'))
            ->setData($partnerData)
            ->success();

        echo $response;
        $app->close();
    }
    
    /**
     * Delete an item.
     */
    public function remove()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Create response object
        $response = new Prism\Response\Json();

        $userId = JFactory::getUser()->get('id');
        if (!$userId) {
            $response
                ->setTitle(JText::_('COM_CROWDFUNDINGPARTNERS_FAIL'))
                ->setText(JText::_('COM_CROWDFUNDINGPARTNERS_ERROR_NOT_LOG_IN'))
                ->failure();

            echo $response;
            $app->close();
        }

        // Get file ID.
        $itemId = $this->input->post->get('id');

        // Get the model
        $model = $this->getModel();
        /** @var $model CrowdfundingPartnersModelPartners */

        // Create an partner object and load the data from database.
        $partner = new Crowdfundingpartners\Partner(JFactory::getDbo());
        $partner->load($itemId);

        // Validate owner of the project.
        $validator = new Crowdfunding\Validator\Project\Owner(JFactory::getDbo(), $partner->getProjectId(), $userId);
        if (!$validator->isValid()) {
            $response
                ->setTitle(JText::_('COM_CROWDFUNDINGPARTNERS_FAIL'))
                ->setText(JText::_('COM_CROWDFUNDINGPARTNERS_ERROR_INVALID_PROJECT'))
                ->failure();

            echo $response;
            $app->close();
        }

        try {
            $model->remove($itemId);
        } catch (RuntimeException $e) {
            $response
                ->setTitle(JText::_('COM_CROWDFUNDINGPARTNERS_FAIL'))
                ->setText(JText::_('COM_CROWDFUNDINGPARTNERS_ERROR_INVALID_PROJECT'))
                ->failure();

            echo $response;
            $app->close();

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception($e->getMessage());
        }

        $response
            ->setTitle(JText::_('COM_CROWDFUNDINGPARTNERS_SUCCESS'))
            ->setText(JText::_('COM_CROWDFUNDINGPARTNERS_PARTNER_DELETED'))
            ->setData(array('id' => $itemId))
            ->success();

        echo $response;
        $app->close();
    }
}
