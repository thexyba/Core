<?php
namespace OpenTribes\Core\Tile;

use OpenTribes\Core\Tile;
interface Repository{
    public function findByName($name);
    public function add(Tile $tile);
}