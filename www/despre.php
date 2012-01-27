<?
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  header('Location: http://www.vivi.ro/politica');
}


$title = "Despre acest site / Contact";
$nowarning = true;

include('header.php');
?>

<div class="plaintext">

<p>
  <strong>Contact</strong>:

  octavian.costache la gmail.com
</p>

<p>
  <strong>Un pic de context</strong>
</p>

<blockquote>
<p>
  Parlamentul nostru este bicameral, format din Senat și Camera Deputaților. Începând cu februarie 2006 pentru Camera Deputaților și (din câte am găsit eu) cu Septembrie 2007 pentru Senat, voturile în cele două camere sunt electronice și publice. <a href="http://www.senat.ro/votpublic/DetaliuVOT.aspx?AppID=a768e1ab-d3e7-480e-8583-73a65f2f9533">Așa arată votarea</a> unei legi la Senat, <a href="http://www.cdep.ro/pls/steno/eVot.Nominal?idv=319">așa arată</a> la Camera Deputaților.
</p><p>
  Pentru fiecare deputat există <a href="http://www.cdep.ro/pls/parlam/structura.mp?idm=229&amp;cam=2&amp;leg=2004">pagini cu detalii</a>: când a intrat în Parlament și când și-a dat demisia, câte minute în total a luat cuvântul și <a href="http://www.cdep.ro/pls/steno/steno.lista?idv=3185&amp;leg=2004&amp;idl=1">înregistrări video ale fiecărei luări de cuvânt</a>. Pentru Senat nu există încă înregistrări video, decât pentru ședințele comune ale celor două camere.
</p>
<p>
În Senat, între Septembrie 2007 și Iunie 2008, 623 din cele 703 de proiecte înregistrate în Senat au fost votate electronic, aproape 90%. Numărul de proiecte înregistrate se găsește în buletinul lagislativ, <a href="http://www.senat.ro/pagini/Proceduri%20parlamentare/Resurse%20parlamentare/buletin%20legislativ%202008/buletin%201/1.%20SintezaActivitatiiLegislativePeSesiune-detaliat.pdf">aici</a> și <a href="http://www.senat.ro/pagini/Proceduri%20parlamentare/Resurse%20parlamentare/buletin%20legislativ%202007/buletin3/1%20BULETIN%20LEGISLATIV%2012.12.2007.pdf">aici</a>. Voturile electronice se pot număra <a href="http://www.senat.ro/votpublic/default.aspx">aici</a>.

</p>

</blockquote>

<p>
  <strong>Ce am făcut cu datele astea</strong>
</p>

<blockquote><p>
  Adunând toate aceste informații am calculat prezența la vot pentru fiecare deputat și senator în perioada de când a început votul electronic. Dacă un senator nu apare în lista unui vot cu nici una din cele patru opțiuni (Da, Nu, Abținere sau NeVot) înseamnă că a fost absent. Apoi am ordonat această listă în funcție de prezența la vot.
</p>
<p>Nu am luat în calcul senatorii/deputații care au fost scuzați de la vot pentru că au fost miniștri sau în delegații, cifrele măsoară pur și simplu la ce procent de voturi a fost fiecare prezent.</p>

<p>
  Astfel de liste există deja <a href="http://projects.washingtonpost.com/congress/110/house/vote-missers/">pentru House Of Representatives din America</a>, analogul Camerei Deputaților de la noi, și pentru <a href="http://projects.washingtonpost.com/congress/110/senate/vote-missers/">Senatul American</a>. Ca să punem lucrurile în perspectivă am adunat datele din amandouă și am făcut un grafic.
</p><p>
  <a href="http://www.vivi.ro/politica/deputati2_ro_us_big.png"><img border=0 class="small_photo" src="http://www.vivi.ro/politica/deputati2_ro_us_small.png" alt="" /></a>
  <br><span style="font-size:0.7em">Un grafic ceva mai hardcore <a href="http://www.vivi.ro/politica/graph_big.png">îl găsiți aici</a>, pentru iubitorii de grafice.</span>
</p><p>
  În House Of Representatives sunt 447 deputați, în Camera Deputaților la noi sunt 321.
</p><p>
  90% din deputații americani au absenteismul sub 10%, în timp ce doar 15% de la noi sunt atât de conștiincioși. Doar 2 (0.5%) deputați americani au lipsit la mai mult de jumătate din voturi și asta <a href="http://projects.washingtonpost.com/congress/110/house/vote-missers/">pentru că au decedat</a>. La noi, <a href="http://www.vivi.ro/politica/index.php?sort=percent">70 de deputați (20%) au lipsit</a> la mai mult de jumătate din voturi.
</p>
</blockquote>

<p>
  <strong>Câteva observații</strong>
</p>
<blockquote><p>
  Știu că unii din oamenii de acolo sunt absenți de la vot pentru că au fost sau sunt în guvern. Din păcate nu am găsit o listă cu membri guvernului în timp ca să pot scoate voturile respective din calcul (și ca să pot semnaliza cine e sau a fost din guvern). Dacă cineva poate să mă ajute cu o astfel de listă ar fi excelent.
</p><p>
  Deasemenea, mulțumesc <a href="http://www.linkedin.com/in/irinadumitrascu">Irinei</a> că m-a ajutat să fac legătura între deputați și unde candidează, lui <a href="http://www.dorinboerescu.ro">Dorin</a> și <a href="http://www.orlando.ro">Orlando</a> că m-au ascultat în timp ce băteam câmpii despre ce vreau să fac. :-)
</p>
</blockquote>

<p>
  <strong>În loc de concluzie</strong>
</p>

<blockquote><p>
  Dacă aveți sugestii, comentarii, observații, greșeli de remarcat, lăsați un comentariu.
</p>
</blockquote>
<p>
  <strong>Vrei să susții proiectul</strong>
</p>
<blockquote>
<p>
  Găsesiți bannere <a href="http://www.vivi.ro/blog/?p=1099">aici</a>. Dacă vreți să integrați informațiile de aici în site-ul vostru, <a href="http://www.vivi.ro">contactați-mă</a>.
  </p>

  </blockquote>

  <p>
    <strong>Despre autorul acestui site</strong>
  </p>

  <blockquote>
  <p>
    Mă numesc Octavian Costache și sunt programator. Nu sunt implicat în politică, îmi place doar să fac statistici iar acest site este mai degrabă un hobby, nu o sursă autoritară de informații. <br><br>Mai multe informații găsiți la mine <a href="http://www.vivi.ro/blog/">pe blog</a>, sau la mine <a href="http://www.vivi.ro/">pe site</a>.
  </p>
</p></blockquote>
</div>
