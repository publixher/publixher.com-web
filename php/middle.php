<div id="middle">
    <script src="//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.5.2/jquery.fileupload.min.js"></script>
    <script src="/js/middle.js"></script>

    <!--    글쓰는 카드-->
    <form action="/php/data/uploadContent.php" method="post" enctype="multipart/form-data" id="upform">
        <div role="tabpanel" id="writing-pane">
            <!-- 위탭 -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#send" aria-controls="home" role="tab"
                                                          data-toggle="tab">똥싸기</a></li>
                <li role="presentation"><a href="#publixh" aria-controls="profile" role="tab" data-toggle="tab">용돈벌기</a>
                </li>
                <li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"
                                                            role="button" aria-expanded="false">
                        <span id="exposeSettingSub">전체공개</span> <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu" id="expSublist">
                        <li><a>나만보기</a></li>
                        <li><a>친구에게 공개</a></li>
                        <li><a>팔로워에게 공개</a></li>
                        <li><a>전체 공개</a></li>
                    </ul>
                </li>
                <li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"
                                                            role="button" aria-expanded="false">
                        <span id="directorySettingSub">비분류</span><span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu" id="dirSublist">
                        <?php
                        require_once'../conf/database_conf.php';
                        require_once'../conf/User.php';
                        session_start();
                        $userinfo=$_SESSION['user'];
                        $userseq=$userinfo->getSEQ();
                        //폴더목록 불러오기
                        $sql1 = "SELECT SEQ,DIR FROM publixher.TBL_FORDER WHERE SEQ_USER=:SEQ_USER";
                        $prepare1 = $db->prepare($sql1);
                        $prepare1->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
                        $prepare1->execute();
                        $forder = $prepare1->fetchAll(PDO::FETCH_ASSOC);
                        for($i=0;$i<count($forder);$i++){
                            echo '<li folderid="'.$forder[$i]['SEQ'].'"><a href="#" >'.$forder[$i]['DIR'].'</a></li>';
                        }
                        ?>
                    </ul>
                </li>
            </ul>
            <!-- 똥싸기와 용돈벌기 내용 -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="send">
                    <div contenteditable="true" class="form-control" id="sendBody" onkeyup="resize(this)"></div>
<!--                    <div class="form-control" id="sendtag" contenteditable="true"></div>-->
                    <hr>
                    <table>
                        <tr>
                            <td class="fileinput">
                                <button class="btn btn-primary" type="button">파일선택
                                    <input type="file" id="fileuploads" name="fileuploads[]" accept="image/*"
                                           data-url="/php/data/fileUp.php" multiple class="fileupform">
                                </button>
                            </td>
                            <td class="taginput">
                                <input type="text" class="tag-inputa form-control" placeholder="인물 , 제목">
                            </td>
                            <td class="taginput">
                                <input type="text" class="tag-inputh form-control" placeholder="히힣힣">
                            </td>
                            <td class="regbtn">
                                <button type="button" id="sendButton" data-loading-text="싸는중..." class="btn btn-primary"
                                        >
                                    보내기
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--여기부턴 용돈벌기 내용-->
                <div role="tabpanel" class="tab-pane" id="publixh">
                    <div>
                        <input type="text" class="form-control" id="saleTitle" placeholder="첫줄이 제목이 됩니다.">
                        <div contenteditable="true" class="form-control" id="publiBody" onkeyup="resize(this)"></div>
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
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                            aria-expanded="false"><span id="sub-category">하위 분류</span><span class="caret"></span>
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
                                        <div class="input-group-addon"><img src="../img/icon.png"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="fileinput">
                                <button class="btn btn-primary">파일선택
                                    <input type="file" id="fileuploadp" name="fileuploadp[]" accept="image/*"
                                           data-url="/php/data/fileUp.php" multiple class="fileupform">
                                </button>
                            </td>
                            <td class="taginput">
                                <input type="text" class="tag-inputa form-control" placeholder="인물 , 제목">
                            </td>
                            <td class="taginput">
                                <input type="text" class="tag-inputh form-control" placeholder="히힣힣">
                            </td>
                            <td class="regbtn">
                                <button type="button" id="publixhButton" data-loading-text="싸는중..." class="btn btn-primary"
                                        autocomplete="off">
                                    출판하기
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