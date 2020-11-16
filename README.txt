SO Planning - Readme
==================

    http://www.soplanning.org

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 3,
    as published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

---------------------------

v1.47.01 (05/11/20)

- Bugfix on datepicker and timepicker, in some cases the position of the picker is not good
- Bugfix for display on mobile
- bugfix on uploaded files, drag and drop on task can fail due to joined files
- Bugfix on task during less than 1 day. They are not visible (although they should be) when specific option - hide tasks covering weekend - is activated
- Several security fixes

v1.47 (21/07/20)

- You can now upload files in tasks !
- new feature in order to cut tasks in 2 parts when covering several days. The task is splitted in 2 tasks, from each side of the selected date
- sticky header for the planning, now you will use browser scrollbars (more fluent and instinctive)
- added planning advanced filters and sorting choice in cookies (saved even when disconnected)
- added planning filters on people/projects/places/resources in cookies (saved even when disconnected)
- added task status in notification email
- added new optional line in the planning, in order to view total number of tasks per day
- 3 new rights for teams : you can now give specific rights on users for their team, and combine with existing rights. 
   1) a user can now manage other users profile (create/modify/delete) from his team with the relevant right
   2) a user can have his planning view limited on people of his team
   3) a user can modify only tasks assigned to people in his team
- In project list, you can click on calendar button in order to access directly to project planning (filtered in the selected project). Now start and end dates of the planning are changed based on first and last task of this project
- Project list : filter on group or text search are now keep in memory/session
- updated spanish version
- Improved starting hour management/calculation when using detailed view in planning
- Added tooltip information on projects page, in order to display number of tasks for each project
- You can now "enlarge" tasks : by drap n' droping the last day of a task, it will enlarge the task to the destination date. Working only with last day of a task covering several days
- security update an several external libraries
- Security update about "remember me" feature and unique keys
- Bugfix on tasks and projects status selection on the planning
- Bugfix on task copy (tasks were linked although they shouldn't be)
- bugfix on colorpicker for statutes
- Bugfix on task move when using option "one assignment max per day"
- Bugfix on project dropdown menu for tasks
- bugfix on colorpicker when limited colors
- bugfix on ICAL when using characters with accents


v1.46.00 (03/03/20)

- New feature, ability to shift a whole project
- Added better timepicker in task form
- Added "remember me" feature to the login page
- Summary below planning now displayed by default
- Autoresize for task comment input 
- Task cell bevel in the right side depending on AM/PM
- PHP 7.2 deprecated code replaced SQL injection vulnerability fixed
- New italian translation version (+help)
- Added Brazilian portuguese language
- Improvement on PDF display
- Option for weekend / days off now hide days/columns when activated
- Correction bug drag & drop
- Bugfix on PDF export
- Manage accent in LDAP connection
- Bugfix on project statutes
- Bugfix : lines are now hidden in summary (below the planning) when "hide empty lines" is activated
- Several security issues fixed

v1.45 (19/08/219)

- new form decicated to task creation ! This form is lighter than the modification form and will save time when creating several tasks.
- improved hour selection in task form, it's now easier to fill hour/minute
- Task during less than one day are now beveled, and the text displayed is colored accordingly
- versioning upgrade, now we'll be able to publish minor releases for small bugs/quickfixes
- improved link between tasks assigned at the same time. Now you can see all people that are linked to this task. Tasks remain separated in the planning view for better understanding.
- new CSV export, with raw data : the file will show all columns for each task
- german translation update
- improved installer/upgrader, will make more checks and will clear smarty cache
- user profile popin improvement (interface and explanations)
- new option in ICAL export, you can now define number of months included for past tasks
- statutes management improved
- mobile version improved
- floating scroll in the planning improved
- many small fixes as usual


v1.44 (13/03/19)

- new fresh module : audit / history. This module will let you register allmost all actions in the planning : task modification, task removal, user modification, etc. You will be able to view all past actions and restore data if needed.
- New design improvments, on desktop and mobile. Closer to current design standards
- New options for task display in SOPlanning parameters : you can choose if you want to display specific icon depending on task data (link, comment, duration), you can decide where the task color come from (assignee, project, or status), etc
- Better quality for SOPlanning icon, so you can add it as favorite in your desktop browser, or in your mobile homepage.
- Drag and drop improved, you see clearly where you move/copy your task
- new german translation and minor fixes in other languages
- Added user or project code on milestone tasks
- many small fixes and improvments as usual
- you can choose what is displayed in task cells : people ID, project ID, people name, project name, ressource, place
- new option in settings, allow to hide days off in the planning
- added "home" button in order to easily display today (+ 2 months) in the planning


v1.43 (18/12/2018)

- Improved drag and drop : now you can select "move task" only for selected task or all repeted tasks
- New option to change text on task cells (increase it if you want)
- Improved statutes management : display options (bold, underline, strike, etc) and more
- New option for administrator in user profile : you can deactivate an account and reactivate it later if needed
- added timezone to SOPlanning : you can now select your own timezone
- Improved color picker for projects and users color
- German translation update + german public holidays
- added option in your user profile in order to define which planning start date you want as default when loading SOPlanning
- new option for the planning : you can hide/display weekends, and define if those days are grey or not
- now compatible with PHP 7.2
- Now you can switch language in your profile without having to logout
- added places and ressources detail in the summary under the planning
- Fixed several small bugs


v1.42 (27/07/2018)

- Big new version of the planning. Mostly code rewriting, not so many changes on display, but performances improved.
- Help pages integrated in the tool. The help content will be improved in the next versions
- LDAP : added login and password for bind command
- Added port parameter for mysql DB
- Several fixes, for display or security improvements
- Many fixes on some specific rights
- Email addresses with new TLD now allowed
- Missing translations added
- Bugfix for iphone display
- Bugfix for some PHP versions

v1.41 (20/12/2017)

- Big change : now statutes are stored in database, for task status and project status. It means you can change status name, add new ones, etc !
- PDF export : summary under the planning is now an option (included or not)
- Project filter on planning : projects covering the timeline are now displayed first in the filter
- Added "people" filter in "My tasks" modules, so you can filter by selecting one or more users
- Improvement in the planning on task order when mixing AM / PM / hours. Now they are ordered based on their start
- Many display improvements
- Several bug fixes : update graph library, PHP 7.1 compliancy, PDF display, etc.
- Bugfix on new version alert : the alert was blocked and will be display again on login page when new version available


v1.40 (21/09/2017)

- PHP7 compliancy !
- option added in task form to disable notification by email
- you can now open task from the summary under the planning (click on start date)
- new design for notification emails
- emails now contain link to open the task
- new menu with statistics : charts for users and projects, showing workload/charge assigned. new charts/stats will come soon
- new module : "my dashboard", allow you to see your task in list mode
- new right : you can define which specific people a user can see in the planning
- many small improvements in interfaces and design
- small bug fixes

v1.39 (01/04/2017)

- Big work on mobile/tablet version
- new scrolls added on the planning, you can now fix display horizontaly and verticaly
- you can now click on the planning header, it will display the planning based on the clicked day/week/month
- Fixed several specific cases on task repeat
- added parameters to define column width in the planning
- added parameters to define cells/tasks width in the planning
- added many new themes with new colors
- added option to let/block guests creating tasks (only if guest access is activated)
- Improve upgrade system, tackle some specific cases
- Improve form fields when filling hours
- Several libraries updated for security and browser compatibility
- user identifier updated to 20 chars max (10 in the past)
- Various bugfixes and small improvements coming from users feedback

v1.38 (28/10/2016)

- XLS export added
- new right added to user : show him only his assigned tasks, he will not see tasks assigned to other people
- Big layout upgrade for better browser compatibility, and maintenance for the future
- added theme option with customization. Only current SOPlanning for the moment, new themes will come later
- added various user fields : address, phone, mobile, job, comment
- Added feature for Logo upload in the application/homepage header
- Summary below planning : display improved, and total time is now mixed (days and hours were separated in the past)
- Translations updated
- Some fixes on user rights
- Various small bugfix
- now possible to start task at 00:00 as start hour

v1.37 (27/06/2016)

- 2 new optional modules : places and ressources. You can now assign a place and/or a ressource in task. The system will check you won't use the same place or ressource at the same time
- Select multiple users when creating tasks : you can know copy the task to many people at the same time (2 maximum in the past)
- New filter on teams added to users management module.
- German translation updated (thanks to contributor)
- Fix on PDF export when the cell content is larger than one page
- Fixed some issues on repeated tasks
- Many small improvments on interfaces and error messages
- Several small issues fixed, reported by end users
- Performances optimized on the planning, specially when many months displayed
- Added Polish translation
- Added task list to PDF export


v1.36 (12/01/2016)

- Swiss public holidays updated
- fixed list of projects displayed when using specific right
- Added SMTP log to Email test (in parameters section) in order to have more details
- Added "last login date" in user list
- Added task information on rollover : creation date, last modification date, and who did it
- display now days off in "planning view per day"
- project list : added shortcut to display and filter the planning based on clicked project
- user list : added shortcut to display and filter the planning based on clicked user
- added new option for ICAL calendar : now possible to display all users or only you, and display all projects or only a specific list of projects
- project : added "archived" status
- project : fixed custom color
- new options/possibilities for task repetition (test it and you will like it !)
- new user preferences (click on your profile name in the header) : choose default view

v1.35

- New fresh view : click on the "zoom" icon and display more detailed cells for tasks : user name, title, duration, day occupency
- Added total hours assigned and occupency rate at bottom of the planning (if activitated)
- Total hours (bottom of the planning) now well computed and displayed in the last right cell of the day
- Now save horizontal and vertical scrolls position for each view (months and days). Refreshing, filtering or switching does not affect anymore planning viewing
- backward / forward buttons are now sticked to dates selected, move for the same duration
- Modification on a repeated task is now applied to all repetitions
- You can now repeat task from every task, not only the original one, and you can repeat in the past (retroplanning)
- Added buttons "Delete before / after" on repeated tasks
- Drag and drop a task covering several days : start date is now relative, defined by the day you moved
- Obsolete code deleted for better compatibility with last versions of PHP
- Completed or abandoned projects : they are now hidden in the planning, unless one task is covering days displayed
- Fulltext search upgraded, can now search on several words, will display all tasks concerned by one of the words

v1.34

- New design/features for date selection in the planning ! Check it out !
- Added free custom field in the tasks, for your specific needs (import/export with your system, etc)
- Improvement in ICAL compatibility (specific cases on Outlook and Google Calendar)
- Added task creator information in notification email
- Task link no longer displayed in the printable version
- Added scrollbar on top for the planning (helpful when many lines)
- Added sorting option "by delivery date" on projects page
- Improved installation process and tests (libraries, rights, etc)
- Filter on user now saved in cookie
- Added TLS option to LDAP connection
- when you click on a project line in the planning, now this project is selected in the task creation form
- Changed session name in order to separate sessions on the same domain. Now you can install several SOPlanning on the same domain or locally.
- Deactivation of some deprecated PHP alerts
- Some minors bugs and missing translations

v1.33

- Danish translation. Thanks to Peter Wollerup.
- Hungarian translation. Thanks to Péter Horváth.
- drag and drop fix for firefox 30
- Various security issues, thanks to Huy-Ngoc Dau

v1.32

- Many small fixes coming for SOPlanning users, thanks to all
- Active directory support (experimental)

v1.31

- new planning display : now possible to display per day with hour slots !
- new funny tool to select project and user when creating task
- fields order changed in task form, for a better experience
- Many small bug fixes due to the new layout
- added sortable option in project list
- Drag and drop fixed
- Norwegian public holidays added, thanks to Kai
- Bug fix for gantt chart on recent PHP version

v1.30

- Design updates : thanks to Sebastien who reviewed all interfaces and applied the same design everywhere. Not a big visual impact, but more flexible and more compatible design !
- auto-installer/upgrade system added. No file modification needed anymore, the interface will help for initial setup or upgrade to a new version.
- Session isolation : you can now install 2 SOPlanning instances on the same url (with 2 differents databases), sessions will not be shared. You will be able to browse both instances.
- Contact form for easier communication with support.
- Planning options (date, months display, etc) are now kept in cookies and restored at your next login.
- added an option in the planning to display the total time per day

v1.29

- Major update on DE translation (thanks to contributors)
- full text search now search also on task title
- Added teams and project groups on the PDF export (only displayed in the planning previously)
- left column on the planning : added link on objects (users or projects) to open directly the popup window.
- Top menu now compatible with Mobile/tablet : click on the menus will open sub-menus
- Bug fix on project deletion (specific rights).
- Email notification when moving a task (drag and drop).
- "Sort by" choice is now stored in cookie, to keep the same preference at next login.
- Modified summary table below the planning : if the planning is displayed by user, now also displays the summary table grouped by user. Same thing for project. Till now, only display by project was available.
- Added small features to send a test email with the setup done. Allow to test easily if it works.
- Bug fixed on total time calculation (when more than 100 hours cumulated)
- Fix on email sending, for task creation. New help text for SMTP setup.
- Date calculation improved on recurring tasks : now escape days off AND go back to the original day for next weeks/months.

v1.28

- Update on planning menu, more "fluid" with smaller screens.
- Bug fix on Firefox and IE : buttons didn't work in the planning (print, export, etc).
- Bug fix on date picker, specific characters incorrectly displayed
- Changed display of links in tasks, and add automatically http://

v1.27
- Small fixes
- New feature to limit assignment to one task per user per day.
- added task creator name on the planning. Displayed when mouse is over the task.
- Added sorting option : can now sort the planning by name (project or user), id, team, group of project. If team or group, their name is also displayed as a separator in the planning.
- Added new option to display planning header (months/days) every x lines.
- Added milestone : can create a task as a milestone, displayed with specific icon in the planning. Also added to Gantt export.
- Added task status : to do, in progress, done, abandoned. Done and abandoned tasks are displayed crossed
- Added also a filter in the planning to display only status selected.
- Added repetition information in the email notification
- Fix bug on task creation : end repetition datepicker was never closed
- Dropdown added on title field when creating a task, in order to display existing titles for the same project.

v1.26

- email notification on task creation/modification/deletion
- improved project list, display all or filter by date
- now possible to send email to new user created to let them modify their password
- the planning is now exactly limited on one month (it was before 1 month + 1 day)
- project charge added on the summary under the planning
- filtered project list displayed on task creation/modification (only active/todo projects displayed)
- fixed XSS issues
- some new security fixes
- added new user right to let him view all people in his team
- can now close opened window (task creation, project creation, etc) with ESC key

v1.25

- Added public holidays import module for several countries
- new PDF calendar export (condensed view)
- Minor fixes on new design
- Some Security upgrades

v1.24

- New full layout !
- Added portuguese and spanish languages
- Added project color in the planning column, and today highlight.

v1.23

- overlapping periods are now displayed perfectly : cells stay on the same line instead
- added groups of users, with ability to filter planning on those groups
- excluded days off and public holidays from the repetition feature

v1.22

- Bug fix on project creation for users with limited rights
- added half day displayed differently in the planning
- added task title : display in mouseover on the planning, and in Gantt export
- added ICAL export (for calendar sync)
- Minutes management in task duration
- added days off management
- small fixes

v1.21

- added "repeat task" feature : daily, weekly, monthly
- fix on user self modification
- Fix on drag and drop when planning is inverted (displayed by project)
- Changed users rights for finer management
- added color management for users => better view in planning when displayed by projects
- some minor fixes coming from users

v1.20
- added Gantt export
- added email field to user profile
- added interface to allow user to change own email and password
- added password recovery feature
- added option to setup the SMTP parameters
- added an url for the SOPlanning instance, for email links
- added option to change SOPlanning name
- replaced "notes" field in a task assigned by a multi-lines input field
- replaced the old color picker
- password are now crypted in the database (sha1)
- fixed accent bug on PDF export
- fixed display bug with "hide empty lines" feature
- fixed when moving a task, the duration is conserved
- fix on months and days translation
- project identifier size extended to 10 cars
- user identifier size extended to 10 cars
- added text filter in the planning view

v1.19
- Check on read/write of important directories
- Deported all config.inc variables in option page (now editable in Soplanning interface)
- add "hide empty line" feature in the planning
- Bug fix on printable version
- Bug fix on CSV export
- Bug fix on drag n drop feature
- Bug fix in database auto upgrade

v1.18
- New overall look and feel
- PHP version check (>= 5.2)
- Database auto-updater for new releases
- Changed default line height in the planning (fits to assignment cells)
- separate rights between admin and planers : admin : all rights (project management / users / planning modification). Planner : can create projects, modify/delete his own projects, assign tasks to his projects. No access to users management.Read only : no modification right, can only view planning.
- paging added to the planning, to limit number of lines displayed
- PDF export

v1.17
- planning view : move/copy option on drag and drop

v1.16
- code rewriting (date management review) => gain x10 in planning view
- somes fixes on planning filters and version check
- Fix on SQL import file

v1.15
- Code rewrite
- Fix on version check (Chrome and display improved)
- Minor fixes

v1.14
- added duplicate button for period
- allow period copy on existing period (as for creation)
- Added online version check
- fixed language detection in firefox
- fixed filter window positionning
- Can now change filter display (number or users / projects per column)
- Better refresh management (not after each window closing, etc)
- Easier to integrate in other links

v1.13
- Fixed filters on planning (position and filter kept when changing date)
- fixed language management on dates for some platforms.

v1.12
- Minor fixes
- Many display improvements
- Planning displayed now in a scrolling layer, without moving other menus
- added options submenu

v1.11
- End date copied from start date when empty
- http link added to period. DON'T FORGET TO RUN /upgrade/1-11.sql
- in planning view, users ordered by name (no longer by user id)
- in planning view, tasks ordered by start date in the bottom projects table
- fixed week display in specific case
- groups of projects added to the planning filter

v1.10
- Add CSV Export functionnality
- Repeat days in table every 10 users
- Add postgresql support
- Add ldap login support
- Add NL translation

