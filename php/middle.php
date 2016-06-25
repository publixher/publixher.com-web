<div id="middle">
    <!--    글쓰는 카드-->
    <form action="/php/data/uploadContent.php" method="post" enctype="multipart/form-data" id="upform">
        <div role="tabpanel" id="writing-pane">
            <!-- 위탭 -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active" id="send-li"><a href="#send" aria-controls="home" role="tab"
                                                          data-toggle="tab">보내기</a></li>
                <li role="presentation" id="pub-li"><a href="#publixh" aria-controls="profile" role="tab" data-toggle="tab">출판하기</a>
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
                    <div contenteditable="true" class="form-control" id="sendBody"><div></div></div>

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
                        <div contenteditable="true" class="form-control" id="publiBody"><div></div></div>
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
    <div class="item-for-sale card" id="th6ro3ZeoT" style="display: block;"><div class="header"><div class="item-profile-wrap"><img src="/img/crop50/ffbbe6785c6060c7f9d06ebb77b86919.jpg" class="profilepic"></div><div class="writer"><a href="/profile/sFaG3qO_qI">전유선</a>&nbsp;<span class="content-date">06월24일</span>&nbsp;<span class="content-expose">전체공개</span><span class="content-category"><span class="item-category">매거진</span><span class="pubico pico-kkuk"></span><span class="item-sub_category">IT</span></span></div> <div class="conf"><a class="pin-a">핀</a><div class="btn-group"> <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span> </button> <ul class="dropdown-menu" role="menu"><li><a class="itemMod"><span class="pubico pico-content-edit"></span>수정</a></li><li><a class="itemDel"><span class="pubico pico-content-remove"></span>삭제</a></li><li><a class="itemTop"><span class="pubico pico-kkuk-up"></span>최상단 컨텐츠로</a></li> </ul></div><br></div><div class="title">찾아주신 모든 분들께 감사합니다!</div></div> <div class="body" style="display: block;"><div id="linksth6ro3ZeoT"><p><span></span></p><div><div>1.소셜 컨텐츠 포탈&nbsp;analograph의&nbsp;beta&nbsp;서비스에 오신 것을 환영합니다! </div><div>&nbsp;&nbsp; 한달 뒤, 모바일 앱과 함께 정식런칭을 계획하고 있으니, 그 전까지&nbsp;어떠한 컨텐츠든 자유롭게 올려시면 됩니다!. <br></div><div><a></a><a></a><a></a><a></a><a></a><a href="/img/origin/1a0c29541e73b30d0e352a0444ed4308.png" data-gallery=""><img src="/img/crop_origin/1a0c29541e73b30d0e352a0444ed4308.png" alt="/img/alt_img.png"></a></div></div><p></p><p><span><br></span></p><p><span>analograph</span><span>는 누구나 자신이 제작한 컨텐츠를 공유하고 또 수익을 얻을 수 있는 서비스 입니다</span><span>. Beta </span><span>서비스에서 작성된 모든 컨텐츠는 정식 서비스로 이전될 예정입니다</span><span>. analograph</span><span>와 함께 새로운 디지털 컨텐츠의 새로운 시대를 열어주세요</span><span>!</span><br></p><p></p><p>현재 모바일 페이지의 경우 기능에 다소의 제약이 있으며 <span>PC</span>의 크롬 브라우저에 최적화 되어 있습니다<span>.</span></p><p></p><p><span></span></p><p>&nbsp;</p><p><span>2 </span>보내기 출판하기<span></span></p><p></p><p><span>Analograph</span>는 중앙 상단에 <span>‘</span>보내기<span>’</span>와 <span>‘</span>출판하기<span>’ </span>두 가지 방식의 공유하기 기능을 제공하고 있습니다<span>. </span></p><p></p><p><span>1.<span>&nbsp;&nbsp;&nbsp;&nbsp; </span></span><span>‘</span>보내기<span>’</span>는 일반적인 <span>SNS</span>와 동일하게 별도의 제약 없이 자신의 글을 공유할 수 있습니다<span>. </span></p><p></p><p><span>2.<span>&nbsp;&nbsp;&nbsp;&nbsp; </span></span><span>‘</span>출판하기<span>’</span>는 <span>analograph</span>만의 특별한 기능입니다<span>. ‘</span>출판하기<span>’</span>를 통해 공유되는 글은 반드시 제목을 포함하여야 하며<span>, </span>카테고리와 가격을 설정할 수 있습니다<span>.</span></p><p></p><p>카테고리가 설정될 경우 사용자는 해당 카테고리 내의 게시글을 시간 순<span>, </span>실시간<span>, </span>일간 및 주간 순위로 탐색할 수 있습니다<span>.</span></p><p></p><p>가격이 설정된 글은 다른 사용자가 해당 컨텐츠를 열람하기 위하여 설정된 만큼의 가격을 지불하여야 합니다<span>. </span>설정하지 않은 경우 무료 컨텐츠가 됩니다<span></span></p><p></p><p>두 가지 방식 모두 공개범위 설정과 폴더 설정<span>, </span>태그 등이 가능합니다<span>. </span>또한 공유된 글에 코멘트와 서브 코멘트가 허용되고 코멘트를 통해 본인의 친구를 태그 하거나 글 작성자에게 본인의 포인트를 기부<span>(</span>응원<span>)</span>하는 것이 가능합니다<span>.</span></p><p></p><p><span>.</span></p><p></p><p><span>3 </span>포인트<span></span></p><p></p><p><span>analograph</span>에서 컨텐츠를 판매하거나 구매할 수 있으며 코멘트를 통한 기부가 가능합니다<span>. </span>이때 한화 원<span>(\)</span>이 아닌 <span>pigs</span>라는 <span>analograph </span>서비스의 포인트가 사용됩니다<span>. </span>포인트는 구매 시 <span>100</span>원에 <span>1pigs</span>가 될 예정입니다<span>. </span>컨텐츠에 따라 가격은 변할 수 있겠지만 통상 컨텐츠당 <span>1~5pigs </span>이내에서 거래가 이루어질 것으로 예상하고 있습니다<span>.</span></p><p></p><p><span>beta</span>기간의 원활한 테스트 및 활동을 위하여 십만 포인트가 가입시 등록됩니다<span>. </span>포인트는 <span>beta</span>기간 내에는 현금 가치가 없으며 정식 런칭 시 모든 포인트가 초기화 될 예정입니다<span>.</span></p><p></p><p><span></span></p><p>&nbsp;</p><p><span>4 </span>익명과 실명<span></span></p><p></p><p><span>analograph</span>는 불특정 다수의 사용자를 타켓으로 한 출판하기 기능을 운영하는 만큼 컨텐츠 제작자의 프라이버시 보호가 중요합니다<span>.</span></p><p></p><p>저희는 익명 프로필의 생성과 원 클릭 전환을 지원합니다<span>. </span>프로필 페이지에서 익명 프로필을 생성 하실 수 있고 우측 상단<span>(pc) </span>혹은 상단<span>(</span>모바일<span>)</span>의 이름 옆의 스왑 아이콘으로 간편하게 익명과 실명을 전환할 수 있습니다<span>.</span></p><p></p><p>익명 프로필은 일반적 계정과 완전히 동일합니다<span>. </span>친구를 맺거나 구독을 할 수 있고 프로필 사진도 바꿀 수 있습니다<span>. </span>또한 누구도 익명 프로필을 통해 실명을 식별할 수 없습니다<span></span></p><p></p><div></div></div></div><div class="tail"> <table><tbody><tr><td class="tknock"><span class="knock"><span class="pubico pico-knock knocked"></span><a>노크</a><span class="badgea"> 4</span></span></td> <td class="tcomment"><span class="comment"><span class="pubico pico-comment"></span><a>코멘트</a><span class="badgea"> 0</span></span></td><td class="tshare"><span class="share"><span class="pubico pico-share"></span><a>공유하기</a></span></td><td class="tprice"><span class="price expanded" style="display: inline;"><a><span class="pubico pico-up-tri"></span></a></span></td></tr></tbody></table></div> </div>
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
