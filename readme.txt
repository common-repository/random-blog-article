=== Random Blog Article ===
Contributors: cohen
Donate link: http://www.blograndom.com
Tags: 
Requires at least: 2.0.2
Tested up to: 2.1
Stable tag: 1.01

Cycle through articles from your favourite blogs with ajax updating. Like a blog roll but using (cached) RSS feeds
to diplay a random article from the blog, one by one.

== Description ==

The random blog article was written to display links to some of my favourite blogs on the homepage of my site. 
I decided the blogroll took up too much space and wanted article titles on the site too.

When the page loads the first blog and article is picked random and displayed. After this, every 15 seconds 
(by default) a new blog and article are randomly picked from your favourite blog list. The RSS feeds are cached so
no need to worry about bandwidth usage or your blog being slowed down!

The plugin just needs to be activated and a small peice of code put in a template file for where ever you'd 
like it to appear. All settings can be controled in the wordpress admin panel and there is no need to edit any code.

== Installation ==

1. Upload the "rbarticle" folder to your wp-content/plugins/ directory.
2. Chmod the cache folder 777.
3. Copy `<?php rbarticle_tpl_init() ?>` to where ever you would like the random article to appear.
4. Add and modify these styles in your template's styles.css file
	#rbarticle_result		(the div container that the article is updated in to)
	#rbarticle_result .blog		(the class for the blog title)
	#rbarticle_result a		(the article link)
	#rbarticle_result .separator	(a span class around the separator (if specified))
5. Activate the plug-in
6. Browse to Options > RB Article
7. Add rss feeds to your list of favourites
8. Enjoy

== Change Log ==

v1.01
- Moved javascript initialise code to php init function (stops js errors on pages without the random blog div)