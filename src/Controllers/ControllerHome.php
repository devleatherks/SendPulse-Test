<?php 
    /**
     * Controller Page Home
     * 
     * PHP Version >= 7.2
     * @author Sergey Kozhedub <malati4ik123@gmail.com>
     */

    namespace Controllers;

    use Psr\Container\ContainerInterface;
    use Slim\Http\Request;
    use Slim\Http\Response;

    class ControllerHome{

        protected $container;

        /**
         * Constructor receives container instance
         * 
         * @param Psr\Container\ContainerInterface $container;
         */
        public function __construct(ContainerInterface $container) {
            $this->container = $container;
        }

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

            return \SP_App::view($this->container, 'html', [], 200);
            

        }

    }

?>