<?php
namespace App\Domain\Repository;

use DateTimeInterface;

abstract class SerializeDtoAbstract
{
    public function toArray(): array
    {
        $array = [];
        $reflection = new \ReflectionClass($this);

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($this);

            if (is_object($value) && property_exists($value, 'value')) {
                $array[$property->getName()] = $value->value;

            } elseif ($value instanceof DateTimeInterface) {
                $array[$property->getName()] = $value->format('Y-m-d H:i:s');

            } elseif ($value && method_exists($value, 'getId')) {
                $array[$property->getName()] = $value->getId();

            } elseif (is_object($value) && method_exists($value, 'toArray')) {
                $array[$property->getName()] = $value->toArray();

            } else {
                $array[$property->getName()] = $value;
            }
        }

        return $array;
    }

}