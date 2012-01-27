package ro.vivi.pistruiatul;

import java.util.Collection;
import java.util.Date;
import java.util.HashMap;
import java.util.HashSet;
import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * Mostly a data structure with information about a deputy. This will be used
 * as a data model, more or less an image of what is in the database.
 *
 * @author vivi
 */
public class Deputy implements PageConsumer {
  static Logger log = Logger.getLogger("ro.vivi.pistruiatul.Deputy");

  Pattern datePattern =
    Pattern.compile("([\\d]*)([ ]*)([\\w]*)([\\s]*)([\\d]*)");

  String[] months = {"ianuarie", "februarie", "martie", "aprilie", "mai",
                     "iunie", "iulie", "august", "septembrie", "octombrie",
                     "noiembrie", "decembrie"};

  Pattern moreInfoPattern =
    Pattern.compile("(.*)<br>data validarii: (.*)( - " +
        "<a href=([^>]*)>([^>])*</a>)?" +
        "(<br>data �ncetarii mandatului:([^>]*) - (\\w*))?" +
        "(<br>�nlocuit de: <([^>]*)><b>([\\w ]*)</b></A>)?" +
        "(<br>�nlocuieste pe: <([^>]*)><b>([\\w ]*)</b></A>)?" +
        "</td>");
  // data validarii: 17 februarie  2004 -
  // <a href="/pls/legis/legis_pck.htp_act?ida=54223" target="LEGIS">
  // HS nr.57/2004</a>
  Pattern dataValidariiP = Pattern.compile("data validarii: ([^>-]*)( - " +
      "(<a href=([^>]*)>)?([^>])*(</a>)?)?(</td>)?");
  Pattern dataIncetariiP = Pattern.compile("data �ncetarii mandatului:([^>]*)" +
      " - (\\w*)( - [^-]*)?(</td>)?");
  Pattern inlocuitDeP =
    Pattern.compile("�nlocuit de: <([^>]*)><b>([\\w ]*)</b></A>(</td>)?");
  Pattern inlocuiesteP =
    Pattern.compile("�nlocuieste pe: <([^>]*)><b>([\\w ]*)</b></A>(</td>)?");

  /** Fetches the url for talking time and list of video footage. */
  Pattern talkTimeInfo =
    Pattern.compile("(.*)ri de cuv�nt �n plen: </td><td>" +
        "<A HREF=\"(/pls/steno/steno.lista\\?idv=(\\d*)&leg=2004&" +
        "idl=(\\d*))\"><b>(\\d*)</b>(.*)");

  protected static String DEP_URL =
      "pls/parlam/structura.mp?idm={IDM}&cam=2&leg=2008";

  /** The deputy's name */
  public String name;

  /** The deputy id's in the cdep.ro site. */
  public String cdepId;
  public int idm;

   /**
   * The id of the senator in the senators table. This is not the person
   * id. If we ever need the person ID, we just get it straight from the
   * database through a helper method.
   */
  public String id;

  /** The id of the most recent party this candidate belongs to. */
  public int mostRecentPartyId;

  /** The time of the most recent party belonging data. */
  private long mostRecentPartyTime = 0;

  /**
   * We make an object for each video list of each candidate. This makes it
   * easier to have that be a PageConsumer.
   */
  private Video video;

  /**
   * When his time in office started. This is usually election day, but for
   * people that quite or died or whatever, new people replace them and they
   * have a new starting date.
   */
  public long startTime;

  /** When his time in office ended. */
  public long endTime;

  /** The number of votes that this candidate could have voted on. */
  public int possibleVotes;

  /** The number of days in which there were any types of votes. */
  public int possibleDays;

  /**
   * The person id of this deputy. This field is initialized at construction
   * time if it's available, or else at the time of insertion in the database.
   */
  public int idperson;

  /**
   * Stores the votes of this deputy during his term. On this data model we will
   * be able to compute his stats about presence.
   *
   * NOTE: we should also be able to use this for the more complicated
   * statistics, in which we align his votes with other people's votes. So we
   * should make this data structure flexible enough.
   */
  private HashMap<String, SingleVote> myVotes = new HashMap<String, SingleVote>();

  private HashMap<SingleVote.Type, Integer> votesAgg =
    new HashMap<SingleVote.Type, Integer>();

  private int partyLineVotes = 0;
  private int maverickVotes = 0;

  /** Keeps a record of the days this candiate went to work. */
  private HashSet<Date> workDays = new HashSet<Date>();

  /**
   * Party belonging over time. Needed to quickly identify what party
   * a person belonged to at the time of a voting session.
   */
  private HashMap<Long, Integer> party = new HashMap<Long, Integer>();

