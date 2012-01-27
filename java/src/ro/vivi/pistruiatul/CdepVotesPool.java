package ro.vivi.pistruiatul;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * Data model for the cdepLaws that were voted electronically.
 * @author vivi
 */
public class CdepVotesPool implements PageConsumer {
  Logger log = Logger.getLogger("ro.vivi.pistruiatul.CdepVotesPool");

  /** The url from which we will grab the daily votes. */
  public static String SEED_PATH =
    "/pls/steno/eVot.Data?dat={DATE}&cam=2&idl=1";

  /** Keeping track of the cdepLaws, indexed by the id of the law. */
  HashMap<String, VotingSession> votes = new HashMap<String, VotingSession>();

  /** The current date I am parsing votes for. */
  private Date currentDate;

  private int daysWithVotes;
  private int finalVotes;
  private int totalVotes;
  
  private String year;

  /**
   * Initializes some of the much needed data structures.
   */
  public CdepVotesPool() {
    // noop.
  }

  /**
   * Runs the parsing of the entire vote pool.
   */
  public void run(String year) {
    this.year = year;
  
    fetchVotesListFromSitePages();

    // Go through each vote and parse it (parse representatives and stuff)
    int count = 0;
    for (String idv : votes.keySet()) {
      count++;
      log.info("Parsing " + count + "/" + votes.keySet().size());
      CdepNominalVote nv = (CdepNominalVote) votes.get(idv);
      nv.run();
    }

    log.info(" + Got deputies: " + Main.deputies.deps.size());
    
    // For each deputy, print some stats. These stats should rather go to the
    // database.
    for (Deputy dep : Main.deputies.deps.values()) {
      log.info(dep.toString() + "\n" +
          dep.getVoteStatsString(votes.values()) + "\n" +
          dep.getTimeInOfficeString());
      dep.flushAggregateStatsToDb(votes.values(), year);
    }
  }

  /**
   * Fetches the list of cdepLaws from the site.
   */
  @SuppressWarnings("deprecation")
  public void fetchVotesListFromSitePages() {
    // Let's start with Nov 2008, even though we know that the first vote was on
    // Feb 04 2009.
    currentDate = new Date(108, 10, 30);
    Date now = new Date();

    // For testing, we might need to only parse a few days.
    int count = Integer.MAX_VALUE;

    while (now.getTime() > currentDate.getTime() && count-- > 0) {
      currentDate.setDate(currentDate.getDate() + 1);
      String path = SEED_PATH.replace("{DATE}", "" +
          getDateString(currentDate));

      log.info("Fetching page: " + path);
      InternetsCrawler.enqueue(Main.HOST, path, this);
    }
    log.info(" -- Done! " + daysWithVotes + " days with votes, " + finalVotes +
        " final votes, " + totalVotes + " total votes.");
  }

  private String getDateString(Date d) {
    return new SimpleDateFormat("yyyyMMdd").format(d);
  }

  /**
   * @inheritDoc
   */
  @SuppressWarnings("deprecation")
  public void consume(String page) {
    //<A HREF="/pls/steno/evot.nominal?uniqueId=5153&idl=1">deschis</A>
    Pattern dateAndTime = Pattern.compile("<A HREF=\"/pls/steno/evot\\." +
        "nominal\\?idv=([0-9]+)&idl=([0-9]+)\">(.*)</A>");

    // consume the page that came back.
    String[] lines = page.split("\n");
    int i = 0;

    boolean hasAny = false;

    while (i < lines.length) {
      String line = lines[i].trim();
      Matcher m = dateAndTime.matcher(line);
      if (m.matches()) {
        // We have a law, let's create an object
        String idv = m.group(1);
        String timeString = m.group(3);

        if (timeString.indexOf(":") >= 0) {
          String[] hm = timeString.split(":");
          currentDate.setHours(Integer.parseInt(hm[0]));
          currentDate.setMinutes(Integer.parseInt(hm[1]));
        } else {
          currentDate.setHours(0);
          currentDate.setMinutes(0);
        }
        long time = currentDate.getTime();

        // Now I might have a line with the type which can be Adoptare,
        // Respingere If it doesn't start with <A HREF
        // This changes in time, maybe we shouldn't rely on it?
        i += 6;
        String type = lines[i];

        if (type.startsWith("Vot final") || type.indexOf("vot final") > 0) {
          finalVotes++;
        }
        totalVotes++;

        // Create a new VotingSession object that will go ahead and parse the
        // information about the vote, who was there and who wasn't.
        CdepNominalVote nv = new CdepNominalVote(time, idv,  year);
        votes.put(idv, nv);

        log.info(" + vote parsed from list time=" + time + " uniqueId=" + idv +
            " type=" + type);
        hasAny = true;
      }
      i++;
    }
    if (hasAny) {
      daysWithVotes++;
    }
  }

  /**
   * Returns a reference to the pool of deputies.
   */
  public Deputies getDeputies() {
    return Main.deputies;
  }

  public HashMap<String, VotingSession> getVotes() {
    return votes;
  }
}
