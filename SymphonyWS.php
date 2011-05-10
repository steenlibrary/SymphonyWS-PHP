<?
/* Symphony Web Services PHP Wrapper (SOAP)
 * Author: Michael Gillen
 * E-mail: mlgillen@sfasu.edu
 * Last Modified: 05/10/2011
 * Notes: Includes standard, security, and patron services.  Admin service coming later.
 */
 
class SymphonyWS
{
	private $STANDARD_WSDL = "soap/standard?wsdl";
	private $SECURITY_WSDL = "soap/security?wsdl";
	private $PATRON_WSDL = "soap/patron?wsdl";
	private $ADMIN_WSDL = "soap/admin?wsdl";

	private $BASE_URL = "http://localhost:8080/symws/";
	private $WS_HEADER = "http://www.sirsidynix.com/xmlns/common/header";
	private $proxyhost="localhost:8080/symws/soap/standard";  
	private $proxyport = 8080;
	private $clientID = "Steenfind";
	
	private $standardService;
	private $securityService;
	private $patronService;
	private $adminService;
	
	private $login;
	
	function __construct(){
		$headerbody = array("clientID" => $this->clientID);       
		$header = new SoapHeader($this->WS_HEADER, "SdHeader", $headerbody); 
		$this->standardService = new SoapClient($this->BASE_URL.$this->STANDARD_WSDL,
								array("proxy_host" => $this->proxyhost,"proxy_port" => $this->proxyport, "trace" => 1));           
		$this->standardService->__setSoapHeaders($header);
		
		$headerbody = array("clientID" => $this->clientID);       
		$header = new SoapHeader($this->WS_HEADER, "SdHeader", $headerbody);
			
		$this->securityService = new SoapClient($this->BASE_URL.$this->SECURITY_WSDL,
			  array("proxy_host" => $this->proxyhost, "proxy_port" => $this->proxyport, "trace" => 1));           
		$this->securityService->__setSoapHeaders($header);
		
		$headerbody = array("clientID" => $this->clientID);
		$header = new SoapHeader($this->WS_HEADER, "SdHeader", $headerbody);
			
		$this->patronService = new SoapClient($this->BASE_URL.$this->PATRON_WSDL,
				array("proxy_host" => $this->proxyhost,"proxy_port" => $this->proxyport, "trace" => 1));           
		$this->patronService->__setSoapHeaders($header); 
	}
	
	/* Standard */
	
