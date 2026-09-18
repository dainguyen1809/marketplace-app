<?php

use Infrastructure\Providers\InfrastructureServiceProvider;
use Infrastructure\Providers\MQServiceProvider;
use Infrastructure\User\Providers\UserInfrastructureServiceProvider;

return [
    InfrastructureServiceProvider::class,
    MQServiceProvider::class,
    UserInfrastructureServiceProvider::class,
];
