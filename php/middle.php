<div id="middle">
    <!--    글쓰는 카드-->
    <form action="/php/data/uploadContent.php" method="post" enctype="multipart/form-data" id="upform">
        <div role="tabpanel" id="writing-pane">
            <!-- 위탭 -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active" id="send-li"><a href="#send" aria-controls="home" role="tab"
                                                                       data-toggle="tab">보내기</a></li>
                <li role="presentation" id="pub-li"><a href="#publixh" aria-controls="profile" role="tab"
                                                       data-toggle="tab">출판하기</a>
                </li>
                <li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"
                                                            role="button" aria-expanded="false">
                        <span id="exposeSettingSub">전체공개</span> <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu" id="expSublist">
                        <li><a>나만보기</a></li>
                        <li><a>친구에게 공개</a></li>
                        <li><a>전체공개</a></li>
                    </ul>
                </li>
                <li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"
                                                            role="button" aria-expanded="false">
                        <span class="pubico pico-folder"></span><span id="directorySettingSub">미분류</span><span
                            class="caret"></span></a>
                    <ul class="dropdown-menu hasInput" role="menu" id="dirSublist">
                        <li><a>미분류</a></li>
                        <?php
                        require_once '../conf/database_conf.php';
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
                        <li><span class="pubico pico-folder-plus"></span><input type="text"
                                                                                class="form-control new-folder"></li>
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
                                <span><span class="pubico pico-file-plus"></span>파일선택</span>
                                <input id="fileuploads" name="fileuploads[]" accept="image/*"
                                       data-url="/php/data/fileUp.php" multiple class="fileupform" type="file">
                            </td>
                            <td class="taginput" colspan="2">
                                <input type="text" class="tag-input form-control" placeholder="인물 , 제목" id="send-tag">
                            </td>

                            <td class="regbtn">
                                <button type="button" id="sendButton" data-loading-text="싸는중..."
                                        class="btn btn-primary">
                                    <span class="pubico pico-pen2">보내기</span>
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--여기부턴 용돈벌기 내용-->
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
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                            aria-expanded="false">
                                        <span id="category">분류</span> <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu" id="categorySelect">
                                        <li><a>매거진</a></li>
                                        <li><a>뉴스</a></li>
                                        <li><a>소설</a></li>
                                        <li><a>만화</a></li>
                                        <li><a>사진</a></li>
                                        <li><a>맛집</a></li>
                                    </ul>
                                </div>
                            </td>
                            <td class="subcateinput">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                            aria-expanded="false"><span id="sub-category">하위 분류</span><span
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
                                <span><span class="pubico pico-file-plus">파일선택</span>
                                <input id="fileuploadp" name="fileuploadp[]" accept="image/*"
                                       data-url="/php/data/fileUp.php" multiple class="fileupform" type="file">
                            </td>
                            <td class="taginput" colspan="2">
                                <input type="text" class="tag-input form-control" placeholder="인물 , 제목" id="publi-tag">
                            </td>
                            <td class="regbtn">
                                <button type="button" id="publixhButton" data-loading-text="싸는중..."
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
    <!--    각 카드가 하나의 아이템-->
<!--    공지사항 시작-->
    <div class="item-for-sale card notice" id="1sdhOPm7Xg" style="display: block;">
        <div class="header">
            <div class="item-profile-wrap"><img src="/img/crop50/e381c49473a8ecbd5d5265c05ea8590c.png"
                                                class="profilepic"></div>
            <div class="writer"><a href="/profile/4jLDaImqHE" style="font-weight: 700;">analograph</a>&nbsp;<span
                    class="content-expose">전체공개</span></div>
            <div class="title">Welcome to analograph</div>
        </div>
        <div class="body">analograph에 찾아주신 모든 분들께 감사합니다!&nbsp;한 달 뒤, 모바일 앱과 함께 정식런칭을 계획하고 있으니, 그 전까지&nbsp;어떠한 컨텐츠든 자유롭게
            올려주세요!<br><div style="display: block; width: 90%; height: 150px; overflow: hidden; border-radius: 15px; position: relative">
                <img src="/img/crop/efc1caf98a65cd6e7739bd650d2a62e1.png" class="BodyPic" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
">
            </div><br><br></div>
        <div class="tail opend-share" style="margin-bottom: 10px;">
            <table>
                <tbody>
                <tr>
                    <td class="tknock"><span class="knock"><span class="pubico pico-knock"></span><a>노크</a><span
                                class="badgea"> </span></span></td>
                    <td class="tcomment"><span class="comment"><span class="pubico pico-comment"></span><a>코멘트</a><span
                                class="badgea"> </span></span></td>
                    <td class="tshare"><span class="share"><span
                                class="pubico pico-share"></span><a>공유하기</a></span></td>
                    <td class="tprice"><span class="price bought"><a><span
                                    class="pubico pico-down-tri"></span></a></span></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<!--    공지사항 끝-->
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.5.2/jquery.fileupload.min.js"></script>
<script>
    var page = 0;
    var mid = '<?=$userID?>';
    var targetID = null;
    var loadOption = {ID: mid, nowpage: page};
</script>
<!--    해시 태그-->
<link rel="stylesheet" href="/plugins/jQuery-tagEditor-master/jquery.tag-editor.css">
<script src="/plugins/jQuery-tagEditor-master/jquery.caret.min.js"></script>
<script src="/plugins/jQuery-tagEditor-master/jquery.tag-editor.min.js"></script>
<!--gif 플레이-->
<link rel="stylesheet" href="/plugins/gifplayer-master/dist/gifplayer.css">
<script src="/plugins/gifplayer-master/dist/jquery.gifplayer.js"></script>

<script src="/js/upform.js"></script>
<script src="/js/itemcard.js"></script>
<script src="/js/itemload.js"></script>
