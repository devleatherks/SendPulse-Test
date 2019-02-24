<?php 
    namespace Helper;

    use Slim\Container;
    use Slim\Http\Request;
    use Slim\Http\Response;

    class SlimApp{

        /**
         * Types of page replies
         *
         * @var array
         */
		const HEADER_MIME_TYPES = [

            'txt'   => 'text/plain',
            'htm'   => 'text/html',
            'html'  => 'text/html',
            'php'   => 'text/html',
            'css'   => 'text/css',
            'js'    => 'application/javascript',
            'json'  => 'application/json',
            'xml'   => 'application/xml',
            'swf'   => 'application/x-shockwave-flash',
            'flv'   => 'video/x-flv',

            # Images
            'png'   => 'image/png',
            'jpe'   => 'image/jpeg',
            'jpeg'  => 'image/jpeg',
            'jpg'   => 'image/jpeg',
            'gif'   => 'image/gif',
            'bmp'   => 'image/bmp',
            'ico'   => 'image/vnd.microsoft.icon',
            'tiff'  => 'image/tiff',
            'tif'   => 'image/tiff',
            'svg'   => 'image/svg+xml',
            'svgz'  => 'image/svg+xml',

            # archives
            'zip'   => 'application/zip',
            'rar'   => 'application/x-rar-compressed',
            'exe'   => 'application/x-msdownload',
            'msi'   => 'application/x-msdownload',
            'cab'   => 'application/vnd.ms-cab-compressed',

            # audio/video
            'mp3'   => 'audio/mpeg',
            'qt'    => 'video/quicktime',
            'mov'   => 'video/quicktime',

            # adobe
            'pdf'   => 'application/pdf',
            'psd'   => 'image/vnd.adobe.photoshop',
            'ai'    => 'application/postscript',
            'eps'   => 'application/postscript',
            'ps'    => 'application/postscript',

            # ms office
            'doc'   => 'application/msword',
            'rtf'   => 'application/rtf',
            'xls'   => 'application/vnd.ms-excel',
            'ppt'   => 'application/vnd.ms-powerpoint',

            # open office
            'odt'   => 'application/vnd.oasis.opendocument.text',
            'ods'   => 'application/vnd.oasis.opendocument.spreadsheet',
        ];

        /**
         * Base Models
         * 
         * @var array
         */
        private static $models = [];


        /**
         * Slim container
         * 
         * @var Slim\Container
         */
        protected static $container;

        /**
         * Constructor receives container instance
         * 
         * @param Slim\Container $container;
         */
        public function __construct(Container $container){
            self::$container = $container;
        }

        /**
         * Connect the module to the controller
         *
         * @param string $namemodel
         * @return void
         */
        protected function model(string $namemodel){

            $model = 'Models\\' . $namemodel;

            if(!empty(self::$models[$model]))
                return self::$models[$model];

            return self::$models[$model] = new $model(self::$container);

        }

        /**
         * Output control
         * 
         * Depending on the transferred header, 
         * the method determines the type of data to be output.
         * 
         * @param \Slim\Container @container
         * @param String @contentType
         * @param Array @out
         * @param int @code
         * @param String @template
         * 
         * @return \Slim\Http\Response
         */
        public static function view(String $contentType, Array $out, int $code = 200, String $template = 'index'): Response{

            if($contentType == 'json')
                return self::$container->response->withStatus($code)
                                ->withHeader('Content-Type', SP_App::HEADER_MIME_TYPES[$contentType])
                                ->write(json_encode($out));
            else
                return self::$container->view->render(self::$container->response, $template . '.phtml', $out);

        }

        /**
         * DIRECTORY_SEPARATOR 
         * 
         * @param String $path
         * 
         * @return String
         * 
         */
        public static function DS(string $path){

            if(DIRECTORY_SEPARATOR  == '/')
                return str_replace('\\', '/', $path);
            elseif(DIRECTORY_SEPARATOR  == '\\')
                return str_replace('/', '\\', $path);

        }

    }

?>