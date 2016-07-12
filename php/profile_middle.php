<div id="middle">
    <script src="//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.5.2/jquery.fileupload.min.js"></script>
    <?php
    require_once '../conf/getTarget.php';
    //글쓰기권한,공개설정 탐색
    $w = "SELECT WRITEAUTH,EXPAUTH,VIEWAUTH FROM publixher.TBL_USER WHERE ID=:ID";
    $p = $db->prepare($w);
    $p->bindValue(':ID', $targetid);
    $p->execute();
    $auth = $p->fetch(PDO::FETCH_ASSOC);
    $writeauth = $auth['WRITEAUTH'];
    $expauth = $auth['EXPAUTH'];
    $viewauth=$auth['VIEWAUTH'];
    //자신일경우
    $I = $targetid == $userID ? 1 : 0;

    //친구목록과 구독목록 불러오기
    //구독목록
    $sql = "SELECT
  USER.USER_NAME,
  REPLACE(USER.PIC, 'profile', 'crop50') AS PIC,
  USER.ID
FROM publixher.TBL_USER AS USER
  INNER JOIN publixher.TBL_FOLLOW AS FOLLOW
    ON FOLLOW.ID_SLAVE=:USER_ID
WHERE USER.ID = FOLLOW.ID_MASTER
ORDER BY USER.USER_NAME ASC";
    $prepare = $db->prepare($sql);
    $prepare->execute(array('USER_ID' => $targetid));
    $master_list = $prepare->fetchAll(PDO::FETCH_ASSOC);
    $sql = "SELECT
  USER.USER_NAME,
  REPLACE(USER.PIC, 'profile', 'crop50') AS PIC,
  USER.ID
FROM publixher.TBL_USER AS USER
INNER JOIN publixher.TBL_FRIENDS AS FRIEND
  ON FRIEND.ID_USER=:USER_ID AND ALLOWED='Y'
