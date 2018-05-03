<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');


JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

class JBusinessDirectoryModelCompanies extends JModelLegacy
{ 
	
	var $company = null;
	
	function __construct(){
		parent::__construct();
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$this->companyId = JFactory::getApplication()->input->get('companyId');
		$this->companyId = intval($this->companyId);
	}

    function getCompany($cmpId=null){
        $companiesTable = $this->getTable("Company");
		$companyId = $this->companyId;
		
		if(!empty($companyId)){
			$companyId = str_replace(".html","",$companyId);
		}
		
		if(isset($cmpId))
			 $companyId = $cmpId;
		if(empty($companyId))
			return;

		$package = $this->getPackage($companyId);
		
		$company = $companiesTable->getCompany($companyId);
		$this->company = $company;
		
		if(empty($this->company)){
			return;
		}
		
        $company->business_hours = $this->getWorkingDays($companyId);

        $company->enableWorkingStatus = false;
        if (!empty($company->business_hours)) {
            foreach ($company->business_hours as $day) {
                if ($day->workHours["status"] == '1') {
                    $company->enableWorkingStatus = true;
                }
            }
        }

        if ($company->enableWorkingStatus)
            $company->workingStatus = $this->getWorkingStatus($company->business_hours,$company->time_zone);
        else
            $company->workingStatus = false;

		$categoryTable = $this->getTable("Category","JBusinessTable");
		
		$category = null;
		if(!empty($company->mainSubcategory)){
			$category = $categoryTable->getCategoryById($company->mainSubcategory);
		}else{
			if(!empty($company->categories)){
				$categories = explode('#|',$company->categories);
				$category = explode("|", $categories[0]);
				$category = $categoryTable->getCategoryById($category[0]);
			}
		}
		
		$path=array();
		if(!empty($category)){
			$path[]=$category;
			if(!empty($category)){
				while($category->parent_id != 1 && !empty($category->parent_id)){
					$category= $categoryTable->getCategoryById($category->parent_id);
					$path[] = $category;
				}
			}			
			$path = array_reverse($path);
			$company->path=$path;
		}

        $company->locations = array();
		if(isset($package->features) && in_array(SECONDARY_LOCATIONS, $package->features) || !$this->appSettings->enable_packages){
			$companyLocationsTable = $this->getTable('CompanyLocations');
			$company->locations = $companyLocationsTable->getCompanyLocations($companyId);
		}
        if(isset($package->features) && in_array(FEATURED_COMPANIES, $package->features) && $this->appSettings->enable_packages){
            $company->featured = 1;
        }

		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateEntityTranslation($company, BUSSINESS_DESCRIPTION_TRANSLATION);
			JBusinessDirectoryTranslations::updateCategoriesTranslation($company->path);
		}

		if(!empty($company->description) && $company->description==strip_tags($company->description)){
			$company->description = str_replace("\n", "<br/>", $company->description);
		}
			
		$userId = JFactory::getUser()->id;
		$company->isBookmarked = false;
		if(!empty($userId)){
			$bookmarkTable = $this->getTable('Bookmark');
			$company->bookmark = $bookmarkTable->getBookmark($companyId, $userId);
		}

		$company->attachments = JBusinessDirectoryAttachments::getAttachments(BUSSINESS_ATTACHMENTS, $companyId, true);
		if (!empty($company->attachments)) {
            foreach ($company->attachments as $attach) {
                $attach->properties = JBusinessUtil::getAttachProperties($attach);
            }
        }
        $attributeConfig = JBusinessUtil::getAttributeConfiguration();
        $company = JBusinessUtil::updateItemDefaultAtrributes($company,$attributeConfig);

        if (!empty($company->categories)) {
            $company->categories = explode('#|', $company->categories);
            foreach ($company->categories as $k=>&$category) {
                $category = explode("|", $category);
            }
        }

        $maxCategories = count($company->categories);
        if(!empty($package->max_categories) && $maxCategories > (int)$package->max_categories)
            $maxCategories = (int)$package->max_categories;

        if(!empty($company->categories)){
        	$company->categories = array_slice($company->categories, 0, $maxCategories);
        }

        //dispatch load listing
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onAfterJBDLoadListing', array($company));
		
