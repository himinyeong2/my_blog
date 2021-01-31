var displays = document.getElementsByClassName("display_s");
for (var i = 0; i < displays.length; i++) {
    displays[i].addEventListener("change", function () {
        if (confirm("변경하시겠습니까?")) {
            id = this.id.split("_");
            value = this.value;
            $.ajax({
                url: "?mode=mod&number=" + id[1] + "&value=" + value,
                success: function (data) {
                    if (data == "SUCCESS") {
                        alert("게시물의 상태가 변경되었습니다!");
                        location.reload();
                    }
                }
            });
        }
    });
}
function del_board(number) {
    if (confirm("정말 삭제하시겠습니까??")) {
        $.ajax({
            url: "?mode=del&number=" + number,
            success: function (data) {
                if (data == "SUCCESS") {
                    alert("게시물이 삭제되었습니다!");
                    location.reload();
                }
            }
        });
    }
}
