<?php

/*
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * Este archivo pertenece a la aplicación de prueba Cupon.
 * El código fuente de la aplicación incluye un archivo llamado LICENSE
 * con toda la información sobre el copyright y la licencia.
 */

namespace AppBundle\Manager;

use AppBundle\Entity\Usuario;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UsuarioManager
{
    private $em;
    private $encoderFactory;
    private $tokenStorage;

    public function __construct(ObjectManager $entityManager, EncoderFactoryInterface $encoderFactory, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->encoderFactory = $encoderFactory;
        $this->tokenStorage = $tokenStorage;
    }

    public function guardar(Usuario $usuario)
    {
        if (null !== $usuario->getPasswordEnClaro()) {
            $this->codificarPassword($usuario);
        }

        $this->em->persist($usuario);
        $this->em->flush();
    }
    
    public function loguear(Usuario $usuario)
    {
        $token = new UsernamePasswordToken($usuario, null, 'frontend', $usuario->getRoles());
        $this->tokenStorage->setToken($token);
    }

    private function codificarPassword(Usuario $usuario)
    {
        $encoder = $this->encoderFactory->getEncoder($usuario);
        $passwordCodificado = $encoder->encodePassword($usuario->getPasswordEnClaro(), $usuario->getSalt());
        $usuario->setPassword($passwordCodificado);
    }
}
