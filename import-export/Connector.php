<?php
use GuzzleHttp\Pool;
use GuzzleHttp\Promise;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
include "import-products/Auth.php";
class Connector {
   
   
   var $limit = 1000;
   var $offset = 0;
   public static $client, $promises, $pool;
   
   public static $baseUrl = "https://online.moysklad.ru/api/remap/1.1";
   public static $username = Auth::login;
   public static $password = Auth::password;
   public static $context;
   
   
   const HEADERS = [
      'auth'           => [ Auth::login, Auth::password ],
      'headers'        => [ 'Content-Type' => 'application/json' ],
      'stream_context' => [
         'ssl' => [
            'allow_self_signed' => true
         ],
      ],
      'verify'         => false,
   ];
   
   function __construct() {
      
      //self::createContext(self::$username, self::$password);
   }
   
   public static function init() {
      self::createContext(self::$username, self::$password);
      self::$client = new Client(self::HEADERS);
      self::$promises = [];
   }
   
   private static function createContext($username, $password) {
      self::$context = stream_context_create(
         ['http' => [
            'method' => 'GET',
            'header' => "Authorization: Basic "
               . base64_encode("$username:$password")
         ]]
      );
   }
   
   public static function addPromise($promise) {
   	if (count(self::$promises)<4) {
	    self::$promises[] = $promise;
    }
    else {
   		self::completeRequests();
	    self::$promises = [];
	    self::$promises[] = $promise;
    }
    
   }
   
   public static function requestAsync($url) {
      return self::$client->requestAsync('GET', $url);
   }
   
   public static function getObjectSync($url) {
      $response = self::$client->request('GET', $url);
      return json_decode($response->getBody());
   }
   
   public static function getRemoteObjectAsync($url) {
      $promise = Connector::requestAsync($url);
      $promise->then(
         function (ResponseInterface $res) {
            $stocksForGroup = json_decode($res->getBody());
            
         },
         function (RequestException $e) {
            echo $e->getMessage() . "\n";
            echo $e->getRequest()->getMethod();
         }
      );
      Connector::addPromise($promise);
   }
   
   public static function getInitialRemoteObjectAsync($url) {
      $promise = Connector::requestAsync($url);
   
      $url_components = parse_url($url);
      
      parse_str($url_components['query'], $query);
      $promise->then(
         function (ResponseInterface $res) use ($query, $url_components){
            $response = json_decode($res->getBody());
            $size = $response->meta->size;
            $limit = $response->meta->limit;
            if ($size > $limit) {
               $iterations = intdiv($size,$limit) + 1;
               for ($i = 1; $i < $iterations; $i++) {
                  $query['offset'] = $i * $limit;
                  $url_components['query'] = http_build_query($query);
                  $offsetUrl = http_build_url($url_components);
                  
                  self::getRemoteObjectAsync($offsetUrl);
               }
            }
         
         },
         function (RequestException $e) {
            echo $e->getMessage() . "\n";
            echo $e->getRequest()->getMethod();
         }
      );
      Connector::addPromise($promise);
   }
   
   
   public static function getRemoteObject2($url) {
      $object = self::getObjectSync($url);
      //var_dump($object);
      
      while (property_exists($object->meta, "nextHref")) {
         
         $tempObject = self::getObjectSync($object->meta->nextHref);
         $object->meta = $tempObject->meta;
         $object->rows = array_merge($object->rows, $tempObject->rows);
      }
      
      return $object;
   }
   
   
   public static function getRemoteObject($url) {
      $object = json_decode(file_get_contents($url, false, self::$context));
      
      while (property_exists($object->meta, "nextHref")) {
         $tempObject = json_decode(file_get_contents($object->meta->nextHref, false, self::$context));
         $object->meta = $tempObject->meta;
         $object->rows = array_merge($object->rows, $tempObject->rows);
      }
      
      return $object;
   }
   
   public static function completePromises($promises) {
      Promise\settle($promises)->wait();
   }

   public static function completeRequests() {
   	
	   Promise\settle(self::$promises)->wait();
	   Log::d("promises: ". count(self::$promises), "http-client");
   }
   
}