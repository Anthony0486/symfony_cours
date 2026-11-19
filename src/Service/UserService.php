<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }
    public function getUsers(): array
    {
        return $this->userRepository->findAll() ?? [];
    }
    public function getUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }
    public function findOneUserListDtoById(int $id){
        return $this->userRepository->findOneUserListDtoById($id);
    }
    public function findAllUserListDtoById(){
        return $this->userRepository->findAllUserListDto();
    }

}