<?php
    class MainPage extends OBModel {
        public $username = "No name";
        public $last_update;

        public function MainPage() {
            $this->last_update = time();
        }
    }