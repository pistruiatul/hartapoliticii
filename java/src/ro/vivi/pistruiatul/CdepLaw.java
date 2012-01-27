package ro.vivi.pistruiatul;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * Data model for holding everything that is related to a certain law.
 * This will be used to load data from the DB if needed, but more likely to dump
 * data in the database.
 *
 * @author vivi
 */
public class CdepLaw implements PageConsumer {
  static Logger log = Logger.getLogger("ro.vivi.pistruiatul.CdepLaw");

  public static String VOTE_URL = "/pls/steno/eVot.Nominal?uniqueId={IDV}";
  public static String LAW_URL = "/pls/proiecte/upl_pck.proiect?idp={IDP}";

  /** Extracts the party of a deputy from his vote line */
  Pattern partyPattern = Pattern.compile("<td align=\"center\">(.*)</td>");

  /** Extracts the deputy ID number and his name from the line with his name */
  Pattern linkDeputyPattern =
    Pattern.compile("<td><A HREF=\"/pls/parlam/structura\\.mp\\?idm=(\\d*)&" +
        "cam=(\\d)&leg=(\\d*)\">(.*)</A></td>");

  /** Id of the law */
  public int id;
  public String idp;
  public String link;

  /** This is the id of the vote for this law */
  public ArrayList<String> idvs;
  public ArrayList<Long> times;

  /** The type of the law can be Respingere, Aprobare */
  public ArrayList<String> types;

  /** The number of the law, in the form of NNN/YYYY */
  public String projectNumber;

  /** The time when this law was voted. */
  public String dateString;


  /** The description of the law, text */
  public String description;

  private static final int VOTES = 1;
  private static final int DETAILS = 2;
  private int nowCrawling = 0;

  private static final int APPROVED = 1;
  private static final int REJECTED = 2;
  private static final int ONGOING = 3;

  /**
   * A link to the container of cdepLaws that has this law. Kindof like if this
   * were a religious law, I want to know if it's from the Bible or The Coran.
   */
  private CdepLaws laws;

  private HashMap<Deputy, String> votes = new HashMap<Deputy, String>();

  /**
   * Constructor.
   * @param projectNumber The id of the vote, used to get to the link for that
   *     particular vote and fetch the list of deputies that voted for it.
   */
  public CdepLaw(String projectNumber, CdepLaws laws) {
    this.projectNumber = projectNumber;
    this.laws = laws;

    idvs = new ArrayList<String>();
    times = new ArrayList<Long>();
    types = new ArrayList<String>();
  }

  /**
   * Add a vote on this law project. We expect to be one vote per law, but only
   * crawling the data will tell us.
   * @param idv
   */
  public void addVote(String idv, String type, String datetime) {
    idvs.add(idv);
    times.add(new Long(getTimeFromVoteString(datetime)));
    types.add(type);
  }

  /**
   * Given a vote id, go to the site and load the votes on that particular
   * instance of a vote. If there are more votes on one given law, this should
   * be refactored to correctly account for all the votes. For now, we make the
   * assumption that there's one (relevant) vote per law.
   * @param idv
   */
  public void loadVotesFromSite(String idv) {
    nowCrawling = VOTES;
    if (!idv.equals(idvs.get(idvs.size() - 1))) {
      log.warning("CdepLaw " + projectNumber + " error, vote " + idv +
          " but registered was " + idvs.get(idvs.size() - 1));
      return;
    }

    String path = VOTE_URL.replace("{IDV}", idv);
    InternetsCrawler.enqueue(Main.HOST, path, this);
  }

  /**
   * Loads the page with details about this law so that we can parse more
   * details about a certain law.
   */
  public void loadDetailsFromSite() {
    nowCrawling = DETAILS;
    String path = LAW_URL.replace("{IDP}", idp);
    InternetsCrawler.enqueue(Main.HOST, path, this);
  }

  /**
   * Stub to redirect the parsing depending on the page that we are currently
   * fetching.
   */
  public void consume(String data) {
    switch (nowCrawling) {
      case VOTES:
        consumeVotesPage(data);
        break;
      case DETAILS:
        consumeDetailsPage(data);
        break;
    }
  }

  private Pattern initiators =
    Pattern.compile("<tr valign=top><td bgcolor=\"#fff0d8\">" +
        "Ini.iator:</td>(.*)");
  private Pattern deputy =
    Pattern.compile("(.*)/pls/parlam/structura\\.mp\\?idm=(\\d*)&" +
        "leg=(\\d*)&cam=(\\d)\">(.*)");

  private static final String GOV_INITIATED = "iator:</td><td>Guvern</td></tr>";

  private static final String STATE =
    "<tr valign=top><td bgcolor=\"#fff0d8\">Stadiu:</td><td>";
  Pattern isLawPattern =
    Pattern.compile("<A HREF=\"/pls/legis/legis_pck\\.htp_act\\?" +
        "nr=(\\d*)&an=(\\d*)\">Lege (\\d*)/(\\d*)</A>$");

