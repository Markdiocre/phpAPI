<?php

interface AuthInterface{
    public function login($data);
    public function logout();
    public function register($data);
}