	function license()
	{
		try {
			$result = $this->standardService->license();
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function lookupMostPopular($listType, $libraryFilter = null, $sourceFile = null, $linesToDisplay = null)
	{
		$options = array();
		$options["listType"] = $listType;
		if($libraryFilter != null)
			$options["libraryFilter"] = $libraryFilter;
		if($sourceFile != null)
			$options["sourceFile"] = $sourceFile;
		if($linesToDisplay != null)
			$options["linesToDisplay"] = $linesToDisplay;
			
		try {
			$result = $this->standardService->lookupMostPopular($options);
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function lookupTitleInfo($titleID)
	{
		try {
			$result = $this->standardService->lookupTitleInfo(array("titleID" => $titleID,
																	"includeAvailabilityInfo" => "true",
																	"includeItemInfo" => "true",
																	"marcEntryFilter" => "NONE"));
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function searchCatalog($options)
	{
		try {
			$result =  $this->standardService->searchCatalog($options);
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function searchCatalogPaging($queryID, $firstHitToDisplay, $lastHitToDisplay)
	{
		try {
			$result =  $this->standardService->searchCatalogPaging(array("queryID" => $queryID,
																		 "firstHitToDisplay" => $firstHitToDisplay,
																		 "lastHitToDisplay" => $lastHitToDisplay,
																		 "includeAvailabilityInfo" => "ALL"));
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function searchInfoDesk($infoDesk = null, $customInfoDesk = null, $hitsToDisplay = null, $filters = null, $includeAvailabilityInfo = null)
	{
		$options = array();
		if($infoDesk != null)
			$options["infoDesk"] = $infoDesk;
		if($customInfoDesk != null)
			$options["customInfoDesk"] = $customInfoDesk;
		if($hitsToDisplay != null)
			$options["hitsToDisplay"] = $hitsToDisplay;
		if($filters != null)
			$options["filters"] = $filters;
		if($includeAvailabilityInfo != null)
			$options["includeAvailabilityInfo"] = $includeAvailabilityInfo;
			
		try {
			$result =  $this->standardService->searchInfoDesk($options);
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function version()
	{
		try {
			$result = $this->standardService->version();
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}

	/* Security */
	function authenticateUser($login, $password = null)
	{
		$options = array();
		$options["login"] = $login;
		if($password != null)
			$options["password"] = $password;
			
		try {
			$headerbody = array("clientID" => $this->clientID);       
			$header = new SoapHeader($this->WS_HEADER, "SdHeader", $headerbody);
			$securityService = new SoapClient($this->BASE_URL.$this->SECURITY_WSDL,
			  array("proxy_host" => $this->proxyhost, "proxy_port" => $this->proxyport, "trace" => 1));           
			$securityService->__setSoapHeaders($header);

			return $securityService->authenticateUser($options);
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function loginUser($login, $password)
	{
		try {
			
			$this->login = $this->securityService->loginUser(array("login" => $login, "password" => $password));
	
			$headerbody = array("clientID" => $this->clientID, 
							"sessionToken" => $this->login->sessionToken);
			$header = new SoapHeader($this->WS_HEADER, "SdHeader", $headerbody);
			
			$this->patronService = new SoapClient($this->BASE_URL.$this->PATRON_WSDL,
				array("proxy_host" => "$this->proxyhost","proxy_port" => $this->proxyport, "trace" => 1));           
			$this->patronService->__setSoapHeaders($header); 
			
			return $this->login;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function logoutUser()
	{
		try {
			$logout = $this->securityService->logoutUser(array("sessionToken" => $this->login->sessionToken));
			return $logout;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function lookupUserInfo($userID)
	{
		try {
			$result = $this->securityService->lookupUserInfo(array("userID" => $userID,
																   "includeAccountability" => "true", 
																   "includeAllowedCommands" => "true"));
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/* Patron */
	function lookupPatronInfo()
	{
		try {
			$patron = $this->patronService->lookupPatronInfo(array("includePatronCheckoutInfo" => "ALL", "userID" => $this->login->userID));
			return $patron;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function lookupMyAccountInfo()
	{
		try {
			$result = $this->patronService->lookupMyAccountInfo(array("includePatronInfo" => "ALL",
																	  "includePatronCirculationInfo" => "ALL",
																	  "includePatronCheckoutInfo" => "ALL", 
																	  "includePatronHoldInfo" => "ACTIVE", 
																	  "includePatronAddressInfo" => "ACTIVE",
																	  "includeFeeInfo" => "ACTIVE",
																	  "includePatronStatusInfo" => "ALL"
																	  ));
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function renewCheckout($userID, $itemID)
	{
		try {
			$result = $this->patronService->renewCheckout(array("itemID" => $itemID, "userID" => $userID));
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function renewMyCheckout($itemID)
	{
		try {
			$result = $this->patronService->renewMyCheckout(array("itemID" => $itemID));
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function listMyHolds()
	{
		try {
			$result = $this->patronService->lookupMyAccountInfo(array("includePatronHoldInfo" => "ACTIVE"));
			return $result->patronHoldInfo;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function createHold($userID, $itemID = null, $titleID = null)
	{
		$options = array();
		if($itemID != null)
			$options["itemID"] = $itemID;
		if($titleID != null)
			$options["titleID"] = $titleID;	
			
		try {
			$result = $this->patronService->createHold($options);
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function cancelHold($userID, $holdID)
	{
		try {
			$result = $this->patronService->cancelHold(array("holdID" => $holdID, "userID" => $userID));
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function createMyHold($itemID = null, $titleID = null)
	{
		$options = array();
		if($itemID != null)
			$options["itemID"] = $itemID;
		if($titleID != null)
			$options["titleID"] = $titleID;	
			
		try {
			$result = $this->patronService->createMyHold($options);
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function cancelMyHold($holdKey)
	{
		try {
			$result = $this->patronService->cancelMyHold(array("holdKey" => "$holdKey"));
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function createSelfRegisteredPatron($options)
	{
		try {
			$result = $this->patronService->createSelfRegisteredPatron($options);
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function lookupMyLists()
	{
		try {
			$result = $this->patronService->lookupMyLists();
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function lookupMyList($myListID, $includeMyListTitleInfo = null) // need to add includeMyListTitleInfo
	{
		$options = array();
		$options["myListID"] = $myListID;
		if($includeMyListTitleInfo != null)
			$options["includeMyListTitleInfo"] = $includeMyListTitleInfo;
			
		try {
			$result = $this->patronService->lookupMyList($options);
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function getMyList($myListID)
	{
		try {
			$result = $this->patronService->getMyList(array("myListID" => $myListID));
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function saveMyList($myList)
	{
		try {
			$result = $this->patronService->saveMyList($myList);
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function deleteMyList($myListID)
	{
		try {
			$result = $this->patronService->deleteMyList(array("myListID" => $myListID));
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function changeMyPIN($currentPIN, $newPIN)
	{
		try {
			$result = $this->patronService->changeMyPIN(array("currentPIN" => $currentPIN,
															   "newPIN" => $newPIN));
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function modifyMyHold($holdKey, $expiresDate = null, $holdPickupLibraryID = null, $mailDeliveryID = null)
	{
		$options = array();
		$options["holdKey"] = $holdKey;
		if($expiresDate != null)
			$options["expiresDate"] = $expiresDate;
		if($holdPickupLibraryID != null)
			$options["holdPickupLibraryID"] = $holdPickupLibraryID;
		if($mailDeliveryID != null)
			$options["mailDeliveryID"] = $mailDeliveryID;
			
		try {
			$result = $this->patronService->modifyMyHold($options);
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function suspendMyHold($holdKey, $suspendStartDate, $suspendEndDate = null)
	{
		$options = array();
		$options["holdKey"] = $holdKey;
		$options["suspendStartDate"] = date("Y-m-d",$suspendStartDate);
		if($suspendEndDate != null)
			$options["suspendEndDate"] = date("Y-m-d",$suspendEndDate);
		
		try {
			$result = $this->patronService->suspendMyHold($options);
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	function unsuspendMyHold($holdKey)
	{
		try {
			$result = $this->patronService->unsuspendMyHold(array("holdKey" => $holdKey));
			return $result;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/* Custom */
	function patronCheckoutInfo()
	{
		try {
			$result = $this->patronService->lookupMyAccountInfo(array("includePatronCheckoutInfo" => "ALL"));
			return $result->patronCheckoutInfo;
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
}
?>