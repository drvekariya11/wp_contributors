wp_contributors
===============

A plugin to display list of contributors on a post.

Admin-Side:

    Add a new metabox, labeled “contributors” to WordPress post-editor page.
    This metabox will display list of authors (wordpress users) with a checkbox for each author.
    User (author/editor/admin) may tick one or more authors name from the list.
    When post saves, states of checkboxes for author-list in “contributors” box will be saved as well.

Front-end:

    Use a post-content filter.
    At the end of post, display a box called “Contributors”.
    It will have list of authors checked for that post.
    Show contributor names with their Gravatars.
    Contributor-names will be clickable and will link to their respective “author” page.

Here is a live demo link http://dharmendrartcamp.tk/hello-world/
