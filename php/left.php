<div id="left">
    <script src="/js/left.js"></script>
    <div class="accordion" id="subslist">
        <p id="subscribe-btn">구독</p>
        <?php
        require_once '../conf/database_conf.php';
        $userinfo = $_SESSION['user'];
        $userID = $userinfo->getID();
        $sql4 = "SELECT USER_NAME,REPLACE(PIC,'profile','crop24') AS PIC,USER.ID,FOLLOW.LAST_CHECK,FOLLOW.LAST_UPDATE FROM publixher.TBL_USER USER INNER JOIN publixher.TBL_FOLLOW FOLLOW ON FOLLOW.ID_MASTER=USER.ID WHERE FOLLOW.ID_SLAVE=:ID_SLAVE";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':ID_SLAVE', $userID);
        $prepare4->execute();
        $masters = $prepare4->fetchAll(PDO::FETCH_ASSOC);
        $masternum = count($masters);
        $msql = "SELECT TITLE,ID,WRITE_DATE FROM publixher.TBL_CONTENT WHERE (ID_WRITER=:ID_WRITER AND DEL='N' AND TBL_CONTENT.FOR_SALE='Y' AND EXPOSE>1)ORDER BY WRITE_DATE DESC LIMIT 0,5";
        $mriprepare = $db->prepare($msql);
        for ($i = 0;
        $i < $masternum;
        $i++) {
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
                          href="#subscribe-collapse<?= $masters[$i]['ID'] ?>"><span class="caret"></span>
                    </span>
            </div>
            <div class="sungho-writable"></div>
            <div id="subscribe-collapse<?= $masters[$i]['ID'] ?>" class="accordion-body collapse well well-sm">
                <div class="accordion-inner">
                    <?php
                    for ($j = 0; $j < count($recontent); $j++) {
                        echo "<div><a href='/content/" . $recontent[$j]['ID'] . "'>" . $recontent[$j]['TITLE'] . "</a></div>";
                    }
                    echo "</div></div></div>";
                    }
                    ?>

                    <div id="community" class="accordion">
                        <p id="community-btn">커뮤니티</p>
                        <?php
                        $sql = "SELECT
  USER_NAME,
  REPLACE(PIC, 'profile', 'crop24') AS PIC,
  USER.ID
FROM publixher.TBL_USER AS USER 
INNER JOIN publixher.TBL_FRIENDS AS COMM 
ON COMM.ID_FRIEND = USER.ID
WHERE USER.COMMUNITY = 1 AND COMM.ID_USER=:ID_USER";
                        $prepare5 = $db->prepare($sql);
                        $prepare5->bindValue(':ID_USER', $userID);
                        $prepare5->execute();
                        $community = $prepare5->fetchAll(PDO::FETCH_ASSOC);
                        $communitynum = count($community);
                        $csql = "SELECT
  IF(TITLE IS NOT NULL,TITLE,LEFT(BODY_TEXT,20)) AS TITLE,
  ID,
  WRITE_DATE
FROM publixher.TBL_CONTENT
WHERE ID_TARGET = :ID_TARGET AND DEL = 'N' AND EXPOSE > 1
ORDER BY WRITE_DATE DESC
LIMIT 5";
                        $cprepare = $db->prepare($csql);
                        for ($i = 0;
                        $i < $communitynum;
                        $i++) {
                        $cprepare->bindValue(':ID_TARGET', $community[$i]['ID']);
                        $cprepare->execute();
                        $comcontent = $cprepare->fetchAll(PDO::FETCH_ASSOC);
                        $comcontnum = count($comcontent);
                        echo "<div class='subpic-wrap'><img src='" . $community[$i]['PIC'] . "' class='comprofile'></div><a href='/profile/" . $community[$i]['ID'] . "' class='nameuser'>" . $community[$i]['USER_NAME'] . "</a>";
                        ?>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                        <span class="accordion-toggle" data-toggle="collapse" data-parent="#community"
                              href="#community-collapse<?= $community[$i]['ID'] ?>"><span class="caret"></span>
                        </span>
                            </div>
                            <div class="sungho-writable"></div>
                            <div id="community-collapse<?= $community[$i]['ID'] ?>" class="accordion-body collapse well well-sm">
                                <div class="accordion-inner">
                                    <?php
                                    for ($j = 0; $j < $comcontnum; $j++) {
                                        echo "<div><a href='/content/" . $comcontent[$j]['ID'] . "'>" . $comcontent[$j]['TITLE'] . "</a></div>";
                                    }
                                    echo "</div></div></div>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div id="report-div">
                    <textarea id="report" class="form-control"
                              placeholder="사용시 오류사항 및 건의사항을 적어서 보내주세요."></textarea>
                                <button class="btn-default" id="report-button" onclick="blur(this)">보내기</button>
                            </div>


                        </div><!--                여기까지 left-->