  /**
   * Constructs a deputy and initializes some default values. The method also
   * looks in the database to get this deputy's person id.
   * @param name The name of this deputy.
   * @param cdepId The id of the deputy on the cdep site, not on our own site.
   */
  @SuppressWarnings("deprecation")
  public Deputy(String name, String cdepId) {
    this.name = name.trim();
    this.cdepId = cdepId.trim();
    this.idm = Integer.parseInt(cdepId);
    this.video = new Video(this);

    this.idperson = getIdPersonFromDb();

    for (SingleVote.Type v : SingleVote.Type.values()) {
      votesAgg.put(v, 0);
    }

    // The start time value should be overridden with the value from the
    // database.
    this.startTime = new Date(108, 8, 20).getTime();

    // The end time will only be overridden if it's present in the database.
    this.endTime = new Date().getTime();
  }

  /**
   * Returns the room this person belongs to. Senator will override this method
   * and return a different room.
   * @return The string with the room this belongs to.
   */
  public String getRoom() {
    return "cdep";
  }

  /**
   * Override this to get whatever id person you need. This needs to be cleaned
   * and refactored.
   * @return The person id of this deputy.
   */
  public int getIdPersonFromDb() {
    return DbManager.getIdPersonForDeputy("2008", this);
  }

  /**
   * Sets the party for this deputy at a given point in time;
   * @param year The year we are working on.
   * @param name The name of the party.
   * @param time The exact time when this deputy belonged to this party.
   */
  public void setParty(String year, String name, long time) {
    int partyId = Parties.getPartyId(name);
    if (mostRecentPartyTime < time) {
      mostRecentPartyTime = time;
      mostRecentPartyId = partyId;
    }
    party.put(time, partyId);

    DbManager.insertPartyBelonging(getRoom(), year, id, idperson, partyId,
        time);
  }

  /**
   * Records a vote and adds it to our data model.
   * @param idv
   * @param voteStr
   * @param time
   */
  @SuppressWarnings("deprecation")
  public void addVote(String idv, String voteStr, long time) {
    SingleVote vote = new SingleVote(time, idv, voteStr, this);
    myVotes.put(idv, vote);

    // Add this day to the days this candidate was in to vote.
    Date d = new Date();
    d.setTime(time);
    d.setHours(1);
    d.setMinutes(0);
    d.setSeconds(0);
    workDays.add(d);

    votesAgg.put(vote.type, votesAgg.get(vote.type) + 1);
  }

  /**
   * Returns a string with this candidate's votes.
   * @param sessions The set with the sessions.
   * @return A string to print at the console.
   */
  public String getVoteStatsString(Collection<VotingSession> sessions) {
    StringBuilder sb = new StringBuilder();
    for (SingleVote.Type v : SingleVote.Type.values()) {
      sb.append(v)
        .append(":")
        .append(votesAgg.get(v).intValue())
        .append(" - ");
    }

    computePossibleVotesAndDays(sessions);

    double percent = possibleVotes > 0 ?
        Math.round(10000 * myVotes.size() / possibleVotes) / 100.0 :
        0;
    sb.append("\n")
      .append(myVotes.size())
      .append(" out of ")
      .append(possibleVotes)
      .append(", ")
      .append(workDays.size())
      .append(" days out of ")
      .append(possibleDays)
      .append("\n")
      .append(percent)
      .append(" %")
      .append("\n")
      .append(getTimeInOfficeString());

    return sb.toString();
  }

  /**
   * Get the number of votes of a certain type from this deputy.
   * @param type The type of vote we need a count for.
   * @return The number of votes of this type.
   */
  public int getVotes(SingleVote.Type type) {
    return votesAgg.get(type);
  }

  public int getWorkDays() {
    return workDays.size();
  }

  public int getPossibleDays() {
    return possibleDays;
  }

  public int getPossibleVotes() {
    return possibleVotes;
  }

  public float getVotesPercent() {
    if (getPossibleVotes() == 0) {
      return 1;
    }
    return (float)myVotes.size() / (float)getPossibleVotes();
  }

  /**
   * Writes aggregate voting statistics about this candidate to the database.
   * @param sessions The entire set of voting sessions.
   * @param year The year for which we are doing this.
   */
  public void flushAggregateStatsToDb(Collection<VotingSession> sessions,
                                      String year) {
    computePossibleVotesAndDays(sessions);
    computePartyAlignment(sessions, year);

    DbManager.insertAggregateDeputyStats(getRoom(), this, year);
    DbManager.insertCurrentBelonging(getRoom(), year, this);
  }