WHERE USER.ID=FRIEND.ID_FRIEND ORDER BY USER.USER_NAME ASC";
    $prepare = $db->prepare($sql);
    $prepare->execute(array('USER_ID' => $targetid));
    $friend_list = $prepare->fetchAll(PDO::FETCH_ASSOC);
    //위에 버튼그룹
    if ($I) {
//        내프로필일경우
        ?>
        <script>
            var I = true;
            var frelation = false;
        </script>
        <div class="btn-group" role="group" id="profile-middle-nav">
            <!--            친구목록-->
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                    친구목록(<?= count($friend_list) ?>)
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu hasInput" role="menu" id="frielist">
                    <li><input type="text" class="form-control"></li>
                    <li style="display: none"><button type="button" onclick="getFacebookFriend()" data-toggle="modal" data-target="#friend-recommend">친구추천받기</button></li>
                    <?php
                    $arr = array();
                    for ($i = 0; $i < count($friend_list); $i++) {
                        echo "<li><div class='friend-list-pic-wrap'><img src='" . $friend_list[$i]['PIC'] . "'></div><a href='/profile/" . $friend_list[$i]['ID'] . "' class='nameuser'>" . $friend_list[$i]['USER_NAME'] . "</a></li>";
                        $arr[] = $friend_list[$i]['USER_NAME'];
                    }
                    $arr = json_encode($arr);
                    echo "<script>var frievar=${arr};</script>";
                    ?>
                </ul>
            </div>
            <!--            구독목록-->
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                    구독목록(<?= count($master_list) ?>)
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu hasInput" role="menu" id="subslist">
                    <li><input type="text" class="form-control"></li>
                    <?php
                    $arr = array();
                    for ($i = 0; $i < count($master_list); $i++) {
                        echo "<li><div class='subs-list-pic-wrap'><img src='" . $master_list[$i]['PIC'] . "'></div><a href='/profile/" . $master_list[$i]['ID'] . "' class='nameuser'>" . $master_list[$i]['USER_NAME'] . "</a></li>";
                        $arr[] = $master_list[$i]['USER_NAME'];
                    }
                    $arr = json_encode($arr);
                    echo "<script>var subsvar=${arr};</script>";
                    ?>
                </ul>
            </div>
            <!--            친구신청목록-->
            <?php
            //친구요청(ID_FRIEND에 내 아이디가 들어가 있고 ALLOWED가 N인것들의 수와 목록을 보여주는것)
            $sql2 = "SELECT
  FRIEND.SEQ,
  FRIEND.ID_USER,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile', 'crop50') AS PIC
FROM publixher.TBL_FRIENDS AS FRIEND
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = FRIEND.ID_USER AND ALLOWED = 'N' AND ID_FRIEND = :ID_FRIEND
ORDER BY USER_NAME ASC";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':ID_FRIEND', $targetid, PDO::PARAM_STR);
            $prepare2->execute();
            $freq_list = $prepare2->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                    친구요청(<span id="frequestnum"><?= count($freq_list) ?></span>)
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu hasInput" role="menu" id="freqlist">
                    <li><input type="text" class="form-control"></li>
                    <?php
                    if (count($freq_list) == 0) {
                        echo '<li><a>친구요청이 없습니다</a></li>';
                    } else {
                        $arr = array();
                        for ($i = 0; $i < count($freq_list); $i++) {
                            echo "<li><div class='freq-list-pic-wrap'><img src='" . $freq_list[$i]['PIC'] . "'></div><a href='/profile/" . $freq_list[$i]['ID_USER'] . "' class='nameuser'>" . $freq_list[$i]['USER_NAME'] . "</a> <a requestid='" . $freq_list[$i]['SEQ'] . "' fid='" . $freq_list[$i]['ID_USER'] . "' class='friendok freqanswer'>O</a> <a requestid='" . $freq_list[$i]['SEQ'] . "' class='friendno freqanswer'>X</a></li>";
                            $arr[] = $freq_list[$i]['USER_NAME'];
                        }
                        $arr = json_encode($arr);
                        echo "<script>var freqvar=${arr};</script>";

                    } ?>
                </ul>
            </div>
            <!--            설정-->
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                    설정
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu hasSelect" role="menu" id="confAut">
                    <li><b>글쓰기 권한</b></li>
                    <li class="radio"><label><input type="radio" name="writeAuth"
                                                    value="0" <?php if ($writeauth == 0) echo 'checked' ?>>나만</label>
                    </li>
                    <li class="radio"><label><input type="radio" name="writeAuth"
                                                    value="1" <?php if ($writeauth == 1) echo 'checked' ?>>친구</label>
                    </li>
                    <li class="radio"><label><input type="radio" name="writeAuth"
                                                    value="2" <?php if ($writeauth == 2) echo 'checked' ?>>전체</label>
                    </li>
                    <li><b>타인이 설정할 수 있는 공개 권한</b></li>
                    <li class="checkbox"><label><input type="checkbox" value="a" class="expAuth" checked disabled>나와 글쓴이</label>
                    </li>
                    <li class="checkbox"><label><input type="checkbox" value="b"
                                                       class="expAuth" <? if (strpos($expauth, 'b') !== false) echo 'checked' ?>>내
                            친구</label></li>
                    <li class="checkbox"><label><input type="checkbox" value="c"
                                                       class="expAuth" <? if (strpos($expauth, 'c') !== false) echo 'checked' ?>>전체</label>
                    <li><b>목록 공개</b></li>
                    <li class="checkbox"><label><input type="checkbox" value="z"
                                                       class="viewAuth" <? if (strpos($viewauth, 'z') !== false) echo 'checked' ?>>친구</label></li>
                    <li class="checkbox"><label><input type="checkbox" value="y"
                                                       class="viewAuth" <? if (strpos($viewauth, 'y') !== false) echo 'checked' ?>>구독</label>
                    </li>
                </ul>
            </div>
        </div>
    <?php } else {
    //        내 프로필이 아닐경우
    ?>
        <div class="btn-group others" role="group">
            <script>
                var I = false;
                var frelation = false;
            </script>
            <!--            친구목록-->
            <?php
            $sql3 = "SELECT ID_FRIEND FROM publixher.TBL_FRIENDS WHERE ID_USER=:ID_USER AND ALLOWED='Y'";
            $prepare3 = $db->prepare($sql3);
            $prepare3->bindValue(':ID_USER', $targetid);
            $prepare3->execute();
            $friends = $prepare3->fetchAll(PDO::FETCH_ASSOC);
            $friendnum = count($friends);
            $frelation = false;
            //나랑 글쓴이랑 친구인지 확인
            foreach ($friends as $fri) {
                $frelation = $userID == $fri['ID_FRIEND'];
                //친구관계면 스크립트에 쓰고 브레이크
                if ($frelation) {
                    echo "<script>var frelation=true;</script>";
                    break;
                }
            }
            ?>
            <!--            친구목록-->
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                    친구목록(<?= count($friend_list) ?>)
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu hasInput" role="menu" id="frielist">
                    <li><input type="text" class="form-control"></li>
                    <?php
                    if(strpos($viewauth, 'z') !== false) {
                        $arr = array();
                        for ($i = 0; $i < count($friend_list); $i++) {
                            echo "<li><div class='friend-list-pic-wrap'><img src='" . $friend_list[$i]['PIC'] . "'></div><a href='/profile/" . $friend_list[$i]['ID'] . "' class='nameuser'>" . $friend_list[$i]['USER_NAME'] . "</a></li>";
                            $arr[] = $friend_list[$i]['USER_NAME'];
                        }
                        $arr = json_encode($arr);
                        echo "<script>var frievar=${arr};</script>";
                    }else{
                        echo "<li><div>친구목록 비공개</div></li>";
                    }
                    ?>
                </ul>
            </div>
            <!--            구독목록-->
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                    구독목록(<?= count($master_list) ?>)
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu hasInput" role="menu" id="subslist">
                    <li><input type="text" class="form-control"></li>
                    <?php
                    if(strpos($viewauth, 'y') !== false) {
                        $arr = array();
                        for ($i = 0; $i < count($master_list); $i++) {
                            echo "<li><div class='subs-list-pic-wrap'><img src='" . $master_list[$i]['PIC'] . "'></div><a href='/profile/" . $master_list[$i]['ID'] . "' class='nameuser'>" . $master_list[$i]['USER_NAME'] . "</a></li>";
                            $arr[] = $master_list[$i]['USER_NAME'];
                        }
                        $arr = json_encode($arr);
                        echo "<script>var subsvar=${arr};</script>";
                    }else{
                        echo "<li><div>구독목록 비공개</div></li>";
                    }
                    ?>
                </ul>
            </div>

            <?php
            //친구신청 버튼
            $sql2 = "SELECT ALLOWED FROM publixher.TBL_FRIENDS WHERE (ID_FRIEND=:ID_FRIEND AND ID_USER=:ID_USER) LIMIT 1";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue('ID_FRIEND', $targetid, PDO::PARAM_STR);
            $prepare2->bindValue('ID_USER', $userID, PDO::PARAM_STR);
            $prepare2->execute();
            $allowed = $prepare2->fetchColumn();
            if (!$allowed) {
                echo '<div id="friend-btn"><button type="button" class="btn btn-default request" id="friequst">친구신청</button></div>';
            } elseif ($allowed == 'N') {
                echo '<div id="friend-btn"><button type="button" class="btn btn-default onrequest" id="friequst" >친구신청중</button></div>';
            } elseif ($allowed == 'Y') {
                echo '<div id="friend-btn"><button type="button" class="btn btn-success onfriend" id="friequst">친구</button></div>';
            }

            //구독신청 버튼
            $sql3 = "SELECT SEQ FROM publixher.TBL_FOLLOW WHERE ID_MASTER=:ID_MASTER AND ID_SLAVE=:ID_SLAVE LIMIT 1";
            $prepare3 = $db->prepare($sql3);
            $prepare3->bindValue('ID_MASTER', $targetid, PDO::PARAM_STR);
            $prepare3->bindValue('ID_SLAVE', $userID, PDO::PARAM_STR);
            $prepare3->execute();
            $subscribe = $prepare3->fetchColumn();
            if (!$subscribe) {
                echo '<div id="subs-btn"><button type="button" class="btn btn-default subscribe" id="subsbtn">구독하기</button></div>';
            } else {
                echo '<div id="subs-btn"><button type="button" class="btn btn-info dis_subscribe" id="subsbtn">구독중</button></div>';
            }
            ?>
        </div>
    <?php } ?>
    <!--    글쓰는 카드-->
    <form action="/php/data/uploadContent.php" method="post" enctype="multipart/form-data" id="upform">
        <?php
        //내 프로필일 경우 ,내 친구고 타겟의 글쓰기 권한이 1일경우,2일경우
        if ($I OR ($frelation AND $writeauth > 0) OR $writeauth > 1) {
            ?>

            <div role="tabpanel" id="writing-pane">
                <!-- 위탭 -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active" id="send-li"><a href="#send" aria-controls="home" role="tab"
                                                                           data-toggle="tab">보내기</a></li>
                    <?php
                    if ($I) {
                        echo '<li role="presentation" id="pub-li"><a href="#publixh" aria-controls="profile" role="tab"data-toggle="tab">출판하기</a></li>';
                    } else {
                        echo '<li role="presentation" id="profile-others"><a disabled class="disabled">출판하기</a>';
                    }
                    ?>
                    <li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"
                                                                role="button" aria-expanded="false">
                            <span id="exposeSettingSub">전체공개</span> <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu" id="expSublist">
                            <li><a>나만보기</a></li>
                            <?php if (strpos($expauth, 'b') !== false and !$I) echo "<li><a>${target['USER_NAME']} 친구에게 공개</a></li>";
                            elseif (strpos($expauth, 'b') !== false and $I) echo "<li><a>친구에게 공개</a></li>";
                            if (strpos($expauth, 'c') !== false OR $I) echo '<li><a>전체공개</a></li>'; ?>
                        </ul>
                    </li>
                    <li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"
                                                                role="button" aria-expanded="false">
                            <span id="directorySettingSub">미분류</span><span
                                class="caret"></span></a>
                        <ul class="dropdown-menu hasInput" role="menu" id="dirSublist">
                            <li><a>미분류</a></li>
                            <?php
                            require_once '../conf/database_conf.php';
                            require_once '../conf/User.php';
                            $userinfo = $_SESSION['user'];
                            $userID = $userinfo->getID();
                            //폴더목록 불러오기
                            $sql1 = "SELECT ID,DIR FROM publixher.TBL_FOLDER WHERE ID_USER=:ID_USER";
                            $prepare1 = $db->prepare($sql1);
                            $prepare1->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
                            $prepare1->execute();
                            $FOLDER = $prepare1->fetchAll(PDO::FETCH_ASSOC);
                            for ($i = 0; $i < count($FOLDER); $i++) {
                                echo '<li folderid="' . $FOLDER[$i]['ID'] . '"><a href="#" >' . $FOLDER[$i]['DIR'] . '</a></li>';
                            }
                            ?>
                            <li><input type="text"
                                                                                    class="form-control new-folder">
                            </li>
                        </ul>
                    </li>
                </ul>
                <!-- 똥싸기와 용돈벌기 내용 -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="send">
                        <div contenteditable="true" class="form-control" id="sendBody">
                            <div></div>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false">
                                <span class="pubico pico-youtube"></span>
                            </button>
                            <ul class="dropdown-menu hasInput" role="menu">
                                <li><input type="text" class="form-control youtube-iframe"></li>
                            </ul>
                        </div>
                        <hr>
                        <table>
                            <tr>
                                <td class="fileinput">
                                    <span class="pubico pico-file-plus"><span>파일선택</span>
                                    <input id="fileuploads" name="fileuploads[]" accept="image/*"
                                           data-url="/php/data/fileUp.php" multiple class="fileupform" type="file">
                                </td>
                                <td class="taginput">
                                    <input type="text" class="tag-input" class="form-control" placeholder="인물 , 제목"
                                           id="send-tag">
                                </td>
                                <td class="regbtn">
                                    <button type="button" id="sendButton" data-loading-text="보내는중"
                                            class="btn btn-primary"
                                            autocomplete="off"><span class="pubico pico-pen2">
                                        보내기
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php if ($I) { ?>
                        <div role="tabpanel" class="tab-pane" id="publixh">
                            <div>
                                <input type="text" class="form-control" id="saleTitle" placeholder="첫줄이 제목이 됩니다.">
                                <div contenteditable="true" class="form-control" id="publiBody">
                                    <div></div>
                                </div>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                            aria-expanded="false">
                                        <span class="pubico pico-youtube"></span>
                                    </button>
                                    <ul class="dropdown-menu hasInput" role="menu">
                                        <li><input type="text" class="form-control youtube-iframe"></li>
                                    </ul>
                                </div>
                            </div>
                            <hr>
                            <table>
                                <tr>
                                    <td class="cateinput">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default dropdown-toggle"
                                                    data-toggle="dropdown"
                                                    aria-expanded="false">
                                                <span id="category">분류</span> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu" id="categorySelect">
                                                <li><a>매거진</a></li>
                                                <li><a>뉴스</a></li>
                                                <li><a>소설</a></li>
                                                <li><a>만화</a></li>
                                                <li><a>사진</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="subcateinput">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default dropdown-toggle"
                                                    data-toggle="dropdown"
                                                    aria-expanded="false"><span id="sub-category">하위 분류</span> <span
                                                    class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu" id="subcategorySelect">
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="priceinput" colspan="2">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="contentCost"
                                                       placeholder="여기에 가격을 입력하세요."
                                                       pattern="[0-9]">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fileinput">
                                        <span class="pubico pico-file-plus"><span>파일선택</span>
                                        <input id="fileuploadp" name="fileuploadp[]" accept="image/*"
                                               data-url="/php/data/fileUp.php" multiple class="fileupform" type="file">
                                    </td>
                                    <td class="taginput" colspan="2">
                                        <input type="text" class="tag-input" class="form-control"
                                               placeholder="인물 , 제목" id="publi-tag">
                                    </td>
                                    <td class="regbtn">
                                        <button type="button" id="publixhButton" data-loading-text="출판중"
                                                class="btn btn-primary"
                                                autocomplete="off"><span class="pubico pico-pen2">
                                            출판하기
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div><?php } ?>
                </div>
            </div>
        <?php } ?>
    </form>
    <div id="topcon"></div>

    <!--    각 카드가 하나의 아이템-->
