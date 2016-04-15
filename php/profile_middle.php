<div id="middle">
    <script src="//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.5.2/jquery.fileupload.min.js"></script>
                <?php
                $sql4 = "SELECT ID_MASTER FROM publixher.TBL_FOLLOW WHERE ID_SLAVE=:ID_SLAVE";
                $prepare4 = $db->prepare($sql4);
                $prepare4->bindValue(':ID_SLAVE', $targetid);
                $prepare4->execute();
                $masters = $prepare4->fetchAll(PDO::FETCH_ASSOC);
                $masternum = count($masters);
                ?>
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                    구독목록(<?= $masternum ?>)
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu hasInput" role="menu" id="subslist">
                    <li><input type="text" class="form-control"></li>
                    <?
                    $arr = array();
                    $msql = "SELECT USER_NAME,PIC,ID FROM publixher.TBL_USER WHERE ID=:ID";
                    $mriprepare = $db->prepare($msql);
                    for ($i = 0; $i < $masternum; $i++) {
                        $mriprepare->bindValue(':ID', $masters[$i]['ID_MASTER'], PDO::PARAM_STR);
                        $mriprepare->execute();
                        $master = $mriprepare->fetch(PDO::FETCH_ASSOC);
                        echo "<li><img src='${master['PIC']}'><a href='profile.php?id=" . $master['ID'] . "' class='nameuser'>" . $master['USER_NAME'] . "</a></li>";
                        $arr[] = $master['USER_NAME'];
                    }
                    $arr = json_encode($arr);
                    echo "<script>var subsvar=${arr};</script>";
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
                echo '<button type="button" class="btn btn-default request" id="friequst">친구신청</button>';
            } elseif ($allowed == 'N') {
                echo '<button type="button" class="btn btn-default request" id="friequst" disabled>친구신청중</button>';
            } elseif ($allowed == 'Y') {
                echo '<button type="button" class="btn btn-success onfriend" id="friequst">내칭구칭구</button>';
            }

            //구독신청 버튼
            $sql3 = "SELECT SEQ FROM publixher.TBL_FOLLOW WHERE ID_MASTER=:ID_MASTER AND ID_SLAVE=:ID_SLAVE LIMIT 1";
            $prepare3 = $db->prepare($sql3);
            $prepare3->bindValue('ID_MASTER', $targetid, PDO::PARAM_STR);
            $prepare3->bindValue('ID_SLAVE', $userID, PDO::PARAM_STR);
            $prepare3->execute();
            $subscribe = $prepare3->fetchColumn();
            if (!$subscribe) {
                echo '<button type="button" class="btn btn-default subscribe" id="subsbtn">구독하기</button>';
            } else {
                echo '<button type="button" class="btn btn-info dis_subscribe" id="subsbtn">구독중</button>';
            }
            ?>
        </div>
    <!--    글쓰는 카드-->
    <form action="/php/data/uploadContent.php" method="post" enctype="multipart/form-data" id="upform">
        <?php
        //내 프로필일 경우 ,내 친구고 타겟의 글쓰기 권한이 1일경우,2일경우
        if ($I OR ($frelation AND $writeauth > 0) OR $writeauth > 1) {
            ?>

            <div role="tabpanel" id="writing-pane">
                <!-- 위탭 -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#send" aria-controls="home" role="tab"
                                                              data-toggle="tab">똥싸기</a></li>
                    <? if ($I) echo '<li role="presentation"><a href="#publixh" aria-controls="profile" role="tab"data-toggle="tab">용돈벌기</a></li>' ?>
                    <li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"
                                                                role="button" aria-expanded="false">
                            <span id="exposeSettingSub">전체공개</span> <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu" id="expSublist">
                            <li><a>나만보기</a></li>
                            <? if (strpos($expauth, 'b') !== false OR $I) echo "<li><a>${target['USER_NAME']}의 친구에게 공개</a></li>" ?>
                            <? if (strpos($expauth, 'c') !== false OR $I) echo '<li><a>전체공개</a></li>' ?>
                        </ul>
                    </li>
                    <li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"
                                                                role="button" aria-expanded="false">
                            <span id="directorySettingSub">비분류</span><span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu" id="dirSublist">
                            <?php
                            require_once '../conf/database_conf.php';
                            require_once '../conf/User.php';
                            session_start();
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
                        </ul>
                    </li>
                </ul>
                <!-- 똥싸기와 용돈벌기 내용 -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="send">
                        <div contenteditable="true" class="form-control" id="sendBody" oninput="resize(this)"
                             onkeyup="resize(this)"></div>
                        <hr>
                        <table>
                            <tr>
                                <td class="fileinput">
                                    <span>파일선택</span>
                                    <input id="fileuploads" name="fileuploads[]" accept="image/*"
                                           data-url="/php/data/fileUp.php" multiple class="fileupform" type="file">
                                </td>
                                <td class="taginput">
                                    <input type="text" class="tag-input" class="form-control" placeholder="인물 , 제목" id="send-tag">
                                </td>
                                <td class="regbtn">
                                    <button type="button" id="sendButton" data-loading-text="싸는중..."
                                            class="btn btn-primary"
                                            autocomplete="off">
                                        보내기
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <? if ($I) { ?>
                        <div role="tabpanel" class="tab-pane" id="publixh">
                            <div>
                                <input type="text" class="form-control" id="saleTitle" placeholder="첫줄이 제목이 됩니다.">
                                <div contenteditable="true" class="form-control" id="publiBody"
                                     oninput="resize(this)" onkeyup="resize(this)"></div>
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
                                                <li><a>사진</a></li>
                                                <li><a>만화</a></li>
                                                <li><a>일러스트</a></li>
                                                <li><a>e-book</a></li>
                                                <li><a>매거진</a></li>
                                                <li><a>CAD</a></li>
                                                <li><a>VR</a></li>
                                                <li><a>맛집</a></li>
                                                <li><a>여행</a></li>
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
                                                <div class="input-group-addon">PIK</div>
                                                <input type="text" class="form-control" id="contentCost"
                                                       placeholder="여기에 가격을 입력하세요."
                                                       pattern="[0-9]">
                                                <div class="input-group-addon"><img src="../img/IMG_1795.png"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fileinput">
                                        <span>파일선택</span>
                                        <input id="fileuploadp" name="fileuploadp[]" accept="image/*"
                                               data-url="/php/data/fileUp.php" multiple class="fileupform" type="file">
                                    </td>
                                    <td class="taginput">
                                        <input type="text" class="tag-input" class="form-control"
                                               placeholder="인물 , 제목" id="publi-tag">
                                    </td>
                                    <td class="regbtn">
                                        <button type="button" id="publixhButton" data-loading-text="싸는중..."
                                                class="btn btn-primary"
                                                autocomplete="off">
                                            출판하기
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div><? } ?>
                </div>
            </div>
        <? } ?>
    </form>
    <div id="topcon"></div>

    <!--    각 카드가 하나의 아이템-->
