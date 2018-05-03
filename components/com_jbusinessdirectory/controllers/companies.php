<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');



class JBusinessDirectoryControllerCompanies extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
	}

	function displayCompany(){
		parent::display();
	}
	
	function showCompany(){
		$model = $this->getModel('companies');
		$model->increaseViewCount();
		JRequest::setVar("view","companies");
		parent::display();
	}
	
	function saveReview(){
		$app = JFactory::getApplication();
		$model = $this->getModel('companies');
		$post = JRequest::get( 'post' );
		$companyId = JRequest::getVar('itemId');
		$company = $model->getPlainCompany($companyId);
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$post["ipAddress"] = $ipAddress;
		
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		if($appSettings->captcha){
			$namespace="jbusinessdirectory.contact";
			$captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];
			$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
			if(!$captcha->checkAnswer($captchaAnswer)){
				$error = $captcha->getError();
				$app->setUserState('com_jbusinessdirectory.add.review.data', $post);
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JBusinessUtil::getCompanyLink($company));
				return;
			}
		}

        $pictures = array();
        foreach( $post as $key => $value )
        {
            if(
                    strpos( $key, 'picture_info' ) !== false
                    ||
                    strpos( $key, 'picture_path' ) !== false
                    ||
                    strpos( $key, 'picture_enable' ) !== false
            ){
                foreach( $value as $k => $v )
                {
                    if( !isset($pictures[$k]) )
                        $pictures[$k] = array('picture_info'=>'', 'picture_path'=>'','picture_enable'=>1);
                    $pictures[$k][$key] = $v;
                }
            }
        }
        $post['pictures'] = $pictures;


		$user = JFactory::getUser();		
		if($appSettings->enable_reviews_users && $user->id ==0){
			$this->setMessage(JText::_('LNG_ERROR_SAVING_REVIEW'),'error');
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
			return;
		}
				
		if ($model->saveReview($post)){
			$this->setMessage(JText::_('LNG_REVIEW_SAVED'));
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		}else {
			$this->setMessage(JText::_('LNG_ERROR_SAVING_REVIEW'),'error');
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));	
		}	
	}

	function updateRating(){
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$user = JFactory::getUser();
		if($appSettings->enable_reviews_users && $user->id ==0){
			exit;
		}
		
		$model = $this->getModel('companies');
		$post = JRequest::get( 'post' );
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$post["ipAddress"] = $ipAddress;
		$ratingId = $model->saveRating($post);
		$nrRatings = $model->getRatingsCount($post['companyId']);
		$company = $model->getCompany($post['companyId']);
		
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<company_statement>';
		echo '<answer  nrRatings="'.($nrRatings).'" id="'.$post["companyId"].'" ratingId="'.$ratingId.'" averageRating="'.$company->averageRating.'"/>';
		echo '</company_statement>';
		echo '</xml>';
		exit;
	}
	
	function reportAbuse(){
		$app = JFactory::getApplication();
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$model = $this->getModel('companies');
		$post = JRequest::get('post');

		$companyId = JRequest::getVar('companyId');
		$company = $model->getPlainCompany($companyId);
		$captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];
		
		if($appSettings->captcha){
			$namespace="jbusinessdirectory.contact";
			$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
			if(!$captcha->checkAnswer($captchaAnswer)){
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$message="Captcha error";
				$this->setRedirect(JBusinessUtil::getCompanyLink($company));
				return;
			}
		}
		
		$model = $this->getModel('companies');
	
		$result = $model->reportAbuse($post);
	
		if($result){
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED'));
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		}else{
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED'),'warning');
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
	
		}
	}
	
	function saveReviewResponse(){
		$app = JFactory::getApplication();
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$model = $this->getModel('companies');
		$post = JRequest::get( 'post' );
		$companyId = JRequest::getVar('companyId');
		$company = $model->getPlainCompany($companyId);
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$post["ipAddress"] = $ipAddress;
		$captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];
		//exit;
		
		if($appSettings->captcha){
			$namespace="jbusinessdirectory.contact";
			$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
			if(!$captcha->checkAnswer($captchaAnswer)){
				$error = $captcha->getError();
				$app->setUserState('com_jbusinessdirectory.review.response.data', $post);
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JBusinessUtil::getCompanyLink($company));
				return;
			}
		}
		
		if ($model->saveReviewResponse($post)){
			$this->setMessage(JText::_('LNG_REVIEW_RESPONSE_SAVED'));
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		}else {
			$this->setMessage(JText::_('LNG_ERROR_SAVING_REVIEW'),'warning');
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		}
	}
	
	function claimCompany(){
		$app = JFactory::getApplication();
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$model = $this->getModel('companies');
		$post = JRequest::get( 'post' );
		$companyId = JRequest::getVar('companyId');
		$company = $model->getPlainCompany($companyId);
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$post["ipAddress"] = $ipAddress;
		//exit;
		$captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];
		
		if($appSettings->captcha){
			$namespace="jbusinessdirectory.contact";
			$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
			if(!$captcha->checkAnswer($captchaAnswer)){
				$error = $captcha->getError();
				$app->setUserState('com_jbusinessdirectory.claim.company.data', $post);
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JBusinessUtil::getCompanyLink($company));
				return;
			}
		}
	
		if ($model->claimCompany($post))
		{
			$this->setMessage(JText::_('LNG_CLAIM_SUCCESSFULLY'));
			EmailService::sendClaimEmail($company,$post);
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		}else {
			$this->setMessage(JText::_('LNG_ERROR_CLAIMING_COMPANY'), 'warning');
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		}
	}
	
	
	function increaseReviewLikeCount(){
		$model = $this->getModel('companies');
		$post = JRequest::get('post');
		$result = $model->increaseReviewLikeCount($post["reviewId"]);
		
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<company_statement>';
		echo '<answer result="'.$result.'" reviewId="'.$post["reviewId"].'"/>';
		echo '</company_statement>';
		echo '</xml>';
		exit;
	}
	
	function increaseReviewDislikeCount(){
		$model = $this->getModel('companies');
		$post = JRequest::get('post');
		$result = $model->increaseReviewDislikeCount($post["reviewId"]);
		
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<company_statement>';
		echo '<answer result="'.$result.'" reviewId="'.$post["reviewId"].'"/>';	
		echo '</company_statement>';
		echo '</xml>';
		exit;
	}
	
	function updateCompanyOwner(){
		$model = $this->getModel('companies');
		$post = JRequest::get('post');
		$result = $model->updateCompanyOwner($post["companyId"], $post["userId"]);
		
		
		echo '<?xml version="1.0" encoding="utf-8"?>';
		echo '<company_statement>';
		echo '<answer result="'.$result.'"/>';
		echo '</company_statement>';
		echo '</xml>';
		exit;
	}
	
	function checkCompanyName(){
		$post = JRequest::get( 'post' );
		$model = $this->getModel('companies');
		$company = $model->getCompanyByName(trim($post["companyName"]));
	
		
		$claim = isset($company->userId)?0:1;
		
		$exists = isset($company)?1:0;
	
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<company_statement>';
		echo '<answer exists="'.$exists.'" claim="'.$claim.'" name="'.trim($post["companyName"]).'"/>';
		echo '</company_statement>';
		echo '</xml>';
		exit;
	}
	
	function contactCompany(){
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$model = $this->getModel('companies');
		
		$data = JRequest::get( 'post' );
		$companyId = JRequest::getVar('companyId');
		$contactId = JRequest::getVar('contactId');
		$company = $model->getPlainCompany($companyId);
		$captchaAnswer = !empty($data['recaptcha_response_field'])?$data['recaptcha_response_field']:$data['g-recaptcha-response'];
		
		if($appSettings->captcha){
			$namespace="jbusinessdirectory.contact";
			$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
			if(!$captcha->checkAnswer($captchaAnswer)){
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JBusinessUtil::getCompanyLink($company));
				return;
			}
		}

		$data['description'] = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$data['contact_id'] = $contactId;
		
		$result = $model->contactCompany($data);
		
		if($result){
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED'));
		}else{
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED'));
		}

		$model->saveCompanyMessages();

		$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		
	}
	
	
	function addBookmark(){
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$data = JRequest::get( 'post' );

		$model = $this->getModel('companies');
	
	
		$result = $model->addBookmark($data);
	
		if($result){
			$this->setMessage(JText::sprintf('COM_JBUSINESS_BOOKMARK_ADDED','<a href="'.JRoute::_('index.php?option=com_jbusinessdirectory&view=managebookmarks').'">'.JText::_('LNG_HERE').'</a>'));
		}else{
			$this->setMessage(JText::_('JLIB_APPLICATION_ERROR_SAVE_FAILED'));
		}
	
		$company = $model->getPlainCompany($data["item_id"]);
		$this->setRedirect(JBusinessUtil::getCompanyLink($company));
	
	}
	
	function updateBookmark(){
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$data = JRequest::get( 'post' );
	
		$model = $this->getModel('companies');
	
	
		$result = $model->updateBookmark($data);
	
		if($result){
			$this->setMessage(JText::_('COM_JBUSINESS_BOOKMARK_UPDATED'));
		}else{
			$this->setMessage(JText::_('JLIB_APPLICATION_ERROR_SAVE_FAILED'));
		}
	
		$company = $model->getPlainCompany($data["item_id"]);
		$this->setRedirect(JBusinessUtil::getCompanyLink($company));
	}
	
	function removeBookmark(){
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$data = JRequest::get( 'post' );
	
		$model = $this->getModel('companies');
		$result = $model->removeBookmark($data);
	
		if($result){
			$this->setMessage(JText::_('COM_JBUSINESS_BOOKMARK_REMVED'));
		}else{
			$this->setMessage(JText::_('JLIB_APPLICATION_ERROR_SAVE_FAILED'));
		}
	
		$company = $model->getPlainCompany($data["item_id"]);
		$this->setRedirect(JBusinessUtil::getCompanyLink($company));
	}
	
	function contactCompanyAjax(){
		// Check for request forgeries.
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$post = JRequest::get( 'post' );
		$captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];
		
		$errorFlag = false;
		$message="";
		if($appSettings->captcha){
			$namespace="jbusinessdirectory.contact";
			$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
			if(!$captcha->checkAnswer($captchaAnswer)){
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$message="Captcha error";
				$errorFlag = true;
			}
		}

		$model = $this->getModel('companies');

		if(!$errorFlag){
		
			$result = $model->contactCompany($post);
		
			if($result){
				$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED'));
			}else{
				$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED'));
				$message="JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED')";
				$errorFlag = true;
			}
		}

		$model->saveCompanyMessages();
	
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<category_statement>';
		echo '<answer error="'.(!$errorFlag ? "0" : "1").'" errorMessage="'.$message.'"';
		echo '</category_statement>';
		echo '</xml>';
		exit;
		
	}
	
	function requestQuoteCompanyAjax(){
		// Check for request forgeries.
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$post = JRequest::get( 'post' );
		$captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];
	
		$errorFlag = false;
		$message="";
		if($appSettings->captcha){
			$namespace="jbusinessdirectory.contact";
			$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
			if(!$captcha->checkAnswer($captchaAnswer)){
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$message="Captcha error";
				$errorFlag = true;
			}
		}
	
		if(!$errorFlag){
			$model = $this->getModel('companies');
		
			$result = $model->requestQuoteCompany($post);
		
			if($result){
				$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED'));
				$message=JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED');
			}else{
				$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED'));
				$message="JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED')";
				$errorFlag = true;
			}
		}
	
	
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<category_statement>';
		echo '<answer error="'.(!$errorFlag ? "0" : "1").'" errorMessage="'.$message.'"/>';
		echo '</category_statement>';
		echo '</xml>';
		exit;
	
	}
	
	function checkBusinessAboutToExpire(){
		$model = $this->getModel('companies');
		$model->checkBusinessAboutToExpire();
	}
	
	function showCompanyWebsite(){
		$model = $this->getModel('companies');
		$company = $model->increaseWebsiteCount(JRequest::getVar('companyId'));
		
		$this->setRedirect($company->website);
	}

	function increaseWebsiteCount(){
		$model = $this->getModel('companies');
		$company = $model->increaseWebsiteCount(JRequest::getVar('companyId'));
		
		JFactory::getApplication()->close();
	}
	
	function getReviewQuestionAnswersAjax(){
		$reviewId = JRequest::getVar('reviewId');
		$model = $this->getModel('Companies');

		$result = $model->getReviewQuestionAnswersAjax($reviewId);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	function saveAnswerAjax(){
		$answerId = JRequest::getVar('answerId');
		$answer = JRequest::getVar('answer');

		$data = array();
		$data["id"] = $answerId;
		$data["answer"] = $answer;

		$model = $this->getModel('Companies');

		$result = $model->saveAnswerAjax($data);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

 	function reportListing(){
    	$app = JFactory::getApplication();
    	$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
    	$model = $this->getModel('companies');
    	$post = JRequest::get('post');
    	
    	$companyId = JRequest::getVar('companyId');
    	$company = $model->getPlainCompany($companyId);

    	if($appSettings->captcha){
            $captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];
    		$namespace="jbusinessdirectory.contact";
    		$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
    		if(!$captcha->checkAnswer($captchaAnswer)){
    			$error = $captcha->getError();
    			$this->setMessage("Captcha error!", 'warning');
    			$message="Captcha error";
    			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
    			return;
    		}
    	}
    	
       $result = $model->reportListing();
      
        if($result){
        	$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_OFFER_REVIEW_ABUSE_SUCCESS'));
        }else{
        	$this->setMessage(JText::_('JLIB_APPLICATION_ERROR_SAVE_FAILED'));
        }
        
        $this->setRedirect(JBusinessUtil::getCompanyLink($company));
    }
    /**
     * Gets the appointment data from the front end, checks the captcha,
     * redirects in case of an error in the captcha. Calls a function to save the
     * appointment data. On success, it sends an email containing the information,
     * and redirects to the company page and displays a success message.
     *
     * On failure, it redirects back to the company page and displays an error.
     */
    function leaveAppointment()
    {
        $app = JFactory::getApplication();
        $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $model = $this->getModel('companies');
        $post = JRequest::get( 'post' );
        $companyId = JRequest::getVar('company_id');
        $company = $model->getPlainCompany($companyId);
        $companyEmail = $company->email;
        $companyName = $company->name;

        if($appSettings->captcha){
	        $captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];
            $namespace="jbusinessdirectory.contact";
            $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
            if(!$captcha->checkAnswer($captchaAnswer)){
                $error = $captcha->getError();
                $this->setMessage("Captcha error!", 'warning');
                $this->setRedirect(JBusinessUtil::getCompanyLink($company));
                return;
            }
        }

        $eventModel = $this->getModel('event');
        $event = $eventModel->getPlainEvent($post['event_id']);
        if ($eventModel->leaveAppointment($post))
        {
            $this->setMessage(JText::_('LNG_APPOINTMENT_RESERVED'));
            EmailService::sendAppointmentEmail($event, $post, $companyEmail, $companyName);
            $this->setRedirect(JBusinessUtil::getCompanyLink($company));
        }else {
            $this->setMessage(JText::_('LNG_ERROR_RESERVING_APPOINTMENT'), 'warning');
            $this->setRedirect(JBusinessUtil::getCompanyLink($company));
        }
    }

    /**
     * Method to get the providers that belong to a service
     *
     */
    function getServiceProvidersAjax()
    {
        $serviceId = JRequest::getVar('serviceId');

        $model = $this->getModel('Companies');
        $result = $model->getServiceProvidersAjax($serviceId);

        /* Send as JSON */
        header("Content-Type: application/json", true);
        echo json_encode($result);
        exit;
    }

    /**
     * Method to get the break and vacation days of a specific service provider
     *
     */
    function getVacationDaysAjax()
    {
        $providerId = JRequest::getVar('providerId');

        $model = $this->getModel('Companies');
        $result = $model->getVacationDaysAjax($providerId);

        /* Send as JSON */
        header("Content-Type: application/json", true);
        echo json_encode($result);
        exit;
    }

    /**
     * Method to get the available hours for a specific service and provider
     *
     */
    function getAvailableHoursAjax()
    {
        $providerId = JRequest::getVar('providerId');
        $serviceId = JRequest::getVar('serviceId');
        $date = JRequest::getVar('date');

        $model = $this->getModel('Companies');
        $result = $model->getAvailableHoursAjax($serviceId, $providerId, $date);

        /* Send as JSON */
        header("Content-Type: application/json", true);
        echo json_encode($result);
        exit;
    }

    function increaseShareCount() {
        $model = $this->getModel('companies');
        $company = $model->increaseShareCount(JRequest::getVar('itemId'), JRequest::getVar('itemType'));

        JFactory::getApplication()->close();
    }

    function getProjectDetailsAjax(){
        $projectId = JRequest::getVar('projectId');
        $model = $this->getModel('Companies');

        $result = $model->getProjectDetails($projectId);

        /* Send as JSON */
        header("Content-Type: application/json", true);
        echo json_encode($result);
        exit;
    }
}
