<?php

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

  function init()
  {
    $this->add_hook('message_part_after', array($this, 'highlight'));
    $this->register_action('plugin.store_query', array($this, 'store_query'));

    $this->map = array();

    $this->include_script('search_highlight.js');
  }

  function store_query($args)
  {
    setcookie('search_query', $_POST['query']);
  }

  function highlight($args)
  {
    $styles = array(
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

    $words = explode(' ', $_COOKIE['search_query']);
    $wn = 0;
    foreach ( $words as $word ) {
      if ( $word == 'AND' || $word == 'OR' || empty($word) ) continue;
      $this->map[$word] = html::span(array('style' => $styles[$wn]), $word);
      $wn++;
    }

    if (  count($this->map) == 0 ) return;

    if ( $args['type'] == 'plain' )
      return array('body' => str_ireplace(array_keys($this->map), array_values($this->map), $args['body']));
    else
      return array('body' => str_ireplace(array_keys($this->map), array_values($this->map), $args['body']));
  }
}
