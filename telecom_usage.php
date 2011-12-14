<?php
// Telecom login details
$data['username'] = ''; // include .xadsl
$data['password'] = '';

// Advanced settings
$loginData['coockie_location']	= 'cookie.txt';
$loginData['url'] 				= 'https://login1.telecom.co.nz/distauth/UI/Login';
$loginData['ref_url'] 			= 'https://login1.telecom.co.nz/distauth/UI/Login?realm=XtraUsers';
$loginData['usage_url'] 		= 'https://www.telecom.co.nz/jetstreamum/xtraSum?link=rdt';
$loginData['user_agent'] 		= 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_2) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.121 Safari/535.2';
$loginData['post_fields']	 	= 'IDToken1='.$data['username'].'&IDToken2='.$data['password'].'&IDButton=Submit&encoded=false&gx_charset=UTF-8';

// Step 1 - Login (only do this when it's needed)
print_r( curl_grab_page($loginData['url'],$loginData['ref_url'],$loginData['post_fields'],$loginData['user_agent'],$loginData['coockie_location']) );

// Step 2 - Load usage data
$usageSource = get_page($loginData['usage_url'],$loginData['user_agent'],$loginData['coockie_location']);

// Step 3 - Parse html code into a usable array
$data_arr = html_to_array($usageSource);
print_r( $data_arr );
// print_r( $data_arr['data'] ); // Just the data array



// $url 		= page to POST data
// $ref_url 	= tell the server which page you came from (spoofing)
// $curl_data 	= curl post field data
// $user_agent 	= user agent to send site
// $cookie_loc 	= cookie file location

// Load login page + store cookie -> login + store logged in state in cookie.
// returns 'array' with 
// 			['status']	1 = ok  0 = error
// 			['message']	status message (will display error is there is one)
// 			['error']	error message (will display what error you got if you got one)
function curl_grab_page($url,$ref_url,$curl_data,$user_agent,$cookie_loc){
	// create blank cookie file if non found
	if(!file_exists($cookie_loc)) {
        $fp = fopen($cookie_loc, "w");
        fclose($fp);
    }
	
	// 
	// First load up the login page and save the cookie
	// 
	
	$ch = curl_init();

	// set URL and other appropriate options
	$options = array( 
		CURLOPT_URL				=> $ref_url,
		CURLOPT_RETURNTRANSFER	=> false,			// return web page 
		// CURLOPT_REFERER		=> true,			// follow redirects 
		// CURLOPT_FOLLOWLOCATION	=> true,			// follow location 
		CURLOPT_USERAGENT		=> $user_agent,     // who am i 
		CURLOPT_COOKIEJAR		=> $cookie_loc,
		CURLOPT_CONNECTTIMEOUT	=> 5,				// timeout on connect (default 120)
		CURLOPT_TIMEOUT			=> 5,				// timeout on response (default 120)
		CURLOPT_MAXREDIRS		=> 10,				// stop after 10 redirects 
		// CURLOPT_AUTOREFERER	=> true,			// set referer on redirect 
	); 
	
	curl_setopt_array($ch, $options);

	// grab URL and pass it to the browser
	ob_start();		// prevent any output
	curl_exec($ch);	// execute the curl command
	ob_end_clean();	// stop preventing output

	sleep(2); // Give telecom a second to breath?
	
	// 
	// Post login info to telecom usage URL and get usage page source 
	// 
	
	// set URL and other appropriate options
	$options = array( 
		CURLOPT_URL				=> $url,
		CURLOPT_RETURNTRANSFER	=> true,			// return web page 
		CURLOPT_FOLLOWLOCATION	=> $ref_url,		// follow redirects 
		CURLOPT_REFERER			=> true,			// follow redirects 
		CURLOPT_FOLLOWLOCATION	=> true,			// follow location 
		CURLOPT_USERAGENT		=> $user_agent,     // who am i 
		CURLOPT_COOKIEFILE		=> $cookie_loc,
		CURLOPT_CONNECTTIMEOUT	=> 5,				// timeout on connect (default 120)
		CURLOPT_TIMEOUT			=> 5,				// timeout on response (default 120)
		CURLOPT_MAXREDIRS		=> 10,				// stop after 10 redirects 
		// CURLOPT_AUTOREFERER	=> true,			// set referer on redirect 
		CURLOPT_POST			=> true,			// send post data 
		CURLOPT_POSTFIELDS		=> $curl_data		// post vars 
	); 
	
	curl_setopt_array($ch, $options);

	// grab URL and pass it to the browser
	ob_start();      // prevent any output
	$source = curl_exec($ch);
	ob_end_clean();  // stop preventing output
	
	// close cURL resource, and free up system resources
	curl_close($ch);
	
	
	if (strstr($source, 'VIGN HPD cache address:')) {
		
		$loginStatus['status'] 	= 1;
		$loginStatus['message']	= 'Logged in';
		
	}elseif (strstr($source,'Maximum sessions limit reached or session quota has exhausted')) {
		
		$loginStatus['status'] 	= 0;
		$loginStatus['message']	= 'Error during login';
		$loginStatus['error']	= 'Maximum sessions limit reached or session quota has exhausted';
		
	}
	else {
		
		$loginStatus['status'] 	= 0;
		$loginStatus['message']	= 'Failed to login';
		$loginStatus['error']	= 'Unknown';
		// $loginStatus['source']	= $source; // give html source that was returned
		
	}
	return $loginStatus;

}

