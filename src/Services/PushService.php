<?php
namespace TeamNeusta\PushMe\Services;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use TeamNeusta\PushMe\DTO\Commit;

class PushService
{
    /**
     * @var ConfigService
     */
    private $configService;
    /**
     * @var Client
     */
    private $client;

    /**
     * PushService constructor.
     *
     * @param ConfigService $configService
     * @param Client $client
     */
    public function __construct(ConfigService $configService, Client $client = null)
    {
        $this->configService = $configService;
        $this->client = $client ?? new Client();
    }

    /**
     * @param Commit[] $commits
     */
    public function pushCommits($version, array $commits = [])
    {
        $requestData = [
            'project' => $this->configService->getProject(),
            'current_version' => $version,
            'commits' => $commits
        ];
        $request = new Request('POST',$this->configService->getEndpoint(),[],\GuzzleHttp\json_encode($requestData));
        try {
            $response = $this->client->send($request);
            print_r($response->getBody()->getContents());
        }catch (\GuzzleHttp\Exception\ServerException $exception) {
            // silent fail...
            var_dump($exception->getResponse()->getBody()->getContents());
        }
    }
}