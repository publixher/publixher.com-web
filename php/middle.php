<div id="middle">
    <!--    글쓰는 카드-->
    <form action="/php/data/uploadContent.php" method="post" enctype="multipart/form-data" id="upform">
        <div role="tabpanel" id="writing-pane">
            <!-- 위탭 -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#send" aria-controls="home" role="tab" data-toggle="tab">보내기</a></li>
                <li role="presentation"><a href="#publixh" aria-controls="profile" role="tab" data-toggle="tab">출판하기</a>
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
                        <span class="pubico pico-folder"></span><span id="directorySettingSub">미분류</span><span class="caret"></span></a>
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
                        <li><span class="pubico pico-folder-plus"></span><input type="text" class="form-control new-folder"></li>
                    </ul>
                </li>
            </ul>
            <!-- 똥싸기와 용돈벌기 내용 -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="send">
                    <div contenteditable="true" class="form-control" id="sendBody" oninput="resize(this)" onkeyup="resize(this)"></div>
                    <!--                    <div class="form-control" id="sendtag" contenteditable="true"></div>-->
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
                                <button type="button" id="sendButton" data-loading-text="싸는중..." class="btn btn-primary">
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
                        <div contenteditable="true" class="form-control" id="publiBody" oninput="resize(this)" onkeyup="resize(this)"></div>
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
                                        <div class="input-group-addon">PIK</div>
                                        <input type="text" class="form-control" id="contentCost"
                                               placeholder="여기에 가격을 입력하세요."
                                               pattern="[0-9]">
                                        <div class="input-group-addon"><span class="pubico pico-24"></span></div>
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
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.5.2/jquery.fileupload.min.js"></script>
<script>
    var page = 0;
    var mid='<?=$userID?>';
    var targetID=null;
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