		return $company;
	}

	function getServicesList(){
        $hasValidService = false;
        $servicesTable = $this->getTable('CompanyServicesList',"Table");
        $services = $servicesTable->getCompanyServices($this->companyId);
        foreach ($services as $service){
            if (!empty($service->service_section)){
                $hasValidService = true;
            }
        }
        if ($hasValidService) {
            return $services;
        }else{
            return array();
        }
    }

	function getPlainCompany($companyId){
		$companiesTable = $this->getTable("Company");
		$company = $companiesTable->getCompany($companyId);
		return $company;
	}

	function getUserRating(){
		//dump($_COOKIE['companyRatingIds']);
		$companyRatingIds=array();
		if(isset($_COOKIE['companyRatingIds']))
			$companyRatingIds = explode("#",$_COOKIE['companyRatingIds']);
			
		//dump($companyRatingIds);
		$ratingId =0;
		foreach($companyRatingIds as $companyRatingId){
			$temp = explode(",",$companyRatingId);
			if(strcmp($temp[0],$this->companyId)==0)
				$ratingId = $temp[1];
		}
		
		$ratingTable = $this->getTable("Rating");
		$rating = $ratingTable->getRating($ratingId);
		//dump($rating);
		
		//exit;
		return $rating;
	}
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Companies', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	
	function getReviews(){
		$reviewsTable = $this->getTable("Review");
		$reviews = $reviewsTable->getReviews($this->companyId, $this->appSettings->show_pending_review,REVIEW_TYPE_BUSINESS);

		if(!empty($reviews)){
			foreach($reviews as $review){
				$review->responses =  $reviewsTable->getCompanyReviewResponse($review->id);
				if(isset($review->scores)){
					$review->scores = explode(",",$review->scores);
				}
				if(isset($review->criteria_ids)){
					$review->criteriaIds = explode(",",$review->criteria_ids);
				}
				if(isset($review->answer_ids)){
					$review->answerIds = explode(",", $review->answer_ids);
				}
				if(isset($review->question_ids)){
					$review->questionIds = explode(",",$review->question_ids);

					$temp = array();
					$i = 0;
					foreach($review->questionIds as $val){
						$temp[$val] = $review->answerIds[$i];
						$i++;
					}
					$review->answerIds = $temp;
				}
                $review->pictures = $reviewsTable->getReviewPictures($review->id);
			}
		}

		return $reviews;
	}
	
	function getReviewCriterias(){
		$reviewsCriteriaTable = $this->getTable("ReviewCriteria");

		if(!$this->appSettings->enable_criteria_category)
		    $criterias = $reviewsCriteriaTable->getCriterias();
		else
		    $criterias = $reviewsCriteriaTable->getCriteriasByCategory($this->companyId);

		$result = array();
		foreach($criterias as $criteria){
			$result[$criteria->id]=$criteria;
		}
		$criterias = $result;
		
		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateReviewCriteriaTranslation($criterias);
		}
		
		return $criterias;
	}

	function getReviewQuestions(){
		$reviewQuestionsTable = $this->getTable("ReviewQuestion");
		$questions = $reviewQuestionsTable->getQuestions();

		$result = array();
		foreach($questions as $question){
			$result[$question->id]=$question;
		}
		$questions = $result;

		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateReviewQuestionTranslation($questions);
		}

		return $questions;
	}

	function getReviewQuestionAnswers(){
		$reviewAnswersTable = $this->getTable("ReviewQuestionAnswer");
		$answers = $reviewAnswersTable->getAnswersByCompany($this->companyId);

		$result = array();
		foreach($answers as $answer){
			$result[$answer->id]=$answer;
		}
		$answers = $result;

		return $answers;
	}
	
	function getCompanyImages(){
		$query = "SELECT *
				FROM #__jbusinessdirectory_company_pictures
				WHERE picture_enable =1 and companyId =".$this->companyId ."
				ORDER BY id ";

		$pictures =  $this->_getList( $query );
		$pictures =  $this->_getList( $query );

		return $pictures;
	}
	
	function getCompanyVideos() {
		$table = $this->getTable("companyvideos");
		$videos = $table->getCompanyVideos($this->companyId);

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

	function getCompanySounds() {
		$table = $this->getTable("companysounds");
		$sounds = $table->getCompanySounds($this->companyId);
	
		if(!empty($sounds)) {
			$data = array();
			foreach($sounds as $sound) {
				
			}
		}
	
		return $sounds;
	}
	
	
	function getCompanyAttributes(){
		$attributesTable = $this->getTable('CompanyAttributes');
		$categoryId = null;
		if($this->appSettings->enable_attribute_category){
            $categoryId = -1;
		    if(!empty($this->company->mainSubcategory))
			    $categoryId= $this->company->mainSubcategory;
		}

		$result = $attributesTable->getCompanyAttributes($this->companyId, $categoryId);
		
		return $result;
	}

    /**
     * Method that retrieves the contacts belonging to a certain company.
     * checks if at least one of them has an email. If so, it returns the contacts,
     * otherwise, if none of the contact has an email, return null
     *
     * @return bool|array
     */
	function getCompanyContacts() {
		$companyContactTable = $this->getTable('CompanyContact', 'Table');
		$contacts = $companyContactTable->getAllCompanyContacts($this->companyId);

		return $contacts;
	}

    /**
     * Method that retrieves the testimonials belonging to a certain company.
     * checks if at least one of them has an title and a name. If so, it returns the testimonials,
     * otherwise, if none of the testimonials has an email, return null
     *
     * @return bool|array
     */
    function getCompanyTestimonials() {
        $companyTestimonialsTable = $this->getTable('CompanyTestimonials', 'Table');
        $testimonials = $companyTestimonialsTable->getAllCompanyTestimonials($this->companyId);
        $hasValidTestimonial = false;
        foreach ($testimonials as $testimonial){
            if (!empty($testimonial->testimonial_title) && !empty($testimonial->testimonial_name)){
                $hasValidTestimonial = true;
            }
        }
        if ($hasValidTestimonial) {
            return $testimonials;
        }else{
            return array();
        }
    }
	
	/**
	 * Retrieve all contacts with email address
	 */
	function getCompanyContactsWithEmail() {
		$companyContactTable = $this->getTable('CompanyContact', 'Table');
		$contacts = $companyContactTable->getAllCompanyContacts($this->companyId);
		
		$result = array();
		foreach($contacts as $contact) {
			if(!empty($contact->contact_email))
				$result[] = $contact;
		}
		
		return $result;
	}

	function getCompanyDepartments(){
	    $companyContactTable = $this->getTable('CompanyContact', 'Table');
	    return $companyContactTable->getAllCompanyContactsDepartment($this->companyId);
	}

	function getCompanyOffers(){
		$table = $this->getTable("Offer");
		$offers =  $table->getCompanyOffers($this->companyId);

		if(!empty($offers)){
			JBusinessDirectoryTranslations::updateOffersTranslation($offers);
			foreach($offers as $offer){
				switch($offer->view_type){
					case 1:
						$offer->link = JBusinessUtil::getofferLink($offer->id, $offer->alias);
						break;
					case 2:
						$offer->link = JRoute::_('index.php?option=com_content&view=article&id='.$offer->article_id);
						break;
					case 3:
						$offer->link = $offer->url;
						break;
					default:
						$offer->link = JBusinessUtil::getofferLink($offer->id, $offer->alias);
				}
				
				if (!empty($offer->categories)) {
					$offer->categories = explode('#|', $offer->categories);
					foreach ($offer->categories as &$category) {
						$category = explode("|", $category);
					}
				}

                $userId = JFactory::getUser()->id;
                $offer->isBookmarked = false;
                if(!empty($userId)){
                    $bookmarkTable = $this->getTable('Bookmark');
                    $offer->bookmark = $bookmarkTable->getBookmark($offer->id, $userId, BOOKMARK_TYPE_OFFER);
                }
			}
		}
		return $offers;
	}
	
	function getCompanyEvents(){
		$table = $this->getTable("Event");
		$events = $table->getCompanyEvents($this->companyId);
		if(!empty($events) && $this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateEventsTranslation($events);
		}

		$recurringEvents = array();
		foreach ($events as $key => $event){
            $recurringEvents[] = $event->id;
			if ($event->recurring_id > 0) {
                if (in_array($event->recurring_id,$recurringEvents)) {
                    unset($events[$key]);
                } else {
                    $recurringEvents[] = $event->recurring_id;
                }
            }
		}
		return $events;
	}
	
	/* 
	 * Retrieve the currect active package for a listing
	 */
	function getPackage($companyId=null){
		if(empty($companyId)){
			$companyId = $this->companyId;
		}
		$table = $this->getTable("Package"); 
		$package = $table->getCurrentActivePackage($companyId);

		return $package;
	}
	
	function claimCompany($data){
		$companiesTable = $this->getTable("Company");
		$companyId = $this->companyId;
		
		if($companiesTable->claimCompany($data)){
			return $this->updateCompanyOwner($data['companyId'], $data['userId']);
		}
		return false;
	}
	
	function saveReview($data){
        $id	= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('review.id');
        $isNew = true;

		$criterias = array();
		$questions = array();
		foreach($data as $key=>$value){
			if(strpos($key, "criteria")===0){
				$key = str_replace("criteria-", "", $key);
				$criterias[$key]=$value;
			}
			else if(strpos($key, "question")===0){
				$key = str_replace("question-", "", $key);
				$questions[$key]=$value;
			}
		}
		
		$rating = 0;
		if(isset($data["review"])){
			$rating = $data["review"];
		}
		if(!empty($criterias)){
			$score = 0;
			foreach($criterias as $key=>$value){
				$score += $value;
			}
			$rating = $score/count($criterias);
			$data["rating"] = number_format($rating,2); 
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
		
		if($this->appSettings->show_pending_review){
			$table->updateReviewScore($data['itemId'], REVIEW_TYPE_BUSINESS);
		}
		
		$reviewId = $table->id;
		foreach($criterias as $key=>$score){
			$table = $this->getTable("ReviewUserCriteria");
			
			$criteriaObj = array();
			$criteriaObj["review_id"]= $reviewId;
			$criteriaObj["criteria_id"]= $key;
			$criteriaObj["score"]= $score;
			// Bind the data.
			//dump($criteriaObj);
			if (!$table->bind($criteriaObj))
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
		}
		
		foreach($questions as $key=>$value){
			$table = $this->getTable("ReviewQuestionAnswer");

			$questionObj = array();
			$questionObj["review_id"] = $reviewId;
			$questionObj["question_id"] = $key;
			$questionObj["answer"] = $value;
			$questionObj["user_id"] = $data["user_id"];

			// Bind the data.
			if (!$table->bind($questionObj))
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
		}

        if(count($data['pictures'])>0){
            $oldId = $isNew?0:$id;
            $this->storePictures($data,  $reviewId, $oldId);
        }

		return true;
	}
	
	function saveRating($data){
		$table = $this->getTable("Rating");
		$ratingId = $table->saveRating($data);
		$table->updateCompanyRating($data['companyId']);
		
		return $ratingId;
	}
	
	function getRatingsCount(){
		$companyId = $this->companyId;
		$table = $this->getTable("Rating");
		return $table->getNumberOfRatings($companyId);
	}
	
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
		
		$reviewsTable = $this->getTable("Review");
		$review = $reviewsTable->getReview($data["reviewId"]);
		$company=$this->getCompany($data["companyId"]);
		$ret = EmailService::sendReportAbuseEmail($data, $review, $company);
		
		return $ret;
	}
	
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
		
		$company=$this->getCompany($data["companyId"]);
		$ret = EmailService::sendReviewResponseEmail($company, $data);
		
		return true;
	}

	/**
	 * Saves a single Review Question Answer
	 * @param $data
	 * @return bool
	 */
	function saveAnswerAjax($data){
		//save in banners table
		$row = $this->getTable("ReviewQuestionAnswer");

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

		return true;
	}
	
	function updateCompanyOwner($companyId, $userId){
		$companiesTable = $this->getTable("Company");
		return $companiesTable->updateCompanyOwner($companyId, $userId);
	}
	
	function getUserCompanies(){
		$user = JFactory::getUser();
		if($user->id == 0 ){
			return null;
		}
		$companiesTable = $this->getTable("Company");
		$companies = $companiesTable->getCompaniesByUserId($user->id);
		
		return $companies;
	}
	
	function getCompanyByName($companyName){
		$companiesTable = $this->getTable("Company");
		return $companiesTable->getCompanyByName($companyName);
	}
	
	function contactCompany($data){
		$company = $this->getTable("Company");
		$company->load($data['companyId']);

		if(!empty($data['contact_id'])) {
			$contactTable = $this->getTable("CompanyContact", "Table");
			$contact = $contactTable->load($data['contact_id']);

			// if the contact has no email, keep the default company email
			if(!empty($contact->contact_email))
			    $company->email = $contact->contact_email;
		}

		$data["description"] = nl2br(htmlspecialchars($data["description"], ENT_QUOTES));
		
		$company->increaseContactsNumber($data['companyId']);
		$ret = EmailService::sendContactCompanyEmail($company, $data);

        // prepare the array with the table fields
        $tmp = array();
        $tmp["id"] = 0;
        $tmp["item_id"] = $data['companyId'];
        $tmp["item_type"] = STATISTIC_ITEM_BUSINESS;
        $tmp["date"] = JBusinessUtil::convertToMysqlFormat(date('Y-m-d')); //current date
        $tmp["type"] = STATISTIC_TYPE_CONTACT;
        $statisticsTable = $this->getTable("Statistics", "JTable");
        if(!$statisticsTable->save($tmp))
            return false;
	
		return $ret;
	}
	
	function addBookmark($data){
		//save in banners table
		$row = $this->getTable("Bookmark");
		
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
		
		return true;
	}
	
	function updateBookmark($data){
		//save in banners table
		$row = $this->getTable("Bookmark");
	
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
	
		return true;
	}
	
	function removeBookmark($data){
		$row = $this->getTable("Bookmark");
		return $row->delete($data["id"]);
	}
	
	
	function requestQuoteCompany($data){
		$company = $this->getTable("Company");
		$company->load($data['companyId']);
	
		$company->increaseContactsNumber($data['companyId']);
		$ret = EmailService::sendRequestQuoteEmail($data, $company);
	
		return $ret;
	}

	/**
	 * Get the listings that are about to expire and send an email to business owners
	 */
	function checkBusinessAboutToExpire(){
		$companyTable = $this->getTable("Company");
		$orderTable = $this->getTable("Order");
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$nrDays = $appSettings->expiration_day_notice;
		$companies = $companyTable->getBusinessAboutToExpire($nrDays);
		foreach($companies as $company){
			echo "sending expiration e-mail to: ".$company->name;
			$result = EmailService::sendExpirationEmail($company, $nrDays);
			if($result){
				$orderTable->updateExpirationEmailDate($company->orderId);
			}
		}
		exit;
	}

    function getClaimDetails(){
        $companiesTable = $this->getTable("Company");
        return $companiesTable->getClaimDetails((int) $this->companyId);
    }
	
	/**
	 * Increase the website access number when clicked
	 * 
	 * @param int $companyId
	 * @return mixed
	 */
	function increaseWebsiteCount($companyId){
		
		$company = $this->getCompany();
		
		$companiesTable = $this->getTable("Company");
		$companiesTable->increaseWebsiteCount($company->id);

        // prepare the array with the table fields
        $data = array();
        $data["id"] = 0;
        $data["item_id"] = $companyId;
        $data["item_type"] = STATISTIC_ITEM_BUSINESS;
        $data["date"] = JBusinessUtil::convertToMysqlFormat(date('Y-m-d')); //current date
        $data["type"] = STATISTIC_TYPE_WEBSITE_CLICK;
        $statisticsTable = $this->getTable("Statistics", "JTable");
        if(!$statisticsTable->save($data))
            return false;

		return $company;
	}
	
	function increaseReviewLikeCount($reviewId){
		$table = $this->getTable("Review");
		return $table->increaseReviewLike($reviewId);
	}
	
	function increaseReviewDislikeCount($reviewId){
		$table = $this->getTable("Review");
		return $table->increaseReviewDislike($reviewId);
	}
	
	function increaseViewCount(){
		$companiesTable = $this->getTable("Company");
		if(!$companiesTable->increaseViewCount($this->companyId))
		    return false;

        // prepare the array with the table fields
        $data = array();
        $data["id"] = 0;
        $data["item_id"] = $this->companyId;
        $data["item_type"] = STATISTIC_ITEM_BUSINESS;
        $data["date"] = date('Y-m-d H:i:s'); 
        $data["type"] = STATISTIC_TYPE_VIEW;
        $statisticsTable = $this->getTable("Statistics", "JTable");
        if(!$statisticsTable->save($data))
            return false;

        return true;
	}
	
	function getViewCount(){
		return $this->increaseViewCount();
	}

	function saveCompanyMessages(){
		$data = array();
		$data["name"] = JRequest::getVar('firstName');
		$data["surname"] = JRequest::getVar('lastName');
		$data["email"] = JRequest::getVar('email');
		$data["message"] = JRequest::getVar('description');
		$data["company_id"] = $this->companyId;
		$data["contact_id"] = JRequest::getVar('contact_id');

		$table = $this->getTable("CompanyMessages");

		$data["message"] = htmlspecialchars($data["message"]);
		
		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());
			dump($table->getError());
			return false;
		}

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());
			dump($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());
			dump($table->getError());
			return false;
		}
		
		return true;
		
	}

 function reportListing(){

        $applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $companiesTable = $this->getTable("Company");
        $companyId = $this->companyId;
        $company = $companiesTable->getCompany($companyId);
        $message = JRequest::getVar('abuseMessage',null);
        $email = JRequest::getVar('reporterEmail',null);
        $reportCause = JRequest::getVar('report-cause',null);

        $result = EmailService::sendAbuseEmail($company, $email, $message, $reportCause);

		return $result;
    }

    /**
     * Return related companies based on the id of the actual company
     *
     * @return mixed
     */
    function getRelatedCompanies()
    {
        $table = $this->getTable("Company");
        $companies = $table->getRelatedCompanies($this->companyId);
        return $companies;
    }

    /**
     * Return company memberships based on the id of the actual company
     *
     * @return mixed
     */
    function getCompanyMemberships()
    {
        $table = $this->getTable("Memberships",'Table');
        $memberships = $table->getCompanyMemberships($this->companyId);
        foreach ($memberships as $membership){
            if(empty($membership->logo_location)){
                $membership->logo_location = '/no_image.jpg';
            }
        }
        return $memberships;
    }

    /**
     * Method to retrieve all associated events to a particular company
     *
     * @return mixed
     */
    function getAssociatedEvents()
    {
        $table = $this->getTable("EventAssociatedCompanies");

        $events = $table->getAssociatedEventsDetails($this->companyId);

        $eventTable = $this->getTable('Event');
        foreach($events as $event)
            $event->pictures = $eventTable->getEventPictures($event->id);

        return $events;
    }

    /**
     * Method to retrieve all services belonging to a particular company
     *
     * @return mixed
     */
    function getServices()
    {
        $table = $this->getTable("CompanyServices");

        $searchDetails = array();
        $searchDetails["companyId"] = $this->companyId;
        $services = $table->getServices($searchDetails);

        if($this->appSettings->enable_multilingual){
            JBusinessDirectoryTranslations::updateCompanyServicesTranslation($services);
        }

        return $services;
    }

    /**
     * Method that gets the service providers associated with a
     * certain service and renders them in a list.
     *
     * @param $serviceId int ID of the service
     * @return string html output
     */
    function getServiceProvidersAjax($serviceId)
    {
        $table = $this->getTable("CompanyServiceProviders");
        $providers = $table->getServiceProviders($serviceId);

        if($this->appSettings->enable_multilingual){
            JBusinessDirectoryTranslations::updateCompanyProvidersTranslation($providers);
        }

        $html = '';
        foreach($providers as $provider)
        {
            $html .= '<a href="javascript:void(0)" class="service-link" onclick="selectProvider('.$provider->id.')"><i class="dir-icon-user"></i> ';
            $html .= $provider->name;
            $html .= '</a>';
            $html .= '<br/>';
        }

        return $html;
    }

    /**
     * Method that retrieves information about the vacations from the database
     * and organizes them into dates which will define the free days
     *
     * @param $providerId int ID of the provider whose dates will be retrieved
     * @return array organized array of the vacation dates
     */
    function getVacationDaysAjax($providerId)
    {
        $table = $this->getTable("CompanyServiceProviderHours");
        $result = $table->getVacations($providerId);

        $vacations = JBusinessUtil::processProviderVacationDays($result);

        return $vacations;
    }

    /**
     * Method to get the available booking hours for a certain provider. This method
     * retrieves all the information about the start and end hours (work & break) for a
     * particular date.
     *
     * It organizes all the hours between the start and work hours (with an interval equal
     * to the service duration) excluding the break hours intervals, into an array.
     *
     * @param $serviceId int ID of the service
     * @param $providerId int ID of the provider
     * @param $date string date
     * @return array|bool array containing the hours | false if start hour empty
     */
    function getAvailableHoursAjax($serviceId, $providerId, $date)
    {
        $date = JBusinessUtil::convertToMysqlFormat($date);
        $table = $this->getTable("CompanyServiceProviderHours");
        $results = $table->getAvailableHours($serviceId, $providerId, $date);

        $availableHours = JBusinessUtil::processProviderAvailableHours($results);

        $html = array();

        foreach($availableHours as $key=>$hours) {
            $html[$key] = '';
            foreach($hours as $hour) {
                $html[$key] .= '<li>';
                $html[$key] .= '<a href="javascript:void(0)" class="service-link" onclick="selectHour(\''.$hour.'\')">'.JBusinessUtil::convertTimeToFormat($hour).'</a>';
                $html[$key] .= '</li>';
            }
        }

        return $html;
    }

    public function getDefaultPackage(){
        $packageTable = $this->getTable("Package");
        $package = $packageTable->getDefaultPackage();

        if(empty($package)){
            $package = new stdClass();
            $package->name = JText::_("LNG_NO_ACTIVE_PACKAGE");
            $package->max_attachments=0;
            $package->max_pictures=0;
            $package->max_categories=0;
            $package->max_videos=0;
            $package->price = 0;
            $package->features = array();
            return $package;
        }

        $packageTable = $this->getTable("Package");
        $package->features = $packageTable->getSelectedFeaturesAsString($package->id);

        if(isset($package->features))
            $package->features = explode(",",$package->features);

        if(!is_array($package->features)){
            $package->features = array($package->features);
        }

        return $package;
    }

    /**
     * Method that returns the working periods. If no working hours are present on the
     * hours table, it will check the companies table, retrieve the hours from there and
     * convert them to the new format.
     *
     * @param $companyId int ID of the company
     * @return mixed
     */
    private function getWorkingHours($companyId) {
        $table = $this->getTable('CompanyServiceProviders', 'JTable');
        $workingHours = $table->getStaffTimetable($companyId, STAFF_WORK_HOURS, BUSINESS_HOURS);

        // if no working hours are set, check the old business hours
        if(empty($workingHours)) {
            if(empty($companyId))
                return $workingHours;

            $companyTable = $this->getTable("Company");
            $company = $companyTable->getCompany($companyId);

            // convert the old business hours to the new format
            if(!empty($company->business_hours)) {
                $openingHours = explode(",", $company->business_hours);

                for($i=0;$i<7;$i++){
                    $tmp = new stdClass();
                    $tmp->startHours = $openingHours[$i*2];
                    $tmp->endHours = $openingHours[$i*2+1];
                    $tmp->statuses = 1;
                    $tmp->periodIds = '';

                    if($tmp->startHours == "closed")
                        $tmp->startHours = '';
                    if($tmp->endHours == "closed")
                        $tmp->endHours = '';

                    $workingHours[$i] = $tmp;
                }
            }
        }

        return $workingHours;
    }

    /**
     * Method that returns the break periods
     *
     * @param $companyId int ID of the company
     * @return mixed
     */
    private function getBreakHours($companyId) {
        $table = $this->getTable('CompanyServiceProviders', 'JTable');
        $result = $table->getStaffTimetable($companyId, STAFF_BREAK_HOURS, BUSINESS_HOURS);

        $breakHours = array();
        foreach ($result as $hours)
            $breakHours[$hours->weekday] = $hours;

        return $breakHours;
    }

    /**
     * Method that returns a complex array organized in a way that it may
     * be simply used in the view.
     *
     * @param $companyId int ID of the company
     * @return array
     */
    public function getWorkingDays($companyId)
    {
        $workHours = $this->getWorkingHours($companyId);
        $breakHours = $this->getBreakHours($companyId);
        $workingDays = JBusinessUtil::getWorkingDays($workHours, $breakHours);

        return $workingDays;
    }

    /**
     *  This function take the business working hours and the time zone set`s these on the edit view and set the status
     *  of the business if it is open or closed on different times.
     *
     * @param $business_hours array here are all the business hours of the business
     * @param $time_zone string here is the offset of the time_zone, taken from the database
     * @return bool
     */
    public function getWorkingStatus($business_hours,$time_zone){
        if (!empty($business_hours)) {

            //set timezone and create a new object date time for that timezone
            $original = new DateTime("now", new DateTimeZone('GMT'));
            $timezoneName = timezone_name_from_abbr("", $time_zone * 3600, false);
            $modified = $original->setTimezone(new DateTimezone($timezoneName));

            //get exact time and day index of the date time object
            foreach ($modified as $key => &$mod) {
                if ($key == 'date'){
                    $currentTime = date('Y-m-d h:i A', strtotime($mod));
                    $dayIndex = date('N', strtotime($mod));
                }
            }

            //check if the day index is a working day or not and if not set workingStatus on false
            if (isset($business_hours[$dayIndex]) && !empty($business_hours[$dayIndex]) && $business_hours[$dayIndex]->workHours["status"]==1) {

                $day = $business_hours[$dayIndex];

                //check if start time and end time of the working day is set.. if they are left empty than the business is
                //supposed to be open and the workingStatus will be set open
                if ((isset($day->workHours["start_time"]) && !empty($day->workHours["start_time"])) && (isset($day->workHours["end_time"]) && !empty($day->workHours["end_time"]))) {

                    $startTime = date("Y-m-d") . " " . $day->workHours["start_time"];
                    $endTime = date("Y-m-d") . " " . $day->workHours["end_time"];

                    //check if there is a break on the working day or not and if not than
                    //will be processed only the working hours
                    if ((isset($day->breakHours["start_time"][0]) && !empty($day->breakHours["start_time"][0])) && (isset($day->breakHours["end_time"][0]) && !empty($day->breakHours["end_time"][0]))) {

                        $startBreakTime = date("Y-m-d") . " " . $day->breakHours["start_time"][0];
                        $endBreakTime = date("Y-m-d") . " " . $day->breakHours["end_time"][0];

                        if (JBusinessUtil::checkDateInterval($startTime, $startBreakTime, $currentTime, false, true) ||
                                JBusinessUtil::checkDateInterval($endBreakTime, $endTime, $currentTime, false, true) ) {
                            return true;
                        } else {
                            return false;
                        }
                    } elseif (JBusinessUtil::checkDateInterval($startTime, $endTime, $currentTime, false, true)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return true;
                }
            }else {
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * Increase the share count of a business, offer or event, depending
     * on the itemType given.
     *
     * @param $id int ID of the item
     * @param $itemType int type of the item, business(1), offer(2) or event(3)
     * @return bool
     */
    function increaseShareCount($id, $itemType) {
        // prepare the array with the table fields
        $data = array();
        $data["id"] = 0;
        $data["item_id"] = $id;
        $data["item_type"] = $itemType;
        $data["date"] = JBusinessUtil::convertToMysqlFormat(date('Y-m-d')); //current date
        $data["type"] = STATISTIC_TYPE_SHARE;
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

    function getCompanyProjects(){
        $companyProjectsTable = $this->getTable('Projects', 'Table');
        $projects = $companyProjectsTable->getCompanyProjects($this->companyId);

        foreach ($projects as &$project){
            if(!empty($project->pictures)){
                $project->pictures = explode('#|',$project->pictures);
                $project->nrPhotos = count($project->pictures);
                foreach ($project->pictures as &$picture) {
                    $picture = explode("|", $picture);
                }
                $project->picture_path = $project->pictures[0][3];
            }else{
                $project->nrPhotos = 0;
                $project->picture_path = "";
            }
        }
        return $projects;
    }

    function getProjectDetails($projectId){

        $projectTable = JTable::getInstance("Projects", "Table", array());
        $project = $projectTable->getProject($projectId);

        if(!empty($project->pictures)){
            $project->pictures = explode('#|',$project->pictures);
            $project->nrPhotos = count($project->pictures);
            foreach ($project->pictures as $index => $picture) {
                $project->pictures[$index] = explode("|", $picture);
            }
            $project->picture_path = $project->pictures[0][3];
        }else{
            $project->nrPhotos = 0;
            $project->picture_path = "";
        }

        $projectGalleryImages = "";
        $projectGalleryImages .= '<div id="projectImageGallery"  style="display:none;">';

        if (!empty($project->pictures)) { ?>
            <?php foreach($project->pictures as $picture) {
                $projectGalleryImages .= '<img src="'.JURI::root().PICTURES_PATH.$picture[3].'" alt="'. $picture[2] .'"  data-image="'.JURI::root().PICTURES_PATH.$picture[3].'"  data-description="'. $picture[2] .'"/>';
                $projectGalleryImages .= '</li>';
            }
        } else {
            $projectGalleryImages .= JText::_("LNG_NO_IMAGES");
        }
        $projectGalleryImages .= '</div>';

        $project->projectGalleryImages = $projectGalleryImages;
        $project->breadCrumbsName = $project->name;
        $name = "";
        $project->name = $name." ".$project->name;

        return $project;
    }

    /**
	 * Method that retrieves all category ids related to the products of the company
	 *
     * @return array containing ids of the categories
	 *
	 * @since 4.9.0
     */
    private function getProductCategoriesIds() {
        $table = $this->getTable("Offer");
        $offers = $table->getCompanyOffers($this->companyId, 0, 0, OFFER_TYPE_PRODUCT);

        $categoryIds = array();
        foreach ($offers as $offer) {
            $catIds = explode(',', $offer->categoryIds);

            $categoryIds = array_unique(array_merge($categoryIds, $catIds));
        }

        return $categoryIds;
    }

    /**
	 * Method that retrieves all categories(only 2 levels) and their parent categories(only if they are lvl2 categories)
	 * from a pre existing array of category ids. It rearranges them into a multi dimensional array. The first element
	 * of the array(itself an array) will contain the parent categories. The other element, will contain all the children categories
	 * of the parents, grouped by the parent category ID as array index.
	 *
	 * (Note: Only the lvl2 categories that have products will be retrieved, lvl1 categories may or may not
	 * have products directly associated to them)
	 *
     * @return array|null
	 *
	 * @since 4.9.0
     */
    public function getProductCategories() {
        $table = $this->getTable("Offer");
        $catIds = $this->getProductCategoriesIds();

        $catIds = array_filter($catIds);
        if (empty($catIds))
            return null;

        $categories = $table->getProductCategories($catIds, $this->companyId);

        $productCategories = array();
        $productCategories[1] = array();

        // include only the parent categories (lvl1)
        foreach ($categories as $cat) {
            if ($cat->level == 1)
                $productCategories[1][] = $cat;
        }

        $productCategories[2] = array();
        foreach ($productCategories[1] as $category) {
            $productCategories[2][$category->id] = new stdClass();
            $productCategories[2][$category->id]->parent = $category->name;

            // include the parent category in the children's array, if there's a product
			// associated directly to that parent category
            if (!empty($category->offerIds))
                $productCategories[2][$category->id]->categories[] = $category;

            // include all children categories (lvl2) that belong to this specific parent
            foreach ($categories as $cat) {
                if ($cat->parent_id == $category->id && !empty($cat->offerIds))
                    $productCategories[2][$category->id]->categories[] = $cat;
            }
        }

        return $productCategories;
    }
}
?>