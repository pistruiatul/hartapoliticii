package ro.vivi.pistruiatul;

import java.util.HashMap;
import java.util.HashSet;
import java.util.logging.Logger;

/**
 * Holds data about one voting session. This ties together all the information
 * about when the entire room votes on something.
 */
public class VotingSession {
  static Logger log = Logger.getLogger("ro.vivi.pistruiatul.VotingSession");

  /**
   * The id of the law that this vote session is referring to.
   */
  public int idLaw = -1;

  /**
   * The time at which the voting occurred.
   */
  public long time;

  /**
   * The link where I can find this vote. This serves as a unique ID also for
   * this vote.
   */
  public String link;

  /**
   * A unique ID of this voting session on the website where this was taken
   * from. For example, for senat.ro it will be the "AppId" used in the link,
   * for cdep.ro it will be the "uniqueId" used in the link.
   */
  public String uniqueId;

  /**
   * This is the type of the vote. Usually something like "vot final" or
   * "respingere proiect", "prezenta".
   */
  public String type;

  /**
   * The subject of the vote, some sort of short summary, usually about what
   * law is being voted and what is going on.
   */
  public String subject;

  /**
   * A pool holding each and every one of the individual votes expressed by the
   * people participating in this voting session.
   */
  HashSet<SingleVote> votes;

  /**
   * Keeps track of all the types of votes, for each party, so we can decide
   * whether certain parties votes along party lines in this session.
   */
  HashMap<Integer, Integer> partyVotes;

  /**
   * An array keeping track of how many votes there were in each of the four
   * possible voting categories. DA, NU, AB and MI.
   */
  public int[] votesAgg;

  /**
   * When true, this voting session is marked as an error and ignored. This
   * happens when stuff is wrong on the website or something.
   */
  public boolean error = false;

  /**
   * Initializes the aggregate votes and the set of single votes.
   */
  public VotingSession() {
    votes = new HashSet<SingleVote>();
    votesAgg = new int[] {0, 0, 0, 0};
    partyVotes = new HashMap<Integer, Integer>();
  }

  /**
   * Add an individual vote to this voting session. Adding the same vote twice
   * is handled gracefully.
   * @param vote The single vote object that needs to be added.
   */
  public void addSingleVote(SingleVote vote) {
    if (votes.contains(vote)) {
      return;
    }

    if (vote.type == SingleVote.Type.DA) {
      votesAgg[0]++;
    } else if (vote.type == SingleVote.Type.NU) {
      votesAgg[1]++;
    } else if (vote.type == SingleVote.Type.AB) {
      votesAgg[2]++;
    } else if (vote.type == SingleVote.Type.MI) {
      votesAgg[3]++;
    }
    votes.add(vote);

    // Add this vote to the aggregates per party.
    // TODO(vivi): Only do this for final votes, we shouldn't do this for when
    // they have a vote about presence or other stupid tests.
    if (type.contains("final")) {
      int partyId = vote.deputy.getPartyId(vote.time);
      if (partyId == -1) {
        log.severe("Deputy [" + vote.deputy + "]: missing party on a vote");
        System.exit(1);
      }
      int key = getKey(partyId, vote.type);
      if (!partyVotes.containsKey(key)) {
        partyVotes.put(key, 0);
      }
      partyVotes.put(key, partyVotes.get(key) + 1);
    }
  }

  /**
   * Counts the number of votes that a party expressed in this session.
   * @param partyId The id of the party.
   * @return The number of votes.
   */
  public int getNumVotesForParty(int partyId) {
    int count = 0;
    for (SingleVote vote : votes) {
      if (vote.deputy.getPartyId(vote.time) == partyId) {
        count++;
      }
    }
    return count;
  }

  /**
   * Returns the party-line vote for the party passed in as a parameter. If
   * this was not a party-line vote, just return Type.MI.
   * @param partyId The id of the party we are interested in.
   * @return The type of vote that was a party-line vote, or MI otherwise.
   */
  public SingleVote.Type getPartyVote(int partyId) {
    // TODO(vivi): What happens with the case where not enough people from the
    // party were present? Do we consider that a party vote? For now, let's do
    // so.
    // First, add all the votes.
    int all = 0;
    for (SingleVote.Type type : SingleVote.Type.values()) {
      Integer pv = partyVotes.get(getKey(partyId, type));
      all += pv == null ? 0 : pv;
    }
    if (all == 0) {
      return SingleVote.Type.MI;
    }

    for (SingleVote.Type type : SingleVote.Type.values()) {
      Integer votes = partyVotes.get(getKey(partyId, type));
      // If the votes of a certain type are more than 90% of party votes, than
      // declare this to be a vote along party lines.
      if (1.0 * (votes == null ? 0 : votes) / all > 0.8) {
        return type;
      }
    }
    return SingleVote.Type.MI;
  }

  /**
   * Returns a hash key that we will use to store party votes.
   * @param partyId The id of the party.
   * @param vote The type of vote.
   * @return An int that will be used as a key.
   */
  private int getKey(int partyId, SingleVote.Type vote) {
    int voteInt = -1;
    if (vote == SingleVote.Type.DA) {
      voteInt = 1;
    } else if (vote == SingleVote.Type.NU) {
      voteInt = 2;
    } else if (vote == SingleVote.Type.AB) {
      voteInt = 3;
    } else if (vote == SingleVote.Type.MI) {
      voteInt = 4;
    }
    return partyId * 10 + voteInt;
  }

  /**
   * Returns the number of votes that are present in this voting session.
   * @return Number of votes.
   */
  public int size() {
    return votes.size();
  }

}
