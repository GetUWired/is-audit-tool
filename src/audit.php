<?php

namespace ISAudit;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Audit Class
 */
class Audit extends \Infusionsoft\Infusionsoft
{
    /**
     * @var A List of all Tags in our App
     */
    private $tags;

    function __construct($config = [])
    {
        parent::__construct($config);
    }

    function pullTags(){
        /* TODO: Pull a list of all tags */
        $table = 'ContactGroup';
        $limit = 100;
        $page = 0;
        $orderBy = 'Id';
        $ascending = true;

        $returnFields = array('GroupName');
        $queryData = array('Id' => '%');
        $selectedFields = array('GroupName', 'Id');
        $this->tags = $this->data()->query($table, $limit, $page, $queryData, $selectedFields, $orderBy, $ascending);
    }
    function showTags(){
        return $this->tags;
    }
        
        
         /* TODO: Get tag applied table */
        
         /* TODO: Get Contacts table */
        
        
         /* TODO: Compare to see when last used, how many contacts have that tag etc */
        
}
