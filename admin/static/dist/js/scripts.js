/*!
    * Start Bootstrap - SB Admin v6.0.2 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2020 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
    (function($) {
    "use strict";

    // Add active state to sidbar nav links
    var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
        $("#layoutSidenav_nav .sb-sidenav a.nav-link").each(function() {
            if (this.href === path) {
                $(this).addClass("active");
            }
        });

    // Toggle the side navigation
    $("#sidebarToggle").on("click", function(e) {
        e.preventDefault();
        $("body").toggleClass("sb-sidenav-toggled");
    });
})(jQuery);


$('#summernote').summernote({
    placeholder: '내용을 입력해주세요',
    tabsize: 2,
    height: 700,
    toolbar: [
        // [groupName, [list of button]]
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']]
    ],
    blockquoteBreakingLevel: 2,
    styleTags: [
        'p',
        { title: 'Blockquote', tag: 'blockquote', className: 'blockquote', value: 'blockquote' },
        'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
    ]

});
if(document.getElementsByName('category')[0]){
    document.getElementsByName('category')[0].addEventListener('change', function () {
        $.ajax({
            url: 'category.php?mode=select&category=' + this.value,
            success: function (data) {
                // alert(data);
                if (document.getElementsByName('sub_category').length == 1) {
                    document.getElementsByName('sub_category')[0].remove();
                    $('#modal_body').append(data);
                }
                else
                    $('#modal_body').append(data);
            }
        })
    });
}
if(document.getElementById('insert_btn')){
    document.getElementById('insert_btn').addEventListener('click', function () {
        document.getElementById('modal').style.display = "block";
    });
}
if(document.getElementById('close_btn')){
    document.getElementById('close_btn').addEventListener('click', function () {
        document.getElementById('modal').style.display = "none";
    });
}


function check(value) {

    if (value == '' || value == null) {
        alert('값을 입력해주세요');
        return false;
    }

    var blank_pattern = /^\s+|\s+$/g;
    if (value.replace(blank_pattern, '') == "") {
        alert(' 공백만 입력되었습니다 ');
        return false;
    }



    //공백 금지
    //var blank_pattern = /^\s+|\s+$/g;(/\s/g
    var blank_pattern = /[\s]/g;
    if (blank_pattern.test(value) == true) {
        alert(' 공백은 사용할 수 없습니다. ');
        return false;
    }


    var special_pattern = /[`~!@#$%^&*|\\\'\";:\/?]/gi;

    if (special_pattern.test(value) == true) {
        alert('특수문자는 사용할 수 없습니다.');
        return false;
    }

   return true;
}