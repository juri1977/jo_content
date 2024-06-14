<?php
namespace JO\JoContent\Userfunc;

class Sessionvar
{
    public function getSessionValues($session_name = null)
    {
        $session_items = [];
        if (null != $session_name) {
            if (!session_id()) @session_start();
            if ($_SESSION[$session_name]) $session_items = $_SESSION[$session_name];
        }
        return $session_items;
    }

    public function getSessiondata(string $content, array $conf): string
    {
        $return = '';
        $session_var_name = "lukidatestapisearch"; //    Name der Session, in der die Suchparameter gespeichert werden
        $searcharray = $this->getSessionValues($session_var_name);
        if (!empty($searcharray['content']['fulltext'])) $return = $searcharray['content']['fulltext'][0];
        return $return;
    }
}
