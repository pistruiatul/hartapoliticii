package ro.vivi.pistruiatul;

/**
 * A structure that holds data about a law, as we get it from the import file.
 * These few bits of information should be generic enough and usually reflects
 * the structure of the laws database.
 */
public class GenericLaw {
  /**
   * The id that this law has in the database. We refer to this law by this
   * ID if we have to point to it. It's okay if this ID changes over time, but
   * we need to be careful to use this ID only when referring to stuff that gets
   * regenerated together, most specifically only in the votes table.
   */
  int id;

  /**
   * The link where this law can be found.
   */
  String link;

  /**
   * The identifier used by the legislature to refer to this law. Usually
   * something like "PL 10/2010". We use it to link to this law, as a short
   * name of sorts.
   */
  String number = null;

  /**
   * The long title of the law.
   */
  String title;
}
