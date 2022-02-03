<?php

namespace Src\Controller;

use Src\TableGateways\VideoGateway;

class VideoController
{

    //private $db;
    private $reqMethod;
    private $where;
    private $when;

    private $videoGateway;

    public function __construct($reqMethod, $where, $when)
    {
        //$this->db = $db;
        $this->reqMethod = $reqMethod;
        $this->where = $where;
        $this->when = $when;

        $this->videoGateway = new VideoGateway();
    }

    public function processRequest()
    {
        switch ($this->reqMethod) {
            case 'GET':
                if (isset($this->where) && isset($this->when))
                    $response = $this->getAllVideos();
                break;
            case 'POST':
                $response = $this->createVideoFromRequest();
                break;
            case 'PUT':
                $response = $this->updateVideoFromRequest($this->userId);
                break;
            case 'DELETE':
                $response = $this->deleteVideo($this->userId);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }

        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getVideo()
    {
        $result = $this->videoGateway->find($this->vidId);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAllVideos()
    {
        $result = $this->videoGateway->findAll($this->where, $this->when);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createVideoFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateVideo($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->videoGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateVideoFromRequest($id)
    {
        $result = $this->videoGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateVideo($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->videoGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteVideo($id)
    {
        $result = $this->videoGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->videoGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateVideo($input)
    {
        if (!isset($input['url'])) {
            return false;
        }
        if (!isset($input['url'])) {
            return false;
        }
        return true;
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
