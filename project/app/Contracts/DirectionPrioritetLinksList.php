<?php

namespace App\Contracts;

use App\Contracts\DirectionPrioritetLink;

final class DirectionPrioritetLinksList
{
    /**
     * @var [] The users
     */
    private array $list;

    /**
     * The constructor.
     * 
     * @param DirectionPrioritetLink ...$user The users
     */
    public function __construct(DirectionPrioritetLink ...$directionPrioritetLink) 
    {
        $this->list = $directionPrioritetLink;
    }
    
    /**
     * Add user to list.
     *
     * @param DirectionPrioritetLink $user The user
     *
     * @return void
     */
    public function add(DirectionPrioritetLink $directionPrioritetLink): void
    {
        $this->list[] = $directionPrioritetLink;
    }

    /**
     * Get all users.
     *
     * @return DirectionPrioritetLink[] The users
     */
    public function all(): array
    {
        return $this->list;
    }
}