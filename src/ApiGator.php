<?php
/**
 *
 */


use pcc\ApigatorBundle\Exception\NullHeadersApigatorException;
use pcc\ApigatorBundle\Exception\NullMethodApigatorException;
use pcc\ApigatorBundle\Exception\NullPayloadApigatorException;
use pcc\ApigatorBundle\Exception\NullUriApigatorException;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
     * Crea una Conexión con "curl.php" a una API REST, tras ello pone a
     * disposición del usuario , tanto la response como un método específico
     * para procesarla según se prefiera.
     * @author Pascual Muñoz Galián <pascual.munoz@pccomponentes.com>
     * @see http://stackoverflow.com/questions/2140419/how-do-i-make-a-request-using-http-basic-authentication-with-php-curl
     */
    class ApiGator {

        /**
         * Los VERBOS de una API REST
         * por defecto GET.
         */
        CONST METHOD_GET = 'GET';
        CONST METHOD_POST = 'POST';
        CONST METHOD_PUT = 'PUT';
        CONST METHOD_PATCH = 'PATCH';

        /**
         * ApiGator constructor.
         *
         * @param null $uri
         * @param null $httpHeaders
         */
        public function __construct($uri = null , $httpHeaders = null, $method = null) {


            if(null === $uri)
            {
                /**
                 * Es posible crear un Apigator sin $uri inicial
                 * aunque no es aconsejado se permite.
                 */
            } else {
                $this->setUri($uri);
            }

            if(null === $httpHeaders){
                $this->headers = $this->getDefaultHeaders();
            } else {
                $this->setHeaders($httpHeaders);
            }

            if(null === $method) {
                $this->setMethod(self::METHOD_GET);
            } else {
                $this->setMethod($method);
            }

        }

        /**
         * El resource que devuelve El curl_init(uri)
         *
         * @var resource
         * @see http://php.net/manual/es/resource.php
         */
        private $ch;

        /**
         * Lo que nos devuelve Curl cuando le hacemos
         * curl_exec()
         * @var json http_response mixed $CurlResponse
         */
        private $CurlResponse;

        /**
         * URI GUELERRRRRRRR.
         * @var type La uri de la api.
         * @see https://www.youtube.com/watch?v=VOakvXIVUvo
         */
        private $uri;

        /**
         * Headers personalizadas de las Request .
         * @var array Con las http headers oficiales.
         */
        private $headers;

        /**
         * @return array
         */
        public function getHeaders()
        {
            return $this->headers;
        }

        /**
         * REST method : GET  , PUT , POST , PATCH , ETC
         * @var string
         */
        private $method;

        /**
         * @return string
         */
        public function getMethod(): string
        {
            return $this->method;
        }

        /**
         * @param string $method
         * @return ApiGator
         */
        public function setMethod(string $method): ApiGator
        {
            $this->method = $method;

            return $this;
        }

        /**
         * Normalmente el array a enviar con el method POST.
         * @var array
         */
        private $payload;

        /**
         * @return array
         */
        public function getPayload(): array
        {
            return $this->payload;
        }

        /**
         * @param array $payload
         * @return ApiGator
         */
        public function setPayload(array $payload): ApiGator
        {
            if (null === $payload){
                throw new NullPayloadApigatorException();
            }
            $this->payload = $payload;

            return $this;
        }

        /**
         * @param $headers
         * @return $this
         */
        public function setHeaders($headers) {
            if (null === $headers)
            {
                throw new NullHeadersApigatorException();
            }
            $this->headers = $headers;
            return $this;
        }


        /**
         * El resource que devuelve El curl_init(uri)
         *
         * @var resource
         * @see http://php.net/manual/es/resource.php
         */
        public function getCh() {
            return $this->ch;
        }

        /**
         * Ejecuta curl_exec y devuelve la response.
         * @return jsonResponse o directamente muere.
         *
         */
        public function getCurlResponse() {
            $this->curlSETOPTS();
            return $this->curlEXEC();
        }

        /**
         * Devuelve la respuesta en Json de la api decodificada como array .
         * @return mixed
         */
        public function getArrayResponse()
        {
            return json_decode($this->getCurlResponse(), true);
        }

        /**
         * Devuelve la respuesta de la api RAW como Json Decodificado
         * @return mixed
         */
        public function getJsonDecodedCurlResponse()
        {
            return json_decode($this->getCurlResponse());
        }

        public function getUri() {
            return $this->uri;
        }

        public function setUri($uri) {
            if (null === $uri){
                throw new NullUriApigatorException();
            }

          $this->uri = $uri;
            return $this;
        }



        public  function getDefaultHeaders()
        {
            $header[] = "Accept: application/json";
            $header[] = 'Content-Type: application/json';
            $header[] = 'Content-length: 0';

            return $header;
        }

        /**
         * Se deja como Alias de la Real por mantener el init-setopts-exec
         * @param $uri
         * @return resource
         */
        private function curlINIT($uri) {

            return $this->ch = curl_init($uri); //aquí se guarda currentUri , que no es la misma que la real de curl , la emula.
        }

        /**
         * Se encarga de settear las opciones del CURL resource y además
         * de verificar que todos los parámetros necesarios estén correctos
         * en caso de detectar cualquier cosa rara lanzará una excepción
         *
         * precondicion : $this->method está inicializado .
         *
         * @throws NullHeadersApigatorException
         */
        private function curlSETOPTS() {

            //si no tenemos la uri no tenemos nada.
            if ( null === $this->getUri())
            {
                throw new NullUriApigatorException();
            } else { //podemos empezar...
                $this->curlINIT($this->getUri());
            }


            if ( null === $this->getHeaders())
            {
                throw new NullHeadersApigatorException();
            }

            if (null === $this->getMethod())
            {
                throw new NullMethodApigatorException();
            }

            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->getHeaders());



            /*
             * Ojo, $this->httpHeader no puede ser null nunca o lanzaría excepción.
             * Si no se settean en el constructor quedan las por defecto .
             *
                        $header[] = "Accept: application/json";
                		$header[] = 'Content-Type: application/json';
                		$header[] = 'Content-length: 0';
                     	$header[] = 'Authorization: ebec63f521baf484da13a550a111e5d6';
             */

            curl_setopt($this->ch, CURLOPT_HEADER, 0); //no queremos el header en la response.
            //curl_setopt($this->Ch, CURLOPT_USERPWD, $username . ":" . $password);
            curl_setopt($this->ch, CURLOPT_TIMEOUT, 50);

            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $this->method); //gracias stackoverflow

            curl_setopt($this->ch, CURLOPT_POST, true);
            //todo: parametrizame (payload)
            //  curl_setopt($this->Ch, CURLOPT_POSTFIELDS, 'key: value'); payload
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
        }

        private function curlEXEC() {
            //miCurlExec() obtenemos response la dejamos cargada
            $this->CurlResponse = curl_exec($this->ch);

            if ($this->CurlResponse === false) {
                $info = curl_error($this->ch);
                curl_close($this->ch) && die("Error en curl_exec(): " . var_export($info));
            }
            return $this->CurlResponse;
        }

        public function __destruct() {
            if ($this->ch !== NULL) {
                try {
                    curl_close($this->ch);
                } catch (ContextErrorException $e) {
                    //todo: quitar echo
                    echo "Apigator no puede Cerrar el Resource de Curl";
                }
            } else {
                //todo:quitar echo
                Echo "El Resource de Curl no existe!! así que no hay nada que cerrar";
            }
        }

        /**
         * Es una closure, para el procesado externo del json.
         * Por supuesto , no hace falta usar esta tecnica y es posible usar
         * directamente el $this->curl_response si se prefiere.
         * Ejemplo:
         * 	  		$funcionDumpDeSymfony = function ($json) {
         * 			//transformamos el json en un Array.
         * 			$arr = json_decode($json, true);
         * 			//si tenemos response que el array apunte a ella.
         * 			$arr = $arr['response'] ? $arr['response'] : $arr;
         *
         * 		return new Response(dump($arr));
         * 	};
         * @param closure $f Donde $f es una función que se encarga de procesar
         * el json como el pamametro que recibe.
         */
        public function procesaResponseCon($f = 'print_r') {
            $f($this->getCurlResponse());
        }

    }
