function board_add(){
  var password = prompt("관리자 암호를 입력하세용~!!");
      $.ajax({
          url: "check_admin.php",
          type: "post",
          data: { "password": password },
          success: function (data) {
              if (data == "SUCCESS") {
        location.href="board.php?mode=add";
              } else {
                  alert("암호를 확인해주세요!!");
              }
          }
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
function board_mod(number){
  var password = prompt("관리자 암호를 입력하세용~!!");
  $.ajax({
      url: "check_admin.php",
      type: "post",
      data: { "password": password },
      success: function (data) {
          if (data == "SUCCESS") {
              location.href='board.php?mode=add&number='+number;
          } else {
              alert("암호를 확인해주세요!!");
          }
      }
  });
}
function wait(){
    alert("준비중입니다!");
    return;
}
function board_del(number) {
  var password = prompt("관리자 암호를 입력하세용~!!");
  $.ajax({
      url: "check_admin.php",
      type: "post",
      data: { "password": password },
      success: function (data) {
          if (data == "SUCCESS") {
              if (confirm("정말로 삭제하시겠습니까?")) {
                  location.href = "board.php?mode=del&number=" + number;
               }
          } else {
              alert("암호를 확인해주세요!!");
          }
      }
  });
  
}

jQuery(function ($) {

    $(".sidebar-dropdown > a").click(function() {
    $(".sidebar-submenu").slideUp(200);
    if (
    $(this)
      .parent()
      .hasClass("active")
    ) {
    $(".sidebar-dropdown").removeClass("active");
    $(this)
      .parent()
      .removeClass("active");
    } else {
    $(".sidebar-dropdown").removeClass("active");
    $(this)
      .next(".sidebar-submenu")
      .slideDown(200);
    $(this)
      .parent()
      .addClass("active");
    }
    });
    
    $("#close-sidebar").click(function() {
    $(".page-wrapper").removeClass("toggled");
    });
    $("#show-sidebar").click(function() {
    $(".page-wrapper").addClass("toggled");
    });
    
    
    
    
    });