Facebook BetterSDK Handle Class
-----------------------------------------------
By: Avihay Menahem
Email: Avihay@hazvuv.com

To start using the class, first you need to config the class file according to your Facebook application details:

```
$this->appID = 'YOUR_APP_ID';
$this->appSecret = 'YOUR_APP_SECRET';
$this->perArray = array('user_about_me', 'publish_stream');
$this->appPage = 'YOUR_APP_FAN_PAGE';
```

Include this file wherever you want to perform the action
and start a new session for the class with the user signed request.
Now, you have an active session for the current user, and to access
his parameters all you need to do is just get theme that way:

```include('class.facebook.php');
$facebook = new Facebook($_REQUEST['signed_request']);

$facebook->userID;				 // User ID returns int num, Example: 731580237
$facebook->pageID;				 // Page ID returns int num, Example: 140238812752434
$facebook->pageLiked; 		 // Check if user has liked the page returns BOOLEAN, Example: 1
$facebook->pageAdmin; 		 // Check if user is admin of the page returns BOOLEAN, Example: 1
$facebook->accessToken; 	 // Get the user's oauth_token Returns String, Example: AAAEfL9792ukBAJgrqjoivyKl3J6ZARzPuW2i9cbI6wpCA9fZB0QPCLc6ZBdRzrjT7rZC8ZCju98lbXlILBu8UonyZBPjnNZA0sZD
```

To perform an like or comment on a object you need to do it this way:

Comment:
```$facebook->graphAction(POST_ID,"comments", THE_MESSAGE);```

Like:
```$facebook->graphAction(POST_ID,"likes");```

Post To Wall (user/page):
```$facebook->graphAction(USER_ID/PAGE_ID, "feed", THE_MESSAGE);```

FREE TO USE UNDER THE MIT LICENSE
http://www.opensource.org/licenses/mit-license.php

FOR MORE Q&A EMAIL ME TO:
Avihay@hazvuv.com