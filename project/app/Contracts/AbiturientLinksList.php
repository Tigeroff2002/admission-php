<?php

namespace App\Contracts;

use App\Contracts\AbiturientLink;

final class AbiturientLinksList
{
    /**
     * @var [] The users
     */
    private array $list;

    /**
     * The constructor.
     * 
     * @param AbiturientLink...$user The users
     */
    public function __construct(AbiturientLink ...$abiturientLink) 
    {
        $this->list = $abiturientLink;
    }
    
    /**
     * Add user to list.
     *
     * @param AbiturientLink $user The user
     *
     * @return void
     */
    public function add(AbiturientLink $abiturientLink): void
    {
        $this->list[] = $abiturientLink;
    }

    /**
     * Get all users.
     *
     * @return AbiturientLink[] The users
     */
    public function all(): array
    {
        return $this->list;
    }
}