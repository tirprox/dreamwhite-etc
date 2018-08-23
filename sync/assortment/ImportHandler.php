<?php
namespace Dreamwhite\Assortment;

require_once 'includes.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class ImportHandler
{

    private $client;

    function __construct()
    {
        $this->client = new Client();
    }

    public function run() {
        foreach (Config::SITES as $site) {
            $url = $site . Config::WPALLIMPORT_TRIGGER_URL;

            $promise = $this->client->requestAsync('GET', $url);

            $promise->then(
                function (ResponseInterface $res) use ($site){
                    $this->handleResponse($res, $site);
                },

                function (RequestException $e) {
                    echo $e->getMessage() . PHP_EOL;
                    echo $e->getRequest()->getMethod();
                }
            );

            $promises[] = $promise;
        }

        $results = Promise\settle($promises)->wait();
    }

    private function process($site) {
        $promise = $this->client->requestAsync('GET', $site . Config::WPALLIMPORT_PROCESSING_URL);
        $promise->then(
            function (ResponseInterface $res) use ($site){
                $this->handleResponse($res, $site);
            },
            function (RequestException $e) {
                echo $e->getMessage() . PHP_EOL;
                echo $e->getRequest()->getMethod();
            }
        );

        Promise\settle($promise)->wait();
    }

    private function handleResponse($res, $site) {

        $body = json_decode($res->getBody(), true);

        $code = $body['status'];
        $message = $body['message'];

        echo "$site: Status $code: $message" . PHP_EOL;

        $complete = $this->contains($message, 'complete') ;
        $notTriggered = $this->contains($message, 'not triggered');
        $alreadyTriggered = $this->contains($message, 'already triggered');
        $alreadyProcessing = $this->contains($message, 'already processing');

        if ($code === 200) {
            if(!$complete && !$notTriggered && !$alreadyProcessing) {
                $this->process($site);
            }
        }

        else if ($code === 403) {
            if($alreadyTriggered) {
                $this->process($site);
            }
        }

        else {
            $this->process($site);
        }
    }

    /*private function contains($haystack, $needle) {
        return strpos($haystack, $needle) !== false;
    }*/

    private function contains($haystack, $needle) {
        return mb_strpos($haystack, $needle) !== false;
    }

}