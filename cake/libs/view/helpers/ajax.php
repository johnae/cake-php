<?php
/* SVN FILE: $Id$ */

/**
 * Helper for AJAX operations.
 *
 * Helps doing AJAX using the Prototype library.
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c) 2005, Cake Software Foundation, Inc.
 *                     1785 E. Sahara Avenue, Suite 490-204
 *                     Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright    Copyright (c) 2005, Cake Software Foundation, Inc.
 * @link         http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package      cake
 * @subpackage   cake.cake.libs.view.helpers
 * @since        CakePHP v 0.10.0.1076
 * @version      $Revision$
 * @modifiedby   $LastChangedBy$
 * @lastmodified $Date$
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */


/**
 * AjaxHelper helper library.
 *
 * Helps doing AJAX using the Prototype library.
 *
 * @package    cake
 * @subpackage cake.cake.libs.view.helpers
 * @since      CakePHP v 0.10.0.1076
 *
 */
class AjaxHelper extends Helper
{

/**
     * Included helpers.
     *
     * @var array
 */
    var $helpers = array('Html', 'Javascript');

/**
     * Names of Javascript callback functions.
     *
     * @var array
 */
    var $callbacks = array('uninitialized', 'loading', 'loaded', 'interactive', 'complete');

/**
     * Names of AJAX options.
     *
     * @var array
 */
    var $ajaxOptions = array('method','position','form','parameters','evalScripts', 'asynchronous', 'onComplete', 'onUninitialized', 'onLoading', 'onLoaded', 'onInteractive');

/**
     * Options for draggable.
     *
     * @var array
 */
    var $dragOptions = array('handle', 'revert', 'constraint', 'change');

/**
     * Options for droppable.
     *
     * @var array
 */
    var $dropOptions = array('accept', 'containment', 'overlap', 'greedy', 'hoverclass', 'onHover', 'onDrop');

/**
     * Options for sortable.
     *
     * @var array
 */
    var $sortOptions = array('tag', 'only', 'overlap', 'constraint', 'containment', 'handle', 'hoverClass', 'ghosting', 'dropOnEmpty', 'onUpdate', 'onChange');

    var $__ajaxKeys = array('url', 'with', 'update', 'loading', 'loaded', 'interactive', 'complete', 'type', 'confirm', 'condition', 'before', 'after', 'fallback');

/**
 * Returns link to remote action
 *
 * Returns a link to a remote action defined by <i>options[url]</i>
 * (using the urlFor format) that's called in the background using
 * XMLHttpRequest. The result of that request can then be inserted into a
 * DOM object whose id can be specified with <i>options[update]</i>.
 * Usually, the result would be a partial prepared by the controller with
 * either renderPartial or renderPartialCollection.
 *
 * Examples:
 * <code>
 *  link("Delete this post",
 *          array("update" => "posts", "url" => "delete/{$postid->id}"));
 *  link(imageTag("refresh"),
 *        array("update" => "emails", "url" => "list_emails" ));
 * </code>
 *
 * By default, these remote requests are processed asynchronous during
 * which various callbacks can be triggered (for progress indicators and
 * the likes).
 *
 * Example:
 * <code>
 *   linkToRemote (word,
 *       array("url" => "undo", "n" => word_counter),
 *       array("complete" => "undoRequestCompleted(request)"));
 * </code>
 *
 * The callbacks that may be specified are:
 *
 * - <i>loading</i>::       Called when the remote document is being
 *                           loaded with data by the browser.
 * - <i>loaded</i>::        Called when the browser has finished loading
 *                           the remote document.
 * - <i>interactive</i>::   Called when the user can interact with the
 *                           remote document, even though it has not
 *                           finished loading.
 * - <i>complete</i>::      Called when the XMLHttpRequest is complete.
 *
 * If you for some reason or another need synchronous processing (that'll
 * block the browser while the request is happening), you can specify
 * <i>options[type] = synchronous</i>.
 *
 * You can customize further browser side call logic by passing
 * in Javascript code snippets via some optional parameters. In
 * their order of use these are:
 *
 * - <i>confirm</i>::      Adds confirmation dialog.
 * -<i>condition</i>::    Perform remote request conditionally
 *                          by this expression. Use this to
 *                          describe browser-side conditions when
 *                          request should not be initiated.
 * - <i>before</i>::       Called before request is initiated.
 * - <i>after</i>::        Called immediately after request was
 *                       initiated and before <i>loading</i>.
 *
 * @param string $title         Title of link
 * @param array $options         Options for JavaScript function
 * @return string                 HTML code for link to remote action
 */
    function link($title, $href = null, $options = array(), $confirm = null)
    {
        if (!isset($href))
        {
            $href = $title;
        }

        $options['url'] = $href;
        if (isset($confirm))
        {
            $options['confirm'] = $confirm;
            unset($confirm);
        }

        $htmlOptions = $this->__getHtmlOptions($options);
        if (empty($options['fallback']) || !isset($options['fallback']))
        {
            $options['fallback'] = $href;
        }
        if (isset($options['id']))
        {
            $htmlOptions['onclick'] = ' return false;';
            return $this->Html->link($title, $href, $htmlOptions) . $this->Javascript->event("$('{$options['id']}')", "click", "function() {" . $this->remoteFunction($options) . "; return true; }");
        }
        else
        {
            $htmlOptions['onclick'] = $this->remoteFunction($options) . '; return false;';
            return $this->Html->link($title, $href, $htmlOptions);
        }
    }

