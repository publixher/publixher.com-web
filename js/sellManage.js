/**
 * Created by gangdong-gyun on 2016. 5. 12..
 */
$(document).ready(function () {
    //jqplot 플러그인
    $.jqplot.config.enablePlugins = true;
    //jqplot 옵션 초기화
    var opts = {
        series: [{
            neighborThreshold: 0
        },{}],
        axesDefaults: {
            tickOptions: {
                fontFamily: 'Helvetica',
                fontSize: '10pt',
                angle: -30
            }
        }
        , axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
                tickOptions: {formatString: "%#m월%#d일 %H시"},
                max: Date.now()
            },
            yaxis: {
                renderer: $.jqplot.LogAxisRenderer,
                tickOptions: {suffix: ' pigs'},
                min: 0
            }
        },
        legend: {
            show: true,
            placement: 'inside'
        },
        seriesDefaults: {
            rendererOptions: {
                smooth: true,
                animation: {
                    show: true
                }
            }
            // ,showMarker:false
        },
        cursor: {zoom: true}
    };
    opts.cursor = {
        zoom: true,
        looseZoom: true,
        showTooltip: true,
        followMouse: true,
        showTooltipOutsideZoom: true,
        constrainOutsideZoom: false
    };
    var page = 0;
    //plot을 전역으로 써준다
    var plot;
    //맨위 컨텐츠 세일 카드 작성하는 함수
    function loadSaleCard(id, title, price, category, sub_category, knock, comment, report, sale, revenue) {
        var card = $('<div>').addClass('item-sale-card').attr('data-itemID', id);
        var span_title = $('<span>').addClass('item-sale-title').text(title);
        var span_price = $('<span>').addClass('item-sale-price').text(price);
        var span_category = $('<span>').addClass('item-sale-category').text(category);
        var span_sub_category = sub_category ? $('<span>').addClass('item-sale-sub_category').text(sub_category) : '';
        var span_knock = $('<span>').addClass('item-sale-knock').text(knock);
        var span_comment = $('<span>').addClass('item-sale-comment').text(comment);
        var span_report = $('<span>').addClass('item-sale-report').text(report);
        var kkuk = sub_category ? $('<span>').addClass('pubico pico-kkuk') : '';
        var span_sale = $('<span>').addClass('item-sale-sale').text(sale);
        var span_revenue = $('<span>').addClass('item-sale-revenue').text(revenue);

        card.append(span_title, span_price, span_category, kkuk, span_sub_category, span_knock,
            span_comment, span_report, span_sale, span_revenue)

        return card;
    }

    //매출 결과 작성
    function loadCMS(total_publixh, total_sale, total_revenue, avg_price, sale_per_item, total_donate, avg_donate, total_point) {
        var result = $('<div>').addClass('cms-result');
        var result_table = $('<table>').addClass('cms-result-table');
        $('<tr>').append($('<td>').text('총 출판'), $('<td>').text(total_publixh + ' 회')).appendTo(result_table);
        $('<tr>').append($('<td>').text('총 판매'), $('<td>').text(total_sale + ' 회')).appendTo(result_table);
        $('<tr>').append($('<td>').text('총 판매금액'), $('<td>').text(total_point + ' pigs')).appendTo(result_table);
        $('<tr>').append($('<td>').text('총 후원'), $('<td>').text(total_donate + ' pigs')).appendTo(result_table);
        $('<tr>').append($('<td>').text('총 매출'), $('<td>').text(total_revenue + ' pigs')).appendTo(result_table);
        $('<tr>').append($('<td>').text('평균 판매 가격'), $('<td>').text(avg_price + ' pigs')).appendTo(result_table);
        $('<tr>').append($('<td>').text('출판당 평균 판매'), $('<td>').text(sale_per_item + ' 회')).appendTo(result_table);
        $('<tr>').append($('<td>').text('출판당 평균 후원'), $('<td>').text(avg_donate + ' pigs')).appendTo(result_table);
        result.append(result_table);
        return result;
    }

    // 최신순,판매순,매출순을 sort로 받고 혹시 더보기를 대비해 page를 받아서 칸에 뿌려주는 함수
    function getmost(sort, page) {
        //스피너
        var spinner = $('<div>')
            .attr('data-loader', 'spinner')
            .addClass('load-item content-load');
        $('#most-content').append(spinner);
        $.ajax({
            url: '/php/data/cms.php',
            dataType: 'json',
            type: 'GET',
            timeout: 10000,
            data: {sort: sort, page: page, action: "most"},
            tryCount: 0,
            retryLimit: 3,
            success: function (res) {
                var most_content = $('#most-content');
                if (res.length == 0) {
                    $('<div>').text('검색 결과가 없습니다')
                        .addClass('most-result')
                        .appendTo(most_content)
                } else {
                    for (var i = 0; i < res.length; i++) {
                        var title = res[i]['TITLE'];
                        var price = res[i]['PRICE'];
                        var category = res[i]['CATEGORY'];
                        var sub_category = res[i]['SUB_CATEGORY'];
                        var knock = res[i]['KNOCK'];
                        var comment = res[i]['COMMENT'];
                        var report = res[i]['REPORT'];
                        var sale = res[i]['SALE'];
                        var revenue = res[i]['REVENUE'];
                        var id = res[i]['ID'];

                        var card = loadSaleCard(id, title, price, category, sub_category
                            , knock, comment, report, sale, revenue);
                        most_content.append(card);
                    }
                    page++;
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                if (textStatus == 'timeout') {
                    this.tryCount++;
                    if (this.tryCount <= this.retryLimit) {
                        //try again
                        $.ajax(this);
                        return;
                    }
                    spinner.detach();
                    return;
                }
                errorReport("cms",{sort: sort, page: page, action: "most"},textStatus,errorThrown)
                //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
            }, complete: function () {
                spinner.detach();
            }
        })
    }

    //월별 매출등
    function getMonthly(start, end) {
        //스피너
        var spinner = $('<div>')
            .attr('data-loader', 'spinner')
            .addClass('load-item content-load');
        $('#most-content').append(spinner);
        $.ajax({
            url: '/php/data/cms.php',
            dataType: 'json',
            type: 'GET',
            timeout: 10000,
            data: {action: "monthly", start: start, end: end},
            tryCount: 0,
            retryLimit: 3,
            success: function (res) {
                var cms_date = $('#cms-result');

                var total_publixh = res['TOTAL_PUBLIXH'];
                var total_sale = res['TOTAL_SALE'];
                var total_revenue = res['TOTAL_REVENUE'];
                var total_donate = res['TOTAL_DONATE'];
                var total_point = res['TOTAL_POINT'];
                var avg_price = Math.round(res['AVG_PRICE']);
                var sale_per_item = Math.round(res['SALE_PER_ITEM']);
                var avg_donate = Math.round(res['DONATE_PER_ITEM']);

                var result = loadCMS(total_publixh, total_sale, total_revenue, avg_price, sale_per_item, total_donate, avg_donate, total_point);
                cms_date.append(result);
            }, complete: function () {
                spinner.detach();
                $('#start_date').removeAttr('disabled');
                $('#end_date').removeAttr('disabled');
            },error:function(xhr,status,error){
            errorReport("cms_monthly",{action: "monthly", start: start, end: end},status,error);
            }
        })
    }

    //그래프를 그려보자 >,.<
    function getItemCms(id) {
        var cms_item = $('#cms_item');
        var spinner = $('<div>')
            .attr('data-loader', 'spinner')
            .addClass('load-item content-load');
        cms_item.append(spinner);

        $.ajax({
            url: '/php/data/cms.php',
            dataType: 'json',
            type: 'GET',
            timeout: 10000,
            data: {action: "item", contentID: id},
            tryCount: 0,
            retryLimit: 3,
            success: function (res) {
                var donate = [];
                var price = [];
                var data = [];
                var ymax = 0;
                opts.series[0].label=opts.series[1].label=undefined;
                for (var i = 0; i < res['DONATE'].length; i++) {
                    donate.push([res['DONATE'][i]['DATE'], res['DONATE'][i]['DONATE']])
                }
                for (var i = 0; i < res['PRICE'].length; i++) {
                    price.push([res['PRICE'][i]['DATE'], res['PRICE'][i]['PRICE']])
                }
                if(donate.length > 0){
                    data.push(donate);
                    opts.series[0].label='후원';
                }

                if(price.length > 0){
                    data.push(price);
                    opts.series[0].label?
                        opts.series[1].label='구매':
                        opts.series[0].label='구매';
                }

                if (data.length < 1) {
                    $('#cms-item').append(
                        $('<div>').addClass('no-data').text('아직 아무도 구매나 후원하지 않았네요;;')
                    );
                    return false;
                }
                //y최대값 구하고 그걸로 tick구하기
                for (var i = 0; i < donate.length; i++) {
                    ymax < donate[i][1] ? ymax = donate[i][1] : null;
                }
                for (var i = 0; i < price.length; i++) {
                    ymax < price[i][1] ? ymax = price[i][1] : null;
                }
                ymax = parseInt(ymax) + parseInt(ymax) * 0.1;
                opts.axes.yaxis.max = ymax;
                opts.axes.yaxis.tickInterval = ymax / 4;
                //그려보자 ^^
                if (plot)    plot.destroy();
                plot = $.jqplot('cms-item', data, opts)

            },
            error: function (xhr, textStatus, errorThrown) {
                if (textStatus == 'timeout') {
                    this.tryCount++;
                    if (this.tryCount <= this.retryLimit) {
                        //try again
                        $.ajax(this);
                        return;
                    }
                    spinner.detach();
                    return;
                }
                errorReport("cms_graph",{action: "item", contentID: id},textStatus,errorThrown)
                //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
            }, complete: function () {
                spinner.detach();
            }
        })
    }

    //위에 버튼 클릭할때 동작
    $('#late-btn,#sell-btn,#money-btn').on('click', function () {
        var id = $(this).attr('id');
        var sort = '';
        switch (id) {
            case 'late-btn':
                sort = 'late';
                break;
            case 'sell-btn':
                sort = 'sell';
                break;
            case 'money-btn':
                sort = 'money';
                break;
        }
        //정렬별 최고 순위 내용물 없애고 새로 로딩함
        $('#most-content').html('');
        //그래프도 삭제
        if (plot) {
            plot.destroy();
            $('#cms-item').removeAttr('style')
        }
        page = 0;
        getmost(sort, page);
    });

    //처음 페이지 로딩되면 late-btn 누르기 트리거
    $('#late-btn').trigger('click');

    //날짜 바꾸면 바뀐날짜로 다시 통계 얻어오기
    $('#sandbox-container .input-daterange').on('changeDate', function () {
        $('#cms-result').html('');
        var start_date = $('#start_date');
        var end_date = $('#end_date');
        var start = start_date.datepicker('getUTCDate').toISOString().slice(0, 10);
        var end = end_date.datepicker('getUTCDate').toISOString().slice(0, 10);
        start_date.attr('disabled', 'disabled');
        end_date.attr('disabled', 'disabled');
        getMonthly(start, end);
    })

    //그래프 그리기
    $(document).on('click', '.item-sale-card', function () {
        var itemId = $(this).attr('data-itemid');
        $('#cms-item').children('.no-data').remove();
        getItemCms(itemId);
    })

});