  /**
   * Computes how often this particular person votes along party lines.
   * @param sessions The set of voting sessions.
   * @param year The year for which we are working.
   */
  private void computePartyAlignment(Collection<VotingSession> sessions,
                                     String year) {
    for (VotingSession session : sessions) {
      // First, see if I voted in this session.
      for (SingleVote vote : session.votes) {
        if (vote.deputy == this) {
          // Now I know that I voted in this voting session. Next step, see
          // what party I belonged to and see if this was a party vote or not.
          // I should have the party vote precomputed.
          Integer partyId = party.get(vote.time);

          // There are some votes where the party is just not specified on the
          // senate website, for example, like this:
          // http://www.senat.ro/VoturiPlenDetaliu.aspx?AppID=bf4a0652-c926-4ef3-a1d5-5f9048ad6425
          // For these we just skip.
          if (partyId == null) {
            continue;
          }
          // If this vote was really weak for the party, just ignore it.
          int count = session.getNumVotesForParty(partyId);
          if (count <= 4 || (partyId == 7 && count <= 3)) {
            continue;
          }

          SingleVote.Type partyVote = session.getPartyVote(partyId);
          // If the partyVote is MI than we consider that this was not a clear
          // vote along party lines.
          if (partyVote != SingleVote.Type.MI) {
            addPartyLineVote(vote.type == partyVote);
            if (vote.type != partyVote) {
              DbManager.updateVoteIsMaverick(this, session, vote, year);
            }
          }
        }
      }
    }
  }

  private void addPartyLineVote(boolean isPartyLine) {
    if (isPartyLine) {
      partyLineVotes++;
    } else {
      maverickVotes++;
    }
  }

  public double getMaverickPercent() {
    return 1.0 * maverickVotes / (maverickVotes + partyLineVotes);
  }

  /**
   * Returns the id of the party this person belonged to at the time passed in
   * as a parameter. The method currently expects that we have exact information
   * about this exact time, does not do intervals.
   * @param time The time at which we want to know the party belonging.
   * @return The id of the party, -1 if no party was found.
   */
  public int getPartyId(long time) {
    if (party.containsKey(time)) {
      return party.get(time);
    }

    // HACK
    // because senat.ro does not list a party for this guy in the voting pages.
    if (Utils.replaceDiacritics(this.name).equals("Badescu Iulian")) {
      return 4;
    }

    // Looks like all the independents are no longer marked in senat.ro
    if (Utils.replaceDiacritics(this.name).equals("Campanu Liviu") ||
        Utils.replaceDiacritics(this.name).equals("Magureanu Cezar Mircea")||
        Utils.replaceDiacritics(this.name).equals("Marcutianu Ovidius")||
        Utils.replaceDiacritics(this.name).equals("Iordanescu Anghel")||
        Utils.replaceDiacritics(this.name).equals("Chivu Sorin Serioja") ||
        Utils.replaceDiacritics(this.name).equals("Ion Vasile")) {
      return 10;
    }

    // Fuck it, let's consider all that are missing votes at this point
    // as being Independents.
    return 10;
  }

  /**
   * Computes the number of votes that this candidate could have been part of.
   * @param sessions The set of voting sessions.
   */
  @SuppressWarnings("deprecation")
  private void computePossibleVotesAndDays(Collection<VotingSession> sessions) {
    HashSet<Date> days = new HashSet<Date>();
    possibleVotes = 0;

    for (VotingSession session : sessions) {
      if (session.time >= startTime &&
          session.time <= endTime &&
          !session.error) {
        possibleVotes++;
      }

      Date d = new Date(session.time);
      d.setHours(1);
      d.setMinutes(0);
      d.setSeconds(0);

      days.add(d);
    }
    possibleDays = days.size();
  }

  /**
   * Debug method for printing stuff to the console.
   * @return The string to be printed.
   */
  public String getTimeInOfficeString() {
    return " -- " + startTime + " - " + endTime;
  }

  /**
   * A to string method for debugging purposes
   */
  @Override
  public String toString() {
    return cdepId + ", " + name;
  }

  /**
   * Get more information about this deputy from the website.
   * TODO(vivi): Delete this method.
   */
  public void getInfoFromSite() {
    String path = getPath();
    InternetsCrawler.enqueue(Main.HOST, path, this);
  }

  /**
   * Get the path we need to crawl to get more info about the deputy.
   * @return The path.
   */
  protected String getPath() {
    return DEP_URL.replace("{IDM}", cdepId);
  }

