<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
class JBusinessDirectoryControllerOffer extends JControllerLegacy {
	
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Generate coupon
	 */
	public function generateCoupon() {
		$model = $this->getModel('Offer');

		$user = JFactory::getUser();
		if (!$user->guest) {
			$model->getCoupon();
		} else {
			JError::raiseWarning(500, JText::_('LNG_YOU_HAVE_TO_BE_LOGGED_IN'));
			$this->setRedirect('index.php?option=com_jbusinessdirectory&view=offers');
		}
	}

	function checkOffersAboutToExpire(){
		$model = $this->getModel('offer');
		$model->checkOffersAboutToExpire();
	}

    /**
     * Save the review for the offer
     */
    function saveReview(){
        $app = JFactory::getApplication();
        $model = $this->getModel('Offer');
        $post = JRequest::get( 'post' );
        $offerId = JRequest::getVar('itemId');
        $offer = $model->getPlainOffer($offerId);

        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $post["ipAddress"] = $ipAddress;
        $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];

        if($appSettings->captcha){
            $namespace="jbusinessdirectory.contact";
            $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
            if(!$captcha->checkAnswer($captchaAnswer)){
                $error = $captcha->getError();
                $app->setUserState('com_jbusinessdirectory.add.review.data', $post);
                $this->setMessage("Captcha error!", 'warning');
                $this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
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

        if ($model->saveReview($post)){
            $this->setMessage(JText::_('LNG_REVIEW_SAVED'));
            $this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
        }else {
            $this->setMessage(JText::_('LNG_ERROR_SAVING_REVIEW'),'error');
            $this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
        }
    }

    function cancelReview(){
        $this->setMessage(JText::_('LNG_OPERATION_CANCELLED'));
        $model = $this->getModel('Offer');
        $offerId = JRequest::getVar('itemId');
        $offer = $model->getPlainOffer($offerId);
        $this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
    }


    function reportAbuse(){
        $app = JFactory::getApplication();
        $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $model = $this->getModel('Offer');
        $post = JRequest::get('post');

        $offerId = JRequest::getVar('companyId');
        $offer = $model->getPlainOffer($offerId);
        $captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];

        if($appSettings->captcha){
            $namespace="jbusinessdirectory.contact";
            $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
            if(!$captcha->checkAnswer($captchaAnswer)){
                $error = $captcha->getError();
                $this->setMessage("Captcha error!", 'warning');
                $message="Captcha error";
                $this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
                return;
            }
        }

        $model = $this->getModel('Offer');

        $result = $model->reportAbuse($post);

        if($result){
            $this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_OFFER_REVIEW_ABUSE_SUCCESS'));
            $this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
        }else{
            $this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_OFFER_REVIEW_ABUSE_FAILED'),'warning');
            $this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));

        }
    }

    function saveReviewResponse(){
        $app = JFactory::getApplication();
        $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $model = $this->getModel('Offer');
        $post = JRequest::get( 'post' );
        $offerId = JRequest::getVar('companyId');
        $offer = $model->getPlainOffer($offerId);
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
                $this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
                return;
            }
        }

        if ($model->saveReviewResponse($post)){
            $this->setMessage(JText::_('LNG_REVIEW_RESPONSE_SAVED'));
            $this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
        }else {
            $this->setMessage(JText::_('LNG_ERROR_SAVING_REVIEW'),'warning');
            $this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
        }
    }

    function increaseReviewLikeCount(){
        $model = $this->getModel('Offer');
        $post = JRequest::get('post');
        $result = $model->increaseReviewLikeCount($post["reviewId"]);

        echo '<?xml version="1.0" encoding="utf-8" ?>';
        echo '<offer_statement>';
        echo '<answer result="'.$result.'" reviewId="'.$post["reviewId"].'"/>';
        echo '</offer_statement>';
        echo '</xml>';
        exit;
    }

    function increaseReviewDislikeCount(){
        $model = $this->getModel('Offer');
        $post = JRequest::get('post');
        $result = $model->increaseReviewDislikeCount($post["reviewId"]);

        echo '<?xml version="1.0" encoding="utf-8" ?>';
        echo '<offer_statement>';
        echo '<answer result="'.$result.'" reviewId="'.$post["reviewId"].'"/>';
        echo '</offer_statement>';
        echo '</xml>';
        exit;
    }

    function contactCompany(){
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $offermodel = $this->getModel('offer');
        $data = JRequest::get( 'post' );

        $offerId = JRequest::getVar('offer_Id');

        $contactId = JRequest::getVar('contactId');


        $offer = $offermodel->getPlainOffer($offerId);
        $captchaAnswer = !empty($data['recaptcha_response_field'])?$data['recaptcha_response_field']:$data['g-recaptcha-response'];

        if($appSettings->captcha){
            $namespace="jbusinessdirectory.contact";
            $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
            if(!$captcha->checkAnswer($captchaAnswer)){
                $error = $captcha->getError();
                $this->setMessage("Captcha error!", 'warning');
                $this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
                return;
            }
        }

        $data['description'] = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $data['contact_id'] = $contactId;
        $data['offer_name'] = $offer->subject;


        $result = $offermodel->contactOfferCompany($data);

        if($result){
            $this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_OFFER_CONTACTED'));
        }else{
            $this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED'));
        }


        $this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
    }

    function addBookmark(){
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $data = JRequest::get( 'post' );

        $model = $this->getModel('offer');

        $result = $model->addBookmark($data);

        if($result){
            $this->setMessage(JText::sprintf('COM_JBUSINESS_BOOKMARK_ADDED','<a href="'.JRoute::_('index.php?option=com_jbusinessdirectory&view=managebookmarks').'">'.JText::_('LNG_HERE').'</a>'));
        }else{
            $this->setMessage(JText::_('JLIB_APPLICATION_ERROR_SAVE_FAILED'));
        }

        if(isset($data['item_link']))
            $link = $data['item_link'];
        else {
            $offer = $model->getPlainOffer($data["item_id"]);
            $link = JBusinessUtil::getOfferLink($offer->id, $offer->alias);
        }

        $this->setRedirect($link);

    }

    function updateBookmark(){
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $data = JRequest::get( 'post' );

        $model = $this->getModel('offer');

        $result = $model->updateBookmark($data);

        if($result){
            $this->setMessage(JText::_('COM_JBUSINESS_BOOKMARK_UPDATED'));
        }else{
            $this->setMessage(JText::_('JLIB_APPLICATION_ERROR_SAVE_FAILED'));
        }

        if(isset($data['item_link']))
            $link = $data['item_link'];
        else {
            $offer = $model->getPlainOffer($data["item_id"]);
            $link = JBusinessUtil::getOfferLink($offer->id, $offer->alias);
        }

        $this->setRedirect($link);
    }

    function removeBookmark(){
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $data = JRequest::get( 'post' );

        $model = $this->getModel('offer');
        $result = $model->removeBookmark($data);

        if($result){
            $this->setMessage(JText::_('COM_JBUSINESS_BOOKMARK_REMVED'));
        }else{
            $this->setMessage(JText::_('JLIB_APPLICATION_ERROR_SAVE_FAILED'));
        }

        if(isset($data['item_link']))
            $link = $data['item_link'];
        else {
            $offer = $model->getPlainOffer($data["item_id"]);
            $link = JBusinessUtil::getOfferLink($offer->id, $offer->alias);
        }

        $this->setRedirect($link);
    }
}