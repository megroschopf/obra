<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }


    public function loginAction(Request $request){
        $helpers = $this->get(helpers::class);

        //recibir json por post

        $json = $request->get('json', null);
        //array a devolver por defecto

        $data = array(
            'status' => 'error',
            'data' => 'send json via POST!!'
        );


        if ($json != null) {
            //hace el login
            //conversion json a array php
            $params = json_decode($json);
        
            $email = (isset($params->email)) ? $params->email : null;
            $password = (isset($params->password)) ? $params->password : null;
        
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "Not Valid Email!!";
        
            $validate_email = $this->get("validator")->validate($email,$emailConstraint);
        
            if($email != null && count($validate_email) == 0 && $password != null){
                $jwt_auth =$this->get(JwtAuth::class);
                $signup = $jwt_auth->signup($email,$password);
        
                $data = array(
                    'status' => 'success',
                    'data' => 'Email Correct',
                    'signup' => $signup
                );
            }else{
                $data = array(
                    'status' => 'error',
                    'data' => 'Email Incorrect'
                );
            }
        
        }
        return $helpers->json($data);
    }

    public function pruebasAction(){
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('BackendBundle:User');
        $users = $userRepo->findAll();

        $helpers = $this->get(Helpers::class);
        return $helpers->json(array(
            'status' => 'success',
            'users' => $users
        ));
    }
}