  /**
   * Consume a page with info about mister deputy. This is the ministry page
   * of this particular deputy.
   * @param data The crawled page from cdep.ro.
   */
  public void consume(String data) {
    String[] lines = data.split("\n");

    int i = 0;
    while (i < lines.length) {
      // TODO(vivi): Uncomment this so we can make percentages.
      maybeParseDatesInfo(lines[i]);

      // TODO(vivi): Uncomment this so we can have more data. :-)
      // maybeParseTalkTimeInfo(lines[i]);

      maybeParseImageUrl(lines[i]);
      i++;
    }
  }

  private Pattern imagePattern = Pattern.compile("(.*)href=\"(/parlamentari/" +
      "l2008/mari/([-\\w]*)\\.jpg)\"(.*)");
  private Pattern smallImagePattern = Pattern.compile("(.*)src=\"(" +
      "/parlamentari/l2008/([-\\w]*)\\.jpg)\" border(.*)");

  /**
   * Checks if this line is the url to the image of this deputy
   * on the cdep site.
   * @param line
   */
  private void maybeParseImageUrl(String line) {
    Matcher m = imagePattern.matcher(line);
    if (m.matches()) {
      //log.info(m.group(2));
      updateImage(m.group(2));

    } else if (line.indexOf("/parlamentari/l2008") > -1) {
      m = smallImagePattern.matcher(line);
      if (m.matches()) {
        updateImage(m.group(2));
      } else {
        log.info(line);
      }
    }
  }

  public void updateImage(String url) {
    DbManager.updateDeputyImage(this, url);
  }

  /**
   * Tries to see if this line contains info about the talk time and the number
   * of points this candidate talked about. We are interested in only the link
   * since the info we are really interested in is there (like total talk time)
   * @param line
   */
  private void maybeParseTalkTimeInfo(String line) {
    Matcher m = talkTimeInfo.matcher(line);
    if (m.matches()) {
      String idv = m.group(3);

      video.setIdv(idv);
      video.crawlInfoFromSite();
    }
  }

  /**
   * Parses a line that might contain info about this deputies time in the
   * parlament, whom he replaced, whom replaces him and why he quit.
   * @param line
   */
  private void maybeParseDatesInfo(String line) {
    String startDate = "";
    String leavingDate = "";
    String motif = "";
    String replacedBy = "";
    String insteadOf = "";

    Matcher m = moreInfoPattern.matcher(line);

    if (m.matches()) {
      String[] parts = line.split("<br>");
      //log.info(lines[i]);

      for (int j = 0; j < parts.length; j++) {
        Matcher x = dataValidariiP.matcher(parts[j]);
        if (x.matches()) {
          startDate = x.group(1);
          //log.info("  start: " + startDate);
        } else if (parts[j].indexOf("data validarii") > -1) {
          log.warning("Should have matched: " + parts[j]);
        }
        x = dataIncetariiP.matcher(parts[j]);
        if (x.matches()) {
          leavingDate = x.group(1);
          motif = x.group(2);
        }
        x = inlocuitDeP.matcher(parts[j]);
        if (x.matches()) {
          replacedBy = x.group(1);
        }
        x = inlocuiesteP.matcher(parts[j]);
        if (x.matches()) {
          insteadOf = x.group(1);
        }
      }

      if (dateToTime(startDate) == 0) {
        log.info(toString() + " start: " + startDate + " / " + leavingDate
            + line);
      }
      updateDateTime(dateToTime(startDate), dateToTime(leavingDate), motif,
          idm);

    } else {
      if (line.indexOf("data �ncetarii mandatului:") > -1) {
        log.warning("  Line did not match reg exp but it should, " +
            "guy not here anymore");
        log.warning("  orig line: " + line);
      }
    }
  }

  public void updateDateTime(long start, long end, String motif, int id) {
    startTime = start;
    endTime = end;
    DbManager.updateDeputy(start, end, motif, id);
  }

  /**
   * Helper method for transforming a date into a time value.
   * @param d The date object.
   * @return The time value.
   */
  @SuppressWarnings("deprecation")
  private long dateToTime(String d) {
    // 12 decembrie 2004
    Matcher m = datePattern.matcher(d.trim());
    if (!m.matches() || d == "") {
      return 0;
    }
    int day = Integer.parseInt(m.group(1));

    int month = 0;
    for (int i = 0; i < months.length; i++) {
      if (months[i].equals(m.group(3))) {
        month = i;
      }
    }

    int year = Integer.parseInt(m.group(5));

    Date date = new Date(year - 1900, month, day);
    return date.getTime();
  }
}

