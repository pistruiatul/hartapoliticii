package ro.vivi.pistruiatul;

import java.util.Date;
import java.util.HashMap;
import java.util.logging.Logger;

/**
 * Manages and parses the candidates for the Euro2009 parliamentary elections.
 * @author vivi
 */
public class EuroCandidates2009 implements PageConsumer {
  Logger log = Logger.getLogger("ro.vivi.pistruiatul.EuroCandidates2009");
  /**
   * A hash map with the url's where we find the candidates. The keys will be
   * for now hardcoded in the constructor, so this will break if the parties
   * table ever changes.
   */
  public int[] parties = {1, 2, 7, 14, 39, 6, 10, 40};

  public int currentParty;

  public int globalCount = 1;

  /**
   * Initializes the url's for the parties that have candidates in this race.
   */
  public EuroCandidates2009() {
  }

  /**
   * Crawls the pages with the candidates.
   */
  public void run() {
    DbManager.deleteEuro2009();
    // Send the URL's to be consumed.
    for (int value : parties) {
      currentParty = value;
      this.consume(InternetsCrawler.getUtfStringFromDisk("eurocandidati2009/" +
          value + ".txt"));
    }
  }

  /**
   * Consumes the page returned by the InternetsCrawler.
   */
  public void consume(String page) {
    String[] lines = page.split("\n");

    for (int i = 0; i < (lines.length + 1) / 5; i++) {
      String name = lines[i * 5];
      String bday = lines[i * 5 + 1];
      String profession = lines[i * 5 + 2];
      String occupation = lines[i * 5 + 3];

      long time = getTimeFromString(bday);

      Person p = new Person();
      p.name = name;
      p.facts.put("birthday", "" + time);
      p.facts.put("occupation", occupation);
      p.facts.put("profession", profession);
      p.facts.put("idparty", "" + currentParty);
      p.facts.put("position", "" + (i + 1));
      //log.info("" + p);
      //http://www.bec2009pe.ro/Documente%20PDF/Declaratii%20de%20avere%20si%20interese-Forta%20Civica/VLAD%20LIVIU%20MARIN.pdf
      //http://www.bec2009pe.ro/Documente%20PDF/Declaratii%20de%20avere%20si%20interese-Forta%20Civica/VLAD%20LIVIU%20MARIAN.pdf
      String cleanName = name.replace("\u0102", "A")
          .replace("\u00C2", "A").replace("\u0162", "T").replace("\u015E", "S")
          .replace("\u0150", "O").replace("\u00C9", "E").replace("\u00C1", "A")
          .replace("\u00D3", "O").replace("\u00D6", "O").replace("-", " ")
          .replace("  ", " ");

      String party = Parties.getPartyName(currentParty);
      if (!party.equals("PNTCD") && !party.equals("PSD")) {
        party = party.equals("FC") ? "Forta Civica" : "candidati " + party;
      }
      String url = "http://www.bec2009pe.ro/Documente PDF/Declaratii de " +
      "avere si interese-" + party + "/" + cleanName + ".pdf";
      url = url.replace(" ", "%20");
      String fn = globalCount++ + ".pdf";
      System.out.println("if [ ! -f " + fn + " ] ; then curl -o " + fn + ".pdf " + url + " ; fi");

      p.id = DbManager.insertEuroCandidate2009(p);
    }
  }

  /**
   * Given a date written like dd.mm.yyyy, return the time that it represents.
   * @param bday
   * @return
   */
  public long getTimeFromString(String bday) {
    String[] n = bday.split("\\.");
    return new Date(Integer.parseInt(n[2]) - 1900,
                    Integer.parseInt(n[1]) - 1,
                    Integer.parseInt(n[0])).getTime();
  }
}

