## Telecom NZ usage
An easy to use .php script to get telecom usage in the form of an array. 

## Usage
1. Add your Telecom `username` and `password` to the `$data['username']` and `$data['password']` array
2. Un/comment the functions you require
3. Use with your own scripts

More information on what each function returns is discussed in the php file.

### 3 step process
#### Use this line of code only once, to get the logged in cookie data saved to the .txt file.
    print_r( curl_grab_page($loginData['url'],$loginData['ref_url'],$loginData['post_fields'],$loginData['user_agent'],$loginData['coockie_location']) );

#### Use these two lines to get the usage data and parse it to an array

    $usageSource = get_page($loginData['usage_url'],$loginData['user_agent'],$loginData['coockie_location']);

    $data_arr = html_to_array($usageSource);

#### Use this line to see what the array contains
    print_r( $data_arr );


### Code examples
Insert these examples below `$data_arr = html_to_array($usageSource);` in the .php script.

#### Get current data used:
    echo $data_arr['data']['used_MB'] . 'MB of data used from a ' . $data_arr['data']['total_MB'] . ' MB cap.';
This will output: `15116.49 MB of data used from a 20480MB cap.`

#### Calculate how much internet you have left:
    $data_left = $data_arr['data']['total_MB'] - $data_arr['data']['used_MB'];
    echo 'You have ' . (int)$data_left . ' MB left.';
This will output: `You have 5363 MB left.`

#### Get custom message when close to going over the cap:
This will check if you have used 15GB or more data and give a customized message.

    if ( $data_arr['data']['used_MB'] >= 15000 ) {
	    echo 'Over half your cap has been used.';
    }else {
	    echo 'You have used less then 15GB of data so far.';
    }
This could output rather `Over half your cap has been used.` or `You have used less then 15GB of data so far.`

### Output example
<pre>Array
(
    [status] => 1
    [message] => Logged in
)
Array
(
    [status] => 1
    [message] => Usage data found
    [data] => Array
        (
            [account_no] => 123 - 456
            [account_type] => Explorer 20GB
            [cyle_start_date] => 09 Dec 2011
            [cyle_end_date] => 08 Jan 2012
            [total_MB] => 20480
            [used_MB] => 10534.9
        )
)</pre>

### Requirements
* PHP 5.3
* PHP cURL

## Why?
1. Telecoms usage page is a joke and hasn't been updated since… ever?
2. Telecom doesn't provide any easy to use API to get usage data
3. I wanted to easily see what my usage using my own scripts

## Limitations
* There appears to be a limit on the amount of login attempts you can do in an hour. <br /> If you try to many times you will get an error like `Maximum sessions limit reached or session quota has exhausted`


## Disclaimer
As you may have noticed I'm not a big fan of Telecom and I will do everything in my power to avoid them at all costs, but in my current living arrangement I have no alternative.

PS, please **don't use them**, there are so many other great alternatives in New Zealand, some of which are:

* [Snap](http://www.snap.net.nz/) (fast but less data)
* [Orcon](http://www.orcon.net.nz/)
* [Slingshot](http://www.slingshot.co.nz/) (slow at times but bulk data)
* [Telstra Clear](http://www.telstraclear.co.nz/residential/inhome/) (If you live in Wellington) 