  /**
   * Gets information from the details page.
   */
  private void consumeDetailsPage(String data) {
    String[] lines = data.split("\n");
    Deputies deputies = laws.getDeputies();

    int lawStatus = 0;
    int gov = 0;
    int i = 0;
    while (i < lines.length) {
      Matcher initMatcher = initiators.matcher(lines[i]);

      if (lines[i].endsWith(GOV_INITIATED)) {
        //log.info("CdepLaw " + projectNumber + ": Guvern ");
        gov++;

      } else if (initMatcher.matches()) {
        //<A HREF="/pls/parlam/structura.mp?idm=191&leg=2004&cam=2">
        //Munteanu&nbsp;Ioan</A>

        String[] dudeLines = lines[i].split("</A>");
        ArrayList<Proponent> props = new ArrayList<Proponent>();
        StringBuilder proponents = new StringBuilder();

        for (String dudeLine : dudeLines) {
          //log.info(dudeLine);
          Matcher d = deputy.matcher(dudeLine);
          Proponent p;
          if (d.matches()) {
            p = new Proponent();
            p.idm = Integer.parseInt(d.group(2));
            p.leg = Integer.parseInt(d.group(3));
            p.chamber = Integer.parseInt(d.group(4));

            props.add(p);
            proponents.append(p.idm + "/" + p.leg + "/" + p.chamber + " ");
          } else {
            if (!dudeLine.equals("</td></tr></table></td></tr>")) {
              log.warning(dudeLine);
            }
          }
        }

        for (Proponent p : props) {
          if (p.chamber == 2 && p.leg == 2008) {
            Deputy dep = deputies.getDeputyForIdm(p.idm);
            if (dep == null) {
              log.warning("Could not find " + p.idm + " idp=" + idp);
            } else {
              DbManager.insertLawProponent(id, dep.idm, p.chamber, props.size());
            }
          }
        }

        //log.info("CdepLaw " + projectNumber + " " + props.size() + " proponents: " +
        //   proponents);
      } else if (lines[i].equals(STATE)) {
        String reason = lines[i + 1];

        Matcher isLawMatcher = isLawPattern.matcher(reason);
        if (isLawMatcher.matches() ||
            reason.startsWith("lege trimis\u0103 la promulgare")) {
          lawStatus = APPROVED;
        } else if (reason.startsWith("procedur\u0103 legislativ\u0103 �ncetat\u0103") ||
            reason.startsWith("Sesizare de neconstitu")) {
          lawStatus = REJECTED;
        } else {
          lawStatus = ONGOING; // ongoing
          //log.info("CdepLaw " + projectNumber + " rejected like this: " +
          //    lines[i+1]);
        }
      } else if (lines[i].startsWith("<td class=\"headline\" width=\"100%\">")) {
        int pos = lines[i].indexOf("<br>") + 4;
        description = lines[i].substring(pos, lines[i].length() - 4);

        DbManager.updateCdepLawDescription(id, description);
      }
      i++;
    }
    DbManager.insertLawStatus(id, lawStatus);
  }

  // Simple inner class to use as a struct;
  class Proponent {
    int idm;
    int chamber;
    int leg;
  }

  /**
   * Parses the page with all the votes on this law/vote.
   * We will run in trouble here when we will want to parse more than just the
   * votes for a particular law. :-) We'll worry about that a little later.
   */
  private void consumeVotesPage(String data) {
    String[] lines = data.split("\n");
    Deputies deputies = laws.getDeputies();

    int i = 0;
    while (i < lines.length) {
      Matcher m = linkDeputyPattern.matcher(lines[i]);
      if (m.matches()) {
        // We have a deputy link, let's get him out of here.
        String idm = m.group(1);
        String name = m.group(4);

        Matcher pm = partyPattern.matcher(lines[++i]);
        String partyName = pm.matches() ? pm.group(1) : "Not matches";

        long time = times.get(times.size() - 1).longValue();
        Deputy dep = deputies.contains(name) ?
                     deputies.get(name) : new Deputy(name, idm);
        dep.setParty("foo", partyName, time);

        if (!deputies.contains(name)) {
          deputies.add(dep);
        }
        // Now that we have the deputy (from the list or not), let's get his
        // vote
        String vote = lines[i + 2];
        votes.put(dep, vote);

        String idv = idvs.get(idvs.size() - 1);
        DbManager.insertPersonVote(Main.YEAR, this.link, vote, this.id, time,
            dep);
      }
      i++;
    }
    log.info("CdepLaw " + this.projectNumber + " has " + votes.size());
  }

  /**
   * Sets the date and time of this date, from a string.
   */
  private long getTimeFromVoteString(String s) {
    try {
      SimpleDateFormat df = new SimpleDateFormat("dd.MM.yyyy hh:mm");
      return df.parse(s).getTime();
    } catch (ParseException pe) {
      pe.printStackTrace();
    }
    return 0;
  }

  /** Sets the description for this law. Usually, by the way it starts, we can
   * figure out extra information... i think.
   */
  public void setDescription(String desc) {
    description = desc.replace("'", "");
  }

  @Override
  public String toString() {
    return types.get(types.size() - 1) + " " + projectNumber + " " +
        description;
  }
}
