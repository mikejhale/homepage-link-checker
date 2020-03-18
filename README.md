# Sample Link Checker Plugin # 
**Tested up to:** 5.3.2 
**Stable tag:** 1.0.0
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

** This plugin was a coding exercise to be used as a sample, and is not intended to be used as an actual plugin. **

## User Story ##

As an administrator, I want to see how my website web pages are linked together to my home page so that I can manually search for ways to improve my SEO rankings.

## Project Requirements ##

1. Add an admin settings page where teh admin can crawl a URL for links. 
2. When triggered, start at the website's homepage URL.
3. Extract all **internal** links. 
4. Check the HTTP status of the links and display the results on the settings page. 
5. Store links temporarily for up to one hour.
6. Provide admin user any error notifications.
7. Add a shortcode where anyone can view the results (if available) on a webpage.

A few notes about the requirements:

* Only crawl the homepage (not recursively through all pages)
* Delete the results based on time only
* Use OOP and PSR
* Does not generate errors, warnings, or notices.
* Passes phpcs inspection