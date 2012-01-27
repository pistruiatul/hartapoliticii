package ro.vivi.pistruiatul;

import java.text.SimpleDateFormat;
import java.util.Arrays;
import java.util.Date;
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
public class CdepNominalVote extends VotingSession implements PageConsumer {
  static Logger log = Logger.getLogger("ro.vivi.pistruiatul.VotingSession");

  public static String VOTE_URL = "/pls/steno/evot.nominal?uniqueId={IDV}&idl=1";
  public static String LAW_URL = "/pls/proiecte/upl_pck.proiect?idp={IDP}";

  /** Extracts the deputy ID number and his name from the line with his name */
  Pattern linkDeputyPattern = Pattern.compile(
      "<td><A HREF=\"/pls/parlam/structura\\.mp\\?idm=(\\d*)&" +
      "cam=(\\d)&leg=(\\d*)\">(.*)</A></td>");
  /** Extracts the party of a deputy from his vote line */
  Pattern partyPattern = Pattern.compile("<td align=\"center\">(.*)</td>");

  Pattern lawPattern = Pattern.compile(
      "<A HREF=\"/pls/proiecte/upl_pck\\.proiect\\?idp=([0-9]*)\" " +
      "TARGET=\"PROIECTE\">PL (.*)</A>");

  // <tr valign="top"><td align="right" bgcolor="#fffef2"
  // nowrap>Subiect vot:</td><td><b>
  String subjectLine =
      "<tr valign=\"top\"><td align=\"right\" bgcolor=\"#fffef2\" nowrap>" +
      "Subiect vot:</td><td><b>";

  private int[] errorVotes = {5093, 5097};

  private int[] miscVotes = {4887, 4898, 4915, 4985, 5046, 5056, 5065, 5098,
      5099, 5100, 5102, 5103, 5147, 5172, 5367, 5371, 5609, 5610, 5658};

  // Whether we think this vote is an error or not.
  public boolean error = false;

  private String year;
  
  /**
   * Constructor.
   * @param uniqueId The id of the vote, used to get to the link for that
   *     particular vote and fetch the list of deputies that voted for it.
   */
  public CdepNominalVote(long time, String uniqueId, String year) {
    this.time = time;
    this.uniqueId = uniqueId;
    this.year = year;
  }

  /**
   * Initiates the crawling of this particular vote.
   */
  public void run() {
    String path = VOTE_URL.replace("{IDV}", uniqueId);
    this.link = "http://" + Main.HOST + path;
    log.info("Fetching vote " + path);
    InternetsCrawler.enqueue(Main.HOST, path, this);
  }


  /**
   * Consumes the page that comes from the server. Mainly parses the votes of
   * the deputies, but also parses stuff like subject of the vote.
   */
  public void consume(String page) {
    String[] lines = page.split("\n");
    CdepLaw cdepLaw = null;

    int i = 0;

    log.info("Consuming " + VOTE_URL.replace("{IDV}", uniqueId));
    while (i < lines.length) {
      String line = lines[i].trim();

      Matcher lawMatcher = lawPattern.matcher(line);
      if (lawMatcher.matches()) {
        String idp = lawMatcher.group(1);
        String projectNumber = lawMatcher.group(2);

        cdepLaw = Main.cdepLaws.getLaw(projectNumber, idp);
        idLaw = cdepLaw.id;

        log.info("This vote belongs to cdepLaw " + idLaw);
      }

      Matcher m = linkDeputyPattern.matcher(line);
      if (m.matches()) {
        int idm = Integer.parseInt(m.group(1));
        String name = m.group(4);

        Matcher pmatch = partyPattern.matcher(lines[i + 1]);
        String party = pmatch.matches() ? pmatch.group(1) : "none";
        String vote = lines[i + 3].trim();

        Deputy dep = Main.deputies.getDeputyForIdm(idm);
        if (dep == null) {
          name = name.replace("-", " ");
          dep = new Deputy(name, "" + idm);
          Main.deputies.add(dep);
          dep.getInfoFromSite();
        }

        dep.setParty(year, party, time);
        dep.addVote(uniqueId, vote, time);

        DbManager.insertPersonVote(Main.YEAR, link, vote, cdepLaw.id,
            time, dep);
        addSingleVote(new SingleVote(time, uniqueId, vote, dep));
      }

      // Test to see if somehow maybe this is the subject of the vote.
      if (line.equals(subjectLine)) {
        // Concatenate the next lines up to "</td>"
        String desc = "";
        int j = 0;

        while (!lines[i + 1 + j].contains("</td>")) {
          desc += lines[i + 1 + j++] + " ";
        }
        // The next line will be the description.
        String separator = "<br>";
        if (!desc.contains(separator)) {
          separator = "- ";
        }

        if (desc.startsWith("Prezen")) {
          // This is a presence vote.
          type = desc;
          subject = "";

        } else if (desc.startsWith("Amendament")) {
          type = "Amendament";
          subject = desc;

        } else if (!desc.contains(separator)) {
          // This is in the type of "(type vote) Description vote.
          Pattern p = Pattern.compile("[(](.*)[)] (.*)");
          Matcher mdesc = p.matcher(desc);
          if (mdesc.matches()) {
            type = mdesc.group(1);
            subject = mdesc.group(2);
          } else {
            // We manually mark the erroneous votes since they are hard to
            // detect otherwise.
            if (Arrays.binarySearch(errorVotes,
                                    Integer.parseInt(uniqueId)) >= 0) {
              this.error = true;
              return;

            } else if (Arrays.binarySearch(miscVotes,
                                           Integer.parseInt(uniqueId)) >= 0) {
              type = "misc";
              subject = desc;
              // This vote is ok, don't do anything.
            } else {
              log.warning("==== I believe this is an eroneous vote!");
              // I think we should entirely ignore this vote.
              System.exit(-1);
            }
          }
        } else {
          String parts[] = desc.split(separator);
          type = parts[0].replace("</b>", "");
          subject = parts[1];
        }
        i++;
      }

      i++;
    }

    if (type == null || subject == null) {
      log.warning("-- This vote has no type or subject!");
    } else {
      log.info("Type:" + type + ", Subject:" + subject);
    }
    if (!error) {
      DbManager.insertNominalVote("cdep", "2008", this);
    }

    log.info("Votes for vote id " + uniqueId + ", " +
        getDateString(time) + " \n" +
        "+ " + votesAgg[0] + " DA\n" +
        "+ " + votesAgg[1] + " NU\n" +
        "+ " + votesAgg[2] + " Abtinere\n" +
        "+ " + votesAgg[3] + " -");
  }


  private String getDateString(long time) {
    Date d = new Date();
    d.setTime(time);
    return new SimpleDateFormat("yyyy-MM-dd").format(d);
  }
}
