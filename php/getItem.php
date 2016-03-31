<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 위 3개의 메타 태그는 *반드시* head 태그의 처음에 와야합니다; 어떤 다른 콘텐츠들은 반드시 이 태그들 *다음에* 와야 합니다 -->
    <title>Publixher</title>
    <!-- 부트스트랩 -->
    <link href="/plugins/bootstrap-3.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
    <link rel="stylesheet" href="/plugins/Bootstrap-Image-Gallery-master/css/bootstrap-image-gallery.min.css">
    <link rel="stylesheet" href="/css/publixherico/style.css">
    <link href="/css/main.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (부트스트랩의 자바스크립트 플러그인을 위해 필요합니다) -->
    <script src="/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="/plugins/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
    <script src="/plugins/bootstrap-3.3.2/dist/js/bootstrap.min.js"></script>
    <script src="//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
    <script src="/plugins/Bootstrap-Image-Gallery-master/js/bootstrap-image-gallery.min.js"></script>
    <script src="/js/plugins.js"></script>
</head>
<body>
<div id="wrap">
    <?php
    require_once'../conf/User.php';
    require_once'../conf/database_conf.php';
    session_start();
    $iid=$_GET['iid'];  //item id
    //$userinfo는 현재 접속한 유저
    $userinfo = $_SESSION['user'];
    $userseq = $userinfo->getSEQ();
    $_GET['id']=$userseq;
    include "left.php";
    //중간
    echo '<div id="middle"><span id="prea"></span>';
    echo '</div>';
    //오른쪽
    include "right.php";
    ?>
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
                                    <li><a>전체 공개</a></li>
                                </ul>
                            </li>
                            <li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"
                                                                        role="button" aria-expanded="false">
                                    <span id="directorySettingSub-mod">비분류</span><span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu" id="dirSublist-mod">
                                    <?php
                                    for ($i = 0; $i < count($forder); $i++) {
                                        echo '<li folderid="' . $forder[$i]['SEQ'] . '"><a href="#" >' . $forder[$i]['DIR'] . '</a></li>';
                                    }
                                    ?>
                                </ul>
                            </li>
                        </ul>
                        <!-- 똥싸기와 용돈벌기 내용 -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane" id="send-mod">
                                <div contenteditable="true" class="form-control" id="sendBody-mod" oninput="resize(this)"></div>
                                <hr>
                                <table>
                                    <tr>
                                        <td class="fileinput">
                                            <span><span class="pubico pico-file-plus"></span>파일선택</span>
                                            <input id="fileuploads-mod" name="fileuploads[]" accept="image/*"
                                                   data-url="/php/data/fileUp.php" multiple class="fileupform" type="file">
                                        </td>
                                        <td class="taginput">
                                            <input type="text" class="tag-inputa form-control" placeholder="인물 , 제목">
                                        </td>
                                        <td class="taginput">
                                            <input type="text" class="tag-inputh form-control" placeholder="히힣힣">
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
                                    <div contenteditable="true" class="form-control" id="publiBody-mod" oninput="resize(this)"></div>
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
                                            <input type="text" class="tag-inputa form-control" placeholder="인물 , 제목">
                                        </td>
                                        <td class="taginput">
                                            <input type="text" class="tag-inputh form-control" placeholder="히힣힣">
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
    <!-- The Bootstrap Image Gallery lightbox, should be a child element of the document body -->
    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-use-bootstrap-modal="false">
        <!-- The container for the modal slides -->
        <div class="slides"></div>
        <!-- Controls for the borderless lightbox -->
        <h3 class="title"></h3>
        <a class="prev">‹</a>
        <a class="next">›</a>
        <a class="close">×</a>
        <a class="play-pause"></a>
        <ol class="indicator"></ol>
        <!-- The modal dialog, which will be used to wrap the lightbox content -->
        <div class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body next"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left prev">
                            <i class="glyphicon glyphicon-chevron-left"></i>
                            Previous
                        </button>
                        <button type="button" class="btn btn-primary next">
                            Next
                            <i class="glyphicon glyphicon-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.5.2/jquery.fileupload.min.js"></script>
    <script>
        var iid=<?=$iid?>;
        var userseq=<?=$userseq?>;
        var loadOption = {seq: userseq, getItem: iid};
    </script>
    <script src="/js/itemcard.js"></script>
    <script src="/js/itemload.js"></script>

    <!--    구글 애널리틱스-->
    <script>(function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
            a = s.createElement(o), m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
        ga('create', 'UA-73277050-1', 'auto');
        ga('send', 'pageview');</script>
</div>
</body>
</html>
