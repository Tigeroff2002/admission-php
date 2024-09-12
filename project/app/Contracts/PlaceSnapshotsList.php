<?php

namespace App\Contracts;

use App\Contracts\PlaceSnapshot;

final class PlaceSnapshotsList
{
    /**
     * @var [] The users
     */
    private array $list;

    /**
     * The constructor.
     * 
     * @param PlaceSnapshot ...$user The users
     */
    public function __construct(PlaceSnapshot ...$placeSnapshot) 
    {
        $this->list = $placeSnapshot;
    }
    
    /**
     * Add user to list.
     *
     * @param PlaceSnapshot $user The user
     *
     * @return void
     */
    public function add(PlaceSnapshot $placeSnapshot): void
    {
        $this->list[] = $placeSnapshot;
    }

    /**
     * Get all users.
     *
     * @return PlaceSnapshot[] The users
     */
    public function all(): array
    {
        return $this->list;
    }
}