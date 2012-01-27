package ro.vivi.pistruiatul;

/**
 * A page consumer is a guy that I can register to the crawler of pages to
 * call back when a page is done (considering that I'm doing this in an async
 * way).
 *
 * @author vivi
 */
public interface PageConsumer {

  /**
   * Tells the consumer that the page that he needed is now here and is ready
   * to be consumed.
   * @param page A string with the contents of the page.
   */
  public void consume(String page);
}
