var addComment = document.getElementById("add-comment");
addComment.addEventListener("click", function () {
    var xhttp;
    var input = CKEDITOR.instances.input.getData();
    var id = document.getElementById("id").value;
    xhttp = new XMLHttpRequest();
    xhttp.open("POST", 'comments/add', true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.onreadystatechange = function () {

        if (xhttp.readyState == 4 && xhttp.status == 200) {
            if (xhttp.responseText !== "") {
                obj = JSON.parse(xhttp.responseText);
                document.getElementById('commenter_name').innerHTML = obj[0].first_name + ' ' + obj[0].last_name;
                document.getElementById('comment_text').innerHTML = obj[0].comment;
                document.getElementById('date').innerHTML = obj[0].created_at;
                document.getElementById('app').innerHTML += document.getElementById('none').innerHTML;
                CKEDITOR.instances.input.setData("");

            }

        }
    }
    xhttp.send("input=" + input + "&id=" + id);

});
function deleteId($this) {

    commentId = $this.getAttribute('data-id');
    discussionId = $this.getAttribute('data-post');
    if (commentId === "" && discussionId === "") {
        commentId = obj[0]['id'];
        discussionId = obj[0]['discussion_id'];
    }

}

function deleteComment($this) {
    $this.setAttribute("href", "/forum/public/index.php/comments/delete?id=" + discussionId + "&cid=" + commentId);
}

function mark($this) {
    xhttp = new XMLHttpRequest();
    xhttp.open("GET", 'comments/mark', true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.onreadystatechange = function () {
        if (xhttp.readyState == 4 && xhttp.status == 200) {

            if (xhttp.responseText !== "") {
                var obj = JSON.parse(xhttp.responseText);
            }
        }
    }
    xhttp.send();
}

function idForBestComment($this) {

    id = $this.getAttribute('data-id');
    descId = $this.getAttribute('data-desc');
    if (id === "" && descId === "") {
        id = obj[0]['id'];
        descId = obj[0]['discussion_id'];
    }
}

function markBestAnswer($this) {
    $this.setAttribute("href", "/forum/public/index.php/comments/mark?id=" + id + "&desc=" + descId);
}

var edit = document.getElementById('edit');
edit.addEventListener("click", function () {
    title = document.getElementById("title").innerHTML;
    desc = document.getElementById("desc").innerHTML;
    document.getElementById('modal-title').value = title;
    CKEDITOR.instances['modal-desc'].setData(desc);
});