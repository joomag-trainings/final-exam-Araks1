var comm = document.getElementById("comment");
comm.addEventListener("click", function () {
    var xhttp;

    var input = CKEDITOR.instances.input.getData();
    var id = document.getElementById("id").value;
    xhttp = new XMLHttpRequest();
    xhttp.open("POST", 'post', true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {

        if (xhttp.readyState == 4 && xhttp.status == 200) {

            if (xhttp.responseText !== "") {

                var obj = JSON.parse(xhttp.responseText);

                document.getElementById('none').style.display = "block";
                document.getElementById('commenter_name').innerHTML = obj[0].first_name;
                document.getElementById('comment_text').innerHTML = obj[0].comment;
                document.getElementById('date').innerHTML = obj[0].created_at;
            }
        }
    }
    xhttp.send("input=" + input + "&id=" + id);


});
function get($this) {
    cid = $this.getAttribute('data-id');
    did = $this.getAttribute('data-post');

}

function set($this) {
    $this.setAttribute("href", "delete?id=" + did + "&cid=" + cid);
}