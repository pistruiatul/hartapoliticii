<?xml version="1.0" encoding="UTF8"?>
<rss version="2.0">
    <channel>
        <title>{$rss.title}</title>
        <link>{$rss.link}</link>
        <description>{$rss.description}</description>
        <language>ro</language>
	    {foreach from=$rss_items item=rss_item}
	      <item>
            <title>{$rss_item.title}</title>
            <description>{$rss_item.description}</description>
            <link>{$rss_item.link}</link>
            <pubDate>{$rss_item.pubDate}</pubDate>
          </item>
	    {/foreach}
    </channel>
</rss>