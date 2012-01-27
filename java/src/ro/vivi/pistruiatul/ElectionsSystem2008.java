package ro.vivi.pistruiatul;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.logging.Logger;

public class ElectionsSystem2008 {
  Logger log = Logger.getLogger("ro.vivi.pistruiatul.ElectionsSystem2008");

  /** A reference to the Main access point, just in case we need it. */
  private Main main;

  /** For each county, the seat group */
  private HashMap<String, SeatGroup> seatGroups =
    new HashMap<String, SeatGroup>();

  /** For each county, the candidates group */
  private HashMap<String, CandidatesGroup> candidatesGroups =
    new HashMap<String, CandidatesGroup>();

  /** A map of the seats that were won by bypassing a candidate
   * with more votes
   */
  private HashMap<Seat, Integer> bypassedSeats = new HashMap<Seat, Integer>();

  // Private helper count variable;
  int straightWinners = 0;
  int firstChoiceWinners = 0;
  int secondChoiceWinners = 0;

  boolean inhibitCount = false;

  /**
   * Loads the data from the data base. The array of counties and all the
   * candidates and votes from those counties.
   * @param main The reference to the main access point of this app.
   */
  public ElectionsSystem2008(Main main) {
    this.main = main;
  }

  /**
   * Loads data from the database.
   */
  private void loadFromDb() {
    DbManager.loadSeatGroups(seatGroups);

    int totalSeats = 0;
    for (SeatGroup sg : seatGroups.values()) {
      CandidatesGroup cg = new CandidatesGroup(sg);
      candidatesGroups.put(sg.room + sg.county, cg);

      totalSeats += sg.seats.size();
    }
    log.info("Total seats: " + totalSeats);
  }

  private boolean inhibitCount(String room) {
    return inhibitCount || room.equals("D");
  }

  /**
   * Runs a simulation for deciding the winners, given a SeatGroup and a
   * CandidatesGroup
   */
  public void decideWinners(SeatGroup sg, CandidatesGroup cg) {
    // Step 1: See who are the winners with 50% + 1 votes.
    for (Seat seat : sg.seats) {
      if (seat.totalVotes == 0) {
        seat.totalVotes = cg.getTotalVotes(seat);
      }
      Candidate topDog = cg.getTopDog(seat);

      if (topDog.votes > seat.totalVotes / 2) {
        topDog.wonSeat = seat;
        seat.winner = topDog;

        sg.countWinner(seat.winner);
        if (!inhibitCount(sg.room)) {
          straightWinners++;
        }
      }
    }

    // Step 2: Get a list of the sorted Candidates for this county
    ArrayList<Candidate> sorted = cg.getSortedCandidates(null);

    /*
    log.info("------------------------------");
    log.info(sg.county + " " + sorted.toString());
    log.info(sg.allocated.toString());
    log.info(sg.wonSeats.toString());
    */

    // For each candidate, start trying to put them in a place, if the
    // allocation for it's party didn't run out yet.
    for (Candidate candidate : sorted) {
      if (candidate.wonSeat != null) {
        continue;
      }
      boolean seatIsFree = candidate.runsForSeat.winner == null;
      boolean partyHasAllocation = sg.hasPartyAllocationFor(candidate);

      int neededExtraVotes = Integer.MAX_VALUE;
      String reason = "Imposibil";

      if (seatIsFree && partyHasAllocation) {
        // Nobody has won this seat yet, and the party allocation allows us one
        // seat! Woohoo, he won.
        candidate.runsForSeat.winner = candidate;
        candidate.wonSeat = candidate.runsForSeat;

        sg.countWinner(candidate);
        if (!inhibitCount(sg.room)) {
          if (bypassedSeats.get(candidate.runsForSeat) == null) {
            firstChoiceWinners++;
          } else {
            secondChoiceWinners++;
          }
        }

      } else if (seatIsFree && !partyHasAllocation) {
        // The seat was empty but the party ran out of seats. This candidate
        // would need to be above any party member of his that won a seat before
        // him. Since those won seats were in a different college, this is
        // pretty straighforward.
        if (!inhibitCount(sg.room)) {
          Integer count = bypassedSeats.get(candidate.runsForSeat);
          count = count == null ?
                  new Integer(1) : new Integer(count.intValue() + 1);
          bypassedSeats.put(candidate.runsForSeat, count);
        }

        Candidate lastWinnerFromThisParty =
          cg.getMostRecentNonMajorityWinner(candidate.partyName);

        if (lastWinnerFromThisParty != null) {
          if (lastWinnerFromThisParty.votes < neededExtraVotes) {
            neededExtraVotes = lastWinnerFromThisParty.votes - candidate.votes;
            reason = "ar fi prins un loc alocat partidului";
          }
        }
      } else if (!seatIsFree && partyHasAllocation) {
        // the seat is not free, but if it would have been, I only needed to
        // be above the candidate right before him (does not need 50%). simple.
        int votes = candidate.runsForSeat.winner.votes + 1 - candidate.votes;
        if (votes < neededExtraVotes) {
          neededExtraVotes = votes;
          reason = "ar fi deputy\u0103\u0219it pe primul clasat";
        }

      } else if (!seatIsFree && !partyHasAllocation) {
        // This is the complicated case. The candidate needs to be above the guy
        // right before him but also above anybody else from his party that
        // already won...
        int minVotesToGetSeat = Integer.MAX_VALUE;
        String tempReason = "No reason";

        Candidate lastWinnerFromThisParty =
          cg.getMostRecentNonMajorityWinner(candidate.partyName);
        if (lastWinnerFromThisParty != null &&
            lastWinnerFromThisParty.votes < minVotesToGetSeat) {
          minVotesToGetSeat = lastWinnerFromThisParty.votes;
          tempReason = "ar fi prins un loc alocat partidului";
        }
        if (minVotesToGetSeat < candidate.runsForSeat.winner.votes + 1) {
          minVotesToGetSeat = candidate.runsForSeat.winner.votes + 1;
          tempReason = "ar fi deputy\u0103\u0219it pe primul clasat";
        }

        if (minVotesToGetSeat - candidate.votes < neededExtraVotes) {
          neededExtraVotes = minVotesToGetSeat - candidate.votes;
          reason = tempReason;
        }
      }

      if (candidate.wonSeat == null) {
        // Also see what would this candidate have needed to go above 50%+1, this
        // trumps all the previous cases of needed votes.
        int votes = candidate.runsForSeat.totalVotes - 2 * candidate.votes + 1;
        if (votes < neededExtraVotes) {
          neededExtraVotes = votes;
          reason = "ar fi ob\u021Binut 50%+1";
        }
      }

      // If this unseating is the closest one, store it.
      if (neededExtraVotes < candidate.runsForSeat.runnerUpVotes) {
        candidate.runsForSeat.runnerUp = candidate;
        candidate.runsForSeat.runnerUpVotes = neededExtraVotes;
        candidate.runsForSeat.runnerUpReason = reason;
      }
      if (!inhibitCount(sg.room)) {
        // Log the current candidate and his reasons
        int winner = candidate.wonSeat == null ? 0 : 1;
        DbManager.updateCandidateReason(candidate, winner, neededExtraVotes,
            reason);
      }
    }
  }


