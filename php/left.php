<div id="left">
    <script src="/js/left.js"></script>
    <div class="accordion" id="subslist">구독목록<br>
        <?php
        require_once '../conf/database_conf.php';
        $userinfo = $_SESSION['user'];
        $userseq = $userinfo->getSEQ();
        $sql4 = "SELECT USER_NAME,PIC,USER.SEQ,FOLLOW.LAST_CHECK,FOLLOW.LAST_UPDATE FROM publixher.TBL_USER USER INNER JOIN publixher.TBL_FOLLOW FOLLOW ON FOLLOW.SEQ_MASTER=USER.SEQ WHERE FOLLOW.SEQ_SLAVE=:SEQ_SLAVE";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':SEQ_SLAVE', $userseq);
        $prepare4->execute();
        $masters = $prepare4->fetchAll(PDO::FETCH_ASSOC);
        $masternum = count($masters);
        $msql = "SELECT TITLE,SEQ FROM publixher.TBL_CONTENT WHERE (SEQ_WRITER=:SEQ_WRITER AND DEL='N' AND TBL_CONTENT.FOR_SALE='Y' AND EXPOSE>1)ORDER BY SEQ DESC LIMIT 0,5";
        $mriprepare = $db->prepare($msql);
        for ($i = 0;
        $i < $masternum;
        $i++) {
        $mriprepare->bindValue(':SEQ_WRITER', $masters[$i]['SEQ'], PDO::PARAM_STR);
        $mriprepare->execute();
        $recontent = $mriprepare->fetchAll(PDO::FETCH_ASSOC);
        echo "<img src='" . $masters[$i]['PIC'] . "' class='subsprofile'><a href='/php/profile.php?id=" . $masters[$i]['SEQ'] . "' class='nameuser'>" . $masters[$i]['USER_NAME'] . "</a>";
        if ($masters[$i]['LAST_CHECK'] < $masters[$i]['LAST_UPDATE']) {
            echo "<span class='newcontent' data-substarget='" . $masters[$i]['SEQ'] . "'>new</span>";
        }
        ?>
        <div class="accordion-group">
            <div class="accordion-heading">
                    <span class="accordion-toggle caret" data-toggle="collapse" data-parent="#subslist"
                          href="#collapse<?= $masters[$i]['SEQ'] ?>">
                    </span>
            </div>
            <div id="collapse<?= $masters[$i]['SEQ'] ?>" class="accordion-body collapse well well-sm">
                <div class="accordion-inner">
                    <?php
                    for ($j = 0; $j < count($recontent); $j++) {
                        echo "<div><a href='/php/getItem.php?iid=" . $recontent[$j]['SEQ'] . "'>" . $recontent[$j]['TITLE'] . "</a></div>";
                    }
                    echo "</div></div></div>";
                    }
                    ?>

                </div>
            </div>