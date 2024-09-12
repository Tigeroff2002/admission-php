<?php

namespace App\Contracts;

use App\Contracts\DirectionShortLink;

final class DirectionShortLinksList
{
    /**
     * @var [] The users
     */
    private array $list;

    /**
     * The constructor.
     * 
     * @param DirectionShortLink ...$user The users
     */
    public function __construct(DirectionShortLink ...$directionLink) 
    {
        $this->list = $directionLink;
    }
    
    /**
     * Add user to list.
     *
     * @param DirectionShortLink $user The user
     *
     * @return void
     */
    public function add(DirectionShortLink $directionLink): void
    {
        $this->list[] = $directionLink;
    }

    /**
     * Get all users.
     *
     * @return DirectionShortLink[] The users
     */
    public function all(): array
    {
        return $this->list;
    }
}