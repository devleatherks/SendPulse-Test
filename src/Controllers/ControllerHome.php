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

        public function api_setPrseURL(Request $request, Response $response, Array $args): Response{

            $data = $this->formValidity($formData);

            if($data->status === false)
                return SlimApp::view($this->container, 'json', $data->response, 201);
            
            

        }

        private function formValidity(array $formData){

            $error = []; $data = []; 

            if(empty($formData))
                $error[] = 'Empty data';

           
            $data['nesting'] = empty($formData['nesting']) ? 1 : (int)$formData['nesting'];

            if(empty($formData['url'])) 
                $error[] = 'Empty url';

            return (object)[
                'status' => empty($error) ? true : false, 
                'data' => empty($error) ? $data : $error
            ];

        }

    }

?>