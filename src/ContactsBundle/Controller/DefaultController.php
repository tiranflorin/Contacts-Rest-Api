<?php

namespace ContactsBundle\Controller;

use ContactsBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function retrieveAllAction()
    {
        $data = [];
        $contacts = $this->getDoctrine()
            ->getRepository('ContactsBundle:Contact')
            ->findAll(); // Not ok for large sets of data

        if (empty($contacts)) {
            return new JsonResponse($data, Response::HTTP_OK);
        }

        foreach ($contacts as $contact) {
            $data[] = $this->transformToArray($contact);
        }

        return new JsonResponse($data);
    }

    public function showAction($contactId)
    {
        $contact = $this->getDoctrine()
            ->getRepository('ContactsBundle:Contact')
            ->find($contactId);

        if (!$contact) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $details = $this->transformToArray($contact);

        return new JsonResponse($details);
    }

    public function createAction(Request $request)
    {
        $model = $request->request->get('model', null);
        $details = json_decode($model);
        $em = $this->getDoctrine()->getManager();
        $contact = new Contact();
        $contact->setName($details->name)
            ->setAddress($details->address)
            ->setTel($details->tel)
            ->setEmail($details->email)
            ->setType($details->type);

        $em->persist($contact);
        $em->flush();

        $details = $this->transformToArray($contact);

        return new JsonResponse($details, Response::HTTP_CREATED);
    }

    public function editAction($contactId, Request $request)
    {
        $contact = $this->getDoctrine()
            ->getRepository('ContactsBundle:Contact')
            ->find($contactId);

        if (!$contact) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $model = $request->request->get('model', null);
        $details = json_decode($model);
        $em = $this->getDoctrine()->getManager();
        $contact->setName($details->name)
            ->setAddress($details->address)
            ->setTel($details->tel)
            ->setEmail($details->email)
            ->setType($details->type);

        $em->persist($contact);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function deleteAction($contactId)
    {
        $contact = $this->getDoctrine()
            ->getRepository('ContactsBundle:Contact')
            ->find($contactId);

        if (!$contact) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($contact);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    private function transformToArray(Contact $contact)
    {
        $contactDetails['id'] = $contact->getId();
        $contactDetails['name'] = $contact->getName();
        $contactDetails['address'] = $contact->getAddress();
        $contactDetails['tel'] = $contact->getTel();
        $contactDetails['email'] = $contact->getEmail();
        $contactDetails['type'] = $contact->getType();

        return $contactDetails;
    }
}
