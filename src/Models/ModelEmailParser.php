<?php 

    namespace Models;

    class ModelEmailParser{

        /**
         * Default Database Models
         * 
         * @var string
         */
        protected $database = 'email_parser';

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
         * @return object extends table
         */
        protected function mongoDB(string $db_table){

            return $this->container->mongoDB->{$database}->{$db_table};
        }

        public function setNewTask(){

            $collection = $this->mongoDB('parser_task');
            
            // $insertOneResult = $collection->insertOne(['_id' => 1, 'name' => 'Alice']);

        }

    }

?>