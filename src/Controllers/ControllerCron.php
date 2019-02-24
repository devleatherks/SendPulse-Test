<?php 
    /**
     * Controller Cron
     * 
     * The controller responds manages parser tasks
     * 
     * PHP Version >= 7.2
     * 
     * @author Sergey Kozhedub <malati4ik123@gmail.com>
     * @package Controllers
     * @version 1.0.1
     */

    namespace Controllers;

    use Psr\Container\ContainerInterface;
    use Slim\Http\Request;
    use Slim\Http\Response;
    use Helper\SlimApp;

    class ControllerCron extends SlimApp{

        /**
         * Creating a new task
         * 
         * @param \Slim\Http\Request $request
         * @param \Slim\Http\Response $response
         * @param Array $args
         * 
         * @return \Slim\Http\Response
         */
        public function api_processQueue(Request $request, Response $response, Array $args){

            # We check running tasks
            if($this->model('ModelEmailParser')->getWorkingStatus() === false)
                # We are requesting a dead task.
                if($task = $this->model('ModelEmailParser')->getNextTask())
                    # Run the following task
                    $this->model('ModelEmailParser')->runTask($task);

            # Otherwise, do nothing.

        }

        public function api_result(Request $request, Response $response, Array $args){

            $result = $this->model('ModelEmailParser')->getall();

            return SlimApp::view('html', [
                'taskList' => (array)$result
            ], 200, 'test');

        }

    }

?>