<?php
include_once 'db/dbcon.php';


function project_list($html)
{

    global $conn;

    $field = $_GET['field'];
    $keyword = $_GET['search_keyword'];
    $WHERE = "WHERE category =1  ";
    $WHERE = ($field != '') ? $WHERE .= " AND " . $field . "  LIKE '%$keyword%'" : $WHERE;

    if ($html == "index") {
        $sql = "SELECT * FROM me_blog_board $WHERE order by reg_date desc LIMIT 0,4";
    } else {
        $sql = "SELECT * FROM me_blog_board $WHERE   order by reg_date";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (sizeof($result) == 0) {
        $content = '  <div align="center" class="col-sm-12">등록된 프로젝트가 없습니다.</div>';

    } else {
        foreach ($result as $data) {
            $hashtag = explode(",", $data['tag']);
            $hash_tag = '';
            if ($data['tag'] != '') {
                foreach ($hashtag as $tag) {
                    $hash_tag .= ' <span>#' . $tag . '</span>';
                }
            }

            $content .= '
            <div align="center" class="col-sm-3">
                    <div class="card border-dark mb-3" style="max-width: 20rem;">
                        <div class="card-header">
                            <img class="cursor_pointer" onclick="location.href=\'board.php?mode=show&number=' . $data['number'] . '\'" src="' . $data['main_image'] . '">
                        </div>
                        <div class="card-body">
                            <h4 class="card-title cursor_pointer" onclick="location.href=\'board.php?mode=show&number=' . $data['number'] . '\'">' . $data['title'] . '</h4>
                            <p class="card-text">
                                ' . $hash_tag . '
                            </p>
                        </div>
                    </div>
                </div>
            ';
        }
    }

    $RETURN = '
        <div class="col-sm-12 mb-4">
        <div class="row">
          ' . $content . '
        </div>
    </div>
        ';
    return $RETURN;
}
function paging($block_start, $block_end,$block_num, $total_block,$page){
    $RETURN = '<ul class="pagination">';

    $category = $_GET['category_number'];
    // if($page <= 1)
    // { //만약 page가 1보다 크거나 같다면
    //   $RETURN.= "<li class='fo_re'>처음</li>"; //처음이라는 글자에 빨간색 표시 
    // }else{
    //     $RETURN.= "<li><a href='?page=1&mode=list&category_number=$category'>처음</a></li>"; //알니라면 처음글자에 1번페이지로 갈 수있게 링크
    // }
    if($page <= 1)
    { //만약 page가 1보다 크거나 같다면 빈값
      
    }else{
    $pre = $page-1; //pre변수에 page-1을 해준다 만약 현재 페이지가 3인데 이전버튼을 누르면 2번페이지로 갈 수 있게 함
    $RETURN.= "<li class='page-item active'><a class='page-link' href='?page=$pre&mode=list&category_number=$category'>&laquo;</a></li>"; //이전글자에 pre변수를 링크한다. 이러면 이전버튼을 누를때마다 현재 페이지에서 -1하게 된다.
    }
    for($i=$block_start; $i<=$block_end; $i++){ 
      //for문 반복문을 사용하여, 초기값을 블록의 시작번호를 조건으로 블록시작번호가 마지박블록보다 작거나 같을 때까지 $i를 반복시킨다
      if($page == $i){ //만약 page가 $i와 같다면 
        $RETURN.= "<li class='page-item active'><a class='page-link'>$i</a></li>"; //현재 페이지에 해당하는 번호에 굵은 빨간색을 적용한다
      }else{
        $RETURN.= "<li class='page-item'><a class='page-link' href='?page=$i&mode=list&category_number=$category'>$i</a></li>"; //아니라면 $i
      }
    }
    
    if($block_num >= $total_block){ //만약 현재 블록이 블록 총개수보다 크거나 같다면 빈 값
    }else{
      $next = $page + 1; //next변수에 page + 1을 해준다.
      $RETURN.= "<li class='page-item'><a class='page-link' href='?page=$next&mode=list&category_number=$category'>&raquo;</a></li>"; //다음글자에 next변수를 링크한다. 현재 4페이지에 있다면 +1하여 5페이지로 이동하게 된다.
    }
    $RETURN.="</ul>";
    // if($page >= $total_page){ //만약 page가 페이지수보다 크거나 같다면
    //     $RETURN.= "<li class='fo_re'>마지막</li>"; //마지막 글자에 긁은 빨간색을 적용한다.
    // }else{
    //     $RETURN.= "<li><a href='?page=$total_page&mode=list&category_number=$category'>마지막</a></li>"; //아니라면 마지막글자에 total_page를 링크한다.
    // }
    return $RETURN;
}
function study_list($html)
{
    global $conn,$paging;
    if ($html == "index") {

        $stmt = $conn->prepare("SELECT b.number, b.title, c.name, b.reg_date FROM me_blog_board b  LEFT OUTER JOIN  me_blog_category c ON b.sub_category = c.number WHERE category = 2 order by b.reg_date desc LIMIT 0,5");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (sizeof($result) == 0) {
            $content = ' <td colspan=4 class="text-center" scope="row">등록된 게시글이 없습니다.</td> ';

        } else {
            foreach ($result as $data) {
                $hashtag = explode(",", $data['tag']);
                $hash_tag = '';
                if ($data['tag'] != '') {
                    foreach ($hashtag as $tag) {
                        $hash_tag .= ' <span>#' . $tag . '</span>';
                    }
                }

                $content .= '
                <tr  class="table-active cursor_pointer"  onclick="location.href=\'board.php?mode=show&number=' . $data['number'] . '\'">
                    <td class="text-center">' . $data['number'] . '</td>
                    <td class="text-center">' . $data['title'] . '</td>
                    <td class="text-center">' . $data['name'] . '</td>
                  </tr>
                ';
            }
        }

        $RETURN = '
            <table class="table table-hover">
            <tbody>
            ' . $content . '
            </tbody>
        </table>
            ';

    } else {

        $sub_category = $_GET['sub_category'];
        $field = $_GET['field'];
        $keyword = $_GET['search_keyword'];
        $WHERE = "WHERE category = 2 ";
        $WHERE = ($sub_category != '') ? $WHERE .= " AND sub_category = " . $sub_category : $WHERE;
        $WHERE = ($field != '') ? $WHERE .= " AND " . $field . "  LIKE '%$keyword%'" : $WHERE;


        /* paging */
        $sql1 =  $conn->prepare("SELECT number FROM me_blog_board " . $WHERE . " order by number desc");
        $sql1->execute();
        $row_num = $sql1->rowCount();

        $page = ($_GET['page']!='')? $_GET['page'] : 1;
        $list = 10;
        $block_ct=5;

        $block_num = ceil($page/$block_ct); // 현재 페이지 블록 구하기
        $block_start = (($block_num - 1) * $block_ct) + 1; // 블록의 시작번호
        $block_end = $block_start + $block_ct - 1; //블록 마지막 번호

        $total_page = ceil($row_num / $list); // 페이징한 페이지 수 구하기
        if($block_end > $total_page) $block_end = $total_page; //만약 블록의 마지박 번호가 페이지수보다 많다면 마지박번호는 페이지 수
        $total_block = ceil($total_page/$block_ct); //블럭 총 개수
        $start_num = ($page-1) * $list; //시작번호 (page-1)에서 $list를 곱한다.

        $paging = paging($block_start, $block_end, $block_num,$total_block, $page);

        /* paging */

        $stmt = $conn->prepare("SELECT b.number, b.title, c.name, b.reg_date FROM me_blog_board b LEFT OUTER JOIN me_blog_category c ON b.sub_category = c.number " . $WHERE . " order by b.number desc LIMIT $start_num, $list");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        


        if (sizeof($result) == 0) {
            $content = ' <td colspan=4 class="text-center" scope="row">등록된 게시글이 없습니다.</td> ';

        } else {
            foreach ($result as $data) {
                $hashtag = explode(",", $data['tag']);
                $hash_tag = '';
                if ($data['tag'] != '') {
                    foreach ($hashtag as $tag) {
                        $hash_tag .= ' <span>#' . $tag . '</span>';
                    }
                }

                $content .= '
                <tr class="table-active cursor_pointer"  onclick="location.href=\'?mode=show&number=' . $data['number'] . '\'">
                    <td class="text-center" scope="row">' . $data['number'] . '</td>
                    <td class="text-center">' . $data['title'] . '</td>
                    <td class="text-center">' . $data['name'] . '</td>
                    <td class="text-center">' . $data['reg_date'] . '</td>
                  </tr>
                ';
            }
        }

        $RETURN = '
            <table class="table table-hover">
            <thead >
                <tr style="background-color: #666; ">
                  <th style="color:white;" class="text-center" scope="col" width="30">no</th>
                  <th style="color:white;" class="text-center" scope="col" width="200">title</th>
                  <th style="color:white;" class="text-center" scope="col" width="100">category</th>
                  <th style="color:white;" class="text-center" scope="col" width="100">date</th>
                </tr>
              </thead>
            <tbody>
            ' . $content . '
            </tbody>
        </table>
            ';
    }
    return $RETURN;
}

function algorithm_list($html)
{
    global $conn,$paging;

    if ($html == "index") {
        $stmt = $conn->prepare("SELECT b.number, b.title, c.name, b.reg_date FROM me_blog_board b  LEFT OUTER JOIN me_blog_category c ON b.sub_category = c.number WHERE category = 3 order by b.reg_date desc LIMIT 0,5");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (sizeof($result) == 0) {
            $content = ' <td colspan=4 class="text-center" scope="row">등록된 게시글이 없습니다.</td> ';

        } else {
            foreach ($result as $data) {
                $hashtag = explode(",", $data['tag']);
                $hash_tag = '';
                if ($data['tag'] != '') {
                    foreach ($hashtag as $tag) {
                        $hash_tag .= ' <span>#' . $tag . '</span>';
                    }
                }

                $content .= '
                <tr  class="table-active cursor_pointer"  onclick="location.href=\'board.php?mode=show&number=' . $data['number'] . '\'">
                    <td class="text-center">' . $data['number'] . '</td>
                    <td class="text-center">' . $data['title'] . '</td>
                    <td class="text-center">' . $data['name'] . '</td>
                  </tr>
                ';
            }
        }

        $RETURN = '
            <table class="table table-hover">
            <tbody>
            ' . $content . '
            </tbody>
        </table>
            ';
    } else {

        $sub_category = $_GET['sub_category'];
        $field = $_GET['field'];
        $keyword = $_GET['search_keyword'];
        $WHERE = "WHERE category = 3 ";
        $WHERE = ($sub_category != '') ? $WHERE .= " AND sub_category = " . $sub_category : $WHERE;
        $WHERE = ($field != '') ? $WHERE .= " AND " . $field . "  LIKE '%$keyword%'" : $WHERE;

        /* paging */
        $sql1 =  $conn->prepare("SELECT number FROM me_blog_board " . $WHERE . " order by number desc"); //전체
        $sql1->execute();
        $row_num = $sql1->rowCount();

        $page = ($_GET['page']!='')? $_GET['page'] : 1;
        $list = 10;
        $block_ct=5;

        $block_num = ceil($page/$block_ct); // 현재 페이지 블록 구하기
        $block_start = (($block_num - 1) * $block_ct) + 1; // 블록의 시작번호
        $block_end = $block_start + $block_ct - 1; //블록 마지막 번호

        $total_page = ceil($row_num / $list); // 페이징한 페이지 수 구하기
        if($block_end > $total_page) $block_end = $total_page; //만약 블록의 마지박 번호가 페이지수보다 많다면 마지박번호는 페이지 수
        $total_block = ceil($total_page/$block_ct); //블럭 총 개수
        $start_num = ($page-1) * $list; //시작번호 (page-1)에서 $list를 곱한다.

        $paging = paging($block_start, $block_end, $block_num,$total_block, $page);

        /* paging */


        $stmt = $conn->prepare("SELECT b.number, b.title, c.name, b.reg_date FROM me_blog_board b  LEFT OUTER JOIN  me_blog_category c ON b.sub_category = c.number " . $WHERE . " order by b.number desc LIMIT $start_num, $list");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (sizeof($result) == 0) {
            $content = ' <td colspan=4 class="text-center" scope="row">등록된 게시글이 없습니다.</td> ';

        } else {
            foreach ($result as $data) {
                $hashtag = explode(",", $data['tag']);
                $hash_tag = '';
                if ($data['tag'] != '') {
                    foreach ($hashtag as $tag) {
                        $hash_tag .= ' <span>#' . $tag . '</span>';
                    }
                }

                $content .= '
                <tr  class="table-active cursor_pointer"   onclick="location.href=\'?mode=show&number=' . $data['number'] . '\'">
                    <td class="text-center" scope="row">' . $data['number'] . '</td>
                    <td class="text-center">' . $data['title'] . '</td>
                    <td class="text-center">' . $data['name'] . '</td>
                    <td class="text-center">' . $data['reg_date'] . '</td>
                  </tr>
                ';
            }
        }

        $RETURN = '
            <table class="table table-hover">
            <thead >
                <tr style="background-color: #666; ">
                  <th style="color:white;" class="text-center" scope="col" width="30">no</th>
                  <th style="color:white;" class="text-center" scope="col" width="200">title</th>
                  <th style="color:white;" class="text-center" scope="col" width="100">category</th>
                  <th style="color:white;" class="text-center" scope="col" width="100">date</th>
                </tr>
              </thead>
            <tbody>
            ' . $content . '
            </tbody>
        </table>
            ';
    }

    return $RETURN;
}
function guest_list()
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM me_blog_guest order by reg_date");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $data) {
        $mod_btn = '<a class="btn small_btn" onclick="check_password(' . $data['number'] . ',\'mod\')">수정</a>';
        $del_btn = ' <a class="btn small_btn" onclick="check_password(' . $data['number'] . ',\'del\')">삭제</a>';
        $mod_reg_btn = ' <a onclick="document.mod_form_' . $data['number'] . '.submit()" class="btn small_btn">수정</a>';
        $cancel_btn = ' <a onclick="open_comments_tab(' . $data['number'] . ')" class="btn small_btn">취소</a>';

        $comments .= '
        <div id="comments_tab_' . $data['number'] . '" class="card  mb-3 pl-0 col-sm-12">
             <form>
             <div class="row card-body">
                 <div align="left" class="col-md-6">' . $data['name'] . ' &nbsp;&nbsp;<span class="board_date">' . $data['reg_date'] . '</span></div>
                 <div align="right" class="col-md-6">
                     ' . $mod_btn . $del_btn . '
                 </div>
                 <div align="left" class="col-md-12 my-2">' . $data['contents'] . '</div>
             </div>
             </form>
         </div>

         <div id="mod_tab_' . $data['number'] . '" style="display:none;" class="card  mb-3 pl-0 col-sm-12">
            <form name="mod_form_' . $data['number'] . '" method="post" action="guest.php?mode=mod&number=' . $data['number'] . '">
                <div class="row card-body">
                    <div align="left" class="col-md-8">' . $data['name'] . ' &nbsp;&nbsp;<span class="board_date">' . $data['reg_date'] . '</span></div>
                    <div align="right" class="col-md-4">
                        ' . $mod_reg_btn . $cancel_btn . '
                    </div>
                    <div align="left" class="col-md-12 my-2"><textarea name="contents">' . $data['contents'] . '</textarea></div>
                </div>
            </form>
        </div>
        ';
    }
    $script = '<script>
        function open_reply_tab(number){
            if(document.getElementById("reply_tab_"+number).style.display=="none"){
                document.getElementById("reply_tab_"+number).style.display="block";
            }else{
                document.getElementById("reply_tab_"+number).style.display="none";
            }
        }
        function open_mod_tab(number){
             document.getElementById("comments_tab_"+number).style.display="none";
             document.getElementById("mod_tab_"+number).style.display="block";

        }
        function open_comments_tab(number){
            document.getElementById("comments_tab_"+number).style.display="block";
            document.getElementById("mod_tab_"+number).style.display="none";

        }
        function del_comments(number){
            $.ajax({
                url:"guest.php?mode=del&number="+number,
                success:function(data){
                   if(data=="SUCCESS"){
                       alert("삭제가 완료되었습니다");
                       location.reload();
                   }else{
                       alert(data);
                   }
                }
            })
        }
        function check_password(number,mode){
            var password = prompt("비밀번호를 입력하세요");
            $.ajax({
                url:"guest.php?mode=check_password",
                type:"post",
                data:{"password":password,"number":number},
                success:function(data){
                    if(data=="SAME"){
                        if(mode=="mod"){
                            open_mod_tab(number);
                        }else if(mode=="del"){
                            if(confirm("정말로 삭제하시겠습니까")){
                               del_comments(number);
                            }
                        }
                    }else{
                       alert("패스워드를 확인해주세요");
                    }
                }
            });
        }
        </script>
    ';
    return $comments . $script;
}
function comments_list($number)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM me_blog_comments WHERE board_number=$number order by sort ");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $data) {
        $mod_btn = '<a class="btn small_btn" onclick="check_password(' . $data['number'] . ',\'mod\')">수정</a>';
        $del_btn = ' <a class="btn small_btn" onclick="check_password(' . $data['number'] . ',\'del\')">삭제</a>';
        $reply_btn = '<a class="btn small_btn" onclick="open_reply_tab(' . $data['number'] . ')">답글</a>';
        $reply_reg_btn = '<a class="btn small_btn" onclick="document.reply_form_' . $data['number'] . '.submit()">등록</a>';
        $mod_reg_btn = ' <a onclick="document.mod_form_' . $data['number'] . '.submit()" class="btn small_btn">수정</a>';
        $cancel_btn = ' <a onclick="open_comments_tab(' . $data['number'] . ')" class="btn small_btn">취소</a>';

        if ($data['parent'] == 0) { //최상위 댓글이면

            $comments .= '
            <div id="comments_tab_' . $data['number'] . '" class="card  mb-3 pl-0 col-sm-12">
                 <form>
                 <div class="row card-body">
                     <div align="left" class="col-md-6">' . $data['name'] . ' &nbsp;&nbsp;<span class="board_date">' . $data['reg_date'] . '</span></div>
                     <div align="right" class="col-md-6">
                         ' . $mod_btn . $del_btn . $reply_btn . '
                     </div>
                     <div align="left" class="col-md-12 my-2">' . $data['comments'] . '</div>
                 </div>
                 </form>
             </div>

             <div id="mod_tab_' . $data['number'] . '" style="display:none;" class="card  mb-3 pl-0 col-sm-12">
                <form name="mod_form_' . $data['number'] . '" method="post" action="board_comments.php?mode=mod&number=' . $data['number'] . '">
                <input hidden type="text" name="board_number" value="' . $data['board_number'] . '">
                    <div class="row card-body">
                        <div align="left" class="col-md-8">' . $data['name'] . ' &nbsp;&nbsp;<span class="board_date">' . $data['reg_date'] . '</span></div>
                        <div align="right" class="col-md-4">
                            ' . $mod_reg_btn . $cancel_btn . '
                        </div>
                        <div align="left" class="col-md-12 my-2"><textarea name="comments">' . $data['comments'] . '</textarea></div>
                    </div>
                </form>
            </div>

             <div id="reply_tab_' . $data['number'] . '" style="display:none;" class="card mb-3 pl-0 col-sm-12">
             <form action="board_comments.php?mode=reg_reply" name="reply_form_' . $data['number'] . '" method="POST">
             <input hidden type="text" name="parent" value="' . $data['number'] . '">
             <input hidden type="text" name="board_number" value="' . $data['board_number'] . '">
                 <div class="row card-body">
                     <div class="col-md-12"><span class="comments_title">ㄴ 답글쓰기</span></div>
                     <div class="col-md-4"><input type="text" placeholder="Name" name="name"></div>
                     <div class="col-md-8"><input type="password" placeholder="Password" name="password"></div>
                     <div class="col-md-12 my-2"><textarea placeholder="Comments" name="comments"></textarea></div>
                     <div class="col-md-12" align="right">' . $reply_reg_btn . '</div>
                 </div>
             </form>
         </div>
            ';
        } else {
            $comments .= '
            <div id="comments_tab_' . $data['number'] . '" class="card  mb-3 pl-0 col-sm-12">
                 <form>
                 <div class="row card-body">
                     <div align="left" class="col-md-6">ㄴ&nbsp;' . $data['name'] . ' &nbsp;&nbsp;<span class="board_date">' . $data['reg_date'] . '</span></div>
                     <div align="right" class="col-md-6">
                         ' . $mod_btn . $del_btn . '
                     </div>
                     <div align="left" class="col-md-12 my-2 ml-3">' . $data['comments'] . '</div>
                 </div>
                 </form>
             </div>

             <div  id="mod_tab_' . $data['number'] . '" style="display:none;" class="card  mb-3 pl-0 col-sm-12">
                <form name="mod_form_' . $data['number'] . '" method="post" action="board_comments.php?mode=mod&number=' . $data['number'] . '">
                <input hidden type="text" name="board_number" value="' . $data['board_number'] . '">
                    <div class="row card-body">
                        <div align="left" class="col-md-8">ㄴ&nbsp;' . $data['name'] . ' &nbsp;&nbsp;<span class="board_date">' . $data['reg_date'] . '</span></div>
                        <div align="right" class="col-md-4">
                            ' . $mod_reg_btn . $cancel_btn . '
                        </div>
                        <div align="left" class="col-md-12 my-2 ml-3"><textarea name="comments" >' . $data['comments'] . '</textarea></div>
                    </div>
                </form>
            </div>

            ';
        }

    }
    $script = '<script>
        function open_reply_tab(number){
            if(document.getElementById("reply_tab_"+number).style.display=="none"){
                document.getElementById("reply_tab_"+number).style.display="block";
            }else{
                document.getElementById("reply_tab_"+number).style.display="none";
            }
        }
        function open_mod_tab(number){
             document.getElementById("comments_tab_"+number).style.display="none";
             document.getElementById("mod_tab_"+number).style.display="block";

        }
        function open_comments_tab(number){
            document.getElementById("comments_tab_"+number).style.display="block";
            document.getElementById("mod_tab_"+number).style.display="none";

        }
        function del_comments(number){
            $.ajax({
                url:"board_comments.php?mode=del&number="+number,
                success:function(data){
                   if(data=="SUCCESS"){
                       alert("삭제가 완료되었습니다");
                       location.reload();
                   }else{
                       alert(data);
                   }
                }
            })
        }
        function check_password(number,mode){
            var password = prompt("비밀번호를 입력하세요");
            $.ajax({
                url:"board_comments.php?mode=check_password",
                type:"post",
                data:{"password":password,"number":number},
                success:function(data){
                    if(data=="SAME"){
                        if(mode=="mod"){
                            open_mod_tab(number);
                        }else if(mode=="del"){
                            if(confirm("정말로 삭제하시겠습니까")){
                               del_comments(number);
                            }
                        }
                    }else{
                       alert("패스워드를 확인해주세요");
                    }
                }
            });
        }
        </script>
    ';
    return $comments . $script;

}
function check_sub_category($parent)
{
    global $conn;

    $stmt = $conn->prepare("SELECT count(number) as cnt FROM me_blog_category WHERE parent = :parent");
    $stmt->bindParam(':parent', $parent);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result[0]['cnt'] == 0) {
        return false;
    } else {
        return true;
    }

}
function upload_image($file, $options)
{

    $up_image_name = $file['name'];
    $up_image_temp = $file['tmp_name'];
    $temp_name = explode(".", $up_image_name);
    $ext = strtolower($temp_name[sizeof($temp_name) - 1]);

    // $options = array("jpg", "jpeg", "gif", "png", "PNG");

    for ($z = 0, $m = sizeof($options), $ext_check = ''; $z < $m; $z++) {
        #echo " $ext = ".$options[$z] ."<br>";
        if ($ext == trim($options[$z])) {
            $ext_check = 'ok';
            break;
        }
    }
    if ($ext_check != "ok") {
        //ERROR
        msg("파일 확장자를 확인해주세요!");
        exit;

    } else {
        $time = date("Ymdhis");
        $random = rand(0, 1000000);
        $file_name = $time . "_" . $random . "." . $ext;

        $image_path = "upload/" . $file_name;
        copy($up_image_temp, $image_path);
    }

    return $image_path;
}
function get_select_box($array_value, $array_name, $value)
{

    for ($i = 0; $i < sizeof($array_value); $i++) {
        $selected = ($array_value[$i] == $value) ? "selected" : "";
        $option .= '
        <option value="' . $array_value[$i] . '" ' . $selected . '>' . $array_name[$i] . '</option>
        ';
    }
    $RETURN = '
    <select name="field" class="custom-select" style="width:100%;">
                    ' . $option . '
                </select>
    ';
    return $RETURN;
}
function get_category($parent, $number)
{

    global $conn;

    if ($parent == 0) {
        $category = "category";
    } else {
        $category = "sub_category";
    }

    $RETURN = '
    <select name="' . $category . '" class="custom-select ">
    <option value="">all category</option>
    ';

    $stmt = $conn->prepare("SELECT * FROM me_blog_category WHERE parent = :parent");
    $stmt->bindParam(':parent', $parent);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $data) {
        $selected = ($data['number'] == $number) ? "selected" : "";
        $RETURN .= '
        <option value="' . $data['number'] . '"  ' . $selected . '>' . $data['name'] . '</option>
    ';
    }
    $RETURN .= '</select>';
    return $RETURN;
}

function gomsg($msg, $url)
{
    echo "<script>alert('" . $msg . "')</script>";
    echo "<script>location.href='" . $url . "'</script>";
    exit;
}
function msg($msg)
{
    echo "<script>alert('" . $msg . "')</script>";
    exit;
}