    function linkToRemote ($title, $options = array(), $html_options = array())
    {
        //trigger_error('Deprecated function: use AjaxHelper::link', E_USER_WARNING);
        $href = '#';
        if (!empty($options['fallback']) && isset($options['fallback']))
        {
            $href = $options['fallback'];
        }
        if (isset($html_options['id']))
        {
            return $this->Html->link($title, $href, $html_options) . $this->Javascript->event("$('{$html_options['id']}')", "click", "function() {" . $this->remoteFunction($options) . "; return true; }");
        }
        else
        {
            $html_options['onclick'] = $this->remoteFunction($options);
            return $this->Html->link($title, $href, $html_options);
        }
    }
/**
  * Creates JavaScript function for remote AJAX call
  *
  * This function creates the javascript needed to make a remote call
  * it is primarily used as a helper for linkToRemote.
  *
  * @see linkToRemote() for docs on options parameter.
  *
  * @param array $options options for javascript
  * @return string html code for link to remote action
  */
    function remoteFunction ($options = null)
    {
        $javascript_options = $this->__optionsForAjax($options);
        $func = isset($options['update']) ? "new Ajax.Updater('{$options['update']}'," : "new Ajax.Request(";

        $func .= "'" . $this->Html->url(isset($options['url']) ? $options['url'] : "") . "'";
        $func .= "$javascript_options)";

        if (isset($options['before']))
        {
            $func = "{$options['before']}; $function";
        }
        if (isset($options['after']))
        {
            $func = "$func; {$options['after']};";
        }
        if (isset($options['condition']))
        {
            $func = "if ({$options['condition']}) { $func; }";
        }
        if (isset($options['confirm']))
        {
            $func = "if (confirm('" . $this->Javascript->escapeString($options['confirm']) . "')) { $func; } else { return false; }";
        }
        return $func;
    }

/**
  * Periodically call remote url via AJAX.
  *
  * Periodically calls the specified url (<i>options[url]</i>) every <i>options[frequency]</i> seconds (default is 10).
  * Usually used to update a specified div (<i>options[update]</i>) with the results of the remote call.
  * The options for specifying the target with url and defining callbacks is the same as linkToRemote.
  *
  * @param array $options         Callback options
  * @return string                 Javascript code
  */
    function remoteTimer ($options = null)
    {
        $frequency = (isset($options['frequency']))? $options['frequency'] : 10;
        $code = "new PeriodicalExecuter(function() {" . $this->remoteFunction($options) . "}, $frequency)";
        return $this->Javascript->codeBlock($code);
    }

/**
  * Returns form tag that will submit using Ajax.
  *
  * Returns a form tag that will submit using XMLHttpRequest in the background instead of the regular
  * reloading POST arrangement. Even though it's using Javascript to serialize the form elements, the form submission
  * will work just like a regular submission as viewed by the receiving side (all elements available in params).
  * The options for specifying the target with :url and defining callbacks is the same as link_to_remote.
  *
  * @param string $id                 Form id
  * @param array $html_options        HTML tag options
  * @param array $options             Callback options
  * @return string                     JavaScript code
  */
    function form($id, $options = array())
    {
        $htmlOptions = $this->__getHtmlOptions($options);
        $htmlOptions['id'] = $id;
        $htmlOptions['onsubmit'] = "return false;";
        return $this->Html->formTag(null, "post", $htmlOptions) . $this->Javascript->event("$('$id')", "submit", "function(){" . $this->remoteFunction($options) . ";}");
    }

/**
  * Returns a button input tag that will submit using Ajax
  *
  * Returns a button input tag that will submit form using XMLHttpRequest in the background instead of regular
  * reloading POST arrangement. <i>options</i> argument is the same as in <i>form_remote_tag</i>
  *
  * @param string $name         Input button name
  * @param string $value         Input button value
  * @param array $html_options    HTML options
  * @param array $options         Callback options
  * @return string                 Ajaxed input button
  */
    function submit ($name, $value, $options = array())
    {
        $htmlOptions = $this->__getHtmlOptions($options);
        $htmlOptions['type'] = 'button';
        $htmlOptions['name'] = $name;
        $htmlOptions['value'] = $value;

        if (!isset($options['with']))
        {
            $options['with'] = 'Form.serialize(this.form)';
        }

        $htmlOptions['onclick'] = $this->remoteFunction($options) . "; return false;";
        return $this->Html->tag('input', $htmlOptions);
    }

/**
  * Observe field and call ajax on change.
  *
  * Observes the field with the DOM ID specified by <i>field_id</i> and makes
  * an Ajax when its contents have changed.
  *
  * Required +options+ are:
  * - <i>frequency</i>:: The frequency (in seconds) at which changes to
  *                       this field will be detected.
  * - <i>url</i>::       @see urlFor() -style options for the action to call
  *                       when the field has changed.
  *
  * Additional options are:
  * - <i>update</i>::    Specifies the DOM ID of the element whose
  *                       innerHTML should be updated with the
  *                       XMLHttpRequest response text.
  * - <i>with</i>::      A Javascript expression specifying the
  *                       parameters for the XMLHttpRequest. This defaults
  *                       to Form.Element.serialize('$field_id'), which can be
  *                       accessed from params['form']['field_id'].
  *
  * Additionally, you may specify any of the options documented in
  * @see linkToRemote().
  *
  * @param string $field_id         DOM ID of field to observe
  * @param array $options             ajax options
  * @return string                     ajax script
  */
    function observeField ($field_id, $options = array())
    {
        if (!isset($options['with']))
        {
            $options['with'] = "Form.Element.serialize('$field_id')";
        }
        return $this->Javascript->codeBlock($this->_buildObserver('Form.Element.Observer', $field_id, $options));
    }

/**
  * Observe entire form and call ajax on change.
  *
  * Like @see observeField(), but operates on an entire form identified by the
  * DOM ID <b>form_id</b>. <b>options</b> are the same as <b>observe_field</b>, except
  * the default value of the <i>with</i> option evaluates to the
  * serialized (request string) value of the form.
  *
  * @param string $field_id     DOM ID of field to observe
  * @param array $options         ajax options
  * @return string                 ajax script
  */
    function observeForm ($field_id, $options = array())
    {
        if (!isset($options['with']))
        {
            $options['with'] = 'Form.serialize(this.form)';
        }
        return $this->Javascript->codeBlock($this->_buildObserver('Form.Observer', $field_id, $options));
    }

/**
  * Create a text field with Autocomplete.
  *
  * Creates an autocomplete field with the given ID and options.
  *
  * options['with'] defaults to "Form.Element.serialize('$field_id')",
  * but can be any valid javascript expression defining the
  *
  * @param string $field_id         DOM ID of field to observe
  * @param array $options             ajax options
  * @return string                     ajax script
  */
    function autoComplete ($field, $url = "", $options = array())
    {
        if (!isset($options['id']))
        {
            $options['id'] = r("/", "_", $field);
        }

        $htmlOptions = $options;
        $ajaxOptions = array('with', 'asynchronous', 'synchronous', 'method', 'position', 'form');

        $htmlOptions['autocomplete'] = "off";

        foreach($ajaxOptions as $key)
        {
            if(isset($options[$key]))
            {
                $ajaxOptions[$key] = $options[$key];
            }
            else
            {
                unset($ajaxOptions[$key]);
            }
        }
        if(!isset($options['class']))
        {
           $options['class'] = "auto_complete";
        }

        $divOptions = array('id' => $options['id'] . "_autoComplete", 'class' => $options['class']);

        return $this->Html->input($field, $htmlOptions) .
          $this->Html->tag("div", $divOptions, true) . "</div>" .
          $this->Javascript->codeBlock("new Ajax.Autocompleter('" . $options['id'] . "', '" .
          $divOptions['id'] . "', '" . $this->Html->url($url) . "'" . $this->__optionsForAjax($ajaxOptions) . ");");
    }

/**
 * Enter description here...
 *
 * @param unknown_type $id
 * @param unknown_type $options
 * @return unknown
 */
    function drag($id, $options = array())
    {
        $options = $this->_optionsForDraggable($options);
        return $this->Javascript->codeBlock("new Draggable('$id'$options);");
    }

/**
     * Private helper method to return an array of options for draggable.
     *
     * @param array $options
     * @return array
 */
    function _optionsForDraggable ($options)
    {
        $options = $this->_optionsToString($options, array('handle','constraint'));
        return $this->_buildOptions($options, $this->dragOptions);
    }

/**
  * For a reference on the options for this function, check out
  * http://wiki.script.aculo.us/scriptaculous/show/Droppables.add
  *
  */
    function drop($id, $options = array())
    {
        $options = $this->_optionsForDroppable($options);
        return $this->Javascript->codeBlock("Droppables.add('$id'$options);");
    }

/**
 * Enter description here...
 *
 * @param unknown_type $options
 * @return unknown
 */
    function _optionsForDroppable ($options)
    {
        $options = $this->_optionsToString($options, array('accept','overlap','hoverclass'));
        return $this->_buildOptions($options, $this->dropOptions);
    }

/**
 * Enter description here...
 *
 * @param unknown_type $id
 * @param unknown_type $options
 * @param unknown_type $ajaxOptions
 */
    function dropRemote($id, $options = array(), $ajaxOptions = array())
    {
        $options['onDrop'] = "function(element){" . $this->remoteFunction($ajaxOptions) . "}";
    }

/**
  * Makes a list or group of floated objects sortable.
  *
  *
  * @param string $id DOM ID of parent
  * @param array $options Array of options to control sort.http://wiki.script.aculo.us/scriptaculous/show/Sortable.create
  * @link http://wiki.script.aculo.us/scriptaculous/show/Sortable.create
  */

