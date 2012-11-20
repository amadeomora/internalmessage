<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * General Controller of HelloWorld component
 */
class InternalMessageController extends JController
{
        /**
         * display task
         *
         * @return void
         */
        function display($cachable = false, $other = false) 
        {
                // set default view if not set
                JRequest::setVar('view', JRequest::getCmd('view', 'internalmessages'));
 
                // call parent behavior
                parent::display($cachable);
        }
}
