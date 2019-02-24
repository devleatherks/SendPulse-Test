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

            // echo 'Url: ' . $domain . '<br>';
            // echo 'Type: ' . $contentType[0] . '<br>';
            // echo 'Code: ' . $code . '<br>';
            // echo '-----------------------------------<br><br>';

            if($contentType[0] == 'text/html' && $code == 200){
                // return ['contentType' => $contentType, 'code' => $code];
                return ['contentType' => $contentType, 'code' => $code, 'response' => $response, 'url' => $domain];
            }

            return false;

        }

        public function findAllUrlInDocument(string $html, string $url){

            $this->booferURL[$url] = $url;

            $host = parse_url($url);

            $dom = new \DOMDocument();

            @$dom->loadHTML($html, LIBXML_NOERROR);

            $xpath = new \DOMXPath($dom);

            $hrefs = $xpath->evaluate("/html/body//a");
            
            for($i = 0; $i < $hrefs->length; $i++){
                
                $href = $hrefs->item($i)->getAttribute('href');

                if(($href === '#') || ($href === '/'))
                    continue; 

                if((stristr($href, 'http://') === false) && (stristr($href, 'https://') === false))
                    $href = $host['scheme'] . '://' . $host['host'] . ($href[0] === '/' ? '' : '/') . $href;

                if(($href === $url) && (array_search($href, $this->booferURL) !== false))
                    continue; 

                if(stristr($href, '://' . $host['host']) !== false)
                    if($result = $this->senderGET($href))
                        $data_URL[] = $result; 

            }

            return $data_URL;

        }

        public function runTask(Array $task){

            if(empty($task))
                return false;

            $parserData = [];

            $this->booferURL[$task['url']] = $task['url'];
            $parserData[$task['url']] = [];
            
            $this->parse(file_get_contents($task['url']), $task['url'], $parserData[$task['url']], 2);

            print_r($parserData);

        }

        public function parse(string $html, string $thisurl, &$saveParse, $maxLevel = 1, $steplevel = 0){

            $steplevel++;

            if($steplevel > $maxLevel)
                return false;

            // echo '<h2>Level: ' . $steplevel . '</h2>';
            // echo '<h2>-------------------------------</h2>';

            $saveParse['emails'] = $this->findAllEmailInDocument($html);
            $urls   = $this->findAllUrlInDocument($html, $thisurl);

            if(empty($urls))
                return false;

            foreach($urls as $url){

                if(array_search($url['url'], $this->booferURL) !== false)
                    continue;

                $parserData[$url['url']] = [];

                $this->parse($url['response'], $url['url'], $parserData[$url['url']], $maxLevel, $steplevel);

            }

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