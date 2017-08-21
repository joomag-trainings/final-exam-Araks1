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
                 obj = JSON.parse(xhttp.responseText);
                document.getElementById('commenter_name').innerHTML = obj[0].first_name+' '+obj[0].last_name;
                document.getElementById('comment_text').innerHTML = obj[0].comment;
                document.getElementById('date').innerHTML = obj[0].created_at;
                document.getElementById('app').innerHTML+=document.getElementById('none').innerHTML;
                CKEDITOR.instances.input.setData("");

            }

        }
    }
    xhttp.send("input=" + input + "&id=" + id);
});
function get($this) {
    cid = $this.getAttribute('data-id');
    did = $this.getAttribute('data-post');
    if(cid === "" && did === ""){
        cid = obj[0]['id'];
        did=obj[0]['discussion_id'];
    }
}

function set($this) {
    $this.setAttribute("href", "delete?id=" + did + "&cid=" + cid);
    console.log(cid);
    console.log(descId);
}

function mark($this) {
    xhttp = new XMLHttpRequest();
    xhttp.open("GET", 'mark', true);
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

function getId($this) {

    id = $this.getAttribute('data-id');
    descId = $this.getAttribute('data-desc');
    if(id === "" && descId === ""){
        id = obj[0]['id'];
        descId=obj[0]['discussion_id'];
    }

}

function setId($this) {
    $this.setAttribute("href", "mark?id=" + id + "&desc=" + descId);
}

var edit = document.getElementById('edit');
edit.addEventListener("click", function () {
    title = document.getElementById("title").innerHTML;
    desc = document.getElementById("desc").innerHTML;
    document.getElementById('modal-title').value = title;
    CKEDITOR.instances['modal-desc'].setData(desc);
});