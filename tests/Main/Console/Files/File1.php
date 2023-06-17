<?php

declare(strict_types=1);

echo "Hello, world!";

$variable = 42;

if ($variable > 30) {
    echo "Variable is greater than 30.";
} else {
    echo "Variable is not greater than 30.";
}

function greet($name) {
    echo "Hello, $name!";
}

greet("Alice");

$fruits = array("apple", "banana", "orange");

foreach ($fruits as $fruit) {
    echo "I like $fruit.";
}

class Person {
    private $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }
}

$person = new Person("Bob");

echo $person->getName();