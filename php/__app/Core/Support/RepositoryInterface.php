<?php


namespace Core\Support;


use Core\Model\Model;

interface RepositoryInterface
{
   public function getModel();
   public function getDtoMap();
   public function dataToModels();
}