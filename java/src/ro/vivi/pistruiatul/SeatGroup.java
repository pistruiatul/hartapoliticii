package ro.vivi.pistruiatul;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.logging.Logger;

/**
 * A seat group represents all the seats in a county, in which redistribution
 * will occurr.
 * @author vivi
 *
 */
public class SeatGroup {
  Logger log = Logger.getLogger("ro.vivi.pistruiatul.SeatGroup");

  /** The list of seats in this group */
  public ArrayList<Seat> seats = new ArrayList<Seat>();

  /** The number of allocated seats per party for this seat group */
  public HashMap<String, Integer> allocated = new HashMap<String, Integer>();

  /** The number of allocated seats per party for this seat group */
  public HashMap<String, Integer> wonSeats = new HashMap<String, Integer>();

  /**
   * The county that this seat group represents.
   */
  public String county;

  public final String CAMERA_DEPUTATILOR = "D";
  public final String SENAT = "S";

  public String room;

  public SeatGroup(String county, String room) {
    this.county = county;
    this.room = room;
    DbManager.getSeatsForCounty(this, seats);
    DbManager.getAllocatedSeatsPerParty(this, allocated);
  }

  /**
   * Returns a reference to the seat by it's name.
   * @param name
   * @return A reference to a Seat object.
   */
  public Seat getSeat(String name) {
    for (Seat seat : seats) {
      if (seat.name.equals(name)) {
        return seat;
      }
    }
    return null;
  }

  /**
   * Counts a seat for a given party as won (so we can compare with the
   * allocation)
   */
  public void countWinner(Candidate c) {
    Integer won = wonSeats.get(c.partyName);
    won = (won == null) ? new Integer(1) : new Integer(won.intValue() + 1);
    wonSeats.put(c.partyName, won);
  }

  /**
   * Returns true if this seat group can accomodate a party allocation for this
   * particular candidate's party.
   * @param c
   * @return
   */
  public boolean hasPartyAllocationFor(Candidate c) {
    Integer alloc = allocated.get(c.partyName);
    int allocSeats = alloc == null ? 0 : alloc.intValue();

    Integer won = wonSeats.get(c.partyName);
    int wonSeats = won == null ? 0 : won.intValue();

    return wonSeats < allocSeats;
  }
}
