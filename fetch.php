<?php

require "vendor/autoload.php";
use PHPHtmlParser\Dom;
$riders = [];
$stage = 7;

//fetch riders from velo.lv
for ($i = 1; $i <= 20; $i++) {
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

//X best stages


//calculate average points
foreach ($riders as &$rider) {
    $rider['skipped'] = 0;

    foreach ($rider['results'] as $key => $result) {
        if ($result == 0) {
            $rider['skipped']++;
            unset($rider['results'][$key]);
        }
    }

    if (count($rider['results']) == $stage) {
        $rider['average'] = array_sum($rider['results']) / count($rider['results']);
    }

    if (count($rider['results']) == ($stage - 1)) {
        $rider['results'][] = (array_sum($rider['results']) / (count($rider['results']))) / 1.15;
        $rider['average'] = array_sum($rider['results']) / count($rider['results']);
    }

    if (count($rider['results']) == ($stage - 2)) {
        $rider['results'][] = (array_sum($rider['results']) / (count($rider['results']))) / 1.25;
        $rider['average'] = array_sum($rider['results']) / (count($rider['results']));
    }

    if (count($rider['results']) >= 1 && count($rider['results']) < ($stage - 2)) {
        $rider['results'][] = (array_sum($rider['results']) / count($rider['results'])) / 1.25;
        while (count($rider['results']) < $stage) {//because 2 stages have 1 combined artificial result
            $rider['results'][] = 0;
        }

        $rider['average'] = array_sum($rider['results']) / (count($rider['results']));
    }

    if (count($rider['results']) == 0) {
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
<?php $passage = 1; ?>
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
            <td><?php echo $i ?></td>
            <td>
                <?php echo $passage; ?>
                <?php
                if ($i == 50) {
                    $passage++;
                }

                if ($i == 100) {
                    $passage++;
                }

                if ($i==200) {
                    $passage++;
                }
                if ($i==300) {
                    $passage++;
                }
                if ($i==500) {
                    $passage++;
                }
                if ($i==700) {
                    $passage++;
                }
                if ($i==900) {
                    $passage++;
                }
                if ($i==1100) {
                    $passage++;
                }
                if ($i==1300) {
                    $passage++;
                }
                if ($i==1500) {
                    $passage++;
                }
                if ($i==1700) {
                    $passage++;
                }
                $i++;

                    ?>
            </td>
            <td><?php echo $rider['number'] ?></td>
            <td><?php echo $rider['name'] ?></td>
            <td>
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
