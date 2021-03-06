<?php

/**
 * Class DBConnectionLite v 1.2
 * Используется вариативный конфиг (либо инстанс INI_Config и ключ подключения, либо ключ подключения для статика)
 * based on DBConnection ver 1.8
 */
class DBConnectionLite extends \PDO
{
    const USED_CONFIG_STATIC  = 0;
    const USED_CONFIG_DYNAMIC = 1;

    private $database_settings = array();
    private $pdo_connection;
    private $table_prefix = '';
    public $is_connected = FALSE;

    /**
     * @param $key_connection - если передана пустая строка - используем секцию, равную имени host/server (что может быть проще в некоторых случаях)
     * @param INI_Config|NULL $config_argv
     *
     * Конфиг может быть передан как:
     * * массив параметров (результат загрузки из определенной секции конфига)
     * * инстанс INI_Config класса
     * * NULL означает, что в проекте используется StaticConfig
     */
    public function __construct($key_connection, $config_argv = NULL)
    {
        $config_type = -1;
        $database_settings = array();
        $key_connection = trim($key_connection);

        // проверяем тип переданного конфига
        if (is_array($config_argv)) {
            // Конфиг передан как массив параметров (содержимое определенной секции)

            $database_settings = $config_argv;
            $this->table_prefix = $config_argv['table_prefix'] ?? '';

        } elseif (get_class($config_argv) === 'INI_Config') {
            // Конфиг передан как инстанс класса INI_Config

            $section_name = empty($key_connection) ? '' : $config_argv->get( "connection/suffix" );
            // $database_settings_section_name = $config_argv->get('global/server') . $section_name;

            $database_settings_section_name = 'database:' . $section_name ;

            $database_settings = $config_argv->get( $database_settings_section_name );

            $this->table_prefix = $config_argv->get(
                $database_settings_section_name . '/table_prefix'
            );

            // в одну строчку
            // $this->database_settings = $config_instance->get( $config_instance->get('host/server') . (empty($key_connection) ? '' : ':' . $config_instance->get( "connection/{$key_connection}" )) );

        } elseif (is_null($config_argv)) {
            // передан NULL, используется StaticConfig
            //@todo: добавить проверку class_exists('StaticConfig');
            $section_name = empty($key_connection) ? '' : ':' . StaticConfig::key( "connection/{$key_connection}" );
            $database_settings_section_name = StaticConfig::key('host/server') . $section_name;
            $database_settings = StaticConfig::key( $database_settings_section_name );
            $this->table_prefix = StaticConfig::key(
                $database_settings_section_name . '/table_prefix'
            );

            // в одну строчку
            // $this->database_settings = StaticConfig::key( StaticConfig::key('host/server') . (empty($key_connection) ? '' : ':' . StaticConfig::key( "connection/{$key_connection}" )) );

        } else {
            // передано непонятно что
            die('Unknown config: ' . get_class($config_argv));
        }

        $this->database_settings = $database_settings;

        $dbhost = $database_settings['hostname'];
        $dbname = $database_settings['database'];
        $dbuser = $database_settings['username'];
        $dbpass = $database_settings['password'];
        $dbport = $database_settings['port'];

        $dsl = "mysql:host=$dbhost;port=$dbport;dbname=$dbname";

        try {
            $dbh = new \PDO($dsl, $dbuser, $dbpass);

            $dbh->exec("SET NAMES utf8 COLLATE utf8_unicode_ci");
            $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            $this->pdo_connection = $dbh;
        } catch (\PDOException $e) {
            $this->connect_error = "Database connection error!: " . $e->getMessage() . "<br/>";
            $this->pdo_connection = null;
            return false;
        }

        if ($config_type === self::USED_CONFIG_STATIC) {
            StaticConfig::setPDO( $dbh );
        }

        $this->is_connected = true;
        return true;
    }

    /**
     * @return null|PDO
     */
    public function getconnection()
    {
        return $this->pdo_connection;
    }

    public function connection()
    {
        return $this->pdo_connection;
    }

    public function status()
    {
        return $this->connect_error;
    }

    /**
     * @return string
     */
    public function get_table_prefix():string
    {
        return $this->table_prefix;
    }
}

/* end class.DBConnectionLite.php */