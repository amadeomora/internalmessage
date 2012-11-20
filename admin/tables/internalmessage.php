<?php
defined('_JEXEC') or die( 'Restricted access' );

class TableInternalMessage extends JTable {
    /** @var int Primary key */
    var $id = null;
    /** @var int Default 0 */
    var $id_refered = 0;
    /** @var int */
    var $id_from = null;
    /** @var int */
    var $id_to = null;
    /** @var datetime */
    var $date = null;
    /** @var longtext */
    var $subject = null;
    /** @var longtext */
    var $text = null;
    /** @var varchar(256) */
    var $attachment_name = null;
    /** @var int Default 0 */
    var $attachment_size = 0;
    /** @var bool Default 0 */
    var $readed = 0;
    /** @var bool Default 0 */
    var $hidden_from = 0;
    /** @var bool Default 0 */
    var $hidden_to = 0;
    
    /**
     * @param database A database connector object
     */
    function __construct( &$db ) {
        parent::__construct( '#__internalmessage', 'id', $db );
    }
}
