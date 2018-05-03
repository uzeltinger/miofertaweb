<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');
require_once( JPATH_COMPONENT_ADMINISTRATOR.'/library/category_lib.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'offercoupon.php');

class JBusinessDirectoryModelOffer extends JModelItem {

	var $offer = null;
	
	function __construct() {
		parent::__construct();
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$this->offerId = JFactory::getApplication()->input->get('offerId');
		$this->offerId = intval($this->offerId);
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Offer', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	function getOffer() {
		$offersTable = JTable::getInstance("Offer", "JTable");
		$offer = $offersTable->getActiveOffer($this->offerId);
		if(empty($offer))
			return $offer;

		$this->offer = $offer;
		
		$offer->pictures = $offersTable->getOfferPictures($this->offerId);
		$this->increaseViewCount($this->offerId);
		
		$companiesTable = JTable::getInstance("Company", "JTable");
		$company = $companiesTable->getCompany($offer->companyId);
		$offer->company=$company;
		$offer->checkOffer = $this->checkOffer();
		
		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateEntityTranslation($offer, OFFER_DESCRIPTION_TRANSLATION);
			if(!empty($offer->company)){
				JBusinessDirectoryTranslations::updateEntityTranslation($offer->company, BUSSINESS_DESCRIPTION_TRANSLATION);
			}
		}

		$offer->attachments = JBusinessDirectoryAttachments::getAttachments(OFFER_ATTACHMENTS, $this->offerId, true);
		if (!empty($offer->attachments)) {
			$offer->attachments = array_slice($offer->attachments,0, $this->appSettings->max_attachments);
			foreach ($offer->attachments as $attach) {
				$attach->properties = JBusinessUtil::getAttachProperties($attach);
			}
		}
        $attributeConfig = JBusinessUtil::getAttributeConfiguration();
        $offer->company = JBusinessUtil::updateItemDefaultAtrributes($offer->company, $attributeConfig);
        if ($this->appSettings->apply_attr_offers) {
			$offer = JBusinessUtil::updateItemDefaultAtrributes($offer, $attributeConfig);
		}

        $userId = JFactory::getUser()->id;
        $offer->isBookmarked = false;
        if(!empty($userId)){
            $bookmarkTable = $this->getTable('Bookmark');
            $offer->bookmark = $bookmarkTable->getBookmark($offer->id, $userId, BOOKMARK_TYPE_OFFER);
        }

		//dispatch load offer
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onAfterJBDLoadOffer', array($offer));
		
		return $offer;
	}

	public function generateCouponCode(){
        $this->offerId = JFactory::getApplication()->input->get('id');
        $user = JFactory::getUser();
        $offerCouponsTable = JTable::getInstance("OfferCoupon", "JTable");
        $userCoupon = $offerCouponsTable->getUserCoupon($this->offerId,$user->id);

        if (!empty($userCoupon)){
            //if user have a coupon then return it
            return $userCoupon->id;
		}else{
        	//if user don`t have a coupon then generate one
        	$totalNumber = $offerCouponsTable->getTotalOfferCoupons($this->offerId)+1;

        	if ($totalNumber>1){
        	    $lastOfferCoupon = $offerCouponsTable->getLastOfferCoupon($this->offerId);
        	    $prefix = explode('-',$lastOfferCoupon->code)[0];
            }else{
                $prefix = strtoupper(substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 5)), 0, 3));
            }

            $number = str_pad($totalNumber, 4, '0', STR_PAD_LEFT);
            $code = $prefix."-".$number;

            //If the total number of available coupons in not reached save the coupon
            $offersTable = JTable::getInstance("Offer", "JTable");
            $offer = $offersTable->getOffer($this->offerId);

            $couponId = false;
            if((($totalNumber-1) < $offer->total_coupons)) {
                $couponId = $offerCouponsTable->saveCoupon($user->id, $this->offerId, $code);
            }

            return $couponId;
		}
    }

	public function getCoupon() {
	    $couponId = $this->generateCouponCode();

	    if($couponId) {
			$model = $this->getInstance('OfferCoupon', 'JBusinessDirectoryModel');
			$model->show($couponId);
		}
	}

	public function checkOffer() {
		$offerCouponsTable = JTable::getInstance("OfferCoupon", "JTable");
		$checkOffer = $offerCouponsTable->checkOffer($this->offerId);
		
		return $checkOffer;
	}

	function getOfferAttributes(){
		$attributesTable = $this->getTable('OfferAttributes');
		$categoryId = null;
		if($this->appSettings->enable_attribute_category) {
            $categoryId = -1;
			if(!empty($this->offer->main_subcategory))
				$categoryId = $this->offer->main_subcategory;
		}
		$result = $attributesTable->getOfferAttributes($this->offerId, $categoryId);

		return $result;
	}

	/**
	 * Get the offers that are about to expire and send an email to the offer owners
	 */
	function checkOffersAboutToExpire(){
		$offerTable = $this->getTable("Offer");
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$nrDays = $appSettings->expiration_day_notice;
		$offers = $offerTable->getOffersAboutToExpire($nrDays);
		foreach($offers as $offer){
			echo "sending expiration e-mail to: ".$offer->subject;
			$result = EmailService::sendOfferExpirationEmail($offer, $nrDays);
			if($result)
				$offerTable->updateExpirationEmailDate($offer->id);
		}
		exit;
	}

	/**
	 * Get All Offer Reviews
	 * @return mixed
	 */
	function getReviews(){
		$reviewsTable = $this->getTable("Review");
		$reviews = $reviewsTable->getReviews($this->offerId, $this->appSettings->show_pending_review,REVIEW_TYPE_OFFER);

		if(!empty($reviews)){
			foreach($reviews as $review){
				$review->responses =  $reviewsTable->getCompanyReviewResponse($review->id);
				if(isset($review->scores)){
					$review->scores = explode(",",$review->scores);
				}
				if(isset($review->answer_ids)){
					$review->answerIds = explode(",", $review->answer_ids);
				}
				$review->pictures = $reviewsTable->getReviewPictures($review->id);
			}
		}

		return $reviews;
	}

	/**
	 * Get the offer based on ID
	 * @param $offerid
	 * @return mixed
	 */
	function getPlainOffer($offerid){
		$offersTable = $this->getTable("Offer");
		$offer = $offersTable->getOffer($offerid);
		return $offer;
	}

	/**
	 * Save a new review on offer
	 *
	 * @param $data
	 * @return bool|int|JException|null|void
	 */
	function saveReview($data){
		$id	= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('review.id');
		$isNew = true;
		$rating = 0;
		if(isset($data["review"])){
			$rating = $data["review"];
			$data["rating"] = $rating;
		}

		$table = $this->getTable("Review");

		// Load the row if saving an existing item.
		if ($id > 0)
		{
			$table->load($id);
			$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());
		}

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());
		}

		$reviewId = $table->id;

		if(count($data['pictures'])>0){
			$oldId = $isNew?0:$id;
			$this->storePictures($data,  $reviewId, $oldId);
		}
		
		$table->updateReviewScore($data['itemId'], REVIEW_TYPE_OFFER);

		return true;
	}

	/**
	 * Increase the review Like Count
	 * @param $reviewId
	 * @return mixed
	 */
	function increaseReviewLikeCount($reviewId){
		$table = $this->getTable("Review");
		return $table->increaseReviewLike($reviewId);
	}

	/**
	 * Increase the review dislike count
	 * @param $reviewId
	 * @return mixed
	 */
	function increaseReviewDislikeCount($reviewId){
		$table = $this->getTable("Review");
		return $table->increaseReviewDislike($reviewId);
	}


	/**
	 * Save the review responses
	 *
	 * @param $data
	 * @return bool|int|JException|null|void
	 */
	function saveReviewResponse($data){
		//save in banners table
		$row = $this->getTable("reviewresponses");
		$data["state"]=1;

		// Bind the form fields to the table
		if (!$row->bind($data))
		{
			dump($this->_db->getErrorMsg());
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			dump($this->_db->getErrorMsg());
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			dump($this->_db->getErrorMsg());
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		$offersTable = JTable::getInstance("Offer", "JTable");
		$offer = $offersTable->getOffer($data['companyId']);
		$companiesTable = JTable::getInstance("Company", "JTable");
		$company = $companiesTable->getCompany($offer->companyId);
		//dump($company);exit;
		$ret = EmailService::sendOfferReviewResponseEmail($offer, $company, $data);

		return $ret;
	}

	/**
	 * Add a report for any review added on an offer
	 * @param $data
	 * @return bool|int|JException|null
	 */
	function reportAbuse($data){

		$data["state"]=1;
		$row = $this->getTable("reviewabuses");

		// Bind the form fields to the table
		if (!$row->bind($data)){

			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		$offersTable = JTable::getInstance("Offer", "JTable");
		$offer = $offersTable->getOffer($data['companyId']);

		$reviewsTable = $this->getTable("Review");
		$review = $reviewsTable->getReview($data["reviewId"]);

		$companiesTable = JTable::getInstance("Company", "JTable");
		$company = $companiesTable->getCompany($offer->companyId);

		$ret = EmailService::sendOfferReportAbuseEmail($data, $review, $company, $offer);

		return $ret;
	}

	function contactOfferCompany($data){
		$company = $this->getTable("Company");
		$company->load($data['companyId']);

		if (!empty($data['contact_id_offer']))
			$company->email = $data['contact_id_offer'];
		
		$data["description"] = nl2br(htmlspecialchars($data["description"], ENT_QUOTES));
		$ret = EmailService::sendOfferContactCompanyEmail($company, $data);

		return $ret;
	}

	/**
	 * Get all event videos
	 *
	 * @return mixed
	 */
	function getOfferVideos() {
		$table = $this->getTable("OfferVideos");
		$videos = $table->getOfferVideos($this->offerId );

		if(!empty($videos)) {
			$data = array();
			foreach($videos as $video) {
				$data = JBusinessUtil::getVideoDetails($video->url);
				$video->url = $data['url'];
				$video->videoType = $data['type'];
				$video->videoThumbnail = $data['thumbnail'];
			}
		}

		return $videos;
	}

    /**
     * Method to increase the view count of the offer, both on the
     * offer and statistics table
     *
     * @param $offerId int ID of the offer
     * @return bool
     */
    function increaseViewCount($offerId) {
        $offersTable = $this->getTable();
        $offersTable->increaseViewCount($offerId);

        // prepare the array with the table fields
        $data = array();
        $data["id"] = 0;
        $data["item_id"] = $offerId;
        $data["item_type"] = STATISTIC_ITEM_OFFER;
        $data["date"] = JBusinessUtil::convertToMysqlFormat(date('Y-m-d')); //current date
        $data["type"] = STATISTIC_TYPE_VIEW;
        $statisticsTable = $this->getTable("Statistics", "JTable");
        if(!$statisticsTable->save($data))
            return false;

        return true;
    }


	function storePictures($data, $reviewId, $oldId){
		$usedFiles = array();
		if(!empty($data['pictures'])){
			foreach ($data['pictures'] as $value) {
				array_push($usedFiles, $value["picture_path"]);
			}
		}

		$pictures_path = JBusinessUtil::makePathFile(JPATH_ROOT."/".PICTURES_PATH);
		$review_pictures_path = JBusinessUtil::makePathFile(REVIEW_PICTURES_PATH.($reviewId)."/");
		JBusinessUtil::removeUnusedFiles($usedFiles, $pictures_path, $review_pictures_path);

		$picture_ids 	= array();
		foreach($data['pictures'] as $value )
		{
			$row = $this->getTable('ReviewPictures');

			$pic = new stdClass();
			$pic->id = 0;
			$pic->reviewId = $reviewId;
			$pic->picture_info = $value['picture_info'];
			$pic->picture_path = $value['picture_path'];
			$pic->picture_enable = $value['picture_enable'];

			$pic->picture_path = JBusinessUtil::moveFile($pic->picture_path, $reviewId, $oldId,REVIEW_PICTURES_PATH);

			//dump("save");
			//dbg($pic);
			//exit;
			if (!$row->bind($pic))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());

			}
			// Make sure the record is valid
			if (!$row->check())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}

			// Store the web link table to the database
			if (!$row->store())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}

			$picture_ids[] = $this->_db->insertid();
		}


		$query = " DELETE FROM #__jbusinessdirectory_review_pictures
				WHERE reviewId = '".$reviewId."'
				".( count($picture_ids)> 0 ? " AND id NOT IN (".implode(',', $picture_ids).")" : "");

		//dbg($query);
		//exit;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}
		//~prepare photos
		//exit;
	}

    function addBookmark($data){
        //save in banners table
        $row = $this->getTable("Bookmark");

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            dump($this->_db->getErrorMsg());
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        // Make sure the record is valid
        if (!$row->check()) {
            dump($this->_db->getErrorMsg());
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            dump($this->_db->getErrorMsg());
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;
    }

    function updateBookmark($data){
        //save in banners table
        $row = $this->getTable("Bookmark");

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            dump($this->_db->getErrorMsg());
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        // Make sure the record is valid
        if (!$row->check()) {
            dump($this->_db->getErrorMsg());
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            dump($this->_db->getErrorMsg());
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;
    }

    function removeBookmark($data){
        $row = $this->getTable("Bookmark");
        return $row->delete($data["id"]);
    }

}
?>

