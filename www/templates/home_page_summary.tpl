{* Smarty *}
<script type="text/javascript" src="http://hartapoliticii.ro/clickheat/js/clickheat.js"></script><noscript><p><a href="http://www.dugwood.com/index.html">CMS</a></p></noscript><script type="text/javascript"><!--
clickHeatSite = 'hartapoliticii';clickHeatGroup = 'homepage';clickHeatServer = 'http://hartapoliticii.ro/clickheat/click.php';initClickHeat(); //-->
</script>

<table width=970 cellspacing=10>
  <tr>
    <td width=210 valign="top" rowspan=2>
      <a class="button big green" href="/?cid=cauta_colegiu">
        <span style="width:160px">
          <img src="/images/icon_map.png" width="30" style="float:left">
          Caută colegiul<br>din care faci parte</span></a>
      <br>
      <a class="button big blue" href="/?cid=sectii_votare">

        <span style="width:160px">
          <img src="/images/icon_house.png" width="30" style="float:left">
          Caută secția ta<br>de votare</span></a>
      <br>
      <b>{$days_until_election}</b> zile până pe 9 Decembrie

      {* ------------------------------------------------------------------*}
      {* Show the politicians that are most present in the news. *}
      {if count($followedPeople)>0}
      <p class="smalltitle">
        <strong>Pentru cei ce-i urmărești</strong><br>
      </p>
      {include file="home_page_most_mentioned_list.tpl" people=$followedPeople}
      <div class="small">
        <a href="/?cid=14&sid=1">Vezi toată lista...</a>
      </div>
      {/if}

      <p class="smalltitle">
        <strong>Cei mai mediatizați</strong><br>
      </p>
      {include file="home_page_most_mentioned_list.tpl" people=$topPeople}

      <div class="small">
        <a href="/?cid=14&sid=3">Vezi toată lista...</a>
      </div>
      {* ------------------------------------------------------------------*}
      {* Show the presence for the senate. *}
      <p class="smalltitle">
        <strong>Prezența Senat</strong><br>
      </p>

      <table width=210 cellspacing=0 cellpadding=0>
        {include file="home_page_summary_presence_list.tpl"
            people=$top_senators}
        <tr>
          <td colspan="2">...</td>
        </tr>
        {include file="home_page_summary_presence_list.tpl"
            people=$bottom_senators}
      </table>

      {* ------------------------------------------------------------------*}
      {* Show the presence for the house of deputies. *}
      <p class="smalltitle">
        <strong>Prezența Cam. Dep.</strong><br>
      </p>

      <table width=210 cellspacing=0 cellpadding=0>
        {include file="home_page_summary_presence_list.tpl" people=$top_cdep}
        <tr>
          <td colspan="2">...</td>
        </tr>
      {include file="home_page_summary_presence_list.tpl" people=$bottom_cdep}
      </table>

      {* ------------------------------------------------------------------*}
      {* A list of active parties. *}
      <p class="smalltitle">
        <strong>
          Partide active în parlament
        </strong>
      </p>
      <table >
      <tr>
        <td valign="center" align="center" width="60">
          <a href="/?cid=17&id=1">
            <img src="/images/parties/1.gif" height="50" border="0">
          </a>
        </td>
        <td valign="center" align="center" width="60">
          <a href="/?cid=17&id=2">
            <img src="/images/parties/2.jpg" width="50" border="0">
          </a>
        </td>
        <tr>
        <td valign="center" align="center" width="60">
          <a href="/?cid=17&id=7">
            <img src="/images/parties/7.jpg" width="50" border="0">
          </a>
        </td>
        <td valign="center" align="center" width="60">
          <a href="/?cid=17&id=14">
            <img src="/images/parties/14.jpg" width="50" border="0">
          </a>
        </td>
      </tr>
      </table>
      Lista de partide este încă în dezvoltare.

    </td>
    <td colspan=2>
      <div class="module" style="padding: 10px; background-color: #f9f9FF">
        <img src="http://hartapoliticii.ro/i/elections_decidingvotes.png"
           align="right" height=140 hspace="10">
        <span class="big"><b>Votul tău contează!</b></span>

        <br>În 2008, zece deputați au câștigat cu
        <a href="http://hartapoliticii.ro/?p=18">sub 100 de voturi</a> diferență.

        <br><br>Încă 26 au câștigat cu sub 500 de voturi diferență. Probabil ai mai mulți prieteni decât atât pe Facebook.

        <br><br>
        36 de locuri înseamnă 10% din Camera Deputaților.
        <br><a href="/?cid=cauta_colegiu">Caută-ți colegiul</a>. Informează-te
        despre candidați.
        <div style="float:right; width=200">
          <div class="fb-like" data-href="http://hartapoliticii.ro/" data-send="false" data-layout="button_count" data-width="200" data-show-faces="true"></div>
        </div><a href="http://hartapoliticii.ro/?p=7646">Susține-i</a>
        pe cei care vrei să câștige. Votează!
     </div>
    </td>

    <tr>
    <td valign="top" width="510">
      {* ------------------------------------------------------------------*}
      {* The main news section from the front page. *}
      <div style="float:right;margin-top:12px;font-size:85%">
        <a href="/?cid=14&sid=0">toate știrile...</a> |
        <a href="/?cid=14&sid=1">doar cei ce-i urmărești...</a>
      </div>

      <div class="news_type_menu">
        Știri recente
      </div>

      {include file="news_list_wide.tpl" news=$news}

      <div class="news_type_menu">
        <div class="add_link_button" onclick="ec.showAddLinkForm()">
          adaugă link <img src="/images/plus.png" align="absmiddle">
        </div>
        Resurse trimise de utilizatori
      </div>

      {include file="electoral_college_add_link_form.tpl"}

      {include file="news_list_ugc.tpl" news=$links}
      <div style="margin-bottom:12px;font-size:85%">
        <a href="/?cid=comunitate">toate...</a>
      </div>

      {*
      For now the score cards are going to be hard coded because:
        + there aren't that many
        + there need to be some changes to split them on cdep vs. senat tags
      TODO: make the list of public score cards dynamic, not hard coded.
      *}
      <br>
      <p class="smalltitle">
        <strong>
          Busola politică pentru parlamentari
        </strong>
      </p>
      <div class="medium">
        <div class="gray" style="margin-left: 10px;">
          O listă de probleme și cum au votat parlamentarii români pe fiecare
          dintre aceste problematici generale.
        </div>
        <table cellspacing=10 cellpadding=0 class="recent_news">

          <tr>
          <td valign="top" width="200" class="small">
            <a href="/?cid=15&tagid=17&room=senat">
              Drepturi Civile Digitale - Senat
            </a>
          </td>

          <td valign="top">
            <span class="small">
              libertate de exprimare, dreptul la viață privată, open copyright.
              <div class="gray">Tag alcătuit de
                <a href="http://apti.ro">
                  <img src="http://apti.ro/sites/default/files/apti.png"
                     align="absmiddle" vspace=5 hspace=5
                     height="15" border=0></a>
              </div>
            </span>
          </td>
          </tr>

          <tr>
          <td valign="top" width="200" class="small">
            <a href="/?cid=15&tagid=17&room=cdep">
              Drepturi Civile Digitale - Cdep
            </a>
          </td>

          <td valign="top">
            <span class="small">
              libertate de exprimare, dreptul la viață privată, open copyright.
              <div class="gray">Tag alcătuit de
                <a href="http://apti.ro">
                  <img src="http://apti.ro/sites/default/files/apti.png"
                     align="absmiddle" vspace=5 hspace=5
                     height="15" border=0></a>
              </div>
            </span>
          </td>
          </tr>

          <tr>
          <td valign="top" width="200" class="small">
            <a href="/?cid=15&tagid=5&room=cdep">
              Despre Adrian Năstase - Cdep
            </a>
          </td>

          <td valign="top">
            <span class="small">
              votarea trimiterii lui în instanță.
              <div class="gray">Tag alcătuit de Vivi.</div>
            </span>
          </td>
          </tr>

        </table>
        <div class="gray" style="margin-left: 10px;">
         Mai multe detalii despre cum au fost alcătuite aceste clasamente
          <a href="http://www.hartapoliticii.ro/?p=4042">aici</a>.
        </div>
      </div>

      {* ------------------------------------------------------ *}
      {* The most recently highlighted shit *}
      <br>
      <p class="smalltitle">
        <strong>
          Cele mai recente declarații marcate ca importante
        </strong>
      </p>
      <div class="medium">
        <div class="gray" style="margin-left: 10px;">
          {section name=d loop=$declarations}
            <div class="declaration_home_page">
              <div class="content">
                "... {$declarations[d].content|truncate:200}"
              </div>
              <div class="explanation">
                <div class="news_list_mention green_link">
                  <a href="/?cid=9&id={$declarations[d].idperson}">
                    {$declarations[d].display_name}</a>
                </div>:
                <div class="declaration_source_home_page">
                 <span class="medium gray">
                     {$declarations[d].time|date_format:"%d&nbsp;%b"}&nbsp;
                     {$declarations[d].time|date_format:"%Y"}
                 </span>
                 &nbsp;
                 <img src="images/popout_icon.gif" border="0"
                      width="12" height="12">&nbsp;

                 <span>
                   <a href="/?name={$declarations[d].name}&exp=person_declarations&decl_type=important">
                       vezi detalii</a>
                 </span>
               </div>
              </div>
            </div>
          {/section}
        </div>
      </div>


    </td>

    <td valign="top" width="250" style="padding-left:10px">
     <p class="smalltitle">
       <strong>
         Blog
       </strong>
     </p>
      {* ------------------------------------------------------------------*}
      {* Show the most recent blog posts and maybe the number of comments. *}
      {section name=b loop=$blogposts}
        <div class="small home_blog_post">
          <span class="gray">
            <em>{$blogposts[b].d|date_format:"%d&nbsp;%b"}</em>
          </span>
          <span class="green_link">
            <a href="/?cid=16&p={$blogposts[b].id}">
              {$blogposts[b].post_title}</a>
          </span>
          <span class="gray home_blog_post_comments">
            <img src="i/comments_icon.png" align="absmiddle" alt="Comments"
                 title="Comments">
            {$blogposts[b].comment_count}
          </span>
        </div>
      {/section}
      <span class="small">
        <em><a href="/?cid=16">vezi tot blogul...</a></em>
      </span>

    <div class="social_box">
      <div class="fb-like" style="margin-top: 5px;margin-bottom:10px;"
           data-href="http://hartapoliticii.ro"
           data-send="false" data-width="240" data-show-faces="false"
           data-action="like" data-font="verdana"></div>

      <!-- Place this tag where you want the +1 button to render -->
      <g:plusone annotation="inline" width="250" href="http://hartapoliticii.ro"></g:plusone>

      {literal}
      <!-- Place this render call where appropriate -->
      <script type="text/javascript">
        window.___gcfg = {lang: 'en'};

        (function() {
          var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
          po.src = 'https://apis.google.com/js/plusone.js';
          var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
        })();
      </script>
      {/literal}

      {literal}
      <div class="follow_on_twitter">
        <a href="https://twitter.com/hartapoliticii" class="twitter-follow-button" data-show-count="false">Follow @hartapoliticii</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
      </div>
      {/literal}
     </div>


      {* ------------------------------------------------------------------ *}
      {* The partners section *}

      <p class="smalltitle">
        <strong>
          Parteneri
        </strong>
      </p>

      <a href="http://www.votulmeu.com/" target="_blank">
        <img src="/images/parteneri-votulmeu.png"
             class="banner-partners"
             alt="Votul Meu">
      </a>

      <a href="http://www.fspub.unibuc.ro/" target="_blank">
        <img src="/images/parteneri-fbpub.jpg"
             class="banner-partners"
             alt="Facultatea de Științe Politice">
      </a>

      <a href="http://www.alegericorecte.ro" target="_blank">
        <img src="/images/parteneri-alegericorecte-2.jpg"
             class="banner-partners"
             alt="Coaliția Pentru Alegeri Corecte 2012">
      </a>

      <a href="http://www.activewatch.ro" target="_blank">
        <img src="/images/parteneri-activewatch.jpg"
             class="banner-partners"
             alt="Active Watch">
      </a>

      <a href="http://www.cineceapromis.ro" target="_blank">
        <img src="/images/parteneri-cineceapromis.jpg"
             class="banner-partners"
             alt="Cine Ce-a Promis">
      </a>

      {* ------------------------------------------------------------------ *}
      {* An explanatory text about this website. *}
      <p class="smalltitle">
        <strong>
          Despre acest site
        </strong>
      </p>
      <div class="small">
      <span itemprop="description">
      Cea mai mare colecție de date despre
      politicieni români care oferă cât mai mult context despre viața lor
      politică.</span>
      <p>
      Cu aceste date am tras concluzii utile cum ar fi câte voturi au
      contat la alegerile parlamentare sau simulatorul de alegeri
      europarlamentare.
      </div>

    </td>
  </tr>
</table>
