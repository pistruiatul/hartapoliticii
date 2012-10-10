<?xml version="1.0" encoding="UTF8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{$rss->title}</title>
        <link>{$rss->link}</link>
        <description>{$rss->description}</description>
        <language>ro</language>
	    {foreach from=$rss->getRssItems() item=rss_item}
	      <item>
            <title>{$rss_item.title}</title>
            <description>{$rss_item.description}</description>
            <link>{$rss->link}</link>
            <guid>{$rss_item.link}</guid>
            <pubDate>{$rss_item.pubDate}</pubDate>
          </item>
	    {/foreach}
	    <atom:link href="{$rss->atomLinkSelf}" rel="self" type="application/rss+xml" />
    </channel>
</rss>