<?php

namespace LightWeightFramework\Container\Register;

interface ContainerRegisterInterface
{
    public function register(): void;

    public function supportsAutoloading(): bool;
}
