<?php

namespace View;

class View
{
    public function fetchPartial($template, $params = array())
    {
        extract($params);
        ob_start();
        include VIEWS_BASEDIR . $template . '.phtml';
        return ob_get_clean();
    }

    public function renderPartial($template, $params = array())
    {
        echo $this->fetchPartial($template, $params);
    }

    public function fetch($template, $params = array())
    {
        $content = $this->fetchPartial($template, $params);
        return $this->fetchPartial('template', array('content' => $content));
    }

    public function render($template, $params = array())
    {
        echo $this->fetch($template, $params);
    }
}