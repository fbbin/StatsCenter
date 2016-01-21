<?php foreach ($data as $td):
    $bg_color = $td['succ_rate'] > 90 ? "#DFFFDF" : "#FFDFDF";
    ?>
    <tr style="background-color: <?=$bg_color?>;">
        <td><?= $td['interface_name'] ?></td>
        <td><?= empty($td['time_str']) ? '00:00 ~ 23:55' : $td['time_str'] ?></td>
        <td><?= number_format($td['total_count']) ?></td>
        <?php if (isset($td['time_key']) and !empty($td['time_key'])) {?>
            <td><a href="javascript: StatsG.openSuccPage(<?=$td['module_id']?>,<?=$td['interface_id']?>,<?=$td['time_key']?>)" style="color: green">
                    <?= number_format($td['succ_count']) ?></a></td>
            <td><a href="javascript: StatsG.openFailPage(<?=$td['module_id']?>,<?=$td['interface_id']?>,<?=$td['time_key']?>)"
                   style="color: <?=$td['fail_count'] > 0? "red" :'black'?>"><?= number_format($td['fail_count']) ?></a></td>
        <?php } else { ?>
            <td><a href="javascript: StatsG.openSuccPage(<?=$td['module_id']?>,<?=$td['interface_id']?>)" style="color: green">
                    <?= number_format($td['succ_count']) ?></a></td>
            <td><a href="javascript: StatsG.openFailPage(<?=$td['module_id']?>,<?=$td['interface_id']?>)"
                   style="color: <?=$td['fail_count'] > 0? "red" :'black'?>"><?= number_format($td['fail_count']) ?></a></td>
        <?php } ?>

        <td style="color: green"><?= $td['succ_rate'] ?>%</td>
        <td><?= $td['max_time'] ?>ms</td>
        <td><?= $td['min_time'] ?>ms</td>
        <td><?= $td['avg_time'] ?>ms</td>
        <td><?= $td['avg_fail_time'] ?>ms</td>
        <td>
            <?php if (!$this->isActiveMenu('stats', 'detail')):?><a href="/stats/detail/?module_id=<?= $td['module_id'] ?>&interface_id=<?= $td['interface_id'] ?>&date_key=<?= $_GET['date_key'] ?>"">查看明细</a>
            &nbsp;&nbsp;|&nbsp;&nbsp; <?php endif; ?>
            <?php if (isset($td['time_key']) and !empty($td['time_key'])) {?>
                <a href="/stats/history/?module_id=<?= $td['module_id'] ?>&interface_id=<?= $td['interface_id'] ?>&date_key=<?= $_GET['date_key'] ?>">历史数据对比</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="/stats/client/?module_id=<?= $td['module_id'] ?>&interface_id=<?= $td['interface_id'] ?>&date_key=<?= $_GET['date_key'] ?>&time_key=<?= $td['time_key']?>">主调明细</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="/stats/server/?module_id=<?= $td['module_id'] ?>&interface_id=<?= $td['interface_id'] ?>&date_key=<?= $_GET['date_key'] ?>&time_key=<?= $td['time_key']?>">被调明细</a>
            <?php } else { ?>
                <a href="/stats/history/?module_id=<?= $td['module_id'] ?>&interface_id=<?= $td['interface_id'] ?>&date_key=<?= $_GET['date_key'] ?>">历史数据对比</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="/stats/client/?module_id=<?= $td['module_id'] ?>&interface_id=<?= $td['interface_id'] ?>&date_key=<?= $_GET['date_key'] ?>">主调明细</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="/stats/server/?module_id=<?= $td['module_id'] ?>&interface_id=<?= $td['interface_id'] ?>&date_key=<?= $_GET['date_key'] ?>">被调明细</a>
            <?php } ?>
        </td>
    </tr>
<?php endforeach; ?>