    function sortable($id, $options = array())
    {
        if (!empty($options['url']))
        {
            $options['with'] = "Sortable.serialize('$id')";
            $options['onUpdate'] = 'function(sortable){' . $this->remoteFunction($options).'}';
        }
        $options = $this->__optionsForSortable($options);
        return $this->Javascript->codeBlock("Sortable.create('$id'$options);");
    }

/**
 * Private method; generates sortables code from array options
 *
 * @param unknown_type $options
 * @return unknown
 */
    function __optionsForSortable ($options)
    {
        $options = $this->_optionsToString($options, array('handle','tag','constraint','ghosting'));
        return $this->_buildOptions($options, $this->sortOptions);
    }

/**
      * Private helper function for Javascript.
  *
  */
    function __optionsForAjax ($options = array())
    {
        $js_options = $this->_buildCallbacks($options);
        $js_options['asynchronous'] = 'true';
        $js_options['evalScripts'] = 'true';
        $options = $this->_optionsToString($options, array('method'));

        foreach($options as $key => $value)
        {
            switch($key)
            {
                case 'type':
                    $js_options['asynchronous'] = ($value == 'synchronous') ? 'false' : 'true';
                break;

                case 'position':
                    $js_options['insertion'] = "Insertion." . Inflector::camelize($options['position']);
                break;

                case 'with':
                    $js_options['parameters'] = $options['with'];
                break;

                case 'form':
                    $js_options['parameters'] = 'Form.serialize(this)';
                break;
            }
        }

        return $this->_buildOptions($js_options, $this->ajaxOptions);
    }


