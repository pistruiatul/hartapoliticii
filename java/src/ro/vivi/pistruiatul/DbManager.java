package ro.vivi.pistruiatul;

import java.sql.*;
import java.util.HashMap;
import java.util.List;
import java.util.logging.Logger;

/**
 * Manages the connection with the database, knows how to insert stuff in there
 * and also knows how to read stuff from there.
 * @author vivi
 */
public class DbManager {
  static Logger log = Logger.getLogger("ro.vivi.pistruiatul.DbManager");

  static boolean NOOP = false;

  public static Connection conn;
  public static PreparedStatement deletePartyPs = null;
  public static PreparedStatement insertPartyPs = null;
  public static PreparedStatement deleteDeputyVotePs = null;
  public static PreparedStatement insertDeputyVotePs = null;
  public static PreparedStatement updateMaverickVotePs = null;
  /** Initializes the connection with the database. */
  static {
    try {
      Class.forName("com.mysql.jdbc.Driver").newInstance();
    } catch (Exception ex) { ex.printStackTrace(); }

    try {
      conn = DriverManager.getConnection("jdbc:mysql://localhost/" +
          "hartapoliticii_pistruiatul?user=root&password=root&" +
          "characterEncoding=utf-8");

    } catch (SQLException ex) {
      // handle any errors
      System.out.println("SQLException: " + ex.getMessage());
      System.out.println("SQLState: " + ex.getSQLState());
      System.out.println("VendorError: " + ex.getErrorCode());
    }
  }

  /** Loads the deputies that are currently in the data base */
  public static void loadParliamentParties(HashMap<String, Integer> parties) {
    try {
      Statement stmt = conn.createStatement();
      ResultSet rs = stmt.executeQuery("SELECT id, name FROM parties");
      while (rs.next()) {
        int id = rs.getInt(1);
        String name = rs.getString(2);

        parties.put(name, new Integer(id));
      }
      rs.close();
      stmt.close();

    } catch (SQLException se) {
      se.printStackTrace();
    }
  }

  /** Loads the deputies that are currently in the data base */
  public static void loadElectionsParties(HashMap<String, Integer> parties) {
    try {
      Statement stmt = conn.createStatement();
      ResultSet rs = stmt.executeQuery("SELECT id, name, long_name " +
          "FROM parties group by name");
      while (rs.next()) {
        int id = rs.getInt(1);
        String name = Utils.replaceDiacritics(
            rs.getString(3).trim().toLowerCase());

        parties.put(name, new Integer(id));
      }
      rs.close();
      stmt.close();
    } catch (SQLException se) {
      se.printStackTrace();
    }
  }

  /** Loads the deputies that are currently in the data base */
  public static void loadCdepLaws(CdepLaws laws, String year) {
    try {
      Statement stmt = conn.createStatement();
      ResultSet rs = stmt.executeQuery(
          "SELECT id, link, number, text FROM cdep_" + year + "_laws");
      while (rs.next()) {
        int id = rs.getInt(1);
        String link = rs.getString(2);
        String number = rs.getString(3);
        String text = rs.getString(4);

        CdepLaw cdepLaw = new CdepLaw(number, laws);
        cdepLaw.id = id;

        // http://www.cdep.ro/pls/proiecte/upl_pck.proiect?idp=123
        int pos = link.indexOf("idp=");
        cdepLaw.idp = link.substring(pos + "idp=".length());

        cdepLaw.description = text;
        laws.laws.put(number, cdepLaw);
      }
      rs.close();
      stmt.close();
    } catch (SQLException se) {
      se.printStackTrace();
    }
  }


  /** Loads the deputies that are currently in the data base */
  public static void getSeatsForCounty(SeatGroup sg, List<Seat> seats) {
    try {
      Statement stmt = conn.createStatement();
      ResultSet rs = stmt.executeQuery(
          "SELECT colegiu FROM results_2008 where colegiu like \"" +
          sg.room + "% " + sg.county + "\" group by colegiu");
      while (rs.next()) {
        Seat s = new Seat();
        s.name = rs.getString(1).trim();

        seats.add(s);
      }
      rs.close();
      stmt.close();
    } catch (SQLException se) {
      se.printStackTrace();
    }
  }