// Get source from a page
// url			= page to get source code 
// user_agent	= user agent to send to page
// cookie_loc	= location of cookie to use

// returns 'string' with
// 		raw html source code
function get_page($url,$user_agent,$cookie_loc)
{
	$ch = curl_init();

	// set URL and other appropriate options
	$options = array( 
		CURLOPT_URL				=> $url,			// URL to load
		CURLOPT_RETURNTRANSFER	=> true,			// return web page 
		CURLOPT_FOLLOWLOCATION	=> true,			// follow redirects 
		CURLOPT_USERAGENT		=> $user_agent,     // who am i 
		CURLOPT_COOKIEFILE		=> $cookie_loc,
		CURLOPT_CONNECTTIMEOUT	=> 5,				// timeout on connect (default 120)
		CURLOPT_TIMEOUT			=> 5,				// timeout on response (default 120)
		CURLOPT_MAXREDIRS		=> 10,				// stop after 10 redirects 
		// CURLOPT_AUTOREFERER	=> true,			// set referer on redirect 
	); 
	
	curl_setopt_array($ch, $options);

	ob_start();      // prevent any output
	return curl_exec($ch);
	ob_end_clean();  // stop preventing output

	// close cURL resource, and free up system resources
	curl_close($ch);

	// $err     = curl_errno($ch); 
	// $errmsg  = curl_error($ch) ; 
	// $header  = curl_getinfo($ch); 
	// curl_close($ch); 
	// 
	//  $header['errno']   = $err; 
	//  $header['errmsg']  = $errmsg; 
	//  $header['content'] = $content; 
	// return $header; 
}

// TODO: add date format option to func <--

// scrap Telecom usage page and output a usfull array
// htmlSource	= html source to output to an array

// returns 2 dimensional 'array' with
// 
// 		['status']			= 1 ok 0 error
// 		['message']			= message of what happened
// 		['data or error']	= returns the below array else and error message
//			['account_no']		= Telecom account no.
//			['account_type']	= Telecom account type
//			['cyle_start_date']	= date internet cycle starts (normally monthly)
//			['cyle_end_date']	= date internet cycle end	 (normally monthly)
//			['total_MB']		= size of cap in MB
//			['used_MB']			= amount of cap already used in MB
function html_to_array($htmlSource='')
{
	if ($htmlSource == '') {
		return false;
	}
	$doc = new DOMDocument();
	// added "@" at the start to silence any errors
	@$doc->loadHTML($htmlSource);

	$xpath = new DOMXpath($doc);

	// does awesomeness untill telecom decides to update their usage page *yeah right*
	$acc_no 						= $xpath->query('//span[@class="formText"]');
	$source_arr['account_no'] 		= trim($acc_no->item(0)->nodeValue);
	
	$table_one 						= $xpath->query('//table[@class="table"]/tr/td');
	$source_arr['account_type'] 	= trim($table_one->item(5)->nodeValue);
	list($start_date, $end_date) 	= explode("-", $table_one->item(3)->nodeValue);
	$source_arr['cyle_start_date'] 	= trim($start_date);
	$source_arr['cyle_end_date'] 	= trim($end_date);

	$source_cap_total 				= $xpath->query('//div[@class="usage1"]/font/b');
	$source_arr['total_MB'] 		= (int) trim($source_cap_total->item(0)->nodeValue);
	
	$source_used_MB 				= $xpath->query("//nobr");
	$source_used_MB 				= trim($source_used_MB->item(0)->nodeValue);
	$source_arr['used_MB'] 			= (float) str_replace(',', '', $source_used_MB);
	
	// check if we have data, if not then it's probably because we arn't logged in
	if ($source_arr['account_no'] != '') {
		
		$loginStatus['status'] 	= 1;
		$loginStatus['message']	= 'Usage data found';
		$loginStatus['data']	= $source_arr;
		
	}else {
		
		$loginStatus['status'] 	= 0;
		$loginStatus['message']	= 'Failed to get usage data';
		$loginStatus['error']	= 'Unknown';
		// $loginStatus['source']	= $source; // give html source that was returned
		
	}
	
	return $loginStatus;
	
	// // 
	// // debug xpath query
	// // note: make sure to comment out the return comment above 
	// foreach ($acc_no as $element) {
	// 	// echo "<br/>[$i-". $element->nodeName. "]";
	// 
	// 	// echo $channel_elements->item(0)->nodeValue;
	// 	// echo $channel_elementsdes;
	// 	$nodes = $element->childNodes;
	// 	foreach ($nodes as $node) {
	// 		// echo $node->nodeValue. "\n";
	// 		$channel_list_arr[] = trim($node->nodeValue);
	// 	}
	// }
	// print_r($channel_list_arr);	
}
?>