    function __getHtmlOptions($options)
    {
        foreach($this->__ajaxKeys as $key)
        {
            unset($options[$key]);
        }
        return $options;
    }

/**
 * Enter description here...
 *
 * @param unknown_type $options
 * @param unknown_type $acceptable
 * @return unknown
 */
    function _buildOptions ($options, $acceptable) {
        if(is_array($options))
        {
            $out = array();
            foreach ($options as $k => $v)
            {
                if(in_array($k, $acceptable))
                {
                    $out[] = "$k:$v";
                }
            }
            $out = join(', ', $out);
            $out = ', {' . $out . '}';
            return $out;
        }
        else
        {
          return false;
        }
    }

/**
     * Return JavaScript text for an observer...
     *
     * @param string $klass Name of JavaScript class
     * @param string $name
     * @param array $options
     * @return string Formatted JavaScript
 */
    function _buildObserver ($klass, $name, $options=null)
    {
        if(!isset($options['with']) && isset($options['update']))
        {
            $options['with'] = 'value';
        }

        $callback = $this->remoteFunction($options);
        $javascript = "new $klass('$name', ";
        $javascript .= (isset($options['frequency']) ? $options['frequency'] : 2) . ", function(element, value) {";
        $javascript .= "$callback})";
        return $javascript;
    }

/**
     * Enter description here... Return JavaScript text for all callbacks...
     *
     * @param array $options
     * @return array
 */
    function _buildCallbacks($options)
    {
        $callbacks = array();
        foreach($this->callbacks as $callback)
        {
            if(isset($options[$callback]))
            {
                $name = 'on' . ucfirst($callback);
                $code = $options[$callback];
                $callbacks[$name] = "function(request){".$code."}";
            }
        }
        return $callbacks;
    }

/**
 * Enter description here...
 *
 * @param unknown_type $options
 * @param unknown_type $stringOpts
 * @return unknown
 */
    function _optionsToString ($options, $stringOpts = array())
    {
        foreach ($stringOpts as $option)
        {
           if(isset($options[$option]) && !$options[$option][0] != "'")
           {
               $options[$option] = "'{$options[$option]}'";
           }
        }
        return $options;
    }

}
?>