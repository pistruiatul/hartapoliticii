package ro.vivi.pistruiatul;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collections;

/**
 * Represents a group of candidates.
 * @author vivi
 *
 */
public class CandidatesGroup {
  ArrayList<Candidate> candidates = new ArrayList<Candidate>();

  public SeatGroup seatGroup;

  public CandidatesGroup(SeatGroup sg) {
    this.seatGroup = sg;
    DbManager.getCandidatesForSeatGroup(sg, candidates);
  }

  /**
   * Returns the total number of votes for a particular seat.
   * @param seat
   * @return
   */
  public int getTotalVotes(Seat seat) {
    int sum = 0;
    for (Candidate c : candidates) {
      sum += c.runsForSeat == seat ? c.votes : 0;
    }
    return sum;
  }

  /**
   * Returns the candidate with the most votes for a particular seat.
   */
  public Candidate getTopDog(Seat seat) {
    ArrayList<Candidate> list = getSortedCandidates(seat);
    return list.get(0);
  }

  /**
   * Returns a candidate object, by name.
   * @param name
   * @return
   */
  public Candidate getCandidate(String name) {
    for (Candidate candidate : candidates) {
      if (candidate.name.equals(name)) {
        return candidate;
      }
    }
    return null;
  }

  /**
   * Returns the most recent winner from the party given as a parameter.
   * @param seat
   * @return
   */
  public Candidate getMostRecentNonMajorityWinner(String partyName) {
    ArrayList<Candidate> list = getSortedCandidates(null);
    Candidate mostRecentWinner = null;
    for (Candidate candidate : list) {
      if (candidate.wonSeat != null &&
          candidate.partyName.equals(partyName) &&
          candidate.votes < candidate.wonSeat.totalVotes / 2) {
        mostRecentWinner = candidate;
      }
    }
    return mostRecentWinner;
  }

  @SuppressWarnings("unchecked")
  public ArrayList<Candidate> getSortedCandidates(Seat seat) {
    ArrayList<Candidate> list = new ArrayList<Candidate>();

    for (Candidate c : candidates) {
      if (seat == null || c.runsForSeat == seat) {
        list.add(c);
      }
    }
    Collections.sort(list);
    return list;
  }
}
