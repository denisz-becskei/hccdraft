<?php

/**
 * @param $map - The name of the map we want images for
 * @param $index - The image index
 * @return string - The relative link to the map images
 *
 * Used to select the actual map's image of index
 */
function select_map($map, $index) {
    switch ($map) {
        case "Ascension Peak":
            switch ($index) {
                case 0:
                    return "images/maps/Ascension Peak/AP1.png";
                case 1:
                    return "images/maps/Ascension Peak/AP2.png";
                case 2:
                    return "images/maps/Ascension Peak/AP3.png";
                case 3:
                    return "images/maps/Ascension Peak/AP4.png";
                case 4:
                    return "images/maps/Ascension Peak/AP5.png";
                case 5:
                    return "images/maps/Ascension Peak/AP6.png";
                case 6:
                    return "images/maps/Logos/AP.png";
                default:
                    echo "HOL' UP!";
            }
            break;
        case "Bazaar":
            switch ($index) {
                case 0:
                    return "images/maps/Bazaar/B1.png";
                case 1:
                    return "images/maps/Bazaar/B2.png";
                case 2:
                    return "images/maps/Bazaar/B3.png";
                case 3:
                    return "images/maps/Bazaar/B4.png";
                case 4:
                    return "images/maps/Bazaar/B5.png";
                case 5:
                    return "images/maps/Bazaar/B6.png";
                case 6:
                    return "images/maps/Logos/B.png";
                default:
                    echo "HOL' UP";
            }
            break;
        case "Brightmarsh":
            switch ($index) {
                case 0:
                    return "images/maps/Brightmarsh/BM1.png";
                case 1:
                    return "images/maps/Brightmarsh/BM2.png";
                case 2:
                    return "images/maps/Brightmarsh/BM3.png";
                case 3:
                    return "images/maps/Brightmarsh/BM4.png";
                case 4:
                    return "images/maps/Brightmarsh/BM5.png";
                case 5:
                    return "images/maps/Brightmarsh/BM6.png";
                case 6:
                    return "images/maps/Logos/BM.png";
                default:
                    echo "HOL' UP";
            }
            break;
        case "Fish Market":
            switch ($index) {
                case 0:
                    return "images/maps/Fish Market/FM1.png";
                case 1:
                    return "images/maps/Fish Market/FM2.png";
                case 2:
                    return "images/maps/Fish Market/FM3.png";
                case 3:
                    return "images/maps/Fish Market/FM4.png";
                case 4:
                    return "images/maps/Fish Market/FM5.png";
                case 5:
                    return "images/maps/Fish Market/FM6.png";
                case 6:
                    return "images/maps/Logos/FM.png";
                default:
                    echo "HOL' UP";
            }
            break;
        case "Frog Isle":
            switch ($index) {
                case 0:
                    return "images/maps/Frog Isle/FI1.png";
                case 1:
                    return "images/maps/Frog Isle/FI2.png";
                case 2:
                    return "images/maps/Frog Isle/FI3.png";
                case 3:
                    return "images/maps/Frog Isle/FI4.png";
                case 4:
                    return "images/maps/Frog Isle/FI5.png";
                case 5:
                    return "images/maps/Frog Isle/FI6.png";
                case 6:
                    return "images/maps/Logos/FI.png";
                default:
                    echo "HOL' UP";
            }
            break;
        case "Frozen Guard":
            switch ($index) {
                case 0:
                    return "images/maps/Frozen Guard/FG1.png";
                case 1:
                    return "images/maps/Frozen Guard/FG2.png";
                case 2:
                    return "images/maps/Frozen Guard/FG3.png";
                case 3:
                    return "images/maps/Frozen Guard/FG4.png";
                case 4:
                    return "images/maps/Frozen Guard/FG5.png";
                case 5:
                    return "images/maps/Frozen Guard/FG6.png";
                case 6:
                    return "images/maps/Logos/FG.png";
                default:
                    echo "HOL' UP";
            }
            break;
        case "Ice Mines":
            switch ($index) {
                case 0:
                    return "images/maps/Ice Mines/IM1.png";
                case 1:
                    return "images/maps/Ice Mines/IM2.png";
                case 2:
                    return "images/maps/Ice Mines/IM3.png";
                case 3:
                    return "images/maps/Ice Mines/IM4.png";
                case 4:
                    return "images/maps/Ice Mines/IM5.png";
                case 5:
                    return "images/maps/Ice Mines/IM6.png";
                case 6:
                    return "images/maps/Logos/IM.png";
                default:
                    echo "HOL' UP";
            }
            break;
        case "Jaguar Falls":
            switch ($index) {
                case 0:
                    return "images/maps/Jaguar Falls/JF1.png";
                case 1:
                    return "images/maps/Jaguar Falls/JF2.png";
                case 2:
                    return "images/maps/Jaguar Falls/JF3.png";
                case 3:
                    return "images/maps/Jaguar Falls/JF4.png";
                case 4:
                    return "images/maps/Jaguar Falls/JF5.png";
                case 5:
                    return "images/maps/Jaguar Falls/JF6.png";
                case 6:
                    return "images/maps/Logos/JF.png";
                default:
                    echo "HOL' UP";
            }
            break;
        case "Serpent Beach":
            switch ($index) {
                case 0:
                    return "images/maps/Serpent Beach/SB1.png";
                case 1:
                    return "images/maps/Serpent Beach/SB2.png";
                case 2:
                    return "images/maps/Serpent Beach/SB3.png";
                case 3:
                    return "images/maps/Serpent Beach/SB4.png";
                case 4:
                    return "images/maps/Serpent Beach/SB5.png";
                case 5:
                    return "images/maps/Serpent Beach/SB6.png";
                case 6:
                    return "images/maps/Logos/SB.png";
                default:
                    echo "HOL' UP";
            }
            break;
        case "Shattered Desert":
            switch ($index) {
                case 0:
                    return "images/maps/Shattered Desert/SD1.png";
                case 1:
                    return "images/maps/Shattered Desert/SD2.png";
                case 2:
                    return "images/maps/Shattered Desert/SD3.png";
                case 3:
                    return "images/maps/Shattered Desert/SD4.png";
                case 4:
                    return "images/maps/Shattered Desert/SD5.png";
                case 5:
                    return "images/maps/Shattered Desert/SD6.png";
                case 6:
                    return "images/maps/Logos/SD.png";
                default:
                    echo "HOL' UP";
            }
            break;
        case "Splitstone Quarry":
            switch ($index) {
                case 0:
                    return "images/maps/Splitstone Quarry/SQ1.png";
                case 1:
                    return "images/maps/Splitstone Quarry/SQ2.png";
                case 2:
                    return "images/maps/Splitstone Quarry/SQ3.png";
                case 3:
                    return "images/maps/Splitstone Quarry/SQ4.png";
                case 4:
                    return "images/maps/Splitstone Quarry/SQ5.png";
                case 5:
                    return "images/maps/Splitstone Quarry/SQ6.png";
                case 6:
                    return "images/maps/Logos/SQ.png";
                default:
                    echo "HOL' UP";
            }
            break;
        case "Stone Keep":
            switch ($index) {
                case 0:
                    return "images/maps/Stone Keep/SK1.png";
                case 1:
                    return "images/maps/Stone Keep/SK2.png";
                case 2:
                    return "images/maps/Stone Keep/SK3.png";
                case 3:
                    return "images/maps/Stone Keep/SK4.png";
                case 4:
                    return "images/maps/Stone Keep/SK5.png";
                case 5:
                    return "images/maps/Stone Keep/SK6.png";
                case 6:
                    return "images/maps/Logos/SK.png";
                default:
                    echo "HOL' UP";
            }
            break;
        case "Timber Mill":
            switch ($index) {
                case 0:
                    return "images/maps/Timber Mill/TM1.png";
                case 1:
                    return "images/maps/Timber Mill/TM2.png";
                case 2:
                    return "images/maps/Timber Mill/TM3.png";
                case 3:
                    return "images/maps/Timber Mill/TM4.png";
                case 4:
                    return "images/maps/Timber Mill/TM5.png";
                case 5:
                    return "images/maps/Timber Mill/TM6.png";
                case 6:
                    return "images/maps/Logos/TM.png";
                default:
                    echo "HOL' UP";
            }
            break;
        case "Warder's Gate":
            switch ($index) {
                case 0:
                    return "images/maps/Warder Gate/WG1.png";
                case 1:
                    return "images/maps/Warder Gate/WG2.png";
                case 2:
                    return "images/maps/Warder Gate/WG3.png";
                case 3:
                    return "images/maps/Warder Gate/WG4.png";
                case 4:
                    return "images/maps/Warder Gate/WG5.png";
                case 5:
                    return "images/maps/Warder Gate/WG6.png";
                case 6:
                    return "images/maps/Logos/WG.png";
                default:
                    echo "HOL' UP";
            }
            break;
    }
    return null;
}