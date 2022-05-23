<?php
    /* Here we select the appropriate sounds regarding which picking phase we're in */
    function sound_selector($champion) {
        return "sounds/picking_phase/".$champion.".ogg";
    }

    function ban_sound_selector($champion) {
        return "sounds/banning_phase/".$champion.".ogg";
    }
