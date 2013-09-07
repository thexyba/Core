<?php

namespace OpenTribes\Core\Player\Activate;

use OpenTribes\Core\Player\Repository as PlayerRepository;
use OpenTribes\Core\Player\Roles\Repository as PlayerRolesRepository;
use OpenTribes\Core\Player\Roles as PlayerRoles;

use OpenTribes\Core\Player\Activate\Exception\NotExists as NotExistsException;
use OpenTribes\Core\Player\Activate\Exception\Invalid as InvalidCodeException;
use OpenTribes\Core\Player\Activate\Exception\Active as AlreadyActiveException;

class Interactor {

    protected $_playerRepository;
    protected $_playerRoleRepository;

    public function __construct(PlayerRepository $playerRepository, PlayerRolesRepository $playerRolesRepository) {
        $this->_playerRepository = $playerRepository;
        $this->_playerRoleRepository = $playerRolesRepository;
    }

    public function __invoke(Request $request) {
        $player = $this->_playerRepository->findByUsername($request->getUsername());
        if (!$player)
            throw new NotExistsException;
        if (!((bool)$player->getActivationCode()))
            throw new AlreadyActiveException;
        if ($player->getActivationCode() !== $request->getCode())
            throw new InvalidCodeException;

        $player->setActivationCode('');
        $roles = new PlayerRoles();
        $roles->addRole($request->getRole());
        $player->setRoles($roles);
        $this->_playerRepository->save($player);
        return new Response($player);
    }

}