  /** Loads the deputies that are currently in the data base */
  public static void getAllocatedSeatsPerParty(SeatGroup sg,
      HashMap<String, Integer> allocated) {
    try {
      Statement stmt = conn.createStatement();
      ResultSet rs = stmt.executeQuery(
          "SELECT partid, numar, idpartid FROM results_2008_allocated " +
          "where judet =\"" + sg.county + "\" and room = \"" +
          sg.room + "\"");
      while (rs.next()) {
        String party = Utils.replaceDiacritics(
            rs.getString(1).trim().toLowerCase());
        Integer seats = new Integer(rs.getInt(2));
        allocated.put(party, seats);
      }
      rs.close();
      stmt.close();
    } catch (SQLException se) {
      se.printStackTrace();
    }
  }


  /** Loads the deputies that are currently in the data base */
  public static void getCandidatesForSeatGroup(SeatGroup sg,
      List<Candidate> candidates) {
    try {
      Statement stmt = conn.createStatement();
      ResultSet rs = stmt.executeQuery(
          "SELECT nume, colegiu, voturi, partid, idcandidat, idperson " +
          "FROM results_2008 " +
          "where colegiu like \"" + sg.room + "% " + sg.county + "\"");
      while (rs.next()) {
        Candidate c = new Candidate();
        c.name = rs.getString(1);
        c.runsForSeat = sg.getSeat(rs.getString(2).trim());
        c.votes = rs.getInt(3);
        c.partyName = Utils.replaceDiacritics(
            rs.getString(4).trim().toLowerCase());
        c.party = ElectionsParties.getPartyId(c.partyName);
        c.id = rs.getInt(5);
        c.idperson = rs.getInt(6);
        candidates.add(c);
      }
      rs.close();
      stmt.close();
    } catch (SQLException se) {
      se.printStackTrace();
    }
  }

  /** Loads the senate laws */
  public static void loadSenateLaws(SenateLaws laws, String year) {
    try {
      Statement stmt = conn.createStatement();
      ResultSet rs = stmt.executeQuery(
          "SELECT id, appid FROM senat_" + year + "_laws");
      while (rs.next()) {
        int id = rs.getInt(1);
        String appId = rs.getString(2);

        SenateLaw law = new SenateLaw(laws);
        law.id = id;
        law.appId = appId;
        laws.laws.put(appId, law);
      }
      rs.close();
      stmt.close();
    } catch (SQLException se) {
      se.printStackTrace();
    }
  }

  /** Loads the deputies that are currently in the data base */
  public static void loadDeputies(HashMap<String, Deputy> deps, String year) {
    try {
      Statement stmt = conn.createStatement();
      ResultSet rs = stmt.executeQuery(
          "SELECT id, idm, name, timein, timeout " +
          "FROM cdep_" + year + "_deputies");
      while (rs.next()) {
        String idm = "" + rs.getInt(2);
        String name = rs.getString(3);

        Deputy dep = new Deputy(name, idm);
        dep.id = "" + rs.getInt(1);

        dep.startTime = rs.getLong(4);
        if (rs.getLong(5) > 0) {
          dep.endTime = rs.getLong(5);
        }

        deps.put(name, dep);
      }
      rs.close();
      stmt.close();
    } catch (SQLException se) {
      se.printStackTrace();
    }
  }

  /** Loads the deputies that are currently in the data base */
  public static void loadSeatGroups(HashMap<String, SeatGroup> seatGroups) {
    try {
      Statement stmt = conn.createStatement();
      ResultSet rs = stmt.executeQuery("SELECT judet, room FROM " +
          "results_2008_allocated group by judet, room");
      while (rs.next()) {
        String room = rs.getString(2);
        String county = rs.getString(1);
        seatGroups.put(room + county, new SeatGroup(county, room));
      }
      rs.close();
      stmt.close();
    } catch (SQLException se) {
      se.printStackTrace();
    }
  }

