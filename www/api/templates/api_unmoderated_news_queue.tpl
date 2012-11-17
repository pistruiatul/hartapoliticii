{* Smarty *}
{*
This API point is consumed in a Python script that crawls and tags the content
that is part of the links exposed here.

For that reason, for now this is all just a list of links, each on one line,
because we will know whether a link was posted when adding it to the news
section from the crawler so we don't need to expose or pass that information
around.

*}
{section loop=$links name=l}
{$links[l].link}
{/section}