</div>
<!--    수정을 위한 모달-->
<div id="itemModModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/php/data/uploadContent.php" method="post" enctype="multipart/form-data" id="mod-form">
                <div role="tabpanel" id="mod-pane">
                    <!-- 위탭 -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"></li>
                        <li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"
                                                                    href="#"
                                                                    role="button" aria-expanded="false">
                                <span id="exposeSetting-mod">전체공개</span> <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu" id="expSublist-mod">
                                <li><a>나만보기</a></li>
                                <li><a>친구에게 공개</a></li>
                                <li><a>전체 공개</a></li>
                            </ul>
                        </li>
                        <li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"
                                                                    href="#"
                                                                    role="button" aria-expanded="false">
                                <span id="directorySettingSub-mod">비분류</span><span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu" id="dirSublist-mod">
                                <?php
                                for ($i = 0; $i < count($FOLDER); $i++) {
                                    echo '<li folderid="' . $FOLDER[$i]['ID'] . '"><a href="#" >' . $FOLDER[$i]['DIR'] . '</a></li>';
                                }
                                ?>
                            </ul>
                        </li>
                    </ul>
                    <!-- 똥싸기와 용돈벌기 내용 -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane" id="send-mod">
                            <div contenteditable="true" class="form-control" id="sendBody-mod" oninput="resize(this)"
                                 onkeyup="resize(this)"></div>
                            <hr>
                            <table>
                                <tr>
                                    <td class="fileinput">
                                        <span><span class="pubico pico-file-plus"></span>파일선택</span>
                                        <input id="fileuploads-mod" name="fileuploads[]" accept="image/*"
                                               data-url="/php/data/fileUp.php" multiple class="fileupform" type="file">
                                    </td>
                                    <td class="taginput">
                                        <input type="text" class="tag-input form-control" placeholder="인물 , 제목" id="send-tag-mod">
                                    </td>
                                    <td class="regbtn">
                                        <button type="button" id="sendButton-mod" data-loading-text="싸는중..."
                                                class="btn btn-primary">
                                            <span class="pubico pico-pen2">보내기</span>
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--여기부턴 용돈벌기 내용-->
                        <div role="tabpanel" class="tab-pane" id="publixh-mod">
                            <div>
                                <input type="text" class="form-control" id="saleTitle-mod">
                                <div contenteditable="true" class="form-control" id="publiBody-mod"
                                     oninput="resize(this)" onkeyup="resize(this)"></div>
                            </div>
                            <hr>
                            <table>
                                <tr>
                                    <td class="cateinput">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default dropdown-toggle"
                                                    data-toggle="dropdown"
                                                    aria-expanded="false">
                                                <span id="category-mod">분류</span> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu" id="categorySelect-mod">
                                                <li><a>사진</a></li>
                                                <li><a>만화</a></li>
                                                <li><a>일러스트</a></li>
                                                <li><a>e-book</a></li>
                                                <li><a>매거진</a></li>
                                                <li><a>CAD</a></li>
                                                <li><a>VR</a></li>
                                                <li><a>맛집</a></li>
                                                <li><a>여행</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="subcateinput">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default dropdown-toggle"
                                                    data-toggle="dropdown"
                                                    aria-expanded="false"><span id="sub-category-mod">하위 분류</span><span
                                                    class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu" id="subcategorySelect-mod">
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="priceinput" colspan="2">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-addon">PIK</div>
                                                <input type="text" class="form-control" id="contentCost-mod"
                                                       placeholder="여기에 가격을 입력하세요."
                                                       pattern="[0-9]">
                                                <div class="input-group-addon"><img src="../img/icon.png"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fileinput">
                                <span><span class="pubico pico-file-plus">파일선택</span>
                                <input id="fileuploadp-mod" name="fileuploadp[]" accept="image/*"
                                       data-url="/php/data/fileUp.php" multiple class="fileupform" type="file">
                                    </td>
                                    <td class="taginput">
                                        <input type="text" class="tag-input form-control" placeholder="인물 , 제목" id="publi-tag-mod">
                                    </td>
                                    <td class="regbtn">
                                        <button type="button" id="publixhButton-mod" data-loading-text="싸는중..."
                                                class="btn btn-primary" autocomplete="off">
                                            <span class="pubico pico-pen2">출판하기</span>
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    var page = 0;
    var targetID ='<?=${targetid}?>';
    var loadOption = {ID: mid, nowpage: page, profile: targetID, I: I, frelation: frelation};
</script>
<!--    해시 태그-->
<link rel="stylesheet" href="/plugins/jQuery-tagEditor-master/jquery.tag-editor.css">
<script src="/plugins/jQuery-tagEditor-master/jquery.caret.min.js"></script>
<script src="/plugins/jQuery-tagEditor-master/jquery.tag-editor.min.js"></script>

<script src="/js/itemcard.js"></script>
<script src="/js/itemload.js"></script>
<script src="/js/upform.js"></script>
<script src="/js/topcon.js"></script>
<script src="/js/profilenav.js"></script>
<!--움직이는 svg아이콘-->
<script src="/plugins/AnimatedSVGIcons-master/js/snap.svg-min.js"></script>
<script src="/plugins/AnimatedSVGIcons-master/js/svgicons-config.js"></script>
<script src="/plugins/AnimatedSVGIcons-master/js/svgicons.js"></script>
<link href="/plugins/AnimatedSVGIcons-master/css/component.css" rel="stylesheet">
<script>
    [].slice.call(document.querySelectorAll('.si-icon')).forEach(function (el) {
        var svgicon = new svgIcon(el, svgIconConfig);
    });
</script>