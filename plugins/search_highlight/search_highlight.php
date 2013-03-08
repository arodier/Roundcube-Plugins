<?php


/**
 * Perform a simple text replace
 * This should be used when the string does not contain HTML
 * (off by default)
 */
define('STR_HIGHLIGHT_SIMPLE', 1);
 
/**
 * Only match whole words in the string
 * (off by default)
 */
define('STR_HIGHLIGHT_WHOLEWD', 2);
 
/**
 * Case sensitive matching
 * (off by default)
 */
define('STR_HIGHLIGHT_CASESENS', 4);
 
/**
 * Overwrite links if matched
 * This should be used when the replacement string is a link
 * (off by default)
 */
define('STR_HIGHLIGHT_STRIPLINKS', 8);
 
/**
 * Search Highlight
 *
 * Highlight searched text in the body of a message
 *
 * @version 1.0
 * @author Andre Rodier
 * @url https://github.com/arodier/Roundcube-Plugins
 */
class search_highlight extends rcube_plugin
{
  public $task = 'mail';
  private $map;
  private $styles;

  function init()
  {
    $this->add_hook('message_part_after', array($this, 'highlight'));
    $this->register_action('plugin.store_query', array($this, 'store_query'));

    $this->map = array();

    # Same styles as google highlighted words
    $this->styles = array(
      'color: #000; background-color: #ff6;',
      'color: #000; background-color: #aff;',
      'color: #000; background-color: #9f9;',
      'color: #000; background-color: #f99;',
      'color: #000; background-color: #f6f;',
      'color: #fff; background-color: #800;',
      'color: #fff; background-color: #0a0;',
      'color: #fff; background-color: #860;',
      'color: #fff; background-color: #049;',
      'color: #fff; background-color: #909;',
    );

    $this->include_script('search_highlight.js');
  }

  function store_query($args)
  {
    $query = $_POST['query'];
    setcookie('search_query', $query);
  }

  function highlight($args)
  {
    $return = array();
    $body = $args['body'];
    $isHtml = $args['type'] != 'plain';

    $words = array_filter(explode(' ', $_COOKIE['search_query']), array($this, 'filter_words'));
    if ( empty($words) ) return;

    $sn = 0;
    foreach ( $words as $word ) {

      $style = $this->styles[$sn];
      $replace = sprintf('<span style="%s">\1</span>', $style);
      $body = $this->highlight_html($body, $word, null, $replace);
      $sn++;
    }

    $return['body'] = $body;

    return $return;
  }

  private function filter_words($word)
  {
    $word = strtoupper($word);
    if ( !empty($word) && ($word != 'OR') && ($word != 'AND') )
      return $word;
  }


  /**
   * Highlight a string in text without corrupting HTML tags
   *
   * @author      Aidan Lister <aidan@php.net>
   * @version     3.1.1
   * @link        http://aidanlister.com/2004/04/highlighting-a-search-string-in-html-text/
   * @param       string          $text           Haystack - The text to search
   * @param       array|string    $needle         Needle - The string to highlight
   * @param       bool            $options        Bitwise set of options
   * @param       array           $highlight      Replacement string
   * @return      Text with needle highlighted
   */
  function highlight_html($text, $needle, $options = null, $highlight = null)
  {
      // Default highlighting
      if ($highlight === null) {
          $highlight = '<strong>\1</strong>';
      }
   
      // Select pattern to use
      if ($options & STR_HIGHLIGHT_SIMPLE) {
          $pattern = '#(%s)#';
          $sl_pattern = '#(%s)#';
      } else {
          $pattern = '#(?!<.*?)(%s)(?![^<>]*?>)#';
          $sl_pattern = '#<a\s(?:.*?)>(%s)</a>#';
      }
   
      // Case sensitivity
      if (!($options & STR_HIGHLIGHT_CASESENS)) {
          $pattern .= 'i';
          $sl_pattern .= 'i';
      }
   
      $needle = (array) $needle;
      foreach ($needle as $needle_s) {
          $needle_s = preg_quote($needle_s);
   
          // Escape needle with optional whole word check
          if ($options & STR_HIGHLIGHT_WHOLEWD) {
              $needle_s = '\b' . $needle_s . '\b';
          }
   
          // Strip links
          if ($options & STR_HIGHLIGHT_STRIPLINKS) {
              $sl_regex = sprintf($sl_pattern, $needle_s);
              $text = preg_replace($sl_regex, '\1', $text);
          }
   
          $regex = sprintf($pattern, $needle_s);
          $text = preg_replace($regex, $highlight, $text);
      }
   
      return $text;
  }
}

