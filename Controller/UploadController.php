<?php

namespace Saro0h\MediaApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UploadController extends Controller
{
    public function uploadAction(Request $request)
    {
        $file = $request->files->get($this->container->getParameter('media_api.field_name'));
        $filename = $request->get('filename');

        if (is_null($file)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'There is no file supplied.');
        }

        $media = $this->get('media_api.upload')->upload($file, $filename);

        $response = new Response($this->get('jms_serializer')->serialize(array('media' => $media), 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function getAction(Request $request)
    {
        $media = $this->getDoctrine()->getRepository('MediaApiBundle:Media')->findOneById($request->get('id'));

        if (is_null($media)) {
             throw new HttpException(Response::HTTP_BAD_REQUEST, 'No media found.');
        }

        $response = new Response($this->get('jms_serializer')->serialize(array('media' => $media), 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function deleteAction(Request $request)
    {
        $media = $this->getDoctrine()->getRepository('MediaApiBundle:Media')->findOneById($request->get('id'));

        if (is_null($media)) {
             throw new HttpException(Response::HTTP_BAD_REQUEST, 'No media found.');
        }

        $this->getDoctrine()->getEntityManager()->remove($media);
        $this->getDoctrine()->getEntityManager()->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
