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

    class SlimModels{

        /**
         * All use table
         * 
         * @var object
         */
        private static $useDatabase = [];

        /**
         * Constructor receives container instance
         * 
         * @param Psr\Container\ContainerInterface $container;
         */
        public function __construct(ContainerInterface $container) {
            $this->container = $container;
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
                    $this->container->mongoDB->{($database == false ? sels::$database : $database)}->{$db_table};
            
        }

    }

?>