</div>
<!-- 친구추천 모달 -->
<div class="modal fade" id="friend-recommend" tabindex="-1" role="dialog" aria-labelledby="friendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="friendModalLabel">혹시 이사람을 아시나요?</h4>
            </div>
            <div class="modal-body" id="recommended-friend">
                
            </div>
        </div>
    </div>
</div>
<script>
    var page = 0;
    var targetID = '<?=$targetid?>';
    var loadOption = {ID: mid, nowpage: page, profile: targetID, I: I, frelation: frelation};
</script>
<!--    해시 태그-->
<link rel="stylesheet" href="/plugins/jQuery-tagEditor-master/jquery.tag-editor.css">
<script src="/plugins/jQuery-tagEditor-master/jquery.caret.min.js"></script>
<script src="/plugins/jQuery-tagEditor-master/jquery.tag-editor.min.js"></script>
<!--gif 플레이-->
<link rel="stylesheet" href="/plugins/gifplayer-master/dist/gifplayer.css">
<script src="/plugins/gifplayer-master/dist/jquery.gifplayer.js"></script>

<script src="/js/itemcard.js"></script>
<script src="/js/itemload.js"></script>
<script src="/js/upform.js"></script>
<script src="/js/topcon.js"></script>
<script src="/js/profilenav.js"></script>
