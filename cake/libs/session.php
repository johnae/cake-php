<?php
/* SVN FILE: $Id$ */

/**
 * Short description for file.
 *
 * Long description for file
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
 * @subpackage   cake.cake.libs
 * @since        CakePHP v .0.10.0.1222
 * @version      $Revision$
 * @modifiedby   $LastChangedBy$
 * @lastmodified $Date$
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Short description for file.
 *
 * Long description for file
 *
 * @package    cake
 * @subpackage cake.cake.libs
 * @since      CakePHP v .0.10.0.1222
 */
class CakeSession extends Object
{
/**
 * Enter description here...
 *
 * @var unknown_type
 */
     var $valid      = false;
/**
 * Enter description here...
 *
 * @var unknown_type
 */
    var $error      = false;
/**
 * Enter description here...
 *
 * @var unknown_type
 */
    var $userAgent  = false;
/**
 * Enter description here...
 *
 * @var unknown_type
 */
    var $path       = false;
/**
 * Enter description here...
 *
 * @var unknown_type
 */
    var $lastError  = null;
/**
 * Enter description here...
 *
 * @var unknown_type
 */
    var $sessionId     = null;
/**
 * Enter description here...
 *
 * @var unknown_type
 */
    var $security     = null;
/**
 * Enter description here...
 *
 * @var unknown_type
 */
    var $time       = false;
/**
 * Enter description here...
 *
 * @var unknown_type
 */
    var $sessionTime = false;
/**
 * Enter description here...
 *
 * @return unknown
 */
    function __construct($base = null)
    {
        $this->host = env('HTTP_HOST');

        if (empty($base))
        {
            $this->path = '/';
        }
        else
        {
            $this->path = $base;
        }

        if (strpos($this->host, ':') !== false)
        {
            $this->host = substr($this->host,0, strpos($this->host, ':'));
        }

        if(env('HTTP_USER_AGENT') != null)
        {
            $this->userAgent = md5(env('HTTP_USER_AGENT').CAKE_SESSION_STRING);
        }
        else
        {
            $this->userAgent = "";
        }

        $this->time = time();
        $this->sessionTime = $this->time + (Security::inactiveMins() * 60);
        $this->security = CAKE_SECURITY;
        $this->_initSession();
        $this->_begin();
        parent::__construct();
    }

/**
 * Enter description here...
 *
 * @param unknown_type $name
 * @return unknown
 */
    function checkSessionVar($name)
    {
        $expression = "return isset(".$this->_sessionVarNames($name).");";
        return eval($expression);
    }

/**
 * Enter description here...
 *
 * @param unknown_type $name
 * @return unknown
 */
    function delSessionVar($name)
    {
        if($this->checkSessionVar($name))
        {
            $var = $this->_sessionVarNames($name);
            eval("unset($var);");
            return true;
        }
        $this->_setError(2, "$name doesn't exist");
        return false;
    }

/**
 * Enter description here...
 *
 * @param unknown_type $errorNumber
 * @return unknown
 */
    function getError($errorNumber)
    {
        if(!is_array($this->error) || !array_key_exists($errorNumber, $this->error))
        {
            return false;
        }
        else
        {
        return $this->error[$errorNumber];
        }
    }

/**
 * Enter description here...
 *
 * @return unknown
 */
    function getLastError()
    {
        if($this->lastError)
        {
            return $this->getError($this->lastError);
        }
        else
        {
            return false;
        }
    }

/**
 * Enter description here...
 *
 * @return unknown
 */
    function isValid()
    {
        return $this->valid;
    }

/**
 * Enter description here...
 *
 * @param unknown_type $name
 * @return unknown
 */
    function readSessionVar($name = null)
    {
        if(is_null($name))
        {
            return $this->returnSessionVars();
        }

        if($this->checkSessionVar($name))
        {
            $result = eval("return ".$this->_sessionVarNames($name).";");
            return $result;
        }
        $this->_setError(2, "$name doesn't exist");
        return false;
    }

/**
 * Enter description here...
 *
 * @param unknown_type $name
 * @return unknown
 */
    function returnSessionVars()
    {
        if(!empty($_SESSION))
        {
            $result = eval("return \$_SESSION;");
            return $result;
        }
        $this->_setError(2, "No Session vars set");
        return false;
    }

/**
 * Enter description here...
 *
 * @param unknown_type $name
 * @param unknown_type $value
 */
    function writeSessionVar($name, $value)
    {
        $expression = $this->_sessionVarNames($name);
        $expression .= " = \$value;";
        eval($expression);
    }

/**
 * Enter description here...
 *
 * @access private
 */
    function _begin()
    {
        session_cache_limiter("must-revalidate");
        session_start();
        $this->_new();
    }

/**
 * Enter description here...
 *
 * @access private
 */
    function _close()
    {
        echo "<pre>";
        echo "CakeSession::_close() Not Implemented Yet";
        echo "</pre>";
        die();
    }

/**
 * Enter description here...
 *
 * @access private
 */
    function _destroy()
    {
        echo "<pre>";
        echo "CakeSession::_destroy() Not Implemented Yet";
        echo "</pre>";
        die();
    }
/**
 * Enter description here...
 *
 * @access private
 */
    function _destroyInvalid()
    {
        $sessionpath = session_save_path();
        if (empty($sessionpath))
        {
            $sessionpath = "/tmp";
        }
        if (isset($_COOKIE[session_name()]))
        {
            setcookie(CAKE_SESSION_COOKIE, '', time()-42000, $this->path);
        }
        $file = $sessionpath.DS."sess_".session_id();
        @unlink($file);
        $this->__construct($this->path);
    }

/**
 * Enter description here...
 *
 * @access private
 */
    function _gc()
    {
        echo "<pre>";
        echo "CakeSession::_gc() Not Implemented Yet";
        echo "</pre>";
        die();
    }

/**
 * Enter description here...
 *
 * @access private
 */
    function _initSession()
    {
        if (function_exists('session_write_close'))
        {
            session_write_close();
        }

        switch ($this->security)
        {
            case 'high':
                $this->cookieLifeTime = 0;
                ini_set('session.referer_check', $this->host);
            break;
            case 'medium':
                $this->cookieLifeTime = 7 * 86400;
            break;
            case 'low':
            default :
                $this->cookieLifeTime = 788940000;
            break;
        }

        switch (CAKE_SESSION_SAVE)
        {
            case 'cake':
                ini_set('session.use_trans_sid', 0);
                ini_set('url_rewriter.tags', '');
                ini_set('session.serialize_handler', 'php');
                ini_set('session.use_cookies', 1);
                ini_set('session.name', CAKE_SESSION_COOKIE);
                ini_set('session.cookie_lifetime', $this->cookieLifeTime);
                ini_set('session.cookie_path', $this->path);
                ini_set('session.gc_probability', 1);
                ini_set('session.gc_maxlifetime', Security::inactiveMins() * 60);
                ini_set('session.auto_start', 0);
                ini_set('session.save_path', TMP.'sessions');
            break;
            case 'database':
                ini_set('session.use_trans_sid', 0);
                ini_set('url_rewriter.tags', '');
                ini_set('session.save_handler', 'user');
                ini_set('session.serialize_handler', 'php');
                ini_set('session.use_cookies', 1);
                ini_set('session.name', CAKE_SESSION_COOKIE);
                ini_set('session.cookie_lifetime', $this->cookieLifeTime);
                ini_set('session.cookie_path', $this->path);
                ini_set('session.gc_probability', 1);
                ini_set('session.gc_maxlifetime', Security::inactiveMins() * 60);
                ini_set('session.auto_start', 0);
                session_set_save_handler(array('CakeSession', '_open'),
                                         array('CakeSession', '_close'),
                                         array('CakeSession', '_read'),
                                         array('CakeSession', '_write'),
                                         array('CakeSession', '_destroy'),
                                         array('CakeSession', '_gc'));
            break;
            case 'php':
                ini_set('session.name', CAKE_SESSION_COOKIE);
                ini_set('session.cookie_lifetime', $this->cookieLifeTime);
                ini_set('session.cookie_path', $this->path);
                ini_set('session.gc_probability', 1);
                ini_set('session.gc_maxlifetime', Security::inactiveMins() * 60);
            break;
            default :
                $config = CONFIGS.CAKE_SESSION_SAVE.'.php';
                if(is_file($config))
                {
                    require_once($config);
                }
                else
                {
                    ini_set('session.name', CAKE_SESSION_COOKIE);
                    ini_set('session.cookie_lifetime', $this->cookieLifeTime);
                    ini_set('session.cookie_path', $this->path);
                    ini_set('session.gc_probability', 1);
                    ini_set('session.gc_maxlifetime', Security::inactiveMins() * 60);
                }
            break;
        }
    }

/**
 * Enter description here...
 *
 * @access private
 *
 */
    function _new()
    {
        if($this->readSessionVar("Config"))
        {
            if($this->userAgent == $this->readSessionVar("Config.userAgent") &&
            $this->time <= $this->readSessionVar("Config.time"))
            {
                $this->writeSessionVar("Config.time", $this->sessionTime);
                $this->valid = true;
            }
            else
            {
                $this->valid = false;
                $this->_setError(1, "Session Highjacking Attempted !!!");
                $this->_destroyInvalid();
            }
        }
        else
        {
            srand((double)microtime() * 1000000);
            $this->writeSessionVar('Config.rand', rand());
            $this->writeSessionVar("Config.time", $this->sessionTime);
            $this->writeSessionVar("Config.userAgent", $this->userAgent);
            $this->valid = true;
            $this->_setError(1, "Session is valid");
        }

        if($this->security == 'high')
        {
            $this->_regenerateId();
        }
        header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
    }

/**
 * Enter description here...
 *
 * @access private
 *
 */
    function _open()
    {
        echo "<pre>";
        echo "CakeSession::_open() Not Implemented Yet";
        echo "</pre>";
        die();
    }

/**
 * Enter description here...
 *
 * @access private
 *
 */
    function _read()
    {
        echo "<pre>";
        echo "CakeSession::_read() Not Implemented Yet";
        echo "</pre>";
        die();
    }

/**
 * Enter description here...
 *
 *
 * @access private
 *
 */
    function _regenerateId()
    {
        $oldSessionId = session_id();
        $sessionpath = session_save_path();
        if (empty($sessionpath))
        {
            $sessionpath = "/tmp";
        }
        if (isset($_COOKIE[session_name()]))
        {
            setcookie(CAKE_SESSION_COOKIE, '', time()-42000, $this->path);
        }
        session_regenerate_id();
        $newSessid = session_id();
        $file = $sessionpath.DS."sess_$oldSessionId";
        @unlink($file);
        $this->_initSession();
        session_id($newSessid);
        session_start();
    }

/**
 * Enter description here...
 *
 * @access private
 *
 */
    function _renew()
    {
        $this->_regenerateId();
    }

/**
 * Enter description here...
 *
 * @param unknown_type $name
 * @return unknown
 * @access private
 */
    function _sessionVarNames($name)
    {
        if(is_string($name))
        {
            if(strpos($name, "."))
            {
                $names = explode(".", $name);
            }
            else
            {
                $names = array($name);
            }
            $expression = $expression = "\$_SESSION";

            foreach($names as $item)
            {
                $expression .= is_numeric($item) ? "[$item]" : "['$item']";
            }
            return $expression;
        }
        $this->setError(3, "$name is not a string");
        return false;
    }

/**
 * Enter description here...
 *
 * @param unknown_type $errorNumber
 * @param unknown_type $errorMessage
 * @access private
 */
    function _setError($errorNumber, $errorMessage)
    {
        if($this->error === false)
        {
            $this->error = array();
        }

        $this->error[$errorNumber] = $errorMessage;
        $this->lastError = $errorNumber;
    }

/**
 * Enter description here...
 *
 * @access private
 */
    function _write()
    {
        echo "<pre>";
        echo "CakeSession::_write() Not Implemented Yet";
        echo "</pre>";
        die();
    }
}
?>