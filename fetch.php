<?php

require "vendor/autoload.php";
use PHPHtmlParser\Dom;
$riders = [];
$stage = 6;

//fetch riders from velo.lv
for ($i = 1; $i <= 17; $i++) {
    $dom = new Dom;
    $dom->loadFromUrl("https://velo.lv/lv/sacensibas/67/kopvertejums/?search=&distance=61&group=&gender=&page=$i");

    $table = $dom->find('tbody tr');

    foreach ($table as $item) {
        $riders[$item->find('.number')->innerHtml] = [
            'name' => $item->find('.first_name')->innerHtml . ' ' . $item->find('.last_name')->innerHtml,
            'number' => $item->find('.number')->innerHtml,
        ];

        for ($j = 1; $j <= $stage; $j++) {
            $riders[$item->find('.number')->innerHtml]['results'][] = $item->find(".distance_points$j")->innerHtml;
        }
    }
}

//calculate average points
foreach ($riders as &$rider) {
    $rider['skipped'] = 0;

    foreach ($rider['results'] as $key => $result) {
        if ($result == 0) {
            $rider['skipped']++;
            unset($rider['results'][$key]);
        }
    }

    if ($rider['skipped'] == 0) {
        $rider['average'] = array_sum($rider['results']) / $stage;
    }


    if ($rider['skipped'] == 1) {
        $rider['results'][] = (array_sum($rider['results']) / ($stage - 1)) / 1.15;
        $rider['average'] = array_sum($rider['results']) / $stage;
    }

    if ($rider['skipped'] == 2) {
        $average = array_sum($rider['results']) / ($stage - 2);
        $rider['results'][] = $average / 1.15;
        $rider['results'][] = $average / 1.25;
        $rider['average'] = array_sum($rider['results']) / $stage;
    }

    if ($rider['skipped'] == 3) {
        $average = array_sum($rider['results']) / ($stage - 3);
        $rider['results'][] = $average / 1.15;
        $rider['results'][] = $average / 1.25;
        $rider['results'][] = 0;
        $rider['average'] = array_sum($rider['results']) / $stage;
    }

    if ($rider['skipped'] == 4) {
        $average = array_sum($rider['results']) / ($stage - 4);
        $rider['results'][] = $average / 1.15;
        $rider['results'][] = $average / 1.25;
        $rider['results'][] = 0;
        $rider['results'][] = 0;
        $rider['average'] = array_sum($rider['results']) / $stage;
    }

    if ($rider['skipped'] == 5) {
        $average = array_sum($rider['results']) / ($stage - 5);
        $rider['results'][] = $average / 1.15;
        $rider['results'][] = $average / 1.25;
        $rider['results'][] = 0;
        $rider['results'][] = 0;
        $rider['results'][] = 0;
        $rider['average'] = array_sum($rider['results']) / $stage;
    }

    if ($rider['skipped'] == 6) {
        $rider['average'] = 0;
    }
}

//sort by average
usort($riders, function($a, $b) {
    return $b['average'] <=> $a['average'];
});




//foreach ($riders as &$rider) {
//    $rider['sum'] = array_sum($rider['results']);
//}
//
////sort by average
//usort($riders, function($a, $b) {
//    return $b['sum'] <=> $a['sum'];
//});

?>
<?php $i = 1; ?>
<table>
    <thead>
    <tr>
        <td>#</td>
        <td>Numurs</td>
        <td>Vārds</td>
        <td>Vidējie punkti</td>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($riders as $key => $rider): ?>
        <tr>
            <td><?php echo $i++ ?></td>
            <td><?php echo $rider['number'] ?></td>
            <td><?php echo $rider['name'] ?></td>
            <td>
<!--                --><?php //echo $rider['sum'] ?>
                <?php echo number_format($rider['average'], 1) ?>
            </td>
            <td><?php echo $rider['skipped'] ?></td>



            <?php foreach ($rider['results'] as $r) {
                echo "<td>";
                echo number_format($r,1);
                echo "</td>";
            } ?>

        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