  /**
   * Considering that all the data is loaded from the database, run a simulation
   * to get the winners out of it.
   */
  public void run() {
    loadFromDb();

    DbManager.emptyResults2008Aggregates();

    // just print some stuff for now.
    for (SeatGroup sg : seatGroups.values()) {
      CandidatesGroup cg = candidatesGroups.get(sg.room + sg.county);
      decideWinners(sg, cg);

      // For each seat, let's demote the winner in some way
      for (Seat seat : sg.seats) {
        inhibitCount = true;
        simulateIfWinnerDidntHaveFiftyPercent(sg, seat);
        inhibitCount = false;

        // Now I can actually print the winner and the runner up.
        DbManager.insertSeatInfoToAggregates(sg, seat);
        DbManager.updateCandidateReason(seat.winner, 1,
              seat.runnerUpVotes, seat.runnerUpReason);

      }
    }

    log.info("50%+1 winners: " + straightWinners);
    log.info("First choice winners: " + firstChoiceWinners);
    //log.info("Bypassed candidates: " + secondChoiceWinners + "/" +
    //    bypassedSeats.size());
    int[] c = new int[10];
    for (Integer choice : bypassedSeats.values()) {
      c[choice.intValue()]++;
    }
    log.info("Second choice winners: " + c[1]);
    log.info("Third choice winners: " + c[2]);
    log.info("Forth choice winners: " + c[3]);
  }


  /**
   * For a particular county and a give seat from that county (with a winner
   * candidate attached to it), see if changing the winner's votes to under 50%
   * would have made a difference.
   * @param county
   * @param seat
   */
  private void simulateIfWinnerDidntHaveFiftyPercent(SeatGroup sg, Seat seat) {
    // Create cloned seatGroup and candidatesGroup. By instantiating the
    // object the data is loaded from the database, so it's all fresh.
    SeatGroup freshSg = new SeatGroup(sg.county, sg.room);
    CandidatesGroup freshCg = new CandidatesGroup(freshSg);

    // How do we demote the winner of this seat? First, let's try to
    // make him have under 50%, if he had over.
    if (seat.winner != null &&
        seat.winner.votes > seat.totalVotes / 2) {
      // Find this winner dude in the CandidatesGroup
      Candidate fool = freshCg.getCandidate(seat.winner.name);

      int extraVotes = fool.votes * 2 - seat.totalVotes;

      fool.runsForSeat.totalVotes = freshCg.getTotalVotes(fool.runsForSeat) +
          extraVotes;

      decideWinners(freshSg, freshCg);

      if (fool.wonSeat == null) {
        if (seat.runnerUpVotes > extraVotes) {
          seat.runnerUpVotes = extraVotes;
          seat.runnerUp = fool.runsForSeat.winner;
          seat.runnerUpReason =
            "ar fi adus ca\u0219tig\u0103torul sub 50%+1";
          DbManager.updateCandidateReason(seat.runnerUp, 0,
              seat.runnerUpVotes, seat.runnerUpReason);
        }
      }
    }
  }
}
