package ro.vivi.pistruiatul;

import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.UnsupportedEncodingException;

/**
 * Starts everything that's needed to parse our stuff.
 * @author vivi (Octavian Costache)
 */
public class Main implements PageConsumer {
  /** The host will be for now always the same */
  public static String HOST = "www.cdep.ro";

  /** The data model for the deputies list */
  public static Deputies deputies;

  /** The data model for the senators list */
  public Senators senators;

  /** The data model for the cdepLaws list */
  public static CdepLaws cdepLaws;
  public static SenateLaws senateLaws;

  /**
   * The thing that parse the eurodeputies. Why does Java have to make
   * everything an object?
   */
  public EuroDeputies eurodeputies;

  /**
   * A parser / holder object for the candidates at the euro parliament
   * elections in June 2009.
   */
  public EuroCandidates2009 euroCandidates2009;

  /**
   * The 2008 elections object, used to load all it's data (the votes, parties
   * and then allocated seats for each party) and eventually do calculations
   * on that data to decide the winners.
   */
  public ElectionsSystem2008 system;

  /**
   * An object used to import the list of dirty candidates published in
   * Catavencu in November 2008.
   */
  public ListaCatavencu listaCatavencu;

  public EuroParliament euroParliament;

  public QvorumEuroRaport2007 euroRaport;
  public static CdepVotesPool cdepVotesPool;

  /** The legislature I am currently parsing / working on. */
  public static String ROOM = "";

  /** The year we are currently working on. */
  public static String YEAR = "";

  /**
   * If the data comes from a file, the location of that file is kept here.
   */
  public static String inputFile = "";

  // The type of parsing we are doing.
  private static final int CDEP_2008 = 0;
  private static final int SENAT_2008 = 1;

  public static final int CDEP = 0;
  public static final int SENAT = 1;

  /**
   * The constructor initializes stuff.
   * @param room The room (senate, cdep) that we are doing the current run for.
   * @param year The year for which we are loading cdepLaws and senators and stuff.
   */
  public Main(String room, String year) {
    Main.ROOM = room;
    Main.YEAR = year;

    // TODO(vivi): These should only be loaded on demand for each importer,
    // only when indeed necessary.
    //cdepLaws = new CdepLaws(this, year);
    //senateLaws = new SenateLaws(this, year);

    deputies = new Deputies(year);

    eurodeputies = new EuroDeputies(this);
    system = new ElectionsSystem2008(this);
    euroCandidates2009 = new EuroCandidates2009();
    listaCatavencu = new ListaCatavencu();
    euroParliament = new EuroParliament();
    euroRaport = new QvorumEuroRaport2007();
  }

  /**
   * Sets the value of the input file that will be used, if necessary. This
   * method is optional, only called for those types of data that need an
   * input file.
   * NOTE(vivi): Eventually, we should read all the data from input files, with
   * the common format. For now, we only do this for the Senate.
   * @param inputFile The location of the input file from which we read data.
   */
  public void setInputFile(String inputFile) {
    Main.inputFile = inputFile;
  }

  /**
   * The main method that starts the process.
   * @param type The type of run this is, CDEP, SENAT.
   * @param year The year for which we are doing a run. Mostly used to pass
   *     through to the run function since the year will be included in the
   *     type anyways.
   * TODO(vivi): This method needs some serious refactoring. :-)
   */
  public void run(int type, String year) throws IOException {
    // Load the deputies in the DeputyChamber
    //deputies.loadFromFile();

    // From the URL with one deputy's votes, get the list of final cdepLaws and
    // write them down somewhere
    //DbManager.deleteAllFromVotes();
    //cdepLaws.loadLawsFromSite();

    //cdepLaws.enhanceInfo();

    //deputies.enhanceInfo();
    //senators.loadFromTextFile();

    //senators.crawlFromSite();
    //DbManager.deleteAllFromSenateVotes();
    //senateLaws.getLawsFromFiles();
    // For each law, go get all the votes of each deputy.
    //eurodeputies.loadEuroFromFile();

    //system.run();

    // Sneaky trafic.ro in here. I should make the crawler a library.
    //TraficHack trafic = new TraficHack();
    //trafic.run();

    //euroCandidates2009.run();
    //listaCatavencu.run();
    //euroParliament.run();
    //euroRaport.run();

    VotesImporter votesImporter = new VotesImporter(Main.inputFile);

    switch(type) {
      case CDEP_2008: votesImporter.run(Main.CDEP, year); break;
      case SENAT_2008: votesImporter.run(Main.SENAT, year); break;
    }
  }

  public void consume(String page) {
    System.out.println(page);
  }

  /**
   * The main method.
   * @param args The default main parameter. 
   */
  public static void main(String args[]) throws IOException {
    if (args.length < 1) {
      System.out.println("Not enough arguments, don't know what to run.");
      System.exit(1);
    }

    Main main;

    // We expect an input file from which we get all the vote stuff.
    if (args.length < 2) {
      System.out.println("Second argument should be the input file.");
      System.exit(1);
    }
    if (args.length >= 3 && args[2].equals("nodb")) {
      DbManager.NOOP = true;
    }

    if (args[0].equals("cdep_2008")) {

      main = new Main("cdep", "2008");
      main.setInputFile(args[1]);
      main.run(CDEP_2008, "2008");

    } else if (args[0].equals("senat_2008")) {
      main = new Main("senat", "2008");
      main.setInputFile(args[1]);
      main.run(SENAT_2008, "2008");

    } else {
      System.out.println(args[0] + " is not something I know how to do.");
    }
  }
}
