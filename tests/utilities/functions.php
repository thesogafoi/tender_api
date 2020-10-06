<?php

function create($model, $numbers = 1, $customData = [], $state = null)
{
    if ($state !== null) {
        return factory($model, $numbers)->states($state)->create();
    } else {
        return factory($model, $numbers)->create($customData);
    }
}
