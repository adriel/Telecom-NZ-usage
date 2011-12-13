## Telecom NZ usage
An easy to use .php script to get telecom usage in the form of an array. 

## Usage
1. Add your Telecom `username` and `password`
2. Un/comment the functions you require
3. Use with your own scripts

More information on what each function returns is discussed in the php file.

### Requirements
* PHP 5.3
* PHP cURL

## Why?
1. Telecoms usage page is a jock and hasn't been updated since… ever?
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