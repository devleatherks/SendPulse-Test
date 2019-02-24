<?php 

    /**
     * Manager Models 
     * 
     * PHP Version >= 7.2
     * 
     * @author Sergey Kozhedub <malati4ik123@gmail.com>
     * @package Models
     * @version 1.0.1
     */

    namespace Models;

    use Helper\SlimModels;

    class ModelEmailParser extends SlimModels{

        /**
         * Default Database Models
         * 
         * @var string
         */
        protected $database = 'email_parser';

        
        /**
         * Set New Task To DB
         */
        public function setNewTask(array $insertData){

            $collection = $this->mongoDB('parser_task');
            
            $insertOneResult = $collection->insertOne($insertData);
            printf("Inserted %d document(s)\n", $insertOneResult->getInsertedCount());

            var_dump($insertOneResult->getInsertedId());

            // self::$container->

            return [];

        }

    }

?>