  /** Loads the deputies that are currently in the data base */
  public static void loadSenators(String year,
      HashMap<String, Senator> senators) {
    try {
      Statement stmt = conn.createStatement();
      ResultSet rs = stmt.executeQuery(
          "SELECT name, id, idperson " +
          "FROM senat_" + year + "_senators");
      while (rs.next()) {
        String name = rs.getString(1);
        int id = rs.getInt(2);

        // HACK: for now we don't have the senator's real id. This would be the
        // id from the cdep site, but we don't have that yet. Will have to
        // refactor this I guess.
        Senator sen = new Senator(name, "" + id);
        sen.id = "" + id;

        senators.put(name, sen);
      }
      rs.close();
      stmt.close();
    } catch (SQLException se) {
      se.printStackTrace();
    }
  }

  /** Inserts a deputy in the database */
  public static int insertSeatInfoToAggregates(SeatGroup sg, Seat seat) {
    String collegeNr = seat.name.substring(0, seat.name.indexOf(' '));
    String sql =
      "INSERT INTO results_2008_agg(college, winnerid, idperson_winner, " +
                                   "runnerupid, idperson_runnerup, reason, " +
                                   "total, difference, winvotes, runvotes, " +
                                   "county, college_nr) " +
      "values('" + seat.name + "', " +
              seat.winner.id + ", " + seat.winner.idperson + ", " +
              seat.runnerUp.id + ", " + seat.runnerUp.idperson + ", " + "'" +
              seat.runnerUpReason + "', " + seat.totalVotes + ", " +
              seat.runnerUpVotes + ", " + seat.winner.votes + ", " +
              seat.runnerUp.votes + ", '" + sg.county + "', '" +
              collegeNr + "')";
    return updateWithAutoIncrement(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertDeputy(Deputy dep) {
    String sql =
        "INSERT IGNORE INTO cdep_" + Main.YEAR +
            "_deputies (idm, name, timein) " +
        "values (" + dep.cdepId + ", '" + dep.name + "', " +
        dep.startTime + ")";
    int result = updateWithAutoIncrement(sql);

    dep.idperson = getIdPersonForDeputy("2008", dep);
    return result;
  }

  /**
   * Fetches the person id for a certain deputy, given the year in which this
   * guy is expected to have been a deputy.
   * @param year
   * @return
   */
  public static int getIdPersonForDeputy(String year, Deputy dep) {
    String sql =
      "SELECT idperson FROM cdep_" + year + "_deputies " +
      "WHERE idm=" + dep.cdepId;
    return selectInt(sql);
  }

  /**
   * Fetches the person id for a certain deputy, given the year in which this
   * guy is expected to have been a deputy.
   * @param year
   * @return
   */
  public static int getIdPersonForSenator(String year, Senator sen) {
    String sql =
      "SELECT idperson FROM senat_" + year + "_senators " +
      "WHERE name='" + sen.name + "'";
    return selectInt(sql);
  }

  /** Inserts a senator in the database */
  public static int insertSenator(Senator sen) {
    String sql =
        "INSERT INTO senat_2004_senators (name, idm) " +
        "values ('" + sen.name + "', '" + sen.idm + "')";
    return updateWithAutoIncrement(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertCdepLaw(CdepLaw cdepLaw) {
    String sql =
      "INSERT INTO cdep_" + Main.YEAR +
          "_laws(link, number, text) " +
      "values('" + cdepLaw.link + "', '" + cdepLaw.projectNumber + "', '" +
                  cdepLaw.description + "') ";
    return updateWithAutoIncrement(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertLaw(int room, String year, String lawLink,
      String projectNumber, String desc) {
    String roomName = "";
    switch(room) {
      case Main.SENAT: roomName = "senat"; break;
      case Main.CDEP: roomName = "cdep"; break;
    }
    // Insert the law.
    updateWithAutoIncrement(
      "INSERT IGNORE INTO " + roomName + "_" + year +
          "_laws(link, number, text) " +
      "values('" + lawLink + "', '" + projectNumber + "', '" + desc + "') ");

    // Because the ID doesn't seem to work correctly when we insert ignore, we
    // just look it up again.
    return selectInt("SELECT id FROM " + roomName + "_" + year + "_laws " +
        "WHERE link='" + lawLink + "'");
  }

  /** Inserts a deputy in the database */
  public static void updateCdepLawDescription(int id, String text) {
    String sql =
      "update cdep_" + Main.YEAR + "_laws SET " +
      "text='" + text + "' " +
      "WHERE id=" + id;
    update(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertSenateLaw(SenateLaw law) {
    String sql =
      "INSERT INTO senat_2004_laws(appId) " +
      "values('" + law.appId + "')";
    return updateWithAutoIncrement(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertParty(String name) {
    String sql =
      "INSERT INTO parties(name) " +
      "values('" + name + "')";
    return updateWithAutoIncrement(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertElectionsParty(String name) {
    String sql =
      "INSERT INTO results_2008_parties(name) " +
      "values('" + name + "')";
    return updateWithAutoIncrement(sql);
  }

  /**
   * Inserts a deputy in the database.
   */
  public static void insertPersonVote(
      String year, String voteLink, String vote,
      int lawId, long time, Deputy dep) {
    if (NOOP) return;
    try {
      if (deleteDeputyVotePs == null) {
        deleteDeputyVotePs = conn.prepareStatement(
            "DELETE FROM " + dep.getRoom() + "_" + year + "_votes " +
            "WHERE idperson= ? AND link= ?");
      }
      deleteDeputyVotePs.setInt(1, dep.idperson);
      deleteDeputyVotePs.setString(2, voteLink);
      deleteDeputyVotePs.executeUpdate();

      if (insertDeputyVotePs == null) {
        String idField = dep.getRoom().equals("cdep") ? "iddep" : "idsen";
        insertDeputyVotePs = conn.prepareStatement(
            "INSERT INTO " + dep.getRoom() + "_" + year + "_votes" +
            "(link, idlaw, vote, time, " + idField + ", idperson) " +
            "VALUES(?, ?, ?, ?, ?, ?)");
      }
      insertDeputyVotePs.setString(1, voteLink);
      insertDeputyVotePs.setInt(2, lawId);
      insertDeputyVotePs.setString(3, vote);
      insertDeputyVotePs.setLong(4, time);
      insertDeputyVotePs.setInt(5, Integer.parseInt(dep.id));
      insertDeputyVotePs.setInt(6, dep.idperson);
      insertDeputyVotePs.executeUpdate();

    } catch (SQLException se) {
      System.out.println("Did not delete " + voteLink + " " + time + " " +
          dep.idperson);
      se.printStackTrace();
      System.exit(1);
    }
  }

  public static void updateVoteIsMaverick(Deputy deputy, VotingSession session,
      SingleVote vote, String year) {
    if (NOOP) return;
    try {
      if (updateMaverickVotePs == null) {
        updateMaverickVotePs = conn.prepareStatement(
            "UPDATE " + deputy.getRoom() + "_" + year + "_votes " +
            "SET maverick=1 " +
            "WHERE link= ? AND time= ? AND idperson= ?");
      }
      updateMaverickVotePs.setString(1, session.link);
      updateMaverickVotePs.setLong(2, vote.time);
      updateMaverickVotePs.setInt(3, deputy.idperson);
      updateMaverickVotePs.executeUpdate();

    } catch (SQLException se) {
      se.printStackTrace();
      log.severe(updateMaverickVotePs.toString());
      System.exit(1);
    }
  }

  /**
   * Inserts a vote - number, description, type, time - into the database.
   */
  public static void insertNominalVote(String what, String year,
                                       VotingSession v) {
    // replace the old data.
    update("DELETE FROM " + what + "_" + year + "_votes_details WHERE " +
        "link = '" + v.link + "'");

    String sql =
      "INSERT INTO " + what + "_" + year + "_votes_details " +
      "   (link, idlaw, vda, vnu, vab, vmi, type, description, time) " +
      "VALUES('" +
        v.link + "', " +
        v.idLaw + ", " +
        v.votesAgg[0] + ", " +
        v.votesAgg[1] + ", " +
        v.votesAgg[2] + ", " +
        v.votesAgg[3] + ", '" +
        v.type + "', '" +
        v.subject + "', " +
        v.time +")";
    //System.out.println(sql);
    update(sql);
  }

  /**
   * Adds to the database the aggregate stats about this deputy. The previous
   * stats are simply removed and replaced.
   */
  public static void insertAggregateDeputyStats(String what, Deputy dep,
      String year) {
    // Clear the previous aggregates.
    update("DELETE FROM " + what + "_" + year + "_votes_agg WHERE idperson=" +
        dep.idperson);

    // Insert the new aggregates.
    String sql =
      "INSERT INTO " +
      what + "_" + year + "_votes_agg(iddep, idperson, vda, vnu, vab, " +
          "vmi, possible, percent, maverick, days_in, days_possible) " +
      "VALUES(" + dep.idm + ", " + dep.idperson + ", " +
              dep.getVotes(SingleVote.Type.DA) + ", " +
              dep.getVotes(SingleVote.Type.NU) + ", " +
              dep.getVotes(SingleVote.Type.AB) + ", " +
              dep.getVotes(SingleVote.Type.MI) + ", " +
              dep.getPossibleVotes() + ", " +
              dep.getVotesPercent() + ", " +
              dep.getMaverickPercent() + ", " +
              dep.getWorkDays() + ", " +
              dep.getPossibleDays() + ")";

    //System.out.println(sql);
    update(sql);
  }

  /**
   * Inserts a fact about a particular party.
   * @param partyId The id of the party for which we are adding a fact.
   * @param attr The attribute, the name of the fact.
   * @param value The value.
   */
  public static void insertPartyFact(int partyId, String attr, String value) {
    long time = System.currentTimeMillis();
    String sql =
      "INSERT IGNORE INTO parties_facts(idparty, attribute, value, time_ms) " +
      "values(" + partyId + ", '" + attr + "', '" + value + "', " + time + ")";
    update(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertSenatorVote(String appId, String vote, Senator sen,
      SenateLaw law, long time) {
    String sql =
      "INSERT INTO senat_2004_votes(uniqueId, idlaw, idsen, vote, time) " +
      "values('" + appId + "', " + law.id + ", " + sen.idm + ", '" +
              vote + "', " + time + ")";
    return updateWithAutoIncrement(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertVideo(Deputy dep, String idv, int sessions,
      int seconds) {
    String sql =
      "INSERT INTO " + Main.ROOM + "_" + Main.YEAR +
          "_video(uniqueId, iddep, sessions, seconds) " +
      "values(" + idv + ", " + dep.idm + ", " + sessions + ", " + seconds + ")";
    return updateWithAutoIncrement(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertLawProponent(int idlaw, int iddep, int chamber,
      int authorscount) {
    String sql =
      "INSERT IGNORE INTO " + Main.ROOM + "_" + Main.YEAR +
          "_laws_proponents(idlaw, iddep, chamber, authorscount) " +
      "values(" + idlaw + ", " + iddep + ", " + chamber + ", " +
              authorscount + ")";
    return updateWithAutoIncrement(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertAwayTime(int idperson, int chamber, long left,
      long back) {
    String sql =
      "INSERT INTO away_times(idperson, chamber, time_left, time_back," +
      "reason) " +
      "values(" + idperson + ", " + chamber + ", " + left + ", " +
              back + ", 'EuroParlamentar')";
    return updateWithAutoIncrement(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertLawStatus(int idlaw, int status) {
    String sql =
      "INSERT IGNORE INTO " + Main.ROOM + "_" + Main.YEAR +
          "_laws_status(idlaw, status) " +
      "values(" + idlaw + ", " + status + ")";
    return updateWithAutoIncrement(sql);
  }

  /** Inserts a deputy in the database */
  public static void insertPartyBelonging(String what, String year,
                                          String depid, int idperson,
                                          int partyid, long time) {
    if (NOOP) return;
    try {
      if (deletePartyPs == null) {
        deletePartyPs = conn.prepareStatement(
            "DELETE FROM " + what + "_" + year + "_belong " +
            "WHERE idperson= ? AND time= ? ");
      }
      deletePartyPs.setInt(1, idperson);
      deletePartyPs.setLong(2, time);
      deletePartyPs.executeUpdate();

      if (insertPartyPs == null) {
        insertPartyPs = conn.prepareStatement(
            "INSERT INTO " + what + "_" + year + "_belong " +
            "(iddep, idperson, idparty, time) VALUES(?, ?, ?, ?)");
      }
      insertPartyPs.setInt(1, Integer.parseInt(depid));
      insertPartyPs.setInt(2, idperson);
      insertPartyPs.setInt(3, partyid);
      insertPartyPs.setLong(4, time);
      insertPartyPs.executeUpdate();

    } catch (SQLException se) {
      se.printStackTrace();

      log.info(deletePartyPs.toString());
      log.info(insertPartyPs.toString());

      System.exit(1);
    }
  }

  /** Inserts a deputy in the database */
  public static void insertCurrentBelonging(String what, String year,
      Deputy dep) {
    // Clear the previous aggregates.
    update("DELETE FROM " + what + "_" + year + "_belong_agg WHERE idperson=" +
        dep.idperson);
    String sql =
      "INSERT INTO " + what + "_" + year + "_belong_agg" +
          "(iddep, idperson, idparty) " +
      "VALUES(" + dep.cdepId + ", " + dep.idperson + ", " +
          dep.mostRecentPartyId + ") ";
    update(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertSenatorPartyBelonging(String year, int id, int pid,
      long time) {
    String sql =
      "INSERT INTO senat_" + year + "_belong(idsen, idparty, time) " +
      "values(" + id + ", " + pid + ", " + time + ") ";

    return updateWithAutoIncrement(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertEuroCandidate2009(Person p) {
    String sql =
      "INSERT INTO euro_2009_candidates(name, profession, occupation, " +
      "birthday, idparty, position, idperson) " +
      "values('" + p.name + "', '" + p.getFact("profession") + "', '" +
              p.getFact("occupation") + "', " + p.getFact("birthday") + ", " +
              p.getFact("idparty") + ", " + p.getFact("position") + ", 0)";
    return updateWithAutoIncrement(sql);
  }


  /** Inserts a deputy in the database */
  public static void updateDeputy(long startDate, long endDate,
      String motif, int id) {
    String sql =
      "UPDATE cdep_" + Main.YEAR + "_deputies SET " +
          "timein = '" + startDate + "', " +
          "timeout = '" + endDate + "', " +
          "motif = '" + motif + "' " +
      "where cdep_" + Main.YEAR + "_deputies.id = " + id;
    update(sql);
  }

  /** Update the image of a deputy */
  public static void updateDeputyImage(Deputy dep, String img) {
    String sql =
      "UPDATE cdep_" + Main.YEAR + "_deputies SET imgurl = '" + img + "' " +
      "where cdep_" + Main.YEAR + "_deputies.idm = " + dep.idm;
    update(sql);
  }

  /** Inserts a deputy in the database */
  public static void updateSenator(long startDate, long endDate,
      String motif, int id) {
    String sql =
      "UPDATE senat_2004_senators SET timein = '" + startDate + "', " +
          "timeout = '" + endDate + "', " +
          "motif = '" + motif + "' " +
      "where senat_2004_senators.id = " + id;
    update(sql);
  }

  /** Update the image of a deputy */
  public static void updateSenatorImage(Senator sen, String img) {
    String sql =
      "UPDATE senat_2004_senators SET imgurl = '" + img + "' " +
      "where senat_2004_senators.idm = " + sen.idm;
    update(sql);
  }


  public static void updateCandidateReason(Candidate candidate, int winner,
      int neededVotes, String reason) {
    String sql =
      "UPDATE results_2008_candidates " +
      "SET winner=" + winner + ", " +
          "college='" + candidate.runsForSeat.name + "', " +
          "difference=" + neededVotes + ", " +
          "reason='" + reason + "' " +
      "WHERE id = " + candidate.id;
    update(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertCatavencu(String name, String text, String url,
      String party) {
    String sql =
      "INSERT INTO catavencu_2008(name, t, url, party, idperson) " +
      "values('" + name + "', '" + text + "', '" + url + "', " +
      "'" + party + "', 0)";
    return updateWithAutoIncrement(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertEuroParliamentPresence(String name, long time) {
    String sql =
      "INSERT INTO euro_parliament_2007(name, time) " +
      "values('" + name + "', " + time + ")";
    return updateWithAutoIncrement(sql);
  }

  /** Inserts a deputy in the database */
  public static int insertQvorumEntry(String name, String text) {
    String sql =
      "INSERT INTO euro_parliament_2007_qvorum(name, text, idperson) " +
      "values('" + name + "', '" + text + "', 0)";
    return updateWithAutoIncrement(sql);
  }

  /** Run a generic update statement. Dumb java and jconnector. */
  private static int updateWithAutoIncrement(String update) {
    try {
      Statement stmt = conn.createStatement();
      stmt.executeUpdate(update);

      int autoIncKeyFromFunc = -1;
      ResultSet rs = stmt.executeQuery("SELECT LAST_INSERT_ID()");
      if (rs.next()) {
        autoIncKeyFromFunc = rs.getInt(1);
      }
      rs.close();
      stmt.close();
      return autoIncKeyFromFunc;

    } catch (SQLException se) {
      System.out.println(update);
      se.printStackTrace();
    }
    return -1;
  }

  private static int getLastInsertId(Statement s) throws SQLException {
    int autoIncKeyFromFunc = -1;
    ResultSet rs = s.executeQuery("SELECT LAST_INSERT_ID()");
    if (rs.next()) {
      autoIncKeyFromFunc = rs.getInt(1);
    }
    rs.close();
    return autoIncKeyFromFunc;
  }

  /** Delete all the votes */
  public static void deleteAllFromVotes() {
    update("TRUNCATE TABLE votes");
    update("TRUNCATE TABLE belongs");
  }

  /** Delete all the votes */
  public static void emptyResults2008Aggregates() {
    update("TRUNCATE TABLE results_2008_agg");
  }


  /** Delete all the votes */
  public static void deleteAllFromSenateVotes() {
    update("TRUNCATE TABLE votes_senators");
    update("TRUNCATE TABLE senators_belongs");
  }

  /** Delete all the votes */
  public static void deleteEuro2009() {
    update("TRUNCATE TABLE euro_2009_candidates");
  }

  /** Delete all the votes */
  public static void deleteQvorum2007() {
    update("TRUNCATE TABLE euro_parliament_2007_qvorum");
  }

  /** Delete all the votes */
  public static void deleteCatavencu() {
    update("TRUNCATE TABLE catavencu_2008");
  }

  /** Delete all the votes */
  public static void deleteEuroParliament() {
    update("TRUNCATE TABLE euro_parliament_2007");
  }

  /**
   * Run a generic update statement. Dumb java and jconnector.
   * @param update The sql query to be executed.
   */
  private static void update(String update) {
    try {
      Statement stmt = conn.createStatement();
      stmt.executeUpdate(update);
      stmt.close();
    } catch (SQLException se) {
      se.printStackTrace();
    }
  }

  private static int selectInt(String select) {
    int result = -1;
    try {
      Statement stmt = conn.createStatement();
      ResultSet rs = stmt.executeQuery(select);
      while (rs.next()) {
        result = rs.getInt(1);
      }
      rs.close();
      stmt.close();

    } catch (SQLException se) {
      se.printStackTrace();
    }
    return result;
  }
}
