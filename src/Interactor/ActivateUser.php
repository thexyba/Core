<?php

namespace OpenTribes\Core\Interactor;

use OpenTribes\Core\Repository\User as UserRepository;
use OpenTribes\Core\Request\ActivateUser as ActivateUserRequest;
use OpenTribes\Core\Response\ActivateUser as ActivateUserResponse;
use OpenTribes\Core\Validator\ActivateUser as ActivateUserValidator;

/**
 * Description of ActivateUser
 *
 * @author BlackScorp<witalimik@web.de>
 */
class ActivateUser {

    private $userRepository;
    private $activateUserValidator;

    public function __construct(UserRepository $userRepository, ActivateUserValidator $activateUserValidator) {
        $this->userRepository        = $userRepository;
        $this->activateUserValidator = $activateUserValidator;
    }

    public function process(ActivateUserRequest $request, ActivateUserResponse $response) {
        $object = $this->activateUserValidator->getObject();
        $user   = $this->userRepository->findOneByUsername($request->getUsername());
     
        if (!$user && !$this->activateUserValidator->isValid()) {
            $response->errors = $this->activateUserValidator->getErrors();
            return false;
        }
        $object->codeIsValid = $request->getActivationCode() === $user->getActivationCode();
        if (!$this->activateUserValidator->isValid()) {
            $response->errors = $this->activateUserValidator->getErrors();
            return false;
        }
        $user->setActivationCode(null);
        $this->userRepository->replace($user);
        $response->username = $user->getUsername();
        return true;
    }

}
