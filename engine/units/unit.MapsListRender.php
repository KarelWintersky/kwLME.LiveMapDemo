<?php

class MapsListRender extends UnitPrototype {
    public $template;

    public function __construct( $route )
    {

    }

    public function run( )
    {
        $maps_list = [];

        $filename = PATH_STORAGE . '/list.json';
        if (is_file($filename)) {
            $json = json_decode( file_get_contents( $filename ) );

            foreach ($json->maps as $i => $map) {
                $alias = $map->alias;
                $title = $map->title;
                $key = str_replace('.', '~', $alias);

                $maps_list[ $key ] = [
                    'alias' =>  $alias,
                    'title' =>  $title
                ];
            }
        }


        /*$dir_listing = scandir(PATH_STORAGE);
        unset($dir_listing[ array_search('.', $dir_listing) ]);
        unset($dir_listing[ array_search('..', $dir_listing) ]);
        unset($dir_listing[ array_search('list.json', $dir_listing) ]);

        foreach ($dir_listing as $folder) {
            $key = str_replace('.', '~', $folder);

            if (!array_key_exists($key, $maps_list)) {

                $fn = PATH_STORAGE . $folder . '/index.json';

                if (is_file( $fn )) {
                    $json = json_decode( file_get_contents( $fn ));


                    $maps_list[ $key ] = [
                        'alias'     =>  $folder,
                        'title'     =>  $json->title
                    ];
                }
            }
        }*/

        // $this->template = new Template('maps.list.html', '$/templates');
        // $this->template->set('/maps_list', $maps_list);
        return $maps_list;
    }

    /**
     *
     * @return mixed
     */
    public function content()
    {
    }
}
