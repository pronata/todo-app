<?php

namespace pronata\Controller;

use pronata\Config\EntityManagerCreator;
use pronata\Entity\TodoList;
use pronata\Entity\User;
use pronata\Error\InvalidParamsException;
use pronata\Request;
use pronata\Response;

class ListController implements CrudControllerInterface
{
    private $todoList;

    private $em;

    public function __construct()
    {
        $this->todoList = new TodoList();
        $emCreator = new EntityManagerCreator();
        $this->em = $emCreator->getOrmEntityManager();
    }

    public function postAction(Request $request)
    {
        $params = $request->getParams();
        if (!isset($params['name'])) {
            throw new InvalidParamsException("Not set 'name' param");
        }
        $this->todoList->setName($params['name']);
        $user = $request->getUser();
        if (isset($user) && ($user instanceof User)) {
            $user = $this->em->find('pronata\Entity\User', $user->getId());
            $this->todoList->setUser($user);
        }
        $this->em->persist($this->todoList);
        $this->em->flush();
        $response = new Response();
        $response->setContent(['id' => $this->todoList->getId(), 'name' => $this->todoList->getName()]);
        $response->setStatusCode(200);
        $response->send();
    }

    public function deleteAction(Request $request)
    {
        $response = new Response();
        $todoListId = $request->getIdUriPart();
        $todoList = $this->em->find('pronata\Entity\TodoList', $todoListId);
        if (isset($todoList)) {
            $this->em->remove($todoList);
            $this->em->flush();

            if (is_null($todoList->getId())) {
                $response->setContent(['id' => $todoListId, 'deleted' => true]);
                $response->setStatusCode(200);
                $response->send();
            }
            else {
                $response->setStatusCode(400);
                $response->setContent(['id' => $todoListId, 'deleted' => false]);
                $response->send();
            }
        }
        else {
            $response->setStatusCode(404);
            $response->setContent(['id' => $todoListId, 'deleted' => false]);
            $response->send();
        }


        /*if ('application/x-www-form-urlencoded' === $request->getContentType())
        {
            parse_str($request->getContent(), $params);
        }*/
    }

    public function getAction(Request $request)
    {
        $params = $request->getParams();
        $startingAfter = isset($params['starting_after']) ? $params['starting_after'] : NULL;
        $endingBefore = isset($params['ending_before']) ? $params['ending_before'] : NULL;
        $offset = isset($params['offset']) ? $params['offset'] : NULL;
        $limit = isset($params['limit']) ? $params['limit'] : NULL;
       // $todoLists = $this->em->getRepository('pronata\Entity\TodoList')
         //   ->findBy(array(), null, $limit, null);
        $qb = $this->em->createQueryBuilder();
        $qb->select('list')->from('pronata\Entity\TodoList', 'list');
        if (!is_null($startingAfter) || !is_null($endingBefore)) {
            if (!is_null($startingAfter)) {
                $qb->where('list.id > :starting_after')->setParameter('starting_after', $startingAfter);
                if (!is_null($endingBefore)) {
                    $qb->andWhere('list.id < :ending_before')->setParameter('ending_before', $endingBefore);
                }
            }
            else {
                $qb->where('list.id < :ending_before')->setParameter('ending_before', $endingBefore);

            }
        }
        if (!is_null($offset)) {
            $qb->setFirstResult($offset);
        }
        if (!is_null($limit)) {
            $qb->setMaxResults($limit);
        }
        $qb->orderBy('list.id', 'ASC');
        $todoLists = $qb->getQuery()->getResult();
        $content['lists'] = [];
        foreach ($todoLists as $todoList) {
            $content['lists'][] = [
                'id' => $todoList->getId(),
                'name' => $todoList->getName()
                ];
        }
        $response = new Response();
        $response->setContent($content);
        $response->setStatusCode(200);
        $response->send();

    }

    public function putAction(Request $request)
    {
        // TODO: Implement putAction() method.
    }

    public function patchAction(Request $request)
    {
        // TODO: Implement patchAction() method.
    }
}