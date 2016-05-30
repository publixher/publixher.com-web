<script src="/js/best.js"></script>
<div class="market">
    <div id="market-nav">
        <div id="market-nav-carousel" class="carousel slide" data-ride="carousel" data-pause="hover">
            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                <div class="item active">
                    <div role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#now" aria-controls="now" role="tab" data-toggle="tab">지금 HOT!</a></li>
                            <li role="presentation"><a href="#daily" aria-controls="daily" role="tab" data-toggle="tab">오늘 HOT!</a></li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="now-hot-list">
                            </div>
                            <div role="tabpanel" class="tab-pane" id="daily-hot-list"></div>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#monthly" aria-controls="monthly" role="tab" data-toggle="tab">주간 HOT!</a></li>
                            <li role="presentation"><a href="#your" aria-controls="your" role="tab" data-toggle="tab">나만 HOT!</a></li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="monthly-hot-list"></div>
                            <div role="tabpanel" class="tab-pane" id="your"></div>
                        </div>
                    </div>
                </div>
            </div>
            <a class="left carousel-control">
                <span class="pubico pico-plus" id="go-market"></span>
            </a>
            <a class="right carousel-control" href="#market-nav-carousel" role="button" data-slide="next">
                전환
            </a>
        </div>
    </div>
</div>