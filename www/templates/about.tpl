
<div class="stats" style="width:300px; float:right">
  <div class="plaintext" style="width:300px">

  <p>
    <strong>Câteva statistici</strong>
  </p>

  Monitorizăm <b>{$total_people}</b> politicieni.
  <br>
  <b>{$count_users}</b> utilizatori înregistrați.
  <br>
  <b>{$follow_users}</b> utilizatori urmăresc <b>{$following_people}</b> politicieni.

  <br><br>
    <b>Căutări recente</b><br>
    {section name=i loop=$recent_searches}
      <span class="small gray">
      {$recent_searches[i].time|date_format:"%b %d %l%p"|replace:"PM":"pm"|replace:"AM":"am"}
      </span>
      <span class="small">
      - <a href="?cid=search&q={$recent_searches[i].query|htmlspecialchars}">{$recent_searches[i].query|htmlspecialchars}</a></span><br>

    {/section}

  </div>
</div>

<div class="plaintext" style="width:650px">

<p>
  <strong>Ce face de fapt site-ul ăsta?</strong>
</p>
<blockquote>
  <p>
    Harta este un punct de referință unde cât mai multe informații
    despre fiecare persoană politică sunt adunate într-un singur loc. Misiunea
    hărții pe lung <a href="https://github.com/pistruiatul/hartapoliticii/wiki/Misiunea-h%C4%83r%C8%9Bii-politicii">o găsiți aici</a>.
  </p>

  <p>
    Proiectul este <a href="https://github.com/pistruiatul/hartapoliticii">open
    source</a>, independent și obiectiv.
    Oricine poate participa activ la a construi noi facilități.
  </p>

  <p>
    Pentru fiecare politician poți vedea cum a votat în parlament
    (pe issues, agregat, cu score-cards), ce funcții a deținut, dacă și unde a
    candidat, mențiuni în mass-media, apariții în liste de
    candidați pătați, declarații de-a lungul timpului.
  </p>

  <p>
    Toate aceste date sunt colectate automat.
  </p>

  <p>
    Un exemplu bun este pagina lui
    <a href="http://hartapoliticii.ro/?name=mustea+razvan+serban">
      Mustea Răzvan Șerban</a>.
  </p>

  <p>
    Pe
    <a href="https://groups.google.com/d/forum/hartapoliticii-discuss">grupul
     de discuții</a> despre proiect se comentează ce se mai întâmplă
    și se postează anunțuri cu ce mai e nou. Înscrie-te!
  </p>
</blockquote>


<p>
  <strong>Un pic despre trecut și despre viitor</strong>
</p>

<blockquote>
  <p>
    De-alungul timpului am adăugat, printre altele
  </p>
  <ul>
    <li><a href="http://www.hartapoliticii.ro/?p=119">date despre alegerile
      europarlamentare</a>,
    <li><a href="http://www.hartapoliticii.ro/?p=3755">monitorizarea automată a
      presei</a>
    <li><a href="http://www.hartapoliticii.ro/?p=3774">extragere de fapte din
      ziare</a>
    <li><a href="http://www.hartapoliticii.ro/?p=4042">un sistem pentru alcătuirea
      de score-cards</a> de către utilizatori
    <li>am calculat <a href="http://www.hartapoliticii.ro/?p=18">câte voturi
      au contat</a> la
    alegeri
    <li>și cine anume
    <a href="http://www.hartapoliticii.ro/?p=22">a fost reales din parlamentul
      anterior</a>
  </ul>

  <p>
    Dacă te interesează cam ce plănuiește harta politicii să implementeze în
    viitorul mediu, <a href="https://github.com/pistruiatul/hartapoliticii/wiki/Idei-concrete-care-trebuie-implementate">uite aici o listă</a>.
    Dacă ai alte păreri, comunică-le și vor fi ascultate.

  </p>
</blockquote>

<p>
  <strong>Despre istoria site-ului</strong>
</p>

<blockquote>
  <p>
    Mă numesc <a href="http://vivi.ro/">Octavian Costache</a>, sunt fondatorul
    acestui proiect și nu am afilieri politice.
    Am început harta politicii în 2008 pentru că îmi doresc să
    schimb România în mai bine.
  </p>

  <p>
    După ce proiectul a devenit open source în Martie 2012, au început să
    contribuie activ și <a href="https://github.com/pistruiatul/hartapoliticii/blob/master/AUTHORS">alți dezvoltatori</a>.
  </p>
</blockquote>

<p>
  <strong>Contact</strong>
</p>

<blockquote>
  <p>
    Pentru orice fel de întrebări despre cum se folosește harta, sugestii,
    feedback <b>octavian.costache [at] gmail.com</b>,
    sau grupul de discuții <a href="https://groups.google.com/d/forum/hartapoliticii-discuss">hartapoliticii-discuss</a>.

  </p>
</blockquote>


</div>
