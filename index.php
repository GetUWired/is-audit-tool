<?php //Remember to stay alert

namespace ISAudit;

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/creds.php';

$infusionsoft = new \Infusionsoft\Infusionsoft(array(
    'clientId' => $clientId,
    'clientSecret' => $clientSecret,
    'redirectUri' => 'http://localhost:8888',
));


/*

// If the serialized token is available in the session storage, we tell the SDK
// to use that token for subsequent requests.
if (isset($_SESSION['token'])) {
    $infusionsoft->setToken(unserialize($_SESSION['token']));
}

*/

// If we are returning from Infusionsoft we need to exchange the code for an
// access token.
if (isset($_GET['code']) ) {
    $accessToken = $infusionsoft->requestAccessToken($_GET['code']);

    $token = new Token();

    $token->update_tokens_in_database($accessToken);
}


if ($infusionsoft->getToken()) {
    print_r($infusionsoft->getToken());
} else {
    echo '<a href="' . $infusionsoft->getAuthorizationUrl() . '">Click here to authorize</a>';
}





//Lets do some work


//Grab ALL custom Fields 

$table = 'DataFormField';
$limit = 200;
$page = 0;
$orderBy = 'Name';
$ascending = true;

$returnFields = array('Name');
$queryData = array('Id' => '%', 'FormId' => -1);
$selectedFields = array('Name', 'Label', 'DataType', 'Id');

//$fields = $infusionsoft->dsQuery("DataFormField",200, 0, $query, $returnFields);

$fields = $infusionsoft->data()->query($table, $limit, $page, $queryData, $selectedFields, $orderBy, $ascending);


$reportFields = array();


echo '<pre>';
//print_r($fields);



foreach($fields as $field){
	$reportFields[$field['Name']] = $field;
}


echo '<h2>You are using <i>'.count($fields) . '</i> out of your 100 custom fields</h2>';
	


$table = 'Contact';
$queryData = array('Id' => '%');

$totalContats = $infusionsoft->data()->count($table, $queryData);





echo '<h2>You have '.$totalContats.' total contants</h2>';

echo '<ul>';




foreach($fields as $field){
	
	
	$table = 'Contact';
	$queryData = array('_'.$field['Name'] => '%');

	$contctsWithData = $infusionsoft->data()->count($table, $queryData);
	
	$contactPercent = number_format((  ((int)$contctsWithData / (int)$totalContats) * 100), 5);
	
	$reportFields[$field['Name']]['totalContacts'] = $contctsWithData;
	$reportFields[$field['Name']]['contactPercent'] = $contactPercent.'%';
	
	
	//echo '<li> <p><b>'.$field['Label'].'</b> is used by '. number_format((  ((int)$contctsWithData / (int)$totalContats) * 100), 5).'% of your contacts</p><p>'.$contctsWithData.' contacts with data</p></li>';
	
}



echo '</ul>';





//Get all webform Ids


$webformIds = $infusionsoft->webForms()->getMap();



echo '<p>This checks fields on Campaign Builder :: Webforms, Internal Forms and Landing pages</p>';
//print_r($webformIds);


//Loop though and check html for custom fields
foreach($webformIds as $webformId => $webformName){
	
	
	$formHTML = $infusionsoft->webForms()->getHTML($webformId);
		
	//echo '<textarea>'.$formHTML.'</textarea>';
		
	//look for our customfields
	
	foreach($reportFields as $reportField){
		
		$pos = strpos($formHTML, $reportField['Name']);
		
		
		//$pos sould be === compared because it could be at the start of a string but in this case I know it wont so I am skipping that
		if($pos){
			$reportFields[$reportField['Name']]['webforms'] .= $webformName.', ';
		}
		
	}
	
}


usort($reportFields, function($a, $b) {
    return $b['totalContacts'] - $a['totalContacts'];
});


print_r($reportFields);

?>
