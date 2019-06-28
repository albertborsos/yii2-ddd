<?php

namespace albertborsos\ddd\interfaces;

interface EntityInterface extends BusinessObject
{
    public function primaryKey();
}
