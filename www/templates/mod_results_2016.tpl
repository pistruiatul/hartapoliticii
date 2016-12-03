{* Smarty *}

<div style="margin: 20px 0 10px 0;">
Candidat:
    <li>
        <a href="?cid=27&colegiul={$college_name|lower}&cam={$cam}">{$college_name|ucwords} pentru
        {if ($cam=="D")}
            Cam. Deputaților
        {else}
            Senat
        {/if}</a>
    </li>
    <li>poziția <b>{$position}</b> pe lista de partid</li>
    <li>din partea
        <a href="?cid=27&colegiul={$college_name|lower}&cam={$cam}">{$party_name}</a>.</li>

<p>
    <!-- show the data from the alegeriparlamentare2016 site if we have it -->
    {if $details.integritate}
        <div style="margin-top: 10px;"><b>Integritate</b></div>{$details.integritate}
    {/if}
    {if $details.stat_de_drept}
        <div style="margin-top: 10px;"><b>Atac la adresa statului de drept</b></div>{$details.stat_de_drept}
    {/if}
    {if $details.controverse}
        <div style="margin-top: 10px;"><b>Controverse</b></div>{$details.controverse}
    {/if}
    {if $details.istoric_politic}
        <div style="margin-top: 10px;"><b>Istoric Politic</b></div>{$details.istoric_politic}
    {/if}

    <div style="margin-top: 10px;">
        {if $details.declaratie_avere}
            <li><a href="{$details.declaratie_avere}" target="_blank">Declarație de avere</a></li>
        {/if}
        {if $details.declaratie_intere}
            <li><a href="{$details.declaratie_intere}" target="_blank">Declarație de interese</a></li>
        {/if}
        {if $details.activitate_parlam}
            <li><a href="{$details.activitate_parlam}" target="_blank">Activitate parlamentară</a></li>
        {/if}
    </div>

    {if $details.sursa}
        <div style="margin-top: 10px;">
            <a href="{$details.sursa}" target="_blank">sursa: alegeriparlamentare2016.ro</a>
        </div>
    {/if}
</p>


</div>
