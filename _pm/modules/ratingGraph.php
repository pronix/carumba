<?php
/**
 * На входе
 *  @var float $rating
 *  @var string $tpl
 * На выходе
 *  Заменяет %rating% на HTML-код в $tpl
 */

    $rating = number_format( $rating, 1, '.', '' );
    list($r1, $r2) = explode('.', $rating);

    // установка граничных условий
    if ($r2 >= 8) {
        $r1++;
        $r2 = 0;
    } elseif ($r2 < 3) {
        $r2 = 0 ;
    } else {
        $r2 = 1;
    }

    $rcount = 5;
    $htmlRating = '<div class="irating" title="'.$rating.'">';
    for ($i=1; $i <= $r1; $i++) {
        $htmlRating .= '<img src="/images/star/mini_green.gif" width="14" height="13" border="0" alt="'.$rating.'" />';
        $rcount--;
    }
    if ($r2) {
        $htmlRating .= '<img src="/images/star/mini_half.gif" width="14" height="13" border="0" alt="'.$rating.'" />';
        $rcount--;
    }
    while ($rcount) {
        $htmlRating .= '<img src="/images/star/mini_grey.gif" width="14" height="13" border="0" alt="'.$rating.'" />';
        $rcount--;
    }
    $htmlRating .= '</div>';
	$tpl = str_replace('%rating%', $htmlRating, $tpl);

?>