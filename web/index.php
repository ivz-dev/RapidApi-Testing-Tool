<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

require '../vendor/autoload.php';
use RapidApi\RapidApiConnect;

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);
// Get container
$container = $app->getContainer();
require_once("dependencies.php");

$app->post('/ajax', function (Request $request, Response $response) use($container){

    $params = $request->getParams();
    $client = $container['httpClient'];

    $resp = $client->post($params['url'],
        [
            "form_params" => [
                "params" => $params['params'],
                "request" => $params['request']
            ]
        ]
    );

    if($resp->getStatusCode() == 200){
        $content =  json_decode($resp->getBody()->getContents(),true);
        if(json_decode($content['content']['payload'],true)){
            $content['content']['payload'] = json_decode($content['content']['payload'],true);
        }

        echo json_encode($content);
    } else {
        echo "Fail!";
    }
    return $response->withStatus(200);
});

$app->post('/ajax/uploadFile', function (Request $request, Response $response) use($container){

    $params = $request->getParams();

    $client = $this->httpClient;
    $uploadServiceResponse = $client->post("http://104.198.149.144:8080", [
        'multipart' => [
            [
                'name' => 'length',
                'contents' => $_FILES[0]['size']
            ],
            [
                "name" => "file",
                "filename" => $_FILES[0]['name'],
                "contents" => file_get_contents($_FILES[0]['tmp_name'])
            ]
        ]
    ]);
    $uploadServiceResponseBody = $uploadServiceResponse->getBody()->getContents();
    return json_decode($uploadServiceResponseBody)->file;
});

$app->get('/', function (Request $request, Response $response) {
    $rapid = new RapidApiConnect("Demo", "e0e4f9cc-c076-4cae-ad5b-f5d49beacd8a");
    $result = $rapid->call('RapidAPI', 'getAll', [
        "sortBy" => "lastUpdated",
        "limit"  => "500"
    ]);

    $packages = [];

    foreach ($result['success'] as $item){
        $packages[] = $item['name'];
    }

    $data['packages'] = $packages;
    return $this->renderer->render($response, "/list.php", $data);
});

$app->get('/{package}', function (Request $request, Response $response, $args) {

    $package =  $args['package'];
    $schema = file_get_contents("https://raw.githubusercontent.com/RapidSoftwareSolutions/Marketplace-$package-Package/master/src/metadata/schema.json");

    if($schema){
        $schema = json_decode($schema, true);
        $currentBlock = $schema['blocks'][0]['name'];

    } else {
        $metadata = file_get_contents("https://rapidapi.xyz/v2/package/$package");
        $metadata = json_decode($metadata, true);
        $currentBlock = $metadata['blocks'][0]['name'];
    }

    return $response->withRedirect("/$package/$currentBlock", 301);
});

$app->get('/{package}/{block}', function (Request $request, Response $response, $args) {
    $blockName =  $args['block'];
    $package =  $args['package'];
    $schema = file_get_contents("https://raw.githubusercontent.com/RapidSoftwareSolutions/Marketplace-$package-Package/master/src/metadata/schema.json");
    if($schema) {
        $schema = json_decode($schema, true);

        $data = [];
        $data['packageName'] = $package;
        $data['currentBlock'] = $blockName;

        foreach($schema['blocks'] as $key=>$blockItem){
            $data['blocks'][] = $blockItem['name'];

            $blockTitle = $blockItem['name'];

            $data[$blockTitle]['blockDescription'] = $schema['blocks'][$key]['description'];

            foreach ($schema['blocks'][$key]['args'] as $block){
                $name = $block['name'];
                $vendorName = (isset($block['vendorSchema']['name'])) ? $block['vendorSchema']['name'] : $block['name'];
                $description = $block['info'];
                $type = $block['type'];
                $required = ($block['required'] == true) ? "required" : "optional";

                $field = [
                    "rapidName" => $name,
                    "vendorName" => $vendorName,
                    "description" => $description,
                    "type" => $type
                ];

                switch ($type) {
                    case "Select":
                        $field['options'] = $block['options'];
                        $field['value'] = "";
                        break;
                    case "List":
                        $field['value'] = [];
                        break;
                    case "Array":
                        $structure = [];
                        foreach ($block['structure'] as $item){
                            $structure[$item['name']] = "";
                        }
                        $field['structure'] = $structure;
                        $field['value'][] = $structure;
                        break;
                    default:
                        $field['value'] = "";
                }

                $data[$blockTitle]['fields'][$required][] = $field;
            }

            $data[$blockTitle]['vendorRequest'] = $schema['blocks'][$key]['vendorRequest'];

            $data[$blockTitle]['rapidRequest']['method'] = "post";
            $data[$blockTitle]['rapidRequest']['paramsType'] = "json";
            $data[$blockTitle]['rapidRequest']['headers']["Content-Type"] = "application/json";
            $data[$blockTitle]['rapidRequest']['url'] = "https://rapidapi.io/connect/{$schema['package']}/$blockTitle";
            $data[$blockTitle]['rapidResponseContent'] = "";
            $data[$blockTitle]['vendorResponseContent'] = "";
        }

        return $this->renderer->render($response, "/test.php", $data);
    } else {
        $metadata = file_get_contents("https://rapidapi.xyz/v2/package/$package");
        $metadata = json_decode($metadata, true);

        $data = [];
        $data['packageName'] = $package;


        foreach($metadata['blocks'] as $block){
            $data['blocks'][] = $block['name'];
        }
        $key = array_search($blockName, $data['blocks']);
        $data['blockDescription'] = $metadata['blocks'][$key]['description'];
        $data['currentBlock'] = $blockName;

        foreach ($metadata['blocks'][$key]['args'] as $block){
            $name = $block['name'];
            $description = $block['info'];
            $type = $block['type'];
            $required = ($block['required'] == true) ? "required" : "optional";

            $field = [
                "rapidName" => $name,
                "description" => $description,
                "type" => $type
            ];

            switch ($type) {
                case "Select":
                    $field['options'] = $block['options'];
                    $field['value'] = "";
                    break;
                case "List":
                    $field['value'] = [];
                    break;
                case "Array":
                    $structure = [];
                    foreach ($block['structure'] as $item){
                        $structure[$item['name']] = "";
                    }
                    $field['structure'] = $structure;
                    $field['value'][] = $structure;
                    break;
                default:
                    $field['value'] = "";
            }

            $data['fields'][$required][] = $field;
        }

        $data['rapidRequest']['method'] = "post";
        $data['rapidRequest']['paramsType'] = "json";
        $data['rapidRequest']['headers']["Content-Type"] = "application/json";
        $data['rapidRequest']['url'] = "https://rapidapi.io/connect/$package/$blockName";

        return $this->renderer->render($response, "/stage.php", $data);
    }


});

$app->run();
