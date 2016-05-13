/**
 * Created by gangdong-gyun on 2016. 5. 12..
 */
$(document).ready(function () {
    var page=0;
    //맨위 컨텐츠 세일 카드 작성하는 함수
    function loadSaleCard(title, price, category, sub_category, knock, comment, report, sale, revenue) {
        var card = $('<div>').addClass('item-sale-card');
        var span_title = $('<span>').addClass('item-sale-title').text(title);
        var span_price = $('<span>').addClass('item-sale-price').text(price);
        var span_category = $('<span>').addClass('item-sale-category').text(category);
        var span_sub_category = sub_category?$('<span>').addClass('item-sale-sub_category').text(sub_category):'';
        var span_knock = $('<span>').addClass('item-sale-knock').text(knock);
        var span_comment = $('<span>').addClass('item-sale-comment').text(comment);
        var span_report = $('<span>').addClass('item-sale-report').text(report);
        var kkuk=sub_category?$('<span>').addClass('pubico pico-kkuk'):'';
        var span_sale = $('<span>').addClass('item-sale-sale').text(sale);
        var span_revenue = $('<span>').addClass('item-sale-revenue').text(revenue);

        card.append(span_title, span_price, span_category,kkuk, span_sub_category, span_knock,
            span_comment, span_report, span_sale, span_revenue);
        return card;
    }
    //매출 결과 작성
    function loadCMS(total_publixh,total_sale,total_revenue,avg_price,sale_per_item,revenue_per_item){
        var result=$('<div>').addClass('cms-result');
        var result_table = $('<table>').addClass('cms-result-table');
        $('<tr>').append($('<td>').text('총 출판'), $('<td>').text(total_publixh+' 회')).appendTo(result_table);
        $('<tr>').append($('<td>').text('총 판매'), $('<td>').text(total_sale)).appendTo(result_table);
        $('<tr>').append($('<td>').text('총 매출'), $('<td>').text(total_revenue)).appendTo(result_table);
        $('<tr>').append($('<td>').text('편균 판매 가격'), $('<td>').text(avg_price)).appendTo(result_table);
        $('<tr>').append($('<td>').text('출판당 평균 판매'), $('<td>').text(sale_per_item)).appendTo(result_table);
        $('<tr>').append($('<td>').text('출판당 평균 매출'), $('<td>').text(revenue_per_item)).appendTo(result_table);
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
            data: {sort: sort, page: page,action:"most"},
            tryCount: 0,
            retryLimit: 3,
            success: function (res) {
                console.log(res)
                var most_content = $('#most-content');
                spinner.detach();
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

                        var card = loadSaleCard(title, price, category, sub_category
                            , knock, comment, report, sale, revenue);

                        most_content.append(card);
                        page++;
                    }
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
                if (xhr.status == 500) {
                    spinner.detach();
                    console.log('서버 오류! 관리자에게 문의하기')
                } else {
                    spinner.detach();
                    console.log('몰랑몰랑')
                }
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
            data: {action:"monthly",start:start,end:end},
            tryCount: 0,
            retryLimit: 3,
            success: function (res) {
                console.log(res)
                var cms_date = $('#cms-result');
                cms_date.detach(spinner);

                var total_publixh = res['TOTAL_PUBLIXH'];
                var total_sale = res['TOTAL_SALE'];
                var total_revenue = res['TOTAL_REVENUE'];
                var avg_price = res['AVG_PRICE'];
                var sale_per_item = res['SALE_PER_ITEM'];
                var revenue_per_item = res['REVENUE_PER_ITEM'];

                var result=loadCMS(total_publixh,total_sale,total_revenue,avg_price,
                sale_per_item,revenue_per_item);
                cms_date.append(result);
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
                if (xhr.status == 500) {
                    spinner.detach();
                    console.log('서버 오류! 관리자에게 문의하기')
                } else {
                    spinner.detach();
                    console.log('몰랑몰랑')
                }
            },complete:function(){
                $('#start_date').removeAttr('disabled');
                $('#end_date').removeAttr('disabled');
            }
        })
    }

    //위에 버튼 클릭할때 동작
    $('#late-btn,#sell-btn,#money-btn').on('click', function () {
        var id = $(this).attr('id');
        var sort = '';
        var page=0;
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
        page=0;
        getmost(sort,page);
    });

    //처음 페이지 로딩되면 late-btn 누르기 트리거
    $('#late-btn').trigger('click');

    //날짜 바꾸면 바뀐날짜로 다시 통계 얻어오기
    $('#sandbox-container .input-daterange').on('changeDate',function(){
        $('#cms-result').html('');
        var start_date=$('#start_date');
        var end_date=$('#end_date');
        var start = start_date.datepicker('getDate');
        var end=end_date.datepicker('getDate');
        start=start.getFullYear()+'/'+start.getMonth()+'/'+start.getDay();
        end=end.getFullYear()+'/'+end.getMonth()+'/'+end.getDay();
        start_date.attr('disabled','disabled');
        end_date.attr('disabled','disabled');
        getMonthly(start,end);
    })

});