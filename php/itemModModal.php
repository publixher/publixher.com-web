<!--    수정을 위한 모달-->
<div id="itemModModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/php/data/uploadContent.php" method="post" enctype="multipart/form-data" id="mod-form">
                <div role="tabpanel" id="mod-pane">
                    <!-- 위탭 -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"></li>
                        <li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"
                                                                    role="button" aria-expanded="false">
                                <span id="exposeSetting-mod">전체공개</span> <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu" id="expSublist-mod">
                                <li><a>나만보기</a></li>
                                <li><a>친구에게 공개</a></li>
                                <li><a>전체공개</a></li>
                            </ul>
                        </li>
                        <li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"
                                                                    role="button" aria-expanded="false">
                                <span class="pubico pico-folder"></span><span id="directorySettingSub-mod">미분류</span><span class="caret"></span></a>
                            <ul class="dropdown-menu hasInput" role="menu" id="dirSublist-mod">
                                <li><a>미분류</a></li>
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
                            <div contenteditable="true" class="form-control" id="sendBody-mod"><div></div></div>
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
                                        <input id="fileuploads-mod" name="fileuploads[]" accept="image/*"
                                               data-url="/php/data/fileUp.php" multiple class="fileupform" type="file">
                                    </td>
                                    <td class="taginput" colspan="2">
                                        <input type="text" class="tag-input form-control" placeholder="인물 , 제목" id="send-tag-mod">
                                    </td>
                                    <td class="regbtn">
                                        <button type="button" id="sendButton-mod" data-loading-text="싸는중..." class="btn btn-primary">
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
                                <div contenteditable="true" class="form-control" id="publiBody-mod"><div></div></div>
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
                                                <span id="category-mod">분류</span> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu" id="categorySelect-mod">
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
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
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
                                                <input type="text" class="form-control" id="contentCost-mod"
                                                       placeholder="여기에 가격을 입력하세요."
                                                       pattern="[0-9]">
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
                                    <td class="taginput" colspan="2">
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