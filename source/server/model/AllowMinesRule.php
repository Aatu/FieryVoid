<?php

class AllowMinesRule implements JsonSerializable {

    public function getRuleName() {
        return 'allowMines';
    }

    public function jsonSerialize(): mixed {
        return 1;
    }
}
