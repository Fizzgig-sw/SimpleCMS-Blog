Simple Blogging Platform
---
---

This is a simple blogging platform. No login, No signup, and No third-party is required.

There are no buttons or links for the editing controls. This is intentional so that visitors do not know they exist.

Titles of blog entries are contained in the address bar. For example **/blog** becomes **Blog**, **/my-cool-story** becomes **My Cool Story**.

Navigation to special access sections of the site is done through the address bar by adding **&edit** to the end of a url.

To create a new page or edit an existing page type the title followed by **&edit**. For example */blog&edit, /a-new-page&edit*. If the page exists you will edit it. If it does not exist you will create it.

If you want more security you can edit the **config.php** file to change the **EDIT_PIN**. Now you need to use **&edit=x** (**x** must be the same as your EDIT_PIN).

There are other special pages:

- /sitemap
- /tags

There are also special controls on these pages if you use **&edit**.
*/sitemap&edit, /tags&edit*


Special access only:

- /docs&edit
- /settings&edit

*These pages will return 403 to normal visitors.*
