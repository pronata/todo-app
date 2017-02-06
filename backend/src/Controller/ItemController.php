<?php

namespace pronata\Controller;

use pronata\Config\EntityManagerCreator;
use pronata\Entity\TodoListItem;
use pronata\Entity\User;
use pronata\Error\InvalidParamsException;
use pronata\Request;
use pronata\Response;

class ItemController implements CrudControllerInterface
{
    private $em;
    private $user;

    public function __construct()
    {
        $this->user = new User();
        $emCreator = new EntityManagerCreator();
        $this->em = $emCreator->getOrmEntityManager();
    }

    public function postAction(Request $request)
    {
        $params = $request->getParams();
        if (!isset($params['list_id'])) {
            throw new InvalidParamsException("Not set 'list_id' param");
        }
        if (!isset($params['title'])) {
            throw new InvalidParamsException("Not set 'title' param");
        }
        $todoListItem = new TodoListItem();
        $todoListItem->setTodoList($this->em->find('pronata\Entity\TodoList', $params['list_id']));
        $todoListItem->setTitle($params['title']);
        $this->em->persist($todoListItem);
        $this->em->flush();
        $response = new Response();
        $response->setContent([
            'id' => $todoListItem->getId(),
            'list_id' => $todoListItem->getTodoList()->getId(),
            'title' => $todoListItem->getTitle(),
            'completed' => $todoListItem->getCompleted()
        ]);
        $response->setStatusCode(200);
        $response->send();

    }

    public function deleteAction(Request $request)
    {
        $response = new Response();
        $todoListItemId = $request->getIdUriPart();
        $todoListItem = $this->em->find('pronata\Entity\TodoListItem', $todoListItemId);
        if (isset($todoListItem)) {
            $todoListId = $todoListItem->getTodoList()->getId();
            $this->em->remove($todoListItem);
            $this->em->flush();

            if (is_null($todoListItem->getId())) {
                $response->setContent(['id' => $todoListItemId, 'list_id' => $todoListId, 'deleted' => true]);
                $response->setStatusCode(200);
                $response->send();
            } else {
                $response->setStatusCode(400);
                $response->setContent(['id' => $todoListItemId, 'deleted' => true]);
                $response->send();
            }
        } else {
            $response->setStatusCode(404);
            $response->setContent(['id' => $todoListItemId, 'deleted' => false]);
            $response->send();
        }
    }

    public function getAction(Request $request)
    {
        $params = $request->getParams();
        $todoListId = isset($params['list_id']) ? $params['list_id'] : NULL;
        if (is_null($todoListId)) {
            throw new InvalidParamsException("Not set 'list_id'");
        }
        $startingAfter = isset($params['starting_after']) ? $params['starting_after'] : NULL;
        $endingBefore = isset($params['ending_before']) ? $params['ending_before'] : NULL;
        $offset = isset($params['offset']) ? $params['offset'] : NULL;
        $limit = isset($params['limit']) ? $params['limit'] : NULL;
        // $todoLists = $this->em->getRepository('pronata\Entity\TodoList')
        //   ->findBy(array(), null, $limit, null);
        $qb = $this->em->createQueryBuilder();
        $qb->select('listItem')->from('pronata\Entity\TodoListItem', 'listItem');
        if (!is_null($startingAfter) || !is_null($endingBefore)) {
            if (!is_null($startingAfter)) {
                $qb->where('todo_list_id = :todo_list_id')->setParameter('todo_list_id', $todoListId);
                $qb->andWhere('listItem.id > :starting_after')->setParameter('starting_after', $startingAfter);
                if (!is_null($endingBefore)) {
                    $qb->andWhere('listItem.id < :ending_before')->setParameter('ending_before', $endingBefore);
                }
            } else {
                $qb->where('listItem.id < :ending_before')->setParameter('ending_before', $endingBefore);

            }
        }
        if (!is_null($offset)) {
            $qb->setFirstResult($offset);
        }
        if (!is_null($limit)) {
            $qb->setMaxResults($limit);
        }
        $qb->orderBy('listItem.id', 'ASC');
        $todoListItems = $qb->getQuery()->getResult();
        $content['items'] = [];
        foreach ($todoListItems as $todoListItem) {
            $content['items'][] = [
                'id' => $todoListItem->getId(),
                'title' => $todoListItem->getTitle(),
                'completed' => $todoListItem->getCompleted(),
            ];
        }
        $response = new Response();
        $response->setContent($content);
        $response->setStatusCode(200);
        $response->send();
    }

    public function putAction(Request $request)
    {
        var_dump($request->getParams());
        // TODO: Implement putAction() method.
    }

    public function patchAction(Request $request)
    {
        $response = new Response();
        $todoListItemId = $request->getIdUriPart();
        if (!isset($todoListItemId)) {
            throw new \Exception('Invalid uri');
        }
        $todoListItem = $this->em->find('pronata\Entity\TodoListItem', $todoListItemId);
        if (isset($todoListItem) and ($todoListItem instanceof TodoListItem)) {
            $todoList = $todoListItem->getTodoList();
            $params = $request->getParams();
            if (isset($params['title']) || isset($params['completed'])) {
                $todoListItem->setTodoList($todoList);
                if (isset($params['title'])) {
                    $todoListItem->setTitle($params['title']);
                }
                if (isset($params['completed'])) {
                    if ($params['completed'] == 'true') {
                        $todoListItem->setCompleted(true);
                    }
                    else {
                        $todoListItem->setCompleted(false);
                    }
                }
                $this->em->merge($todoListItem);
                $this->em->flush();
                    $content = [
                        'id' => $todoListItem->getId(),
                        'title' => $todoListItem->getTitle(),
                        'completed' => $todoListItem->getCompleted(),
                        'updated' => true
                    ];
                $response = new Response();
                $response->setContent($content);
                $response->setStatusCode(200);
                $response->send();
            }
            else {
                throw new InvalidParamsException("Invalid params");
            }
        }
        else {
            $response->setStatusCode(404);
            $response->setContent(['id' => $todoListItemId, 'updated' => false]);
            $response->send();
        }
    }
}