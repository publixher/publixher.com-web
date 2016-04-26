<div id="left">
    <script src="/js/left.js"></script>
    <div class="accordion" id="subslist">구독목록<br>
        <?php
        require_once '../conf/database_conf.php';
        $userinfo = $_SESSION['user'];
        $userID = $userinfo->getID();
        $sql4 = "SELECT USER_NAME,REPLACE(PIC,'profile','crop34') AS PIC,USER.ID,FOLLOW.LAST_CHECK,FOLLOW.LAST_UPDATE FROM publixher.TBL_USER USER INNER JOIN publixher.TBL_FOLLOW FOLLOW ON FOLLOW.ID_MASTER=USER.ID WHERE FOLLOW.ID_SLAVE=:ID_SLAVE";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':ID_SLAVE', $userID);
        $prepare4->execute();
        $masters = $prepare4->fetchAll(PDO::FETCH_ASSOC);
        $masternum = count($masters);
        $msql = "SELECT TITLE,ID,WRITE_DATE FROM publixher.TBL_CONTENT WHERE (ID_WRITER=:ID_WRITER AND DEL='N' AND TBL_CONTENT.FOR_SALE='Y' AND EXPOSE>1)ORDER BY WRITE_DATE DESC LIMIT 0,5";
        $mriprepare = $db->prepare($msql);
        for ($i = 0;$i < $masternum;$i++) {
        $mriprepare->bindValue(':ID_WRITER', $masters[$i]['ID'], PDO::PARAM_STR);
        $mriprepare->execute();
        $recontent = $mriprepare->fetchAll(PDO::FETCH_ASSOC);
        echo "<div class='subpic-wrap'><img src='" . $masters[$i]['PIC'] . "' class='subsprofile'></div><a href='/profile/" . $masters[$i]['ID'] . "' class='nameuser'>" . $masters[$i]['USER_NAME'] . "</a>";
        if ($masters[$i]['LAST_CHECK'] < $masters[$i]['LAST_UPDATE']) {
            echo "<span class='newcontent' data-substarget='" . $masters[$i]['ID'] . "'>new</span>";
        }
        ?>
        <div class="accordion-group">
            <div class="accordion-heading">
                    <span class="accordion-toggle" data-toggle="collapse" data-parent="#subslist"
                          href="#collapse<?= $masters[$i]['ID'] ?>"><span class="caret"></span>
                    </span>
            </div>
            <div class="sungho-writable"></div>
            <div id="collapse<?= $masters[$i]['ID'] ?>" class="accordion-body collapse well well-sm">
                <div class="accordion-inner">
                    <?php
                    for ($j = 0; $j < count($recontent); $j++) {
                        echo "<div><a href='/content/" . $recontent[$j]['ID'] . "'>" . $recontent[$j]['TITLE'] . "</a></div>";
                    }
                    echo "</div></div></div>";
                    }
                    ?>

                </div>
            </div>