<?php

declare(strict_types=1);

namespace Unit\Reflection\Data;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'my_test_table')]
class MyTestEntity
{
    #[ORM\Column(name: 'id')]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    public int $id;

    #[ORM\Column(name: 'nazev')]
    public string $name;

    #[ORM\Column]
    public float $price;
}