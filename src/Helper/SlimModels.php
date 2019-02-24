<?php 
    /**
     * Manager Models 
     * 
     * PHP Version >= 7.2
     * 
     * @author Sergey Kozhedub <malati4ik123@gmail.com>
     * @package Helper
     * @version 1.0.1
     */

    namespace Helper;

    use Slim\Container;

    class SlimModels{

        /**
         * All use table
         * 
         * @var object
         */
        private static $useDatabase = [];

         /**
         * Slim container
         * 
         * @var Slim\Container
         */
        protected static $container;

        /**
         * Constructor receives container instance
         * 
         * @param  Slim\Container $container;
         */
        public function __construct(Container $container) {
            self::$container = $container;
        }

        /**
         * Get Object DB
         * 
         * @param $db_table - use table
         * @param $database - use database
         * 
         * @return object extends table
         */
        protected function mongoDB(string $db_table, $database = false){

            $name_id = $database . '_' . $db_table;

            if(empty($this->useDatabase[$name_id]))
                return $this->useDatabase[$name_id] = 
                    self::$container->mongoDB->{($database == false ? $this->database : $database)}->{$db_table};
            
        }

    }

?>