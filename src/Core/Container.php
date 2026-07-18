<?php
// Datoteka: src/Core/Container.php

class Container {
    private array $bindings = [];
    private array $instances = [];

    // Povezuje ključ (ime klase) s funkcijom koja stvara taj objekt
    public function bind(string $key, callable $resolver): void {
        $this->bindings[$key] = $resolver;
    }

    // Dohvaća instancu (Singleton pristup za pojedinačni zahtjev)
    public function get(string $key) {
        if (!isset($this->instances[$key])) {
            if (!isset($this->bindings[$key])) {
                throw new Exception("Nije pronađen binding za klasu: {$key}");
            }
            $this->instances[$key] = call_user_func($this->bindings[$key], $this);
        }

        return $this->instances[$key];
    }
}