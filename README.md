# orbit-bundle Wordpress Plugin
Create wordpress custom post types and custom taxonomies. Search and filter through the wordpress post types and create custom queries using simple shortcodes.

## Installing the Plugin
1. Upload "orbit-bundle" to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Create a new page.
4. Paste code [orbit_query] for posts.
5. Publish the page.

## Customising the templates in the Theme
1. Create a folder "orbit_query" within the theme.
2. To create a new template "card". Create a file with the name "articles-card" inside the folder "wp-content/[your-theme]/orbit_query".
3. To use the new created template, within the shortcode [orbit_query] use [orbit_query style=card].  

## Parameters of orbit_query shortcode
1. cache: By default the value is 0. Expects value in ms for which the query will be cached in the transient cache.
2. tax_query: Show posts associated with certain taxonomy and terms. Display posts that have one taxonomy '''[orbit_query tax_query="video-category:reels"]''' or multiple terms of the same taxonomy [orbit_query tax_query="video-category:reels,brands"] or multiple terms of different taxonomies [orbit_query tax_query="video-category:reels,brands#regions:asia,africa"]
3. date_query: Show posts associated with a certain time and date period. [orbit_query date_query="after:2019/01/01"] will posts that were published after 1st January 2019. [orbit_query date_query="after:2019/01/01"] will posts that were published before 1st January 2019. [orbit_query date_query="after:2019/01/01#before:2019/31/12"] will posts that were published between 1st January 2019 and 31st December 2019.  
4. sticky_posts: By default the value is 0. If set to 1, it will keep the sticky posts on the top.
5. exclude_sticky_posts: By default the value is 0. If set to 1, it will remove all the sticky posts from the query.
6. post_type: Show posts associated with certain post status. By default the value is post. Display posts with multiple post type [orbit_query post_type="post,video"]. Use 'any' to retrieve any type except revisions and types with 'exclude_from_search' set to true.
7. post_status: Show posts associated with certain post status. By default the value is	publish. Display posts with one status [orbit_query post_status="publish"] or multiple post statuses [orbit_query post_status="publish,draft"]. Use 'any' to retrieve any status except those from post statuses with 'exclude_from_search' set to true (i.e. trash and auto-draft).
8. posts_per_page: The number of posts that should appear on a page. By default the value is 10.
9. post__not_in: use post ids. Specify post NOT to retrieve. [orbit_query post__not_in="1,41,32"]
10. post__in: use post ids. Specify posts to retrieve. If you use sticky_posts attribute, they will be included (prepended!) in the posts you retrieve whether you want it or not. To suppress this behaviour set exclude_sticky_posts to 1. [orbit_query post__in="1,41,32"]
11. s: Show posts based on a keyword search. [orbit_query s="keyword"]
12. author: Show posts associated with certain author. Display posts by author, using author id.
13. cat: Show posts associated with certain categories using category id. Display posts that have one category (and any children of that category) [orbit_query cat="2"] or multiple categories [orbit_query cat="2,6,7"].
14. tag: Show posts associated with certain tag using tag slug. Display posts that have one tag [orbit_query tag="cooking"] or multiple tags [orbit_query tag="bread,baking"].
15. offset: The number of post to displace or pass over. By default the value is 0. This parameter is ignored if posts_per_page is set to -1.
16. pagination: By default the value is 0. If set to 1, pagination appears below the posts.
17. paged: The number of page whose default value is 1. Show the posts that would normally show up just on page X when using the "Older Entries" link.
18. style:
19. order: Designates the ascending or descending order of the 'orderby' parameter. Defaults to 'DESC'.
20. orderby: Sort retrieved posts by parameter. Defaults to 'date (post_date)'. One or more options can be passed. Follow [WP DOCUMENTATION](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters) to find the options that can be passed in the shortcode.



Works with WP Pusher plugin
