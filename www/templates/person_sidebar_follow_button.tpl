{* Smarty *}


<div class="follow_button {if $following}unfollow{else}follow{/if}"
     person_id="{$person_id}"
     action="{if $following}unfollow{else}follow{/if}">
</div>