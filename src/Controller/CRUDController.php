<?php

// http://localhost:8007/crud/list

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CRUDController extends AbstractController
{
    #[Route('/crud/list', name: 'c_r_u_d')]
    public function index(EntityManagerInterface $em): Response
    {
        $tasks = $em->getRepository(Task:: class)->findBy([], ['id'=>'ASC']);
        return $this->render('crud/index.html.twig', ['tasks'=>$tasks]);
    }

    #[Route('/create', name: 'create_task', methods: ['POST'])]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $title = trim($request->get('title'));

        if (!empty($title)) {

            $entityManager = $doctrine->getManager();
            
            $task = new Task();
            $task->setTitle($title);
            $entityManager->persist($task); // prepare for saving to db
            $entityManager->flush(); // saving is done by this line, insert in db
            
            return new Response('task added');

        } else {
            return $this->redirectToRoute('c_r_u_d');
        }
        // exit($request->get('title'));
    }

    #[Route('/update/{id}', name: 'update_task')]
    public function update($id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);
        $task->setStatus(!$task->getStatus());
        $entityManager->flush();
        return $this->redirectToRoute('c_r_u_d');

        // exit('crud update new task: update a new task' . $id);
    }

    #[Route('/delete/{id}', name: 'delete_task')]
    public function delete($id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);

        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('c_r_u_d');

        // exit('crud delete task: delete a new task' . $id);
    }
}
