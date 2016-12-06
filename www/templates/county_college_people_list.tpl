{* Smarty *}

<table width="250" cellpadding=2>
    {section name=c loop=$people}

    <tr>
        <td valign="top">
            <div class="medium {if !$people[c].makes_it}really_light_gray{/if}">
                {$smarty.section.c.index+1}. <a href="?name={$people[c].name|replace:' ':'+'}">
                    {$people[c].display_name}</a>

                    <div class="small {if !$people[c].makes_it}really_light_gray{/if}" style="margin-left:15px">
                        {$people[c].history_snippet}
                    </div>

            </div>
        </td>
        {/section}

        {* only show this if there actually are minorities candidates around *}

</table>