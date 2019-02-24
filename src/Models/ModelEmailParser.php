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

        private $parserData = [];

        public function getWorkingStatus(bool $returnObject = false){

            if($workTask = $this->getRunningTasks())
                return $returnObject ? $workTask : true;
            
            return false;

        }

        public function getRunningTasks(){

            $collection = $this->mongoDB('parser_task');

            $insertOneResult = $collection->find(['action' => 0, 'work' => 1]);

            return empty($insertOneResult) ? false : $insertOneResult;

        }

        public function findAllEmailInDocument(string $html){

            preg_match_all('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})/', $html, $potential_emails, PREG_SET_ORDER);

            $potential_emails = array_unique($potential_emails[0]);

            $emails = [];

            foreach($potential_emails as $email){
                if(filter_var($email, FILTER_VALIDATE_EMAIL))
                    $emails[] = $email;
            }

            return $emails;

        }

        function senderGET(string $domain){

            $curlInit = curl_init();

            curl_setopt($curlInit, CURLOPT_URL, $domain);
            curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);

           
            $response       = curl_exec($curlInit);
            $contentType    = curl_getinfo($curlInit, CURLINFO_CONTENT_TYPE);
            $code           = curl_getinfo($curlInit, CURLINFO_HTTP_CODE);

            curl_close($curlInit);

            $contentType = explode(';', $contentType);

            if($contentType[0] == 'text/html' && $code == 200){
                return ['contentType' => $contentType, 'code' => $code];
                // return ['contentType' => $contentType, 'code' => $code, 'response' => $response];
            }

            return false;

        }

        public function findAllUrlInDocument(string $html, string $url){

            preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $html, $match);

            $host = parse_url($url)['host'];

            $data_URL = [];
            
            foreach($match[0] as $address){
                if(stristr($address, '://' . $host) !== false){
                    $data_URL = $this->senderGET($address);
                }
            }

            return $data_URL;
            return false;
           

        }

        public function runTask(Array $task){

            if(empty($task))
                return false;

            $parserData = [];
            
            $this->parse(file_get_contents($task['url']), $parserData, 3);

        }

        public function parse(string $html, &$saveParse, $maxLevel = 1, $steplevel = 1){

            $emails = $this->findAllEmailInDocument($html);
            $urls   = $this->findAllUrlInDocument($html, $task['url']);

            

        }

        public function getNextTask(){

            $collection = $this->mongoDB('parser_task');

            $insertOneResult = $collection->findOne(['action' => 0, 'work' => 0]);

            return empty($insertOneResult) ? false : $insertOneResult;

        }
        
        /**
         * Set New Task To DB
         */
        public function setNewTask(array $insertData){

            $db_table = 'parser_task';

            $collection = $this->mongoDB($db_table);

            $insertData['_id'] = $this->getNextSequence($db_table);
            $insertData['status'] = 0;
            $insertData['work'] = 0;

            $insertOneResult = $collection->insertOne($insertData);

            return $insertOneResult;

        }

    }

?>