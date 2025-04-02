<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FileController extends Controller
{
    public function serveImage($filename)
    {
        $path = WRITEPATH . 'uploads/logosEmpresasExpositoras/' . $filename;

        if (!file_exists($path)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        return $this->response
            ->setContentType(mime_content_type($path))
            ->setBody(file_get_contents($path));
    }
    public function serveImageRender($filename)
    {
        $path = WRITEPATH . 'uploads/renders/' . $filename;

        if (!file_exists($path)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        return $this->response
            ->setContentType(mime_content_type($path))
            ->setBody(file_get_contents($path));
    }
}
