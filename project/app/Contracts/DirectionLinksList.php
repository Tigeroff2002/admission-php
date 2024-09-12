<?php

namespace App\Contracts;

use App\Contracts\DirectionLink;

final class DirectionLinksList
{
    /**
     * @var [] The users
     */
    private array $list;

    /**
     * The constructor.
     * 
     * @param DirectionLink ...$user The users
     */
    public function __construct(DirectionLink ...$directionLink) 
    {
        $this->list = $directionLink;
    }
    
    /**
     * Add user to list.
     *
     * @param DirectionLink $user The user
     *
     * @return void
     */
    public function add(DirectionLink $directionLink): void
    {
        $this->list[] = $directionLink;
    }

    /**
     * Get all users.
     *
     * @return DirectionLink[] The users
     */
    public function all(): array
    {
        return $this->list;
    }
}