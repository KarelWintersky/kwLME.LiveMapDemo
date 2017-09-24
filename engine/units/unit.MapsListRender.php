<?php

class MapsListRender extends UnitPrototype {
    public $template;

    public function __construct( $route )
    {

    }

    public function run( $route )
    {
        // читаем список каталогов в /storage и оформляем это как список
        $dir = scandir(PATH_STORAGE);
        unset($dir[ array_search('.', $dir)]);
        unset($dir[ array_search('..', $dir)]);
        unset($dir[ array_search('template.json', $dir)]);

        $this->template = new Template('maps.list.html', '$/templates');
        $this->template->set('/mapslist', $dir);
    }

    /**
     *
     * @return mixed
     */
    public function content()
    {
        return $this->template->render();
    }
}