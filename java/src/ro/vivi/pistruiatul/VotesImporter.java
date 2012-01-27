package ro.vivi.pistruiatul;

import java.io.*;
import java.nio.charset.Charset;
import java.util.Collection;
import java.util.Date;
import java.util.HashMap;
import java.util.HashSet;
import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class VotesImporter {
  Logger log = Logger.getLogger("ro.vivi.pistruiatul.VotesImporter");

  /**
   * The location of the input file from which we will be reading data.
   */
  String inputFile;

  /**
   * The VotesImporter is mostly a static method that parses the aggregate
   * file of data and puts all the info in the database.
   */
  public VotesImporter(String inputFile) {
    this.inputFile = inputFile;
  }

  /**
   * The method that does the job. Goes through the agg file and puts everything
   * in the data base. This method should be as simple as possible (we're trying
   * out the idea of having a pipeline style data acquisition where most steps
   * are simple).
   * @param room The room for which we are doing this run. Used to figure out
   *     what is the set of participants (deputies or senators) and where
   *     we are going to store all the data we get from here.
   * @param year The year for which we are doing the parsing.
   */
  public void run(int room, String year) throws IOException {
    // Load the existing senators and deputies from the database. These two
    // objects should be unified to be a Forum of sorts, PeoplePool or
    // something like that, that takes room and year as parameters and returns
    // the right set of people.
    Senators senators = new Senators(year);
    Deputies deputies = new Deputies(year);

    HashSet<VotingSession> votingSessions = new HashSet<VotingSession>();

    // A new vote object is created whenever a name is found, and added to the
    // voting session when the v_vote tag is found. Anything in between can be
    // considered information about the vote. For now, only the party of the
    // candidate is in between.
    SingleVote vote = null;

    // The deputy reference is used and reused to create a senator or a deputy.
    // We use "Deputy" only because this is the base class, it should probably
    // be something more generic, like Participant.
    Deputy deputy = null;

    // A reference to the current law.
    GenericLaw law = new GenericLaw();

    // A reference to the voting session. This object is replaces when the
    // vote_end tag is encountered.
    VotingSession votingSession = new VotingSession();

    // Have the name of the room on which we are working.
    String roomName = room == Main.SENAT ? "senat" : "cdep";

    BufferedReader br = new BufferedReader(
        new InputStreamReader(new FileInputStream(inputFile), "UTF-8"));
    String line;

    int lawCount = 1;

    while ((line = br.readLine()) != null) {
      if (line.startsWith("vote_link:")) {
        votingSession.link = getLineContent(line);
        votingSession.uniqueId = getUniqueIdFromLink(votingSession.link, room);

      } else if (line.startsWith("vote_time:")) {
        votingSession.time = getTimeForVote(getLineContent(line));

      } else if (line.startsWith("vote_type:")) {
        votingSession.type = getLineContent(line);
        if (votingSession.type.length() > 250) {
          votingSession.type = votingSession.type.substring(0, 250);
        }

      } else if (line.startsWith("law_link:")) {
        law.link = getLineContent(line);

      } else if (line.startsWith("law_desc:")) {
        law.title = getLineContent(line);
        votingSession.subject = law.title;

      } else if (line.startsWith("law_num:")) {
        law.number = getLineContent(line);

      } else if (line.startsWith("v_name:")) {
        // Contains the name of the participant in this voting session. This is
        // the first line of a single vote, so we get a reference to the
        // participant and create a single vote object.
        String name = getLineContent(line);
        if (name.indexOf('-') > 0 || name.indexOf('.') > 0) {
          name = cleanNameHack(name);
        }
        // Try to find the name in the senators table. If the name is not there,
        // crash with a bang. Adding people and names to the database should be
        // done in a separate step of the pipeline.
        if (room == Main.CDEP) {
          deputy = deputies.get(name);
        } else if (room == Main.SENAT) {
          deputy = senators.get(name);
        }
        if (deputy == null) {
          log.warning("FATAL: Could not find senator " + name);
          System.exit(1);
        }
        vote = new SingleVote();
        vote.deputy = deputy;

      } else if (line.startsWith("v_party")) {
        // This line contains the name of the party to which this participant
        // belongs to at the time of the vote. This is a good way of identifying
        // when participants switch parties in midstream.
        if (deputy == null) {
          log.severe("FATAL: Got party, but no participant name.");
          System.exit(1);
        }
        String party = getLineContent(line);
        if (!party.isEmpty()) {
          deputy.setParty(year, party, votingSession.time);
        }

      } else if (line.startsWith("v_vote")) {
        // Contains the actual content of the vote. This also signals that we
        // have all the necessary information about the single vote, so we can
        // go ahead and add it to our data models.
        if (vote == null) {
          log.severe("FATAL: Got vote content, but no participant name.");
          System.exit(1);
        }
        vote.time = votingSession.time;
        String value = getLineContent(line);
        vote.type = SingleVote.getTypeFromString(value);
        votingSession.addSingleVote(vote);

        // v_name:Mocanu Toader
        // v_party:PD-L
        // v_vote:DA
        deputy.addVote(votingSession.uniqueId, value, votingSession.time);

        // We used this vote, it's time to move on to the next one.
        vote = null;

      } else if (line.startsWith("vote_end:")) {
        // Write the law to the database and get back and ID. Use that id for
        // writing the votes in the database.
        log.info(votingSession.uniqueId + " " +
            votingSession.time + " " +
            votingSession.type + " " +
            votingSession.size());

        // HACK: we should expect this to be set in the file, but for the Senate
        // we currently don't do that, so we just extract it from the link.
        if (law.number == null) {
          law.number = getLawNumberFromLink(law.link);
        }

        int lawId = -1;
        if (!law.number.equals("None")) {
          lawId = DbManager.insertLaw(room, year, law.link, law.number,
              law.title);
        }
        votingSession.idLaw = lawId;

        log.info(law.link + " " + law.number + " " + lawId + " i." + lawCount);
        lawCount++;

        // Dump the voting session in the database.
        for (SingleVote v : votingSession.votes) {
          DbManager.insertPersonVote(year, votingSession.link,
              v.getTypeAsString(), lawId, votingSession.time, v.deputy);
        }

        // Insert the nominal votes details into the database, at this point
        // we have all we need.
        DbManager.insertNominalVote(roomName, year, votingSession);
        votingSessions.add(votingSession);

        law = new GenericLaw();
        votingSession = new VotingSession();
      }
    }

    // This is at the very end of the file now, we can do aggregate stats on
    // senators.
    Collection<? extends Deputy> people = room == Main.SENAT ?
        senators.senators.values() :
        deputies.deps.values();
    computePeopleAggregates(year, votingSessions, people);

    computePartyLineVotes(roomName, year, votingSessions);
  }

  /**
   * Computes the aggregates for senators or deputies, depending on the room
   * we are working on. It then flushes these aggregates in the database.
   * @param year The year we are working on.
   * @param sessions The set of voting sessions.
   * @param people A set of people (deputies, senators).
   */
  private void computePeopleAggregates(String year,
      HashSet<VotingSession> sessions, Collection<? extends Deputy> people) {
    // I can go through the senators and print their stats.
    for (Deputy person : people) {
      person.flushAggregateStatsToDb(sessions, year);

      log.info(person.name + " " + person.getVoteStatsString(sessions));
      log.info(person.name + " is a " + 100 * person.getMaverickPercent() +
          "% maverick");
    }
  }

  /**
   * Calculates for each party how many of the final votes were party-line
   * votes and how many were not.
   * @param sessions The set of voting sessions.
   */
  private void computePartyLineVotes(String roomName, String year,
                                     HashSet<VotingSession> sessions) {
    // All the final votes there were.
    int allVotes = 0;
    // The final votes at which a party (more than 4 members) participated.
    int[] allFinalVotes = new int[20];
    // The final votes that were votes along party lines.
    int[] partyLineVotes = new int[20];

    for (VotingSession session : sessions) {
      if (session.type.contains("final")) {
        allVotes++;
        for (int i = 0; i <= 14; i++) {
          int count = session.getNumVotesForParty(i);

          // Only count a vote if more than 4 people from a party participated.
          if (count > 4 || (i == 7 && count > 3)) {
            allFinalVotes[i]++;

            SingleVote.Type type = session.getPartyVote(i);
            if (type != SingleVote.Type.MI) {
              // If this is indeed a party line vote for this party, mark it
              // as such.
              partyLineVotes[i]++;
            }
          }
        }
      }
    }

    // 1 = PNL
    // 2 = PD-L
    // 3 = Minoritati
    // 7 = UDMR
    // 9 = PD
    // 10 = Indep
    // 14 = PSD+PC
    String prefix = roomName + year;
    for (int i = 1; i <= 14; i++) {
      if (i == 1 || i == 2 || i == 3 || i == 7 || i == 10 || i == 14) {
        DbManager.insertPartyFact(i, prefix + "/all_votes", "" + allVotes);
        DbManager.insertPartyFact(i, prefix + "/party_votes",
            "" + allFinalVotes[i]);
        DbManager.insertPartyFact(i, prefix + "/party_line_votes",
            "" + partyLineVotes[i]);
      }
    }

    log.info("PNL: " + partyLineVotes[1] + " / " + allFinalVotes[1]);
    log.info("PD-L: " + partyLineVotes[2] + " / " + allFinalVotes[2]);
    log.info("Minoritati: " + partyLineVotes[3] + " / " + allFinalVotes[3]);
    log.info("UDMR: " + partyLineVotes[7] + " / " + allFinalVotes[7]);
    log.info("Indep: " + partyLineVotes[10] + " / " + allFinalVotes[10]);
    log.info("PSD+PC: " + partyLineVotes[14] + " / " + allFinalVotes[14]);
  }

  /**
   * Extracts the unique id of the law from the link. We specify the room
   * because links for the two different rooms have a different structure and
   * different unique ids.
   * @param link The link of the voting session.
   * @param room The room (cdep or senat).
   * @return A string representing the unique id of this particular voting
   *     session on the respective site where it was taken from.
   */
  private String getUniqueIdFromLink(String link, int room) {
    if (room == Main.SENAT) {
      // The link looks like this:
      // http://www.senat.ro/VoturiPlenDetaliu.aspx?AppID=995a3b6f-e807-4c
      return link.split("AppID=")[1];

    } else if (room == Main.CDEP) {
      // We need to extract the uniqueId= from this link.
      // http://www.cdep.ro/pls/steno/evot.nominal?idv=7273&idl=1
      Pattern p = Pattern.compile(
          "http://www\\.cdep\\.ro/pls/steno/evot\\.nominal\\?" +
          "idv=(\\d+)&idl=(\\d+)");
      Matcher m = p.matcher(link);
      if (m.matches()) {
        return m.group(1);
      }
    }
    return "";
  }

  /**
   * A few hacks to clean up the name of people that are in the file. This is
   * necessary due to inconsistencies within the same file.
   * @param name The name, as read from the file.
   * @return The cleaned up name.
   */
  private String cleanNameHack(String name) {
    name = name.replace("-", " ");
    name = name.replace("C.tin", "Constantin");
    return name;
  }

  /**
   * For a line that begins with "tag:" returns the content without the tag.
   * @param line The line.
   * @return The part that's after the colon.
   */
  private String getLineContent(String line) {
    return line.substring(line.indexOf(':') + 1).trim();
  }

  /**
   * Returns the law's number from the link.
   * @param link Given a link to a senate law, return the number of the law.
   * @return The number of the law.
   */
  private String getLawNumberFromLink(String link) {
    // NR=L661&AN=2008
    Pattern p =
        Pattern.compile("http://webapp\\.senat\\.ro/sergiusenat\\.proie" +
        "ct\\.asp\\?cod=(\\d+)&pos=(\\d+)&NR=L(\\d+)&AN=(\\d+)");
    Matcher m = p.matcher(link);
    if (m.matches()) {
      return m.group(3) + "/" + m.group(4);
    }
    return "";
  }

  /**
   * From a date string, return the time value.
   * 22-12-2008 04:22
   */
  @SuppressWarnings("deprecation")
  private long getTimeForVote(String time) {
    Pattern p = Pattern.compile("(\\d+)-(\\d+)-(\\d+) (\\d+):(\\d+)");
    Matcher m = p.matcher(time);
    if (m.matches()) {
      Date d = new Date(0);
      d.setYear(Integer.parseInt(m.group(3)) - 1900);
      d.setMonth(Integer.parseInt(m.group(2)) - 1);
      d.setDate(Integer.parseInt(m.group(1)));

      d.setHours(Integer.parseInt(m.group(4)) - 6);
      d.setMinutes(Integer.parseInt(m.group(5)));
      d.setSeconds(0);

      return d.getTime();
    }
    return 0;
  }
}
