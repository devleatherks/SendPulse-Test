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
        private $booferURL = [];

        /**
         * Returns the status of processes
         * 
         * Returns true if there are active objects.
         * 
         * @param bool $returnObject = false - true - return object db
         * @return object | bool  
         */
        public function getWorkingStatus(bool $returnObject = false){

            if($workTask = $this->getRunningTasks())
                return $returnObject ? $workTask : true;
            
            return false;

        }

        /**
         * Returns all active tasks
         * 
         * @return object | bool  
         */
        public function getRunningTasks(){

            $collection = $this->mongoDB('parser_task');

            $insertOneResult = $collection->find(['action' => 0, 'work' => 1]);

            return empty($insertOneResult) ? false : $insertOneResult;

        }

        /**
         * Searches for all emails on page
         * 
         * @param string $html - html code page 
         * @return array - emails
         */
        public function findAllEmailInDocument(string $html){

            preg_match_all("/[-a-z0-9!#$%&'*_`{|}~]+[-a-z0-9!#$%&'*_`{|}~\.=?]*@[a-zA-Z0-9_-]+[a-zA-Z0-9\._-]+/i", $html, $potential_emails);

            if(empty($potential_emails))
                return [];

            $potential_emails = array_unique($potential_emails[0]);

            $emails = [];

            foreach($potential_emails as $email){
                if(filter_var($email, FILTER_VALIDATE_EMAIL))
                    $emails[] = $email;
            }

            return $emails;

        }

        /**
         * For query queries with checks
         * 
         * Checks on the type of the received 
         * document and on the page status.
         * Returns html only if the text / html page.
         * Otherwise false
         * 
         * @param string $domain - url 
         * @return string | bool
         */
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
                return $response;
            }

            return false;

        }

        /**
         * Finds all the links on the page.
         * 
         * @param string $html - text/html
         * @param string $url -  url this page
         * 
         * @return array - all url page
         */
        public function findAllUrlInDocument(string $html, string $url){

            $this->booferURL[$url] = $url; $host = parse_url($url);

            $dom = new \DOMDocument();

            $dom->loadHTML($html, LIBXML_NOERROR);

            $hrefs = (new \DOMXPath($dom))->evaluate("/html/body//a");

            $data_URL = [];
            
            for($i = 0; $i < $hrefs->length; $i++){
                
                $href = $hrefs->item($i)->getAttribute('href');

                if(($href === '#') || ($href === '/'))
                    continue; 

                # If the url is not complete, then we substitute the current domain
                if((stristr($href, 'http://') === false) && (stristr($href, 'https://') === false))
                    $href = $host['scheme'] . '://' . $host['host'] . ($href[0] === '/' ? '' : '/') . $href;

                # Check for duplicate links
                if(array_search($href, $this->booferURL) !== false){
                    continue;
                }

                # Check that the link to the current site
                if(stristr($href, '://' . $host['host']) !== false)
                    $data_URL[] = $href;       

            }

            return $data_URL;

        }

        /**
         * Running task
         * @param Array $task 
         */
        public function runTask(Array $task){

            if(empty($task))
                return false;

            $parserData = [];  $parserData[$task['url']] = [];

            $resultParse = $this->parser($this->senderGET($task['url']), $task['url'], $parserData[$task['url']], 6);

            // $resultParse

            return $resultParse;

        }


        /**
         * Algorithm tree by reference
         * Generates a tree
         * @param string $html - text/html
         * @param string $thisurl - url
         * @param array &$saveParse - link to key
         * @param int $maxLevel 
         * @param int $steplevel
         * 
         * @return Array
         */
        public function parser(string $html, string $thisurl, array &$saveParse, int $maxLevel = 1, int $steplevel = 1){

            $steplevel++;

            if($steplevel >= $maxLevel)
                return false;

            if(array_search($thisurl, $this->booferURL) !== false)
                return false;

            $saveParse  = $this->findAllEmailInDocument($html);
            $urls       = $this->findAllUrlInDocument($html, $thisurl);

            if(empty($urls))
                return false;

            foreach($urls as $url){

                if(array_search($url, $this->booferURL) !== false)
                    continue;
                
                $saveParse[$url] = [];
                    $this->parse($this->senderGET($url), $url, $saveParse[$url], $maxLevel, $steplevel);
                
                $this->booferURL[$thisurl] = $thisurl;

            }

            $this->booferURL[$thisurl] = $thisurl;
                
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

        /**
         * Set New Task To DB
         */
        public function saveParser(array $parser_result){

            $db_table = 'parser_result';

            $collection = $this->mongoDB($db_table);

            $insertData = [];

            $insertData['_id'] = $this->getNextSequence($db_table);
            $insertData['result'] = $parser_result;

            $insertOneResult = $collection->insertOne($insertData);

            return $insertOneResult;

        }

    }

?>