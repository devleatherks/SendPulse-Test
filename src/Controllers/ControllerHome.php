<?php 
    /**
     * Controller Page Home
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

    class ControllerHome extends SlimApp{

        /**
         * Home page view
         * 
         * @param \Slim\Http\Request $request
         * @param \Slim\Http\Response $response
         * @param Array $args
         * 
         * @return \Slim\Http\Response
         */
        public function view_main(Request $request, Response $response, Array $args): Response{

            return SlimApp::view($this->container, 'html', [], 200);
            

        }

        /**
         * Creating a new task
         * 
         * @param \Slim\Http\Request $request
         * @param \Slim\Http\Response $response
         * @param Array $args
         * 
         * @return \Slim\Http\Response
         */
        public function api_setPrseURL(Request $request, Response $response, Array $args): Response{

            # Post || flow
            $parsed = $request->getParsedBody(); 

            $data = $this->formValidity($parsed);

            if($data->status === false)
                return SlimApp::view($this->container, 'json', $data->response, 201);
            
            if($result = $this->model('ModelEmailParser')->setNewTask([
                'nesting'   => $data->response['nesting'],
                'url'       => $data->response['url'],
            ])){
                return SlimApp::view($this->container, 'json', $result, 200);
            }else{
                return SlimApp::view($this->container, 'json', [
                    'error' => 'system Error'
                ], 201);
            }

        }

        /**
         * Check the validity of the form
         * 
         * @param array $formData
         * @return array
         */
        private function formValidity(array $formData): Array{

            $error = []; $data = []; 

            if(empty($formData))
                $error[] = 'Empty data';

           
            $data['nesting'] = empty($formData['nesting']) ? 1 : (int)$formData['nesting'];

            if(empty($formData['url'])) 
                $error[] = 'Empty url';

            if(filter_var($formData['url'], FILTER_VALIDATE_URL)){
                $data['nesting'] = $formData['url'];
            }else{
                $error[] = 'URL not valid';
            }

            return (object)[
                'status' => empty($error) ? true : false, 
                'data' => empty($error) ? $data : $error
            ];

        }

    }

?>