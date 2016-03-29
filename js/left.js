/**
 * Created by gangdong-gyun on 2016. 3. 29..
 */
$(document).ready(function(){
    $('.accordion-toggle,.nameuser').on('click', function () {
        var mu=($(this).attr('href')).replace('#collapse','')
        $.ajax({url:"php/data/subscribe.php", type: "GET", data: {mu:mu,action:"check"}, dataType: 'json'})
        $('.newcontent[data-substarget='+mu+']').remove();
    })
});