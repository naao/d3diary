d3diary
=======================

D3diary is a fork version of minidiary that is made to be reproduced by the D3(duplicatable V3) specification. Some convenient function additions are given. 

** Necessary environment
-Core
-- XOOPS 2.0.16a-JP, JPEx
-- XOOPS Cube Legacy（checked on 2.1.8）
-PHP
-- PHP5.1 over
-MySQL
-- MySQL-4.0.x or over (detail is not sure)
-- (MyISAM)
-Altsys is necessary

**The main function
-For module dupulicatable（Duplicatable V3）

-D3forum comment integration
-- Please specify forum_id and forum_dirname on the management screen. 
-- Please specify "Format for comment-integration" for forum concerned
    d3diary::d3diaryD3commentContent::d3diary
    ^^^^^^^change only here for installed d3diary's directory name

-notification function
-- When a new diary in specified member is contributed
-- When a new commenting in specified member is contributed
    valid only with d3-comment integration, not with xoops original
    comment system

-inport from minidiary, d3diary, weblogD3 or d3blog
-- Importing diary data (except for weblogD3 and d3blog's trackback data)
-- Importing xoops comment
    Not necessay for d3-comment. In that case, setting for both d3diary
    and d3forum will be enough.
    And from xoops comment to d3 forum import is also available
    with the d3forum module's function.
-- Inpoirting event notifications
-- Please transfer image files manually.

-View authority cooperating with friend function of XSNS and myfriends module.
-- Articles on each category can be set. 
-- Please specify the use setting the directory name of a coordinated module
    on the management screen. 

-External blog setting
-- Available for each category setting.
-- The aggregation display of the d3diary and the external blog setting 
  is possible. 
   (Up to one for the person in the page of everybody in index and the newly
     arrived diary block.)
-- Please do the authority and the external blog setting of each category
   effectively on the management screen. 

-Tag function
-- It extracts it with specific classification tag. 
-- Visualization of classification tag by tag cloud.

-HTML enable setting
-- HTML post available for user groups allowed by preferences setting.
-- For HTML post allowed users, default value of HTML post for each
   category is selectable.
-- HTML enable check is added on Post form.
-- FCKeditor is available. it is recommended to download the newest one 
   from following site.
   http://xoops.peak.ne.jp/md/mydownloads/singlefile.php?lid=93
-- Use of FCKeditor is selectable on preferences setting. For HTML post 
   allowed users, FCKeditor is loadable by pushing button on edit form.

-RSS output (each user and each entire user & category)

-When using the d3-comment integration was done, two comment blocks
  that synchronized with the inspection authority of d3diary former
  article were prepared. 
-- Topics block and posts block are available.
-- The aggregation display is enabled to extract other d3forum forum topics.
--- The "OR" condition of category+forum is available.
  *The other disregard when specification only categoriess or forums.
  *The "OR" condition when both categories and forum specified.
  *All forums are retrieval object when both categories and forum specified.

--- When "Assign tetail text" is selected YES, assign does the latest article
   for the topic. If the template is edited if necessary, the display becomes
   possible. 

-- It is displayed on the main page of d3forum, and it introduces the 
   comment former article inspection authority synchronization with the list
   display and all categorylist display separately as Tips on the site. 

-Two d3pipes block joints
-- Newly arrived diary joint and integrated comment joint similar to the 
   above-mentioned. 
-- Maximum display items available for both blocks.
-- The integrated comment joint displays the latest article only by doing
   the setting that displays the article text at the topics display. 

**Future functions
- Increments of image uploading directory
- Trackback function

** Changed history
2011/ 8/21  ver0.22  (Requires module update)
	- In diarylist block, added two sort order, new comment first and comment numbers .
	  Both are only for sorting after licked up new diaries.
	- Added diarylist block option that force eliminate tag. In case of selected "No" ,
	  summary text before [pagebreak] as it is.
	- Fixed bug of diarylist block option "Assign tetail text was not valid.
	- Added function to insert and show the uploaded image into body text.

2011/ 8/14  ver0.21  (Requires module update)
	- Added function of d3blog's "Enable to show title and summary for not permitted entry".
	　Added following two settings on prefferences screen.
	-- Open area for title and summary.
	-- Show comment or not in case of enabled showing title and summary.
	- Trouble is found in rotation image deletion/processing that has been registered
	  when there is a preview image in the edit display, and it was fixed.
	- The detail of the template is changed a little for the smart phone display. 

2011/ 8/11  ver0.20a
	- Fixed no body text of mail importing from iPhone in case the body text after photos.(thx AXYZ-SAK)

2011/ 8/ 8  ver0.20a
	- With using d3comment class, since the constructor coding was wrong,
	　screen whiteout occurs after d3forum version up to 0.86. It is fixed.

2011/ 8/ 2  ver0.20a
	- Fixed no body text of mail importing from iPhone.(thx Honda)

2011/ 7/30  ver0.20a
	- Fixed bug that jump to the shown personnel url although selected the block option
	  "Show limitation" to "No limit, show all items even when each personal page"
	  on category block, calendar block, and monthlist block. （thx ZAL2000）

2011/ 7/25  ver0.20
	- Block option cannot be edited on xoops-2 cores (thx zukkiina)
	- Added color select on pagenavi's present page.

2011/ 7/ 8  ver0.20 RC
	- Remove the neccesary environment php4. php5.0 over is neccesary, php5.1 over is reccomended.
	- Reduce notices for php5.3 environment.

2011/ 6/16  ver0.18 alpha
	- Fixed bug that cannot show date in photolist page. Module update is neccesary.

2011/ 6/ 9  ver0.18 alpha
	- Fixed hits and comments number were not assigned in whatsnew puguin.

2011/ 6/ 4  ver0.18 alpha
	- Fixed bug of photo info is not trancated in photolist block.
	- Added photolist page and changed links in blocks of category, tagcroud, and blogger.
	　Added submenu "Photo List" in main menu.
	- Sorted view function is added in index, diarylist, bloggerlist, photolist pages.
	　It sorts with keeping category or tag query condition.
	- Fixed bug that "friend's diary" in main menu shows owner's diary.
	- Changed class names to eliminate possobility of confliction with other modules.

2011/ 5/ 6  ver0.17b
	- Category swap function was very complecated in category edit screen because only swap each column.
	  Now it's modified that you can input the sequence number directry.
	- Fixed bug of redirect function when a guest acccess to the TOP page in version 0.17 and 0.17a.

2011/ 5/ 5  ver0.17, 0.17a
	- Image rotate (right 90degree / left 90degree) function is added on edit screen after e-mail import.
	- Fixed an alart using FCKeditor even though body texed is written but "no input" is shown,
	　changed by no check the body text when using FCKeditor. (thx nouphet)
	- Changed behavior when the oversize error of uploaded image is occured, sent data will be remain with
	  some notifying alart.
	- Added import function from weblogD3. (trackback will not be imported)

2011/ 5/ 3  ver0.16b
	- Fixed unrecognized e-mail inport for "name" <mail address> type FROM address.(thx honda)
	- Changed POP3 and mailDecode class name to avoid cinfliction.

2011/ 4/18  ver0.16a
	- To fix the garble by server multibyte environment when decode the mail, added input of character code detect order 
	  in the prefference screen and changed the convert encoding in each time.
	- Added "Mail Import" link at the top area of the owners' index page.
	- Modified specification that future's entry link without permisstion was visible in calendar.

2011/ 4/ 4  ver0.16
	- Correction of bug that all comments on diary are displayed from newly arrived diary block
	  of everybody to commenting newly arrived block after diary is clicked.
	- The correction of trouble which the tag cloud in the upper part of the main page is not displayed. (thx hori)
	- Fixed warning error in state without tag。(thx Ken@)
	- Fixed the error of closing <div> tag in block_photolist.html template.(thx guest)
	- It is corrected that the url automatic setting on each blog site rss was a unmounting by the setting of
	  an external blog of the category. 
	- Added function of the diary and the image by email. 

2011/ 1/10  ver0.15
	- Tag edit mode on personal entry list (index.php) page is added when queried on a tag.
	-- Select entries by each CheckBox and select the operation at the bottom of the lists.
	-- Delete / Edit / Add operation is available for multi entries at once.
	- Also tag addition mode is available on category queried list bottom "Into tag edit mode" link.

2010/12/22  ver0.14b
	- Fixed bug that future time diary is shown on new dary/blog block, d3pipes joint etc.
	　(trust- class/d3diaryPermissionAbstract.class.php)

2010/12/15  ver0.14a
	- Fixed breadcrumbs don't show diary title when selected a title after a category.（thx hiro1613）
	- Fixed bug that missing stripslashes of diary's preview function in case of php setting "pagic_quote_GPC=on".（thx Sakai）

2010/12/08  ver0.14
	- Added page navigation on Tag clowd block in case of so many Tags.
	 -- It devides items by the block option "Desplay items" to control the show numbers.
	 -- Show order can be set to "Tag name / Counts / Datetime" by "ASC/DESC".
	- Added open / close clickable area for all members Tag link list in edit screen.
	- Added query by Tag and Category on two blocks with block options.（block_diarylist.html）(block_side_entry.html)
	 -- Ao category also can be set by category name as tag name that private category is available same as common category.
	 -- Comma (,) separator is available for multiple input.
	 -- Wild card "%" can be contain. (ex.: "%diary%" matches "my diary" "diary for me" "my diary for me".）
	- Added the photo show block. Order time (with page navi) or random selection is available.
	- Modified two comment integration blocks that could not show old diary's comment.
	- Added piCal plugin into extras folder in archive.
	- Improved some vulnerabilities on several pages.
	- Fixed notice and warning errors. (thx minmin)

2010/11/28  ver0.13c
	- Fixed bug that BBcode was not enabled at the side information for each uploaded picture.(trust - main/detail.php)
	- Editting the side information for each uploaded picture was not valid on preview. (trust - main/edit.php)
	- Fixed some error at the d3blog wrap process.（html - index.php）
	- Fixed invalid d3comObj for other comment integrated forums. (trust - class/func.php)
	- Fixed notification-author's name which was not changed when "use name "is selected.
		(trust - main/edit.php, class/d3diaryD3commentContent.class.php)
	- Added whatsnew module's plugin function. (html - include/data.inc.php、trust - include/whatsnew.inc.php)
	- Added Portuguese language files into the extras folder in archive. (thx Ohse)

2010/11/19  ver0.13b
	- Fixed integrated comment count setting on prefference was not enabled for detail page.(detail.html)
	- Fixed white out when comment integrated module has not own class. (thx caplico)
	- Added tag list on index page and diarylist page's each entries.
	- Added commented out clearfix for two or three images inline on detail and edit preview pages.

2010/11/12  ver0.13a
	- Fixed some SQL injection vulnerability.
	- Fixed a single uploaded image comes left side in detail page.
	- Fixed missing of the category which is set the group authentification in editcategory page.
	- Added the import function from d3blog module.

2010/11/10  ver0.13
	- Fix the garble in new entry list getting external blog including html.(thx KAZU)
	- Side information for each uploaded picture is available. Needs module update and prefferences setting.
	- Re-constructed the edit.php scripts and added deletion function of two or more images in one time.

2010/11/02  ver0.12b3  (Development version)
	- Added ATOM feed to RSS adn html feed is commented out.（thx OhYeah）
	- Fix the wrong "checked" value of SUB checkbox on categoryedit page.

2010/10/31  ver0.12b2  (Development version)
	- Made a template for RDF feed and RSS2.0 feed is also available.（thx OhYeah）
	- For a server without mb_string environment, mb_string emulator is ready for trust side.
	　If the nao-pon's hypcommon_func is already installed, no need to overwrite.（thx nao-pon）
	- Moved the d3pipes block pipe file to extras folder in the archive.
	- Attachfile module's plugin is added in extras directory and trust side include directory.
	  It's upload / download permissions are controlled by entriy's write / view permissions.

2010/10/27  ver0.12b1  (Development version)
	- Fixed the constant warning at mypage with xsxs module. (thx naoki)
	- Fixed the link error of external blog openarea image at diarylist block.
	- Fixed that selector of common category on diarylist page cannot be displayed while side menu position is set right or left side.

2010/10/19  ver0.12a2  (Development version)
	- Bug fix that cannot work on php4 environment.(thx river-stone)
	- Bug fix that RSS feed link on block "b_side_person" cannot perse required category.

2010/10/19  ver0.12a1 (Development version)
	- Fixed bug that entry with html is not sanitized correctly on block_diarylist. (thx guest)	

2010/10/18  ver0.12 (Development version)
	- Optimization of list view logic and SQL.
	- Fix confliction to d3blog blocks.
	- xoops breadcrumbs for all pages , selection on prefference page.
	- Fix bug in query of parent category.
	- Fix bug in "Prev" "Next" link of same post time.
	- In list page , display with sanitize function is valid before "pagebreak" tag.
	　If no pagebreak tag, no change as before.
	- The edit permission setting is added in prefference screen to disable to edit by no dhecked group members.
	- Viewcomment page can display all comments until oldest to latest comment.
	- User name selection ( uname or name )in prefference page is available.
	- Centralization of smarty variables assign in each source. ( to be countinue )
	- Fixed bug of missing field in importing function between both d3diary.
	- Group list and user list of view permission option in category setting and diary form page is available, which setting is added in prefference page, with suggestion function for user list.（thx hodaka [d3blog])（thx nao-pon [xp-wiki])
	- Fixe bug of unexpected date-time of external blog in php4 environment.（thx haruka）
	- Newest snoopy for getting external blog feed.
	- Shift the common procedures to class , which is called with instance call method, and also used by each block source.（thx nao-pon [xp-wiki])
	- The class related to the display authority is changed independent and is set on the prefferences screen. （thx GIJOE [pico]）
	- Several new blocks are available, each personal side menu blocks. They are also selectable whether displaying personal page or all member's aggregation list page by prefference screen.
	- Debug time is available while in xoops php debug mode.
	- Month and day query is added in diarylist page and the external blog entry is also displayed.
	- Javascript sources ( including prototype.js, tag.js ) are moved to "trust_path/lib/js/" and loaded by loder.php.（thx nao-pon [xp-wiki])
	- Entry's counter is available , which counts except the access by owner self.
	- When viewing the top page, user having own entry will be redirected to his index page and the guest or no entry user to diarylist page.
	- Category link is added in diarylist page. 。（thx OhYeah!）
	- HTMLpurifier function is added on submit. (php5, protector are neccessary）Exclude setting in prefference screen.（thx GIJOE [pico]）
	- Nico-video insertion is available.
	- Bug fix of uploading or deletion images while mainly in preview mode. 

2010/08/05  ver0.11a3 (Development version)
	- "PREV" "NEXT" diary link is displayed for all entries.
	- Sub categories are available for one sub-level.
	-- In categoryedit page, move the category line under the parent category,
	  and check the "SUB" checkbox and then submit.
	-- Both the parent and sub categories are queried by clicked the parent category.
	-- The category which is set to external blog is not valid for subcategory.

2010/07/31  ver0.11 (Development version)
	- Preview mode modification.
	-- Image file uploading in preview mode is available.
	-- Image delition is now also in preview mode to avoid entry text being erazed under writing.
	-- Preview width will be noe same as detail page.

2010/07/04  ver0.10
	- Fixed bug that wrong link of no category at side-bar. (thx AXYZ-SAK)
	- Fixed the authentification of comment list page.
	- Added the function to show friends' entries.
	-- Added friends list with link and friends' entries view link on side-bar.

2010/06/20  ver0.09b (Development version)
	- CSS layout : also subitems in sidebar.
	- Specified date and time subscribe is available. （thx mizuki）
	-- Permission for Specifying date in preference screen.
	-- For future entry, each entry list, comment list, serach result,
	   and d3comment list are also controlled and hided except for 
	   administrator and entry's owner.

2010/05/16  ver0.09 (Development version)
	- Development version for studying CSS layout.
	-- CSS is editable on preferences screen same as templates.
	-- Modified side bar and main area for CSS layout.
	-- Changed the list images on side bar using CSS for speed up and adjustment.
	-- Changed the layout of uploaded images using CSS float layout, to improve
	   the layout crumble on narrow themes.
	- Displaying user avatars , using selection is available on prefferences.（thx taroj）

2010/05/11  ver0.08b2
	- Page division of personal monthlist in side bar.
	- Fixed bug of long title cause layout crumble in index page.

2010/05/10  ver0.08a
	- Fixed bug which cannot use smily list on "simple" textarea.(thx taroj)
	- Fixed bug that common category displayed "no category" in diarylist block.

2010/05/09  ver0.08
	- Administration authority was limited for administroator group, it is 
	  changed also to module administration group members.
	- Fixed bug that a member which have administration authority editing
	  other member's diary changes the right to himself. 
	- Fixed the errors of RSS page.
	- Common category for all members. (made by administration member) thx mimmin
	- Query function on diarylist page by a common category.
	- Checking each diary's selection and transferrance to other category
	  on each person's page with category mode.
	- Update ping transmission function with setting and server lists on 
	  prefference screen, and it's uage selectable on diary editting screen. thx Ken

2010/05/02  ver0.07
	-　Added page navigation for each person's list, it's controled
	  and devided pages by preferences setting. External blog display
	  or openarea setting may change the entry list cout, but the 
	  display speed will be faster.
		
2010/04/20  ver0.06
	-　Preview function except for uploading images.
	-　Thumbnail image size on preferences setting. :thx kuma
	-　Pagenavigation display modification :thx kenpa
	-　HTML post available for user groups allowed by preferences setting.
	-　Dhtmltarea with BBcode helper is selectable on preferences screen for 
	  body textarea, additionally to existed simple form.
	-　For HTML post allowed users, default value of HTML post for each
	  category is selectable.
	-　Use of FCKeditor is selectable on preferences setting. For HTML post 
	  allowed users, FCKeditor is loadable by pushing button on edit form.

2009/08/30  ver0.05
	-　Increase query item numbers for the list_block.
	-　Fixed bug of not display when YouTube tag includes "-".
	-　Fixed bug of displaying d3comment in rightarea.html when 
	  using category's authentification.
	-　Fixed bug of URL link cannot not jump correctry in list_block
	  when using each user's whole external blog setting.

2009/07/16  ver0.04
	- added an option of maximum display numbers for each
	  person of diarylist-block.
	- Fixed D3comment block's incorrect authentification.
	
2009/07/05 ver0.03
	-　Fixed various bugs until ver0.02d, d3pipes' posts-joint bug.
	-　Changed external-linked article's authentification 
	  function of d3comment integration class.
	-　Added external-linked article's authentification
	  function for d3comment blocks.
	  see http://www.naaon.com/modules/plactice/index.php/home/hobby/xoops/d3com_auth.htm

2009/06/30 ver0.02 newly released
